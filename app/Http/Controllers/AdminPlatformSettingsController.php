<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\PlatformSettingVersion;
use App\Services\PlatformSettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformSettingsController extends Controller
{
    public function __construct(
        private readonly PlatformSettingsService $settingsService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $settings = $this->settingsService->currentSnapshot();
        $activeVersion = $this->settingsService->activeVersion();
        $scheduledVersion = $this->settingsService->scheduledVersion();
        $auditHistory = $this->settingsService->auditHistory(30);
        $labels = $this->settingsService->labels();

        $admin = $this->currentAdmin($request);
        $canManageSettings = $this->canManage($request, $admin);

        return view('admin.platform-settings', compact(
            'settings',
            'activeVersion',
            'scheduledVersion',
            'auditHistory',
            'labels',
            'admin',
            'canManageSettings'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        $admin = $this->currentAdmin($request);
        abort_unless(
            $this->canManage($request, $admin),
            403,
            'Your Admin account does not have permission to manage platform settings.'
        );

        $adminId = $admin?->id
            ?: (
                $request->session()->get('admin_account_id')
                    ? (int) $request->session()->get('admin_account_id')
                    : null
            );

        if ($request->input('operation') === 'cancel_scheduled') {
            $validated = $request->validate([
                'version_id' => [
                    'required',
                    'integer',
                    'exists:platform_setting_versions,id',
                ],
            ]);

            $version = PlatformSettingVersion::query()
                ->findOrFail((int) $validated['version_id']);

            $this->settingsService->cancelScheduled(
                $version,
                $adminId
            );

            return back()->with(
                'success',
                'Scheduled platform policy cancelled.'
            );
        }

        $validated = $request->validate([
            'commission_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'delivery_fee_per_seller' => [
                'required',
                'numeric',
                'min:0',
                'max:100000',
            ],
            'checkout_enabled' => [
                'required',
                'boolean',
            ],
            'registrations_enabled' => [
                'required',
                'boolean',
            ],
            'max_sellers_per_checkout' => [
                'required',
                'integer',
                'min:0',
                'max:50',
            ],
            'buyer_cancellation_window_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:10080',
            ],
            'seller_settlement_hold_days' => [
                'required',
                'integer',
                'min:0',
                'max:90',
            ],
            'rider_payout_minimum' => [
                'required',
                'numeric',
                'min:0',
                'max:100000',
            ],
            'registration_decision_email_enabled' => [
                'required',
                'boolean',
            ],
            'maintenance_mode' => [
                'required',
                'boolean',
            ],
            'maintenance_message' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'effective_at' => [
                'required',
                'date',
            ],
            'change_reason' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'material_change_confirmed' => [
                'accepted',
            ],
        ]);

        $effectiveAt = CarbonImmutable::parse(
            $validated['effective_at'],
            config('app.timezone')
        );

        /*
         * A time already in the past is interpreted as "apply now". This keeps
         * datetime-local forms robust against a short delay between opening
         * the page and clicking Save.
         */
        if ($effectiveAt->isPast()) {
            $effectiveAt = CarbonImmutable::instance(now());
        }

        $version = $this->settingsService->publish(
            $validated,
            $effectiveAt,
            $adminId,
            $validated['change_reason']
        );

        return back()->with(
            'success',
            $version->isScheduled()
                ? 'Platform policy v' . $version->version_number
                    . ' scheduled for '
                    . $version->effective_at->format('M j, Y h:i A') . '.'
                : 'Platform policy v' . $version->version_number
                    . ' published successfully.'
        );
    }

    private function currentAdmin(Request $request): ?AdminAccount
    {
        $id = (int) $request->session()->get(
            'admin_account_id',
            0
        );

        return $id > 0
            ? AdminAccount::query()->find($id)
            : null;
    }

    private function canManage(
        Request $request,
        ?AdminAccount $admin
    ): bool {
        if (!$request->session()->get('is_admin')) {
            return false;
        }

        /*
         * Backward-compatible fallback for legacy Admin sessions created
         * before admin_account_id was persisted. Real DB-backed Admin
         * accounts are permission checked below.
         */
        if (!$admin) {
            return !$request->session()->get('admin_account_id');
        }

        return $admin->canManagePlatformSettings();
    }
}
