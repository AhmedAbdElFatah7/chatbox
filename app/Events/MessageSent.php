<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\PrivateChannel; 
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Broadcast to both sender and receiver
        return [
            new PrivateChannel('chat.' . $this->message->sender_id),
            new PrivateChannel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'username' => $this->message->sender->username,
                'image' => $this->message->sender->image,
            ],
            'receiver' => [
                'id' => $this->message->receiver->id,
                'name' => $this->message->receiver->name,
                'username' => $this->message->receiver->username,
                'image' => $this->message->receiver->image,
            ],
            'is_read' => $this->message->is_read,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
