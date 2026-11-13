<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\V1\MobileRiderNotificationController;
use App\Models\Accounts\CourierAccount;
use App\Services\Mobile\MobileFcmSender;
use App\Services\Mobile\MobilePushAccess;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DispatchMobilePush extends Command
{
    protected $signature = 'sari:mobile-push {--limit=100 : Devices per database batch; all eligible batches are scanned}';
    protected $description = 'Relay recipient-scoped SARI notifications and messages to registered Flutter devices via FCM';

    public function handle(MobileFcmSender $sender, MobilePushAccess $access): int
    {
        if (!config('services.sari_fcm.project_id') || !config('services.sari_fcm.credentials_file')) {
            $this->warn('FCM credentials are not configured; no notifications sent.');
            return self::FAILURE;
        }

        $sent = 0;
        $failed = 0;
        $batchSize = max(1, min(2000, (int) $this->option('limit')));
        // ID pagination continues after every batch, even when earlier devices are removed.
        DB::table('sari_mobile_devices')->where('push_enabled', true)
            ->where('registered_at', '<', now()->subSeconds(30))
            ->chunkById($batchSize, function ($devices) use ($sender, $access, &$sent, &$failed): void {
                foreach ($devices as $device) {
                    try {
                        if (!$access->allows($device)) {
                            DB::table('sari_mobile_devices')->where('id', $device->id)->delete();
                            continue;
                        }
                        if ($device->account_role === 'rider') {
                            $rider = CourierAccount::query()->findOrFail($device->account_id);
                            app(MobileRiderNotificationController::class)->sync($rider);
                        }
                        foreach ($this->eventsFor($device) as $event) {
                            // Registration, logout, bans or opt-out may change during a long scan.
                            $current = DB::table('sari_mobile_devices')->where('id', $device->id)->first();
                            if (!$current || !$current->push_enabled
                                || (int) $current->personal_access_token_id !== (int) $device->personal_access_token_id
                                || !$access->allows($current)) {
                                break;
                            }
                            if (DB::table('sari_mobile_push_receipts')->where('mobile_device_id', $device->id)
                                ->where('event_key', $event['key'])->exists()) {
                                continue;
                            }
                            if (!$sender->send($current->token_ciphertext, $event['title'], $event['body'], $event['type'], $event['id'])) {
                                $failed++;
                                continue;
                            }
                            DB::table('sari_mobile_push_receipts')->insertOrIgnore([
                                'mobile_device_id' => $device->id, 'event_key' => $event['key'], 'sent_at' => now(),
                            ]);
                            $sent++;
                        }
                    } catch (Throwable $e) {
                        $failed++;
                        Log::warning('Push scan failed', ['device_id' => $device->id, 'exception' => get_class($e)]);
                    }
                }
            });
        $this->info("FCM accepted {$sent} push notification(s); {$failed} delivery/scan failure(s).");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /** Filter delivered events in SQL BEFORE limiting, so pending backlogs can drain. */
    private function pending(Builder $query, object $device, string $column, string $prefix): Builder
    {
        // Column names and prefixes are constants supplied below, never request input.
        $eventKey = DB::connection()->getDriverName() === 'sqlite'
            ? '? || '.$column
            : 'CONCAT(?, '.$column.')';

        return $query->whereNotExists(function (Builder $receipts) use ($device, $eventKey, $prefix): void {
            $receipts->selectRaw('1')->from('sari_mobile_push_receipts as receipts')
                ->where('receipts.mobile_device_id', $device->id)
                ->whereRaw('receipts.event_key = '.$eventKey, [$prefix]);
        });
    }

    private function eventsFor(object $device): array
    {
        $events = [];
        // Match receipt retention: never replay old events when 30-day receipts are pruned.
        $retentionStart = now()->subDays(30);
        $registeredAt = Carbon::parse($device->registered_at);
        $since = $registeredAt->gt($retentionStart) ? $registeredAt : $retentionStart;
        if ($device->account_role === 'buyer') {
            $orders = DB::table('marketplace_order_events as e')
                ->join('marketplace_orders as o', 'o.id', '=', 'e.marketplace_order_id')
                ->where('o.buyer_account_id', $device->account_id)
                ->whereIn('e.audience', ['buyer', 'both'])->where('e.created_at', '>=', $since);
            foreach ($this->pending($orders, $device, 'e.id', 'order:')->orderBy('e.id')->limit(15)->get(['e.id']) as $row) {
                $events[] = ['key'=>'order:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'order', 'title'=>'Order update', 'body'=>'You have an update to your SARI order.'];
            }
            $messages = DB::table('buyer_seller_messages as m')->where('m.buyer_account_id', $device->account_id)
                ->where('m.sender_role', 'seller')->where('m.created_at', '>=', $since);
            foreach ($this->pending($messages, $device, 'm.id', 'message:')->orderBy('m.id')->limit(15)->get(['m.id']) as $row) {
                $events[] = ['key'=>'message:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'message', 'title'=>'New seller message', 'body'=>'Open SARI to view your message.'];
            }
        } elseif ($device->account_role === 'rider') {
            $notifications = DB::table('courier_notifications as n')
                ->where('n.courier_account_id', $device->account_id)->where('n.category','!=','message')
                ->whereNull('n.deleted_at')->whereNull('n.archived_at')->where('n.occurred_at', '>=', $since);
            foreach ($this->pending($notifications, $device, 'n.id', 'rider:')->orderBy('n.id')->limit(25)->get(['n.id']) as $row) {
                $events[] = ['key'=>'rider:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'rider', 'title'=>'Rider update', 'body'=>'A delivery or Logistics update is available.'];
            }
            $messages = DB::table('logistics_messages as m')->where('m.courier_account_id', $device->account_id)
                ->where('m.sender_role', 'logistics')->where('m.created_at', '>=', $since);
            foreach ($this->pending($messages, $device, 'm.id', 'rider-message:')->orderBy('m.id')->limit(15)->get(['m.id']) as $row) {
                $events[] = ['key'=>'rider-message:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'message', 'title'=>'New Logistics message', 'body'=>'Open SARI Rider to read.'];
            }
        } elseif ($device->account_role === 'logistics') {
            $notifications = DB::table('logistics_notifications as n')
                ->where('n.logistics_account_id', $device->account_id)
                ->whereNull('n.logistics_hub_account_id')->where('n.recipient_type', 'manager')
                ->where('n.recipient_key', 'manager')->where('n.created_at', '>=', $since);
            foreach ($this->pending($notifications, $device, 'n.id', 'logistics:')->orderBy('n.id')->limit(25)->get(['n.id']) as $row) {
                $events[] = ['key'=>'logistics:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'logistics', 'title'=>'Logistics update', 'body'=>'You have a new SARI Logistics notification.'];
            }
            $messages = DB::table('logistics_messages as m')->where('m.logistics_account_id', $device->account_id)
                ->where('m.sender_role', 'rider')->where('m.created_at', '>=', $since);
            foreach ($this->pending($messages, $device, 'm.id', 'logistics-message:')->orderBy('m.id')->limit(15)->get(['m.id']) as $row) {
                $events[] = ['key'=>'logistics-message:'.$row->id, 'id'=>(string)$row->id,
                    'type'=>'message', 'title'=>'New Rider message', 'body'=>'Open SARI Logistics to read.'];
            }
        }
        return $events;
    }
}
