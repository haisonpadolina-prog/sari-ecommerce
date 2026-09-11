<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiderEarning extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'courier_account_id',
        'delivery_fee_amount',
        'currency',
        'status',
        'earned_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'delivery_fee_amount' => 'decimal:2',
        'earned_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierAccount::class, 'courier_account_id');
    }

    public function payoutItems(): HasMany
    {
        return $this->hasMany(RiderPayoutRequestItem::class, 'rider_earning_id');
    }
}
