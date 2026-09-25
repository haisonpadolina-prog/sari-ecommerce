<?php
namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerVoucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerVoucherController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $vouchers=SellerVoucher::query()
            ->where('seller_account_id',$seller->id)
            ->latest()->paginate(20);
        return view('seller.vouchers',compact('seller','vouchers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $seller=$this->seller($request);
        $validated=$request->validate([
            'code'=>['required','string','max:64','alpha_dash',Rule::unique('seller_vouchers','code')->where(fn($q)=>$q->where('seller_account_id',$seller->id))],
            'name'=>['required','string','max:120'],
            'discount_type'=>['required','in:percentage,fixed'],
            'discount_value'=>['required','numeric','gt:0'],
            'minimum_spend'=>['nullable','numeric','min:0'],
            'maximum_discount'=>['nullable','numeric','min:0'],
            'usage_limit'=>['nullable','integer','min:1'],
            'starts_at'=>['nullable','date'],
            'ends_at'=>['nullable','date','after_or_equal:starts_at'],
        ]);
        if ($validated['discount_type']==='percentage' && (float)$validated['discount_value']>100) {
            return back()->withErrors(['discount_value'=>'Percentage discount cannot exceed 100%.'])->withInput();
        }
        SellerVoucher::create([
            ...$validated,
            'seller_account_id'=>$seller->id,
            'code'=>strtoupper($validated['code']),
            'minimum_spend'=>$validated['minimum_spend'] ?? 0,
            'is_active'=>true,
        ]);
        return back()->with('success','Voucher created.');
    }

    public function toggle(Request $request, SellerVoucher $voucher): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$voucher->seller_account_id===(int)$seller->id,404);
        $voucher->update(['is_active'=>!$voucher->is_active]);
        return back()->with('success','Voucher status updated.');
    }

    public function destroy(Request $request, SellerVoucher $voucher): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$voucher->seller_account_id===(int)$seller->id,404);
        abort_if($voucher->redemptions()->exists(),422,'Used vouchers cannot be deleted. Deactivate them instead.');
        $voucher->delete();
        return back()->with('success','Voucher deleted.');
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
