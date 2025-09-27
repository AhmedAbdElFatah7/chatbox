<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\PrivateChannel; 
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public $message;

    public function __construct($message)
    {
        // $message ممكن يكون مصفوفة أو Model
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // استخدم PrivateChannel لو عايز توكن أو تحقق، أو Channel لو عام
        return new PrivateChannel('chat');
        // أو per room: return new PrivateChannel("chat.{$this->message->room_id}");
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id ?? null,
            'text' => $this->message->text ?? $this->message,
            'user' => $this->message->user ?? null,
            'created_at' => $this->message->created_at ?? now()->toDateTimeString(),
        ];
    }
}
