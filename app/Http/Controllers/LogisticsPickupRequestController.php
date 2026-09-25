<?php

namespace App\Http\Controllers;

use App\Models\LogisticsParcel;
use App\Models\MarketplaceOrder;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsPickupRequestController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    public function index(): View
    {
        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        return view('logistics.pickup-requests', compact('orders'));
    }

    public function verify(MarketplaceOrder $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $locked->status !== 'ready_for_pickup'
                || filled($locked->courier_email)
            ) {
                throw ValidationException::withMessages([
                    'order' => 'This pickup request is no longer available for Logistics verification.',
                ]);
            }

            $parcel = LogisticsParcel::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($parcel && in_array($parcel->status, ['awaiting_intake', 'received', 'sorted'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'This parcel has already moved beyond pickup verification.',
                ]);
            }

            LogisticsParcel::query()->updateOrCreate(
                ['marketplace_order_id' => $locked->id],
                [
                    'status' => 'pickup_verified',
                    'sorting_zone' => null,
                    'received_at' => null,
                    'sorted_at' => null,
                ]
            );

            $this->workflow->record(
                $locked,
                'both',
                'pickup_verified',
                'Pickup Request Verified',
                'SARI Logistics verified the Seller pickup request for order '
                    . $locked->order_number
                    . '. It is now ready for Rider assignment.'
            );
        }, 3);

        return back()->with('success', 'Pickup request verified. You can now assign an approved Rider.');
    }
}