<?php

namespace App\Console\Commands;

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Read-only by default; opt-in recovery for identifiable SARI demo accounts.
 * Never approves pending products or changes non-demo accounts.
 */
class InspectDemoCatalog extends Command
{
    protected $signature = 'sari:demo-catalog
        {--apply : Allow explicitly selected changes (requires confirmation)}
        {--open-demo-stores : Open eligible SARI DEMO ### stores}
        {--restore-demo-images : Recover missing/invalid demo pictures from DummyJSON samples}';

    protected $description = 'Inspect demo seller visibility and image files, optionally restore demo-only data';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $openStores = (bool) $this->option('open-demo-stores');
        $restoreImages = (bool) $this->option('restore-demo-images');

        if ($apply && !$openStores && !$restoreImages) {
            $this->error('Select --open-demo-stores and/or --restore-demo-images with --apply.');
            return self::FAILURE;
        }

        // Demo names may include an optional category after ' | ', for example:
        // 'SARI DEMO 006 | Food & Beverage'. Match the full name, not real sellers.
        $sellers = SellerAccount::query()
            ->where('store_name', 'like', 'SARI DEMO %')
            ->get()
            ->filter(fn (SellerAccount $seller): bool =>
                preg_match('/^SARI DEMO [0-9]{3}(?:\s*\|\s*[^\r\n]+)?$/i', trim((string) $seller->store_name)) === 1
            );

        if ($sellers->isEmpty()) {
            $this->warn('No SARI DEMO ### seller stores (with optional category suffix) found in THIS database.');
            return self::SUCCESS;
        }

        $products = SellerProduct::query()
            ->with(['seller', 'galleryImages'])
            ->whereIn('seller_account_id', $sellers->pluck('id'))
            ->orderBy('seller_account_id')
            ->orderBy('id')
            ->get()
            ->filter(fn (SellerProduct $product): bool =>
                str_ends_with((string) $product->name, ' (Demo)')
            );

        $this->info('Database: ' . config('database.default') . ' | Demo stores: ' . $sellers->count()
            . ' | Demo products: ' . $products->count());

        $this->table(['Store ID', 'Store', 'Status', 'Account', 'Approved', 'Pending/flagged'],
            $sellers->map(function (SellerAccount $seller) use ($products): array {
                $items = $products->where('seller_account_id', $seller->id);
                return [
                    $seller->id,
                    $seller->store_name,
                    $seller->store_status ?: 'open',
                    $seller->account_status ?: 'active',
                    $items->where('moderation_status', 'approved')->count(),
                    $items->whereIn('moderation_status', ['pending', 'flagged'])->count(),
                ];
            })->values()->all()
        );

        $issues = $products->filter(function (SellerProduct $product): bool {
            if ($this->validImage((string) ($product->image_path ?: ''))) {
                return false;
            }
            // A missing cover already falls back to an available gallery image
            // in the earlier image endpoint patch. An existing corrupt cover
            // does not, so it still needs repair even with a good gallery.
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                return true;
            }
            return !$product->galleryImages->contains(fn ($image): bool =>
                $this->validImage((string) ($image->path ?: ''))
            );
        });
        $this->info('Demo products needing cover-image repair: ' . $issues->count());
        $this->table(['Product ID', 'Seller ID', 'Product', 'Moderation', 'Cover'],
            $issues->map(fn (SellerProduct $product): array => [
                $product->id, $product->seller_account_id, $product->name,
                $product->moderation_status, $product->image_path ? 'missing/invalid' : 'not set',
            ])->values()->all()
        );

        if (!$apply) {
            $this->comment('READ-ONLY. To change demo-only data, pass --apply and the intended action flag(s).');
            return self::SUCCESS;
        }

        if (!$this->confirm('Modify ONLY eligible SARI DEMO ### stores (optional category suffix) and/or (Demo) product images?', false)) {
            $this->warn('Cancelled; no changes made.');
            return self::SUCCESS;
        }

        $opened = 0;
        if ($openStores) {
            foreach ($sellers as $seller) {
                if ((string) $seller->store_status === 'open') {
                    continue;
                }
                if (!$this->canOpen($seller)) {
                    $this->warn("Skipped restricted seller #{$seller->id}.");
                    continue;
                }
                $seller->forceFill(['store_status' => 'open'])->save();
                $opened++;
            }
            $this->info("Opened {$opened} eligible DEMO stores. Pending products are still NOT approved.");
        }

        if ($restoreImages) {
            $this->restoreImages($issues);
        }

        $this->comment('Approved products from eligible open stores may now appear at /buyer/products.');
        return self::SUCCESS;
    }

    private function canOpen(SellerAccount $seller): bool
    {
        if (in_array((string) $seller->account_status, ['banned', 'deactivated', 'blocked'], true)) {
            return false;
        }
        if ((int) ($seller->warning_count ?? 0) >= 3 && filled($seller->suspension_reason)) {
            return false;
        }
        return !$seller->suspended_until || now()->greaterThanOrEqualTo($seller->suspended_until);
    }

    private function validImage(string $path): bool
    {
        if ($path === '') {
            return false;
        }
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists($path) || $disk->size($path) > 8 * 1024 * 1024) {
                return false;
            }
            $content = $disk->get($path);
            return is_string($content) && @getimagesizefromstring($content) !== false;
        } catch (Throwable) {
            return false;
        }
    }

    private function restoreImages($issues): void
    {
        // Use the seller's own valid gallery upload before any external sample.
        $reused = 0;
        $issues = $issues->reject(function (SellerProduct $product) use (&$reused): bool {
            foreach ($product->galleryImages as $image) {
                if ($this->validImage((string) ($image->path ?: ''))) {
                    $product->forceFill(['image_path' => $image->path])->save();
                    $reused++;
                    return true;
                }
            }
            return false;
        });
        $this->info("Recovered {$reused} demo covers using existing gallery uploads.");
        if ($issues->isEmpty()) {
            return;
        }

        try {
            // Download a single public, fictional demo catalogue; never infer a
            // URL from user-controlled product data or fetch arbitrary domains.
            $response = Http::connectTimeout(5)->timeout(20)
                ->get('https://dummyjson.com/products', ['limit' => 0]);
            $response->throw();
            $catalogue = $response->json('products');
            if (!is_array($catalogue)) {
                throw new \RuntimeException('Unexpected demo API payload.');
            }
        } catch (Throwable $error) {
            $this->error('Demo image catalogue unavailable: ' . $error->getMessage());
            return;
        }

        $byTitle = [];
        foreach ($catalogue as $item) {
            if (is_array($item) && isset($item['title'])) {
                $byTitle[mb_strtolower(trim((string) $item['title']))] = $item;
            }
        }

        $restored = 0;
        foreach ($issues as $product) {
            $title = preg_replace('/\s*\(Demo\)$/i', '', trim((string) $product->name));
            $item = $byTitle[mb_strtolower((string) $title)] ?? null;
            $url = is_array($item) ? ($item['thumbnail'] ?? ($item['images'][0] ?? null)) : null;

            $parts = is_string($url) ? parse_url($url) : false;
            if (!is_array($parts)
                || ($parts['scheme'] ?? '') !== 'https'
                || strtolower((string) ($parts['host'] ?? '')) !== 'cdn.dummyjson.com'
                || !str_starts_with((string) ($parts['path'] ?? ''), '/product-images/')) {
                $this->warn("No approved demo image source for product #{$product->id}: {$title}");
                continue;
            }

            try {
                $imageResponse = Http::connectTimeout(5)->timeout(20)->get($url);
                $imageResponse->throw();
                $bytes = $imageResponse->body();
                if (strlen($bytes) < 100 || strlen($bytes) > 5 * 1024 * 1024
                    || @getimagesizefromstring($bytes) === false) {
                    throw new \RuntimeException('Image bytes are missing, oversized, or invalid.');
                }
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
                $extension = match ($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    default => null,
                };
                if (!$extension) {
                    throw new \RuntimeException('Image format not allowed.');
                }

                $path = 'seller-products/demo-catalog/' . $product->id . '/'
                    . hash('sha256', (string) $title) . '.' . $extension;
                if (!Storage::disk('public')->put($path, $bytes)) {
                    throw new \RuntimeException('Failed to write to public storage disk.');
                }
                $product->forceFill(['image_path' => $path])->save();
                $restored++;
            } catch (Throwable $error) {
                $this->warn("Failed product #{$product->id}: " . $error->getMessage());
            }
        }

        $this->info("Restored {$restored} missing DEMO product cover images.");
        $this->comment('Sample images come from DummyJSON; do not treat them as verified merchant photos.');
    }
}
