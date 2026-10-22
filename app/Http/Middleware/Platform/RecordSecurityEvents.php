<?php

namespace App\Http\Middleware\Platform;

use App\Services\Admin\AdminSecurityCenterService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordSecurityEvents
{
    public function __construct(
        private readonly AdminSecurityCenterService $security
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $isLoginAttempt = $request->isMethod('POST')
            && (string) ($request->route()?->getName() ?? '') === 'login.submit';

        $email = $isLoginAttempt
            ? strtolower(trim((string) $request->input('email', '')))
            : '';

        /** @var Response $response */
        $response = $next($request);

        if ($isLoginAttempt && $email !== '' && (string) $request->input('password', '') !== '') {
            $authenticated = (bool) (
                $request->session()->get('is_admin')
                || $request->session()->get('is_buyer')
                || $request->session()->get('is_seller')
                || $request->session()->get('is_logistics')
                || $request->session()->get('is_courier')
            );

            if (!$authenticated) {
                $this->security->recordFailedLogin($request, $email);
            }
        }

        return $response;
    }
}
