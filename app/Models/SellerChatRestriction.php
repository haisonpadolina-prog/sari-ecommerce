<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerChatRestriction extends Model
{
    protected $fillable = [
        'seller_account_id',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'unblocked_at',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
        'unblocked_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }
}
