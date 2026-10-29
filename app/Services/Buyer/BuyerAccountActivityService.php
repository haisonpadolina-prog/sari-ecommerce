<?php

namespace App\Services\Buyer;

use App\Models\Platform\AdminUserActivity;
use App\Models\Platform\BuyerAccountActivityState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BuyerAccountActivityService
{
    /**
     * Only actions submitted by buyers. Background seller/courier order events
     * remain on the Notifications page and are never used in this activity list.
     */
    private const TITLES = [
        'login' => 'Signed in to SARI',
        'logout' => 'Signed out of SARI',
        'buyer_account_update' => 'Profile update submitted',
        'buyer_account_addresses_store' => 'Delivery address submitted',
        'buyer_account_addresses_update' => 'Address update submitted',
        'buyer_account_addresses_default' => 'Default address change submitted',
        'buyer_account_addresses_use_registration' => 'Registration address selected',
        'buyer_account_addresses_destroy' => 'Address removal submitted',
        'buyer_cart_items_store' => 'Cart addition submitted',
        'buyer_cart_items_update' => 'Cart update submitted',
        'buyer_cart_items_destroy' => 'Cart removal submitted',
        'buyer_checkout_store' => 'Checkout submitted',
        'buyer_orders_cancel' => 'Order cancellation requested',
        'buyer_orders_reviews_store' => 'Product review submitted',
        'buyer_messages_send' => 'Seller message submitted',
    ];

    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function paginate(Request $request, int $perPage = 10, bool $archived = false): LengthAwarePaginator
    {
        $results = $this->activities($request, $archived)
            ->orderByDesc('id')
            ->paginate(max(5, min(50, $perPage)))
            ->withQueryString();

        $ids = collect($results->items())->pluck('id');
        $states = BuyerAccountActivityState::query()
            ->where('buyer_key', $this->identity->key($request))
            ->whereIn('admin_user_activity_id', $ids)
            ->get()
            ->keyBy('admin_user_activity_id');

        foreach ($results->items() as $event) {
            $event->setAttribute('is_read', $states->get($event->id)?->read_at !== null);
            $event->setAttribute('display_title', $this->title((string) $event->action));
            $event->setAttribute('display_message', $this->message((string) $event->action));
            $event->setAttribute('destination_url', $this->destination((string) $event->action));
        }

        return $results;
    }

    public function unreadCount(Request $request): int
    {
        return $this->activities($request, false)
            ->whereNotIn('id', BuyerAccountActivityState::query()
                ->select('admin_user_activity_id')
                ->where('buyer_key', $this->identity->key($request))
                ->whereNotNull('read_at'))
            ->count();
    }

    public function markRead(Request $request, AdminUserActivity $event): void
    {
        $this->owned($request, $event);
        BuyerAccountActivityState::query()->updateOrCreate($this->keys($request, $event), ['read_at' => now()]);
    }

    public function markAllRead(Request $request): void
    {
        $buyerKey = $this->identity->key($request);
        $now = now();

        $this->activities($request, false)
            ->orderBy('id')
            ->chunkById(200, function (Collection $events) use ($buyerKey, $now): void {
                BuyerAccountActivityState::query()->upsert(
                    $events->map(static fn (AdminUserActivity $event) => [
                        'buyer_key' => $buyerKey,
                        'admin_user_activity_id' => $event->id,
                        'read_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all(),
                    ['buyer_key', 'admin_user_activity_id'],
                    ['read_at', 'updated_at']
                );
            });
    }

    public function archive(Request $request, AdminUserActivity $event): void
    {
        $this->owned($request, $event);
        abort_if(BuyerAccountActivityState::query()
            ->where($this->keys($request, $event))->whereNotNull('deleted_at')->exists(), 404);
        BuyerAccountActivityState::query()->updateOrCreate($this->keys($request, $event), [
            'read_at' => now(), 'archived_at' => now(),
        ]);
    }

    public function restore(Request $request, AdminUserActivity $event): void
    {
        $this->owned($request, $event);
        BuyerAccountActivityState::query()->where($this->keys($request, $event))
            ->whereNotNull('archived_at')->whereNull('deleted_at')
            ->firstOrFail()->update(['archived_at' => null]);
    }

    public function deleteForBuyer(Request $request, AdminUserActivity $event): void
    {
        $this->owned($request, $event);
        BuyerAccountActivityState::query()->updateOrCreate($this->keys($request, $event), [
            'read_at' => now(), 'archived_at' => null, 'deleted_at' => now(),
        ]);
    }

    public function buyerProfile(Request $request): array
    {
        $this->identity->guard($request);
        $buyer = $this->identity->account($request);
        $social = $buyer ? null : $this->identity->social($request);
        $name = $buyer
            ? trim((string) $buyer->first_name . ' ' . (string) $buyer->last_name)
            : trim((string) ($social?->name ?? $request->session()->get('buyer_name', '')));
        $name = $name !== '' ? $name : 'SARI Buyer';
        $path = trim((string) ($buyer?->profile_image_path ?? ''));
        $image = $path !== '' ? Storage::disk('public')->url($path) : ($social?->avatar_url ?: null);
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = mb_substr($parts[0] ?? 'B', 0, 1);
        $last = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';

        return [
            'name' => $name,
            'image' => $image,
            'initials' => mb_strtoupper($first . $last),
        ];
    }

    private function activities(Request $request, bool $archived): Builder
    {
        $this->identity->guard($request);
        $accountId = $this->identity->accountId($request);
        $role = $accountId ? 'buyer' : 'social_buyer';
        $id = $accountId ?: $this->identity->socialId($request);
        $buyerKey = $this->identity->key($request);

        $states = BuyerAccountActivityState::query()
            ->select('admin_user_activity_id')
            ->where('buyer_key', $buyerKey)
            ->whereNull('deleted_at');
        if ($archived) {
            $states->whereNotNull('archived_at');
        } else {
            $states->whereNull('archived_at');
        }

        $query = AdminUserActivity::query()
            ->where('user_role', $role)
            ->where('user_id', $id)
            ->whereIn('action', array_keys(self::TITLES));

        return $archived
            ? $query->whereIn('id', $states)
            : $query->whereNotIn('id', BuyerAccountActivityState::query()
                ->select('admin_user_activity_id')
                ->where('buyer_key', $buyerKey)
                ->where(function (Builder $query): void {
                    $query->whereNotNull('archived_at')->orWhereNotNull('deleted_at');
                }));
    }

    private function owned(Request $request, AdminUserActivity $event): void
    {
        $this->identity->guard($request);
        $accountId = $this->identity->accountId($request);
        abort_unless(
            $event->user_role === ($accountId ? 'buyer' : 'social_buyer')
            && (int) $event->user_id === (int) ($accountId ?: $this->identity->socialId($request))
            && array_key_exists((string) $event->action, self::TITLES),
            403
        );
    }

    private function keys(Request $request, AdminUserActivity $event): array
    {
        return ['buyer_key' => $this->identity->key($request), 'admin_user_activity_id' => $event->id];
    }

    private function title(string $action): string
    {
        return self::TITLES[$action] ?? 'Account activity';
    }

    private function message(string $action): string
    {
        return match ($action) {
            'login' => 'You signed in to your buyer account.',
            'logout' => 'You signed out of your buyer account.',
            'buyer_account_update' => 'You submitted changes to your buyer profile.',
            'buyer_account_addresses_store' => 'You submitted a new delivery address.',
            'buyer_account_addresses_update' => 'You submitted changes to a saved address.',
            'buyer_account_addresses_default' => 'You submitted a new default delivery address.',
            'buyer_account_addresses_use_registration' => 'You submitted your registration address for delivery.',
            'buyer_account_addresses_destroy' => 'You submitted a request to remove a saved address.',
            'buyer_cart_items_store' => 'You submitted an item to your shopping cart.',
            'buyer_cart_items_update' => 'You submitted a quantity change in your cart.',
            'buyer_cart_items_destroy' => 'You submitted an item removal from your cart.',
            'buyer_checkout_store' => 'You submitted a checkout order.',
            'buyer_orders_cancel' => 'You submitted a request to cancel an order.',
            'buyer_orders_reviews_store' => 'You submitted a review for a purchase.',
            'buyer_messages_send' => 'You submitted a message to a seller.',
            default => 'You performed an action on your buyer account.',
        };
    }

    private function destination(string $action): string
    {
        if (str_starts_with($action, 'buyer_account_')) {
            return route('buyer.account');
        }
        if (str_starts_with($action, 'buyer_cart_')) {
            return route('buyer.cart');
        }
        if (str_starts_with($action, 'buyer_messages_')) {
            return route('buyer.messages');
        }
        if (str_starts_with($action, 'buyer_checkout_') || str_starts_with($action, 'buyer_orders_')) {
            return route('buyer.orders');
        }
        return route('buyer.account');
    }
}
