<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\SellerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerAdminChatController extends Controller
{
    public function sellerIndex(Request $request)
    {
        $seller = $this->resolveSeller($request);

        ChatMessage::where('seller_account_id', $seller->id)
            ->where('sender_role', 'admin')
            ->whereNull('read_by_seller_at')
            ->update(['read_by_seller_at' => now()]);

        $messages = ChatMessage::where('seller_account_id', $seller->id)
            ->oldest('created_at')
            ->get();

        $stats = [
            'total_messages' => $messages->count(),
            'unread' => 0,
            'sent_by_seller' => $messages->where('sender_role', 'seller')->count(),
            'admin_replies' => $messages->where('sender_role', 'admin')->count(),
        ];

        return view('seller.messages', compact('seller', 'messages', 'stats'));
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

        $adminChannel = config('sari_chat.admin_channel');

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

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        $seller->refreshSuspensionStatus();
        $seller->ensureRealtimeToken();

        return $seller;
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->session()->get('is_admin'), 403, 'Admin session required.');
    }
}
