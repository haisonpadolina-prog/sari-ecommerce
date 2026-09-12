<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourierDashboardController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $courierEmail = strtolower(
            (string) $request->session()->get('courier_email', '')
        );

        $currentDelivery = MarketplaceOrder::query()
            ->with('logisticsParcel')
            ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->latest('updated_at')
            ->first();

        $assignments = MarketplaceOrder::query()
            ->with('seller')
            ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
            ->where('status', 'courier_accepted')
            ->latest('accepted_at')
            ->limit(5)
            ->get();

        $stats = [
            'assigned_waiting' => MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
                ->where('status', 'courier_accepted')
                ->count(),
            'active_deliveries' => MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->count(),
            'completed_today' => MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->count(),
            'earnings_today' => (float) MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [$courierEmail])
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->sum('delivery_fee'),
        ];

        return view('courier.dashboard', compact(
            'currentDelivery',
            'assignments',
            'stats'
        ));
    }
}
