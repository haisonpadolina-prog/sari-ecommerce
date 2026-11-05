<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\LogisticsServiceArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileLogisticsPickupController extends Controller
{
    private const ACTIVE_PICKUP_STATUSES = [
        'courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer',
    ];

    private const ACTIVE_LAST_MILE_STATUSES = ['assigned', 'out_for_delivery'];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsNotificationService $notifications,
    ) {}

    private function provider(Request $request): LogisticsAccount
    {
        /** @var LogisticsAccount $provider */
        $provider = $request->user();
        return $provider;
    }

    private function facility(Request $request, int $id): LogisticsFacility
    {
        return LogisticsFacility::query()
            ->forLogistics((int) $this->provider($request)->id)
            ->active()
            ->findOrFail($id);
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'min:1'],
        ]);

        $provider = $this->provider($request);
        $facility = $this->facility($request, (int) $validated['facility_id']);

        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel', 'deliveryRouteStops.facility'])
            ->where('logistics_account_id', $provider->id)
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->whereHas('deliveryRouteStops', fn ($q) => $q
                ->where('stop_key', 'origin_facility')
                ->where('logistics_facility_id', $facility->id))
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        foreach ($orders as $order) {
            $issues = $this->pickupIssues($order);
            if ($order->logisticsParcel && $order->logisticsParcel->status !== 'pickup_verified') {
                $issues[] = 'Parcel has already entered the Logistics intake flow.';
            }
            $order->setAttribute('mobile_pickup_issues', $issues);
            $order->setAttribute('mobile_pickup_state', $issues
                ? 'attention'
                : ($order->logisticsParcel?->status === 'pickup_verified' ? 'verified' : 'pending'));
        }

        $groups = $orders
            ->groupBy(fn (MarketplaceOrder $order): string => $this->pickupGroupKey($order))
            ->map(function (Collection $group, string $key): array {
                /** @var MarketplaceOrder $first */
                $first = $group->first();
                $origin = $first->deliveryRouteStops->firstWhere('stop_key', 'origin_facility');
                $verified = $group->where('mobile_pickup_state', 'verified')->count();
                $attention = $group->where('mobile_pickup_state', 'attention')->count();

                return [
                    'key' => $key,
                    'seller_name' => $first->seller?->store_name ?: $first->pickup_name ?: 'SARI Seller',
                    'pickup_name' => $first->pickup_name ?: 'Seller Pickup',
                    'pickup_address' => $first->pickup_address ?: '',
                    'origin_facility_id' => (int) ($origin?->logistics_facility_id ?? 0),
                    'origin_facility_name' => $origin?->facility?->name ?: $origin?->name,
                    'order_count' => $group->count(),
                    'verified_count' => $verified,
                    'pending_count' => $group->count() - $verified - $attention,
                    'attention_count' => $attention,
                    'ready_for_assignment' => $attention === 0 && $verified === $group->count(),
                    'order_ids' => $group->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'orders' => $group->map(fn (MarketplaceOrder $order): array => [
                        'id' => (int) $order->id,
                        'order_number' => (string) $order->order_number,
                        'buyer_name' => (string) ($order->buyer_name ?? ''),
                        'status' => (string) $order->status,
                        'pickup_state' => (string) $order->getAttribute('mobile_pickup_state'),
                        'issues' => (array) $order->getAttribute('mobile_pickup_issues'),
                        'total' => (float) $order->total,
                    ])->values()->all(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'facility_id' => (int) $facility->id,
                'groups' => $groups,
                'riders' => $this->riderPool($provider),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'min:1'],
            'order_ids' => ['required', 'array', 'min:1', 'max:200'],
            'order_ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $provider = $this->provider($request);
        $facility = $this->facility($request, (int) $validated['facility_id']);
        $ids = collect($validated['order_ids'])->map(fn ($id) => (int) $id)->sort()->values();

        $result = DB::transaction(function () use ($provider, $facility, $ids): array {
            $orders = MarketplaceOrder::query()
                ->with(['seller', 'deliveryRouteStops.facility'])
                ->whereIn('id', $ids)
                ->where('logistics_account_id', $provider->id)
                ->whereHas('deliveryRouteStops', fn ($q) => $q
                    ->where('stop_key', 'origin_facility')
                    ->where('logistics_facility_id', $facility->id))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($orders->count() !== $ids->count()) {
                throw ValidationException::withMessages([
                    'order_ids' => 'One or more pickup requests are unavailable for this hub.',
                ]);
            }

            $parcels = LogisticsParcel::query()
                ->whereIn('marketplace_order_id', $ids)
                ->orderBy('marketplace_order_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('marketplace_order_id');

            $count = 0;
            foreach ($orders as $order) {
                if ($order->status !== 'ready_for_pickup' || filled($order->courier_email)) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number.' is no longer available for verification.',
                    ]);
                }

                $issues = $this->pickupIssues($order);
                if ($issues) {
                    throw ValidationException::withMessages(['order_ids' => $order->order_number.': '.$issues[0]]);
                }

                $existing = $parcels->get($order->id);
                if ($existing?->status === 'pickup_verified') {
                    continue;
                }
                if ($existing) {
                    throw ValidationException::withMessages([
                        'order_ids' => $order->order_number.' has already entered the Logistics intake flow.',
                    ]);
                }

                LogisticsParcel::query()->create([
                    'marketplace_order_id' => $order->id,
                    'status' => 'pickup_verified',
                ]);

                $actor = $provider->displayName();
                $this->workflow->record(
                    $order,
                    'both',
                    'pickup_verified',
                    'Pickup Request Verified',
                    $actor.' verified the Seller pickup request for order '.$order->order_number.'. It is now ready for Rider assignment.'
                );

                $message = $actor.' verified the pickup request for order '.$order->order_number.'. It is ready for Rider assignment.';
                $data = [
                    'order_id' => (int) $order->id,
                    'order_number' => (string) $order->order_number,
                    'origin_facility_id' => (int) $facility->id,
                ];
                $this->notifications->notifyManager((int) $provider->id,
                    'pickup_verified:order:'.$order->id, 'pickup_verified',
                    'Pickup request verified', $message, 'logistics.delivery-assignment', $data);
                $this->notifications->notifyFacilityOperators((int) $provider->id, (int) $facility->id,
                    'pickup_verified:order:'.$order->id, 'pickup_verified',
                    'Pickup ready for Rider assignment', $message, 'logistics.delivery-assignment', $data);
                $count++;
            }

            return ['verified_count' => $count];
        }, 3);

        return response()->json([
            'success' => true,
            'message' => $result['verified_count'] > 0
                ? $result['verified_count'].' pickup request(s) verified.'
                : 'Selected pickup requests were already verified.',
            'data' => $result,
        ]);
    }

    public function assign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'min:1'],
            'rider_id' => ['required', 'integer', 'exists:courier_accounts,id'],
            'order_ids' => ['required', 'array', 'min:1', 'max:200'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:marketplace_orders,id'],
        ]);

        $provider = $this->provider($request);
        $facility = $this->facility($request, (int) $validated['facility_id']);
        $ids = collect($validated['order_ids'])->map(fn ($id) => (int) $id)->sort()->values();

        $result = DB::transaction(function () use ($provider, $facility, $ids, $validated): array {
            $queue = MarketplaceOrder::query()
                ->with(['seller', 'deliveryRouteStops.facility'])
                ->where('logistics_account_id', $provider->id)
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->whereHas('deliveryRouteStops', fn ($q) => $q
                    ->where('stop_key', 'origin_facility')
                    ->where('logistics_facility_id', $facility->id))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $selected = $queue->whereIn('id', $ids->all())->values();
            if ($selected->count() !== $ids->count()) {
                throw ValidationException::withMessages(['order_ids' => 'One or more orders are no longer available.']);
            }

            $keys = $selected->map(fn (MarketplaceOrder $order): string => $this->pickupGroupKey($order))->unique();
            if ($keys->count() !== 1) {
                throw ValidationException::withMessages(['order_ids' => 'A pickup batch must have the same Seller, pickup address and origin hub.']);
            }

            $wholeGroupIds = $queue
                ->filter(fn (MarketplaceOrder $order): bool => $this->pickupGroupKey($order) === $keys->first())
                ->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            if ($wholeGroupIds !== $ids->all()) {
                throw ValidationException::withMessages(['order_ids' => 'Select the complete Seller pickup batch before assigning one Rider.']);
            }

            $parcels = LogisticsParcel::query()
                ->whereIn('marketplace_order_id', $ids)
                ->orderBy('marketplace_order_id')
                ->lockForUpdate()
                ->get()->keyBy('marketplace_order_id');
            foreach ($selected as $order) {
                if ($parcels->get($order->id)?->status !== 'pickup_verified') {
                    throw ValidationException::withMessages(['order_ids' => 'All orders in this Seller group must be verified before assignment.']);
                }
            }

            $rider = CourierAccount::query()
                ->whereKey((int) $validated['rider_id'])
                ->where('logistics_account_id', $provider->id)
                ->where('account_status', 'active')
                ->where('availability_status', 'online')
                ->lockForUpdate()
                ->first();
            if (! $rider) {
                throw ValidationException::withMessages(['rider_id' => 'Select an active, online Rider from this Logistics network.']);
            }

            $approved = $rider->registration_application_id
                ? RegistrationApplication::query()->whereKey($rider->registration_application_id)
                    ->where('logistics_account_id', $provider->id)->where('role', 'rider')->where('status', 'approved')->exists()
                : true;
            if (! $approved) {
                throw ValidationException::withMessages(['rider_id' => 'The selected Rider is not approved for this Logistics network.']);
            }

            $pickupOrder = $selected->first();
            if (! $pickupOrder || ! LogisticsServiceArea::matchesPickupRider($rider, $pickupOrder->pickup_address)) {
                throw ValidationException::withMessages(['rider_id' => 'This Rider is outside the Seller pickup barangay.']);
            }

            $email = strtolower(trim((string) $rider->email));
            $busyPickup = MarketplaceOrder::query()
                ->whereRaw('LOWER(TRIM(courier_email)) = ?', [$email])
                ->whereIn('status', self::ACTIVE_PICKUP_STATUSES)
                ->where(function ($query): void {
                    $query->whereDoesntHave('logisticsParcel')
                        ->orWhereHas('logisticsParcel', fn ($parcel) => $parcel->where('status', 'awaiting_intake'));
                })->exists();
            $busyLastMile = DB::table('last_mile_assignments')
                ->where('courier_account_id', $rider->id)
                ->whereIn('status', self::ACTIVE_LAST_MILE_STATUSES)->exists();
            if ($busyPickup || $busyLastMile) {
                throw ValidationException::withMessages(['rider_id' => 'This Rider already has an active pickup or last-mile route.']);
            }

            $riderName = trim($rider->first_name.' '.($rider->middle_initial ? $rider->middle_initial.'. ' : '').$rider->last_name) ?: 'SARI Rider';
            $assignedAt = now();
            $assignedBy = $provider->displayName();

            foreach ($selected as $order) {
                $order->forceFill([
                    'status' => 'courier_accepted',
                    'courier_name' => $riderName,
                    'courier_email' => $email,
                    'accepted_at' => $assignedAt,
                ])->save();

                $updated = LogisticsParcel::query()
                    ->where('marketplace_order_id', $order->id)
                    ->where('status', 'pickup_verified')
                    ->update([
                        'status' => 'awaiting_intake',
                        'sorting_zone' => null,
                        'received_at' => null,
                        'sorted_at' => null,
                        'updated_at' => $assignedAt,
                    ]);
                if ($updated !== 1) {
                    throw ValidationException::withMessages(['order_ids' => 'A parcel changed during assignment. Refresh and retry.']);
                }

                $this->workflow->record($order, 'both', 'courier_accepted', 'Pickup Rider Assigned by Logistics',
                    $riderName.' was assigned by '.$assignedBy.' to collect this order as part of a '.$selected->count().'-parcel Seller pickup batch.');

                $message = $riderName.' was assigned to collect order '.$order->order_number.'.';
                $data = ['order_id'=>(int)$order->id,'order_number'=>(string)$order->order_number,
                    'rider_id'=>(int)$rider->id,'rider_name'=>$riderName,'origin_facility_id'=>(int)$facility->id,
                    'batch_size'=>(int)$selected->count()];
                $this->notifications->notifyManager((int)$provider->id,'pickup_rider_assigned:order:'.$order->id,
                    'pickup_rider_assigned','Pickup Rider assigned',$message,'logistics.delivery-monitoring',$data);
                $this->notifications->notifyFacilityOperators((int)$provider->id,(int)$facility->id,
                    'pickup_rider_assigned:order:'.$order->id,'pickup_rider_assigned',
                    'Rider assigned to your origin-hub pickup',$message,'logistics.delivery-monitoring',$data);
            }

            return [
                'assigned_count' => $selected->count(),
                'rider_id' => (int) $rider->id,
                'rider_name' => $riderName,
            ];
        }, 3);

        return response()->json([
            'success' => true,
            'message' => $result['assigned_count'].' parcel(s) assigned to '.$result['rider_name'].'.',
            'data' => $result,
        ]);
    }

    private function riderPool(LogisticsAccount $provider): array
    {
        $approvedIds = RegistrationApplication::query()
            ->where('logistics_account_id', $provider->id)
            ->where('role', 'rider')->where('status', 'approved')->pluck('id');

        $busyPickupEmails = MarketplaceOrder::query()
            ->whereIn('status', self::ACTIVE_PICKUP_STATUSES)
            ->whereNotNull('courier_email')
            ->pluck('courier_email')->filter()->map(fn ($e) => strtolower(trim((string)$e)))->unique();
        $busyLastMileIds = DB::table('last_mile_assignments')
            ->whereIn('status', self::ACTIVE_LAST_MILE_STATUSES)->pluck('courier_account_id')->map(fn($id)=>(int)$id)->unique();

        return CourierAccount::query()
            ->where('logistics_account_id', $provider->id)
            ->where('account_status', 'active')
            ->where('availability_status', 'online')
            ->where(function ($q) use ($approvedIds): void {
                $q->whereIn('registration_application_id', $approvedIds)
                    ->orWhereNull('registration_application_id');
            })
            ->orderBy('first_name')->orderBy('last_name')->get()
            ->map(function (CourierAccount $rider) use ($busyPickupEmails, $busyLastMileIds): array {
                $busy = $busyPickupEmails->contains(strtolower(trim((string)$rider->email)))
                    || $busyLastMileIds->contains((int)$rider->id);
                return [
                    'id' => (int)$rider->id,
                    'name' => trim($rider->first_name.' '.$rider->last_name),
                    'email' => (string)$rider->email,
                    'barangay_name' => (string)($rider->barangay_name ?? ''),
                    'municipality_name' => (string)($rider->municipality_name ?? ''),
                    'vehicle_type' => (string)($rider->vehicle_type ?? ''),
                    'busy' => $busy,
                ];
            })->values()->all();
    }

    private function pickupGroupKey(MarketplaceOrder $order): string
    {
        $origin = $order->deliveryRouteStops->firstWhere('stop_key', 'origin_facility');
        $seller = $order->seller_account_id ? 'seller:'.$order->seller_account_id : 'order:'.$order->id;
        return json_encode([$seller, $this->normalizePickupText($order->pickup_address), $origin?->logistics_facility_id], JSON_THROW_ON_ERROR);
    }

    private function pickupIssues(MarketplaceOrder $order): array
    {
        $issues = [];
        $origin = $order->deliveryRouteStops->firstWhere('stop_key', 'origin_facility');
        $facility = $origin?->facility;
        if (blank($order->pickup_address)) $issues[] = 'Pickup address is missing.';
        if (! $facility) $issues[] = 'Origin hub is missing.';
        elseif ((int)$facility->logistics_account_id !== (int)$order->logistics_account_id) $issues[] = 'Origin hub belongs to another provider.';
        elseif (! $facility->is_active) $issues[] = 'Origin hub is inactive.';
        return $issues;
    }

    private function normalizePickupText(?string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string)$value)) ?: '');
    }
}
