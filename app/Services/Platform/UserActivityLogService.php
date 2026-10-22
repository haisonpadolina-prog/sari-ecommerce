<?php

namespace App\Services\Platform;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserActivityLogService
{
    private ?bool $activityTableAvailable = null;

    public function actorFromSession(Request $request): ?array
    {
        if ($request->session()->get('is_buyer')) {
            $buyerId = (int) $request->session()->get('buyer_account_id', 0);

            if ($buyerId > 0) {
                return ['role' => 'buyer', 'id' => $buyerId];
            }

            $socialBuyerId = (int) $request->session()->get('buyer_social_account_id', 0);

            if ($socialBuyerId > 0) {
                return ['role' => 'social_buyer', 'id' => $socialBuyerId];
            }
        }

        if ($request->session()->get('is_seller')) {
            $sellerId = (int) $request->session()->get('seller_account_id', 0);

            if ($sellerId > 0) {
                return ['role' => 'seller', 'id' => $sellerId];
            }
        }

        if ($request->session()->get('is_logistics')) {
            $logisticsId = (int) $request->session()->get('logistics_account_id', 0);

            if ($logisticsId <= 0) {
                $email = strtolower(trim((string) $request->session()->get('logistics_email', '')));

                if ($email !== '') {
                    $logisticsId = (int) LogisticsAccount::query()
                        ->where('email', $email)
                        ->value('id');
                }
            }

            if ($logisticsId > 0) {
                return ['role' => 'logistics', 'id' => $logisticsId];
            }
        }

        return null;
    }

    public function recordUserEvent(
        Request $request,
        array $actor,
        string $action,
        ?string $description = null,
        array $metadata = []
    ): void {
        $this->record(
            request: $request,
            role: (string) ($actor['role'] ?? ''),
            userId: (int) ($actor['id'] ?? 0),
            action: $action,
            description: $description,
            metadata: array_merge(['source' => 'user'], $metadata),
            adminAccountId: null
        );
    }

    public function recordAdminEvent(
        Request $request,
        string $role,
        int $userId,
        string $action,
        ?string $description = null,
        array $metadata = []
    ): void {
        $this->record(
            request: $request,
            role: $role,
            userId: $userId,
            action: $action,
            description: $description,
            metadata: array_merge(['source' => 'admin'], $metadata),
            adminAccountId: (int) $request->session()->get('admin_account_id', 0) ?: null
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
            return 'Submitted a ' . strtoupper($request->method()) . ' account action.';
        }

        $readable = Str::of($routeName)
            ->replace(['.', '-', '_'], ' ')
            ->squish()
            ->title()
            ->toString();

        return 'Submitted action: ' . $readable . '.';
    }

    public function requestMetadata(Request $request, ?int $responseStatus = null): array
    {
        $routeName = (string) ($request->route()?->getName() ?? '');

        return array_filter([
            'route_name' => $routeName !== '' ? $routeName : null,
            'method' => strtoupper($request->method()),
            'path' => '/' . ltrim($request->path(), '/'),
            'response_status' => $responseStatus,
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) ($request->userAgent() ?? ''), 500, ''),
        ], static fn ($value) => $value !== null && $value !== '');
    }

    private function record(
        Request $request,
        string $role,
        int $userId,
        string $action,
        ?string $description,
        array $metadata,
        ?int $adminAccountId
    ): void {
        if (
            !$this->activityTableExists()
            || $userId <= 0
            || !in_array($role, ['buyer', 'social_buyer', 'seller', 'logistics'], true)
        ) {
            return;
        }

        AdminUserActivity::query()->create([
            'admin_account_id' => $adminAccountId,
            'user_role' => $role,
            'user_id' => $userId,
            'action' => Str::limit(strtolower(trim($action)), 60, ''),
            'description' => $description,
            'metadata' => array_merge(
                $this->requestMetadata($request),
                $metadata
            ),
        ]);
    }

    private function activityTableExists(): bool
    {
        if ($this->activityTableAvailable !== null) {
            return $this->activityTableAvailable;
        }

        return $this->activityTableAvailable = Schema::hasTable('admin_user_activities');
    }
}
