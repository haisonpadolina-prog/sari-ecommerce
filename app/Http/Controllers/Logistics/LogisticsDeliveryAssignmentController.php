<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsDeliveryAssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Active pickup delivery statuses
    |--------------------------------------------------------------------------
    |
    | Used when checking if a Rider already has an active Seller pickup job.
    |
    */

    private const ACTIVE_PICKUP_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    /*
    |--------------------------------------------------------------------------
    | Active last-mile assignment statuses
    |--------------------------------------------------------------------------
    */

    private const ACTIVE_LAST_MILE_STATUSES = [
        'assigned',
        'out_for_delivery',
    ];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Pickup Assignment
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics =
            $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Verified, unassigned Seller pickup orders
        |--------------------------------------------------------------------------
        */

        $orders =
            MarketplaceOrder::query()
                ->with([
                    'seller',
                    'logisticsParcel',
                    'deliveryRouteStops.facility',
                ])
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'status',
                    'ready_for_pickup'
                )
                ->whereNull(
                    'courier_email'
                )
                ->whereHas(
                    'logisticsParcel',
                    fn ($query) =>
                        $query->where(
                            'status',
                            'pickup_verified'
                        )
                )
                ->oldest('ready_at')
                ->oldest('id')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Group orders into Seller pickup batches
        |--------------------------------------------------------------------------
        |
        | SAME:
        | - Seller
        | - Pickup address
        | - Origin hub
        |
        | NOT PART OF GROUP KEY:
        | - Buyer
        | - Product
        | - Destination hub
        |
        */

        $pickupGroups =
            $orders
                ->groupBy(
                    fn (MarketplaceOrder $order): string =>
                        $this->pickupGroupKey(
                            $order
                        )
                )
                ->map(
                    function (
                        Collection $group,
                        string $groupKey
                    ): array {
                        /** @var MarketplaceOrder $firstOrder */
                        $firstOrder =
                            $group->first();

                        $routeStops =
                            $firstOrder
                                ->deliveryRouteStops
                                ->keyBy('stop_key');

                        $originStop =
                            $routeStops->get(
                                'origin_facility'
                            );

                        $originFacility =
                            $originStop?->facility;

                        $destinationNames =
                            $group
                                ->map(
                                    function (
                                        MarketplaceOrder $order
                                    ): ?string {
                                        $destination =
                                            $order
                                                ->deliveryRouteStops
                                                ->firstWhere(
                                                    'stop_key',
                                                    'destination_facility'
                                                );

                                        return $destination
                                                ?->facility
                                                ?->name
                                            ?: $destination
                                                ?->name;
                                    }
                                )
                                ->filter()
                                ->unique()
                                ->values();

                        $buyerCount =
                            $group
                                ->pluck(
                                    'buyer_name'
                                )
                                ->filter()
                                ->unique()
                                ->count();

                        $totalValue =
                            $group
                                ->sum(
                                    fn (
                                        MarketplaceOrder $order
                                    ): float =>
                                        (float) $order->total
                                );

                        return [
                            'key' =>
                                $groupKey,

                            'seller_name' =>
                                $firstOrder
                                    ->seller
                                    ?->store_name
                                ?: $firstOrder
                                    ->pickup_name
                                ?: 'SARI Seller',

                            'pickup_name' =>
                                $firstOrder
                                    ->pickup_name
                                ?: $firstOrder
                                    ->seller
                                    ?->store_name
                                ?: 'Seller Pickup',

                            'pickup_address' =>
                                $firstOrder
                                    ->pickup_address
                                ?: 'Pickup address unavailable',

                            'origin_facility_id' =>
                                $originStop
                                    ?->logistics_facility_id,

                            'origin_facility_name' =>
                                $originFacility
                                    ?->name
                                ?: $originStop
                                    ?->name
                                ?: 'Origin hub unavailable',

                            'orders' =>
                                $group->values(),

                            'order_count' =>
                                $group->count(),

                            'buyer_count' =>
                                $buyerCount,

                            'destination_count' =>
                                $destinationNames->count(),

                            'destination_names' =>
                                $destinationNames,

                            'total_value' =>
                                $totalValue,

                            'oldest_ready_at' =>
                                $group
                                    ->pluck('ready_at')
                                    ->filter()
                                    ->sort()
                                    ->first(),
                        ];
                    }
                )
                ->sortBy(
                    fn (array $group) =>
                        $group['oldest_ready_at']
                        ?: now()
                )
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Approved, active and online Riders
        |--------------------------------------------------------------------------
        */

        $approvedRiderApplicationIds =
            RegistrationApplication::query()
                ->where(
                    function ($query) use ($logistics): void {
                        $query
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->orWhereNull(
                                'logistics_account_id'
                            );
                    }
                )
                ->where(
                    'role',
                    'rider'
                )
                ->where(
                    'status',
                    'approved'
                )
                ->select('id');

        $riders =
            CourierAccount::query()
                ->where(
                    function ($query) use ($logistics): void {
                        $query
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->orWhereNull(
                                'logistics_account_id'
                            );
                    }
                )
                ->where(
                    'account_status',
                    'active'
                )
                ->where(
                    'availability_status',
                    'online'
                )
                ->whereIn(
                    'registration_application_id',
                    $approvedRiderApplicationIds
                )
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Riders busy with active Seller pickup work
        |--------------------------------------------------------------------------
        |
        | courier_email remains as the pickup Rider snapshot even after hub
        | intake, so a Rider is only considered busy while the related parcel
        | is still awaiting intake at the origin hub.
        |
        */

        $busyPickupEmails =
            MarketplaceOrder::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->whereIn(
                    'status',
                    self::ACTIVE_PICKUP_STATUSES
                )
                ->whereNotNull(
                    'courier_email'
                )
                ->whereHas(
                    'logisticsParcel',
                    fn ($query) =>
                        $query->where(
                            'status',
                            'awaiting_intake'
                        )
                )
                ->pluck(
                    'courier_email'
                )
                ->filter()
                ->map(
                    fn ($email): string =>
                        strtolower(
                            trim(
                                (string) $email
                            )
                        )
                )
                ->unique()
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Riders busy with active Last-Mile work
        |--------------------------------------------------------------------------
        */

        $busyLastMileRiderIds =
            DB::table(
                'last_mile_assignments'
            )
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->whereIn(
                    'status',
                    self::ACTIVE_LAST_MILE_STATUSES
                )
                ->pluck(
                    'courier_account_id'
                )
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->unique()
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Add availability metadata
        |--------------------------------------------------------------------------
        */

        $riders =
            $riders->map(
                function (
                    CourierAccount $rider
                ) use (
                    $busyPickupEmails,
                    $busyLastMileRiderIds
                ): CourierAccount {
                    $email =
                        strtolower(
                            trim(
                                (string) $rider->email
                            )
                        );

                    $busyPickup =
                        $busyPickupEmails
                            ->contains(
                                $email
                            );

                    $busyLastMile =
                        $busyLastMileRiderIds
                            ->contains(
                                (int) $rider->id
                            );

                    $rider->setAttribute(
                        'is_dispatch_busy',
                        $busyPickup
                        || $busyLastMile
                    );

                    $rider->setAttribute(
                        'dispatch_busy_reason',
                        $busyLastMile
                            ? 'Last-mile delivery'
                            : (
                                $busyPickup
                                    ? 'Seller pickup'
                                    : null
                            )
                    );

                    return $rider;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $stats = [
            'verified_orders' =>
                $orders->count(),

            'pickup_groups' =>
                $pickupGroups->count(),

            'multi_order_groups' =>
                $pickupGroups
                    ->filter(
                        fn (array $group): bool =>
                            $group['order_count'] > 1
                    )
                    ->count(),

            'available_riders' =>
                $riders
                    ->filter(
                        fn (CourierAccount $rider): bool =>
                            ! (bool) $rider
                                ->getAttribute(
                                    'is_dispatch_busy'
                                )
                    )
                    ->count(),

            'busy_riders' =>
                $riders
                    ->filter(
                        fn (CourierAccount $rider): bool =>
                            (bool) $rider
                                ->getAttribute(
                                    'is_dispatch_busy'
                                )
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
    | Batch assign Seller pickup
    |--------------------------------------------------------------------------
    */

    public function assignBatch(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate([
                'order_ids' => [
                    'required',
                    'array',
                    'min:1',
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

        $logistics =
            CurrentLogisticsAccount::resolve(
                $request
            );

        $selectedOrderIds =
            collect(
                $validated['order_ids']
            )
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->unique()
                ->values();

        $assignedCount =
            $selectedOrderIds->count();

        DB::transaction(
            function () use (
                $selectedOrderIds,
                $validated,
                $logistics
            ): void {
                /*
                |--------------------------------------------------------------------------
                | Lock all selected orders
                |--------------------------------------------------------------------------
                */

                $lockedOrders =
                    MarketplaceOrder::query()
                        ->whereIn(
                            'id',
                            $selectedOrderIds
                                ->all()
                        )
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->lockForUpdate()
                        ->get();

                if (
                    $lockedOrders->count()
                    !== $selectedOrderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'One or more selected orders do not belong to this Logistics account.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate order states
                |--------------------------------------------------------------------------
                */

                foreach (
                    $lockedOrders
                    as $order
                ) {
                    if (
                        $order->status
                        !== 'ready_for_pickup'
                        ||
                        filled(
                            $order->courier_email
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' =>
                                'One or more selected orders are no longer available for pickup assignment.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Validate verified parcels
                |--------------------------------------------------------------------------
                */

                $verifiedParcels =
                    LogisticsParcel::query()
                        ->whereIn(
                            'marketplace_order_id',
                            $selectedOrderIds
                                ->all()
                        )
                        ->where(
                            'status',
                            'pickup_verified'
                        )
                        ->lockForUpdate()
                        ->get();

                if (
                    $verifiedParcels->count()
                    !== $selectedOrderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Every order in the batch must be verified by Logistics before Rider assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Load route data for grouping validation
                |--------------------------------------------------------------------------
                */

                $lockedOrders->load([
                    'seller',
                    'deliveryRouteStops.facility',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Selected orders must be one Seller pickup group
                |--------------------------------------------------------------------------
                */

                $groupKeys =
                    $lockedOrders
                        ->map(
                            fn (
                                MarketplaceOrder $order
                            ): string =>
                                $this->pickupGroupKey(
                                    $order
                                )
                        )
                        ->unique()
                        ->values();

                if (
                    $groupKeys->count() !== 1
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'A pickup batch must contain orders from the same Seller, pickup location, and origin hub.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Origin hub must exist
                |--------------------------------------------------------------------------
                */

                foreach (
                    $lockedOrders
                    as $order
                ) {
                    $originStop =
                        $order
                            ->deliveryRouteStops
                            ->firstWhere(
                                'stop_key',
                                'origin_facility'
                            );

                    if (
                        ! $originStop
                        ||
                        ! $originStop
                            ->logistics_facility_id
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' =>
                                'One or more orders do not have a valid origin sorting hub.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Lock selected Rider
                |--------------------------------------------------------------------------
                */

                $rider =
                    CourierAccount::query()
                        ->whereKey(
                            (int) $validated[
                                'rider_id'
                            ]
                        )
                        ->where(
                            function (
                                $query
                            ) use (
                                $logistics
                            ): void {
                                $query
                                    ->where(
                                        'logistics_account_id',
                                        $logistics->id
                                    )
                                    ->orWhereNull(
                                        'logistics_account_id'
                                    );
                            }
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Confirm Rider approval
                |--------------------------------------------------------------------------
                */

                $approvedRider =
                    RegistrationApplication::query()
                        ->whereKey(
                            $rider
                                ->registration_application_id
                        )
                        ->where(
                            function (
                                $query
                            ) use (
                                $logistics
                            ): void {
                                $query
                                    ->where(
                                        'logistics_account_id',
                                        $logistics->id
                                    )
                                    ->orWhereNull(
                                        'logistics_account_id'
                                    );
                            }
                        )
                        ->where(
                            'role',
                            'rider'
                        )
                        ->where(
                            'status',
                            'approved'
                        )
                        ->exists();

                if (
                    ! $approvedRider
                    ||
                    (
                        $rider
                            ->logistics_account_id
                        &&
                        (int) $rider
                            ->logistics_account_id
                            !==
                            (int) $logistics->id
                    )
                    ||
                    $rider->account_status
                        !== 'active'
                    ||
                    $rider->availability_status
                        !== 'online'
                ) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'Only approved, active and online Riders from your Logistics network can receive pickup batches.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Claim legacy Rider under this Logistics account
                |--------------------------------------------------------------------------
                */

                if (
                    ! $rider
                        ->logistics_account_id
                ) {
                    $rider->forceFill([
                        'logistics_account_id' =>
                            $logistics->id,
                    ])->save();

                    if (
                        $rider
                            ->registration_application_id
                    ) {
                        RegistrationApplication::query()
                            ->whereKey(
                                $rider
                                    ->registration_application_id
                            )
                            ->whereNull(
                                'logistics_account_id'
                            )
                            ->update([
                                'logistics_account_id' =>
                                    $logistics->id,
                            ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Rider cannot have another active Seller pickup
                |--------------------------------------------------------------------------
                |
                | courier_email remains on the MarketplaceOrder as a historical
                | pickup Rider snapshot. Therefore the Rider is only considered
                | occupied while the LogisticsParcel is still awaiting intake.
                |
                */

                $hasActivePickup =
                    MarketplaceOrder::query()
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->whereRaw(
                            'LOWER(courier_email) = ?',
                            [
                                strtolower(
                                    trim(
                                        (string) $rider
                                            ->email
                                    )
                                ),
                            ]
                        )
                        ->whereIn(
                            'status',
                            self::ACTIVE_PICKUP_STATUSES
                        )
                        ->whereHas(
                            'logisticsParcel',
                            fn ($query) =>
                                $query->where(
                                    'status',
                                    'awaiting_intake'
                                )
                        )
                        ->lockForUpdate()
                        ->exists();

                if ($hasActivePickup) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'This Rider already has an active Seller pickup batch.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Rider cannot have active Last-Mile work
                |--------------------------------------------------------------------------
                */

                $hasActiveLastMile =
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->whereIn(
                            'status',
                            self::ACTIVE_LAST_MILE_STATUSES
                        )
                        ->exists();

                if ($hasActiveLastMile) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'This Rider currently has an active last-mile delivery assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Rider display name
                |--------------------------------------------------------------------------
                */

                $riderName =
                    trim(
                        $rider->first_name
                        . ' '
                        . (
                            $rider->middle_initial
                                ? $rider
                                    ->middle_initial
                                    . '. '
                                : ''
                        )
                        . $rider->last_name
                    );

                $riderName =
                    $riderName
                    ?: 'SARI Rider';

                $batchSize =
                    $lockedOrders->count();

                /*
                |--------------------------------------------------------------------------
                | Assign entire batch to one Rider
                |--------------------------------------------------------------------------
                */

                foreach (
                    $lockedOrders
                    as $lockedOrder
                ) {
                    $lockedOrder->forceFill([
                        'status' =>
                            'courier_accepted',

                        'courier_name' =>
                            $riderName,

                        'courier_email' =>
                            strtolower(
                                trim(
                                    (string) $rider
                                        ->email
                                )
                            ),

                        'accepted_at' =>
                            now(),
                    ])->save();

                    LogisticsParcel::query()
                        ->where(
                            'marketplace_order_id',
                            $lockedOrder->id
                        )
                        ->update([
                            'status' =>
                                'awaiting_intake',

                            'sorting_zone' =>
                                null,

                            'received_at' =>
                                null,

                            'sorted_at' =>
                                null,

                            'updated_at' =>
                                now(),
                        ]);

                    $this->workflow->record(
                        $lockedOrder,
                        'both',
                        'courier_accepted',
                        'Pickup Rider Assigned by Logistics',
                        $riderName
                            . ' was assigned by '
                            . $logistics
                                ->displayName()
                            . ' to collect this order as part of a '
                            . $batchSize
                            . '-parcel Seller pickup batch.'
                    );
                }
            },
            3
        );

        return back()->with(
            'success',
            $assignedCount
                . ' '
                . (
                    $assignedCount === 1
                        ? 'parcel was'
                        : 'parcels were'
                )
                . ' assigned to the pickup Rider successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy single-order assignment
    |--------------------------------------------------------------------------
    |
    | Retained so old links/routes continue to work.
    | It delegates to the same batch assignment logic.
    |
    */

    public function assign(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->orderAccess->ownedOrder(
                $request,
                $order
            );

        $request->merge([
            'order_ids' => [
                $order->id,
            ],
        ]);

        return $this->assignBatch(
            $request
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Seller pickup grouping key
    |--------------------------------------------------------------------------
    */

    private function pickupGroupKey(
        MarketplaceOrder $order
    ): string {
        $originStop =
            $order
                ->deliveryRouteStops
                ->firstWhere(
                    'stop_key',
                    'origin_facility'
                );

        $sellerKey =
            $order->seller_account_id
                ? 'seller:'
                    . $order
                        ->seller_account_id
                : 'seller-name:'
                    . $this->normalizeText(
                        $order->pickup_name
                        ?: $order
                            ->seller
                            ?->store_name
                        ?: 'unknown'
                    );

        $pickupAddress =
            $this->normalizeText(
                $order->pickup_address
                ?: ''
            );

        $originFacilityId =
            $originStop
                ?->logistics_facility_id
            ?: 'none';

        return implode(
            '|',
            [
                $sellerKey,
                'pickup:' . $pickupAddress,
                'origin:' . $originFacilityId,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize grouping text
    |--------------------------------------------------------------------------
    */

    private function normalizeText(
        ?string $value
    ): string {
        $value =
            trim(
                (string) $value
            );

        $value =
            preg_replace(
                '/\s+/u',
                ' ',
                $value
            ) ?: $value;

        return mb_strtolower(
            $value
        );
    }
}