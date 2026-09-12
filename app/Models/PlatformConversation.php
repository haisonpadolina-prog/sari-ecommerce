<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlatformConversation extends Model
{
    protected $fillable = [
        'uuid',
        'conversation_type',
        'context_type',
        'context_id',
        'dedupe_key',
        'subject',
        'status',
        'created_by_role',
        'created_by_id',
        'last_message_at',
        'closed_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function participants(): HasMany
    {
        return $this->hasMany(
            PlatformConversationParticipant::class,
            'platform_conversation_id'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            PlatformMessage::class,
            'platform_conversation_id'
        );
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(
            PlatformMessage::class,
            'platform_conversation_id'
        )->latestOfMany();
    }
}
