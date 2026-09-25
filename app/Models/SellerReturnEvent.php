<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellerReturnEvent extends Model {
    protected $fillable=['seller_return_request_id','actor_role','actor_id','status','title','message'];
}