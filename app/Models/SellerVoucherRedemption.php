<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellerVoucherRedemption extends Model {
    protected $fillable=['seller_voucher_id','marketplace_order_id','buyer_account_id','buyer_social_account_id','discount_amount'];
    protected $casts=['discount_amount'=>'decimal:2'];
}