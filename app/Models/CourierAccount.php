<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CourierAccount extends Authenticatable
{
    protected $table = 'courier_accounts';

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
        'password',
        'vehicle_type',
        'plate_number',
        'orcr_path',
        'id_path',
        'account_status',
        'approved_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
    ];
}
