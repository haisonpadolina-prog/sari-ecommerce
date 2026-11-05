<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mobile Rider notification feed backed by the same courier_notifications table
 * used by the Laravel Rider web UI. This keeps read state synchronized across
 * web and mobile while still using Sanctum authentication for the app.
 */
class MobileRiderNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $this->assertReady();
        $this->sync($rider);

        $base = DB::table('courier_notifications')
            ->where('courier_account_id', $rider->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at');

        $unread = (clone $base)->whereNull('read_at')->count();
        $messageUnread = (clone $base)
            ->where('category', 'message')
            ->whereNull('read_at')
            ->count();

        $items = (clone $base)
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->limit(60)
            ->get()
            ->map(fn ($row) => $this->present($row))
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unread,
                'messages_unread_count' => $messageUnread,
                'items' => $items,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function summary(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $this->assertReady();
        $this->sync($rider);

        $base = DB::table('courier_notifications')
            ->where('courier_account_id', $rider->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at');

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => (clone $base)->whereNull('read_at')->count(),
                'messages_unread_count' => (clone $base)
                    ->where('category', 'message')
                    ->whereNull('read_at')
                    ->count(),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function read(Request $request, int $notification): JsonResponse
    {
        $rider = $this->rider($request);
        $this->assertReady();
        $row = $this->owned($rider, $notification);
        $now = now();

        DB::table('courier_notifications')
            ->where('id', $row->id)
            ->where('courier_account_id', $rider->id)
            ->update([
                'read_at' => $row->read_at ?? $now,
                'updated_at' => $now,
            ]);

        // Reading the notification on mobile also marks the same Logistics
        // message read for the web Rider UI.
        if ($row->category === 'message'
            && preg_match('/^message:(\d+)$/', (string) $row->event_key, $match)) {
            LogisticsMessage::query()
                ->where('courier_account_id', $rider->id)
                ->where('sender_role', 'logistics')
                ->whereKey((int) $match[1])
                ->whereNull('read_at')
                ->update(['read_at' => $now]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ])->header('Cache-Control', 'no-store, private');
    }

    public function readAll(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $this->assertReady();
        $this->sync($rider);
        $now = now();

        DB::table('courier_notifications')
            ->where('courier_account_id', $rider->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->whereNull('read_at')
            ->update(['read_at' => $now, 'updated_at' => $now]);

        LogisticsMessage::query()
            ->where('courier_account_id', $rider->id)
            ->where('sender_role', 'logistics')
            ->whereNull('read_at')
            ->update(['read_at' => $now]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'data' => [
                'unread_count' => 0,
                'messages_unread_count' => 0,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    private function sync(CourierAccount $rider): void
    {
        $id = (int) $rider->id;
        $now = now();
        $rows = [];

        $messages = LogisticsMessage::query()
            ->where('courier_account_id', $id)
            ->where('sender_role', 'logistics')
            ->latest('id')
            ->limit(80)
            ->get(['id', 'body', 'read_at', 'created_at']);

        foreach ($messages as $message) {
            $rows[] = [
                'courier_account_id' => $id,
                'event_key' => 'message:'.$message->id,
                'category' => 'message',
                'title' => 'New message from Logistics',
                'message' => mb_strimwidth(trim((string) $message->body), 0, 220, '…'),
                'target_url' => '/courier/messages',
                'occurred_at' => $message->created_at ?? $now,
                'read_at' => $message->read_at,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $email = strtolower(trim((string) $rider->email));
        $orders = MarketplaceOrder::query()
            ->whereRaw('LOWER(courier_email) = ?', [$email])
            ->whereIn('status', [
                'courier_accepted', 'heading_pickup', 'arrived_pickup',
                'in_transit', 'arrived_buyer', 'delivered', 'completed',
            ])
            ->latest('updated_at')
            ->limit(80)
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
            $rows[] = [
                'courier_account_id' => $id,
                'event_key' => 'order:'.$order->id.':'.$status.':'.$time->format('YmdHis.u'),
                'category' => 'delivery',
                'title' => $statusLabels[$status] ?? 'Delivery update',
                'message' => 'Order '.$order->order_number.' · '.str_replace('_', ' ', $status),
                'target_url' => '/courier/delivery/'.$order->id,
                'occurred_at' => $time,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (Schema::hasTable('last_mile_assignments')) {
            $batches = DB::table('last_mile_assignments')
                ->where('courier_account_id', $id)
                ->whereIn('status', ['assigned', 'out_for_delivery', 'completed'])
                ->orderByDesc('updated_at')
                ->limit(60)
                ->get(['id', 'assignment_code', 'status', 'updated_at', 'assigned_at']);

            foreach ($batches as $batch) {
                $time = Carbon::parse($batch->updated_at ?? $batch->assigned_at ?? $now);
                $status = (string) $batch->status;
                $rows[] = [
                    'courier_account_id' => $id,
                    'event_key' => 'lastmile:'.$batch->id.':'.$status.':'.$time->format('YmdHis.u'),
                    'category' => 'last_mile',
                    'title' => match ($status) {
                        'assigned' => 'New last-mile batch assigned',
                        'out_for_delivery' => 'Last-mile route started',
                        default => 'Last-mile batch completed',
                    },
                    'message' => 'Batch '.$batch->assignment_code.' · '.str_replace('_', ' ', $status),
                    'target_url' => '/courier/last-mile',
                    'occurred_at' => $time,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (Schema::hasTable('finance_payout_requests')) {
            $payouts = FinancePayoutRequest::query()
                ->where('owner_type', 'courier')
                ->where('owner_id', $id)
                ->latest('updated_at')
                ->limit(40)
                ->get(['id', 'request_code', 'status', 'requested_at', 'updated_at']);

            foreach ($payouts as $payout) {
                $status = (string) $payout->status;
                $time = $payout->updated_at ?? $payout->requested_at ?? $now;
                $rows[] = [
                    'courier_account_id' => $id,
                    'event_key' => 'payout:'.$payout->id.':'.$status,
                    'category' => 'earnings',
                    'title' => match ($status) {
                        'pending', 'requested', 'submitted' => 'Withdrawal request received',
                        'approved', 'processing' => 'Withdrawal request approved',
                        'paid', 'completed' => 'Payout completed',
                        default => 'Payout status updated',
                    },
                    'message' => 'Request '.($payout->request_code ?: $payout->id).' · '.str_replace('_', ' ', $status),
                    'target_url' => '/courier/earnings',
                    'occurred_at' => $time,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (Schema::hasTable('cod_remittance_batches')) {
            $batches = CodRemittanceBatch::query()
                ->where('courier_account_id', $id)
                ->latest('updated_at')
                ->limit(40)
                ->get(['id', 'batch_code', 'status', 'submitted_at', 'updated_at']);

            foreach ($batches as $batch) {
                $status = (string) $batch->status;
                $time = $batch->updated_at ?? $batch->submitted_at ?? $now;
                $rows[] = [
                    'courier_account_id' => $id,
                    'event_key' => 'cod:'.$batch->id.':'.$status,
                    'category' => 'remittance',
                    'title' => match ($status) {
                        'submitted', 'pending_verification' => 'COD remittance submitted',
                        'verified', 'confirmed', 'reconciled' => 'COD remittance verified',
                        default => 'COD remittance update',
                    },
                    'message' => 'Batch '.$batch->batch_code.' · '.str_replace('_', ' ', $status),
                    'target_url' => '/courier/cod-remittance',
                    'occurred_at' => $time,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('courier_notifications')->insertOrIgnore($chunk);
        }

        $readMessageKeys = $messages
            ->filter(fn ($message) => $message->read_at !== null)
            ->map(fn ($message) => 'message:'.$message->id)
            ->all();

        if ($readMessageKeys !== []) {
            DB::table('courier_notifications')
                ->where('courier_account_id', $id)
                ->whereIn('event_key', $readMessageKeys)
                ->whereNull('read_at')
                ->update(['read_at' => $now, 'updated_at' => $now]);
        }
    }

    private function present(object $row): array
    {
        $occurred = Carbon::parse($row->occurred_at);

        return [
            'id' => (int) $row->id,
            'title' => (string) $row->title,
            'message' => (string) $row->message,
            'category' => (string) $row->category,
            'url' => (string) $row->target_url,
            'unread' => $row->read_at === null,
            'time' => $occurred->diffForHumans(),
            'occurred_at' => $occurred->toIso8601String(),
        ];
    }

    private function owned(CourierAccount $rider, int $notification): object
    {
        $row = DB::table('courier_notifications')
            ->where('courier_account_id', $rider->id)
            ->where('id', $notification)
            ->whereNull('deleted_at')
            ->first();

        abort_unless($row, 404, 'Notification not found.');
        return $row;
    }

    private function assertReady(): void
    {
        abort_unless(
            Schema::hasTable('courier_notifications')
                && Schema::hasColumn('courier_notifications', 'archived_at')
                && Schema::hasColumn('courier_notifications', 'deleted_at'),
            503,
            'Rider notification migration is pending.'
        );
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
