<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsHubTransferController extends Controller
{
    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        /*
        |--------------------------------------------------------------------------
        | Sorted parcels ready for Hub-to-Hub transfer
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
                'delivery_route_stops as origin_stop',
                function ($join) {
                    $join
                        ->on(
                            'origin_stop.marketplace_order_id',
                            '=',
                            'orders.id'
                        )
                        ->where(
                            'origin_stop.stop_key',
                            '=',
                            'origin_facility'
                        );
                }
            )
            ->join(
                'delivery_route_stops as destination_stop',
                function ($join) {
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
                'logistics_facilities as origin',
                'origin.id',
                '=',
                'origin_stop.logistics_facility_id'
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
                'sorted'
            )
            ->select([
                'parcel.id as parcel_id',
                'parcel.sorting_zone',

                'orders.id as order_id',
                'orders.order_number',
                'orders.buyer_name',

                'origin.id as origin_facility_id',
                'origin.name as origin_facility_name',

                'destination.id as destination_facility_id',
                'destination.name as destination_facility_name',
            ])
            ->orderBy('orders.order_number')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Group by Origin Hub → Destination Hub
        |--------------------------------------------------------------------------
        */

        $transferGroups = $readyParcels->groupBy(
            fn ($parcel) =>
                $parcel->origin_facility_id
                . '|'
                . $parcel->destination_facility_id
        );

        /*
        |--------------------------------------------------------------------------
        | Existing transfers
        |--------------------------------------------------------------------------
        */

        $transfers = DB::table('hub_transfers as transfer')
            ->join(
                'logistics_facilities as origin',
                'origin.id',
                '=',
                'transfer.origin_facility_id'
            )
            ->join(
                'logistics_facilities as destination',
                'destination.id',
                '=',
                'transfer.destination_facility_id'
            )
            ->where(
                'transfer.logistics_account_id',
                $logistics->id
            )
            ->select([
                'transfer.*',
                'origin.name as origin_name',
                'destination.name as destination_name',
            ])
            ->orderByDesc('transfer.id')
            ->get();

        foreach ($transfers as $transfer) {
            $transfer->parcel_count = DB::table(
                'hub_transfer_parcels'
            )
                ->where(
                    'hub_transfer_id',
                    $transfer->id
                )
                ->count();
        }

        return view(
            'logistics.hub-transfers',
            compact(
                'transferGroups',
                'transfers'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'parcel_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'parcel_ids.*' => [
                'integer',
                'distinct',
                'exists:logistics_parcels,id',
            ],

            'vehicle_type' => [
                'required',
                'string',
                'max:50',
            ],

            'vehicle_plate' => [
                'nullable',
                'string',
                'max:50',
            ],

            'driver_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $logistics =
            CurrentLogisticsAccount::resolve($request);

        DB::transaction(
            function () use (
                $validated,
                $logistics
            ): void {
                $parcelIds = collect(
                    $validated['parcel_ids']
                )
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $parcels = DB::table(
                    'logistics_parcels as parcel'
                )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'parcel.marketplace_order_id'
                    )
                    ->join(
                        'delivery_route_stops as origin_stop',
                        function ($join) {
                            $join
                                ->on(
                                    'origin_stop.marketplace_order_id',
                                    '=',
                                    'orders.id'
                                )
                                ->where(
                                    'origin_stop.stop_key',
                                    '=',
                                    'origin_facility'
                                );
                        }
                    )
                    ->join(
                        'delivery_route_stops as destination_stop',
                        function ($join) {
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
                    ->whereIn(
                        'parcel.id',
                        $parcelIds->all()
                    )
                    ->where(
                        'orders.logistics_account_id',
                        $logistics->id
                    )
                    ->where(
                        'parcel.status',
                        'sorted'
                    )
                    ->select([
                        'parcel.id',
                        'origin_stop.logistics_facility_id as origin_id',
                        'destination_stop.logistics_facility_id as destination_id',
                    ])
                    ->lockForUpdate()
                    ->get();

                if (
                    $parcels->count()
                    !== $parcelIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'parcel_ids' =>
                            'Some selected parcels are no longer available for transfer.',
                    ]);
                }

                $routePairs = $parcels
                    ->map(
                        fn ($parcel) =>
                            $parcel->origin_id
                            . '|'
                            . $parcel->destination_id
                    )
                    ->unique();

                if ($routePairs->count() !== 1) {
                    throw ValidationException::withMessages([
                        'parcel_ids' =>
                            'A Hub Transfer may only contain parcels with the same origin and destination hub.',
                    ]);
                }

                $first = $parcels->first();

                $transferId = DB::table(
                    'hub_transfers'
                )->insertGetId([
                    'logistics_account_id' =>
                        $logistics->id,

                    'origin_facility_id' =>
                        $first->origin_id,

                    'destination_facility_id' =>
                        $first->destination_id,

                    'transfer_code' =>
                        'HT-'
                        . now()->format('Ymd-His')
                        . '-'
                        . strtoupper(
                            substr(
                                bin2hex(random_bytes(3)),
                                0,
                                6
                            )
                        ),

                    'vehicle_type' =>
                        $validated['vehicle_type'],

                    'vehicle_plate' =>
                        $validated['vehicle_plate']
                        ?? null,

                    'driver_name' =>
                        $validated['driver_name']
                        ?? null,

                    'notes' =>
                        $validated['notes']
                        ?? null,

                    'status' =>
                        'preparing',

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);

                foreach ($parcelIds as $parcelId) {
                    DB::table(
                        'hub_transfer_parcels'
                    )->insert([
                        'hub_transfer_id' =>
                            $transferId,

                        'logistics_parcel_id' =>
                            $parcelId,

                        'created_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);
                }

                DB::table('logistics_parcels')
                    ->whereIn(
                        'id',
                        $parcelIds->all()
                    )
                    ->update([
                        'status' =>
                            'linehaul_assigned',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Hub Transfer created successfully.'
        );
    }

    public function dispatch(
        Request $request,
        int $transfer
    ): RedirectResponse {
        $logistics =
            CurrentLogisticsAccount::resolve($request);

        DB::transaction(
            function () use (
                $logistics,
                $transfer
            ): void {
                $hubTransfer = DB::table(
                    'hub_transfers'
                )
                    ->where(
                        'id',
                        $transfer
                    )
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $hubTransfer,
                    404
                );

                if (
                    $hubTransfer->status
                    !== 'preparing'
                ) {
                    throw ValidationException::withMessages([
                        'transfer' =>
                            'This Hub Transfer cannot be dispatched.',
                    ]);
                }

                DB::table('hub_transfers')
                    ->where(
                        'id',
                        $transfer
                    )
                    ->update([
                        'status' =>
                            'in_transit',

                        'dispatched_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                $parcelIds = DB::table(
                    'hub_transfer_parcels'
                )
                    ->where(
                        'hub_transfer_id',
                        $transfer
                    )
                    ->pluck(
                        'logistics_parcel_id'
                    );

                DB::table('logistics_parcels')
                    ->whereIn(
                        'id',
                        $parcelIds
                    )
                    ->update([
                        'status' =>
                            'linehaul_in_transit',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Hub Transfer dispatched.'
        );
    }

    public function receive(
        Request $request,
        int $transfer
    ): RedirectResponse {
        $logistics =
            CurrentLogisticsAccount::resolve($request);

        DB::transaction(
            function () use (
                $logistics,
                $transfer
            ): void {
                $hubTransfer = DB::table(
                    'hub_transfers'
                )
                    ->where(
                        'id',
                        $transfer
                    )
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $hubTransfer,
                    404
                );

                if (
                    $hubTransfer->status
                    !== 'in_transit'
                ) {
                    throw ValidationException::withMessages([
                        'transfer' =>
                            'Only an in-transit Hub Transfer may be received.',
                    ]);
                }

                DB::table('hub_transfers')
                    ->where(
                        'id',
                        $transfer
                    )
                    ->update([
                        'status' =>
                            'received',

                        'received_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                $parcelIds = DB::table(
                    'hub_transfer_parcels'
                )
                    ->where(
                        'hub_transfer_id',
                        $transfer
                    )
                    ->pluck(
                        'logistics_parcel_id'
                    );

                DB::table('logistics_parcels')
                    ->whereIn(
                        'id',
                        $parcelIds
                    )
                    ->update([
                        'status' =>
                            'destination_received',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Transfer received at destination hub.'
        );
    }
}