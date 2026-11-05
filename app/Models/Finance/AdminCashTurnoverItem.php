<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminCashTurnoverItem extends Model
{
    protected $fillable = [
        'admin_cash_turnover_id',
        'cod_remittance_batch_id',
        'expected_amount',
    ];

    protected function casts(): array
    {
        return ['expected_amount' => 'decimal:2'];
    }

    public function turnover(): BelongsTo
    {
        return $this->belongsTo(AdminCashTurnover::class, 'admin_cash_turnover_id');
    }

    public function codRemittanceBatch(): BelongsTo
    {
        return $this->belongsTo(CodRemittanceBatch::class, 'cod_remittance_batch_id');
    }
}
