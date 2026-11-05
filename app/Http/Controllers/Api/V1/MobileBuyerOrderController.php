<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MobileBuyerOrderController extends Controller
{
    /** @var array<int,SellerProduct> */
    private array $productsById = [];

    /** @var array<int,SellerProductVariant> */
    private array $variantsById = [];

    public function index(Request $request): JsonResponse
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $orders = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->with('seller:id,store_name,store_logo_path')
            ->latest('id')
            ->paginate((int) ($validated['per_page'] ?? 15));

        $this->primeItemMedia($orders->getCollection());

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()
                ->map(fn (MarketplaceOrder $order) => $this->present($order, false))
                ->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'links' => [
                'next' => $orders->nextPageUrl(),
                'prev' => $orders->previousPageUrl(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function show(Request $request, MarketplaceOrder $order): JsonResponse
    {
        abort_unless(
            (int) $order->buyer_account_id === (int) $request->user()->getKey(),
            404
        );

        $order->load('seller:id,store_name,store_logo_path');
        $this->primeItemMedia(collect([$order]));

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
            'store_logo_url' => $this->mediaUrl($order->seller?->store_logo_path),
            'status' => (string) $order->status,
            'status_label' => $order->statusLabel(),
            'payment_method' => (string) $order->payment_method,
            'payment_status' => (string) $order->payment_status,
            'subtotal' => (string) $order->subtotal,
            'delivery_fee' => (string) $order->delivery_fee,
            'total' => (string) $order->total,
            'created_at' => $order->created_at?->toIso8601String(),
            'delivered_at' => $order->delivered_at?->toIso8601String(),

            // Always include lightweight order items so mobile order cards can
            // render actual product images without making one detail request per card.
            'items' => $this->presentItems((array) ($order->items ?? [])),
        ];

        if ($includeDetails) {
            $data['buyer_name'] = $order->buyer_name;
            $data['buyer_phone'] = $order->buyer_phone;
            $data['buyer_address'] = $order->buyer_address;
        }

        return $data;
    }

    private function presentItems(array $items): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item): array {
                $productId = (int) ($item['product_id'] ?? 0);
                $variantId = (int) ($item['variant_id'] ?? 0);

                $product = $this->productsById[$productId] ?? null;
                $variant = $this->variantsById[$variantId] ?? null;

                $imagePath = $variant?->image_path ?: $product?->image_path;

                $item['product_id'] = $productId ?: null;
                $item['variant_id'] = $variantId ?: null;
                $item['image_url'] = $this->mediaUrl($imagePath);

                return $item;
            })
            ->values()
            ->all();
    }

    private function primeItemMedia(Collection $orders): void
    {
        $productIds = collect();
        $variantIds = collect();

        foreach ($orders as $order) {
            foreach ((array) ($order->items ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $productId = (int) ($item['product_id'] ?? 0);
                $variantId = (int) ($item['variant_id'] ?? 0);

                if ($productId > 0) {
                    $productIds->push($productId);
                }

                if ($variantId > 0) {
                    $variantIds->push($variantId);
                }
            }
        }

        $this->productsById = SellerProduct::query()
            ->whereIn('id', $productIds->unique()->values()->all())
            ->get(['id', 'image_path'])
            ->keyBy('id')
            ->all();

        $this->variantsById = SellerProductVariant::query()
            ->whereIn('id', $variantIds->unique()->values()->all())
            ->get(['id', 'image_path'])
            ->keyBy('id')
            ->all();
    }

    private function mediaUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        return $path !== ''
            ? Storage::disk('public')->url($path)
            : null;
    }
}


