<?php

namespace App\Http\Controllers;

use App\Models\CourierAccount;
use App\Models\LogisticsParcel;
use App\Models\MarketplaceOrder;
use App\Models\RegistrationApplication;
use App\Services\MarketplaceOrderWorkflowService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsDeliveryAssignmentController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        $approvedRiderApplicationIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        $riders = CourierAccount::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('account_status', 'active')
            ->where('availability_status', 'online')
            ->whereIn('registration_application_id', $approvedRiderApplicationIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $busyRiderEmails = MarketplaceOrder::query()
            ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
            ->whereNotNull('courier_email')
            ->pluck('courier_email')
            ->filter()
            ->map(fn ($email): string => strtolower((string) $email))
            ->unique()
            ->values()
            ->all();

        return view('logistics.delivery-assignment', compact(
            'orders',
            'riders',
            'busyRiderEmails'
        ));
    }

    public function assign(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $validated = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:courier_accounts,id'],
        ]);

        $logistics = CurrentLogisticsAccount::resolve($request);

        DB::transaction(function () use ($order, $validated, $logistics): void {
            $lockedOrder = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedOrder->status !== 'ready_for_pickup'
                || $lockedOrder->courier_email
            ) {
                throw ValidationException::withMessages([
                    'order' => 'This order is no longer available for Logistics assignment.',
                ]);
            }

            $rider = CourierAccount::query()
                ->whereKey((int) $validated['rider_id'])
                ->where(function ($query) use ($logistics): void {
                    $query->where('logistics_account_id', $logistics->id)
                        ->orWhereNull('logistics_account_id');
                })
                ->lockForUpdate()
                ->firstOrFail();

            $approvedRider = RegistrationApplication::query()
                ->whereKey($rider->registration_application_id)
                ->where('role', 'rider')
                ->where('status', 'approved')
                ->exists();

            if (
                !$approvedRider
                || (
                    $rider->logistics_account_id
                    && (int) $rider->logistics_account_id !== (int) $logistics->id
                )
                || $rider->account_status !== 'active'
                || $rider->availability_status !== 'online'
            ) {
                throw ValidationException::withMessages([
                    'rider_id' => 'Only approved, active and online Riders can receive deliveries.',
                ]);
            }

            if (!$rider->logistics_account_id) {
                $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

                if ($rider->registration_application_id) {
                    RegistrationApplication::query()
                        ->whereKey($rider->registration_application_id)
                        ->whereNull('logistics_account_id')
                        ->update(['logistics_account_id' => $logistics->id]);
                }
            }

            $hasActiveDelivery = MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)])
                ->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)
                ->lockForUpdate()
                ->exists();

            if ($hasActiveDelivery) {
                throw ValidationException::withMessages([
                    'rider_id' => 'This Rider already has an active delivery.',
                ]);
            }

            $riderName = trim(
                $rider->first_name . ' ' .
                ($rider->middle_initial ? $rider->middle_initial . '. ' : '') .
                $rider->last_name
            );

            $lockedOrder->forceFill([
                'status' => 'courier_accepted',
                'courier_name' => $riderName ?: 'SARI Rider',
                'courier_email' => strtolower((string) $rider->email),
                'accepted_at' => now(),
            ])->save();

            LogisticsParcel::query()->updateOrCreate(
                ['marketplace_order_id' => $lockedOrder->id],
                [
                    'status' => 'awaiting_intake',
                    'sorting_zone' => null,
                    'received_at' => null,
                    'sorted_at' => null,
                ]
            );

            $this->workflow->record(
                $lockedOrder,
                'both',
                'courier_accepted',
                'Rider Assigned by Logistics',
                ($riderName ?: 'A SARI Rider') .
                    ' was assigned by SARI Logistics to order ' .
                    $lockedOrder->order_number . '.'
            );
        });

        return back()->with('success', 'Rider assigned successfully.');
    }
}
