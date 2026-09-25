<?php
namespace App\Http\Controllers;

use App\Models\ComplianceMessage;
use App\Models\SellerAccount;
use App\Models\SellerWarning;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerComplianceCenterController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $warnings=SellerWarning::query()->where('seller_account_id',$seller->id)->latest()->get();
        $messages=ComplianceMessage::query()->where('seller_account_id',$seller->id)->oldest()->get();
        ComplianceMessage::query()
            ->where('seller_account_id',$seller->id)
            ->where('sender_role','admin')
            ->whereNull('read_at')
            ->update(['read_at'=>now()]);
        return view('seller.compliance-center',compact('seller','warnings','messages'));
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
