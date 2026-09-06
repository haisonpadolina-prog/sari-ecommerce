<?php

namespace App\Services;

use App\Models\SellerProduct;
use App\Models\SellerProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductVariantInventoryService
{
    public function payload(SellerProduct $product): array
    {
        if (!(bool) $product->has_variants) {
            return [];
        }

        return SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (SellerProductVariant $variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'options' => is_array($variant->option_values) ? $variant->option_values : [],
                'price' => (float) $variant->price,
                'stock' => (int) $variant->stock,
                'available' => (int) $variant->stock > 0,
            ])
            ->values()
            ->all();
    }

    public function resolve(SellerProduct $product, int $variantId): SellerProductVariant
    {
        return SellerProductVariant::query()
            ->whereKey($variantId)
            ->where('seller_product_id', $product->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function assertAvailable(SellerProduct $product, ?int $variantId, int $quantity): ?SellerProductVariant
    {
        $quantity = max(1, $quantity);

        if (!(bool) $product->has_variants) {
            if ((int) $product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity is no longer available.',
                ]);
            }

            return null;
        }

        if (!$variantId) {
            throw ValidationException::withMessages([
                'variant_id' => 'Please select a product variation before adding this item.',
            ]);
        }

        $variant = $this->resolve($product, $variantId);

        if ((int) $variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'The selected variation does not have enough stock.',
            ]);
        }

        return $variant;
    }

    public function deductForOrder(SellerProduct $product, ?int $variantId, int $quantity): void
    {
        $quantity = max(1, $quantity);

        DB::transaction(function () use ($product, $variantId, $quantity) {
            $lockedProduct = SellerProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!(bool) $lockedProduct->has_variants) {
                if ((int) $lockedProduct->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'This product no longer has enough stock.',
                    ]);
                }

                $lockedProduct->decrement('stock', $quantity);
                return;
            }

            if (!$variantId) {
                throw ValidationException::withMessages([
                    'variant_id' => 'Please select a product variation.',
                ]);
            }

            $variant = SellerProductVariant::query()
                ->whereKey($variantId)
                ->where('seller_product_id', $lockedProduct->id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $variant->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'The selected variation sold out or no longer has enough stock.',
                ]);
            }

            $variant->decrement('stock', $quantity);
            $this->syncProductStock($lockedProduct);
        }, 3);
    }

    public function restoreForCancelledOrder(SellerProduct $product, ?int $variantId, int $quantity): void
    {
        $quantity = max(1, $quantity);

        DB::transaction(function () use ($product, $variantId, $quantity) {
            $lockedProduct = SellerProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProduct) {
                return;
            }

            if (!(bool) $lockedProduct->has_variants || !$variantId) {
                $lockedProduct->increment('stock', $quantity);
                return;
            }

            $variant = SellerProductVariant::query()
                ->whereKey($variantId)
                ->where('seller_product_id', $lockedProduct->id)
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                return;
            }

            $variant->increment('stock', $quantity);
            $this->syncProductStock($lockedProduct);
        }, 3);
    }

    private function syncProductStock(SellerProduct $product): void
    {
        $remaining = SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->where('is_active', true)
            ->sum('stock');

        $product->forceFill(['stock' => (int) $remaining])->save();
    }
}
