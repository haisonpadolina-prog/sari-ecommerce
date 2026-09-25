<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellerInventoryMovement extends Model {
    protected $fillable=['seller_account_id','seller_product_id','seller_product_variant_id','quantity_before','quantity_after','quantity_delta','reason','note'];
}