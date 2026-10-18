<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsRiderManagementController extends Controller
{
    private const PICKUP_ORDER_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    private const ACTIVE_LAST_MILE_STATUSES = [
        'assigned',
        'out_for_delivery',
    ];

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Hub Operator account and location validation
        |--------------------------------------------------------------------------
        */

        if ($hubFacility) {
            abort_unless(
                (int) $hubFacility->logistics_account_id
                    === (int) $logistics->id,
                403,
                'Your assigned hub does not belong to this Logistics network.'
            );

            /*
             * An incomplete hub address must never expand Rider visibility.
             */

            abort_if(
                blank($hubFacility->province_name)
                    || blank($hubFacility->municipality_name),
                403,
                'Your hub needs a valid province and municipality.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Approved Rider applications
        |--------------------------------------------------------------------------
        |
        | Manager retains the original provider and legacy approval support.
        | Hub Operators can only see Rider accounts owned by this provider.
        */

        $approvedApplicationIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query
                    ->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        /*
        |--------------------------------------------------------------------------
        | Rider directory query
        |--------------------------------------------------------------------------
        */

        $riderQuery = CourierAccount::query();

        if ($hubFacility) {

            /*
             * Municipality-wide local directory.
             *
             * All barangays in the assigned hub's municipality are included,
             * but riders from another provider are never included.
             */

            $riderQuery
                ->where('logistics_account_id', $logistics->id)
                ->whereRaw('LOWER(TRIM(province_name)) = ?', [
                    mb_strtolower(
                        trim((string) $hubFacility->province_name)
                    ),
                ])
                ->whereRaw('LOWER(TRIM(municipality_name)) = ?', [
                    mb_strtolower(
                        trim((string) $hubFacility->municipality_name)
                    ),
                ]);

        } else {

            /*
             * Preserve Main Logistics' provider-wide and legacy Rider pool.
             */

            $riderQuery->where(
                function ($query) use ($logistics): void {
                    $query
                        ->where('logistics_account_id', $logistics->id)
                        ->orWhereNull('logistics_account_id');
                }
            );
        }

        $riders = $riderQuery
            ->whereNotNull('registration_application_id')
            ->whereIn(
                'registration_application_id',
                $approvedApplicationIds
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Visible active Seller-pickup work
        |--------------------------------------------------------------------------
        |
        | A Hub Operator can inspect only pickups whose origin facility
        | is their assigned hub.
        |
        | The Manager retains the original provider-wide pickup details.
        */

        $pickupQuery = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where('logistics_account_id', $logistics->id)
            ->whereNotNull('courier_email')
            ->whereIn('status', self::PICKUP_ORDER_STATUSES)
            ->whereHas(
                'logisticsParcel',
                function ($query): void {
                    $query->where('status', 'awaiting_intake');
                }
            );

        if ($hubFacility) {
            $pickupQuery->whereHas(
                'deliveryRouteStops',
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

        $pickupOrders = $pickupQuery
            ->orderBy('accepted_at')
            ->orderBy('id')
            ->get();

        $pickupOrdersByEmail = $pickupOrders->groupBy(
            fn (MarketplaceOrder $order): string =>
                strtolower(
                    trim((string) $order->courier_email)
                )
        );

        /*
        |--------------------------------------------------------------------------
        | Visible active Last-Mile assignments
        |--------------------------------------------------------------------------
        |
        | Hub Operators only receive assignments whose destination facility
        | matches their assigned hub.
        */

        $lastMileQuery = DB::table(
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
            );

        if ($hubFacility) {
            $lastMileQuery->where(
                'assignment.destination_facility_id',
                $hubFacility->id
            );
        }

        $lastMileAssignments = $lastMileQuery
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
            ->orderByDesc('assignment.id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Preserve existing Last-Mile order counts
        |--------------------------------------------------------------------------
        */

        foreach ($lastMileAssignments as $assignment) {
            $assignment->orders = DB::table(
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
                ->orderBy('orders.order_number')
                ->get();

            $assignment->order_count = $assignment->orders->count();
        }

        $lastMileByRider = $lastMileAssignments->groupBy(
            fn ($assignment): int =>
                (int) $assignment->courier_account_id
        );

        /*
        |--------------------------------------------------------------------------
        | Network-wide busy indicators
        |--------------------------------------------------------------------------
        |
        | Local Rider availability must account for assignments elsewhere
        | in the same Logistics network.
        |
        | We retrieve only Rider identifiers here, not unrelated order
        | details, Buyer details, or other hubs' assignment manifests.
        */

        if ($hubFacility) {

            $busyPickupEmails = MarketplaceOrder::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereNotNull('courier_email')
                ->whereIn(
                    'status',
                    self::PICKUP_ORDER_STATUSES
                )
                ->whereHas(
                    'logisticsParcel',
                    function ($query): void {
                        $query->where(
                            'status',
                            'awaiting_intake'
                        );
                    }
                )
                ->pluck('courier_email')
                ->map(
                    fn ($email): string =>
                        strtolower(trim((string) $email))
                )
                ->filter()
                ->unique()
                ->values();

            $busyLastMileIds = DB::table('last_mile_assignments')
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->whereIn(
                    'status',
                    self::ACTIVE_LAST_MILE_STATUSES
                )
                ->pluck('courier_account_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();

        } else {

            /*
             * For Main Logistics, the visible work already includes
             * the whole provider network.
             */

            $busyPickupEmails = $pickupOrdersByEmail->keys();

            $busyLastMileIds = $lastMileByRider->keys();
        }

        /*
        |--------------------------------------------------------------------------
        | Build Rider rows
        |--------------------------------------------------------------------------
        |
        | Preserve the array structure expected by rider-management.blade.php.
        */

        $riderRows = $riders->map(
            function (CourierAccount $rider) use (
                $pickupOrdersByEmail,
                $lastMileByRider,
                $busyPickupEmails,
                $busyLastMileIds,
                $isHubOperator
            ): array {

                $workItems = [];

                $riderEmail = strtolower(
                    trim((string) $rider->email)
                );

                $riderPickupOrders = $pickupOrdersByEmail->get(
                    $riderEmail,
                    collect()
                );

                /*
                |--------------------------------------------------------------------------
                | Visible pickup batch
                |--------------------------------------------------------------------------
                */

                if ($riderPickupOrders->isNotEmpty()) {

                    $firstOrder = $riderPickupOrders->first();

                    $originStop = $firstOrder
                        ->deliveryRouteStops
                        ->firstWhere(
                            'stop_key',
                            'origin_facility'
                        );

                    $sellerName = $firstOrder->seller?->store_name
                        ?: $firstOrder->pickup_name
                        ?: 'Seller Pickup';

                    $originName = $originStop?->facility?->name
                        ?: $originStop?->name
                        ?: 'Origin Hub';

                    $parcelCount = $riderPickupOrders->count();

                    $pickedUpCount = $riderPickupOrders
                        ->where('status', 'in_transit')
                        ->count();

                    $atSellerCount = $riderPickupOrders
                        ->where('status', 'arrived_pickup')
                        ->count();

                    $headingCount = $riderPickupOrders
                        ->where('status', 'heading_pickup')
                        ->count();

                    if ($pickedUpCount === $parcelCount) {

                        $workStatus = 'Heading to origin hub';

                    } elseif ($pickedUpCount > 0) {

                        $workStatus = $pickedUpCount
                            . '/'
                            . $parcelCount
                            . ' picked up';

                    } elseif ($atSellerCount > 0) {

                        $workStatus = 'At Seller pickup';

                    } elseif ($headingCount > 0) {

                        $workStatus = 'Heading to Seller';

                    } else {

                        $workStatus = 'Pickup assigned';
                    }

                    $workItems[] = [
                        'type' => 'pickup',
                        'label' => 'Pickup Batch',

                        'title' => $parcelCount
                            . ' '
                            . (
                                $parcelCount === 1
                                    ? 'parcel'
                                    : 'parcels'
                            ),

                        'route' => $sellerName
                            . ' → '
                            . $originName,

                        'status' => $workStatus,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Visible Last-Mile batches
                |--------------------------------------------------------------------------
                */

                foreach (
                    $lastMileByRider->get(
                        (int) $rider->id,
                        collect()
                    )
                    as $assignment
                ) {

                    $count = (int) $assignment->order_count;

                    $workItems[] = [
                        'type' => 'last_mile',
                        'label' => 'Last Mile',

                        'title' => $count
                            . ' '
                            . (
                                $count === 1
                                    ? 'parcel'
                                    : 'parcels'
                            ),

                        'route' => $assignment->facility_name
                            . ' → '
                            . $assignment->delivery_area,

                        'status' =>
                            $assignment->status === 'out_for_delivery'
                                ? 'Out for delivery'
                                : 'Assigned',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Rider availability
                |--------------------------------------------------------------------------
                |
                | Keep active work in other hubs out of work_items.
                | Only use the global results to determine busy status.
                */

                $isAccountActive = $rider->account_status === 'active';

                $isOnline = $rider->availability_status === 'online';

                $hasVisibleWork = count($workItems) > 0;

                $busyInNetwork =
                    $busyPickupEmails->contains($riderEmail)
                    || $busyLastMileIds->contains((int) $rider->id);

                $isBusy = $hasVisibleWork || $busyInNetwork;

                $isBusyElsewhere =
                    $isHubOperator
                    && $busyInNetwork
                    && ! $hasVisibleWork;

                $state = ! $isAccountActive
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
                    'rider' => $rider,

                    'work_items' => $workItems,

                    'is_busy' => $isBusy,

                    'is_busy_elsewhere' => $isBusyElsewhere,

                    'is_online' => $isOnline,

                    'is_active' => $isAccountActive,

                    'state' => $state,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Rider statistics
        |--------------------------------------------------------------------------
        |
        | Counts are calculated from the scoped Rider list only.
        */

        $stats = [
            'total' => $riderRows->count(),

            'active' => $riderRows
                ->where('is_active', true)
                ->count(),

            'online' => $riderRows
                ->where('is_online', true)
                ->where('is_active', true)
                ->count(),

            'busy' => $riderRows
                ->where('is_busy', true)
                ->count(),

            'available' => $riderRows
                ->where('state', 'available')
                ->count(),

            'barangays' => $riders
                ->pluck('barangay_name')
                ->filter()
                ->map(
                    fn ($value): string =>
                        mb_strtolower(trim((string) $value))
                )
                ->unique()
                ->count(),

            'municipalities' => $riders
                ->pluck('municipality_name')
                ->filter()
                ->map(
                    fn ($value): string =>
                        mb_strtolower(trim((string) $value))
                )
                ->unique()
                ->count(),
        ];

        return view(
            'logistics.rider-management',
            compact(
                'riderRows',
                'stats',
                'logistics',
                'isHubOperator',
                'hubFacility'
            )
        );
    }
}
