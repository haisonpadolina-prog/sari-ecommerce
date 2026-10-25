<?php

namespace App\Models\Accounts;

use App\Models\Delivery\LogisticsFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsHubAccount extends Model
{
    protected $fillable = [
        'logistics_account_id',
        'logistics_facility_id',
        'name',
        'email',
        'password',
        'role',
        'account_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function logisticsAccount(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsAccount::class,
            'logistics_account_id'
        );
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsFacility::class,
            'logistics_facility_id'
        );
    }

    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    public function isHubOperator(): bool
    {
        return $this->role === 'hub_operator';
    }
}