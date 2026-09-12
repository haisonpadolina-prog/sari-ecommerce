<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationDecisionEmail;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminRegistrationController extends Controller
{
    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }

    public function index(Request $request)
    {
        $this->guard($request);

        $allowedRoles = ['buyer', 'seller', 'courier', 'logistics', 'rider'];
        $allowedStatuses = ['all', 'pending', 'approved', 'rejected'];

        $role = strtolower(trim((string) $request->query('role', '')));
        $role = in_array($role, $allowedRoles, true) ? $role : null;

        $status = strtolower(trim((string) $request->query('status', 'pending')));
        $status = in_array($status, $allowedStatuses, true) ? $status : 'pending';

        /*
         * RegistrationApplication remains the permanent review history.
         * Approving a registration creates/updates the actual role account but
         * does not remove this row, so approved/rejected records can always be
         * reopened from this page.
         */
        $applications = RegistrationApplication::query()
            ->when($role, fn ($query) => $query->where('role', $role))
            ->when(
                $status !== 'all',
                fn ($query) => $query->where('status', $status)
            )
            ->latest('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ONE AGGREGATE QUERY FOR ALL REGISTRATION COUNTS
        |--------------------------------------------------------------------------
        |
        | The old page executed seven separate COUNT queries. This keeps the
        | exact same statistics while reducing the dashboard refresh cost.
        */
        $summary = RegistrationApplication::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending")
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected")
            ->selectRaw("SUM(CASE WHEN role = 'buyer' AND status = 'pending' THEN 1 ELSE 0 END) AS buyers")
            ->selectRaw("SUM(CASE WHEN role = 'seller' AND status = 'pending' THEN 1 ELSE 0 END) AS sellers")
            ->selectRaw("SUM(CASE WHEN role = 'courier' AND status = 'pending' THEN 1 ELSE 0 END) AS couriers")
            ->selectRaw("SUM(CASE WHEN role = 'logistics' AND status = 'pending' THEN 1 ELSE 0 END) AS logistics")
            ->selectRaw("SUM(CASE WHEN role = 'rider' AND status = 'pending' THEN 1 ELSE 0 END) AS riders")
            ->selectRaw("SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) AS mix_buyers")
            ->selectRaw("SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) AS mix_sellers")
            ->selectRaw("SUM(CASE WHEN role = 'courier' THEN 1 ELSE 0 END) AS mix_couriers")
            ->selectRaw("SUM(CASE WHEN role = 'logistics' THEN 1 ELSE 0 END) AS mix_logistics")
            ->selectRaw("SUM(CASE WHEN role = 'rider' THEN 1 ELSE 0 END) AS mix_riders")
            ->first();

        $stats = [
            'total' => (int) ($summary->total ?? 0),
            'pending' => (int) ($summary->pending ?? 0),
            'approved' => (int) ($summary->approved ?? 0),
            'rejected' => (int) ($summary->rejected ?? 0),
            'buyers' => (int) ($summary->buyers ?? 0),
            'sellers' => (int) ($summary->sellers ?? 0),
            'couriers' => (int) ($summary->couriers ?? 0),
            'logistics' => (int) ($summary->logistics ?? 0),
            'riders' => (int) ($summary->riders ?? 0),
        ];

        // Role counters should describe the status currently being viewed,
        // not always the pending queue. This makes Approved/Rejected history
        // easier to understand in the UI.
        $roleSummary = RegistrationApplication::query()
            ->when(
                $status !== 'all',
                fn ($query) => $query->where('status', $status)
            )
            ->selectRaw("SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) AS buyers")
            ->selectRaw("SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) AS sellers")
            ->selectRaw("SUM(CASE WHEN role = 'courier' THEN 1 ELSE 0 END) AS couriers")
            ->selectRaw("SUM(CASE WHEN role = 'logistics' THEN 1 ELSE 0 END) AS logistics")
            ->selectRaw("SUM(CASE WHEN role = 'rider' THEN 1 ELSE 0 END) AS riders")
            ->first();

        $roleStats = [
            'buyers' => (int) ($roleSummary->buyers ?? 0),
            'sellers' => (int) ($roleSummary->sellers ?? 0),
            'couriers' => (int) ($roleSummary->couriers ?? 0),
            'logistics' => (int) ($roleSummary->logistics ?? 0),
            'riders' => (int) ($roleSummary->riders ?? 0),
        ];

        // All-time role distribution for the light-mode Registration Mix card.
        // It intentionally ignores the current table filter so the Admin sees
        // the real composition of every registration record in the system.
        $registrationMix = [
            'buyer' => (int) ($summary->mix_buyers ?? 0),
            'seller' => (int) ($summary->mix_sellers ?? 0),
            'courier' => (int) ($summary->mix_couriers ?? 0),
            'logistics' => (int) ($summary->mix_logistics ?? 0),
            'rider' => (int) ($summary->mix_riders ?? 0),
        ];

        return view('admin.registrations', compact(
            'applications',
            'stats',
            'roleStats',
            'registrationMix',
            'role',
            'status'
        ));
    }

    public function approve(
        Request $request,
        RegistrationApplication $application
    ) {
        $this->guard($request);

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1500'],
        ]);

        DB::transaction(function () use ($application, $validated) {
            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Only pending registrations can be approved.',
                ]);
            }

            if ($locked->role === 'rider') {
                throw ValidationException::withMessages([
                    'application' => 'Rider registrations are reviewed by SARI Logistics.',
                ]);
            }

            $common = [
                'registration_application_id' => $locked->id,
                'last_name' => $locked->last_name,
                'first_name' => $locked->first_name,
                'middle_initial' => $locked->middle_initial,
                'sex' => $locked->sex,
                'email' => $locked->email,
                'contact_no' => $locked->contact_no,
                'birthday' => $locked->birthday?->toDateString(),
                'age' => $locked->age,

                'province_code' => $locked->province_code,
                'province_name' => $locked->province_name,
                'municipality_code' => $locked->municipality_code,
                'municipality_name' => $locked->municipality_name,
                'barangay_code' => $locked->barangay_code,
                'barangay_name' => $locked->barangay_name,
                'street_address' => $locked->street_address,

                'password' => $locked->password,
                'profile_image_path' => $locked->profile_image_path,
                'id_path' => $locked->id_path,
                'approved_at' => now(),
            ];

            if ($locked->role === 'buyer') {
                $buyer = BuyerAccount::query()->firstOrNew([
                    'email' => $locked->email,
                ]);

                $buyer->forceFill(array_merge($common, [
                    'account_status' => 'active',
                ]))->save();
            } elseif ($locked->role === 'seller') {
                $seller = SellerAccount::query()->firstOrNew([
                    'email' => $locked->email,
                ]);

                $seller->forceFill(array_merge($common, [
                    'store_name' => $locked->business_name,
                    'line_of_business' => $locked->line_of_business,
                    'business_permit_path' => $locked->business_permit_path,
                    'registration_status' => 'approved',
                    'account_status' => 'active',
                ]));

                /*
                 * Do not generate the realtime token inside this transaction.
                 * Seller login already guarantees the token before entering
                 * the Seller application, so approval only needs to persist
                 * the account itself.
                 */
                $seller->save();
            } elseif ($locked->role === 'courier') {
                $courier = CourierAccount::query()->firstOrNew([
                    'email' => $locked->email,
                ]);

                $courier->forceFill(array_merge($common, [
                    'vehicle_type' => $locked->vehicle_type,
                    'plate_number' => $locked->plate_number,
                    'orcr_path' => $locked->orcr_path,
                    'account_status' => 'active',
                ]))->save();
            } elseif ($locked->role === 'logistics') {
                $logistics = LogisticsAccount::query()->firstOrNew([
                    'email' => $locked->email,
                ]);

                $logistics->forceFill(array_merge($common, [
                    'business_name' => $locked->business_name,
                    'business_permit_path' => $locked->business_permit_path,
                    'account_status' => 'active',
                ]))->save();
            } else {
                throw ValidationException::withMessages([
                    'application' => 'Unsupported registration role.',
                ]);
            }

            $locked->forceFill([
                'status' => 'approved',
                'admin_note' => $validated['admin_note'] ?? null,
                'reviewed_at' => now(),
                'approved_at' => now(),
                'rejected_at' => null,
            ])->save();
        });

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'approved',
            'Your SARI ' .
            ucfirst($application->role) .
            ' registration has been approved. You may now log in using the email and password you submitted.'
        );

        return $this->success(
            $request,
            ucfirst($application->role) .
            ' registration approved successfully.',
            'approved',
            (int) $application->id
        );
    }

    public function reject(
        Request $request,
        RegistrationApplication $application
    ) {
        $this->guard($request);

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:1500'],
        ]);

        DB::transaction(function () use ($application, $validated) {
            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Only pending registrations can be rejected.',
                ]);
            }

            if ($locked->role === 'rider') {
                throw ValidationException::withMessages([
                    'application' => 'Rider registrations are reviewed by SARI Logistics.',
                ]);
            }

            $locked->forceFill([
                'status' => 'rejected',
                'admin_note' => $validated['admin_note'],
                'reviewed_at' => now(),
                'approved_at' => null,
                'rejected_at' => now(),
            ])->save();
        });

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'not approved',
            'Your SARI ' .
            ucfirst($application->role) .
            ' registration was not approved. Administrator note: ' .
            $application->admin_note
        );

        return $this->success(
            $request,
            'Registration rejected and decision recorded.',
            'rejected',
            (int) $application->id
        );
    }

    public function document(
        Request $request,
        RegistrationApplication $application,
        string $document
    ) {
        $this->guard($request);

        $column = match ($document) {
            'id' => 'id_path',
            'permit' => 'business_permit_path',
            'orcr' => 'orcr_path',
            default => abort(404),
        };

        $path = $application->{$column};

        abort_unless(
            $path && Storage::disk('local')->exists($path),
            404,
            'Uploaded document not found.'
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    }

    private function queueDecisionEmail(
        RegistrationApplication $application,
        string $decision,
        string $message
    ): void {
        try {
            SendRegistrationDecisionEmail::dispatch(
                email: (string) $application->email,
                subject: 'SARI Registration ' . ucfirst($decision),
                message: $message
            );
        } catch (\Throwable $e) {
            /*
             * Email delivery must never undo or delay an Admin decision.
             * The database remains the source of truth.
             */
            Log::warning(
                'SARI registration decision email could not be queued.',
                [
                    'application_id' => $application->id,
                    'email' => $application->email,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    private function success(
        Request $request,
        string $message,
        ?string $status = null,
        ?int $selectedApplicationId = null
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'status' => $status,
                'application_id' => $selectedApplicationId,
            ]);
        }

        if ($status && $selectedApplicationId) {
            $params = [
                'status' => $status,
                'selected' => $selectedApplicationId,
            ];

            $returnRole = strtolower(trim((string) $request->input('return_role', '')));
            if (in_array($returnRole, ['buyer', 'seller', 'courier', 'logistics', 'rider'], true)) {
                $params['role'] = $returnRole;
            }

            return redirect()
                ->route('admin.registrations', $params)
                ->with('success', $message);
        }

        return back()->with('success', $message);
    }
}
