<?php

namespace App\Events;

use App\Models\PlatformMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlatformMessageReactionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PlatformMessage $message,
        public array $reactions
    ) {
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
        return 'platform.reaction';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->platform_conversation_id,
            'conversation_uuid' => $this->message->conversation?->uuid,
            'reactions' => $this->reactions,
        ];
    }
}
