<?php

namespace Tests\Feature;

use App\Models\Accounts\AdminAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPlatformCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_open_campaign_management(): void
    {
        $this->get('/admin/promotions')->assertRedirect(route('login'));
    }

    public function test_admin_can_create_published_buyer_campaign(): void
    {
        $admin = AdminAccount::query()->create([
            'name' => 'Campaign Admin',
            'email' => 'campaign-admin@example.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'can_manage_platform_settings' => true,
        ]);

        $response = $this
            ->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
            ->post('/admin/promotions', [
                'name' => '12.12 Mega Sale',
                'description' => 'Buyer-only platform campaign.',
                'state' => 'published',
                'target_scope' => 'all',
                'sale_enabled' => '1',
                'sale_discount_type' => 'percentage',
                'sale_discount_value' => '15',
                'sale_max_discount' => '300',
                'voucher_enabled' => '1',
                'voucher_code' => 'SARI1212',
                'voucher_discount_type' => 'fixed',
                'voucher_discount_value' => '120',
                'voucher_minimum_spend' => '1000',
                'voucher_usage_limit' => '5000',
                'voucher_per_buyer_limit' => '1',
                'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('platform_campaigns', [
            'name' => '12.12 Mega Sale',
            'state' => 'published',
            'target_scope' => 'all',
            'voucher_code' => 'SARI1212',
            'sale_enabled' => 1,
            'voucher_enabled' => 1,
        ]);
    }
}
