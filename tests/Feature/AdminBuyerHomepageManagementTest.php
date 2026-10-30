<?php

namespace Tests\Feature;

use App\Models\Platform\BuyerHomepageBanner;
use App\Services\Buyer\BuyerHomepageBannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBuyerHomepageManagementTest extends TestCase
{
    use RefreshDatabase;

    private function validFields(string $slot = 'hero_1'): array
    {
        return array_merge(BuyerHomepageBannerService::defaults()[$slot], [
            'slot' => $slot,
            'title' => 'SARI Weekend Collection',
            'button_text' => 'Explore deals',
            'button_url' => '/buyer/products?discounted=1',
            'text_x' => 19,
            'text_y' => 36,
            'button_x' => 21,
            'button_y' => 76,
            'text_color' => '#ABCDEF',
        ]);
    }

    public function test_non_admin_cannot_open_or_publish(): void
    {
        $this->get(route('admin.buyer-homepage'))->assertRedirect(route('login'));
        $this->post(route('admin.buyer-homepage.update'), $this->validFields())->assertForbidden();
        $this->assertDatabaseCount('buyer_homepage_banners', 0);
    }

    public function test_admin_can_publish_one_slot_without_changing_other_slots(): void
    {
        $this->withSession(['is_admin' => true])
            ->post(route('admin.buyer-homepage.update'), $this->validFields())
            ->assertRedirect(route('admin.buyer-homepage', ['slot' => 'hero_1']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('buyer_homepage_banners', [
            'slot' => 'hero_1', 'title' => 'SARI Weekend Collection',
            'text_x' => 19, 'button_x' => 21, 'text_color' => '#ABCDEF',
        ]);
        $this->assertDatabaseCount('buyer_homepage_banners', 1);

        $banners = app(BuyerHomepageBannerService::class)->banners();
        $this->assertCount(6, $banners);
        $this->assertSame('SARI Weekend Collection', $banners['hero_1']['title']);
        $this->assertSame('Beauty bestsellers', $banners['side_beauty']['title']);
    }

    public function test_admin_can_upload_and_replace_banner_image(): void
    {
        Storage::fake('public');
        $fields = $this->validFields();
        $fields['image'] = UploadedFile::fake()->image('banner.jpg', 1600, 900);

        $this->withSession(['is_admin' => true])
            ->post(route('admin.buyer-homepage.update'), $fields)
            ->assertRedirect();

        $banner = BuyerHomepageBanner::query()->where('slot', 'hero_1')->firstOrFail();
        $this->assertStringStartsWith('buyer-homepage/', $banner->image_path);
        Storage::disk('public')->assertExists($banner->image_path);

        $fields['image'] = UploadedFile::fake()->image('replacement.png', 1600, 900);
        $this->post(route('admin.buyer-homepage.update'), $fields)->assertRedirect();
        Storage::disk('public')->assertMissing($banner->image_path);
    }

    public function test_invalid_image_and_unsafe_link_cannot_be_published(): void
    {
        $fields = $this->validFields();
        $fields['button_url'] = 'javascript:alert(1)';
        $this->withSession(['is_admin' => true])
            ->post(route('admin.buyer-homepage.update'), $fields)
            ->assertSessionHasErrors('button_url');

        $fields['button_url'] = '/buyer/products';
        $fields['image'] = UploadedFile::fake()->create('payload.svg', 100, 'image/svg+xml');
        $this->post(route('admin.buyer-homepage.update'), $fields)
            ->assertSessionHasErrors('image');
        $this->assertDatabaseCount('buyer_homepage_banners', 0);
    }
}
