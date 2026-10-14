<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsDeliveryAssignmentController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Orders available for pickup assignment
        |--------------------------------------------------------------------------
        */

        $orders = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
                'deliveryRouteStops',
            ])
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->where(
                'status',
                'ready_for_pickup'
            )
            ->whereNull('courier_email')
            ->whereHas(
                'logisticsParcel',
                fn ($query) =>
                    $query->where(
                        'status',
                        'pickup_verified'
                    )
            )
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pickup Groups
        |--------------------------------------------------------------------------
        |
        | Same:
        | - Seller
        | - Pickup address
        | - Origin sorting facility
        |
        | = one pickup group
        |
        */

        $pickupGroups = $orders->groupBy(
            fn (MarketplaceOrder $order): string =>
                $this->pickupGroupKey($order)
        );

        /*
        |--------------------------------------------------------------------------
        | Approved Riders
        |--------------------------------------------------------------------------
        */

        $approvedRiderApplicationIds =
            RegistrationApplication::query()
                ->where(
                    function ($query) use ($logistics): void {
                        $query
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->orWhereNull(
                                'logistics_account_id'
                            );
                    }
                )
                ->where('role', 'rider')
                ->where('status', 'approved')
                ->select('id');

        $riders = CourierAccount::query()
            ->where(
                function ($query) use ($logistics): void {
                    $query
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        )
                        ->orWhereNull(
                            'logistics_account_id'
                        );
                }
            )
            ->where('account_status', 'active')
            ->where('availability_status', 'online')
            ->whereIn(
                'registration_application_id',
                $approvedRiderApplicationIds
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Riders already busy with another pickup/delivery
        |--------------------------------------------------------------------------
        */

        $busyRiderEmails = MarketplaceOrder::query()
            ->whereIn(
                'status',
                self::ACTIVE_DELIVERY_STATUSES
            )
            ->whereNotNull('courier_email')
            ->pluck('courier_email')
            ->filter()
            ->map(
                fn ($email): string =>
                    strtolower((string) $email)
            )
            ->unique()
            ->values()
            ->all();

        return view(
            'logistics.delivery-assignment',
            compact(
                'orders',
                'pickupGroups',
                'riders',
                'busyRiderEmails'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Batch assignment
    |--------------------------------------------------------------------------
    |
    | One Rider receives many orders from the same Seller pickup location.
    |
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

        $orderIds = collect(
            $validated['order_ids']
        )
            ->map(
                fn ($id): int => (int) $id
            )
            ->unique()
            ->sort()
            ->values();

        $assignedCount = 0;

        DB::transaction(
            function () use (
                $validated,
                $logistics,
                $orderIds,
                &$assignedCount
            ): void {
                /*
                |--------------------------------------------------------------------------
                | Lock selected orders
                |--------------------------------------------------------------------------
                */

                $orders = MarketplaceOrder::query()
                    ->with([
                        'seller',
                        'deliveryRouteStops',
                    ])
                    ->whereIn(
                        'id',
                        $orderIds->all()
                    )
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    )
                    ->lockForUpdate()
                    ->get();

                if (
                    $orders->count()
                    !== $orderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'One or more selected orders do not belong to this Logistics account.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate order states
                |--------------------------------------------------------------------------
                */

                foreach ($orders as $order) {
                    if (
                        $order->status
                            !== 'ready_for_pickup'
                        || $order->courier_email
                    ) {
                        throw ValidationException::withMessages([
                            'order_ids' =>
                                'One or more orders are no longer available for pickup assignment.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Ensure these orders really belong to one pickup group
                |--------------------------------------------------------------------------
                */

                $groupKeys = $orders
                    ->map(
                        fn (
                            MarketplaceOrder $order
                        ): string =>
                            $this->pickupGroupKey($order)
                    )
                    ->unique();

                if ($groupKeys->count() !== 1) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Only orders from the same Seller, pickup address, and origin hub can be assigned together.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Verify all pickup requests
                |--------------------------------------------------------------------------
                */

                $verifiedParcels =
                    LogisticsParcel::query()
                        ->whereIn(
                            'marketplace_order_id',
                            $orderIds->all()
                        )
                        ->where(
                            'status',
                            'pickup_verified'
                        )
                        ->lockForUpdate()
                        ->get()
                        ->keyBy(
                            'marketplace_order_id'
                        );

                if (
                    $verifiedParcels->count()
                    !== $orders->count()
                ) {
                    throw ValidationException::withMessages([
                        'order_ids' =>
                            'Every order in this pickup group must be verified before Rider assignment.',
                    ]);
                }

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
                        function ($query) use (
                            $logistics
                        ): void {
                            $query
                                ->where(
                                    'logistics_account_id',
                                    $logistics->id
                                )
                                ->orWhereNull(
                                    'logistics_account_id'
                                );
                        }
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Rider must be approved
                |--------------------------------------------------------------------------
                */

                $approvedRider =
                    RegistrationApplication::query()
                        ->whereKey(
                            $rider
                                ->registration_application_id
                        )
                        ->where(
                            'role',
                            'rider'
                        )
                        ->where(
                            'status',
                            'approved'
                        )
                        ->exists();

                if (
                    ! $approvedRider
                    || (
                        $rider->logistics_account_id
                        && (int)
                            $rider->logistics_account_id
                            !== (int) $logistics->id
                    )
                    || $rider->account_status
                        !== 'active'
                    || $rider->availability_status
                        !== 'online'
                ) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'Only approved, active and online Riders from your Logistics network can receive pickup assignments.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Attach legacy rider if needed
                |--------------------------------------------------------------------------
                */

                if (! $rider->logistics_account_id) {
                    $rider->forceFill([
                        'logistics_account_id' =>
                            $logistics->id,
                    ])->save();

                    if (
                        $rider
                            ->registration_application_id
                    ) {
                        RegistrationApplication::query()
                            ->whereKey(
                                $rider
                                    ->registration_application_id
                            )
                            ->whereNull(
                                'logistics_account_id'
                            )
                            ->update([
                                'logistics_account_id' =>
                                    $logistics->id,
                            ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Rider cannot already have ANOTHER active pickup/delivery
                |--------------------------------------------------------------------------
                |
                | Multiple orders inside THIS batch are allowed.
                |
                */

                $hasActiveDelivery =
                    MarketplaceOrder::query()
                        ->whereRaw(
                            'LOWER(courier_email) = ?',
                            [
                                strtolower(
                                    (string)
                                        $rider->email
                                ),
                            ]
                        )
                        ->whereIn(
                            'status',
                            self::ACTIVE_DELIVERY_STATUSES
                        )
                        ->lockForUpdate()
                        ->exists();

                if ($hasActiveDelivery) {
                    throw ValidationException::withMessages([
                        'rider_id' =>
                            'This Rider already has another active pickup or delivery.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Rider display name
                |--------------------------------------------------------------------------
                */

                $riderName = trim(
                    $rider->first_name
                    . ' '
                    . (
                        $rider->middle_initial
                            ? $rider
                                ->middle_initial
                                . '. '
                            : ''
                    )
                    . $rider->last_name
                );

                $riderEmail = strtolower(
                    (string) $rider->email
                );

                /*
                |--------------------------------------------------------------------------
                | Assign the SAME Rider to ALL orders in this pickup group
                |--------------------------------------------------------------------------
                */

                foreach ($orders as $order) {
                    $order->forceFill([
                        'status' =>
                            'courier_accepted',

                        'courier_name' =>
                            $riderName
                            ?: 'SARI Rider',

                        'courier_email' =>
                            $riderEmail,

                        'accepted_at' =>
                            now(),
                    ])->save();

                    $parcel = $verifiedParcels->get(
                        $order->id
                    );

                    $parcel->forceFill([
                        'status' =>
                            'awaiting_intake',

                        'sorting_zone' =>
                            null,

                        'received_at' =>
                            null,

                        'sorted_at' =>
                            null,
                    ])->save();

                    $this->workflow->record(
                        $order,
                        'both',
                        'courier_accepted',
                        'Pickup Rider Assigned by Logistics',
                        (
                            $riderName
                            ?: 'A SARI Rider'
                        )
                        . ' was assigned by '
                        . $logistics->displayName()
                        . ' to pickup order '
                        . $order->order_number
                        . ' as part of a grouped Seller pickup.'
                    );
                }

                $assignedCount =
                    $orders->count();
            }
        );

        return back()->with(
            'success',
            $assignedCount
            . ' '
            . (
                $assignedCount === 1
                    ? 'order was'
                    : 'orders were'
            )
            . ' assigned to the Rider successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Keep old single-order endpoint working
    |--------------------------------------------------------------------------
    */

    public function assign(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $request->merge([
            'order_ids' => [
                $order->id,
            ],
        ]);

        return $this->assignBatch(
            $request
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pickup grouping key
    |--------------------------------------------------------------------------
    */

    private function pickupGroupKey(
        MarketplaceOrder $order
    ): string {
        $originFacility =
            $order
                ->deliveryRouteStops
                ->firstWhere(
                    'stop_key',
                    'origin_facility'
                );

        return implode('|', [
            (string)
                $order->seller_account_id,

            strtolower(
                trim(
                    (string)
                        $order->pickup_address
                )
            ),

            (string) (
                $originFacility
                    ?->logistics_facility_id
                ?? 'no-origin-hub'
            ),
        ]);
    }
}