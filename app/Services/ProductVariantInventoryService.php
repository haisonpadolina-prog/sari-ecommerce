<?php

namespace App\Services;

use App\Models\SellerProduct;
use App\Models\SellerProductVariant;
use App\Models\SellerInventoryMovement;
use Illuminate\Support\Facades\Schema;
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

                $before = (int) $lockedProduct->stock;
                $lockedProduct->decrement('stock', $quantity);
                if (Schema::hasTable('seller_inventory_movements')) {
                    SellerInventoryMovement::create([
                        'seller_account_id' => $lockedProduct->seller_account_id,
                        'seller_product_id' => $lockedProduct->id,
                        'seller_product_variant_id' => null,
                        'quantity_before' => $before,
                        'quantity_after' => $before - $quantity,
                        'quantity_delta' => -$quantity,
                        'reason' => 'order_deduction',
                        'note' => 'Stock reserved/deducted by Buyer checkout.',
                    ]);
                }
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

            $before = (int) $variant->stock;
            $variant->decrement('stock', $quantity);
            if (Schema::hasTable('seller_inventory_movements')) {
                SellerInventoryMovement::create([
                    'seller_account_id' => $lockedProduct->seller_account_id,
                    'seller_product_id' => $lockedProduct->id,
                    'seller_product_variant_id' => $variant->id,
                    'quantity_before' => $before,
                    'quantity_after' => $before - $quantity,
                    'quantity_delta' => -$quantity,
                    'reason' => 'order_deduction',
                    'note' => 'Variant stock reserved/deducted by Buyer checkout.',
                ]);
            }
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
                $before = (int) $lockedProduct->stock;
                $lockedProduct->increment('stock', $quantity);
                if (Schema::hasTable('seller_inventory_movements')) {
                    SellerInventoryMovement::create([
                        'seller_account_id' => $lockedProduct->seller_account_id,
                        'seller_product_id' => $lockedProduct->id,
                        'seller_product_variant_id' => null,
                        'quantity_before' => $before,
                        'quantity_after' => $before + $quantity,
                        'quantity_delta' => $quantity,
                        'reason' => 'order_cancel_restore',
                        'note' => 'Stock restored after Buyer cancellation.',
                    ]);
                }
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

            $before = (int) $variant->stock;
            $variant->increment('stock', $quantity);
            if (Schema::hasTable('seller_inventory_movements')) {
                SellerInventoryMovement::create([
                    'seller_account_id' => $lockedProduct->seller_account_id,
                    'seller_product_id' => $lockedProduct->id,
                    'seller_product_variant_id' => $variant->id,
                    'quantity_before' => $before,
                    'quantity_after' => $before + $quantity,
                    'quantity_delta' => $quantity,
                    'reason' => 'order_cancel_restore',
                    'note' => 'Variant stock restored after Buyer cancellation.',
                ]);
            }
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
