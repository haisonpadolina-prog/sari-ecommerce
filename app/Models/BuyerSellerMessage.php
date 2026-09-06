<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerSellerMessage extends Model
{
    protected $fillable = [
        'seller_account_id',
        'buyer_account_id',
        'buyer_social_account_id',
        'marketplace_order_id',
        'sender_role',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
