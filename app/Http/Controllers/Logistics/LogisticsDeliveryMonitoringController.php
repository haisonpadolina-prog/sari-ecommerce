<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LogisticsDeliveryMonitoringController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'out_for_delivery',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {}

    public function index(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        $logistics = $this->orderAccess->prepare($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        $hubFacility = CurrentLogisticsHub::requireOperatingFacility($request);

        /*
        |--------------------------------------------------------------------------
        | Hub Identity and Provider Boundary
        |--------------------------------------------------------------------------
        |
        | Main Logistics:
        | - May monitor orders across its own Logistics network.
        |
        | Hub Operator:
        | - May monitor an order only when its actual origin or
        |   destination route stop belongs to the assigned facility.
        */

        if ($hubFacility) {
            abort_unless(
                (int) $hubFacility->logistics_account_id
                    === (int) $logistics->id,
                403,
                'Your assigned hub does not belong to this Logistics network.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Authorized Monitoring Scope
        |--------------------------------------------------------------------------
        |
        | Missing route stops do not grant Hub Operator visibility.
        | Monitoring does not generate or backfill delivery routes.
        */

        $visibleOrders = CurrentLogisticsHub::scopeOrders(
            $request,
            MarketplaceOrder::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                ),
            'involved'
        );

        $relations = [
            'seller',
            'logisticsParcel',
            'deliveryRouteStops.facility',
        ];

        /*
        |--------------------------------------------------------------------------
        | Active Marketplace Orders
        |--------------------------------------------------------------------------
        |
        | Include ready_for_pickup so tracking begins at the actual
        | Seller pickup stage, before a Rider has been assigned.
        |
        | Do not require courier_email for the entire active queue.
        |
        | courier_name and courier_email remain the historical
        | Pickup Rider snapshot. Actual Last-Mile Rider data is
        | loaded separately below.
        */

        $activeOrders = (clone $visibleOrders)
            ->with($relations)
            ->whereIn(
                'status',
                self::ACTIVE_DELIVERY_STATUSES
            )
            ->latest('updated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Delivered Today
        |--------------------------------------------------------------------------
        |
        | Preserve the existing latest-20 delivery history.
        | Calculate the KPI independently, without the 20-row cap.
        */

        $today = now()->toDateString();

        $deliveredTodayCount = (clone $visibleOrders)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', $today)
            ->count();

        $deliveredToday = (clone $visibleOrders)
            ->with($relations)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', $today)
            ->latest('delivered_at')
            ->limit(20)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Authorized Related Metadata
        |--------------------------------------------------------------------------
        |
        | Load Rider and transfer information only for orders already
        | included in the authenticated provider/hub monitoring scope.
        */

        $allOrders = $activeOrders
            ->concat($deliveredToday)
            ->unique('id')
            ->values();

        $orderIds = $allOrders
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values();

        $lastMileAssignments = collect();

        $hubTransfers = collect();

        if ($orderIds->isNotEmpty()) {

            /*
            |--------------------------------------------------------------------------
            | Actual Last-Mile Rider Assignment
            |--------------------------------------------------------------------------
            */

            $lastMileAssignments = DB::table(
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
                ->where(
                    'assignment.logistics_account_id',
                    $logistics->id
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

            /*
            |--------------------------------------------------------------------------
            | Hub Transfer Information
            |--------------------------------------------------------------------------
            */

            $hubTransfers = DB::table(
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
                ->where(
                    'transfer.logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'orders.logistics_account_id',
                    $logistics->id
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
                ->orderBy('transfer.id')
                ->get()
                ->groupBy('order_id')
                ->map(
                    fn (Collection $rows) => $rows->last()
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Decorate Active and Delivered Orders
        |--------------------------------------------------------------------------
        |
        | Preserve every existing monitor_* field required by the Blade.
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
        | Accurate Monitoring Statistics
        |--------------------------------------------------------------------------
        |
        | Use the resolved monitoring stage.
        |
        | An order that already has a Last-Mile Rider assigned should
        | not be incorrectly counted as a parcel sitting at the hub.
        */

        $stats = [
            'active' => $activeOrders->count(),

            'at_hub' => $activeOrders
                ->filter(
                    fn (MarketplaceOrder $order): bool => in_array(
                        $order->getAttribute('monitor_stage_key'),
                        [
                            'origin_received',
                            'sorted',
                            'linehaul_assigned',
                            'destination_received',
                        ],
                        true
                    )
                )
                ->count(),

            'linehaul' => $activeOrders
                ->where(
                    'monitor_stage_group',
                    'linehaul'
                )
                ->count(),

            'out_for_delivery' => $activeOrders
                ->filter(
                    fn (MarketplaceOrder $order): bool => in_array(
                        $order->getAttribute('monitor_stage_key'),
                        [
                            'out_for_delivery',
                            'arrived_buyer',
                        ],
                        true
                    )
                )
                ->count(),

            'delivered_today' => $deliveredTodayCount,
        ];

        return view(
            'logistics.delivery-monitoring',
            compact(
                'activeOrders',
                'deliveredToday',
                'stats',
                'isHubOperator',
                'hubFacility'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Monitoring Stage and Metadata
    |--------------------------------------------------------------------------
    */

    private function decorateOrders(
        Collection $orders,
        Collection $lastMileAssignments,
        Collection $hubTransfers
    ): void {
        foreach ($orders as $order) {
            $lastMile = $lastMileAssignments->get(
                $order->id
            );

            $transfer = $hubTransfers->get(
                $order->id
            );

            $routeStops = $order
                ->deliveryRouteStops
                ->keyBy('stop_key');

            $origin = $routeStops->get(
                'origin_facility'
            );

            $destination = $routeStops->get(
                'destination_facility'
            );

            $originId = (int) (
                $origin?->logistics_facility_id ?? 0
            );

            $destinationId = (int) (
                $destination?->logistics_facility_id ?? 0
            );

            /*
             * Local handoff is valid only when BOTH route stops have
             * actual facility IDs and those IDs match.
             */

            $isLocalHandoff = $originId > 0
                && $destinationId > 0
                && $originId === $destinationId;

            $stage = $this->resolveStage(
                $order,
                $order->logisticsParcel?->status,
                $isLocalHandoff
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

            /*
             * Additional metadata for the upcoming Monitoring timeline
             * UI update. Does not break the existing Blade.
             */

            $order->setAttribute(
                'monitor_is_local_handoff',
                $isLocalHandoff
            );

            $handler = $this->resolveCurrentHandler(
                $order,
                $lastMile,
                $transfer,
                $origin,
                $destination
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
    | Resolve the Seven-Step Parcel Journey
    |--------------------------------------------------------------------------
    |
    | 1. Seller Pickup
    | 2. Origin Hub
    | 3. Sorting
    | 4. Hub Transfer (only when different hubs)
    | 5. Destination Hub
    | 6. Last Mile
    | 7. Buyer Delivery
    |
    | Order and parcel statuses are combined. Pickup Rider assignment
    | must NOT be mistaken for an actual confirmed Seller pickup.
    */

    private function resolveStage(
        MarketplaceOrder $order,
        ?string $parcelStatus,
        bool $isLocalHandoff
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Final Buyer Delivery
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'delivered') {
            return $this->stage(
                'delivered',
                'Delivered',
                'Parcel handoff to Buyer completed.',
                7,
                'delivered',
                'green'
            );
        }

        if (
            $order->status === 'arrived_buyer'
            || $parcelStatus === 'arrived_buyer'
        ) {
            return $this->stage(
                'arrived_buyer',
                'Arrived at Buyer',
                'Local Rider has reached the delivery address.',
                7,
                'last_mile',
                'blue'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Advanced Parcel Stages
        |--------------------------------------------------------------------------
        |
        | Once a parcel is physically received by a hub, its parcel
        | state becomes the authoritative Logistics progress indicator.
        */

        $lateStage = match ($parcelStatus) {
            'out_for_delivery' => $this->stage(
                'out_for_delivery',
                'Out for Delivery',
                'Local Rider is delivering the parcel to the Buyer.',
                6,
                'last_mile',
                'blue'
            ),

            'last_mile_assigned' => $this->stage(
                'last_mile_assigned',
                'Local Rider Assigned',
                'Parcel is assigned to a Rider for final delivery.',
                6,
                'last_mile',
                'amber'
            ),

            'destination_received' => $this->stage(
                'destination_received',

                $isLocalHandoff
                    ? 'Local Hub Handoff Complete'
                    : 'At Destination Hub',

                $isLocalHandoff
                    ? 'Origin and destination are the same hub. Parcel is ready for Last-Mile Assignment; no linehaul transfer is required.'
                    : 'Transfer receipt was confirmed at the destination hub. Parcel is ready for Last-Mile Assignment.',

                5,
                'hub',
                'green'
            ),

            'linehaul_in_transit' => $this->stage(
                'linehaul_in_transit',
                'Hub Transfer In Transit',
                'Parcel is moving between the origin and destination hubs.',
                4,
                'linehaul',
                'blue'
            ),

            'linehaul_assigned' => $this->stage(
                'linehaul_assigned',
                'Transfer Manifest Ready',
                'Parcel is on a transfer manifest awaiting dispatch at the origin hub.',
                4,
                'hub',
                'amber'
            ),

            'sorted' => $this->stage(
                'sorted',
                'Sorted at Origin Hub',
                'Parcel is sorted and ready for inter-hub transfer planning.',
                3,
                'hub',
                'green'
            ),

            'received' => $this->stage(
                'origin_received',
                'Received at Origin Hub',
                'The pickup Rider handoff has been confirmed at the origin hub.',
                2,
                'hub',
                'green'
            ),

            default => null,
        };

        if ($lateStage !== null) {
            return $lateStage;
        }

        /*
        |--------------------------------------------------------------------------
        | Seller Pickup Request
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'ready_for_pickup') {
            return $parcelStatus === 'pickup_verified'
                ? $this->stage(
                    'pickup_verified',
                    'Pickup Verified',
                    'Seller pickup request is verified and ready for Rider assignment.',
                    1,
                    'pickup',
                    'amber'
                )
                : $this->stage(
                    'ready_for_pickup',
                    'Awaiting Pickup Verification',
                    'Seller pickup is ready for review by the assigned origin hub.',
                    1,
                    'pickup',
                    'amber'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Actual Pickup Rider Workflow
        |--------------------------------------------------------------------------
        |
        | Pickup Assignment immediately changes the parcel to
        | awaiting_intake.
        |
        | Until picked_up_at is recorded, prioritize the actual order
        | Rider stage instead of falsely claiming the parcel is already
        | travelling to the origin hub.
        */

        if (! $order->picked_up_at) {
            $pickupStage = match ($order->status) {
                'arrived_pickup' => $this->stage(
                    'arrived_pickup',
                    'Rider at Seller',
                    'The assigned pickup Rider arrived at the Seller location.',
                    1,
                    'pickup',
                    'amber'
                ),

                'heading_pickup' => $this->stage(
                    'heading_pickup',
                    'Heading to Seller',
                    'The assigned pickup Rider is travelling to the Seller.',
                    1,
                    'pickup',
                    'blue'
                ),

                'courier_accepted' => $this->stage(
                    'courier_accepted',
                    'Pickup Rider Assigned',
                    'A Rider has been assigned to collect this Seller pickup batch.',
                    1,
                    'pickup',
                    'amber'
                ),

                default => null,
            };

            if ($pickupStage !== null) {
                return $pickupStage;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Confirmed Seller Pickup → Origin Hub
        |--------------------------------------------------------------------------
        */

        if (
            $parcelStatus === 'awaiting_intake'
            && $order->picked_up_at
        ) {
            return $this->stage(
                'awaiting_intake',
                'Heading to Origin Hub',
                'Seller pickup is confirmed and the parcel is awaiting origin-hub intake.',
                1,
                'pickup',
                'blue'
            );
        }

        if ($parcelStatus === 'pickup_verified') {
            return $this->stage(
                'pickup_verified',
                'Pickup Verified',
                'Seller parcel is verified for Rider assignment.',
                1,
                'pickup',
                'amber'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Other In-Transit State
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'in_transit') {
            return $this->stage(
                'in_transit',

                $order->picked_up_at
                    ? 'Pickup Completed'
                    : 'Pickup In Transit',

                $order->picked_up_at
                    ? 'Seller pickup is recorded; waiting for the next Logistics milestone.'
                    : 'Pickup status is in transit, but the pickup confirmation is not yet recorded.',

                1,
                'pickup',

                $order->picked_up_at
                    ? 'blue'
                    : 'neutral'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Safe Fallback
        |--------------------------------------------------------------------------
        */

        return $this->stage(
            'pickup_pending',
            $order->statusLabel(),
            'Delivery operation is currently in progress.',
            1,
            'pickup',
            'neutral'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Standardized Stage Data
    |--------------------------------------------------------------------------
    */

    private function stage(
        string $key,
        string $label,
        string $note,
        int $step,
        string $group,
        string $tone
    ): array {
        return compact(
            'key',
            'label',
            'note',
            'step',
            'group',
            'tone'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Current Handler
    |--------------------------------------------------------------------------
    |
    | The current handler must reflect the current delivery leg,
    | not always the original Pickup Rider snapshot.
    */

    private function resolveCurrentHandler(
        MarketplaceOrder $order,
        mixed $lastMile,
        mixed $transfer,
        mixed $origin,
        mixed $destination
    ): array {
        $stage = $order->getAttribute(
            'monitor_stage_key'
        );

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Rider
        |--------------------------------------------------------------------------
        */

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
        ) {
            $name = $lastMile
                ? trim(
                    (string) $lastMile->first_name
                    .' '
                    .(string) $lastMile->last_name
                )
                : '';

            return [
                'name' => $name
                    ?: 'Local Rider (details pending)',

                'role' => 'Last-Mile Rider',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Inter-Hub Linehaul Driver
        |--------------------------------------------------------------------------
        */

        if ($stage === 'linehaul_in_transit') {
            return [
                'name' => $transfer?->driver_name
                    ?: 'Linehaul Driver (details pending)',

                'role' => 'Linehaul Driver',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Origin Hub Operations
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $stage,
                [
                    'origin_received',
                    'sorted',
                    'linehaul_assigned',
                ],
                true
            )
        ) {
            return [
                'name' => $origin?->facility?->name
                    ?: $origin?->name
                    ?: 'Origin Hub',

                'role' => 'Origin Hub Operations',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Destination Hub Operations
        |--------------------------------------------------------------------------
        */

        if ($stage === 'destination_received') {
            return [
                'name' => $destination?->facility?->name
                    ?: $destination?->name
                    ?: 'Destination Hub',

                'role' => 'Destination Hub Operations',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Initial Pickup Queue
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $stage,
                [
                    'ready_for_pickup',
                    'pickup_verified',
                ],
                true
            )
        ) {
            return [
                'name' => $origin?->facility?->name
                    ?: $origin?->name
                    ?: 'Origin Hub Queue',

                'role' => 'Pickup Operations',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Pickup Rider
        |--------------------------------------------------------------------------
        */

        return [
            'name' => $order->courier_name
                ?: 'Pickup Rider (not yet assigned)',

            'role' => 'Pickup Rider',
        ];
    }
}