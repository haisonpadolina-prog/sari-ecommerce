<?php

namespace App\Services\Buyer;

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Promotions\SellerVoucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BuyerCatalogService
{
    /** @var array<int, int>|null */
    private ?array $soldCounts = null;

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
        $query = $this->approvedProductsQuery()
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id');

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get();

        return $products
            ->map(fn (SellerProduct $product) => $this->toBuyerProduct($product))
            ->values()
            ->all();
    }

    public function find(int $productId): ?array
    {
        $product = SellerProduct::query()
            ->with([
                'seller:id,store_name,email,store_description,store_phone,store_public_email,store_status,store_logo_path,store_banner_path,account_status,warning_count,suspended_until,suspension_reason,created_at',
                'seller.vouchers' => $this->activeVoucherConstraint(...),
                'activeVariants',
                'galleryImages',
                'reviews:id,seller_product_id,rating,comment,created_at',
                'reviews.sellerReply:id,product_review_id,seller_account_id,reply,created_at',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereKey($productId)
            ->buyerVisible()
            ->first();

        return $product ? $this->toBuyerProduct($product) : null;
    }

    public function productsForShop(string $shopSlug): array
    {
        $sellerId = $this->sellerIdFromSlug($shopSlug);

        if (!$sellerId) {
            return [];
        }

        return $this->approvedProductsQuery()
            ->where('seller_account_id', $sellerId)
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get()
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

        $seller = SellerAccount::query()
            ->with(['vouchers' => $this->activeVoucherConstraint(...)])
            ->whereKey($sellerId)
            ->buyerAvailable()
            ->first();

        if (!$seller) {
            return null;
        }

        $products = $this->approvedProductsQuery()
            ->where('seller_account_id', $sellerId)
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get()
            ->values();

        return $this->toBuyerShop($seller, $products);
    }

    public function shopResults(string $query): array
    {
        $query = strtolower(trim($query));

        if ($query === '') {
            return [];
        }

        $like = '%' . $query . '%';

        return SellerAccount::query()
            ->where(function (Builder $sellerQuery) use ($like): void {
                $sellerQuery
                    ->where('store_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->buyerAvailable()
            ->whereHas('products', fn (Builder $productQuery) => $productQuery->buyerVisible())
            ->withCount([
                'products as buyer_visible_products_count' =>
                    fn (Builder $productQuery) => $productQuery->buyerVisible(),
            ])
            ->limit(5)
            ->get()
            ->map(fn (SellerAccount $seller) => $this->toBuyerShopResult($seller))
            ->values()
            ->all();
    }

    public function categories(): array
    {
        $discovered = SellerProduct::query()
            ->buyerVisible()
            ->select('category')
            ->distinct()
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
        $reviewCount = isset($product->reviews_count)
            ? (int) $product->reviews_count
            : ($product->relationLoaded('reviews') ? $product->reviews->count() : 0);

        $rating = isset($product->reviews_avg_rating)
            ? round((float) $product->reviews_avg_rating, 1)
            : ($reviewCount > 0 && $product->relationLoaded('reviews')
                ? round((float) $product->reviews->avg('rating'), 1)
                : 0.0);

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
            'voucher' => $this->primaryVoucher($seller, (string) ($product->voucher_code ?? '')),
            'free_shipping' => (bool) ($product->free_shipping ?? false),
            // Marketplace checkout supports Cash on Delivery for buyer products.
            // Keep this explicit so the existing product card can render its COD badge.
            'cod' => true,
            'rating' => $rating,
            'rating_count' => $reviewCount,
            'sold' => $this->soldCountForProduct((int) $product->id),
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
            'shop_logo' => $this->publicMediaUrl($seller?->store_logo_path),
            'shop_description' => (string) ($seller?->store_description ?? ''),
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
                    'seller_reply' => $review->sellerReply ? [
                        'reply' => (string) $review->sellerReply->reply,
                        'created_at' => $review->sellerReply->created_at?->format('M d, Y'),
                    ] : null,
                ])->values()->all()
                : [],
        ];
    }

    private function toBuyerShopResult(SellerAccount $seller): array
    {
        $storeName = trim((string) ($seller->store_name ?: 'SARI Seller Store'));
        $username = $seller->email
            ? str($seller->email)->before('@')->toString()
            : 'seller' . $seller->id;

        return [
            'slug' => 'seller-' . $seller->id,
            'seller_account_id' => (int) $seller->id,
            'name' => $storeName,
            'store_name' => $storeName,
            'username' => $username,
            'logo' => $this->publicMediaUrl($seller->store_logo_path) ?: asset('images/sari-logo.png'),
            'badge' => 'SARI Seller',
            'products_count' => (int) ($seller->buyer_visible_products_count ?? 0),
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

        $ratingCount = (int) $products->sum(function (SellerProduct $product): int {
            if (isset($product->reviews_count)) {
                return (int) $product->reviews_count;
            }

            return $product->relationLoaded('reviews') ? $product->reviews->count() : 0;
        });

        $ratingTotal = (float) $products->sum(function (SellerProduct $product): float {
            $count = isset($product->reviews_count)
                ? (int) $product->reviews_count
                : ($product->relationLoaded('reviews') ? $product->reviews->count() : 0);

            if ($count <= 0) {
                return 0.0;
            }

            $average = isset($product->reviews_avg_rating)
                ? (float) $product->reviews_avg_rating
                : (float) $product->reviews->avg('rating');

            return $average * $count;
        });

        return [
            'slug' => 'seller-' . $seller->id,
            'seller_account_id' => (int) $seller->id,
            'name' => $storeName,
            'store_name' => $storeName,
            'description' => (string) ($seller->store_description ?? ''),
            'public_phone' => (string) ($seller->store_phone ?? ''),
            'public_email' => (string) ($seller->store_public_email ?? ''),
            'username' => $username,
            'logo' => $this->publicMediaUrl($seller->store_logo_path) ?: asset('images/sari-logo.png'),
            'banner' => $this->publicMediaUrl($seller->store_banner_path),
            'badge' => 'SARI Seller',
            'rating' => $ratingCount > 0 ? round($ratingTotal / $ratingCount, 1) : 0,
            'rating_count' => $ratingCount,
            'products_count' => $products->count(),
            'followers' => '—',
            'following' => '—',
            'response_rate' => '—',
            'response_time' => '—',
            'joined' => $seller->created_at?->diffForHumans() ?: 'SARI seller',
            'categories' => $categories,
            'vouchers' => $seller->relationLoaded('vouchers')
                ? $seller->vouchers->map(fn (SellerVoucher $voucher) => $this->voucherPayload($voucher))->values()->all()
                : [],
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

    private function soldCountForProduct(int $productId): int
    {
        if ($productId <= 0) {
            return 0;
        }

        $counts = $this->soldCounts();

        return (int) ($counts[$productId] ?? 0);
    }

    /**
     * @return array<int, int>
     */
    private function soldCounts(): array
    {
        if ($this->soldCounts !== null) {
            return $this->soldCounts;
        }

        $this->soldCounts = Cache::remember('buyer.catalog.sold-counts.v1', now()->addMinute(), function (): array {
            $counts = [];

            MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->select(['id', 'items'])
                ->orderBy('id')
                ->chunkById(250, function ($orders) use (&$counts): void {
                    foreach ($orders as $order) {
                        foreach ((array) $order->items as $item) {
                            $productId = (int) ($item['product_id'] ?? 0);
                            $quantity = max(0, (int) ($item['qty'] ?? $item['quantity'] ?? 0));

                            if ($productId <= 0 || $quantity <= 0) {
                                continue;
                            }

                            $counts[$productId] = ($counts[$productId] ?? 0) + $quantity;
                        }
                    }
                });

            return $counts;
        });

        return $this->soldCounts;
    }

    private function approvedProductsQuery(): Builder
    {
        return SellerProduct::query()
            ->with([
                'seller:id,store_name,email,store_description,store_phone,store_public_email,store_status,store_logo_path,store_banner_path,account_status,warning_count,suspended_until,suspension_reason,created_at',
                'activeVariants',
                'galleryImages',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->buyerVisible();
    }

    private function activeVoucherConstraint(Builder|Relation $query): void
    {
        $query
            ->currentlyUsable()
            ->orderByDesc('created_at');
    }

    private function primaryVoucher(?SellerAccount $seller, string $preferredCode = ''): ?array
    {
        if (!$seller || !$seller->relationLoaded('vouchers')) {
            return null;
        }

        $preferredCode = strtoupper(trim($preferredCode));
        $voucher = $preferredCode !== ''
            ? $seller->vouchers->first(fn (SellerVoucher $candidate) => strtoupper((string) $candidate->code) === $preferredCode)
            : null;

        $voucher ??= $seller->vouchers->first();

        return $voucher instanceof SellerVoucher ? $this->voucherPayload($voucher) : null;
    }

    private function voucherPayload(SellerVoucher $voucher): array
    {
        $value = $voucher->discount_type === 'percentage'
            ? rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') . '% off'
            : '₱' . number_format((float) $voucher->discount_value, 2) . ' off';

        return [
            'id' => (int) $voucher->id,
            'code' => (string) $voucher->code,
            'name' => (string) $voucher->name,
            'type' => (string) $voucher->discount_type,
            'value' => $value,
            'minimum' => (float) $voucher->minimum_spend,
            'maximum_discount' => $voucher->maximum_discount !== null ? (float) $voucher->maximum_discount : null,
            'ends_at' => $voucher->ends_at?->toIso8601String(),
        ];
    }

    private function publicMediaUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        return $path !== '' ? Storage::disk('public')->url($path) : null;
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
