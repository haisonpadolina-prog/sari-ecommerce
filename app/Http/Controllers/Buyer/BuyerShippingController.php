<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerShippingController extends Controller
{
    public function __construct(
        private readonly BuyerActivityService $activity,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        return view('buyer.shipping', [
            'shipments' => $this->activity->shippingHistory($request),
            'activity' => $this->activity,
        ]);
    }
}
