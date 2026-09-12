<?php

namespace App\Http\Controllers;

use App\Models\PlatformComplaint;
use App\Models\PlatformConversation;
use App\Services\PlatformMessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformMessagingController extends Controller
{
    public function __construct(
        private readonly PlatformMessagingService $messaging
    ) {
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'actor' => $this->messaging->me($request),
        ]);
    }

    public function connections(Request $request): JsonResponse
    {
        return response()->json(
            $this->messaging->connections($request)
        );
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'conversations' => $this->messaging
                ->inbox($request)
                ->values(),
        ]);
    }

    public function openDirect(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_role' => [
                'required',
                'string',
                Rule::in([
                    'admin',
                    'buyer',
                    'social_buyer',
                    'seller',
                    'rider',
                    'courier',
                    'logistics',
                ]),
            ],
            'target_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $conversation = $this->messaging->openDirect(
            $request,
            $validated['target_role'],
            (int) $validated['target_id']
        );

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'uuid' => $conversation->uuid,
                'type' => $conversation->conversation_type,
                'subject' => $conversation->subject,
                'status' => $conversation->status,
            ],
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    public function openReportConversation(
        Request $request,
        PlatformComplaint $complaint
    ): JsonResponse {
        $conversation = $this->messaging
            ->openReportConversation(
                $request,
                $complaint
            );

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'uuid' => $conversation->uuid,
                'type' => $conversation->conversation_type,
                'subject' => $conversation->subject,
                'status' => $conversation->status,
                'context' => [
                    'type' => $conversation->context_type,
                    'id' => $conversation->context_id,
                ],
            ],
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    public function show(
        Request $request,
        PlatformConversation $conversation
    ): JsonResponse {
        return response()->json(
            $this->messaging->detail(
                $request,
                $conversation
            )
        );
    }

    public function send(
        Request $request,
        PlatformConversation $conversation
    ): JsonResponse {
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        // Sender identity comes only from the signed-in session.
        // Client payload cannot impersonate another SARI account.
        $message = $this->messaging->send(
            $request,
            $conversation,
            $validated['body']
        );

        return response()->json([
            'message' =>
                $this->messaging->messagePayload($message),
        ], 201);
    }

    public function markRead(
        Request $request,
        PlatformConversation $conversation
    ): JsonResponse {
        return response()->json([
            'last_read_message_id' =>
                $this->messaging->markRead(
                    $request,
                    $conversation
                ),
        ]);
    }
}
