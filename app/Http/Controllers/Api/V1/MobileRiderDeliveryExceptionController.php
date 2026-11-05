<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Services\Logistics\LogisticsNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MobileRiderDeliveryExceptionController extends Controller
{
    public function __construct(private readonly LogisticsNotificationService $notifications) {}

    public function failed(
        Request $request,
        MarketplaceOrder $order,
        MarketplaceOrderWorkflowService $workflow,
    ): JsonResponse {
        $this->guardDirectDelivery($request, $order);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        abort_unless(
            in_array($order->status, ['in_transit', 'arrived_buyer'], true),
            422,
            'This delivery can no longer be reported as failed.'
        );

        $workflow->record(
            $order,
            'logistics',
            'delivery_failed_reported',
            'Rider reported a failed delivery',
            trim((string) $validated['reason'])
        );

        $this->notifyProvider(
            $order,
            'delivery_failed_reported:order:'.(int) $order->id,
            'delivery_failed_reported',
            'Rider reported a failed delivery',
            trim((string) $validated['reason'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Failed delivery report sent to SARI Logistics.',
            'data' => [
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->status,
                'incident' => 'delivery_failed_reported',
                'reason' => trim((string) $validated['reason']),
            ],
        ]);
    }

    public function reschedule(
        Request $request,
        MarketplaceOrder $order,
        MarketplaceOrderWorkflowService $workflow,
    ): JsonResponse {
        $this->guardDirectDelivery($request, $order);

        $validated = $request->validate([
            'rescheduled_for' => ['required', 'date', 'after:now'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless(
            in_array($order->status, ['in_transit', 'arrived_buyer'], true),
            422,
            'This delivery can no longer be rescheduled.'
        );

        $when = Carbon::parse((string) $validated['rescheduled_for']);
        $reason = trim((string) ($validated['reason'] ?? ''));

        $message = 'Requested delivery schedule: '.$when->toDateTimeString().'.';
        if ($reason !== '') {
            $message .= ' Reason: '.$reason;
        }

        $workflow->record(
            $order,
            'logistics',
            'delivery_reschedule_requested',
            'Rider requested delivery reschedule',
            $message
        );

        $this->notifyProvider(
            $order,
            'delivery_reschedule_requested:order:'.(int) $order->id,
            'delivery_reschedule_requested',
            'Rider requested delivery reschedule',
            $message
        );

        return response()->json([
            'success' => true,
            'message' => 'Reschedule request sent to SARI Logistics.',
            'data' => [
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->status,
                'incident' => 'delivery_reschedule_requested',
                'rescheduled_for' => $when->toIso8601String(),
                'reason' => $reason !== '' ? $reason : null,
            ],
        ]);
    }

    public function returnToSorting(
        Request $request,
        MarketplaceOrder $order,
        MarketplaceOrderWorkflowService $workflow,
    ): JsonResponse {
        $this->guardDirectDelivery($request, $order);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        abort_unless(
            in_array($order->status, ['in_transit', 'arrived_buyer'], true),
            422,
            'This parcel can no longer be submitted for return to sorting.'
        );

        $reason = trim((string) $validated['reason']);

        $workflow->record(
            $order,
            'logistics',
            'return_to_sorting_requested',
            'Rider requested return to sorting',
            $reason
        );

        $this->notifyProvider(
            $order,
            'return_to_sorting_requested:order:'.(int) $order->id,
            'return_to_sorting_requested',
            'Rider requested return to sorting',
            $reason
        );

        return response()->json([
            'success' => true,
            'message' => 'Return-to-sorting request sent to SARI Logistics.',
            'data' => [
                'order_id' => (int) $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->status,
                'incident' => 'return_to_sorting_requested',
                'reason' => $reason,
            ],
        ]);
    }

    private function notifyProvider(
        MarketplaceOrder $order,
        string $eventKey,
        string $type,
        string $title,
        string $message,
    ): void {
        $providerId = (int) ($order->logistics_account_id ?? 0);

        if ($providerId <= 0) {
            return;
        }

        $this->notifications->notifyManager(
            $providerId,
            $eventKey,
            $type,
            $title,
            $message,
            'logistics.delivery-monitoring',
            [
                'order_id' => (int) $order->id,
                'order_number' => (string) $order->order_number,
            ],
        );
    }

    private function guardDirectDelivery(Request $request, MarketplaceOrder $order): void
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();

        abort_unless(
            strtolower(trim((string) $order->courier_email)) === strtolower(trim((string) $rider->email)),
            404
        );

        // Logistics pickup orders use the hub/scanner workflow and must not use
        // these direct Buyer-delivery exception actions.
        $order->loadMissing('logisticsParcel');
        abort_if($order->logisticsParcel !== null, 422, 'Use the Logistics parcel workflow for this order.');
    }
}

