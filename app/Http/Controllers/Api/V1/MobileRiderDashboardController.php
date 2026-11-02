<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\RiderEarning;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileRiderDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $email = strtolower(trim((string) $rider->email));

        $assigned = MarketplaceOrder::query()
            ->with(['seller:id,store_name', 'logisticsParcel'])
            ->whereRaw('LOWER(courier_email) = ?', [$email]);

        $directActive = (clone $assigned)
            ->whereDoesntHave('logisticsParcel')
            ->whereIn('status', [
                'courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer',
            ])
            ->count();

        $pickupActive = (clone $assigned)
            ->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit'])
            ->whereHas('logisticsParcel', fn (Builder $parcel) => $parcel->where('status', 'awaiting_intake'))
            ->count();

        $current = (clone $assigned)
            ->where(function (Builder $query): void {
                $query->where(function (Builder $direct): void {
                    $direct->whereDoesntHave('logisticsParcel')
                        ->whereIn('status', [
                            'courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer',
                        ]);
                })->orWhere(function (Builder $pickup): void {
                    $pickup->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit'])
                        ->whereHas('logisticsParcel', fn (Builder $parcel) => $parcel->where('status', 'awaiting_intake'));
                });
            })
            ->latest('updated_at')
            ->first();

        $lastMileActive = DB::table('last_mile_assignments')
            ->where('courier_account_id', $rider->id)
            ->whereIn('status', ['assigned', 'out_for_delivery'])
            ->count();

        $completedToday = RiderEarning::query()
            ->where('courier_account_id', $rider->id)
            ->whereDate('earned_at', now()->toDateString())
            ->count();

        $todayEarnings = (float) RiderEarning::query()
            ->where('courier_account_id', $rider->id)
            ->whereDate('earned_at', now()->toDateString())
            ->sum('delivery_fee_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'rider' => [
                    'id' => (int) $rider->id,
                    'name' => trim($rider->first_name.' '.$rider->last_name),
                    'email' => $rider->email,
                    'availability_status' => $rider->availability_status === 'online' ? 'online' : 'offline',
                    'rating' => (float) ($rider->rating ?? 5),
                ],
                'summary' => [
                    'pickup_active' => $pickupActive,
                    'direct_delivery_active' => $directActive,
                    'last_mile_active' => $lastMileActive,
                    'completed_today' => $completedToday,
                    'earnings_today' => round($todayEarnings, 2),
                    'unread_messages' => LogisticsMessage::query()
                        ->where('courier_account_id', $rider->id)
                        ->where('sender_role', 'logistics')
                        ->whereNull('read_at')
                        ->count(),
                ],
                'current_assignment' => $current ? [
                    'id' => (int) $current->id,
                    'order_number' => $current->order_number,
                    'status' => $current->status,
                    'status_label' => $current->statusLabel(),
                    'seller_name' => $current->seller?->store_name,
                    'buyer_name' => $current->buyer_name,
                    'buyer_address' => $current->buyer_address,
                    'is_logistics_pickup' => (bool) $current->logisticsParcel,
                ] : null,
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
