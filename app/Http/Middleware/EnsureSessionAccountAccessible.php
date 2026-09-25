<?php

namespace App\Http\Middleware;

use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\SellerAccount;
use App\Models\SocialAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionAccountAccessible
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('is_admin')) {
            return $next($request);
        }

        $account = null;
        $sessionKeys = [];

        if ($request->session()->get('is_seller')) {
            $account = SellerAccount::query()->find(
                (int) $request->session()->get('seller_account_id')
            );
            $sessionKeys = ['is_seller', 'seller_account_id'];
        } elseif ($request->session()->get('is_buyer')) {
            $buyerId = (int) $request->session()->get('buyer_account_id', 0);
            $socialId = (int) $request->session()->get('buyer_social_account_id', 0);

            if ($buyerId > 0) {
                $account = BuyerAccount::query()->find($buyerId);
            } elseif ($socialId > 0) {
                $account = SocialAccount::query()->find($socialId);
            }

            $sessionKeys = [
                'is_buyer',
                'buyer_account_id',
                'buyer_social_account_id',
                'buyer_email',
                'buyer_name',
                'buyer_avatar',
                'auth_provider',
            ];
        } elseif ($request->session()->get('is_courier')) {
            $courierId = (int) $request->session()->get('courier_account_id', 0);
            $account = $courierId > 0
                ? CourierAccount::query()->find($courierId)
                : null;

            $sessionKeys = [
                'is_courier',
                'courier_account_id',
                'courier_email',
                'courier_name',
            ];
        } elseif ($request->session()->get('is_logistics')) {
            $account = LogisticsAccount::query()->find(
                (int) $request->session()->get('logistics_account_id')
            );

            $sessionKeys = [
                'is_logistics',
                'logistics_account_id',
                'logistics_email',
                'logistics_name',
            ];
        }

        if (!$account) {
            return $next($request);
        }

        $status = strtolower((string) ($account->account_status ?? 'active'));

        if (!in_array($status, ['banned', 'deactivated'], true)) {
            return $next($request);
        }

        $request->session()->forget($sessionKeys);

        $message = $status === 'banned'
            ? 'This SARI account has been banned by an administrator.'
            : 'This SARI account is currently suspended by an administrator.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'status' => $status,
            ], 403);
        }

        return redirect()
            ->route('login')
            ->withErrors(['email' => $message]);
    }
}
