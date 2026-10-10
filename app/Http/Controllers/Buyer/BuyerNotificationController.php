<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Services\Buyer\BuyerActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerNotificationController extends Controller
{
    public function __construct(
        private readonly BuyerActivityService $activity,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.notifications', [
            'notifications' => $this->activity->notifications($request),
            'unreadCount' => $this->activity->unreadNotificationCount($request),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => $this->activity->unreadNotificationCount($request),
        ]);
    }

    public function read(Request $request, MarketplaceOrderEvent $event): RedirectResponse
    {
        $this->activity->markRead($request, $event);

        return redirect()->route('buyer.orders.show', [
            'order' => $event->marketplace_order_id,
        ]);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $this->activity->markAllRead($request);

        return back()->with('success', 'Notifications marked as read.');
    }
}
