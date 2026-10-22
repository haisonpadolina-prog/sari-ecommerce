<?php

namespace App\Models\Accounts;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAccount extends Model
{
    protected $fillable = [
        'provider',
        'provider_user_id',
        'email',
        'name',
        'avatar_url',
        'last_login_at',
        'account_status',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'buyer_social_account_id');
    }
}
