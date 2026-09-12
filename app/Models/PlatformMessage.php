<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformMessage extends Model
{
    protected $fillable = [
        'platform_conversation_id',
        'sender_role',
        'sender_id',
        'message_type',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'metadata',
        'legacy_source',
        'legacy_id',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            PlatformConversation::class,
            'platform_conversation_id'
        );
    }
}
