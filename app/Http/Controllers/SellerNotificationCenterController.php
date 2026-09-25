<?php
namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerNotificationCenterController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $notifications=SellerNotification::query()
            ->where('seller_account_id',$seller->id)
            ->latest()->paginate(30);
        $unread=SellerNotification::query()->where('seller_account_id',$seller->id)->whereNull('read_at')->count();
        return view('seller.notifications',compact('seller','notifications','unread'));
    }
    public function read(Request $request, SellerNotification $notification): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$notification->seller_account_id===(int)$seller->id,404);
        $notification->update(['read_at'=>now()]);
        return back();
    }
    public function readAll(Request $request): RedirectResponse
    {
        $seller=$this->seller($request);
        SellerNotification::query()->where('seller_account_id',$seller->id)->whereNull('read_at')->update(['read_at'=>now()]);
        return back()->with('success','Notifications marked as read.');
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
