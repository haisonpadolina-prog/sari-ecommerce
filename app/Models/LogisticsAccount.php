<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LogisticsAccount extends Authenticatable
{
    protected $table = 'logistics_accounts';

    protected $fillable = [
        'registration_application_id',
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
        'business_name',
        'password',
        'id_path',
        'business_permit_path',
        'account_status',
        'approved_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
    ];

    public function riderApplications(): HasMany
    {
        return $this->hasMany(RegistrationApplication::class, 'logistics_account_id');
    }

    public function riders(): HasMany
    {
        return $this->hasMany(CourierAccount::class, 'logistics_account_id');
    }

    public function displayName(): string
    {
        $businessName = trim((string) $this->business_name);

        if ($businessName !== '') {
            return $businessName;
        }

        $name = trim($this->first_name . ' ' . $this->last_name);

        return $name !== '' ? $name : 'SARI Logistics';
    }
}
