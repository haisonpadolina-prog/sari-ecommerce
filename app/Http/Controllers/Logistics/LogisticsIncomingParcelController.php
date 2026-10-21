<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LogisticsIncomingParcelController extends Controller
{
    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsNotificationService $notifications,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Incoming Parcels
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        /*
         * Origin-hub intake is for Logistics-managed pickup parcels,
         * not direct deliveries or the final-mile Buyer arrival stage.
         *
         * Received and advanced historical rows remain visible.
         */

        $ordersQuery = MarketplaceOrder::query()
            ->with([
                'seller',
                'deliveryRouteStops.facility',
            ])
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'in_transit')
            ->whereNotNull('courier_email')
            ->whereHas('logisticsParcel');

        /*
         * Hub Operators see only their assigned origin facility.
         * Managers retain network-wide visibility.
         */

        if ($hubFacility) {
            $ordersQuery->whereHas(
                'deliveryRouteStops',
                function ($query) use ($hubFacility): void {
                    $query
                        ->where('stop_key', 'origin_facility')
                        ->where(
                            'logistics_facility_id',
                            $hubFacility->id
                        );
                }
            );
        }

        $orders = $ordersQuery
            ->latest('picked_up_at')
            ->get();

        $parcels = LogisticsParcel::query()
            ->whereIn(
                'marketplace_order_id',
                $orders->pluck('id')
            )
            ->get()
            ->keyBy('marketplace_order_id');

        return view(
            'logistics.incoming-parcels',
            compact(
                'orders',
                'parcels',
                'hubFacility',
                'isHubOperator'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Receive at Origin Hub
    |--------------------------------------------------------------------------
    */

    public function receive(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {

        /*
         * Authorize the Hub Operator against the existing origin route
         * BEFORE calling the legacy ownedOrder() resolver. That resolver
         * can generate route stops for previously unrouted orders.
         */

        $logistics = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        if ($isHubOperator) {
            CurrentLogisticsHub::authorizeOrder(
                $request,
                $order,
                'origin'
            );

            $hubFacility = CurrentLogisticsHub::requireFacility($request);
        } else {
            $order = $this->orderAccess->ownedOrder($request, $order);
            $hubFacility = null;
        }

        $logisticsId = (int) $logistics->id;

        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Lock the order and parcel to prevent duplicate intake.
         * All changes, including notification creation, either succeed
         * together or roll back.
         */

        $result = DB::transaction(function () use (
            $request,
            $order,
            $logisticsId,
            $hubFacility,
            $validated
        ): array {

            $lockedOrder = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->where('logistics_account_id', $logisticsId)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * The Pickup Rider must have confirmed physical pickup.
             */

            if (
                $lockedOrder->status !== 'in_transit'
                || ! $lockedOrder->picked_up_at
                || blank($lockedOrder->courier_email)
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'Only a parcel already picked up by its assigned Rider can be received at the origin hub.',
                ]);
            }

            /*
             * Confirm the actual origin facility under the row lock.
             */

            $origin = $lockedOrder->deliveryRouteStops()
                ->where('stop_key', 'origin_facility')
                ->lockForUpdate()
                ->first();

            $origin?->load('facility');

            $originFacility = $origin?->facility;

            if (
                ! $originFacility
                || ! $originFacility->is_active
                || (int) $originFacility->logistics_account_id
                    !== $logisticsId
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'The assigned origin hub is missing, inactive or belongs to another Logistics provider.',
                ]);
            }

            /*
             * Re-check the Hub Operator's CURRENT facility assignment,
             * not just the facility captured before the transaction.
             * A Hub Operator cannot receive another hub's parcel.
             */

            if ($hubFacility) {
                $currentHubFacility = CurrentLogisticsHub::requireFacility(
                    $request
                );

                abort_unless(
                    (int) $currentHubFacility->id === (int) $hubFacility->id
                        && (int) $originFacility->id === (int) $currentHubFacility->id,
                    403,
                    'This parcel is assigned to another origin Logistics hub.'
                );
            }

            /*
             * Do not create a new LogisticsParcel here. The parcel must
             * already exist from Pickup Verification / Assignment.
             */

            $parcel = LogisticsParcel::query()
                ->where(
                    'marketplace_order_id',
                    $lockedOrder->id
                )
                ->lockForUpdate()
                ->first();

            if (
                ! $parcel
                || $parcel->status !== 'awaiting_intake'
            ) {
                throw ValidationException::withMessages([
                    'order' =>
                        'This parcel is not awaiting origin-hub intake. Refresh the incoming queue.',
                ]);
            }

            /*
             * Record one actual receipt timestamp.
             */

            $receivedAt = now();

            $parcel->fill([
                'status' => 'received',
                'notes' => $validated['notes']
                    ?? $parcel->notes,
                'received_at' => $receivedAt,
                'sorted_at' => null,
            ])->save();

            /*
             * Synchronize the actual origin-hub milestone.
             * Preserve an existing historical reached_at timestamp.
             */

            if (! $origin->reached_at) {
                $origin->forceFill([
                    'reached_at' => $receivedAt,
                ])->save();
            }

            /*
             * Include origin-hub intake in the Seller/Buyer order history.
             */

            $this->workflow->record(
                $lockedOrder,
                'both',
                'origin_hub_received',
                'Origin Hub Intake Confirmed',
                'Parcel for order '
                    . $lockedOrder->order_number
                    . ' was received at '
                    . $originFacility->name
                    . '.'
            );

            /*
            |--------------------------------------------------------------------------
            | Persistent notifications for the actual origin-hub intake
            |--------------------------------------------------------------------------
            |
            | The event key is per order and recipient. An already-received
            | parcel cannot trigger another event, and createOrFirst() does
            | not reset an existing notification's read_at value.
            |
            | These inserts are within the same DB transaction as intake.
            */

            $eventKey = 'origin_hub_received:order:'.$lockedOrder->id;

            $message = 'Parcel for order '
                . $lockedOrder->order_number
                . ' was received at '
                . $originFacility->name
                . '. It is ready for parcel sorting.';

            $data = [
                'order_id' => (int) $lockedOrder->id,
                'order_number' => (string) $lockedOrder->order_number,
                'origin_facility_id' => (int) $originFacility->id,
                'received_at' => $receivedAt->toIso8601String(),
            ];

            /*
             * Main Logistics Manager notification.
             */

            $this->notifications->notifyManager(
                logisticsAccountId: $logisticsId,
                eventKey: $eventKey,
                type: 'origin_hub_received',
                title: 'Parcel received at origin hub',
                message: $message,
                actionRoute: 'logistics.parcel-sorting',
                data: $data
            );

            /*
             * Individual notifications for the active operators
             * of the actual origin facility.
             */

            $this->notifications->notifyFacilityOperators(
                logisticsAccountId: $logisticsId,
                facilityId: (int) $originFacility->id,
                eventKey: $eventKey,
                type: 'origin_hub_received',
                title: 'Incoming parcel ready for sorting',
                message: $message,
                actionRoute: 'logistics.parcel-sorting',
                data: $data
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Logs — Return Actual Origin Facility
            |--------------------------------------------------------------------------
            |
            | Return verified facility information from the database.
            | Never trust browser-submitted facility IDs.
            |
            */

            return [
                'facility_id' => (int) $originFacility->id,
                'facility_name' => (string) $originFacility->name,
            ];

        }, 3);

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | This part executes only AFTER the database transaction
        | has successfully committed.
        |
        | The shared middleware will use these server-side attributes
        | to record the completed parcel intake.
        |
        | Duplicate intake, invalid parcel states, and unauthorized
        | actions throw before reaching this section.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_facility_id',
            $result['facility_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Parcel received at '
                . $result['facility_name']
                . '. It is now ready for sorting.'
        );
    }
}
