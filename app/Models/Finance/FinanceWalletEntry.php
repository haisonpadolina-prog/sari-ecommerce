<?php

namespace App\Models\Finance;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceWalletEntry extends Model
{
    protected $fillable = [
        'finance_wallet_id',
        'marketplace_order_id',
        'idempotency_key',
        'entry_type',
        'category',
        'amount',
        'available_delta',
        'reserved_delta',
        'available_balance_after',
        'reserved_balance_after',
        'source_type',
        'source_id',
        'description',
        'metadata',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'available_delta' => 'decimal:2',
            'reserved_delta' => 'decimal:2',
            'available_balance_after' => 'decimal:2',
            'reserved_balance_after' => 'decimal:2',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(FinanceWallet::class, 'finance_wallet_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
