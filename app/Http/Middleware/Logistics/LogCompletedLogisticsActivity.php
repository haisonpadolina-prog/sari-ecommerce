<?php

namespace App\Http\Middleware\Logistics;

use App\Services\Logistics\LogisticsActivityLogger;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class LogCompletedLogisticsActivity
{
    /**
     * Each entry: module, action, description, subject type,
     * route parameter (if any), and facility resolution source.
     *
     * Only controller-confirmed changes are logged. All listed operations
     * must set $request->attributes->set('activity_changed', true) after
     * a successful database operation. A success flash alone is insufficient.
     *
     * Facility deletion is omitted: its audit snapshot is saved inside
     * LogisticsFacilityController::destroy() before the hub is deleted.
     */
    private const EVENTS = [
        'logistics.pickup-requests.verify' =>
            ['Pickup Requests', 'verified', 'Pickup request verified', 'order', 'order', 'origin'],

        'logistics.pickup-requests.batch-verify' =>
            ['Pickup Requests', 'batch_verified', 'Pickup batch verified', null, null, 'origin'],

        'logistics.delivery-assignment.assign' =>
            ['Pickup Assignment', 'assigned', 'Pickup rider assigned', 'order', 'order', 'origin'],

        'logistics.delivery-assignment.batch-assign' =>
            ['Pickup Assignment', 'batch_assigned', 'Pickup rider assigned to batch', null, null, 'origin'],

        'logistics.incoming-parcels.receive' =>
            ['Incoming Parcels', 'received', 'Parcel received at origin hub', 'order', 'order', 'origin'],

        'logistics.parcel-sorting.sort' =>
            ['Parcel Sorting', 'sorted', 'Parcel sorting completed', 'parcel', 'parcel', 'origin'],

        'logistics.hub-transfers.store' =>
            ['Hub Transfers', 'created', 'Hub transfer created', 'transfer', null, 'transfer_origin'],

        'logistics.hub-transfers.dispatch' =>
            ['Hub Transfers', 'dispatched', 'Hub transfer dispatched', 'transfer', 'transfer', 'transfer_origin'],

        'logistics.hub-transfers.receive' =>
            ['Hub Transfers', 'received', 'Hub transfer received at destination hub', 'transfer', 'transfer', 'transfer_destination'],

        'logistics.last-mile-assignment.batch-assign' =>
            ['Last Mile Assignment', 'assigned', 'Last-mile Rider assigned', 'assignment', null, 'destination'],

        'logistics.rider-applications.approve' =>
            ['Rider Applications', 'approved', 'Rider application approved', 'application', 'application', null],

        'logistics.rider-applications.reject' =>
            ['Rider Applications', 'rejected', 'Rider application rejected', 'application', 'application', null],

        'logistics.facilities.store' =>
            ['Sorting Facilities', 'created', 'Municipal hub and operator created', 'facility', null, 'facility'],

        'logistics.facilities.update' =>
            ['Sorting Facilities', 'updated', 'Municipal hub updated', 'facility', 'facility', 'facility'],

        'logistics.facilities.toggle' =>
            ['Sorting Facilities', 'status_changed', 'Municipal hub status changed', 'facility', 'facility', 'facility'],

        'logistics.delivery-routes.update' =>
            ['Delivery Routes', 'updated', 'Delivery route refreshed', 'order', 'order', null],

        'logistics.delivery-routes.stops.reached' =>
            ['Delivery Routes', 'reached', 'Delivery route stop marked reached', 'stop', 'stop', null],

        /*
         * This event becomes active when LogisticsMessageController::send()
         * sets activity_changed=true after confirming its write.
         *
         * Do not log raw message content, contact details, or request payloads.
         */
        'logistics.messages.send' =>
            ['Messaging', 'sent', 'Message sent to Rider', 'rider', 'rider', null],

        'logistics.account-management.update' =>
            ['Account Management', 'updated', 'Main Logistics account updated', null, null, null],
    ];

    public function __construct(
        private readonly LogisticsActivityLogger $logger
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Request
    |--------------------------------------------------------------------------
    */

    public function handle(Request $request, Closure $next): Response
    {
        /*
         * Let existing validation, authorization and business logic run first.
         */

        $response = $next($request);

        $routeName = $request->route()?->getName();

        $event = self::EVENTS[$routeName ?? ''] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Check Eligible Operation
        |--------------------------------------------------------------------------
        */

        if (
            $event === null
            || ! in_array(
                $request->method(),
                ['POST', 'PUT', 'PATCH', 'DELETE'],
                true
            )
            || ! $response instanceof RedirectResponse
            || $response->getStatusCode() < 300
            || $response->getStatusCode() >= 400
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | Verify New Success Flash
        |--------------------------------------------------------------------------
        |
        | Do not accept an error redirect or an old success message
        | carried over from the previous request.
        |
        */

        $newFlashKeys = (array) $request->session()->get(
            '_flash.new',
            []
        );

        if (
            ! in_array('success', $newFlashKeys, true)
            || ! $request->session()->has('success')
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | Confirm Actual Change
        |--------------------------------------------------------------------------
        |
        | All integrated controllers must explicitly set this flag
        | after their successful database operation.
        |
        | true  = confirmed change
        | false = no actual change
        | null  = controller is not yet integrated
        |
        */

        if (
            $request->attributes->get('activity_changed') !== true
        ) {
            return $response;
        }

        [
            $module,
            $action,
            $description,
            $subjectType,
            $parameter,
            $facilitySource,
        ] = $event;

        /*
        |--------------------------------------------------------------------------
        | Record Completed Activity
        |--------------------------------------------------------------------------
        */

        try {
            /*
            |--------------------------------------------------------------------------
            | Resolve Subject ID
            |--------------------------------------------------------------------------
            |
            | Route parameter may be an integer or a route-bound model.
            |
            */

            $subjectId = $parameter
                ? $this->routeId($request, $parameter)
                : null;

            /*
             * Created IDs and overrides must come from controller-set
             * attributes, never HTTP form or query input.
             */

            $subjectId = $this->positiveId(
                $request->attributes->get('activity_subject_id')
            ) ?? $subjectId;

            /*
            |--------------------------------------------------------------------------
            | Newly Created Hub Transfer
            |--------------------------------------------------------------------------
            */

            if (
                $routeName === 'logistics.hub-transfers.store'
            ) {
                $subjectId ??= $this->positiveId(
                    $request->attributes->get(
                        'activity_created_transfer_id'
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Newly Created Facility
            |--------------------------------------------------------------------------
            */

            if (
                $routeName === 'logistics.facilities.store'
            ) {
                $subjectId ??= $this->positiveId(
                    $request->attributes->get(
                        'activity_created_facility_id'
                    )
                );
            }

            /*
             * These newly created records must have an actual reference.
             * Do not silently save a reference-less creation event.
             */

            if (
                in_array(
                    $routeName,
                    [
                        'logistics.hub-transfers.store',
                        'logistics.facilities.store',
                        'logistics.last-mile-assignment.batch-assign',
                    ],
                    true
                )
                && $subjectId === null
            ) {
                throw new RuntimeException(
                    'Missing confirmed activity subject for '.$routeName
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve Activity Facility
            |--------------------------------------------------------------------------
            */

            $facilityId = null;

            if (
                ! CurrentLogisticsHub::isHubOperator($request)
            ) {
                $providerId = (int)
                    CurrentLogisticsAccount::resolve($request)->id;

                $override = $this->positiveId(
                    $request->attributes->get(
                        'activity_facility_id'
                    )
                );

                /*
                 * Prefer the post-transaction facility selected by
                 * the controller.
                 *
                 * Independently verify that it belongs to the
                 * authenticated Logistics provider.
                 */

                if ($override !== null) {

                    $facilityId = $this->ownedFacilityId(
                        $providerId,
                        $override
                    );

                    if ($facilityId === null) {
                        throw new RuntimeException(
                            'Audit facility is not owned by the signed-in Logistics provider.'
                        );
                    }

                } else {

                    /*
                     * Fallback for operations without a confirmed override.
                     *
                     * Mixed-hub batches resolve to null, indicating
                     * a network-wide activity.
                     */

                    $facilityId = $this->affectedFacilityId(
                        $request,
                        $providerId,
                        $facilitySource,
                        $subjectId
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save Audit Record
            |--------------------------------------------------------------------------
            |
            | For Hub Operators, the logger always resolves the
            | assigned facility from their authenticated identity.
            |
            | Only one record is created per confirmed request.
            |
            */

            $this->logger->record(
                $request,
                $module,
                $action,
                $description,
                $subjectType,
                $subjectId,
                $facilityId
            );

        } catch (Throwable $exception) {

            /*
             * The business transaction has already committed.
             *
             * Report failed audit writes rather than displaying
             * a false operational failure to the user.
             */

            report($exception);
        }

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Route Parameter
    |--------------------------------------------------------------------------
    */

    private function routeId(
        Request $request,
        string $parameter
    ): ?int {
        $value = $request->route($parameter);

        if (
            is_object($value)
            && method_exists($value, 'getKey')
        ) {
            $value = $value->getKey();
        }

        return $this->positiveId($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Positive ID
    |--------------------------------------------------------------------------
    */

    private function positiveId(mixed $value): ?int
    {
        if (
            ! is_int($value)
            && ! (
                is_string($value)
                && ctype_digit($value)
            )
        ) {
            return null;
        }

        $id = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        return $id === false
            ? null
            : (int) $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Facility Ownership
    |--------------------------------------------------------------------------
    */

    private function ownedFacilityId(
        int $providerId,
        ?int $facilityId
    ): ?int {
        if ($facilityId === null) {
            return null;
        }

        return DB::table('logistics_facilities')
            ->where('id', $facilityId)
            ->where(
                'logistics_account_id',
                $providerId
            )
            ->exists()
                ? $facilityId
                : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Affected Facility
    |--------------------------------------------------------------------------
    */

    private function affectedFacilityId(
        Request $request,
        int $providerId,
        ?string $source,
        ?int $subjectId
    ): ?int {

        /*
        |--------------------------------------------------------------------------
        | Network-Wide Activity
        |--------------------------------------------------------------------------
        */

        if ($source === null) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Direct Facility
        |--------------------------------------------------------------------------
        */

        if ($source === 'facility') {

            $facilityId = $this->positiveId(
                $request->attributes->get(
                    'activity_created_facility_id'
                )
            ) ?? $this->routeId($request, 'facility');

            return $this->ownedFacilityId(
                $providerId,
                $facilityId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hub Transfer Facility
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $source,
                ['transfer_origin', 'transfer_destination'],
                true
            )
        ) {
            $transferId = $subjectId
                ?? $this->routeId($request, 'transfer')
                ?? $this->positiveId(
                    $request->attributes->get(
                        'activity_created_transfer_id'
                    )
                );

            if ($transferId === null) {
                return null;
            }

            $column = $source === 'transfer_origin'
                ? 'origin_facility_id'
                : 'destination_facility_id';

            $facilityId = $this->positiveId(
                DB::table('hub_transfers')
                    ->where('id', $transferId)
                    ->where(
                        'logistics_account_id',
                        $providerId
                    )
                    ->value($column)
            );

            return $this->ownedFacilityId(
                $providerId,
                $facilityId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Origin / Destination Resolution
        |--------------------------------------------------------------------------
        */

        if (
            ! in_array(
                $source,
                ['origin', 'destination'],
                true
            )
        ) {
            return null;
        }

        $orderId = $this->routeId(
            $request,
            'order'
        );

        $parcelId = $this->routeId(
            $request,
            'parcel'
        );

        /*
        |--------------------------------------------------------------------------
        | Resolve Parcel's Parent Order
        |--------------------------------------------------------------------------
        */

        if (
            $orderId === null
            && $parcelId !== null
        ) {
            $orderId = $this->positiveId(
                DB::table('logistics_parcels as parcels')
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'parcels.marketplace_order_id'
                    )
                    ->where('parcels.id', $parcelId)
                    ->where(
                        'orders.logistics_account_id',
                        $providerId
                    )
                    ->value('orders.id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Selected Order IDs
        |--------------------------------------------------------------------------
        */

        if ($orderId !== null) {

            $orderIds = [$orderId];

        } else {

            /*
             * Browser-supplied order IDs are used only to look up
             * existing provider-owned route stops.
             *
             * They are never trusted as facility IDs.
             */

            $submitted = $request->input(
                'order_ids',
                []
            );

            if (
                ! is_array($submitted)
                || $submitted === []
                || count($submitted) > 300
            ) {
                return null;
            }

            $orderIds = [];

            foreach ($submitted as $rawId) {

                $id = $this->positiveId($rawId);

                if ($id === null) {
                    return null;
                }

                $orderIds[] = $id;
            }

            $orderIds = array_values(
                array_unique($orderIds)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Actual Route Stops
        |--------------------------------------------------------------------------
        */

        $stopKey = $source === 'origin'
            ? 'origin_facility'
            : 'destination_facility';

        $facilitiesByOrder = DB::table(
            'delivery_route_stops as stops'
        )
            ->join(
                'marketplace_orders as orders',
                'orders.id',
                '=',
                'stops.marketplace_order_id'
            )
            ->where(
                'orders.logistics_account_id',
                $providerId
            )
            ->whereIn(
                'orders.id',
                $orderIds
            )
            ->where(
                'stops.stop_key',
                $stopKey
            )
            ->whereNotNull(
                'stops.logistics_facility_id'
            )
            ->pluck(
                'stops.logistics_facility_id',
                'stops.marketplace_order_id'
            );

        /*
        |--------------------------------------------------------------------------
        | Validate Resolved Facilities
        |--------------------------------------------------------------------------
        */

        if (
            $facilitiesByOrder->count()
            !== count($orderIds)
        ) {
            return null;
        }

        $uniqueFacilities = $facilitiesByOrder
            ->values()
            ->unique()
            ->values();

        /*
         * Multiple facilities indicate a provider-wide batch.
         * Do not guess or assign an incorrect hub.
         */

        if ($uniqueFacilities->count() !== 1) {
            return null;
        }

        return $this->ownedFacilityId(
            $providerId,
            $this->positiveId(
                $uniqueFacilities->first()
            )
        );
    }
}
