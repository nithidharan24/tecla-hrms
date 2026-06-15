<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message,
        public string $senderName,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('chat.' . $this->message->conversation_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender_role'     => $this->message->sender_role,
            'sender_name'     => $this->senderName,
            'body'            => $this->message->body,
            'created_at'      => $this->message->created_at->format('h:i A'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
