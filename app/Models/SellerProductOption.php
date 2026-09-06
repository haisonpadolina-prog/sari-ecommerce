<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProductOption extends Model
{
    protected $fillable = [
        'seller_product_id',
        'name',
        'position',
    ];

    public function values()
    {
        return $this->hasMany(SellerProductOptionValue::class, 'seller_product_option_id')
            ->orderBy('position');
    }
}
