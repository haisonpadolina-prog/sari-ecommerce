<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsDashboardController extends Controller
{
    private const PICKUP_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
    ];

    private const DELIVERY_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Approved Rider Applications
        |--------------------------------------------------------------------------
        */

        $approvedRiderApplicationIds =
            RegistrationApplication::query()
                ->where(function ($query) use ($logistics): void {
                    $query
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->orWhereNull(
                            'logistics_account_id'
                        );
                })
                ->where('role', 'rider')
                ->where('status', 'approved')
                ->select('id');

        /*
        |--------------------------------------------------------------------------
        | Reusable owned Order / Parcel queries
        |--------------------------------------------------------------------------
        */

        $ownedOrders = fn () =>
            MarketplaceOrder::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                );

        $ownedParcels = fn () =>
            LogisticsParcel::query()
                ->whereHas(
                    'order',
                    fn ($query) =>
                        $query->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                );

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [

            /*
             * Riders / Network
             */
            'pending_rider_applications' =>
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
                    ->where('role', 'rider')
                    ->where('status', 'pending')
                    ->count(),

            'active_riders' =>
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
                    ->whereIn(
                        'registration_application_id',
                        $approvedRiderApplicationIds
                    )
                    ->count(),

            'online_riders' =>
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
                    ->count(),

            'active_facilities' =>
                LogisticsFacility::query()
                    ->forLogistics(
                        $logistics->id
                    )
                    ->active()
                    ->count(),

            /*
             * Seller Pickup
             */
            'pickup_requests' =>
                $ownedOrders()
                    ->where(
                        'status',
                        'ready_for_pickup'
                    )
                    ->whereNull(
                        'courier_email'
                    )
                    ->count(),

            'pickup_active' =>
                $ownedOrders()
                    ->whereIn(
                        'status',
                        self::PICKUP_ACTIVE_STATUSES
                    )
                    ->whereNotNull(
                        'courier_email'
                    )
                    ->count(),

            /*
             * Origin Hub
             */
            'awaiting_intake' =>
                $ownedParcels()
                    ->where(
                        'status',
                        'awaiting_intake'
                    )
                    ->count(),

            'sorting_queue' =>
                $ownedParcels()
                    ->where(
                        'status',
                        'received'
                    )
                    ->count(),

            'ready_for_transfer' =>
                $ownedParcels()
                    ->where(
                        'status',
                        'sorted'
                    )
                    ->count(),

            /*
             * Hub Transfer
             */
            'hub_transfer_preparing' =>
                DB::table('hub_transfers')
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->where(
                        'status',
                        'preparing'
                    )
                    ->count(),

            'hub_transfer_in_transit' =>
                DB::table('hub_transfers')
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->where(
                        'status',
                        'in_transit'
                    )
                    ->count(),

            /*
             * Destination Hub
             */
            'destination_received' =>
                $ownedParcels()
                    ->where(
                        'status',
                        'destination_received'
                    )
                    ->count(),

            /*
             * Last Mile
             */
            'last_mile_assigned' =>
                DB::table('last_mile_assignments')
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->where(
                        'status',
                        'assigned'
                    )
                    ->count(),

            'last_mile_out_for_delivery' =>
                DB::table('last_mile_assignments')
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->where(
                        'status',
                        'out_for_delivery'
                    )
                    ->count(),

            /*
             * Completed
             */
            'delivered_today' =>
                $ownedOrders()
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->whereDate(
                        'delivered_at',
                        now()->toDateString()
                    )
                    ->count(),

            'total_delivered' =>
                $ownedOrders()
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->count(),

            'active_deliveries' =>
                $ownedOrders()
                    ->whereIn(
                        'status',
                        self::DELIVERY_ACTIVE_STATUSES
                    )
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Derived Statistics
        |--------------------------------------------------------------------------
        */

        $stats['hub_transfer_active'] =
            $stats['hub_transfer_preparing']
            + $stats['hub_transfer_in_transit'];

        $stats['last_mile_active'] =
            $stats['last_mile_assigned']
            + $stats['last_mile_out_for_delivery'];

        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders =
            $ownedOrders()
                ->with([
                    'seller',
                    'logisticsParcel',
                ])
                ->latest('updated_at')
                ->limit(8)
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Last-Mile details for Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrderIds =
            $recentOrders
                ->pluck('id')
                ->all();

        $lastMileByOrder = collect();

        if (! empty($recentOrderIds)) {
            $lastMileByOrder =
                DB::table(
                    'last_mile_assignment_orders as link'
                )
                    ->join(
                        'last_mile_assignments as assignment',
                        'assignment.id',
                        '=',
                        'link.last_mile_assignment_id'
                    )
                    ->join(
                        'courier_accounts as rider',
                        'rider.id',
                        '=',
                        'assignment.courier_account_id'
                    )
                    ->whereIn(
                        'link.marketplace_order_id',
                        $recentOrderIds
                    )
                    ->select([
                        'link.marketplace_order_id',

                        'assignment.assignment_code',
                        'assignment.delivery_area',
                        'assignment.status',

                        'rider.first_name',
                        'rider.last_name',
                    ])
                    ->get()
                    ->keyBy(
                        'marketplace_order_id'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Recent Hub Transfers
        |--------------------------------------------------------------------------
        */

        $recentTransfers =
            DB::table(
                'hub_transfers as transfer'
            )
                ->join(
                    'logistics_facilities as origin',
                    'origin.id',
                    '=',
                    'transfer.origin_facility_id'
                )
                ->join(
                    'logistics_facilities as destination',
                    'destination.id',
                    '=',
                    'transfer.destination_facility_id'
                )
                ->where(
                    'transfer.logistics_account_id',
                    $logistics->id
                )
                ->select([
                    'transfer.id',
                    'transfer.transfer_code',
                    'transfer.vehicle_type',
                    'transfer.vehicle_plate',
                    'transfer.driver_name',
                    'transfer.status',
                    'transfer.dispatched_at',
                    'transfer.received_at',
                    'transfer.created_at',

                    'origin.name as origin_name',
                    'destination.name as destination_name',
                ])
                ->orderByDesc(
                    'transfer.id'
                )
                ->limit(4)
                ->get();

        foreach ($recentTransfers as $transfer) {
            $transfer->parcel_count =
                DB::table(
                    'hub_transfer_parcels'
                )
                    ->where(
                        'hub_transfer_id',
                        $transfer->id
                    )
                    ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Operations Pipeline
        |--------------------------------------------------------------------------
        */

        $pipeline = [
            [
                'label' => 'Pickup Queue',
                'value' => $stats['pickup_requests'],
                'route' => 'logistics.pickup-requests',
                'description' => 'Seller parcels waiting for Logistics verification.',
                'step' => '01',
            ],

            [
                'label' => 'Pickup Assigned',
                'value' => $stats['pickup_active'],
                'route' => 'logistics.delivery-assignment',
                'description' => 'Orders currently assigned to pickup Riders.',
                'step' => '02',
            ],

            [
                'label' => 'Hub Intake',
                'value' => $stats['awaiting_intake'],
                'route' => 'logistics.incoming-parcels',
                'description' => 'Picked-up parcels waiting at the origin hub.',
                'step' => '03',
            ],

            [
                'label' => 'Sorting Queue',
                'value' => $stats['sorting_queue'],
                'route' => 'logistics.parcel-sorting',
                'description' => 'Origin-hub parcels waiting to be sorted.',
                'step' => '04',
            ],

            [
                'label' => 'Ready for Transfer',
                'value' => $stats['ready_for_transfer'],
                'route' => 'logistics.hub-transfers',
                'description' => 'Sorted parcels ready for line-haul batching.',
                'step' => '05',
            ],

            [
                'label' => 'Hub Transfers',
                'value' => $stats['hub_transfer_active'],
                'route' => 'logistics.hub-transfers',
                'description' => 'Preparing or currently in-transit hub transfers.',
                'step' => '06',
            ],

            [
                'label' => 'Destination Hub',
                'value' => $stats['destination_received'],
                'route' => 'logistics.last-mile-assignment',
                'description' => 'Parcels received and waiting for local Riders.',
                'step' => '07',
            ],

            [
                'label' => 'Last Mile',
                'value' => $stats['last_mile_active'],
                'route' => 'logistics.last-mile-assignment',
                'description' => 'Local delivery batches assigned or on route.',
                'step' => '08',
            ],
        ];

        $logisticsName =
            $logistics->displayName();

        return view(
            'logistics.dashboard',
            compact(
                'stats',
                'pipeline',
                'recentOrders',
                'lastMileByOrder',
                'recentTransfers',
                'logistics',
                'logisticsName'
            )
        );
    }
}