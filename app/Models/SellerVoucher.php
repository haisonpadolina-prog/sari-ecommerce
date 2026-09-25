<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class SellerVoucher extends Model {
    protected $fillable=['seller_account_id','code','name','discount_type','discount_value','minimum_spend','maximum_discount','usage_limit','used_count','starts_at','ends_at','is_active'];
    protected $casts=['discount_value'=>'decimal:2','minimum_spend'=>'decimal:2','maximum_discount'=>'decimal:2','starts_at'=>'datetime','ends_at'=>'datetime','is_active'=>'boolean'];
    public function seller(): BelongsTo { return $this->belongsTo(SellerAccount::class,'seller_account_id'); }
    public function redemptions(): HasMany { return $this->hasMany(SellerVoucherRedemption::class); }
    public function isUsableAt($at=null): bool {
        $at=$at ?: now();
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->lte($at))
            && (!$this->ends_at || $this->ends_at->gte($at))
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}