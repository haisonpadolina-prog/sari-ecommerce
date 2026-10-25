<?php

namespace App\Services\Delivery;

use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\ParcelQrScanEvent;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\SellerNotification;
use App\Services\Logistics\LogisticsNotificationService;
use Illuminate\Support\Facades\Schema;

final class ParcelQrScanNotificationService
{
    public function __construct(
        private readonly LogisticsNotificationService $logisticsNotifications,
    ) {
    }

    public function dispatch(ParcelQrScanEvent $scan): void
    {
        $scan->loadMissing('order');

        $order = $scan->order;

        if (! $order) {
            return;
        }

        $facilityId = $this->resolveFacilityId($scan);
        $facilityName = $facilityId
            ? LogisticsFacility::query()->whereKey($facilityId)->value('name')
            : null;

        $checkpoint = $this->checkpointCopy(
            (string) $scan->checkpoint,
            $facilityName ? (string) $facilityName : null
        );

        $message = 'Order '.$order->order_number.' — '.$checkpoint['message'];

        /*
        |--------------------------------------------------------------------------
        | Buyer notification
        |--------------------------------------------------------------------------
        |
        | Buyer notifications are backed by MarketplaceOrderEvent. firstOrCreate()
        | keeps accidental duplicate scans from producing duplicate notifications.
        |
        */
        MarketplaceOrderEvent::query()->firstOrCreate(
            [
                'marketplace_order_id' => (int) $order->id,
                'seller_account_id' => (int) $order->seller_account_id,
                'audience' => 'buyer',
                'type' => 'qr_scan_'.$scan->checkpoint,
            ],
            [
                'title' => $checkpoint['title'],
                'message' => $message,
                'status' => (string) $order->status,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Seller notification
        |--------------------------------------------------------------------------
        |
        | SellerNotification is the persistent Seller notification source and its
        | model observer already handles best-effort realtime broadcast.
        |
        */
        if (Schema::hasTable('seller_notifications')) {
            SellerNotification::query()->firstOrCreate(
                [
                    'seller_account_id' => (int) $order->seller_account_id,
                    'type' => 'parcel_scan',
                    'title' => $checkpoint['title'],
                    'message' => $message,
                    'action_url' => '/seller/shipping/'.$order->id,
                ],
                [
                    'data' => [
                        'parcel_qr_scan_event_id' => (int) $scan->id,
                        'marketplace_order_id' => (int) $order->id,
                        'order_number' => (string) $order->order_number,
                        'checkpoint' => (string) $scan->checkpoint,
                        'facility_id' => $facilityId,
                        'actor_role' => (string) $scan->actor_role,
                        'scanned_at' => $scan->scanned_at?->toIso8601String(),
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Logistics notifications
        |--------------------------------------------------------------------------
        |
        | Every successful QR checkpoint notifies the parent Logistics manager.
        | When the scan belongs to an actual facility, active operators assigned
        | to that facility receive their own recipient-scoped notification too.
        |
        */
        $logisticsId = (int) ($order->logistics_account_id ?? 0);

        if ($logisticsId <= 0) {
            return;
        }

        $eventKey = 'parcel_qr_scan:'.$scan->id;
        $data = [
            'parcel_qr_scan_event_id' => (int) $scan->id,
            'marketplace_order_id' => (int) $order->id,
            'order_number' => (string) $order->order_number,
            'checkpoint' => (string) $scan->checkpoint,
            'facility_id' => $facilityId,
            'actor_role' => (string) $scan->actor_role,
            'actor_id' => $scan->actor_id ? (int) $scan->actor_id : null,
            'scanned_at' => $scan->scanned_at?->toIso8601String(),
        ];

        $this->logisticsNotifications->notifyManager(
            logisticsAccountId: $logisticsId,
            eventKey: $eventKey,
            type: 'parcel_scan',
            title: $checkpoint['title'],
            message: $message,
            actionRoute: $checkpoint['logistics_route'],
            data: $data
        );

        if ($facilityId) {
            $this->logisticsNotifications->notifyFacilityOperators(
                logisticsAccountId: $logisticsId,
                facilityId: $facilityId,
                eventKey: $eventKey,
                type: 'parcel_scan',
                title: $checkpoint['title'],
                message: $message,
                actionRoute: $checkpoint['logistics_route'],
                data: $data
            );
        }
    }

    private function resolveFacilityId(ParcelQrScanEvent $scan): ?int
    {
        if ((int) ($scan->logistics_facility_id ?? 0) > 0) {
            return (int) $scan->logistics_facility_id;
        }

        $stopKey = match ((string) $scan->checkpoint) {
            'pickup_rider_handoff',
            'origin_hub_intake',
            'origin_hub_transfer_scan' => 'origin_facility',

            'destination_hub_transfer_scan',
            'destination_ready_for_delivery',
            'last_mile_rider_handoff' => 'destination_facility',

            default => null,
        };

        if (! $stopKey) {
            return null;
        }

        $facilityId = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $scan->marketplace_order_id)
            ->where('stop_key', $stopKey)
            ->value('logistics_facility_id');

        return $facilityId ? (int) $facilityId : null;
    }

    /**
     * @return array{title: string, message: string, logistics_route: string}
     */
    private function checkpointCopy(string $checkpoint, ?string $facilityName): array
    {
        return match ($checkpoint) {
            'pickup_rider_handoff' => [
                'title' => 'Parcel QR scanned by pickup Rider',
                'message' => 'the Seller handoff was QR-verified by the pickup Rider. The parcel is moving to the origin Logistics hub.',
                'logistics_route' => 'logistics.incoming-parcels',
            ],
            'origin_hub_intake' => [
                'title' => 'Parcel QR scanned at origin hub',
                'message' => 'the parcel was QR-verified and received at '.($facilityName ?: 'the origin Logistics hub').'.',
                'logistics_route' => 'logistics.parcel-sorting',
            ],
            'origin_hub_transfer_scan' => [
                'title' => 'Parcel QR scanned for hub transfer',
                'message' => 'the parcel was QR-verified at '.($facilityName ?: 'the origin Logistics hub').' while being loaded for inter-hub transfer.',
                'logistics_route' => 'logistics.hub-transfers',
            ],
            'destination_hub_transfer_scan' => [
                'title' => 'Parcel QR scanned at destination hub',
                'message' => 'the parcel was QR-verified at '.($facilityName ?: 'the destination Logistics hub').' as part of its hub-transfer arrival.',
                'logistics_route' => 'logistics.hub-transfers',
            ],
            'destination_ready_for_delivery' => [
                'title' => 'Parcel ready for final delivery',
                'message' => 'the parcel was QR-verified at '.($facilityName ?: 'the destination Logistics hub').' and cleared for its assigned local Rider.',
                'logistics_route' => 'logistics.delivery-monitoring',
            ],
            'last_mile_rider_handoff' => [
                'title' => 'Parcel handed to local Rider',
                'message' => 'the assigned local Rider QR-verified custody of the parcel before leaving the destination hub.',
                'logistics_route' => 'logistics.delivery-monitoring',
            ],
            'final_delivery_scan' => [
                'title' => 'Delivery completed by Rider QR scan',
                'message' => 'the assigned Rider QR-verified the parcel at the final Buyer handoff and delivery was completed.',
                'logistics_route' => 'logistics.delivery-monitoring',
            ],
            default => [
                'title' => 'Parcel QR scan recorded',
                'message' => 'a verified parcel QR scan was recorded in the delivery workflow.',
                'logistics_route' => 'logistics.delivery-monitoring',
            ],
        };
    }
}
