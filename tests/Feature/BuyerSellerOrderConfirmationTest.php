<?php

use App\Events\BuyerSellerMessageUpdated;
use App\Events\SellerOrderUpdated;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Event::fake([BuyerSellerMessageUpdated::class, SellerOrderUpdated::class]);
});

function confirmationTestBuyer(string $email): BuyerAccount
{
    $buyer = new BuyerAccount();
    $buyer->forceFill([
        'first_name' => 'Buyer', 'last_name' => 'Example', 'sex' => 'Female',
        'email' => $email, 'contact_no' => '09123456789', 'birthday' => '2000-01-01',
        'age' => 26, 'province_code' => '1339', 'province_name' => 'Metro Manila',
        'municipality_code' => '133900', 'municipality_name' => 'Manila',
        'barangay_code' => '133900001', 'barangay_name' => 'Barangay One',
        'street_address' => 'One Test Street', 'password' => bcrypt('TestPass2026!'),
        'account_status' => 'active', 'approved_at' => now(),
    ]);
    $buyer->save();
    return $buyer;
}

function confirmationTestOrder(BuyerAccount $buyer, SellerAccount $seller): MarketplaceOrder
{
    return MarketplaceOrder::create([
        'order_number' => 'SARI-CONF-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
        'buyer_account_id' => $buyer->id, 'seller_account_id' => $seller->id,
        'buyer_name' => 'Buyer Example', 'buyer_email' => $buyer->email,
        'buyer_phone' => '09123456789', 'buyer_address' => '1 Example Street, Manila',
        'payment_method' => 'COD', 'payment_status' => 'pending',
        'items' => [['name' => 'Blue Shirt', 'qty' => 2, 'price' => 199.50]],
        'subtotal' => 399, 'delivery_fee' => 50, 'total' => 449, 'status' => 'new',
    ]);
}

test('checkout confirmation is created once per seller-specific order', function (): void {
    $seller = SellerAccount::create(['email' => 'sari-confirm-shop@example.test', 'store_name' => 'Test Shop']);
    $buyer = confirmationTestBuyer('buyer-confirm-1@example.test');
    $order = confirmationTestOrder($buyer, $seller);
    $service = app(BuyerSellerOrderConfirmationService::class);

    $service->createForOrder($order);
    $service->createForOrder($order);

    expect(BuyerSellerMessage::query()->where('order_confirmation_order_id', $order->id)->count())->toBe(1);
    $message = BuyerSellerMessage::query()->where('order_confirmation_order_id', $order->id)->firstOrFail();
    expect($message->sender_role)->toBe('seller');
    expect($message->message_type)->toBe('order_confirmation');
    expect($message->order->buyer_account_id)->toBe($buyer->id);
});

test('only the owner can confirm an address and confirmation is idempotent', function (): void {
    $seller = SellerAccount::create(['email' => 'sari-confirm-shop2@example.test', 'store_name' => 'Test Shop']);
    $buyer = confirmationTestBuyer('buyer-confirm-2@example.test');
    $other = confirmationTestBuyer('other-buyer-confirm@example.test');
    $order = confirmationTestOrder($buyer, $seller);
    app(BuyerSellerOrderConfirmationService::class)->createForOrder($order);
    $path = route('buyer.messages.confirm-address', $order);

    $this->withSession(['is_buyer' => true, 'buyer_account_id' => $other->id])
        ->postJson($path)->assertNotFound();
    expect($order->fresh()->delivery_address_confirmed_at)->toBeNull();

    $this->withSession(['is_buyer' => true, 'buyer_account_id' => $buyer->id])
        ->postJson($path)->assertOk()->assertJsonPath('confirmed', true);
    $this->postJson($path)->assertOk()->assertJsonPath('already_confirmed', true);
    expect($order->fresh()->delivery_address_confirmed_at)->not->toBeNull();
    expect(BuyerSellerMessage::query()->where('marketplace_order_id', $order->id)
        ->where('message_type', 'address_confirmed')->count())->toBe(1);
});

test('a buyer thread exposes only their own seller messages', function (): void {
    $seller = SellerAccount::create(['email' => 'sari-confirm-shop3@example.test', 'store_name' => 'Test Shop']);
    $buyer = confirmationTestBuyer('buyer-confirm-3@example.test');
    $other = confirmationTestBuyer('other-buyer-3@example.test');
    $order = confirmationTestOrder($buyer, $seller);
    app(BuyerSellerOrderConfirmationService::class)->createForOrder($order);

    $this->withSession(['is_buyer' => true, 'buyer_account_id' => $other->id])
        ->getJson(route('buyer.messages.thread', $seller))->assertNotFound();

    $this->withSession(['is_buyer' => true, 'buyer_account_id' => $buyer->id])
        ->getJson(route('buyer.messages.thread', $seller))->assertOk()
        ->assertJsonPath('messages.0.order_confirmation.address', '1 Example Street, Manila');
});
