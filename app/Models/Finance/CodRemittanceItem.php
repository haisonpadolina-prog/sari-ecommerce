<?php

namespace App\Models\Finance;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodRemittanceItem extends Model
{
    protected $fillable = [
        'cod_remittance_batch_id',
        'marketplace_order_id',
        'expected_amount',
        'collected_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'collected_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CodRemittanceBatch::class, 'cod_remittance_batch_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
