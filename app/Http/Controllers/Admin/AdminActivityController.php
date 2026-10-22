<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminActivityController extends Controller
{
    public function users(Request $request): View
    {
        $this->currentAdmin($request);

        $filters = [
            'role' => strtolower(trim((string) $request->query('role', 'all'))),
            'type' => strtolower(trim((string) $request->query('type', 'all'))),
            'per_page' => (int) $request->query('per_page', 10),
        ];

        if (!in_array($filters['role'], ['all', 'buyer', 'seller', 'logistics'], true)) {
            $filters['role'] = 'all';
        }

        if (!in_array($filters['type'], ['all', 'login', 'logout', 'user_action', 'admin_action'], true)) {
            $filters['type'] = 'all';
        }

        if (!in_array($filters['per_page'], [10, 20, 30, 50], true)) {
            $filters['per_page'] = 10;
        }

        $stats = [
            'today' => 0,
            'logins' => 0,
            'logouts' => 0,
            'actions' => 0,
        ];

        $logs = null;

        if (Schema::hasTable('admin_user_activities')) {
            $roles = ['buyer', 'social_buyer', 'seller', 'logistics'];
            $base = AdminUserActivity::query()->whereIn('user_role', $roles);
            $today = now()->startOfDay();

            $stats = [
                'today' => (clone $base)->where('created_at', '>=', $today)->count(),
                'logins' => (clone $base)->where('created_at', '>=', $today)->where('action', 'login')->count(),
                'logouts' => (clone $base)->where('created_at', '>=', $today)->where('action', 'logout')->count(),
                'actions' => (clone $base)
                    ->where('created_at', '>=', $today)
                    ->whereNotIn('action', ['login', 'logout'])
                    ->count(),
            ];

            $query = clone $base;

            if ($filters['role'] === 'buyer') {
                $query->whereIn('user_role', ['buyer', 'social_buyer']);
            } elseif ($filters['role'] !== 'all') {
                $query->where('user_role', $filters['role']);
            }

            if ($filters['type'] === 'login') {
                $query->where('action', 'login');
            } elseif ($filters['type'] === 'logout') {
                $query->where('action', 'logout');
            } elseif ($filters['type'] === 'user_action') {
                $query->whereNull('admin_account_id')->whereNotIn('action', ['login', 'logout']);
            } elseif ($filters['type'] === 'admin_action') {
                $query->whereNotNull('admin_account_id');
            }

            $logs = $query
                ->latest('created_at')
                ->latest('id')
                ->paginate($filters['per_page'])
                ->withQueryString();

            $subjects = $this->activitySubjects($logs->getCollection());
            $adminIds = $logs->getCollection()
                ->pluck('admin_account_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $adminNames = $adminIds->isEmpty()
                ? collect()
                : AdminAccount::query()->whereIn('id', $adminIds)->pluck('name', 'id');

            $logs->setCollection(
                $logs->getCollection()->map(function (AdminUserActivity $event) use ($subjects, $adminNames): array {
                    $key = strtolower($event->user_role) . ':' . (int) $event->user_id;
                    $subject = $subjects[$key] ?? [
                        'name' => $this->fallbackActivityName($event->user_role, (int) $event->user_id),
                        'email' => 'Account unavailable',
                        'role' => $this->activityRoleLabel($event->user_role),
                    ];

                    $metadata = is_array($event->metadata) ? $event->metadata : [];

                    return [
                        'created_at' => $event->created_at?->copy()->timezone('Asia/Manila'),
                        'user_name' => $subject['name'],
                        'user_email' => $subject['email'],
                        'role' => $subject['role'],
                        'action' => $event->action,
                        'action_label' => $this->activityActionLabel($event->action, $metadata),
                        'description' => $event->description ?: 'Activity recorded.',
                        'performed_by' => $event->admin_account_id
                            ? ($adminNames[(int) $event->admin_account_id] ?? 'Administrator')
                            : 'Account user',
                        'source' => $event->admin_account_id ? 'admin' : 'user',
                        'ip' => $metadata['ip'] ?? $metadata['admin_ip'] ?? '—',
                        'route_name' => $metadata['route_name'] ?? null,
                    ];
                })
            );
        }

        return view('admin.user-activity', compact('logs', 'stats', 'filters'));
    }

    public function admin(Request $request): View
    {
        $admin = $this->currentAdmin($request);
        $filter = strtolower(trim((string) $request->query('type', 'all')));

        if (!in_array($filter, ['all', 'login', 'logout', 'action'], true)) {
            $filter = 'all';
        }

        $stats = [
            'today' => 0,
            'logins' => 0,
            'logouts' => 0,
            'actions' => 0,
        ];

        $logs = null;

        if (Schema::hasTable('admin_user_activities')) {
            $base = AdminUserActivity::query()
                ->where('user_role', 'admin')
                ->where('user_id', $admin->id);

            $today = now()->startOfDay();

            $stats = [
                'today' => (clone $base)->where('created_at', '>=', $today)->count(),
                'logins' => (clone $base)->where('created_at', '>=', $today)->where('action', 'login')->count(),
                'logouts' => (clone $base)->where('created_at', '>=', $today)->where('action', 'logout')->count(),
                'actions' => (clone $base)
                    ->where('created_at', '>=', $today)
                    ->whereNotIn('action', ['login', 'logout'])
                    ->count(),
            ];

            $query = clone $base;

            if ($filter === 'login') {
                $query->where('action', 'login');
            } elseif ($filter === 'logout') {
                $query->where('action', 'logout');
            } elseif ($filter === 'action') {
                $query->whereNotIn('action', ['login', 'logout']);
            }

            $logs = $query
                ->latest('created_at')
                ->latest('id')
                ->paginate(10)
                ->withQueryString();

            $logs->setCollection(
                $logs->getCollection()->map(function (AdminUserActivity $event): array {
                    $metadata = is_array($event->metadata) ? $event->metadata : [];

                    return [
                        'created_at' => $event->created_at?->copy()->timezone('Asia/Manila'),
                        'action' => $event->action,
                        'action_label' => $this->adminActionLabel($event->action, $metadata),
                        'description' => $event->description ?: 'Administrator activity recorded.',
                        'route_name' => $metadata['route_name'] ?? null,
                        'method' => $metadata['method'] ?? '—',
                        'path' => $metadata['path'] ?? null,
                        'ip' => $metadata['ip'] ?? '—',
                        'response_status' => $metadata['response_status'] ?? null,
                    ];
                })
            );
        }

        return view('admin.admin-activity', [
            'adminAccount' => $admin,
            'logs' => $logs,
            'stats' => $stats,
            'filter' => $filter,
        ]);
    }

    private function currentAdmin(Request $request): AdminAccount
    {
        if (!$request->session()->get('is_admin')) {
            throw new HttpResponseException(
                redirect()->route('login')
            );
        }

        $adminId = (int) $request->session()->get('admin_account_id', 0);
        $admin = $adminId > 0
            ? AdminAccount::query()->find($adminId)
            : null;

        if (!$admin) {
            $request->session()->forget(['is_admin', 'admin_account_id']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw new HttpResponseException(
                redirect()
                    ->route('login')
                    ->with('error', 'Your administrator session expired. Please sign in again.')
            );
        }

        return $admin;
    }

    private function activitySubjects($events): array
    {
        $grouped = collect($events)
            ->groupBy(fn (AdminUserActivity $event) => strtolower($event->user_role))
            ->map(fn ($rows) => $rows->pluck('user_id')->map(fn ($id) => (int) $id)->unique()->values());

        $subjects = [];

        if (($grouped['buyer'] ?? collect())->isNotEmpty()) {
            BuyerAccount::withTrashed()
                ->whereIn('id', $grouped['buyer'])
                ->get()
                ->each(function (BuyerAccount $account) use (&$subjects): void {
                    $name = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
                    $subjects['buyer:' . $account->id] = [
                        'name' => $name !== '' ? $name : 'Buyer #' . $account->id,
                        'email' => (string) ($account->email ?? '—'),
                        'role' => 'Buyer',
                    ];
                });
        }

        if (($grouped['social_buyer'] ?? collect())->isNotEmpty()) {
            SocialAccount::query()
                ->whereIn('id', $grouped['social_buyer'])
                ->get()
                ->each(function (SocialAccount $account) use (&$subjects): void {
                    $subjects['social_buyer:' . $account->id] = [
                        'name' => trim((string) ($account->name ?: 'Social Buyer #' . $account->id)),
                        'email' => (string) ($account->email ?? '—'),
                        'role' => 'Buyer',
                    ];
                });
        }

        if (($grouped['seller'] ?? collect())->isNotEmpty()) {
            SellerAccount::query()
                ->whereIn('id', $grouped['seller'])
                ->get()
                ->each(function (SellerAccount $account) use (&$subjects): void {
                    $personName = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
                    $name = trim((string) ($account->store_name ?? ''));
                    $subjects['seller:' . $account->id] = [
                        'name' => $name !== '' ? $name : ($personName !== '' ? $personName : 'Seller #' . $account->id),
                        'email' => (string) ($account->email ?? '—'),
                        'role' => 'Seller',
                    ];
                });
        }

        if (($grouped['logistics'] ?? collect())->isNotEmpty()) {
            LogisticsAccount::withTrashed()
                ->whereIn('id', $grouped['logistics'])
                ->get()
                ->each(function (LogisticsAccount $account) use (&$subjects): void {
                    $subjects['logistics:' . $account->id] = [
                        'name' => $account->displayName(),
                        'email' => (string) ($account->email ?? '—'),
                        'role' => 'Logistics',
                    ];
                });
        }

        return $subjects;
    }

    private function activityActionLabel(string $action, array $metadata): string
    {
        return match ($action) {
            'login' => 'Signed in',
            'logout' => 'Signed out',
            'profile_updated' => 'Profile updated',
            'suspended' => 'Account suspended',
            'restored' => 'Account restored',
            'banned' => 'Account banned',
            'unbanned', 'ban_cancelled' => 'Ban removed',
            'deactivated' => 'Account deactivated',
            'deleted' => 'Account deleted',
            'admin_note' => 'Admin note added',
            default => Str::of((string) ($metadata['route_name'] ?? $action))
                ->replace(['.', '-', '_'], ' ')
                ->squish()
                ->title()
                ->toString(),
        };
    }

    private function adminActionLabel(string $action, array $metadata): string
    {
        return match ($action) {
            'login' => 'Admin signed in',
            'logout' => 'Admin signed out',
            default => Str::of((string) ($metadata['route_name'] ?? $action))
                ->replace(['.', '-', '_'], ' ')
                ->squish()
                ->title()
                ->toString(),
        };
    }

    private function activityRoleLabel(string $role): string
    {
        return match (strtolower($role)) {
            'buyer', 'social_buyer' => 'Buyer',
            'seller' => 'Seller',
            'logistics' => 'Logistics',
            default => Str::of($role)->replace('_', ' ')->title()->toString(),
        };
    }

    private function fallbackActivityName(string $role, int $id): string
    {
        return $this->activityRoleLabel($role) . ' #' . $id;
    }
}
