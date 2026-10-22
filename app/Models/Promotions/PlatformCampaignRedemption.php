<?php

namespace App\Models\Promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformCampaignRedemption extends Model
{
    protected $fillable = [
        'platform_campaign_id',
        'checkout_reference',
        'buyer_account_id',
        'buyer_social_account_id',
        'voucher_code',
        'discount_amount',
        'order_ids',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'order_ids' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PlatformCampaign::class, 'platform_campaign_id');
    }
}
