<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Services\Seller\SellerAiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class SellerAiAssistantController extends Controller
{
    public function message(
        Request $request,
        SellerAiAssistantService $assistant
    ): JsonResponse {
        $seller = $request->attributes->get('sellerAccount');

        if (!$seller instanceof SellerAccount) {
            abort_unless($request->session()->get('is_seller'), 403);

            $seller = SellerAccount::query()->findOrFail(
                (int) $request->session()->get('seller_account_id')
            );
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => [
                'required_with:history',
                'string',
                Rule::in(['user', 'assistant']),
            ],
            'history.*.content' => [
                'required_with:history',
                'string',
                'max:1200',
            ],
            'page_context' => ['nullable', 'array'],
            'page_context.title' => ['nullable', 'string', 'max:120'],
            'page_context.path' => ['nullable', 'string', 'max:240'],
            'page_context.key' => ['nullable', 'string', 'max:80'],
        ]);

        $question = trim((string) $validated['message']);
        $pageContext = (array) ($validated['page_context'] ?? []);

        try {
            $result = $assistant->reply(
                $seller,
                $question,
                (array) ($validated['history'] ?? []),
                $pageContext
            );

            return response()->json([
                'reply' => (string) ($result['reply'] ?? ''),
                'provider' => (string) ($result['provider'] ?? 'local'),
            ]);
        } catch (Throwable $e) {
            /*
            | Final HTTP boundary:
            | A context query, provider outage, or unexpected service exception
            | must never surface Laravel's generic "Server Error" to the Seller.
            */
            Log::error('SARI Seller AI request failed at controller boundary.', [
                'seller_account_id' => $seller->id,
                'page_key' => $pageContext['key'] ?? null,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'reply' => $this->emergencyReply(
                    $question,
                    $pageContext
                ),
                'provider' => 'safe_fallback',
                'degraded' => true,
            ], 200);
        }
    }

    private function emergencyReply(
        string $question,
        array $pageContext
    ): string {
        $q = Str::lower($question);
        $pageKey = (string) ($pageContext['key'] ?? '');

        if (
            Str::contains($q, [
                'scam',
                'scammed',
                'fraud',
                'fraudulent',
                'phishing',
                'stolen',
                'unauthorized',
            ])
        ) {
            return 'If you suspect a scam or fraudulent activity, avoid sending additional payments, passwords, OTPs, or other sensitive credentials. Keep the relevant order details, screenshots, and transaction information, then contact SARI Admin Support through Chat / Messaging so a human administrator can review the case. If you are already on Chat / Messaging, send the supporting details in the Admin Support conversation and wait for the administrator’s response.';
        }

        if (
            $pageKey === 'messaging'
            && Str::contains($q, ['chat', 'admin', 'support'])
        ) {
            return 'You are already on Chat / Messaging. Use the SARI Admin Support conversation for account actions, disputes, suspected scams, enforcement concerns, or cases that require a human administrator. I can still help explain your Seller workspace and store data here.';
        }

        return 'SARI AI encountered a temporary service issue while processing that request. Your Seller workspace is still available. Please try the question again; for account actions, disputes, suspected scams, or enforcement concerns, use Chat / Messaging to contact SARI Admin Support.';
    }
}
