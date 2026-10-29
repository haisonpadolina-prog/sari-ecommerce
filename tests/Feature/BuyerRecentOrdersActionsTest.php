<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\ProductReview;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\SellerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->buyer = BuyerAccount::query()->create([
        'last_name' => 'Buyer',
        'first_name' => 'Recent',
        'sex' => 'N/A',
        'email' => 'recent.buyer@example.test',
        'contact_no' => '09170000001',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '130000000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137404000',
        'municipality_name' => 'Quezon City',
        'barangay_code' => '137404001',
        'barangay_name' => 'Commonwealth',
        'street_address' => 'Test Address',
        'password' => Hash::make('Buyer@2026'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    $this->seller = SellerAccount::query()->create([
        'email' => 'recent.seller@example.test',
        'store_name' => 'Recent Seller Store',
        'account_status' => 'active',
        'registration_status' => 'approved',
    ]);

    $this->product = SellerProduct::query()->create([
        'seller_account_id' => $this->seller->id,
        'name' => 'Delivered Test Product',
        'category' => 'Test',
        'price' => 500,
        'stock' => 10,
        'moderation_status' => 'approved',
    ]);

    $this->order = MarketplaceOrder::query()->create([
        'order_number' => 'SARI-RECENT-0001',
        'seller_account_id' => $this->seller->id,
        'buyer_account_id' => $this->buyer->id,
        'buyer_name' => 'Recent Buyer',
        'buyer_phone' => '09170000001',
        'buyer_address' => 'Test Address',
        'payment_method' => 'COD',
        'payment_status' => 'paid',
        'items' => [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'qty' => 1,
            'price' => 500,
        ]],
        'subtotal' => 500,
        'delivery_fee' => 50,
        'total' => 550,
        'status' => 'delivered',
        'delivered_at' => now(),
    ]);

    $this->buyerSession = [
        'is_buyer' => true,
        'buyer_account_id' => $this->buyer->id,
        'buyer_email' => $this->buyer->email,
        'buyer_name' => 'Recent Buyer',
    ];
});

it('shows delivered recent-order review and return actions', function (): void {
    $this->withSession($this->buyerSession)
        ->get(route('buyer.recent-orders'))
        ->assertOk()
        ->assertSee('Rate & review')
        ->assertSee('Request return / refund');
});

it('stores a rating only against a delivered product from the owned order', function (): void {
    $this->withSession($this->buyerSession)
        ->post(route('buyer.orders.reviews.store', $this->order), [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Item arrived in good condition.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductReview::query()
        ->where('marketplace_order_id', $this->order->id)
        ->where('seller_product_id', $this->product->id)
        ->where('buyer_account_id', $this->buyer->id)
        ->where('rating', 5)
        ->exists())->toBeTrue();
});

it('creates one return request and prevents duplicate seller notifications', function (): void {
    $payload = [
        'reason' => 'Damaged item',
        'details' => 'The item arrived with visible damage.',
    ];

    $this->withSession($this->buyerSession)
        ->post(route('buyer.orders.return-request', $this->order), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(SellerReturnRequest::query()->where('marketplace_order_id', $this->order->id)->count())->toBe(1)
        ->and(SellerNotification::query()->where('seller_account_id', $this->seller->id)->where('type', 'return_request')->count())->toBe(1)
        ->and(MarketplaceOrderEvent::query()->where('marketplace_order_id', $this->order->id)->where('type', 'return_requested')->count())->toBe(1);

    $this->withSession($this->buyerSession)
        ->post(route('buyer.orders.return-request', $this->order), $payload)
        ->assertSessionHasErrors('return');

    expect(SellerReturnRequest::query()->where('marketplace_order_id', $this->order->id)->count())->toBe(1)
        ->and(SellerNotification::query()->where('seller_account_id', $this->seller->id)->where('type', 'return_request')->count())->toBe(1);
});

it('rejects review and return actions from a different buyer', function (): void {
    $otherBuyer = BuyerAccount::query()->create([
        'last_name' => 'Other',
        'first_name' => 'Buyer',
        'sex' => 'N/A',
        'email' => 'other.buyer@example.test',
        'contact_no' => '09170000002',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '130000000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137404000',
        'municipality_name' => 'Quezon City',
        'barangay_code' => '137404001',
        'barangay_name' => 'Commonwealth',
        'street_address' => 'Other Test Address',
        'password' => Hash::make('Buyer@2026'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    $otherSession = [
        'is_buyer' => true,
        'buyer_account_id' => $otherBuyer->id,
        'buyer_email' => $otherBuyer->email,
        'buyer_name' => 'Other Buyer',
    ];

    $this->withSession($otherSession)
        ->post(route('buyer.orders.reviews.store', $this->order), [
            'product_id' => $this->product->id,
            'rating' => 5,
        ])
        ->assertForbidden();

    $this->withSession($otherSession)
        ->post(route('buyer.orders.return-request', $this->order), [
            'reason' => 'Damaged item',
            'details' => 'Unauthorized request.',
        ])
        ->assertForbidden();

    expect(ProductReview::query()->count())->toBe(0)
        ->and(SellerReturnRequest::query()->count())->toBe(0);
});

