<?php

namespace App\Http\Controllers;

use App\Models\PlatformComplaint;
use App\Models\PlatformConversation;
use App\Models\PlatformMessage;
use App\Services\PlatformMessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function openSupport(Request $request): JsonResponse
    {
        $conversation = $this->messaging
            ->openAdminSupportConversation($request);

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
                'nullable',
                'string',
                'max:3000',
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:15360',
                'mimes:jpg,jpeg,png,webp,gif,pdf,txt,csv,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mov,mp3,m4a,wav',
            ],
        ]);

        if (
            blank($validated['body'] ?? null)
            && !$request->hasFile('attachment')
        ) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or attach a file.',
            ]);
        }

        // Sender identity comes only from the signed-in session.
        // Client payload cannot impersonate another SARI account.
        $message = $this->messaging->send(
            $request,
            $conversation,
            $validated['body'] ?? null,
            $request->file('attachment')
        );

        return response()->json([
            'message' =>
                $this->messaging->messagePayloadForRequest(
                    $request,
                    $message
                ),
        ], 201);
    }

    public function attachment(
        Request $request,
        PlatformMessage $message
    ): StreamedResponse {
        $this->messaging->assertCanAccessMessage(
            $request,
            $message
        );

        abort_if(
            !$message->attachment_path
            || !Storage::disk('local')->exists($message->attachment_path),
            404,
            'Attachment not found.'
        );

        $name = $message->attachment_name ?: 'attachment';
        $headers = [
            'Content-Type' => $message->attachment_mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=300',
        ];

        if ($request->boolean('download')) {
            return Storage::disk('local')->download(
                $message->attachment_path,
                $name,
                $headers
            );
        }

        return Storage::disk('local')->response(
            $message->attachment_path,
            $name,
            $headers,
            'inline'
        );
    }

    public function react(
        Request $request,
        PlatformMessage $message
    ): JsonResponse {
        $validated = $request->validate([
            'emoji' => [
                'required',
                'string',
                Rule::in(PlatformMessagingService::REACTION_EMOJIS),
            ],
        ]);

        return response()->json(
            $this->messaging->toggleReaction(
                $request,
                $message,
                $validated['emoji']
            )
        );
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
