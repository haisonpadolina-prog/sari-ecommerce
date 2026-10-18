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

    /**
     * The manager sees the provider's queue.
     *
     * A Hub Operator sees only orders whose actual origin_facility
     * stop is the operator's assigned hub.
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
        | Hub-level queue visibility
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
        | Preserve original pickup queue states
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
        | Queue statistics
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

    /**
     * Single pickup request verification.
     *
     * A Hub Operator must pass origin-hub authorization before any
     * legacy route-resolution logic is allowed to run.
     */
    public function verify(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Authorize selected order
        |--------------------------------------------------------------------------
        */

        if (CurrentLogisticsHub::isHubOperator($request)) {
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
        | Reuse the same secure batch verification flow
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'order_ids' => [$owned->id],
        ]);

        return $this->verifyBatch($request);
    }

    /**
     * Verify up to 200 pickup requests.
     *
     * All submitted order IDs are checked again inside the transaction.
     * This prevents a Hub Operator from verifying another facility's
     * parcels through a manually constructed POST request.
     */
    public function verifyBatch(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Request validation
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
        | Resolve current account and assigned hub
        |--------------------------------------------------------------------------
        */

        $logistics = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator(
            $request
        );

        $hubAccount = $isHubOperator
            ? CurrentLogisticsHub::requireAccount($request)
            : null;

        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        $ids = collect($validated['order_ids'])
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Transactional pickup verification
        |--------------------------------------------------------------------------
        */

        $count = DB::transaction(function () use (
            $request,
            $ids,
            $logistics,
            $hubAccount,
            $hubFacility
        ): int {
            /*
            |--------------------------------------------------------------
            | Lock orders using a stable ordering.
            |--------------------------------------------------------------
            */

            $query = MarketplaceOrder::query()
                ->whereIn('id', $ids)
                ->where(
                    'logistics_account_id',
                    $logistics->id
                );

            /*
            |--------------------------------------------------------------
            | Reapply origin-hub authorization inside the transaction.
            |
            | Manager: provider-wide visibility.
            | Hub:     assigned origin facility only.
            |--------------------------------------------------------------
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
            |--------------------------------------------------------------
            | Lock actual origin route stops.
            |
            | The selected IDs must still belong to the same permitted
            | facility when the transaction processes the verification.
            |--------------------------------------------------------------
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
            |--------------------------------------------------------------
            | Load current route data and lock existing parcels.
            |--------------------------------------------------------------
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
            |--------------------------------------------------------------
            | Record the actual verifying operator.
            |--------------------------------------------------------------
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

            $count = 0;

            /*
            |--------------------------------------------------------------------------
            | Verify each requested order
            |--------------------------------------------------------------------------
            */

            foreach ($orders as $order) {
                $origin = $origins->get($order->id);

                /*
                |----------------------------------------------------------
                | Hub Operator ownership recheck.
                |
                | Never trust the order IDs supplied by the browser.
                |----------------------------------------------------------
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
                |----------------------------------------------------------
                | Original order-state validation
                |----------------------------------------------------------
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
                |----------------------------------------------------------
                | Validate pickup address and origin facility
                |----------------------------------------------------------
                */

                $issues = $this->pickupIssues($order);

                if ($issues) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .': '.$issues[0],
                    ]);
                }

                /*
                |----------------------------------------------------------
                | Check current parcel state
                |----------------------------------------------------------
                */

                $parcel = $parcels->get($order->id);

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
                |----------------------------------------------------------
                | Create verified Logistics Parcel
                |----------------------------------------------------------
                */

                LogisticsParcel::query()->create([
                    'marketplace_order_id' => $order->id,
                    'status' => 'pickup_verified',
                ]);

                /*
                |----------------------------------------------------------
                | Record Seller and Buyer workflow event
                |----------------------------------------------------------
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
                |----------------------------------------------------------
                | Persist notifications only for a newly verified pickup.
                |
                | This remains inside the same transaction as parcel
                | creation and the Seller/Buyer workflow event. A failed
                | verification cannot leave an orphaned notification.
                |
                | Each recipient has a separate read state. Repeating the
                | event uses the same key and will not reset read_at.
                |----------------------------------------------------------
                */

                $eventKey = 'pickup_verified:order:'.$order->id;

                $notificationMessage = $actor
                    .' verified the pickup request for order '
                    .$order->order_number
                    .'. It is ready for Rider assignment.';

                $notificationData = [
                    'order_id' => (int) $order->id,
                    'order_number' => (string) $order->order_number,
                    'origin_facility_id' => (int) $origin->logistics_facility_id,
                ];

                $this->notifications->notifyManager(
                    logisticsAccountId: (int) $logistics->id,
                    eventKey: $eventKey,
                    type: 'pickup_verified',
                    title: 'Pickup request verified',
                    message: $notificationMessage,
                    actionRoute: 'logistics.delivery-assignment',
                    data: $notificationData
                );

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

                $count++;
            }

            return $count;
        }, 3);

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

    /**
     * Keep the original Seller + pickup location + origin hub
     * grouping behavior.
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

    /**
     * @return array<int, string>
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
        | Pickup address
        |--------------------------------------------------------------------------
        */

        if (blank($order->pickup_address)) {
            $issues[] =
                'Pickup address is missing. Ask the Seller to update it.';
        }

        /*
        |--------------------------------------------------------------------------
        | Origin facility validation
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

    /**
     * Normalize pickup addresses for stable Seller batch grouping.
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
