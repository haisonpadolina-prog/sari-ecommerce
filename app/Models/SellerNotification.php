<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellerNotification extends Model {
    protected $fillable=['seller_account_id','type','title','message','action_url','data','read_at'];
    protected $casts=['data'=>'array','read_at'=>'datetime'];
}