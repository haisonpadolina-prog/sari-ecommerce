<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Events\ChatMessageSent;
use App\Models\Messaging\ChatMessage;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Accounts\SellerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class SellerAdminChatController extends Controller
{
    public function sellerIndex(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $sellerChatRestriction = \App\Models\Messaging\SellerChatRestriction::query()
            ->where('seller_account_id', $seller->id)
            ->first();

        $sellerChatBlocked = (bool) ($sellerChatRestriction?->is_blocked);

        /*
         * Unified Seller inbox:
         * - SARI Admin Support remains the default conversation.
         * - Every buyer connected to this Seller by an order or a prior
         *   BuyerSellerMessage becomes another conversation in the same page.
         * - ?buyer=account-{id} / social-{id} selects that buyer thread.
         */
        $buyerConversations = $this->buyerConversationList($seller);

        $selectedBuyerKey = trim(
            (string) $request->query('buyer', '')
        );

        $selectedBuyer = null;
        $buyerMessages = collect();

        if ($selectedBuyerKey !== '') {
            $selectedBuyer = $buyerConversations->firstWhere(
                'key',
                $selectedBuyerKey
            );

            abort_unless($selectedBuyer, 404);

            $buyerMessagesQuery = BuyerSellerMessage::query()
                ->where('seller_account_id', $seller->id)
                ->orderBy('id');

            $this->applyBuyerKey(
                $buyerMessagesQuery,
                $selectedBuyerKey
            );

            $buyerMessages = $buyerMessagesQuery->get();

            $readQuery = BuyerSellerMessage::query()
                ->where('seller_account_id', $seller->id)
                ->where('sender_role', 'buyer')
                ->whereNull('read_at');

            $this->applyBuyerKey(
                $readQuery,
                $selectedBuyerKey
            );

            $readQuery->update([
                'read_at' => now(),
            ]);

            /*
             * The list was created before the selected thread was marked read.
             * Keep the selected row visually in sync immediately.
             */
            $buyerConversations = $buyerConversations
                ->map(function (array $row) use ($selectedBuyerKey) {
                    if ($row['key'] === $selectedBuyerKey) {
                        $row['unread'] = 0;
                    }

                    return $row;
                })
                ->values();

            $selectedBuyer['unread'] = 0;
        }

        return view('seller.messages', compact(
            'seller',
            'sellerChatRestriction',
            'sellerChatBlocked',
            'buyerConversations',
            'selectedBuyer',
            'buyerMessages'
        ));
    }

    public function sellerSend(Request $request)
    {
        $seller = $this->resolveSeller($request);

        $validated = $this->validateMessage($request);
        $attachment = $this->storeAttachment($request);

        $message = ChatMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'seller',
            'body' => $validated['message'] ?? null,
            ...$attachment,
            'read_by_seller_at' => now(),
        ]);

        $message->load('seller');
        broadcast(new ChatMessageSent($message));

        return $request->expectsJson()
            ? response()->json(['message' => $this->payload($message)])
            : back()->with('success', 'Message sent to SARI Admin.');
    }

    public function adminIndex(Request $request)
    {
        $this->requireAdmin($request);

        $sellers = SellerAccount::query()
            ->withCount([
                'chatMessages as unread_messages_count' => function ($query) {
                    $query->where('sender_role', 'seller')
                        ->whereNull('read_by_admin_at');
                },
                'chatMessages as message_count',
            ])
            ->orderByDesc(
                ChatMessage::select('created_at')
                    ->whereColumn('seller_account_id', 'seller_accounts.id')
                    ->latest('created_at')
                    ->limit(1)
            )
            ->orderBy('store_name')
            ->get();

        $selectedSeller = null;

        if ($request->filled('seller')) {
            $selectedSeller = $sellers->firstWhere('id', (int) $request->integer('seller'));
        }

        $selectedSeller ??= $sellers->first();

        $messages = collect();

        if ($selectedSeller) {
            $selectedSeller->ensureRealtimeToken();

            ChatMessage::where('seller_account_id', $selectedSeller->id)
                ->where('sender_role', 'seller')
                ->whereNull('read_by_admin_at')
                ->update(['read_by_admin_at' => now()]);

            $messages = ChatMessage::where('seller_account_id', $selectedSeller->id)
                ->oldest('created_at')
                ->get();
        }

        $stats = [
            'total_messages' => ChatMessage::count(),
            'unread' => ChatMessage::where('sender_role', 'seller')->whereNull('read_by_admin_at')->count(),
            'seller_threads' => SellerAccount::whereHas('chatMessages')->count(),
            'admin_sent' => ChatMessage::where('sender_role', 'admin')->count(),
        ];

        $adminChannel = config('sari.chat.admin_channel');

        return view('admin.messages', compact(
            'sellers',
            'selectedSeller',
            'messages',
            'stats',
            'adminChannel'
        ));
    }

    public function adminSend(Request $request, SellerAccount $seller)
    {
        $this->requireAdmin($request);

        $validated = $this->validateMessage($request);
        $attachment = $this->storeAttachment($request);

        $message = ChatMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'body' => $validated['message'] ?? null,
            ...$attachment,
            'read_by_admin_at' => now(),
        ]);

        $message->load('seller');
        broadcast(new ChatMessageSent($message));

        return $request->expectsJson()
            ? response()->json(['message' => $this->payload($message)])
            : back()->with('success', 'Message sent to seller.');
    }

    public function sellerRead(Request $request)
    {
        $seller = $this->resolveSeller($request);

        ChatMessage::where('seller_account_id', $seller->id)
            ->where('sender_role', 'admin')
            ->whereNull('read_by_seller_at')
            ->update(['read_by_seller_at' => now()]);

        return response()->noContent();
    }

    public function adminRead(Request $request, SellerAccount $seller)
    {
        $this->requireAdmin($request);

        ChatMessage::where('seller_account_id', $seller->id)
            ->where('sender_role', 'seller')
            ->whereNull('read_by_admin_at')
            ->update(['read_by_admin_at' => now()]);

        return response()->noContent();
    }

    public function attachment(Request $request, ChatMessage $message)
    {
        $allowed = (bool) $request->session()->get('is_admin');

        if (!$allowed && $request->session()->get('is_seller')) {
            $allowed = (int) $request->session()->get('seller_account_id') ===
                (int) $message->seller_account_id;
        }

        abort_unless($allowed, 403);
        abort_unless($message->attachment_path, 404);
        abort_unless(Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->response(
            $message->attachment_path,
            $message->attachment_name,
            ['Content-Disposition' => 'inline; filename="' . addslashes($message->attachment_name) . '"']
        );
    }

    private function buyerConversationList(
        SellerAccount $seller
    ): Collection {
        $keys = collect();

        MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->get([
                'buyer_account_id',
                'buyer_social_account_id',
            ])
            ->each(function ($row) use ($keys): void {
                if ($row->buyer_account_id) {
                    $keys->push(
                        'account-' . $row->buyer_account_id
                    );
                } elseif ($row->buyer_social_account_id) {
                    $keys->push(
                        'social-' . $row->buyer_social_account_id
                    );
                }
            });

        BuyerSellerMessage::query()
            ->where('seller_account_id', $seller->id)
            ->get([
                'buyer_account_id',
                'buyer_social_account_id',
            ])
            ->each(function ($row) use ($keys): void {
                if ($row->buyer_account_id) {
                    $keys->push(
                        'account-' . $row->buyer_account_id
                    );
                } elseif ($row->buyer_social_account_id) {
                    $keys->push(
                        'social-' . $row->buyer_social_account_id
                    );
                }
            });

        return $keys
            ->filter()
            ->unique()
            ->map(function (string $key) use ($seller): array {
                [$type, $id] = $this->parseBuyerKey($key);

                if ($type === 'account') {
                    $buyer = BuyerAccount::query()->find($id);

                    $name = $buyer
                        ? trim(
                            $buyer->first_name
                            . ' '
                            . $buyer->last_name
                        )
                        : 'Buyer #' . $id;

                    $email = $buyer?->email;
                } else {
                    $buyer = SocialAccount::query()->find($id);
                    $name = $buyer?->name
                        ?: 'Social Buyer #' . $id;
                    $email = $buyer?->email;
                }

                $messageQuery = BuyerSellerMessage::query()
                    ->where(
                        'seller_account_id',
                        $seller->id
                    );

                $this->applyBuyerKey(
                    $messageQuery,
                    $key
                );

                $latestMessage = (clone $messageQuery)
                    ->latest('id')
                    ->first();

                $messageCount = (clone $messageQuery)->count();

                $unreadQuery = (clone $messageQuery)
                    ->where('sender_role', 'buyer')
                    ->whereNull('read_at');

                $orderQuery = MarketplaceOrder::query()
                    ->where(
                        'seller_account_id',
                        $seller->id
                    );

                $this->applyBuyerKey(
                    $orderQuery,
                    $key
                );

                $latestOrder = (clone $orderQuery)
                    ->latest('id')
                    ->first();

                $orderCount = (clone $orderQuery)->count();

                $preview = trim(
                    (string) ($latestMessage?->body ?? '')
                );

                if ($preview === '' && $latestOrder) {
                    $preview =
                        'Order '
                        . $latestOrder->order_number;
                }

                if ($preview === '') {
                    $preview = 'Buyer conversation';
                }

                $lastAt =
                    $latestMessage?->created_at
                    ?? $latestOrder?->created_at;

                $initials = collect(
                    preg_split(
                        '/\s+/',
                        trim($name)
                    ) ?: []
                )
                    ->filter()
                    ->take(2)
                    ->map(
                        fn ($part) =>
                            mb_strtoupper(
                                mb_substr($part, 0, 1)
                            )
                    )
                    ->implode('');

                return [
                    'key' => $key,
                    'name' => $name,
                    'email' => $email,
                    'initials' => $initials ?: 'B',
                    'preview' => $preview,
                    'unread' => $unreadQuery->count(),
                    'message_count' => $messageCount,
                    'order_count' => $orderCount,
                    'last_at' => $lastAt,
                    'type' => $type,
                ];
            })
            ->sortByDesc(
                fn (array $row) =>
                    $row['last_at']?->getTimestamp()
                    ?? 0
            )
            ->values();
    }

    private function parseBuyerKey(string $key): array
    {
        abort_unless(
            preg_match(
                '/^(account|social)-(\d+)$/',
                $key,
                $matches
            ),
            404
        );

        return [
            $matches[1],
            (int) $matches[2],
        ];
    }

    private function applyBuyerKey(
        $query,
        string $key
    ): void {
        [$type, $id] =
            $this->parseBuyerKey($key);

        $query->where(
            $type === 'account'
                ? 'buyer_account_id'
                : 'buyer_social_account_id',
            $id
        );
    }

    private function validateMessage(Request $request): array
    {
        return $request->validate([
            'message' => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            'attachment' => [
                'nullable',
                'file',
                'max:8192',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,zip,txt',
                'required_without:message',
            ],
        ], [
            'message.required_without' => 'Write a message or attach a file.',
            'attachment.required_without' => 'Write a message or attach a file.',
            'attachment.max' => 'Attachments must be 8 MB or smaller.',
        ]);
    }

    private function storeAttachment(Request $request): array
    {
        if (!$request->hasFile('attachment')) {
            return [
                'attachment_path' => null,
                'attachment_name' => null,
                'attachment_mime' => null,
                'attachment_size' => null,
            ];
        }

        $file = $request->file('attachment');

        return [
            'attachment_path' => $file->store('chat-attachments', 'local'),
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_mime' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function payload(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'seller_id' => $message->seller_account_id,
            'seller_name' => $message->seller->store_name ?: $message->seller->email,
            'sender_role' => $message->sender_role,
            'body' => $message->body,
            'attachment_name' => $message->attachment_name,
            'attachment_mime' => $message->attachment_mime,
            'attachment_size' => $message->attachment_size,
            'attachment_url' => $message->attachment_path
                ? route('chat.attachments.show', $message, false)
                : null,
            'created_at' => $message->created_at?->toIso8601String(),
            'time' => $message->created_at?->format('h:i A'),
        ];
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403, 'Seller session required.');

        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        $seller->refreshSuspensionStatus();
        $seller->ensureRealtimeToken();
        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->session()->get('is_admin'), 403, 'Admin session required.');
    }
}
