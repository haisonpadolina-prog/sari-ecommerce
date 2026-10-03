<?php
namespace App\Models\Catalog;
use Illuminate\Database\Eloquent\Model;
class SellerReviewReply extends Model {
    protected $fillable=['product_review_id','seller_account_id','reply'];
}