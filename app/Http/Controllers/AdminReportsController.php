<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\RegistrationApplication;
use App\Models\RiderEarning;
use App\Models\SellerSettlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $orders = MarketplaceOrder::query()->get();

        /*
        | Financial metrics come from immutable/derived finance ledgers rather
        | than recomputing money from whichever commission rate is current now.
        |
        | gmv here means merchandise GMV only. Delivery fees are shown
        | separately so they are not silently mixed into seller merchandise.
        */
        $stats = [
            'orders' => $orders->count(),
            'active' => $orders->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'gmv' => (float) SellerSettlement::query()->sum('merchandise_amount'),
            'delivery_fees' => (float) RiderEarning::query()->sum('delivery_fee_amount'),
            'platform_commission' => (float) OrderCommission::query()->sum('net_commission'),
            'seller_net_payable' => (float) SellerSettlement::query()->sum('seller_net_amount'),
            'registrations' => RegistrationApplication::query()->count(),
            'pending_registrations' => RegistrationApplication::query()->where('status', 'pending')->count(),
        ];

        $statusBreakdown = $orders->groupBy('status')->map->count()->sortDesc();
        $recent = $orders->sortByDesc('created_at')->take(12)->values();

        return view('admin.reports', compact('stats', 'statusBreakdown', 'recent'));
    }
}
