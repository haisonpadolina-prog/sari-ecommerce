<?php

namespace App\Http\Controllers;

use App\Models\LogisticsParcel;
use App\Models\MarketplaceOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsIncomingParcelController extends Controller
{
    public function index(): View
    {
        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->whereIn('status', ['in_transit', 'arrived_buyer'])
            ->whereNotNull('courier_email')
            ->latest('picked_up_at')
            ->get();

        $parcels = LogisticsParcel::query()
            ->whereIn('marketplace_order_id', $orders->pluck('id'))
            ->get()
            ->keyBy('marketplace_order_id');

        return view('logistics.incoming-parcels', compact('orders', 'parcels'));
    }

    public function receive(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        abort_unless($order->status === 'in_transit', 422, 'Parcel must be picked up and still in transit before hub intake.');

        $validated = $request->validate([
            'notes' => ['nullable','string','max:1000'],
        ]);

        $parcel = LogisticsParcel::query()->firstOrNew(['marketplace_order_id' => $order->id]);
        abort_if($parcel->exists && $parcel->status === 'sorted', 422, 'This parcel is already sorted.');
        $parcel->fill([
            'status' => 'received',
            'notes' => $validated['notes'] ?? $parcel->notes,
            'received_at' => $parcel->received_at ?: now(),
            'sorted_at' => null,
        ])->save();

        return back()->with('success', 'Parcel received at Logistics hub.');
    }
}
