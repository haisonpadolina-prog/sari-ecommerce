<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionAuditLog extends Model
{
    protected $fillable = [
        'order_commission_id',
        'commission_rate_id',
        'admin_account_id',
        'actor_type',
        'action',
        'description',
        'old_values',
        'new_values',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    public function orderCommission(): BelongsTo
    {
        return $this->belongsTo(OrderCommission::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(CommissionRate::class, 'commission_rate_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'admin_account_id');
    }
}
