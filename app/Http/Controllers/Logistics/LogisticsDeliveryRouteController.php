<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\DeliveryRouteService;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LogisticsDeliveryRouteController extends Controller
{
    public function __construct(
        private readonly DeliveryRouteService $deliveryRoute,
        private readonly LogisticsOrderAccess $orderAccess,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Delivery Route / Journey
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        MarketplaceOrder $order
    ): View {
        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        $order->load([
            'seller',
            'buyer',
            'logisticsParcel',
            'logisticsAccount',
        ]);

        abort_unless(
            $order->logisticsParcel
                || $order->status === 'ready_for_pickup',
            404
        );

        /*
         * Ensure the standard route exists:
         *
         * Seller
         * -> Origin Hub
         * -> Destination Hub
         * -> Buyer
         */

        $this->deliveryRoute->ensureStops($order);

        /*
         * Synchronize route history with milestones already confirmed
         * through the actual operational workflow.
         */

        $this->syncOperationalMilestones($order);

        $stops = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->with('facility')
            ->orderBy('sequence')
            ->get();

        return view(
            'logistics.delivery-route',
            compact(
                'order',
                'stops'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Nearest Facilities
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {

        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        abort_unless(
            $order->logisticsParcel()->exists()
                || $order->status === 'ready_for_pickup',
            404
        );

        /*
         * Completed journeys must remain historical records.
         */

        if ($order->status === 'delivered') {
            throw ValidationException::withMessages([
                'route' => 'This delivery is already completed. Its route is now a read-only historical record.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Logs — Capture Previous Route
        |--------------------------------------------------------------------------
        |
        | A successful refresh does not automatically mean that
        | the assigned route has changed.
        |
        | Compare route details before and after the operation.
        |
        */

        $before = $this->routeSnapshot($order);

        /*
        |--------------------------------------------------------------------------
        | Existing Route Refresh
        |--------------------------------------------------------------------------
        */

        $this->deliveryRoute->refreshFacilities($order);

        /*
        |--------------------------------------------------------------------------
        | Activity Logs — Detect Actual Change
        |--------------------------------------------------------------------------
        */

        $after = $this->routeSnapshot($order);

        $request->attributes->set(
            'activity_changed',
            $before !== $after
        );

        /*
         * The middleware resolves the order ID automatically.
         *
         * A route refresh can affect both origin and destination hubs,
         * so its activity remains network-wide.
         */

        return back()->with(
            'success',
            'Nearest origin and destination facilities refreshed inside your Logistics network.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Facility Reached
    |--------------------------------------------------------------------------
    |
    | Available for active deliveries where a milestone was not already
    | provided by an operational module.
    |
    | Completed journeys are read-only.
    |
    */

    public function reached(
        Request $request,
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): RedirectResponse {

        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        /*
        |--------------------------------------------------------------------------
        | Completed Order Restriction
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'delivered') {
            throw ValidationException::withMessages([
                'route' => 'Completed delivery journeys are read-only historical records.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Existing Milestone Update
        |--------------------------------------------------------------------------
        |
        | The service validates:
        |
        | - Stop belongs to the selected order.
        | - Stop has not already been reached.
        | - The stop is an eligible sorting facility.
        | - Previous delivery milestones are satisfied.
        |
        */

        $this->deliveryRoute->markReached(
            $order,
            $stop
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | markReached() throws an exception for invalid operations.
        |
        | Reaching this section confirms that the milestone
        | was updated successfully.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        /*
         * Associate the activity with its actual facility when present.
         * The logger independently verifies facility ownership.
         */

        if ($stop->logistics_facility_id !== null) {

            $request->attributes->set(
                'activity_facility_id',
                (int) $stop->logistics_facility_id
            );
        }

        return back()->with(
            'success',
            $stop->name . ' marked as reached.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Snapshot For Audit Change Detection
    |--------------------------------------------------------------------------
    |
    | Compare meaningful route information without recording
    | raw address or coordinate values in Activity Logs.
    |
    | Exclude updated_at because a successful refresh may update
    | timestamps even when route details remain unchanged.
    |
    */

    private function routeSnapshot(
        MarketplaceOrder $order
    ): array {

        return DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->orderBy('sequence')
            ->orderBy('id')
            ->get()
            ->map(
                fn (DeliveryRouteStop $stop): array => [

                    'id' => (int) $stop->id,

                    'stop_key' => $stop->stop_key,

                    'sequence' => $stop->sequence,

                    'type' => $stop->type,

                    'name' => $stop->name,

                    'address_snapshot' => $stop->address_snapshot,

                    'logistics_facility_id' =>
                        $stop->logistics_facility_id,

                    'latitude' => $stop->latitude,

                    'longitude' => $stop->longitude,

                    'reached_at' =>
                        $stop->reached_at?->toISOString(),
                ]
            )
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Synchronize Operational Milestones
    |--------------------------------------------------------------------------
    |
    | Seller:
    |     Marketplace order pickup / Seller arrival
    |
    | Origin Hub:
    |     Logistics Incoming Parcel intake
    |
    | Destination Hub:
    |     Hub Transfer destination receipt
    |
    | Buyer:
    |     Last-Mile arrival at Buyer
    |
    */

    private function syncOperationalMilestones(
        MarketplaceOrder $order
    ): void {

        $stops = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->get()
            ->keyBy('stop_key');

        /** @var DeliveryRouteStop|null $sellerStop */

        $sellerStop = $stops->get(
            DeliveryRouteService::STOP_SELLER
        );

        /** @var DeliveryRouteStop|null $originStop */

        $originStop = $stops->get(
            DeliveryRouteService::STOP_ORIGIN_FACILITY
        );

        /** @var DeliveryRouteStop|null $destinationStop */

        $destinationStop = $stops->get(
            DeliveryRouteService::STOP_DESTINATION_FACILITY
        );

        /** @var DeliveryRouteStop|null $buyerStop */

        $buyerStop = $stops->get(
            DeliveryRouteService::STOP_BUYER
        );

        /*
        |--------------------------------------------------------------------------
        | Seller Pickup
        |--------------------------------------------------------------------------
        */

        $sellerReachedAt =
            $order->picked_up_at
            ?? $order->arrived_pickup_at;

        $this->applyReachedAtIfMissing(
            $sellerStop,
            $sellerReachedAt
        );

        /*
        |--------------------------------------------------------------------------
        | Origin Hub Intake
        |--------------------------------------------------------------------------
        */

        $parcel = DB::table('logistics_parcels')
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->first([
                'id',
                'received_at',
            ]);

        $this->applyReachedAtIfMissing(
            $originStop,
            $parcel?->received_at
        );

        /*
        |--------------------------------------------------------------------------
        | Destination Hub Receipt
        |--------------------------------------------------------------------------
        */

        $transfer = null;

        if ($parcel) {

            $transfer = DB::table(
                'hub_transfer_parcels as htp'
            )
                ->join(
                    'hub_transfers as ht',
                    'ht.id',
                    '=',
                    'htp.hub_transfer_id'
                )
                ->where(
                    'htp.logistics_parcel_id',
                    $parcel->id
                )
                ->select([
                    'ht.origin_facility_id',
                    'ht.destination_facility_id',
                    'ht.received_at',
                ])
                ->first();
        }

        /*
         * Confirm that the transfer matches the displayed route.
         */

        $transferMatchesRoute =
            $transfer
            && (
                ! $originStop?->logistics_facility_id

                || (int) $transfer->origin_facility_id
                    === (int) $originStop->logistics_facility_id
            )

            && (
                ! $destinationStop?->logistics_facility_id

                || (int) $transfer->destination_facility_id
                    === (int) $destinationStop->logistics_facility_id
            );

        if ($transferMatchesRoute) {

            $this->applyReachedAtIfMissing(
                $destinationStop,
                $transfer->received_at
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Buyer Arrival
        |--------------------------------------------------------------------------
        |
        | Prefer physical arrival time.
        | Use delivery time as a fallback for older records.
        |
        */

        $buyerReachedAt =
            $order->arrived_buyer_at
            ?? $order->delivered_at;

        $this->applyReachedAtIfMissing(
            $buyerStop,
            $buyerReachedAt
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Reached Timestamp Once
    |--------------------------------------------------------------------------
    |
    | Preserve existing historical timestamps.
    |
    */

    private function applyReachedAtIfMissing(
        ?DeliveryRouteStop $stop,
        mixed $timestamp
    ): void {

        if (
            ! $stop
            || $stop->reached_at
            || blank($timestamp)
        ) {
            return;
        }

        DeliveryRouteStop::query()
            ->whereKey($stop->id)
            ->whereNull('reached_at')
            ->update([
                'reached_at' => $timestamp,

                'updated_at' => now(),
            ]);
    }
}
