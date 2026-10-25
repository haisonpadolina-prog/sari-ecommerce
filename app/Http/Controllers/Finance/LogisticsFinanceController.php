<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Finance\LogisticsEarning;
use App\Services\Finance\WalletService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsFinanceController extends Controller
{
    public function __construct(
        private readonly WalletService $wallets
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        abort_if(
            (bool) $request->session()->get('is_logistics_hub_operator', false),
            403,
            'Finance and company payouts are restricted to the Logistics Manager.'
        );

        $wallet = $this->wallets->wallet('logistics', $logistics->id);

        $earnings = LogisticsEarning::query()
            ->with('order')
            ->where('logistics_account_id', $logistics->id)
            ->latest('eligible_at')
            ->paginate(20);

        $base = LogisticsEarning::query()
            ->where('logistics_account_id', $logistics->id);

        $grossShipping = (float) (clone $base)->sum('gross_shipping_revenue');
        $pickupRiderCost = (float) (clone $base)->sum('pickup_rider_cost');
        $lastMileRiderCost = (float) (clone $base)->sum('last_mile_rider_cost');
        $netLogistics = (float) (clone $base)->sum('net_logistics_amount');
        $pendingRemittance = (float) (clone $base)
            ->where('status', 'pending_remittance')
            ->sum('net_logistics_amount');

        // Canonical keys plus compatibility aliases for older cached/views.
        // Keeping both shapes prevents a finance page 500 during rolling updates.
        $stats = [
            'gross_shipping' => $grossShipping,
            'pickup_rider_cost' => $pickupRiderCost,
            'last_mile_rider_cost' => $lastMileRiderCost,
            'net_logistics' => $netLogistics,
            'pending_remittance' => $pendingRemittance,

            'gross' => $grossShipping,
            'pickup_cost' => $pickupRiderCost,
            'last_mile_cost' => $lastMileRiderCost,
            'net' => $netLogistics,
            'pending' => $pendingRemittance,
        ];

        $payoutRequests = FinancePayoutRequest::query()
            ->where('owner_type', 'logistics')
            ->where('owner_id', $logistics->id)
            ->latest('requested_at')
            ->limit(20)
            ->get();

        $walletEntries = $wallet->entries()
            ->latest('occurred_at')
            ->limit(30)
            ->get();

        return view('logistics.finance', compact(
            'logistics',
            'wallet',
            'earnings',
            'stats',
            'payoutRequests',
            'walletEntries'
        ));
    }
}
