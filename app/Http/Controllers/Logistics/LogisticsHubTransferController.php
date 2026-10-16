<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LogisticsHubTransferController extends Controller
{
    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Hub Transfers
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics =
            $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Sorted parcels waiting for linehaul
        |--------------------------------------------------------------------------
        */

        $readyParcels =
            LogisticsParcel::query()
                ->with([
                    'order.seller',
                    'order.deliveryRouteStops.facility',
                ])
                ->where(
                    'status',
                    'sorted'
                )
                ->whereHas(
                    'order',
                    fn ($query) =>
                        $query->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                )
                ->whereNotIn(
                    'id',
                    DB::table(
                        'hub_transfer_parcels'
                    )->select(
                        'logistics_parcel_id'
                    )
                )
                ->oldest('sorted_at')
                ->oldest('id')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Group by Origin Hub → Destination Hub
        |--------------------------------------------------------------------------
        */

        $readyGroups =
            $readyParcels
                ->map(
                    function (
                        LogisticsParcel $parcel
                    ): array {
                        $order =
                            $parcel->order;

                        $stops =
                            $order
                                ?->deliveryRouteStops
                                ?->keyBy('stop_key')
                            ?? collect();

                        $origin =
                            $stops->get(
                                'origin_facility'
                            );

                        $destination =
                            $stops->get(
                                'destination_facility'
                            );

                        return [
                            'parcel' =>
                                $parcel,

                            'order' =>
                                $order,

                            'origin_stop' =>
                                $origin,

                            'destination_stop' =>
                                $destination,

                            'origin_facility' =>
                                $origin?->facility,

                            'destination_facility' =>
                                $destination?->facility,
                        ];
                    }
                )
                ->filter(
                    fn (array $row): bool =>
                        filled(
                            $row['origin_stop']
                                ?->logistics_facility_id
                        )
                        &&
                        filled(
                            $row['destination_stop']
                                ?->logistics_facility_id
                        )
                        &&
                        (
                            (int) $row['origin_stop']
                                ->logistics_facility_id
                            !==
                            (int) $row['destination_stop']
                                ->logistics_facility_id
                        )
                )
                ->groupBy(
                    function (array $row): string {
                        return implode(
                            ':',
                            [
                                (int) $row[
                                    'origin_stop'
                                ]->logistics_facility_id,

                                (int) $row[
                                    'destination_stop'
                                ]->logistics_facility_id,
                            ]
                        );
                    }
                )
                ->map(
                    function (
                        Collection $rows,
                        string $key
                    ): array {
                        $first =
                            $rows->first();

                        $parcels =
                            $rows
                                ->pluck('parcel')
                                ->values();

                        $orders =
                            $rows
                                ->pluck('order')
                                ->filter()
                                ->values();

                        $origin =
                            $first[
                                'origin_facility'
                            ];

                        $destination =
                            $first[
                                'destination_facility'
                            ];

                        return [
                            'key' =>
                                $key,

                            'origin_facility_id' =>
                                (int) $first[
                                    'origin_stop'
                                ]->logistics_facility_id,

                            'destination_facility_id' =>
                                (int) $first[
                                    'destination_stop'
                                ]->logistics_facility_id,

                            'origin_name' =>
                                $origin?->name
                                ?: $first[
                                    'origin_stop'
                                ]->name,

                            'origin_code' =>
                                $origin?->code,

                            'origin_municipality' =>
                                $origin
                                    ?->municipality_name,

                            'destination_name' =>
                                $destination?->name
                                ?: $first[
                                    'destination_stop'
                                ]->name,

                            'destination_code' =>
                                $destination?->code,

                            'destination_municipality' =>
                                $destination
                                    ?->municipality_name,

                            'parcels' =>
                                $parcels,

                            'orders' =>
                                $orders,

                            'parcel_count' =>
                                $parcels->count(),

                            'seller_count' =>
                                $orders
                                    ->pluck(
                                        'seller_account_id'
                                    )
                                    ->filter()
                                    ->unique()
                                    ->count(),

                            'buyer_count' =>
                                $orders
                                    ->pluck(
                                        'buyer_name'
                                    )
                                    ->filter()
                                    ->unique()
                                    ->count(),

                            'oldest_sorted_at' =>
                                $parcels
                                    ->pluck(
                                        'sorted_at'
                                    )
                                    ->filter()
                                    ->sort()
                                    ->first(),
                        ];
                    }
                )
                ->sortBy(
                    fn (array $group) =>
                        $group[
                            'oldest_sorted_at'
                        ]
                        ?: now()
                )
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Existing transfers
        |--------------------------------------------------------------------------
        */

        $transfers =
            DB::table(
                'hub_transfers as transfer'
            )
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
                    'origin.code as origin_code',
                    'origin.municipality_name as origin_municipality',

                    'destination.name as destination_name',
                    'destination.code as destination_code',
                    'destination.municipality_name as destination_municipality',
                ])
                ->orderByRaw(
                    "
                    CASE transfer.status
                        WHEN 'in_transit' THEN 1
                        WHEN 'preparing' THEN 2
                        WHEN 'received' THEN 3
                        ELSE 4
                    END
                    "
                )
                ->orderByDesc(
                    'transfer.id'
                )
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Parcels belonging to existing transfers
        |--------------------------------------------------------------------------
        */

        $transferParcels =
            collect();

        if ($transfers->isNotEmpty()) {
            $transferParcels =
                DB::table(
                    'hub_transfer_parcels as item'
                )
                    ->join(
                        'logistics_parcels as parcel',
                        'parcel.id',
                        '=',
                        'item.logistics_parcel_id'
                    )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'parcel.marketplace_order_id'
                    )
                    ->whereIn(
                        'item.hub_transfer_id',
                        $transfers
                            ->pluck('id')
                            ->all()
                    )
                    ->select([
                        'item.hub_transfer_id',
                        'item.logistics_parcel_id',

                        'parcel.status as parcel_status',
                        'parcel.sorting_zone',

                        'orders.id as order_id',
                        'orders.order_number',
                        'orders.buyer_name',
                        'orders.buyer_address',
                        'orders.pickup_name',
                        'orders.total',
                    ])
                    ->orderBy(
                        'orders.order_number'
                    )
                    ->get()
                    ->groupBy(
                        'hub_transfer_id'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */

        $today =
            now('Asia/Manila')
                ->toDateString();

        $stats = [
            'ready_parcels' =>
                $readyGroups->sum(
                    'parcel_count'
                ),

            'ready_routes' =>
                $readyGroups->count(),

            'preparing' =>
                $transfers
                    ->where(
                        'status',
                        'preparing'
                    )
                    ->count(),

            'in_transit' =>
                $transfers
                    ->where(
                        'status',
                        'in_transit'
                    )
                    ->count(),

            'received_today' =>
                $transfers
                    ->filter(
                        function ($transfer) use (
                            $today
                        ): bool {
                            if (
                                $transfer->status
                                !== 'received'
                                ||
                                blank(
                                    $transfer
                                        ->received_at
                                )
                            ) {
                                return false;
                            }

                            return substr(
                                (string) $transfer
                                    ->received_at,
                                0,
                                10
                            ) === $today;
                        }
                    )
                    ->count(),
        ];

        return view(
            'logistics.hub-transfers',
            compact(
                'readyGroups',
                'transfers',
                'transferParcels',
                'stats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Transfer Manifest
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate([
                'parcel_ids' => [
                    'required',
                    'array',
                    'min:1',
                    'max:300',
                ],

                'parcel_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:logistics_parcels,id',
                ],

                'vehicle_type' => [
                    'required',
                    'string',
                    'max:80',
                ],

                'vehicle_plate' => [
                    'nullable',
                    'string',
                    'max:40',
                ],

                'driver_name' => [
                    'required',
                    'string',
                    'max:140',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:1500',
                ],
            ]);

        $logistics =
            $this->orderAccess
                ->prepare($request);

        $parcelIds =
            collect(
                $validated['parcel_ids']
            )
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->unique()
                ->sort()
                ->values();

        $transferCode =
            DB::transaction(
                function () use (
                    $parcelIds,
                    $validated,
                    $logistics
                ): string {
                    $parcels =
                        LogisticsParcel::query()
                            ->with([
                                'order.deliveryRouteStops.facility',
                            ])
                            ->whereIn(
                                'id',
                                $parcelIds
                                    ->all()
                            )
                            ->where(
                                'status',
                                'sorted'
                            )
                            ->whereHas(
                                'order',
                                fn ($query) =>
                                    $query->where(
                                        'logistics_account_id',
                                        $logistics->id
                                    )
                            )
                            ->orderBy('id')
                            ->lockForUpdate()
                            ->get();

                    if (
                        $parcels->count()
                        !==
                        $parcelIds->count()
                    ) {
                        throw ValidationException::withMessages([
                            'parcel_ids' =>
                                'One or more parcels are no longer available for Hub Transfer. Refresh the page.',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Parcels must share one origin and one destination
                    |--------------------------------------------------------------------------
                    */

                    $routePairs =
                        $parcels
                            ->map(
                                function (
                                    LogisticsParcel $parcel
                                ): string {
                                    $stops =
                                        $parcel
                                            ->order
                                            ?->deliveryRouteStops
                                            ?->keyBy(
                                                'stop_key'
                                            )
                                        ?? collect();

                                    $origin =
                                        $stops->get(
                                            'origin_facility'
                                        );

                                    $destination =
                                        $stops->get(
                                            'destination_facility'
                                        );

                                    if (
                                        ! $origin
                                        ||
                                        ! $destination
                                        ||
                                        ! $origin
                                            ->logistics_facility_id
                                        ||
                                        ! $destination
                                            ->logistics_facility_id
                                    ) {
                                        throw ValidationException::withMessages([
                                            'parcel_ids' =>
                                                'One or more parcels do not have a complete Hub route.',
                                        ]);
                                    }

                                    if (
                                        (int) $origin
                                            ->logistics_facility_id
                                        ===
                                        (int) $destination
                                            ->logistics_facility_id
                                    ) {
                                        throw ValidationException::withMessages([
                                            'parcel_ids' =>
                                                'A Hub Transfer requires different origin and destination facilities.',
                                        ]);
                                    }

                                    return
                                        (int) $origin
                                            ->logistics_facility_id
                                        . ':'
                                        . (int) $destination
                                            ->logistics_facility_id;
                                }
                            )
                            ->unique()
                            ->values();

                    if (
                        $routePairs->count()
                        !== 1
                    ) {
                        throw ValidationException::withMessages([
                            'parcel_ids' =>
                                'All parcels in one manifest must have the same origin and destination hubs.',
                        ]);
                    }

                    [$originFacilityId, $destinationFacilityId] =
                        array_map(
                            'intval',
                            explode(
                                ':',
                                $routePairs->first()
                            )
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Facilities must belong to Logistics account
                    |--------------------------------------------------------------------------
                    */

                    $facilityCount =
                        DB::table(
                            'logistics_facilities'
                        )
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->whereIn(
                                'id',
                                [
                                    $originFacilityId,
                                    $destinationFacilityId,
                                ]
                            )
                            ->count();

                    if (
                        $facilityCount !== 2
                    ) {
                        throw ValidationException::withMessages([
                            'parcel_ids' =>
                                'The selected Hub route does not belong to this Logistics network.',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Create transfer
                    |--------------------------------------------------------------------------
                    */

                    $transferCode =
                        $this
                            ->generateTransferCode();

                    $transferId =
                        DB::table(
                            'hub_transfers'
                        )
                            ->insertGetId([
                                'logistics_account_id' =>
                                    $logistics->id,

                                'origin_facility_id' =>
                                    $originFacilityId,

                                'destination_facility_id' =>
                                    $destinationFacilityId,

                                'transfer_code' =>
                                    $transferCode,

                                'vehicle_type' =>
                                    trim(
                                        $validated[
                                            'vehicle_type'
                                        ]
                                    ),

                                'vehicle_plate' =>
                                    filled(
                                        $validated[
                                            'vehicle_plate'
                                        ] ?? null
                                    )
                                        ? strtoupper(
                                            trim(
                                                $validated[
                                                    'vehicle_plate'
                                                ]
                                            )
                                        )
                                        : null,

                                'driver_name' =>
                                    trim(
                                        $validated[
                                            'driver_name'
                                        ]
                                    ),

                                'status' =>
                                    'preparing',

                                'notes' =>
                                    filled(
                                        $validated[
                                            'notes'
                                        ] ?? null
                                    )
                                        ? trim(
                                            $validated[
                                                'notes'
                                            ]
                                        )
                                        : null,

                                'dispatched_at' =>
                                    null,

                                'received_at' =>
                                    null,

                                'created_at' =>
                                    now(),

                                'updated_at' =>
                                    now(),
                            ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Attach parcels
                    |--------------------------------------------------------------------------
                    */

                    $pivotRows =
                        $parcelIds
                            ->map(
                                fn (int $parcelId): array => [
                                    'hub_transfer_id' =>
                                        $transferId,

                                    'logistics_parcel_id' =>
                                        $parcelId,

                                    'created_at' =>
                                        now(),

                                    'updated_at' =>
                                        now(),
                                ]
                            )
                            ->all();

                    DB::table(
                        'hub_transfer_parcels'
                    )->insert(
                        $pivotRows
                    );

                    LogisticsParcel::query()
                        ->whereIn(
                            'id',
                            $parcelIds
                                ->all()
                        )
                        ->update([
                            'status' =>
                                'linehaul_assigned',

                            'updated_at' =>
                                now(),
                        ]);

                    return $transferCode;
                },
                3
            );

        return back()->with(
            'success',
            'Transfer '
                . $transferCode
                . ' created successfully. Review the manifest before dispatch.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dispatch Transfer
    |--------------------------------------------------------------------------
    */

    public function dispatch(
        Request $request,
        int $transfer
    ): RedirectResponse {
        $logistics =
            $this->orderAccess
                ->prepare($request);

        $code =
            DB::transaction(
                function () use (
                    $transfer,
                    $logistics
                ): string {
                    $record =
                        DB::table(
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

                    if (! $record) {
                        abort(404);
                    }

                    if (
                        $record->status
                        !== 'preparing'
                    ) {
                        throw ValidationException::withMessages([
                            'transfer' =>
                                'Only a preparing transfer can be dispatched.',
                        ]);
                    }

                    $parcelIds =
                        DB::table(
                            'hub_transfer_parcels'
                        )
                            ->where(
                                'hub_transfer_id',
                                $record->id
                            )
                            ->pluck(
                                'logistics_parcel_id'
                            );

                    if (
                        $parcelIds->isEmpty()
                    ) {
                        throw ValidationException::withMessages([
                            'transfer' =>
                                'This transfer manifest does not contain any parcels.',
                        ]);
                    }

                    $validCount =
                        LogisticsParcel::query()
                            ->whereIn(
                                'id',
                                $parcelIds
                                    ->all()
                            )
                            ->where(
                                'status',
                                'linehaul_assigned'
                            )
                            ->lockForUpdate()
                            ->count();

                    if (
                        $validCount
                        !==
                        $parcelIds->count()
                    ) {
                        throw ValidationException::withMessages([
                            'transfer' =>
                                'One or more manifest parcels are not ready for dispatch.',
                        ]);
                    }

                    DB::table(
                        'hub_transfers'
                    )
                        ->where(
                            'id',
                            $record->id
                        )
                        ->update([
                            'status' =>
                                'in_transit',

                            'dispatched_at' =>
                                now(),

                            'updated_at' =>
                                now(),
                        ]);

                    LogisticsParcel::query()
                        ->whereIn(
                            'id',
                            $parcelIds
                                ->all()
                        )
                        ->update([
                            'status' =>
                                'linehaul_in_transit',

                            'updated_at' =>
                                now(),
                        ]);

                    return
                        $record
                            ->transfer_code;
                },
                3
            );

        return back()->with(
            'success',
            'Transfer '
                . $code
                . ' has departed the origin hub.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destination Hub Receipt
    |--------------------------------------------------------------------------
    */

    public function receive(
        Request $request,
        int $transfer
    ): RedirectResponse {
        $logistics =
            $this->orderAccess
                ->prepare($request);

        $code =
            DB::transaction(
                function () use (
                    $transfer,
                    $logistics
                ): string {
                    $record =
                        DB::table(
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

                    if (! $record) {
                        abort(404);
                    }

                    if (
                        $record->status
                        !== 'in_transit'
                    ) {
                        throw ValidationException::withMessages([
                            'transfer' =>
                                'Only an in-transit Hub Transfer can be received.',
                        ]);
                    }

                    $parcelIds =
                        DB::table(
                            'hub_transfer_parcels'
                        )
                            ->where(
                                'hub_transfer_id',
                                $record->id
                            )
                            ->pluck(
                                'logistics_parcel_id'
                            );

                    $validCount =
                        LogisticsParcel::query()
                            ->whereIn(
                                'id',
                                $parcelIds
                                    ->all()
                            )
                            ->where(
                                'status',
                                'linehaul_in_transit'
                            )
                            ->lockForUpdate()
                            ->count();

                    if (
                        $validCount
                        !==
                        $parcelIds->count()
                    ) {
                        throw ValidationException::withMessages([
                            'transfer' =>
                                'One or more parcels are not in the expected linehaul state.',
                        ]);
                    }

                    DB::table(
                        'hub_transfers'
                    )
                        ->where(
                            'id',
                            $record->id
                        )
                        ->update([
                            'status' =>
                                'received',

                            'received_at' =>
                                now(),

                            'updated_at' =>
                                now(),
                        ]);

                    LogisticsParcel::query()
                        ->whereIn(
                            'id',
                            $parcelIds
                                ->all()
                        )
                        ->update([
                            'status' =>
                                'destination_received',

                            'updated_at' =>
                                now(),
                        ]);

                    return
                        $record
                            ->transfer_code;
                },
                3
            );

        return back()->with(
            'success',
            'Transfer '
                . $code
                . ' was received at the destination hub. Parcels are ready for Last-Mile Assignment.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer Code
    |--------------------------------------------------------------------------
    */

    private function generateTransferCode(): string
    {
        do {
            $code =
                'HT-'
                . now()
                    ->format('ymd')
                . '-'
                . Str::upper(
                    Str::random(5)
                );
        } while (
            DB::table(
                'hub_transfers'
            )
                ->where(
                    'transfer_code',
                    $code
                )
                ->exists()
        );

        return $code;
    }
}