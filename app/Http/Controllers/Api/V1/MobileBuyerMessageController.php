<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MobileBuyerMessageController extends Controller
{
    public function __construct(
        private readonly BuyerSellerOrderConfirmationService $chat,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);

        $sellerIds = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->pluck('seller_account_id')
            ->merge(BuyerSellerMessage::query()
                ->where('buyer_account_id', $buyer->id)
                ->pluck('seller_account_id'))
            ->filter()
            ->unique()
            ->values();

        $sellers = SellerAccount::query()
            ->whereIn('id', $sellerIds)
            ->orderBy('store_name')
            ->get()
            ->map(function (SellerAccount $seller) use ($buyer): array {
                $last = BuyerSellerMessage::query()
                    ->where('buyer_account_id', $buyer->id)
                    ->where('seller_account_id', $seller->id)
                    ->latest('id')
                    ->first();

                return [
                    'seller_id' => (int) $seller->id,
                    'store_name' => $seller->store_name,
                    'last_message' => $last?->body,
                    'last_message_at' => $last?->created_at?->toIso8601String(),
                    'unread_count' => BuyerSellerMessage::query()
                        ->where('buyer_account_id', $buyer->id)
                        ->where('seller_account_id', $seller->id)
                        ->where('sender_role', 'seller')
                        ->whereNull('read_at')
                        ->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sellers->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function thread(Request $request, SellerAccount $seller): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardSeller($buyer, $seller);

        BuyerSellerMessage::query()
            ->where('buyer_account_id', $buyer->id)
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'seller')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = BuyerSellerMessage::query()
            ->where('buyer_account_id', $buyer->id)
            ->where('seller_account_id', $seller->id)
            ->with('order:id,order_number')
            ->oldest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'seller' => [
                    'id' => (int) $seller->id,
                    'store_name' => $seller->store_name,
                ],
                'messages' => $messages->map(fn (BuyerSellerMessage $message) => $this->payload($message))->values(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function send(Request $request, SellerAccount $seller): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardSeller($buyer, $seller);

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
            'order_id' => ['nullable', 'integer', 'exists:marketplace_orders,id'],
        ]);

        $body = trim((string) ($validated['body'] ?? ''));
        $attachment = $request->file('attachment');

        if ($body === '' && !$attachment) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or choose a file before sending.',
            ]);
        }

        $orderId = null;
        if (!empty($validated['order_id'])) {
            $order = MarketplaceOrder::query()
                ->whereKey((int) $validated['order_id'])
                ->where('buyer_account_id', $buyer->id)
                ->where('seller_account_id', $seller->id)
                ->firstOrFail();
            $orderId = $order->id;
        }

        $attachmentData = [];
        if ($attachment instanceof UploadedFile) {
            $path = $attachment->store('buyer-seller-messages', 'public');
            $attachmentData = [
                'attachment_disk' => 'public',
                'attachment_path' => $path,
                'attachment_original_name' => $attachment->getClientOriginalName(),
                'attachment_mime_type' => $attachment->getMimeType(),
                'attachment_size' => (int) $attachment->getSize(),
            ];
        }

        $message = BuyerSellerMessage::create(array_merge([
            'seller_account_id' => $seller->id,
            'buyer_account_id' => $buyer->id,
            'buyer_social_account_id' => null,
            'marketplace_order_id' => $orderId,
            'message_type' => $attachment
                ? (str_starts_with((string) $attachment->getMimeType(), 'image/') ? 'image' : 'file')
                : 'text',
            'sender_role' => 'buyer',
            'body' => $body,
        ], $attachmentData));

        $this->chat->announce($message);
        $message->loadMissing('order:id,order_number');

        return response()->json([
            'success' => true,
            'message' => 'Message sent.',
            'data' => $this->payload($message),
        ], 201);
    }

    private function guardSeller(BuyerAccount $buyer, SellerAccount $seller): void
    {
        $allowed = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->where('seller_account_id', $seller->id)
            ->exists()
            || BuyerSellerMessage::query()
                ->where('buyer_account_id', $buyer->id)
                ->where('seller_account_id', $seller->id)
                ->exists()
            || SellerProduct::query()
                ->buyerVisible()
                ->where('seller_account_id', $seller->id)
                ->exists();

        abort_unless($allowed, 404);
    }

    private function payload(BuyerSellerMessage $message): array
    {
        return [
            'id' => (int) $message->id,
            'seller_id' => (int) $message->seller_account_id,
            'order_id' => $message->marketplace_order_id ? (int) $message->marketplace_order_id : null,
            'order_number' => $message->order?->order_number,
            'sender_role' => $message->sender_role,
            'message_type' => $message->message_type,
            'body' => $message->body,
            'read_at' => $message->read_at?->toIso8601String(),
            'created_at' => $message->created_at?->toIso8601String(),
            'attachment' => $message->attachment_path ? [
                'url' => Storage::disk($message->attachment_disk ?: 'public')->url($message->attachment_path),
                'name' => $message->attachment_original_name,
                'mime_type' => $message->attachment_mime_type,
                'size' => (int) ($message->attachment_size ?? 0),
            ] : null,
        ];
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }
}
