<?php

namespace App\Events;

use App\Models\SellerAccount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerRealtimeAlert implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SellerAccount $seller,
        public string $type,
        public string $title,
        public string $message,
        public ?string $productName = null,
        public ?int $warningNumber = null,
        public ?string $suspendedUntil = null,
    ) {
        $this->seller->ensureRealtimeToken();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('sari.seller.' . $this->seller->realtime_token);
    }

    public function broadcastAs(): string
    {
        return 'seller.compliance.alert';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'product_name' => $this->productName,
            'warning_number' => $this->warningNumber,
            'max_warnings' => 3,
            'suspended_until' => $this->suspendedUntil,
            'sent_at' => now()->toIso8601String(),
        ];
    }
}
