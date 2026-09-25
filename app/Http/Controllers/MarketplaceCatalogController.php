<?php

namespace App\Http\Controllers;

use App\Models\SellerProduct;
use App\Services\BuyerCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarketplaceCatalogController extends Controller
{
    public function __construct(
        private readonly BuyerCatalogService $catalog
    ) {
    }

    public function category(Request $request, string $category): View
    {
        $definition = $this->catalog->categoryDefinition($category);
        abort_unless($definition, 404);

        $search = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'featured');

        if (!in_array($sort, ['featured', 'newest', 'price_low', 'price_high'], true)) {
            $sort = 'featured';
        }

        return view('marketplace.category', [
            'category' => $definition,
            'categories' => $this->catalog->marketplaceCategories(),
            'products' => $this->catalog->categoryProducts($category, $search, $sort),
            'searchQuery' => $search,
            'sort' => $sort,
            'isBuyer' => (bool) $request->session()->get('is_buyer'),
        ]);
    }

    public function image(SellerProduct $product): StreamedResponse
    {
        $product->loadMissing('seller:id,store_status');

        abort_unless($product->moderation_status === 'approved', 404);
        abort_unless(is_null($product->archived_at), 404);
        abort_unless(
            $product->seller
            && (is_null($product->seller->store_status) || $product->seller->store_status === 'open'),
            404
        );
        abort_unless($product->image_path, 404);
        abort_unless(Storage::disk('public')->exists($product->image_path), 404);

        return Storage::disk('public')->response($product->image_path);
    }
}
