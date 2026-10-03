<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class SellerProductOptionValue extends Model
{
    protected $fillable = [
        'seller_product_option_id',
        'value',
        'position',
    ];
}
