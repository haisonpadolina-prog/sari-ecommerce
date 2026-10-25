<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Logistics\LogisticsNotificationService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsServiceArea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsLastMileAssignmentController extends Controller
{
    public function __construct(
        private readonly LogisticsNotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $isHubOperator =
            CurrentLogisticsHub::isHubOperator($request);

        $hubFacility =
            CurrentLogisticsHub::requireOperatingFacility($request);

        $readyParcelsQuery =
            DB::table('logistics_parcels as parcel')
                ->join(
                    'marketplace_orders as orders',
                    'orders.id',
                    '=',
                    'parcel.marketplace_order_id'
                )
                ->join(
                    'delivery_route_stops as destination_stop',
                    function ($join): void {
                        $join
                            ->on(
                                'destination_stop.marketplace_order_id',
                                '=',
                                'orders.id'
                            )
                            ->where(
                                'destination_stop.stop_key',
                                '=',
                                'destination_facility'
                            );
                    }
                )
                ->join(
                    'logistics_facilities as destination',
                    'destination.id',
                    '=',
                    'destination_stop.logistics_facility_id'
                )
                ->where(
                    'orders.logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'parcel.status',
                    'destination_received'
                )
                ->whereNotExists(
                    function ($query): void {
                        $query
                            ->selectRaw('1')
                            ->from(
                                'last_mile_assignment_orders as assigned'
                            )
                            ->whereColumn(
                                'assigned.marketplace_order_id',
                                'orders.id'
                            );
                    }
                )
                ->where('orders.status', 'in_transit')
                ->where('destination.logistics_account_id', $logistics->id)
                ->where('destination.is_active', true)
                ->select([
                    'parcel.id as parcel_id',

                    'orders.id as order_id',
                    'orders.order_number',
                    'orders.buyer_name',
                    'orders.buyer_address',

                    'orders.courier_name as pickup_rider_name',
                    'orders.courier_email as pickup_rider_email',

                    'destination.id as destination_facility_id',
                    'destination.name as destination_facility_name',
                    'destination.municipality_name as destination_municipality',
                    'destination.province_name as destination_province',
                ])
                ->orderBy('orders.order_number');

        if ($hubFacility) {
            $readyParcelsQuery->where(
                'destination.id',
                $hubFacility->id
            );
        }

        $readyParcels = $readyParcelsQuery->get();

        $readyParcels = $readyParcels->map(
            function ($parcel) {
                $parcel->barangay_name =
                    $this->extractBarangay(
                        $parcel->buyer_address,
                        $parcel->destination_municipality
                    );

                return $parcel;
            }
        );

        $deliveryGroups = $readyParcels->groupBy(
            function ($parcel): string {
                return implode(
                    '|',
                    [
                        $parcel->destination_facility_id,

                        strtolower(
                            trim($parcel->barangay_name)
                        ),
                    ]
                );
            }
        );

        $approvedApplications =
            RegistrationApplication::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where('role', 'rider')
                ->where('status', 'approved')
                ->select('id');

        $ridersQuery =
            CourierAccount::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where('account_status', 'active')
                ->where('availability_status', 'online')
                ->whereIn(
                    'registration_application_id',
                    $approvedApplications
                );

        if ($hubFacility) {
            $ridersQuery->whereRaw(
                'LOWER(TRIM(province_name)) = ?',
                [
                    mb_strtolower(
                        trim($hubFacility->province_name)
                    ),
                ]
            );

            $ridersQuery->whereRaw(
                'LOWER(TRIM(municipality_name)) = ?',
                [
                    strtolower(
                        trim(
                            (string) $hubFacility->municipality_name
                        )
                    ),
                ]
            );
        }

        $riders =
            $ridersQuery
                ->orderBy('barangay_name')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

        $assignmentsQuery =
            DB::table('last_mile_assignments as assignment')
                ->join(
                    'courier_accounts as rider',
                    'rider.id',
                    '=',
                    'assignment.courier_account_id'
                )
                ->join(
                    'logistics_facilities as facility',
                    'facility.id',
                    '=',
                    'assignment.destination_facility_id'
                )
                ->where(
                    'assignment.logistics_account_id',
                    $logistics->id
                )
                ->select([
                    'assignment.*',

                    'rider.first_name',
                    'rider.last_name',
                    'rider.vehicle_type',

                    'facility.name as facility_name',
                ]);

        if ($hubFacility) {
            $assignmentsQuery->where(
                'assignment.destination_facility_id',
                $hubFacility->id
            );
        }

        $assignments =
            $assignmentsQuery
                ->orderByDesc('assignment.id')
                ->get();

        foreach ($assignments as $assignment) {
            $assignment->order_count =
                DB::table('last_mile_assignment_orders')
                    ->where(
                        'last_mile_assignment_id',
                        $assignment->id
                    )
                    ->count();
        }

        $busyPickupEmails = MarketplaceOrder::query()
            ->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
            ])
            ->where(function ($query): void {
                $query
                    ->whereDoesntHave('logisticsParcel')
                    ->orWhereHas(
                        'logisticsParcel',
                        fn ($parcel) => $parcel->where(
                            'status',
                            'awaiting_intake'
                        )
                    );
            })
            ->pluck('courier_email')
            ->map(
                fn ($email) => strtolower(
                    trim((string) $email)
                )
            );

        $busyRiderIds = DB::table('last_mile_assignments')
            ->whereIn('status', [
                'assigned',
                'out_for_delivery',
            ])
            ->whereIn(
                'courier_account_id',
                $riders->modelKeys()
            )
            ->pluck('courier_account_id')
            ->merge(
                $riders
                    ->filter(
                        fn ($rider) => $busyPickupEmails->contains(
                            strtolower(trim($rider->email))
                        )
                    )
                    ->modelKeys()
            )
            ->map(fn ($id) => (int) $id)
            ->unique();

        return view(
            'logistics.last-mile-assignment',
            compact(
                'deliveryGroups',
                'riders',
                'assignments',
                'hubFacility',
                'isHubOperator',
                'busyRiderIds'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Last-Mile Batch
    |--------------------------------------------------------------------------
    */

    public function assignBatch(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([
            'rider_id' => [
                'required',
                'integer',
                'exists:courier_accounts,id',
            ],

            'order_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'order_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:marketplace_orders,id',
            ],
        ]);

        $logistics =
            CurrentLogisticsAccount::resolve($request);

        $isHubOperator =
            CurrentLogisticsHub::isHubOperator($request);

        $hubFacility =
            CurrentLogisticsHub::requireOperatingFacility($request);

        $orderIds = collect($validated['order_ids'])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Atomic Last-Mile Assignment
        |--------------------------------------------------------------------------
        |
        | Return the newly created assignment and destination hub
        | so that activity logging only happens after commit.
        |
        */

        $activityContext = DB::transaction(
            function () use (
                $request,
                $validated,
                $logistics,
                $orderIds,
                $hubFacility
            ): array {

                /*
                |--------------------------------------------------------------------------
                | Lock and Verify Orders
                |--------------------------------------------------------------------------
                */

                $orders =
                    DB::table('marketplace_orders as orders')
                        ->join(
                            'logistics_parcels as parcel',
                            'parcel.marketplace_order_id',
                            '=',
                            'orders.id'
                        )
                        ->join(
                            'delivery_route_stops as destination_stop',
                            function ($join): void {
                                $join
                                    ->on(
                                        'destination_stop.marketplace_order_id',
                                        '=',
                                        'orders.id'
                                    )
                                    ->where(
                                        'destination_stop.stop_key',
                                        '=',
                                        'destination_facility'
                                    );
                            }
                        )
                        ->join(
                            'logistics_facilities as destination',
                            'destination.id',
                            '=',
                            'destination_stop.logistics_facility_id'
                        )
                        ->whereIn(
                            'orders.id',
                            $orderIds->all()
                        )
                        ->where(
                            'orders.logistics_account_id',
                            $logistics->id
                        )
                        ->where(
                            'parcel.status',
                            'destination_received'
                        )
                        ->where(
                            'orders.status',
                            'in_transit'
                        )
                        ->select([
                            'orders.id',
                            'orders.order_number',
                            'orders.buyer_address',

                            'parcel.id as parcel_id',

                            'destination.id as destination_facility_id',
                            'destination.name as destination_facility_name',
                            'destination.municipality_name as destination_municipality',
                            'destination.province_name as destination_province',
                        ])
                        ->lockForUpdate()
                        ->get();

                if ($orders->count() !== $orderIds->count()) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Some selected parcels are no longer available for last-mile assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Hub Operator Authorization
                |--------------------------------------------------------------------------
                */

                if ($hubFacility) {
                    $currentHub =
                        CurrentLogisticsHub::requireOperatingFacility($request);

                    abort_unless(
                        (int) $currentHub->id
                            === (int) $hubFacility->id,
                        403,
                        'Your assigned Logistics hub has changed. Sign in again.'
                    );
                }

                if (
                    $hubFacility
                    && $orders->contains(
                        fn ($order): bool =>
                            (int) $order->destination_facility_id
                            !== (int) $hubFacility->id
                    )
                ) {
                    abort(
                        403,
                        'One or more selected parcels belong to another destination Logistics hub.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Assignment
                |--------------------------------------------------------------------------
                */

                $alreadyAssigned =
                    DB::table('last_mile_assignment_orders')
                        ->whereIn(
                            'marketplace_order_id',
                            $orderIds->all()
                        )
                        ->exists();

                if ($alreadyAssigned) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'One or more selected orders already have a last-mile assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Same Destination Hub + Barangay
                |--------------------------------------------------------------------------
                */

                $groupKeys = $orders
                    ->map(function ($order): string {
                        return implode(
                            '|',
                            [
                                $order->destination_facility_id,

                                strtolower(
                                    trim(
                                        $this->extractBarangay(
                                            $order->buyer_address,
                                            $order->destination_municipality
                                        )
                                    )
                                ),
                            ]
                        );
                    })
                    ->unique();

                if ($groupKeys->count() !== 1) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Last-mile batches must contain orders for the same destination hub and barangay.',
                    ]);
                }

                $firstOrder = $orders->first();

                /*
                |--------------------------------------------------------------------------
                | Validate Destination Facility
                |--------------------------------------------------------------------------
                */

                $destinationFacility =
                    DB::table('logistics_facilities')
                        ->where(
                            'id',
                            (int) $firstOrder->destination_facility_id
                        )
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->first(['id', 'name']);

                if (! $destinationFacility) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'The destination hub is inactive or does not belong to this Logistics network.',
                    ]);
                }

                $deliveryArea = $this->extractBarangay(
                    $firstOrder->buyer_address,
                    $firstOrder->destination_municipality
                );

                /*
                |--------------------------------------------------------------------------
                | Selected Rider
                |--------------------------------------------------------------------------
                */

                $rider = CourierAccount::query()
                    ->whereKey(
                        (int) $validated['rider_id']
                    )
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->where('account_status', 'active')
                    ->where('availability_status', 'online')
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Verify Rider Approval
                |--------------------------------------------------------------------------
                */

                $approved =
                    RegistrationApplication::query()
                        ->whereKey(
                            $rider->registration_application_id
                        )
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->where('role', 'rider')
                        ->where('status', 'approved')
                        ->exists();

                if (! $approved) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'The selected Rider is not approved for this Logistics network.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Check Barangay Service Area
                |--------------------------------------------------------------------------
                */

                $firstOrder->barangay_name = $deliveryArea;

                if (! LogisticsServiceArea::matchesRider(
                    $rider,
                    $firstOrder
                )) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'Select a Rider assigned to '
                            .$deliveryArea
                            .', '
                            .$firstOrder->destination_municipality
                            .'.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Conflicting Rider Assignments
                |--------------------------------------------------------------------------
                */

                $busyPickup = MarketplaceOrder::query()
                    ->whereRaw(
                        'LOWER(TRIM(courier_email)) = ?',
                        [strtolower(trim($rider->email))]
                    )
                    ->whereIn('status', [
                        'courier_accepted',
                        'heading_pickup',
                        'arrived_pickup',
                        'in_transit',
                        'arrived_buyer',
                    ])
                    ->where(function ($query): void {
                        $query
                            ->whereDoesntHave('logisticsParcel')
                            ->orWhereHas(
                                'logisticsParcel',
                                fn ($parcel) => $parcel->where(
                                    'status',
                                    'awaiting_intake'
                                )
                            );
                    })
                    ->exists();

                if ($busyPickup) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'This Rider already has an active Seller pickup or delivery.',
                    ]);
                }

                $riderBusy =
                    DB::table('last_mile_assignments')
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->whereIn('status', [
                            'assigned',
                            'out_for_delivery',
                        ])
                        ->exists();

                if ($riderBusy) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'This Rider already has an active last-mile batch.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Last-Mile Assignment
                |--------------------------------------------------------------------------
                */

                $assignmentCode =
                    'LM-'
                    .now()->format('Ymd-His')
                    .'-'
                    .strtoupper(bin2hex(random_bytes(3)));

                $assignmentId =
                    DB::table('last_mile_assignments')
                        ->insertGetId([
                            'logistics_account_id' => $logistics->id,

                            'destination_facility_id' =>
                                $firstOrder->destination_facility_id,

                            'courier_account_id' => $rider->id,

                            'assignment_code' => $assignmentCode,

                            'delivery_area' => $deliveryArea,

                            'status' => 'assigned',

                            'assigned_at' => now(),

                            'created_at' => now(),

                            'updated_at' => now(),
                        ]);

                /*
                |--------------------------------------------------------------------------
                | Attach Orders
                |--------------------------------------------------------------------------
                */

                foreach ($orderIds as $orderId) {
                    DB::table('last_mile_assignment_orders')
                        ->insert([
                            'last_mile_assignment_id' => $assignmentId,

                            'marketplace_order_id' => $orderId,

                            'created_at' => now(),

                            'updated_at' => now(),
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Update Parcel Status
                |--------------------------------------------------------------------------
                |
                | Do not overwrite marketplace_orders.courier_email.
                | This remains the historical Pickup Rider snapshot.
                |
                */

                $updatedParcels =
                    DB::table('logistics_parcels')
                        ->whereIn(
                            'marketplace_order_id',
                            $orderIds->all()
                        )
                        ->where(
                            'status',
                            'destination_received'
                        )
                        ->update([
                            'status' => 'last_mile_assigned',

                            'updated_at' => now(),
                        ]);

                if ($updatedParcels !== $orderIds->count()) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'A parcel changed while creating the Last-Mile batch. No assignment was saved. Refresh and retry.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Persistent Notifications
                |--------------------------------------------------------------------------
                */

                $riderName = trim(
                    (string) $rider->first_name
                    .' '
                    .(string) $rider->last_name
                ) ?: 'SARI Rider';

                $eventKey =
                    'last_mile_assigned:assignment:'
                    .$assignmentId;

                $message =
                    'Last-Mile batch '
                    .$assignmentCode
                    .' containing '
                    .$orderIds->count()
                    .' parcel(s) for '
                    .$deliveryArea
                    .', '
                    .$firstOrder->destination_municipality
                    .' was assigned to '
                    .$riderName
                    .'.';

                $data = [
                    'assignment_id' => (int) $assignmentId,

                    'assignment_code' => $assignmentCode,

                    'destination_facility_id' =>
                        (int) $destinationFacility->id,

                    'rider_id' => (int) $rider->id,

                    'rider_name' => $riderName,

                    'delivery_area' => $deliveryArea,

                    'parcel_count' => (int) $orderIds->count(),

                    'order_ids' => $orderIds->all(),
                ];

                /*
                 * Main Logistics Notification
                 */

                $this->notifications->notifyManager(
                    logisticsAccountId: (int) $logistics->id,
                    eventKey: $eventKey,
                    type: 'last_mile_assigned',
                    title: 'Last-Mile Rider assigned',
                    message: $message,
                    actionRoute: 'logistics.last-mile-assignment',
                    data: $data
                );

                /*
                 * Destination Hub Notification
                 */

                $this->notifications->notifyFacilityOperators(
                    logisticsAccountId: (int) $logistics->id,
                    facilityId: (int) $destinationFacility->id,
                    eventKey: $eventKey,
                    type: 'last_mile_assigned',
                    title: 'Your Last-Mile batch is assigned',
                    message: $message,
                    actionRoute: 'logistics.last-mile-assignment',
                    data: $data
                );

                /*
                |--------------------------------------------------------------------------
                | Activity Logs — Return Actual Saved Values
                |--------------------------------------------------------------------------
                */

                return [
                    'assignment_id' => (int) $assignmentId,

                    'destination_facility_id' =>
                        (int) $destinationFacility->id,
                ];
            },
            3
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | Execute only after successful transaction commit.
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_facility_id',
            $activityContext['destination_facility_id']
        );

        $request->attributes->set(
            'activity_subject_id',
            $activityContext['assignment_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        $location =
            $hubFacility
                ? $hubFacility->name
                : 'the destination Logistics hub';

        return back()->with(
            'success',
            'Last-mile Rider assigned successfully from '
                .$location
                .'.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Barangay Address Parser
    |--------------------------------------------------------------------------
    */

    private function extractBarangay(
        ?string $address,
        ?string $municipality = null
    ): string {
        return LogisticsServiceArea::fromAddress(
            $address,
            $municipality
        );
    }
}
