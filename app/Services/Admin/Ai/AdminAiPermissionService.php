<?php

namespace App\Services\Admin\Ai;

use App\Models\Accounts\AdminAccount;

class AdminAiPermissionService
{
    public const ROLES = ['observer', 'product_reviewer', 'support', 'finance'];

    public function role(AdminAccount $admin): string
    {
        return $admin->role === 'super_admin' ? 'super_admin' : ($admin->ai_role ?: 'legacy_reader');
    }

    public function canRead(AdminAccount $admin, string $operation): bool
    {
        return match ($this->role($admin)) {
            'super_admin' => true,
            'legacy_reader' => $operation !== 'admin_permissions',
            'product_reviewer' => in_array($operation, ['products', 'accounts', 'action_center', 'action_history', 'permissions', 'guide']),
            'support' => in_array($operation, ['accounts', 'orders', 'journey', 'locations', 'logistics_exceptions', 'complaints', 'returns', 'action_center', 'action_history', 'permissions', 'guide']),
            'finance' => in_array($operation, ['commissions', 'analytics', 'cod_remittances', 'seller_performance', 'returns', 'action_center', 'action_history', 'permissions', 'guide']),
            default => in_array($operation, ['permissions', 'guide', 'action_history']),
        };
    }

    public function actions(AdminAccount $admin): array
    {
        if (! config('admin_ai.allow_actions')) {
            return [];
        }

        return match ($this->role($admin)) {
            'super_admin' => AdminAiActionService::TYPES,
            'product_reviewer' => ['approve_product', 'reject_product'],
            default => [],
        };
    }

    public function authorizeRead(AdminAccount $admin, string $operation): void
    {
        abort_unless($this->canRead($admin, $operation), 403, 'Your AI role cannot access this operation.');
    }
}
