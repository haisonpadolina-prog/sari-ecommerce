<?php

namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerProductDraft;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerProductDraftController extends Controller
{
    /**
     * Return the seller's draft list and optionally one selected draft.
     *
     * GET /seller/product-draft
     * GET /seller/product-draft?draft_id=123
     */
    public function index(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $draftId = max(0, (int) $request->query('draft_id', 0));

        $selectedDraft = $draftId > 0
            ? SellerProductDraft::query()
                ->where('seller_account_id', $seller->id)
                ->whereKey($draftId)
                ->first()
            : null;

        return response()->json([
            'ok' => true,
            'draft' => $selectedDraft
                ? $this->draftPayload($selectedDraft)
                : null,
            'drafts' => $this->draftList($seller),
        ]);
    }

    /**
     * Save a new draft or update one existing draft.
     *
     * - No draft_id => creates a new draft.
     * - Existing seller-owned draft_id => updates only that draft.
     */
    public function store(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $validated = $this->validateDraft($request);

        $forceNewDraft = $request->boolean('force_new_draft');
        $draftId = $forceNewDraft
            ? 0
            : max(0, (int) ($validated['draft_id'] ?? 0));

        if ($draftId > 0) {
            $draft = SellerProductDraft::query()
                ->where('seller_account_id', $seller->id)
                ->whereKey($draftId)
                ->firstOrFail();
        } else {
            $draft = new SellerProductDraft();
            $draft->forceFill([
                'seller_account_id' => $seller->id,
                'payload' => [],
                'gallery_image_paths' => [],
                'variant_image_paths' => [],
            ])->save();
        }

        $coverPath = $draft->cover_image_path;
        $galleryPaths = array_values((array) ($draft->gallery_image_paths ?? []));
        $variantPaths = (array) ($draft->variant_image_paths ?? []);

        if ($request->hasFile('image')) {
            $this->deletePublicFile($coverPath);

            $coverPath = $request->file('image')->store(
                'seller-product-drafts/' . $seller->id . '/' . $draft->id . '/cover',
                'public'
            );
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($galleryPaths as $path) {
                $this->deletePublicFile($path);
            }

            $galleryPaths = [];

            foreach (array_slice((array) $request->file('gallery_images', []), 0, 12) as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $galleryPaths[] = $file->store(
                    'seller-product-drafts/' . $seller->id . '/' . $draft->id . '/gallery',
                    'public'
                );
            }
        }

        $variantFiles = (array) $request->file('variants', []);

        foreach ($variantFiles as $index => $variantFileRow) {
            $file = is_array($variantFileRow)
                ? ($variantFileRow['image'] ?? null)
                : null;

            if (!$file instanceof UploadedFile) {
                continue;
            }

            $optionsJson = (string) data_get(
                $validated,
                'variants.' . $index . '.options',
                ''
            );

            $options = json_decode($optionsJson, true);
            $key = $this->variantKey(is_array($options) ? $options : []);

            if ($key === '') {
                continue;
            }

            $this->deletePublicFile($variantPaths[$key] ?? null);

            $variantPaths[$key] = $file->store(
                'seller-product-drafts/' . $seller->id . '/' . $draft->id . '/variants',
                'public'
            );
        }

        $payload = $request->except([
            '_token',
            '_method',
            'image',
            'gallery_images',
        ]);

        unset($payload['draft_id'], $payload['force_new_draft']);

        $draft->forceFill([
            'seller_account_id' => $seller->id,
            'payload' => $payload,
            'cover_image_path' => $coverPath,
            'gallery_image_paths' => array_values($galleryPaths),
            'variant_image_paths' => $variantPaths,
        ])->save();

        $freshDraft = $draft->fresh();

        return response()->json([
            'ok' => true,
            'message' => $draftId > 0
                ? 'Draft updated.'
                : 'New draft saved.',
            'mode' => $draftId > 0 ? 'updated' : 'created',
            'draft' => $this->draftPayload($freshDraft),
            'drafts' => $this->draftList($seller),
        ]);
    }

    /**
     * Delete one seller-owned draft.
     */
    public function destroy(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $validated = $request->validate([
            'draft_id' => ['required', 'integer', 'min:1'],
        ]);

        $draft = SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->whereKey((int) $validated['draft_id'])
            ->firstOrFail();

        $this->deleteDraftMedia($draft);
        $draft->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Draft deleted.',
            'drafts' => $this->draftList($seller),
        ]);
    }

    /**
     * Serve one temporary draft image, scoped to both seller + draft id.
     */
    public function media(
        Request $request,
        string $kind,
        ?string $key = null
    ) {
        $seller = $this->resolveSeller($request);

        $draftId = max(0, (int) $request->query('draft_id', 0));
        abort_if($draftId <= 0, 404);

        $draft = SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->whereKey($draftId)
            ->firstOrFail();

        $galleryPaths = array_values((array) ($draft->gallery_image_paths ?? []));
        $variantPaths = (array) ($draft->variant_image_paths ?? []);

        $path = match ($kind) {
            'cover' => $draft->cover_image_path,
            'gallery' => $galleryPaths[(int) ($key ?? -1)] ?? null,
            'variant' => $variantPaths[(string) ($key ?? '')] ?? null,
            default => null,
        };

        abort_unless(
            $path && Storage::disk('public')->exists($path),
            404
        );

        return Storage::disk('public')->response($path);
    }

    private function draftList(SellerAccount $seller): array
    {
        return SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->latest('updated_at')
            ->get()
            ->map(function (SellerProductDraft $draft) {
                $payload = (array) ($draft->payload ?? []);

                return [
                    'id' => (int) $draft->id,
                    'name' => trim((string) ($payload['name'] ?? ''))
                        ?: 'Untitled product',
                    'category' => trim((string) ($payload['category'] ?? ''))
                        ?: 'No category',
                    'saved_at' => $draft->updated_at?->toIso8601String(),
                    'has_cover' => filled($draft->cover_image_path),
                    'cover_image_url' => $draft->cover_image_path
                        ? route('seller.products.draft-media', [
                            'kind' => 'cover',
                            'key' => 'cover',
                            'draft_id' => $draft->id,
                        ])
                        : null,
                    'gallery_count' => count(
                        (array) ($draft->gallery_image_paths ?? [])
                    ),
                ];
            })
            ->values()
            ->all();
    }

    private function draftPayload(SellerProductDraft $draft): array
    {
        $gallery = array_values(
            (array) ($draft->gallery_image_paths ?? [])
        );

        $variants = (array) ($draft->variant_image_paths ?? []);

        return [
            'id' => (int) $draft->id,
            'payload' => (array) ($draft->payload ?? []),
            'saved_at' => $draft->updated_at?->toIso8601String(),

            'cover_image_url' => $draft->cover_image_path
                ? route('seller.products.draft-media', [
                    'kind' => 'cover',
                    'key' => 'cover',
                    'draft_id' => $draft->id,
                ])
                : null,

            'gallery_images' => collect($gallery)
                ->map(fn ($path, $index) => [
                    'index' => $index,
                    'url' => route('seller.products.draft-media', [
                        'kind' => 'gallery',
                        'key' => $index,
                        'draft_id' => $draft->id,
                    ]),
                ])
                ->values()
                ->all(),

            'variant_images' => collect($variants)
                ->map(fn ($path, $key) => [
                    'key' => $key,
                    'url' => route('seller.products.draft-media', [
                        'kind' => 'variant',
                        'key' => $key,
                        'draft_id' => $draft->id,
                    ]),
                ])
                ->values()
                ->all(),
        ];
    }

    private function validateDraft(Request $request): array
    {
        return $request->validate([
            'draft_id' => ['nullable', 'integer', 'min:1'],
            'force_new_draft' => ['nullable', 'boolean'],

            'name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'custom_category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:120'],
            'sku' => ['nullable', 'string', 'max:120'],

            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'flash_sale_ends_at' => ['nullable', 'date'],

            'free_shipping' => ['nullable', 'boolean'],
            'cash_on_delivery' => ['nullable', 'boolean'],

            'condition' => ['nullable', 'string', 'max:80'],
            'package_weight' => ['nullable', 'numeric', 'min:0'],
            'package_length' => ['nullable', 'numeric', 'min:0'],
            'package_width' => ['nullable', 'numeric', 'min:0'],
            'package_height' => ['nullable', 'numeric', 'min:0'],
            'preparation_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'voucher_code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:5000'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'gallery_images' => ['nullable', 'array', 'max:12'],
            'gallery_images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'has_variants' => ['nullable', 'boolean'],

            'specifications' => ['nullable', 'array', 'max:30'],
            'specifications.*.name' => ['nullable', 'string', 'max:80'],
            'specifications.*.value' => ['nullable', 'string', 'max:255'],
            'specifications.*.unit' => ['nullable', 'string', 'max:40'],

            'variants' => ['nullable', 'array', 'max:100'],
            'variants.*.options' => ['nullable', 'string', 'max:2000'],
            'variants.*.sku' => ['nullable', 'string', 'max:120'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);
    }

    private function variantKey(array $options): string
    {
        if (!$options) {
            return '';
        }

        ksort($options);

        return sha1(
            json_encode(
                $options,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );
    }

    private function deleteDraftMedia(SellerProductDraft $draft): void
    {
        $this->deletePublicFile($draft->cover_image_path);

        foreach ((array) ($draft->gallery_image_paths ?? []) as $path) {
            $this->deletePublicFile($path);
        }

        foreach ((array) ($draft->variant_image_paths ?? []) as $path) {
            $this->deletePublicFile($path);
        }

        Storage::disk('public')->deleteDirectory(
            'seller-product-drafts/' .
            $draft->seller_account_id .
            '/' .
            $draft->id
        );
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        if (!$request->session()->get('is_seller')) {
            abort(403, 'Seller session required.');
        }

        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        return SellerAccount::query()->findOrFail(
            $request->session()->get('seller_account_id')
        );
    }
}
