<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Platform\AdminUserActivity;
use App\Services\Buyer\BuyerAccountActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BuyerAccountActivityActionController extends Controller
{
    public function __construct(private readonly BuyerAccountActivityService $activity)
    {
    }

    public function read(Request $request, AdminUserActivity $event): RedirectResponse
    {
        $this->activity->markRead($request, $event);
        return back()->with('success', 'Activity marked as read.');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $this->activity->markAllRead($request);
        return back()->with('success', 'All recent activity marked as read.');
    }

    public function archive(Request $request, AdminUserActivity $event): RedirectResponse
    {
        $this->activity->archive($request, $event);
        return back()->with('success', 'Activity archived.');
    }

    public function restore(Request $request, AdminUserActivity $event): RedirectResponse
    {
        $this->activity->restore($request, $event);
        return back()->with('success', 'Activity restored.');
    }

    public function destroy(Request $request, AdminUserActivity $event): RedirectResponse
    {
        $this->activity->deleteForBuyer($request, $event);
        return back()->with('success', 'Activity removed from your list.');
    }
}
