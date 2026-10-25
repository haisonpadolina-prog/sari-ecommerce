<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Finance\FinanceWallet;
use App\Models\Finance\OrderCommission;
use App\Models\Platform\PlatformSetting;
use App\Services\Finance\PayoutService;
use App\Services\Finance\ShippingEarningService;
use App\Services\Finance\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFinanceController extends Controller
{
    public function __construct(
        private readonly WalletService $wallets,
        private readonly PayoutService $payouts
    ) {
    }

    public function index(Request $request): View
    {
        $admin = $this->treasuryAdmin($request);
        $platformWallet = $this->wallets->platformWallet();

        $payoutRequests = FinancePayoutRequest::query()
            ->with(['wallet', 'reviewedByAdmin'])
            ->latest('requested_at')
            ->paginate(25);

        $payoutRequests->setCollection(
            $payoutRequests->getCollection()->map(function (FinancePayoutRequest $payout): FinancePayoutRequest {
                $payout->setAttribute('owner_label', $this->ownerLabel($payout));

                return $payout;
            })
        );

        $walletStats = [
            'wallet_count' => FinanceWallet::query()->count(),
            'available' => (float) FinanceWallet::query()->sum('available_balance'),
            'reserved' => (float) FinanceWallet::query()->sum('reserved_balance'),
            'lifetime_credits' => (float) FinanceWallet::query()->sum('lifetime_credits'),
            'lifetime_payouts' => (float) FinanceWallet::query()->sum('lifetime_payouts'),
        ];

        $payoutStats = [
            'pending' => (float) FinancePayoutRequest::query()->where('status', 'pending')->sum('amount'),
            'approved' => (float) FinancePayoutRequest::query()->where('status', 'approved')->sum('amount'),
            'paid' => (float) FinancePayoutRequest::query()->where('status', 'paid')->sum('amount'),
        ];

        $remittanceStats = [
            'awaiting' => (float) CodRemittanceBatch::query()
                ->whereIn('status', ['submitted', 'discrepancy'])
                ->sum('expected_amount'),
            'verified' => (float) CodRemittanceBatch::query()
                ->where('status', 'verified')
                ->sum('expected_amount'),
            'discrepancy_count' => CodRemittanceBatch::query()
                ->where('status', 'discrepancy')
                ->count(),
        ];

        $commissionStats = [
            'earned' => (float) OrderCommission::query()->sum('net_commission'),
            'orders' => OrderCommission::query()->count(),
        ];

        $platformEntries = $platformWallet->entries()
            ->latest('occurred_at')
            ->limit(30)
            ->get();

        $shippingPolicy = [
            'pickup_percent' => (float) PlatformSetting::valueOf(
                'shipping_pickup_rider_percent',
                ShippingEarningService::PICKUP_PERCENT_DEFAULT
            ),
            'last_mile_percent' => (float) PlatformSetting::valueOf(
                'shipping_last_mile_rider_percent',
                ShippingEarningService::LAST_MILE_PERCENT_DEFAULT
            ),
        ];
        $shippingPolicy['logistics_percent'] = round(
            max(0, 100 - $shippingPolicy['pickup_percent'] - $shippingPolicy['last_mile_percent']),
            4
        );

        return view('admin.finance', compact(
            'admin',
            'platformWallet',
            'platformEntries',
            'walletStats',
            'payoutStats',
            'remittanceStats',
            'commissionStats',
            'payoutRequests',
            'shippingPolicy'
        ));
    }

    public function updateShippingPolicy(Request $request): RedirectResponse
    {
        $this->treasuryAdmin($request);

        $validated = $request->validate([
            'pickup_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'last_mile_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $pickup = round((float) $validated['pickup_percent'], 4);
        $lastMile = round((float) $validated['last_mile_percent'], 4);

        if ($pickup + $lastMile > 100) {
            return back()->withErrors([
                'shipping_policy' => 'Pickup Rider and Final-Mile Rider shares cannot exceed 100% combined.',
            ])->withInput();
        }

        PlatformSetting::putValue('shipping_pickup_rider_percent', $pickup);
        PlatformSetting::putValue('shipping_last_mile_rider_percent', $lastMile);

        return back()->with(
            'success',
            'Shipping earnings policy updated. Logistics receives the remaining '.
                number_format(100 - $pickup - $lastMile, 2).'%.'
        );
    }

    public function requestPlatformPayout(Request $request): RedirectResponse
    {
        $admin = $this->admin($request);
        abort_unless($admin->canManagePlatformSettings(), 403, 'Platform treasury access requires an authorized Admin.');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'payout_method' => ['required', 'in:gcash,maya,bank,manual_cash'],
            'account_name' => ['required', 'string', 'min:2', 'max:120'],
            'account_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);

        $this->payouts->request(
            WalletService::PLATFORM_OWNER_TYPE,
            WalletService::PLATFORM_OWNER_ID,
            (float) $validated['amount'],
            $validated['payout_method'],
            $validated['account_name'],
            $validated['account_reference']
        );

        return back()->with('success', 'Platform treasury payout request created. Funds are now reserved.');
    }

    public function approve(Request $request, FinancePayoutRequest $payout): RedirectResponse
    {
        $admin = $this->treasuryAdmin($request);
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->payouts->approve($payout, $admin->id, $validated['admin_note'] ?? null);

        return back()->with('success', 'Payout request approved. No external transfer is claimed yet.');
    }

    public function reject(Request $request, FinancePayoutRequest $payout): RedirectResponse
    {
        $admin = $this->treasuryAdmin($request);
        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $this->payouts->reject($payout, $admin->id, $validated['admin_note']);

        return back()->with('success', 'Payout rejected and the reserved balance was returned to the wallet.');
    }

    public function markPaid(Request $request, FinancePayoutRequest $payout): RedirectResponse
    {
        $admin = $this->treasuryAdmin($request);
        $validated = $request->validate([
            'external_reference' => ['required', 'string', 'min:3', 'max:191'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->payouts->markPaid(
            $payout,
            $admin->id,
            $validated['external_reference'],
            $validated['admin_note'] ?? null
        );

        return back()->with('success', 'External payout confirmed and the reserved wallet balance was closed.');
    }

    private function admin(Request $request): AdminAccount
    {
        abort_unless($request->session()->get('is_admin'), 403, 'Admin session required.');

        $id = (int) $request->session()->get('admin_account_id', 0);
        abort_if($id <= 0, 403, 'Database-backed Admin account required.');

        return AdminAccount::query()->findOrFail($id);
    }

    private function treasuryAdmin(Request $request): AdminAccount
    {
        $admin = $this->admin($request);

        abort_unless(
            $admin->canManagePlatformSettings(),
            403,
            'Treasury payout approval requires an authorized Admin.'
        );

        return $admin;
    }

    private function ownerLabel(FinancePayoutRequest $payout): string
    {
        return match ($payout->owner_type) {
            'seller' => SellerAccount::query()->find($payout->owner_id)?->store_name ?: 'Seller #'.$payout->owner_id,
            'logistics' => LogisticsAccount::query()->find($payout->owner_id)?->displayName() ?: 'Logistics #'.$payout->owner_id,
            'courier' => optional(CourierAccount::query()->find($payout->owner_id), function (CourierAccount $rider): string {
                $name = trim($rider->first_name.' '.$rider->last_name);

                return $name !== '' ? $name : 'Rider #'.$rider->id;
            }) ?: 'Rider #'.$payout->owner_id,
            WalletService::PLATFORM_OWNER_TYPE => 'SARI Platform Treasury',
            default => ucfirst($payout->owner_type).' #'.$payout->owner_id,
        };
    }
}
