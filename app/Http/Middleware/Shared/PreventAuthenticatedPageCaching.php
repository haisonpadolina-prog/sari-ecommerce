<?php

namespace App\Http\Middleware\Shared;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Do not let browser history or intermediary caches reuse authenticated pages.
 * The role check is defense in depth: a logged-out user must never be served
 * a protected role URL even if a controller forgets an authentication check.
 */
class PreventAuthenticatedPageCaching
{
    private const ROLE_PATHS = [
        'buyer' => 'is_buyer',
        'seller' => 'is_seller',
        'admin' => 'is_admin',
        'courier' => 'is_courier',
        'logistics' => 'is_logistics',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $roleFlag = $this->roleFlagForPath($request);
        $hasRoleSession = $this->hasRoleSession($request);

        // These prefixes are private app areas. Public registration paths
        // start with /register or /rider/activate and are unaffected.
        if ($roleFlag !== null && ! $request->session()->get($roleFlag, false)) {
            $response = ($request->expectsJson() || $request->ajax())
                ? response()->json(['message' => 'Please sign in again.'], 401)
                : redirect()->route('login');

            return $this->disableCaching($response);
        }

        $response = $next($request);

        if ($roleFlag !== null || $hasRoleSession || $this->hasRoleSession($request)) {
            $this->disableCaching($response);
        }

        return $response;
    }

    private function roleFlagForPath(Request $request): ?string
    {
        foreach (self::ROLE_PATHS as $prefix => $flag) {
            if ($request->is($prefix, $prefix.'/*')) {
                return $flag;
            }
        }

        return null;
    }

    private function hasRoleSession(Request $request): bool
    {
        foreach (self::ROLE_PATHS as $flag) {
            if ((bool) $request->session()->get($flag, false)) {
                return true;
            }
        }

        return false;
    }

    private function disableCaching(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'private, no-store, no-cache, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
