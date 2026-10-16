<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsRiderManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Pickup-leg statuses
    |--------------------------------------------------------------------------
    |
    | The marketplace order may already be "in_transit", but the pickup Rider
    | is only considered busy while the parcel is still awaiting intake at
    | the origin hub.
    |
    */

    private const PICKUP_ORDER_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    /*
    |--------------------------------------------------------------------------
    | Active Last-Mile statuses
    |--------------------------------------------------------------------------
    */

    private const ACTIVE_LAST_MILE_STATUSES = [
        'assigned',
        'out_for_delivery',
    ];

    public function index(Request $request): View
    {
        $logistics =
            CurrentLogisticsAccount::resolve($request);

        /*
        |--------------------------------------------------------------------------
        | Approved Rider applications
        |--------------------------------------------------------------------------
        */

        $approvedApplicationIds =
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

        /*
        |--------------------------------------------------------------------------
        | Riders belonging to this Logistics network
        |--------------------------------------------------------------------------
        */

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
                ->whereNotNull(
                    'registration_application_id'
                )
                ->whereIn(
                    'registration_application_id',
                    $approvedApplicationIds
                )
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Active Seller-pickup work
        |--------------------------------------------------------------------------
        |
        | One Rider may have multiple MarketplaceOrder rows because one pickup
        | batch can contain several Seller orders.
        |
        | We only consider the pickup Rider busy while the parcel has not yet
        | been accepted by the origin sorting hub.
        |
        */

        $pickupOrders =
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
                ->whereNotNull(
                    'courier_email'
                )
                ->whereIn(
                    'status',
                    self::PICKUP_ORDER_STATUSES
                )
                ->whereHas(
                    'logisticsParcel',
                    fn ($query) =>
                        $query->where(
                            'status',
                            'awaiting_intake'
                        )
                )
                ->orderBy('accepted_at')
                ->orderBy('id')
                ->get();

        $pickupOrdersByEmail =
            $pickupOrders->groupBy(
                fn (MarketplaceOrder $order): string =>
                    strtolower(
                        trim(
                            (string) $order->courier_email
                        )
                    )
            );

        /*
        |--------------------------------------------------------------------------
        | Active Last-Mile assignments
        |--------------------------------------------------------------------------
        */

        $lastMileAssignments =
            DB::table(
                'last_mile_assignments as assignment'
            )
                ->join(
                    'logistics_facilities as facility',
                    'facility.id',
                    '=',
                    'assignment.destination_facility_id'
                )
                ->where(
                    'assignment.logistics_account_id',
                    $logistics->id
                )
                ->whereIn(
                    'assignment.status',
                    self::ACTIVE_LAST_MILE_STATUSES
                )
                ->select([
                    'assignment.id',
                    'assignment.assignment_code',
                    'assignment.courier_account_id',
                    'assignment.delivery_area',
                    'assignment.status',
                    'assignment.assigned_at',
                    'assignment.started_at',

                    'facility.id as facility_id',
                    'facility.name as facility_name',
                    'facility.municipality_name as facility_municipality',
                ])
                ->orderByDesc(
                    'assignment.id'
                )
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Attach Order information to Last-Mile batches
        |--------------------------------------------------------------------------
        */

        foreach ($lastMileAssignments as $assignment) {
            $assignment->orders =
                DB::table(
                    'last_mile_assignment_orders as link'
                )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'link.marketplace_order_id'
                    )
                    ->where(
                        'link.last_mile_assignment_id',
                        $assignment->id
                    )
                    ->select([
                        'orders.id',
                        'orders.order_number',
                        'orders.buyer_name',
                        'orders.status',
                    ])
                    ->orderBy(
                        'orders.order_number'
                    )
                    ->get();

            $assignment->order_count =
                $assignment->orders->count();
        }

        $lastMileByRider =
            $lastMileAssignments
                ->groupBy(
                    fn ($assignment): int =>
                        (int) $assignment
                            ->courier_account_id
                );

        /*
        |--------------------------------------------------------------------------
        | Build Rider rows
        |--------------------------------------------------------------------------
        */

        $riderRows =
            $riders->map(
                function (
                    CourierAccount $rider
                ) use (
                    $pickupOrdersByEmail,
                    $lastMileByRider
                ): array {
                    $workItems = [];

                    /*
                    |--------------------------------------------------------------------------
                    | Pickup batch
                    |--------------------------------------------------------------------------
                    */

                    $riderEmail =
                        strtolower(
                            trim(
                                (string) $rider->email
                            )
                        );

                    $riderPickupOrders =
                        $pickupOrdersByEmail->get(
                            $riderEmail,
                            collect()
                        );

                    if (
                        $riderPickupOrders
                            ->isNotEmpty()
                    ) {
                        $firstOrder =
                            $riderPickupOrders
                                ->first();

                        $originStop =
                            $firstOrder
                                ->deliveryRouteStops
                                ->firstWhere(
                                    'stop_key',
                                    'origin_facility'
                                );

                        $sellerName =
                            $firstOrder
                                ->seller
                                ?->store_name
                            ?: $firstOrder
                                ->pickup_name
                            ?: 'Seller Pickup';

                        $originName =
                            $originStop
                                ?->facility
                                ?->name
                            ?: $originStop
                                ?->name
                            ?: 'Origin Hub';

                        $parcelCount =
                            $riderPickupOrders
                                ->count();

                        $pickedUpCount =
                            $riderPickupOrders
                                ->where(
                                    'status',
                                    'in_transit'
                                )
                                ->count();

                        $atSellerCount =
                            $riderPickupOrders
                                ->where(
                                    'status',
                                    'arrived_pickup'
                                )
                                ->count();

                        $headingCount =
                            $riderPickupOrders
                                ->where(
                                    'status',
                                    'heading_pickup'
                                )
                                ->count();

                        if (
                            $pickedUpCount
                            === $parcelCount
                        ) {
                            $workStatus =
                                'Heading to origin hub';
                        } elseif (
                            $pickedUpCount > 0
                        ) {
                            $workStatus =
                                $pickedUpCount
                                . '/'
                                . $parcelCount
                                . ' picked up';
                        } elseif (
                            $atSellerCount > 0
                        ) {
                            $workStatus =
                                'At Seller pickup';
                        } elseif (
                            $headingCount > 0
                        ) {
                            $workStatus =
                                'Heading to Seller';
                        } else {
                            $workStatus =
                                'Pickup assigned';
                        }

                        $workItems[] = [
                            'type' =>
                                'pickup',

                            'label' =>
                                'Pickup Batch',

                            'title' =>
                                $parcelCount
                                . ' '
                                . (
                                    $parcelCount === 1
                                        ? 'parcel'
                                        : 'parcels'
                                ),

                            'route' =>
                                $sellerName
                                . ' → '
                                . $originName,

                            'status' =>
                                $workStatus,
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Last-Mile batches
                    |--------------------------------------------------------------------------
                    */

                    $riderLastMile =
                        $lastMileByRider->get(
                            (int) $rider->id,
                            collect()
                        );

                    foreach (
                        $riderLastMile
                        as $assignment
                    ) {
                        $count =
                            (int) $assignment
                                ->order_count;

                        $workItems[] = [
                            'type' =>
                                'last_mile',

                            'label' =>
                                'Last Mile',

                            'title' =>
                                $count
                                . ' '
                                . (
                                    $count === 1
                                        ? 'parcel'
                                        : 'parcels'
                                ),

                            'route' =>
                                $assignment
                                    ->facility_name
                                . ' → '
                                . $assignment
                                    ->delivery_area,

                            'status' =>
                                $assignment->status
                                    === 'out_for_delivery'
                                        ? 'Out for delivery'
                                        : 'Assigned',
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Rider State
                    |--------------------------------------------------------------------------
                    */

                    $isAccountActive =
                        $rider->account_status
                        === 'active';

                    /*
                    | Explicitly require "online".
                    | NULL or any other value is not online.
                    */

                    $isOnline =
                        $rider
                            ->availability_status
                        === 'online';

                    $isBusy =
                        count($workItems) > 0;

                    $state =
                        ! $isAccountActive
                            ? 'inactive'
                            : (
                                ! $isOnline
                                    ? 'offline'
                                    : (
                                        $isBusy
                                            ? 'busy'
                                            : 'available'
                                    )
                            );

                    return [
                        'rider' =>
                            $rider,

                        'work_items' =>
                            $workItems,

                        'is_busy' =>
                            $isBusy,

                        'is_online' =>
                            $isOnline,

                        'is_active' =>
                            $isAccountActive,

                        'state' =>
                            $state,
                    ];
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Rider statistics
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' =>
                $riderRows->count(),

            'active' =>
                $riderRows
                    ->where(
                        'is_active',
                        true
                    )
                    ->count(),

            'online' =>
                $riderRows
                    ->where(
                        'is_online',
                        true
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->count(),

            'busy' =>
                $riderRows
                    ->where(
                        'is_busy',
                        true
                    )
                    ->count(),

            'available' =>
                $riderRows
                    ->where(
                        'state',
                        'available'
                    )
                    ->count(),

            'barangays' =>
                $riders
                    ->pluck(
                        'barangay_name'
                    )
                    ->filter()
                    ->map(
                        fn ($value) =>
                            strtolower(
                                trim(
                                    (string) $value
                                )
                            )
                    )
                    ->unique()
                    ->count(),

            'municipalities' =>
                $riders
                    ->pluck(
                        'municipality_name'
                    )
                    ->filter()
                    ->map(
                        fn ($value) =>
                            strtolower(
                                trim(
                                    (string) $value
                                )
                            )
                    )
                    ->unique()
                    ->count(),
        ];

        return view(
            'logistics.rider-management',
            compact(
                'riderRows',
                'stats',
                'logistics'
            )
        );
    }
}