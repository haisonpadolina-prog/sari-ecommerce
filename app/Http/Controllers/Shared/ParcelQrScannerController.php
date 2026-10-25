<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Courier\CourierLastMileController;
use App\Http\Controllers\Logistics\LogisticsHubTransferController;
use App\Http\Controllers\Logistics\LogisticsIncomingParcelController;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\ParcelQrScanRecorder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\ParcelQrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParcelQrScannerController extends Controller
{
    public function __construct(
        private readonly ParcelQrCode $parcelQrCode,
        private readonly ParcelQrScanRecorder $scanRecorder,
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsIncomingParcelController $incomingParcels,
        private readonly LogisticsHubTransferController $hubTransfers,
        private readonly CourierLastMileController $lastMile,
    ) {
    }

    public function courier(Request $request): View
    {
        abort_unless($request->session()->get('is_courier'), 403);

        return view('courier.parcel-scanner');
    }

    public function logistics(Request $request): View
    {
        CurrentLogisticsAccount::resolve($request);
        CurrentLogisticsHub::requireOperatingFacility($request);

        return view('logistics.parcel-scanner');
    }

    public function scanCourier(Request $request): JsonResponse
    {
        abort_unless($request->session()->get('is_courier'), 403);

        $validated = $request->validate([
            'payload' => ['required', 'string', 'max:1000'],
        ]);

        $order = $this->parcelQrCode->resolve($validated['payload']);
        $rider = $this->currentRider($request);
        $parcel = LogisticsParcel::query()
            ->where('marketplace_order_id', $order->id)
            ->first();

        $assignment = DB::table('last_mile_assignment_orders as item')
            ->join(
                'last_mile_assignments as assignment',
                'assignment.id',
                '=',
                'item.last_mile_assignment_id'
            )
            ->where('item.marketplace_order_id', $order->id)
            ->where('assignment.courier_account_id', $rider->id)
            ->select([
                'assignment.id',
                'assignment.status',
                'assignment.logistics_account_id',
            ])
            ->first();

        if (
            $assignment
            && $parcel
            && in_array(
                (string) $parcel->status,
                ['last_mile_assigned', 'out_for_delivery', 'arrived_buyer', 'delivered'],
                true
            )
        ) {
            return $this->handleLastMileRiderScan(
                $request,
                $order,
                $rider,
                $assignment,
                $parcel
            );
        }

        if ($this->isPickupRiderFor($rider, $order)) {
            return $this->handlePickupRiderScan($order, $rider);
        }

        throw ValidationException::withMessages([
            'payload' => 'This parcel is not assigned to your Rider account.',
        ]);
    }

    public function scanLogistics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payload' => ['required', 'string', 'max:1000'],
        ]);

        $order = $this->parcelQrCode->resolve($validated['payload']);
        $logistics = CurrentLogisticsAccount::resolve($request);

        abort_unless(
            (int) $order->logistics_account_id === (int) $logistics->id,
            403,
            'This parcel belongs to another Logistics provider.'
        );

        $parcel = LogisticsParcel::query()
            ->where('marketplace_order_id', $order->id)
            ->first();

        if (! $parcel) {
            throw ValidationException::withMessages([
                'payload' => 'This order does not have a Logistics parcel record yet.',
            ]);
        }

        [$actorRole, $actorId, $facilityId] = $this->logisticsActor($request, (int) $logistics->id);

        if ($parcel->status === 'awaiting_intake') {
            $this->incomingParcels->receive($request, $order);

            $originFacilityId = (int) (
                $order->deliveryRouteStops()
                    ->where('stop_key', 'origin_facility')
                    ->value('logistics_facility_id')
                ?? 0
            );

            $this->scanRecorder->record(
                $order,
                'origin-hub:order:'.$order->id,
                'origin_hub_intake',
                $actorRole,
                $actorId,
                $originFacilityId > 0 ? $originFacilityId : $facilityId,
                'order',
                (int) $order->id
            );

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Origin Hub Received',
                'message' => 'QR verified. Parcel custody was received by the authorized origin hub and is ready for sorting.',
                'next_url' => route('logistics.parcel-sorting'),
                'next_label' => 'Open Parcel Sorting',
            ]);
        }

        if ($parcel->status === 'linehaul_assigned') {
            return $this->handleOriginHubTransferDispatchScan(
                $request,
                $order,
                $logistics->id,
                $actorRole,
                $actorId,
                $facilityId
            );
        }

        if ($parcel->status === 'linehaul_in_transit') {
            return $this->handleDestinationHubTransferScan(
                $request,
                $order,
                $logistics->id,
                $actorRole,
                $actorId,
                $facilityId
            );
        }

        if ($parcel->status === 'last_mile_assigned') {
            return $this->handleDestinationReadyForDeliveryScan(
                $order,
                $logistics->id,
                $actorRole,
                $actorId,
                $facilityId
            );
        }

        return response()->json($this->logisticsStatusResponse($order, $parcel));
    }

    private function currentRider(Request $request): CourierAccount
    {
        $riderId = (int) $request->session()->get('courier_account_id', 0);

        $rider = CourierAccount::query()
            ->whereKey($riderId)
            ->where('account_status', 'active')
            ->first();

        abort_unless($rider, 403, 'Active Rider session required.');

        return $rider;
    }

    private function isPickupRiderFor(
        CourierAccount $rider,
        MarketplaceOrder $order
    ): bool {
        return strtolower(trim((string) $order->courier_email))
            === strtolower(trim((string) $rider->email));
    }

    private function handlePickupRiderScan(
        MarketplaceOrder $order,
        CourierAccount $rider
    ): JsonResponse {
        $parcel = LogisticsParcel::query()
            ->where('marketplace_order_id', $order->id)
            ->first();

        if (! $parcel) {
            throw ValidationException::withMessages([
                'payload' => 'This assigned order is not a Logistics pickup parcel.',
            ]);
        }

        $order->refresh();

        if ($order->status === 'courier_accepted') {
            $order = $this->workflow->transition(
                $order,
                'courier_accepted',
                'heading_pickup',
                'Rider Is Heading to Pickup',
                ($order->courier_name ?: 'Rider')
                    .' started the Seller pickup trip after scanning the SARI parcel QR for order '
                    .$order->order_number.'.',
                'both',
                ['pickup_started_at' => now()]
            );
        }

        if ($order->status === 'heading_pickup') {
            $order = $this->workflow->transition(
                $order,
                'heading_pickup',
                'arrived_pickup',
                'Rider Arrived at Seller',
                ($order->courier_name ?: 'Rider')
                    .' verified the physical parcel at the Seller using its SARI QR for order '
                    .$order->order_number.'.',
                'both',
                ['arrived_pickup_at' => now()]
            );
        }

        if ($order->status === 'arrived_pickup') {
            $order = $this->workflow->transition(
                $order,
                'arrived_pickup',
                'in_transit',
                'Order Picked Up',
                'Order '
                    .$order->order_number
                    .' was QR-verified and physically picked up by '
                    .($order->courier_name ?: 'the Rider')
                    .'. It is now moving to the assigned origin Logistics hub.',
                'both',
                ['picked_up_at' => now()]
            );
        }

        if ($order->status !== 'in_transit' || ! $order->picked_up_at) {
            throw ValidationException::withMessages([
                'payload' => 'This parcel is not in a QR-pickup eligible state.',
            ]);
        }

        $this->scanRecorder->record(
            $order,
            'pickup-rider:order:'.$order->id,
            'pickup_rider_handoff',
            'rider',
            (int) $rider->id,
            null,
            'order',
            (int) $order->id
        );

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'stage' => 'Picked Up',
            'message' => 'QR verified. Seller handoff is complete and the parcel is now in transit to its origin hub.',
            'next_url' => route('courier.pickups'),
            'next_label' => 'Back to Seller Pickups',
        ]);
    }

    private function handleOriginHubTransferDispatchScan(
        Request $request,
        MarketplaceOrder $order,
        int $logisticsId,
        string $actorRole,
        int $actorId,
        ?int $facilityId
    ): JsonResponse {
        $transfer = DB::table('hub_transfer_parcels as item')
            ->join(
                'logistics_parcels as parcel',
                'parcel.id',
                '=',
                'item.logistics_parcel_id'
            )
            ->join(
                'hub_transfers as transfer',
                'transfer.id',
                '=',
                'item.hub_transfer_id'
            )
            ->where('parcel.marketplace_order_id', $order->id)
            ->where('transfer.logistics_account_id', $logisticsId)
            ->where('transfer.status', 'preparing')
            ->select([
                'transfer.id',
                'transfer.transfer_code',
                'transfer.origin_facility_id',
                'transfer.destination_facility_id',
            ])
            ->first();

        if (! $transfer) {
            throw ValidationException::withMessages([
                'payload' => 'This parcel is assigned for linehaul, but no preparing Hub Transfer manifest contains it.',
            ]);
        }

        if ($facilityId !== null) {
            abort_unless(
                (int) $transfer->origin_facility_id === $facilityId,
                403,
                'Only the origin hub may scan this parcel for transfer dispatch.'
            );
        }

        $this->scanRecorder->record(
            $order,
            'origin-transfer:'.$transfer->id.':order:'.$order->id,
            'origin_hub_transfer_scan',
            $actorRole,
            $actorId,
            (int) $transfer->origin_facility_id,
            'hub_transfer',
            (int) $transfer->id
        );

        $total = DB::table('hub_transfer_parcels')
            ->where('hub_transfer_id', $transfer->id)
            ->count();

        $scanned = $this->scanRecorder->countForContext(
            'hub_transfer',
            (int) $transfer->id,
            'origin_hub_transfer_scan'
        );

        if ($scanned >= $total && $total > 0) {
            $this->hubTransfers->dispatch($request, (int) $transfer->id);

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Hub Transfer Dispatched',
                'message' => 'QR verified. All '.$total.' parcel(s) in transfer '.$transfer->transfer_code.' were scanned at the origin hub, so the transfer is now in transit to the destination hub.',
                'next_url' => route('logistics.hub-transfers'),
                'next_label' => 'Open Hub Transfers',
            ]);
        }

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'stage' => 'Transfer Loading '.$scanned.'/'.$total,
            'message' => 'QR verified for transfer '.$transfer->transfer_code.'. Scan the remaining parcel(s) before the Hub Transfer can depart.',
            'next_url' => route('logistics.hub-transfers'),
            'next_label' => 'Open Hub Transfers',
        ]);
    }

    private function handleDestinationHubTransferScan(
        Request $request,
        MarketplaceOrder $order,
        int $logisticsId,
        string $actorRole,
        int $actorId,
        ?int $facilityId
    ): JsonResponse {
        $transfer = DB::table('hub_transfer_parcels as item')
            ->join(
                'logistics_parcels as parcel',
                'parcel.id',
                '=',
                'item.logistics_parcel_id'
            )
            ->join(
                'hub_transfers as transfer',
                'transfer.id',
                '=',
                'item.hub_transfer_id'
            )
            ->where('parcel.marketplace_order_id', $order->id)
            ->where('transfer.logistics_account_id', $logisticsId)
            ->where('transfer.status', 'in_transit')
            ->select([
                'transfer.id',
                'transfer.transfer_code',
                'transfer.destination_facility_id',
            ])
            ->first();

        if (! $transfer) {
            throw ValidationException::withMessages([
                'payload' => 'No active in-transit Hub Transfer contains this parcel.',
            ]);
        }

        if ($facilityId !== null) {
            abort_unless(
                (int) $transfer->destination_facility_id === $facilityId,
                403,
                'This parcel is assigned to another destination hub.'
            );
        }

        $this->scanRecorder->record(
            $order,
            'destination-transfer:'.$transfer->id.':order:'.$order->id,
            'destination_hub_transfer_scan',
            $actorRole,
            $actorId,
            (int) $transfer->destination_facility_id,
            'hub_transfer',
            (int) $transfer->id
        );

        $total = DB::table('hub_transfer_parcels')
            ->where('hub_transfer_id', $transfer->id)
            ->count();

        $scanned = $this->scanRecorder->countForContext(
            'hub_transfer',
            (int) $transfer->id,
            'destination_hub_transfer_scan'
        );

        if ($scanned >= $total && $total > 0) {
            $this->hubTransfers->receive($request, (int) $transfer->id);

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Destination Hub Received',
                'message' => 'QR verified. All '.$total.' parcel(s) in transfer '.$transfer->transfer_code.' were scanned, so the transfer was received and released to Last-Mile Assignment.',
                'next_url' => route('logistics.last-mile-assignment'),
                'next_label' => 'Open Last-Mile Assignment',
            ]);
        }

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'stage' => 'Destination Hub Scan '.$scanned.'/'.$total,
            'message' => 'QR verified for transfer '.$transfer->transfer_code.'. Scan the remaining parcel(s) before the transfer can be received as a complete batch.',
            'next_url' => route('logistics.hub-transfers'),
            'next_label' => 'Open Hub Transfers',
        ]);
    }

    private function handleDestinationReadyForDeliveryScan(
        MarketplaceOrder $order,
        int $logisticsId,
        string $actorRole,
        int $actorId,
        ?int $facilityId
    ): JsonResponse {
        $assignment = DB::table('last_mile_assignment_orders as item')
            ->join(
                'last_mile_assignments as assignment',
                'assignment.id',
                '=',
                'item.last_mile_assignment_id'
            )
            ->where('item.marketplace_order_id', $order->id)
            ->where('assignment.logistics_account_id', $logisticsId)
            ->whereIn('assignment.status', ['assigned', 'out_for_delivery'])
            ->select([
                'assignment.id',
                'assignment.courier_account_id',
                'assignment.status',
            ])
            ->first();

        if (! $assignment) {
            throw ValidationException::withMessages([
                'payload' => 'Assign this destination-hub parcel to a local Rider before scanning it as Ready for Delivery.',
            ]);
        }

        $destinationFacilityId = (int) (
            $order->deliveryRouteStops()
                ->where('stop_key', 'destination_facility')
                ->value('logistics_facility_id')
            ?? 0
        );

        if ($facilityId !== null && $destinationFacilityId > 0) {
            abort_unless(
                $destinationFacilityId === $facilityId,
                403,
                'Only the destination hub may scan this parcel as Ready for Delivery.'
            );
        }

        $this->scanRecorder->record(
            $order,
            'ready-delivery:'.$assignment->id.':order:'.$order->id,
            'destination_ready_for_delivery',
            $actorRole,
            $actorId,
            $destinationFacilityId > 0 ? $destinationFacilityId : $facilityId,
            'last_mile_assignment',
            (int) $assignment->id
        );

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'stage' => 'Ready for Delivery',
            'message' => 'QR verified at the destination hub. This parcel is cleared for the assigned local Rider. The Rider must scan the same parcel QR before departure and again at the Buyer to complete delivery.',
            'next_url' => route('logistics.delivery-monitoring'),
            'next_label' => 'Open Delivery Monitoring',
        ]);
    }

    private function handleLastMileRiderScan(
        Request $request,
        MarketplaceOrder $order,
        CourierAccount $rider,
        object $assignment,
        LogisticsParcel $parcel
    ): JsonResponse {
        if ($parcel->status === 'delivered' || $assignment->status === 'completed') {
            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Delivered',
                'message' => 'QR verified. This parcel is already delivered and the last-mile record is complete.',
                'next_url' => route('courier.history'),
                'next_label' => 'Open Delivery History',
            ]);
        }

        if ($assignment->status === 'assigned') {
            $readyScanExists = DB::table('parcel_qr_scan_events')
                ->where('marketplace_order_id', $order->id)
                ->where('checkpoint', 'destination_ready_for_delivery')
                ->where('context_type', 'last_mile_assignment')
                ->where('context_id', (int) $assignment->id)
                ->exists();

            if (! $readyScanExists) {
                throw ValidationException::withMessages([
                    'payload' => 'Logistics must scan this parcel at the destination hub and mark it Ready for Delivery before the Rider can take custody.',
                ]);
            }

            $this->scanRecorder->record(
                $order,
                'last-mile:'.$assignment->id.':order:'.$order->id,
                'last_mile_rider_handoff',
                'rider',
                (int) $rider->id,
                null,
                'last_mile_assignment',
                (int) $assignment->id
            );

            $total = DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', $assignment->id)
                ->count();

            $scanned = $this->scanRecorder->countForContext(
                'last_mile_assignment',
                (int) $assignment->id,
                'last_mile_rider_handoff'
            );

            if ($scanned >= $total && $total > 0) {
                $this->lastMile->start($request, (int) $assignment->id);

                return response()->json([
                    'ok' => true,
                    'order_number' => $order->order_number,
                    'stage' => 'Out for Delivery',
                    'message' => 'QR verified. All '.$total.' parcel(s) in your assigned batch were handed over by the destination hub, so your delivery route is now active. Scan each parcel again only when the Buyer physically receives it.',
                    'next_url' => route('courier.last-mile'),
                    'next_label' => 'Open Last-Mile Deliveries',
                ]);
            }

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Rider Handoff '.$scanned.'/'.$total,
                'message' => 'QR verified. Scan the remaining parcel(s) in this assigned batch before leaving the destination hub.',
                'next_url' => route('courier.last-mile'),
                'next_label' => 'Open Last-Mile Deliveries',
            ]);
        }

        if ($assignment->status === 'out_for_delivery') {
            if ($parcel->status === 'out_for_delivery') {
                $this->lastMile->arrived(
                    $request,
                    (int) $assignment->id,
                    (int) $order->id
                );
            }

            $parcel->refresh();
            $order->refresh();

            if ($parcel->status === 'arrived_buyer' && $order->status === 'arrived_buyer') {
                $this->lastMile->delivered(
                    $request,
                    (int) $assignment->id,
                    (int) $order->id
                );
            }

            $order->refresh();
            $parcel->refresh();

            if ($parcel->status !== 'delivered' || $order->status !== 'delivered') {
                throw ValidationException::withMessages([
                    'payload' => 'The final delivery scan could not complete this parcel. Refresh your delivery assignment and try again.',
                ]);
            }

            $this->scanRecorder->record(
                $order,
                'final-delivery:'.$assignment->id.':order:'.$order->id,
                'final_delivery_scan',
                'rider',
                (int) $rider->id,
                null,
                'last_mile_assignment',
                (int) $assignment->id
            );

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Delivery Successful',
                'message' => 'Final parcel QR scan recorded. The Buyer delivery is complete and COD collection is now recorded as paid for this order.',
                'next_url' => route('courier.history'),
                'next_label' => 'Open Delivery History',
            ]);
        }

        throw ValidationException::withMessages([
            'payload' => 'This last-mile parcel is not ready for a Rider QR checkpoint.',
        ]);
    }

    /**
     * @return array{0: string, 1: int, 2: int|null}
     */
    private function logisticsActor(Request $request, int $logisticsId): array
    {
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        if (CurrentLogisticsHub::isHubOperator($request)) {
            return [
                'hub_operator',
                (int) $request->session()->get('logistics_hub_account_id', 0),
                (int) $facility->id,
            ];
        }

        return ['logistics_manager', $logisticsId, (int) $facility->id];
    }

    private function logisticsStatusResponse(
        MarketplaceOrder $order,
        LogisticsParcel $parcel
    ): array {
        return match ((string) $parcel->status) {
            'received' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'At Origin Hub',
                'message' => 'QR verified. This parcel was already received and is ready for sorting.',
                'next_url' => route('logistics.parcel-sorting'),
                'next_label' => 'Open Parcel Sorting',
            ],
            'sorted' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Sorted — Create Transfer Manifest',
                'message' => 'QR verified. This parcel is sorted. Add it to a Hub Transfer manifest, then scan it again at the origin hub when physically loading the transfer.',
                'next_url' => route('logistics.hub-transfers'),
                'next_label' => 'Open Hub Transfers',
            ],
            'linehaul_assigned' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Ready for Hub Transfer Scan',
                'message' => 'QR verified. This parcel is assigned to a preparing Hub Transfer and is ready for the outbound origin-hub scan.',
                'next_url' => route('logistics.parcel-scanner'),
                'next_label' => 'Scan for Transfer Dispatch',
            ],
            'destination_received' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Destination Hub Received',
                'message' => 'QR verified. Assign a local Rider first. After assignment, scan this same parcel QR again at the destination hub to mark it Ready for Delivery.',
                'next_url' => route('logistics.last-mile-assignment'),
                'next_label' => 'Open Last-Mile Assignment',
            ],
            'last_mile_assigned', 'out_for_delivery' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Last Mile',
                'message' => 'QR verified. This parcel is already in the local Rider delivery workflow.',
                'next_url' => route('logistics.delivery-monitoring'),
                'next_label' => 'Open Delivery Monitoring',
            ],
            'arrived_buyer' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'At Buyer',
                'message' => 'QR verified. The last-mile Rider has reached the Buyer.',
                'next_url' => route('logistics.delivery-monitoring'),
                'next_label' => 'Open Delivery Monitoring',
            ],
            'delivered' => [
                'ok' => true,
                'order_number' => $order->order_number,
                'stage' => 'Delivered',
                'message' => 'QR verified. Delivery is already complete.',
                'next_url' => route('logistics.delivery-monitoring'),
                'next_label' => 'Open Delivery Monitoring',
            ],
            default => throw ValidationException::withMessages([
                'payload' => 'This parcel is not in a Logistics QR-scannable stage yet.',
            ]),
        };
    }
}
