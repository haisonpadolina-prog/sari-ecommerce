<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('admin-ai:maintenance')->hourly()->withoutOverlapping();

// Requires the server's cron: * * * * * php /path/to/artisan schedule:run
Schedule::command('sari:mobile-push --limit=100')->everyMinute()->withoutOverlapping();

Schedule::command('sari:mobile-prune')->dailyAt('03:00')->withoutOverlapping();
