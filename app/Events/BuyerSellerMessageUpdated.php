<?php

namespace App\Events;

use App\Models\Messaging\BuyerSellerMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuyerSellerMessageUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BuyerSellerMessage $message)
    {
    }

    public function broadcastOn(): array
    {
        $buyerType = $this->message->buyer_account_id ? 'account' : 'social';
        $buyerId = (int) ($this->message->buyer_account_id ?: $this->message->buyer_social_account_id);

        return [
            new PrivateChannel('sari.buyer-seller.seller.' . $this->message->seller_account_id),
            new PrivateChannel('sari.buyer-seller.buyer.' . $buyerType . '.' . $buyerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'buyer-seller.message';
    }

    public function broadcastWith(): array
    {
        // Never broadcast addresses, phone numbers, or message bodies: authorized
        // clients retrieve updated data through their own authenticated API.
        return [
            'message_id' => (int) $this->message->id,
            'seller_id' => (int) $this->message->seller_account_id,
        ];
    }
}
