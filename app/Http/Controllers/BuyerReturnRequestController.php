<?php
namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\SellerNotification;
use App\Models\SellerReturnEvent;
use App\Models\SellerReturnRequest;
use App\Services\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerReturnRequestController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity) {}

    public function store(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        $this->guardOwned($request,$order);
        abort_unless($order->status==='delivered',422,'Only delivered orders can be returned.');

        $validated=$request->validate([
            'reason'=>['required','string','max:120'],
            'details'=>['nullable','string','max:1200'],
        ]);

        DB::transaction(function() use($request,$order,$validated){
            $return=SellerReturnRequest::firstOrCreate(
                ['marketplace_order_id'=>$order->id],
                [
                    'seller_account_id'=>$order->seller_account_id,
                    'buyer_account_id'=>$order->buyer_account_id,
                    'buyer_social_account_id'=>$order->buyer_social_account_id,
                    'reason'=>$validated['reason'],
                    'details'=>$validated['details'] ?? null,
                    'requested_amount'=>(float)$order->subtotal,
                    'status'=>'requested',
                ]
            );
            SellerReturnEvent::firstOrCreate(
                ['seller_return_request_id'=>$return->id,'status'=>'requested'],
                [
                    'actor_role'=>'buyer',
                    'actor_id'=>$order->buyer_account_id ?: $order->buyer_social_account_id,
                    'title'=>'Return Requested',
                    'message'=>$validated['details'] ?? $validated['reason'],
                ]
            );
            SellerNotification::create([
                'seller_account_id'=>$order->seller_account_id,
                'type'=>'return_request',
                'title'=>'New return request',
                'message'=>"Buyer requested a return for {$order->order_number}.",
                'action_url'=>route('seller.returns.index'),
                'data'=>['order_id'=>$order->id,'return_request_id'=>$return->id],
            ]);
        });

        return back()->with('success','Return request submitted to the Seller.');
    }

    private function guardOwned(Request $request, MarketplaceOrder $order): void
    {
        $this->identity->guard($request);
        $owned=$this->identity->accountId($request)
            ? (int)$order->buyer_account_id===(int)$this->identity->accountId($request)
            : (int)$order->buyer_social_account_id===(int)$this->identity->socialId($request);
        abort_unless($owned,403);
    }
}
