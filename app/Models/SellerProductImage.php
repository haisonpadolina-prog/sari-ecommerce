<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProductImage extends Model
{
    protected $fillable = [
        'seller_product_id',
        'path',
        'sort_order',
        'alt_text',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }
}
