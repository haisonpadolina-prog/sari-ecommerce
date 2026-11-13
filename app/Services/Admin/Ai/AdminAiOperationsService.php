<?php

namespace App\Services\Admin\Ai;

use App\Models\Accounts\AdminAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Finance\AdminCashTurnover;
use App\Models\Finance\BranchRemittanceBatch;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\AdminAiAction;
use App\Models\Platform\PlatformComplaint;
use App\Models\Registration\RegistrationApplication;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminAiOperationsService
{
    public const OPERATIONS = ['action_center', 'logistics_exceptions', 'complaints', 'returns', 'cod_remittances', 'seller_performance', 'action_history', 'permissions', 'admin_permissions'];

    public function run(AdminAccount $admin, string $operation, array $input): array
    {
        app(AdminAiPermissionService::class)->authorizeRead($admin, $operation);
        $allowed = match ($operation) {
            'logistics_exceptions' => ['offset', 'logistics_id', 'kind'],
            'complaints' => ['offset', 'status', 'id'],
            'returns' => ['offset', 'status', 'id', 'seller_id'],
            'cod_remittances' => ['offset', 'status', 'logistics_id', 'courier_id', 'from', 'to', 'stage'],
            'seller_performance' => ['offset', 'seller_id', 'from', 'to'],
            'action_history' => ['offset', 'status'],
            default => [],
        };
        abort_if(array_diff(array_keys($input), $allowed), 422, 'Unsupported operation filters.');
        $p = Validator::make($input, [
            'offset' => ['sometimes', 'integer', 'min:0', 'max:10000'],
            'id' => ['sometimes', 'integer', 'min:1'], 'seller_id' => ['sometimes', 'integer', 'min:1'],
            'logistics_id' => ['sometimes', 'integer', 'min:1'], 'courier_id' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'max:40'],
            'stage' => ['sometimes', Rule::in(['rider', 'branch', 'admin'])],
            'kind' => ['sometimes', Rule::in(['stale', 'failed', 'hub'])],
            'from' => ['required_with:to', 'date_format:Y-m-d'],
            'to' => ['required_with:from', 'date_format:Y-m-d', 'after_or_equal:from'],
        ])->validate();
        if ($operation === 'seller_performance') {
            Validator::make($p, ['from' => ['required'], 'to' => ['required']])->validate();
        }
        if (isset($p['from'])) {
            abort_if(CarbonImmutable::parse($p['from'])->diffInDays(CarbonImmutable::parse($p['to'])) > 366, 422, 'Select at most 366 days.');
        }
        $result = match ($operation) {
            'action_center' => $this->actionCenter($admin),
            'logistics_exceptions' => $this->exceptions($p),
            'complaints' => $this->complaints($p),
            'returns' => $this->returns($p),
            'cod_remittances' => $this->cod($p),
            'seller_performance' => $this->performance($p),
            'action_history' => $this->history($admin, $p),
            'permissions' => ['rows' => [['admin_id' => $admin->id, 'ai_role' => app(AdminAiPermissionService::class)->role($admin), 'allowed_actions' => app(AdminAiPermissionService::class)->actions($admin)]], 'note' => 'AI-specific permissions only. Existing admin page permissions are unchanged.'],
            'admin_permissions' => ['rows' => AdminAccount::query()->orderBy('id')->limit(100)->get(['id', 'name', 'role', 'ai_role'])->toArray(), 'total' => AdminAccount::query()->count(), 'note' => 'First 100 administrators. Only super_admin can propose AI role assignment.'],
        };

        return array_merge($result, ['source' => $operation, 'filters' => $p, 'as_of' => now()->toIso8601String()]);
    }

    private function page($query, array $p, callable $map): array
    {
        $total = (clone $query)->count();
        $offset = $p['offset'] ?? 0;
        $rows = $query->offset($offset)->limit(20)->get()->map($map)->values()->all();

        return ['rows' => $rows, 'total' => $total, 'offset' => $offset, 'limit' => 20, 'has_more' => $offset + count($rows) < $total];
    }

    private function actionCenter(AdminAccount $admin): array
    {
        $permissions = app(AdminAiPermissionService::class);
        $rows = [];
        if ($permissions->canRead($admin, 'products')) {
            $rows[] = ['id' => 'products', 'label' => 'Pending or flagged products', 'value' => SellerProduct::query()->whereNull('archived_at')->whereIn('moderation_status', ['pending', 'flagged'])->count(), 'url' => route('admin.product-reviews', [], false)];
        }
        if ($permissions->canRead($admin, 'registrations')) {
            $rows[] = ['id' => 'applications', 'label' => 'Pending seller applications', 'value' => RegistrationApplication::query()->where('role', 'seller')->where('status', 'pending')->count(), 'url' => route('admin.registrations', [], false)];
        }
        if ($permissions->canRead($admin, 'complaints')) {
            $rows[] = ['id' => 'complaints', 'label' => 'Open complaints', 'value' => PlatformComplaint::query()->where('status', 'open')->count(), 'url' => route('admin.complaints', [], false)];
        }
        if ($permissions->canRead($admin, 'logistics_exceptions')) {
            $rows[] = ['id' => 'deliveries', 'label' => 'Active orders without recent update', 'value' => $this->exceptionQuery('stale')->count(), 'url' => route('admin.monitoring', [], false)];
        }
        if ($permissions->canRead($admin, 'returns')) {
            $rows[] = ['id' => 'returns', 'label' => 'Returns awaiting review or completion', 'value' => SellerReturnRequest::query()->whereIn('status', ['requested', 'approved', 'returned'])->count(), 'url' => route('admin.complaints', [], false)];
        }

        return ['rows' => $rows, 'note' => 'Counts are scoped to your AI permissions. Stale is no recorded update, not a proven missed delivery deadline. Open the named chat tool for individual records.'];
    }

    private function exceptionQuery(string $kind)
    {
        $q = MarketplaceOrder::query()->whereNotIn('status', ['delivered', 'cancelled']);
        if ($kind === 'failed') {
            return $q->whereHas('latestEvent', fn ($event) => $event->whereIn('status', ['failed', 'delivery_failed', 'failed_delivery']));
        }
        $q->where('updated_at', '<', now()->subHours(max(1, (int) config('admin_ai.stale_order_hours', 48))));
        if ($kind === 'hub') {
            $q->whereHas('logisticsParcel', fn ($parcel) => $parcel->whereNotNull('received_at')->whereNull('sorted_at'));
        }

        return $q;
    }

    private function exceptions(array $p): array
    {
        $kind = $p['kind'] ?? 'stale';
        $q = $this->exceptionQuery($kind)->with(['latestEvent' => fn ($event) => $event->select(['marketplace_order_events.id', 'marketplace_order_events.marketplace_order_id', 'marketplace_order_events.status', 'marketplace_order_events.title', 'marketplace_order_events.created_at']), 'logisticsParcel:id,marketplace_order_id,status,received_at,sorted_at']);
        if (isset($p['logistics_id'])) {
            $q->where('logistics_account_id', $p['logistics_id']);
        }

        return array_merge($this->page($q->orderBy('updated_at')->orderBy('id'), $p, fn ($o) => [
            'id' => $o->id, 'order_number' => $o->order_number, 'status' => $o->status, 'logistics_id' => $o->logistics_account_id,
            'updated_at' => $o->updated_at?->toIso8601String(), 'latest_event' => $o->latestEvent?->title,
            'parcel_status' => $o->logisticsParcel?->status, 'received_at' => $o->logisticsParcel?->received_at?->toIso8601String(),
            'url' => route('admin.monitoring', [], false),
        ]), ['note' => 'stale: active order updated_at older than configured threshold; hub: also received but not sorted. failed: latest order event has an explicit failed status. No inferred live location or promised arrival time.']);
    }

    private function complaints(array $p): array
    {
        $q = PlatformComplaint::query();
        foreach (['id', 'status'] as $key) {
            if (isset($p[$key])) {
                $q->where($key, $p[$key]);
            }
        }

        return array_merge($this->page($q->orderByDesc('id'), $p, fn ($r) => [
            'id' => $r->id, 'reporter_role' => $r->reporter_role, 'subject' => $r->subject,
            'description' => $r->description, 'status' => $r->status, 'admin_note' => $r->admin_note,
            'created_at' => $r->created_at?->toIso8601String(), 'url' => route('admin.complaints', [], false),
        ]), ['note' => 'Complaint schema does not link orders, sellers or uploaded evidence. Do not infer those relationships. Reporter identifiers are withheld.']);
    }

    private function returns(array $p): array
    {
        $q = SellerReturnRequest::query()->with('order:id,order_number,status,total,payment_status');
        foreach (['id' => 'id', 'status' => 'status', 'seller_id' => 'seller_account_id'] as $key => $column) {
            if (isset($p[$key])) {
                $q->where($column, $p[$key]);
            }
        }
        $result = $this->page($q->orderByDesc('id'), $p, fn ($r) => [
            'id' => $r->id, 'order_id' => $r->marketplace_order_id, 'order_number' => $r->order?->order_number,
            'seller_id' => $r->seller_account_id, 'buyer_id' => $r->buyer_account_id, 'status' => $r->status,
            'reason' => $r->reason, 'details' => $r->details, 'seller_response' => $r->seller_response,
            'requested_amount' => $r->requested_amount, 'approved_amount' => $r->approved_amount,
            'order_total' => $r->order?->total, 'payment_status' => $r->order?->payment_status,
            'refunded_at' => $r->refunded_at?->toIso8601String(), 'url' => route('admin.monitoring', [], false),
        ]);
        if (isset($p['id'])) {
            $result['events'] = SellerReturnRequest::query()->find($p['id'])?->events()->orderByDesc('id')->limit(50)->get(['id', 'actor_role', 'status', 'title', 'message', 'created_at'])->toArray() ?? [];
        }

        return array_merge($result, ['note' => 'Existing seller return workflow and internal refund ledger only. No payment transfer or uploaded return proof is implied. Complaint records have no automatic link to returns.']);
    }

    private function cod(array $p): array
    {
        $stage = $p['stage'] ?? 'rider';
        abort_if($stage !== 'rider' && isset($p['courier_id']), 422, 'Courier filtering is available only for rider batches.');
        $class = match ($stage) {
            'branch' => BranchRemittanceBatch::class, 'admin' => AdminCashTurnover::class, default => CodRemittanceBatch::class
        };
        $q = $class::query()->where('currency', 'PHP');
        foreach (['status' => 'status', 'logistics_id' => 'logistics_account_id', 'courier_id' => 'courier_account_id'] as $key => $column) {
            if (isset($p[$key])) {
                $q->where($column, $p[$key]);
            }
        }
        $this->range($q, $p, 'submitted_at');
        // Report recorded handover fields separately; submitted cash is not confirmed received cash.
        $summary = (clone $q)->selectRaw('COALESCE(SUM(expected_amount),0) AS expected_amount, COALESCE(SUM(submitted_amount),0) AS submitted_amount, COALESCE(SUM(confirmed_amount),0) AS confirmed_amount')->first();
        $result = $this->page($q->orderByDesc('id'), $p, fn ($r) => [
            'id' => $r->id, 'stage' => $stage, 'batch_code' => $r->batch_code ?? $r->turnover_code, 'courier_id' => $r->courier_account_id,
            'logistics_id' => $r->logistics_account_id, 'hub' => $r->hub_name_snapshot, 'currency' => $r->currency,
            'expected_amount' => $r->expected_amount, 'submitted_amount' => $r->submitted_amount,
            'confirmed_amount' => $r->confirmed_amount, 'difference_amount' => $r->difference_amount,
            'unconfirmed_amount' => round(max(0, (float) $r->expected_amount - (float) $r->confirmed_amount), 2),
            'status' => $r->status, 'central_reconciled_at' => $r->central_reconciled_at?->toIso8601String(),
            'url' => route('admin.monitoring.logistics.show', ['logistics' => $r->logistics_account_id], false),
        ]);
        $result['summary'] = $summary->getAttributes();
        $result['note'] = 'Stage-specific PHP batches, filtered by submitted_at in Manila dates. Rider=Rider to hub, branch=branch to Main, admin=Main to SARI Admin. Stages overlap the same cash; query separately and never add them together. Expected = batch parcel amounts; submitted = recorded declaration; confirmed = receiving stage cash count. Unconfirmed is a review amount, not proven missing cash. Unbatched collections are excluded. No physical cash verification or transfer is performed by this tool.';

        return $result;
    }

    private function range($q, array $p, string $column): void
    {
        if (isset($p['from'])) {
            $q->whereBetween($column, [CarbonImmutable::parse($p['from'], 'Asia/Manila')->startOfDay()->utc(), CarbonImmutable::parse($p['to'], 'Asia/Manila')->endOfDay()->utc()]);
        }
    }

    private function performance(array $p): array
    {
        $q = MarketplaceOrder::query();
        $this->range($q, $p, 'created_at');
        if (isset($p['seller_id'])) {
            $q->where('seller_account_id', $p['seller_id']);
        }
        $summary = $q->selectRaw("seller_account_id AS seller_id, COUNT(*) AS orders, SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS delivered, SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled, SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS delivered_gmv")->groupBy('seller_account_id');
        $total = DB::query()->fromSub(clone $summary, 'seller_metrics')->count();
        $rows = $summary->orderBy('seller_account_id')->offset($p['offset'] ?? 0)->limit(20)->get()->map(function ($r) {
            $a = $r->getAttributes();
            $a['cancellation_rate_percent'] = round((int) $r->cancelled / max(1, (int) $r->orders) * 100, 2);

            return $a;
        })->all();
        $ids = array_column($rows, 'seller_id');
        $names = DB::table('seller_accounts')->whereIn('id', $ids)->pluck('store_name', 'id');
        // A bounded aggregate query calculates elapsed fulfillment time for the displayed sellers.
        $duration = match (DB::connection()->getDriverName()) {
            'sqlite' => '(julianday(delivered_at) - julianday(created_at)) * 24',
            'mysql', 'mariadb' => 'TIMESTAMPDIFF(SECOND, created_at, delivered_at) / 3600.0',
            'pgsql' => 'EXTRACT(EPOCH FROM (delivered_at - created_at)) / 3600.0',
            default => throw new \InvalidArgumentException('Unsupported database driver.'),
        };
        $times = MarketplaceOrder::query()->whereIn('seller_account_id', $ids)->where('status', 'delivered')->whereNotNull('delivered_at')->whereColumn('delivered_at', '>=', 'created_at');
        $this->range($times, $p, 'created_at');
        $times = $times->selectRaw('seller_account_id, AVG('.$duration.') AS hours')->groupBy('seller_account_id')->pluck('hours', 'seller_account_id');
        foreach ($rows as &$row) {
            $row['store'] = $names[$row['seller_id']] ?? null;
            $row['average_fulfillment_hours'] = isset($times[$row['seller_id']]) ? round((float) $times[$row['seller_id']], 2) : null;
        }
        unset($row);

        return ['rows' => $rows, 'total' => $total, 'limit' => 20, 'offset' => $p['offset'] ?? 0, 'has_more' => ($p['offset'] ?? 0) + count($rows) < $total,
            'chart' => ['title' => 'Delivered GMV by seller (displayed cohort)', 'unit' => 'PHP', 'rows' => array_map(fn ($r) => ['label' => $r['store'].' #'.$r['seller_id'], 'value' => (float) $r['delivered_gmv']], $rows)],
            'definition' => 'Order cohort created within Manila dates; current delivered/cancelled status. GMV includes fees, not profit. Fulfillment = delivered_at minus created_at. General complaints cannot be attributed to sellers because the schema has no seller link. Chart covers displayed sellers only.'];
    }

    private function history(AdminAccount $admin, array $p): array
    {
        $q = AdminAiAction::query();
        if ($admin->role !== 'super_admin') {
            $q->where('admin_account_id', $admin->id);
        }
        if (isset($p['status'])) {
            Validator::make($p, ['status' => [Rule::in(['pending', 'expired', 'executed', 'cancelled', 'failed'])]])->validate();
            if ($p['status'] === 'expired') {
                $q->where('status', 'pending')->where('expires_at', '<=', now());
            } elseif ($p['status'] === 'pending') {
                $q->where('status', 'pending')->where('expires_at', '>', now());
            } else {
                $q->where('status', $p['status']);
            }
        }

        return $this->page($q->orderByDesc('created_at')->orderByDesc('id'), $p, fn ($r) => [
            'id' => $r->id, 'admin_id' => $r->admin_account_id, 'type' => $r->type,
            'status' => $r->status === 'pending' && $r->expires_at->isPast() ? 'expired' : $r->status,
            'target_ids' => $r->payload['input']['ids'] ?? [], 'details' => $r->payload['input'],
            'confirmed_by' => in_array($r->status, ['executed', 'failed']) ? $r->admin_account_id : null,
            'result' => $r->result, 'created_at' => $r->created_at?->toIso8601String(), 'updated_at' => $r->updated_at?->toIso8601String(),
        ]);
    }
}
