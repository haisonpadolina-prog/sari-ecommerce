<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\CodRemittanceBatch;
use App\Services\Finance\CodRemittanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourierRemittanceController extends Controller
{
    public function __construct(
        private readonly CodRemittanceService $remittances
    ) {
    }

    public function index(Request $request): View
    {
        $rider = $this->rider($request);
        $eligibleOrders = $this->remittances->eligibleOrdersForRider($rider);
        $expectedTotal = round((float) $eligibleOrders->sum('total'), 2);

        $facilities = LogisticsFacility::query()
            ->active()
            ->when(
                $rider->logistics_account_id,
                fn ($query) => $query->forLogistics((int) $rider->logistics_account_id)
            )
            ->orderBy('name')
            ->get();

        $batches = CodRemittanceBatch::query()
            ->with(['facility'])
            ->withCount('items')
            ->where('courier_account_id', $rider->id)
            ->latest('submitted_at')
            ->limit(20)
            ->get();

        return view('courier.remittances', compact(
            'rider',
            'eligibleOrders',
            'expectedTotal',
            'facilities',
            'batches'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $rider = $this->rider($request);

        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer', 'distinct'],
            'logistics_facility_id' => ['required', 'integer'],
            'rider_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $batch = $this->remittances->submitBatch(
            $rider,
            $validated['order_ids'],
            null,
            'cash_hub',
            null,
            $validated['rider_note'] ?? null,
            (int) $validated['logistics_facility_id']
        );

        return redirect()
            ->route('courier.remittances.index')
            ->with(
                'success',
                'Cash remittance '.$batch->batch_code.' prepared. Bring the full physical COD cash to '.($batch->hub_name_snapshot ?: 'the selected Logistics hub').' and present the batch QR.'
            );
    }

    private function rider(Request $request): CourierAccount
    {
        abort_unless($request->session()->get('is_courier'), 403, 'Rider session required.');

        $id = (int) $request->session()->get('courier_account_id', 0);
        $rider = $id > 0
            ? CourierAccount::query()->whereKey($id)->where('account_status', 'active')->first()
            : null;

        abort_unless($rider, 403, 'An active Rider account is required.');

        return $rider;
    }
}
