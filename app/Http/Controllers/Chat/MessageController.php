<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DeleteMessageRequest;
use App\Http\Requests\MessageRequest;
use App\Models\Conversation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

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
        $messageRequest = new MessageRequest();
        $validator = Validator::make($request->all(), $messageRequest->rules($request));

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

    public function deleteMessage(Request $request)
    {
        $deleteMessageRequest = new DeleteMessageRequest();
        $validator = Validator::make($request->all(), $deleteMessageRequest->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        foreach ($request->ids as $id)
        {
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
            'user_id'         => is_null($message->user_id) ? 0 : $message->user_id,
            'conversation_id' => $message->conversation_id,
            'date'            => $message->created_at->format('Y-m-d H:i:s'),
        ];

        if ($message['type'] == 'file')
        {
            // Check if the file exists
            if (file_exists($message['body'])) {
                // Get the base name (file name without extension)
                $baseName = pathinfo($message['body'], PATHINFO_BASENAME);

                // Assuming you want to remove the time prefix (if any)
                $fileName = preg_replace('/^\d+\./', '', $baseName);
            } else {
                $fileName = 'no file!';
            }

            $message_data['file_name'] = $fileName;
        }

        return $this->sendResponse($message_data);
    }

    public function get_file($request, $type)
    {
        $file = $request->file('file');
        $file_name = time().'.'.$file->getClientOriginalName();

        $path = 'files/' . $type.'/' ;

        $file->move($path, $file_name);
        $file_name = $path.$file_name ;

        return $file_name ;
    }
}
