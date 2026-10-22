<?php

namespace App\Http\Middleware\Logistics;

use App\Support\CurrentLogisticsAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('is_logistics')) {
            return redirect()->route('login');
        }

        // Re-resolve on every protected request so a stale session cannot bypass
        // rejection, suspension, deactivation, or any other non-active status.
        CurrentLogisticsAccount::resolve($request);

        return $next($request);
    }
}
