<?php

namespace App\Models\Finance;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsEarning extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'logistics_account_id',
        'gross_shipping_revenue',
        'pickup_rider_cost',
        'last_mile_rider_cost',
        'net_logistics_amount',
        'currency',
        'status',
        'eligible_at',
        'released_at',
        'policy_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'gross_shipping_revenue' => 'decimal:2',
            'pickup_rider_cost' => 'decimal:2',
            'last_mile_rider_cost' => 'decimal:2',
            'net_logistics_amount' => 'decimal:2',
            'eligible_at' => 'datetime',
            'released_at' => 'datetime',
            'policy_snapshot' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }
}
