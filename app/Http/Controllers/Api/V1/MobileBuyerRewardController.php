<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileBuyerRewardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var BuyerAccount $buyer */
        $buyer=$request->user();
        $rate=(float)config('sari.buyer.reward_points_per_peso',0.10);
        $orders=MarketplaceOrder::query()->where('buyer_account_id',$buyer->id)
            ->where('status','delivered')->whereNotNull('delivered_at')
            ->latest('delivered_at')->limit(100)->get(['id','order_number','total','delivered_at']);
        $spent=(float)$orders->sum(fn(MarketplaceOrder $o)=>(float)$o->total);
        $activities=$orders->map(function(MarketplaceOrder $order) use($rate): array {
            $total=(float)$order->total;
            return [
                'order_id'=>(int)$order->id,'order_number'=>(string)$order->order_number,
                'order_total'=>$total,'earned_points'=>(int)floor($total*$rate),
                'delivered_at'=>$order->delivered_at?->toIso8601String(),
            ];
        })->values();
        return response()->json(['success'=>true,'data'=>[
            'points'=>(int)$activities->sum('earned_points'),'spent'=>round($spent,2),
            'rate'=>$rate,'activities'=>$activities,
        ]])->header('Cache-Control','no-store');
    }
}
