<?php

namespace App\Http\Middleware\Logistics;

use App\Support\CurrentLogisticsHub;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsOperatingBranch
{
    private const OPERATIONAL_ROUTE_PREFIXES = [
        'logistics.dashboard',
        'logistics.pickup-requests',
        'logistics.delivery-assignment',
        'logistics.rider-management',
        'logistics.incoming-parcels',
        'logistics.parcel-sorting',
        'logistics.parcel-scanner',
        'logistics.hub-transfers',
        'logistics.last-mile-assignment',
        'logistics.delivery-monitoring',
        'logistics.reports',
        'logistics.remittances',
        'logistics.branch-treasury',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = (string) optional($request->route())->getName();

        if (! $this->isOperationalRoute($routeName)) {
            return $next($request);
        }

        // Hub Operators already have a permanent assigned facility.
        if (CurrentLogisticsHub::isHubOperator($request)) {
            CurrentLogisticsHub::requireOperatingFacility($request);

            return $next($request);
        }

        if (CurrentLogisticsHub::hasOperatingFacility($request)) {
            return $next($request);
        }

        CurrentLogisticsHub::clearOperatingContext($request);

        if ($request->isMethod('GET')) {
            $request->session()->put(
                'logistics_branch_intended_url',
                $request->fullUrl()
            );
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Select an operating Logistics branch first.',
                'redirect' => route('logistics.branch.select'),
            ], 409);
        }

        return redirect()
            ->route('logistics.branch.select')
            ->with('branch_notice', 'Choose the branch you are operating before opening Logistics operations.');
    }

    private function isOperationalRoute(string $routeName): bool
    {
        if ($routeName === '') {
            return false;
        }

        foreach (self::OPERATIONAL_ROUTE_PREFIXES as $prefix) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix.'.')) {
                return true;
            }
        }

        return false;
    }
}
