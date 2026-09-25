<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProductVersion extends Model
{
    protected $fillable = [
        'seller_product_id',
        'version_number',
        'name',
        'category',
        'brand',
        'condition',
        'sku',
        'price',
        'stock',
        'low_stock_threshold',
        'discount',
        'flash_sale_ends_at',
        'free_shipping',
        'package_weight',
        'package_length',
        'package_width',
        'package_height',
        'preparation_days',
        'voucher_code',
        'description',
        'specifications',
        'has_variants',
        'variants_snapshot',
        'gallery_snapshot',
        'image_path',
        'moderation_status',
        'screening_risk',
        'screening_reason',
        'matched_terms',
        'changed_fields',
        'snapshot_reason',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'flash_sale_ends_at' => 'datetime',
            'free_shipping' => 'boolean',
            'package_weight' => 'decimal:3',
            'package_length' => 'decimal:2',
            'package_width' => 'decimal:2',
            'package_height' => 'decimal:2',
            'preparation_days' => 'integer',
            'low_stock_threshold' => 'integer',
            'specifications' => 'array',
            'has_variants' => 'boolean',
            'variants_snapshot' => 'array',
            'gallery_snapshot' => 'array',
            'matched_terms' => 'array',
            'changed_fields' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }
}
