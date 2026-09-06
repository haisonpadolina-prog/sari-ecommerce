<?php

namespace App\Models;

use App\Events\SellerOrderUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceOrder extends Model
{
    protected $fillable = [
        'order_number',
        'checkout_reference',
        'seller_account_id',
        'buyer_account_id',
        'buyer_social_account_id',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_address',
        'payment_method',
        'payment_status',
        'items',
        'subtotal',
        'delivery_fee',
        'total',
        'pickup_name',
        'pickup_address',
        'status',
        'courier_name',
        'courier_email',
        'accepted_at',
        'ready_at',
        'pickup_started_at',
        'arrived_pickup_at',
        'picked_up_at',
        'arrived_buyer_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'accepted_at' => 'datetime',
        'ready_at' => 'datetime',
        'pickup_started_at' => 'datetime',
        'arrived_pickup_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'arrived_buyer_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (self $order): void {
            $order->broadcastSellerRealtimeUpdate();
        });

        static::updated(function (self $order): void {
            if (!$order->wasChanged([
                'status',
                'items',
                'subtotal',
                'delivery_fee',
                'total',
                'buyer_name',
                'buyer_email',
                'buyer_phone',
                'buyer_address',
                'payment_method',
                'payment_status',
                'courier_name',
                'courier_email',
                'accepted_at',
                'ready_at',
                'pickup_started_at',
                'arrived_pickup_at',
                'picked_up_at',
                'arrived_buyer_at',
                'delivered_at',
                'cancelled_at',
                'cancellation_reason',
            ])) {
                return;
            }

            $order->broadcastSellerRealtimeUpdate();
        });
    }

    private function broadcastSellerRealtimeUpdate(): void
    {
        if (!(int) $this->seller_account_id) {
            return;
        }

        $seller = SellerAccount::query()->find($this->seller_account_id);

        if (!$seller) {
            return;
        }

        $token = $seller->ensureRealtimeToken();

        SellerOrderUpdated::dispatch(
            $this->fresh() ?? $this,
            $token
        );
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(BuyerAccount::class, 'buyer_account_id');
    }

    public function socialBuyer(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'buyer_social_account_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MarketplaceOrderEvent::class, 'marketplace_order_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'marketplace_order_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new' => 'Order Placed',
            'preparing' => 'Preparing',
            'ready_for_pickup' => 'Ready for Pickup',
            'courier_accepted' => 'Courier Accepted',
            'heading_pickup' => 'Heading to Pickup',
            'arrived_pickup' => 'Courier at Seller',
            'in_transit' => 'In Transit',
            'arrived_buyer' => 'Courier at Buyer',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => str($this->status)->replace('_', ' ')->title()->toString(),
        };
    }
}
