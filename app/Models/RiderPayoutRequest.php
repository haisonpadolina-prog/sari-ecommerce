<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiderPayoutRequest extends Model
{
    protected $fillable = [
        'courier_account_id',
        'amount',
        'status',
        'admin_note',
        'reviewed_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierAccount::class, 'courier_account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RiderPayoutRequestItem::class, 'rider_payout_request_id');
    }
}
