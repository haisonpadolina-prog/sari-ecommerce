<?php

namespace App\Http\Controllers;

use App\Models\BuyerAccount;
use App\Models\BuyerSellerMessage;
use App\Models\MarketplaceOrder;
use App\Models\SellerAccount;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
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

    public function index(Request $request): View
    {
        $seller = $this->seller($request);
        $conversations = $this->conversationList($seller);
        $selectedKey = (string) $request->query('buyer', $conversations->first()['key'] ?? '');
        $selected = $conversations->firstWhere('key', $selectedKey);
        $messages = collect();

        if ($selected) {
            $query = BuyerSellerMessage::query()
                ->where('seller_account_id', $seller->id)
                ->orderBy('id');

            $this->applyBuyerKey($query, $selectedKey);
            $messages = $query->get();

            $readQuery = BuyerSellerMessage::query()
                ->where('seller_account_id', $seller->id)
                ->where('sender_role', 'buyer')
                ->whereNull('read_at');

            $this->applyBuyerKey($readQuery, $selectedKey);
            $readQuery->update(['read_at' => now()]);
        }

        return view('seller.buyer-messages', compact('seller', 'conversations', 'selected', 'messages'));
    }

    public function send(Request $request, string $buyerKey): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        [$type, $id] = $this->parseBuyerKey($buyerKey);

        BuyerSellerMessage::create([
            'seller_account_id' => $seller->id,
            'buyer_account_id' => $type === 'account' ? $id : null,
            'buyer_social_account_id' => $type === 'social' ? $id : null,
            'sender_role' => 'seller',
            'body' => trim($validated['body']),
        ]);

        return redirect()
            ->route('seller.buyer-messages', ['buyer' => $buyerKey])
            ->with('success', 'Reply sent to buyer.');
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
