<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Registration\RegistrationApplication;
use App\Services\Logistics\LogisticsNotificationService;
use App\Support\LogisticsServiceArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileLogisticsLastMileAssignmentController extends Controller
{
    public function __construct(private readonly LogisticsNotificationService $notifications) {}

    private function provider(Request $request): LogisticsAccount
    {
        /** @var LogisticsAccount $provider */
        $provider = $request->user();
        return $provider;
    }

    private function facility(Request $request, int $id): LogisticsFacility
    {
        return LogisticsFacility::query()->forLogistics((int)$this->provider($request)->id)->active()->findOrFail($id);
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(['facility_id'=>['required','integer','min:1']]);
        $provider = $this->provider($request);
        $facility = $this->facility($request, (int)$validated['facility_id']);

        $rows = DB::table('marketplace_orders as orders')
            ->join('logistics_parcels as parcel','parcel.marketplace_order_id','=','orders.id')
            ->join('delivery_route_stops as destination_stop', function ($join): void {
                $join->on('destination_stop.marketplace_order_id','=','orders.id')
                    ->where('destination_stop.stop_key','=','destination_facility');
            })
            ->join('logistics_facilities as destination','destination.id','=','destination_stop.logistics_facility_id')
            ->leftJoin('last_mile_assignment_orders as assigned','assigned.marketplace_order_id','=','orders.id')
            ->whereNull('assigned.id')
            ->where('orders.logistics_account_id',$provider->id)
            ->where('parcel.status','destination_received')
            ->where('orders.status','in_transit')
            ->where('destination.id',$facility->id)
            ->orderBy('orders.id')
            ->get([
                'orders.id','orders.order_number','orders.buyer_name','orders.buyer_address','orders.total',
                'parcel.id as parcel_id','destination.id as destination_facility_id',
                'destination.name as destination_facility_name','destination.municipality_name as destination_municipality',
                'destination.province_name as destination_province',
            ]);

        $groups = $rows->groupBy(function ($row): string {
            $area = LogisticsServiceArea::fromAddress($row->buyer_address, $row->destination_municipality);
            return LogisticsServiceArea::barangay($area) ?: strtolower(trim($area));
        })->map(function ($group, $key): array {
            $first = $group->first();
            $area = LogisticsServiceArea::fromAddress($first->buyer_address, $first->destination_municipality);
            return [
                'key' => (string)$key,
                'delivery_area' => $area,
                'destination_facility_id' => (int)$first->destination_facility_id,
                'destination_facility_name' => (string)$first->destination_facility_name,
                'municipality_name' => (string)$first->destination_municipality,
                'province_name' => (string)$first->destination_province,
                'order_count' => $group->count(),
                'order_ids' => $group->pluck('id')->map(fn($id)=>(int)$id)->values()->all(),
                'orders' => $group->map(fn($r) => [
                    'id'=>(int)$r->id,'order_number'=>(string)$r->order_number,
                    'buyer_name'=>(string)($r->buyer_name ?? ''),'buyer_address'=>(string)($r->buyer_address ?? ''),
                    'total'=>(float)$r->total,'parcel_id'=>(int)$r->parcel_id,
                ])->values()->all(),
            ];
        })->values();

        $riders = CourierAccount::query()
            ->where('logistics_account_id',$provider->id)
            ->where('account_status','active')->where('availability_status','online')
            ->orderBy('first_name')->orderBy('last_name')->get()
            ->map(function (CourierAccount $rider): array {
                $busyPickup = DB::table('marketplace_orders')
                    ->whereRaw('LOWER(TRIM(courier_email)) = ?', [strtolower(trim((string)$rider->email))])
                    ->whereIn('status',['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer'])->exists();
                $busyLastMile = DB::table('last_mile_assignments')
                    ->where('courier_account_id',$rider->id)->whereIn('status',['assigned','out_for_delivery'])->exists();
                return [
                    'id'=>(int)$rider->id,
                    'name'=>trim($rider->first_name.' '.$rider->last_name),
                    'barangay_name'=>(string)($rider->barangay_name ?? ''),
                    'municipality_name'=>(string)($rider->municipality_name ?? ''),
                    'vehicle_type'=>(string)($rider->vehicle_type ?? ''),
                    'busy'=>$busyPickup || $busyLastMile,
                ];
            })->values();

        return response()->json(['success'=>true,'data'=>[
            'facility_id'=>(int)$facility->id,'groups'=>$groups,'riders'=>$riders,
        ]])->header('Cache-Control','no-store');
    }

    public function assign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id'=>['required','integer','min:1'],
            'rider_id'=>['required','integer','exists:courier_accounts,id'],
            'order_ids'=>['required','array','min:1'],
            'order_ids.*'=>['required','integer','distinct','exists:marketplace_orders,id'],
        ]);
        $provider = $this->provider($request);
        $facility = $this->facility($request,(int)$validated['facility_id']);
        $orderIds = collect($validated['order_ids'])->map(fn($id)=>(int)$id)->unique()->sort()->values();

        $result = DB::transaction(function () use ($provider,$facility,$validated,$orderIds): array {
            $orders = DB::table('marketplace_orders as orders')
                ->join('logistics_parcels as parcel','parcel.marketplace_order_id','=','orders.id')
                ->join('delivery_route_stops as destination_stop', function ($join): void {
                    $join->on('destination_stop.marketplace_order_id','=','orders.id')
                        ->where('destination_stop.stop_key','=','destination_facility');
                })
                ->join('logistics_facilities as destination','destination.id','=','destination_stop.logistics_facility_id')
                ->whereIn('orders.id',$orderIds->all())
                ->where('orders.logistics_account_id',$provider->id)
                ->where('parcel.status','destination_received')
                ->where('orders.status','in_transit')
                ->select(['orders.id','orders.order_number','orders.buyer_address','parcel.id as parcel_id',
                    'destination.id as destination_facility_id','destination.name as destination_facility_name',
                    'destination.municipality_name as destination_municipality','destination.province_name as destination_province'])
                ->lockForUpdate()->get();

            if ($orders->count() !== $orderIds->count()) {
                throw ValidationException::withMessages(['order_ids'=>'Some selected parcels are no longer available for last-mile assignment.']);
            }
            if ($orders->contains(fn($o)=>(int)$o->destination_facility_id !== (int)$facility->id)) abort(403);
            if (DB::table('last_mile_assignment_orders')->whereIn('marketplace_order_id',$orderIds->all())->exists()) {
                throw ValidationException::withMessages(['order_ids'=>'One or more selected orders already have a last-mile assignment.']);
            }

            $groupKeys = $orders->map(function ($o): string {
                return (int)$o->destination_facility_id.'|'.LogisticsServiceArea::barangay(
                    LogisticsServiceArea::fromAddress($o->buyer_address,$o->destination_municipality));
            })->unique();
            if ($groupKeys->count() !== 1) {
                throw ValidationException::withMessages(['order_ids'=>'Last-mile batches must contain orders for the same destination hub and barangay.']);
            }

            $first = $orders->first();
            $deliveryArea = LogisticsServiceArea::fromAddress($first->buyer_address,$first->destination_municipality);
            $rider = CourierAccount::query()->whereKey((int)$validated['rider_id'])
                ->where('logistics_account_id',$provider->id)->where('account_status','active')
                ->where('availability_status','online')->lockForUpdate()->firstOrFail();

            $approved = $rider->registration_application_id
                ? RegistrationApplication::query()->whereKey($rider->registration_application_id)
                    ->where('logistics_account_id',$provider->id)->where('role','rider')->where('status','approved')->exists()
                : true;
            if (! $approved) throw ValidationException::withMessages(['rider_id'=>'The selected Rider is not approved for this Logistics network.']);

            $serviceProbe = (object)[
                'destination_province'=>(string)$first->destination_province,
                'destination_municipality'=>(string)$first->destination_municipality,
                'barangay_name'=>$deliveryArea,
            ];
            if (! LogisticsServiceArea::matchesRider($rider,$serviceProbe)) {
                throw ValidationException::withMessages(['rider_id'=>'Select a Rider assigned to '.$deliveryArea.'.']);
            }

            $busyPickup = DB::table('marketplace_orders')->whereRaw('LOWER(TRIM(courier_email)) = ?', [strtolower(trim((string)$rider->email))])
                ->whereIn('status',['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer'])->exists();
            if ($busyPickup) throw ValidationException::withMessages(['rider_id'=>'This Rider already has an active Seller pickup or delivery.']);

            $activeBatches = DB::table('last_mile_assignments')->where('courier_account_id',$rider->id)
                ->whereIn('status',['assigned','out_for_delivery'])->lockForUpdate()->get([
                    'id','assignment_code','status','destination_facility_id','delivery_area']);
            $existing = null;
            if ($activeBatches->isNotEmpty()) {
                if ($activeBatches->count() !== 1 || $activeBatches->first()->status !== 'assigned'
                    || (int)$activeBatches->first()->destination_facility_id !== (int)$facility->id
                    || LogisticsServiceArea::barangay($activeBatches->first()->delivery_area) !== LogisticsServiceArea::barangay($deliveryArea)) {
                    throw ValidationException::withMessages(['rider_id'=>'This Rider has an active route or batch for another hub/barangay.']);
                }
                $existing = $activeBatches->first();
            }

            if ($existing) {
                $assignmentId=(int)$existing->id; $assignmentCode=(string)$existing->assignment_code;
                DB::table('last_mile_assignments')->where('id',$assignmentId)->update(['updated_at'=>now()]);
            } else {
                $assignmentCode='LM-'.now()->format('Ymd-His').'-'.Str::upper(bin2hex(random_bytes(3)));
                $assignmentId=DB::table('last_mile_assignments')->insertGetId([
                    'logistics_account_id'=>$provider->id,'destination_facility_id'=>$facility->id,
                    'courier_account_id'=>$rider->id,'assignment_code'=>$assignmentCode,
                    'delivery_area'=>$deliveryArea,'status'=>'assigned','assigned_at'=>now(),
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }

            foreach ($orderIds as $orderId) {
                DB::table('last_mile_assignment_orders')->insert([
                    'last_mile_assignment_id'=>$assignmentId,'marketplace_order_id'=>$orderId,
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
            $updated=DB::table('logistics_parcels')->whereIn('marketplace_order_id',$orderIds->all())
                ->where('status','destination_received')->update(['status'=>'last_mile_assigned','updated_at'=>now()]);
            if ($updated !== $orderIds->count()) {
                throw ValidationException::withMessages(['order_ids'=>'A parcel changed while creating the last-mile batch.']);
            }

            $riderName=trim($rider->first_name.' '.$rider->last_name) ?: 'SARI Rider';
            $eventKey='last_mile_assigned:assignment:'.$assignmentId.($existing?':added:'.hash('sha256',$orderIds->implode(',')):'');
            $message='Last-Mile batch '.$assignmentCode.' containing '.$orderIds->count().' parcel(s) for '.$deliveryArea.' was assigned to '.$riderName.'.';
            $data=['assignment_id'=>$assignmentId,'assignment_code'=>$assignmentCode,'destination_facility_id'=>(int)$facility->id,
                'rider_id'=>(int)$rider->id,'rider_name'=>$riderName,'delivery_area'=>$deliveryArea,
                'parcel_count'=>$orderIds->count(),'order_ids'=>$orderIds->all()];
            $this->notifications->notifyManager((int)$provider->id,$eventKey,'last_mile_assigned','Last-Mile Rider assigned',$message,
                'logistics.last-mile-assignment',$data);
            $this->notifications->notifyFacilityOperators((int)$provider->id,(int)$facility->id,$eventKey,'last_mile_assigned',
                'Your Last-Mile batch is assigned',$message,'logistics.last-mile-assignment',$data);

            return $data;
        },3);

        return response()->json(['success'=>true,'message'=>'Last-mile batch assigned successfully.','data'=>$result]);
    }
}
