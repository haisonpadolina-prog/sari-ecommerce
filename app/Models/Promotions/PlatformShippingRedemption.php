<?php

namespace App\Models\Promotions;

use Illuminate\Database\Eloquent\Model;

class PlatformShippingRedemption extends Model
{
    protected $fillable = [
        'platform_campaign_id', 'marketplace_order_id', 'checkout_reference',
        'buyer_account_id', 'buyer_social_account_id', 'amount_cents', 'state',
        'policy_snapshot', 'used_at', 'released_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer', 'policy_snapshot' => 'array',
        'used_at' => 'datetime', 'released_at' => 'datetime',
    ];
}
