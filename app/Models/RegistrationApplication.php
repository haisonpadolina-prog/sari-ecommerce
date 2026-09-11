<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationApplication extends Model
{
    protected $fillable = [
        'role',
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
        'business_name',
        'line_of_business',
        'vehicle_type',
        'plate_number',
        'id_path',
        'business_permit_path',
        'orcr_path',
        'status',
        'admin_note',
        'reviewed_at',
        'approved_at',
        'rejected_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthday' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }

    public function fullName(): string
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_initial ? $this->middle_initial . '. ' : '') .
            $this->last_name
        );
    }
}
