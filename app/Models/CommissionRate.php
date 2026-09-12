<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionRate extends Model
{
    protected $fillable = [
        'rate_percent',
        'calculation_basis',
        'effective_from',
        'effective_until',
        'changed_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'rate_percent' => 'decimal:4',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    public function changedByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'changed_by_admin_id');
    }

    public function orderCommissions(): HasMany
    {
        return $this->hasMany(OrderCommission::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(CommissionAuditLog::class);
    }

    public function scopeActiveAt(Builder $query, mixed $at): Builder
    {
        return $query
            ->where('effective_from', '<=', $at)
            ->where(function (Builder $window) use ($at): void {
                $window->whereNull('effective_until')
                    ->orWhere('effective_until', '>', $at);
            });
    }
}
