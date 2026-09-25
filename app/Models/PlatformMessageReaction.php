<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformMessageReaction extends Model
{
    protected $fillable = [
        'platform_message_id',
        'reactor_role',
        'reactor_id',
        'emoji',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(
            PlatformMessage::class,
            'platform_message_id'
        );
    }
}
