<?php

namespace App\Services\Admin;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminSecurityCenterService
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'new', 'preparing', 'ready_for_pickup', 'courier_accepted',
        'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer',
    ];

    private const SECURITY_ACTIONS = [
        'failed_login',
        'force_logout',
        'security_alert_acknowledged',
    ];

    public function centerData(Request $request): array
    {
        $risks = $this->riskAlerts();
        $sessions = $this->activeSessions($request);
        $failedLogins = $this->failedLoginEvents(40);
        $audit = $this->auditTimeline(60);

        return [
            'metrics' => [
                'critical' => $risks->where('severity', 'critical')->count(),
                'high_risk' => $risks->where('severity', 'high')->count(),
                'failed_logins_24h' => $this->failedLoginCount(now()->subDay()),
                'blocked' => $this->blockedAccountCount(),
                'suspended' => max(0, $this->restrictedAccountCount() - $this->blockedAccountCount()),
                'active_sessions' => $sessions->count(),
            ],
            'risks' => $risks,
            'sessions' => $sessions,
            'failedLogins' => $failedLogins,
            'audit' => $audit,
        ];
    }

    public function criticalAlertFeed(): array
    {
        $acknowledged = $this->recentAcknowledgements();

        $alerts = $this->riskAlerts()
            ->where('severity', 'critical')
            ->reject(fn (array $alert) => $acknowledged->contains($alert['fingerprint']))
            ->values();

        return [
            'count' => $alerts->count(),
            'alerts' => $alerts->map(fn (array $alert) => [
                'fingerprint' => $alert['fingerprint'],
                'severity' => $alert['severity'],
                'title' => $alert['title'],
                'message' => $alert['message'],
                'url' => $alert['url'] ?? null,
            ])->all(),
            'checked_at' => now()->toIso8601String(),
        ];
    }

    public function acknowledgeAlert(Request $request, int $adminId, string $fingerprint): void
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return;
        }

        AdminUserActivity::query()->create([
            'admin_account_id' => $adminId,
            'user_role' => 'admin',
            'user_id' => $adminId,
            'action' => 'security_alert_acknowledged',
            'description' => 'Administrator acknowledged a critical security or operations alert.',
            'metadata' => [
                'fingerprint' => Str::limit($fingerprint, 180, ''),
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) ($request->userAgent() ?? ''), 500, ''),
                'source' => 'security_center',
            ],
        ]);
    }

    public function forceLogout(Request $request, int $adminId, string $sessionHandle): array
    {
        $table = (string) config('session.table', 'sessions');
        abort_unless(
            config('session.driver') === 'database'
                && !config('session.encrypt')
                && Schema::hasTable($table),
            422,
            'Unencrypted database sessions are required for remote logout.'
        );

        $cutoff = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;
        $row = DB::table($table)
            ->where('last_activity', '>=', $cutoff)
            ->orderByDesc('last_activity')
            ->limit(500)
            ->get(['id', 'payload'])
            ->first(fn ($candidate) => hash_equals(
                $this->sessionHandle((string) $candidate->id),
                strtolower($sessionHandle)
            ));

        abort_unless($row, 404, 'Active session no longer exists.');

        $sessionId = (string) $row->id;
        if (hash_equals((string) $request->session()->getId(), $sessionId)) {
            abort(422, 'You cannot force-log out the administrator session currently performing this action.');
        }

        $identity = $this->identityFromPayload((string) $row->payload);
        abort_unless($identity !== null, 422, 'This session is not linked to a SARI account.');

        DB::table($table)->where('id', $sessionId)->delete();

        if (Schema::hasTable('admin_user_activities')) {
            AdminUserActivity::query()->create([
                'admin_account_id' => $adminId,
                'user_role' => $identity['role'],
                'user_id' => $identity['id'],
                'action' => 'force_logout',
                'description' => 'Administrator remotely terminated an active account session.',
                'metadata' => [
                    'session_id_suffix' => substr($sessionId, -8),
                    'target_role' => $identity['role'],
                    'target_id' => $identity['id'],
                    'target_name' => $this->subjectName($identity['role'], $identity['id']),
                    'before' => ['session' => 'active'],
                    'after' => ['session' => 'terminated'],
                    'ip' => $request->ip(),
                    'source' => 'security_center',
                ],
            ]);
        }

        return $identity;
    }

    public function recordFailedLogin(Request $request, string $email): void
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return;
        }

        $email = strtolower(trim($email));
        if ($email === '') {
            return;
        }

        $identity = $this->identityForEmail($email);
        $role = $identity['role'] ?? 'security';
        $id = (int) ($identity['id'] ?? 0);

        AdminUserActivity::query()->create([
            'admin_account_id' => null,
            'user_role' => $role,
            'user_id' => $id,
            'action' => 'failed_login',
            'description' => 'Unsuccessful sign-in attempt recorded.',
            'metadata' => [
                'event' => 'failed_login',
                'email_hash' => hash('sha256', $email),
                'email_masked' => $this->maskEmail($email),
                'resolved_role' => $identity['role'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) ($request->userAgent() ?? ''), 500, ''),
                'path' => '/' . ltrim($request->path(), '/'),
                'source' => 'security_monitor',
            ],
        ]);
    }

    public function activeSessions(Request $request): Collection
    {
        $table = (string) config('session.table', 'sessions');

        if (config('session.driver') !== 'database' || config('session.encrypt') || !Schema::hasTable($table)) {
            return collect();
        }

        $cutoff = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;
        $currentId = (string) $request->session()->getId();

        return DB::table($table)
            ->where('last_activity', '>=', $cutoff)
            ->orderByDesc('last_activity')
            ->limit(250)
            ->get()
            ->map(function ($row) use ($currentId): ?array {
                $identity = $this->identityFromPayload((string) $row->payload);
                if (!$identity) {
                    return null;
                }

                return [
                    'session_handle' => $this->sessionHandle((string) $row->id),
                    'session_suffix' => substr((string) $row->id, -8),
                    'role' => $identity['role'],
                    'role_label' => $this->roleLabel($identity['role']),
                    'user_id' => $identity['id'],
                    'name' => $this->subjectName($identity['role'], $identity['id']),
                    'ip' => (string) ($row->ip_address ?? '—'),
                    'browser' => $this->browserLabel((string) ($row->user_agent ?? '')),
                    'last_seen_at' => Carbon::createFromTimestamp((int) $row->last_activity),
                    'current' => hash_equals($currentId, (string) $row->id),
                ];
            })
            ->filter()
            ->values();
    }

    public function riskAlerts(): Collection
    {
        $alerts = collect();
        $alerts = $alerts->concat($this->failedLoginRisks());
        $alerts = $alerts->concat($this->buyerCancellationRisks());
        $alerts = $alerts->concat($this->buyerRefundRisks());
        $alerts = $alerts->concat($this->sellerCancellationRisks());
        $alerts = $alerts->concat($this->operationalRisks());

        $rank = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];

        return $alerts
            ->sortByDesc(fn (array $row) => ($rank[$row['severity']] ?? 0) * 10000000000 + (int) ($row['score'] ?? 0))
            ->values();
    }

    private function failedLoginRisks(): Collection
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return collect();
        }

        $events = AdminUserActivity::query()
            ->where('action', 'failed_login')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->latest('id')
            ->limit(300)
            ->get();

        return $events
            ->groupBy(function (AdminUserActivity $event): string {
                $metadata = is_array($event->metadata) ? $event->metadata : [];
                return (string) ($metadata['email_hash'] ?? ('ip:' . ($metadata['ip'] ?? 'unknown')));
            })
            ->filter(fn (Collection $group) => $group->count() >= 5)
            ->map(function (Collection $group, string $key): array {
                /** @var AdminUserActivity $latest */
                $latest = $group->first();
                $metadata = is_array($latest->metadata) ? $latest->metadata : [];
                $count = $group->count();
                $severity = $count >= 8 ? 'critical' : 'high';
                $role = $latest->user_role !== 'security' ? $latest->user_role : null;
                $userId = $latest->user_id > 0 ? (int) $latest->user_id : null;

                return [
                    'fingerprint' => 'failed-login:' . substr(hash('sha256', $key), 0, 24),
                    'severity' => $severity,
                    'score' => min(100, 55 + ($count * 5)),
                    'category' => 'Authentication',
                    'title' => 'Repeated failed sign-in attempts',
                    'message' => $count . ' unsuccessful sign-ins were recorded in the last 15 minutes for ' . ($metadata['email_masked'] ?? 'one identity') . '.',
                    'role' => $role,
                    'user_id' => $userId,
                    'url' => Route::has('admin.security') ? route('admin.security', ['section' => 'failed-logins']) : null,
                    'metadata' => ['attempts' => $count, 'ip' => $metadata['ip'] ?? null],
                ];
            })
            ->values();
    }

    private function buyerCancellationRisks(): Collection
    {
        $since = now()->subDays(30);
        $alerts = collect();

        foreach ([['buyer_account_id', 'buyer'], ['buyer_social_account_id', 'social_buyer']] as [$column, $role]) {
            $rows = MarketplaceOrder::query()
                ->whereNotNull($column)
                ->where('created_at', '>=', $since)
                ->selectRaw($column . ' AS subject_id')
                ->selectRaw('COUNT(*) AS orders_count')
                ->selectRaw("SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) AS cancelled_count")
                ->groupBy($column)
                ->havingRaw('COUNT(*) >= 5')
                ->get();

            foreach ($rows as $row) {
                $orders = (int) $row->orders_count;
                $cancelled = (int) $row->cancelled_count;
                $rate = $orders > 0 ? ($cancelled / $orders) * 100 : 0;
                if ($cancelled < 3 || $rate < 60) {
                    continue;
                }

                $id = (int) $row->subject_id;
                $alerts->push([
                    'fingerprint' => 'buyer-cancel:' . $role . ':' . $id,
                    'severity' => $cancelled >= 6 && $rate >= 75 ? 'high' : 'medium',
                    'score' => min(100, (int) round(35 + $rate * .6)),
                    'category' => 'Buyer behavior',
                    'title' => 'Unusually high cancellation rate',
                    'message' => $this->subjectName($role, $id) . ' cancelled ' . $cancelled . ' of ' . $orders . ' orders in the last 30 days (' . number_format($rate, 0) . '%).',
                    'role' => $role,
                    'user_id' => $id,
                    'url' => Route::has('admin.monitoring.buyers.show') ? route('admin.monitoring.buyers.show', ['role' => $role, 'id' => $id]) : null,
                    'metadata' => ['orders' => $orders, 'cancelled' => $cancelled, 'rate' => round($rate, 1)],
                ]);
            }
        }

        return $alerts;
    }

    private function buyerRefundRisks(): Collection
    {
        if (!Schema::hasTable('seller_return_requests')) {
            return collect();
        }

        $alerts = collect();
        $since = now()->subDays(30);

        foreach ([['buyer_account_id', 'buyer'], ['buyer_social_account_id', 'social_buyer']] as [$column, $role]) {
            $rows = SellerReturnRequest::query()
                ->whereNotNull($column)
                ->where('created_at', '>=', $since)
                ->selectRaw($column . ' AS subject_id')
                ->selectRaw('COUNT(*) AS request_count')
                ->selectRaw('COALESCE(SUM(requested_amount),0) AS requested_total')
                ->groupBy($column)
                ->havingRaw('COUNT(*) >= 3')
                ->get();

            foreach ($rows as $row) {
                $id = (int) $row->subject_id;
                $count = (int) $row->request_count;
                $alerts->push([
                    'fingerprint' => 'buyer-refund:' . $role . ':' . $id,
                    'severity' => $count >= 5 ? 'high' : 'medium',
                    'score' => min(100, 45 + ($count * 8)),
                    'category' => 'Refund behavior',
                    'title' => 'Repeated return/refund requests',
                    'message' => $this->subjectName($role, $id) . ' opened ' . $count . ' return/refund requests in the last 30 days.',
                    'role' => $role,
                    'user_id' => $id,
                    'url' => Route::has('admin.monitoring.buyers.show') ? route('admin.monitoring.buyers.show', ['role' => $role, 'id' => $id]) : null,
                    'metadata' => ['requests' => $count, 'requested_total' => round((float) $row->requested_total, 2)],
                ]);
            }
        }

        return $alerts;
    }

    private function sellerCancellationRisks(): Collection
    {
        $rows = MarketplaceOrder::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->whereIn('status', ['delivered', 'cancelled'])
            ->selectRaw('seller_account_id AS subject_id')
            ->selectRaw('COUNT(*) AS terminal_count')
            ->selectRaw("SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) AS cancelled_count")
            ->groupBy('seller_account_id')
            ->havingRaw('COUNT(*) >= 5')
            ->get();

        return $rows->map(function ($row): ?array {
            $terminal = (int) $row->terminal_count;
            $cancelled = (int) $row->cancelled_count;
            $rate = $terminal > 0 ? ($cancelled / $terminal) * 100 : 0;
            if ($cancelled < 3 || $rate < 40) {
                return null;
            }

            $id = (int) $row->subject_id;
            return [
                'fingerprint' => 'seller-cancel:' . $id,
                'severity' => $rate >= 65 ? 'high' : 'medium',
                'score' => min(100, (int) round(40 + $rate * .7)),
                'category' => 'Seller operations',
                'title' => 'Seller cancellation rate needs review',
                'message' => $this->subjectName('seller', $id) . ' has ' . $cancelled . ' cancelled terminal orders out of ' . $terminal . ' in the last 30 days.',
                'role' => 'seller',
                'user_id' => $id,
                'url' => Route::has('admin.monitoring.sellers.show') ? route('admin.monitoring.sellers.show', $id) : null,
                'metadata' => ['terminal_orders' => $terminal, 'cancelled' => $cancelled, 'rate' => round($rate, 1)],
            ];
        })->filter()->values();
    }

    private function operationalRisks(): Collection
    {
        $alerts = collect();
        $stuck = $this->stuckOrders();

        $veryLate = $stuck->filter(fn (array $row) => ($row['hours_stuck'] ?? 0) >= 48);
        if ($veryLate->isNotEmpty()) {
            $alerts->push([
                'fingerprint' => 'ops-orders-48h:' . now()->toDateString(),
                'severity' => 'critical',
                'score' => 95,
                'category' => 'Order operations',
                'title' => 'Orders stalled for more than 48 hours',
                'message' => $veryLate->count() . ' active order(s) have not progressed for at least 48 hours and need immediate intervention.',
                'role' => null,
                'user_id' => null,
                'url' => Route::has('admin.monitoring') ? route('admin.monitoring') : null,
                'metadata' => ['orders' => $veryLate->pluck('order_number')->take(10)->all()],
            ]);
        } elseif ($stuck->isNotEmpty()) {
            $alerts->push([
                'fingerprint' => 'ops-orders-stuck:' . now()->toDateString(),
                'severity' => 'high',
                'score' => 78,
                'category' => 'Order operations',
                'title' => 'Stuck order queue detected',
                'message' => $stuck->count() . ' active order(s) have exceeded the expected progress window.',
                'role' => null,
                'user_id' => null,
                'url' => Route::has('admin.monitoring') ? route('admin.monitoring') : null,
                'metadata' => ['orders' => $stuck->pluck('order_number')->take(10)->all()],
            ]);
        }

        return $alerts;
    }

    public function stuckOrders(): Collection
    {
        $thresholds = [
            'new' => 24,
            'preparing' => 24,
            'ready_for_pickup' => 12,
            'courier_accepted' => 8,
            'heading_pickup' => 8,
            'arrived_pickup' => 8,
            'in_transit' => 24,
            'arrived_buyer' => 12,
        ];

        return MarketplaceOrder::query()
            ->whereIn('status', array_keys($thresholds))
            ->where('updated_at', '<=', now()->subHours(8))
            ->latest('updated_at')
            ->limit(150)
            ->get()
            ->map(function (MarketplaceOrder $order) use ($thresholds): ?array {
                $hours = max(0, (int) floor($order->updated_at?->diffInHours(now()) ?? 0));
                $limit = $thresholds[$order->status] ?? 24;
                if ($hours < $limit) {
                    return null;
                }
                return [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'status_label' => $order->statusLabel(),
                    'hours_stuck' => $hours,
                    'seller_id' => (int) $order->seller_account_id,
                    'logistics_id' => $order->logistics_account_id ? (int) $order->logistics_account_id : null,
                ];
            })
            ->filter()
            ->sortByDesc('hours_stuck')
            ->values();
    }

    private function failedLoginEvents(int $limit): Collection
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return collect();
        }

        return AdminUserActivity::query()
            ->where('action', 'failed_login')
            ->latest('created_at')
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (AdminUserActivity $event): array {
                $metadata = is_array($event->metadata) ? $event->metadata : [];
                return [
                    'created_at' => $event->created_at,
                    'role' => $event->user_role,
                    'user_id' => (int) $event->user_id,
                    'name' => $event->user_id > 0 ? $this->subjectName($event->user_role, (int) $event->user_id) : ($metadata['email_masked'] ?? 'Unknown identity'),
                    'email_masked' => $metadata['email_masked'] ?? '—',
                    'ip' => $metadata['ip'] ?? '—',
                    'browser' => $this->browserLabel((string) ($metadata['user_agent'] ?? '')),
                ];
            });
    }

    private function auditTimeline(int $limit): Collection
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return collect();
        }

        $adminNames = AdminAccount::query()->pluck('name', 'id');

        return AdminUserActivity::query()
            ->whereNotNull('admin_account_id')
            ->latest('created_at')
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (AdminUserActivity $event) use ($adminNames): array {
                $metadata = is_array($event->metadata) ? $event->metadata : [];
                return [
                    'created_at' => $event->created_at,
                    'admin' => $adminNames[(int) $event->admin_account_id] ?? ('Admin #' . $event->admin_account_id),
                    'action' => $event->action,
                    'action_label' => Str::of((string) ($metadata['route_name'] ?? $event->action))->replace(['.', '-', '_'], ' ')->squish()->title()->toString(),
                    'description' => $event->description ?: 'Administrative action recorded.',
                    'subject' => $event->user_role === 'admin'
                        ? 'Platform / Admin'
                        : $this->subjectName($event->user_role, (int) $event->user_id),
                    'reason' => $metadata['reason'] ?? ($event->description ?: null),
                    'before' => is_array($metadata['before'] ?? null) ? $metadata['before'] : null,
                    'after' => is_array($metadata['after'] ?? null) ? $metadata['after'] : null,
                    'ip' => $metadata['ip'] ?? '—',
                    'route_name' => $metadata['route_name'] ?? null,
                ];
            });
    }

    private function recentAcknowledgements(): Collection
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return collect();
        }

        return AdminUserActivity::query()
            ->where('action', 'security_alert_acknowledged')
            ->where('created_at', '>=', now()->subHours(12))
            ->latest('id')
            ->limit(300)
            ->get()
            ->map(function (AdminUserActivity $event): ?string {
                $metadata = is_array($event->metadata) ? $event->metadata : [];
                return $metadata['fingerprint'] ?? null;
            })
            ->filter()
            ->unique()
            ->values();
    }

    private function failedLoginCount(Carbon $since): int
    {
        if (!Schema::hasTable('admin_user_activities')) {
            return 0;
        }

        return AdminUserActivity::query()->where('action', 'failed_login')->where('created_at', '>=', $since)->count();
    }

    private function blockedAccountCount(): int
    {
        return BuyerAccount::query()->where('account_status', 'blocked')->count()
            + SellerAccount::query()->where('account_status', 'blocked')->count()
            + LogisticsAccount::query()->where('account_status', 'blocked')->count()
            + CourierAccount::query()->where('account_status', 'blocked')->count()
            + SocialAccount::query()->where('account_status', 'blocked')->count();
    }

    private function restrictedAccountCount(): int
    {
        $states = ['blocked', 'deactivated', 'suspended', 'banned'];
        $count = BuyerAccount::query()->whereIn('account_status', $states)->count()
            + SellerAccount::query()->whereIn('account_status', $states)->count()
            + LogisticsAccount::query()->whereIn('account_status', $states)->count()
            + CourierAccount::query()->whereIn('account_status', $states)->count()
            + SocialAccount::query()->whereIn('account_status', $states)->count();

        $count += SellerAccount::query()
            ->where('account_status', 'active')
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '>', now())
            ->count();

        return $count;
    }


    private function sessionHandle(string $sessionId): string
    {
        return hash_hmac('sha256', $sessionId, (string) config('app.key'));
    }

    private function identityForEmail(string $email): ?array
    {
        $lookups = [
            ['admin', AdminAccount::class],
            ['buyer', BuyerAccount::class],
            ['seller', SellerAccount::class],
            ['rider', CourierAccount::class],
            ['logistics', LogisticsAccount::class],
            ['social_buyer', SocialAccount::class],
        ];

        foreach ($lookups as [$role, $class]) {
            $id = $class::query()->whereRaw('LOWER(email) = ?', [$email])->value('id');
            if ($id) {
                return ['role' => $role, 'id' => (int) $id];
            }
        }

        return null;
    }

    private function identityFromPayload(string $payload): ?array
    {
        $decoded = base64_decode($payload, true);
        if ($decoded === false) {
            return null;
        }

        $data = @unserialize($decoded, ['allowed_classes' => false]);
        if (!is_array($data)) {
            return null;
        }

        $map = [
            ['flag' => 'is_admin', 'id' => 'admin_account_id', 'role' => 'admin'],
            ['flag' => 'is_buyer', 'id' => 'buyer_account_id', 'role' => 'buyer'],
            ['flag' => 'is_buyer', 'id' => 'buyer_social_account_id', 'role' => 'social_buyer'],
            ['flag' => 'is_seller', 'id' => 'seller_account_id', 'role' => 'seller'],
            ['flag' => 'is_logistics', 'id' => 'logistics_account_id', 'role' => 'logistics'],
            ['flag' => 'is_courier', 'id' => 'courier_account_id', 'role' => 'rider'],
        ];

        foreach ($map as $entry) {
            if (!empty($data[$entry['flag']]) && (int) ($data[$entry['id']] ?? 0) > 0) {
                return ['role' => $entry['role'], 'id' => (int) $data[$entry['id']]];
            }
        }

        return null;
    }

    private function subjectName(string $role, int $id): string
    {
        if ($id <= 0) {
            return 'Unknown account';
        }

        return match ($role) {
            'admin' => (string) (AdminAccount::query()->whereKey($id)->value('name') ?: 'Administrator #' . $id),
            'buyer' => (string) (($account = BuyerAccount::withTrashed()->find($id)) ? (trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? '')) ?: 'Buyer #' . $id) : 'Buyer #' . $id),
            'social_buyer' => (string) (SocialAccount::query()->whereKey($id)->value('name') ?: 'Social Buyer #' . $id),
            'seller' => (string) (($seller = SellerAccount::query()->find($id)) ? ($seller->store_name ?: trim(($seller->first_name ?? '') . ' ' . ($seller->last_name ?? ''))) : 'Seller #' . $id),
            'logistics' => (string) (($logistics = LogisticsAccount::withTrashed()->find($id)) ? $logistics->displayName() : 'Logistics #' . $id),
            'rider', 'courier' => (string) (($rider = CourierAccount::withTrashed()->find($id)) ? (trim(($rider->first_name ?? '') . ' ' . ($rider->last_name ?? '')) ?: 'Rider #' . $id) : 'Rider #' . $id),
            default => Str::of($role)->replace('_', ' ')->title()->toString() . ' #' . $id,
        };
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'social_buyer', 'buyer' => 'Buyer',
            'rider', 'courier' => 'Rider',
            'logistics' => 'Logistics',
            'seller' => 'Seller',
            'admin' => 'Administrator',
            default => Str::of($role)->replace('_', ' ')->title()->toString(),
        };
    }

    private function browserLabel(string $userAgent): string
    {
        if ($userAgent === '') {
            return 'Unknown browser';
        }

        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Browser',
        };

        $platform = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Macintosh') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Device',
        };

        return $browser . ' · ' . $platform;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($domain === '') {
            return Str::mask($email, '*', 2);
        }

        $maskedLocal = strlen($local) <= 2
            ? substr($local, 0, 1) . '*'
            : substr($local, 0, 2) . str_repeat('*', max(2, strlen($local) - 2));

        return $maskedLocal . '@' . $domain;
    }
}
