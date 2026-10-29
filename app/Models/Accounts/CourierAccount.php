<?php

namespace App\Models\Accounts;

use Laravel\Sanctum\HasApiTokens;

use App\Models\Finance\RiderEarning;
use App\Models\Finance\RiderPayoutRequest;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CourierAccount extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
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
        'license_expiry_date',
        'driver_license_path',
        'activation_token_hash',
        'activation_expires_at',
        'activated_at',
        'vehicle_model',
        'availability_status',
    ];

    protected $hidden = ['password', 'activation_token_hash'];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
        'license_expiry_date' => 'date',
        'activation_expires_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(LogisticsAccount::class, 'logistics_account_id');
    }

    public function registrationApplication(): BelongsTo
    {
        return $this->belongsTo(RegistrationApplication::class, 'registration_application_id');
    }

    public function riderEarnings(): HasMany
    {
        return $this->hasMany(RiderEarning::class, 'courier_account_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(RiderPayoutRequest::class, 'courier_account_id');
    }

    public function marketplaceOrders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'courier_email', 'email');
    }
}
