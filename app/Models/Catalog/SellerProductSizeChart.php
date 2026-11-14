<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProductSizeChart extends Model
{
    protected $fillable = ['seller_product_id', 'unit', 'measurement_type', 'garment_type', 'fit', 'rows'];
    protected function casts(): array { return ['rows' => 'array']; }
    public function product(): BelongsTo { return $this->belongsTo(SellerProduct::class, 'seller_product_id'); }
}
