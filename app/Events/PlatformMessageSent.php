<?php

namespace App\Events;

use App\Models\PlatformMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlatformMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PlatformMessage $message)
    {
        $this->message->loadMissing('conversation.participants');
    }

    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;

        if (!$conversation) {
            return [];
        }

        return $conversation->participants
            ->filter(fn ($participant) => !$participant->left_at && filled($participant->realtime_token))
            ->map(fn ($participant) => new Channel(
                'sari.platform.participant.' . $participant->realtime_token
            ))
            ->values()
            ->all();
    }

    public function broadcastAs(): string
    {
        return 'platform.message';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->platform_conversation_id,
            'conversation_uuid' => $this->message->conversation?->uuid,
            'sender_role' => $this->message->sender_role,
            'sender_id' => $this->message->sender_id,
            'message_type' => $this->message->message_type,
            'body' => $this->message->body,
            'metadata' => $this->message->metadata,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
