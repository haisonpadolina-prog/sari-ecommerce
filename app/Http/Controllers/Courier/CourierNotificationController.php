<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Rider-specific notifications. Sources are genuine courier-scoped records.
 * Syncing at read/poll time means existing dispatch and messaging controllers
 * do not need risky changes to their delivery or payout workflows.
 */
class CourierNotificationController extends Controller
{
    private function account(Request $request): CourierAccount
    {
        abort_unless($request->session()->get('is_courier'), 403);

        $id = (int) $request->session()->get('courier_account_id', 0);
        $account = $id > 0
            ? CourierAccount::query()->whereKey($id)->where('account_status', 'active')->first()
            : null;

        if (! $account) {
            $email = strtolower(trim((string) $request->session()->get('courier_email', '')));
            abort_if($email === '', 403);
            $account = CourierAccount::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->where('account_status', 'active')
                ->first();
        }
        abort_unless($account, 403, 'Active rider account required.');
        return $account;
    }

    private function sync(CourierAccount $account): void
    {
        // When this patch was deployed without its migration, never break the entire Rider UI.
        if (! Schema::hasTable('courier_notifications')) return;
        $id = (int) $account->id;
        $now = now();
        $new = [];

        // Incoming messages from Logistics, *not* the rider's sent messages.
        $messages = LogisticsMessage::query()
            ->where('courier_account_id', $id)
            ->where('sender_role', 'logistics')
            ->latest('id')->limit(80)
            ->get(['id', 'body', 'read_at', 'created_at']);

        foreach ($messages as $message) {
            $new[] = [
                'courier_account_id' => $id,
                'event_key' => 'message:'.$message->id,
                'category' => 'message',
                'title' => 'New message from Logistics',
                'message' => mb_strimwidth(trim((string) $message->body), 0, 220, '…'),
                'target_url' => route('courier.messages'),
                'occurred_at' => $message->created_at ?? $now,
                'read_at' => $message->read_at,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Pickup and direct-delivery status updates for this actual rider.
        $email = strtolower(trim((string) $account->email));
        $orders = MarketplaceOrder::query()
            ->whereRaw('LOWER(courier_email) = ?', [$email])
            ->whereIn('status', [
                'courier_accepted', 'heading_pickup', 'arrived_pickup',
                'in_transit', 'arrived_buyer', 'delivered', 'completed',
            ])
            ->latest('updated_at')->limit(80)
            ->get(['id', 'order_number', 'status', 'updated_at']);

        $statusLabels = [
            'courier_accepted' => 'New pickup assignment',
            'heading_pickup' => 'Pickup in progress',
            'arrived_pickup' => 'Arrived at seller pickup',
            'in_transit' => 'Delivery in transit',
            'arrived_buyer' => 'Arrived at buyer',
            'delivered' => 'Delivery completed',
            'completed' => 'Delivery completed',
        ];
        foreach ($orders as $order) {
            $status = (string) $order->status;
            $time = $order->updated_at ?? $now;
            $title = $statusLabels[$status] ?? 'Delivery update';
            $new[] = [
                'courier_account_id' => $id,
                'event_key' => 'order:'.$order->id.':'.$status.':'.$time->format('YmdHis.u'),
                'category' => 'delivery',
                'title' => $title,
                'message' => 'Order '.(string) $order->order_number.' · '.str_replace('_', ' ', $status),
                'target_url' => in_array($status, ['delivered', 'completed'], true)
                    ? route('courier.history')
                    : route('courier.delivery.show', ['order' => $order->id]),
                'occurred_at' => $time,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Last-mile batches are owned by courier_account_id, not global batches.
        $batches = DB::table('last_mile_assignments')
            ->where('courier_account_id', $id)
            ->whereIn('status', ['assigned', 'out_for_delivery', 'completed'])
            ->orderByDesc('updated_at')->limit(60)
            ->get(['id', 'assignment_code', 'status', 'updated_at', 'assigned_at']);
        foreach ($batches as $batch) {
            $time = Carbon::parse($batch->updated_at ?? $batch->assigned_at ?? $now);
            $status = (string) $batch->status;
            $title = match ($status) {
                'assigned' => 'New last-mile batch assigned',
                'out_for_delivery' => 'Last-mile route started',
                default => 'Last-mile batch completed',
            };
            $new[] = [
                'courier_account_id' => $id,
                'event_key' => 'lastmile:'.$batch->id.':'.$status.':'.$time->format('YmdHis.u'),
                'category' => 'last_mile',
                'title' => $title,
                'message' => 'Batch '.(string) $batch->assignment_code.' · '.str_replace('_', ' ', $status),
                'target_url' => route('courier.last-mile'),
                'occurred_at' => $time,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Rider wallet payout requests: actual finance-owned records, scoped by rider.
        $payouts = \App\Models\Finance\FinancePayoutRequest::query()
            ->where('owner_type', 'courier')
            ->where('owner_id', $id)
            ->latest('updated_at')->limit(40)
            ->get(['id', 'request_code', 'status', 'amount', 'requested_at', 'updated_at']);
        foreach ($payouts as $payout) {
            $status = (string) $payout->status;
            $time = $payout->updated_at ?? $payout->requested_at ?? $now;
            $new[] = [
                'courier_account_id' => $id,
                'event_key' => 'payout:'.$payout->id.':'.$status,
                'category' => 'earnings',
                'title' => match ($status) {
                    'pending', 'requested', 'submitted' => 'Withdrawal request received',
                    'approved', 'processing' => 'Withdrawal request approved',
                    'paid', 'completed' => 'Payout completed',
                    'rejected', 'cancelled' => 'Payout status updated',
                    default => 'Payout status updated',
                },
                'message' => 'Request '.(string) ($payout->request_code ?: $payout->id).' · '.str_replace('_', ' ', $status),
                'target_url' => route('courier.earnings'),
                'occurred_at' => $time,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Rider COD remittance status changes, without querying another rider's batches.
        $codBatches = \App\Models\Finance\CodRemittanceBatch::query()
            ->where('courier_account_id', $id)
            ->latest('updated_at')->limit(40)
            ->get(['id', 'batch_code', 'status', 'updated_at', 'submitted_at']);
        foreach ($codBatches as $batch) {
            $status = (string) $batch->status;
            $time = $batch->updated_at ?? $batch->submitted_at ?? $now;
            $new[] = [
                'courier_account_id' => $id,
                'event_key' => 'cod:'.$batch->id.':'.$status,
                'category' => 'remittance',
                'title' => match ($status) {
                    'submitted', 'pending_verification' => 'COD remittance submitted',
                    'verified', 'confirmed', 'reconciled' => 'COD remittance verified',
                    default => 'COD remittance update',
                },
                'message' => 'Batch '.(string) $batch->batch_code.' · '.str_replace('_', ' ', $status),
                'target_url' => route('courier.remittances.index'),
                'occurred_at' => $time,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($new, 100) as $rows) {
            DB::table('courier_notifications')->insertOrIgnore($rows);
        }

        // Opening the Chat page already marks incoming messages as read.
        // Keep the notification badge in sync with that existing behavior.
        $readMessageKeys = $messages->filter(fn ($m) => $m->read_at !== null)
            ->map(fn ($m) => 'message:'.$m->id)->all();
        if ($readMessageKeys !== []) {
            DB::table('courier_notifications')
                ->where('courier_account_id', $id)
                ->whereIn('event_key', $readMessageKeys)
                ->whereNull('read_at')
                ->update(['read_at' => $now, 'updated_at' => $now]);
        }
    }

    /** New columns are installed by an additive migration. Keep header alive until it runs. */
    private function foldersReady(): bool
    {
        return Schema::hasTable('courier_notifications')
            && Schema::hasColumn('courier_notifications', 'archived_at')
            && Schema::hasColumn('courier_notifications', 'deleted_at');
    }

    /** Only visible, non-archived records appear in the shared header/sidebar feed. */
    private function data(CourierAccount $account): array
    {
        if (! $this->foldersReady()) {
            return ['unread_count' => 0, 'messages_unread_count' => 0, 'items' => []];
        }
        $this->sync($account);
        $base = DB::table('courier_notifications')
            ->where('courier_account_id', $account->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at');
        $unread = (clone $base)->whereNull('read_at')->count();
        $messageUnread = (clone $base)->where('category', 'message')->whereNull('read_at')->count();
        $records = (clone $base)->orderByDesc('occurred_at')->orderByDesc('id')->limit(40)->get();
        $items = $records->map(fn ($row) => [
            'id' => (int) $row->id,
            'title' => (string) $row->title,
            'message' => (string) $row->message,
            'category' => (string) $row->category,
            'url' => (string) $row->target_url,
            'unread' => $row->read_at === null,
            'time' => Carbon::parse($row->occurred_at)->diffForHumans(),
            'occurred_at' => Carbon::parse($row->occurred_at)->toIso8601String(),
        ])->all();
        return ['unread_count' => $unread, 'messages_unread_count' => $messageUnread, 'items' => $items];
    }

    private function ownedNotification(CourierAccount $account, int $notification)
    {
        $row = DB::table('courier_notifications')
            ->where('courier_account_id', $account->id)
            ->where('id', $notification)
            ->whereNull('deleted_at')
            ->first();
        abort_unless($row, 404, 'Notification not found.');
        return $row;
    }

    public function index(Request $request): View
    {
        $account = $this->account($request);
        $showArchived = $request->query('filter') === 'archived';
        $unreadCount = 0;
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
        if ($this->foldersReady()) {
            $this->sync($account);
            $base = DB::table('courier_notifications')
                ->where('courier_account_id', $account->id)
                ->whereNull('deleted_at');
            $unreadCount = (clone $base)->whereNull('archived_at')->whereNull('read_at')->count();
            $notifications = (clone $base)
                ->when($showArchived, fn ($query) => $query->whereNotNull('archived_at'),
                    fn ($query) => $query->whereNull('archived_at'))
                ->orderByDesc($showArchived ? 'archived_at' : 'occurred_at')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString();
        }
        return view('courier.notifications', compact('notifications', 'showArchived', 'unreadCount'));
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json($this->data($this->account($request)))
            ->header('Cache-Control', 'no-store, private');
    }

    public function read(Request $request, int $notification): JsonResponse|RedirectResponse
    {
        abort_unless($this->foldersReady(), 503, 'Rider notification migration is pending.');
        $account = $this->account($request);
        $row = $this->ownedNotification($account, $notification);
        $now = now();
        DB::table('courier_notifications')
            ->where('id', $row->id)
            ->where('courier_account_id', $account->id)
            ->update(['read_at' => $row->read_at ?? $now, 'updated_at' => $now]);
        if ($row->category === 'message' && preg_match('/^message:(\d+)$/', (string) $row->event_key, $match)) {
            LogisticsMessage::query()->where('courier_account_id', $account->id)
                ->where('sender_role', 'logistics')->whereKey((int) $match[1])
                ->whereNull('read_at')->update(['read_at' => $now]);
        }
        if ($request->expectsJson()) return response()->json(['ok' => true, 'url' => $row->target_url]);
        if ($request->boolean('stay')) return back()->with('success', 'Notification marked as read.');
        return $this->localTarget($row->target_url)
            ? redirect()->to($row->target_url)
            : back()->with('success', 'Notification marked as read.');
    }

    /** Keep user-owned targets local when following notification link redirects. */
    private function localTarget(?string $target): bool
    {
        if (! is_string($target) || $target === '') return false;
        if (str_starts_with($target, '/') && ! str_starts_with($target, '//')) return true;
        $host = parse_url($target, PHP_URL_HOST);
        $scheme = parse_url($target, PHP_URL_SCHEME);
        return in_array($scheme, ['http', 'https'], true)
            && is_string($host) && strcasecmp($host, (string) request()->getHost()) === 0;
    }

    public function readAll(Request $request): JsonResponse|RedirectResponse
    {
        abort_unless($this->foldersReady(), 503, 'Rider notification migration is pending.');
        $account = $this->account($request);
        $this->sync($account);
        $now = now();
        DB::table('courier_notifications')->where('courier_account_id', $account->id)
            ->whereNull('archived_at')->whereNull('deleted_at')
            ->whereNull('read_at')->update(['read_at' => $now, 'updated_at' => $now]);
        LogisticsMessage::query()->where('courier_account_id', $account->id)
            ->where('sender_role', 'logistics')->whereNull('read_at')->update(['read_at' => $now]);
        if ($request->expectsJson()) return response()->json(['ok' => true, 'unread_count' => 0, 'messages_unread_count' => 0]);
        return back()->with('success', 'Notifications marked as read.');
    }

    public function archive(Request $request, int $notification): RedirectResponse
    {
        abort_unless($this->foldersReady(), 503);
        $account = $this->account($request);
        $row = $this->ownedNotification($account, $notification);
        abort_if($row->archived_at !== null, 404);
        $now = now();
        DB::table('courier_notifications')
            ->where('courier_account_id', $account->id)->where('id', $notification)
            ->update(['archived_at' => $now, 'read_at' => $row->read_at ?? $now, 'updated_at' => $now]);
        if ($row->category === 'message' && preg_match('/^message:(\d+)$/', (string) $row->event_key, $match)) {
            LogisticsMessage::query()->where('courier_account_id', $account->id)
                ->where('sender_role', 'logistics')->whereKey((int) $match[1])
                ->whereNull('read_at')->update(['read_at' => $now]);
        }
        return redirect()->route('courier.notifications')->with('success', 'Notification archived.');
    }

    public function restore(Request $request, int $notification): RedirectResponse
    {
        abort_unless($this->foldersReady(), 503);
        $account = $this->account($request);
        $row = $this->ownedNotification($account, $notification);
        abort_if($row->archived_at === null, 404);
        DB::table('courier_notifications')
            ->where('courier_account_id', $account->id)->where('id', $notification)
            ->update(['archived_at' => null, 'updated_at' => now()]);
        return redirect()->route('courier.notifications', ['filter' => 'archived'])
            ->with('success', 'Notification restored to Recent.');
    }

    public function destroy(Request $request, int $notification): RedirectResponse
    {
        abort_unless($this->foldersReady(), 503);
        $account = $this->account($request);
        $row = $this->ownedNotification($account, $notification);
        $now = now();
        // Soft delete preserves the unique event_key. Polling must never recreate a deleted source.
        DB::table('courier_notifications')
            ->where('courier_account_id', $account->id)->where('id', $notification)
            ->update(['deleted_at' => $now, 'read_at' => $row->read_at ?? $now, 'updated_at' => $now]);
        if ($row->category === 'message' && preg_match('/^message:(\d+)$/', (string) $row->event_key, $match)) {
            LogisticsMessage::query()->where('courier_account_id', $account->id)
                ->where('sender_role', 'logistics')->whereKey((int) $match[1])
                ->whereNull('read_at')->update(['read_at' => $now]);
        }
        return back()->with('success', 'Notification deleted from your history.');
    }
}
