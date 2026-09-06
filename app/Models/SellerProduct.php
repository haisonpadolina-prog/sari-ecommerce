<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerProduct extends Model
{
    protected $fillable = [
        'seller_account_id',
        'name',
        'category',
        'brand',
        'condition',
        'sku',
        'price',
        'stock',
        'low_stock_threshold',
        'discount',
        'free_shipping',
        'package_weight',
        'package_length',
        'package_width',
        'package_height',
        'preparation_days',
        'voucher_code',
        'description',
        'image_path',
        'moderation_status',
        'screening_risk',
        'screening_reason',
        'matched_terms',
        'admin_review_note',
        'reviewed_at',
        'requires_re_review',
        'last_sensitive_edit_at',
        'archived_at',
        'archive_reason',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'free_shipping' => 'boolean',
            'package_weight' => 'decimal:3',
            'package_length' => 'decimal:2',
            'package_width' => 'decimal:2',
            'package_height' => 'decimal:2',
            'preparation_days' => 'integer',
            'low_stock_threshold' => 'integer',
            'matched_terms' => 'array',
            'reviewed_at' => 'datetime',
            'requires_re_review' => 'boolean',
            'last_sensitive_edit_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(SellerWarning::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(SellerProductVariant::class, 'seller_product_id')
            ->where('is_active', true)
            ->orderBy('id');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(SellerProductImage::class, 'seller_product_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'seller_product_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(SellerProductVersion::class);
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(SellerProductVersion::class)->latestOfMany();
    }

    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }
}
