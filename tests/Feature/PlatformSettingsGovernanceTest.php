<?php

namespace Tests\Feature;

use App\Models\AdminAccount;
use App\Models\PlatformSetting;
use App\Models\PlatformSettingAudit;
use App\Models\PlatformSettingVersion;
use App\Services\PlatformSettingsService;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlatformSettingsGovernanceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): AdminAccount
    {
        return AdminAccount::query()->create([
            'name' => 'Settings Admin',
            'email' => 'settings-admin@example.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'can_manage_platform_settings' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_immediate_policy_creates_version_and_audit_rows(): void
    {
        $admin = $this->admin();
        $service = app(PlatformSettingsService::class);
        $settings = $service->currentSnapshot();

        $settings['delivery_fee_per_seller'] = 95.00;
        $settings['max_sellers_per_checkout'] = 4;

        $version = $service->publish(
            $settings,
            now(),
            $admin->id,
            'Update delivery operations for the current service period.'
        );

        $this->assertSame(1, $version->version_number);
        $this->assertSame('published', $version->status);
        $this->assertSame(
            95.0,
            (float) PlatformSetting::valueOf(
                'delivery_fee_per_seller',
                0
            )
        );

        $this->assertDatabaseHas('platform_setting_audits', [
            'platform_setting_version_id' => $version->id,
            'setting_key' => 'delivery_fee_per_seller',
            'action' => 'setting_published',
        ]);

        $this->assertDatabaseHas('platform_setting_audits', [
            'platform_setting_version_id' => $version->id,
            'setting_key' => 'max_sellers_per_checkout',
            'action' => 'setting_published',
        ]);
    }

    public function test_future_policy_activates_by_effective_time_without_overwriting_current_value_early(): void
    {
        $admin = $this->admin();
        $service = app(PlatformSettingsService::class);
        $settings = $service->currentSnapshot();

        // Freeze only after currentSnapshot() has guaranteed a commission
        // rate that is already effective at the database's current time.
        $baseTime = now()->addSecond();
        Carbon::setTestNow($baseTime);

        $currentFee = (float) $settings['delivery_fee_per_seller'];
        $futureFee = $currentFee + 25;

        $settings['delivery_fee_per_seller'] = $futureFee;

        $version = $service->publish(
            $settings,
            now()->addDay(),
            $admin->id,
            'Schedule the next delivery fee policy for tomorrow.'
        );

        $this->assertSame('scheduled', $version->status);
        $this->assertSame(
            $currentFee,
            (float) PlatformSetting::valueOf(
                'delivery_fee_per_seller',
                $currentFee
            )
        );

        // Advance relative to the actual scheduled effective_at value.
        // Do not hard-code a wall-clock time because the test database/app
        // timezone may initialize the baseline later in the day.
        Carbon::setTestNow(
            $version->effective_at->copy()->addMinute()
        );

        $this->assertSame(
            $futureFee,
            (float) PlatformSetting::valueOf(
                'delivery_fee_per_seller',
                0
            )
        );
    }

    public function test_scheduled_policy_can_be_cancelled_before_effective_time(): void
    {
        $admin = $this->admin();
        $service = app(PlatformSettingsService::class);
        $settings = $service->currentSnapshot();

        $baseTime = now()->addSecond();
        Carbon::setTestNow($baseTime);

        $settings['rider_payout_minimum'] = 500;

        $version = $service->publish(
            $settings,
            now()->addHours(4),
            $admin->id,
            'Schedule a minimum rider payout for the next operating shift.'
        );

        $service->cancelScheduled(
            $version,
            $admin->id,
            'Cancelled during policy review.'
        );

        $this->assertDatabaseHas('platform_setting_versions', [
            'id' => $version->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('platform_setting_audits', [
            'platform_setting_version_id' => $version->id,
            'action' => 'scheduled_policy_cancelled',
        ]);
    }

    public function test_second_policy_is_rejected_while_future_policy_is_pending(): void
    {
        $admin = $this->admin();
        $service = app(PlatformSettingsService::class);
        $settings = $service->currentSnapshot();

        $baseTime = now()->addSecond();
        Carbon::setTestNow($baseTime);

        $first = $settings;
        $first['registrations_enabled'] = false;

        $service->publish(
            $first,
            now()->addHours(2),
            $admin->id,
            'Pause new registrations later today for scheduled maintenance.'
        );

        $second = $settings;
        $second['checkout_enabled'] = false;

        $this->expectException(ValidationException::class);

        $service->publish(
            $second,
            now(),
            $admin->id,
            'Attempt another policy before cancelling the scheduled version.'
        );
    }
}
