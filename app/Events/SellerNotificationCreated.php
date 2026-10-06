<?php

namespace App\Events;

use App\Models\Platform\SellerNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SellerNotification $notification,
        public string $sellerRealtimeToken
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sari.seller.' . $this->sellerRealtimeToken),
        ];
    }

    public function broadcastAs(): string
    {
        return 'seller.notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => (int) $this->notification->id,
            'type' => (string) $this->notification->type,
            'title' => (string) $this->notification->title,
            'message' => $this->notification->message,
            'action_url' => $this->notification->action_url
                ?: route('seller.notifications.index', [], false),
            'read_url' => route(
                'seller.notifications.read',
                $this->notification,
                false
            ),
            'data' => $this->notification->data ?: [],
            'created_at' => $this->notification->created_at?->toIso8601String(),
            'time' => $this->notification->created_at?->format('h:i A'),
            'unread' => is_null($this->notification->read_at),
        ];
    }
}
