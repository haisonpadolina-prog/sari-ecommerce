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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SellerAccount extends Model
{
    protected $fillable = [
        'email',
        'store_name',
        'store_description',
        'store_phone',
        'store_public_email',
        'store_status',
        'pickup_address',
        'pickup_instructions',
        'store_logo_path',
        'store_banner_path',
        'warning_count',
        'account_status',
        'suspended_at',
        'suspended_until',
        'suspension_reason',
        'realtime_token',
    ];

    protected function casts(): array
    {
        return [
            'suspended_at' => 'datetime',
            'suspended_until' => 'datetime',
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
            $this->account_status === 'suspended'
            && $this->suspended_until
            && $this->suspended_until->isPast()
        ) {
            $servedThreeWarningSuspension = $this->warning_count >= 3;

            $this->update([
                'warning_count' => $servedThreeWarningSuspension
                    ? 0
                    : $this->warning_count,
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

        return $this->account_status === 'suspended'
            && $this->suspended_until?->isFuture();
    }
}
