<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceWallet extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'currency',
        'available_balance',
        'reserved_balance',
        'lifetime_credits',
        'lifetime_payouts',
    ];

    protected function casts(): array
    {
        return [
            'available_balance' => 'decimal:2',
            'reserved_balance' => 'decimal:2',
            'lifetime_credits' => 'decimal:2',
            'lifetime_payouts' => 'decimal:2',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FinanceWalletEntry::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(FinancePayoutRequest::class);
    }
}
