<?php

namespace App\Models\Finance;

use App\Models\Accounts\AdminAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminCashTurnoverCount extends Model
{
    protected $fillable = [
        'admin_cash_turnover_id',
        'admin_account_id',
        'confirmed_amount',
        'difference_amount',
        'result',
        'note',
        'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_amount' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'counted_at' => 'datetime',
        ];
    }

    public function turnover(): BelongsTo
    {
        return $this->belongsTo(AdminCashTurnover::class, 'admin_cash_turnover_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'admin_account_id');
    }
}
