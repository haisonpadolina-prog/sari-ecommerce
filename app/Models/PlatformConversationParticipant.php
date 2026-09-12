<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformConversationParticipant extends Model
{
    protected $fillable = [
        'platform_conversation_id',
        'participant_role',
        'participant_id',
        'joined_at',
        'last_read_message_id',
        'muted_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'muted_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            PlatformConversation::class,
            'platform_conversation_id'
        );
    }

    public function lastReadMessage(): BelongsTo
    {
        return $this->belongsTo(
            PlatformMessage::class,
            'last_read_message_id'
        );
    }
}
