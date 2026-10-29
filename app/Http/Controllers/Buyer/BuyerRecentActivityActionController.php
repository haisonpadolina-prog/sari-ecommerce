<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Services\Buyer\BuyerActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BuyerRecentActivityActionController extends Controller
{
    public function __construct(
        private readonly BuyerActivityService $activity,
    ) {
    }

    public function archive(Request $request, MarketplaceOrderEvent $event): RedirectResponse
    {
        $this->activity->archive($request, $event);

        return back()->with('success', 'Activity archived.');
    }

    public function restore(Request $request, MarketplaceOrderEvent $event): RedirectResponse
    {
        $this->activity->restore($request, $event);

        return back()->with('success', 'Activity restored.');
    }

    public function destroy(Request $request, MarketplaceOrderEvent $event): RedirectResponse
    {
        $this->activity->deleteForBuyer($request, $event);

        return back()->with('success', 'Activity deleted from your history.');
    }
}
