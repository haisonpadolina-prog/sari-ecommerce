<?php

namespace App\Events;

use App\Models\MarketplaceOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerOrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MarketplaceOrder $order,
        public string $sellerRealtimeToken
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('sari.seller.' . $this->sellerRealtimeToken),
        ];
    }

    public function broadcastAs(): string
    {
        return 'seller.order.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => (int) $this->order->id,
            'order_number' => (string) $this->order->order_number,
            'seller_account_id' => (int) $this->order->seller_account_id,
            'status' => (string) $this->order->status,
            'status_label' => $this->order->statusLabel(),
            'updated_at' => $this->order->updated_at?->toIso8601String(),
            'revision' => $this->order->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}