<?php

namespace App\Models\Platform;

use App\Models\Accounts\AdminAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformAnnouncement extends Model
{
    protected $fillable = [
        'admin_account_id',
        'title',
        'message',
        'image_path',
        'audiences',
        'severity',
        'status',
        'action_label',
        'action_url',
        'starts_at',
        'ends_at',
        'published_at',
        'paused_at',
    ];

    protected $casts = [
        'audiences' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
        'paused_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminAccount::class, 'admin_account_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(PlatformAnnouncementRead::class, 'platform_announcement_id');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $q): void {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q): void {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    public function scopeForAudience(Builder $query, string $role): Builder
    {
        return $query->whereJsonContains('audiences', $role);
    }

    public function displayState(): string
    {
        if ($this->status === 'draft') {
            return 'Draft';
        }

        if ($this->status === 'paused') {
            return 'Paused';
        }

        if ($this->status !== 'published') {
            return ucfirst((string) $this->status);
        }

        if ($this->starts_at?->isFuture()) {
            return 'Scheduled';
        }

        if ($this->ends_at?->isPast()) {
            return 'Ended';
        }

        return 'Live';
    }
}
