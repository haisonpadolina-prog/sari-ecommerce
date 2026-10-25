<?php

namespace App\Models\Finance;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchRemittanceBatch extends Model
{
    protected $fillable = [
        'logistics_account_id',
        'origin_facility_id',
        'destination_facility_id',
        'batch_code',
        'origin_name_snapshot',
        'destination_name_snapshot',
        'expected_amount',
        'submitted_amount',
        'confirmed_amount',
        'difference_amount',
        'currency',
        'status',
        'branch_note',
        'main_branch_note',
        'prepared_by_type',
        'prepared_by_id',
        'verified_by_type',
        'verified_by_id',
        'submitted_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'submitted_amount' => 'decimal:2',
            'confirmed_amount' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }

    public function originFacility(): BelongsTo
    {
        return $this->belongsTo(LogisticsFacility::class, 'origin_facility_id');
    }

    public function destinationFacility(): BelongsTo
    {
        return $this->belongsTo(LogisticsFacility::class, 'destination_facility_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BranchRemittanceItem::class);
    }
}
