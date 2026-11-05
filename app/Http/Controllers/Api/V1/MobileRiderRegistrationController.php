<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Registration\AccountRegistrationPolicy;
use App\Services\Registration\MobileRegistrationOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobileRiderRegistrationController extends Controller
{
    private const VERIFIED_TTL_MINUTES = 10;

    public function __construct(
        private readonly MobileRegistrationOtpService $otp,
        private readonly AccountEmailBanService $emailBans,
        private readonly AccountRegistrationPolicy $policy,
    ) {}

    public function providers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);
        $search = trim((string) ($validated['q'] ?? ''));

        $providers = LogisticsAccount::query()
            ->where('account_status', 'active')
            ->withCount([
                'riderApplications as approved_riders_count' => fn ($query) => $query
                    ->where('role', 'rider')
                    ->where('status', 'approved'),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function ($query) use ($like): void {
                    $query->where('business_name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('barangay_name', 'like', $like);
                });
            })
            ->orderBy('province_name')
            ->orderBy('municipality_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $providers->map(fn (LogisticsAccount $provider) => [
                'id' => (int) $provider->id,
                'name' => $provider->displayName(),
                'location' => collect([
                    $provider->municipality_name,
                    $provider->province_name,
                ])->filter()->implode(', '),
                'coverage' => collect([
                    $provider->barangay_name,
                    $provider->municipality_name,
                    $provider->province_name,
                ])->filter()->implode(', '),
                'approvedRiders' => (int) ($provider->approved_riders_count ?? 0),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function sendOtp(Request $request, LogisticsAccount $logistics): JsonResponse
    {
        $this->activeProvider($logistics);
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
        ]);
        $email = $this->policy->normalizeEmail($validated['email']);

        $this->guardEmailForOtp($email);

        $result = $this->otp->sendCode($email, $this->purpose($logistics));
        $status = (int) ($result['status'] ?? 200);
        unset($result['ok'], $result['status']);

        return response()->json([
            'success' => $status >= 200 && $status < 300,
            ...$result,
        ], $status);
    }

    public function verifyOtp(Request $request, LogisticsAccount $logistics): JsonResponse
    {
        $this->activeProvider($logistics);
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);
        $email = $this->policy->normalizeEmail($validated['email']);

        $result = $this->otp->verifyCode(
            $email,
            (string) $validated['otp'],
            $this->purpose($logistics)
        );
        $status = (int) ($result['status'] ?? 200);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => (string) ($result['message'] ?? 'OTP verification failed.'),
            ], $status);
        }

        Cache::put(
            $this->verifiedKey($email, $logistics),
            true,
            now()->addMinutes(self::VERIFIED_TTL_MINUTES)
        );

        return response()->json([
            'success' => true,
            'message' => 'Email verified. Submit your Rider application.',
            'data' => [
                'email' => $email,
                'logistics_id' => (int) $logistics->id,
                'verified_for_seconds' => self::VERIFIED_TTL_MINUTES * 60,
            ],
        ]);
    }

    public function apply(Request $request, LogisticsAccount $logistics): JsonResponse
    {
        $this->activeProvider($logistics);

        $request->merge([
            'email' => $this->policy->normalizeEmail($request->input('email')),
            'contact_no' => $this->policy->normalizeMobile(
                $request->input('contact_no', $request->input('contact_number'))
            ),
            'license_number' => strtoupper(trim((string) $request->input('license_number'))),
            'plate_number' => strtoupper(trim((string) $request->input('plate_number'))),
        ]);

        $personName = ['required', 'string', 'min:2', 'max:50', "regex:/^[\pL\s.'\-]+$/u"];
        $validated = $request->validate([
            'last_name' => $personName,
            'first_name' => $personName,
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^\pL$/u'],
            'sex' => ['required', Rule::in(['Male', 'Female'])],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => [
                'required', 'date',
                'before_or_equal:'.now()->subYears(18)->toDateString(),
                'after_or_equal:1900-01-01',
            ],
            'province_code' => ['required', 'string', 'max:30'],
            'province_name' => ['required', 'string', 'max:150'],
            'municipality_code' => ['required', 'string', 'max:30'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'barangay_code' => ['required', 'string', 'max:30'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'street_address' => ['required', 'string', 'max:500'],
            'license_number' => ['required', 'string', 'size:13', 'regex:/^[A-Z]\d{2}-\d{2}-\d{6}$/'],
            'license_expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'vehicle_type' => ['required', Rule::in(['Motorcycle', 'Car', 'Van'])],
            'plate_number' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9 -]+$/'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'id_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'orcr_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'terms' => ['accepted'],
        ]);

        $email = $this->policy->normalizeEmail($validated['email']);
        $mobile = $this->policy->normalizeMobile($validated['contact_no']);

        if (! Cache::get($this->verifiedKey($email, $logistics), false)) {
            throw ValidationException::withMessages([
                'email' => 'Verify this email address with the 6-digit code before submitting your Rider application.',
            ]);
        }

        if ($this->emailBans->isBanned($email)) {
            throw ValidationException::withMessages(['email' => 'This email address is unavailable.']);
        }

        $existing = RegistrationApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($existing && $existing->status === 'pending') {
            $provider = $existing->logistics?->displayName() ?? 'a Logistics provider';
            throw ValidationException::withMessages([
                'email' => 'This email already has a Rider application waiting for review by '.$provider.'.',
            ]);
        }
        if ($existing && $existing->status === 'approved') {
            throw ValidationException::withMessages([
                'email' => 'This Rider registration was already approved. Please log in instead.',
            ]);
        }

        $exceptId = $existing?->id;
        if ($this->policy->emailInUse($email, $exceptId)) {
            throw ValidationException::withMessages(['email' => 'This email address is already used by a SARI account or active registration.']);
        }
        if ($this->policy->mobileInUse($mobile, $exceptId)) {
            throw ValidationException::withMessages(['contact_no' => 'This mobile number is already used by a SARI account or active registration.']);
        }
        if ($this->riderLicenseInUse($validated['license_number'], $exceptId)) {
            throw ValidationException::withMessages(['license_number' => 'This driver\'s license number is already registered.']);
        }
        if ($this->riderPlateInUse($validated['plate_number'], $exceptId)) {
            throw ValidationException::withMessages(['plate_number' => 'This plate number is already registered.']);
        }

        $folder = 'registration-documents/rider/'.$logistics->id.'/'.now()->format('Y/m');
        $idPath = $request->file('id_document')->store($folder.'/license', 'local');
        $orcrPath = $request->file('orcr_document')->store($folder.'/orcr', 'local');
        $profilePath = $request->file('profile_image')->store('profile-images/rider/'.now()->format('Y/m'), 'public');

        if (! $idPath || ! $orcrPath || ! $profilePath || ! Storage::disk('public')->exists($profilePath)) {
            $this->deleteNewFiles($idPath, $orcrPath, $profilePath);
            throw ValidationException::withMessages([
                'documents' => 'One or more Rider registration files could not be saved. Please upload them again.',
            ]);
        }

        $oldId = $existing?->id_path;
        $oldOrcr = $existing?->orcr_path;
        $oldProfile = $existing?->profile_image_path;
        $birthday = Carbon::parse((string) $validated['birthday'])->startOfDay();

        try {
            $application = DB::transaction(function () use (
                $validated, $email, $mobile, $birthday, $logistics,
                $idPath, $orcrPath, $profilePath, $existing
            ): RegistrationApplication {
                $payload = [
                    'role' => 'rider',
                    'logistics_account_id' => $logistics->id,
                    'last_name' => $this->cleanSpacing((string) $validated['last_name']),
                    'first_name' => $this->cleanSpacing((string) $validated['first_name']),
                    'middle_initial' => filled($validated['middle_initial'] ?? null)
                        ? mb_strtoupper(trim((string) $validated['middle_initial']))
                        : null,
                    'sex' => $validated['sex'],
                    'email' => $email,
                    'contact_no' => $mobile,
                    'birthday' => $birthday->toDateString(),
                    'age' => $birthday->age,
                    'province_code' => $validated['province_code'],
                    'province_name' => trim((string) $validated['province_name']),
                    'municipality_code' => $validated['municipality_code'],
                    'municipality_name' => trim((string) $validated['municipality_name']),
                    'barangay_code' => $validated['barangay_code'],
                    'barangay_name' => trim((string) $validated['barangay_name']),
                    'street_address' => trim((string) $validated['street_address']),
                    'password' => Hash::make((string) $validated['password']),
                    'business_name' => null,
                    'line_of_business' => null,
                    'vehicle_type' => $validated['vehicle_type'],
                    'plate_number' => $validated['plate_number'],
                    'license_number' => $validated['license_number'],
                    'license_expiry_date' => $validated['license_expiry_date'],
                    'profile_image_path' => $profilePath,
                    'id_path' => $idPath,
                    'business_permit_path' => null,
                    'orcr_path' => $orcrPath,
                    'status' => 'pending',
                    'admin_note' => null,
                    'reviewed_at' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                ];

                $application = $existing
                    ? RegistrationApplication::query()->whereKey($existing->id)->lockForUpdate()->firstOrFail()
                    : new RegistrationApplication();
                $application->forceFill($payload)->save();

                return $application;
            });
        } catch (\Throwable $exception) {
            $this->deleteNewFiles($idPath, $orcrPath, $profilePath);
            throw $exception;
        }

        if ($existing && $existing->status === 'rejected') {
            if ($oldId && $oldId !== $idPath) Storage::disk('local')->delete($oldId);
            if ($oldOrcr && $oldOrcr !== $orcrPath) Storage::disk('local')->delete($oldOrcr);
            if ($oldProfile && $oldProfile !== $profilePath) Storage::disk('public')->delete($oldProfile);
        }

        Cache::forget($this->verifiedKey($email, $logistics));
        $this->otp->clear($email, $this->purpose($logistics));

        return response()->json([
            'success' => true,
            'message' => 'Rider application submitted for Logistics review.',
            'data' => [
                'registration_id' => (int) $application->id,
                'email' => $email,
                'role' => 'rider',
                'status' => 'pending',
                'logistics_id' => (int) $logistics->id,
                'logistics_name' => $logistics->displayName(),
            ],
        ], 201);
    }

    private function activeProvider(LogisticsAccount $logistics): void
    {
        abort_unless($logistics->account_status === 'active', 404);
    }

    private function guardEmailForOtp(string $email): void
    {
        if ($this->emailBans->isBanned($email)) {
            throw ValidationException::withMessages(['email' => 'This email address is unavailable.']);
        }

        $existing = RegistrationApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($existing && $existing->status === 'pending') {
            $provider = $existing->logistics?->displayName() ?? 'a Logistics provider';
            throw ValidationException::withMessages([
                'email' => 'This email already has a Rider application waiting for review by '.$provider.'.',
            ]);
        }
        if ($existing && $existing->status === 'approved') {
            throw ValidationException::withMessages([
                'email' => 'This Rider registration was already approved. Please log in instead.',
            ]);
        }

        if ($this->policy->emailInUse($email, $existing?->id)) {
            throw ValidationException::withMessages([
                'email' => 'An approved SARI account already uses this email address.',
            ]);
        }
    }

    private function purpose(LogisticsAccount $logistics): string
    {
        return 'rider-'.$logistics->id;
    }

    private function verifiedKey(string $email, LogisticsAccount $logistics): string
    {
        return 'sari:mobile-rider-registration-verified:'.$logistics->id.':'.hash('sha256', $email);
    }

    private function riderLicenseInUse(string $licenseNumber, ?int $exceptApplicationId): bool
    {
        $licenseNumber = strtoupper(trim($licenseNumber));

        return CourierAccount::query()->whereRaw('UPPER(license_number) = ?', [$licenseNumber])->exists()
            || RegistrationApplication::query()
                ->when($exceptApplicationId, fn ($query) => $query->whereKeyNot($exceptApplicationId))
                ->where('role', 'rider')
                ->whereRaw('UPPER(license_number) = ?', [$licenseNumber])
                ->whereIn('status', ['unverified', 'pending', 'approved'])
                ->exists();
    }

    private function riderPlateInUse(string $plateNumber, ?int $exceptApplicationId): bool
    {
        $plateNumber = strtoupper(trim($plateNumber));

        return CourierAccount::query()->whereRaw('UPPER(plate_number) = ?', [$plateNumber])->exists()
            || RegistrationApplication::query()
                ->when($exceptApplicationId, fn ($query) => $query->whereKeyNot($exceptApplicationId))
                ->where('role', 'rider')
                ->whereRaw('UPPER(plate_number) = ?', [$plateNumber])
                ->whereIn('status', ['unverified', 'pending', 'approved'])
                ->exists();
    }

    private function deleteNewFiles(?string $idPath, ?string $orcrPath, ?string $profilePath): void
    {
        if ($idPath) Storage::disk('local')->delete($idPath);
        if ($orcrPath) Storage::disk('local')->delete($orcrPath);
        if ($profilePath) Storage::disk('public')->delete($profilePath);
    }

    private function cleanSpacing(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?: trim($value);
    }
}
