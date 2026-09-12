<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformSettingVersion extends Model
{
    protected $fillable = [
        'version_number',
        'settings',
        'effective_at',
        'status',
        'change_reason',
        'created_by_admin_id',
        'commission_rate_id',
        'cancelled_at',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'settings' => 'array',
        'effective_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            AdminAccount::class,
            'created_by_admin_id'
        );
    }

    public function commissionRate(): BelongsTo
    {
        return $this->belongsTo(
            CommissionRate::class,
            'commission_rate_id'
        );
    }

    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeEffectiveAt(Builder $query, $at): Builder
    {
        return $query
            ->notCancelled()
            ->where('effective_at', '<=', $at);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query
            ->where('status', 'scheduled')
            ->where('effective_at', '>', now());
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled'
            && $this->effective_at
            && $this->effective_at->isFuture();
    }
}
