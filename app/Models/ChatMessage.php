<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'seller_account_id',
        'sender_role',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'read_by_seller_at',
        'read_by_admin_at',
    ];

    protected function casts(): array
    {
        return [
            'attachment_size' => 'integer',
            'read_by_seller_at' => 'datetime',
            'read_by_admin_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerAccount::class, 'seller_account_id');
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    public function attachmentIsImage(): bool
    {
        return str_starts_with((string) $this->attachment_mime, 'image/');
    }
}
