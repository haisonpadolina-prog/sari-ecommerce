<?php

namespace App\Models\Messaging;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerSellerMessage extends Model
{
    protected $fillable = [
        'seller_account_id',
        'buyer_account_id',
        'buyer_social_account_id',
        'marketplace_order_id',
        'message_type',
        'sender_role',
        'body',
        'attachment_disk',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(BuyerAccount::class, 'buyer_account_id');
    }

    public function socialBuyer(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'buyer_social_account_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
