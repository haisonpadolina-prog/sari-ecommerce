<?php

namespace App\Services\Mobile;

use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\DB;

final class MobileRiderTracking
{
    public function assertOwnedActiveJob(CourierAccount $rider, string $type, int $job): void
    {
        if ($type === 'order') {
            $ok = MarketplaceOrder::query()->whereKey($job)
                ->whereRaw('LOWER(courier_email) = ?', [strtolower(trim((string) $rider->email))])
                ->whereIn('status', ['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer'])
                ->exists();
        } else {
            $ok = DB::table('last_mile_assignments')->where('id', $job)
                ->where('courier_account_id', $rider->id)->where('status','out_for_delivery')->exists();
        }
        abort_unless($ok, 404, 'Active assignment not found.');
    }

    /** No stale data or stopped sharing; never expose location of an unrelated assignment. */
    public function current(int $riderId, string $type, int $job): ?array
    {
        $row = DB::table('sari_mobile_rider_locations')->where('courier_account_id', $riderId)
            ->where('assignment_type', $type)->where('assignment_id', $job)
            ->where('sharing_enabled', true)->where('recorded_at', '>=', now()->subMinutes(2))
            ->first();
        return $row ? [
            'latitude' => (float) $row->latitude,
            'longitude' => (float) $row->longitude,
            'accuracy_m' => $row->accuracy_m === null ? null : (int) $row->accuracy_m,
            'recorded_at' => $row->recorded_at,
            'assignment_type' => $type,
            'assignment_id' => $job,
        ] : null;
    }
}
