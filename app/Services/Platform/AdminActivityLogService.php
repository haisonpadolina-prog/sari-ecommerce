<?php

namespace App\Services\Platform;

use App\Models\Accounts\AdminAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminActivityLogService
{
    private ?bool $activityTableAvailable = null;

    public function actorFromSession(Request $request): ?int
    {
        if (!$request->session()->get('is_admin')) {
            return null;
        }

        $adminId = (int) $request->session()->get('admin_account_id', 0);

        return $adminId > 0 ? $adminId : null;
    }

    public function recordLogin(Request $request, int $adminId): void
    {
        $this->touchLoginMetadata($request, $adminId);

        $this->record(
            $request,
            $adminId,
            'login',
            'Administrator signed in to SARI.',
            ['event' => 'login']
        );
    }

    public function recordLogout(Request $request, int $adminId): void
    {
        $this->record(
            $request,
            $adminId,
            'logout',
            'Administrator signed out of SARI.',
            ['event' => 'logout']
        );
    }

    public function recordRequest(Request $request, int $adminId, int $responseStatus): void
    {
        $this->record(
            $request,
            $adminId,
            $this->actionForRequest($request),
            $this->descriptionForRequest($request),
            [
                'event' => 'admin_action',
                'response_status' => $responseStatus,
            ]
        );
    }

    public function actionForRequest(Request $request): string
    {
        $routeName = (string) ($request->route()?->getName() ?? '');

        if ($routeName !== '') {
            return Str::limit(
                str_replace(['.', '-'], '_', strtolower($routeName)),
                60,
                ''
            );
        }

        return Str::limit(
            strtolower($request->method()) . '_request',
            60,
            ''
        );
    }

    public function descriptionForRequest(Request $request): string
    {
        $routeName = (string) ($request->route()?->getName() ?? '');

        if ($routeName === '') {
            return 'Administrator submitted a ' . strtoupper($request->method()) . ' action.';
        }

        $readable = Str::of($routeName)
            ->replace(['.', '-', '_'], ' ')
            ->squish()
            ->title()
            ->toString();

        return 'Performed: ' . $readable . '.';
    }

    public function requestMetadata(Request $request): array
    {
        $routeName = (string) ($request->route()?->getName() ?? '');

        return array_filter([
            'route_name' => $routeName !== '' ? $routeName : null,
            'method' => strtoupper($request->method()),
            'path' => '/' . ltrim($request->path(), '/'),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) ($request->userAgent() ?? ''), 500, ''),
        ], static fn ($value) => $value !== null && $value !== '');
    }

    private function record(
        Request $request,
        int $adminId,
        string $action,
        string $description,
        array $metadata = []
    ): void {
        if (!$this->activityTableExists() || $adminId <= 0) {
            return;
        }

        AdminUserActivity::query()->create([
            'admin_account_id' => $adminId,
            'user_role' => 'admin',
            'user_id' => $adminId,
            'action' => Str::limit(strtolower(trim($action)), 60, ''),
            'description' => $description,
            'metadata' => array_merge(
                $this->requestMetadata($request),
                $metadata
            ),
        ]);
    }

    private function touchLoginMetadata(Request $request, int $adminId): void
    {
        $admin = AdminAccount::query()->find($adminId);

        if (!$admin) {
            return;
        }

        $updates = [];
        $table = $admin->getTable();

        if (Schema::hasColumn($table, 'last_login_at')) {
            $updates['last_login_at'] = now();
        }

        if (Schema::hasColumn($table, 'last_login_ip')) {
            $updates['last_login_ip'] = $request->ip();
        }

        if (Schema::hasColumn($table, 'last_login_user_agent')) {
            $updates['last_login_user_agent'] = Str::limit((string) ($request->userAgent() ?? ''), 1000, '');
        }

        if (Schema::hasColumn($table, 'login_count')) {
            $updates['login_count'] = ((int) ($admin->login_count ?? 0)) + 1;
        }

        if ($updates !== []) {
            $admin->forceFill($updates)->save();
        }
    }

    private function activityTableExists(): bool
    {
        if ($this->activityTableAvailable !== null) {
            return $this->activityTableAvailable;
        }

        return $this->activityTableAvailable = Schema::hasTable('admin_user_activities');
    }
}
