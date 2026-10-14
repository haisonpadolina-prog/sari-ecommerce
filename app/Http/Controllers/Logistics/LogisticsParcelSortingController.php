<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsParcelSortingController extends Controller
{
    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $parcels = LogisticsParcel::query()
            ->with([
                'order.seller',
                'order.deliveryRouteStops',
            ])
            ->whereHas(
                'order',
                fn ($query) =>
                    $query->where(
                        'logistics_account_id',
                        $logistics->id
                    )
            )
            ->whereIn('status', ['received', 'sorted'])
            ->latest('received_at')
            ->get();

        return view(
            'logistics.parcel-sorting',
            compact('parcels')
        );
    }

    public function sort(
        Request $request,
        LogisticsParcel $parcel
    ): RedirectResponse {
        $parcel->loadMissing('order');

        abort_unless($parcel->order, 404);

        $this->orderAccess->ownedOrder(
            $request,
            $parcel->order
        );

        abort_unless(
            $parcel->status === 'received',
            422,
            'Only received parcels can be sorted.'
        );

        $validated = $request->validate([
            'sorting_zone' => [
                'required',
                'string',
                'max:80',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $parcel->update([
            'status' => 'sorted',
            'sorting_zone' => $validated['sorting_zone'],
            'notes' => $validated['notes'] ?? $parcel->notes,
            'sorted_at' => now(),
        ]);

        return back()->with(
            'success',
            'Parcel sorted and cleared for the next delivery leg.'
        );
    }
}
