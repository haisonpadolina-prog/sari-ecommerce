<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class PruneMobileData extends Command
{
    protected $signature = 'sari:mobile-prune';
    protected $description = 'Delete stale latest-Rider positions and old push-delivery receipts';

    public function handle(): int
    {
        $locations = DB::table('sari_mobile_rider_locations')
            ->where('recorded_at', '<', now()->subDay())->delete();
        $receipts = DB::table('sari_mobile_push_receipts')
            ->where('sent_at', '<', now()->subDays(30))->delete();
        $devices = DB::table('sari_mobile_devices')
            ->where('last_seen_at', '<', now()->subDays(90))->delete();
        $this->info("Pruned {$locations} locations, {$receipts} push receipts, {$devices} devices.");
        return self::SUCCESS;
    }
}
