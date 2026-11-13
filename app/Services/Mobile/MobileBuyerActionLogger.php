<?php

namespace App\Services\Mobile;

use App\Models\Accounts\BuyerAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class MobileBuyerActionLogger
{
    public function record(BuyerAccount $buyer, string $action): void
    {
        try {
            if (!Schema::hasTable('admin_user_activities')) {
                return;
            }
            AdminUserActivity::query()->create([
                'admin_account_id'=>null, 'user_role'=>'buyer', 'user_id'=>(int)$buyer->id,
                'action'=>$action, 'description'=>'Buyer '.($action === 'login' ? 'signed in' : 'signed out').' on SARI mobile.',
                'metadata'=>['source'=>'mobile'],
            ]);
        } catch (Throwable $e) {
            // Authentication should not be blocked by activity audit availability.
            Log::warning('Mobile Buyer activity logging failed', ['exception'=>get_class($e)]);
        }
    }
}
