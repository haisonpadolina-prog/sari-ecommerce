<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsHub;
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
        private readonly LogisticsNotificationService $notifications,
        private readonly MarketplaceOrderWorkflowService $workflow,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Transfer Board: preserve existing Blade data contracts and hub scoping
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);
        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);
        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        $readyQuery = LogisticsParcel::query()
            ->with(['order.seller', 'order.deliveryRouteStops.facility'])
            ->where('status', 'sorted')
            ->whereHas('order', fn ($query) => $query->where(
                'logistics_account_id', $logistics->id
            ))
            ->whereNotIn('id', DB::table('hub_transfer_parcels')
                ->select('logistics_parcel_id'));

        if ($hubFacility) {
            $readyQuery->whereHas('order.deliveryRouteStops', function ($query) use ($hubFacility): void {
                $query->where('stop_key', 'origin_facility')
                    ->where('logistics_facility_id', $hubFacility->id);
            });
        }

        $readyParcels = $readyQuery->oldest('sorted_at')->oldest('id')->get();

        $readyGroups = $readyParcels
            ->map(function (LogisticsParcel $parcel): array {
                $order = $parcel->order;
                $stops = $order?->deliveryRouteStops?->keyBy('stop_key') ?? collect();
                $origin = $stops->get('origin_facility');
                $destination = $stops->get('destination_facility');

                return [
                    'parcel' => $parcel,
                    'order' => $order,
                    'origin_stop' => $origin,
                    'destination_stop' => $destination,
                    'origin_facility' => $origin?->facility,
                    'destination_facility' => $destination?->facility,
                ];
            })
            ->filter(fn (array $row): bool => filled($row['origin_stop']?->logistics_facility_id)
                && filled($row['destination_stop']?->logistics_facility_id)
                && (int) $row['origin_stop']->logistics_facility_id
                    !== (int) $row['destination_stop']->logistics_facility_id
            )
            ->groupBy(fn (array $row): string => (int) $row['origin_stop']->logistics_facility_id
                .':'.(int) $row['destination_stop']->logistics_facility_id
            )
            ->map(function (Collection $rows, string $key): array {
                $first = $rows->first();
                $parcels = $rows->pluck('parcel')->values();
                $orders = $rows->pluck('order')->filter()->values();
                $origin = $first['origin_facility'];
                $destination = $first['destination_facility'];

                return [
                    'key' => $key,
                    'origin_facility_id' => (int) $first['origin_stop']->logistics_facility_id,
                    'destination_facility_id' => (int) $first['destination_stop']->logistics_facility_id,
                    'origin_name' => $origin?->name ?: $first['origin_stop']->name,
                    'origin_code' => $origin?->code,
                    'origin_municipality' => $origin?->municipality_name,
                    'destination_name' => $destination?->name ?: $first['destination_stop']->name,
                    'destination_code' => $destination?->code,
                    'destination_municipality' => $destination?->municipality_name,
                    'parcels' => $parcels,
                    'orders' => $orders,
                    'parcel_count' => $parcels->count(),
                    'seller_count' => $orders->pluck('seller_account_id')->filter()->unique()->count(),
                    'buyer_count' => $orders->pluck('buyer_name')->filter()->unique()->count(),
                    'oldest_sorted_at' => $parcels->pluck('sorted_at')->filter()->sort()->first(),
                ];
            })
            ->sortBy(fn (array $group) => $group['oldest_sorted_at'] ?: now())
            ->values();

        $transfersQuery = DB::table('hub_transfers as transfer')
            ->join('logistics_facilities as origin', 'origin.id', '=', 'transfer.origin_facility_id')
            ->join('logistics_facilities as destination', 'destination.id', '=', 'transfer.destination_facility_id')
            ->where('transfer.logistics_account_id', $logistics->id);

        if ($hubFacility) {
            $transfersQuery->where(function ($query) use ($hubFacility): void {
                $query->where('transfer.origin_facility_id', $hubFacility->id)
                    ->orWhere('transfer.destination_facility_id', $hubFacility->id);
            });
        }

        $transfers = $transfersQuery
            ->select([
                'transfer.*',
                'origin.name as origin_name',
                'origin.code as origin_code',
                'origin.municipality_name as origin_municipality',
                'destination.name as destination_name',
                'destination.code as destination_code',
                'destination.municipality_name as destination_municipality',
            ])
            ->orderByRaw("CASE transfer.status WHEN 'in_transit' THEN 1 WHEN 'preparing' THEN 2 WHEN 'received' THEN 3 ELSE 4 END")
            ->orderByDesc('transfer.id')
            ->get();

        $transferParcels = collect();

        if ($transfers->isNotEmpty()) {
            $transferParcels = DB::table('hub_transfer_parcels as item')
                ->join('logistics_parcels as parcel', 'parcel.id', '=', 'item.logistics_parcel_id')
                ->join('marketplace_orders as orders', 'orders.id', '=', 'parcel.marketplace_order_id')
                ->whereIn('item.hub_transfer_id', $transfers->pluck('id')->all())
                ->select([
                    'item.hub_transfer_id', 'item.logistics_parcel_id',
                    'parcel.status as parcel_status', 'parcel.sorting_zone',
                    'orders.id as order_id', 'orders.order_number',
                    'orders.buyer_name', 'orders.buyer_address',
                    'orders.pickup_name', 'orders.total',
                ])
                ->orderBy('orders.order_number')
                ->get()
                ->groupBy('hub_transfer_id');
        }

        $today = now('Asia/Manila')->toDateString();

        $stats = [
            'ready_parcels' => $readyGroups->sum('parcel_count'),
            'ready_routes' => $readyGroups->count(),
            'preparing' => $transfers->where('status', 'preparing')->count(),
            'in_transit' => $transfers->where('status', 'in_transit')->count(),
            'received_today' => $transfers->filter(fn ($transfer): bool => $transfer->status === 'received'
                && filled($transfer->received_at)
                && substr((string) $transfer->received_at, 0, 10) === $today
            )->count(),
        ];

        return view('logistics.hub-transfers', compact(
            'readyGroups', 'transfers', 'transferParcels', 'stats',
            'hubFacility', 'isHubOperator'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Transfer Manifest: sorted -> linehaul_assigned
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parcel_ids' => ['required', 'array', 'min:1', 'max:300'],
            'parcel_ids.*' => ['required', 'integer', 'distinct', 'exists:logistics_parcels,id'],
            'vehicle_type' => ['required', 'string', 'max:80'],
            'vehicle_plate' => ['nullable', 'string', 'max:40'],
            'driver_name' => ['required', 'string', 'max:140'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $logistics = $this->orderAccess->prepare($request);
        $hubFacility = CurrentLogisticsHub::isHubOperator($request)
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        $parcelIds = collect($validated['parcel_ids'])
            ->map(fn ($id): int => (int) $id)->unique()->sort()->values();

        $transferCode = DB::transaction(function () use (
            $request, $parcelIds, $validated, $logistics, $hubFacility
        ): string {
            $parcels = LogisticsParcel::query()
                ->with(['order.deliveryRouteStops.facility'])
                ->whereIn('id', $parcelIds->all())
                ->where('status', 'sorted')
                ->whereHas('order', fn ($query) => $query->where(
                    'logistics_account_id', $logistics->id
                ))
                ->orderBy('id')->lockForUpdate()->get();

            if ($parcels->count() !== $parcelIds->count()) {
                throw ValidationException::withMessages([
                    'parcel_ids' => 'One or more parcels are no longer available for Hub Transfer. Refresh the page.',
                ]);
            }

            $routePairs = $parcels->map(function (LogisticsParcel $parcel): string {
                $stops = $parcel->order?->deliveryRouteStops?->keyBy('stop_key') ?? collect();
                $origin = $stops->get('origin_facility');
                $destination = $stops->get('destination_facility');

                if (
                    ! $origin?->logistics_facility_id
                    || ! $destination?->logistics_facility_id
                ) {
                    throw ValidationException::withMessages([
                        'parcel_ids' => 'One or more parcels do not have a complete Hub route.',
                    ]);
                }

                if ((int) $origin->logistics_facility_id === (int) $destination->logistics_facility_id) {
                    throw ValidationException::withMessages([
                        'parcel_ids' => 'A Hub Transfer requires different origin and destination facilities.',
                    ]);
                }

                return (int) $origin->logistics_facility_id.':'.(int) $destination->logistics_facility_id;
            })->unique()->values();

            if ($routePairs->count() !== 1) {
                throw ValidationException::withMessages([
                    'parcel_ids' => 'All parcels in one manifest must have the same origin and destination hubs.',
                ]);
            }

            [$originFacilityId, $destinationFacilityId] = array_map(
                'intval', explode(':', $routePairs->first())
            );

            if ($hubFacility) {
                $currentHub = CurrentLogisticsHub::requireFacility($request);
                abort_unless(
                    (int) $currentHub->id === (int) $hubFacility->id
                    && $originFacilityId === (int) $currentHub->id,
                    403,
                    'Only the operator of the origin hub may create this Hub Transfer.'
                );
            }

            $facilities = DB::table('logistics_facilities')
                ->where('logistics_account_id', $logistics->id)
                ->whereIn('id', [$originFacilityId, $destinationFacilityId])
                ->where('is_active', true)
                ->orderBy('id')->lockForUpdate()->get(['id', 'name'])
                ->keyBy('id');

            if ($facilities->count() !== 2) {
                throw ValidationException::withMessages([
                    'parcel_ids' => 'The origin and destination hubs must be active and belong to this Logistics network.',
                ]);
            }

            $transferCode = $this->generateTransferCode();
            $transferId = DB::table('hub_transfers')->insertGetId([
                'logistics_account_id' => $logistics->id,
                'origin_facility_id' => $originFacilityId,
                'destination_facility_id' => $destinationFacilityId,
                'transfer_code' => $transferCode,
                'vehicle_type' => trim($validated['vehicle_type']),
                'vehicle_plate' => filled($validated['vehicle_plate'] ?? null)
                    ? strtoupper(trim($validated['vehicle_plate'])) : null,
                'driver_name' => trim($validated['driver_name']),
                'status' => 'preparing',
                'notes' => filled($validated['notes'] ?? null)
                    ? trim($validated['notes']) : null,
                'dispatched_at' => null,
                'received_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('hub_transfer_parcels')->insert(
                $parcelIds->map(fn (int $id): array => [
                    'hub_transfer_id' => $transferId,
                    'logistics_parcel_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );

            $updated = LogisticsParcel::query()
                ->whereIn('id', $parcelIds->all())
                ->where('status', 'sorted')
                ->update(['status' => 'linehaul_assigned', 'updated_at' => now()]);

            if ($updated !== $parcelIds->count()) {
                throw ValidationException::withMessages([
                    'parcel_ids' => 'A parcel changed during transfer creation. Refresh and retry.',
                ]);
            }

            /*
             * A prepared manifest is an origin-hub action, not a departure.
             * Notify only Manager and the origin hub at this stage.
             */

            $data = $this->transferNotificationData(
                $transferId, $transferCode, $originFacilityId,
                $destinationFacilityId, $parcelIds->count()
            );

            $message = 'Transfer '.$transferCode.' with '.$parcelIds->count()
                .' parcel(s) was prepared at '.$facilities[$originFacilityId]->name
                .'. Review the manifest before dispatch.';

            $eventKey = 'hub_transfer_created:transfer:'.$transferId;

            $this->notifications->notifyManager(
                (int) $logistics->id,
                $eventKey,
                'hub_transfer_created',
                'Hub Transfer manifest created',
                $message,
                'logistics.hub-transfers',
                $data
            );

            $this->notifications->notifyFacilityOperators(
                (int) $logistics->id,
                $originFacilityId,
                $eventKey,
                'hub_transfer_created',
                'Transfer manifest ready for dispatch',
                $message,
                'logistics.hub-transfers',
                $data
            );

            return $transferCode;
        }, 3);

        return back()->with(
            'success',
            'Transfer '.$transferCode
                .' created successfully. Review the manifest before dispatch.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dispatch: preparing -> in_transit
    | Alert the receiving destination hub.
    |--------------------------------------------------------------------------
    */

    public function dispatch(Request $request, int $transfer): RedirectResponse
    {
        $logistics = $this->orderAccess->prepare($request);

        $hubFacility = CurrentLogisticsHub::isHubOperator($request)
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        $code = DB::transaction(function () use (
            $request, $transfer, $logistics, $hubFacility
        ): string {
            $record = DB::table('hub_transfers')
                ->where('id', $transfer)
                ->where('logistics_account_id', $logistics->id)
                ->lockForUpdate()
                ->first();

            abort_unless($record, 404);

            if ($hubFacility) {
                $currentHub = CurrentLogisticsHub::requireFacility($request);

                abort_unless(
                    (int) $currentHub->id === (int) $hubFacility->id
                    && (int) $record->origin_facility_id === (int) $currentHub->id,
                    403,
                    'Only the operator of the origin hub may dispatch this transfer.'
                );
            }

            if ($record->status !== 'preparing') {
                throw ValidationException::withMessages([
                    'transfer' => 'Only a preparing transfer can be dispatched.',
                ]);
            }

            $parcelIds = DB::table('hub_transfer_parcels')
                ->where('hub_transfer_id', $record->id)
                ->pluck('logistics_parcel_id');

            if ($parcelIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'transfer' => 'This transfer manifest does not contain any parcels.',
                ]);
            }

            $validIds = LogisticsParcel::query()
                ->whereIn('id', $parcelIds->all())
                ->where('status', 'linehaul_assigned')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id');

            if ($validIds->count() !== $parcelIds->count()) {
                throw ValidationException::withMessages([
                    'transfer' => 'One or more manifest parcels are not ready for dispatch.',
                ]);
            }

            $facilities = $this->activeTransferFacilities(
                (int) $logistics->id,
                (int) $record->origin_facility_id,
                (int) $record->destination_facility_id
            );

            $dispatchedAt = now();

            DB::table('hub_transfers')
                ->where('id', $record->id)
                ->update([
                    'status' => 'in_transit',
                    'dispatched_at' => $dispatchedAt,
                    'updated_at' => $dispatchedAt,
                ]);

            LogisticsParcel::query()
                ->whereIn('id', $parcelIds->all())
                ->update([
                    'status' => 'linehaul_in_transit',
                    'updated_at' => $dispatchedAt,
                ]);

            $orders = MarketplaceOrder::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereHas('logisticsParcel', fn ($query) => $query->whereIn('id', $parcelIds->all()))
                ->get();

            foreach ($orders as $order) {
                $this->workflow->record($order, 'both', 'hub_transfer_dispatched', 'Hub Transfer Dispatched',
                    'Transfer '.$record->transfer_code.' departed '.$facilities[(int) $record->origin_facility_id]->name.'.');
            }

            /*
             * Actual departure notification.
             * Destination hub receives its first transfer alert here.
             */

            $data = $this->transferNotificationData(
                (int) $record->id,
                (string) $record->transfer_code,
                (int) $record->origin_facility_id,
                (int) $record->destination_facility_id,
                $parcelIds->count()
            );

            $message = 'Transfer '.$record->transfer_code
                .' with '.$parcelIds->count()
                .' parcel(s) departed '
                .$facilities[(int) $record->origin_facility_id]->name
                .' for '
                .$facilities[(int) $record->destination_facility_id]->name
                .'.';

            $eventKey = 'hub_transfer_dispatched:transfer:'.$record->id;

            $this->notifications->notifyManager(
                (int) $logistics->id,
                $eventKey,
                'hub_transfer_dispatched',
                'Hub Transfer dispatched',
                $message,
                'logistics.hub-transfers',
                $data
            );

            $this->notifications->notifyFacilityOperators(
                (int) $logistics->id,
                (int) $record->origin_facility_id,
                $eventKey,
                'hub_transfer_dispatched',
                'Your transfer has departed',
                $message,
                'logistics.hub-transfers',
                $data
            );

            $this->notifications->notifyFacilityOperators(
                (int) $logistics->id,
                (int) $record->destination_facility_id,
                $eventKey,
                'hub_transfer_dispatched',
                'Incoming transfer is on its way',
                $message,
                'logistics.hub-transfers',
                $data
            );

            return (string) $record->transfer_code;
        }, 3);

        return back()->with(
            'success',
            'Transfer '.$code.' has departed the origin hub.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destination Receipt:
    | in_transit -> received; parcels -> destination_received
    |--------------------------------------------------------------------------
    */

    public function receive(Request $request, int $transfer): RedirectResponse
    {
        $logistics = $this->orderAccess->prepare($request);

        $hubFacility = CurrentLogisticsHub::isHubOperator($request)
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        $code = DB::transaction(function () use (
            $request, $transfer, $logistics, $hubFacility
        ): string {
            $record = DB::table('hub_transfers')
                ->where('id', $transfer)
                ->where('logistics_account_id', $logistics->id)
                ->lockForUpdate()
                ->first();

            abort_unless($record, 404);

            if ($hubFacility) {
                $currentHub = CurrentLogisticsHub::requireFacility($request);

                abort_unless(
                    (int) $currentHub->id === (int) $hubFacility->id
                    && (int) $record->destination_facility_id === (int) $currentHub->id,
                    403,
                    'Only the operator of the destination hub may receive this transfer.'
                );
            }

            if ($record->status !== 'in_transit') {
                throw ValidationException::withMessages([
                    'transfer' => 'Only an in-transit Hub Transfer can be received.',
                ]);
            }

            $parcelIds = DB::table('hub_transfer_parcels')
                ->where('hub_transfer_id', $record->id)
                ->pluck('logistics_parcel_id');

            if ($parcelIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'transfer' => 'This transfer manifest does not contain any parcels.',
                ]);
            }

            $validIds = LogisticsParcel::query()
                ->whereIn('id', $parcelIds->all())
                ->where('status', 'linehaul_in_transit')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id');

            if ($validIds->count() !== $parcelIds->count()) {
                throw ValidationException::withMessages([
                    'transfer' => 'One or more parcels are not in the expected linehaul state.',
                ]);
            }

            $facilities = $this->activeTransferFacilities(
                (int) $logistics->id,
                (int) $record->origin_facility_id,
                (int) $record->destination_facility_id
            );

            $receivedAt = now();

            DB::table('hub_transfers')
                ->where('id', $record->id)
                ->update([
                    'status' => 'received',
                    'received_at' => $receivedAt,
                    'updated_at' => $receivedAt,
                ]);

            LogisticsParcel::query()
                ->whereIn('id', $parcelIds->all())
                ->update([
                    'status' => 'destination_received',
                    'updated_at' => $receivedAt,
                ]);

            $orders = MarketplaceOrder::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereHas('logisticsParcel', fn ($query) => $query->whereIn('id', $parcelIds->all()))
                ->get();

            DeliveryRouteStop::query()
                ->whereIn('marketplace_order_id', $orders->modelKeys())
                ->where('stop_key', 'destination_facility')
                ->where('logistics_facility_id', $record->destination_facility_id)
                ->whereNull('reached_at')
                ->update(['reached_at' => $receivedAt]);

            foreach ($orders as $order) {
                $this->workflow->record($order, 'both', 'destination_hub_received', 'Destination Hub Receipt Confirmed',
                    'Transfer '.$record->transfer_code.' was received at '.$facilities[(int) $record->destination_facility_id]->name.'.');
            }

            /*
             * The origin receives confirmation.
             * The destination receives its Last-Mile action alert.
             */

            $data = $this->transferNotificationData(
                (int) $record->id,
                (string) $record->transfer_code,
                (int) $record->origin_facility_id,
                (int) $record->destination_facility_id,
                $parcelIds->count()
            );

            $message = 'Transfer '.$record->transfer_code
                .' with '.$parcelIds->count()
                .' parcel(s) was received at '
                .$facilities[(int) $record->destination_facility_id]->name
                .'. Parcels are ready for Last-Mile Assignment.';

            $eventKey = 'hub_transfer_received:transfer:'.$record->id;

            $this->notifications->notifyManager(
                (int) $logistics->id,
                $eventKey,
                'hub_transfer_received',
                'Hub Transfer received',
                $message,
                'logistics.last-mile-assignment',
                $data
            );

            $this->notifications->notifyFacilityOperators(
                (int) $logistics->id,
                (int) $record->origin_facility_id,
                $eventKey,
                'hub_transfer_received',
                'Your transfer was received',
                $message,
                'logistics.hub-transfers',
                $data
            );

            $this->notifications->notifyFacilityOperators(
                (int) $logistics->id,
                (int) $record->destination_facility_id,
                $eventKey,
                'hub_transfer_received',
                'Parcels ready for Last-Mile Assignment',
                $message,
                'logistics.last-mile-assignment',
                $data
            );

            return (string) $record->transfer_code;
        }, 3);

        return back()->with(
            'success',
            'Transfer '.$code
                .' was received at the destination hub. Parcels are ready for Last-Mile Assignment.'
        );
    }

    /**
     * Both facilities must be current, active members of this provider.
     * This helper is used while the transfer row is locked.
     */
    private function activeTransferFacilities(
        int $providerId,
        int $originId,
        int $destinationId
    ): Collection {
        $facilities = DB::table('logistics_facilities')
            ->where('logistics_account_id', $providerId)
            ->whereIn('id', [$originId, $destinationId])
            ->where('is_active', true)
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id', 'name'])
            ->keyBy('id');

        if ($originId === $destinationId || $facilities->count() !== 2) {
            throw ValidationException::withMessages([
                'transfer' => 'Both transfer hubs must be active and belong to this Logistics network.',
            ]);
        }

        return $facilities;
    }

    private function transferNotificationData(
        int $transferId,
        string $transferCode,
        int $originId,
        int $destinationId,
        int $parcelCount
    ): array {
        return [
            'transfer_id' => $transferId,
            'transfer_code' => $transferCode,
            'origin_facility_id' => $originId,
            'destination_facility_id' => $destinationId,
            'parcel_count' => $parcelCount,
        ];
    }

    private function generateTransferCode(): string
    {
        do {
            $code = 'HT-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (
            DB::table('hub_transfers')
                ->where('transfer_code', $code)
                ->exists()
        );

        return $code;
    }
}