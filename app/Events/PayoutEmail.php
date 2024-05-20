<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayoutEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $message_body, $message_subject;
    /**
     * Create a new event instance.
     */
    public function __construct($user, $message_body, $message_subject)
    {
        $this->user = $user;
        $this->message_body = $message_body;
        $this->message_subject = $message_subject;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
