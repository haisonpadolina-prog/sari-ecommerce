<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\DeliveryRouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileBuyerShippingController extends Controller
{
    private const SHIPPING_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
        'delivered',
    ];

    public function index(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $orders = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->with([
                'seller:id,store_name,store_logo_path',
                'logisticsParcel:id,marketplace_order_id,status,received_at,sorted_at',
            ])
            ->whereIn('status', self::SHIPPING_STATUSES)
            ->latest('updated_at')
            ->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn (MarketplaceOrder $order) => [
                'id' => (int) $order->id,
                'order_number' => $order->order_number,
                'store_name' => $order->seller?->store_name,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'parcel_status' => $order->logisticsParcel?->status,
                'courier_name' => $order->courier_name,
                'updated_at' => $order->updated_at?->toIso8601String(),
                'delivered_at' => $order->delivered_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function tracking(Request $request, MarketplaceOrder $order, DeliveryRouteService $routes): JsonResponse
    {
        $buyer = $this->buyer($request);
        abort_unless((int) $order->buyer_account_id === (int) $buyer->id, 404);
        $order->loadMissing(['seller', 'buyer', 'logisticsParcel']);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'courier_name' => $order->courier_name ?: null,
                'updated_at' => $order->updated_at?->toIso8601String(),
                'route' => $routes->buyerPayload($order),
                'events' => $order->events()
                    ->whereIn('audience', ['buyer', 'both'])
                    ->oldest('id')
                    ->get()
                    ->map(fn ($event) => [
                        'id' => (int) $event->id,
                        'type' => $event->type,
                        'title' => $event->title,
                        'message' => $event->message,
                        'status' => $event->status,
                        'created_at' => $event->created_at?->toIso8601String(),
                    ])->values(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }
}
