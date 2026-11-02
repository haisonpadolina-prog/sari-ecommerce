<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileRiderLastMileController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $assignments = DB::table('last_mile_assignments as assignment')
            ->join('logistics_facilities as facility', 'facility.id', '=', 'assignment.destination_facility_id')
            ->where('assignment.courier_account_id', $rider->id)
            ->whereIn('assignment.status', ['assigned', 'out_for_delivery', 'completed'])
            ->select([
                'assignment.*',
                'facility.name as facility_name',
                'facility.address as facility_address',
                'facility.municipality_name as facility_municipality',
            ])
            ->orderByRaw("CASE WHEN assignment.status = 'out_for_delivery' THEN 1 WHEN assignment.status = 'assigned' THEN 2 ELSE 3 END")
            ->orderByDesc('assignment.id')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->parcel_count = DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', $assignment->id)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $assignments->map(fn ($assignment) => $this->assignmentPayload($assignment))->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function show(Request $request, int $assignment): JsonResponse
    {
        $rider = $this->rider($request);
        $lastMile = $this->assignment($rider, $assignment);

        $orders = DB::table('last_mile_assignment_orders as link')
            ->join('marketplace_orders as orders', 'orders.id', '=', 'link.marketplace_order_id')
            ->leftJoin('logistics_parcels as parcel', 'parcel.marketplace_order_id', '=', 'orders.id')
            ->where('link.last_mile_assignment_id', $assignment)
            ->select([
                'orders.id', 'orders.order_number', 'orders.buyer_name', 'orders.buyer_phone',
                'orders.buyer_address', 'orders.status as order_status', 'orders.total',
                'orders.payment_method', 'parcel.status as parcel_status',
            ])
            ->orderBy('orders.order_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => $this->assignmentPayload($lastMile),
                'orders' => $orders,
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function start(Request $request, int $assignment): JsonResponse
    {
        $rider = $this->rider($request);

        DB::transaction(function () use ($rider, $assignment): void {
            $lastMile = DB::table('last_mile_assignments')
                ->where('id', $assignment)
                ->where('courier_account_id', $rider->id)
                ->lockForUpdate()
                ->first();

            abort_unless($lastMile, 404);

            if ($lastMile->status !== 'assigned') {
                throw ValidationException::withMessages([
                    'assignment' => 'This last-mile assignment cannot be started.',
                ]);
            }

            $orderIds = DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', $assignment)
                ->pluck('marketplace_order_id');

            if ($orderIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'assignment' => 'This last-mile assignment has no parcels.',
                ]);
            }

            $eligibleParcels = DB::table('logistics_parcels')
                ->whereIn('marketplace_order_id', $orderIds)
                ->where('status', 'last_mile_assigned')
                ->lockForUpdate()
                ->count();

            if ($eligibleParcels !== $orderIds->count()) {
                throw ValidationException::withMessages([
                    'assignment' => 'One or more parcels are no longer ready for last-mile delivery.',
                ]);
            }

            DB::table('last_mile_assignments')->where('id', $assignment)->update([
                'status' => 'out_for_delivery',
                'started_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('logistics_parcels')
                ->whereIn('marketplace_order_id', $orderIds)
                ->where('status', 'last_mile_assigned')
                ->update([
                    'status' => 'out_for_delivery',
                    'updated_at' => now(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Last-mile delivery route started.',
        ]);
    }

    public function arrived(Request $request, int $assignment, MarketplaceOrder $order): JsonResponse
    {
        $rider = $this->rider($request);

        DB::transaction(function () use ($rider, $assignment, $order): void {
            $lastMile = $this->lockedAssignment($rider, $assignment);

            if ($lastMile->status !== 'out_for_delivery') {
                throw ValidationException::withMessages([
                    'assignment' => 'Start the last-mile delivery route first.',
                ]);
            }

            $this->guardLink($assignment, $order->id);

            $parcel = DB::table('logistics_parcels')
                ->where('marketplace_order_id', $order->id)
                ->lockForUpdate()
                ->first();
            abort_unless($parcel, 404);

            if ($parcel->status === 'arrived_buyer' && $order->status === 'arrived_buyer') {
                return;
            }

            if ($parcel->status !== 'out_for_delivery' || $order->status !== 'in_transit') {
                throw ValidationException::withMessages([
                    'order' => 'This parcel is not currently out for delivery.',
                ]);
            }

            $this->workflow->transition(
                $order,
                'in_transit',
                'arrived_buyer',
                'Last-Mile Rider Reached Buyer',
                $this->riderName($rider).' reached the buyer location for order '.$order->order_number.'.',
                'both',
                ['arrived_buyer_at' => now()]
            );

            DB::table('logistics_parcels')
                ->where('marketplace_order_id', $order->id)
                ->update(['status' => 'arrived_buyer', 'updated_at' => now()]);
        });

        return response()->json(['success' => true, 'message' => 'Arrival at Buyer confirmed.']);
    }

    public function delivered(Request $request, int $assignment, MarketplaceOrder $order): JsonResponse
    {
        $rider = $this->rider($request);

        DB::transaction(function () use ($rider, $assignment, $order): void {
            $lastMile = $this->lockedAssignment($rider, $assignment);
            $this->guardLink($assignment, $order->id);

            $parcel = DB::table('logistics_parcels')
                ->where('marketplace_order_id', $order->id)
                ->lockForUpdate()
                ->first();
            abort_unless($parcel, 404);

            if ($parcel->status === 'delivered' && $order->status === 'delivered') {
                return;
            }

            if ($parcel->status !== 'arrived_buyer' || $order->status !== 'arrived_buyer') {
                throw ValidationException::withMessages([
                    'order' => 'Confirm arrival at the Buyer before completing delivery.',
                ]);
            }

            $this->workflow->transition(
                $order,
                'arrived_buyer',
                'delivered',
                'Order Delivered Successfully',
                'Order '.$order->order_number.' was successfully delivered by '.$this->riderName($rider).'.',
                'both',
                [
                    'delivered_at' => now(),
                    'payment_status' => $order->payment_method === 'COD' ? 'paid' : $order->payment_status,
                ],
                $rider->id
            );

            DB::table('logistics_parcels')
                ->where('marketplace_order_id', $order->id)
                ->update(['status' => 'delivered', 'updated_at' => now()]);

            $remaining = DB::table('last_mile_assignment_orders as link')
                ->join('marketplace_orders as orders', 'orders.id', '=', 'link.marketplace_order_id')
                ->where('link.last_mile_assignment_id', $assignment)
                ->where('orders.status', '!=', 'delivered')
                ->count();

            if ($remaining === 0 && $lastMile->status !== 'completed') {
                DB::table('last_mile_assignments')->where('id', $assignment)->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Last-mile delivery completed.']);
    }

    private function assignment(CourierAccount $rider, int $assignment): object
    {
        $row = DB::table('last_mile_assignments as assignment')
            ->join('logistics_facilities as facility', 'facility.id', '=', 'assignment.destination_facility_id')
            ->where('assignment.id', $assignment)
            ->where('assignment.courier_account_id', $rider->id)
            ->select([
                'assignment.*',
                'facility.name as facility_name',
                'facility.address as facility_address',
                'facility.municipality_name as facility_municipality',
            ])
            ->first();
        abort_unless($row, 404);
        return $row;
    }

    private function lockedAssignment(CourierAccount $rider, int $assignment): object
    {
        $row = DB::table('last_mile_assignments')
            ->where('id', $assignment)
            ->where('courier_account_id', $rider->id)
            ->lockForUpdate()
            ->first();
        abort_unless($row, 404);
        return $row;
    }

    private function guardLink(int $assignment, int $orderId): void
    {
        abort_unless(
            DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', $assignment)
                ->where('marketplace_order_id', $orderId)
                ->exists(),
            404
        );
    }

    private function assignmentPayload(object $assignment): array
    {
        return [
            'id' => (int) $assignment->id,
            'assignment_code' => $assignment->assignment_code,
            'delivery_area' => $assignment->delivery_area,
            'status' => $assignment->status,
            'facility_name' => $assignment->facility_name ?? null,
            'facility_address' => $assignment->facility_address ?? null,
            'facility_municipality' => $assignment->facility_municipality ?? null,
            'parcel_count' => isset($assignment->parcel_count) ? (int) $assignment->parcel_count : null,
            'assigned_at' => $assignment->assigned_at ?? null,
            'started_at' => $assignment->started_at ?? null,
            'completed_at' => $assignment->completed_at ?? null,
        ];
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }

    private function riderName(CourierAccount $rider): string
    {
        return trim($rider->first_name.' '.$rider->last_name) ?: 'Rider';
    }
}
