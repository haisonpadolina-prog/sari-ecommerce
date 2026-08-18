<?php

namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Models\SellerProductVersion;
use App\Services\ProductComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerProductController extends Controller
{
    public function store(Request $request, ProductComplianceService $scanner)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureCanSell($seller);

        $validated = $this->validateProduct($request);

        $scan = $scanner->scan(
            $validated['name'],
            $validated['description'] ?? null,
            $validated['category']
        );

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('seller-products', 'public')
            : null;

        SellerProduct::create([
            'seller_account_id' => $seller->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'sku' => ($validated['sku'] ?? null) ?: 'SARI-' . Str::upper(Str::random(8)),
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'discount' => $validated['discount'] ?? 0,
            'voucher_code' => $validated['voucher_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'moderation_status' => $scan['flagged'] ? 'flagged' : 'pending',
            'screening_risk' => $scan['risk'],
            'screening_reason' => $scan['reason'],
            'matched_terms' => $scan['matched_terms'],
            'requires_re_review' => false,
        ]);

        return redirect()
            ->route('seller.dashboard')
            ->with(
                $scan['flagged'] ? 'warning' : 'success',
                $scan['flagged']
                    ? 'Product uploaded but was flagged for administrator review. No seller warning was issued automatically.'
                    : 'Product uploaded successfully and is pending administrator review.'
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
            return back()->withErrors([
                'product' => 'Restore this product from Archived Products before editing it.',
            ]);
        }

        $validated = $this->validateProduct($request);

        $newValues = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'sku' => ($validated['sku'] ?? null) ?: $product->sku,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'discount' => $validated['discount'] ?? 0,
            'voucher_code' => $validated['voucher_code'] ?? null,
            'description' => $validated['description'] ?? null,
        ];

        $changedFields = $this->changedFields($product, $newValues);
        $imageChanged = $request->hasFile('image');

        if ($imageChanged) {
            $changedFields[] = 'image';
        }

        $changedFields = array_values(array_unique($changedFields));

        if (empty($changedFields)) {
            return back()->with('success', 'No product changes were detected.');
        }

        $this->snapshotProduct($product, $changedFields);

        if ($imageChanged) {
            // Keep the previous image file because product version history may reference it.
            $newValues['image_path'] = $request->file('image')->store('seller-products', 'public');
        }

        $sensitiveFields = ['name', 'category', 'description', 'image'];
        $sensitiveChanged = !empty(array_intersect($changedFields, $sensitiveFields));

        if ($sensitiveChanged) {
            $scan = $scanner->scan(
                $newValues['name'],
                $newValues['description'] ?? null,
                $newValues['category']
            );

            $newValues = array_merge($newValues, [
                'moderation_status' => $scan['flagged'] ? 'flagged' : 'pending',
                'screening_risk' => $scan['risk'],
                'screening_reason' => $scan['reason'],
                'matched_terms' => $scan['matched_terms'],
                'admin_review_note' => null,
                'reviewed_at' => null,
                'requires_re_review' => true,
                'last_sensitive_edit_at' => now(),
            ]);
        }

        $product->update($newValues);

        if ($sensitiveChanged) {
            return redirect()
                ->route('seller.dashboard')
                ->with(
                    $product->fresh()->moderation_status === 'flagged' ? 'warning' : 'success',
                    $product->fresh()->moderation_status === 'flagged'
                        ? 'Changes saved. The edited listing was flagged and requires administrator review.'
                        : 'Changes saved. Because listing details changed, the product is pending administrator re-review.'
                );
        }

        return redirect()
            ->route('seller.dashboard')
            ->with('success', 'Product inventory details updated successfully.');
    }

    public function archive(Request $request, SellerProduct $product)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);

        $product->update([
            'archived_at' => now(),
            'archive_reason' => 'archived',
        ]);

        return back()->with('success', 'Product moved to Archived Products.');
    }

    public function destroy(Request $request, SellerProduct $product)
    {
        $seller = $this->resolveSeller($request);
        $this->ensureOwnership($seller, $product);

        // Intentional recoverable delete: preserve compliance evidence and history.
        $product->update([
            'archived_at' => now(),
            'archive_reason' => 'deleted',
        ]);

        return back()->with('success', 'Product removed from the active dashboard and moved to Archived Products.');
    }

    public function archived(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $products = $seller->products()
            ->whereNotNull('archived_at')
            ->with('latestVersion')
            ->latest('archived_at')
            ->get();

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

        return back()->with('success', 'Product restored to your dashboard.');
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
        abort_unless($version->image_path, 404);
        abort_unless(Storage::disk('public')->exists($version->image_path), 404);

        return Storage::disk('public')->response($version->image_path);
    }

    public function image(Request $request, SellerProduct $product)
    {
        $allowed = $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $sellerId = (int) $request->session()->get('seller_account_id');
            $allowed = $sellerId === (int) $product->seller_account_id;
        }

        abort_unless($allowed, 403);
        abort_unless($product->image_path, 404);
        abort_unless(Storage::disk('public')->exists($product->image_path), 404);

        return Storage::disk('public')->response($product->image_path);
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'voucher_code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=800,min_height=800'],
        ], [
            'image.dimensions' => 'For a clear product photo, please upload an image that is at least 800 × 800 pixels.',
            'image.max' => 'Product images must be 4 MB or smaller.',
        ]);
    }

    private function changedFields(SellerProduct $product, array $newValues): array
    {
        $changed = [];

        foreach ($newValues as $field => $newValue) {
            $oldValue = $product->{$field};

            if (in_array($field, ['price', 'discount'], true)) {
                $isDifferent = (float) $oldValue !== (float) $newValue;
            } elseif ($field === 'stock') {
                $isDifferent = (int) $oldValue !== (int) $newValue;
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

        SellerProductVersion::create([
            'seller_product_id' => $product->id,
            'version_number' => $nextVersion,
            'name' => $product->name,
            'category' => $product->category,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'discount' => $product->discount,
            'voucher_code' => $product->voucher_code,
            'description' => $product->description,
            'image_path' => $product->image_path,
            'moderation_status' => $product->moderation_status,
            'screening_risk' => $product->screening_risk,
            'screening_reason' => $product->screening_reason,
            'matched_terms' => $product->matched_terms,
            'changed_fields' => $changedFields,
            'snapshot_reason' => 'seller_edit',
        ]);
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        if (!$request->session()->get('is_seller')) {
            abort(403, 'Seller session required.');
        }

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        $seller->refreshSuspensionStatus();
        $seller->ensureRealtimeToken();

        return $seller;
    }

    private function ensureOwnership(SellerAccount $seller, SellerProduct $product): void
    {
        abort_unless((int) $product->seller_account_id === (int) $seller->id, 403);
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
}
