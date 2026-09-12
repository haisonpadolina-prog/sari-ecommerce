<?php

use App\Http\Middleware\EnforcePlatformOperationalRules;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Central policy enforcement for the small set of public commerce
         * actions controlled by Admin Platform Settings. The middleware
         * no-ops for every unrelated route.
         */
        $middleware->appendToGroup(
            'web',
            EnforcePlatformOperationalRules::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
