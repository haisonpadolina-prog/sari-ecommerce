<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerChatModerationAction extends Model
{
    protected $fillable = [
        'seller_account_id',
        'action_type',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }
}
