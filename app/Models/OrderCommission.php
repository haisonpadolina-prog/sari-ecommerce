<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderCommission extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'seller_account_id',
        'commission_rate_id',
        'eligible_amount',
        'rate_percent',
        'gross_commission',
        'adjustment_total',
        'net_commission',
        'status',
        'earned_at',
        'source',
        'policy_snapshot',
    ];

    protected $casts = [
        'eligible_amount' => 'decimal:2',
        'rate_percent' => 'decimal:4',
        'gross_commission' => 'decimal:2',
        'adjustment_total' => 'decimal:2',
        'net_commission' => 'decimal:2',
        'earned_at' => 'datetime',
        'policy_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(CommissionRate::class, 'commission_rate_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(CommissionAdjustment::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(CommissionAuditLog::class);
    }

    public function sellerSettlement(): HasOne
    {
        return $this->hasOne(SellerSettlement::class, 'order_commission_id');
    }
}
