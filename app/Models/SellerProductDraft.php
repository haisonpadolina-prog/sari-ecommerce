<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProductDraft extends Model
{
    protected $fillable = [
        'seller_account_id',
        'payload',
        'cover_image_path',
        'gallery_image_paths',
        'variant_image_paths',
    ];

    protected $casts = [
        'payload' => 'array',
        'gallery_image_paths' => 'array',
        'variant_image_paths' => 'array',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }
}
