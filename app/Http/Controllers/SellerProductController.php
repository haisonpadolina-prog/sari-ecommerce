<?php

namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Models\SellerProductDraft;
use App\Models\SellerProductImage;
use App\Models\SellerProductVariant;
use App\Models\SellerProductVersion;
use App\Services\ProductComplianceService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerProductController extends Controller
{
    /**
     * Dedicated seller product-management page.
     *
     * The page intentionally reuses the existing lazy Product Library JSON
     * endpoint and every existing CRUD/compliance action. No old product
     * backend behavior is removed or duplicated here.
     */
    public function index(Request $request)
    {
        $seller = $this->resolveSeller($request);

        /*
         * Professional first-paint path:
         * - render the first 12 products with the HTML response
         * - do not make the browser wait for the Product Library AJAX call
         * - load the complete library in the background after first paint
         */
        $initialProducts = $this->productLibraryPayload($seller, 12);
        $catalogSummary = $this->catalogInventorySummary($seller);

        return view('seller.products', [
            'sellerAccount' => $seller,
            'initialProductLibrary' => [
                'success' => true,
                'server_now' => now()->toIso8601String(),
                'count' => $catalogSummary['total'],
                'initial_count' => $initialProducts->count(),
                'summary' => $catalogSummary,
                'products' => $initialProducts->values()->all(),
            ],
        ]);
    }


    /**
     * Dedicated Add Product page.
     *
     * Product creation is intentionally separated from the catalog page so
     * the seller can work in a full Seller Center workspace rather than a
     * constrained modal.
     */
    public function create(Request $request)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureCanSell($seller);

        return view('seller.products.create', [
            'sellerAccount' => $seller,
        ]);
    }

    /**
     * Return the seller's persisted Add Product draft.
     */
    public function draft(Request $request)
    {
        $seller = $this->resolveSeller($request);
        $draft = SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->first();

        return response()->json([
            'ok' => true,
            'draft' => $draft ? $this->draftPayload($draft) : null,
        ]);
    }

    /**
     * Persist an incomplete Add Product form, including temporary media.
     */
    public function saveDraft(Request $request)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureCanSell($seller);

        $validated = $this->validateDraft($request);
        $draft = SellerProductDraft::firstOrNew([
            'seller_account_id' => $seller->id,
        ]);

        $coverPath = $draft->cover_image_path;
        $galleryPaths = array_values((array) ($draft->gallery_image_paths ?? []));
        $variantPaths = (array) ($draft->variant_image_paths ?? []);

        if ($request->hasFile('image')) {
            $this->deletePublicFile($coverPath);
            $coverPath = $request->file('image')->store(
                'seller-product-drafts/' . $seller->id . '/cover',
                'public'
            );
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($galleryPaths as $path) {
                $this->deletePublicFile($path);
            }

            $galleryPaths = [];
            foreach ($request->file('gallery_images', []) as $file) {
                if ($file instanceof UploadedFile) {
                    $galleryPaths[] = $file->store(
                        'seller-product-drafts/' . $seller->id . '/gallery',
                        'public'
                    );
                }
            }
        }

        $variantFiles = $request->file('variants', []);
        foreach ((array) $variantFiles as $index => $variantFileRow) {
            $file = is_array($variantFileRow) ? ($variantFileRow['image'] ?? null) : null;
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $optionsJson = (string) data_get($validated, 'variants.' . $index . '.options', '');
            $options = json_decode($optionsJson, true);
            $key = $this->variantKey(is_array($options) ? $options : []);

            if ($key === '') {
                continue;
            }

            $this->deletePublicFile($variantPaths[$key] ?? null);
            $variantPaths[$key] = $file->store(
                'seller-product-drafts/' . $seller->id . '/variants',
                'public'
            );
        }

        $payload = $request->except([
            '_token',
            '_method',
            'image',
            'gallery_images',
        ]);

        $draft->forceFill([
            'payload' => $payload,
            'cover_image_path' => $coverPath,
            'gallery_image_paths' => array_values($galleryPaths),
            'variant_image_paths' => $variantPaths,
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => 'Draft saved to your seller account.',
            'draft' => $this->draftPayload($draft->fresh()),
        ]);
    }

    public function deleteDraft(Request $request)
    {
        $seller = $this->resolveSeller($request);
        $draft = SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->first();

        if ($draft) {
            $this->deleteDraftMedia($draft);
            $draft->delete();
        }

        return response()->json([
            'ok' => true,
            'message' => 'Draft cleared.',
        ]);
    }

    public function draftMedia(Request $request, string $kind, ?string $key = null)
    {
        $seller = $this->resolveSeller($request);
        $draft = SellerProductDraft::query()
            ->where('seller_account_id', $seller->id)
            ->firstOrFail();

        $galleryPaths = array_values((array) ($draft->gallery_image_paths ?? []));
        $variantPaths = (array) ($draft->variant_image_paths ?? []);

        $path = match ($kind) {
            'cover' => $draft->cover_image_path,
            'gallery' => $galleryPaths[(int) ($key ?? -1)] ?? null,
            'variant' => $variantPaths[(string) ($key ?? '')] ?? null,
            default => null,
        };

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    public function store(Request $request, ProductComplianceService $scanner)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureCanSell($seller);

        $validated = $this->validateProduct($request);
        $flashSaleEndsAt = $this->normalizeFlashSaleEndsAt(
            $validated['flash_sale_ends_at'] ?? null,
            (float) ($validated['discount'] ?? 0)
        );
        $draft = $this->submittedDraft($request, $seller);

        $category = $validated['category'] === 'Others' && trim((string) ($validated['custom_category'] ?? '')) !== ''
            ? trim((string) $validated['custom_category'])
            : $validated['category'];

        $specifications = $this->normalizeSpecifications($validated['specifications'] ?? []);
        $hasVariants = $request->boolean('has_variants');
        $variants = $hasVariants
            ? $this->normalizeVariants($validated['variants'] ?? [])
            : [];

        $this->validateInventoryMode($hasVariants, $validated, $variants);

        [$parentPrice, $parentStock] = $this->resolveParentInventory(
            $hasVariants,
            $validated,
            $variants
        );

        $uploadedImage = $request->file('image');
        $draftCoverPath = $draft?->cover_image_path;
        $imageForScan = $uploadedImage ?: $draftCoverPath;

        $screeningDescription = $this->buildScreeningDescription(
            $validated['description'] ?? null,
            $validated['brand'] ?? null,
            $specifications,
            $variants
        );

        $scan = $scanner->scan(
            $validated['name'],
            $screeningDescription,
            $category,
            $imageForScan
        );

        $draftGalleryPaths = array_values((array) ($draft?->gallery_image_paths ?? []));
        $draftVariantPaths = (array) ($draft?->variant_image_paths ?? []);

        $product = DB::transaction(function () use (
            $seller,
            $validated,
            $category,
            $specifications,
            $hasVariants,
            $variants,
            $parentPrice,
            $parentStock,
            $flashSaleEndsAt,
            $uploadedImage,
            $draftCoverPath,
            $draftGalleryPaths,
            $draftVariantPaths,
            $request,
            $scan
        ) {
            $imagePath = $uploadedImage
                ? $uploadedImage->store('seller-products', 'public')
                : $this->promoteDraftFile($draftCoverPath, 'seller-products');

            $product = new SellerProduct();
            $product->forceFill([
                'seller_account_id' => $seller->id,
                'name' => $validated['name'],
                'category' => $category,
                'brand' => $validated['brand'] ?? null,
                'condition' => $validated['condition'] ?? null,
                'sku' => ($validated['sku'] ?? null) ?: 'SARI-' . Str::upper(Str::random(8)),
                'price' => $parentPrice,
                'stock' => $parentStock,
                'low_stock_threshold' => (int) ($validated['low_stock_threshold'] ?? 5),
                'discount' => $validated['discount'] ?? 0,
                'flash_sale_ends_at' => $flashSaleEndsAt,
                'free_shipping' => (bool) ($validated['free_shipping'] ?? false),
                'package_weight' => $validated['package_weight'] ?? null,
                'package_length' => $validated['package_length'] ?? null,
                'package_width' => $validated['package_width'] ?? null,
                'package_height' => $validated['package_height'] ?? null,
                'preparation_days' => $validated['preparation_days'] ?? null,
                'voucher_code' => $validated['voucher_code'] ?? null,
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'specifications' => $this->jsonColumnValue($product, 'specifications', $specifications),
                'has_variants' => $hasVariants,

                'moderation_status' => $scan['flagged'] ? 'flagged' : 'pending',
                'screening_risk' => $scan['risk'],
                'risk_score' => $scan['score'],
                'screening_engine' => $scan['engine'],
                'screening_reason' => $scan['reason'],
                'matched_terms' => $this->jsonColumnValue($product, 'matched_terms', $scan['matched_terms']),
                'ai_flagged' => $scan['ai_flagged'],
                'ai_categories' => $this->jsonColumnValue($product, 'ai_categories', $scan['ai_categories']),
                'ai_decision' => $scan['ai_decision'],
                'ai_policy_category' => $scan['ai_policy_category'],
                'ai_confidence' => $scan['ai_confidence'],
                'ai_reason' => $scan['ai_reason'],
                'ai_signals' => $this->jsonColumnValue($product, 'ai_signals', $scan['ai_signals']),
                'ai_image_reviewed' => $scan['ai_image_reviewed'],
                'ai_text_reviewed' => $scan['ai_text_reviewed'],
                'ai_status' => $scan['ai_status'],
                'ai_response_id' => $scan['ai_response_id'] ?: null,
                'screened_at' => now(),
                'requires_re_review' => false,
            ]);
            $product->save();

            $this->appendGalleryImages(
                $product,
                (array) $request->file('gallery_images', []),
                $draftGalleryPaths
            );

            if ($hasVariants) {
                $this->replaceVariants($product, $variants, $draftVariantPaths);
            }

            return $product->fresh(['galleryImages', 'activeVariants']);
        });

        $scanner->record($product, $scan, 'create');

        if ($draft) {
            $this->deleteDraftMedia($draft);
            $draft->delete();
        }

        $message = $scan['flagged']
            ? 'Product uploaded and automatically flagged by SARI screening for administrator review. No seller warning was issued automatically.'
            : ($scan['ai_status'] === 'completed'
                ? 'Product screened successfully and is pending administrator approval.'
                : 'Product uploaded and is pending administrator approval. AI inspection was unavailable, so it was not auto-approved.');

        return $this->actionSuccess(
            $request,
            $message,
            $scan['flagged'] ? 'warning' : 'success',
            route('seller.products.index')
        );
    }

    public function update(
        Request $request,
        SellerProduct $product,
        ProductComplianceService $scanner
    ) {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);
        $this->ensureCanSell($seller);

        if ($product->isArchived()) {
            return $this->actionError(
                $request,
                'product',
                'Restore this product from Archived Products before editing it.'
            );
        }

        $validated = $this->validateProduct($request);
        $category = $validated['category'] === 'Others' && trim((string) ($validated['custom_category'] ?? '')) !== ''
            ? trim((string) $validated['custom_category'])
            : $validated['category'];

        $flashSaleEndsAt = $request->exists('flash_sale_ends_at')
            ? $this->normalizeFlashSaleEndsAt(
                $validated['flash_sale_ends_at'] ?? null,
                (float) ($validated['discount'] ?? 0)
            )
            : $product->flash_sale_ends_at;

        if ((float) ($validated['discount'] ?? 0) <= 0) {
            $flashSaleEndsAt = null;
        }

        $currentSpecs = $this->decodeJsonArray($product->specifications);
        $currentVariants = $this->currentVariantRows($product);

        $brand = $request->exists('brand')
            ? ($validated['brand'] ?? null)
            : $product->brand;

        $specifications = $request->exists('specifications')
            ? $this->normalizeSpecifications($validated['specifications'] ?? [])
            : $currentSpecs;

        $hasVariants = $request->exists('has_variants')
            ? $request->boolean('has_variants')
            : (bool) $product->has_variants;

        $variantsPayloadProvided = $request->exists('variants');
        $variants = $hasVariants
            ? ($variantsPayloadProvided
                ? $this->normalizeVariants($validated['variants'] ?? [])
                : $currentVariants)
            : [];

        $this->validateInventoryMode($hasVariants, $validated, $variants, $product);

        [$parentPrice, $parentStock] = $this->resolveParentInventory(
            $hasVariants,
            $validated,
            $variants,
            $product
        );

        $newValues = [
            'name' => $validated['name'],
            'category' => $category,
            'brand' => $brand,
            'condition' => $validated['condition'] ?? $product->condition,
            'sku' => ($validated['sku'] ?? null) ?: $product->sku,
            'price' => $parentPrice,
            'stock' => $parentStock,
            'low_stock_threshold' => (int) ($validated['low_stock_threshold'] ?? $product->low_stock_threshold ?? 5),
            'discount' => $validated['discount'] ?? 0,
            'flash_sale_ends_at' => $flashSaleEndsAt,
            'free_shipping' => $request->boolean('free_shipping'),
            'package_weight' => $request->exists('package_weight') ? ($validated['package_weight'] ?? null) : $product->package_weight,
            'package_length' => $request->exists('package_length') ? ($validated['package_length'] ?? null) : $product->package_length,
            'package_width' => $request->exists('package_width') ? ($validated['package_width'] ?? null) : $product->package_width,
            'package_height' => $request->exists('package_height') ? ($validated['package_height'] ?? null) : $product->package_height,
            'preparation_days' => $request->exists('preparation_days') ? ($validated['preparation_days'] ?? null) : $product->preparation_days,
            'voucher_code' => $validated['voucher_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'specifications' => $this->jsonColumnValue($product, 'specifications', $specifications),
            'has_variants' => $hasVariants,
        ];

        $changedFields = $this->changedFields($product, $newValues);
        $imageChanged = $request->hasFile('image');
        $galleryChanged = $request->hasFile('gallery_images')
            || !empty($validated['remove_gallery_image_ids'] ?? []);
        $variantImagesChanged = $this->hasVariantImageUpload($request);

        $variantsChanged = $this->variantFingerprint($currentVariants, true)
            !== $this->variantFingerprint($variants, true);

        $variantOptionsChanged = $this->variantFingerprint($currentVariants, false)
            !== $this->variantFingerprint($variants, false);

        if ($variantsChanged) {
            $changedFields[] = 'variants';
        }
        if ($variantImagesChanged) {
            $changedFields[] = 'variant_images';
        }
        if ($galleryChanged) {
            $changedFields[] = 'gallery';
        }
        if ($imageChanged) {
            $changedFields[] = 'image';
        }

        $changedFields = array_values(array_unique($changedFields));

        if (empty($changedFields)) {
            return $this->actionSuccess(
                $request,
                'No product changes were detected.',
                'success',
                route('seller.products.index')
            );
        }

        $sensitiveFields = [
            'name',
            'category',
            'brand',
            'condition',
            'description',
            'specifications',
            'has_variants',
            'image',
            'gallery',
            'variant_images',
        ];

        $sensitiveChanged = !empty(array_intersect($changedFields, $sensitiveFields))
            || $variantOptionsChanged;

        $scan = null;

        if ($sensitiveChanged) {
            $imageForScan = $imageChanged
                ? $request->file('image')
                : $product->image_path;

            $screeningDescription = $this->buildScreeningDescription(
                $newValues['description'] ?? null,
                $brand,
                $specifications,
                $variants
            );

            $scan = $scanner->scan(
                $newValues['name'],
                $screeningDescription,
                $newValues['category'],
                $imageForScan
            );

            $newValues = array_merge($newValues, [
                'moderation_status' => $scan['flagged'] ? 'flagged' : 'pending',
                'screening_risk' => $scan['risk'],
                'risk_score' => $scan['score'],
                'screening_engine' => $scan['engine'],
                'screening_reason' => $scan['reason'],
                'matched_terms' => $this->jsonColumnValue($product, 'matched_terms', $scan['matched_terms']),
                'ai_flagged' => $scan['ai_flagged'],
                'ai_categories' => $this->jsonColumnValue($product, 'ai_categories', $scan['ai_categories']),
                'ai_decision' => $scan['ai_decision'],
                'ai_policy_category' => $scan['ai_policy_category'],
                'ai_confidence' => $scan['ai_confidence'],
                'ai_reason' => $scan['ai_reason'],
                'ai_signals' => $this->jsonColumnValue($product, 'ai_signals', $scan['ai_signals']),
                'ai_image_reviewed' => $scan['ai_image_reviewed'],
                'ai_text_reviewed' => $scan['ai_text_reviewed'],
                'ai_status' => $scan['ai_status'],
                'ai_response_id' => $scan['ai_response_id'] ?: null,
                'screened_at' => now(),
                'admin_review_note' => null,
                'reviewed_at' => null,
                'requires_re_review' => true,
                'last_sensitive_edit_at' => now(),
            ]);
        }

        DB::transaction(function () use (
            $request,
            $validated,
            $product,
            $newValues,
            $changedFields,
            $imageChanged,
            $galleryChanged,
            $hasVariants,
            $variants,
            $variantsChanged,
            $variantImagesChanged
        ) {
            $this->snapshotProduct($product, $changedFields);

            if ($imageChanged) {
                // Keep the old cover image because version history may reference it.
                $newValues['image_path'] = $request->file('image')->store('seller-products', 'public');
            }

            $product->forceFill($newValues);
            $product->save();

            if ($galleryChanged) {
                $this->updateGalleryImages(
                    $product,
                    (array) $request->file('gallery_images', []),
                    (array) ($validated['remove_gallery_image_ids'] ?? [])
                );
            }

            if ($variantsChanged
                || $variantImagesChanged
                || (bool) $product->getOriginal('has_variants') !== $hasVariants) {
                $this->replaceVariants($product, $hasVariants ? $variants : []);
            }
        });

        if ($sensitiveChanged && $scan) {
            $scanner->record($product->fresh(), $scan, 'sensitive_edit');
            $freshProduct = $product->fresh();

            $message = $freshProduct->moderation_status === 'flagged'
                ? 'Changes saved. SARI screening flagged the edited listing and administrator re-review is required.'
                : 'Changes saved. Sensitive listing details changed, so administrator re-review is required.';

            return $this->actionSuccess(
                $request,
                $message,
                $freshProduct->moderation_status === 'flagged' ? 'warning' : 'success',
                route('seller.products.index')
            );
        }

        return $this->actionSuccess(
            $request,
            'Product inventory details updated successfully.',
            'success',
            route('seller.products.index')
        );
    }

    public function archive(Request $request, SellerProduct $product)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);

        $product->update([
            'archived_at' => now(),
            'archive_reason' => 'archived',
        ]);

        return $this->actionSuccess(
            $request,
            'Product moved to Archived Products.',
            'success'
        );
    }

    public function destroy(Request $request, SellerProduct $product)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);

        /*
        |--------------------------------------------------------------------------
        | Permanent delete from Archived Products
        |--------------------------------------------------------------------------
        |
        | Preserve the existing Product Management delete behavior by default:
        | without `permanent=1`, this method still performs a recoverable delete
        | and simply moves the product into Archived Products.
        |
        | A true hard delete is accepted only when:
        | - the request explicitly sends permanent=1, and
        | - the product is already archived.
        |
        */
        if ($request->boolean('permanent')) {
            $this->ensureCanSell($seller);

            if (!$product->isArchived()) {
                return $this->actionError(
                    $request,
                    'product',
                    'Only products already inside Archived Products can be permanently deleted.'
                );
            }

            /*
            | Gather file paths BEFORE the transaction. We only delete physical
            | files after the database hard delete succeeds, so a blocked delete
            | cannot leave the database pointing at missing media.
            */
            $coverPath = $product->image_path;

            $galleryPaths = SellerProductImage::query()
                ->where('seller_product_id', $product->id)
                ->pluck('path')
                ->filter()
                ->values();

            $variantPaths = SellerProductVariant::query()
                ->where('seller_product_id', $product->id)
                ->pluck('image_path')
                ->filter()
                ->values();

            $versionPaths = SellerProductVersion::query()
                ->where('seller_product_id', $product->id)
                ->pluck('image_path')
                ->filter()
                ->values();

            try {
                DB::transaction(function () use ($product) {
                    /*
                    | Catalog-owned children are removed first. If another table
                    | protects this product through a foreign key (orders,
                    | compliance history, etc.), the final hard delete will fail
                    | and the whole transaction will roll back.
                    */
                    SellerProductImage::query()
                        ->where('seller_product_id', $product->id)
                        ->delete();

                    SellerProductVariant::query()
                        ->where('seller_product_id', $product->id)
                        ->delete();

                    SellerProductVersion::query()
                        ->where('seller_product_id', $product->id)
                        ->delete();

                    /*
                    | Direct table delete guarantees a real hard delete even if
                    | SoftDeletes is introduced on SellerProduct later.
                    */
                    $deleted = DB::table($product->getTable())
                        ->where($product->getKeyName(), $product->getKey())
                        ->delete();

                    if ($deleted !== 1) {
                        throw new \RuntimeException('Product record was not deleted.');
                    }
                });
            } catch (\Illuminate\Database\QueryException $exception) {
                report($exception);

                return $this->actionError(
                    $request,
                    'product',
                    'This product is linked to protected order, transaction, or compliance history, so it cannot be permanently deleted.',
                    409
                );
            } catch (\Throwable $exception) {
                report($exception);

                return $this->actionError(
                    $request,
                    'product',
                    'Unable to permanently delete this product right now.',
                    500
                );
            }

            /*
            | Database deletion succeeded. Physical media can now be removed.
            | Paths are unique, so deleting them here will not affect another
            | seller product.
            */
            collect([$coverPath])
                ->merge($galleryPaths)
                ->merge($variantPaths)
                ->merge($versionPaths)
                ->filter(fn ($path) => is_string($path) && trim($path) !== '')
                ->unique()
                ->each(fn ($path) => $this->deletePublicFile($path));

            return $this->actionSuccess(
                $request,
                'Product permanently deleted.',
                'success'
            );
        }

        /*
        | Existing recoverable delete behavior — preserved exactly.
        */
        $product->update([
            'archived_at' => now(),
            'archive_reason' => 'deleted',
        ]);

        return $this->actionSuccess(
            $request,
            'Product removed from the active catalog and moved to Archived Products.',
            'success'
        );
    }


    public function library(Request $request)
    {
        $seller = $this->resolveSeller($request);
        $payload = $this->productLibraryPayload($seller);

        return response()
            ->json([
                'success' => true,
                'server_now' => now()->toIso8601String(),
                'count' => $payload->count(),
                'products' => $payload->values(),
            ])
            ->header('Cache-Control', 'private, max-age=5, stale-while-revalidate=20');
    }

    /**
     * Return the exact Inventory Overview / Catalog Health counters with one
     * lightweight aggregate query. Keeping these values in the initial HTML
     * removes the client-side placeholder flash without loading full products.
     */
    private function catalogInventorySummary(SellerAccount $seller): array
    {
        $row = $seller->products()
            ->whereNull('archived_at')
            ->selectRaw(
                "COUNT(*) AS total,
                SUM(CASE WHEN moderation_status = 'approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN moderation_status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN stock > 0 AND stock <= COALESCE(low_stock_threshold, 5) THEN 1 ELSE 0 END) AS low,
                SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) AS out_count,
                SUM(CASE WHEN moderation_status = 'approved' AND stock > 0 THEN 1 ELSE 0 END) AS ready,
                SUM(CASE WHEN moderation_status <> 'approved' OR (stock > 0 AND stock <= COALESCE(low_stock_threshold, 5)) THEN 1 ELSE 0 END) AS attention"
            )
            ->first();

        return [
            'total' => (int) ($row?->total ?? 0),
            'approved' => (int) ($row?->approved ?? 0),
            'pending' => (int) ($row?->pending ?? 0),
            'low' => (int) ($row?->low ?? 0),
            'out' => (int) ($row?->out_count ?? 0),
            'ready' => (int) ($row?->ready ?? 0),
            'attention' => (int) ($row?->attention ?? 0),
        ];
    }


    /**
     * Build the Product Management payload without loading every review row.
     *
     * Reviews are reduced to SQL COUNT/AVG aggregates and only the columns
     * needed by Product Management are selected. The optional limit is used
     * by index() for an immediate first-screen render.
     */
    private function productLibraryPayload(
        SellerAccount $seller,
        ?int $limit = null
    ) {
        $query = $seller->products()
            ->whereNull('archived_at')
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
                'low_stock_threshold',
                'discount',
                'flash_sale_ends_at',
                'free_shipping',
                'package_weight',
                'package_length',
                'package_width',
                'package_height',
                'preparation_days',
                'voucher_code',
                'description',
                'image_path',
                'moderation_status',
                'specifications',
                'has_variants',
                'created_at',
                'updated_at',
            ])
            ->with([
                'activeVariants:id,seller_product_id,sku,option_values,price,stock,image_path,is_active',
                'galleryImages:id,seller_product_id,path,sort_order,alt_text',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest('id');

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (SellerProduct $product) {
                $specifications = $this->decodeJsonArray(
                    $product->specifications ?? []
                );

                $variants = $product->activeVariants
                    ->map(function (SellerProductVariant $variant) {
                        return [
                            'id' => (int) $variant->id,
                            'sku' => $variant->sku,
                            'options' => is_array($variant->option_values)
                                ? $variant->option_values
                                : $this->decodeJsonArray($variant->option_values),
                            'price' => (float) $variant->price,
                            'stock' => (int) $variant->stock,
                            'image_url' => $variant->image_path
                                ? route('seller.products.variant-image', $variant)
                                : null,
                        ];
                    })
                    ->values();

                $statusLabel = match ($product->moderation_status) {
                    'approved' => 'Approved',
                    'pending' => 'Pending',
                    'flagged' => 'Flagged',
                    'rejected' => 'Rejected',
                    'removed' => 'Removed',
                    default => ucfirst((string) $product->moderation_status),
                };

                $stock = (int) $product->stock;
                $threshold = max(
                    0,
                    (int) ($product->low_stock_threshold ?? 5)
                );

                $effectiveDiscount = $this->effectiveDiscountPercent($product);
                $ratingCount = (int) ($product->reviews_count ?? 0);
                $ratingAverage = $ratingCount > 0
                    ? round((float) ($product->reviews_avg_rating ?? 0), 1)
                    : 0.0;

                return [
                    'id' => (int) $product->id,
                    'name' => (string) $product->name,
                    'category' => (string) $product->category,
                    'brand' => $product->brand,
                    'condition' => $product->condition,
                    'sku' => $product->sku,
                    'price' => (float) $product->price,
                    'stock' => $stock,
                    'low_stock_threshold' => $threshold,
                    'discount' => (float) ($product->discount ?? 0),
                    'effective_discount' => $effectiveDiscount,
                    'sale_price' => round(
                        $effectiveDiscount > 0
                            ? (float) $product->price * (1 - $effectiveDiscount / 100)
                            : (float) $product->price,
                        2
                    ),
                    'flash_sale_active' => $this->isFlashSaleActive($product),
                    'flash_sale_ends_at' => $product->flash_sale_ends_at?->toIso8601String(),
                    'free_shipping' => (bool) ($product->free_shipping ?? false),
                    'package_weight' => $product->package_weight !== null
                        ? (float) $product->package_weight
                        : null,
                    'package_length' => $product->package_length !== null
                        ? (float) $product->package_length
                        : null,
                    'package_width' => $product->package_width !== null
                        ? (float) $product->package_width
                        : null,
                    'package_height' => $product->package_height !== null
                        ? (float) $product->package_height
                        : null,
                    'preparation_days' => $product->preparation_days !== null
                        ? (int) $product->preparation_days
                        : null,
                    'rating' => $ratingAverage,
                    'rating_count' => $ratingCount,
                    'on_trend' => $product->moderation_status === 'approved'
                        && $ratingCount >= 3
                        && $ratingAverage >= 4.5,
                    'mall_badge' => $product->moderation_status === 'approved',
                    'voucher_code' => $product->voucher_code,
                    'description' => $product->description,
                    'moderation_status' => (string) $product->moderation_status,
                    'status_label' => $statusLabel,
                    'stock_state' => $stock <= 0
                        ? 'out-of-stock'
                        : ($stock <= $threshold ? 'low-stock' : 'in-stock'),
                    'has_variants' => (bool) $product->has_variants,
                    'specifications' => array_values($specifications),
                    'variants' => $variants,
                    'image_url' => $product->image_path
                        ? route('seller.products.image', $product)
                        : ($product->galleryImages->first()
                            ? route(
                                'seller.products.gallery-image',
                                $product->galleryImages->first()
                            )
                            : null),
                    'gallery' => $product->galleryImages
                        ->map(fn (SellerProductImage $image) => [
                            'id' => (int) $image->id,
                            'url' => route(
                                'seller.products.gallery-image',
                                $image
                            ),
                            'sort_order' => (int) $image->sort_order,
                        ])
                        ->values(),
                    'created_at_human' => $product->created_at?->diffForHumans(),
                    'updated_at' => $product->updated_at?->toIso8601String(),
                ];
            });
    }

    public function archived(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $products = $seller->products()
            ->whereNotNull('archived_at')
            ->with('latestVersion')
            ->latest('archived_at')
            ->get([
                'id',
                'seller_account_id',
                'name',
                'category',
                'brand',
                'sku',
                'price',
                'stock',
                'image_path',
                'moderation_status',
                'archived_at',
                'archive_reason',
            ]);

        return view('seller.archived-products', compact('seller', 'products'));
    }

    public function restore(Request $request, SellerProduct $product)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);
        $this->ensureCanSell($seller);

        $product->update([
            'archived_at' => null,
            'archive_reason' => null,
        ]);

        return $this->actionSuccess(
            $request,
            'Product restored to Product Management.',
            'success'
        );
    }

    public function versionImage(Request $request, SellerProductVersion $version)
    {
        $product = $version->product;
        $allowed = $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $sellerId = (int) $request->session()->get('seller_account_id');
            $allowed = $sellerId === (int) $product->seller_account_id;
        }

        abort_unless($allowed, 403);
        return $this->cachedPublicImageResponse($version->image_path);
    }

    public function image(Request $request, SellerProduct $product)
    {
        $allowed = $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $sellerId = (int) $request->session()->get('seller_account_id');
            $allowed = $sellerId === (int) $product->seller_account_id;
        }

        abort_unless($allowed, 403);
        return $this->cachedPublicImageResponse($product->image_path);
    }

    public function galleryImage(Request $request, SellerProductImage $image)
    {
        $product = $image->product;
        $allowed = $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $allowed = (int) $request->session()->get('seller_account_id')
                === (int) $product->seller_account_id;
        }

        abort_unless($allowed, 403);
        return $this->cachedPublicImageResponse($image->path);
    }

    public function variantImage(Request $request, SellerProductVariant $variant)
    {
        $product = $variant->product;
        $allowed = $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $allowed = (int) $request->session()->get('seller_account_id')
                === (int) $product->seller_account_id;
        }

        abort_unless($allowed, 403);
        return $this->cachedPublicImageResponse($variant->image_path);
    }

    /**
     * Product image bytes are immutable because uploads receive unique paths.
     * Let the browser reuse them instead of re-requesting PHP on every visit.
     */
    private function cachedPublicImageResponse(?string $path)
    {
        abort_unless(
            $path && Storage::disk('public')->exists($path),
            404
        );

        return Storage::disk('public')->response(
            $path,
            null,
            [
                'Cache-Control' => 'private, max-age=86400, immutable',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'draft_id' => ['nullable', 'integer', 'exists:seller_product_drafts,id'],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'custom_category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:120'],
            'condition' => ['nullable', 'in:new,like_new,used'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'flash_sale_ends_at' => ['nullable', 'date'],
            'free_shipping' => ['nullable', 'boolean'],
            'package_weight' => ['nullable', 'numeric', 'min:0', 'max:999999.999'],
            'package_length' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'package_width' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'package_height' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'preparation_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'voucher_code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_gallery_image_ids' => ['nullable', 'array'],
            'remove_gallery_image_ids.*' => ['integer', 'exists:seller_product_images,id'],
            'has_variants' => ['nullable', 'boolean'],

            'specifications' => ['nullable', 'array', 'max:30'],
            'specifications.*.name' => ['nullable', 'string', 'max:80'],
            'specifications.*.value' => ['nullable', 'string', 'max:255'],
            'specifications.*.unit' => ['nullable', 'string', 'max:30'],

            'variants' => ['nullable', 'array', 'max:100'],
            'variants.*.options' => ['nullable', 'string', 'max:3000'],
            'variants.*.sku' => ['nullable', 'string', 'max:120'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function validateDraft(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'custom_category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:120'],
            'condition' => ['nullable', 'in:new,like_new,used'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'flash_sale_ends_at' => ['nullable', 'date'],
            'free_shipping' => ['nullable', 'boolean'],
            'package_weight' => ['nullable', 'numeric', 'min:0', 'max:999999.999'],
            'package_length' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'package_width' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'package_height' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'preparation_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'voucher_code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'has_variants' => ['nullable', 'boolean'],
            'specifications' => ['nullable', 'array', 'max:30'],
            'specifications.*.name' => ['nullable', 'string', 'max:80'],
            'specifications.*.value' => ['nullable', 'string', 'max:255'],
            'specifications.*.unit' => ['nullable', 'string', 'max:30'],
            'variants' => ['nullable', 'array', 'max:100'],
            'variants.*.options' => ['nullable', 'string', 'max:3000'],
            'variants.*.sku' => ['nullable', 'string', 'max:120'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function validateInventoryMode(
        bool $hasVariants,
        array $validated,
        array $variants,
        ?SellerProduct $existing = null
    ): void {
        if (!$hasVariants) {
            $price = $validated['price'] ?? $existing?->price;
            $stock = $validated['stock'] ?? $existing?->stock;

            if ($price === null || $stock === null) {
                throw ValidationException::withMessages([
                    'product' => 'Simple products require a price and stock quantity.',
                ]);
            }

            return;
        }

        if (empty($variants)) {
            throw ValidationException::withMessages([
                'product' => 'Variant products require at least one generated variant.',
            ]);
        }

        foreach ($variants as $index => $variant) {
            if (!isset($variant['price']) || $variant['price'] === '' || !isset($variant['stock']) || $variant['stock'] === '') {
                throw ValidationException::withMessages([
                    'product' => 'Every variant needs its own price and stock quantity. Check variant row ' . ($index + 1) . '.',
                ]);
            }

            if (empty($variant['option_values'])) {
                throw ValidationException::withMessages([
                    'product' => 'Every variant must contain at least one option value.',
                ]);
            }
        }
    }

    private function resolveParentInventory(
        bool $hasVariants,
        array $validated,
        array $variants,
        ?SellerProduct $existing = null
    ): array {
        if (!$hasVariants) {
            return [
                (float) ($validated['price'] ?? $existing?->price ?? 0),
                (int) ($validated['stock'] ?? $existing?->stock ?? 0),
            ];
        }

        $prices = array_map(fn (array $variant) => (float) $variant['price'], $variants);
        $stock = array_sum(array_map(fn (array $variant) => (int) $variant['stock'], $variants));

        return [min($prices), $stock];
    }

    private function normalizeSpecifications(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $value = trim((string) ($item['value'] ?? ''));
            $unit = trim((string) ($item['unit'] ?? ''));

            if ($name === '' || $value === '') {
                continue;
            }

            $result[] = [
                'name' => Str::limit($name, 80, ''),
                'value' => Str::limit($value, 255, ''),
                'unit' => $unit !== '' ? Str::limit($unit, 30, '') : null,
            ];
        }

        return array_values($result);
    }

    private function normalizeVariants(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $options = json_decode((string) ($item['options'] ?? ''), true);

            if (!is_array($options)) {
                $options = [];
            }

            $cleanOptions = [];
            foreach ($options as $name => $value) {
                $name = trim((string) $name);
                $value = trim((string) $value);

                if ($name !== '' && $value !== '') {
                    $cleanOptions[Str::limit($name, 80, '')] = Str::limit($value, 120, '');
                }
            }

            if (empty($cleanOptions)) {
                continue;
            }

            $result[] = [
                'option_values' => $cleanOptions,
                'sku' => trim((string) ($item['sku'] ?? '')) ?: null,
                'price' => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
                'stock' => isset($item['stock']) && $item['stock'] !== '' ? (int) $item['stock'] : null,
                'image_file' => ($item['image'] ?? null) instanceof UploadedFile ? $item['image'] : null,
                'image_path' => $item['image_path'] ?? null,
            ];
        }

        return array_values($result);
    }

    private function replaceVariants(
        SellerProduct $product,
        array $variants,
        array $draftVariantPaths = []
    ): void {
        $existing = SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->get();

        $existingByKey = [];
        foreach ($existing as $row) {
            $existingByKey[$this->variantKey((array) $row->option_values)] = $row;
        }

        $keepPaths = [];
        $rows = [];

        foreach ($variants as $variant) {
            $key = $this->variantKey($variant['option_values'] ?? []);
            $old = $existingByKey[$key] ?? null;
            $imagePath = $variant['image_path'] ?? $old?->image_path;

            if (($variant['image_file'] ?? null) instanceof UploadedFile) {
                $imagePath = $variant['image_file']->store(
                    'seller-products/' . $product->id . '/variants',
                    'public'
                );
            } elseif (!empty($draftVariantPaths[$key])) {
                $imagePath = $this->promoteDraftFile(
                    $draftVariantPaths[$key],
                    'seller-products/' . $product->id . '/variants'
                );
            }

            if ($imagePath) {
                $keepPaths[] = $imagePath;
            }

            $rows[] = [
                'seller_product_id' => $product->id,
                'sku' => $variant['sku'] ?: 'SARI-V-' . Str::upper(Str::random(8)),
                'option_values' => $variant['option_values'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'image_path' => $imagePath,
                'is_active' => true,
            ];
        }

        SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->delete();

        foreach ($rows as $row) {
            SellerProductVariant::create($row);
        }

        foreach ($existing as $old) {
            if ($old->image_path && !in_array($old->image_path, $keepPaths, true)) {
                $this->deletePublicFile($old->image_path);
            }
        }
    }

    private function currentVariantRows(SellerProduct $product): array
    {
        return SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (SellerProductVariant $variant) => [
                'option_values' => is_array($variant->option_values)
                    ? $variant->option_values
                    : $this->decodeJsonArray($variant->option_values),
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'stock' => (int) $variant->stock,
                'image_path' => $variant->image_path,
            ])
            ->values()
            ->all();
    }

    private function buildScreeningDescription(
        ?string $description,
        ?string $brand,
        array $specifications,
        array $variants
    ): string {
        $parts = [];

        if (trim((string) $description) !== '') {
            $parts[] = 'Description: ' . trim((string) $description);
        }

        if (trim((string) $brand) !== '') {
            $parts[] = 'Brand: ' . trim((string) $brand);
        }

        foreach ($specifications as $spec) {
            $parts[] = 'Specification ' . $spec['name'] . ': ' . $spec['value'] . ($spec['unit'] ? ' ' . $spec['unit'] : '');
        }

        foreach ($variants as $variant) {
            $optionText = collect($variant['option_values'] ?? [])
                ->map(fn ($value, $name) => $name . ': ' . $value)
                ->implode(', ');

            if ($optionText !== '') {
                $parts[] = 'Variant: ' . $optionText;
            }
        }

        return implode("\n", $parts);
    }

    private function variantFingerprint(array $variants, bool $includeInventory): string
    {
        $normalized = array_map(function (array $variant) use ($includeInventory) {
            $options = $variant['option_values'] ?? [];
            ksort($options);

            $row = ['options' => $options];

            if ($includeInventory) {
                $row['sku'] = trim((string) ($variant['sku'] ?? ''));
                $row['price'] = number_format((float) ($variant['price'] ?? 0), 2, '.', '');
                $row['stock'] = (int) ($variant['stock'] ?? 0);
            }

            return $row;
        }, $variants);

        usort($normalized, fn ($a, $b) => strcmp(json_encode($a), json_encode($b)));

        return hash('sha256', json_encode($normalized));
    }

    private function changedFields(SellerProduct $product, array $newValues): array
    {
        $changed = [];

        foreach ($newValues as $field => $newValue) {
            $oldValue = $product->{$field};

            if (in_array($field, ['price', 'discount', 'package_weight', 'package_length', 'package_width', 'package_height'], true)) {
                $isDifferent = (float) $oldValue !== (float) $newValue;
            } elseif ($field === 'flash_sale_ends_at') {
                $oldTimestamp = $oldValue ? Carbon::parse($oldValue)->getTimestamp() : null;
                $newTimestamp = $newValue ? Carbon::parse($newValue)->getTimestamp() : null;
                $isDifferent = $oldTimestamp !== $newTimestamp;
            } elseif (in_array($field, ['stock', 'low_stock_threshold', 'preparation_days'], true)) {
                $isDifferent = (int) $oldValue !== (int) $newValue;
            } elseif (in_array($field, ['has_variants', 'free_shipping'], true)) {
                $isDifferent = (bool) $oldValue !== (bool) $newValue;
            } elseif ($field === 'specifications') {
                $isDifferent = $this->canonicalJson($oldValue) !== $this->canonicalJson($newValue);
            } else {
                $isDifferent = trim((string) ($oldValue ?? '')) !== trim((string) ($newValue ?? ''));
            }

            if ($isDifferent) {
                $changed[] = $field;
            }
        }

        return $changed;
    }

    private function snapshotProduct(SellerProduct $product, array $changedFields): void
    {
        $nextVersion = ((int) $product->versions()->max('version_number')) + 1;
        $variantSnapshot = $this->currentVariantRows($product);
        $gallerySnapshot = $product->galleryImages()
            ->get()
            ->map(fn (SellerProductImage $image) => [
                'path' => $image->path,
                'sort_order' => (int) $image->sort_order,
                'alt_text' => $image->alt_text,
            ])
            ->values()
            ->all();

        $snapshot = new SellerProductVersion();
        $snapshot->forceFill([
            'seller_product_id' => $product->id,
            'version_number' => $nextVersion,
            'name' => $product->name,
            'category' => $product->category,
            'brand' => $product->brand,
            'condition' => $product->condition,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'low_stock_threshold' => (int) ($product->low_stock_threshold ?? 5),
            'discount' => $product->discount,
            'flash_sale_ends_at' => $product->flash_sale_ends_at,
            'free_shipping' => (bool) ($product->free_shipping ?? false),
            'package_weight' => $product->package_weight,
            'package_length' => $product->package_length,
            'package_width' => $product->package_width,
            'package_height' => $product->package_height,
            'preparation_days' => $product->preparation_days,
            'voucher_code' => $product->voucher_code,
            'description' => $product->description,
            'specifications' => $this->decodeJsonArray($product->specifications),
            'has_variants' => (bool) $product->has_variants,
            'variants_snapshot' => $variantSnapshot,
            'gallery_snapshot' => $gallerySnapshot,
            'image_path' => $product->image_path,
            'moderation_status' => $product->moderation_status,
            'screening_risk' => $product->screening_risk,
            'screening_reason' => $product->screening_reason,
            'matched_terms' => $product->matched_terms,
            'changed_fields' => $changedFields,
            'snapshot_reason' => 'seller_edit',
        ]);
        $snapshot->save();
    }

    private function appendGalleryImages(
        SellerProduct $product,
        array $uploadedFiles = [],
        array $draftPaths = []
    ): void {
        $paths = [];

        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $file->store(
                    'seller-products/' . $product->id . '/gallery',
                    'public'
                );
            }
        }

        if (!$paths) {
            foreach ($draftPaths as $draftPath) {
                $promoted = $this->promoteDraftFile(
                    $draftPath,
                    'seller-products/' . $product->id . '/gallery'
                );
                if ($promoted) {
                    $paths[] = $promoted;
                }
            }
        }

        foreach ($paths as $index => $path) {
            SellerProductImage::create([
                'seller_product_id' => $product->id,
                'path' => $path,
                'sort_order' => $index,
                'alt_text' => $product->name,
            ]);
        }
    }

    private function updateGalleryImages(
        SellerProduct $product,
        array $uploadedFiles,
        array $removeIds
    ): void {
        if ($removeIds) {
            $images = SellerProductImage::query()
                ->where('seller_product_id', $product->id)
                ->whereIn('id', array_map('intval', $removeIds))
                ->get();

            foreach ($images as $image) {
                $this->deletePublicFile($image->path);
                $image->delete();
            }
        }

        foreach ($uploadedFiles as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store(
                'seller-products/' . $product->id . '/gallery',
                'public'
            );

            SellerProductImage::create([
                'seller_product_id' => $product->id,
                'path' => $path,
                'sort_order' => $product->galleryImages()->max('sort_order') + 1,
                'alt_text' => $product->name,
            ]);
        }
    }

    private function hasVariantImageUpload(Request $request): bool
    {
        foreach ((array) $request->file('variants', []) as $row) {
            if (is_array($row) && ($row['image'] ?? null) instanceof UploadedFile) {
                return true;
            }
        }

        return false;
    }

    private function variantKey(array $options): string
    {
        if (!$options) {
            return '';
        }

        ksort($options);
        return sha1(json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function promoteDraftFile(?string $path, string $targetDirectory): ?string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $destination = trim($targetDirectory, '/') . '/' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->copy($path, $destination);
        return $destination;
    }

    private function submittedDraft(Request $request, SellerAccount $seller): ?SellerProductDraft
    {
        $draftId = (int) $request->input('draft_id', 0);
        if ($draftId <= 0) {
            return null;
        }

        $draft = SellerProductDraft::query()->find($draftId);
        abort_unless($draft && (int) $draft->seller_account_id === (int) $seller->id, 403);

        return $draft;
    }

    private function draftPayload(SellerProductDraft $draft): array
    {
        $gallery = array_values((array) ($draft->gallery_image_paths ?? []));
        $variants = (array) ($draft->variant_image_paths ?? []);

        return [
            'id' => (int) $draft->id,
            'payload' => (array) ($draft->payload ?? []),
            'saved_at' => $draft->updated_at?->toIso8601String(),
            'cover_image_url' => $draft->cover_image_path
                ? route('seller.products.draft-media', ['kind' => 'cover', 'key' => 'cover'])
                : null,
            'gallery_images' => collect($gallery)->map(fn ($path, $index) => [
                'index' => $index,
                'url' => route('seller.products.draft-media', ['kind' => 'gallery', 'key' => $index]),
            ])->values()->all(),
            'variant_images' => collect($variants)->map(fn ($path, $key) => [
                'key' => $key,
                'url' => route('seller.products.draft-media', ['kind' => 'variant', 'key' => $key]),
            ])->values()->all(),
        ];
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

        /*
        | Middleware-protected Seller routes already resolved this model.
        | Reusing it avoids another SellerAccount SELECT + status refresh.
        */
        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        /*
        | Fallback for routes that intentionally do not use Seller middleware.
        */
        $seller->refreshSuspensionStatus();

        if (!$seller->realtime_token) {
            $seller->ensureRealtimeToken();
        }

        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }

    private function ensureOwnership(SellerAccount $seller, SellerProduct $product): void
    {
        abort_unless((int) $product->seller_account_id === (int) $seller->id, 403);
    }

    private function normalizeFlashSaleEndsAt(mixed $value, float $discountPercent): ?Carbon
    {
        $raw = trim((string) ($value ?? ''));

        if ($raw === '') {
            return null;
        }

        if ($discountPercent <= 0) {
            return null;
        }

        return Carbon::parse($raw)->utc();
    }

    private function isFlashSaleActive(SellerProduct $product): bool
    {
        return $product->moderation_status === 'approved'
            && (float) ($product->discount ?? 0) > 0
            && $product->flash_sale_ends_at !== null
            && now()->lt($product->flash_sale_ends_at);
    }

    private function effectiveDiscountPercent(SellerProduct $product): float
    {
        $discount = min(100, max(0, (float) ($product->discount ?? 0)));

        if ($discount <= 0) {
            return 0.0;
        }

        if (
            $product->moderation_status === 'approved'
            && $product->flash_sale_ends_at !== null
            && now()->gte($product->flash_sale_ends_at)
        ) {
            return 0.0;
        }

        return $discount;
    }

    private function ensureCanSell(SellerAccount $seller): void
    {
        if ($seller->isSuspended()) {
            throw ValidationException::withMessages([
                'product' => 'Your seller account is suspended until ' .
                    $seller->suspended_until?->format('M d, Y h:i A') .
                    '. Selling actions are temporarily disabled.',
            ]);
        }
    }

    private function jsonColumnValue(SellerProduct $product, string $field, array $values): array|string
    {
        if ($product->hasCast($field, ['array', 'json', 'object', 'collection'])) {
            return array_values($values);
        }

        return json_encode(array_values($values), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }

    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function canonicalJson(mixed $value): string
    {
        $decoded = $this->decodeJsonArray($value);
        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }

    /**
     * Return JSON to the persistent Seller shell, while preserving the old
     * redirect + flash behavior for normal non-AJAX requests.
     */
    private function actionSuccess(
        Request $request,
        string $message,
        string $type = 'success',
        ?string $redirectUrl = null
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'type' => $type,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $redirect = $redirectUrl
            ? redirect()->to($redirectUrl)
            : back();

        return $redirect->with($type, $message);
    }

    private function actionError(
        Request $request,
        string $key,
        string $message,
        int $status = 422
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    $key => [$message],
                ],
            ], $status);
        }

        return back()->withErrors([
            $key => $message,
        ]);
    }

}
