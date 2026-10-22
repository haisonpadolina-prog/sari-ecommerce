<?php

namespace App\Services\Admin;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPlatformMonitoringService
{
    public const ACTIVE_DELIVERY_STATUSES = [
        'new',
        'preparing',
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function overview(): array
    {
        return [
            'sellers' => SellerAccount::query()->count(),
            'logistics' => LogisticsAccount::query()->count(),
            'buyers' => BuyerAccount::query()->count() + SocialAccount::query()->count(),
            'riders' => CourierAccount::query()->count(),
            'active_deliveries' => MarketplaceOrder::query()
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->count(),
        ];
    }

    public function sellerDirectory(string $search, string $status): LengthAwarePaginator
    {
        return SellerAccount::query()
            ->withCount([
                'products',
                'products as live_products_count' => fn (Builder $query) => $query
                    ->whereNull('archived_at')
                    ->where('moderation_status', 'approved'),
                'marketplaceOrders',
                'marketplaceOrders as delivered_orders_count' => fn (Builder $query) => $query
                    ->where('status', 'delivered'),
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%' . $search . '%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('store_name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like);
                });
            })
            ->when($status !== 'all', function (Builder $query) use ($status): void {
                if ($status === 'deactivated') {
                    $query->where(function (Builder $state): void {
                        $state->where('account_status', 'deactivated')
                            ->orWhere(function (Builder $timed): void {
                                $timed->where('account_status', 'active')
                                    ->whereNotNull('suspended_until')
                                    ->where('suspended_until', '>', now());
                            });
                    });
                    return;
                }

                if ($status === 'active') {
                    $query->where('account_status', 'active')
                        ->where(function (Builder $available): void {
                            $available->whereNull('suspended_until')
                                ->orWhere('suspended_until', '<=', now());
                        });
                    return;
                }

                $query->where('account_status', $status);
            })
            ->latest('created_at')
            ->paginate(18)
            ->withQueryString();
    }

    public function logisticsDirectory(string $search, string $status): LengthAwarePaginator
    {
        return LogisticsAccount::query()
            ->withCount([
                'facilities',
                'riders',
                'marketplaceOrders',
                'marketplaceOrders as active_deliveries_count' => fn (Builder $query) => $query
                    ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES),
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%' . $search . '%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('business_name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like);
                });
            })
            ->when($status !== 'all', fn (Builder $query) => $query->where('account_status', $status))
            ->latest('created_at')
            ->paginate(18)
            ->withQueryString();
    }

    public function riderDirectory(string $search, string $status): LengthAwarePaginator
    {
        return CourierAccount::query()
            ->with('logistics:id,business_name,first_name,last_name')
            ->withCount([
                'marketplaceOrders',
                'marketplaceOrders as active_deliveries_count' => fn (Builder $query) => $query
                    ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES),
                'marketplaceOrders as delivered_orders_count' => fn (Builder $query) => $query
                    ->where('status', 'delivered'),
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%' . $search . '%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('plate_number', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like);
                });
            })
            ->when($status !== 'all', fn (Builder $query) => $query->where('account_status', $status))
            ->latest('created_at')
            ->paginate(18)
            ->withQueryString();
    }

    public function buyerDirectory(string $search, string $status): LengthAwarePaginator
    {
        $registered = DB::table('buyer_accounts')->select([
            'id',
            DB::raw("'buyer' AS role_key"),
            'first_name',
            'last_name',
            DB::raw('NULL AS social_name'),
            'email',
            'account_status',
            'province_name',
            'municipality_name',
            'barangay_name',
            'created_at',
        ]);

        $social = DB::table('social_accounts')->select([
            'id',
            DB::raw("'social_buyer' AS role_key"),
            DB::raw('NULL AS first_name'),
            DB::raw('NULL AS last_name'),
            'name AS social_name',
            'email',
            'account_status',
            DB::raw('NULL AS province_name'),
            DB::raw('NULL AS municipality_name'),
            DB::raw('NULL AS barangay_name'),
            'created_at',
        ]);

        $union = $registered->unionAll($social);

        $paginator = DB::query()
            ->fromSub($union, 'buyer_directory')
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%' . $search . '%';
                $query->where(function ($inner) use ($like): void {
                    $inner->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('social_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like);
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('account_status', $status))
            ->orderByDesc('created_at')
            ->paginate(18)
            ->withQueryString();

        $this->attachBuyerOrderStats($paginator);

        return $paginator;
    }

    private function attachBuyerOrderStats(LengthAwarePaginator $paginator): void
    {
        $items = collect($paginator->items());

        $registeredIds = $items->where('role_key', 'buyer')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $socialIds = $items->where('role_key', 'social_buyer')->pluck('id')->map(fn ($id) => (int) $id)->all();

        $registeredStats = $this->buyerStatsForIds('buyer_account_id', $registeredIds);
        $socialStats = $this->buyerStatsForIds('buyer_social_account_id', $socialIds);

        foreach ($items as $item) {
            $stats = $item->role_key === 'social_buyer'
                ? ($socialStats[(int) $item->id] ?? null)
                : ($registeredStats[(int) $item->id] ?? null);

            $item->display_name = trim((string) ($item->social_name ?: (($item->first_name ?? '') . ' ' . ($item->last_name ?? ''))))
                ?: 'SARI Buyer';
            $item->orders_count = (int) ($stats->orders_count ?? 0);
            $item->delivered_orders_count = (int) ($stats->delivered_orders_count ?? 0);
            $item->cancelled_orders_count = (int) ($stats->cancelled_orders_count ?? 0);
        }
    }

    private function buyerStatsForIds(string $column, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return MarketplaceOrder::query()
            ->whereIn($column, $ids)
            ->selectRaw($column . ' AS buyer_key')
            ->selectRaw('COUNT(*) AS orders_count')
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS delivered_orders_count")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders_count")
            ->groupBy($column)
            ->get()
            ->keyBy(fn ($row) => (int) $row->buyer_key)
            ->all();
    }

    public function sellerDetail(SellerAccount $seller): array
    {
        $products = SellerProduct::query()
            ->where('seller_account_id', $seller->id)
            ->latest('updated_at')
            ->paginate(12, ['*'], 'products')
            ->withQueryString();

        $orders = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->latest('created_at')
            ->limit(12)
            ->get();

        $orderQuery = MarketplaceOrder::query()->where('seller_account_id', $seller->id);

        return [
            'account' => $seller,
            'roleKey' => 'seller',
            'kind' => 'seller',
            'products' => $products,
            'orders' => $orders,
            'stats' => [
                'products' => SellerProduct::query()->where('seller_account_id', $seller->id)->count(),
                'live_products' => SellerProduct::query()
                    ->where('seller_account_id', $seller->id)
                    ->whereNull('archived_at')
                    ->where('moderation_status', 'approved')
                    ->count(),
                'orders' => (clone $orderQuery)->count(),
                'delivered' => (clone $orderQuery)->where('status', 'delivered')->count(),
                'cancelled' => (clone $orderQuery)->where('status', 'cancelled')->count(),
            ],
            'activity' => $this->activity('seller', (int) $seller->id),
        ];
    }

    public function logisticsDetail(LogisticsAccount $logistics): array
    {
        $facilities = LogisticsFacility::query()
            ->where('logistics_account_id', $logistics->id)
            ->withCount('routeStops')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $riders = CourierAccount::query()
            ->where('logistics_account_id', $logistics->id)
            ->withCount([
                'marketplaceOrders',
                'marketplaceOrders as active_deliveries_count' => fn (Builder $query) => $query
                    ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES),
                'marketplaceOrders as delivered_orders_count' => fn (Builder $query) => $query
                    ->where('status', 'delivered'),
            ])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $orders = MarketplaceOrder::query()
            ->where('logistics_account_id', $logistics->id)
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->latest('updated_at')
            ->limit(20)
            ->get();

        $orderQuery = MarketplaceOrder::query()->where('logistics_account_id', $logistics->id);

        return [
            'account' => $logistics,
            'roleKey' => 'logistics',
            'kind' => 'logistics',
            'facilities' => $facilities,
            'riders' => $riders,
            'orders' => $orders,
            'stats' => [
                'facilities' => $facilities->count(),
                'active_facilities' => $facilities->where('is_active', true)->count(),
                'riders' => $riders->count(),
                'active_riders' => $riders->where('account_status', 'active')->count(),
                'active_deliveries' => (clone $orderQuery)->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)->count(),
                'delivered' => (clone $orderQuery)->where('status', 'delivered')->count(),
            ],
            'activity' => $this->activity('logistics', (int) $logistics->id),
        ];
    }

    public function buyerDetail(string $role, int $id): array
    {
        $isSocial = $role === 'social_buyer';
        $account = $isSocial
            ? SocialAccount::query()->findOrFail($id)
            : BuyerAccount::query()->findOrFail($id);

        $orderQuery = MarketplaceOrder::query()
            ->where($isSocial ? 'buyer_social_account_id' : 'buyer_account_id', $id);

        $orders = (clone $orderQuery)
            ->latest('created_at')
            ->paginate(12, ['*'], 'orders')
            ->withQueryString();

        return [
            'account' => $account,
            'roleKey' => $role,
            'kind' => 'buyer',
            'isSocialBuyer' => $isSocial,
            'orders' => $orders,
            'stats' => [
                'orders' => (clone $orderQuery)->count(),
                'delivered' => (clone $orderQuery)->where('status', 'delivered')->count(),
                'cancelled' => (clone $orderQuery)->where('status', 'cancelled')->count(),
                'in_progress' => (clone $orderQuery)->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)->count(),
                'successful_spend' => round((float) (clone $orderQuery)->where('status', 'delivered')->sum('total'), 2),
            ],
            'activity' => $this->activity($role, $id),
        ];
    }

    public function riderDetail(CourierAccount $rider): array
    {
        $ordersQuery = MarketplaceOrder::query()
            ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)]);

        $activeOrders = (clone $ordersQuery)
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->latest('updated_at')
            ->get();

        $recentOrders = (clone $ordersQuery)
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return [
            'account' => $rider->load('logistics'),
            'roleKey' => 'rider',
            'kind' => 'rider',
            'activeOrders' => $activeOrders,
            'orders' => $recentOrders,
            'currentLoad' => $this->currentRiderLoad($activeOrders),
            'stats' => [
                'assigned' => (clone $ordersQuery)->count(),
                'active' => $activeOrders->count(),
                'delivered' => (clone $ordersQuery)->where('status', 'delivered')->count(),
                'cancelled' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
            ],
            'activity' => $this->activity('rider', (int) $rider->id),
        ];
    }

    private function currentRiderLoad(Collection $orders): Collection
    {
        $load = collect();

        foreach ($orders as $order) {
            foreach ((array) ($order->items ?? []) as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $variantId = (int) ($item['variant_id'] ?? 0);
                $name = trim((string) ($item['name'] ?? 'Product')) ?: 'Product';
                $key = ($productId ?: $name) . ':' . $variantId;

                if (!$load->has($key)) {
                    $load->put($key, [
                        'product_id' => $productId ?: null,
                        'name' => $name,
                        'variant_label' => trim((string) ($item['variant_label'] ?? '')),
                        'quantity' => 0,
                        'orders' => [],
                    ]);
                }

                $row = $load->get($key);
                $row['quantity'] += max(1, (int) ($item['qty'] ?? 1));
                $row['orders'][] = (string) $order->order_number;
                $row['orders'] = array_values(array_unique($row['orders']));
                $load->put($key, $row);
            }
        }

        return $load->values();
    }

    private function activity(string $role, int $id): Collection
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return collect();
        }

        return AdminUserActivity::query()
            ->where('user_role', $role)
            ->where('user_id', $id)
            ->latest('created_at')
            ->limit(12)
            ->get();
    }
}
