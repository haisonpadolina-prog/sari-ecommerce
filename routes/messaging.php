<?php

use App\Http\Controllers\PlatformMessagingController;
use Illuminate\Support\Facades\Route;

Route::prefix('messaging/api')->group(function (): void {
    Route::get('/me', [PlatformMessagingController::class, 'me'])
        ->name('messaging.api.me');

    Route::get('/connections', [PlatformMessagingController::class, 'connections'])
        ->name('messaging.api.connections');

    Route::get('/conversations', [PlatformMessagingController::class, 'index'])
        ->name('messaging.api.index');

    Route::post('/conversations/direct', [PlatformMessagingController::class, 'openDirect'])
        ->name('messaging.api.direct');

    Route::post('/support', [PlatformMessagingController::class, 'openSupport'])
        ->name('messaging.api.support');

    Route::post('/reports/{complaint}/conversation', [PlatformMessagingController::class, 'openReportConversation'])
        ->whereNumber('complaint')
        ->name('messaging.api.report-support');

    Route::get('/conversations/{conversation}', [PlatformMessagingController::class, 'show'])
        ->name('messaging.api.show');

    Route::post('/conversations/{conversation}/messages', [PlatformMessagingController::class, 'send'])
        ->name('messaging.api.send');

    Route::get('/messages/{message}/attachment', [PlatformMessagingController::class, 'attachment'])
        ->name('messaging.api.attachment');

    Route::post('/messages/{message}/reaction', [PlatformMessagingController::class, 'react'])
        ->name('messaging.api.react');

    Route::post('/conversations/{conversation}/read', [PlatformMessagingController::class, 'markRead'])
        ->name('messaging.api.read');
});
