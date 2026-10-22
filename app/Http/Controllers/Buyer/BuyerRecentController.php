<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerRecentController extends Controller
{
    public function __construct(
        private readonly BuyerActivityService $activity,
    ) {
    }

    public function orders(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.recent-orders', [
            'recentOrders' => $this->activity->recentOrders($request, 12),
        ]);
    }

    public function activity(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.recent-activity', [
            'recentActivity' => $this->activity->latestNotifications($request, 30),
        ]);
    }
}
