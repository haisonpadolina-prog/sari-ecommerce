<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\ProductReview;
use App\Models\Catalog\SellerProduct;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\SellerNotification;
use App\Services\Buyer\BuyerIdentityService;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BuyerSellerMessageController extends Controller
{
    public function __construct(
        private readonly BuyerIdentityService $identity,
        private readonly BuyerSellerOrderConfirmationService $chat,
    ) {
    }

    public function buyerIndex(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $this->identity->guard($request);
        $sellerIds = $this->identity->apply(MarketplaceOrder::query(), $request)
            ->distinct()->pluck('seller_account_id')
            ->merge($this->identity->apply(BuyerSellerMessage::query(), $request)
                ->distinct()->pluck('seller_account_id'))
            ->merge(SellerProduct::query()->buyerVisible()->distinct()
                ->pluck('seller_account_id'))
            ->filter()->unique()->values();

        $sellers = SellerAccount::query()->whereIn('id', $sellerIds)
            ->orderBy('store_name')->get();

        $lastSellerId = (int) $this->identity->apply(MarketplaceOrder::query(), $request)
            ->latest('id')->value('seller_account_id');
        $requestedSeller = (int) $request->query('seller', 0);
        $selectedSellerId = $requestedSeller ?: ($lastSellerId ?: (int) ($sellers->first()?->id));
        $selectedSeller = $sellers->firstWhere('id', $selectedSellerId);
        $messages = collect();

        if ($selectedSeller) {
            $messages = $this->messagesForSeller($request, $selectedSeller);
            $this->markSellerRead($request, $selectedSeller);
        }

        $unreadCounts = $this->identity->apply(BuyerSellerMessage::query(), $request)
            ->where('sender_role', 'seller')->whereNull('read_at')
            ->selectRaw('seller_account_id, COUNT(*) AS total')
            ->groupBy('seller_account_id')->pluck('total', 'seller_account_id');

        $sellerReviewCount = 0;
        $sellerAverageRating = null;
        if ($selectedSeller) {
            $rating = ProductReview::query()
                ->where('seller_account_id', $selectedSeller->id)
                ->selectRaw('COUNT(*) AS review_count, AVG(rating) AS average_rating')
                ->first();
            $sellerReviewCount = (int) ($rating?->review_count ?? 0);
            $sellerAverageRating = $sellerReviewCount > 0
                ? round((float) $rating->average_rating, 1)
                : null;
        }

        return view('buyer.messages', compact(
            'sellers', 'selectedSeller', 'messages', 'unreadCounts',
            'sellerReviewCount', 'sellerAverageRating'
        ));
    }

    public function thread(Request $request, SellerAccount $seller): JsonResponse
    {
        $this->identity->guard($request);
        abort_unless($this->canContactSeller($request, $seller), 404);

        $messages = $this->messagesForSeller($request, $seller);
        $this->markSellerRead($request, $seller);

        return response()->json([
            'messages' => $messages->map(fn (BuyerSellerMessage $message) =>
                $this->messagePayload($message))->values(),
            'unread_total' => $this->unreadTotal($request),
        ])->header('Cache-Control', 'no-store');
    }

    public function unread(Request $request): JsonResponse
    {
        $this->identity->guard($request);

        return response()->json([
            'unread_total' => $this->unreadTotal($request),
        ])->header('Cache-Control', 'no-store');
    }

    public function buyerSend(Request $request, SellerAccount $seller): RedirectResponse|JsonResponse
    {
        $this->identity->guard($request);
        abort_unless($this->canContactSeller($request, $seller), 404);

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar,7z'],
            'order_id' => ['nullable', 'integer', 'exists:marketplace_orders,id'],
        ]);

        $body = trim((string) ($validated['body'] ?? ''));
        $attachment = $request->file('attachment');
        if ($body === '' && !$attachment) {
            return $this->validationError('body', 'Write a message or choose a file before sending.');
        }

        $orderId = null;
        if (!empty($validated['order_id'])) {
            $order = $this->identity->apply(MarketplaceOrder::query(), $request)
                ->whereKey((int) $validated['order_id'])
                ->where('seller_account_id', $seller->id)->first();
            abort_unless($order, 404);
            $orderId = $order->id;
        }

        $message = DB::transaction(function () use ($request, $seller, $orderId, $body, $attachment): BuyerSellerMessage {
            $storedAttachment = $attachment ? $this->storeAttachment($attachment) : null;
            $messageType = 'text';
            if ($storedAttachment) {
                $messageType = str_starts_with((string) $storedAttachment['attachment_mime_type'], 'image/') ? 'image' : 'file';
            }

            $message = BuyerSellerMessage::create(array_merge(
                $this->identity->columns($request), [
                    'seller_account_id' => $seller->id,
                    'marketplace_order_id' => $orderId,
                    'message_type' => $messageType,
                    'sender_role' => 'buyer',
                    'body' => $body,
                ], $storedAttachment ?? []
            ));

            $this->notifySeller($request, $seller, 'New buyer message', 'You have a new message from a buyer.');
            $this->chat->announce($message);
            return $message;
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->messagePayload($message)], 201);
        }

        return redirect()->route('buyer.messages', ['seller' => $seller->id])
            ->with('success', 'Message sent to seller.');
    }

    public function confirmAddress(Request $request, MarketplaceOrder $order): RedirectResponse|JsonResponse
    {
        $this->identity->guard($request);
        abort_unless($this->identity->apply(MarketplaceOrder::query(), $request)
            ->whereKey($order->id)->exists(), 404);

        $confirmed = DB::transaction(function () use ($request, $order): bool {
            $locked = $this->identity->apply(MarketplaceOrder::query(), $request)
                ->whereKey($order->id)->lockForUpdate()->firstOrFail();

            abort_if(in_array($locked->status, ['cancelled', 'delivered'], true), 409,
                'This order can no longer be confirmed.');

            if ($locked->delivery_address_confirmed_at) {
                return false;
            }

            $locked->forceFill(['delivery_address_confirmed_at' => now()])->save();

            $message = BuyerSellerMessage::create(array_merge(
                $this->identity->columns($request), [
                    'seller_account_id' => $locked->seller_account_id,
                    'marketplace_order_id' => $locked->id,
                    'message_type' => 'address_confirmed',
                    'sender_role' => 'buyer',
                    'body' => 'I confirm that my delivery address and phone number for order '
                        . $locked->order_number . ' are correct.',
                ]
            ));

            MarketplaceOrderEvent::create([
                'marketplace_order_id' => $locked->id,
                'seller_account_id' => $locked->seller_account_id,
                'audience' => 'both',
                'type' => 'address_confirmed',
                'title' => 'Buyer confirmed delivery details',
                'message' => 'Delivery address and contact number confirmed by the buyer.',
                'status' => $locked->status,
            ]);
            $this->notifySeller($request, $locked->seller,
                'Delivery details confirmed',
                'The buyer confirmed delivery details for order ' . $locked->order_number . '.');
            $this->chat->announce($message);
            return true;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'confirmed' => true,
                'already_confirmed' => !$confirmed,
                'message' => $confirmed ? 'Delivery details confirmed.' : 'Delivery details were already confirmed.',
            ]);
        }

        return redirect()->route('buyer.messages', ['seller' => $order->seller_account_id])
            ->with('success', 'Delivery details confirmed.');
    }

    private function messagesForSeller(Request $request, SellerAccount $seller)
    {
        return $this->identity->apply(BuyerSellerMessage::query(), $request)
            ->where('seller_account_id', $seller->id)
            ->with('order')->orderBy('id')->get();
    }

    private function markSellerRead(Request $request, SellerAccount $seller): void
    {
        $this->identity->apply(BuyerSellerMessage::query(), $request)
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'seller')->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function messagePayload(BuyerSellerMessage $message): array
    {
        $payload = $this->chat->payload($message);
        $payload['attachment'] = $this->attachmentPayload($message);

        return $payload;
    }

    private function attachmentPayload(BuyerSellerMessage $message): ?array
    {
        if (!filled($message->attachment_path ?? null)) {
            return null;
        }

        $disk = $message->attachment_disk ?: 'public';

        return [
            'url' => Storage::disk($disk)->url($message->attachment_path),
            'name' => $message->attachment_original_name ?: basename((string) $message->attachment_path),
            'mime_type' => $message->attachment_mime_type,
            'extension' => pathinfo((string) ($message->attachment_original_name ?: $message->attachment_path), PATHINFO_EXTENSION),
            'size' => (int) ($message->attachment_size ?? 0),
            'is_image' => str_starts_with((string) ($message->attachment_mime_type ?? ''), 'image/'),
        ];
    }

    private function storeAttachment(UploadedFile $file): array
    {
        $path = $file->store('buyer-seller-messages', 'public');

        return [
            'attachment_disk' => 'public',
            'attachment_path' => $path,
            'attachment_original_name' => $file->getClientOriginalName(),
            'attachment_mime_type' => $file->getMimeType(),
            'attachment_size' => (int) $file->getSize(),
        ];
    }

    private function unreadTotal(Request $request): int
    {
        return $this->identity->apply(BuyerSellerMessage::query(), $request)
            ->where('sender_role', 'seller')->whereNull('read_at')->count();
    }

    private function notifySeller(Request $request, SellerAccount $seller, string $title, string $body): void
    {
        SellerNotification::create([
            'seller_account_id' => $seller->id,
            'type' => 'buyer_message',
            'title' => $title,
            'message' => $body,
            'action_url' => route('seller.messages', ['buyer' => $this->identity->key($request)]),
        ]);
    }

    private function canContactSeller(Request $request, SellerAccount $seller): bool
    {
        if ($this->identity->apply(MarketplaceOrder::query(), $request)
            ->where('seller_account_id', $seller->id)->exists()) {
            return true;
        }
        if ($this->identity->apply(BuyerSellerMessage::query(), $request)
            ->where('seller_account_id', $seller->id)->exists()) {
            return true;
        }
        return SellerProduct::query()->buyerVisible()
            ->where('seller_account_id', $seller->id)->exists();
    }

    private function validationError(string $field, string $message): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages([$field => $message]);
    }
}
