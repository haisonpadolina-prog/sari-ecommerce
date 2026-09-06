<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\SellerAccount;
use App\Models\SellerChatModerationAction;
use App\Models\SellerChatRestriction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminSellerChatActionController extends Controller
{
    private const ADMIN_ONLINE_CACHE_KEY = 'sari_admin_support_online';
    private const ADMIN_LAST_SEEN_CACHE_KEY = 'sari_admin_support_last_seen';

    public function presence(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        // The Messages page also sends online=0 during pagehide/unload, so the
        // offline bot can activate immediately instead of waiting for the TTL.
        if ($request->has('online') && !$request->boolean('online')) {
            Cache::forget(self::ADMIN_ONLINE_CACHE_KEY);

            return response()->json([
                'success' => true,
                'online' => false,
            ]);
        }

        $stamp = now()->timestamp;

        // Safety TTL in case the browser closes without delivering pagehide.
        Cache::put(self::ADMIN_ONLINE_CACHE_KEY, $stamp, now()->addMinutes(3));
        Cache::forever(self::ADMIN_LAST_SEEN_CACHE_KEY, $stamp);

        return response()->json([
            'success' => true,
            'online' => true,
            'last_seen' => now()->toIso8601String(),
        ]);
    }

    public function warning(Request $request, SellerAccount $seller): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($seller, $validated): void {
            $seller->refresh();
            $current = max(0, (int) ($seller->warning_count ?? 0));

            if ($current >= 3) {
                throw ValidationException::withMessages([
                    'warning' => 'This seller already has 3 active warnings.',
                ]);
            }

            $next = min(3, $current + 1);
            $seller->warning_count = $next;

            if ($next >= 3) {
                // Keep account_status as-is (normally 'active'). The current SARI
                // restriction system treats suspended_until as the suspension flag;
                // account_status is reserved for states such as banned/deactivated.
                $seller->suspended_until = now()->addDays(30);
                $seller->suspension_reason = 'Automatic 30-day suspension after Warning 3/3. ' . $validated['reason'];
            }

            $seller->save();

            SellerChatModerationAction::create([
                'seller_account_id' => $seller->id,
                'action_type' => 'warning',
                'reason' => $validated['reason'],
                'metadata' => [
                    'warning_number' => $next,
                    'max_warnings' => 3,
                    'auto_suspended' => $next >= 3,
                    'suspended_until' => $seller->suspended_until?->toIso8601String(),
                ],
            ]);

            $body = "Compliance Warning {$next}/3: {$validated['reason']}";
            if ($next >= 3) {
                $body .= ' Your selling privileges are suspended for 30 days.';
            }

            $this->createAdminNotice($seller, $body);
        });

        return redirect()
            ->route('admin.messages', ['seller' => $seller->id])
            ->with('success', 'Warning issued to seller.');
    }

    public function suspend(Request $request, SellerAccount $seller): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        if (in_array((string) $seller->account_status, ['banned', 'deactivated'], true)) {
            throw ValidationException::withMessages([
                'suspension' => 'This seller account cannot be suspended while it is banned or deactivated.',
            ]);
        }

        DB::transaction(function () use ($seller, $validated): void {
            // Do not change account_status to 'suspended'. Existing SARI middleware
            // and account-state checks use suspended_until while account_status stays active.
            $seller->suspended_until = now()->addDays(30);
            $seller->suspension_reason = $validated['reason'];
            $seller->save();

            SellerChatModerationAction::create([
                'seller_account_id' => $seller->id,
                'action_type' => 'suspend_30_days',
                'reason' => $validated['reason'],
                'metadata' => [
                    'suspended_until' => $seller->suspended_until?->toIso8601String(),
                ],
            ]);

            $this->createAdminNotice(
                $seller,
                'Account Suspension: Your seller account has been suspended for 30 days. Reason: ' . $validated['reason']
            );
        });

        return redirect()
            ->route('admin.messages', ['seller' => $seller->id])
            ->with('success', 'Seller suspended for 30 days.');
    }

    public function block(Request $request, SellerAccount $seller): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($seller, $validated): void {
            SellerChatRestriction::query()->updateOrCreate(
                ['seller_account_id' => $seller->id],
                [
                    'is_blocked' => true,
                    'block_reason' => $validated['reason'],
                    'blocked_at' => now(),
                    'unblocked_at' => null,
                ]
            );

            SellerChatModerationAction::create([
                'seller_account_id' => $seller->id,
                'action_type' => 'block_chat',
                'reason' => $validated['reason'],
            ]);

            $this->createAdminNotice(
                $seller,
                'Support messaging has been temporarily restricted. Reason: ' . $validated['reason']
            );
        });

        return redirect()
            ->route('admin.messages', ['seller' => $seller->id])
            ->with('success', 'Seller support chat blocked.');
    }

    public function unblock(Request $request, SellerAccount $seller): RedirectResponse
    {
        $this->ensureAdmin($request);

        DB::transaction(function () use ($seller): void {
            SellerChatRestriction::query()->updateOrCreate(
                ['seller_account_id' => $seller->id],
                [
                    'is_blocked' => false,
                    'block_reason' => null,
                    'unblocked_at' => now(),
                ]
            );

            SellerChatModerationAction::create([
                'seller_account_id' => $seller->id,
                'action_type' => 'unblock_chat',
                'reason' => 'Seller support messaging restored by administrator.',
            ]);

            $this->createAdminNotice(
                $seller,
                'Support messaging has been restored. You can send messages to SARI Admin again.'
            );
        });

        return redirect()
            ->route('admin.messages', ['seller' => $seller->id])
            ->with('success', 'Seller support chat unblocked.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless((bool) $request->session()->get('is_admin'), 403, 'Admin session required.');
    }

    private function createAdminNotice(SellerAccount $seller, string $body): void
    {
        $message = new ChatMessage();
        $message->seller_account_id = $seller->id;
        $message->sender_role = 'admin';
        $message->body = $body;

        if (Schema::hasColumn('chat_messages', 'is_bot')) {
            $message->is_bot = false;
        }

        $message->save();
        $this->broadcastMessage($message);
    }

    private function broadcastMessage(ChatMessage $message): void
    {
        // Keep the feature compatible with the project's current Reverb event,
        // but never let a broadcasting problem roll back a moderation action.
        try {
            if (class_exists(\App\Events\ChatMessageSent::class)) {
                event(new \App\Events\ChatMessageSent($message));
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
