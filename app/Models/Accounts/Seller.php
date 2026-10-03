<?php
namespace App\Models\Accounts;

use App\Models\Catalog\Product;
use App\Models\Orders\SellerOrder;
use App\Models\Shared\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = [
        'user_id','name','slug','description','logo_path','banner_path',
        'status','rejection_reason','commission_bps','pickup_address_id',
    ];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function pickupAddress(): BelongsTo { return $this->belongsTo(Address::class, 'pickup_address_id'); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orders(): HasMany { return $this->hasMany(SellerOrder::class); }
}
