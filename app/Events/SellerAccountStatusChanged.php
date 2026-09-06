<?php

namespace App\Events;

use App\Models\SellerAccount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerAccountStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SellerAccount $seller,
        public string $action,
        public string $message
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('sari.seller.' . $this->seller->realtime_token)];
    }

    public function broadcastAs(): string
    {
        return 'seller.account-status';
    }

    public function broadcastWith(): array
    {
        return [
            'seller_id' => $this->seller->id,
            'action' => $this->action,
            'message' => $this->message,
            'account_status' => (string) ($this->seller->account_status ?: 'active'),
            'warning_count' => (int) ($this->seller->warning_count ?? 0),
            'suspended_until' => $this->seller->suspended_until
                ? (string) $this->seller->suspended_until
                : null,
        ];
    }
}
