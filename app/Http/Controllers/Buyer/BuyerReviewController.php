<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Catalog\ProductReview;
use App\Services\Buyer\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class BuyerReviewController extends Controller
{
    private const MAX_REVIEW_IMAGES = 5;

    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function store(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        $this->guardOwned($request, $order);

        if ($order->status !== 'delivered') {
            throw ValidationException::withMessages([
                'review' => 'Reviews can only be submitted after delivery.',
            ]);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:seller_products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1500'],
            'images' => ['nullable', 'array', 'max:' . self::MAX_REVIEW_IMAGES],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array', 'max:' . self::MAX_REVIEW_IMAGES],
            'remove_images.*' => ['string', 'max:500'],
        ]);

        $productId = (int) $validated['product_id'];

        $belongsToOrder = collect((array) $order->items)
            ->contains(fn ($item) => (int) ($item['product_id'] ?? 0) === $productId);

        if (!$belongsToOrder) {
            abort(422, 'That product does not belong to this order.');
        }

        $existingReview = ProductReview::query()
            ->where('marketplace_order_id', $order->id)
            ->where('seller_product_id', $productId)
            ->first();

        $existingPaths = collect((array) ($existingReview?->image_paths ?? []))
            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
            ->values();

        $requestedRemovals = collect((array) ($validated['remove_images'] ?? []))
            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
            ->unique()
            ->values();

        // Only allow deleting paths already attached to this owned review. Never
        // trust a submitted storage path as authority to delete an arbitrary file.
        $pathsToRemove = $existingPaths
            ->filter(fn (string $path) => $requestedRemovals->containsStrict($path))
            ->values();

        $remainingPaths = $existingPaths
            ->reject(fn (string $path) => $pathsToRemove->containsStrict($path))
            ->values();

        $uploadedFiles = collect($request->file('images', []))
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->values();

        if (($remainingPaths->count() + $uploadedFiles->count()) > self::MAX_REVIEW_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'A review can contain up to ' . self::MAX_REVIEW_IMAGES . ' images.',
            ]);
        }

        $newPaths = collect();

        try {
            foreach ($uploadedFiles as $file) {
                $newPaths->push(
                    $file->store(
                        'product-reviews/' . $order->id . '/' . $productId,
                        'public'
                    )
                );
            }

            $imagePaths = $remainingPaths
                ->concat($newPaths)
                ->filter()
                ->values()
                ->all();

            ProductReview::query()->updateOrCreate(
                [
                    'marketplace_order_id' => $order->id,
                    'seller_product_id' => $productId,
                ],
                array_merge($this->identity->columns($request), [
                    'seller_account_id' => $order->seller_account_id,
                    'rating' => (int) $validated['rating'],
                    'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
                    'image_paths' => $imagePaths ?: null,
                ])
            );
        } catch (Throwable $exception) {
            foreach ($newPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        // Delete removed media only after the database update succeeds so a
        // failed save cannot orphan the review's existing references.
        foreach ($pathsToRemove as $path) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', 'Thank you. Your product review was saved.');
    }

    private function guardOwned(Request $request, MarketplaceOrder $order): void
    {
        $this->identity->guard($request);

        $owned = $this->identity->accountId($request)
            ? (int) $order->buyer_account_id === (int) $this->identity->accountId($request)
            : (int) $order->buyer_social_account_id === (int) $this->identity->socialId($request);

        abort_unless($owned, 403);
    }
}
