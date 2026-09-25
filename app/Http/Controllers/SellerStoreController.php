<?php
namespace App\Http\Controllers;

use App\Models\SellerAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerStoreController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        return view('seller.store',compact('seller'));
    }
    public function update(Request $request): RedirectResponse
    {
        $seller=$this->seller($request);
        $validated=$request->validate([
            'store_name'=>['required','string','max:150'],
            'store_description'=>['nullable','string','max:2000'],
            'store_phone'=>['nullable','string','max:40'],
            'store_public_email'=>['nullable','email','max:255'],
            'store_status'=>['required','in:open,paused'],
            'pickup_instructions'=>['nullable','string','max:1200'],
            'street_address'=>['nullable','string','max:1000'],
        ]);
        $seller->forceFill($validated)->save();
        return back()->with('success','Store information updated.');
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
