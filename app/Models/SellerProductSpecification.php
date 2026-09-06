<?php

namespace App\Models;

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
