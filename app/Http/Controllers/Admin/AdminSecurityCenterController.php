<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AdminAccount;
use App\Services\Admin\AdminSecurityCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSecurityCenterController extends Controller
{
    public function __construct(
        private readonly AdminSecurityCenterService $security
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        if (!$admin) {
            return redirect()->route('login');
        }

        $data = $this->security->centerData($request);

        return view('admin.security-center', array_merge($data, [
            'adminAccount' => $admin,
        ]));
    }

    public function alertFeed(Request $request): JsonResponse
    {
        if (!$this->currentAdmin($request)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json($this->security->criticalAlertFeed());
    }


    public function acknowledge(Request $request): JsonResponse|RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        if (!$admin) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        $validated = $request->validate([
            'fingerprint' => ['required', 'string', 'max:180'],
        ]);

        $this->security->acknowledgeAlert($request, (int) $admin->id, $validated['fingerprint']);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Alert acknowledged.');
    }

    public function forceLogout(Request $request, string $sessionHandle): JsonResponse|RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        if (!$admin) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        $identity = $this->security->forceLogout($request, (int) $admin->id, $sessionHandle);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'identity' => $identity]);
        }

        return back()->with('success', 'Active session terminated successfully.');
    }

    private function currentAdmin(Request $request): ?AdminAccount
    {
        if (!$request->session()->get('is_admin')) {
            return null;
        }

        $adminId = (int) $request->session()->get('admin_account_id', 0);
        if ($adminId <= 0) {
            return null;
        }

        return AdminAccount::query()->find($adminId);
    }
}
