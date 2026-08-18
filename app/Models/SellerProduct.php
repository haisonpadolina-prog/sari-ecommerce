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
