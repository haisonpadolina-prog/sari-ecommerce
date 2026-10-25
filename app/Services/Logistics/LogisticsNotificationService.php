<?php

namespace App\Services\Logistics;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\LogisticsNotification;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Persists recipient-specific Logistics notifications.
 *
 * Call this service from an authorized workflow, preferably within the
 * workflow's existing DB::transaction(), after its state change succeeds.
 *
 * This service validates notification recipients. Authorization for the
 * user performing the original workflow action remains in its controller.
 */
final class LogisticsNotificationService
{
    /*
    |--------------------------------------------------------------------------
    | Allowed Notification Destinations
    |--------------------------------------------------------------------------
    */

    private const SHARED_ROUTES = [
        'logistics.dashboard',
        'logistics.pickup-requests',
        'logistics.delivery-assignment',
        'logistics.rider-management',
        'logistics.incoming-parcels',
        'logistics.parcel-sorting',
        'logistics.hub-transfers',
        'logistics.last-mile-assignment',
        'logistics.delivery-monitoring',
        'logistics.reports',
    ];

    private const MANAGER_ONLY_ROUTES = [
        'logistics.rider-applications',
        'logistics.facilities.index',
        'logistics.messages',
        'logistics.account-management',
    ];

    /*
    |--------------------------------------------------------------------------
    | Main Logistics Manager Notification
    |--------------------------------------------------------------------------
    */

    public function notifyManager(
        int $logisticsAccountId,
        string $eventKey,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionRoute = 'logistics.dashboard',
        array $data = []
    ): LogisticsNotification {
        $this->requireActiveProvider($logisticsAccountId);

        return $this->persist(
            logisticsAccountId: $logisticsAccountId,
            hubAccountId: null,
            recipientType: LogisticsNotification::RECIPIENT_MANAGER,
            recipientKey: LogisticsNotification::RECIPIENT_MANAGER,
            eventKey: $eventKey,
            type: $type,
            title: $title,
            message: $message,
            actionRoute: $actionRoute,
            data: $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Specific Hub Operator Notification
    |--------------------------------------------------------------------------
    |
    | Each operator has an independent notification mailbox.
    |
    */

    public function notifyHubOperator(
        int $hubAccountId,
        string $eventKey,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionRoute = 'logistics.dashboard',
        array $data = []
    ): LogisticsNotification {
        $operator = $this->requireActiveOperator($hubAccountId);

        return $this->persist(
            logisticsAccountId: (int) $operator->logistics_account_id,
            hubAccountId: (int) $operator->id,
            recipientType: LogisticsNotification::RECIPIENT_HUB_OPERATOR,
            recipientKey: 'hub:'.$operator->id,
            eventKey: $eventKey,
            type: $type,
            title: $title,
            message: $message,
            actionRoute: $actionRoute,
            data: $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Notify All Operators of One Facility
    |--------------------------------------------------------------------------
    |
    | Only active operators assigned to the specified facility receive
    | notifications. This does not create a Manager notification.
    |
    */

    public function notifyFacilityOperators(
        int $logisticsAccountId,
        int $facilityId,
        string $eventKey,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionRoute = 'logistics.dashboard',
        array $data = []
    ): Collection {
        $this->requireActiveProvider($logisticsAccountId);

        $facility = LogisticsFacility::query()
            ->whereKey($facilityId)
            ->where('logistics_account_id', $logisticsAccountId)
            ->where('is_active', true)
            ->first();

        if (! $facility) {
            throw new RuntimeException(
                'The notification facility is inactive or belongs to another Logistics provider.'
            );
        }

        $operators = LogisticsHubAccount::query()
            ->where('logistics_account_id', $logisticsAccountId)
            ->where('logistics_facility_id', $facility->id)
            ->where('role', 'hub_operator')
            ->where('account_status', 'active')
            ->orderBy('id')
            ->get();

        return $operators->map(
            fn (LogisticsHubAccount $operator): LogisticsNotification =>
                $this->persist(
                    logisticsAccountId: $logisticsAccountId,
                    hubAccountId: (int) $operator->id,
                    recipientType: LogisticsNotification::RECIPIENT_HUB_OPERATOR,
                    recipientKey: 'hub:'.$operator->id,
                    eventKey: $eventKey,
                    type: $type,
                    title: $title,
                    message: $message,
                    actionRoute: $actionRoute,
                    data: $data
                )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Notify an Order's Origin or Destination Hub
    |--------------------------------------------------------------------------
    |
    | Uses actual delivery_route_stops records.
    | Never guesses the recipient using Seller/Buyer address strings.
    |
    */

    public function notifyOrderHubOperators(
        MarketplaceOrder $order,
        string $stopKey,
        string $eventKey,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionRoute = 'logistics.dashboard',
        array $data = []
    ): Collection {
        if (
            ! in_array(
                $stopKey,
                ['origin_facility', 'destination_facility'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'Notifications can target only the actual origin or destination facility.'
            );
        }

        $providerId = (int) MarketplaceOrder::query()
            ->whereKey($order->getKey())
            ->value('logistics_account_id');

        if ($providerId <= 0) {
            throw new RuntimeException(
                'Order has no assigned Logistics provider.'
            );
        }

        $facilityId = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->getKey())
            ->where('stop_key', $stopKey)
            ->value('logistics_facility_id');

        /*
         * Missing facility assignment:
         * Do not notify a random or default hub.
         */

        if (! $facilityId) {
            return collect();
        }

        return $this->notifyFacilityOperators(
            logisticsAccountId: $providerId,
            facilityId: (int) $facilityId,
            eventKey: $eventKey,
            type: $type,
            title: $title,
            message: $message,
            actionRoute: $actionRoute,
            data: $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Parent Logistics Provider
    |--------------------------------------------------------------------------
    */

    private function requireActiveProvider(
        int $providerId
    ): LogisticsAccount {
        if ($providerId <= 0) {
            throw new InvalidArgumentException(
                'A valid Logistics provider ID is required.'
            );
        }

        $provider = LogisticsAccount::query()
            ->whereKey($providerId)
            ->where('account_status', 'active')
            ->first();

        if (! $provider) {
            throw new RuntimeException(
                'The Logistics provider is not active or does not exist.'
            );
        }

        return $provider;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Hub Operator and Assigned Facility
    |--------------------------------------------------------------------------
    */

    private function requireActiveOperator(
        int $operatorId
    ): LogisticsHubAccount {
        if ($operatorId <= 0) {
            throw new InvalidArgumentException(
                'A valid Hub Operator ID is required.'
            );
        }

        $operator = LogisticsHubAccount::query()
            ->with(['facility', 'logisticsAccount'])
            ->whereKey($operatorId)
            ->where('role', 'hub_operator')
            ->where('account_status', 'active')
            ->first();

        $facility = $operator?->facility;

        $provider = $operator?->logisticsAccount;

        if (
            ! $operator
            || ! $facility
            || ! $facility->is_active
            || ! $provider
            || $provider->account_status !== 'active'
            || (int) $facility->id !== (int) $operator->logistics_facility_id
            || (int) $facility->logistics_account_id
                !== (int) $operator->logistics_account_id
            || (int) $provider->id
                !== (int) $operator->logistics_account_id
        ) {
            throw new RuntimeException(
                'The recipient Hub Operator has no valid active hub assignment.'
            );
        }

        return $operator;
    }

    /*
    |--------------------------------------------------------------------------
    | Persist Notification With Duplicate Protection
    |--------------------------------------------------------------------------
    |
    | The unique database index uses:
    |
    | logistics_account_id + recipient_key + event_key
    |
    | Repeating the same event for the same recipient returns the
    | existing notification without resetting its read_at value.
    |
    */

    private function persist(
        int $logisticsAccountId,
        ?int $hubAccountId,
        string $recipientType,
        string $recipientKey,
        string $eventKey,
        string $type,
        string $title,
        ?string $message,
        ?string $actionRoute,
        array $data
    ): LogisticsNotification {
        $eventKey = trim($eventKey);

        $type = trim($type);

        $title = trim($title);

        /*
        |--------------------------------------------------------------------------
        | Validate Notification Fields
        |--------------------------------------------------------------------------
        */

        if (
            $eventKey === ''
            || mb_strlen($eventKey) > 100
        ) {
            throw new InvalidArgumentException(
                'eventKey must contain 1–100 characters.'
            );
        }

        if (
            ! preg_match('/^[a-z][a-z0-9_]*$/', $type)
            || strlen($type) > 50
        ) {
            throw new InvalidArgumentException(
                'Invalid notification type. Use lowercase snake_case.'
            );
        }

        if (
            $title === ''
            || mb_strlen($title) > 160
        ) {
            throw new InvalidArgumentException(
                'Notification title must contain 1–160 characters.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Restrict Notification Destinations
        |--------------------------------------------------------------------------
        |
        | Hub Operators cannot be redirected to Manager-only modules.
        | Arbitrary external URLs are not accepted.
        |
        */

        $routes = $recipientType
            === LogisticsNotification::RECIPIENT_MANAGER

            ? array_merge(
                self::SHARED_ROUTES,
                self::MANAGER_ONLY_ROUTES
            )

            : self::SHARED_ROUTES;

        if (
            $actionRoute !== null
            && ! in_array($actionRoute, $routes, true)
        ) {
            throw new InvalidArgumentException(
                'Notification action route is not allowed for this recipient.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Unique Notification Identity
        |--------------------------------------------------------------------------
        */

        $identity = [
            'logistics_account_id' => $logisticsAccountId,
            'recipient_key' => $recipientKey,
            'event_key' => $eventKey,
        ];

        /*
        |--------------------------------------------------------------------------
        | Create or Return Existing Record
        |--------------------------------------------------------------------------
        */

        return LogisticsNotification::query()->createOrFirst(
            $identity,
            [
                'logistics_hub_account_id' => $hubAccountId,

                'recipient_type' => $recipientType,

                'type' => $type,

                'title' => $title,

                'message' => $message,

                'action_route' => $actionRoute,

                'action_params' => [],

                'data' => $data,
            ]
        );
    }
}
