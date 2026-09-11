<?php

namespace App\Http\Controllers;

use App\Models\CourierAccount;
use App\Models\LogisticsParcel;
use App\Models\MarketplaceOrder;
use App\Models\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogisticsDashboardController extends Controller
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
        $riderApplicationIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        $stats = [
            'pending_rider_applications' => RegistrationApplication::query()
                ->where(function ($query) use ($logistics): void {
                    $query->where('logistics_account_id', $logistics->id)
                        ->orWhereNull('logistics_account_id');
                })
                ->where('role', 'rider')
                ->where('status', 'pending')
                ->count(),
            'active_riders' => CourierAccount::query()
                ->where(function ($query) use ($logistics): void {
                    $query->where('logistics_account_id', $logistics->id)
                        ->orWhereNull('logistics_account_id');
                })
                ->where('account_status', 'active')
                ->whereIn('registration_application_id', $riderApplicationIds)
                ->count(),
            'pickup_requests' => MarketplaceOrder::query()
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),
            'active_deliveries' => MarketplaceOrder::query()
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->whereNotNull('courier_email')
                ->count(),
            'delivered_today' => MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->count(),
            'awaiting_intake' => LogisticsParcel::query()
                ->where('status', 'awaiting_intake')
                ->count(),
            'sorting_queue' => LogisticsParcel::query()
                ->where('status', 'received')
                ->count(),
        ];

        $recentOrders = MarketplaceOrder::query()
            ->with(['seller','logisticsParcel'])
            ->whereIn('status', array_merge(
                ['ready_for_pickup'],
                self::ACTIVE_DELIVERY_STATUSES,
                ['delivered']
            ))
            ->latest('updated_at')
            ->limit(8)
            ->get();

        return view('logistics.dashboard', compact('stats', 'recentOrders'));
    }
}
