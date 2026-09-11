<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\LogisticsParcel;
use Illuminate\Contracts\View\View;

class LogisticsReportsController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function index(): View
    {
        $monthStart = now()->copy()->startOfMonth();

        $stats = [
            'waiting_assignment' => MarketplaceOrder::query()
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),
            'active_deliveries' => MarketplaceOrder::query()
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->count(),
            'delivered_today' => MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->count(),
            'delivered_this_month' => MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->where('delivered_at', '>=', $monthStart)
                ->count(),
            'delivery_fees_this_month' => (float) MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->where('delivered_at', '>=', $monthStart)
                ->sum('delivery_fee'),
            'hub_received' => LogisticsParcel::query()->whereNotNull('received_at')->count(),
            'hub_sorted' => LogisticsParcel::query()->where('status', 'sorted')->count(),
        ];

        $recentDelivered = MarketplaceOrder::query()
            ->with('seller')
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->limit(15)
            ->get();

        return view('logistics.reports', compact('stats', 'recentDelivered'));
    }
}
