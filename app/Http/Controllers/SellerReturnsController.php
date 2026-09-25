<?php
namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\SellerAccount;
use App\Models\SellerNotification;
use App\Models\SellerReturnEvent;
use App\Models\SellerReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\CommissionService;
use App\Models\OrderCommission;
use App\Models\SellerSettlement;

class SellerReturnsController extends Controller
{
    public function __construct(private readonly CommissionService $commissions) {}

    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $status=(string)$request->query('status','all');
        $query=SellerReturnRequest::query()
            ->with(['order','events'])
            ->where('seller_account_id',$seller->id)
            ->latest();
        if (in_array($status,['requested','approved','rejected','returned','refunded','closed'],true)) {
            $query->where('status',$status);
        } else {
            $status='all';
        }
        $returns=$query->paginate(20)->withQueryString();
        $stats=SellerReturnRequest::query()
            ->where('seller_account_id',$seller->id)
            ->selectRaw("COUNT(*) total")
            ->selectRaw("SUM(CASE WHEN status='requested' THEN 1 ELSE 0 END) requested")
            ->selectRaw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) approved")
            ->selectRaw("SUM(CASE WHEN status='refunded' THEN 1 ELSE 0 END) refunded")
            ->first();
        return view('seller.returns',compact('seller','returns','stats','status'));
    }

    public function review(Request $request, SellerReturnRequest $returnRequest): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$returnRequest->seller_account_id===(int)$seller->id,404);
        $validated=$request->validate([
            'decision'=>['required','in:approve,reject'],
            'seller_response'=>['required','string','max:1200'],
            'approved_amount'=>['nullable','numeric','min:0'],
        ]);
        abort_unless($returnRequest->status==='requested',422,'This return request has already been reviewed.');

        DB::transaction(function() use($returnRequest,$seller,$validated){
            $status=$validated['decision']==='approve' ? 'approved' : 'rejected';
            $approved=$status==='approved'
                ? min((float)($validated['approved_amount'] ?? $returnRequest->requested_amount),(float)$returnRequest->requested_amount)
                : null;
            $returnRequest->update([
                'status'=>$status,
                'approved_amount'=>$approved,
                'seller_response'=>$validated['seller_response'],
                'reviewed_at'=>now(),
            ]);
            SellerReturnEvent::create([
                'seller_return_request_id'=>$returnRequest->id,
                'actor_role'=>'seller','actor_id'=>$seller->id,'status'=>$status,
                'title'=>$status==='approved'?'Return Approved':'Return Rejected',
                'message'=>$validated['seller_response'],
            ]);
        });
        return back()->with('success','Return request reviewed.');
    }

    public function markReturned(Request $request, SellerReturnRequest $returnRequest): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$returnRequest->seller_account_id===(int)$seller->id,404);
        abort_unless($returnRequest->status==='approved',422,'Only approved returns can be marked returned.');
        $returnRequest->update(['status'=>'returned','returned_at'=>now()]);
        SellerReturnEvent::create([
            'seller_return_request_id'=>$returnRequest->id,'actor_role'=>'seller','actor_id'=>$seller->id,
            'status'=>'returned','title'=>'Returned Item Received','message'=>'Seller confirmed receipt of the returned item.',
        ]);
        return back()->with('success','Returned item recorded.');
    }

    public function refund(Request $request, SellerReturnRequest $returnRequest): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$returnRequest->seller_account_id===(int)$seller->id,404);
        abort_unless($returnRequest->status==='returned',422,'The returned item must be received before recording a refund.');

        DB::transaction(function() use($returnRequest,$seller){
            $locked=SellerReturnRequest::query()->whereKey($returnRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status==='returned',422,'Return status changed. Refresh and try again.');
            $amount=(float)($locked->approved_amount ?? $locked->requested_amount);
            PaymentTransaction::firstOrCreate(
                ['idempotency_key'=>'seller-return-refund-'.$locked->id],
                [
                    'marketplace_order_id'=>$locked->marketplace_order_id,
                    'provider'=>'internal-ledger',
                    'provider_reference'=>'RETURN-'.$locked->id,
                    'channel'=>'seller-return',
                    'type'=>'refund',
                    'amount'=>$amount,
                    'currency'=>'PHP',
                    'status'=>'recorded',
                    'refunded_at'=>now(),
                    'metadata'=>[
                        'seller_return_request_id'=>$locked->id,
                        'note'=>'Internal refund ledger only; no external payment-provider transfer is implied.',
                    ],
                ]
            );

            $order=$locked->order()->with(['commission','sellerSettlement'])->firstOrFail();
            $commission=$order->commission;
            $settlement=$order->sellerSettlement;

            if ($commission && (float)$commission->net_commission > 0 && (float)$order->subtotal > 0) {
                $ratio=min(1, $amount / (float)$order->subtotal);
                $commissionReduction=round(-1 * min(
                    (float)$commission->net_commission,
                    (float)$commission->gross_commission * $ratio
                ), 2);

                if ($commissionReduction < 0) {
                    $this->commissions->addAdjustment(
                        $commission,
                        $commissionReduction,
                        'buyer_refund',
                        'Commission reduced because a Seller-approved Buyer refund was recorded.',
                        null,
                        'RETURN-'.$locked->id,
                        ['seller_return_request_id'=>$locked->id]
                    );
                }
            }

            if ($settlement) {
                $settlement->refresh();
                $newMerchandise=round(max(0,(float)$settlement->merchandise_amount-$amount),2);
                $newNet=round(max(
                    0,
                    $newMerchandise
                        - (float)$settlement->platform_commission_amount
                        - (float)$settlement->withholding_tax_amount
                ),2);
                $settlement->forceFill([
                    'merchandise_amount'=>$newMerchandise,
                    'seller_net_amount'=>$newNet,
                    'status'=>$newMerchandise <= 0 ? 'refunded' : $settlement->status,
                ])->save();
            }

            $locked->update(['status'=>'refunded','refunded_at'=>now()]);
            SellerReturnEvent::create([
                'seller_return_request_id'=>$locked->id,'actor_role'=>'seller','actor_id'=>$seller->id,
                'status'=>'refunded','title'=>'Refund Recorded',
                'message'=>'Refund was recorded in the internal payment ledger.',
            ]);
        });

        return back()->with('success','Refund recorded in the internal ledger.');
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
