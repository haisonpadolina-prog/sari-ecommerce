<?php

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductDraft;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->seller = SellerAccount::query()->create([
        'email' => 'sizeguide.seller@example.test',
        'store_name' => 'Size Guide Seller',
        'account_status' => 'active',
        'registration_status' => 'approved',
        'store_status' => 'open',
    ]);
    $this->product = SellerProduct::query()->create([
        'seller_account_id' => $this->seller->id,
        'name' => 'Cotton shirt',
        'category' => 'Fashion',
        'price' => 350,
        'stock' => 10,
        'moderation_status' => 'approved',
    ]);
    $this->sellerSession = ['is_seller' => true, 'seller_account_id' => $this->seller->id];
    $this->buyerSession = ['is_buyer' => true, 'buyer_account_id' => 10];
});

it('saves, serves and explicitly removes a draft size guide independently of gallery images', function (): void {
    // Real 2x2 PNG; no GD image-generation extension required.
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGP8//8/AwMDEwMDAwMDAwAkBgMB/DXemwAAAABJRU5ErkJggg==');

    $response = $this->withSession($this->sellerSession)->postJson(route('seller.products.draft.save'), [
        'name' => 'Cotton shirt draft',
        'size_guide_image' => UploadedFile::fake()->createWithContent('size-chart.png', $png),
    ])->assertOk()->assertJsonPath('ok', true);

    $draftId = (int) $response->json('draft.id');
    $draft = SellerProductDraft::query()->findOrFail($draftId);
    expect($draft->size_guide_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($draft->size_guide_image_path);

    $this->withSession($this->sellerSession)
        ->get(route('seller.products.draft-media', [
            'kind' => 'size-guide', 'key' => 'chart', 'draft_id' => $draftId,
        ]))->assertOk();

    $oldPath = $draft->size_guide_image_path;
    $this->withSession($this->sellerSession)->postJson(route('seller.products.draft.save'), [
        'draft_id' => $draftId,
        'remove_size_guide_image' => '1',
    ])->assertOk()->assertJsonPath('draft.size_guide_image_url', null);

    expect($draft->fresh()->size_guide_image_path)->toBeNull();
    Storage::disk('public')->assertMissing($oldPath);
});

it('rejects non-image size guide uploads', function (): void {
    $this->withSession($this->sellerSession)->postJson(route('seller.products.draft.save'), [
        'size_guide_image' => UploadedFile::fake()->create('size-chart.pdf', 10, 'application/pdf'),
    ])->assertUnprocessable()->assertJsonValidationErrors('size_guide_image');
});

it('serves size charts only to buyers while the product is approved and available', function (): void {
    Storage::disk('public')->put('seller-products/size-guides/test.png', 'test image');
    $this->product->update(['size_guide_image_path' => 'seller-products/size-guides/test.png']);
    $route = route('buyer.product.size-guide', ['product' => $this->product->id]);

    $this->get($route)->assertForbidden();
    $this->withSession($this->buyerSession)->get($route)->assertOk();

    $this->product->update(['moderation_status' => 'pending']);
    $this->withSession($this->buyerSession)->get($route)->assertNotFound();
});

it('does not allow a different seller to view an unpublished product size guide', function (): void {
    Storage::disk('public')->put('seller-products/size-guides/test.png', 'test image');
    $this->product->update(['size_guide_image_path' => 'seller-products/size-guides/test.png']);

    $other = SellerAccount::query()->create([
        'email' => 'sizeguide.other@example.test',
        'store_name' => 'Another Seller',
        'account_status' => 'active',
        'registration_status' => 'approved',
    ]);
    $this->withSession(['is_seller' => true, 'seller_account_id' => $other->id])
        ->get(route('seller.products.size-guide', ['product' => $this->product->id]))
        ->assertForbidden();
});
