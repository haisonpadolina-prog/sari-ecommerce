<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AdminAccount;
use App\Models\Platform\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    private function adminId(Request $request): int
    {
        $id = (int) $request->session()->get('admin_account_id', 0);
        abort_unless($request->session()->get('is_admin') && $id > 0
            && AdminAccount::query()->whereKey($id)->exists(), 403);
        return $id;
    }

    public function index(Request $request): JsonResponse
    {
        $adminId = $this->adminId($request);
        $input = $request->validate(['after' => ['nullable', 'integer', 'min:0']]);
        $cursor = (int) ($input['after'] ?? 0);
        $ceiling = (int) AdminNotification::query()->max('id');
        $watermark = (int) DB::table('admin_notification_states')->where('admin_account_id', $adminId)->value('last_read_id');
        $unread = fn () => AdminNotification::query()->where('id', '>', $watermark)
            ->where('id', '<=', $ceiling)->whereNotExists(function ($query) use ($adminId): void {
                $query->selectRaw('1')->from('admin_notification_reads')
                    ->whereColumn('admin_notification_reads.admin_notification_id', 'admin_notifications.id')
                    ->where('admin_account_id', $adminId);
            });
        $rows = AdminNotification::query()->where('id', '<=', $ceiling)->latest('id')->limit(25)->get();
        $readIds = DB::table('admin_notification_reads')->where('admin_account_id', $adminId)
            ->whereIn('admin_notification_id', $rows->pluck('id'))->pluck('admin_notification_id')->all();
        $new = $request->has('after')
            ? $unread()->where('id', '>', $cursor)->orderBy('id')->limit(50)->get()
            : collect();

        return response()->json([
            'unread_count' => $unread()->count(),
            'unread_messages_count' => \App\Models\Messaging\ChatMessage::query()->where('sender_role', 'seller')->whereNull('read_by_admin_at')->count(),
            'latest_id' => $ceiling,
            'cursor' => $new->count() === 50 ? (int) $new->last()->id : $ceiling,
            'has_more' => $new->count() === 50,
            'notifications' => $rows->map(fn (AdminNotification $row) => array_merge($row->payload(), [
                'read' => (int) $row->id <= $watermark || in_array((int) $row->id, array_map('intval', $readIds), true),
            ]))->values(),
            'new_notifications' => $new->map(fn (AdminNotification $row) => $row->payload())->values(),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function read(Request $request, AdminNotification $notification): JsonResponse
    {
        $adminId = $this->adminId($request);
        DB::table('admin_notification_reads')->insertOrIgnore([
            'admin_account_id' => $adminId,
            'admin_notification_id' => $notification->id,
            'read_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }

    public function readAll(Request $request): JsonResponse
    {
        $adminId = $this->adminId($request);
        // The client submits the displayed snapshot so a simultaneous new event stays unread.
        $input = $request->validate(['through' => ['required', 'integer', 'min:0']]);
        $through = min((int) $input['through'], (int) AdminNotification::query()->max('id'));
        DB::table('admin_notification_states')->insertOrIgnore(['admin_account_id' => $adminId, 'last_read_id' => 0]);
        DB::table('admin_notification_states')->where('admin_account_id', $adminId)
            ->where('last_read_id', '<', $through)->update(['last_read_id' => $through]);
        return response()->json(['ok' => true]);
    }
}
