<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourierLastMileController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $rider = $this->currentRider($request);

        $assignments = DB::table('last_mile_assignments as assignment')
            ->join(
                'logistics_facilities as facility',
                'facility.id',
                '=',
                'assignment.destination_facility_id'
            )
            ->where(
                'assignment.courier_account_id',
                $rider->id
            )
            ->whereIn(
                'assignment.status',
                [
                    'assigned',
                    'out_for_delivery',
                    'completed',
                ]
            )
            ->select([
                'assignment.*',
                'facility.name as facility_name',
                'facility.address as facility_address',
                'facility.municipality_name as facility_municipality',
            ])
            ->orderByRaw("
                CASE
                    WHEN assignment.status = 'out_for_delivery' THEN 1
                    WHEN assignment.status = 'assigned' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('assignment.id')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->orders = DB::table(
                'last_mile_assignment_orders as link'
            )
                ->join(
                    'marketplace_orders as orders',
                    'orders.id',
                    '=',
                    'link.marketplace_order_id'
                )
                ->leftJoin(
                    'logistics_parcels as parcel',
                    'parcel.marketplace_order_id',
                    '=',
                    'orders.id'
                )
                ->where(
                    'link.last_mile_assignment_id',
                    $assignment->id
                )
                ->select([
                    'orders.id',
                    'orders.order_number',
                    'orders.buyer_name',
                    'orders.buyer_phone',
                    'orders.buyer_address',
                    'orders.status as order_status',

                    'parcel.status as parcel_status',
                ])
                ->orderBy('orders.order_number')
                ->get();
        }

        return view(
            'courier.last-mile',
            compact(
                'assignments',
                'rider'
            )
        );
    }

    public function start(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $rider = $this->currentRider($request);

        DB::transaction(
            function () use (
                $rider,
                $assignment
            ): void {
                $lastMile = DB::table(
                    'last_mile_assignments'
                )
                    ->where('id', $assignment)
                    ->where(
                        'courier_account_id',
                        $rider->id
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                if (
                    $lastMile->status
                    !== 'assigned'
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'This last-mile assignment cannot be started.',
                    ]);
                }

                $orderIds = DB::table(
                    'last_mile_assignment_orders'
                )
                    ->where(
                        'last_mile_assignment_id',
                        $assignment
                    )
                    ->pluck(
                        'marketplace_order_id'
                    );

                DB::table(
                    'last_mile_assignments'
                )
                    ->where(
                        'id',
                        $assignment
                    )
                    ->update([
                        'status' =>
                            'out_for_delivery',

                        'started_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                DB::table(
                    'logistics_parcels'
                )
                    ->whereIn(
                        'marketplace_order_id',
                        $orderIds
                    )
                    ->where(
                        'status',
                        'last_mile_assigned'
                    )
                    ->update([
                        'status' =>
                            'out_for_delivery',

                        'updated_at' =>
                            now(),
                    ]);

                /*
                 * Keep marketplace order in transit until
                 * the Rider actually reaches the Buyer.
                 */
                DB::table(
                    'marketplace_orders'
                )
                    ->whereIn(
                        'id',
                        $orderIds
                    )
                    ->whereNotIn(
                        'status',
                        [
                            'arrived_buyer',
                            'delivered',
                        ]
                    )
                    ->update([
                        'status' =>
                            'in_transit',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Last-mile delivery route started.'
        );
    }

    public function arrived(
        Request $request,
        int $assignment,
        int $order
    ): RedirectResponse {
        $rider = $this->currentRider($request);

        DB::transaction(
            function () use (
                $rider,
                $assignment,
                $order
            ): void {
                $lastMile = DB::table(
                    'last_mile_assignments'
                )
                    ->where('id', $assignment)
                    ->where(
                        'courier_account_id',
                        $rider->id
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                if (
                    $lastMile->status
                    !== 'out_for_delivery'
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'Start the last-mile delivery route first.',
                    ]);
                }

                $belongsToAssignment =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->exists();

                abort_unless(
                    $belongsToAssignment,
                    404
                );

                $parcel = DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $parcel,
                    404
                );

                if (
                    ! in_array(
                        $parcel->status,
                        [
                            'out_for_delivery',
                            'arrived_buyer',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This parcel is not currently out for delivery.',
                    ]);
                }

                DB::table(
                    'marketplace_orders'
                )
                    ->where(
                        'id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'arrived_buyer',

                        'arrived_buyer_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'arrived_buyer',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Arrival at Buyer confirmed.'
        );
    }

    public function delivered(
        Request $request,
        int $assignment,
        int $order
    ): RedirectResponse {
        $rider = $this->currentRider($request);

        DB::transaction(
            function () use (
                $rider,
                $assignment,
                $order
            ): void {
                $lastMile = DB::table(
                    'last_mile_assignments'
                )
                    ->where('id', $assignment)
                    ->where(
                        'courier_account_id',
                        $rider->id
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                $belongsToAssignment =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->exists();

                abort_unless(
                    $belongsToAssignment,
                    404
                );

                $parcel = DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $parcel,
                    404
                );

                if (
                    $parcel->status
                    !== 'arrived_buyer'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Confirm arrival at the Buyer before marking the parcel delivered.',
                    ]);
                }

                DB::table(
                    'marketplace_orders'
                )
                    ->where(
                        'id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'delivered',

                        'delivered_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'delivered',

                        'updated_at' =>
                            now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Complete assignment when every parcel is delivered
                |--------------------------------------------------------------------------
                */

                $orderIds = DB::table(
                    'last_mile_assignment_orders'
                )
                    ->where(
                        'last_mile_assignment_id',
                        $assignment
                    )
                    ->pluck(
                        'marketplace_order_id'
                    );

                $remaining =
                    DB::table(
                        'marketplace_orders'
                    )
                        ->whereIn(
                            'id',
                            $orderIds
                        )
                        ->where(
                            'status',
                            '!=',
                            'delivered'
                        )
                        ->count();

                if ($remaining === 0) {
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'id',
                            $assignment
                        )
                        ->update([
                            'status' =>
                                'completed',

                            'completed_at' =>
                                now(),

                            'updated_at' =>
                                now(),
                        ]);
                }
            }
        );

        return back()->with(
            'success',
            'Parcel delivered successfully.'
        );
    }

    private function currentRider(
        Request $request
    ): object {
        abort_unless(
            $request->session()->get(
                'is_courier'
            ),
            403
        );

        $email = strtolower(
            trim(
                (string) $request
                    ->session()
                    ->get(
                        'courier_email',
                        ''
                    )
            )
        );

        $rider = DB::table(
            'courier_accounts'
        )
            ->whereRaw(
                'LOWER(email) = ?',
                [$email]
            )
            ->first();

        abort_unless(
            $rider,
            403
        );

        return $rider;
    }
}