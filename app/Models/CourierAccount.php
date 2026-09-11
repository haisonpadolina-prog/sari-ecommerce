<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CourierAccount extends Authenticatable
{
    protected $table = 'courier_accounts';

    protected $fillable = [
        'registration_application_id',
        'logistics_account_id',
        'last_name',
        'first_name',
        'middle_initial',
        'sex',
        'email',
        'contact_no',
        'birthday',
        'age',
        'province_code',
        'province_name',
        'municipality_code',
        'municipality_name',
        'barangay_code',
        'barangay_name',
        'street_address',
        'password',
        'vehicle_type',
        'plate_number',
        'orcr_path',
        'id_path',
        'account_status',
        'approved_at',
        'rating',
        'license_number',
        'vehicle_model',
        'availability_status',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }

    public function riderEarnings(): HasMany
    {
        return $this->hasMany(RiderEarning::class, 'courier_account_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(RiderPayoutRequest::class, 'courier_account_id');
    }
}
