<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\BuyerNotificationRead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MobileBuyerNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'archived' => ['nullable', 'boolean'],
        ]);
        $archived = $request->boolean('archived');

        $notifications = $this->notificationQuery($buyer, $archived)
            ->with([
                'order:id,order_number,seller_account_id,buyer_account_id,status,courier_name,created_at,updated_at',
                'seller:id,store_name',
            ])
            ->latest('id')
            ->paginate((int) ($validated['per_page'] ?? 20));

        $readIds = BuyerNotificationRead::query()
            ->where('buyer_key', $this->buyerKey($buyer))
            ->whereIn('marketplace_order_event_id', collect($notifications->items())->pluck('id'))
            ->whereNotNull('read_at')
            ->pluck('marketplace_order_event_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        return response()->json([
            'success' => true,
            'data' => collect($notifications->items())->map(function (MarketplaceOrderEvent $event) use ($readIds): array {
                return [
                    'id' => (int) $event->id,
                    'order_id' => (int) $event->marketplace_order_id,
                    'order_number' => $event->order?->order_number,
                    'type' => $event->type,
                    'title' => $event->title,
                    'message' => $event->message,
                    'status' => $event->status,
                    'store_name' => $event->seller?->store_name,
                    'is_read' => $readIds->has((int) $event->id),
                    'created_at' => $event->created_at?->toIso8601String(),
                ];
            })->values(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'links' => [
                'next' => $notifications->nextPageUrl(),
                'prev' => $notifications->previousPageUrl(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function summary(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $readIds = BuyerNotificationRead::query()
            ->select('marketplace_order_event_id')
            ->where('buyer_key', $this->buyerKey($buyer))
            ->whereNotNull('read_at');

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $this->notificationQuery($buyer, false)
                    ->whereNotIn('marketplace_order_events.id', $readIds)
                    ->count(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function read(Request $request, MarketplaceOrderEvent $event): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardEvent($buyer, $event);

        BuyerNotificationRead::query()->updateOrCreate(
            [
                'buyer_key' => $this->buyerKey($buyer),
                'marketplace_order_event_id' => $event->id,
            ],
            ['read_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
    }

    public function readAll(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $buyerKey = $this->buyerKey($buyer);
        $now = now();

        $this->notificationQuery($buyer, false)
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

        return response()->json(['success' => true, 'message' => 'Notifications marked as read.']);
    }

    private function notificationQuery(BuyerAccount $buyer, bool $archived = false): Builder
    {
        $buyerKey = $this->buyerKey($buyer);

        $query = MarketplaceOrderEvent::query()
            ->whereIn('audience', ['buyer', 'both'])
            ->whereHas('order', fn (Builder $orders) => $orders->where('buyer_account_id', $buyer->id))
            ->whereNotExists(function (QueryBuilder $readQuery) use ($buyerKey): void {
                $readQuery->selectRaw('1')
                    ->from('buyer_notification_reads')
                    ->whereColumn('buyer_notification_reads.marketplace_order_event_id', 'marketplace_order_events.id')
                    ->where('buyer_notification_reads.buyer_key', $buyerKey)
                    ->whereNotNull('buyer_notification_reads.deleted_at');
            });

        $archiveFilter = function (QueryBuilder $readQuery) use ($buyerKey): void {
            $readQuery->selectRaw('1')
                ->from('buyer_notification_reads')
                ->whereColumn('buyer_notification_reads.marketplace_order_event_id', 'marketplace_order_events.id')
                ->where('buyer_notification_reads.buyer_key', $buyerKey)
                ->whereNotNull('buyer_notification_reads.archived_at');
        };

        return $archived ? $query->whereExists($archiveFilter) : $query->whereNotExists($archiveFilter);
    }

    private function guardEvent(BuyerAccount $buyer, MarketplaceOrderEvent $event): void
    {
        abort_unless(
            in_array($event->audience, ['buyer', 'both'], true)
                && $event->order()->where('buyer_account_id', $buyer->id)->exists(),
            404
        );
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }

    private function buyerKey(BuyerAccount $buyer): string
    {
        return 'account-'.$buyer->id;
    }
}
