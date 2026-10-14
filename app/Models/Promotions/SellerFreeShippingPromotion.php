<?php

namespace App\Models\Promotions;

use App\Models\Accounts\SellerAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SellerFreeShippingPromotion extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_ENDED = 'ended';

    public const TIMEZONE = 'Asia/Manila';

    protected $fillable = [
        'seller_account_id',
        'minimum_order_amount',
        'maximum_shipping_discount',
        'starts_at',
        'ends_at',
        'status',
        'paused_at',
        'ended_at',
    ];

    protected $casts = [
        'minimum_order_amount' => 'decimal:2',
        'maximum_shipping_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paused_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeLiveAt(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->enabled()
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>', $at);
    }

    public function isLiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status === self::STATUS_ACTIVE
            && $this->starts_at !== null
            && $this->ends_at !== null
            && $this->starts_at->lte($at)
            && $this->ends_at->gt($at);
    }

    public function displayStatus(?Carbon $at = null): string
    {
        $at ??= now();

        if ($this->status === self::STATUS_ENDED || $this->ended_at || $this->ends_at?->lte($at)) {
            return 'Ended';
        }

        if ($this->status === self::STATUS_PAUSED) {
            return 'Paused';
        }

        if ($this->starts_at?->gt($at)) {
            return 'Scheduled';
        }

        return 'Active';
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
