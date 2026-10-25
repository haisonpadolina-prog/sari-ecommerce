<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Services\Finance\PayoutService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FinancePayoutController extends Controller
{
    public function __construct(
        private readonly PayoutService $payouts
    ) {
    }

    public function sellerStore(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('is_seller'), 403);

        $seller = $request->attributes->get('sellerAccount');

        if (! $seller instanceof SellerAccount) {
            $seller = SellerAccount::query()->findOrFail(
                (int) $request->session()->get('seller_account_id')
            );
        }

        $validated = $this->validatedDestination($request);

        $this->payouts->request(
            'seller',
            $seller->id,
            (float) $validated['amount'],
            $validated['payout_method'],
            $validated['account_name'],
            $validated['account_reference']
        );

        return back()->with(
            'success',
            'Withdrawal request submitted. Funds are reserved until Admin confirms the external payout.'
        );
    }

    public function logisticsStore(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        abort_if(
            (bool) $request->session()->get('is_logistics_hub_operator', false),
            403,
            'Only the Logistics Manager can request a company payout.'
        );

        $validated = $this->validatedDestination($request);

        $this->payouts->request(
            'logistics',
            $logistics->id,
            (float) $validated['amount'],
            $validated['payout_method'],
            $validated['account_name'],
            $validated['account_reference']
        );

        return back()->with(
            'success',
            'Logistics payout request submitted for Admin review.'
        );
    }

    /** @return array{amount:numeric-string|float|int,payout_method:string,account_name:string,account_reference:string} */
    private function validatedDestination(Request $request): array
    {
        return $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'payout_method' => ['required', 'in:gcash,maya,bank,manual_cash'],
            'account_name' => ['required', 'string', 'min:2', 'max:120'],
            'account_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);
    }
}
