<?php

namespace App\Models\Accounts;

use App\Models\Catalog\SellerProduct;
use App\Models\Compliance\ComplianceMessage;
use App\Models\Compliance\SellerWarning;
use App\Models\Finance\OrderCommission;
use App\Models\Finance\SellerSettlement;
use App\Models\Messaging\ChatMessage;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\SellerNotification;
use App\Models\Promotions\SellerVoucher;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SellerAccount extends Model
{
    use SoftDeletes;
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
        'profile_image_path',
        'store_name',
        'line_of_business',
        'store_description',
        'store_phone',
        'store_public_email',
        'store_status',
        'pickup_address',
        'pickup_instructions',
        'id_path',
        'business_permit_path',
        'registration_status',
        'approved_at',
        'warning_count',
        'account_status',
        'suspended_at',
        'suspended_until',
        'suspension_reason',
        'banned_at',
        'ban_reason',
        'deactivated_at',
        'deactivation_reason',
        'realtime_token',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'suspended_until' => 'datetime',
            'banned_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(SellerProduct::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(SellerWarning::class);
    }

    public function complianceMessages(): HasMany
    {
        return $this->hasMany(ComplianceMessage::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'seller_account_id');
    }

    public function orderCommissions(): HasMany
    {
        return $this->hasMany(OrderCommission::class, 'seller_account_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(SellerSettlement::class, 'seller_account_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(SellerVoucher::class, 'seller_account_id');
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(SellerReturnRequest::class, 'seller_account_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(SellerNotification::class, 'seller_account_id');
    }

    public function ensureRealtimeToken(): string
    {
        if (!$this->realtime_token) {
            $this->forceFill([
                'realtime_token' => Str::random(64),
            ])->save();
        }

        return $this->realtime_token;
    }

    public function refreshSuspensionStatus(): void
    {
        if (
            $this->account_status === 'suspended' &&
            $this->suspended_until &&
            $this->suspended_until->isPast()
        ) {
            $servedThreeWarningSuspension = $this->warning_count >= 3;

            $this->update([
                'warning_count' => $servedThreeWarningSuspension ? 0 : $this->warning_count,
                'account_status' => $servedThreeWarningSuspension
                    ? 'active'
                    : ($this->warning_count > 0 ? 'warning' : 'active'),
                'suspended_at' => null,
                'suspended_until' => null,
                'suspension_reason' => null,
            ]);
        }
    }

    public function isSuspended(): bool
    {
        $this->refreshSuspensionStatus();

        return $this->account_status === 'suspended' &&
            $this->suspended_until?->isFuture();
    }
}
