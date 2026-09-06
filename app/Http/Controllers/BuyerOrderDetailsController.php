<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class BuyerOrderDetailsController extends Controller
{
    /**
     * Display one buyer order with the full tracking/details page.
     *
     * This controller is intentionally separate from BuyerOrderController
     * so the existing Buyer order list, cancellation, review, and other
     * backend methods do not need to be changed.
     */
    public function __invoke(Request $request, MarketplaceOrder $order)
    {
        /*
        |--------------------------------------------------------------------------
        | BUYER SESSION CHECK
        |--------------------------------------------------------------------------
        */
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $buyerId = (int) $request->session()->get('buyer_account_id');

        /*
        |--------------------------------------------------------------------------
        | ORDER OWNERSHIP CHECK
        |--------------------------------------------------------------------------
        |
        | A buyer must only be able to open his/her own order.
        |
        */
        abort_unless(
            $buyerId > 0 &&
            (int) $order->buyer_account_id === $buyerId,
            403,
            'You are not allowed to view this order.'
        );

        /*
        |--------------------------------------------------------------------------
        | LOAD DATA REQUIRED BY order-details.blade.php
        |--------------------------------------------------------------------------
        */
        $order->loadMissing([
            'seller',
            'reviews',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ORDER DETAILS VIEW
        |--------------------------------------------------------------------------
        */
        return view('buyer.order-details', [
            'order' => $order,
        ]);
    }
}
