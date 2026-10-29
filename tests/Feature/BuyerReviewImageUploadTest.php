<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\ProductReview;
use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $this->buyer = BuyerAccount::query()->create([
        'last_name' => 'Buyer',
        'first_name' => 'Review',
        'sex' => 'N/A',
        'email' => 'review.images.buyer@example.test',
        'contact_no' => '09170000011',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '130000000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137404000',
        'municipality_name' => 'Quezon City',
        'barangay_code' => '137404001',
        'barangay_name' => 'Commonwealth',
        'street_address' => 'Review Test Address',
        'password' => Hash::make('Buyer@2026'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    $this->seller = SellerAccount::query()->create([
        'email' => 'review.images.seller@example.test',
        'store_name' => 'Review Image Store',
        'account_status' => 'active',
        'registration_status' => 'approved',
    ]);

    $this->product = SellerProduct::query()->create([
        'seller_account_id' => $this->seller->id,
        'name' => 'Review Image Product',
        'category' => 'Test',
        'price' => 500,
        'stock' => 10,
        'moderation_status' => 'approved',
    ]);

    $this->order = MarketplaceOrder::query()->create([
        'order_number' => 'SARI-REVIEW-IMG-0001',
        'seller_account_id' => $this->seller->id,
        'buyer_account_id' => $this->buyer->id,
        'buyer_name' => 'Review Buyer',
        'buyer_phone' => '09170000011',
        'buyer_address' => 'Review Test Address',
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
        'buyer_name' => 'Review Buyer',
    ];
});

it('stores review photos with a delivered owned product review', function (): void {
    $this->withSession($this->buyerSession)
        ->post(route('buyer.orders.reviews.store', $this->order), [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'The actual item matches the listing.',
            'images' => [
                UploadedFile::fake()->image('front.jpg', 800, 800),
                UploadedFile::fake()->image('package.png', 800, 800),
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $review = ProductReview::query()->firstOrFail();

    expect($review->image_paths)->toHaveCount(2);

    foreach ($review->image_paths as $path) {
        Storage::disk('public')->assertExists($path);
    }
});

it('removes only an image already attached to the owned review', function (): void {
    $oldPath = 'product-reviews/' . $this->order->id . '/' . $this->product->id . '/old.jpg';
    Storage::disk('public')->put($oldPath, 'old image');

    ProductReview::query()->create([
        'seller_product_id' => $this->product->id,
        'seller_account_id' => $this->seller->id,
        'marketplace_order_id' => $this->order->id,
        'buyer_account_id' => $this->buyer->id,
        'rating' => 4,
        'comment' => 'Original review.',
        'image_paths' => [$oldPath],
    ]);

    $this->withSession($this->buyerSession)
        ->post(route('buyer.orders.reviews.store', $this->order), [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Updated review.',
            'remove_images' => [$oldPath, 'unrelated/private/file.jpg'],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $review = ProductReview::query()->firstOrFail();

    expect($review->image_paths)->toBeNull();
    Storage::disk('public')->assertMissing($oldPath);
});
