<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Platform\AdminUserActivity;
use App\Services\Buyer\BuyerAccountActivityService;
use App\Services\Mobile\MobileBuyerSessionBridge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MobileBuyerRecentActivityController extends Controller
{
    public function __construct(private readonly BuyerAccountActivityService $activity,
                                private readonly MobileBuyerSessionBridge $bridge) {}

    public function index(Request $request): JsonResponse
    {
        $this->bridge->attach($request);
        $input = $request->validate([
            'per_page'=>['nullable','integer','min:5','max:50'],
            'archived'=>['nullable','boolean'],
        ]);
        $page = $this->activity->paginate($request, (int)($input['per_page'] ?? 10), $request->boolean('archived'));
        return response()->json(['success'=>true,'data'=>collect($page->items())->map(static fn($row)=>[
            'id'=>(int)$row->id,'title'=>$row->display_title,'message'=>$row->display_message,
            'action'=>(string)$row->action,'is_read'=>(bool)$row->is_read,
            'created_at'=>$row->created_at?->toIso8601String(),
        ])->values(),'meta'=>['current_page'=>$page->currentPage(), 'last_page'=>$page->lastPage(),
            'total'=>$page->total()], 'unread_count'=>$this->activity->unreadCount($request)])
            ->header('Cache-Control','private, no-store');
    }

    public function summary(Request $request): JsonResponse
    {
        $this->bridge->attach($request);
        return response()->json(['success'=>true,'data'=>['unread_count'=>$this->activity->unreadCount($request)]])
            ->header('Cache-Control','private, no-store');
    }

    public function read(Request $request, AdminUserActivity $event): JsonResponse
    {
        $this->bridge->attach($request);
        $this->activity->markRead($request,$event);
        return $this->ok();
    }

    public function readAll(Request $request): JsonResponse
    {
        $this->bridge->attach($request);
        $this->activity->markAllRead($request);
        return $this->ok();
    }

    public function archive(Request $request, AdminUserActivity $event): JsonResponse
    {
        $this->bridge->attach($request);
        $this->activity->archive($request,$event);
        return $this->ok();
    }

    public function restore(Request $request, AdminUserActivity $event): JsonResponse
    {
        $this->bridge->attach($request);
        $this->activity->restore($request,$event);
        return $this->ok();
    }

    public function destroy(Request $request, AdminUserActivity $event): JsonResponse
    {
        $this->bridge->attach($request);
        $this->activity->deleteForBuyer($request,$event);
        return $this->ok();
    }

    private function ok(): JsonResponse { return response()->json(['success'=>true]); }
}
