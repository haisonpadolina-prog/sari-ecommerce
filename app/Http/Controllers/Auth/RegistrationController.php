<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Buyer\BuyerRegistrationController;
use App\Http\Controllers\Seller\SellerRegistrationController;
use App\Http\Controllers\Logistics\LogisticsRegistrationController;

use App\Models\Registration\RegistrationApplication;
use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $role = strtolower(trim((string) $request->query('role', '')));

        if (in_array($role, ['buyer', 'seller', 'logistics'], true)) {
            return redirect()->route('register.' . $role);
        }

        return view('pages.register', ['defaultRole' => '']);
    }

    /**
     * Keep existing registration links and open forms compatible.
     * Role-specific controllers own new submissions and validation rules.
     */
    public function store(Request $request, RegistrationApplicationService $registration): JsonResponse|RedirectResponse
    {
        $action = (string) $request->input('_registration_action', '');

        if ($action === 'otp_send') {
            return $registration->sendOtp($request);
        }

        if ($action === 'otp_verify') {
            return $registration->verifyOtp($request);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['buyer', 'seller', 'logistics'])],
        ]);

        $controller = match ($validated['role']) {
            'buyer' => BuyerRegistrationController::class,
            'seller' => SellerRegistrationController::class,
            'logistics' => LogisticsRegistrationController::class,
        };

        return app($controller)->store($request);
    }

    public function pending(Request $request): View
    {
        $application = $this->trackedApplication($request);

        if ($application) {
            $request->session()->put([
                'registration_tracking_id' => (int) $application->id,
                'registration_email' => $application->email,
                'registration_role' => $application->role,
            ]);

            if ($application->role === 'rider' && $application->logistics) {
                $request->session()->put(
                    'registration_logistics_name',
                    $application->logistics->displayName()
                );
            }
        }

        return view('pages.registration-pending', compact('application'));
    }

    public function status(Request $request): JsonResponse
    {
        $application = $this->trackedApplication($request);

        if (!$application) {
            return response()->json([
                'message' => 'No registration is being tracked in this browser session.',
            ], 404);
        }

        $reviewer = $application->role === 'rider'
            ? ($application->logistics?->displayName() ?? 'SARI Logistics / Sorting Center')
            : 'administrator';

        return response()->json([
            'id' => (int) $application->id,
            'status' => (string) $application->status,
            'role' => (string) $application->role,
            'email' => (string) $application->email,
            'reviewer' => $reviewer,
            'admin_note' => $application->status === 'rejected'
                ? (string) ($application->admin_note ?? '')
                : null,
            'reviewed_at' => $application->reviewed_at?->toIso8601String(),
            'approved_at' => $application->approved_at?->toIso8601String(),
            'rejected_at' => $application->rejected_at?->toIso8601String(),
        ]);
    }

    private function trackedApplication(Request $request): ?RegistrationApplication
    {
        $trackingId = (int) $request->session()->get('registration_tracking_id', 0);
        $email = strtolower(trim((string) $request->session()->get('registration_email', '')));

        if ($trackingId > 0) {
            $query = RegistrationApplication::query()->with('logistics')->whereKey($trackingId);

            if ($email !== '') {
                $query->where('email', $email);
            }

            $application = $query->first();
            if ($application) {
                return $application;
            }
        }

        // Backward-compatible fallback for a registration submitted before this
        // tracking update was installed, as long as its email is still in session.
        if ($email !== '') {
            return RegistrationApplication::query()
                ->with('logistics')
                ->where('email', $email)
                ->latest('id')
                ->first();
        }

        return null;
    }

}
