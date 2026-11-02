<?php

namespace App\Models\Delivery;

use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryProof extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'courier_account_id',
        'last_mile_assignment_id',
        'proof_type',
        'photo_path',
        'recipient_name',
        'notes',
        'latitude',
        'longitude',
        'captured_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'captured_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierAccount::class, 'courier_account_id');
    }
}
