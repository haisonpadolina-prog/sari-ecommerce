<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\DeliveryProof;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileRiderProofController extends Controller
{
    public function store(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $lastMileAssignmentId = DB::table('last_mile_assignment_orders as link')
            ->join('last_mile_assignments as assignment', 'assignment.id', '=', 'link.last_mile_assignment_id')
            ->where('link.marketplace_order_id', $order->id)
            ->where('assignment.courier_account_id', $rider->id)
            ->value('assignment.id');

        $directOwned = strtolower(trim((string) $order->courier_email))
            === strtolower(trim((string) $rider->email));

        abort_unless($lastMileAssignmentId || $directOwned, 404);

        if (!in_array($order->status, ['arrived_buyer', 'delivered'], true)) {
            throw ValidationException::withMessages([
                'order' => 'Proof of delivery can only be attached after arrival at the Buyer.',
            ]);
        }

        $path = $request->file('photo')->store(
            'delivery-proofs/'.$order->id.'/'.$rider->id,
            'local'
        );

        $proof = DeliveryProof::query()->create([
            'marketplace_order_id' => $order->id,
            'courier_account_id' => $rider->id,
            'last_mile_assignment_id' => $lastMileAssignmentId ? (int) $lastMileAssignmentId : null,
            'proof_type' => 'delivered',
            'photo_path' => $path,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'captured_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proof of delivery saved.',
            'data' => [
                'id' => (int) $proof->id,
                'order_id' => (int) $order->id,
                'captured_at' => $proof->captured_at?->toIso8601String(),
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
