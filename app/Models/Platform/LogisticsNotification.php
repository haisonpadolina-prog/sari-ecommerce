<?php

namespace App\Models\Platform;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\LogisticsHubAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class LogisticsNotification extends Model
{
    public const RECIPIENT_MANAGER = 'manager';
    public const RECIPIENT_HUB_OPERATOR = 'hub_operator';

    protected $table = 'logistics_notifications';

    protected $fillable = [
        'logistics_account_id',
        'logistics_hub_account_id',
        'recipient_type',
        'recipient_key',
        'type',
        'title',
        'message',
        'action_route',
        'action_params',
        'data',
        'event_key',
        'read_at',
    ];

    protected $casts = [
        'logistics_account_id' => 'integer',
        'logistics_hub_account_id' => 'integer',
        'action_params' => 'array',
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function logisticsAccount(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsAccount::class,
            'logistics_account_id'
        );
    }

    public function hubAccount(): BelongsTo
    {
        return $this->belongsTo(
            LogisticsHubAccount::class,
            'logistics_hub_account_id'
        );
    }

    /**
     * Scope ALL mailbox reads and updates to an exact recipient.
     *
     * A null Hub Operator ID selects the Manager mailbox.
     * It must never mean "all Hub Operators".
     */
    public function scopeForRecipient(
        Builder $query,
        int $logisticsAccountId,
        ?int $hubAccountId = null
    ): Builder {
        if ($logisticsAccountId <= 0) {
            throw new InvalidArgumentException(
                'A valid Logistics provider ID is required.'
            );
        }

        $query->where(
            'logistics_account_id',
            $logisticsAccountId
        );

        /*
        |--------------------------------------------------------------------------
        | Main Logistics Manager Mailbox
        |--------------------------------------------------------------------------
        */

        if ($hubAccountId === null) {
            return $query
                ->whereNull('logistics_hub_account_id')
                ->where(
                    'recipient_type',
                    self::RECIPIENT_MANAGER
                )
                ->where(
                    'recipient_key',
                    self::RECIPIENT_MANAGER
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Specific Hub Operator Mailbox
        |--------------------------------------------------------------------------
        */

        if ($hubAccountId <= 0) {
            throw new InvalidArgumentException(
                'A valid Hub Operator ID is required.'
            );
        }

        return $query
            ->where(
                'logistics_hub_account_id',
                $hubAccountId
            )
            ->where(
                'recipient_type',
                self::RECIPIENT_HUB_OPERATOR
            )
            ->where(
                'recipient_key',
                'hub:'.$hubAccountId
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Read State
    |--------------------------------------------------------------------------
    */

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
