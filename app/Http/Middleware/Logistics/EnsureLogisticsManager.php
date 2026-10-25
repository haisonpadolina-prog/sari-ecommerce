<?php

namespace App\Http\Middleware\Logistics;

use App\Support\CurrentLogisticsAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsManager
{
    /**
     * Restrict treasury/finance operations to the main Logistics provider account.
     * Hub operators may work operational pages but must never access company funds.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('is_logistics')) {
            return redirect()->route('login');
        }

        if ((bool) $request->session()->get('is_logistics_hub_operator', false)) {
            abort(403, 'Finance and company payouts are restricted to the Logistics Manager.');
        }

        // Re-resolve the provider account on every protected finance request so
        // stale/deactivated Logistics sessions cannot access treasury data.
        CurrentLogisticsAccount::resolve($request);

        return $next($request);
    }
}
