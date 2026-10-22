<?php

use App\Http\Middleware\Platform\EnforcePlatformOperationalRules;
use App\Http\Middleware\Platform\RecordUserActivity;
use App\Http\Middleware\Platform\RecordAdminActivity;
use App\Http\Middleware\Platform\RecordSecurityEvents;
use App\Http\Middleware\Shared\EnsureSessionAccountAccessible;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->appendToGroup(
            'web',
            EnsureSessionAccountAccessible::class
        );

        $middleware->appendToGroup(
            'web',
            EnforcePlatformOperationalRules::class
        );

        $middleware->appendToGroup(
            'web',
            RecordUserActivity::class
        );

        $middleware->appendToGroup(
            'web',
            RecordAdminActivity::class
        );

        $middleware->appendToGroup(
            'web',
            RecordSecurityEvents::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();