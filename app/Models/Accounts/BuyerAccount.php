<?php

namespace App\Models\Accounts;

use Laravel\Sanctum\HasApiTokens;

use App\Models\Catalog\ProductReview;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\BuyerCartItem;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerAccount extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    protected $table = 'buyer_accounts';

    protected $fillable = [
        'profile_image_path',
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

    public function registrationApplication(): BelongsTo
    {
        return $this->belongsTo(RegistrationApplication::class, 'registration_application_id');
    }

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

    public function addresses(): HasMany
    {
        return $this->hasMany(BuyerAddress::class, 'buyer_account_id');
    }

}
