<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Support\LogisticsOrderAccess;
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

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $riderApplicationIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        $ownedOrders = fn () => MarketplaceOrder::query()
            ->where('logistics_account_id', $logistics->id);

        $ownedParcels = fn () => LogisticsParcel::query()
            ->whereHas(
                'order',
                fn ($query) =>
                    $query->where(
                        'logistics_account_id',
                        $logistics->id
                    )
            );

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

            'facilities' => LogisticsFacility::query()
                ->forLogistics($logistics->id)
                ->active()
                ->count(),

            'pickup_requests' => $ownedOrders()
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),

            'active_deliveries' => $ownedOrders()
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->whereNotNull('courier_email')
                ->count(),

            'delivered_today' => $ownedOrders()
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->count(),

            'awaiting_intake' => $ownedParcels()
                ->where('status', 'awaiting_intake')
                ->count(),

            'sorting_queue' => $ownedParcels()
                ->where('status', 'received')
                ->count(),
        ];

        $recentOrders = $ownedOrders()
            ->with(['seller', 'logisticsParcel'])
            ->whereIn(
                'status',
                array_merge(
                    ['ready_for_pickup'],
                    self::ACTIVE_DELIVERY_STATUSES,
                    ['delivered']
                )
            )
            ->latest('updated_at')
            ->limit(8)
            ->get();

        return view(
            'logistics.dashboard',
            compact(
                'stats',
                'recentOrders',
                'logistics'
            )
        );
    }
}
