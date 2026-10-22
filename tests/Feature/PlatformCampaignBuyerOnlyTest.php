<?php

namespace Tests\Feature;

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Promotions\PlatformCampaign;
use App\Services\Promotions\PlatformCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlatformCampaignBuyerOnlyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_live_admin_sale_changes_buyer_quote_without_mutating_seller_price(): void
    {
        $seller = SellerAccount::query()->create([
            'email' => 'seller-campaign@example.test',
            'store_name' => 'Campaign Seller',
            'account_status' => 'active',
        ]);

        $product = SellerProduct::query()->create([
            'seller_account_id' => $seller->id,
            'name' => 'Campaign Product',
            'category' => 'Fashion',
            'price' => 1000,
            'stock' => 10,
            'discount' => 0,
            'moderation_status' => 'approved',
        ]);

        PlatformCampaign::query()->create([
            'name' => '12.12 Buyer Sale',
            'slug' => '12-12-buyer-sale',
            'state' => 'published',
            'target_scope' => 'all',
            'sale_enabled' => true,
            'sale_discount_type' => 'percentage',
            'sale_discount_value' => 20,
            'voucher_enabled' => false,
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addDay(),
        ]);

        $quote = app(PlatformCampaignService::class)
            ->saleQuote($product, null, 1000);

        $this->assertSame(800.0, $quote['buyer_price']);
        $this->assertSame(200.0, $quote['discount']);
        $this->assertSame(1000.0, (float) $product->fresh()->price);
    }

    public function test_scheduled_or_ended_campaign_is_not_applied_to_buyer_price(): void
    {
        $seller = SellerAccount::query()->create([
            'email' => 'seller-scheduled@example.test',
            'store_name' => 'Scheduled Seller',
            'account_status' => 'active',
        ]);

        $product = SellerProduct::query()->create([
            'seller_account_id' => $seller->id,
            'name' => 'Scheduled Product',
            'category' => 'Electronics',
            'price' => 500,
            'stock' => 5,
            'discount' => 0,
            'moderation_status' => 'approved',
        ]);

        PlatformCampaign::query()->create([
            'name' => 'Future 1.1',
            'slug' => 'future-1-1',
            'state' => 'published',
            'target_scope' => 'all',
            'sale_enabled' => true,
            'sale_discount_type' => 'percentage',
            'sale_discount_value' => 50,
            'voucher_enabled' => false,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
        ]);

        $quote = app(PlatformCampaignService::class)
            ->saleQuote($product, null, 500);

        $this->assertSame(500.0, $quote['buyer_price']);
        $this->assertSame(0.0, $quote['discount']);
    }

    public function test_platform_voucher_is_limited_per_buyer_and_allocated_across_sellers(): void
    {
        $sellerA = SellerAccount::query()->create([
            'email' => 'voucher-a@example.test',
            'store_name' => 'Seller A',
            'account_status' => 'active',
        ]);
        $sellerB = SellerAccount::query()->create([
            'email' => 'voucher-b@example.test',
            'store_name' => 'Seller B',
            'account_status' => 'active',
        ]);

        $productA = SellerProduct::query()->create([
            'seller_account_id' => $sellerA->id,
            'name' => 'A',
            'category' => 'Home',
            'price' => 600,
            'stock' => 5,
            'moderation_status' => 'approved',
        ]);
        $productB = SellerProduct::query()->create([
            'seller_account_id' => $sellerB->id,
            'name' => 'B',
            'category' => 'Home',
            'price' => 400,
            'stock' => 5,
            'moderation_status' => 'approved',
        ]);

        $campaign = PlatformCampaign::query()->create([
            'name' => '12.12 Voucher',
            'slug' => '12-12-voucher',
            'state' => 'published',
            'target_scope' => 'all',
            'sale_enabled' => false,
            'voucher_enabled' => true,
            'voucher_code' => 'SARI1212',
            'voucher_discount_type' => 'fixed',
            'voucher_discount_value' => 100,
            'voucher_minimum_spend' => 500,
            'voucher_per_buyer_limit' => 1,
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addDay(),
        ]);

        $service = app(PlatformCampaignService::class);
        $identity = ['buyer_account_id' => 77, 'buyer_social_account_id' => null];
        $lines = collect([
            ['product' => $productA, 'seller_id' => $sellerA->id, 'buyer_line_total' => 600],
            ['product' => $productB, 'seller_id' => $sellerB->id, 'buyer_line_total' => 400],
        ]);

        $resolved = $service->resolveVoucherForCheckout('SARI1212', $lines, $identity);

        $this->assertNotNull($resolved);
        $this->assertSame(100.0, $resolved['discount']);
        $this->assertSame(60.0, $resolved['allocations'][$sellerA->id]);
        $this->assertSame(40.0, $resolved['allocations'][$sellerB->id]);

        $service->recordVoucherRedemption(
            $campaign,
            'CHK-TEST-1',
            $identity,
            100,
            [1, 2]
        );

        $this->expectException(ValidationException::class);
        $service->resolveVoucherForCheckout('SARI1212', $lines, $identity);
    }
}
