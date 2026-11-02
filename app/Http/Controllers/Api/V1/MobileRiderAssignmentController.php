<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MobileRiderAssignmentController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:40'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)])
            ->where(function (Builder $query): void {
                $query->where(function (Builder $direct): void {
                    $direct->whereDoesntHave('logisticsParcel')
                        ->whereIn('status', [
                            'courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer',
                        ]);
                })->orWhere(function (Builder $pickup): void {
                    $pickup->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit'])
                        ->whereHas('logisticsParcel', fn (Builder $parcel) => $parcel->where('status', 'awaiting_intake'));
                });
            })
            ->when(filled($validated['status'] ?? null), fn ($q) => $q->where('status', $validated['status']))
            ->latest('updated_at');

        $orders = $query->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn (MarketplaceOrder $order) => $this->present($order))->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function pickups(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)])
            ->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit'])
            ->whereHas('logisticsParcel', fn (Builder $parcel) => $parcel->where('status', 'awaiting_intake'))
            ->latest('updated_at')
            ->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn (MarketplaceOrder $order) => $this->present($order))->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function deliveries(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)])
            ->whereDoesntHave('logisticsParcel')
            ->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer'])
            ->latest('updated_at')
            ->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn (MarketplaceOrder $order) => $this->present($order))->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function show(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $order = $this->assigned($request, $order);
        $order->loadMissing(['seller', 'logisticsParcel', 'deliveryRouteStops']);

        return response()->json([
            'success' => true,
            'data' => $this->present($order, true),
        ])->header('Cache-Control', 'no-store');
    }

    public function proceedPickup(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $order = $this->assigned($request, $order);
        $updated = $this->workflow->transition(
            $order,
            'courier_accepted',
            'heading_pickup',
            'Rider Is Heading to Pickup',
            ($order->courier_name ?: 'Rider').' is now heading to the seller pickup location for order '.$order->order_number.'.',
            'both',
            ['pickup_started_at' => now()]
        );

        return $this->changed($updated, 'Pickup trip started.');
    }

    public function arrivedPickup(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $order = $this->assigned($request, $order);
        $updated = $this->workflow->transition(
            $order,
            'heading_pickup',
            'arrived_pickup',
            'Rider Arrived at Seller',
            ($order->courier_name ?: 'Rider').' arrived at the seller pickup point for order '.$order->order_number.'.',
            'both',
            ['arrived_pickup_at' => now()]
        );

        return $this->changed($updated, 'Arrival at seller confirmed.');
    }

    public function confirmPickup(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $order = $this->assigned($request, $order);
        $updated = $this->workflow->transition(
            $order,
            'arrived_pickup',
            'in_transit',
            'Order Picked Up',
            'Order '.$order->order_number.' was picked up and is now in transit.',
            'both',
            ['picked_up_at' => now()]
        );

        return $this->changed($updated, 'Pickup confirmed.');
    }

    public function arrivedBuyer(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $order = $this->assigned($request, $order);
        $this->assertNotLogisticsFinalMile($order);

        $updated = $this->workflow->transition(
            $order,
            'in_transit',
            'arrived_buyer',
            'Rider Reached Buyer',
            ($order->courier_name ?: 'Rider').' reached the buyer location for order '.$order->order_number.'.',
            'both',
            ['arrived_buyer_at' => now()]
        );

        return $this->changed($updated, 'Buyer arrival confirmed.');
    }

    public function complete(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $rider = $this->rider($request);
        $order = $this->assigned($request, $order);
        $this->assertNotLogisticsFinalMile($order);

        $updated = $this->workflow->transition(
            $order,
            'arrived_buyer',
            'delivered',
            'Order Delivered Successfully',
            'Order '.$order->order_number.' was successfully delivered to '.$order->buyer_name.'.',
            'both',
            [
                'delivered_at' => now(),
                'payment_status' => $order->payment_method === 'COD' ? 'paid' : $order->payment_status,
            ],
            $rider->id
        );

        return $this->changed($updated, 'Delivery completed successfully.');
    }

    private function assigned(Request $request, MarketplaceOrder $order): MarketplaceOrder
    {
        $rider = $this->rider($request);
        abort_unless(
            strtolower(trim((string) $order->courier_email)) === strtolower(trim((string) $rider->email)),
            404
        );
        return $order;
    }

    private function assertNotLogisticsFinalMile(MarketplaceOrder $order): void
    {
        if (LogisticsParcel::query()->where('marketplace_order_id', $order->id)->exists()) {
            throw ValidationException::withMessages([
                'order' => 'Final delivery for Logistics-managed orders must use the assigned last-mile workflow.',
            ]);
        }
    }

    private function changed(MarketplaceOrder $order, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->present($order->fresh(['seller', 'logisticsParcel']), true),
        ]);
    }

    private function present(MarketplaceOrder $order, bool $details = false): array
    {
        $actions = match ($order->status) {
            'courier_accepted' => ['proceed_pickup'],
            'heading_pickup' => ['arrived_pickup'],
            'arrived_pickup' => ['confirm_pickup'],
            'in_transit' => $order->logisticsParcel ? [] : ['arrived_buyer'],
            'arrived_buyer' => $order->logisticsParcel ? [] : ['complete'],
            default => [],
        };

        $data = [
            'id' => (int) $order->id,
            'order_number' => $order->order_number,
            'package_code' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
            'seller_name' => $order->seller?->store_name ?: $order->pickup_name,
            'pickup_name' => $order->pickup_name,
            'pickup_address' => $order->pickup_address,
            'buyer_name' => $order->buyer_name,
            'buyer_phone' => $order->buyer_phone,
            'buyer_address' => $order->buyer_address,
            'delivery_fee' => $order->courierDeliveryFee(),
            'parcel_status' => $order->logisticsParcel?->status,
            'is_logistics_pickup' => (bool) $order->logisticsParcel,
            'allowed_actions' => $actions,
            'updated_at' => $order->updated_at?->toIso8601String(),
        ];

        if ($details) {
            $data['items'] = $order->items ?? [];
            $data['route_stops'] = $order->relationLoaded('deliveryRouteStops')
                ? $order->deliveryRouteStops->map(fn ($stop) => [
                    'id' => (int) $stop->id,
                    'sequence' => (int) $stop->sequence,
                    'key' => $stop->stop_key,
                    'name' => $stop->name,
                    'address' => $stop->address_snapshot,
                    'lat' => $stop->latitude,
                    'lng' => $stop->longitude,
                ])->values()
                : [];
        }

        return $data;
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
