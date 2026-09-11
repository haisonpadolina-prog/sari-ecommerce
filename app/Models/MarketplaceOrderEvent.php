<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceOrderEvent extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'seller_account_id',
        'audience',
        'type',
        'title',
        'message',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }
}
