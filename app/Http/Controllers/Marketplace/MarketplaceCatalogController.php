<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;

use App\Models\Catalog\SellerProduct;
use App\Services\Buyer\BuyerCatalogService;
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
        abort_unless($product->isBuyerVisible(), 404);
        abort_unless($product->image_path, 404);
        abort_unless(Storage::disk('public')->exists($product->image_path), 404);

        return Storage::disk('public')->response($product->image_path);
    }
}
