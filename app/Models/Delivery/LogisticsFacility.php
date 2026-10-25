<?php

namespace App\Models\Delivery;

use App\Models\Accounts\LogisticsAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsFacility extends Model
{
    protected $fillable = [
        'logistics_account_id',
        'code',
        'name',
        'address',
        'province_name',
        'municipality_name',
        'barangay_name',
        'latitude',
        'longitude',
        'is_active',
        'is_main_branch',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
        'is_main_branch' => 'boolean',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsAccount::class,
            'logistics_account_id'
        );
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(
            DeliveryRouteStop::class,
            'logistics_facility_id'
        );
    }

    public function scopeForLogistics(
        Builder $query,
        int $logisticsAccountId
    ): Builder {
        return $query->where(
            'logistics_account_id',
            $logisticsAccountId
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeMainBranch(Builder $query): Builder
    {
        return $query->where('is_main_branch', true);
    }
}
