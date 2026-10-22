<?php

use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Registration\RegistrationApplication;
use App\Services\Products\ProductComplianceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('public');
});

function registrationCategorySeller(array $overrides = []): SellerAccount
{
    return SellerAccount::query()->create(array_merge([
        'last_name' => 'Seller',
        'first_name' => 'Category',
        'email' => 'category-seller@example.test',
        'contact_no' => '09123456789',
        'password' => Hash::make('secret123'),
        'store_name' => 'Category Test Store',
        'line_of_business' => 'Home & Living',
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),
    ], $overrides));
}

function registrationCategorySellerSession(SellerAccount $seller): array
{
    return [
        'is_seller' => true,
        'seller_account_id' => $seller->id,
    ];
}

function productCompliancePass(): array
{
    return [
        'flagged' => false,
        'risk' => 'low',
        'score' => 0,
        'reason' => 'Test scan passed.',
        'matched_terms' => [],
        'matched_rules' => [],
        'engine' => 'test-scanner',
        'ai_flagged' => false,
        'ai_decision' => 'allow',
        'ai_policy_category' => 'none',
        'ai_confidence' => 100,
        'ai_reason' => 'Test scan passed.',
        'ai_signals' => [],
        'ai_image_reviewed' => false,
        'ai_text_reviewed' => true,
        'ai_status' => 'completed',
        'ai_response_id' => null,
        'ai_categories' => [],
    ];
}

test('add product shows the category chosen during seller registration as read only', function (): void {
    $seller = registrationCategorySeller();

    $this->withSession(registrationCategorySellerSession($seller))
        ->get(route('seller.products.create'))
        ->assertOk()
        ->assertSee('Home & Living')
        ->assertSee('Selected by you during Seller registration')
        ->assertSee('name="category"', false)
        ->assertSee('type="hidden"', false)
        ->assertDontSee('Choose your selling category');
});

test('product submission always uses seller registration category even when browser sends another category', function (): void {
    $seller = registrationCategorySeller();
    $scan = productCompliancePass();

    $this->mock(ProductComplianceService::class, function ($mock) use ($scan): void {
        $mock->shouldReceive('scan')
            ->once()
            ->withArgs(fn (string $name, ?string $description, string $category, mixed $image): bool =>
                $name === 'Desk Lamp' && $category === 'Home & Living'
            )
            ->andReturn($scan);

        $mock->shouldReceive('record')
            ->once()
            ->with(\Mockery::type(SellerProduct::class), $scan, 'create');
    });

    $this->withSession(registrationCategorySellerSession($seller))
        ->post(route('seller.products.store'), [
            'name' => 'Desk Lamp',
            'category' => 'Electronics', // must be ignored
            'description' => 'Simple lamp for a study desk.',
            'price' => 1000,
            'stock' => 100,
            'has_variants' => 0,
            'discount' => 0,
            'low_stock_threshold' => 5,
            'cash_on_delivery' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('seller.products.index'));

    expect(SellerProduct::query()->where('seller_account_id', $seller->id)->value('category'))
        ->toBe('Home & Living');
});

test('older seller account recovers its original category from its seller registration and saves it back to the account', function (): void {
    $seller = registrationCategorySeller([
        'email' => 'legacy-registration@example.test',
        'line_of_business' => null,
        'registration_application_id' => null,
    ]);

    $application = RegistrationApplication::query()->create([
        'role' => 'seller',
        'last_name' => 'Seller',
        'first_name' => 'Legacy',
        'sex' => 'Male',
        'email' => 'legacy-registration@example.test',
        'contact_no' => '09123456789',
        'birthday' => '1990-01-01',
        'age' => 36,
        'province_code' => '130000000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137404000',
        'municipality_name' => 'Quezon City',
        'barangay_code' => '137404001',
        'barangay_name' => 'Commonwealth',
        'street_address' => 'Test Address',
        'password' => Hash::make('secret123'),
        'business_name' => 'Legacy Store',
        'line_of_business' => 'Fashion',
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    $this->withSession(registrationCategorySellerSession($seller))
        ->get(route('seller.products.create'))
        ->assertOk()
        ->assertSee('Fashion')
        ->assertDontSee('Choose your selling category');

    $seller->refresh();

    expect($seller->line_of_business)->toBe('Fashion')
        ->and((int) $seller->registration_application_id)->toBe((int) $application->id);
});
