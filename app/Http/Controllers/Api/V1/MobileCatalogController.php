<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Catalog\SellerProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MobileCatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['newest', 'price_low', 'price_high'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $query = SellerProduct::query()->buyerVisible()
            ->with('seller:id,store_name,store_logo_path')
            ->select(['id', 'seller_account_id', 'name', 'category', 'brand',
                'price', 'stock', 'discount', 'description', 'image_path', 'created_at']);

        if (filled($filters['q'] ?? null)) {
            $search = trim($filters['q']);
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('brand', 'like', '%'.$search.'%');
            });
        }

        if (filled($filters['category'] ?? null)) {
            $query->where('category', $filters['category']);
        }

        match ($filters['sort'] ?? 'newest') {
            'price_low' => $query->orderBy('price')->orderBy('id'),
            'price_high' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->latest('id'),
        };

        $products = $query->paginate((int) ($filters['per_page'] ?? 15));

        return response()->json([
            'success' => true,
            'data' => $products->getCollection()->map(fn (SellerProduct $product) =>
                $this->present($product))->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'links' => ['next' => $products->nextPageUrl(), 'prev' => $products->previousPageUrl()],
        ]);
    }

    public function show(SellerProduct $product): JsonResponse
    {
        abort_unless($product->isBuyerVisible(), 404);
        $product->load('seller:id,store_name,store_logo_path');

        return response()->json(['success' => true, 'data' => $this->present($product)]);
    }

    private function present(SellerProduct $product): array
    {
        return [
            'id' => (int) $product->id,
            'seller_id' => (int) $product->seller_account_id,
            'store_name' => (string) ($product->seller?->store_name ?? 'SARI Store'),
            'name' => (string) $product->name,
            'category' => (string) $product->category,
            'brand' => $product->brand,
            'description' => $product->description,
            'price' => (string) $product->price,
            'stock' => (int) $product->stock,
            'discount' => (string) ($product->discount ?? '0'),
            'image_url' => filled($product->image_path)
                ? route('marketplace.product.image', ['product' => $product->id]) : null,
        ];
    }
}
