<?php

namespace App\Models\Platform;

use App\Models\Orders\MarketplaceOrderEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerNotificationRead extends Model
{
    protected $fillable = [
        'buyer_key',
        'marketplace_order_event_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrderEvent::class, 'marketplace_order_event_id');
    }
}
