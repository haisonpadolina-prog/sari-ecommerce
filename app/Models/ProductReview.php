<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'seller_product_id',
        'seller_account_id',
        'marketplace_order_id',
        'buyer_account_id',
        'buyer_social_account_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(BuyerAccount::class, 'buyer_account_id');
    }

    public function socialBuyer(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'buyer_social_account_id');
    }
}
