<?php

namespace App\Http\Controllers;

use App\Models\SellerProduct;
use App\Models\SellerProductImage;
use App\Models\SellerProductVariant;
use App\Services\BuyerCatalogService;
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

        return view('buyer.products', [
            'products' => $this->catalog->catalog(),
            'categories' => $this->catalog->categories(),
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
        abort_unless($product->moderation_status === 'approved', 404);
        abort_unless(is_null($product->archived_at), 404);
        abort_unless($product->image_path, 404);
        abort_unless(Storage::disk('public')->exists($product->image_path), 404);

        return Storage::disk('public')->response($product->image_path);
    }

    public function galleryImage(Request $request, SellerProductImage $image)
    {
        abort_unless($this->isBuyer($request), 403);

        $product = $image->product;
        abort_unless($product, 404);
        abort_unless($product->moderation_status === 'approved', 404);
        abort_unless(is_null($product->archived_at), 404);
        abort_unless($image->path && Storage::disk('public')->exists($image->path), 404);

        return Storage::disk('public')->response($image->path);
    }

    public function variantImage(Request $request, SellerProductVariant $variant)
    {
        abort_unless($this->isBuyer($request), 403);

        $product = $variant->product;
        abort_unless($product, 404);
        abort_unless($variant->is_active, 404);
        abort_unless($product->moderation_status === 'approved', 404);
        abort_unless(is_null($product->archived_at), 404);
        abort_unless($variant->image_path && Storage::disk('public')->exists($variant->image_path), 404);

        return Storage::disk('public')->response($variant->image_path);
    }

    private function isBuyer(Request $request): bool
    {
        return (bool) $request->session()->get('is_buyer');
    }
}
