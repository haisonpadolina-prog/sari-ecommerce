<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsPickupRequestController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel', 'deliveryRouteStops'])
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        return view(
            'logistics.pickup-requests',
            compact('orders')
        );
    }

    public function verify(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $owned = $this->orderAccess->ownedOrder(
            $request,
            $order
        );

        DB::transaction(function () use ($owned): void {
            $locked = MarketplaceOrder::query()
                ->whereKey($owned->id)
                ->where(
                    'logistics_account_id',
                    $owned->logistics_account_id
                )
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $locked->status !== 'ready_for_pickup'
                || filled($locked->courier_email)
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'This pickup request is no longer available for Logistics verification.',
                ]);
            }

            $parcel = LogisticsParcel::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if (
                $parcel
                && in_array(
                    $parcel->status,
                    ['awaiting_intake', 'received', 'sorted'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'This parcel has already moved beyond pickup verification.',
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

        return back()->with(
            'success',
            'Pickup request verified. You can now assign an approved Rider.'
        );
    }
}
