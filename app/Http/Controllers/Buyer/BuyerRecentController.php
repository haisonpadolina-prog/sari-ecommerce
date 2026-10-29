<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerActivityService;
use App\Services\Buyer\BuyerAccountActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerRecentController extends Controller
{
    public function __construct(
        private readonly BuyerActivityService $activity,
        private readonly BuyerAccountActivityService $accountActivity,
    ) {
    }

    public function orders(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.recent-orders', [
            'recentOrders' => $this->activity->recentOrdersPaginated($request, 8),
        ]);
    }

    public function activity(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.recent-activity', [
            'recentActivity' => $this->accountActivity->paginate($request, 10, $request->query('filter') === 'archived'),
            'showArchived' => $request->query('filter') === 'archived',
            'unreadCount' => $this->accountActivity->unreadCount($request),
            'buyerProfile' => $this->accountActivity->buyerProfile($request),
        ]);
    }
}
