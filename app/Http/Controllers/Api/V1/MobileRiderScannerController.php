<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Support\ParcelQrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileRiderScannerController extends Controller
{
    public function __construct(
        private readonly ParcelQrCode $parcelQrCode,
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:1000', 'required_without:payload'],
            'payload' => ['nullable', 'string', 'max:1000', 'required_without:code'],
        ]);

        $payload = (string) ($validated['payload'] ?? $validated['code']);
        $order = $this->parcelQrCode->resolve($payload);
        $parcel = LogisticsParcel::query()
            ->where('marketplace_order_id', $order->id)
            ->first();

        $lastMile = DB::table('last_mile_assignment_orders as link')
            ->join('last_mile_assignments as assignment', 'assignment.id', '=', 'link.last_mile_assignment_id')
            ->where('link.marketplace_order_id', $order->id)
            ->where('assignment.courier_account_id', $rider->id)
            ->select(['assignment.id', 'assignment.status'])
            ->first();

        $pickupOwned = strtolower(trim((string) $order->courier_email))
            === strtolower(trim((string) $rider->email));

        if (!$lastMile && !$pickupOwned) {
            throw ValidationException::withMessages([
                'code' => 'This parcel is not assigned to your Rider account.',
            ]);
        }

        if ($lastMile) {
            $nextAction = match ((string) ($parcel?->status ?? '')) {
                'last_mile_assigned' => 'start_last_mile',
                'out_for_delivery' => 'arrived_buyer',
                'arrived_buyer' => 'deliver',
                default => null,
            };
        } else {
            $nextAction = match ($order->status) {
                'courier_accepted' => 'proceed_pickup',
                'heading_pickup' => 'arrived_pickup',
                'arrived_pickup' => 'confirm_pickup',
                'in_transit' => $parcel ? null : 'arrived_buyer',
                'arrived_buyer' => $parcel ? null : 'complete',
                default => null,
            };
        }

        return response()->json([
            'success' => true,
            'message' => 'Parcel verified for this Rider.',
            'data' => [
                'valid' => true,
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'parcel_status' => $parcel?->status,
                'assignment_type' => $lastMile ? 'last_mile' : ($parcel ? 'logistics_pickup' : 'direct'),
                'last_mile_assignment_id' => $lastMile ? (int) $lastMile->id : null,
                'next_action' => $nextAction,
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
