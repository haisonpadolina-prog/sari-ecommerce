<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use Illuminate\Contracts\View\View;

class LogisticsPickupRequestController extends Controller
{
    public function index(): View
    {
        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        return view('logistics.pickup-requests', compact('orders'));
    }
}
