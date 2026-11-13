<?php

namespace App\Http\Middleware\Admin;

use App\Models\Accounts\AdminAccount;
use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleAdminAiRequests extends ThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        // Laravel may prioritize ThrottleRequests subclasses before custom guards.
        // Resolve and validate the server-side session here as well.
        abort_unless($request->session()->get('is_admin'), 401);
        $admin = AdminAccount::query()->find((int) $request->session()->get('admin_account_id'));
        abort_unless($admin, 403);

        // A versioned, per-admin/per-operation counter cannot collide with
        // the generic IP bucket used by other SARI routes or older packages.
        return 'sari-admin-ai:v2:'.$admin->id.':'.$request->route()->getName();
    }

    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        return parent::buildException($request, $key, $maxAttempts, static function ($request, $headers) {
            $seconds = max(1, (int) ($headers['Retry-After'] ?? 60));

            return response()->json([
                'message' => 'Please wait '.$seconds.' seconds before another request of this type.',
                'code' => 'admin_ai_rate_limited',
                'retry_after' => $seconds,
            ], 429, array_merge($headers, ['Cache-Control' => 'private, no-store']));
        });
    }
}
