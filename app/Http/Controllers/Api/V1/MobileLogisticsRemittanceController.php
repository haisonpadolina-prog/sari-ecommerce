<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\CodRemittanceBatch;
use App\Services\Finance\CodRemittanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileLogisticsRemittanceController extends Controller
{
    public function __construct(private readonly CodRemittanceService $remittances) {}

    private function provider(Request $request): LogisticsAccount
    {
        /** @var LogisticsAccount $provider */
        $provider=$request->user(); return $provider;
    }

    private function facility(Request $request,int $id): LogisticsFacility
    {
        return LogisticsFacility::query()->forLogistics((int)$this->provider($request)->id)->active()->findOrFail($id);
    }

    public function index(Request $request): JsonResponse
    {
        $v=$request->validate(['facility_id'=>['required','integer','min:1']]);
        $provider=$this->provider($request); $facility=$this->facility($request,(int)$v['facility_id']);
        $base=CodRemittanceBatch::query()->with(['courier:id,first_name,last_name,email','facility:id,name'])
            ->where('logistics_account_id',$provider->id)->where('logistics_facility_id',$facility->id);
        $rows=(clone $base)->latest('submitted_at')->limit(100)->get()->map(fn(CodRemittanceBatch $b)=>[
            'id'=>(int)$b->id,'batch_code'=>(string)$b->batch_code,'status'=>(string)$b->status,
            'expected_amount'=>(float)$b->expected_amount,'submitted_amount'=>(float)$b->submitted_amount,
            'confirmed_amount'=>$b->confirmed_amount === null ? null : (float)$b->confirmed_amount,
            'difference_amount'=>$b->difference_amount === null ? null : (float)$b->difference_amount,
            'rider_name'=>trim(($b->courier?->first_name ?? '').' '.($b->courier?->last_name ?? '')),
            'submitted_at'=>$b->submitted_at?->toIso8601String(),'verified_at'=>$b->verified_at?->toIso8601String(),
        ])->values();
        $stats=[
            'submitted'=>(float)(clone $base)->where('status','submitted')->sum('expected_amount'),
            'verified'=>(float)(clone $base)->where('status','verified')->sum('confirmed_amount'),
            'discrepancy'=>(float)(clone $base)->where('status','discrepancy')->selectRaw('COALESCE(SUM(ABS(difference_amount)),0) total')->value('total'),
            'pending_count'=>(clone $base)->whereIn('status',['submitted','discrepancy'])->count(),
        ];
        return response()->json(['success'=>true,'data'=>['facility_id'=>(int)$facility->id,'items'=>$rows,'stats'=>$stats]])->header('Cache-Control','no-store');
    }

    public function show(Request $request,CodRemittanceBatch $batch): JsonResponse
    {
        $v=$request->validate(['facility_id'=>['required','integer','min:1']]);
        $provider=$this->provider($request); $facility=$this->facility($request,(int)$v['facility_id']);
        abort_unless((int)$batch->logistics_account_id === (int)$provider->id,404);
        abort_unless((int)$batch->logistics_facility_id === (int)$facility->id,403);
        $batch->load(['courier','facility','items.order']);
        return response()->json(['success'=>true,'data'=>[
            'id'=>(int)$batch->id,'batch_code'=>$batch->batch_code,'status'=>$batch->status,
            'expected_amount'=>(float)$batch->expected_amount,'submitted_amount'=>(float)$batch->submitted_amount,
            'confirmed_amount'=>$batch->confirmed_amount === null ? null : (float)$batch->confirmed_amount,
            'difference_amount'=>$batch->difference_amount === null ? null : (float)$batch->difference_amount,
            'rider_name'=>trim(($batch->courier?->first_name ?? '').' '.($batch->courier?->last_name ?? '')),
            'items'=>$batch->items->map(fn($item)=>[
                'order_id'=>(int)$item->marketplace_order_id,'order_number'=>(string)($item->order?->order_number ?? ''),
                'expected_amount'=>(float)$item->expected_amount,
            ])->values(),
        ]])->header('Cache-Control','no-store');
    }

    public function verify(Request $request,CodRemittanceBatch $batch): JsonResponse
    {
        $v=$request->validate([
            'facility_id'=>['required','integer','min:1'],
            'confirmed_amount'=>['required','numeric','min:0','max:999999999.99'],
            'logistics_reference'=>['nullable','string','max:191'],
            'logistics_note'=>['nullable','string','max:1000'],
        ]);
        $provider=$this->provider($request); $facility=$this->facility($request,(int)$v['facility_id']);
        abort_unless((int)$batch->logistics_account_id === (int)$provider->id,404);
        abort_unless((int)$batch->logistics_facility_id === (int)$facility->id,403);
        $verified=$this->remittances->verifyBatch($batch,$provider,(float)$v['confirmed_amount'],'logistics_manager',(int)$provider->id,
            $v['logistics_reference'] ?? null,$v['logistics_note'] ?? null);
        return response()->json([
            'success'=>$verified->status === 'verified',
            'message'=>$verified->status === 'verified'
                ? ($facility->is_main_branch
                    ? 'COD cash verified at the Main Branch and centrally reconciled.'
                    : 'COD cash verified at '.$facility->name.'. Rider custody is cleared; branch treasury remains pending.')
                : 'Physical cash count does not match the expected amount.',
            'data'=>[
                'id'=>(int)$verified->id,'status'=>(string)$verified->status,
                'expected_amount'=>(float)$verified->expected_amount,
                'confirmed_amount'=>(float)($verified->confirmed_amount ?? 0),
                'difference_amount'=>(float)($verified->difference_amount ?? 0),
            ],
        ],$verified->status === 'verified' ? 200 : 422);
    }
}
