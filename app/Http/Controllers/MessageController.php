<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BaseController;
use App\Models\Conversation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;
use Illuminate\Support\Facades\Validator;

class MessageController extends BaseController
{
    public function getMessages($id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()
            ->with(['participants' => function($builder) use ($user) {
            $builder->where('id', '<>', $user->id);
        }])
        ->findOrFail($id);

        if (is_null($conversation))
        {
            return $this->sendResponse([]);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->where(function($query) use ($user) {
                $query
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->whereNull('deleted_at');
                    })
                    ->orWhereRaw('id IN (
                        SELECT message_id FROM recipients
                        WHERE recipients.message_id = messages.id
                        AND recipients.user_id = ?
                        AND recipients.deleted_at IS NULL
                    )', [$user->id]);
            })->get();

        return $this->extracted_data($messages);
    }

    public function sendMessage(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'conversation_id' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->input('user_id');
                }),
                'int',
                'exists:conversations,id',
            ],
            'user_id' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->input('conversation_id');
                }),
                'int',
                'exists:users,id',
            ],
            'text' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->hasFile('image') && !$request->hasFile('file');
                }),
                'string'
            ],
            'image' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->post('text') && !$request->hasFile('file');
                }),
                'image',
                'mimes:jpeg,png,bmp,jpg,gif,svg'
            ],
            'file' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->hasFile('image') && !$request->post('text');
                }),
                'file',
                'mimes:pdf,doc,txt'
            ]
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = Auth::user();

        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');

        DB::beginTransaction();
        try
        {
            if ($conversation_id)
            {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            }
            else
            {
                $conversation = Conversation::where('type', '=', 'peer')
                    ->whereHas('participants', function ($builder) use ($user_id, $user) {
                    $builder->join('participants as participants2', 'participants2.conversation_id', '=', 'participants.conversation_id')
                            ->where('participants.user_id', '=', $user_id)
                            ->where('participants2.user_id', '=', $user->id);
                })->first();

                if (!$conversation)
                {
                    $conversation = Conversation::create([
                        'type' => 'peer',
                    ]);

                    $conversation->participants()->attach([
                        $user->id => ['joined_at' => now()],
                        $user_id => ['joined_at' => now()],
                    ]);
                }
            }

            if ($request->hasFile('file'))
            {
                $type = 'file';
                $message = $this->get_file($request, "message");
            }
            else if ($request->hasFile('image'))
            {
                $type = 'image';
                $message = (new RegisterController)->get_image($request, "message");
            }
            else
            {
                $type = 'text';
                $message = $request->post('text');
            }

            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'type' => $type,
                'body' => $message,
            ]);

            DB::statement('
                INSERT INTO recipients (user_id, message_id)
                SELECT user_id, ? FROM participants
                WHERE conversation_id = ?
                AND user_id <> ?
            ', [$message->id, $conversation->id, $user->id]);

            $conversation->update([
                'last_message_id' => $message->id,
            ]);

            DB::commit();

            $message->load('user');

            broadcast(new MessageSent($message));

        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $this->show($message);
    }

    public function deleteMessage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'target' => ['required', 'string'],
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $user->sentMessages()
            ->where('id', '=', $id)
            ->update([
                'deleted_at' => Carbon::now(),
            ]);

        if ($request->target == 'me')
        {
            Recipient::where([
                'user_id' => $user->id,
                'message_id' => $id,
            ])->delete();

        }
        else
        {
            Recipient::where([
                'message_id' => $id,
            ])->delete();
        }

        return $this->sendResponse([]);
    }

    public function extracted_data($messages)
    {
        if (empty($messages))
        {
            return $this->sendResponse([]);
        }

        foreach ($messages as $key => &$message)
        {
            $response = $this->show($message);
            if ($response->getData()->status == 'failure')
            {
                return $response;
            }
            $messages[$key] = $response->getData()->data;
        }

        $messages = $messages->sortByDesc('date');
        $messages = array_values($messages->all());

        return $this->sendResponse($messages);
    }

    public function show($message)
    {
        if (is_null($message))
        {
            return $this->sendError(["error" => "this message isn't found"]);
        }

        $message_data = [
            'id'              => $message->id,
            'type'            => $message->type,
            'body'            => $message->body,
            'user_id'         => $message->user_id,
            'conversation_id' => $message->conversation_id,
            'date'            => $message->created_at->format('Y-m-d H:i:s'),
        ];

        return $this->sendResponse($message_data);
    }

    public function get_file($request, $type)
    {
        $file = $request->file('file');
        $file_name = time().'.'.$file->getClientOriginalExtension();

        $path = 'files/' . $type.'/' ;

        $file->move($path, $file_name);
        $file_name = $path.$file_name ;

        return $file_name ;
    }
}
