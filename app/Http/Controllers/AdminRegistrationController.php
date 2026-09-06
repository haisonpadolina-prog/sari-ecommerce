<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationDecisionEmail;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
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

        $role = $request->query('role');
        $status = $request->query('status', 'pending');

        $applications = RegistrationApplication::query()
            ->when(
                in_array($role, ['buyer', 'seller', 'courier'], true),
                fn ($query) => $query->where('role', $role)
            )
            ->when(
                in_array($status, ['pending', 'approved', 'rejected'], true),
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
            ->first();

        $stats = [
            'total' => (int) ($summary->total ?? 0),
            'pending' => (int) ($summary->pending ?? 0),
            'approved' => (int) ($summary->approved ?? 0),
            'rejected' => (int) ($summary->rejected ?? 0),
            'buyers' => (int) ($summary->buyers ?? 0),
            'sellers' => (int) ($summary->sellers ?? 0),
            'couriers' => (int) ($summary->couriers ?? 0),
        ];

        return view('admin.registrations', compact(
            'applications',
            'stats',
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
                'id_path' => $locked->id_path,
                'approved_at' => now(),
            ];

            if ($locked->role === 'buyer') {
                BuyerAccount::query()->updateOrCreate(
                    ['email' => $locked->email],
                    array_merge($common, [
                        'account_status' => 'active',
                    ])
                );
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
                CourierAccount::query()->updateOrCreate(
                    ['email' => $locked->email],
                    array_merge($common, [
                        'vehicle_type' => $locked->vehicle_type,
                        'plate_number' => $locked->plate_number,
                        'orcr_path' => $locked->orcr_path,
                        'account_status' => 'active',
                    ])
                );
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
            ' registration approved successfully.'
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
            'Registration rejected and decision recorded.'
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
        string $message
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
