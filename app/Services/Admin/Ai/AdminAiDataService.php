<?php

namespace App\Services\Admin\Ai;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\OrderCommission;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\AdminUserActivity;
use App\Models\Registration\RegistrationApplication;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminAiDataService
{
    public function run(string $operation, array $input, ?AdminAccount $admin = null): array
    {
        if ($admin) {
            app(AdminAiPermissionService::class)->authorizeRead($admin, $operation);
        }
        if (in_array($operation, AdminAiOperationsService::OPERATIONS, true)) {
            abort_unless($admin, 403);

            return app(AdminAiOperationsService::class)->run($admin, $operation, $input);
        }
        if ($admin && app(AdminAiPermissionService::class)->role($admin) === 'product_reviewer' && $operation === 'accounts') {
            abort_unless(($input['role'] ?? '') === 'seller', 403);
        }
        $common = ['query', 'id', 'status', 'offset'];
        $allowed = match ($operation) {
            'products' => [...$common, 'seller_id', 'low_stock'],
            'accounts' => [...$common, 'role', 'municipality'],
            'orders' => [...$common, 'seller_id', 'buyer_id', 'social_buyer_id', 'logistics_id', 'municipality', 'payment_method', 'min_total', 'from', 'to'],
            'analytics' => ['seller_id', 'buyer_id', 'social_buyer_id', 'logistics_id', 'municipality', 'payment_method', 'min_total', 'status', 'from', 'to', 'metric', 'group'],
            'commissions' => ['seller_id', 'status', 'from', 'to'],
            'journey' => ['id'], 'locations' => ['municipality', 'logistics_id', 'offset'],
            'audit' => ['query', 'offset', 'from', 'to'],
            'registrations' => ['status', 'municipality', 'offset', 'from', 'to'],
            'alerts', 'guide' => [], default => [],
        };
        if (array_diff(array_keys($input), $allowed)) {
            throw new \InvalidArgumentException('Unsupported filter for this operation.');
        }
        $data = match ($operation) {
            'products' => $this->products($input),
            'accounts' => $this->accounts($input),
            'orders' => $this->orders($input),
            'journey' => $this->journey($input),
            'analytics' => $this->analytics($input),
            'locations' => $this->locations($input),
            'alerts' => $this->alerts(),
            'audit' => $this->audit($input),
            'guide' => $this->guide(),
            'commissions' => $this->commissions($input),
            'registrations' => $this->registrations($input),
            default => throw new \InvalidArgumentException('Unknown read operation.'),
        };

        return array_merge($data, ['as_of' => now()->toIso8601String(), 'source' => $operation]);
    }

    private function searchInput(array $input): array
    {
        return Validator::make($input, [
            'query' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'id' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'max:40'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'seller_id' => ['nullable', 'integer', 'min:1'],
            'buyer_id' => ['nullable', 'integer', 'min:1'],
            'social_buyer_id' => ['nullable', 'integer', 'min:1'],
            'logistics_id' => ['nullable', 'integer', 'min:1'],
            'low_stock' => ['nullable', 'boolean'],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'min_total' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
        ])->validate();
    }

    private function dateRange($query, array $p)
    {
        if (! empty($p['from'])) {
            $query->where('created_at', '>=', CarbonImmutable::parse($p['from'], 'Asia/Manila')->startOfDay()->utc());
        }
        if (! empty($p['to'])) {
            $query->where('created_at', '<=', CarbonImmutable::parse($p['to'], 'Asia/Manila')->endOfDay()->utc());
        }

        return $query;
    }

    private function page($query, array $input, callable $map): array
    {
        $offset = (int) ($input['offset'] ?? 0);
        $total = (clone $query)->count();
        $rows = $query->orderByDesc('id')->offset($offset)->limit(20)->get()->map($map)->values()->all();

        return ['rows' => $rows, 'total' => $total, 'offset' => $offset, 'limit' => 20,
            'has_more' => $offset + count($rows) < $total, 'filters' => $input];
    }

    private function products(array $input): array
    {
        $p = $this->searchInput($input);
        $q = SellerProduct::query()->with('seller:id,store_name')->select([
            'id', 'seller_account_id', 'name', 'price', 'stock', 'low_stock_threshold',
            'moderation_status', 'archived_at', 'updated_at',
        ]);
        if (! empty($p['id'])) {
            $q->whereKey($p['id']);
        }
        if (! empty($p['query'])) {
            $q->where('name', 'like', '%'.$p['query'].'%');
        }
        if (! empty($p['status'])) {
            $p['status'] === 'review' ? $q->whereIn('moderation_status', ['pending', 'flagged'])->whereNull('archived_at') : $q->where('moderation_status', $p['status']);
        }
        if (! empty($p['seller_id'])) {
            $q->where('seller_account_id', $p['seller_id']);
        }
        if (! empty($p['low_stock'])) {
            $q->whereColumn('stock', '<=', 'low_stock_threshold')->whereNull('archived_at');
        }

        return $this->page($q, $p, fn ($r) => [
            'id' => $r->id, 'name' => $r->name, 'seller_id' => $r->seller_account_id,
            'seller' => $r->seller?->store_name, 'price' => $r->price, 'stock' => $r->stock,
            'status' => $r->moderation_status, 'archived' => $r->archived_at !== null,
            'image_url' => route('admin.compliance.products.media', ['product' => $r->id, 'kind' => 'cover'], false),
            'url' => route('admin.product-reviews', [], false),
        ]);
    }

    private function accounts(array $input): array
    {
        $role = Validator::make($input, ['role' => ['required', Rule::in(['seller', 'buyer', 'social_buyer', 'logistics', 'rider'])]])->validate()['role'];
        $p = $this->searchInput($input);
        $class = match ($role) {
            'seller' => SellerAccount::class, 'buyer' => BuyerAccount::class,
            'social_buyer' => SocialAccount::class, 'logistics' => LogisticsAccount::class,
            'rider' => CourierAccount::class,
        };
        $columns = $role === 'social_buyer'
            ? ['id', 'name', 'account_status']
            : ['id', 'first_name', 'last_name', 'account_status', 'province_name', 'municipality_name', 'barangay_name'];
        if ($role === 'seller') {
            $columns[] = 'store_name';
        }
        if ($role === 'logistics') {
            $columns[] = 'business_name';
        }
        $q = $class::query()->select($columns);
        if (! empty($p['id'])) {
            $q->whereKey($p['id']);
        }
        if (! empty($p['status'])) {
            $q->where('account_status', $p['status']);
        }
        if (! empty($p['municipality']) && $role !== 'social_buyer') {
            $q->where('municipality_name', $p['municipality']);
        }
        if (! empty($p['query'])) {
            $q->where(function ($q) use ($p, $role) {
                if ($role === 'social_buyer') {
                    $q->where('name', 'like', '%'.$p['query'].'%');

                    return;
                }
                $q->where('first_name', 'like', '%'.$p['query'].'%')->orWhere('last_name', 'like', '%'.$p['query'].'%');
                if ($role === 'seller') {
                    $q->orWhere('store_name', 'like', '%'.$p['query'].'%');
                }
                if ($role === 'logistics') {
                    $q->orWhere('business_name', 'like', '%'.$p['query'].'%');
                }
            });
        }

        return $this->page($q, $p, function ($r) use ($role) {
            $url = match ($role) {
                'seller' => route('admin.monitoring.sellers.show', ['seller' => $r->id], false),
                'logistics' => route('admin.monitoring.logistics.show', ['logistics' => $r->id], false),
                'rider' => route('admin.monitoring.riders.show', ['rider' => $r->id], false),
                default => route('admin.monitoring.buyers.show', ['role' => $role, 'id' => $r->id], false),
            };

            return ['id' => $r->id, 'role' => $role, 'name' => $r->name ?? trim($r->first_name.' '.$r->last_name),
                'store' => $r->store_name ?? $r->business_name, 'status' => $r->account_status,
                'province' => $r->province_name, 'municipality' => $r->municipality_name,
                'barangay' => $r->barangay_name, 'url' => $url];
        });
    }

    private function orderQuery(array $p)
    {
        $q = MarketplaceOrder::query();
        foreach (['seller_id' => 'seller_account_id', 'buyer_id' => 'buyer_account_id', 'social_buyer_id' => 'buyer_social_account_id', 'logistics_id' => 'logistics_account_id', 'status' => 'status', 'payment_method' => 'payment_method'] as $key => $column) {
            if (! empty($p[$key])) {
                $q->where($column, $p[$key]);
            }
        }
        if (! empty($p['id'])) {
            $q->whereKey($p['id']);
        }
        if (! empty($p['query'])) {
            $q->where('order_number', 'like', '%'.$p['query'].'%');
        }
        if (! empty($p['municipality'])) {
            $q->where('buyer_address', 'like', '%'.$p['municipality'].'%');
        }
        if (isset($p['min_total'])) {
            $q->where('total', '>=', $p['min_total']);
        }

        return $this->dateRange($q, $p);
    }

    private function orders(array $input): array
    {
        $p = $this->searchInput($input);

        return $this->page($this->orderQuery($p)->select([
            'id', 'order_number', 'status', 'total', 'payment_status', 'payment_method',
            'seller_account_id', 'buyer_account_id', 'logistics_account_id', 'updated_at',
        ]), $p, fn ($r) => array_merge($r->toArray(), ['url' => route('admin.monitoring', [], false)]));
    }

    private function journey(array $input): array
    {
        $id = Validator::make($input, ['id' => ['required', 'integer', 'min:1']])->validate()['id'];
        $order = MarketplaceOrder::query()->findOrFail($id);

        return ['rows' => [[
            'id' => $order->id, 'order_number' => $order->order_number, 'status' => $order->statusLabel(),
            'seller_id' => $order->seller_account_id, 'buyer_id' => $order->buyer_account_id,
            'social_buyer_id' => $order->buyer_social_account_id, 'logistics_id' => $order->logistics_account_id,
            'courier' => $order->courier_name, 'updated_at' => $order->updated_at?->toIso8601String(),
            'url' => route('admin.monitoring', [], false),
        ]], 'events' => $order->events()->orderByDesc('id')->limit(50)
            ->get(['id', 'title', 'message', 'status', 'created_at'])->toArray(),
            'note' => 'Latest recorded events only; timestamps are not live GPS. At most 50 events.'];
    }

    private function analytics(array $input): array
    {
        $v = Validator::make($input, [
            'from' => ['required', 'date_format:Y-m-d'], 'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
            'metric' => ['required', Rule::in(['orders', 'gmv', 'delivery_fees'])],
            'group' => ['required', Rule::in(['day', 'month', 'status', 'seller', 'logistics'])],
        ])->validate();
        $from = CarbonImmutable::parse($v['from'], 'Asia/Manila')->startOfDay();
        $to = CarbonImmutable::parse($v['to'], 'Asia/Manila')->endOfDay();
        abort_if($from->diffInDays($to) > 366, 422, 'Select at most 366 days.');
        $p = $this->searchInput($input);
        $timestamp = $v['metric'] === 'orders' ? 'created_at' : 'delivered_at';
        // Date filtering must use the metric timestamp, not orderQuery's created-at default.
        $scope = $p;
        unset($scope['from'], $scope['to']);
        $q = $this->orderQuery($scope);
        $undated = 0;
        if ($v['metric'] !== 'orders') {
            $q->where('status', 'delivered');
            $undated = (clone $q)->whereNull('delivered_at')->count();
        }
        $q->whereBetween($timestamp, [$from->utc(), $to->utc()]);
        $matchingRecords = (clone $q)->count();
        $localDate = match (DB::connection()->getDriverName()) {
            'sqlite' => "DATE($timestamp, '+8 hours')",
            'mysql', 'mariadb' => "DATE(DATE_ADD($timestamp, INTERVAL 8 HOUR))",
            'pgsql' => "DATE($timestamp + INTERVAL '8 hours')",
            default => throw new \InvalidArgumentException('Unsupported analytics database driver.'),
        };
        $expression = match ($v['group']) {
            'day', 'month' => $localDate, 'status' => 'status',
            'seller' => 'seller_account_id', 'logistics' => 'logistics_account_id',
        };
        $aggregate = match ($v['metric']) {
            'orders' => 'COUNT(*)', 'gmv' => 'SUM(total)', 'delivery_fees' => 'SUM(delivery_fee)',
        };
        $result = $q->selectRaw($expression.' AS bucket, '.$aggregate.' AS value')
            ->groupByRaw($expression)->orderBy('bucket')->limit(1001)->get();
        abort_if($result->count() > 1000, 422, 'Too many groups. Narrow the filters.');
        $groups = [];
        // An empty day is a real zero in a successfully queried period, not a fabricated sale.
        if (in_array($v['group'], ['day', 'month'], true)) {
            $cursor = $v['group'] === 'month' ? $from->startOfMonth() : $from->startOfDay();
            while ($cursor->lte($to)) {
                $groups[$cursor->format($v['group'] === 'month' ? 'Y-m' : 'Y-m-d')] = 0.0;
                $cursor = $v['group'] === 'month' ? $cursor->addMonth() : $cursor->addDay();
            }
        }
        foreach ($result as $row) {
            $key = (string) ($row->bucket ?? 'Unassigned');
            if ($v['group'] === 'month') {
                $key = substr($key, 0, 7);
            }
            $groups[$key] = ($groups[$key] ?? 0) + (float) $row->value;
        }
        $rows = collect($groups)->map(fn ($value, $label) => ['label' => (string) $label, 'value' => round($value, 2)])->values()->all();

        return ['rows' => $rows, 'chart' => [
            'title' => $v['metric'].' by '.$v['group'], 'rows' => $rows,
            'metric' => $v['metric'], 'group' => $v['group'],
            'unit' => $v['metric'] === 'orders' ? 'orders' : 'PHP',
            'from' => $v['from'], 'to' => $v['to'], 'timezone' => 'Asia/Manila',
            'date_basis' => $timestamp, 'matching_records' => $matchingRecords,
            'undated_delivered_records' => $undated,
        ], 'total' => round(array_sum($groups), 2), 'matching_records' => $matchingRecords,
            'filters' => array_merge($p, $v),
            'definition' => 'Manila date filtering and day/month buckets. Orders use created_at; delivered GMV/delivery fees use delivered_at and status=delivered. GMV includes fees, not profit. Dates without matching activity are zero. Undated delivered records are excluded from dated metrics; the undated count is across the other filters, not this date range.',
            'url' => route('admin.reports', [], false)];
    }

    private function locations(array $input): array
    {
        $p = $this->searchInput($input);
        $q = LogisticsFacility::query()->select(['id', 'name', 'logistics_account_id', 'municipality_name', 'province_name', 'latitude', 'longitude', 'is_active']);
        if (! empty($p['municipality'])) {
            $q->where('municipality_name', $p['municipality']);
        }
        if (! empty($p['logistics_id'])) {
            $q->where('logistics_account_id', $p['logistics_id']);
        }

        return $this->page($q, $p, fn ($r) => array_merge($r->toArray(), [
            'url' => route('admin.monitoring.logistics.show', ['logistics' => $r->logistics_account_id], false),
        ]));
    }

    public function alerts(): array
    {
        $hours = max(1, (int) config('admin_ai.stale_order_hours'));
        $stale = MarketplaceOrder::query()->whereNotIn('status', ['delivered', 'cancelled'])
            ->where('updated_at', '<', now()->subHours($hours));
        $recentCancelled = MarketplaceOrder::query()->where('status', 'cancelled')->whereBetween('cancelled_at', [now()->subDays(7), now()])->count();
        $priorCancelled = MarketplaceOrder::query()->where('status', 'cancelled')->whereBetween('cancelled_at', [now()->subDays(14), now()->subDays(7)])->count();
        $recentGmv = (float) MarketplaceOrder::query()->where('status', 'delivered')->whereBetween('delivered_at', [now()->subDays(7), now()])->sum('total');
        $priorGmv = (float) MarketplaceOrder::query()->where('status', 'delivered')->whereBetween('delivered_at', [now()->subDays(14), now()->subDays(7)])->sum('total');
        $drop = $priorGmv > 0 ? round(max(0, ($priorGmv - $recentGmv) / $priorGmv * 100), 1) : 0;

        return ['rows' => [
            ['label' => 'Delivered GMV drop vs previous 7 days, percent (alert at 20%)', 'value' => $drop >= 20 ? $drop : 0, 'url' => route('admin.reports', [], false)],
            ['label' => 'Cancellations in last 7 days (alert if >=5 and >=50% increase)', 'value' => $recentCancelled >= 5 && $recentCancelled >= max(1, $priorCancelled) * 1.5 ? $recentCancelled : 0, 'url' => route('admin.monitoring', [], false)],
            ['label' => 'Active orders with no update for '.$hours.' hours', 'value' => (clone $stale)->count(), 'url' => route('admin.monitoring', [], false)],
            ['label' => 'Low stock products', 'value' => SellerProduct::query()->whereNull('archived_at')->whereColumn('stock', '<=', 'low_stock_threshold')->count(), 'url' => route('admin.product-reviews', [], false)],
            ['label' => 'Pending applications older than 3 days', 'value' => RegistrationApplication::query()->where('status', 'pending')->where('created_at', '<', now()->subDays(3))->count(), 'url' => route('admin.registrations', [], false)],
            ['label' => 'Products awaiting review', 'value' => SellerProduct::query()->whereNull('archived_at')->whereIn('moderation_status', ['pending', 'flagged'])->count(), 'url' => route('admin.product-reviews', [], false)],
        ], 'stale_orders' => $stale->orderBy('updated_at')->limit(10)->get(['id', 'order_number', 'status', 'updated_at'])->toArray(),
            'note' => 'Stale means no recorded update, not a confirmed missed delivery promise.'];
    }

    private function audit(array $input): array
    {
        $p = $this->searchInput($input);
        $q = AdminUserActivity::query()->where('user_role', 'admin');
        if (! empty($p['query'])) {
            $q->where('action', 'like', '%'.$p['query'].'%');
        }
        $q = $this->dateRange($q, $p);

        return $this->page($q, $p, fn ($r) => [
            'id' => $r->id, 'admin_id' => $r->admin_account_id, 'action' => $r->action,
            'description' => $r->description, 'created_at' => $r->created_at?->toIso8601String(),
            'url' => route('admin.recent-activity', [], false),
        ]);
    }

    private function commissions(array $input): array
    {
        $v = Validator::make($input, [
            'from' => ['required', 'date_format:Y-m-d'], 'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ])->validate();
        $from = CarbonImmutable::parse($v['from'], 'Asia/Manila')->startOfDay();
        $to = CarbonImmutable::parse($v['to'], 'Asia/Manila')->endOfDay();
        abort_if($from->diffInDays($to) > 366, 422, 'Select at most 366 days.');
        $p = $this->searchInput($input);
        $q = OrderCommission::query()->whereBetween('earned_at', [$from->utc(), $to->utc()]);
        if (! empty($p['seller_id'])) {
            $q->where('seller_account_id', $p['seller_id']);
        }
        if (! empty($p['status'])) {
            $q->where('status', $p['status']);
        }
        $rows = $q->selectRaw('seller_account_id AS label, SUM(net_commission) AS value')->groupBy('seller_account_id')->orderBy('seller_account_id')->limit(1001)->get()->toArray();
        abort_if(count($rows) > 1000, 422, 'Too many sellers. Narrow the filters.');

        return ['rows' => $rows, 'chart' => ['title' => 'Net commission by seller ID', 'rows' => $rows, 'unit' => 'PHP'],
            'filters' => array_merge($p, $v), 'total' => round(array_sum(array_column($rows, 'value')), 2),
            'definition' => 'SUM(net_commission), earned_at within Manila dates; recorded commission, not cash collected. Includes statuses unless filtered.',
            'url' => route('admin.commissions', [], false)];
    }

    private function registrations(array $input): array
    {
        $p = $this->searchInput($input);
        $q = RegistrationApplication::query()->select(['id', 'role', 'first_name', 'last_name', 'business_name', 'status', 'municipality_name', 'created_at']);
        if (! empty($p['status'])) {
            $q->where('status', $p['status']);
        }
        if (! empty($p['municipality'])) {
            $q->where('municipality_name', $p['municipality']);
        }
        $q = $this->dateRange($q, $p);

        return $this->page($q, $p, fn ($r) => array_merge($r->toArray(), ['url' => route('admin.registrations', [], false)]));
    }

    private function guide(): array
    {
        return ['workflows' => [
            ['task' => 'Review products', 'steps' => 'Open Product Reviews, inspect product/media/seller, then approve or reject.', 'url' => route('admin.product-reviews', [], false)],
            ['task' => 'Investigate delivery', 'steps' => 'Search order, inspect recorded journey, then check assigned logistics provider in Monitoring.', 'url' => route('admin.monitoring', [], false)],
            ['task' => 'Manage announcements', 'steps' => 'Create draft, review audience and dates, then publish.', 'url' => route('admin.announcements', [], false)],
            ['task' => 'Financial reports', 'steps' => 'Open Reports or Commissions for authoritative financial records.', 'url' => route('admin.reports', [], false)],
        ], 'limitations' => 'No refund execution, arbitrary SQL, shell access, permanent deletion, live GPS, or generated product images. Existing product photos only.'];
    }
}
