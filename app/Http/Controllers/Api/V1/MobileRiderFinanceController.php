<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Finance\RiderEarning;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Finance\CodRemittanceService;
use App\Services\Finance\PayoutService;
use App\Services\Finance\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileRiderFinanceController extends Controller
{
    public function __construct(
        private readonly WalletService $wallets,
        private readonly PayoutService $payouts,
        private readonly CodRemittanceService $remittances,
    ) {}

    public function earnings(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $wallet = $this->wallets->wallet('courier', $rider->id);
        $ledger = RiderEarning::query()
            ->with('order')
            ->where('courier_account_id', $rider->id)
            ->latest('earned_at')
            ->get();

        $today = now('Asia/Manila')->startOfDay();
        $week = now('Asia/Manila')->startOfWeek();
        $month = now('Asia/Manila')->startOfMonth();

        $sumSince = function ($boundary) use ($ledger): float {
            return (float) $ledger
                ->filter(fn (RiderEarning $earning) => $earning->earned_at
                    ?->copy()
                    ->setTimezone('Asia/Manila')
                    ->greaterThanOrEqualTo($boundary) ?? false)
                ->sum('delivery_fee_amount');
        };

        return response()->json([
            'success' => true,
            'data' => [
                'today' => round($sumSince($today), 2),
                'this_week' => round($sumSince($week), 2),
                'this_month' => round($sumSince($month), 2),
                'awaiting_cod_remittance' => (float) $ledger
                    ->where('status', 'pending_remittance')
                    ->sum('delivery_fee_amount'),
                'available_for_payout' => (float) $wallet->available_balance,
                'reserved_for_payout' => (float) $wallet->reserved_balance,
                'paid' => (float) $wallet->lifetime_payouts,
                'completed_delivery_count' => $ledger
                    ->whereIn('earning_type', ['direct_delivery', 'last_mile_leg'])
                    ->count(),
                'daily' => collect(range(6, 0))->map(function (int $daysAgo) use ($ledger): array {
                    $date = now('Asia/Manila')->subDays($daysAgo);
                    return [
                        'day' => $date->format('D'),
                        'amount' => (float) $ledger
                            ->filter(fn (RiderEarning $earning) => $earning->earned_at
                                ?->copy()
                                ->setTimezone('Asia/Manila')
                                ->isSameDay($date) ?? false)
                            ->sum('delivery_fee_amount'),
                    ];
                })->values(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function transactions(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $rows = RiderEarning::query()
            ->with('order:id,order_number,status,buyer_name')
            ->where('courier_account_id', $rider->id)
            ->latest('earned_at')
            ->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $rows->getCollection()->map(fn (RiderEarning $earning) => [
                'id' => (int) $earning->id,
                'order_id' => (int) $earning->marketplace_order_id,
                'order_number' => $earning->order?->order_number,
                'buyer_name' => $earning->order?->buyer_name,
                'earning_type' => $earning->earning_type,
                'amount' => (float) $earning->delivery_fee_amount,
                'status' => $earning->status,
                'earned_at' => $earning->earned_at?->toIso8601String(),
                'released_at' => $earning->released_at?->toIso8601String(),
                'paid_at' => $earning->paid_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function payoutIndex(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $rows = FinancePayoutRequest::query()
            ->where('owner_type', 'courier')
            ->where('owner_id', $rider->id)
            ->latest('requested_at')
            ->limit(50)
            ->get()
            ->map(fn (FinancePayoutRequest $payout) => [
                'id' => (int) $payout->id,
                'request_code' => $payout->request_code,
                'amount' => (float) $payout->amount,
                'currency' => $payout->currency,
                'payout_method' => $payout->payout_method,
                'destination_label' => $payout->destination_label,
                'status' => $payout->status,
                'requested_at' => $payout->requested_at?->toIso8601String(),
                'reviewed_at' => $payout->reviewed_at?->toIso8601String(),
                'paid_at' => $payout->paid_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $rows->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function payoutStore(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'payout_method' => ['required', 'in:gcash,maya,bank,manual_cash'],
            'account_name' => ['required', 'string', 'min:2', 'max:120'],
            'account_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);

        $payout = $this->payouts->request(
            'courier',
            $rider->id,
            (float) $validated['amount'],
            $validated['payout_method'],
            $validated['account_name'],
            $validated['account_reference']
        );

        return response()->json([
            'success' => true,
            'message' => 'Payout request submitted.',
            'data' => [
                'id' => (int) $payout->id,
                'request_code' => $payout->request_code,
                'amount' => (float) $payout->amount,
                'status' => $payout->status,
            ],
        ], 201);
    }

    public function remittanceEligible(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $orders = $this->remittances->eligibleOrdersForRider($rider);

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn (MarketplaceOrder $order) => [
                'id' => (int) $order->id,
                'order_number' => $order->order_number,
                'buyer_name' => $order->buyer_name,
                'total' => (float) $order->total,
                'delivered_at' => $order->delivered_at?->toIso8601String(),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function remittanceIndex(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $rows = CodRemittanceBatch::query()
            ->with(['items.order', 'facility'])
            ->where('courier_account_id', $rider->id)
            ->latest('id')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ])->header('Cache-Control', 'no-store');
    }

    public function remittanceShow(Request $request, CodRemittanceBatch $batch): JsonResponse
    {
        $rider = $this->rider($request);
        abort_unless((int) $batch->courier_account_id === (int) $rider->id, 404);

        return response()->json([
            'success' => true,
            'data' => $batch->load(['items.order', 'facility']),
        ])->header('Cache-Control', 'no-store');
    }

    public function remittanceStore(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:marketplace_orders,id'],
            'logistics_facility_id' => ['nullable', 'integer', 'exists:logistics_facilities,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $batch = $this->remittances->submitBatch(
            $rider,
            $validated['order_ids'],
            null,
            'cash_hub',
            null,
            $validated['note'] ?? null,
            isset($validated['logistics_facility_id']) ? (int) $validated['logistics_facility_id'] : null,
        );

        return response()->json([
            'success' => true,
            'message' => 'COD cash remittance batch prepared.',
            'data' => $batch->load(['items.order', 'facility']),
        ], 201);
    }

    public function history(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = RiderEarning::query()
            ->with('order')
            ->where('courier_account_id', $rider->id)
            ->whereIn('earning_type', ['direct_delivery', 'last_mile_leg'])
            ->when(filled($validated['date_from'] ?? null), fn ($q) => $q->whereDate('earned_at', '>=', $validated['date_from']))
            ->when(filled($validated['date_to'] ?? null), fn ($q) => $q->whereDate('earned_at', '<=', $validated['date_to']))
            ->when(filled($validated['q'] ?? null), function ($q) use ($validated): void {
                $search = '%'.$validated['q'].'%';
                $q->whereHas('order', fn ($orders) => $orders
                    ->where('order_number', 'like', $search)
                    ->orWhere('buyer_name', 'like', $search));
            })
            ->latest('earned_at');

        $rows = $query->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'data' => $rows->getCollection()->map(fn (RiderEarning $earning) => [
                'earning_id' => (int) $earning->id,
                'order_id' => (int) $earning->marketplace_order_id,
                'order_number' => $earning->order?->order_number,
                'customer' => $earning->order?->buyer_name,
                'route' => collect([
                    $earning->order?->pickup_address,
                    $earning->order?->buyer_address,
                ])->filter()->implode(' → '),
                'fee' => (float) $earning->delivery_fee_amount,
                'earning_type' => $earning->earning_type,
                'status' => $earning->status,
                'earned_at' => $earning->earned_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
