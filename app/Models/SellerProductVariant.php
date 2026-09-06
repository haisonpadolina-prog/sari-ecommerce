<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProductVariant extends Model
{
    protected $fillable = [
        'seller_product_id',
        'sku',
        'option_values',
        'price',
        'stock',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'option_values' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }
}
