<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsLastMileAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        /*
        |--------------------------------------------------------------------------
        | Parcels already received at destination hub
        |--------------------------------------------------------------------------
        */

        $readyParcels = DB::table('logistics_parcels as parcel')
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
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('last_mile_assignment_orders as assigned')
                    ->whereColumn(
                        'assigned.marketplace_order_id',
                        'orders.id'
                    );
            })
            ->select([
                'parcel.id as parcel_id',

                'orders.id as order_id',
                'orders.order_number',
                'orders.buyer_name',
                'orders.buyer_address',

                // Preserve pickup Rider info.
                'orders.courier_name as pickup_rider_name',
                'orders.courier_email as pickup_rider_email',

                'destination.id as destination_facility_id',
                'destination.name as destination_facility_name',
                'destination.municipality_name as destination_municipality',
            ])
            ->orderBy('orders.order_number')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Extract barangay/service area
        |--------------------------------------------------------------------------
        */

        $readyParcels = $readyParcels->map(
            function ($parcel) {
                $parcel->barangay_name =
                    $this->extractBarangay(
                        $parcel->buyer_address
                    );

                return $parcel;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Group by Destination Hub + Barangay
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Sta. Cruz Hub
        |   └── Bubukal
        |       ├── Order 1
        |       ├── Order 2
        |       └── Order 3
        |
        | = one local Rider
        |
        */

        $deliveryGroups = $readyParcels->groupBy(
            function ($parcel): string {
                return implode('|', [
                    $parcel->destination_facility_id,
                    strtolower(
                        trim($parcel->barangay_name)
                    ),
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Approved local Riders
        |--------------------------------------------------------------------------
        */

        $approvedApplications =
            RegistrationApplication::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where('role', 'rider')
                ->where('status', 'approved')
                ->select('id');

        $riders = CourierAccount::query()
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->where('account_status', 'active')
            ->where('availability_status', 'online')
            ->whereIn(
                'registration_application_id',
                $approvedApplications
            )
            ->orderBy('barangay_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Existing Last-Mile Assignments
        |--------------------------------------------------------------------------
        */

        $assignments = DB::table(
            'last_mile_assignments as assignment'
        )
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
            ])
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

        return view(
            'logistics.last-mile-assignment',
            compact(
                'deliveryGroups',
                'riders',
                'assignments'
            )
        );
    }

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

        $orderIds = collect(
            $validated['order_ids']
        )
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values();

        DB::transaction(
            function () use (
                $validated,
                $logistics,
                $orderIds
            ): void {
                /*
                |--------------------------------------------------------------------------
                | Lock and verify orders
                |--------------------------------------------------------------------------
                */

                $orders = DB::table(
                    'marketplace_orders as orders'
                )
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
                    ->select([
                        'orders.id',
                        'orders.order_number',
                        'orders.buyer_address',

                        'parcel.id as parcel_id',

                        'destination.id as destination_facility_id',
                        'destination.name as destination_facility_name',
                        'destination.municipality_name as destination_municipality',
                    ])
                    ->lockForUpdate()
                    ->get();

                if (
                    $orders->count()
                    !== $orderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Some selected parcels are no longer available for last-mile assignment.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate assignment
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
                | Every order must have same destination hub + barangay
                |--------------------------------------------------------------------------
                */

                $groupKeys = $orders
                    ->map(function ($order): string {
                        return implode('|', [
                            $order->destination_facility_id,

                            strtolower(
                                trim(
                                    $this->extractBarangay(
                                        $order->buyer_address
                                    )
                                )
                            ),
                        ]);
                    })
                    ->unique();

                if ($groupKeys->count() !== 1) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Last-mile batches must contain orders for the same destination hub and barangay.',
                    ]);
                }

                $firstOrder = $orders->first();

                $deliveryArea =
                    $this->extractBarangay(
                        $firstOrder->buyer_address
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
                    ->where(
                        'account_status',
                        'active'
                    )
                    ->where(
                        'availability_status',
                        'online'
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $approved =
                    RegistrationApplication::query()
                        ->whereKey(
                            $rider
                                ->registration_application_id
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
                | Rider must belong to destination municipality + barangay
                |--------------------------------------------------------------------------
                */

                $riderMunicipality =
                    strtolower(
                        trim(
                            (string) $rider->municipality_name
                        )
                    );

                $destinationMunicipality =
                    strtolower(
                        trim(
                            (string)
                                $firstOrder->destination_municipality
                        )
                    );

                $riderBarangay =
                    strtolower(
                        trim(
                            (string) $rider->barangay_name
                        )
                    );

                $destinationBarangay =
                    strtolower(
                        trim($deliveryArea)
                    );

                if (
                    $riderMunicipality
                        !== $destinationMunicipality
                    || $riderBarangay
                        !== $destinationBarangay
                ) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'Select a Rider assigned to '
                            . $deliveryArea
                            . ', '
                            . $firstOrder
                                ->destination_municipality
                            . '.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | One active last-mile batch per Rider for now
                |--------------------------------------------------------------------------
                */

                $riderBusy =
                    DB::table('last_mile_assignments')
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->whereIn(
                            'status',
                            [
                                'assigned',
                                'out_for_delivery',
                            ]
                        )
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

                $assignmentId =
                    DB::table(
                        'last_mile_assignments'
                    )->insertGetId([
                        'logistics_account_id' =>
                            $logistics->id,

                        'destination_facility_id' =>
                            $firstOrder
                                ->destination_facility_id,

                        'courier_account_id' =>
                            $rider->id,

                        'assignment_code' =>
                            'LM-'
                            . now()->format('Ymd-His')
                            . '-'
                            . strtoupper(
                                substr(
                                    bin2hex(
                                        random_bytes(3)
                                    ),
                                    0,
                                    6
                                )
                            ),

                        'delivery_area' =>
                            $deliveryArea,

                        'status' =>
                            'assigned',

                        'assigned_at' =>
                            now(),

                        'created_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Attach all orders to same local Rider batch
                |--------------------------------------------------------------------------
                */

                foreach ($orderIds as $orderId) {
                    DB::table(
                        'last_mile_assignment_orders'
                    )->insert([
                        'last_mile_assignment_id' =>
                            $assignmentId,

                        'marketplace_order_id' =>
                            $orderId,

                        'created_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Parcel state
                |--------------------------------------------------------------------------
                |
                | Do NOT overwrite marketplace_orders.courier_email.
                | That remains the pickup Rider snapshot.
                |--------------------------------------------------------------------------
                */

                DB::table('logistics_parcels')
                    ->whereIn(
                        'marketplace_order_id',
                        $orderIds->all()
                    )
                    ->update([
                        'status' =>
                            'last_mile_assigned',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Last-mile Rider assigned successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Temporary V1 address parser
    |--------------------------------------------------------------------------
    |
    | "Barangay Bubukal, Santa Cruz, Laguna"
    | becomes:
    | "Bubukal"
    |
    */

    private function extractBarangay(
        ?string $address
    ): string {
        $address = trim(
            (string) $address
        );

        if (
            preg_match(
                '/Barangay\s+([^,]+)/i',
                $address,
                $matches
            )
        ) {
            return trim(
                $matches[1]
            );
        }

        $firstPart = trim(
            explode(',', $address)[0] ?? ''
        );

        return preg_replace(
            '/^Brgy\.?\s*/i',
            '',
            $firstPart
        ) ?: 'Unknown Area';
    }
}