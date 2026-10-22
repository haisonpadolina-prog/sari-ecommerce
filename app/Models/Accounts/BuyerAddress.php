<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerAddress extends Model
{
    protected $fillable = [
        'buyer_account_id',
        'label',
        'recipient_name',
        'contact_no',
        'street_address',
        'barangay_name',
        'municipality_name',
        'province_name',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(BuyerAccount::class, 'buyer_account_id');
    }

    public function fullAddress(): string
    {
        return collect([
            $this->street_address,
            $this->barangay_name,
            $this->municipality_name,
            $this->province_name,
            $this->postal_code,
            'Philippines',
        ])->filter(fn ($part) => filled($part))->implode(', ');
    }
}
