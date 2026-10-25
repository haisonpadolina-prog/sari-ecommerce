<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductImage;
use App\Models\Catalog\SellerProductVariant;
use App\Services\Buyer\BuyerCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuyerProductController extends Controller
{
    public function __construct(
        private readonly BuyerCatalogService $catalog
    ) {
    }

    public function index(Request $request)
    {
        if (!$this->isBuyer($request)) {
            return redirect()->route('login');
        }

        $searchQuery = trim((string) $request->query('search', ''));
        $products = $this->catalog->catalog();
        $productCollection = collect($products);

        $trendingPool = $productCollection
            ->filter(fn (array $product) => (int) ($product['stock'] ?? 0) > 0);

        if ($trendingPool->isEmpty()) {
            $trendingPool = $productCollection;
        }

        $trendingProducts = $trendingPool
            ->sort(function (array $left, array $right): int {
                foreach (['sold', 'rating_count', 'rating', 'id'] as $field) {
                    $comparison = ((float) ($right[$field] ?? 0)) <=> ((float) ($left[$field] ?? 0));

                    if ($comparison !== 0) {
                        return $comparison;
                    }
                }

                return 0;
            })
            ->take(3)
            ->values()
            ->all();

        $popularCategories = $productCollection
            ->groupBy(fn (array $product) => strtolower(trim((string) ($product['category'] ?? 'General'))))
            ->map(function ($categoryProducts): array {
                $first = $categoryProducts->first();

                return [
                    'name' => trim((string) ($first['category'] ?? 'General')) ?: 'General',
                    'count' => $categoryProducts->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->all();

        return view('buyer.products', [
            'products' => $products,
            'categories' => $this->catalog->categories(),
            'trendingProducts' => $trendingProducts,
            'popularCategories' => $popularCategories,
            'searchQuery' => $searchQuery,
            'shopResults' => $this->catalog->shopResults($searchQuery),
        ]);
    }

    public function shop(Request $request, string $shop)
    {
        if (!$this->isBuyer($request)) {
            return redirect()->route('login');
        }

        $shopData = $this->catalog->shop($shop);

        abort_unless($shopData, 404);

        return view('buyer.shop', [
            'shop' => $shopData,
            'products' => $this->catalog->productsForShop($shop),
        ]);
    }

    public function show(Request $request, int $product)
    {
        if (!$this->isBuyer($request)) {
            return redirect()->route('login');
        }

        $selectedProduct = $this->catalog->find($product);

        abort_unless($selectedProduct, 404);

        return view('buyer.product-details', [
            'product' => $selectedProduct,
            'products' => $this->catalog->catalog(12),
        ]);
    }

    /**
     * Buyer-safe product image endpoint.
     * SellerProductController::image() intentionally allows only Admin/Seller,
     * so Buyer gets a separate read-only endpoint for approved active listings.
     */
    public function image(Request $request, SellerProduct $product)
    {
        abort_unless($this->isBuyer($request), 403);
        abort_unless($product->isBuyerVisible(), 404);
        abort_unless($product->image_path, 404);
        abort_unless(Storage::disk('public')->exists($product->image_path), 404);

        return Storage::disk('public')->response($product->image_path);
    }

    public function galleryImage(Request $request, SellerProductImage $image)
    {
        abort_unless($this->isBuyer($request), 403);

        $product = $image->product;
        abort_unless($product, 404);
        abort_unless($product->isBuyerVisible(), 404);
        abort_unless($image->path && Storage::disk('public')->exists($image->path), 404);

        return Storage::disk('public')->response($image->path);
    }

    public function variantImage(Request $request, SellerProductVariant $variant)
    {
        abort_unless($this->isBuyer($request), 403);

        $product = $variant->product;
        abort_unless($product, 404);
        abort_unless($variant->is_active, 404);
        abort_unless($product->isBuyerVisible(), 404);
        abort_unless($variant->image_path && Storage::disk('public')->exists($variant->image_path), 404);

        return Storage::disk('public')->response($variant->image_path);
    }

    private function isBuyer(Request $request): bool
    {
        return (bool) $request->session()->get('is_buyer');
    }
}
