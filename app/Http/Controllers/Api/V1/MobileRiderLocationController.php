<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Mobile\MobileRiderTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MobileRiderLocationController extends Controller
{
    public function __construct(private readonly MobileRiderTracking $tracking) {}

    public function upload(Request $request): JsonResponse
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        $input = $request->validate([
            'assignment_type' => ['required', Rule::in(['order','last_mile'])],
            'assignment_id' => ['required', 'integer', 'min:1'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_m' => ['nullable', 'integer', 'between:0,150'],
            'recorded_at' => ['required', 'date'],
        ]);
        $capturedAt = Carbon::parse($input['recorded_at']);
        if ($capturedAt->lt(now()->subMinutes(2)) || $capturedAt->gt(now()->addSeconds(30))) {
            throw ValidationException::withMessages(['recorded_at' => 'Upload only a recent GPS reading.']);
        }
        $this->tracking->assertOwnedActiveJob($rider, $input['assignment_type'], (int)$input['assignment_id']);
        DB::table('sari_mobile_rider_locations')->updateOrInsert(
            ['courier_account_id'=>$rider->id, 'assignment_type'=>$input['assignment_type'],
             'assignment_id'=>(int)$input['assignment_id']],
            ['latitude'=>$input['latitude'],'longitude'=>$input['longitude'],
             'accuracy_m'=>$input['accuracy_m'] ?? null,'sharing_enabled'=>true,
             'recorded_at'=>$capturedAt,'updated_at'=>now()]
        );
        return response()->json(['success'=>true,'message'=>'GPS location updated.'])
            ->header('Cache-Control','private, no-store');
    }

    public function stop(Request $request): JsonResponse
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        $input = $request->validate([
            'assignment_type' => ['required', Rule::in(['order','last_mile'])],
            'assignment_id' => ['required','integer','min:1'],
        ]);
        DB::table('sari_mobile_rider_locations')->where('courier_account_id',$rider->id)
            ->where('assignment_type',$input['assignment_type'])
            ->where('assignment_id',(int)$input['assignment_id'])
            ->update(['sharing_enabled'=>false,'updated_at'=>now()]);
        return response()->json(['success'=>true,'message'=>'Location sharing stopped.']);
    }

    public function buyerOrder(Request $request, MarketplaceOrder $order): JsonResponse
    {
        abort_unless((int)$order->buyer_account_id === (int)$request->user()->id, 404);
        // Only show location if the delivery is actively underway.
        abort_unless(in_array($order->status,
            ['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer','out_for_delivery'], true), 404);
        $lastMile = DB::table('last_mile_assignment_orders as ao')
            ->join('last_mile_assignments as a','a.id','=','ao.last_mile_assignment_id')
            ->where('ao.marketplace_order_id',$order->id)
            ->first(['a.id','a.courier_account_id']);
        if ($lastMile) {
            $isActive = DB::table('last_mile_assignments')->where('id',$lastMile->id)->where('status','out_for_delivery')->exists();
            $position = $isActive ? $this->tracking->current((int)$lastMile->courier_account_id,'last_mile',(int)$lastMile->id) : null;
        } else {
            $rider = CourierAccount::query()
                ->whereRaw('LOWER(email) = ?', [strtolower(trim((string)$order->courier_email))])->first();
            $position = $rider ? $this->tracking->current((int)$rider->id,'order',(int)$order->id) : null;
        }
        return response()->json(['success'=>true,'data'=>['location'=>$position]])
            ->header('Cache-Control','private, no-store');
    }

    public function logisticsRider(Request $request, CourierAccount $rider): JsonResponse
    {
        /** @var LogisticsAccount $manager */
        $manager = $request->user();
        abort_unless((int)$rider->logistics_account_id === (int)$manager->id, 404);
        $positions = DB::table('sari_mobile_rider_locations')
            ->where('courier_account_id',$rider->id)->where('sharing_enabled',true)
            ->where('recorded_at','>=',now()->subMinutes(2))->latest('recorded_at')->limit(3)->get();
        $location = null;
        foreach ($positions as $row) {
            if ($row->assignment_type === 'order') {
                $allowed = MarketplaceOrder::query()->whereKey($row->assignment_id)
                    ->where('logistics_account_id',$manager->id)
                    ->whereRaw('LOWER(courier_email) = ?', [strtolower((string)$rider->email)])
                    ->whereIn('status',['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer'])->exists();
            } else {
                $allowed = DB::table('last_mile_assignments')->where('id',$row->assignment_id)
                    ->where('logistics_account_id',$manager->id)
                    ->where('courier_account_id',$rider->id)->where('status','out_for_delivery')->exists();
            }
            if ($allowed) {
                $location = $this->tracking->current((int)$rider->id,(string)$row->assignment_type,(int)$row->assignment_id);
                break;
            }
        }
        return response()->json(['success'=>true,'data'=>['location'=>$location]])
            ->header('Cache-Control','private, no-store');
    }
}
