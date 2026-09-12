<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsParcel extends Model
{
    protected $fillable = [
        'marketplace_order_id', 'status', 'sorting_zone', 'notes', 'received_at', 'sorted_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'sorted_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
