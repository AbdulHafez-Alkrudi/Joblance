<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\BaseController;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConversationController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()->with([
            'lastMessage',
            'participants' => function($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },])
            ->withCount([
                'recipients as new_messages' => function($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])->get();

        return $this->extracted_data($conversations);
    }

    public function show($id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()->with([
            'lastMessage',
            'participants' => function($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },])
            ->withCount([
                'recipients as new_messages' => function($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->find($id);

        return $this->getConversation($conversation);
    }

    public function addParticipant(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $conversation = Conversation::query()->find($request->conversation_id);
        $conversation->participants()->attach($request->post('user_id'), [
            'joined_at' => Carbon::now(),
        ]);

        return $this->sendResponse([]);
    }

    public function removeParticipant(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $conversation = Conversation::query()->find($request->conversation_id);
        $conversation->participants()->detach($request->post('user_id'));

        return $this->sendResponse([]);
    }

    public function markAsRead($id)
    {
        Recipient::where('user_id', '=', Auth::id())
            ->whereNull('read_at')
            ->whereRaw('message_id IN (
                SELECT id FROM messages WHERE conversation_id = ?
            )', [$id])
            ->update([
                'read_at' => Carbon::now(),
            ]);

        return $this->sendResponse([]);
    }

    public function destroy($id)
    {
        Participant::where('user_id', '=', Auth::id())
            ->where('conversation_id', '=', $id)
            ->delete();

        return $this->sendResponse([]);
    }

    public function extracted_data($conversations)
    {
        if (empty($conversations))
        {
            return $this->sendResponse([]);
        }

        foreach ($conversations as $key => &$conversation)
        {
            $response = $this->getConversation($conversation);
            if ($response->getData()->status == 'failure')
            {
                return $response;
            }
            $conversations[$key] = $response->getData()->data;
        }

        $conversations = $conversations->sortByDesc('date');
        $conversations = array_values($conversations->all());

        return $this->sendResponse($conversations);
    }

    public function getConversation($conversation)
    {
        if (is_null($conversation))
        {
            return $this->sendError(["error" => "this conversation isn't found"]);
        }

        $conversation_data = [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'new_messages' => $conversation->new_messages,
            'date' => $conversation->created_at->format('Y-m-d H:i:s'),
            'last_message' => (new MessageController)->show($conversation->lastMessage)->getData()->data,
            'participant' => $conversation->participants[0]->show($conversation->participants[0]),
        ];

        return $this->sendResponse($conversation_data);
    }
}
