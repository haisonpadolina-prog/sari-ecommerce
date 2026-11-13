<?php

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductImage;
use App\Models\Catalog\SellerProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $this->seller = SellerAccount::query()->create([
        'email' => 'compliance.media.seller@example.test',
        'store_name' => 'Compliance Media Seller',
        'account_status' => 'active',
        'registration_status' => 'approved',
        'store_status' => 'open',
    ]);

    $this->product = SellerProduct::query()->create([
        'seller_account_id' => $this->seller->id,
        'name' => 'Media review shirt',
        'category' => 'Fashion',
        'price' => 480,
        'stock' => 10,
        'moderation_status' => 'pending',
    ]);
});

it('displays seller-uploaded cover, gallery, variant and size guide in the Admin review queue', function (): void {
    $disk = Storage::disk('public');
    $disk->put('seller-products/cover.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $disk->put('seller-products/gallery.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $disk->put('seller-products/variant.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $disk->put('seller-products/size-guides/guide.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));

    $this->product->update([
        'image_path' => 'seller-products/cover.png',
        'size_guide_image_path' => 'seller-products/size-guides/guide.png',
    ]);

    $gallery = SellerProductImage::query()->create([
        'seller_product_id' => $this->product->id,
        'path' => 'seller-products/gallery.png',
        'sort_order' => 0,
    ]);

    $variant = SellerProductVariant::query()->create([
        'seller_product_id' => $this->product->id,
        'sku' => 'SHIRT-BLUE-M',
        'option_values' => ['Color' => 'Blue', 'Size' => 'M'],
        'price' => 480,
        'stock' => 5,
        'image_path' => 'seller-products/variant.png',
        'is_active' => true,
    ]);

    $response = $this->withSession(['is_admin' => true])
        ->get(route('admin.seller-compliance'))
        ->assertOk();

    $response
        ->assertSee(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover'], false))
        ->assertSee(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'gallery', 'media' => $gallery->id], false))
        ->assertSee(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'variant', 'media' => $variant->id], false))
        ->assertSee(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'size-guide'], false))
        ->assertSee('Gallery 1')
        ->assertSee('Variant: SHIRT-BLUE-M')
        ->assertSee('Size guide')
        ->assertSee('Uploaded images');

    $this->withSession(['is_admin' => true])
        ->get(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'size-guide']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    $galleryResponse = $this->withSession(['is_admin' => true])
        ->get(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'gallery', 'media' => $gallery->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    // A successful status alone is insufficient: the bytes must be a real PNG.
    expect(substr($galleryResponse->streamedContent(), 0, 8))
        ->toBe("\x89PNG\r\n\x1a\n");
});

it('uses the first existing gallery photo as the preview when cover is missing', function (): void {
    Storage::disk('public')->put('seller-products/gallery-only.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $this->product->update(['image_path' => 'seller-products/missing.png']);

    SellerProductImage::query()->create([
        'seller_product_id' => $this->product->id,
        'path' => 'seller-products/gallery-only.png',
        'sort_order' => 0,
    ]);

    $this->withSession(['is_admin' => true])
        ->get(route('admin.seller-compliance'))
        ->assertOk()
        ->assertSee(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover'], false));

    $this->withSession(['is_admin' => true])
        ->get(route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('shows an honest missing-image state instead of broken image links', function (): void {
    $this->product->update(['image_path' => 'seller-products/not-here.png']);

    $response = $this->withSession(['is_admin' => true])
        ->get(route('admin.seller-compliance'))
        ->assertOk();

    $response->assertSee('No uploaded images available for review.');
    $response->assertDontSee('src="' . route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover'], false) . '"', false);
});

it('restricts size guide access to Admin and the owning Seller', function (): void {
    Storage::disk('public')->put('seller-products/size-guides/review.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $this->product->update(['size_guide_image_path' => 'seller-products/size-guides/review.png']);

    $otherSeller = SellerAccount::query()->create([
        'email' => 'compliance.other.seller@example.test',
        'store_name' => 'Other Seller',
    ]);

    $route = route('seller.products.size-guide', $this->product);

    // The web middleware redirects unauthenticated browser requests;
    // a JSON request receives a 401 before the controller is reached.
    $this->get($route)->assertRedirect(route('login'));
    $this->getJson($route)->assertUnauthorized();
    $this->withSession(['is_seller' => true, 'seller_account_id' => $otherSeller->id])
        ->get($route)->assertForbidden();
    $this->withSession(['is_seller' => true, 'seller_account_id' => $this->seller->id])
        ->get($route)->assertOk();

    // Admin media uses its own /admin route. The /seller route remains
    // Seller-area-only under PreventAuthenticatedPageCaching middleware.
    $this->withSession(['is_seller' => false, 'seller_account_id' => null, 'is_admin' => true])
        ->get(route('admin.compliance.products.media', [
            'product' => $this->product, 'kind' => 'size-guide',
        ]))->assertOk()->assertHeader('Content-Type', 'image/png');
});


it('only allows Admin to fetch pending listing image bytes', function (): void {
    Storage::disk('public')->put('seller-products/cover.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg=='));
    $this->product->update(['image_path' => 'seller-products/cover.png']);
    $url = route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover']);

    $this->get($url)->assertRedirect(route('login'));
    $this->getJson($url)->assertUnauthorized();
    // A Seller cannot cross the /admin route boundary even for their own product.
    $this->withSession(['is_seller' => true, 'seller_account_id' => $this->seller->id])
        ->get($url)->assertRedirect(route('login'));
    $this->withSession(['is_seller' => false, 'seller_account_id' => null, 'is_admin' => true])
        ->get($url)
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('never serves an image belonging to another product', function (): void {
    $otherProduct = SellerProduct::query()->create([
        'seller_account_id' => $this->seller->id,
        'name' => 'Other product',
        'category' => 'Fashion',
        'price' => 100,
        'stock' => 2,
        'moderation_status' => 'pending',
    ]);
    $gallery = SellerProductImage::query()->create([
        'seller_product_id' => $otherProduct->id,
        'path' => 'seller-products/unrelated.png',
        'sort_order' => 0,
    ]);
    $url = route('admin.compliance.products.media', [
        'product' => $this->product, 'kind' => 'gallery', 'media' => $gallery->id,
    ]);
    $this->withSession(['is_admin' => true])->get($url)->assertNotFound();
});

it('returns an explicit error for a stored file that is not a real image', function (): void {
    Storage::disk('public')->put('seller-products/not-an-image.png', 'not real image bytes');
    $this->product->update(['image_path' => 'seller-products/not-an-image.png']);
    $url = route('admin.compliance.products.media', ['product' => $this->product, 'kind' => 'cover']);
    $this->withSession(['is_admin' => true])->get($url)->assertStatus(415);
});

it('detects image content by bytes rather than filename extension', function (): void {
    Storage::disk('public')->put(
        'seller-products/mislabeled.jpg',
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg==')
    );
    $this->product->update(['image_path' => 'seller-products/mislabeled.jpg']);

    $this->withSession(['is_admin' => true])
        ->get(route('admin.compliance.products.media', [
            'product' => $this->product, 'kind' => 'cover',
        ]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});
