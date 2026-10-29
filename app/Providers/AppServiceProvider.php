<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Accounts\SellerAccount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::viaRequest('sari-chat-session', static function (Request $request): ?GenericUser {
            if ($request->session()->get('is_seller')) {
                $id = (int) $request->session()->get('seller_account_id');
                if ($id > 0 && SellerAccount::query()->whereKey($id)->exists()) {
                    return new GenericUser(['id' => 'seller-' . $id]);
                }
            }
            if ($request->session()->get('is_buyer')) {
                $accountId = (int) $request->session()->get('buyer_account_id');
                $socialId = (int) $request->session()->get('buyer_social_account_id');
                if ($accountId > 0 && BuyerAccount::query()->whereKey($accountId)->exists()) {
                    return new GenericUser(['id' => 'buyer-account-' . $accountId]);
                }
                if ($socialId > 0 && SocialAccount::query()->whereKey($socialId)->exists()) {
                    return new GenericUser(['id' => 'buyer-social-' . $socialId]);
                }
            }
            return null;
        });

        // Application routes are registered from routes/web.php.
        // Keeping route loading out of this provider prevents the entire
        // application from failing to boot when an optional route file is
        // missing during a partial deployment.
    }
}
