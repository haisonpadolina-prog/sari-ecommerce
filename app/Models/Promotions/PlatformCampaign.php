<?php

namespace App\Models\Promotions;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PlatformCampaign extends Model
{
    public const TIMEZONE = 'Asia/Manila';

    protected $fillable = [
        'admin_account_id',
        'name',
        'slug',
        'description',
        'state',
        'target_scope',
        'sale_enabled',
        'sale_discount_type',
        'sale_discount_value',
        'sale_max_discount',
        'voucher_enabled',
        'voucher_code',
        'voucher_discount_type',
        'voucher_discount_value',
        'voucher_minimum_spend',
        'voucher_maximum_discount',
        'voucher_usage_limit',
        'voucher_used_count',
        'voucher_per_buyer_limit',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'sale_enabled' => 'boolean',
        'sale_discount_value' => 'decimal:2',
        'sale_max_discount' => 'decimal:2',
        'voucher_enabled' => 'boolean',
        'voucher_discount_value' => 'decimal:2',
        'voucher_minimum_spend' => 'decimal:2',
        'voucher_maximum_discount' => 'decimal:2',
        'voucher_usage_limit' => 'integer',
        'voucher_used_count' => 'integer',
        'voucher_per_buyer_limit' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'admin_account_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            SellerProduct::class,
            'platform_campaign_products',
            'platform_campaign_id',
            'seller_product_id'
        );
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(
            SellerAccount::class,
            'platform_campaign_sellers',
            'platform_campaign_id',
            'seller_account_id'
        );
    }

    public function categories(): HasMany
    {
        return $this->hasMany(PlatformCampaignCategory::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(PlatformCampaignRedemption::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('state', 'published');
    }

    public static function promotionNow(): Carbon
    {
        return Carbon::now(self::TIMEZONE);
    }

    public static function storageNow(): Carbon
    {
        return self::promotionNow()->copy()->utc();
    }

    public function scopeLive(Builder $query, ?Carbon $at = null): Builder
    {
        $at = $at
            ? $at->copy()->utc()
            : self::storageNow();

        return $query
            ->published()
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>=', $at);
    }

    public function isLive(?Carbon $at = null): bool
    {
        $at = $at
            ? $at->copy()->utc()
            : self::storageNow();

        return $this->state === 'published'
            && $this->starts_at !== null
            && $this->ends_at !== null
            && $this->starts_at->copy()->utc()->lte($at)
            && $this->ends_at->copy()->utc()->gte($at);
    }

    public function displayStatus(?Carbon $at = null): string
    {
        $at = $at
            ? $at->copy()->utc()
            : self::storageNow();

        if ($this->state === 'draft') {
            return 'Draft';
        }

        if ($this->state === 'paused') {
            return 'Paused';
        }

        if ($this->starts_at && $this->starts_at->copy()->utc()->gt($at)) {
            return 'Scheduled';
        }

        if ($this->ends_at && $this->ends_at->copy()->utc()->lt($at)) {
            return 'Ended';
        }

        return $this->isLive($at) ? 'Active' : 'Published';
    }

    public function startsAtManila(): ?Carbon
    {
        return $this->starts_at?->copy()->timezone(self::TIMEZONE);
    }

    public function endsAtManila(): ?Carbon
    {
        return $this->ends_at?->copy()->timezone(self::TIMEZONE);
    }
}
