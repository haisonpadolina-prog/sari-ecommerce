<?php

namespace App\Models\Finance;

use App\Models\Accounts\AdminAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancePayoutRequest extends Model
{
    protected $fillable = [
        'finance_wallet_id',
        'request_code',
        'owner_type',
        'owner_id',
        'amount',
        'currency',
        'payout_method',
        'destination_label',
        'destination_details',
        'status',
        'admin_note',
        'reviewed_by_admin_id',
        'external_reference',
        'requested_at',
        'reviewed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'destination_details' => 'encrypted:array',
            'requested_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(FinanceWallet::class, 'finance_wallet_id');
    }

    public function reviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'reviewed_by_admin_id');
    }
}
