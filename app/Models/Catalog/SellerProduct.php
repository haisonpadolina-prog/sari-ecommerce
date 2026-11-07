<?php

namespace App\Models\Catalog;

use App\Models\Accounts\SellerAccount;
use App\Models\Compliance\SellerWarning;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        'flash_sale_starts_at',
        'flash_sale_ends_at',
        'free_shipping',
        'cash_on_delivery',
        'package_weight',
        'package_length',
        'package_width',
        'package_height',
        'preparation_days',
        'voucher_code',
        'description',
        'image_path',
        'size_guide_image_path',
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
            'flash_sale_starts_at' => 'datetime',
            'flash_sale_ends_at' => 'datetime',
            'free_shipping' => 'boolean',
            'cash_on_delivery' => 'boolean',
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

    /**
     * Real Flash Sale window, evaluated against server time (UTC storage).
     * Older end-only promotions remain live until their end after approval.
     */
    public function isFlashSaleActive(): bool
    {
        if ($this->moderation_status !== 'approved'
            || (float) ($this->discount ?? 0) <= 0
            || $this->flash_sale_ends_at === null) {
            return false;
        }

        $now = now();

        return ($this->flash_sale_starts_at === null || $now->gte($this->flash_sale_starts_at))
            && $now->lt($this->flash_sale_ends_at);
    }

    /**
     * Ordinary unscheduled discounts retain their existing behavior.
     * Scheduled/end-only Flash Sales never discount outside the live window.
     */
    public function effectiveDiscountPercent(): float
    {
        $discount = min(100.0, max(0.0, (float) ($this->discount ?? 0)));
        if ($discount <= 0) {
            return 0.0;
        }

        return $this->flash_sale_ends_at === null || $this->isFlashSaleActive()
            ? $discount
            : 0.0;
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

    /**
     * Buyer-visible marketplace inventory.
     *
     * Keep this invariant in one place so catalog pages, direct product URLs,
     * image endpoints, and cart operations cannot disagree about whether a
     * seller listing is currently purchasable.
     */
    public function scopeBuyerVisible(Builder $query): Builder
    {
        return $query
            ->where('moderation_status', 'approved')
            ->whereNull('archived_at')
            ->whereHas('seller', fn (Builder $sellerQuery) => $sellerQuery->buyerAvailable());
    }

    public function isBuyerVisible(): bool
    {
        $this->loadMissing('seller');

        if ($this->moderation_status !== 'approved' || $this->archived_at !== null) {
            return false;
        }

        $seller = $this->seller;

        if (!$seller) {
            return false;
        }

        return $seller->isBuyerAvailable();
    }
}
