<?php

namespace App\Http\Controllers;

use App\Models\SellerAccount;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->get('is_seller')) {
            return redirect()->route('login');
        }

        $sellerAccount = $this->resolveSeller($request);
        $sellerAccount->refreshSuspensionStatus();
        $sellerAccount->ensureRealtimeToken();

        $products = $sellerAccount->products()
            ->whereNull('archived_at')
            ->with('latestVersion')
            ->latest()
            ->get();

        $complianceMessages = $sellerAccount->complianceMessages()
            ->latest()
            ->take(8)
            ->get()
            ->reverse()
            ->values();

        return view('seller.dashboard', compact(
            'sellerAccount',
            'products',
            'complianceMessages'
        ));
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        $sellerId = $request->session()->get('seller_account_id');

        if ($sellerId) {
            $seller = SellerAccount::find($sellerId);

            if ($seller) {
                return $seller;
            }
        }

        $seller = SellerAccount::firstOrCreate(
            ['email' => 'seller@gmail.com'],
            ['store_name' => 'SARI Seller Store']
        );

        $seller->ensureRealtimeToken();
        $request->session()->put('seller_account_id', $seller->id);

        return $seller;
    }
}
