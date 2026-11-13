<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class AdminNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(public int $notificationId)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('sari.admin.notifications')];
    }

    public function broadcastAs(): string
    {
        return 'admin.notification.created';
    }

    public function broadcastWith(): array
    {
        // Invalidate the authenticated feed; no user content goes over the socket.
        return ['id' => $this->notificationId];
    }
}
