<?php

namespace App\Services;

use App\Models\BuyerCartItem;
use App\Models\SellerProduct;
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

    public function add(Request $request, int $productId, ?int $variantId, int $quantity): BuyerCartItem
    {
        $quantity = max(1, $quantity);

        $product = SellerProduct::query()
            ->with(['seller', 'activeVariants'])
            ->whereKey($productId)
            ->where('moderation_status', 'approved')
            ->whereNull('archived_at')
            ->firstOrFail();

        $this->inventory->assertAvailable($product, $variantId, $quantity);

        $identity = $this->identity->columns($request);

        $lookup = array_merge($identity, [
            'seller_product_id' => $product->id,
            'seller_product_variant_id' => $variantId,
        ]);

        $existing = BuyerCartItem::query()->where($lookup)->first();
        $newQuantity = $quantity + (int) ($existing?->quantity ?? 0);

        $this->inventory->assertAvailable($product, $variantId, $newQuantity);

        return BuyerCartItem::query()->updateOrCreate(
            $lookup,
            ['quantity' => $newQuantity]
        )->fresh(['product.seller', 'variant']);
    }

    public function update(Request $request, BuyerCartItem $item, int $quantity): BuyerCartItem
    {
        $this->guardOwnership($request, $item);

        $quantity = max(1, $quantity);
        $item->loadMissing(['product', 'variant']);

        if (!$item->product || $item->product->moderation_status !== 'approved' || $item->product->archived_at) {
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

    public function remove(Request $request, BuyerCartItem $item): void
    {
        $this->guardOwnership($request, $item);
        $item->delete();
    }

    public function clear(Request $request): void
    {
        $this->identity->apply(BuyerCartItem::query(), $request)->delete();
    }

    public function count(Request $request): int
    {
        return (int) $this->identity
            ->apply(BuyerCartItem::query(), $request)
            ->sum('quantity');
    }

    public function unitPrice(BuyerCartItem $item): float
    {
        $base = (float) ($item->variant?->price ?? $item->product?->price ?? 0);
        $discount = min(100, max(0, (float) ($item->product?->discount ?? 0)));

        return round($discount > 0 ? $base * (1 - ($discount / 100)) : $base, 2);
    }

    public function lineTotal(BuyerCartItem $item): float
    {
        return round($this->unitPrice($item) * max(1, (int) $item->quantity), 2);
    }

    public function subtotal(Collection $items): float
    {
        return round($items->sum(fn (BuyerCartItem $item) => $this->lineTotal($item)), 2);
    }

    public function groupedBySeller(Collection $items): Collection
    {
        return $items->groupBy(fn (BuyerCartItem $item) => (int) $item->product->seller_account_id);
    }

    public function deliveryFee(Collection $items): float
    {
        if ($items->isEmpty()) {
            return 0.0;
        }

        $feePerSeller = (float) config('sari_buyer.delivery_fee_per_seller', 80);

        return round(
            $this->groupedBySeller($items)->sum(function (Collection $group) use ($feePerSeller) {
                /*
                | A seller group gets free shipping only when every item in
                | that seller's checkout group is marked free_shipping.
                */
                $allItemsShipFree = $group->every(
                    fn (BuyerCartItem $item) => (bool) ($item->product?->free_shipping ?? false)
                );

                return $allItemsShipFree ? 0 : $feePerSeller;
            }),
            2
        );
    }

    public function variantLabel(BuyerCartItem $item): string
    {
        $options = $item->variant?->option_values ?? [];

        if (!is_array($options) || empty($options)) {
            return 'Standard';
        }

        return collect($options)
            ->map(fn ($value, $key) => ucfirst((string) $key) . ': ' . $value)
            ->implode(' • ');
    }

    private function guardOwnership(Request $request, BuyerCartItem $item): void
    {
        $this->identity->guard($request);

        $owned = $this->identity->accountId($request)
            ? (int) $item->buyer_account_id === (int) $this->identity->accountId($request)
            : (int) $item->buyer_social_account_id === (int) $this->identity->socialId($request);

        abort_unless($owned, 403);
    }
}
