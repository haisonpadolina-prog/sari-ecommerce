<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
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
        if (! $request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $courierEmail = $this->courierEmail($request);

        $currentDelivery = MarketplaceOrder::query()
            ->with('logisticsParcel')
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [$courierEmail]
            )
            ->whereIn(
                'status',
                self::ACTIVE_DELIVERY_STATUSES
            )
            ->latest('updated_at')
            ->first();

        return $this->dashboard(
            $request,
            $currentDelivery
        );
    }

    public function show(
        Request $request,
        MarketplaceOrder $order
    ): View|RedirectResponse {
        if (! $request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $courierEmail = $this->courierEmail($request);

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        | Rider may only open an order assigned to their own account.
        */

        abort_unless(
            strtolower(
                (string) $order->courier_email
            ) === $courierEmail,
            403
        );

        abort_unless(
            in_array(
                $order->status,
                self::ACTIVE_DELIVERY_STATUSES,
                true
            ),
            404
        );

        $order->load('logisticsParcel');

        return $this->dashboard(
            $request,
            $order
        );
    }

    private function dashboard(
        Request $request,
        ?MarketplaceOrder $currentDelivery
    ): View {
        $courierEmail = $this->courierEmail($request);

        $assignments = MarketplaceOrder::query()
            ->with('seller')
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [$courierEmail]
            )
            ->where(
                'status',
                'courier_accepted'
            )
            ->latest('accepted_at')
            ->get();

        $stats = [
            'assigned_waiting' =>
                MarketplaceOrder::query()
                    ->whereRaw(
                        'LOWER(courier_email) = ?',
                        [$courierEmail]
                    )
                    ->where(
                        'status',
                        'courier_accepted'
                    )
                    ->count(),

            'active_deliveries' =>
                MarketplaceOrder::query()
                    ->whereRaw(
                        'LOWER(courier_email) = ?',
                        [$courierEmail]
                    )
                    ->whereIn(
                        'status',
                        self::ACTIVE_DELIVERY_STATUSES
                    )
                    ->count(),

            'completed_today' =>
                MarketplaceOrder::query()
                    ->whereRaw(
                        'LOWER(courier_email) = ?',
                        [$courierEmail]
                    )
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->whereDate(
                        'delivered_at',
                        now()->toDateString()
                    )
                    ->count(),

            'earnings_today' =>
                (float) MarketplaceOrder::query()
                    ->whereRaw(
                        'LOWER(courier_email) = ?',
                        [$courierEmail]
                    )
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->whereDate(
                        'delivered_at',
                        now()->toDateString()
                    )
                    ->selectRaw(
                        '
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN original_delivery_fee > 0
                                    THEN original_delivery_fee
                                    ELSE delivery_fee
                                END
                            ),
                            0
                        ) AS courier_fee_total
                        '
                    )
                    ->value('courier_fee_total'),
        ];

        return view(
            'courier.dashboard',
            compact(
                'currentDelivery',
                'assignments',
                'stats'
            )
        );
    }

    private function courierEmail(
        Request $request
    ): string {
        return strtolower(
            (string) $request
                ->session()
                ->get('courier_email', '')
        );
    }
}