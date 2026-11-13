<?php

use App\Http\Controllers\Admin\AdminAiAssistantController;
use App\Http\Middleware\Admin\EnsureAdminAiAccess;
use App\Http\Middleware\Admin\ThrottleAdminAiRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/ai')->name('admin.ai.')->middleware(EnsureAdminAiAccess::class)->group(function () {
    Route::get('/status', [AdminAiAssistantController::class, 'status'])
        ->middleware(ThrottleAdminAiRequests::class.':30,1')->name('status');
    Route::get('/alerts', [AdminAiAssistantController::class, 'alerts'])
        ->middleware(ThrottleAdminAiRequests::class.':15,1')->name('alerts');
    Route::post('/read', [AdminAiAssistantController::class, 'read'])->middleware(ThrottleAdminAiRequests::class.':15,1')->name('read');
    Route::post('/prepare', [AdminAiAssistantController::class, 'prepare'])->middleware(ThrottleAdminAiRequests::class.':10,1')->name('prepare');
    Route::post('/chat', [AdminAiAssistantController::class, 'chat'])
        ->middleware(ThrottleAdminAiRequests::class.':6,1')->name('chat');
    Route::post('/translate', [AdminAiAssistantController::class, 'translate'])
        ->middleware(ThrottleAdminAiRequests::class.':6,1')->name('translate');
    Route::post('/actions/{action}', [AdminAiAssistantController::class, 'decide'])
        ->middleware(ThrottleAdminAiRequests::class.':15,1')->whereUuid('action')->name('decide');
    Route::delete('/threads/{thread}', [AdminAiAssistantController::class, 'forget'])
        ->middleware(ThrottleAdminAiRequests::class.':15,1')->whereUuid('thread')->name('forget');
});
