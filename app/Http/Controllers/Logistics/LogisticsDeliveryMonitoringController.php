<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogisticsDeliveryMonitoringController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $activeOrders = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->whereIn(
                'status',
                self::ACTIVE_DELIVERY_STATUSES
            )
            ->whereNotNull('courier_email')
            ->latest('updated_at')
            ->get();

        $deliveredToday = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->where('status', 'delivered')
            ->whereDate(
                'delivered_at',
                now()->toDateString()
            )
            ->latest('delivered_at')
            ->limit(12)
            ->get();

        return view(
            'logistics.delivery-monitoring',
            compact(
                'activeOrders',
                'deliveredToday'
            )
        );
    }
}
