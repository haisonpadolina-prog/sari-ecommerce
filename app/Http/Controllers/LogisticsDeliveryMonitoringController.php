<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use Illuminate\Contracts\View\View;

class LogisticsDeliveryMonitoringController extends Controller
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
        $activeOrders = MarketplaceOrder::query()
            ->with(['seller','logisticsParcel'])
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->whereNotNull('courier_email')
            ->latest('updated_at')
            ->get();

        $deliveredToday = MarketplaceOrder::query()
            ->with(['seller','logisticsParcel'])
            ->where('status', 'delivered')
            ->whereDate('delivered_at', now()->toDateString())
            ->latest('delivered_at')
            ->limit(12)
            ->get();

        return view('logistics.delivery-monitoring', compact(
            'activeOrders',
            'deliveredToday'
        ));
    }
}
