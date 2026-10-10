<?php

namespace App\Services\Buyer;

use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\BuyerNotificationRead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BuyerActivityService
{
    private const SHIPPING_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly BuyerIdentityService $identity,
    ) {
    }

    public function recentOrders(Request $request, int $limit = 4): Collection
    {
        return $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->with([
                'seller:id,store_name,store_logo_path',
                'logisticsParcel:id,marketplace_order_id,status',
            ])
            ->latest('created_at')
            ->limit(max(1, $limit))
            ->get();
    }

    public function activeShipments(Request $request, ?int $limit = null): Collection
    {
        $query = $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->with([
                'seller:id,store_name,store_logo_path',
                'logisticsParcel:id,marketplace_order_id,status,received_at,sorted_at',
            ])
            ->whereIn('status', self::SHIPPING_STATUSES)
            ->latest('updated_at');

        if ($limit !== null) {
            $query->limit(max(1, $limit));
        }

        return $query->get();
    }

    public function shippingHistory(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        return $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->with([
                'seller:id,store_name,store_logo_path',
                'logisticsParcel:id,marketplace_order_id,status,received_at,sorted_at',
            ])
            ->whereIn('status', array_merge(self::SHIPPING_STATUSES, ['delivered']))
            ->latest('updated_at')
            ->paginate(max(5, min(50, $perPage)));
    }

    public function notifications(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $notifications = $this->notificationQuery($request)
            ->with([
                'order:id,order_number,seller_account_id,buyer_account_id,buyer_social_account_id,status,courier_name,created_at,updated_at',
                'seller:id,store_name',
            ])
            ->latest('id')
            ->paginate(max(5, min(50, $perPage)));

        $this->decorateReadState($request, collect($notifications->items()));

        return $notifications;
    }

    public function latestNotifications(Request $request, int $limit = 4): Collection
    {
        $notifications = $this->notificationQuery($request)
            ->with([
                'order:id,order_number,seller_account_id,buyer_account_id,buyer_social_account_id,status,courier_name,created_at,updated_at',
                'seller:id,store_name',
            ])
            ->latest('id')
            ->limit(max(1, $limit))
            ->get();

        $this->decorateReadState($request, $notifications);

        return $notifications;
    }

    public function unreadNotificationCount(Request $request): int
    {
        $buyerKey = $this->identity->key($request);

        return $this->notificationQuery($request)
            ->whereNotIn(
                'marketplace_order_events.id',
                BuyerNotificationRead::query()
                    ->select('marketplace_order_event_id')
                    ->where('buyer_key', $buyerKey)
                    ->whereNotNull('read_at')
            )
            ->count();
    }

    public function markRead(Request $request, MarketplaceOrderEvent $event): void
    {
        abort_unless($this->eventBelongsToBuyer($request, $event), 403);

        BuyerNotificationRead::query()->updateOrCreate(
            [
                'buyer_key' => $this->identity->key($request),
                'marketplace_order_event_id' => $event->id,
            ],
            ['read_at' => now()]
        );
    }

    public function markAllRead(Request $request): void
    {
        $buyerKey = $this->identity->key($request);
        $now = now();

        $this->notificationQuery($request)
            ->select('marketplace_order_events.id')
            ->orderBy('marketplace_order_events.id')
            ->chunkById(200, function (Collection $events) use ($buyerKey, $now): void {
                $rows = $events->map(fn (MarketplaceOrderEvent $event) => [
                    'buyer_key' => $buyerKey,
                    'marketplace_order_event_id' => $event->id,
                    'read_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                BuyerNotificationRead::query()->upsert(
                    $rows,
                    ['buyer_key', 'marketplace_order_event_id'],
                    ['read_at', 'updated_at']
                );
            }, 'marketplace_order_events.id', 'id');
    }

    public function eventBelongsToBuyer(Request $request, MarketplaceOrderEvent $event): bool
    {
        $this->identity->guard($request);

        if (!in_array($event->audience, ['buyer', 'both'], true)) {
            return false;
        }

        $event->loadMissing('order');
        $order = $event->order;

        if (!$order) {
            return false;
        }

        if ($accountId = $this->identity->accountId($request)) {
            return (int) $order->buyer_account_id === $accountId;
        }

        return (int) $order->buyer_social_account_id === (int) $this->identity->socialId($request);
    }

    public function shippingStatusLabel(MarketplaceOrder $order): string
    {
        return match ($order->status) {
            'ready_for_pickup' => 'Waiting for pickup',
            'courier_accepted' => 'Rider assigned',
            'heading_pickup' => 'Rider heading to seller',
            'arrived_pickup' => 'Rider at seller',
            'in_transit' => 'In transit',
            'arrived_buyer' => 'Rider arrived',
            'delivered' => 'Delivered',
            default => $order->statusLabel(),
        };
    }

    public function shippingProgress(MarketplaceOrder $order): int
    {
        return match ($order->status) {
            'ready_for_pickup' => 15,
            'courier_accepted' => 30,
            'heading_pickup' => 42,
            'arrived_pickup' => 55,
            'in_transit' => 75,
            'arrived_buyer' => 92,
            'delivered' => 100,
            default => 0,
        };
    }

    private function notificationQuery(Request $request): Builder
    {
        $this->identity->guard($request);

        return MarketplaceOrderEvent::query()
            ->whereIn('audience', ['buyer', 'both'])
            ->whereHas('order', function (Builder $orderQuery) use ($request): void {
                if ($accountId = $this->identity->accountId($request)) {
                    $orderQuery->where('buyer_account_id', $accountId);

                    return;
                }

                $orderQuery->where('buyer_social_account_id', $this->identity->socialId($request));
            });
    }

    private function decorateReadState(Request $request, Collection $notifications): void
    {
        if ($notifications->isEmpty()) {
            return;
        }

        $readIds = BuyerNotificationRead::query()
            ->where('buyer_key', $this->identity->key($request))
            ->whereIn('marketplace_order_event_id', $notifications->pluck('id'))
            ->whereNotNull('read_at')
            ->pluck('marketplace_order_event_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        $notifications->each(function (MarketplaceOrderEvent $event) use ($readIds): void {
            $event->setAttribute('is_read', $readIds->has((int) $event->id));
        });
    }
}
