<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchRemittanceItem extends Model
{
    protected $fillable = [
        'branch_remittance_batch_id',
        'cod_remittance_batch_id',
        'expected_amount',
        'branch_received_at',
        'central_released_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'branch_received_at' => 'datetime',
            'central_released_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BranchRemittanceBatch::class, 'branch_remittance_batch_id');
    }

    public function codRemittanceBatch(): BelongsTo
    {
        return $this->belongsTo(CodRemittanceBatch::class, 'cod_remittance_batch_id');
    }
}
