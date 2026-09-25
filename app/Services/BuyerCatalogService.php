<?php

namespace App\Services;

use App\Models\SellerAccount;
use App\Models\SellerProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BuyerCatalogService
{
    private const MARKETPLACE_CATEGORIES = [
        'fashion' => [
            'name' => 'Fashion',
            'subtitle' => 'Everyday style',
            'image' => 'images/cat-fashion.jpg',
            'keywords' => ['fashion', 'apparel', 'shoe'],
        ],
        'electronics' => [
            'name' => 'Electronics',
            'subtitle' => 'Smart essentials',
            'image' => 'images/cat-electronics.jpg',
            'keywords' => ['electronic', 'appliance'],
        ],
        'home-living' => [
            'name' => 'Home & Living',
            'subtitle' => 'Make it yours',
            'image' => 'images/cat-home.jpg',
            'keywords' => ['home', 'living', 'furniture'],
        ],
        'beauty' => [
            'name' => 'Beauty',
            'subtitle' => 'Care & confidence',
            'image' => 'images/cat-beauty.jpg',
            'keywords' => ['beauty', 'personal care'],
        ],
        'accessories' => [
            'name' => 'Accessories',
            'subtitle' => 'The finishing touch',
            'image' => 'images/cat-accessories.jpg',
            'keywords' => ['accessor', 'jewel', 'watch', 'fashion', 'apparel'],
        ],
        'food-essentials' => [
            'name' => 'Food & Essentials',
            'subtitle' => 'Everyday needs',
            'image' => 'images/cat-food.jpg',
            'keywords' => ['food', 'beverage', 'grocery', 'essential'],
        ],
        'sports' => [
            'name' => 'Sports',
            'subtitle' => 'Move your way',
            'image' => 'images/cat-sports.jpg',
            'keywords' => ['sport', 'outdoor'],
        ],
        'lifestyle' => [
            'name' => 'Lifestyle',
            'subtitle' => 'Live it your way',
            'image' => 'images/cat-lifestyle.jpg',
            'keywords' => [
                'lifestyle', 'book', 'stationery', 'automotive', 'baby', 'kids',
                'pet', 'health', 'wellness', 'toy', 'collectible', 'other',
            ],
        ],
    ];

    public function approvedProducts(): Collection
    {
        return $this->approvedProductsQuery()
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();
    }

    public function catalog(?int $limit = null): array
    {
        $products = $this->approvedProducts();

        if ($limit !== null && $limit > 0) {
            $products = $products->take($limit);
        }

        return $products
            ->map(fn (SellerProduct $product) => $this->toBuyerProduct($product))
            ->values()
            ->all();
    }

    public function find(int $productId): ?array
    {
        $product = SellerProduct::query()
            ->with([
                'seller:id,store_name,email,store_status,created_at',
                'activeVariants',
                'galleryImages',
                'reviews:id,seller_product_id,rating,comment,created_at',
            ])
            ->whereKey($productId)
            ->where('moderation_status', 'approved')
            ->whereNull('archived_at')
            ->whereHas('seller', fn ($query) => $query->where(function ($q) {
                $q->whereNull('store_status')->orWhere('store_status', 'open');
            }))
            ->first();

        return $product ? $this->toBuyerProduct($product) : null;
    }

    public function productsForShop(string $shopSlug): array
    {
        $sellerId = $this->sellerIdFromSlug($shopSlug);

        if (!$sellerId) {
            return [];
        }

        return $this->approvedProducts()
            ->where('seller_account_id', $sellerId)
            ->map(fn (SellerProduct $product) => $this->toBuyerProduct($product))
            ->values()
            ->all();
    }

    public function shop(string $shopSlug): ?array
    {
        $sellerId = $this->sellerIdFromSlug($shopSlug);

        if (!$sellerId) {
            return null;
        }

        $seller = SellerAccount::query()->find($sellerId);

        if (!$seller) {
            return null;
        }

        $products = $this->approvedProducts()
            ->where('seller_account_id', $sellerId)
            ->values();

        return $this->toBuyerShop($seller, $products);
    }

    public function shopResults(string $query): array
    {
        $query = strtolower(trim($query));

        if ($query === '') {
            return [];
        }

        return $this->approvedProducts()
            ->groupBy('seller_account_id')
            ->map(function (Collection $products) {
                $seller = $products->first()?->seller;

                return $seller ? $this->toBuyerShop($seller, $products) : null;
            })
            ->filter()
            ->filter(function (array $shop) use ($query) {
                return str_contains(strtolower($shop['name'] ?? ''), $query)
                    || str_contains(strtolower($shop['store_name'] ?? ''), $query)
                    || str_contains(strtolower($shop['username'] ?? ''), $query);
            })
            ->take(5)
            ->values()
            ->all();
    }

    public function categories(): array
    {
        $discovered = $this->approvedProducts()
            ->pluck('category')
            ->filter()
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique(fn ($category) => strtolower($category))
            ->values();

        if ($discovered->isEmpty()) {
            $discovered = collect([
                'Food', 'Fashion', 'Beauty', 'Electronics',
                'Home', 'Sports', 'Lifestyle', 'Accessories',
            ]);
        }

        return $discovered
            ->map(fn (string $name) => [
                'name' => $name,
                'image' => $this->categoryImage($name),
            ])
            ->values()
            ->all();
    }

    public function homeCategories(): array
    {
        return array_slice($this->categories(), 0, 8);
    }

    public function marketplaceCategories(): array
    {
        return collect(self::MARKETPLACE_CATEGORIES)
            ->map(function (array $definition, string $slug) {
                return [
                    'slug' => $slug,
                    'name' => $definition['name'],
                    'subtitle' => $definition['subtitle'],
                    'image' => $definition['image'],
                ];
            })
            ->values()
            ->all();
    }

    public function categoryDefinition(string $slug): ?array
    {
        $slug = strtolower(trim($slug));
        $definition = self::MARKETPLACE_CATEGORIES[$slug] ?? null;

        if (!$definition) {
            return null;
        }

        return [
            'slug' => $slug,
            'name' => $definition['name'],
            'subtitle' => $definition['subtitle'],
            'image' => $definition['image'],
        ];
    }

    public function categoryProducts(
        string $slug,
        string $search = '',
        string $sort = 'featured',
        int $perPage = 20,
    ): LengthAwarePaginator {
        $slug = strtolower(trim($slug));
        $definition = self::MARKETPLACE_CATEGORIES[$slug] ?? null;

        abort_unless($definition, 404);

        $query = $this->approvedProductsQuery();
        $keywords = $definition['keywords'];

        $query->where(function (Builder $categoryQuery) use ($keywords) {
            foreach ($keywords as $keyword) {
                $categoryQuery->orWhere('category', 'like', '%' . $keyword . '%');
            }
        });

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $searchQuery) use ($search) {
                $like = '%' . $search . '%';

                $searchQuery
                    ->where('name', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhereHas('seller', fn (Builder $sellerQuery) =>
                        $sellerQuery->where('store_name', 'like', $like)
                    );
            });
        }

        match ($sort) {
            'newest' => $query->orderByDesc('id'),
            'price_low' => $query->orderBy('price')->orderByDesc('id'),
            'price_high' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->orderByDesc('reviewed_at')->orderByDesc('id'),
        };

        $paginator = $query
            ->paginate(max(8, min(48, $perPage)))
            ->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()
                ->map(fn (SellerProduct $product) => $this->toBuyerProduct($product))
        );

        return $paginator;
    }

    private function toBuyerProduct(SellerProduct $product): array
    {
        $variants = $product->activeVariants ?? collect();
        $optionGroups = [];

        foreach ($variants as $variant) {
            $options = is_array($variant->option_values)
                ? $variant->option_values
                : (json_decode((string) $variant->option_values, true) ?: []);

            foreach ($options as $name => $value) {
                $key = trim((string) $name);
                $value = trim((string) $value);

                if ($key === '' || $value === '') {
                    continue;
                }

                $optionGroups[$key] ??= [];

                if (!in_array($value, $optionGroups[$key], true)) {
                    $optionGroups[$key][] = $value;
                }
            }
        }

        $basePrice = max(0, (float) ($product->price ?? 0));
        $discountPercent = $this->effectiveDiscountPercent($product);
        $sellingPrice = $this->discounted($basePrice, $discountPercent);
        $stock = $variants->isNotEmpty()
            ? (int) $variants->sum('stock')
            : (int) ($product->stock ?? 0);

        $seller = $product->seller;
        $storeName = trim((string) ($seller?->store_name ?: 'SARI Seller Store'));
        $rating = $product->reviews?->count()
            ? round((float) $product->reviews->avg('rating'), 1)
            : 0.0;

        $variantPayload = $variants->map(function ($variant) use ($discountPercent) {
            $options = is_array($variant->option_values)
                ? $variant->option_values
                : (json_decode((string) $variant->option_values, true) ?: []);

            /*
             * Buyer-facing labels should be clean and easy to scan.
             * Example:
             *   Variant: Green + Size: S  ->  Green • S
             *
             * Put the main visual/style option first, then size.
             */
            $preferredOrder = [
                'variant' => 10,
                'color' => 20,
                'colour' => 20,
                'design' => 30,
                'scent' => 40,
                'storage' => 50,
                'material' => 60,
                'style' => 70,
                'capacity' => 80,
                'flavor' => 90,
                'size' => 100,
                'shoe size' => 110,
            ];

            $cleanLabel = collect($options)
                ->filter(fn ($value) => trim((string) $value) !== '')
                ->sortBy(function ($value, $key) use ($preferredOrder) {
                    return $preferredOrder[strtolower(trim((string) $key))] ?? 95;
                })
                ->map(fn ($value) => trim((string) $value))
                ->implode(' • ');

            return [
                'id' => (int) $variant->id,
                'sku' => (string) ($variant->sku ?? ''),
                'options' => $options,
                'label' => $cleanLabel !== '' ? $cleanLabel : 'Variant #' . $variant->id,
                'price' => $this->discounted((float) $variant->price, $discountPercent),
                'old_price' => $discountPercent > 0 ? (float) $variant->price : null,
                'stock' => (int) $variant->stock,
                'available' => (int) $variant->stock > 0,
                'image' => $variant->image_path
                    ? 'buyer/product-variants/' . $variant->id . '/image'
                    : null,
            ];
        })->values()->all();

        $colorValues = $this->optionValuesMatching($optionGroups, ['color', 'colour']);
        $sizeValues = $this->optionValuesMatching($optionGroups, ['size', 'shoe size']);
        $variations = $colorValues ?: (array_values($optionGroups)[0] ?? []);

        $gallery = collect();

        // Cover image first.
        if ($product->image_path) {
            $gallery->push('buyer/products/' . $product->id . '/image');
        }

        // Every seller gallery image — no buyer-side count cap.
        foreach ($product->galleryImages ?? collect() as $image) {
            $gallery->push('buyer/product-images/' . $image->id . '/image');
        }

        // Variant-specific images also belong in the buyer gallery so buyers can
        // browse every seller-supplied product image before choosing a variant.
        foreach ($variantPayload as $variant) {
            if (!empty($variant['image'])) {
                $gallery->push($variant['image']);
            }
        }

        $gallery = $gallery
            ->filter()
            ->unique()
            ->values();

        return [
            'id' => (int) $product->id,
            'name' => (string) $product->name,
            'category' => (string) ($product->category ?: 'General'),
            'marketplace_category' => (string) ($product->category ?: 'General'),
            'price' => $sellingPrice,
            'old_price' => $discountPercent > 0 ? $basePrice : null,
            'discount_percent' => $discountPercent,
            'flash_sale_active' => $this->isFlashSaleActive($product),
            'flash_sale_ends_at' => $product->flash_sale_ends_at?->toIso8601String(),
            'voucher' => null,
            'free_shipping' => (bool) ($product->free_shipping ?? false),
            'rating' => $rating,
            'rating_count' => (int) ($product->reviews?->count() ?? 0),
            'sold' => 0,
            'stock' => $stock,
            'image' => $product->image_path
                ? 'buyer/products/' . $product->id . '/image'
                : ($gallery->first() ?: $this->categoryImage((string) $product->category)),
            'images' => $gallery->values()->all(),
            'badge' => 'Approved',
            'marketplace_badge' => $storeName,
            'description' => (string) ($product->description ?? ''),
            'variations' => array_values($variations),
            'sizes' => array_values($sizeValues),
            'option_groups' => $optionGroups,
            'variants' => $variantPayload,
            'variant_images' => collect($variantPayload)
                ->filter(fn (array $variant) => !empty($variant['image']))
                ->mapWithKeys(fn (array $variant) => [(string) $variant['id'] => $variant['image']])
                ->all(),
            'condition' => (string) ($product->condition ?? ''),
            'package' => [
                'weight_kg' => $product->package_weight !== null ? (float) $product->package_weight : null,
                'length_cm' => $product->package_length !== null ? (float) $product->package_length : null,
                'width_cm' => $product->package_width !== null ? (float) $product->package_width : null,
                'height_cm' => $product->package_height !== null ? (float) $product->package_height : null,
            ],
            'preparation_days' => $product->preparation_days !== null ? (int) $product->preparation_days : null,
            'low_stock_threshold' => (int) ($product->low_stock_threshold ?? 5),
            'shop_slug' => 'seller-' . (int) $product->seller_account_id,
            'shop_name' => $storeName,
            'store_name' => $storeName,
            'seller_account_id' => (int) $product->seller_account_id,
            'shop_badge' => 'SARI Seller',
            'shop_rating' => $rating,
            'shop_followers' => '—',
            'shop_response_rate' => '—',
            'shop_response_time' => '—',
            'sku' => (string) ($product->sku ?? ''),
            'brand' => (string) ($product->brand ?? ''),
            'has_variants' => (bool) ($product->has_variants ?? $variants->isNotEmpty()),
            'reviews' => $product->relationLoaded('reviews')
                ? $product->reviews->map(fn ($review) => [
                    'rating' => (int) $review->rating,
                    'comment' => (string) ($review->comment ?? ''),
                    'created_at' => $review->created_at?->format('M d, Y'),
                ])->values()->all()
                : [],
        ];
    }

    private function toBuyerShop(SellerAccount $seller, Collection $products): array
    {
        $categories = $products->pluck('category')->filter()->unique()->values()->all();
        array_unshift($categories, 'All Products');

        $storeName = trim((string) ($seller->store_name ?: 'SARI Seller Store'));
        $username = $seller->email
            ? str($seller->email)->before('@')->toString()
            : 'seller' . $seller->id;

        $ratings = $products
            ->flatMap(fn ($product) => $product->reviews ?? collect())
            ->pluck('rating');

        return [
            'slug' => 'seller-' . $seller->id,
            'seller_account_id' => (int) $seller->id,
            'name' => $storeName,
            'store_name' => $storeName,
            'username' => $username,
            'logo' => 'images/sari-logo.png',
            'badge' => 'SARI Seller',
            'rating' => $ratings->isEmpty() ? 0 : round((float) $ratings->avg(), 1),
            'rating_count' => $ratings->count(),
            'products_count' => $products->count(),
            'followers' => '—',
            'following' => '—',
            'response_rate' => '—',
            'response_time' => '—',
            'joined' => $seller->created_at?->diffForHumans() ?: 'SARI seller',
            'categories' => $categories,
        ];
    }

    private function isFlashSaleActive(SellerProduct $product): bool
    {
        return (float) ($product->discount ?? 0) > 0
            && $product->flash_sale_ends_at !== null
            && now()->lt($product->flash_sale_ends_at);
    }

    private function effectiveDiscountPercent(SellerProduct $product): float
    {
        $discount = min(100, max(0, (float) ($product->discount ?? 0)));

        if ($discount <= 0) {
            return 0.0;
        }

        if ($product->flash_sale_ends_at !== null && now()->gte($product->flash_sale_ends_at)) {
            return 0.0;
        }

        return $discount;
    }

    private function discounted(float $base, float $discountPercent): float
    {
        return round($discountPercent > 0 ? $base * (1 - ($discountPercent / 100)) : $base, 2);
    }

    private function sellerIdFromSlug(string $shopSlug): ?int
    {
        if (!preg_match('/^seller-(\d+)$/', $shopSlug, $matches)) {
            return null;
        }

        $sellerId = (int) ($matches[1] ?? 0);

        return $sellerId > 0 ? $sellerId : null;
    }

    private function optionValuesMatching(array $groups, array $needles): array
    {
        foreach ($groups as $name => $values) {
            $normalizedName = strtolower(trim((string) $name));

            foreach ($needles as $needle) {
                if ($normalizedName === $needle || str_contains($normalizedName, $needle)) {
                    return array_values(array_unique($values));
                }
            }
        }

        return [];
    }

    private function approvedProductsQuery(): Builder
    {
        return SellerProduct::query()
            ->with([
                'seller:id,store_name,email,store_status,created_at',
                'activeVariants',
                'galleryImages',
                'reviews:id,seller_product_id,rating,comment,created_at',
            ])
            ->where('moderation_status', 'approved')
            ->whereNull('archived_at')
            ->whereHas('seller', fn (Builder $query) => $query->where(function (Builder $sellerQuery) {
                $sellerQuery->whereNull('store_status')->orWhere('store_status', 'open');
            }));
    }

    private function categoryImage(string $category): string
    {
        $category = strtolower($category);

        return match (true) {
            str_contains($category, 'food') => 'images/cat-food.jpg',
            str_contains($category, 'beauty') => 'images/cat-beauty.jpg',
            str_contains($category, 'electronic'),
            str_contains($category, 'appliance') => 'images/cat-electronics.jpg',
            str_contains($category, 'home'),
            str_contains($category, 'furniture') => 'images/cat-home.jpg',
            str_contains($category, 'sport'),
            str_contains($category, 'outdoor') => 'images/cat-sports.jpg',
            str_contains($category, 'fashion'),
            str_contains($category, 'apparel'),
            str_contains($category, 'shoe') => 'images/cat-fashion.jpg',
            str_contains($category, 'jewel'),
            str_contains($category, 'watch'),
            str_contains($category, 'accessor') => 'images/cat-accessories.jpg',
            default => 'images/cat-lifestyle.jpg',
        };
    }
}
