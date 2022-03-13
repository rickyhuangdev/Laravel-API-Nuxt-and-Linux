<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use App\Repositories\Eloquent\Criteria\WithTrash;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //
    protected $chats;
    protected $messages;

    public function __construct(IChat $chats, IMessage $messages)
    {
        $this->messages = $messages;
        $this->chats = $chats;
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required']
        ]);
        $recipient = $request->recipient;
        $body = $request->body;
        $user = auth()->user();
        //check if there is an existing chat

        $chat = $user->getChatWithUser($recipient);
        if (!$chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [
                $user->id,
                $recipient
            ]);
        }
        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ]);
        return new MessageResource($message);
    }

    public function getUserChats()
    {
        $chats = $this->chats->getUserChats();
        return ChatResource::collection($chats);
    }

    public function getChatMessages($id)
    {
        $messages = $this->messages->withCriteria([
            new WithTrash()
        ])->findWhere('chat_id', $id);
        return MessageResource::collection($messages);
    }

    public function markAsRead($id)
    {

    }

    public function destroyMessage($id)
    {

    }
}
