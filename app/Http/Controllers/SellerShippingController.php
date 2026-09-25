<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\SellerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerShippingController extends Controller
{
    private const SHIPPING_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
        'delivered',
    ];

    private const AWAITING_PICKUP_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
    ];

    private const IN_TRANSIT_STATUSES = [
        'in_transit',
        'arrived_buyer',
    ];

    public function index(Request $request): View
    {
        $seller = $this->seller($request);

        $filter = (string) $request->query('status', 'all');

        if (!in_array($filter, ['all', 'awaiting_pickup', 'in_transit', 'delivered'], true)) {
            $filter = 'all';
        }

        $search = trim((string) $request->query('q', ''));

        $baseQuery = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->whereIn('status', self::SHIPPING_STATUSES);

        $stats = [
            'active' => (clone $baseQuery)
                ->where('status', '!=', 'delivered')
                ->count(),

            'awaiting_pickup' => (clone $baseQuery)
                ->whereIn('status', self::AWAITING_PICKUP_STATUSES)
                ->count(),

            'in_transit' => (clone $baseQuery)
                ->whereIn('status', self::IN_TRANSIT_STATUSES)
                ->count(),

            'delivered' => (clone $baseQuery)
                ->where('status', 'delivered')
                ->count(),
        ];

        $ordersQuery = (clone $baseQuery)
            ->with([
                'logisticsParcel',
                'events' => fn ($query) => $query
                    ->whereIn('audience', ['seller', 'both'])
                    ->orderByDesc('created_at'),
            ]);

        if ($filter === 'awaiting_pickup') {
            $ordersQuery->whereIn('status', self::AWAITING_PICKUP_STATUSES);
        } elseif ($filter === 'in_transit') {
            $ordersQuery->whereIn('status', self::IN_TRANSIT_STATUSES);
        } elseif ($filter === 'delivered') {
            $ordersQuery->where('status', 'delivered');
        }

        if ($search !== '') {
            $like = '%' . $search . '%';

            $ordersQuery->where(function ($query) use ($like): void {
                $query
                    ->where('order_number', 'like', $like)
                    ->orWhere('buyer_name', 'like', $like)
                    ->orWhere('buyer_email', 'like', $like)
                    ->orWhere('courier_name', 'like', $like)
                    ->orWhere('courier_email', 'like', $like);
            });
        }

        $orders = $ordersQuery
            ->latest('updated_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $shippingStates = $orders->getCollection()
            ->mapWithKeys(
                fn (MarketplaceOrder $order): array => [
                    $order->id => $this->shippingState($order),
                ]
            );

        return view('seller.shipping.index', compact(
            'seller',
            'orders',
            'shippingStates',
            'stats',
            'filter',
            'search'
        ));
    }

    public function show(Request $request, MarketplaceOrder $order): View
    {
        $seller = $this->seller($request);
        $this->assertOwnedShipment($order, $seller);

        abort_unless(
            in_array($order->status, self::SHIPPING_STATUSES, true),
            404
        );

        $order->load([
            'logisticsParcel',
            'events' => fn ($query) => $query
                ->whereIn('audience', ['seller', 'both'])
                ->oldest('created_at'),
        ]);

        $shippingState = $this->shippingState($order);
        $timeline = $this->timeline($order);
        $revision = $this->revision($order);

        return view('seller.shipping.show', compact(
            'seller',
            'order',
            'shippingState',
            'timeline',
            'revision'
        ));
    }

    public function liveState(
        Request $request,
        MarketplaceOrder $order
    ): JsonResponse {
        $seller = $this->seller($request);
        $this->assertOwnedShipment($order, $seller);

        abort_unless(
            in_array($order->status, self::SHIPPING_STATUSES, true),
            404
        );

        $order->load([
            'logisticsParcel',
            'events' => fn ($query) => $query
                ->whereIn('audience', ['seller', 'both'])
                ->latest('created_at'),
        ]);

        $state = $this->shippingState($order);

        return response()->json([
            'order_id' => (int) $order->id,
            'order_number' => (string) $order->order_number,
            'status' => (string) $order->status,
            'status_label' => $order->statusLabel(),
            'shipping_label' => $state['label'],
            'shipping_detail' => $state['detail'],
            'progress' => $state['progress'],
            'parcel_status' => $order->logisticsParcel?->status,
            'sorting_zone' => $order->logisticsParcel?->sorting_zone,
            'courier_name' => $order->courier_name,
            'revision' => $this->revision($order),
        ]);
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        $sellerId = (int) $request->session()->get('seller_account_id', 0);

        abort_if($sellerId < 1, 403);

        return SellerAccount::query()->findOrFail($sellerId);
    }

    private function assertOwnedShipment(
        MarketplaceOrder $order,
        SellerAccount $seller
    ): void {
        abort_unless(
            (int) $order->seller_account_id === (int) $seller->id,
            404
        );
    }

    private function shippingState(MarketplaceOrder $order): array
    {
        $parcel = $order->logisticsParcel;
        $parcelStatus = (string) ($parcel?->status ?? '');

        if ($order->status === 'delivered') {
            return [
                'label' => 'Delivered',
                'detail' => 'The Buyer delivery was completed successfully.',
                'progress' => 100,
                'tone' => 'success',
            ];
        }

        if ($order->status === 'arrived_buyer') {
            return [
                'label' => 'Arrived at Buyer',
                'detail' => 'The Rider reached the Buyer and is completing the handoff.',
                'progress' => 94,
                'tone' => 'info',
            ];
        }

        if ($order->status === 'in_transit') {
            if ($parcelStatus === 'sorted') {
                return [
                    'label' => 'Sorted · Final Delivery',
                    'detail' => $parcel?->sorting_zone
                        ? 'Parcel cleared from sorting zone ' . $parcel->sorting_zone . ' for final delivery.'
                        : 'Parcel was sorted by Logistics and cleared for final delivery.',
                    'progress' => 82,
                    'tone' => 'info',
                ];
            }

            if ($parcelStatus === 'received') {
                return [
                    'label' => 'At Logistics Hub',
                    'detail' => 'Logistics received the parcel and is preparing it for sorting.',
                    'progress' => 70,
                    'tone' => 'hub',
                ];
            }

            return [
                'label' => 'Moving to Logistics Hub',
                'detail' => 'The Rider has collected the parcel from the Seller.',
                'progress' => 56,
                'tone' => 'info',
            ];
        }

        if ($order->status === 'arrived_pickup') {
            return [
                'label' => 'Rider at Seller',
                'detail' => 'The assigned Rider arrived at the Seller pickup point.',
                'progress' => 44,
                'tone' => 'warning',
            ];
        }

        if ($order->status === 'heading_pickup') {
            return [
                'label' => 'Rider Heading to Seller',
                'detail' => 'The assigned Rider is on the way to collect the parcel.',
                'progress' => 34,
                'tone' => 'warning',
            ];
        }

        if ($order->status === 'courier_accepted') {
            return [
                'label' => 'Rider Assigned',
                'detail' => $order->courier_name
                    ? $order->courier_name . ' was assigned by SARI Logistics.'
                    : 'SARI Logistics assigned a Rider to this shipment.',
                'progress' => 25,
                'tone' => 'warning',
            ];
        }

        return [
            'label' => $parcelStatus === 'pickup_verified'
                ? 'Pickup Verified'
                : 'Awaiting Pickup',
            'detail' => $parcelStatus === 'pickup_verified'
                ? 'Logistics verified the pickup request. Rider assignment is next.'
                : 'The parcel is ready and waiting for Logistics pickup processing.',
            'progress' => $parcelStatus === 'pickup_verified' ? 18 : 12,
            'tone' => 'warning',
        ];
    }

    private function timeline(MarketplaceOrder $order): array
    {
        $entries = [];

        $push = function (
            ?\DateTimeInterface $time,
            string $title,
            string $detail,
            string $kind = 'system'
        ) use (&$entries): void {
            if (!$time) {
                return;
            }

            $entries[] = [
                'time' => $time,
                'title' => $title,
                'detail' => $detail,
                'kind' => $kind,
            ];
        };

        $push(
            $order->ready_at,
            'Ready for Pickup',
            'Seller packed the order and marked the parcel ready for Logistics pickup.',
            'seller'
        );

        $push(
            $order->accepted_at,
            'Rider Assigned',
            ($order->courier_name ?: 'A SARI Rider') . ' was assigned to this shipment.',
            'rider'
        );

        $push(
            $order->pickup_started_at,
            'Rider Heading to Seller',
            'The Rider started travelling to the Seller pickup point.',
            'rider'
        );

        $push(
            $order->arrived_pickup_at,
            'Rider Arrived at Seller',
            'The Rider reached the pickup location and prepared for handoff.',
            'rider'
        );

        $push(
            $order->picked_up_at,
            'Parcel Picked Up',
            'The Seller handed the parcel to the assigned Rider.',
            'rider'
        );

        $push(
            $order->logisticsParcel?->received_at,
            'Received by Logistics Hub',
            'SARI Logistics received the parcel for hub intake.',
            'logistics'
        );

        $push(
            $order->logisticsParcel?->sorted_at,
            'Parcel Sorted',
            $order->logisticsParcel?->sorting_zone
                ? 'Parcel sorted under zone ' . $order->logisticsParcel->sorting_zone . ' and cleared for final delivery.'
                : 'Parcel sorted and cleared for final delivery.',
            'logistics'
        );

        $push(
            $order->arrived_buyer_at,
            'Arrived at Buyer',
            'The Rider reached the Buyer delivery location.',
            'rider'
        );

        $push(
            $order->delivered_at,
            'Delivered',
            'The Buyer delivery was completed.',
            'success'
        );

        foreach ($order->events as $event) {
            if (!$event->created_at) {
                continue;
            }

            $duplicate = collect($entries)->contains(function (array $entry) use ($event): bool {
                return abs(
                    $entry['time']->getTimestamp()
                    - $event->created_at->getTimestamp()
                ) <= 2
                    && str_contains(
                        strtolower($entry['title']),
                        strtolower((string) $event->status)
                    );
            });

            if (!$duplicate) {
                $entries[] = [
                    'time' => $event->created_at,
                    'title' => (string) ($event->title ?: $event->status ?: 'Shipment Update'),
                    'detail' => (string) ($event->message ?: 'Shipment status updated.'),
                    'kind' => 'event',
                ];
            }
        }

        usort(
            $entries,
            fn (array $a, array $b): int =>
                $b['time']->getTimestamp() <=> $a['time']->getTimestamp()
        );

        return $entries;
    }

    private function revision(MarketplaceOrder $order): string
    {
        $eventUpdated = $order->events
            ->max(fn ($event) => $event->updated_at?->timestamp ?? 0);

        return implode('|', [
            (string) ($order->updated_at?->timestamp ?? 0),
            (string) ($order->logisticsParcel?->updated_at?->timestamp ?? 0),
            (string) ($eventUpdated ?: 0),
            (string) $order->status,
            (string) ($order->logisticsParcel?->status ?? ''),
        ]);
    }
}
