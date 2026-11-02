<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Messaging\LogisticsMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MobileRiderMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $logisticsId = (int) ($rider->logistics_account_id ?? 0);

        if ($logisticsId <= 0) {
            return response()->json([
                'success' => true,
                'data' => ['messages' => [], 'unread_count' => 0],
            ]);
        }

        LogisticsMessage::query()
            ->where('courier_account_id', $rider->id)
            ->where('logistics_account_id', $logisticsId)
            ->where('sender_role', 'logistics')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = LogisticsMessage::query()
            ->where('courier_account_id', $rider->id)
            ->where('logistics_account_id', $logisticsId)
            ->oldest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->map(fn (LogisticsMessage $message) => [
                    'id' => (int) $message->id,
                    'sender_role' => $message->sender_role,
                    'body' => $message->body,
                    'read_at' => $message->read_at?->toIso8601String(),
                    'created_at' => $message->created_at?->toIso8601String(),
                ])->values(),
                'unread_count' => 0,
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function send(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $logisticsId = (int) ($rider->logistics_account_id ?? 0);

        if ($logisticsId <= 0) {
            throw ValidationException::withMessages([
                'message' => 'This Rider is not currently connected to a Logistics provider.',
            ]);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $message = LogisticsMessage::query()->create([
            'courier_account_id' => $rider->id,
            'logistics_account_id' => $logisticsId,
            'sender_role' => 'rider',
            'body' => $validated['body'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent to Logistics.',
            'data' => [
                'id' => (int) $message->id,
                'sender_role' => $message->sender_role,
                'body' => $message->body,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
