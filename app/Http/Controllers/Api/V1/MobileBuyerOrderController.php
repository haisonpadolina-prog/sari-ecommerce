<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileBuyerOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $orders = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->with('seller:id,store_name')
            ->latest('id')->paginate((int) ($validated['per_page'] ?? 15));

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn (MarketplaceOrder $order) =>
                $this->present($order, false))->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'links' => ['next' => $orders->nextPageUrl(), 'prev' => $orders->previousPageUrl()],
        ])->header('Cache-Control', 'no-store');
    }

    public function show(Request $request, MarketplaceOrder $order): JsonResponse
    {
        abort_unless((int) $order->buyer_account_id === (int) $request->user()->getKey(), 404);
        $order->load('seller:id,store_name');

        return response()->json([
            'success' => true,
            'data' => $this->present($order, true),
        ])->header('Cache-Control', 'no-store');
    }

    private function present(MarketplaceOrder $order, bool $includeDetails): array
    {
        $data = [
            'id' => (int) $order->id,
            'order_number' => (string) $order->order_number,
            'checkout_reference' => $order->checkout_reference,
            'seller_id' => (int) $order->seller_account_id,
            'store_name' => $order->seller?->store_name,
            'status' => (string) $order->status,
            'status_label' => $order->statusLabel(),
            'payment_method' => (string) $order->payment_method,
            'payment_status' => (string) $order->payment_status,
            'subtotal' => (string) $order->subtotal,
            'delivery_fee' => (string) $order->delivery_fee,
            'total' => (string) $order->total,
            'created_at' => $order->created_at?->toIso8601String(),
            'delivered_at' => $order->delivered_at?->toIso8601String(),
        ];

        if ($includeDetails) {
            $data['items'] = $order->items ?? [];
            $data['buyer_name'] = $order->buyer_name;
            $data['buyer_phone'] = $order->buyer_phone;
            $data['buyer_address'] = $order->buyer_address;
        }

        return $data;
    }
}
