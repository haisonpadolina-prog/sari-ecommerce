<?php
namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\SellerAccount;
use App\Models\SellerSettlement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerFinanceController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $settlements=SellerSettlement::query()
            ->with(['order','commission'])
            ->where('seller_account_id',$seller->id)
            ->latest('eligible_at')
            ->paginate(20);

        $base=SellerSettlement::query()->where('seller_account_id',$seller->id);
        $stats=[
            'gross'=>(float)(clone $base)->sum('merchandise_amount'),
            'commission'=>(float)(clone $base)->sum('platform_commission_amount'),
            'net'=>(float)(clone $base)->sum('seller_net_amount'),
            'pending'=>(float)(clone $base)->whereNull('paid_at')->sum('seller_net_amount'),
            'paid'=>(float)(clone $base)->whereNotNull('paid_at')->sum('seller_net_amount'),
        ];

        return view('seller.finance',compact('seller','settlements','stats'));
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');
        if ($seller instanceof SellerAccount) {
            return $seller;
        }
        return SellerAccount::query()->findOrFail((int) $request->session()->get('seller_account_id'));
    }
}
