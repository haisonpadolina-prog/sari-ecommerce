<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderPayoutRequestItem extends Model
{
    protected $fillable = [
        'rider_payout_request_id',
        'rider_earning_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(RiderPayoutRequest::class, 'rider_payout_request_id');
    }

    public function earning(): BelongsTo
    {
        return $this->belongsTo(RiderEarning::class, 'rider_earning_id');
    }
}
