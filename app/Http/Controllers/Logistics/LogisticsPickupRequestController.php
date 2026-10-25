<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsPickupRequestController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsOrderAccess $orderAccess,
        private readonly LogisticsNotificationService $notifications,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Pickup Request Queue
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $query = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email');

        /*
        |--------------------------------------------------------------------------
        | Hub-Level Queue Visibility
        |--------------------------------------------------------------------------
        */

        $orders = CurrentLogisticsHub::scopeOrders(
            $request,
            $query,
            'origin'
        )
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pickup Queue States
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {
            $issues = $this->pickupIssues($order);

            if (
                $order->logisticsParcel
                && $order->logisticsParcel->status !== 'pickup_verified'
            ) {
                $issues[] = 'Parcel has already entered the Logistics intake flow. Review its status.';
            }

            $order->setAttribute(
                'pickup_issues',
                $issues
            );

            $order->setAttribute(
                'pickup_queue_state',
                $issues
                    ? 'attention'
                    : (
                        $order->logisticsParcel?->status === 'pickup_verified'
                            ? 'verified'
                            : 'pending'
                    )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Queue Statistics
        |--------------------------------------------------------------------------
        */

        $verified = $orders->where(
            'pickup_queue_state',
            'verified'
        );

        $stats = [
            'total' => $orders->count(),

            'pending' => $orders
                ->where('pickup_queue_state', 'pending')
                ->count(),

            'verified' => $verified->count(),

            'attention' => $orders
                ->where('pickup_queue_state', 'attention')
                ->count(),

            'batches' => $verified
                ->groupBy(
                    fn (MarketplaceOrder $order): string =>
                        $this->pickupGroupKey($order)
                )
                ->count(),
        ];

        return view(
            'logistics.pickup-requests',
            compact('orders', 'stats')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Single Pickup Verification
    |--------------------------------------------------------------------------
    */

    public function verify(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Authorize Selected Order
        |--------------------------------------------------------------------------
        */

        if (CurrentLogisticsHub::hasOperatingFacility($request)) {

            CurrentLogisticsHub::authorizeOrder(
                $request,
                $order,
                'origin'
            );

            $owned = $order;

        } else {

            $owned = $this->orderAccess->ownedOrder(
                $request,
                $order
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reuse Secure Batch Verification
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'order_ids' => [$owned->id],
        ]);

        return $this->verifyBatch($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Batch Pickup Verification
    |--------------------------------------------------------------------------
    */

    public function verifyBatch(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Request Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'order_ids' => [
                'required',
                'array',
                'min:1',
                'max:200',
            ],

            'order_ids.*' => [
                'required',
                'integer',
                'distinct',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Resolve Authenticated Logistics Identity
        |--------------------------------------------------------------------------
        */

        $logistics = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator(
            $request
        );

        $hubAccount = $isHubOperator
            ? CurrentLogisticsHub::requireAccount($request)
            : null;

        $hubFacility = CurrentLogisticsHub::requireOperatingFacility($request);

        $ids = collect($validated['order_ids'])
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Transactional Pickup Verification
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use (
            $request,
            $ids,
            $logistics,
            $hubAccount,
            $hubFacility
        ): array {

            /*
            |--------------------------------------------------------------------------
            | Lock Selected Orders
            |--------------------------------------------------------------------------
            */

            $query = MarketplaceOrder::query()
                ->whereIn('id', $ids)
                ->where(
                    'logistics_account_id',
                    $logistics->id
                );

            /*
            |--------------------------------------------------------------------------
            | Reapply Hub Authorization
            |--------------------------------------------------------------------------
            */

            $orders = CurrentLogisticsHub::scopeOrders(
                $request,
                $query,
                'origin'
            )
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($orders->count() !== $ids->count()) {

                throw ValidationException::withMessages([
                    'order_ids' =>
                        'One or more pickup requests are unavailable '
                        .'for your Logistics account or assigned origin '
                        .'hub. Refresh the queue.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Lock Origin Route Stops
            |--------------------------------------------------------------------------
            */

            $origins = DeliveryRouteStop::query()
                ->whereIn(
                    'marketplace_order_id',
                    $ids
                )
                ->where(
                    'stop_key',
                    'origin_facility'
                )
                ->orderBy('marketplace_order_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('marketplace_order_id');

            /*
            |--------------------------------------------------------------------------
            | Load Route Data and Lock Existing Parcels
            |--------------------------------------------------------------------------
            */

            $orders->load('deliveryRouteStops.facility');

            $parcels = LogisticsParcel::query()
                ->whereIn(
                    'marketplace_order_id',
                    $ids
                )
                ->orderBy('marketplace_order_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('marketplace_order_id');

            /*
            |--------------------------------------------------------------------------
            | Resolve Actual Verifying Operator
            |--------------------------------------------------------------------------
            */

            $actor = $hubAccount
                ? (
                    trim((string) $hubAccount->name)
                    ?: 'Hub Operator'
                )
                    .' ('
                    .($hubFacility?->name ?: 'Assigned Hub')
                    .')'
                : $logistics->displayName();

            /*
            |--------------------------------------------------------------------------
            | Verification Tracking
            |--------------------------------------------------------------------------
            |
            | Count only newly verified parcels.
            |
            | Facility IDs are collected from the actual successfully
            | verified orders, not from browser-supplied facility IDs.
            |
            */

            $count = 0;

            $verifiedFacilityIds = [];

            /*
            |--------------------------------------------------------------------------
            | Process Selected Orders
            |--------------------------------------------------------------------------
            */

            foreach ($orders as $order) {

                $origin = $origins->get($order->id);

                /*
                |--------------------------------------------------------------------------
                | Hub Ownership Recheck
                |--------------------------------------------------------------------------
                */

                if (
                    $hubFacility
                    && (
                        ! $origin
                        || (
                            (int) $origin->logistics_facility_id
                            !== (int) $hubFacility->id
                        )
                    )
                ) {

                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'A selected pickup request is no longer '
                            .'assigned to your origin hub. Refresh the queue.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Order State Validation
                |--------------------------------------------------------------------------
                */

                if (
                    $order->status !== 'ready_for_pickup'
                    || filled($order->courier_email)
                ) {

                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .' is no longer available for verification. '
                            .'Refresh the queue.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Pickup Address and Facility Validation
                |--------------------------------------------------------------------------
                */

                $issues = $this->pickupIssues($order);

                if ($issues) {

                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .': '.$issues[0],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Existing Parcel State
                |--------------------------------------------------------------------------
                */

                $parcel = $parcels->get($order->id);

                /*
                 * Already verified parcels must not generate
                 * duplicate workflow events, notifications, or logs.
                 */

                if ($parcel?->status === 'pickup_verified') {
                    continue;
                }

                if ($parcel) {

                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .' has already entered the Logistics intake flow.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Verified Logistics Parcel
                |--------------------------------------------------------------------------
                */

                LogisticsParcel::query()->create([
                    'marketplace_order_id' => $order->id,
                    'status' => 'pickup_verified',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Record Seller and Buyer Workflow Event
                |--------------------------------------------------------------------------
                */

                $this->workflow->record(
                    $order,
                    'both',
                    'pickup_verified',
                    'Pickup Request Verified',
                    $actor
                        .' verified the Seller pickup request for order '
                        .$order->order_number
                        .'. It is now ready for Rider assignment.'
                );

                /*
                |--------------------------------------------------------------------------
                | Persist Notifications
                |--------------------------------------------------------------------------
                |
                | Notifications remain inside the same database
                | transaction as the original pickup verification.
                |
                */

                $eventKey = 'pickup_verified:order:'.$order->id;

                $notificationMessage = $actor
                    .' verified the pickup request for order '
                    .$order->order_number
                    .'. It is ready for Rider assignment.';

                $notificationData = [
                    'order_id' => (int) $order->id,

                    'order_number' => (string) $order->order_number,

                    'origin_facility_id' =>
                        (int) $origin->logistics_facility_id,
                ];

                /*
                |--------------------------------------------------------------------------
                | Main Logistics Notification
                |--------------------------------------------------------------------------
                */

                $this->notifications->notifyManager(
                    logisticsAccountId: (int) $logistics->id,
                    eventKey: $eventKey,
                    type: 'pickup_verified',
                    title: 'Pickup request verified',
                    message: $notificationMessage,
                    actionRoute: 'logistics.delivery-assignment',
                    data: $notificationData
                );

                /*
                |--------------------------------------------------------------------------
                | Origin Hub Notification
                |--------------------------------------------------------------------------
                */

                $this->notifications->notifyFacilityOperators(
                    logisticsAccountId: (int) $logistics->id,
                    facilityId: (int) $origin->logistics_facility_id,
                    eventKey: $eventKey,
                    type: 'pickup_verified',
                    title: 'Pickup ready for Rider assignment',
                    message: $notificationMessage,
                    actionRoute: 'logistics.delivery-assignment',
                    data: $notificationData
                );

                /*
                |--------------------------------------------------------------------------
                | Track Newly Verified Order
                |--------------------------------------------------------------------------
                */

                $count++;

                $verifiedFacilityIds[] =
                    (int) $origin->logistics_facility_id;
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve Actual Affected Hub
            |--------------------------------------------------------------------------
            |
            | One hub:
            | Store its facility ID in the activity record.
            |
            | Multiple hubs:
            | Keep the batch as a network-wide activity for the manager.
            |
            | Already-verified orders do not affect this calculation.
            |
            */

            $uniqueFacilityIds = array_values(
                array_unique($verifiedFacilityIds)
            );

            return [
                'count' => $count,

                'facility_id' => count($uniqueFacilityIds) === 1
                    ? $uniqueFacilityIds[0]
                    : null,
            ];

        }, 3);

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | This code executes only after the database transaction
        | has completed successfully.
        |
        | The middleware checks these server-side attributes.
        |
        | activity_changed:
        |     true  = at least one newly verified pickup
        |     false = no actual changes
        |
        | activity_facility_id:
        |     Actual hub ID when all newly verified parcels
        |     belong to the same origin hub.
        |
        */

        $count = $result['count'];

        $request->attributes->set(
            'activity_changed',
            $count > 0
        );

        if ($result['facility_id'] !== null) {

            $request->attributes->set(
                'activity_facility_id',
                $result['facility_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            $count > 0
                ? $count.' pickup '
                    .(
                        $count === 1
                            ? 'request verified'
                            : 'requests verified'
                    )
                    .'. Continue to Pickup Assignment.'
                : 'These pickup requests are already verified. '
                    .'Continue to Pickup Assignment.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Seller Pickup Batch Grouping
    |--------------------------------------------------------------------------
    */

    private function pickupGroupKey(MarketplaceOrder $order): string
    {
        $origin = $order->deliveryRouteStops->firstWhere(
            'stop_key',
            'origin_facility'
        );

        $seller = $order->seller_account_id
            ? 'seller:'.$order->seller_account_id
            : 'order:'.$order->id;

        return json_encode([
            $seller,

            $this->normalizePickupText(
                $order->pickup_address
            ),

            $origin?->logistics_facility_id,

        ], JSON_THROW_ON_ERROR);
    }

    /*
    |--------------------------------------------------------------------------
    | Pickup Validation Issues
    |--------------------------------------------------------------------------
    */

    private function pickupIssues(MarketplaceOrder $order): array
    {
        $issues = [];

        $origin = $order->deliveryRouteStops->firstWhere(
            'stop_key',
            'origin_facility'
        );

        $facility = $origin?->facility;

        /*
        |--------------------------------------------------------------------------
        | Pickup Address
        |--------------------------------------------------------------------------
        */

        if (blank($order->pickup_address)) {

            $issues[] =
                'Pickup address is missing. Ask the Seller to update it.';
        }

        /*
        |--------------------------------------------------------------------------
        | Origin Facility Validation
        |--------------------------------------------------------------------------
        */

        if (! $facility) {

            $issues[] =
                'Origin hub is missing. Review the delivery route.';

        } elseif (
            (int) $facility->logistics_account_id
            !== (int) $order->logistics_account_id
        ) {

            $issues[] =
                'Origin hub belongs to another Logistics provider.';

        } elseif (! $facility->is_active) {

            $issues[] =
                'Origin hub is inactive. Review the delivery route.';
        }

        return $issues;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Pickup Location
    |--------------------------------------------------------------------------
    */

    private function normalizePickupText(?string $value): string
    {
        return mb_strtolower(
            preg_replace(
                '/\s+/u',
                ' ',
                trim((string) $value)
            ) ?: ''
        );
    }
}
