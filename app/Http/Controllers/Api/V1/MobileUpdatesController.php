<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Compact polling snapshot. Flutter can request this on resume/foreground at a sensible interval. */
class MobileUpdatesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $account = $request->user();
        if ($account instanceof BuyerAccount) {
            $events = DB::table('marketplace_order_events as e')
                ->join('marketplace_orders as o', 'o.id', '=', 'e.marketplace_order_id')
                ->where('o.buyer_account_id', $account->id)->whereIn('e.audience', ['buyer','both'])
                ->whereNotExists(function ($q) use ($account): void {
                    $q->selectRaw('1')->from('buyer_notification_reads as r')
                        ->whereColumn('r.marketplace_order_event_id', 'e.id')
                        ->where('r.buyer_key', 'account-'.$account->id)
                        ->where(function ($q): void { $q->whereNotNull('r.deleted_at')->orWhereNotNull('r.archived_at'); });
                });
            $unread = (clone $events)->whereNotExists(function ($q) use ($account): void {
                $q->selectRaw('1')->from('buyer_notification_reads as r')
                    ->whereColumn('r.marketplace_order_event_id', 'e.id')
                    ->where('r.buyer_key', 'account-'.$account->id)->whereNotNull('r.read_at');
            })->count();
            $messages = DB::table('buyer_seller_messages')->where('buyer_account_id', $account->id)
                ->where('sender_role', 'seller');
            $data = ['role'=>'buyer','unread_notifications'=>$unread,
                'unread_messages'=>(clone $messages)->whereNull('read_at')->count(),
                'latest_notification_id'=>(int) ((clone $events)->max('e.id') ?? 0),
                'latest_message_id'=>(int) ($messages->max('id') ?? 0)];
        } elseif ($account instanceof CourierAccount) {
            app(MobileRiderNotificationController::class)->sync($account);
            $feed = DB::table('courier_notifications')->where('courier_account_id', $account->id)
                ->whereNull('archived_at')->whereNull('deleted_at');
            $messages = DB::table('logistics_messages')->where('courier_account_id', $account->id)
                ->where('sender_role', 'logistics');
            $data = ['role'=>'rider',
                'unread_notifications'=>(clone $feed)->whereNull('read_at')->count(),
                'unread_messages'=>(clone $messages)->whereNull('read_at')->count(),
                'latest_notification_id'=>(int) ((clone $feed)->max('id') ?? 0),
                'latest_message_id'=>(int) ($messages->max('id') ?? 0)];
        } elseif ($account instanceof LogisticsAccount) {
            $feed = DB::table('logistics_notifications')->where('logistics_account_id', $account->id)
                ->where('recipient_type', 'manager')->where('recipient_key', 'manager')
                ->whereNull('logistics_hub_account_id');
            $messages = DB::table('logistics_messages')
                ->where('logistics_account_id', $account->id)
                ->where('sender_role', 'rider');
            $data = ['role'=>'logistics',
                'unread_notifications'=>(clone $feed)->whereNull('read_at')->count(),
                'unread_messages'=>(clone $messages)->whereNull('read_at')->count(),
                'latest_notification_id'=>(int) ($feed->max('id') ?? 0),
                'latest_message_id'=>(int) ($messages->max('id') ?? 0)];
        } else {
            abort(403);
        }
        return response()->json(['success'=>true,'data'=>$data])->header('Cache-Control','private, no-store');
    }
}
