<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationDecisionEmail;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
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

    public function approve(Request $request, RegistrationApplication $application)
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1500'],
        ]);

        DB::transaction(function () use ($application, $validated, $logistics): void {
            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnedRiderApplication($locked, $logistics);

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Only pending Rider applications can be approved.',
                ]);
            }

            if (!$locked->logistics_account_id) {
                $locked->logistics_account_id = $logistics->id;
                $locked->save();
            }

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
                    'orcr_path' => $locked->orcr_path,
                    'id_path' => $locked->id_path,
                    'account_status' => 'active',
                    'approved_at' => now(),
                ])->save();

            $locked->forceFill([
                'status' => 'approved',
                'admin_note' => $validated['review_note'] ?? null,
                'reviewed_at' => now(),
                'approved_at' => now(),
                'rejected_at' => null,
            ])->save();
        });

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'approved',
            'Your SARI Rider application to ' . $logistics->displayName() . ' has been approved. You may now log in using the email and password you submitted.'
        );

        return back()->with('success', 'Rider application approved successfully.');
    }

    public function reject(Request $request, RegistrationApplication $application)
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $validated = $request->validate([
            'review_note' => ['required', 'string', 'min:5', 'max:1500'],
        ]);

        DB::transaction(function () use ($application, $validated, $logistics): void {
            $locked = RegistrationApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnedRiderApplication($locked, $logistics);

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Only pending Rider applications can be rejected.',
                ]);
            }

            if (!$locked->logistics_account_id) {
                $locked->logistics_account_id = $logistics->id;
            }

            $locked->forceFill([
                'status' => 'rejected',
                'admin_note' => $validated['review_note'],
                'reviewed_at' => now(),
                'approved_at' => null,
                'rejected_at' => now(),
            ])->save();
        });

        $application->refresh();

        $this->queueDecisionEmail(
            $application,
            'not approved',
            'Your SARI Rider application to ' . $logistics->displayName() . ' was not approved. Review note: ' . $application->admin_note
        );

        return back()->with('success', 'Rider application rejected and decision recorded.');
    }

    public function document(Request $request, RegistrationApplication $application, string $document)
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $this->assertOwnedRiderApplication($application, $logistics);

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

        return response()->file(Storage::disk('local')->path($path));
    }

    private function assertOwnedRiderApplication(RegistrationApplication $application, LogisticsAccount $logistics): void
    {
        abort_unless(
            $application->role === 'rider'
            && (
                !$application->logistics_account_id
                || (int) $application->logistics_account_id === (int) $logistics->id
            ),
            404
        );
    }

    private function queueDecisionEmail(RegistrationApplication $application, string $decision, string $message): void
    {
        try {
            SendRegistrationDecisionEmail::dispatch(
                email: (string) $application->email,
                subject: 'SARI Rider Application ' . ucfirst($decision),
                message: $message
            );
        } catch (\Throwable $e) {
            Log::warning('SARI Rider application decision email could not be queued.', [
                'application_id' => $application->id,
                'email' => $application->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
