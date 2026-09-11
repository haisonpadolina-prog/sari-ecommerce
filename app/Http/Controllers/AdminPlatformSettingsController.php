<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformSettingsController extends Controller
{
    public function __construct(
        private readonly CommissionService $commissions
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $settings = [
            'commission_rate' => (float) $this->commissions->currentRate()->rate_percent,
            'delivery_fee_per_seller' => (float) PlatformSetting::valueOf(
                'delivery_fee_per_seller',
                config('sari_buyer.delivery_fee_per_seller', 80)
            ),
        ];

        return view('admin.platform-settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'delivery_fee_per_seller' => ['required', 'numeric', 'min:0', 'max:100000'],
        ]);

        $this->commissions->setCurrentRate(
            (float) $validated['commission_rate'],
            $request->session()->get('admin_account_id')
                ? (int) $request->session()->get('admin_account_id')
                : null,
            'Commission rate updated from Admin Platform Settings.'
        );

        PlatformSetting::putValue(
            'delivery_fee_per_seller',
            $validated['delivery_fee_per_seller']
        );

        return back()->with('success', 'Platform settings saved.');
    }
}
