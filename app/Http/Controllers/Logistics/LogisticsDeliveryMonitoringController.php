<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LogisticsDeliveryMonitoringController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
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
        $logistics =
            $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Active Marketplace Orders
        |--------------------------------------------------------------------------
        |
        | marketplace_orders.courier_* remains the Seller pickup Rider
        | snapshot. Last-mile Rider information is loaded separately below.
        |
        */

        $activeOrders =
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
                ->whereIn(
                    'status',
                    self::ACTIVE_DELIVERY_STATUSES
                )
                ->whereNotNull(
                    'courier_email'
                )
                ->latest('updated_at')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Delivered Today
        |--------------------------------------------------------------------------
        */

        $deliveredToday =
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
                    'delivered'
                )
                ->whereDate(
                    'delivered_at',
                    now()->toDateString()
                )
                ->latest('delivered_at')
                ->limit(20)
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Load monitoring metadata
        |--------------------------------------------------------------------------
        */

        $allOrders =
            $activeOrders
                ->concat($deliveredToday)
                ->unique('id')
                ->values();

        $orderIds =
            $allOrders
                ->pluck('id')
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Assignments
        |--------------------------------------------------------------------------
        */

        $lastMileAssignments =
            collect();

        if ($orderIds->isNotEmpty()) {
            $lastMileAssignments =
                DB::table(
                    'last_mile_assignment_orders as item'
                )
                    ->join(
                        'last_mile_assignments as assignment',
                        'assignment.id',
                        '=',
                        'item.last_mile_assignment_id'
                    )
                    ->join(
                        'courier_accounts as rider',
                        'rider.id',
                        '=',
                        'assignment.courier_account_id'
                    )
                    ->whereIn(
                        'item.marketplace_order_id',
                        $orderIds->all()
                    )
                    ->select([
                        'item.marketplace_order_id as order_id',

                        'assignment.id as assignment_id',
                        'assignment.assignment_code',
                        'assignment.delivery_area',
                        'assignment.status as assignment_status',
                        'assignment.assigned_at',
                        'assignment.started_at',
                        'assignment.completed_at',

                        'rider.id as rider_id',
                        'rider.first_name',
                        'rider.last_name',
                        'rider.email as rider_email',
                        'rider.vehicle_type',
                        'rider.plate_number',
                        'rider.barangay_name',
                        'rider.municipality_name',
                    ])
                    ->get()
                    ->keyBy('order_id');
        }

        /*
        |--------------------------------------------------------------------------
        | Hub Transfer Information
        |--------------------------------------------------------------------------
        */

        $hubTransfers =
            collect();

        if ($orderIds->isNotEmpty()) {
            $hubTransfers =
                DB::table(
                    'hub_transfer_parcels as item'
                )
                    ->join(
                        'logistics_parcels as parcel',
                        'parcel.id',
                        '=',
                        'item.logistics_parcel_id'
                    )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'parcel.marketplace_order_id'
                    )
                    ->join(
                        'hub_transfers as transfer',
                        'transfer.id',
                        '=',
                        'item.hub_transfer_id'
                    )
                    ->whereIn(
                        'orders.id',
                        $orderIds->all()
                    )
                    ->select([
                        'orders.id as order_id',

                        'transfer.id as transfer_id',
                        'transfer.transfer_code',
                        'transfer.status as transfer_status',
                        'transfer.vehicle_type',
                        'transfer.vehicle_plate',
                        'transfer.driver_name',
                        'transfer.dispatched_at',
                        'transfer.received_at',
                    ])
                    ->orderBy(
                        'transfer.id'
                    )
                    ->get()
                    ->groupBy('order_id')
                    ->map(
                        fn (Collection $rows) =>
                            $rows->last()
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Decorate Orders
        |--------------------------------------------------------------------------
        */

        $this->decorateOrders(
            $activeOrders,
            $lastMileAssignments,
            $hubTransfers
        );

        $this->decorateOrders(
            $deliveredToday,
            $lastMileAssignments,
            $hubTransfers
        );

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $atHubStatuses = [
            'received',
            'sorted',
            'linehaul_assigned',
            'destination_received',
            'last_mile_assigned',
        ];

        $outForDeliveryStatuses = [
            'out_for_delivery',
            'arrived_buyer',
        ];

        $stats = [
            'active' =>
                $activeOrders->count(),

            'at_hub' =>
                $activeOrders
                    ->filter(
                        fn (MarketplaceOrder $order): bool =>
                            in_array(
                                $order
                                    ->logisticsParcel
                                    ?->status,
                                $atHubStatuses,
                                true
                            )
                    )
                    ->count(),

            'linehaul' =>
                $activeOrders
                    ->filter(
                        fn (MarketplaceOrder $order): bool =>
                            $order
                                ->logisticsParcel
                                ?->status
                            === 'linehaul_in_transit'
                    )
                    ->count(),

            'out_for_delivery' =>
                $activeOrders
                    ->filter(
                        fn (MarketplaceOrder $order): bool =>
                            in_array(
                                $order
                                    ->logisticsParcel
                                    ?->status,
                                $outForDeliveryStatuses,
                                true
                            )
                            ||
                            $order->status
                                === 'arrived_buyer'
                    )
                    ->count(),

            'delivered_today' =>
                $deliveredToday->count(),
        ];

        return view(
            'logistics.delivery-monitoring',
            compact(
                'activeOrders',
                'deliveredToday',
                'stats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attach Monitoring Metadata
    |--------------------------------------------------------------------------
    */

    private function decorateOrders(
        Collection $orders,
        Collection $lastMileAssignments,
        Collection $hubTransfers
    ): void {
        foreach ($orders as $order) {
            $lastMile =
                $lastMileAssignments->get(
                    $order->id
                );

            $transfer =
                $hubTransfers->get(
                    $order->id
                );

            $parcelStatus =
                $order
                    ->logisticsParcel
                    ?->status;

            $stage =
                $this->resolveStage(
                    $order,
                    $parcelStatus
                );

            $order->setAttribute(
                'monitor_stage_key',
                $stage['key']
            );

            $order->setAttribute(
                'monitor_stage_label',
                $stage['label']
            );

            $order->setAttribute(
                'monitor_stage_note',
                $stage['note']
            );

            $order->setAttribute(
                'monitor_stage_step',
                $stage['step']
            );

            $order->setAttribute(
                'monitor_stage_group',
                $stage['group']
            );

            $order->setAttribute(
                'monitor_stage_tone',
                $stage['tone']
            );

            $order->setAttribute(
                'monitor_last_mile',
                $lastMile
            );

            $order->setAttribute(
                'monitor_transfer',
                $transfer
            );

            $handler =
                $this->resolveCurrentHandler(
                    $order,
                    $lastMile,
                    $transfer
                );

            $order->setAttribute(
                'monitor_handler_name',
                $handler['name']
            );

            $order->setAttribute(
                'monitor_handler_role',
                $handler['role']
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Current Journey Stage
    |--------------------------------------------------------------------------
    */

    private function resolveStage(
        MarketplaceOrder $order,
        ?string $parcelStatus
    ): array {
        if ($order->status === 'delivered') {
            return [
                'key' =>
                    'delivered',

                'label' =>
                    'Delivered',

                'note' =>
                    'Parcel handoff to Buyer completed.',

                'step' =>
                    7,

                'group' =>
                    'delivered',

                'tone' =>
                    'green',
            ];
        }

        if (
            $order->status === 'arrived_buyer'
            ||
            $parcelStatus === 'arrived_buyer'
        ) {
            return [
                'key' =>
                    'arrived_buyer',

                'label' =>
                    'Arrived at Buyer',

                'note' =>
                    'Local Rider has reached the delivery address.',

                'step' =>
                    7,

                'group' =>
                    'last_mile',

                'tone' =>
                    'blue',
            ];
        }

        switch ($parcelStatus) {
            case 'out_for_delivery':
                return [
                    'key' =>
                        'out_for_delivery',

                    'label' =>
                        'Out for Delivery',

                    'note' =>
                        'Local Rider is delivering the parcel to the Buyer.',

                    'step' =>
                        6,

                    'group' =>
                        'last_mile',

                    'tone' =>
                        'blue',
                ];

            case 'last_mile_assigned':
                return [
                    'key' =>
                        'last_mile_assigned',

                    'label' =>
                        'Local Rider Assigned',

                    'note' =>
                        'Parcel is assigned to a Rider for final delivery.',

                    'step' =>
                        6,

                    'group' =>
                        'last_mile',

                    'tone' =>
                        'amber',
                ];

            case 'destination_received':
                return [
                    'key' =>
                        'destination_received',

                    'label' =>
                        'At Destination Hub',

                    'note' =>
                        'Linehaul handoff completed at the destination facility.',

                    'step' =>
                        5,

                    'group' =>
                        'hub',

                    'tone' =>
                        'green',
                ];

            case 'linehaul_in_transit':
                return [
                    'key' =>
                        'linehaul_in_transit',

                    'label' =>
                        'Hub Transfer In Transit',

                    'note' =>
                        'Parcel is moving between sorting facilities.',

                    'step' =>
                        4,

                    'group' =>
                        'linehaul',

                    'tone' =>
                        'blue',
                ];

            case 'linehaul_assigned':
                return [
                    'key' =>
                        'linehaul_assigned',

                    'label' =>
                        'Transfer Manifest Ready',

                    'note' =>
                        'Parcel is assigned to a Hub Transfer manifest.',

                    'step' =>
                        4,

                    'group' =>
                        'hub',

                    'tone' =>
                        'amber',
                ];

            case 'sorted':
                return [
                    'key' =>
                        'sorted',

                    'label' =>
                        'Sorted at Origin Hub',

                    'note' =>
                        'Parcel is sorted and ready for linehaul planning.',

                    'step' =>
                        3,

                    'group' =>
                        'hub',

                    'tone' =>
                        'green',
                ];

            case 'received':
                return [
                    'key' =>
                        'origin_received',

                    'label' =>
                        'Received at Origin Hub',

                    'note' =>
                        'Pickup Rider handoff has been confirmed.',

                    'step' =>
                        2,

                    'group' =>
                        'hub',

                    'tone' =>
                        'green',
                ];

            case 'awaiting_intake':
                return [
                    'key' =>
                        'awaiting_intake',

                    'label' =>
                        'Heading to Origin Hub',

                    'note' =>
                        'Pickup Rider is carrying the parcel to the origin facility.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'blue',
                ];

            case 'pickup_verified':
                return [
                    'key' =>
                        'pickup_verified',

                    'label' =>
                        'Pickup Verified',

                    'note' =>
                        'Seller parcel is verified for pickup dispatch.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'amber',
                ];
        }

        switch ($order->status) {
            case 'arrived_pickup':
                return [
                    'key' =>
                        'arrived_pickup',

                    'label' =>
                        'Rider at Seller',

                    'note' =>
                        'Pickup Rider has arrived at the Seller location.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'amber',
                ];

            case 'heading_pickup':
                return [
                    'key' =>
                        'heading_pickup',

                    'label' =>
                        'Heading to Seller',

                    'note' =>
                        'Pickup Rider is travelling to the Seller.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'blue',
                ];

            case 'courier_accepted':
                return [
                    'key' =>
                        'courier_accepted',

                    'label' =>
                        'Pickup Rider Assigned',

                    'note' =>
                        'A Rider has accepted the Seller pickup batch.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'amber',
                ];

            default:
                return [
                    'key' =>
                        'in_transit',

                    'label' =>
                        $order->statusLabel(),

                    'note' =>
                        'Delivery operation is currently in progress.',

                    'step' =>
                        1,

                    'group' =>
                        'pickup',

                    'tone' =>
                        'neutral',
                ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Current Handler
    |--------------------------------------------------------------------------
    */

    private function resolveCurrentHandler(
        MarketplaceOrder $order,
        mixed $lastMile,
        mixed $transfer
    ): array {
        $stage =
            $order->getAttribute(
                'monitor_stage_key'
            );

        if (
            in_array(
                $stage,
                [
                    'last_mile_assigned',
                    'out_for_delivery',
                    'arrived_buyer',
                    'delivered',
                ],
                true
            )
            &&
            $lastMile
        ) {
            $name =
                trim(
                    $lastMile->first_name
                    . ' '
                    . $lastMile->last_name
                );

            return [
                'name' =>
                    $name ?: 'Local Rider',

                'role' =>
                    'Last-Mile Rider',
            ];
        }

        if (
            $stage === 'linehaul_in_transit'
            &&
            $transfer
        ) {
            return [
                'name' =>
                    $transfer->driver_name
                    ?: 'Linehaul Driver',

                'role' =>
                    'Linehaul Driver',
            ];
        }

        if (
            in_array(
                $stage,
                [
                    'origin_received',
                    'sorted',
                    'linehaul_assigned',
                    'destination_received',
                ],
                true
            )
        ) {
            return [
                'name' =>
                    'Sorting Facility',

                'role' =>
                    'Hub Operations',
            ];
        }

        return [
            'name' =>
                $order->courier_name
                ?: 'Pickup Rider',

            'role' =>
                'Pickup Rider',
        ];
    }
}