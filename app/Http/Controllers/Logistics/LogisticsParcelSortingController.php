<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LogisticsParcelSortingController extends Controller
{
    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsNotificationService $notifications,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Origin Hub Sorting Queue
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        /*
         * Keep the existing Received / Sorted work queue.
         *
         * A local parcel that completes sorting moves to
         * destination_received and becomes available on the
         * Last-Mile Assignment page instead.
         */

        $parcelsQuery = LogisticsParcel::query()
            ->with([
                'order.seller',
                'order.deliveryRouteStops.facility',
            ])
            ->whereHas(
                'order',
                function ($query) use ($logistics): void {
                    $query->where(
                        'logistics_account_id',
                        $logistics->id
                    );
                }
            )
            ->whereIn('status', [
                'received',
                'sorted',
            ]);

        /*
         * Hub Operators can only access parcels physically assigned
         * to their origin hub.
         */

        if ($hubFacility) {
            $parcelsQuery->whereHas(
                'order.deliveryRouteStops',
                function ($query) use ($hubFacility): void {
                    $query
                        ->where('stop_key', 'origin_facility')
                        ->where(
                            'logistics_facility_id',
                            $hubFacility->id
                        );
                }
            );
        }

        $parcels = $parcelsQuery
            ->latest('received_at')
            ->get();

        return view(
            'logistics.parcel-sorting',
            compact(
                'parcels',
                'hubFacility',
                'isHubOperator'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Sort Parcel
    |--------------------------------------------------------------------------
    |
    | Different hubs:
    |     received -> sorted -> Hub Transfer
    |
    | Same hub:
    |     received -> destination_received -> Last-Mile Assignment
    |
    | In both cases, sorting information and the actual sorting
    | timestamp are preserved.
    */

    public function sort(
        Request $request,
        LogisticsParcel $parcel
    ): RedirectResponse {
        $parcel->loadMissing('order');

        abort_unless($parcel->order, 404);

        /*
         * Resolve the parent network without changing the signed-in identity.
         * For Hub Operators, authorize against the EXISTING origin route
         * BEFORE any legacy ownedOrder() route-resolution side effects.
         */

        $logistics = CurrentLogisticsAccount::resolve($request);

        if (CurrentLogisticsHub::isHubOperator($request)) {
            CurrentLogisticsHub::authorizeOrder(
                $request,
                $parcel->order,
                'origin'
            );

            $hubFacility = CurrentLogisticsHub::requireFacility($request);
            $ownedOrder = $parcel->order;
        } else {
            $ownedOrder = $this->orderAccess->ownedOrder(
                $request,
                $parcel->order
            );

            $hubFacility = null;
        }

        /*
         * Preserve the existing Sorting form fields.
         */

        $validated = $request->validate([
            'sorting_zone' => [
                'required',
                'string',
                'max:80',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $zone = trim($validated['sorting_zone']);

        if ($zone === '') {
            throw ValidationException::withMessages([
                'sorting_zone' =>
                    'Enter a valid sorting zone or rack.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Atomic Sorting Transaction
        |--------------------------------------------------------------------------
        |
        | Every status, timestamp and event is committed together.
        | If a validation or database operation fails, changes roll back.
        */

        $result = DB::transaction(
            function () use (
                $request,
                $parcel,
                $ownedOrder,
                $logistics,
                $hubFacility,
                $zone,
                $validated
            ): array {

                /*
                 * Lock order first, followed by its parcel.
                 */

                $order = MarketplaceOrder::query()
                    ->whereKey($ownedOrder->id)
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedParcel = LogisticsParcel::query()
                    ->whereKey($parcel->id)
                    ->where(
                        'marketplace_order_id',
                        $order->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Parcel State Validation
                |--------------------------------------------------------------------------
                */

                if ($lockedParcel->status !== 'received') {
                    throw ValidationException::withMessages([
                        'parcel' =>
                            'Only received parcels can be sorted. Refresh the queue.',
                    ]);
                }

                if (! $lockedParcel->received_at) {
                    throw ValidationException::withMessages([
                        'parcel' =>
                            'Origin-hub receipt time is missing. Confirm intake before sorting.',
                    ]);
                }

                if (
                    $order->status !== 'in_transit'
                    || ! $order->picked_up_at
                    || blank($order->courier_email)
                ) {
                    throw ValidationException::withMessages([
                        'parcel' =>
                            'This order has no valid completed Rider pickup. Refresh the queue.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Lock Origin and Destination Route Stops
                |--------------------------------------------------------------------------
                */

                $routeStops = $order->deliveryRouteStops()
                    ->whereIn('stop_key', [
                        'origin_facility',
                        'destination_facility',
                    ])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('stop_key');

                $origin = $routeStops->get('origin_facility');
                $destination = $routeStops->get('destination_facility');

                $origin?->load('facility');
                $destination?->load('facility');

                $originFacility = $origin?->facility;
                $destinationFacility = $destination?->facility;

                /*
                 * Both facilities must be active and belong
                 * to the same assigned Logistics provider.
                 */

                if (
                    ! $originFacility
                    || ! $destinationFacility
                    || ! $originFacility->is_active
                    || ! $destinationFacility->is_active
                    || (int) $originFacility->logistics_account_id
                        !== (int) $order->logistics_account_id
                    || (int) $destinationFacility->logistics_account_id
                        !== (int) $order->logistics_account_id
                ) {
                    throw ValidationException::withMessages([
                        'parcel' =>
                            'A valid, active origin and destination hub are required. Review the delivery route.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Hub Operator Authorization
                |--------------------------------------------------------------------------
                */

                if ($hubFacility) {
                    $currentHubFacility = CurrentLogisticsHub::requireFacility(
                        $request
                    );

                    abort_unless(
                        (int) $currentHubFacility->id === (int) $hubFacility->id
                            && (int) $originFacility->id === (int) $currentHubFacility->id,
                        403,
                        'This parcel belongs to another origin Logistics hub.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Identify Local vs Inter-Hub Delivery
                |--------------------------------------------------------------------------
                |
                | Compare actual facility IDs, not display names.
                */

                $isLocalHandoff =
                    (int) $originFacility->id
                    === (int) $destinationFacility->id;

                $sortedAt = now();

                $notes = array_key_exists('notes', $validated)
                    ? (
                        trim((string) $validated['notes'])
                        ?: null
                    )
                    : $lockedParcel->notes;

                /*
                |--------------------------------------------------------------------------
                | Save Sorting Result
                |--------------------------------------------------------------------------
                |
                | Same hub:
                |     destination_received
                |
                | Different hubs:
                |     sorted
                |
                | No transfer manifest is generated here.
                */

                $lockedParcel->fill([
                    'status' => $isLocalHandoff
                        ? 'destination_received'
                        : 'sorted',

                    'sorting_zone' => $zone,

                    'notes' => $notes,

                    'sorted_at' => $sortedAt,
                ])->save();

                /*
                |--------------------------------------------------------------------------
                | Preserve Origin Hub Milestone
                |--------------------------------------------------------------------------
                |
                | Use the actual receipt time when an older record
                | has no reached_at timestamp.
                */

                if (! $origin->reached_at) {
                    $origin->forceFill([
                        'reached_at' => $lockedParcel->received_at,
                    ])->save();
                }

                /*
                |--------------------------------------------------------------------------
                | Record Sorting Event
                |--------------------------------------------------------------------------
                */

                $this->workflow->record(
                    $order,
                    'both',
                    'origin_hub_sorted',
                    'Origin Hub Sorting Completed',
                    'Parcel for order '
                        . $order->order_number
                        . ' was sorted at '
                        . $originFacility->name
                        . ' in zone/rack '
                        . $zone
                        . '.'
                );

                /*
                |--------------------------------------------------------------------------
                | Local Hub Handoff
                |--------------------------------------------------------------------------
                |
                | The destination is physically the same hub.
                | The sorted parcel can proceed directly to the
                | local Last-Mile Assignment queue.
                */

                if ($isLocalHandoff) {

                    /*
                     * The destination becomes ready at sorting time.
                     * Never overwrite an existing historical milestone.
                     */

                    if (! $destination->reached_at) {
                        $destination->forceFill([
                            'reached_at' => $sortedAt,
                        ])->save();
                    }

                    $this->workflow->record(
                        $order,
                        'both',
                        'local_hub_handoff',
                        'Local Hub Handoff Completed',
                        'Parcel for order '
                            . $order->order_number
                            . ' is ready for Last-Mile Assignment at '
                            . $originFacility->name
                            . '. Origin and destination use the same '
                            . 'municipal hub; no Hub Transfer is required.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Persistent Logistics Notifications
                |--------------------------------------------------------------------------
                |
                | Same hub: Manager + origin/destination hub operators receive
                | an alert linking to Last-Mile Assignment. Notify this one
                | facility only once; origin and destination are identical.
                |
                | Different hubs: Manager + origin hub operators receive an
                | alert linking to Hub Transfers. The destination hub will
                | be notified later by the actual transfer workflow.
                |
                | All inserts share this transaction with sorting and its
                | Seller/Buyer workflow events. A failure rolls everything back.
                */

                $eventKey = 'origin_hub_sorted:order:'.$order->id;

                $actionRoute = $isLocalHandoff
                    ? 'logistics.last-mile-assignment'
                    : 'logistics.hub-transfers';

                $notificationMessage = $isLocalHandoff
                    ? 'Parcel for order '.$order->order_number
                        .' was sorted at '.$originFacility->name
                        .' in zone/rack '.$zone
                        .'. The local hub handoff is complete; assign a Last-Mile Rider.'
                    : 'Parcel for order '.$order->order_number
                        .' was sorted at '.$originFacility->name
                        .' in zone/rack '.$zone
                        .'. It is ready for an inter-hub transfer.';

                $notificationData = [
                    'order_id' => (int) $order->id,
                    'order_number' => (string) $order->order_number,
                    'parcel_id' => (int) $lockedParcel->id,
                    'origin_facility_id' => (int) $originFacility->id,
                    'destination_facility_id' => (int) $destinationFacility->id,
                    'sorting_zone' => $zone,
                    'local_handoff' => $isLocalHandoff,
                ];

                $this->notifications->notifyManager(
                    logisticsAccountId: (int) $logistics->id,
                    eventKey: $eventKey,
                    type: 'origin_hub_sorted',
                    title: $isLocalHandoff
                        ? 'Parcel ready for Last-Mile Assignment'
                        : 'Sorted parcel ready for Hub Transfer',
                    message: $notificationMessage,
                    actionRoute: $actionRoute,
                    data: $notificationData
                );

                $this->notifications->notifyFacilityOperators(
                    logisticsAccountId: (int) $logistics->id,
                    facilityId: (int) $originFacility->id,
                    eventKey: $eventKey,
                    type: 'origin_hub_sorted',
                    title: $isLocalHandoff
                        ? 'Local parcel ready for Last-Mile Assignment'
                        : 'Your sorted parcel is ready for Hub Transfer',
                    message: $notificationMessage,
                    actionRoute: $actionRoute,
                    data: $notificationData
                );

                /*
                |--------------------------------------------------------------------------
                | Return Verified Sorting Result
                |--------------------------------------------------------------------------
                */

                return [
                    'hub_name' => $originFacility->name,
                    'facility_id' => (int) $originFacility->id,
                    'local' => $isLocalHandoff,
                ];
            },
            3
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | Set audit attributes only after the sorting transaction
        | commits successfully.
        |
        | The facility ID comes from the actual database record,
        | not from user-submitted input.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_facility_id',
            $result['facility_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            $result['local']
                ? 'Parcel sorted at '
                    . $result['hub_name']
                    . '. Local hub handoff completed; proceed to '
                    . 'Last-Mile Assignment. No Hub Transfer is needed.'
                : 'Parcel sorted at '
                    . $result['hub_name']
                    . ' and cleared for Hub Transfer.'
        );
    }
}
