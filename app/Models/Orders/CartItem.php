<?php
namespace App\Models\Orders;

use App\Models\Cart;
use App\Models\Catalog\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id','product_variant_id','quantity','selected'];

    protected function casts(): array
    {
        return ['selected' => 'boolean'];
    }

    public function cart(): BelongsTo { return $this->belongsTo(Cart::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
