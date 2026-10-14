<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsIncomingParcelController extends Controller
{
    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $orders = MarketplaceOrder::query()
            ->with(['seller', 'deliveryRouteStops'])
            ->where('logistics_account_id', $logistics->id)
            ->whereIn('status', ['in_transit', 'arrived_buyer'])
            ->whereNotNull('courier_email')
            ->latest('picked_up_at')
            ->get();

        $parcels = LogisticsParcel::query()
            ->whereIn(
                'marketplace_order_id',
                $orders->pluck('id')
            )
            ->get()
            ->keyBy('marketplace_order_id');

        return view(
            'logistics.incoming-parcels',
            compact('orders', 'parcels')
        );
    }

    public function receive(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        abort_unless(
            $order->status === 'in_transit',
            422,
            'Parcel must be picked up and still in transit before hub intake.'
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $parcel = LogisticsParcel::query()->firstOrNew([
            'marketplace_order_id' => $order->id,
        ]);

        abort_if(
            $parcel->exists && $parcel->status === 'sorted',
            422,
            'This parcel is already sorted.'
        );

        $parcel->fill([
            'status' => 'received',
            'notes' => $validated['notes'] ?? $parcel->notes,
            'received_at' => $parcel->received_at ?: now(),
            'sorted_at' => null,
        ])->save();

        return back()->with(
            'success',
            'Parcel received at your Logistics network.'
        );
    }
}
