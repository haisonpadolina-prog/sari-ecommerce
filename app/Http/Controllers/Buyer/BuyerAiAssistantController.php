<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Services\Buyer\BuyerAiAssistantService;
use App\Services\Buyer\BuyerIdentityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class BuyerAiAssistantController extends Controller
{
    public function message(Request $request, BuyerAiAssistantService $assistant, BuyerIdentityService $identity): JsonResponse
    {
        $identity->guard($request);

        // A deactivated local buyer must never use the assistant through a stale session.
        if ($buyerId = $identity->accountId($request)) {
            abort_unless(BuyerAccount::query()->whereKey($buyerId)->where('account_status', 'active')->exists(), 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'history' => ['nullable', 'array', 'max:8'],
            'history.*.role' => ['required_with:history', Rule::in(['user', 'assistant'])],
            'history.*.content' => ['required_with:history', 'string', 'max:1200'],
            'page_context' => ['nullable', 'array'],
            'page_context.path' => ['nullable', 'string', 'max:240'],
        ]);

        try {
            $response = $assistant->reply(
                $request,
                (string) $validated['message'],
                (array) ($validated['history'] ?? []),
                (array) ($validated['page_context'] ?? [])
            );

            return response()->json($response)->header('Cache-Control', 'private, no-store');
        } catch (Throwable $exception) {
            Log::error('SARI Buyer AI failed.', ['exception_type' => get_class($exception)]);
            return response()->json([
                'reply' => 'SARI AI cannot check your Buyer information right now. Your account and shopping pages still work—please try again shortly.',
                'products' => [],
                'provider' => 'safe_fallback',
                'degraded' => true,
            ])->header('Cache-Control', 'private, no-store');
        }
    }
}
