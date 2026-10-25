<?php

namespace App\Models\Delivery;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelQrScanEvent extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'scan_key',
        'checkpoint',
        'actor_role',
        'actor_id',
        'logistics_facility_id',
        'context_type',
        'context_id',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            MarketplaceOrder::class,
            'marketplace_order_id'
        );
    }
}
