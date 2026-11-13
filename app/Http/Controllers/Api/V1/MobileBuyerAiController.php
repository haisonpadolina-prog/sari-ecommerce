<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerAiAssistantService;
use App\Services\Mobile\MobileBuyerSessionBridge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

final class MobileBuyerAiController extends Controller
{
    public function message(Request $request, MobileBuyerSessionBridge $bridge, BuyerAiAssistantService $ai): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required','string','max:1200'],
            'history' => ['nullable','array','max:8'],
            'history.*.role' => ['required_with:history',Rule::in(['user','assistant'])],
            'history.*.content' => ['required_with:history','string','max:1200'],
            'page_context' => ['nullable','array'],
            'page_context.path' => ['nullable','string','max:240'],
        ]);
        $bridge->attach($request);
        try {
            $reply = $ai->reply($request, $validated['message'], $validated['history'] ?? [], $validated['page_context'] ?? []);
            return response()->json(['success'=>true,'data'=>$reply])->header('Cache-Control','private, no-store');
        } catch (Throwable $e) {
            Log::warning('Mobile Buyer AI request failed', ['exception'=>get_class($e)]);
            return response()->json(['success'=>false,'message'=>'SARI AI is temporarily unavailable.'],503)
                ->header('Cache-Control','private, no-store');
        }
    }
}
