<?php

namespace App\Http\Controllers;

use App\Models\BuyerAccount;
use App\Services\BuyerCatalogService;
use Illuminate\Http\Request;

class BuyerPageController extends Controller
{
    public function __construct(
        private readonly BuyerCatalogService $catalog
    ) {
    }

    public function home(Request $request)
    {
        if (!$this->isBuyer($request)) {
            return redirect()->route('login');
        }

        $buyerAccount = $this->syncBuyerSession($request);

        return view('buyer.home', [
            'categories' => $this->catalog->homeCategories(),
            'featuredProducts' => $this->catalog->catalog(8),
            'buyerAccount' => $buyerAccount,
        ]);
    }

    public function cart(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.cart');
    }

    public function checkout(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.checkout');
    }

    public function orders(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.orders');
    }

    public function messages(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.messages');
    }

    public function account(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.account');
    }

    public function rewards(Request $request)
    {
        return $this->renderBuyerView($request, 'buyer.rewards');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'is_buyer',
            'buyer_account_id',
            'buyer_social_account_id',
            'buyer_email',
            'buyer_name',
            'buyer_avatar',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function renderBuyerView(Request $request, string $view)
    {
        if (!$this->isBuyer($request)) {
            return redirect()->route('login');
        }

        return view($view, [
            'buyerAccount' => $this->syncBuyerSession($request),
        ]);
    }

    private function isBuyer(Request $request): bool
    {
        return (bool) $request->session()->get('is_buyer');
    }

    private function syncBuyerSession(Request $request): ?BuyerAccount
    {
        $buyerId = (int) $request->session()->get('buyer_account_id', 0);

        if ($buyerId <= 0) {
            return null;
        }

        $buyer = BuyerAccount::query()->find($buyerId);

        if (!$buyer || $buyer->account_status !== 'active') {
            return null;
        }

        $buyerName = trim($buyer->first_name . ' ' . $buyer->last_name);

        $request->session()->put([
            'buyer_email' => $buyer->email,
            'buyer_name' => $buyerName !== '' ? $buyerName : 'SARI Buyer',
        ]);

        return $buyer;
    }
}
