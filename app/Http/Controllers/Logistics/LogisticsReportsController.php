<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogisticsReportsController extends Controller
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
        $monthStart = now()->copy()->startOfMonth();

        $orders = fn () => MarketplaceOrder::query()
            ->where(
                'logistics_account_id',
                $logistics->id
            );

        $parcels = fn () => LogisticsParcel::query()
            ->whereHas(
                'order',
                fn ($query) =>
                    $query->where(
                        'logistics_account_id',
                        $logistics->id
                    )
            );

        $stats = [
            'waiting_assignment' => $orders()
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),

            'active_deliveries' => $orders()
                ->whereIn(
                    'status',
                    self::ACTIVE_DELIVERY_STATUSES
                )
                ->count(),

            'delivered_today' => $orders()
                ->where('status', 'delivered')
                ->whereDate(
                    'delivered_at',
                    now()->toDateString()
                )
                ->count(),

            'delivered_this_month' => $orders()
                ->where('status', 'delivered')
                ->where(
                    'delivered_at',
                    '>=',
                    $monthStart
                )
                ->count(),

            'delivery_fees_this_month' =>
                (float) $orders()
                    ->where('status', 'delivered')
                    ->where(
                        'delivered_at',
                        '>=',
                        $monthStart
                    )
                    ->sum('delivery_fee'),

            'hub_received' => $parcels()
                ->whereNotNull('received_at')
                ->count(),

            'hub_sorted' => $parcels()
                ->where('status', 'sorted')
                ->count(),
        ];

        $recentDelivered = $orders()
            ->with('seller')
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->limit(15)
            ->get();

        return view(
            'logistics.reports',
            compact('stats', 'recentDelivered')
        );
    }
}
