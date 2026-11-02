<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\BuyerCartItem;
use App\Services\Buyer\BuyerCartService;
use App\Services\Products\ProductVariantInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileBuyerCartController extends Controller
{
    public function __construct(
        private readonly BuyerCartService $cart,
        private readonly ProductVariantInventoryService $inventory,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $items = $this->items($buyer);
        $shipping = $this->cart->shippingSummary($items);
        $subtotal = $this->cart->subtotal($items);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items->map(fn (BuyerCartItem $item) => $this->present($item))->values(),
                'count' => $items->count(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => round($subtotal + (float) $shipping['delivery_fee'], 2),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function store(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:seller_products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:seller_product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $product = SellerProduct::query()
            ->with(['seller', 'activeVariants'])
            ->whereKey((int) $validated['product_id'])
            ->buyerVisible()
            ->firstOrFail();

        $variantId = isset($validated['variant_id']) ? (int) $validated['variant_id'] : null;
        $quantity = (int) $validated['quantity'];
        $existing = BuyerCartItem::query()
            ->where('buyer_account_id', $buyer->id)
            ->whereNull('buyer_social_account_id')
            ->where('seller_product_id', $product->id)
            ->where('seller_product_variant_id', $variantId)
            ->first();

        $newQuantity = $quantity + (int) ($existing?->quantity ?? 0);
        $this->inventory->assertAvailable($product, $variantId, $newQuantity);

        $item = BuyerCartItem::query()->updateOrCreate([
            'buyer_account_id' => $buyer->id,
            'buyer_social_account_id' => null,
            'seller_product_id' => $product->id,
            'seller_product_variant_id' => $variantId,
        ], ['quantity' => $newQuantity])->fresh(['product.seller', 'product.activeVariants', 'variant']);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'data' => [
                'item' => $this->present($item),
                'cart_count' => BuyerCartItem::query()->where('buyer_account_id', $buyer->id)->count(),
            ],
        ], 201);
    }

    public function update(Request $request, BuyerCartItem $item): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardItem($buyer, $item);
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:999']]);
        $item->loadMissing(['product', 'variant']);
        abort_unless($item->product && $item->product->isBuyerVisible(), 422);

        $this->inventory->assertAvailable($item->product, $item->seller_product_variant_id, (int) $validated['quantity']);
        $item->update(['quantity' => (int) $validated['quantity']]);
        $item = $item->fresh(['product.seller', 'product.activeVariants', 'variant']);

        return response()->json(['success' => true, 'message' => 'Cart updated.', 'data' => $this->present($item)]);
    }

    public function destroy(Request $request, BuyerCartItem $item): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardItem($buyer, $item);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item removed from cart.']);
    }

    public function summary(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        return response()->json([
            'success' => true,
            'data' => ['count' => BuyerCartItem::query()->where('buyer_account_id', $buyer->id)->count()],
        ])->header('Cache-Control', 'no-store');
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }

    private function items(BuyerAccount $buyer)
    {
        return BuyerCartItem::query()
            ->where('buyer_account_id', $buyer->id)
            ->whereNull('buyer_social_account_id')
            ->with(['product.seller', 'product.activeVariants', 'variant'])
            ->latest('updated_at')
            ->get()
            ->filter(fn (BuyerCartItem $item) => $item->product)
            ->values();
    }

    private function guardItem(BuyerAccount $buyer, BuyerCartItem $item): void
    {
        abort_unless((int) $item->buyer_account_id === (int) $buyer->id, 404);
    }

    private function present(BuyerCartItem $item): array
    {
        $availability = $this->cart->availability($item);
        $imagePath = $item->variant?->image_path ?: $item->product?->image_path;
        return [
            'id' => (int) $item->id,
            'product_id' => (int) $item->seller_product_id,
            'variant_id' => $item->seller_product_variant_id ? (int) $item->seller_product_variant_id : null,
            'name' => (string) ($item->product?->name ?? ''),
            'store_name' => (string) ($item->product?->seller?->store_name ?? ''),
            'variant_label' => $this->cart->variantLabel($item),
            'quantity' => (int) $item->quantity,
            'unit_price' => $this->cart->unitPrice($item),
            'line_total' => $this->cart->lineTotal($item),
            'max_stock' => (int) $availability['max_stock'],
            'selectable' => (bool) $availability['selectable'],
            'availability_reason' => $availability['reason'],
            'image_url' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
        ];
    }
}
