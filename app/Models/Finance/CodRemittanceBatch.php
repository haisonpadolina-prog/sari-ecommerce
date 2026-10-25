<?php

namespace App\Models\Finance;

use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CodRemittanceBatch extends Model
{
    protected $fillable = [
        'logistics_account_id',
        'logistics_facility_id',
        'courier_account_id',
        'batch_code',
        'hub_name_snapshot',
        'hub_address_snapshot',
        'expected_amount',
        'submitted_amount',
        'confirmed_amount',
        'difference_amount',
        'currency',
        'remittance_method',
        'status',
        'rider_reference',
        'logistics_reference',
        'rider_note',
        'logistics_note',
        'verified_by_type',
        'verified_by_id',
        'submitted_at',
        'verified_at',
        'central_reconciled_at',
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
            'central_reconciled_at' => 'datetime',
        ];
    }

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }


    public function facility(): BelongsTo
    {
        return $this->belongsTo(LogisticsFacility::class, 'logistics_facility_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierAccount::class, 'courier_account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CodRemittanceItem::class);
    }

    public function branchRemittanceItem(): HasOne
    {
        return $this->hasOne(BranchRemittanceItem::class, 'cod_remittance_batch_id');
    }
}
