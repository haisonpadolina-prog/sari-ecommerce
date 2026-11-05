<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Catalog\SellerProduct;
use App\Services\Buyer\BuyerCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MobileCatalogController extends Controller
{
    public function __construct(
        private readonly BuyerCatalogService $catalog,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['newest', 'price_low', 'price_high'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $query = SellerProduct::query()
            ->buyerVisible()
            ->with('seller:id,store_name,store_logo_path')
            ->select([
                'id',
                'seller_account_id',
                'name',
                'category',
                'brand',
                'condition',
                'sku',
                'price',
                'stock',
                'discount',
                'description',
                'image_path',
                'preparation_days',
                'created_at',
            ]);

        if (filled($filters['q'] ?? null)) {
            $search = trim((string) $filters['q']);

            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('brand', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%');
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
            'data' => $products->getCollection()
                ->map(fn (SellerProduct $product) => $this->presentCard($product))
                ->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'links' => [
                'next' => $products->nextPageUrl(),
                'prev' => $products->previousPageUrl(),
            ],
        ]);
    }

    public function show(SellerProduct $product): JsonResponse
    {
        abort_unless($product->isBuyerVisible(), 404);

        // Use the same buyer catalog service as the Laravel web marketplace so
        // pricing, discounts, option groups and variants cannot drift between
        // web and mobile.
        $payload = $this->catalog->find((int) $product->id);
        abort_unless(is_array($payload), 404);

        $product->loadMissing([
            'seller:id,store_name,store_logo_path',
            'activeVariants',
            'galleryImages',
        ]);

        $coverUrl = $this->mediaUrl($product->image_path);

        $variantModels = $product->activeVariants->keyBy(
            fn ($variant) => (int) $variant->id
        );

        $variants = collect((array) ($payload['variants'] ?? []))
            ->map(function ($variant) use ($variantModels): array {
                $variant = is_array($variant) ? $variant : [];
                $id = (int) ($variant['id'] ?? 0);
                $model = $variantModels->get($id);
                $imageUrl = $this->mediaUrl($model?->image_path);

                // Keep both keys because existing Flutter versions use either.
                $variant['image'] = $imageUrl;
                $variant['image_url'] = $imageUrl;
                $variant['available'] = (bool) ($variant['available'] ?? ((int) ($variant['stock'] ?? 0) > 0));

                return $variant;
            })
            ->values()
            ->all();

        $images = collect();

        if ($coverUrl) {
            $images->push($coverUrl);
        }

        foreach ($product->galleryImages as $galleryImage) {
            $url = $this->mediaUrl($galleryImage->path);
            if ($url) {
                $images->push($url);
            }
        }

        foreach ($variants as $variant) {
            if (!empty($variant['image_url'])) {
                $images->push($variant['image_url']);
            }
        }

        $images = $images->filter()->unique()->values()->all();

        $payload['seller_id'] = (int) $product->seller_account_id;
        $payload['seller_account_id'] = (int) $product->seller_account_id;
        $payload['store_name'] = (string) ($product->seller?->store_name ?? ($payload['store_name'] ?? 'SARI Store'));
        $payload['shop_name'] = $payload['store_name'];
        $payload['store_logo_url'] = $this->mediaUrl($product->seller?->store_logo_path);
        $payload['shop_logo'] = $payload['store_logo_url'];

        $payload['image_url'] = $coverUrl ?: ($images[0] ?? null);
        $payload['image'] = $payload['image_url'];
        $payload['images'] = $images;

        $payload['variants'] = $variants;
        $payload['has_variants'] = $product->activeVariants->isNotEmpty();
        $payload['option_groups'] = is_array($payload['option_groups'] ?? null)
            ? $payload['option_groups']
            : [];

        return response()->json([
            'success' => true,
            'data' => $payload,
        ])->header('Cache-Control', 'no-store');
    }

    private function presentCard(SellerProduct $product): array
    {
        return [
            'id' => (int) $product->id,
            'seller_id' => (int) $product->seller_account_id,
            'seller_account_id' => (int) $product->seller_account_id,
            'store_name' => (string) ($product->seller?->store_name ?? 'SARI Store'),
            'store_logo_url' => $this->mediaUrl($product->seller?->store_logo_path),
            'name' => (string) $product->name,
            'category' => (string) ($product->category ?: 'General'),
            'brand' => $product->brand,
            'condition' => $product->condition,
            'sku' => $product->sku,
            'description' => $product->description,
            'price' => (string) $product->price,
            'stock' => (int) $product->stock,
            'discount' => (string) ($product->discount ?? '0'),
            'preparation_days' => $product->preparation_days !== null
                ? (int) $product->preparation_days
                : null,
            'image_url' => $this->mediaUrl($product->image_path),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}


