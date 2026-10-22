<?php

namespace App\Http\Middleware\Platform;

use App\Services\Platform\AdminActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordAdminActivity
{
    public function __construct(
        private readonly AdminActivityLogService $activityLog
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $before = $this->activityLog->actorFromSession($request);

        /** @var Response $response */
        $response = $next($request);

        $after = $this->activityLog->actorFromSession($request);

        if ($before !== $after) {
            if ($before) {
                $this->activityLog->recordLogout($request, $before);
            }

            if ($after) {
                $this->activityLog->recordLogin($request, $after);
            }

            return $response;
        }

        if ($after && $this->shouldRecordAction($request)) {
            $this->activityLog->recordRequest(
                $request,
                $after,
                $response->getStatusCode()
            );
        }

        return $response;
    }

    private function shouldRecordAction(Request $request): bool
    {
        if (!in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        return !in_array((string) ($request->route()?->getName() ?? ''), [
            'login.submit',
            'admin.logout',
        ], true);
    }
}
