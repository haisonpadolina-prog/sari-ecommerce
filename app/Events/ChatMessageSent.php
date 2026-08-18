<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        $this->message->loadMissing('seller');
        $this->message->seller->ensureRealtimeToken();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sari.seller.' . $this->message->seller->realtime_token),
            new Channel(config('sari_chat.admin_channel')),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'seller_id' => $this->message->seller_account_id,
            'seller_name' => $this->message->seller->store_name ?: $this->message->seller->email,
            'sender_role' => $this->message->sender_role,
            'body' => $this->message->body,
            'attachment_name' => $this->message->attachment_name,
            'attachment_mime' => $this->message->attachment_mime,
            'attachment_size' => $this->message->attachment_size,
            'attachment_url' => $this->message->attachment_path
                ? route('chat.attachments.show', $this->message, false)
                : null,
            'created_at' => $this->message->created_at?->toIso8601String(),
            'time' => $this->message->created_at?->format('h:i A'),
        ];
    }
}
