<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;

use App\Jobs\SendRegistrationDecisionEmail;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LogisticsRiderApplicationController extends Controller
{
    public function index(Request $request)
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $status = (string) $request->query('status', 'pending');

        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $applications = RegistrationApplication::query()
            ->with('logistics')
            ->where('role', 'rider')
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('status', $status)
            ->latest('created_at')
            ->get();

        $summary = RegistrationApplication::query()
            ->where('role', 'rider')
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending")
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected")
            ->first();

        $stats = [
            'total' => (int) ($summary->total ?? 0),
            'pending' => (int) ($summary->pending ?? 0),
            'approved' => (int) ($summary->approved ?? 0),
            'rejected' => (int) ($summary->rejected ?? 0),
        ];

        return view('logistics.rider-applications', compact(
            'applications',
            'status',
            'stats',
            'logistics'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Rider Application
    |--------------------------------------------------------------------------
    */

    public function approve(
        Request $request,
        RegistrationApplication $application
    ) {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $validated = $request->validate([
            'review_note' => [
                'nullable',
                'string',
                'max:1500',
            ],
        ]);

        DB::transaction(function () use (
            $application,
            $validated,
            $logistics
        ): void {

            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnedRiderApplication(
                $locked,
                $logistics
            );

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' =>
                        'Only pending Rider applications can be approved.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Associate Rider With Logistics Provider
            |--------------------------------------------------------------------------
            */

            if (!$locked->logistics_account_id) {
                $locked->logistics_account_id = $logistics->id;
                $locked->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Create or Update Rider Account
            |--------------------------------------------------------------------------
            */

            $rider = CourierAccount::query()->firstOrNew([
                'email' => $locked->email,
            ]);

            $rider->forceFill([
                'registration_application_id' => $locked->id,

                'logistics_account_id' => $logistics->id,

                'last_name' => $locked->last_name,
                'first_name' => $locked->first_name,
                'middle_initial' => $locked->middle_initial,

                'sex' => $locked->sex,
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

                'vehicle_type' => $locked->vehicle_type,
                'plate_number' => $locked->plate_number,
                'license_number' => $locked->license_number,
                'license_expiry_date' => $locked->license_expiry_date?->toDateString(),

                'orcr_path' => $locked->orcr_path,
                'id_path' => $locked->id_path,
                'driver_license_path' => $locked->id_path,

                'account_status' => 'active',

                'approved_at' => now(),
            ])->save();

            /*
            |--------------------------------------------------------------------------
            | Finalize Approval
            |--------------------------------------------------------------------------
            */

            $locked->forceFill([
                'status' => 'approved',

                'admin_note' => $validated['review_note'] ?? null,

                'reviewed_at' => now(),
                'approved_at' => now(),

                'rejected_at' => null,
            ])->save();
        });

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration — APPROVAL
        |--------------------------------------------------------------------------
        |
        | Only mark this action after successful transaction commit.
        |
        | The middleware records:
        |
        | Module: Rider Applications
        | Action: approved
        | Subject: application
        | Subject ID: actual application ID
        |
        | Review notes, credentials and uploaded documents
        | are never copied into Activity Logs.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_subject_id',
            (int) $application->id
        );

        /*
        |--------------------------------------------------------------------------
        | Queue Approval Email
        |--------------------------------------------------------------------------
        */

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'approved',
            'Your SARI Rider application to '
                . $logistics->displayName()
                . ' has been approved. You may now log in using the email and password you submitted.'
        );

        return back()->with(
            'success',
            'Rider application approved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Rider Application
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        RegistrationApplication $application
    ) {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $validated = $request->validate([
            'review_note' => [
                'required',
                'string',
                'min:5',
                'max:1500',
            ],
        ]);

        DB::transaction(function () use (
            $application,
            $validated,
            $logistics
        ): void {

            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnedRiderApplication(
                $locked,
                $logistics
            );

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' =>
                        'Only pending Rider applications can be rejected.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Associate Application With Provider
            |--------------------------------------------------------------------------
            */

            if (!$locked->logistics_account_id) {
                $locked->logistics_account_id = $logistics->id;
            }

            /*
            |--------------------------------------------------------------------------
            | Finalize Rejection
            |--------------------------------------------------------------------------
            */

            $locked->forceFill([
                'status' => 'rejected',

                'admin_note' => $validated['review_note'],

                'reviewed_at' => now(),

                'approved_at' => null,

                'rejected_at' => now(),
            ])->save();
        });

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration — REJECTION
        |--------------------------------------------------------------------------
        |
        | The rejection is recorded only after the transaction
        | has committed successfully.
        |
        | The private review note must not appear in Activity Logs.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        $request->attributes->set(
            'activity_subject_id',
            (int) $application->id
        );

        /*
        |--------------------------------------------------------------------------
        | Queue Rejection Email
        |--------------------------------------------------------------------------
        */

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'not approved',
            'Your SARI Rider application to '
                . $logistics->displayName()
                . ' was not approved. Review note: '
                . $application->admin_note
        );

        return back()->with(
            'success',
            'Rider application rejected and decision recorded.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View Rider Documents
    |--------------------------------------------------------------------------
    */

    public function document(
        Request $request,
        RegistrationApplication $application,
        string $document
    ) {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwnedRiderApplication(
            $application,
            $logistics
        );

        $column = match ($document) {
            'id' => 'id_path',
            'orcr' => 'orcr_path',
            default => abort(404),
        };

        $path = $application->{$column};

        abort_unless(
            $path && Storage::disk('local')->exists($path),
            404,
            'Uploaded Rider document not found.'
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Application Ownership
    |--------------------------------------------------------------------------
    */

    private function assertOwnedRiderApplication(
        RegistrationApplication $application,
        LogisticsAccount $logistics
    ): void {
        abort_unless(
            $application->role === 'rider'
            && (
                !$application->logistics_account_id
                || (int) $application->logistics_account_id
                    === (int) $logistics->id
            ),
            404
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Queue Registration Decision Email
    |--------------------------------------------------------------------------
    */

    private function queueDecisionEmail(
        RegistrationApplication $application,
        string $decision,
        string $message
    ): void {
        try {
            SendRegistrationDecisionEmail::dispatch(
                email: (string) $application->email,

                subject: 'SARI Rider Application '
                    . ucfirst($decision),

                message: $message
            );

        } catch (\Throwable $e) {

            Log::warning(
                'SARI Rider application decision email could not be queued.',
                [
                    'application_id' => $application->id,

                    'email' => $application->email,

                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}
