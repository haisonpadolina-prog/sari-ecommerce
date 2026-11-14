<?php

namespace App\Console\Commands;

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Services\Products\ProductComplianceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SeedSellerFlowProducts extends Command
{
    protected $signature = 'sari:seed-seller-flow-products {--dry-run : Validate without adding products or files} {--allow-nonlocal : Intentionally add QA products outside local/testing}';

    protected $description = 'Add one screened, review-required COD product to each of the first 10 managed flow Sellers.';

    private const SELLER_MARKER = 'SARI_SELLER_FLOW50_V1';

    private const PRODUCT_MARKER = '[SARI_FLOW10_PRODUCTS_V1]';

    public function handle(ProductComplianceService $scanner): int
    {
        if (! app()->environment(['local', 'testing']) && ! $this->option('allow-nonlocal')) {
            $this->error('Use --allow-nonlocal only to intentionally add synthetic product fixtures to a demo environment.');

            return self::FAILURE;
        }
        $lock = null;
        try {
            $items = $this->fixtures();
            $this->preflight($items);
            if ($this->option('dry-run')) {
                $this->info('Validated 10 managed Sellers and 10 illustrated product images. No products, files, or screening calls created.');

                return self::SUCCESS;
            }
            $directory = storage_path('app/private/sari-seller-flow-products');
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new RuntimeException('Cannot create the private product seed directory.');
            }
            $lock = fopen($directory.'/seed.lock', 'c');
            if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another flow product seed is running.');
            }
            $this->preflight($items);
            $scans = [];
            foreach ($items as $index => $item) {
                if (SellerProduct::query()->where('sku', $item['sku'])->exists()) {
                    continue;
                }
                $relative = 'demo-flow10-products-v1/'.$item['image'];
                $disk = Storage::disk('public');
                if ($disk->exists($relative)) {
                    if (hash('sha256', $disk->get($relative)) !== $item['sha256']) {
                        throw new RuntimeException('A bundled product image path contains a changed file. Refusing to overwrite it.');
                    }
                } elseif (! $disk->put($relative, file_get_contents(database_path('data/sari_flow_products/images/'.$item['image'])))) {
                    throw new RuntimeException('Could not install a product image.');
                }
                $this->line('Screening product '.($index + 1).'/10 for '.$item['seller_email']);
                $scans[$item['sku']] = $scanner->scan($item['name'], $this->description($item), $item['category'], $relative);
            }
            $created = DB::transaction(function () use ($items, $scans, $scanner): int {
                SellerAccount::query()->whereIn('email', array_column($items, 'seller_email'))->orderBy('id')->lockForUpdate()->get();
                $this->preflight($items);
                $sellers = SellerAccount::query()->whereIn('email', array_column($items, 'seller_email'))->get()->keyBy('email');
                $created = 0;
                foreach ($items as $item) {
                    if (SellerProduct::query()->where('sku', $item['sku'])->exists()) {
                        continue;
                    }
                    $scan = $scans[$item['sku']];
                    $product = new SellerProduct;
                    $product->forceFill([
                        'seller_account_id' => $sellers[$item['seller_email']]->id,
                        'name' => $item['name'], 'category' => $item['category'], 'brand' => 'SARI QA Demo', 'condition' => 'new', 'sku' => $item['sku'],
                        'price' => 100, 'stock' => 20, 'discount' => 0, 'low_stock_threshold' => 5,
                        'cash_on_delivery' => true, 'free_shipping' => false, 'preparation_days' => 1,
                        'package_weight' => 0.250, 'package_length' => 20, 'package_width' => 15, 'package_height' => 10,
                        'has_variants' => false, 'specifications' => json_encode([['name' => 'Purpose', 'value' => 'Synthetic order-flow fixture']], JSON_THROW_ON_ERROR),
                        'description' => $this->description($item), 'image_path' => 'demo-flow10-products-v1/'.$item['image'],
                        'moderation_status' => $scan['flagged'] ? 'flagged' : 'pending', 'screening_risk' => $scan['risk'],
                        'risk_score' => $scan['score'], 'screening_engine' => $scan['engine'], 'screening_reason' => $scan['reason'],
                        'matched_terms' => $scan['matched_terms'], 'ai_flagged' => $scan['ai_flagged'],
                        'ai_categories' => json_encode($scan['ai_categories'], JSON_THROW_ON_ERROR),
                        'ai_decision' => $scan['ai_decision'], 'ai_policy_category' => $scan['ai_policy_category'],
                        'ai_confidence' => $scan['ai_confidence'], 'ai_reason' => $scan['ai_reason'],
                        'ai_signals' => json_encode($scan['ai_signals'], JSON_THROW_ON_ERROR),
                        'ai_image_reviewed' => $scan['ai_image_reviewed'], 'ai_text_reviewed' => $scan['ai_text_reviewed'],
                        'ai_status' => $scan['ai_status'], 'ai_response_id' => $scan['ai_response_id'] ?: null,
                        'screened_at' => now(), 'requires_re_review' => false,
                    ])->save();
                    $scanner->record($product, $scan, 'flow_demo_seed');
                    $created++;
                }

                return $created;
            }, 3);
            $products = SellerProduct::query()->whereIn('sku', array_column($items, 'sku'))->with('seller')->orderBy('sku')->get();
            $this->table(['Product ID', 'Seller', 'Product', 'Price', 'Stock', 'Review'], $products->map(fn ($p) => [$p->id, $p->seller->email, $p->name, number_format((float) $p->price, 2), $p->stock, $p->moderation_status])->all());
            $this->info('Created '.$created.' products. Existing products, edits, stock and moderation decisions were preserved.');
            $this->line('Next: Admin > Products for Review. Review these 10 fixtures before Buyer checkout. No listings were auto-approved.');
            $this->line('No accounts, passwords, orders, hubs, routes, payments or wallet balances were changed.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Product seed stopped because of a database, screening or filesystem error. New products and screening logs are atomic; existing products are preserved.');

            return self::FAILURE;
        } finally {
            if (is_resource($lock)) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }

    private function description(array $item): string
    {
        return self::PRODUCT_MARKER.' '.$item['description'].' Illustrated demo image; synthetic listing for controlled order-to-delivery QA. No real merchant claims. Demo packaging: 0.250 kg, 20 x 15 x 10 cm.';
    }

    private function fixtures(): array
    {
        $path = database_path('data/sari_flow_products/products.json');
        if (! is_file($path)) {
            throw new RuntimeException('Missing bundled product fixtures.');
        }
        $items = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR)['products'] ?? [];
        if (count($items) !== 10) {
            throw new RuntimeException('Expected exactly 10 product fixtures.');
        }
        foreach ($items as $index => $item) {
            $slot = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            if (($item['sku'] ?? '') !== 'SARI-FLOW10-'.$slot || ($item['seller_email'] ?? '') !== 'seller.flow.'.$slot.'@example.test' || ($item['image'] ?? '') !== 'product-'.$slot.'.png') {
                throw new RuntimeException('Invalid managed fixture identity.');
            }
            foreach (['name', 'category', 'description'] as $field) {
                if (empty($item[$field])) {
                    throw new RuntimeException('Incomplete product fixture metadata.');
                }
            }
            $image = database_path('data/sari_flow_products/images/'.$item['image']);
            if (! is_file($image) || hash_file('sha256', $image) !== ($item['sha256'] ?? '')) {
                throw new RuntimeException('Missing or changed product illustration.');
            }
        }

        return $items;
    }

    private function preflight(array $items): void
    {
        foreach ([
            'seller_accounts' => ['registration_application_id', 'deleted_at', 'line_of_business'],
            'registration_applications' => ['admin_note', 'role'],
            'seller_products' => ['cash_on_delivery', 'has_variants', 'specifications', 'package_weight', 'package_length', 'package_width', 'package_height', 'preparation_days', 'archived_at', 'screening_engine', 'risk_score', 'ai_status', 'ai_response_id', 'ai_signals', 'screened_at', 'requires_re_review'],
            'product_moderation_logs' => ['seller_product_id', 'source', 'engine'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException('Required existing project migrations are missing for '.$table.'.');
            }
        }
        $sellers = SellerAccount::query()->with('registrationApplication')->whereIn('email', array_column($items, 'seller_email'))->get()->keyBy('email');
        $products = SellerProduct::query()->whereIn('sku', array_column($items, 'sku'))->get()->groupBy('sku');
        foreach ($items as $item) {
            $seller = $sellers->get($item['seller_email']);
            if (! $seller || $seller->registrationApplication?->admin_note !== self::SELLER_MARKER || $seller->registrationApplication?->role !== 'seller') {
                throw new RuntimeException('Missing or unrelated flow Seller: '.$item['seller_email'].'. Run the 50-Seller seed first.');
            }
            if ($seller->account_status !== 'active' || $seller->registration_status !== 'approved' || ! $seller->isBuyerAvailable() || $seller->line_of_business !== $item['category']) {
                throw new RuntimeException('A target Seller is restricted, paused, or has a changed category. Review that Seller before seeding products.');
            }
            $matches = $products->get($item['sku'], collect());
            if ($matches->count() > 1 || ($matches->count() === 1 && ((int) $matches->first()->seller_account_id !== (int) $seller->id || $matches->first()->archived_at !== null || ! str_contains((string) $matches->first()->description,self::PRODUCT_MARKER)))) {
                throw new RuntimeException('A reserved SKU is duplicated, archived, or belongs to another listing. Existing inventory will not be overwritten.');
            }
        }
    }
}
