<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\LogisticsNotification;
use App\Services\Logistics\LogisticsNotificationService;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Support\ParcelQrCode;
use App\Support\ParcelSortingLocator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Logistics MANAGER mobile API: same parcel/order tables & workflow events as web.
 * Hub Operator accounts are intentionally NOT accepted by the mobile token guard.
 * Every facility/parcel/transfer is explicitly scoped to the authenticated provider.
 */
class MobileLogisticsController extends Controller
{
    public function __construct(
        private readonly ParcelQrCode $codes,
        private readonly MarketplaceOrderWorkflowService $workflow,
        private readonly LogisticsNotificationService $notifications,
    ) {}

    private function provider(Request $request): LogisticsAccount
    {
        /** @var LogisticsAccount $provider */
        $provider = $request->user();
        return $provider;
    }

    private function facility(Request $request, int $facilityId): LogisticsFacility
    {
        return LogisticsFacility::query()->forLogistics((int) $this->provider($request)->id)
            ->active()->findOrFail($facilityId);
    }

    private function orderScope(Request $request)
    {
        return MarketplaceOrder::query()->where('logistics_account_id', $this->provider($request)->id);
    }

    private function parcelScope(Request $request)
    {
        return LogisticsParcel::query()->whereHas('order', fn ($q) =>
            $q->where('logistics_account_id', $this->provider($request)->id));
    }

    private function scopedToFacility($query, int $facilityId, string $stopKey)
    {
        return $query->whereHas('order.deliveryRouteStops', fn ($q) =>
            $q->where('stop_key', $stopKey)->where('logistics_facility_id', $facilityId));
    }

    private function parcelData(LogisticsParcel $parcel): array
    {
        $order = $parcel->order;
        return [
            'id' => (int) $parcel->id,
            'order_id' => (int) $parcel->marketplace_order_id,
            'order_number' => (string) ($order?->order_number ?? ''),
            'status' => (string) $parcel->status,
            'order_status' => (string) ($order?->status ?? ''),
            'buyer_name' => (string) ($order?->buyer_name ?? ''),
            'sorting_zone' => $parcel->sorting_zone,
            'received_at' => $parcel->received_at?->toIso8601String(),
            'sorted_at' => $parcel->sorted_at?->toIso8601String(),
        ];
    }

    private function ok(array $data, string $message = 'OK'): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data])
            ->header('Cache-Control', 'no-store');
    }

    public function branches(Request $request): JsonResponse
    {
        $facilities = LogisticsFacility::query()->forLogistics((int) $this->provider($request)->id)
            ->active()->orderByDesc('is_main_branch')->orderBy('name')
            ->get(['id', 'name', 'code', 'municipality_name', 'province_name', 'is_main_branch']);
        return $this->ok(['branches' => $facilities]);
    }

    public function profile(Request $request): JsonResponse
    {
        $provider = $this->provider($request);
        return $this->ok(['id' => (int) $provider->id,
            'name' => $provider->displayName(), 'email' => $provider->email,
            'business_name' => $provider->business_name, 'contact_no' => $provider->contact_no,
            'role' => 'logistics', 'account_status' => $provider->account_status]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $id = (int) $this->provider($request)->id;
        $parcels = $this->parcelScope($request);
        $totals = (clone $parcels)->selectRaw('status, COUNT(*) as total')->groupBy('status')
            ->pluck('total', 'status');
        $incoming = (int) ($totals['awaiting_intake'] ?? 0);
        $sorting = (int) ($totals['received'] ?? 0);
        $transfers = DB::table('hub_transfers')->where('logistics_account_id', $id)
            ->whereIn('status', ['preparing', 'in_transit'])->count();
        $lastMile = (int) ($totals['destination_received'] ?? 0);
        $pickups = $this->orderScope($request)->whereIn('status',
            ['ready_for_pickup', 'pickup_verified'])->count();
        $unread = LogisticsNotification::query()->forRecipient($id)->unread()->count();
        return $this->ok(compact('incoming', 'sorting', 'transfers', 'lastMile', 'pickups', 'unread') + [
            'total_parcels' => (clone $parcels)->count(),
        ]);
    }

    public function incoming(Request $request): JsonResponse
    {
        $params = $request->validate(['facility_id' => ['required', 'integer', 'min:1']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $items = $this->scopedToFacility($this->parcelScope($request)
            ->with('order')->where('status', 'awaiting_intake'), $facility->id, 'origin_facility')
            ->latest('id')->limit(100)->get()->map(fn ($p) => $this->parcelData($p))->values()->all();
        return $this->ok(['items' => $items, 'facility_id' => (int) $facility->id]);
    }

    public function sorting(Request $request): JsonResponse
    {
        $params = $request->validate(['facility_id' => ['required', 'integer', 'min:1']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $items = $this->scopedToFacility($this->parcelScope($request)
            ->with('order')->whereIn('status', ['received', 'sorted']), $facility->id, 'origin_facility')
            ->latest('id')->limit(100)->get()->map(fn ($p) => $this->parcelData($p))->values()->all();
        return $this->ok(['items' => $items, 'facility_id' => (int) $facility->id]);
    }

    public function pickups(Request $request): JsonResponse
    {
        $items = $this->orderScope($request)->whereIn('status', ['ready_for_pickup', 'pickup_verified'])
            ->latest('id')->limit(100)->get(['id', 'order_number', 'status', 'pickup_name', 'pickup_address'])
            ->map(fn ($row) => ['id' => $row->id, 'order_number' => $row->order_number,
                'status' => $row->status, 'pickup_name' => $row->pickup_name,
                'pickup_address' => $row->pickup_address])->values()->all();
        return $this->ok(['items' => $items]);
    }

    public function lastMile(Request $request): JsonResponse
    {
        $params = $request->validate(['facility_id' => ['required', 'integer', 'min:1']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $items = $this->scopedToFacility($this->parcelScope($request)
            ->with('order')->where('status', 'destination_received'), $facility->id, 'destination_facility')
            ->latest('id')->limit(100)->get()->map(fn ($p) => $this->parcelData($p))->values()->all();
        return $this->ok(['items' => $items]);
    }

    public function monitoring(Request $request): JsonResponse
    {
        $params = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($params['q'])).'%';
        $items = $this->orderScope($request)->where(function ($q) use ($like) {
            $q->where('order_number', 'like', $like)->orWhere('buyer_name', 'like', $like);
        })->latest('id')->limit(30)->get(['id', 'order_number', 'status', 'buyer_name', 'courier_name'])
          ->map(fn ($r) => ['id'=>$r->id,'order_number'=>$r->order_number,'status'=>$r->status,
                'buyer_name'=>$r->buyer_name,'courier_name'=>$r->courier_name])->values()->all();
        return $this->ok(['items' => $items]);
    }

    /** Signed QR only: manual entry must be an actual SARI QR payload. */
    public function verify(Request $request): JsonResponse
    {
        $params = $request->validate([
            'payload' => ['required', 'string', 'max:1000'],
            'facility_id' => ['required', 'integer', 'min:1'],
        ]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $order = $this->codes->resolve($params['payload']);
        abort_unless((int) $order->logistics_account_id === (int) $this->provider($request)->id, 404);
        $parcel = LogisticsParcel::query()->where('marketplace_order_id', $order->id)->firstOrFail();
        $stops = $order->deliveryRouteStops()->whereIn('stop_key', ['origin_facility', 'destination_facility'])
            ->get(['stop_key', 'logistics_facility_id'])->keyBy('stop_key');
        $origin = (int) ($stops->get('origin_facility')?->logistics_facility_id ?? 0);
        $destination = (int) ($stops->get('destination_facility')?->logistics_facility_id ?? 0);
        abort_unless($facility->id === $origin || $facility->id === $destination, 403,
            'This parcel belongs to a different hub.');
        $next = match ($parcel->status) {
            'awaiting_intake' => $facility->id === $origin && $order->status === 'in_transit' && $order->picked_up_at
                ? 'receive' : null,
            'received' => $facility->id === $origin ? 'sort' : null,
            'linehaul_in_transit' => $facility->id === $destination ? 'transfer_receive' : null,
            'destination_received' => $facility->id === $destination ? 'last_mile' : null,
            default => null,
        };
        return $this->ok(['valid' => true, 'parcel' => $this->parcelData($parcel),
            'order_number' => (string) $order->order_number, 'facility_id' => (int) $facility->id,
            'next_action' => $next, 'origin_facility_id' => $origin,
            'destination_facility_id' => $destination], 'Signed SARI parcel QR verified.');
    }

    public function receive(Request $request, LogisticsParcel $parcel): JsonResponse
    {
        $params = $request->validate(['facility_id' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $order = $this->orderScope($request)->whereHas('logisticsParcel', fn ($q) => $q->whereKey($parcel->id))
            ->firstOrFail();
        $data = DB::transaction(function () use ($request, $parcel, $order, $facility, $params) {
            $locked = $this->orderScope($request)->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $p = $this->parcelScope($request)->whereKey($parcel->id)->lockForUpdate()->firstOrFail();
            $origin = $locked->deliveryRouteStops()->where('stop_key', 'origin_facility')->lockForUpdate()->firstOrFail();
            abort_unless((int) $origin->logistics_facility_id === (int) $facility->id, 403);
            if ($locked->status !== 'in_transit' || !$locked->picked_up_at || blank($locked->courier_email)
                || $p->status !== 'awaiting_intake') {
                throw ValidationException::withMessages(['parcel'=>'Parcel is not ready for origin-hub intake.']);
            }
            $now = now();
            $p->fill(['status'=>'received','received_at'=>$now,'sorted_at'=>null,
                'notes'=>$params['notes'] ?? $p->notes])->save();
            if (!$origin->reached_at) $origin->forceFill(['reached_at'=>$now])->save();
            $this->workflow->record($locked, 'both', 'origin_hub_received', 'Origin Hub Intake Confirmed',
                'Parcel for order '.$locked->order_number.' was received at '.$facility->name.'.');
            $this->notifications->notifyManager((int) $this->provider($request)->id,
                'origin_hub_received:order:'.$locked->id, 'origin_hub_received',
                'Parcel received at origin hub', 'Parcel '.$locked->order_number.' received at '.$facility->name.'.',
                'logistics.parcel-sorting', ['order_id'=>$locked->id,'parcel_id'=>$p->id]);
            return $this->parcelData($p->load('order'));
        }, 3);
        return $this->ok($data, 'Parcel received and synced with web Logistics.');
    }

    public function sort(Request $request, LogisticsParcel $parcel): JsonResponse
    {
        $params = $request->validate(['facility_id' => ['required', 'integer', 'min:1'],
            'sorting_zone' => ['nullable', 'string', 'max:80'], 'notes' => ['nullable', 'string', 'max:1000']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $this->parcelScope($request)->whereKey($parcel->id)->firstOrFail();
        $data = DB::transaction(function () use ($request, $parcel, $facility, $params) {
            $p = $this->parcelScope($request)->whereKey($parcel->id)->lockForUpdate()->firstOrFail();
            $order = $this->orderScope($request)->whereKey($p->marketplace_order_id)->lockForUpdate()->firstOrFail();
            $stops = $order->deliveryRouteStops()->whereIn('stop_key', ['origin_facility','destination_facility'])
                ->orderBy('id')->lockForUpdate()->get()->keyBy('stop_key');
            $origin = $stops->get('origin_facility'); $destination = $stops->get('destination_facility');
            if (!$origin || !$destination || (int) $origin->logistics_facility_id !== (int) $facility->id) abort(403);
            $destFacility = $this->facility($request, (int) $destination->logistics_facility_id);
            if ($p->status !== 'received' || !$p->received_at || $order->status !== 'in_transit'
                || !$order->picked_up_at || blank($order->courier_email)) {
                throw ValidationException::withMessages(['parcel'=>'Parcel is not ready to be sorted.']);
            }
            $local = (int) $facility->id === (int) $destFacility->id;
            $manual = trim((string) ($params['sorting_zone'] ?? ''));
            $zone = $manual !== '' ? $manual : ParcelSortingLocator::forParcel(
                (int) $p->id, (int) $facility->id, (int) $destFacility->id);
            $now = now();
            $p->fill(['status'=>$local?'destination_received':'sorted','sorting_zone'=>$zone,
                'sorted_at'=>$now,'notes'=>$params['notes'] ?? $p->notes])->save();
            if (!$origin->reached_at) $origin->forceFill(['reached_at'=>$p->received_at])->save();
            $this->workflow->record($order,'both','origin_hub_sorted','Origin Hub Sorting Completed',
                'Parcel '.$order->order_number.' sorted at '.$facility->name.'.');
            if ($local) {
                if (!$destination->reached_at) $destination->forceFill(['reached_at'=>$now])->save();
                $this->workflow->record($order,'both','local_hub_handoff','Local Hub Handoff Completed',
                    'Parcel ready for last-mile assignment at '.$facility->name.'.');
            }
            $this->notifications->notifyManager((int) $this->provider($request)->id,
                'origin_hub_sorted:order:'.$order->id, 'origin_hub_sorted',
                'Parcel sorting completed', 'Parcel '.$order->order_number.' sorted at '.$facility->name.'.',
                $local ? 'logistics.last-mile-assignment':'logistics.hub-transfers',
                ['order_id'=>$order->id,'parcel_id'=>$p->id]);
            return $this->parcelData($p->load('order'));
        }, 3);
        return $this->ok($data, 'Parcel sorting confirmed.');
    }

    public function transfers(Request $request): JsonResponse
    {
        $params = $request->validate(['facility_id'=>['nullable','integer','min:1']]);
        $facilityId = isset($params['facility_id']) ? (int) $this->facility($request, (int) $params['facility_id'])->id : null;
        $query = DB::table('hub_transfers as t')
            ->join('logistics_facilities as o','o.id','=','t.origin_facility_id')
            ->join('logistics_facilities as d','d.id','=','t.destination_facility_id')
            ->where('t.logistics_account_id', $this->provider($request)->id);
        if ($facilityId) $query->where(fn ($q) => $q->where('t.origin_facility_id',$facilityId)
            ->orWhere('t.destination_facility_id',$facilityId));
        $rows = $query->orderByDesc('t.id')->limit(100)->get([
            't.id','t.transfer_code','t.status','t.origin_facility_id','t.destination_facility_id',
            'o.name as origin_name','d.name as destination_name', 't.dispatched_at','t.received_at']);
        return $this->ok(['items'=>$rows]);
    }

    public function transfer(Request $request, int $transfer): JsonResponse
    {
        $t = DB::table('hub_transfers')->where('logistics_account_id', $this->provider($request)->id)
            ->where('id', $transfer)->first();
        abort_unless($t,404);
        $items = DB::table('hub_transfer_parcels as item')->join('logistics_parcels as p',
            'p.id','=','item.logistics_parcel_id')->join('marketplace_orders as o',
            'o.id','=','p.marketplace_order_id')->where('item.hub_transfer_id', $transfer)
            ->where('o.logistics_account_id', $this->provider($request)->id)
            ->get(['p.id as parcel_id','p.status','o.id as order_id','o.order_number']);
        return $this->ok(['transfer'=>$t, 'items'=>$items]);
    }

    public function transferAction(Request $request, int $transfer, string $action): JsonResponse
    {
        abort_unless(in_array($action, ['dispatch','receive'], true), 404);
        $params = $request->validate(['facility_id'=>['required','integer','min:1']]);
        $facility = $this->facility($request, (int) $params['facility_id']);
        $result = DB::transaction(function () use ($request, $facility, $transfer, $action) {
            $t = DB::table('hub_transfers')->where('logistics_account_id', $this->provider($request)->id)
                ->where('id', $transfer)->lockForUpdate()->first();
            abort_unless($t,404);
            $expected = $action === 'dispatch' ? 'preparing' : 'in_transit';
            $hub = $action === 'dispatch' ? 'origin_facility_id' : 'destination_facility_id';
            abort_unless((int) $t->{$hub} === (int) $facility->id,403);
            if ($t->status !== $expected) {
                throw ValidationException::withMessages(['transfer'=>'Transfer status changed; refresh the manifest.']);
            }
            $ids = DB::table('hub_transfer_parcels')->where('hub_transfer_id', $transfer)
                ->pluck('logistics_parcel_id')->all();
            if (!$ids) throw ValidationException::withMessages(['transfer'=>'Empty transfer manifest.']);
            $old = $action === 'dispatch' ? 'linehaul_assigned' : 'linehaul_in_transit';
            $count = $this->parcelScope($request)->whereIn('id',$ids)->where('status',$old)
                ->orderBy('id')->lockForUpdate()->pluck('id')->count();
            if ($count !== count($ids)) {
                throw ValidationException::withMessages(['transfer'=>'Manifest parcel states do not match the expected workflow.']);
            }
            $now = now();
            $new = $action === 'dispatch' ? 'linehaul_in_transit' : 'destination_received';
            DB::table('hub_transfers')->where('id',$transfer)->update([
                'status'=>$action === 'dispatch'?'in_transit':'received',
                $action === 'dispatch'?'dispatched_at':'received_at'=>$now,'updated_at'=>$now]);
            LogisticsParcel::query()->whereIn('id',$ids)->update(['status'=>$new,'updated_at'=>$now]);
            $orders = $this->orderScope($request)->whereHas('logisticsParcel', fn ($q) => $q->whereIn('id',$ids))->get();
            if ($action === 'receive') {
                DeliveryRouteStop::query()->whereIn('marketplace_order_id', $orders->modelKeys())
                    ->where('stop_key','destination_facility')->where('logistics_facility_id', $facility->id)
                    ->whereNull('reached_at')->update(['reached_at'=>$now]);
            }
            foreach ($orders as $order) {
                $this->workflow->record($order,'both', $action === 'dispatch'?'hub_transfer_dispatched':'destination_hub_received',
                    $action === 'dispatch'?'Hub Transfer Dispatched':'Destination Hub Receipt Confirmed',
                    'Transfer '.$t->transfer_code.' '.$action.' at '.$facility->name.'.');
            }
            $this->notifications->notifyManager((int) $this->provider($request)->id,
                ($action === 'dispatch'?'hub_transfer_dispatched:':'hub_transfer_received:').'transfer:'.$t->id,
                $action === 'dispatch'?'hub_transfer_dispatched':'hub_transfer_received',
                'Hub Transfer '.($action==='dispatch'?'dispatched':'received'),
                'Transfer '.$t->transfer_code.' processed at '.$facility->name.'.',
                $action === 'dispatch'?'logistics.hub-transfers':'logistics.last-mile-assignment',
                ['transfer_id'=>$t->id]);
            return ['transfer_id'=>(int)$t->id,'status'=>$action === 'dispatch'?'in_transit':'received'];
        },3);
        return $this->ok($result, 'Hub transfer updated.');
    }

    public function notifications(Request $request): JsonResponse
    {
        $rows = LogisticsNotification::query()->forRecipient((int)$this->provider($request)->id)
            ->latest('id')->limit(100)->get(['id','type','title','message','read_at','created_at']);
        return $this->ok(['items'=>$rows, 'unread_count'=>LogisticsNotification::query()
            ->forRecipient((int)$this->provider($request)->id)->unread()->count()]);
    }

    public function readNotification(Request $request, int $notification): JsonResponse
    {
        $row = LogisticsNotification::query()->forRecipient((int)$this->provider($request)->id)
            ->findOrFail($notification);
        if (!$row->read_at) $row->forceFill(['read_at'=>now()])->save();
        return $this->ok(['id'=>$row->id, 'read_at'=>$row->read_at?->toIso8601String()]);
    }

    public function riders(Request $request): JsonResponse
    {
        $rows = $this->provider($request)->riders()->where('account_status','active')
            ->orderBy('first_name')->limit(250)->get(['id','first_name','last_name','email']);
        return $this->ok(['items'=>$rows]);
    }

    public function messages(Request $request): JsonResponse
    {
        $params = $request->validate(['rider_id'=>['required','integer','min:1']]);
        $rider = $this->provider($request)->riders()->where('account_status','active')
            ->findOrFail((int)$params['rider_id']);
        LogisticsMessage::query()->where('logistics_account_id', $this->provider($request)->id)
            ->where('courier_account_id',$rider->id)->where('sender_role','rider')
            ->whereNull('read_at')->update(['read_at'=>now()]);
        $messages = LogisticsMessage::query()->where('logistics_account_id',$this->provider($request)->id)
            ->where('courier_account_id',$rider->id)->oldest('id')->limit(100)->get();
        return $this->ok(['items'=>$messages]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $params = $request->validate(['rider_id'=>['required','integer','min:1'],
            'body'=>['required','string','max:2000']]);
        $rider = $this->provider($request)->riders()->where('account_status','active')
            ->findOrFail((int)$params['rider_id']);
        $message = LogisticsMessage::query()->create(['courier_account_id'=>$rider->id,
            'logistics_account_id'=>$this->provider($request)->id,
            'sender_role'=>'logistics','body'=>trim($params['body'])]);
        return response()->json(['success'=>true,'data'=>['id'=>$message->id]],201);
    }
}
