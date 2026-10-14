<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\DeliveryRouteService;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsDeliveryRouteController extends Controller
{
    public function __construct(
        private readonly DeliveryRouteService $deliveryRoute,
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function edit(
        Request $request,
        MarketplaceOrder $order
    ): View {
        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        $order->load([
            'seller',
            'buyer',
            'logisticsParcel',
            'logisticsAccount',
        ]);

        abort_unless($order->logisticsParcel, 404);

        $stops = $this->deliveryRoute->ensureStops($order);

        return view(
            'logistics.delivery-route',
            compact('order', 'stops')
        );
    }

    public function update(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        abort_unless(
            $order->logisticsParcel()->exists(),
            404
        );

        $this->deliveryRoute->refreshFacilities($order);

        return back()->with(
            'success',
            'Nearest origin and destination facilities refreshed inside your Logistics network.'
        );
    }

    public function reached(
        Request $request,
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): RedirectResponse {
        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        $this->deliveryRoute->markReached(
            $order,
            $stop
        );

        return back()->with(
            'success',
            $stop->name . ' marked as reached.'
        );
    }
}
