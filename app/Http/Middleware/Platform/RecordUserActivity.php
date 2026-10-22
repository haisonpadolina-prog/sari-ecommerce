<?php

namespace App\Http\Middleware\Platform;

use App\Services\Platform\UserActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordUserActivity
{
    public function __construct(
        private readonly UserActivityLogService $activityLog
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $before = $this->activityLog->actorFromSession($request);

        /** @var Response $response */
        $response = $next($request);

        $after = $this->activityLog->actorFromSession($request);

        if ($this->actorKey($before) !== $this->actorKey($after)) {
            if ($before) {
                $this->activityLog->recordUserEvent(
                    $request,
                    $before,
                    'logout',
                    $after
                        ? 'Signed out because the active account was switched.'
                        : 'Signed out of SARI.',
                    [
                        'event' => 'logout',
                        'response_status' => $response->getStatusCode(),
                    ]
                );
            }

            if ($after) {
                $this->activityLog->recordUserEvent(
                    $request,
                    $after,
                    'login',
                    $before
                        ? 'Signed in and switched the active account.'
                        : 'Signed in to SARI.',
                    [
                        'event' => 'login',
                        'response_status' => $response->getStatusCode(),
                    ]
                );
            }

            return $response;
        }

        $actor = $after ?: $before;

        if ($actor && $this->shouldRecordAction($request)) {
            $this->activityLog->recordUserEvent(
                $request,
                $actor,
                $this->activityLog->actionForRequest($request),
                $this->activityLog->descriptionForRequest($request),
                array_merge(
                    $this->activityLog->requestMetadata(
                        $request,
                        $response->getStatusCode()
                    ),
                    ['event' => 'account_action']
                )
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
            'marketplace.login',
            'buyer.logout',
            'seller.logout',
            'logistics.logout',
        ], true);
    }

    private function actorKey(?array $actor): string
    {
        if (!$actor) {
            return '';
        }

        return (string) ($actor['role'] ?? '')
            . ':'
            . (int) ($actor['id'] ?? 0);
    }
}
