<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerCartItem extends Model
{
    protected $fillable = [
        'buyer_account_id',
        'buyer_social_account_id',
        'seller_product_id',
        'seller_product_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(BuyerAccount::class, 'buyer_account_id');
    }

    public function socialBuyer(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'buyer_social_account_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(SellerProductVariant::class, 'seller_product_variant_id');
    }
}
