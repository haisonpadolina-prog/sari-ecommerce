<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
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
    ) {}

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $orders = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops.facility',
            ])
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        foreach ($orders as $order) {
            $issues = $this->pickupIssues($order);

            if (
                $order->logisticsParcel
                && $order->logisticsParcel->status !== 'pickup_verified'
            ) {
                $issues[] = 'Parcel has already entered the Logistics intake flow. Review its status.';
            }

            $order->setAttribute('pickup_issues', $issues);

            $order->setAttribute(
                'pickup_queue_state',
                $issues
                    ? 'attention'
                    : (
                        $order->logisticsParcel?->status === 'pickup_verified'
                            ? 'verified'
                            : 'pending'
                    )
            );
        }

        $verified = $orders->where(
            'pickup_queue_state',
            'verified'
        );

        $stats = [
            'total' => $orders->count(),

            'pending' => $orders
                ->where('pickup_queue_state', 'pending')
                ->count(),

            'verified' => $verified->count(),

            'attention' => $orders
                ->where('pickup_queue_state', 'attention')
                ->count(),

            'batches' => $verified
                ->groupBy(
                    fn (MarketplaceOrder $order): string =>
                        $this->pickupGroupKey($order)
                )
                ->count(),
        ];

        return view(
            'logistics.pickup-requests',
            compact('orders', 'stats')
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

        $request->merge([
            'order_ids' => [$owned->id],
        ]);

        return $this->verifyBatch($request);
    }

    public function verifyBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_ids' => [
                'required',
                'array',
                'min:1',
                'max:200',
            ],

            'order_ids.*' => [
                'required',
                'integer',
                'distinct',
            ],
        ]);

        $logistics = CurrentLogisticsAccount::resolve($request);

        $ids = collect($validated['order_ids'])
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        $count = DB::transaction(function () use ($ids, $logistics): int {
            $orders = MarketplaceOrder::query()
                ->whereIn('id', $ids)
                ->where('logistics_account_id', $logistics->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($orders->count() !== $ids->count()) {
                throw ValidationException::withMessages([
                    'order_ids' => 'One or more requests are unavailable in your Logistics account. Refresh the queue.',
                ]);
            }

            $orders->load('deliveryRouteStops.facility');

            $parcels = LogisticsParcel::query()
                ->whereIn('marketplace_order_id', $ids)
                ->orderBy('marketplace_order_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('marketplace_order_id');

            $count = 0;

            foreach ($orders as $order) {
                if (
                    $order->status !== 'ready_for_pickup'
                    || filled($order->courier_email)
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .' is no longer available for verification. Refresh the queue.',
                    ]);
                }

                $issues = $this->pickupIssues($order);

                if ($issues) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .': '.$issues[0],
                    ]);
                }

                $parcel = $parcels->get($order->id);

                if ($parcel?->status === 'pickup_verified') {
                    continue;
                }

                if ($parcel) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number
                            .' has already entered the Logistics intake flow.',
                    ]);
                }

                LogisticsParcel::query()->create([
                    'marketplace_order_id' => $order->id,
                    'status' => 'pickup_verified',
                ]);

                $this->workflow->record(
                    $order,
                    'both',
                    'pickup_verified',
                    'Pickup Request Verified',
                    $logistics->displayName()
                        .' verified the Seller pickup request for order '
                        .$order->order_number
                        .'. It is now ready for Rider assignment.'
                );

                $count++;
            }

            return $count;
        }, 3);

        return back()->with(
            'success',
            $count > 0
                ? $count.' pickup '
                    .($count === 1 ? 'request verified' : 'requests verified')
                    .'. Continue to Pickup Assignment.'
                : 'These pickup requests are already verified. Continue to Pickup Assignment.'
        );
    }

    private function pickupGroupKey(MarketplaceOrder $order): string
    {
        $origin = $order->deliveryRouteStops->firstWhere(
            'stop_key',
            'origin_facility'
        );

        $seller = $order->seller_account_id
            ? 'seller:'.$order->seller_account_id
            : 'order:'.$order->id;

        return json_encode([
            $seller,
            $this->normalizePickupText($order->pickup_address),
            $origin?->logistics_facility_id,
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<int, string>
     */
    private function pickupIssues(MarketplaceOrder $order): array
    {
        $issues = [];

        $origin = $order->deliveryRouteStops->firstWhere(
            'stop_key',
            'origin_facility'
        );

        $facility = $origin?->facility;

        if (blank($order->pickup_address)) {
            $issues[] = 'Pickup address is missing. Ask the Seller to update it.';
        }

        if (! $facility) {
            $issues[] = 'Origin hub is missing. Review the delivery route.';
        } elseif (
            (int) $facility->logistics_account_id
            !== (int) $order->logistics_account_id
        ) {
            $issues[] = 'Origin hub belongs to another Logistics provider.';
        } elseif (! $facility->is_active) {
            $issues[] = 'Origin hub is inactive. Review the delivery route.';
        }

        return $issues;
    }

    private function normalizePickupText(?string $value): string
    {
        return mb_strtolower(
            preg_replace('/\s+/u', ' ', trim((string) $value)) ?: ''
        );
    }
}