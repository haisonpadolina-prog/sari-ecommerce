<?php

namespace Tests\Feature;

use App\Events\SellerOrderUpdated;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Promotions\PlatformCampaign;
use App\Models\Promotions\PlatformShippingRedemption;
use App\Services\Promotions\PlatformShippingBudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlatformShippingBudgetTest extends TestCase
{
    use RefreshDatabase;

    private PlatformCampaign $campaign;
    private SellerProduct $product;
    private BuyerAccount $buyer;
    private SellerAccount $seller;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([SellerOrderUpdated::class]);
        $this->seller = SellerAccount::create(['email' => 'ship-seller@example.test', 'store_name' => 'Ship Seller', 'account_status' => 'active']);
        $this->buyer = BuyerAccount::create([
            'last_name' => 'Buyer', 'first_name' => 'Shipping', 'sex' => 'N/A',
            'email' => 'shipping-buyer@example.test', 'contact_no' => '09170000001',
            'birthday' => '2000-01-01', 'age' => 26, 'province_code' => '04', 'province_name' => 'Laguna',
            'municipality_code' => '041', 'municipality_name' => 'San Pablo', 'barangay_code' => '0411',
            'barangay_name' => 'Test', 'street_address' => 'Test', 'password' => bcrypt('Test123!'), 'account_status' => 'active',
        ]);
        $this->product = SellerProduct::create(['seller_account_id' => $this->seller->id, 'name' => 'Shipping Product',
            'category' => 'Home', 'price' => 1000, 'stock' => 20, 'moderation_status' => 'approved']);
        $this->campaign = PlatformCampaign::create([
            'name' => 'Shipping Promo', 'slug' => 'shipping-promo', 'state' => 'published', 'target_scope' => 'all',
            'shipping_enabled' => true, 'shipping_code' => 'SHIP50', 'shipping_budget_cents' => 10000,
            'shipping_cap_cents' => 5000, 'shipping_minimum_cents' => 50000, 'shipping_per_buyer_limit' => 1,
            'starts_at' => now()->subHour(), 'ends_at' => now()->addDay(),
        ]);
    }

    private function order(string $reference = 'CHK-SHIP-1'): MarketplaceOrder
    {
        return MarketplaceOrder::create([
            'order_number' => 'SHIP-'.uniqid(), 'checkout_reference' => $reference,
            'seller_account_id' => $this->seller->id, 'buyer_account_id' => $this->buyer->id,
            'buyer_name' => 'Shipping Buyer', 'buyer_email' => $this->buyer->email,
            'buyer_phone' => '09170000001', 'buyer_address' => 'Test Street, San Pablo, Laguna, Philippines',
            'payment_method' => 'COD', 'payment_status' => 'pending', 'items' => [], 'status' => 'new',
            'original_subtotal' => 1000, 'seller_receivable_subtotal' => 1000, 'subtotal' => 1000,
            'original_delivery_fee' => 80, 'delivery_fee' => 80, 'total' => 1080,
        ]);
    }

    private function apply(MarketplaceOrder $order): void
    {
        DB::transaction(fn () => app(PlatformShippingBudgetService::class)->applyToOrder('SHIP50', $order,
            [['product' => $this->product, 'buyer_line_total' => 1000]]));
    }

    public function test_reservation_changes_only_buyer_fee_and_preserves_seller_and_courier_basis(): void
    {
        $order = $this->order(); $this->apply($order); $this->apply($order);
        $order->refresh();
        $this->assertSame(30.0, (float) $order->delivery_fee);
        $this->assertSame(1030.0, (float) $order->total);
        $this->assertSame(80.0, $order->courierDeliveryFee());
        $this->assertSame(0.0, $order->sellerFundedShippingDiscount());
        $this->assertSame(1000.0, (float) $order->seller_receivable_subtotal);
        $this->assertDatabaseCount('platform_shipping_redemptions', 1);
        $this->assertSame(['allocated' => 10000, 'reserved' => 5000, 'used' => 0, 'available' => 5000], app(PlatformShippingBudgetService::class)->totals($this->campaign->fresh()));
    }

    public function test_cancellation_releases_once_and_restores_buyer_use(): void
    {
        $order = $this->order(); $this->apply($order);
        $order->update(['status' => 'cancelled']);
        app(PlatformShippingBudgetService::class)->syncOrder($order);
        $this->assertSame(10000, app(PlatformShippingBudgetService::class)->totals($this->campaign->fresh())['available']);
        $this->assertDatabaseHas('platform_shipping_redemptions', ['state' => 'released']);
        $this->apply($this->order('CHK-SECOND'));
        $this->assertSame(5000, app(PlatformShippingBudgetService::class)->totals($this->campaign->fresh())['reserved']);
    }

    public function test_delivery_marks_used_and_later_cancellation_does_not_refund_spent_subsidy(): void
    {
        $order = $this->order(); $this->apply($order);
        $order->update(['status' => 'delivered', 'delivered_at' => now(), 'payment_status' => 'paid']);
        app(PlatformShippingBudgetService::class)->syncOrder($order);
        $order->update(['status' => 'cancelled']);
        $totals = app(PlatformShippingBudgetService::class)->totals($this->campaign->fresh());
        $this->assertSame(5000, $totals['used']); $this->assertSame(0, $totals['reserved']);
    }

    public function test_budget_exhaustion_rolls_back_reservation(): void
    {
        $this->campaign->update(['shipping_budget_cents' => 5000, 'shipping_per_buyer_limit' => 5]);
        $this->apply($this->order());
        try { $this->apply($this->order('CHK-SECOND')); $this->fail('Expected exhausted budget'); }
        catch (ValidationException $exception) { $this->assertArrayHasKey('shipping_code', $exception->errors()); }
        $this->assertDatabaseCount('platform_shipping_redemptions', 1);
        $this->assertSame(0, app(PlatformShippingBudgetService::class)->totals($this->campaign->fresh())['available']);
    }

    public function test_usage_limit_applies_across_new_checkouts_but_not_split_orders_in_same_checkout(): void
    {
        $this->apply($this->order()); $this->apply($this->order());
        $this->assertDatabaseCount('platform_shipping_redemptions', 2);
        $this->expectException(ValidationException::class);
        $this->apply($this->order('CHK-SECOND'));
    }

    public function test_shipping_cap_cannot_exceed_remaining_fee_after_seller_discount(): void
    {
        $order = $this->order();
        $order->update(['shipping_discount_amount' => 60, 'delivery_fee' => 20, 'total' => 1020, 'shipping_discount_funder' => 'seller']);
        $this->apply($order); $order->refresh();
        $this->assertSame(20.0, (float) $order->platform_shipping_discount_amount);
        $this->assertSame(60.0, $order->sellerFundedShippingDiscount());
        $this->assertSame(0.0, (float) $order->delivery_fee);
    }

    public function test_paused_campaign_rejects_redemption(): void
    {
        $this->campaign->update(['state' => 'paused']);
        $this->expectException(ValidationException::class); $this->apply($this->order());
    }

    public function test_ineligible_address_rejects_and_valid_area_matches_whole_component(): void
    {
        $this->campaign->update(['shipping_areas' => ['Manila']]);
        $this->expectException(ValidationException::class); $this->apply($this->order());
    }

    public function test_ineligible_product_scope_rejects(): void
    {
        $this->campaign->update(['target_scope' => 'products']);
        $this->expectException(ValidationException::class); $this->apply($this->order());
    }

    public function test_minimum_spend_rejects(): void
    {
        $this->campaign->update(['shipping_minimum_cents' => 100001]);
        $this->expectException(ValidationException::class); $this->apply($this->order());
    }

    public function test_cancelled_discounted_order_cannot_be_reopened(): void
    {
        $order = $this->order(); $this->apply($order); $order->update(['status' => 'cancelled']);
        $this->expectException(ValidationException::class); $order->update(['status' => 'new']);
    }

    private function campaignInput(): array
    {
        return ['name' => 'Admin Shipping', 'state' => 'published', 'target_scope' => 'all',
            'sale_enabled' => '0', 'voucher_enabled' => '0', 'shipping_enabled' => '1',
            'shipping_code' => 'ADMINSHIP', 'shipping_budget' => '5000.00', 'shipping_cap' => '50.00',
            'shipping_minimum' => '500.00', 'shipping_per_buyer_limit' => 1,
            'activate_immediately' => '1', 'ends_at' => now('Asia/Manila')->addDay()->format('Y-m-d H:i:s')];
    }

    public function test_admin_can_create_shipping_only_campaign_and_open_budget_ui(): void
    {
        $this->withSession(['is_admin' => true])->post('/admin/promotions', $this->campaignInput())->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('platform_campaigns', ['shipping_code' => 'ADMINSHIP', 'shipping_budget_cents' => 500000]);
        $this->withSession(['is_admin' => true])->get('/admin/promotions?edit='.$this->campaign->id)->assertOk()->assertSee('Shipping Voucher &amp; Budget', false)->assertSee('Available');
    }

    public function test_admin_cannot_reduce_budget_below_committed_amount_or_delete_history(): void
    {
        $this->apply($this->order());
        $input = $this->campaignInput(); $input['shipping_budget'] = '49.99'; $input['shipping_cap'] = '40';
        $this->withSession(['is_admin' => true])->patch('/admin/promotions/'.$this->campaign->id, $input)->assertSessionHasErrors('shipping_budget');
        $this->withSession(['is_admin' => true])->delete('/admin/promotions/'.$this->campaign->id)->assertSessionHasErrors('campaign');
    }

    public function test_guest_cannot_create_shipping_campaign_and_invalid_precision_is_rejected(): void
    {
        $this->post('/admin/promotions', $this->campaignInput())->assertRedirect(route('login'));
        $input = $this->campaignInput(); $input['shipping_budget'] = '1.001';
        $this->withSession(['is_admin' => true])->post('/admin/promotions', $input)->assertSessionHasErrors('shipping_budget');
    }

    private function checkoutPayload(): array
    {
        return ['buyer_name' => 'Shipping Buyer', 'buyer_email' => $this->buyer->email,
            'buyer_phone' => '09170000001', 'buyer_address' => 'Street, San Pablo, Laguna, Philippines',
            'payment_method' => 'COD', 'shipping_code' => 'SHIP50'];
    }

    public function test_web_checkout_commits_shipping_reservation_and_clears_cart(): void
    {
        $this->seller->forceFill(['registration_status' => 'approved', 'approved_at' => now()])->save();
        \App\Models\Orders\BuyerCartItem::create(['buyer_account_id' => $this->buyer->id,
            'seller_product_id' => $this->product->id, 'quantity' => 1]);
        $session = ['is_buyer' => true, 'buyer_account_id' => $this->buyer->id];
        $this->withSession($session)->get('/buyer/checkout?shipping_code=SHIP50')
            ->assertOk()->assertViewHas('platformShippingDiscount', 50.0);
        $this->withSession($session)->postJson('/buyer/checkout', $this->checkoutPayload())->assertOk()->assertJsonPath('ok', true);
        $this->assertDatabaseCount('buyer_cart_items', 0);
        $this->assertDatabaseHas('marketplace_orders', ['buyer_account_id' => $this->buyer->id, 'platform_shipping_discount_amount' => 50]);
        $this->assertSame(5000, $this->campaign->fresh()->shipping_reserved_cents);
    }

    public function test_web_checkout_failure_rolls_back_orders_stock_and_budget(): void
    {
        $this->seller->forceFill(['registration_status' => 'approved', 'approved_at' => now()])->save();
        $this->campaign->update(['shipping_budget_cents' => 4999]);
        \App\Models\Orders\BuyerCartItem::create(['buyer_account_id' => $this->buyer->id,
            'seller_product_id' => $this->product->id, 'quantity' => 1]);
        $this->withSession(['is_buyer' => true, 'buyer_account_id' => $this->buyer->id])
            ->postJson('/buyer/checkout', $this->checkoutPayload())->assertUnprocessable()->assertJsonValidationErrors('shipping_code');
        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertDatabaseCount('platform_shipping_redemptions', 0);
        $this->assertDatabaseCount('buyer_cart_items', 1);
        $this->assertSame(20, (int) $this->product->fresh()->stock);
    }

    public function test_platform_subsidy_is_not_deducted_by_seller_finance_settlement(): void
    {
        $order = $this->order(); $this->apply($order);
        $order->update(['status' => 'delivered', 'delivered_at' => now(), 'payment_status' => 'paid']);
        $commission = app(\App\Services\Finance\CommissionService::class)->recordForDeliveredOrder($order);
        $settlement = app(\App\Services\Finance\SellerSettlementService::class)->recordForCompletedOrder($order, $commission);
        $this->assertSame(900.0, (float) $settlement->seller_net_amount);
    }

    public function test_mobile_preview_and_checkout_use_same_shipping_authority(): void
    {
        $this->seller->forceFill(['registration_status' => 'approved', 'approved_at' => now()])->save();
        \App\Models\Orders\BuyerCartItem::create(['buyer_account_id' => $this->buyer->id,
            'seller_product_id' => $this->product->id, 'quantity' => 1]);
        \Laravel\Sanctum\Sanctum::actingAs($this->buyer, ['*']);
        $this->postJson('/api/v1/buyer/checkout/preview', ['shipping_code' => 'SHIP50', 'buyer_address' => 'Street, San Pablo, Laguna'])
            ->assertOk()->assertJsonPath('data.shipping.platform_shipping_discount', 50)->assertJsonPath('data.grand_total', 1030);
        $this->postJson('/api/v1/buyer/checkout', $this->checkoutPayload())->assertCreated();
        $this->assertSame(5000, $this->campaign->fresh()->shipping_reserved_cents);
    }

    public function test_reconcile_command_repairs_terminal_bulk_updates_idempotently(): void
    {
        $order = $this->order(); $this->apply($order);
        DB::table('marketplace_orders')->where('id', $order->id)->update(['status' => 'cancelled']);
        $this->artisan('sari:shipping-budget-reconcile')->assertSuccessful();
        $this->artisan('sari:shipping-budget-reconcile')->assertSuccessful();
        $this->assertSame(0, $this->campaign->fresh()->shipping_reserved_cents);
        $this->assertDatabaseHas('platform_shipping_redemptions', ['state' => 'released']);
    }

    public function test_valid_location_social_identity_and_feature_disable_rules(): void
    {
        $this->campaign->update(['shipping_areas' => ['laguna']]);
        $service = app(PlatformShippingBudgetService::class);
        $quote = $service->quote('ship50', [['product' => $this->product, 'buyer_line_total' => 1000]],
            ['buyer_social_account_id' => 99], 'Street, San Pablo, Laguna, Philippines', 8000);
        $this->assertSame(5000, $quote['amount_cents']);
        \App\Models\Platform\PlatformSetting::putValue('platform_campaigns_enabled', false);
        $this->expectException(ValidationException::class);
        $service->quote('SHIP50', [['product' => $this->product, 'buyer_line_total' => 1000]],
            ['buyer_account_id' => $this->buyer->id], 'Street, Laguna', 8000);
    }

    public function test_money_conversion_uses_exact_centavos(): void
    {
        $this->assertSame(10001, PlatformShippingBudgetService::cents('100.01'));
        $this->assertSame(10, PlatformShippingBudgetService::cents('0.1'));
        $this->expectException(ValidationException::class); PlatformShippingBudgetService::cents('1e3');
    }
}
