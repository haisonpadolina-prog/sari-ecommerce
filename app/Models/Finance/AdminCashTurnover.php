<?php

namespace App\Models\Finance;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminCashTurnover extends Model
{
    protected $fillable = [
        'logistics_account_id',
        'main_facility_id',
        'turnover_code',
        'expected_amount',
        'submitted_amount',
        'confirmed_amount',
        'difference_amount',
        'currency',
        'status',
        'logistics_note',
        'admin_note',
        'prepared_by_logistics_account_id',
        'received_by_admin_account_id',
        'submitted_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'submitted_amount' => 'decimal:2',
            'confirmed_amount' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }

    public function mainFacility(): BelongsTo
    {
        return $this->belongsTo(LogisticsFacility::class, 'main_facility_id');
    }

    public function receivedByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'received_by_admin_account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AdminCashTurnoverItem::class);
    }

    public function counts(): HasMany
    {
        return $this->hasMany(AdminCashTurnoverCount::class);
    }
}
