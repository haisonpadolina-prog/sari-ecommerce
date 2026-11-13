<?php

namespace App\Http\Middleware\Admin;

use App\Models\Accounts\AdminAccount;
use Closure;
use Illuminate\Http\Request;

class EnsureAdminAiAccess
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->session()->get('is_admin'), 403);
        $admin = AdminAccount::query()->find((int) $request->session()->get('admin_account_id'));
        abort_unless($admin, 403);
        abort_unless(config('admin_ai.enabled'), 503, 'AI assistant is disabled.');
        $request->attributes->set('ai_admin', $admin);
        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
