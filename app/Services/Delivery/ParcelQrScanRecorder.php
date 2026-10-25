<?php

namespace App\Services\Delivery;

use App\Models\Delivery\ParcelQrScanEvent;
use App\Models\Orders\MarketplaceOrder;

class ParcelQrScanRecorder
{
    public function __construct(
        private readonly ParcelQrScanNotificationService $notifications,
    ) {
    }

    public function record(
        MarketplaceOrder $order,
        string $scanKey,
        string $checkpoint,
        string $actorRole,
        ?int $actorId = null,
        ?int $facilityId = null,
        ?string $contextType = null,
        ?int $contextId = null,
    ): ParcelQrScanEvent {
        $scan = ParcelQrScanEvent::query()->firstOrCreate(
            ['scan_key' => $scanKey],
            [
                'marketplace_order_id' => $order->id,
                'checkpoint' => $checkpoint,
                'actor_role' => $actorRole,
                'actor_id' => $actorId,
                'logistics_facility_id' => $facilityId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'scanned_at' => now(),
            ]
        );

        /*
         * Notification writes are idempotent too, so calling dispatch() for an
         * existing scan safely repairs a partially completed notification fanout
         * without creating duplicates.
         */
        $this->notifications->dispatch($scan);

        return $scan;
    }

    public function countForContext(
        string $contextType,
        int $contextId,
        string $checkpoint
    ): int {
        return ParcelQrScanEvent::query()
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->where('checkpoint', $checkpoint)
            ->distinct('marketplace_order_id')
            ->count('marketplace_order_id');
    }
}
