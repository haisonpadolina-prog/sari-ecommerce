<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductModerationLog extends Model
{
    protected $fillable = [
        'seller_product_id',
        'seller_account_id',
        'source',
        'decision',
        'risk',
        'risk_score',
        'engine',
        'matched_rules',
        'matched_terms',
        'ai_flagged',
        'ai_categories',
        'ai_scores',
        'reason',
    ];

    protected $casts = [
        'matched_rules' => 'array',
        'matched_terms' => 'array',
        'ai_flagged' => 'boolean',
        'ai_categories' => 'array',
        'ai_scores' => 'array',
        'risk_score' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class, 'seller_product_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }
}
