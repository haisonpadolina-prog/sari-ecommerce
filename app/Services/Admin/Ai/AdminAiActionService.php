<?php

namespace App\Services\Admin\Ai;

use App\Http\Controllers\Admin\AdminSellerAccountStatusController;
use App\Http\Controllers\Admin\AdminSellerComplianceController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Platform\AdminAiAction;
use App\Models\Platform\AdminAiThread;
use App\Models\Platform\AdminUserActivity;
use App\Models\Platform\PlatformAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class AdminAiActionService
{
    public const TYPES = ['approve_product', 'reject_product', 'suspend_seller_30', 'lift_seller_suspension',
        'ban_seller', 'unban_seller', 'block_account', 'unblock_account', 'message_seller', 'announcement', 'assign_ai_role'];

    public function authorize(AdminAccount $admin, ?string $type = null): void
    {
        $allowed = app(AdminAiPermissionService::class)->actions($admin);
        abort_unless($type ? in_array($type, $allowed, true) : count($allowed) > 0, 403, 'Your AI role cannot perform this action, or AI actions are disabled.');
    }

    public function prepare(AdminAccount $admin, array $input): array
    {
        $this->authorize($admin);
        foreach (['reason', 'title', 'message'] as $field) {
            if (isset($input[$field]) && is_string($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }
        $v = Validator::make($input, [
            'type' => ['required', Rule::in(self::TYPES)],
            'ai_role' => ['required_if:type,assign_ai_role', Rule::in(AdminAiPermissionService::ROLES)],
            'ids' => ['required_unless:type,announcement', 'array', 'min:1', 'max:10'],
            'ids.*' => ['integer', 'min:1', 'distinct'],
            'role' => ['required_if:type,block_account,unblock_account', Rule::in(['buyer', 'seller', 'logistics', 'social_buyer'])],
            'reason' => ['required_if:type,reject_product,suspend_seller_30,ban_seller,block_account', 'nullable', 'string', 'min:5', 'max:500'],
            'message' => ['required_if:type,message_seller,announcement', 'nullable', 'string', 'min:1', 'max:3000'],
            'title' => ['required_if:type,announcement', 'nullable', 'string', 'min:1', 'max:160'],
            'audiences' => ['required_if:type,announcement', 'array', 'min:1', 'max:4'],
            'audiences.*' => [Rule::in(['buyer', 'seller', 'logistics', 'rider']), 'distinct'],
            'publish' => ['sometimes', 'boolean'],
        ])->validate();
        $this->authorize($admin, $v['type']);
        foreach (['reason', 'message', 'title'] as $field) {
            if (isset($v[$field]) && trim($v[$field]) === '') {
                throw ValidationException::withMessages([$field => 'This field cannot be blank.']);
            }
        }
        $targets = [];
        foreach ($v['ids'] ?? [] as $id) {
            $model = $this->target($v, (int) $id);
            $targets[] = ['id' => (int) $id, 'name' => $model->name ?? $model->store_name ?? trim($model->first_name.' '.$model->last_name),
                'fingerprint' => $this->fingerprint($model)];
        }
        $action = AdminAiAction::query()->create([
            'id' => (string) Str::uuid(), 'admin_account_id' => $admin->id, 'type' => $v['type'],
            'payload' => ['input' => $v, 'targets' => $targets], 'expires_at' => now()->addMinutes(10),
        ]);

        return $this->preview($action);
    }

    private function target(array $v, int $id, bool $lock = false)
    {
        $class = match (true) {
            in_array($v['type'], ['approve_product', 'reject_product']) => SellerProduct::class,
            in_array($v['type'], ['block_account', 'unblock_account']) => match ($v['role']) {
                'seller' => SellerAccount::class, 'buyer' => BuyerAccount::class,
                'logistics' => LogisticsAccount::class, 'social_buyer' => SocialAccount::class,
            },
            default => SellerAccount::class,
        };
        if ($v['type'] === 'assign_ai_role') {
            $class = AdminAccount::class;
        }
        $q = $class::query()->whereKey($id);
        if ($lock) {
            $q->lockForUpdate();
        }

        $model = $q->firstOrFail();
        if ($v['type'] === 'assign_ai_role') {
            abort_if($model->role === 'super_admin', 422, 'Super admin AI privileges cannot be changed here.');
        }
        if (in_array($v['type'], ['approve_product', 'reject_product'], true)) {
            abort_unless($model->archived_at === null && in_array($model->moderation_status, ['pending', 'flagged'], true), 409, 'Only active pending or flagged products can be reviewed.');
        }

        return $model;
    }

    private function fingerprint($model): string
    {
        // Only mutation-relevant state; no credential or document attributes.
        return hash('sha256', json_encode($model->only([
            'id', 'role', 'ai_role', 'updated_at', 'account_status', 'moderation_status', 'archived_at',
            'suspended_until', 'warning_count', 'name', 'store_name', 'first_name', 'last_name',
        ])));
    }

    public function preview(AdminAiAction $action): array
    {
        $payload = $action->payload;

        return ['id' => $action->id, 'type' => $action->type, 'status' => $action->status,
            'expires_at' => $action->expires_at->toIso8601String(),
            'details' => $payload['input'],
            'targets' => array_map(fn ($t) => ['id' => $t['id'], 'name' => $t['name']], $payload['targets']),
            'effect' => match ($action->type) {
                'assign_ai_role' => 'Changes AI-only access for the selected administrators. Existing admin page permissions stay unchanged.',
                'ban_seller' => 'Bans seller access and removes ALL non-archived seller listings. Unbanning does not republish listings.',
                'block_account' => 'Blocks sign-in and active sessions. For sellers, ALL non-archived listings are also removed.',
                'unban_seller', 'unblock_account' => 'Restores account access; removed listings still require separate review.',
                'suspend_seller_30' => 'Restricts seller selling privileges for 30 days and notifies the seller.',
                'lift_seller_suspension' => 'Clears the suspension and resets active warnings to zero.',
                'reject_product' => 'Sets the product to rejected and records a seller compliance message.',
                'approve_product' => 'Approves moderation; buyer publication still depends on seller eligibility.',
                'message_seller' => 'Sends the exact text as a seller compliance message and a realtime alert.',
                default => 'Creates the shown announcement in the selected draft/published mode.',
            },
            'warning' => $action->type === 'announcement'
                ? 'Published announcements are visible to ALL accounts in the selected audience roles.'
                : 'This executes the existing admin workflow, including its messages and notifications.',
        ];
    }

    public function decide(Request $request, AdminAccount $admin, string $id, bool $confirm): array
    {
        $this->authorize($admin);

        try {
            return DB::transaction(function () use ($request, $admin, $id, $confirm) {
                $action = AdminAiAction::query()->where('admin_account_id', $admin->id)->lockForUpdate()->findOrFail($id);
                $this->authorize($admin, $action->type);
                if ($action->status === 'executed') {
                    return $action->result;
                }
                abort_unless($action->status === 'pending' && $action->expires_at->isFuture(), 409, 'Proposal expired or cancelled. Ask for a new preview.');
                if (! $confirm) {
                    $action->update(['status' => 'cancelled']);

                    return ['ok' => true, 'message' => 'Proposal cancelled.'];
                }
                $v = $action->payload['input'];
                // Lock and revalidate every target before any write. A stale preview never silently changes scope.
                $models = [];
                foreach ($action->payload['targets'] as $target) {
                    $model = $this->target($v, $target['id'], true);
                    abort_unless(hash_equals($target['fingerprint'], $this->fingerprint($model)), 409,
                        'A target changed since preview. Ask for a new proposal.');
                    $models[] = $model;
                }
                $results = [];
                if ($action->type === 'announcement') {
                    $announcement = PlatformAnnouncement::query()->create([
                        'admin_account_id' => $admin->id, 'title' => trim($v['title']), 'message' => trim($v['message']),
                        'audiences' => $v['audiences'], 'severity' => 'info',
                        'status' => ! empty($v['publish']) ? 'published' : 'draft',
                        'published_at' => ! empty($v['publish']) ? now() : null,
                        'starts_at' => ! empty($v['publish']) ? now() : null,
                    ]);
                    $results[] = ['id' => $announcement->id, 'message' => 'Announcement '.$announcement->status.'.'];
                }
                foreach ($models as $model) {
                    if ($action->type === 'assign_ai_role') {
                        $model->forceFill(['ai_role' => $v['ai_role']])->save();
                        AdminAiThread::query()->where('admin_account_id', $model->id)->get()->each(fn ($thread) => $thread->update(['messages' => []]));
                        $results[] = ['id' => $model->id, 'message' => 'AI role updated to '.$v['ai_role'].'.'];

                        continue;
                    }
                    $child = $request->duplicate([], ['reason' => $v['reason'] ?? null, 'message' => $v['message'] ?? null]);
                    $child->setJson(new InputBag(['reason' => $v['reason'] ?? null, 'message' => $v['message'] ?? null]));
                    $child->headers->set('Accept', 'application/json');
                    $child->setLaravelSession($request->session());
                    $response = match ($action->type) {
                        'approve_product' => app(AdminSellerComplianceController::class)->approve($child, $model),
                        'reject_product' => app(AdminSellerComplianceController::class)->reject($child, $model),
                        'message_seller' => app(AdminSellerComplianceController::class)->reply($child, $model),
                        'suspend_seller_30' => app(AdminSellerAccountStatusController::class)->suspend30($child, $model),
                        'lift_seller_suspension' => app(AdminSellerAccountStatusController::class)->liftSuspension($child, $model),
                        'ban_seller' => app(AdminSellerAccountStatusController::class)->ban($child, $model),
                        'unban_seller' => app(AdminSellerAccountStatusController::class)->unban($child, $model),
                        'block_account' => app(AdminUsersController::class)->block($child, $v['role'], $model->id),
                        'unblock_account' => app(AdminUsersController::class)->unblock($child, $v['role'], $model->id),
                    };
                    $body = $response->getData(true);
                    if ($response->getStatusCode() >= 400) {
                        throw ValidationException::withMessages(['action' => $body['message'] ?? 'Action failed.']);
                    }
                    $results[] = ['id' => $model->id, 'message' => $body['message'] ?? 'Completed.'];
                }
                $result = ['ok' => true, 'message' => 'Confirmed action completed.', 'results' => $results];
                $action->update(['status' => 'executed', 'result' => $result]);
                AdminUserActivity::query()->create([
                    'admin_account_id' => $admin->id, 'user_role' => 'admin', 'user_id' => $admin->id,
                    'action' => 'ai_confirmed_action', 'description' => 'Confirmed AI proposal: '.$action->type,
                    'metadata' => ['proposal_id' => $action->id, 'target_ids' => $v['ids'] ?? [], 'results' => $results],
                ]);

                return $result;
            });
        } catch (\Throwable $error) {
            // Persist a safe failure outside the rolled-back mutation transaction.
            // Authorization, missing targets and expired previews are not executed attempts.
            if ($confirm && ! ($error instanceof HttpExceptionInterface && in_array($error->getStatusCode(), [403, 404], true))) {
                $failure = new AdminAiAction;
                $failure->result = ['ok' => false, 'message' => 'Execution failed or target state changed. No target changes were committed. Request a fresh preview.'];
                AdminAiAction::query()->whereKey($id)->where('admin_account_id', $admin->id)
                    ->where('status', 'pending')->where('expires_at', '>', now())
                    ->update(['status' => 'failed', 'result' => $failure->getAttributes()['result'], 'updated_at' => now()]);
            }
            throw $error;
        }
    }
}
