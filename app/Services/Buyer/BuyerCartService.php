<?php

namespace App\Services\Buyer;

use App\Services\Products\ProductVariantInventoryService;

use App\Models\Orders\BuyerCartItem;
use App\Models\Platform\PlatformSetting;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BuyerCartService
{
    public function __construct(
        private readonly BuyerIdentityService $identity,
        private readonly ProductVariantInventoryService $inventory,
    ) {
    }

    public function items(Request $request): Collection
    {
        return $this->identity
            ->apply(BuyerCartItem::query(), $request)
            ->with([
                'product.seller',
                'product.activeVariants',
                'variant',
            ])
            ->latest('updated_at')
            ->get()
            ->filter(fn (BuyerCartItem $item) => $item->product)
            ->values();
    }

    public function add(
        Request $request,
        int $productId,
        ?int $variantId,
        int $quantity
    ): BuyerCartItem {
        $quantity = max(1, $quantity);

        $product = SellerProduct::query()
            ->with(['seller', 'activeVariants'])
            ->whereKey($productId)
            ->buyerVisible()
            ->first();

        if (!$product) {
            throw ValidationException::withMessages([
                'cart' => 'This product is no longer available in the marketplace.',
            ]);
        }

        $this->inventory->assertAvailable(
            $product,
            $variantId,
            $quantity
        );

        $identity = $this->identity->columns($request);

        $lookup = array_merge($identity, [
            'seller_product_id' => $product->id,
            'seller_product_variant_id' => $variantId,
        ]);

        $existing = BuyerCartItem::query()
            ->where($lookup)
            ->first();

        $newQuantity = $quantity
            + (int) ($existing?->quantity ?? 0);

        $this->inventory->assertAvailable(
            $product,
            $variantId,
            $newQuantity
        );

        return BuyerCartItem::query()->updateOrCreate(
            $lookup,
            ['quantity' => $newQuantity]
        )->fresh(['product.seller', 'variant']);
    }

    public function update(
        Request $request,
        BuyerCartItem $item,
        int $quantity
    ): BuyerCartItem {
        $this->guardOwnership($request, $item);

        $quantity = max(1, $quantity);
        $item->loadMissing(['product', 'variant']);

        if (!$item->product || !$item->product->isBuyerVisible()) {
            throw ValidationException::withMessages([
                'cart' => 'This product is no longer available in the marketplace.',
            ]);
        }

        $this->inventory->assertAvailable(
            $item->product,
            $item->seller_product_variant_id,
            $quantity
        );

        $item->update(['quantity' => $quantity]);

        return $item->fresh(['product.seller', 'variant']);
    }

    public function remove(
        Request $request,
        BuyerCartItem $item
    ): void {
        $this->guardOwnership($request, $item);
        $item->delete();
    }

    public function clear(Request $request): void
    {
        $this->identity
            ->apply(BuyerCartItem::query(), $request)
            ->delete();
    }

    public function count(Request $request): int
    {
        return (int) $this->identity
            ->apply(BuyerCartItem::query(), $request)
            ->sum('quantity');
    }

    public function unitPrice(BuyerCartItem $item): float
    {
        if (!$item->product) {
            return 0.0;
        }

        return $this->unitPriceFor($item->product, $item->variant);
    }

    public function unitPriceFor(
        SellerProduct $product,
        ?SellerProductVariant $variant = null
    ): float {
        $base = (float) ($variant?->price ?? $product->price ?? 0);

        $discount = min(
            100,
            max(
                0,
                (float) ($product->discount ?? 0)
            )
        );

        if (
            $product->flash_sale_ends_at !== null
            && now()->gte($product->flash_sale_ends_at)
        ) {
            $discount = 0.0;
        }

        return round(
            $discount > 0
                ? $base * (1 - ($discount / 100))
                : $base,
            2
        );
    }

    public function lineTotal(BuyerCartItem $item): float
    {
        return round(
            $this->unitPrice($item)
                * max(1, (int) $item->quantity),
            2
        );
    }

    /**
     * Describe whether a cart row can be selected for checkout without
     * removing rows that became unavailable after the buyer added them.
     *
     * @return array{selectable: bool, quantity_editable: bool, product_visible: bool, reason: ?string, max_stock: int}
     */
    public function availability(BuyerCartItem $item): array
    {
        $item->loadMissing(['product.seller', 'variant']);

        $product = $item->product;

        if (!$product || !$product->isBuyerVisible()) {
            return [
                'selectable' => false,
                'quantity_editable' => false,
                'product_visible' => false,
                'reason' => 'This product is no longer available in the marketplace.',
                'max_stock' => 0,
            ];
        }

        if ((bool) $product->has_variants) {
            $variant = $item->variant;

            if (
                !$variant
                || (int) $variant->seller_product_id !== (int) $product->id
                || !(bool) $variant->is_active
            ) {
                return [
                    'selectable' => false,
                    'quantity_editable' => false,
                    'product_visible' => true,
                    'reason' => 'The selected product variation is no longer available.',
                    'max_stock' => 0,
                ];
            }

            $maxStock = max(0, (int) $variant->stock);
        } else {
            $maxStock = max(0, (int) $product->stock);
        }

        if ($maxStock < 1) {
            return [
                'selectable' => false,
                'quantity_editable' => false,
                'product_visible' => true,
                'reason' => 'This item is currently out of stock.',
                'max_stock' => 0,
            ];
        }

        if ((int) $item->quantity > $maxStock) {
            return [
                'selectable' => false,
                'quantity_editable' => true,
                'product_visible' => true,
                'reason' => "Only {$maxStock} item" . ($maxStock === 1 ? '' : 's') . ' remain. Update the quantity to continue.',
                'max_stock' => $maxStock,
            ];
        }

        return [
            'selectable' => true,
            'quantity_editable' => true,
            'product_visible' => true,
            'reason' => null,
            'max_stock' => $maxStock,
        ];
    }

    public function subtotal(Collection $items): float
    {
        return round(
            $items->sum(
                fn (BuyerCartItem $item) => $this->lineTotal($item)
            ),
            2
        );
    }

    public function groupedBySeller(
        Collection $items
    ): Collection {
        return $items->groupBy(
            fn (BuyerCartItem $item) =>
                (int) $item->product->seller_account_id
        );
    }

    public function deliveryFeePerSeller(): float
    {
        return (float) PlatformSetting::valueOf(
            'delivery_fee_per_seller',
            config('sari.buyer.delivery_fee_per_seller', 80)
        );
    }

    public function deliveryFee(Collection $items): float
    {
        if ($items->isEmpty()) {
            return 0.0;
        }

        $feePerSeller = $this->deliveryFeePerSeller();

        return round(
            $this->groupedBySeller($items)
                ->sum(
                    function (
                        Collection $group
                    ) use ($feePerSeller) {
                        $allItemsShipFree = $group->every(
                            fn (BuyerCartItem $item) =>
                                (bool) (
                                    $item->product?->free_shipping
                                    ?? false
                                )
                        );

                        return $allItemsShipFree
                            ? 0
                            : $feePerSeller;
                    }
                ),
            2
        );
    }

    public function variantLabel(
        BuyerCartItem $item
    ): string {
        return $this->variantLabelFor($item->variant);
    }

    public function variantLabelFor(?SellerProductVariant $variant): string
    {
        $options = $variant?->option_values ?? [];

        if (!is_array($options) || empty($options)) {
            return 'Standard';
        }

        return collect($options)
            ->map(
                fn ($value, $key) =>
                    ucfirst((string) $key) . ': ' . $value
            )
            ->implode(' • ');
    }

    private function guardOwnership(
        Request $request,
        BuyerCartItem $item
    ): void {
        $this->identity->guard($request);

        $owned = $this->identity->accountId($request)
            ? (int) $item->buyer_account_id
                === (int) $this->identity->accountId($request)
            : (int) $item->buyer_social_account_id
                === (int) $this->identity->socialId($request);

        abort_unless($owned, 403);
    }
}
