<?php

namespace App\Http\Controllers;

use App\Models\CourierAccount;
use App\Models\MarketplaceOrder;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogisticsRiderManagementController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $riders = CourierAccount::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->whereNotNull('registration_application_id')
            ->latest('approved_at')
            ->get();

        $activeOrders = MarketplaceOrder::query()
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->whereNotNull('courier_email')
            ->latest('updated_at')
            ->get()
            ->keyBy(fn (MarketplaceOrder $order): string => strtolower((string) $order->courier_email));

        $stats = [
            'total' => $riders->count(),
            'active' => $riders->where('account_status', 'active')->count(),
            'busy' => $riders
                ->filter(fn (CourierAccount $rider): bool => $activeOrders->has(strtolower((string) $rider->email)))
                ->count(),
        ];

        return view('logistics.rider-management', compact(
            'riders',
            'activeOrders',
            'stats',
            'logistics'
        ));
    }

}
