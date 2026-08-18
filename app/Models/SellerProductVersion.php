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
        'sku',
        'price',
        'stock',
        'discount',
        'voucher_code',
        'description',
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
            'matched_terms' => 'array',
            'changed_fields' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }
}
