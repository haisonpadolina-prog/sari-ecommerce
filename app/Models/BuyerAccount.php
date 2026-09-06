<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerAccount extends Authenticatable
{
    protected $table = 'buyer_accounts';

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
        'id_path',
        'account_status',
        'approved_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
    ];

    public function cartItems(): HasMany
    {
        return $this->hasMany(BuyerCartItem::class, 'buyer_account_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'buyer_account_id');
    }

    public function sellerMessages(): HasMany
    {
        return $this->hasMany(BuyerSellerMessage::class, 'buyer_account_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'buyer_account_id');
    }
}
