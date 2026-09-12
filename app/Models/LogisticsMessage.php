<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsMessage extends Model
{
    protected $fillable = ['courier_account_id', 'logistics_account_id', 'sender_role', 'body', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierAccount::class, 'courier_account_id');
    }

    public function logisticsAccount(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }
}
