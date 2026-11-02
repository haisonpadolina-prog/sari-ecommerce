<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Catalog\ProductReview;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Services\Products\ProductVariantInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class MobileBuyerOrderActionController extends Controller
{
    public function __construct(
        private readonly ProductVariantInventoryService $inventory,
    ) {}

    public function cancel(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardOrder($buyer, $order);
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $locked = MarketplaceOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'new') {
                throw ValidationException::withMessages([
                    'order' => 'This order can no longer be cancelled because fulfillment already started.',
                ]);
            }

            foreach ((array) $locked->items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                if ($productId <= 0) {
                    continue;
                }

                $product = SellerProduct::query()->find($productId);
                if (!$product) {
                    continue;
                }

                $this->inventory->restoreForCancelledOrder(
                    $product,
                    !empty($item['variant_id']) ? (int) $item['variant_id'] : null,
                    max(1, (int) ($item['qty'] ?? 1))
                );
            }

            $locked->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => trim((string) (
                    $validated['reason'] ?? 'Buyer cancelled before preparation.'
                )),
            ])->save();

            MarketplaceOrderEvent::create([
                'marketplace_order_id' => $locked->id,
                'seller_account_id' => $locked->seller_account_id,
                'audience' => 'both',
                'type' => 'cancelled',
                'title' => 'Order Cancelled',
                'message' => 'The buyer cancelled order '.$locked->order_number.' before preparation started.',
                'status' => 'cancelled',
            ]);
        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled and reserved stock was restored.',
        ]);
    }

    public function review(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardOrder($buyer, $order);

        if ($order->status !== 'delivered') {
            throw ValidationException::withMessages([
                'review' => 'Reviews can only be submitted after delivery.',
            ]);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:seller_products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1500'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $productId = (int) $validated['product_id'];
        $belongs = collect((array) $order->items)
            ->contains(fn ($item) => (int) ($item['product_id'] ?? 0) === $productId);

        if (!$belongs) {
            throw ValidationException::withMessages([
                'product_id' => 'That product does not belong to this order.',
            ]);
        }

        $paths = collect();
        try {
            foreach (collect($request->file('images', []))->filter(fn ($file) => $file instanceof UploadedFile) as $file) {
                $paths->push($file->store('product-reviews/'.$order->id.'/'.$productId, 'public'));
            }

            $review = ProductReview::query()->updateOrCreate(
                [
                    'marketplace_order_id' => $order->id,
                    'seller_product_id' => $productId,
                ],
                [
                    'buyer_account_id' => $buyer->id,
                    'buyer_social_account_id' => null,
                    'seller_account_id' => $order->seller_account_id,
                    'rating' => (int) $validated['rating'],
                    'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
                    'image_paths' => $paths->isNotEmpty() ? $paths->values()->all() : null,
                ]
            );
        } catch (Throwable $exception) {
            foreach ($paths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => 'Product review saved.',
            'data' => [
                'id' => (int) $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'image_urls' => collect($review->image_paths ?? [])
                    ->map(fn ($path) => Storage::disk('public')->url($path))
                    ->values(),
            ],
        ], $review->wasRecentlyCreated ? 201 : 200);
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }

    private function guardOrder(BuyerAccount $buyer, MarketplaceOrder $order): void
    {
        abort_unless((int) $order->buyer_account_id === (int) $buyer->id, 404);
    }
}
