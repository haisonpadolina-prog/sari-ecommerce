<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsDeliveryAssignmentController extends Controller
{
    private const ACTIVE_PICKUP_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    private const ACTIVE_LAST_MILE_STATUSES = [
        'assigned',
        'out_for_delivery',
    ];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsOrderAccess $orderAccess,
        private readonly LogisticsNotificationService $notifications,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Pickup Assignment Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $queueQuery = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email');

        /*
         * The manager sees the provider-wide queue.
         * A Hub Operator sees only orders originating from their hub.
         *
         * Include pending siblings, not just verified parcels.
         */

        $queueOrders = CurrentLogisticsHub::scopeOrders(
            $request,
            $queueQuery,
            'origin'
        )
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        $queueGroups = $queueOrders->groupBy(
            fn (MarketplaceOrder $order): string => $this->pickupGroupKey($order)
        );

        $orders = $queueOrders
            ->filter(
                fn (MarketplaceOrder $order): bool => $order->logisticsParcel?->status === 'pickup_verified'
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Seller Pickup Groups
        |--------------------------------------------------------------------------
        */

        $pickupGroups = $orders
            ->groupBy(
                fn (MarketplaceOrder $order): string => $this->pickupGroupKey($order)
            )
            ->map(function (
                Collection $group,
                string $groupKey
            ) use ($queueGroups): array {

                $firstOrder = $group->first();

                $origin = $firstOrder
                    ->deliveryRouteStops
                    ->firstWhere('stop_key', 'origin_facility');

                $destinationNames = $group
                    ->map(function (MarketplaceOrder $order): ?string {
                        $destination = $order
                            ->deliveryRouteStops
                            ->firstWhere(
                                'stop_key',
                                'destination_facility'
                            );

                        return $destination?->facility?->name
                            ?: $destination?->name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $allSiblings = $queueGroups->get(
                    $groupKey,
                    collect()
                );

                $unverified = $allSiblings
                    ->filter(
                        fn (MarketplaceOrder $order): bool => $order->logisticsParcel?->status
                            !== 'pickup_verified'
                    )
                    ->values();

                return [
                    'key' => $groupKey,

                    'seller_name' => $firstOrder->seller?->store_name
                        ?: $firstOrder->pickup_name
                        ?: 'SARI Seller',

                    'pickup_name' => $firstOrder->pickup_name
                        ?: $firstOrder->seller?->store_name
                        ?: 'Seller Pickup',

                    'pickup_address' => $firstOrder->pickup_address
                        ?: 'Pickup address unavailable',

                    'origin_facility_id' => $origin?->logistics_facility_id,

                    'origin_facility_name' => $origin?->facility?->name
                        ?: $origin?->name
                        ?: 'Origin hub unavailable',

                    'orders' => $group->values(),

                    'order_count' => $group->count(),

                    'buyer_count' => $group
                        ->pluck('buyer_name')
                        ->filter()
                        ->unique()
                        ->count(),

                    'destination_count' => $destinationNames->count(),

                    'destination_names' => $destinationNames,

                    'total_value' => $group->sum(
                        fn (MarketplaceOrder $order): float => (float) $order->total
                    ),

                    'oldest_ready_at' => $group
                        ->pluck('ready_at')
                        ->filter()
                        ->sort()
                        ->first(),

                    'total_group_orders' => $allSiblings->count(),

                    'pending_count' => $unverified->count(),

                    'pending_order_numbers' => $unverified->pluck('order_number')->values(),

                    'is_blocked' => $unverified->isNotEmpty(),
                ];
            })
            ->sortBy(
                fn (array $group) => $group['oldest_ready_at'] ?: now()
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Approved Riders
        |--------------------------------------------------------------------------
        */

        $approvedIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query
                    ->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        $hubFacility = CurrentLogisticsHub::requireOperatingFacility($request);

        $riderQuery = CourierAccount::query();

        if ($hubFacility) {

            /*
             * Hub Operators may use only approved Riders belonging
             * to their provider and local municipality/province.
             *
             * They cannot claim unowned legacy Rider accounts.
             */

            $riderQuery
                ->where('logistics_account_id', $logistics->id)
                ->whereRaw('LOWER(TRIM(municipality_name)) = ?', [
                    mb_strtolower(
                        trim((string) $hubFacility->municipality_name)
                    ),
                ])
                ->whereRaw('LOWER(TRIM(province_name)) = ?', [
                    mb_strtolower(
                        trim((string) $hubFacility->province_name)
                    ),
                ]);

        } else {

            /*
             * Preserve the manager's existing Rider pool.
             */

            $riderQuery->where(function ($query) use ($logistics): void {
                $query
                    ->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            });
        }

        $riders = $riderQuery
            ->where('account_status', 'active')
            ->where('availability_status', 'online')
            ->whereIn('registration_application_id', $approvedIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Network-wide Busy Rider Checks
        |--------------------------------------------------------------------------
        */

        $busyPickupEmails = MarketplaceOrder::query()
            ->whereIn('status', [...self::ACTIVE_PICKUP_STATUSES, 'arrived_buyer'])
            ->whereNotNull('courier_email')
            ->where(function ($query): void {
                $query->whereDoesntHave('logisticsParcel')
                    ->orWhereHas('logisticsParcel', fn ($parcel) => $parcel->where('status', 'awaiting_intake'));
            })
            ->pluck('courier_email')
            ->filter()
            ->map(
                fn ($email): string => strtolower(trim((string) $email))
            )
            ->unique()
            ->values();

        $busyLastMileIds = DB::table('last_mile_assignments')
            ->whereIn(
                'status',
                self::ACTIVE_LAST_MILE_STATUSES
            )
            ->pluck('courier_account_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $riders = $riders->map(
            function (CourierAccount $rider) use (
                $busyPickupEmails,
                $busyLastMileIds
            ): CourierAccount {

                $busyPickup = $busyPickupEmails->contains(
                    strtolower(trim((string) $rider->email))
                );

                $busyLastMile = $busyLastMileIds->contains(
                    (int) $rider->id
                );

                $rider->setAttribute(
                    'is_dispatch_busy',
                    $busyPickup || $busyLastMile
                );

                $rider->setAttribute(
                    'dispatch_busy_reason',
                    $busyLastMile
                        ? 'Last-mile delivery'
                        : ($busyPickup ? 'Seller pickup' : null)
                );

                return $rider;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [
            'verified_orders' => $orders->count(),

            'pickup_groups' => $pickupGroups->count(),

            'multi_order_groups' => $pickupGroups
                ->filter(
                    fn (array $group): bool => $group['order_count'] > 1
                )
                ->count(),

            'blocked_groups' => $pickupGroups
                ->filter(
                    fn (array $group): bool => $group['is_blocked']
                )
                ->count(),

            'unverified_orders' => $queueOrders->count() - $orders->count(),

            'available_riders' => $riders
                ->filter(
                    fn (CourierAccount $rider): bool => ! (bool) $rider->getAttribute('is_dispatch_busy')
                )
                ->count(),

            'busy_riders' => $riders
                ->filter(
                    fn (CourierAccount $rider): bool => (bool) $rider->getAttribute('is_dispatch_busy')
                )
                ->count(),
        ];

        return view(
            'logistics.delivery-assignment',
            compact(
                'pickupGroups',
                'riders',
                'stats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Complete Seller Pickup Batch
    |--------------------------------------------------------------------------
    */

    public function assignBatch(Request $request): RedirectResponse
    {
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
                'exists:marketplace_orders,id',
            ],

            'rider_id' => [
                'required',
                'integer',
                'exists:courier_accounts,id',
            ],
        ]);

        $logistics = CurrentLogisticsAccount::resolve($request);

        $hubAccount = CurrentLogisticsHub::isHubOperator($request)
            ? CurrentLogisticsHub::requireAccount($request)
            : null;

        $hubFacility = CurrentLogisticsHub::requireOperatingFacility($request);

        $ids = collect($validated['order_ids'])
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        /*
         * Recheck the whole group inside the transaction.
         * Do not trust the order IDs submitted by the browser.
         *
         * ACTIVITY LOGS:
         * Return the actual origin hub after successful assignment.
         */

        $assignedFacilityId = DB::transaction(
            function () use (
                $request,
                $ids,
                $validated,
                $logistics,
                $hubAccount,
                $hubFacility
            ): int {

                /*
                 * Lock the entire available queue so the existing
                 * Seller sibling completeness rule is preserved.
                 */

                $queueQuery = MarketplaceOrder::query()
                    ->where('logistics_account_id', $logistics->id)
                    ->where('status', 'ready_for_pickup')
                    ->whereNull('courier_email');

                $queue = CurrentLogisticsHub::scopeOrders(
                    $request,
                    $queueQuery,
                    'origin'
                )
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                /*
                 * Lock origin route stops and independently confirm
                 * actual facility ownership.
                 */

                $origins = DeliveryRouteStop::query()
                    ->whereIn(
                        'marketplace_order_id',
                        $queue->pluck('id')->all()
                    )
                    ->where('stop_key', 'origin_facility')
                    ->orderBy('marketplace_order_id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('marketplace_order_id');

                $queue->load([
                    'seller',
                    'deliveryRouteStops.facility',
                ]);

                $selected = $queue
                    ->whereIn('id', $ids->all())
                    ->values();

                if ($selected->count() !== $ids->count()) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'One or more orders are no longer available. Refresh the queue.',
                    ]);
                }

                /*
                 * The batch must contain the same Seller, normalized
                 * pickup address, and origin facility.
                 */

                $keys = $selected
                    ->map(
                        fn (MarketplaceOrder $order): string => $this->pickupGroupKey($order)
                    )
                    ->unique();

                if ($keys->count() !== 1) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'A pickup batch must have the same Seller, pickup address and origin hub.',
                    ]);
                }

                /*
                 * The submitted IDs must equal every ready and
                 * unassigned order in this Seller group.
                 */

                $wholeGroupIds = $queue
                    ->filter(
                        fn (MarketplaceOrder $order): bool => $this->pickupGroupKey($order)
                            === $keys->first()
                    )
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->sort()
                    ->values()
                    ->all();

                if ($wholeGroupIds !== $ids->all()) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'Awaiting complete batch verification. Verify all sibling orders under Pickup Requests before assigning one Rider.',
                    ]);
                }

                /*
                 * Lock parcel records.
                 */

                $parcels = LogisticsParcel::query()
                    ->whereIn(
                        'marketplace_order_id',
                        $ids->all()
                    )
                    ->orderBy('marketplace_order_id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('marketplace_order_id');

                /*
                 * Validate every parcel and its origin hub.
                 */

                foreach ($selected as $order) {

                    $origin = $origins->get($order->id);
                    $facility = $origin?->facility;

                    if (
                        $hubFacility
                        && (int) ($origin?->logistics_facility_id ?? 0)
                            !== (int) $hubFacility->id
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' => 'A selected order is not assigned to your origin hub. Refresh the queue.',
                        ]);
                    }

                    if (
                        blank($order->pickup_address)
                        || ! $facility
                        || ! $facility->is_active
                        || (int) $facility->logistics_account_id
                            !== (int) $logistics->id
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' => $order->order_number
                                .' has a missing address or invalid origin hub.',
                        ]);
                    }

                    if (
                        $parcels->get($order->id)?->status
                        !== 'pickup_verified'
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' => 'All orders in this Seller group must be verified before assignment.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Selected Rider
                |--------------------------------------------------------------------------
                */

                $riderQuery = CourierAccount::query()
                    ->whereKey((int) $validated['rider_id']);

                if ($hubFacility) {

                    /*
                     * Hub Operator:
                     * Provider + municipality + province must match.
                     */

                    $riderQuery
                        ->where('logistics_account_id', $logistics->id)
                        ->whereRaw('LOWER(TRIM(municipality_name)) = ?', [
                            mb_strtolower(
                                trim((string) $hubFacility->municipality_name)
                            ),
                        ])
                        ->whereRaw('LOWER(TRIM(province_name)) = ?', [
                            mb_strtolower(
                                trim((string) $hubFacility->province_name)
                            ),
                        ]);

                } else {

                    /*
                     * Manager:
                     * Preserve existing provider-wide and legacy pool.
                     */

                    $riderQuery->where(function ($query) use ($logistics): void {
                        $query
                            ->where('logistics_account_id', $logistics->id)
                            ->orWhereNull('logistics_account_id');
                    });
                }

                $rider = $riderQuery
                    ->lockForUpdate()
                    ->first();

                if (! $rider) {
                    throw ValidationException::withMessages([
                        'rider_id' => 'Select an eligible Rider for your assigned Logistics service area.',
                    ]);
                }

                $approved = RegistrationApplication::query()
                    ->whereKey(
                        $rider->registration_application_id
                    )
                    ->where(function ($query) use ($logistics): void {
                        $query
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->orWhereNull('logistics_account_id');
                    })
                    ->where('role', 'rider')
                    ->where('status', 'approved')
                    ->exists();

                if (
                    ! $approved
                    || (
                        $rider->logistics_account_id
                        && (int) $rider->logistics_account_id
                            !== (int) $logistics->id
                    )
                    || $rider->account_status !== 'active'
                    || $rider->availability_status !== 'online'
                ) {
                    throw ValidationException::withMessages([
                        'rider_id' => 'Select an approved, active, online Rider from your Logistics network.',
                    ]);
                }

                /*
                 * Preserve legacy Rider claiming for Manager only.
                 * Hub Operators cannot claim unowned Rider accounts.
                 */

                if (! $hubFacility && ! $rider->logistics_account_id) {

                    $rider->forceFill([
                        'logistics_account_id' => $logistics->id,
                    ])->save();

                    if ($rider->registration_application_id) {

                        RegistrationApplication::query()
                            ->whereKey(
                                $rider->registration_application_id
                            )
                            ->whereNull('logistics_account_id')
                            ->update([
                                'logistics_account_id' => $logistics->id,
                            ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Conflicting Pickup and Last-Mile Assignments
                |--------------------------------------------------------------------------
                */

                $email = strtolower(
                    trim((string) $rider->email)
                );

                $busyPickup = MarketplaceOrder::query()
                    ->whereRaw('LOWER(TRIM(courier_email)) = ?', [$email])
                    ->whereIn('status', [...self::ACTIVE_PICKUP_STATUSES, 'arrived_buyer'])
                    ->where(function ($query): void {
                        $query->whereDoesntHave('logisticsParcel')
                            ->orWhereHas('logisticsParcel', fn ($parcel) => $parcel->where('status', 'awaiting_intake'));
                    })->exists();

                if ($busyPickup) {
                    throw ValidationException::withMessages([
                        'rider_id' => 'Rider already has an active Seller pickup or direct delivery.',
                    ]);
                }

                $busyLastMile = DB::table('last_mile_assignments')
                    ->where(
                        'courier_account_id',
                        $rider->id
                    )
                    ->whereIn(
                        'status',
                        self::ACTIVE_LAST_MILE_STATUSES
                    )
                    ->exists();

                if ($busyLastMile) {
                    throw ValidationException::withMessages([
                        'rider_id' => 'Rider has an active last-mile assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Atomic Whole-Batch Assignment
                |--------------------------------------------------------------------------
                */

                $riderName = trim(
                    $rider->first_name
                    .' '
                    .(
                        $rider->middle_initial
                            ? $rider->middle_initial.'. '
                            : ''
                    )
                    .$rider->last_name
                ) ?: 'SARI Rider';

                $assignedAt = now();

                $assignedBy = $hubAccount
                    ? (trim((string) $hubAccount->name) ?: 'Hub Operator')
                        .' ('.$hubFacility->name.')'
                    : $logistics->displayName();

                foreach ($selected as $order) {

                    $order->forceFill([
                        'status' => 'courier_accepted',
                        'courier_name' => $riderName,
                        'courier_email' => $email,
                        'accepted_at' => $assignedAt,
                    ])->save();

                    $updated = LogisticsParcel::query()
                        ->where(
                            'marketplace_order_id',
                            $order->id
                        )
                        ->where(
                            'status',
                            'pickup_verified'
                        )
                        ->update([
                            'status' => 'awaiting_intake',
                            'sorting_zone' => null,
                            'received_at' => null,
                            'sorted_at' => null,
                            'updated_at' => $assignedAt,
                        ]);

                    if ($updated !== 1) {
                        throw ValidationException::withMessages([
                            'order_ids' => 'A parcel changed during assignment. No orders were assigned. Refresh and retry.',
                        ]);
                    }

                    $this->workflow->record(
                        $order,
                        'both',
                        'courier_accepted',
                        'Pickup Rider Assigned by Logistics',
                        $riderName
                            .' was assigned by '
                            .$assignedBy
                            .' to collect this order as part of a '
                            .$selected->count()
                            .'-parcel Seller pickup batch.'
                    );

                    /*
                    |----------------------------------------------------------
                    | Persistent Logistics notifications
                    |
                    | Same transaction: a failed batch leaves no alerts.
                    | Each order has a stable per-recipient event key.
                    |----------------------------------------------------------
                    */

                    $originFacilityId = (int) $origins
                        ->get($order->id)
                        ?->logistics_facility_id;

                    $eventKey = 'pickup_rider_assigned:order:'.$order->id;

                    $notificationMessage = $riderName
                        .' was assigned by '
                        .$assignedBy
                        .' to collect order '
                        .$order->order_number
                        .' as part of a '
                        .$selected->count()
                        .'-parcel Seller pickup batch.';

                    $notificationData = [
                        'order_id' => (int) $order->id,
                        'order_number' => (string) $order->order_number,
                        'rider_id' => (int) $rider->id,
                        'rider_name' => $riderName,
                        'origin_facility_id' => $originFacilityId,
                        'batch_size' => (int) $selected->count(),
                    ];

                    $this->notifications->notifyManager(
                        logisticsAccountId: (int) $logistics->id,
                        eventKey: $eventKey,
                        type: 'pickup_rider_assigned',
                        title: 'Pickup Rider assigned',
                        message: $notificationMessage,
                        actionRoute: 'logistics.delivery-monitoring',
                        data: $notificationData
                    );

                    $this->notifications->notifyFacilityOperators(
                        logisticsAccountId: (int) $logistics->id,
                        facilityId: $originFacilityId,
                        eventKey: $eventKey,
                        type: 'pickup_rider_assigned',
                        title: 'Rider assigned to your origin-hub pickup',
                        message: $notificationMessage,
                        actionRoute: 'logistics.delivery-monitoring',
                        data: $notificationData
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Activity Logs — Confirm Affected Hub
                |--------------------------------------------------------------------------
                |
                | All selected orders belong to the same origin hub,
                | as enforced by the pickup group validation.
                |
                | Return that hub for the audit middleware.
                |
                */

                return (int) $origins
                    ->get($selected->first()->id)
                    ?->logistics_facility_id;
            },
            3
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | Reaching this point means the assignment transaction
        | has successfully committed.
        |
        | This includes both single-order and batch assignment.
        |
        | The middleware will create only one activity record
        | for the successful request.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_facility_id',
            $assignedFacilityId
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            $ids->count()
            .' '
            .(
                $ids->count() === 1
                    ? 'parcel was'
                    : 'parcels were'
            )
            .' assigned to the pickup Rider successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy Single-Order Assignment
    |--------------------------------------------------------------------------
    */

    public function assign(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {

        if (CurrentLogisticsHub::hasOperatingFacility($request)) {

            /*
             * Don't call legacy ownedOrder() before authorization:
             * it may generate route stops for previously unrouted orders.
             */

            CurrentLogisticsHub::authorizeOrder(
                $request,
                $order,
                'origin'
            );

        } else {

            $order = $this->orderAccess->ownedOrder(
                $request,
                $order
            );
        }

        $request->merge([
            'order_ids' => [$order->id],
        ]);

        return $this->assignBatch($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Same Seller Group Key as Pickup Requests
    |--------------------------------------------------------------------------
    */

    private function pickupGroupKey(
        MarketplaceOrder $order
    ): string {

        $origin = $order->deliveryRouteStops
            ->firstWhere(
                'stop_key',
                'origin_facility'
            );

        $seller = $order->seller_account_id
            ? 'seller:'.$order->seller_account_id
            : 'order:'.$order->id;

        return json_encode([
            $seller,
            $this->normalizePickupText($order->pickup_address),
            $origin?->logistics_facility_id,
        ], JSON_THROW_ON_ERROR);
    }

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
