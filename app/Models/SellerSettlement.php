<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerSettlement extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'seller_account_id',
        'order_commission_id',
        'merchandise_amount',
        'platform_commission_amount',
        'withholding_rate',
        'withholding_tax_amount',
        'seller_net_amount',
        'currency',
        'status',
        'withholding_status',
        'eligible_at',
        'paid_at',
        'external_settlement_reference',
        'policy_snapshot',
    ];

    protected $casts = [
        'merchandise_amount' => 'decimal:2',
        'platform_commission_amount' => 'decimal:2',
        'withholding_rate' => 'decimal:4',
        'withholding_tax_amount' => 'decimal:2',
        'seller_net_amount' => 'decimal:2',
        'eligible_at' => 'datetime',
        'paid_at' => 'datetime',
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

    public function commission(): BelongsTo
    {
        return $this->belongsTo(OrderCommission::class, 'order_commission_id');
    }
}
