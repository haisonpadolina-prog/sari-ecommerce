<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Services\BuyerCatalogService;
use App\Services\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerPageController extends Controller
{
    public function __construct(
        private readonly BuyerCatalogService $catalog,
        private readonly BuyerIdentityService $identity,
    ) {
    }

    public function home(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $buyerAccount = $this->identity->account($request);

        return view('buyer.home', [
            'categories' => $this->catalog->homeCategories(),
            'featuredProducts' => $this->catalog->catalog(8),
            'buyerAccount' => $buyerAccount,
        ]);
    }

    public function rewards(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $orders = $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->get();

        $spent = (float) $orders->sum('total');
        $points = (int) floor($spent * (float) config('sari_buyer.reward_points_per_peso', 0.10));

        return view('buyer.rewards', compact('orders', 'spent', 'points'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'is_buyer',
            'buyer_account_id',
            'buyer_social_account_id',
            'buyer_email',
            'buyer_name',
            'buyer_avatar',
            'auth_provider',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
