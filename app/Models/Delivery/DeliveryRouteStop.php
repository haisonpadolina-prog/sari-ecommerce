<?php

namespace App\Models\Delivery;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRouteStop extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'logistics_facility_id',
        'sequence',
        'stop_key',
        'type',
        'name',
        'address_snapshot',
        'latitude',
        'longitude',
        'reached_at',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'reached_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsFacility::class,
            'logistics_facility_id'
        );
    }
}
