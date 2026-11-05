<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\ParcelQrScanRecorder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\ParcelQrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileRiderScannerController extends Controller
{
    public function __construct(
        private readonly ParcelQrCode $parcelQrCode,
        private readonly ParcelQrScanRecorder $scanRecorder,
        private readonly MarketplaceOrderWorkflowService $workflow,
    ) {}

    /**
     * Verify an assigned SARI parcel and record the same database checkpoints
     * used by the Rider web workflow. The mobile app never depends on a browser
     * session: Sanctum identifies the Rider and all progress is stored in DB.
     */
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

        if (! $lastMile && ! $pickupOwned) {
            throw ValidationException::withMessages([
                'code' => 'This parcel is not assigned to your Rider account.',
            ]);
        }

        $message = 'Parcel verified for this Rider.';
        $checkpoint = null;
        $actionCompleted = false;

        if ($lastMile) {
            [$message, $checkpoint, $actionCompleted] = $this->handleLastMile(
                $order,
                $parcel,
                $rider,
                $lastMile,
            );
        } elseif ($parcel) {
            [$message, $checkpoint, $actionCompleted] = $this->handleLogisticsPickup(
                $order,
                $parcel,
                $rider,
            );
        } else {
            [$message, $checkpoint] = $this->handleDirectDelivery(
                $order,
                $rider,
            );
        }

        $order->refresh();
        $parcel?->refresh();

        $nextAction = $lastMile
            ? match ((string) ($parcel?->status ?? '')) {
                'last_mile_assigned' => 'start_last_mile',
                'out_for_delivery' => 'arrived_buyer',
                'arrived_buyer' => 'deliver',
                default => null,
            }
            : match ($order->status) {
                'courier_accepted' => 'proceed_pickup',
                'heading_pickup' => 'arrived_pickup',
                'arrived_pickup' => $parcel ? 'scan_pickup' : 'confirm_pickup',
                'in_transit' => $parcel ? null : 'arrived_buyer',
                'arrived_buyer' => $parcel ? null : 'complete',
                default => null,
            };

        $cod = null;
        if ($checkpoint === 'mobile_final_delivery_verified' && $this->isCod($order)) {
            $cod = $this->prepareCodCollection(
                $order,
                $rider,
                $lastMile ? (int) $lastMile->id : null,
            );
            $message = 'Final parcel QR verified. Capture proof of delivery, collect the exact COD cash, then confirm cash receipt in the app.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'valid' => true,
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'parcel_status' => $parcel?->status,
                'assignment_type' => $lastMile ? 'last_mile' : ($parcel ? 'logistics_pickup' : 'direct'),
                'last_mile_assignment_id' => $lastMile ? (int) $lastMile->id : null,
                'checkpoint' => $checkpoint,
                'action_completed' => $actionCompleted,
                'next_action' => $nextAction,
                'requires_payment' => (bool) ($cod['requires_payment'] ?? false),
                'payment_method' => $cod['payment_method'] ?? $order->payment_method,
                'amount_due' => $cod['amount_due'] ?? null,
                'currency' => $cod['currency'] ?? null,
                'payment_token' => $cod['payment_token'] ?? null,
            ],
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * Confirm physical COD cash receipt after a recent Rider QR verification.
     * This is the Sanctum/mobile equivalent of the web scanner's session-bound
     * COD confirmation. The short-lived token is bound to Rider + order +
     * last-mile context, and the delivery proof must already exist.
     */
    public function confirmCod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required', 'string', 'max:128'],
            'cash_received' => ['required', 'accepted'],
        ]);

        $rider = $this->rider($request);
        $order = MarketplaceOrder::query()->findOrFail((int) $validated['order_id']);
        $cacheKey = $this->codCacheKey((int) $rider->id, (int) $order->id);
        $pending = Cache::get($cacheKey);

        if (
            ! is_array($pending)
            || (int) ($pending['order_id'] ?? 0) !== (int) $order->id
            || (int) ($pending['rider_id'] ?? 0) !== (int) $rider->id
            || (int) ($pending['verified_at'] ?? 0) < now()->subMinutes(10)->timestamp
            || ! is_string($pending['token_hash'] ?? null)
            || ! hash_equals(
                $pending['token_hash'],
                hash('sha256', (string) $validated['payment_token'])
            )
        ) {
            throw ValidationException::withMessages([
                'payment_token' => 'COD confirmation requires a recent verified parcel QR scan by this Rider. Scan the parcel again.',
            ]);
        }

        if (! $this->isCod($order) || $order->status !== 'arrived_buyer') {
            throw ValidationException::withMessages([
                'order_id' => 'This COD order is no longer awaiting final Buyer handoff.',
            ]);
        }

        $assignmentId = isset($pending['last_mile_assignment_id'])
            ? (int) $pending['last_mile_assignment_id']
            : null;
        $contextType = $assignmentId !== null ? 'last_mile_assignment' : 'order';
        $contextId = $assignmentId ?? (int) $order->id;

        if ($assignmentId !== null) {
            $owned = DB::table('last_mile_assignments')
                ->where('id', $assignmentId)
                ->where('courier_account_id', (int) $rider->id)
                ->where('status', 'out_for_delivery')
                ->exists();
            $linked = DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', $assignmentId)
                ->where('marketplace_order_id', (int) $order->id)
                ->exists();
            $parcelReady = LogisticsParcel::query()
                ->where('marketplace_order_id', $order->id)
                ->where('status', 'arrived_buyer')
                ->exists();
            $proof = DB::table('delivery_proofs')
                ->where('marketplace_order_id', $order->id)
                ->where('courier_account_id', (int) $rider->id)
                ->where('last_mile_assignment_id', $assignmentId)
                ->exists();

            if (! $owned || ! $linked || ! $parcelReady || ! $proof) {
                throw ValidationException::withMessages([
                    'order_id' => 'The last-mile assignment changed or proof of delivery is missing. Refresh the job and try again.',
                ]);
            }
        } else {
            $pickupOwned = strtolower(trim((string) $order->courier_email))
                === strtolower(trim((string) $rider->email));
            $proof = DB::table('delivery_proofs')
                ->where('marketplace_order_id', $order->id)
                ->where('courier_account_id', (int) $rider->id)
                ->exists();

            if (
                ! $pickupOwned
                || LogisticsParcel::query()->where('marketplace_order_id', $order->id)->exists()
                || ! $proof
            ) {
                throw ValidationException::withMessages([
                    'order_id' => 'This direct COD delivery is not ready for cash confirmation by your Rider account.',
                ]);
            }
        }

        $verified = DB::table('parcel_qr_scan_events')
            ->where('marketplace_order_id', $order->id)
            ->where('checkpoint', 'mobile_final_delivery_verified')
            ->where('actor_role', 'rider')
            ->where('actor_id', (int) $rider->id)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->exists();

        if (! $verified) {
            throw ValidationException::withMessages([
                'payment_token' => 'The final parcel QR verification is missing. Scan the parcel again before confirming COD cash.',
            ]);
        }

        $this->scanRecorder->record(
            $order,
            'mobile-cod-cash-confirmed:'.$contextType.':'.$contextId.':order:'.$order->id,
            'mobile_cod_cash_confirmed',
            'rider',
            (int) $rider->id,
            null,
            $contextType,
            $contextId,
        );

        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'COD cash receipt confirmed. You can now complete the delivery.',
            'data' => [
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'amount_received' => number_format((float) $order->total, 2, '.', ''),
                'currency' => 'PHP',
                'checkpoint' => 'mobile_cod_cash_confirmed',
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function handleLogisticsPickup(
        MarketplaceOrder $order,
        LogisticsParcel $parcel,
        CourierAccount $rider,
    ): array {
        if (! in_array($order->status, ['arrived_pickup', 'in_transit'], true)) {
            throw ValidationException::withMessages([
                'payload' => 'Start the pickup trip and confirm arrival at the Seller before scanning this parcel.',
            ]);
        }

        $advanced = false;
        if ($order->status === 'arrived_pickup') {
            $order = $this->workflow->transition(
                $order,
                'arrived_pickup',
                'in_transit',
                'Order Picked Up',
                'Order '.$order->order_number.' was QR-verified and physically picked up by '.($order->courier_name ?: 'the Rider').'. It is now moving to the assigned origin Logistics hub.',
                'both',
                ['picked_up_at' => now()]
            );
            $advanced = true;
        }

        if ($parcel->status !== 'awaiting_intake') {
            throw ValidationException::withMessages([
                'payload' => 'This Logistics parcel is no longer awaiting Rider pickup.',
            ]);
        }

        $this->scanRecorder->record(
            $order,
            'pickup-rider:order:'.$order->id,
            'pickup_rider_handoff',
            'rider',
            (int) $rider->id,
            null,
            'order',
            (int) $order->id,
        );

        return [
            $advanced
                ? 'QR verified. Seller handoff is complete and the parcel is now in transit to its origin hub.'
                : 'QR verified. Seller pickup checkpoint is recorded.',
            'pickup_rider_handoff',
            $advanced,
        ];
    }

    private function handleDirectDelivery(
        MarketplaceOrder $order,
        CourierAccount $rider,
    ): array {
        if ($order->status !== 'arrived_buyer') {
            return ['Parcel verified for this direct-delivery assignment.', null];
        }

        $this->scanRecorder->record(
            $order,
            'mobile-final-verified:direct:order:'.$order->id,
            'mobile_final_delivery_verified',
            'rider',
            (int) $rider->id,
            null,
            'order',
            (int) $order->id,
        );

        return [
            'Final parcel QR verified. Capture proof of delivery, then complete the handoff.',
            'mobile_final_delivery_verified',
        ];
    }

    private function handleLastMile(
        MarketplaceOrder $order,
        ?LogisticsParcel $parcel,
        CourierAccount $rider,
        object $assignment,
    ): array {
        if (! $parcel) {
            throw ValidationException::withMessages([
                'payload' => 'The assigned last-mile parcel record is missing.',
            ]);
        }

        if ($assignment->status === 'assigned') {
            $ready = DB::table('parcel_qr_scan_events')
                ->where('marketplace_order_id', $order->id)
                ->where('checkpoint', 'destination_ready_for_delivery')
                ->where('context_type', 'last_mile_assignment')
                ->where('context_id', (int) $assignment->id)
                ->exists();

            if (! $ready) {
                throw ValidationException::withMessages([
                    'payload' => 'Logistics must first scan this parcel at the destination hub as Ready for Delivery.',
                ]);
            }

            $this->scanRecorder->record(
                $order,
                'last-mile:'.$assignment->id.':order:'.$order->id,
                'last_mile_rider_handoff',
                'rider',
                (int) $rider->id,
                null,
                'last_mile_assignment',
                (int) $assignment->id,
            );

            $total = DB::table('last_mile_assignment_orders')
                ->where('last_mile_assignment_id', (int) $assignment->id)
                ->count();
            $scanned = $this->scanRecorder->countForContext(
                'last_mile_assignment',
                (int) $assignment->id,
                'last_mile_rider_handoff',
            );

            return [
                $scanned >= $total && $total > 0
                    ? 'All parcels in this batch are QR-verified. You can now start the last-mile route.'
                    : 'Rider handoff recorded. Scan the remaining parcels before starting the route ('.$scanned.'/'.$total.').',
                'last_mile_rider_handoff',
                false,
            ];
        }

        if ($assignment->status === 'out_for_delivery') {
            if ($parcel->status !== 'arrived_buyer' || $order->status !== 'arrived_buyer') {
                throw ValidationException::withMessages([
                    'payload' => 'Confirm Arrived at Buyer before scanning the final parcel QR.',
                ]);
            }

            $this->scanRecorder->record(
                $order,
                'mobile-final-verified:last-mile:'.$assignment->id.':order:'.$order->id,
                'mobile_final_delivery_verified',
                'rider',
                (int) $rider->id,
                null,
                'last_mile_assignment',
                (int) $assignment->id,
            );

            return [
                'Final parcel QR verified. Capture proof of delivery, then complete the handoff.',
                'mobile_final_delivery_verified',
                false,
            ];
        }

        if ($assignment->status === 'completed' || $parcel->status === 'delivered') {
            return ['This parcel is already delivered.', 'final_delivery_scan', false];
        }

        throw ValidationException::withMessages([
            'payload' => 'This last-mile parcel is not ready for a Rider QR checkpoint.',
        ]);
    }

    private function prepareCodCollection(
        MarketplaceOrder $order,
        CourierAccount $rider,
        ?int $lastMileAssignmentId,
    ): array {
        $token = Str::random(48);
        $contextType = $lastMileAssignmentId !== null ? 'last_mile_assignment' : 'order';
        $contextId = $lastMileAssignmentId ?? (int) $order->id;

        // Keep the same semantic checkpoint used by the web Rider scanner.
        $this->scanRecorder->record(
            $order,
            'buyer-cod-qr:order:'.$order->id,
            'buyer_cod_qr_verified',
            'rider',
            (int) $rider->id,
            null,
            $contextType,
            $contextId,
        );

        Cache::put(
            $this->codCacheKey((int) $rider->id, (int) $order->id),
            [
                'order_id' => (int) $order->id,
                'rider_id' => (int) $rider->id,
                'last_mile_assignment_id' => $lastMileAssignmentId,
                'verified_at' => now()->timestamp,
                'token_hash' => hash('sha256', $token),
            ],
            now()->addMinutes(10),
        );

        return [
            'requires_payment' => true,
            'payment_method' => 'COD',
            'amount_due' => number_format((float) $order->total, 2, '.', ''),
            'currency' => 'PHP',
            'payment_token' => $token,
        ];
    }

    private function codCacheKey(int $riderId, int $orderId): string
    {
        return 'sari:mobile-rider-cod:'.$riderId.':'.$orderId;
    }

    private function isCod(MarketplaceOrder $order): bool
    {
        return strtoupper(trim((string) $order->payment_method)) === 'COD';
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
