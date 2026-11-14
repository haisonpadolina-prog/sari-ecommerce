<?php

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\ProductModerationLog;
use App\Models\Catalog\SellerProduct;
use App\Services\Products\ProductComplianceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function sariFlowProductScan(bool $flagged = false): array
{
    return ['flagged' => $flagged, 'risk' => $flagged ? 'medium' : 'low', 'score' => $flagged ? 40 : 0, 'reason' => 'Synthetic test scanner result.', 'matched_terms' => [], 'matched_rules' => [], 'engine' => 'test-only', 'ai_flagged' => false, 'ai_decision' => 'review', 'ai_policy_category' => 'none', 'ai_confidence' => 0, 'ai_reason' => 'Test result.', 'ai_signals' => [], 'ai_image_reviewed' => false, 'ai_text_reviewed' => false, 'ai_status' => 'unavailable', 'ai_response_id' => null, 'ai_categories' => []];
}

beforeEach(function () {
    $this->originalStoragePath = storage_path();
    $this->testStorage = sys_get_temp_dir().'/sari-flow-products-test-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->testStorage, 0700, true);
    $this->app->useStoragePath($this->testStorage);
    Storage::fake('public');
    config(['hashing.bcrypt.rounds' => 4]);
    Http::preventStrayRequests();
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(0);
    $this->scanner = Mockery::mock(ProductComplianceService::class)->makePartial();
    $this->scanner->shouldReceive('scan')->andReturn(sariFlowProductScan());
    $this->app->instance(ProductComplianceService::class, $this->scanner);
});
afterEach(function () {
    $this->app->useStoragePath($this->originalStoragePath);
    File::deleteDirectory($this->testStorage);
});

it('validates without products media changes or screening calls', function () {
    $this->scanner->shouldNotReceive('scan');
    $count = count(Storage::disk('public')->allFiles());
    $this->artisan('sari:seed-seller-flow-products', ['--dry-run' => true])->assertExitCode(0);
    expect(SellerProduct::count())->toBe(0)->and(ProductModerationLog::count())->toBe(0)
        ->and(count(Storage::disk('public')->allFiles()))->toBe($count)
        ->and(is_dir(storage_path('app/private/sari-seller-flow-products')))->toBeFalse();
});

it('creates exactly one complete pending product for each of the first ten managed sellers', function () {
    $hash = SellerAccount::where('email', 'seller.flow.001@example.test')->value('password');
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(0);
    expect(SellerProduct::count())->toBe(10)->and(ProductModerationLog::count())->toBe(10)
        ->and(SellerProduct::buyerVisible()->count())->toBe(0);
    foreach (SellerProduct::with('seller')->orderBy('sku')->get() as $index => $product) {
        expect($product->seller->email)->toBe(sprintf('seller.flow.%03d@example.test', $index + 1))
            ->and($product->category)->toBe($product->seller->line_of_business)
            ->and($product->cash_on_delivery)->toBeTrue()
            ->and($product->stock)->toBe(20)
            ->and((float) $product->price)->toBe(100.0)
            ->and((float) $product->package_weight)->toBe(0.25)
            ->and((float) $product->package_length)->toBe(20.0)
            ->and($product->preparation_days)->toBe(1)
            ->and($product->moderation_status)->toBe('pending');
        Storage::disk('public')->assertExists($product->image_path);
    }
    expect(SellerAccount::where('email', 'seller.flow.001@example.test')->value('password'))->toBe($hash)
        ->and(DB::table('marketplace_orders')->count())->toBe(0)
        ->and(DB::table('finance_wallet_entries')->count())->toBe(0);
    $this->scanner->shouldHaveReceived('scan')->times(10);
});

it('preserves edited approved products and consumed inventory without repeat screening', function () {
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(0);
    $product = SellerProduct::first();
    $product->update(['stock' => 17, 'price' => 120, 'moderation_status' => 'approved', 'image_path' => 'custom/product.png']);
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(0);
    expect(SellerProduct::count())->toBe(10)->and(ProductModerationLog::count())->toBe(10)
        ->and($product->fresh()->stock)->toBe(17)
        ->and((float) $product->fresh()->price)->toBe(120.0)
        ->and($product->fresh()->moderation_status)->toBe('approved')
        ->and($product->fresh()->image_path)->toBe('custom/product.png')
        ->and(SellerProduct::buyerVisible()->count())->toBe(1);
    $this->scanner->shouldHaveReceived('scan')->times(10);
});

it('keeps flagged scanner decisions for admin review rather than auto approving', function () {
    $scanner = Mockery::mock(ProductComplianceService::class)->makePartial();
    $scanner->shouldReceive('scan')->andReturn(sariFlowProductScan(true));
    $this->app->instance(ProductComplianceService::class, $scanner);
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(0);
    expect(SellerProduct::where('moderation_status', 'flagged')->count())->toBe(10)
        ->and(ProductModerationLog::where('decision', 'flagged')->count())->toBe(10)
        ->and(SellerProduct::buyerVisible()->count())->toBe(0);
});

it('requires the managed seller seed and does not silently restore archived sellers', function () {
    SellerAccount::where('email', 'seller.flow.001@example.test')->firstOrFail()->delete();
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(1);
    expect(SellerProduct::count())->toBe(0)->and(SellerAccount::count())->toBe(49);
});

it('does not overwrite an unrelated listing with a reserved SKU', function () {
    $seller = SellerAccount::first();
    $product = SellerProduct::create(['seller_account_id' => $seller->id, 'sku' => 'SARI-FLOW10-001', 'name' => 'Preserve This', 'category' => 'Electronics', 'price' => 999, 'stock' => 7]);
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(1);
    expect(SellerProduct::count())->toBe(1)->and($product->fresh()->name)->toBe('Preserve This')->and($product->fresh()->stock)->toBe(7);
});

it('rolls back all new products and logs on a later database failure without leaking SQL details', function () {
    DB::unprepared("CREATE TRIGGER stop_flow_product BEFORE INSERT ON seller_products WHEN NEW.sku = 'SARI-FLOW10-005' BEGIN SELECT RAISE(ABORT, 'PRIVATE_FLOW_PRODUCT_SENTINEL'); END;");
    $exit = Artisan::call('sari:seed-seller-flow-products');
    expect($exit)->toBe(1)->and(Artisan::output())->not->toContain('PRIVATE_FLOW_PRODUCT_SENTINEL')
        ->and(SellerProduct::count())->toBe(0)->and(ProductModerationLog::count())->toBe(0);
});

it('requires the explicit demo override outside local or testing', function () {
    $this->app['env'] = 'production';
    $this->artisan('sari:seed-seller-flow-products')->assertExitCode(1);
    $this->artisan('sari:seed-seller-flow-products', ['--dry-run' => true, '--allow-nonlocal' => true])->assertExitCode(0);
    expect(SellerProduct::count())->toBe(0);
});
