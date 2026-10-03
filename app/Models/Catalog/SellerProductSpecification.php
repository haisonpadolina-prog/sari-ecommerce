<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class SellerProductSpecification extends Model
{
    protected $fillable = [
        'seller_product_id',
        'name',
        'value',
        'position',
    ];
}
