<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionAdjustment extends Model
{
    protected $fillable = [
        'order_commission_id',
        'type',
        'amount',
        'reason',
        'reference',
        'created_by_admin_id',
        'adjusted_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'adjusted_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function orderCommission(): BelongsTo
    {
        return $this->belongsTo(OrderCommission::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'created_by_admin_id');
    }
}
