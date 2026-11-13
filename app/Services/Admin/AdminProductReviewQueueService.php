<?php

namespace App\Services\Admin;

use App\Models\Catalog\SellerProduct;
use Illuminate\Support\Facades\Storage;

class AdminProductReviewQueueService
{
    public function load(): array
    {
        $reviewProducts = SellerProduct::query()
            ->with(['seller', 'latestVersion', 'galleryImages', 'activeVariants'])
            ->whereNull('archived_at')
            ->whereIn('moderation_status', ['pending', 'flagged'])
            ->latest('created_at')
            ->get();

        // Only advertise files that exist on the configured public disk.
        // Keep the URLs behind the existing Admin/Seller-authorized image routes.
        $disk = Storage::disk('public');
        $reviewMedia = $reviewProducts->mapWithKeys(function (SellerProduct $product) use ($disk): array {
            $coverPath = $product->availableCoverImagePath();
            $coverUrl = $coverPath ? route('admin.compliance.products.media', ['product' => $product, 'kind' => 'cover'], false) : null;
            $images = [];

            if ($coverUrl) {
                $images[] = [
                    'label' => 'Cover image',
                    'url' => $coverUrl,
                    'fit' => 'cover',
                ];
            }

            foreach ($product->galleryImages as $index => $image) {
                if (!$image->path || $image->path === $coverPath || !$disk->exists($image->path)) {
                    continue;
                }

                $images[] = [
                    'label' => 'Gallery ' . ($index + 1),
                    'url' => route('admin.compliance.products.media', ['product' => $product, 'kind' => 'gallery', 'media' => $image->id], false),
                    'fit' => 'cover',
                ];
            }

            foreach ($product->activeVariants as $variant) {
                if (!$variant->image_path || !$disk->exists($variant->image_path)) {
                    continue;
                }

                $images[] = [
                    'label' => 'Variant: ' . ($variant->sku ?: '#' . $variant->id),
                    'url' => route('admin.compliance.products.media', ['product' => $product, 'kind' => 'variant', 'media' => $variant->id], false),
                    'fit' => 'contain',
                ];
            }

            if ($product->size_guide_image_path && $disk->exists($product->size_guide_image_path)) {
                $images[] = [
                    'label' => 'Size guide',
                    'url' => route('admin.compliance.products.media', ['product' => $product, 'kind' => 'size-guide'], false),
                    'fit' => 'contain',
                ];
            }

            return [$product->id => [
                'cover_url' => $coverUrl,
                'images' => $images,
            ]];
        });

        return compact('reviewProducts', 'reviewMedia');
    }
}
