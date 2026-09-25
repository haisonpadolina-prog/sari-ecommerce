<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class SellerReturnRequest extends Model {
    protected $fillable=['marketplace_order_id','seller_account_id','buyer_account_id','buyer_social_account_id','reason','details','requested_amount','approved_amount','status','seller_response','reviewed_at','returned_at','refunded_at'];
    protected $casts=['requested_amount'=>'decimal:2','approved_amount'=>'decimal:2','reviewed_at'=>'datetime','returned_at'=>'datetime','refunded_at'=>'datetime'];
    public function order(): BelongsTo { return $this->belongsTo(MarketplaceOrder::class,'marketplace_order_id'); }
    public function events(): HasMany { return $this->hasMany(SellerReturnEvent::class); }
}