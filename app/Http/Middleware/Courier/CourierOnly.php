<?php

namespace App\Http\Middleware\Courier;

use App\Models\Accounts\CourierAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourierOnly
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! $request->session()->get('is_courier')) {
            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Please sign in to your Rider account.',
                ], 401)
                : redirect()->route('login');
        }

        $id = (int) $request->session()->get(
            'courier_account_id',
            0
        );

        $email = strtolower(trim(
            (string) $request->session()->get(
                'courier_email',
                ''
            )
        ));

        $rider = CourierAccount::query()
            ->with('logistics')
            ->when(
                $id > 0,
                fn ($query) => $query->whereKey($id)
            )
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('account_status', 'active')
            ->first();

        if (
            ! $rider
            || (
                $rider->logistics_account_id
                && $rider->logistics?->account_status !== 'active'
            )
        ) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Your Rider access has changed. Please sign in again.',
                ], 403)
                : redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'An active Rider account and Logistics provider are required.',
                    ]);
        }

        $request->session()->put(
            'courier_account_id',
            $rider->id
        );

        return $next($request);
    }
}