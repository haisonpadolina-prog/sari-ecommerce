<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Models\Accounts\BuyerAccount;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SellerBuyerMessageController extends Controller
{
    private function seller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403);

        return SellerAccount::findOrFail($request->session()->get('seller_account_id'));
    }

    public function index(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);

        $buyerKey = trim(
            (string) $request->query('buyer', '')
        );

        if ($buyerKey !== '') {
            abort_unless(
                $this->buyerIsAccessibleToSeller(
                    $seller,
                    $buyerKey
                ),
                404
            );
        }

        return redirect()->route(
            'seller.messages',
            $buyerKey !== ''
                ? ['buyer' => $buyerKey]
                : []
        );
    }

    public function thread(
        Request $request,
        string $buyerKey
    ): JsonResponse {
        $seller = $this->seller($request);

        abort_unless(
            $this->buyerIsAccessibleToSeller(
                $seller,
                $buyerKey
            ),
            404
        );

        $query = BuyerSellerMessage::query()
            ->where(
                'seller_account_id',
                $seller->id
            )
            ->orderBy('id');

        $this->applyBuyerKey(
            $query,
            $buyerKey
        );

        $messages = $query
            ->get()
            ->map(fn (BuyerSellerMessage $message) => [
                'id' => (int) $message->id,
                'sender_role' =>
                    (string) $message->sender_role,
                'body' => (string) $message->body,
                'created_at' =>
                    $message->created_at?->toIso8601String(),
            ])
            ->values();

        $readQuery = BuyerSellerMessage::query()
            ->where(
                'seller_account_id',
                $seller->id
            )
            ->where('sender_role', 'buyer')
            ->whereNull('read_at');

        $this->applyBuyerKey(
            $readQuery,
            $buyerKey
        );

        $readQuery->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function send(
        Request $request,
        string $buyerKey
    ): RedirectResponse|JsonResponse {
        $seller = $this->seller($request);

        abort_unless(
            $this->buyerIsAccessibleToSeller(
                $seller,
                $buyerKey
            ),
            404
        );

        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        [$type, $id] =
            $this->parseBuyerKey($buyerKey);

        $message = BuyerSellerMessage::create([
            'seller_account_id' => $seller->id,
            'buyer_account_id' =>
                $type === 'account'
                    ? $id
                    : null,
            'buyer_social_account_id' =>
                $type === 'social'
                    ? $id
                    : null,
            'sender_role' => 'seller',
            'body' => trim($validated['body']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => [
                    'id' => (int) $message->id,
                    'sender_role' =>
                        (string) $message->sender_role,
                    'body' => (string) $message->body,
                    'created_at' =>
                        $message->created_at?->toIso8601String(),
                ],
            ], 201);
        }

        return redirect()
            ->route(
                'seller.messages',
                ['buyer' => $buyerKey]
            )
            ->with(
                'success',
                'Message sent to buyer.'
            );
    }

    private function conversationList(SellerAccount $seller): Collection
    {
        $keys = collect();

        MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->get(['buyer_account_id', 'buyer_social_account_id'])
            ->each(function ($row) use ($keys) {
                if ($row->buyer_account_id) {
                    $keys->push('account-' . $row->buyer_account_id);
                } elseif ($row->buyer_social_account_id) {
                    $keys->push('social-' . $row->buyer_social_account_id);
                }
            });

        BuyerSellerMessage::query()
            ->where('seller_account_id', $seller->id)
            ->get(['buyer_account_id', 'buyer_social_account_id'])
            ->each(function ($row) use ($keys) {
                if ($row->buyer_account_id) {
                    $keys->push('account-' . $row->buyer_account_id);
                } elseif ($row->buyer_social_account_id) {
                    $keys->push('social-' . $row->buyer_social_account_id);
                }
            });

        return $keys->filter()->unique()->map(function (string $key) use ($seller) {
            [$type, $id] = $this->parseBuyerKey($key);

            if ($type === 'account') {
                $buyer = BuyerAccount::query()->find($id);
                $name = $buyer ? trim($buyer->first_name . ' ' . $buyer->last_name) : 'Buyer #' . $id;
                $email = $buyer?->email;
            } else {
                $buyer = SocialAccount::query()->find($id);
                $name = $buyer?->name ?: 'Social Buyer #' . $id;
                $email = $buyer?->email;
            }

            $unreadQuery = BuyerSellerMessage::query()
                ->where('seller_account_id', $seller->id)
                ->where('sender_role', 'buyer')
                ->whereNull('read_at');
            $this->applyBuyerKey($unreadQuery, $key);

            return [
                'key' => $key,
                'name' => $name,
                'email' => $email,
                'unread' => $unreadQuery->count(),
            ];
        })->values();
    }

    private function buyerIsAccessibleToSeller(
        SellerAccount $seller,
        string $buyerKey
    ): bool {
        [$type, $id] =
            $this->parseBuyerKey($buyerKey);

        $buyerColumn =
            $type === 'account'
                ? 'buyer_account_id'
                : 'buyer_social_account_id';

        if (
            MarketplaceOrder::query()
                ->where(
                    'seller_account_id',
                    $seller->id
                )
                ->where(
                    $buyerColumn,
                    $id
                )
                ->exists()
        ) {
            return true;
        }

        return BuyerSellerMessage::query()
            ->where(
                'seller_account_id',
                $seller->id
            )
            ->where(
                $buyerColumn,
                $id
            )
            ->exists();
    }

    private function parseBuyerKey(string $key): array
    {
        abort_unless(preg_match('/^(account|social)-(\d+)$/', $key, $m), 404);

        return [$m[1], (int) $m[2]];
    }

    private function applyBuyerKey($query, string $key): void
    {
        [$type, $id] = $this->parseBuyerKey($key);

        $query->where($type === 'account' ? 'buyer_account_id' : 'buyer_social_account_id', $id);
    }
}
