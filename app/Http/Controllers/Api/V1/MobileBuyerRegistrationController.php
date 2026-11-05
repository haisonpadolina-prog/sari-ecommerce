<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Registration\AccountRegistrationPolicy;
use App\Services\Registration\MobileRegistrationOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobileBuyerRegistrationController extends Controller
{
    public function __construct(
        private readonly MobileRegistrationOtpService $otp,
        private readonly AccountEmailBanService $emailBans,
        private readonly AccountRegistrationPolicy $policy,
    ) {}

    public function apply(Request $request): JsonResponse
    {
        $request->merge([
            'email' => $this->policy->normalizeEmail($request->input('email')),
            'contact_no' => $this->policy->normalizeMobile($request->input('contact_no', $request->input('contact_number'))),
        ]);

        $nameRule = ['required', 'string', 'min:2', 'max:50', "regex:/^[\pL\s.'\-]+$/u"];
        $validated = $request->validate([
            'last_name' => $nameRule,
            'first_name' => $nameRule,
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^\pL$/u'],
            'sex' => ['nullable', 'in:Male,Female'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString(), 'after_or_equal:1900-01-01'],
            'region_code' => ['nullable', 'string', 'max:30'],
            'region_name' => ['nullable', 'string', 'max:150'],
            'province_code' => ['nullable', 'string', 'max:30'],
            'province_name' => ['nullable', 'string', 'max:150'],
            'municipality_code' => ['nullable', 'string', 'max:30'],
            'municipality_name' => ['nullable', 'string', 'max:150'],
            'barangay_code' => ['nullable', 'string', 'max:30'],
            'barangay_name' => ['nullable', 'string', 'max:150'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'postal_code' => ['nullable', 'regex:/^\d{4}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'terms' => ['accepted'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $email = (string) $validated['email'];
        $mobile = (string) $validated['contact_no'];

        if ($this->emailBans->isBanned($email)) {
            throw ValidationException::withMessages(['email' => 'This email address is unavailable.']);
        }

        $existing = RegistrationApplication::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if ($existing && ($existing->role !== 'buyer' || $existing->status !== 'unverified')) {
            throw ValidationException::withMessages(['email' => 'This email address is already registered or awaiting another SARI registration.']);
        }
        if (!$existing && $this->policy->emailInUse($email)) {
            throw ValidationException::withMessages(['email' => 'This email address is already registered or awaiting review.']);
        }

        $exceptId = $existing ? (int) $existing->id : null;
        if ((!$existing || (string) $existing->contact_no !== $mobile) && $this->policy->mobileInUse($mobile, $exceptId)) {
            throw ValidationException::withMessages(['contact_no' => 'This mobile number is already registered or awaiting review.']);
        }

        $birthday = Carbon::parse((string) $validated['birthday'])->startOfDay();
        $oldProfile = $existing?->profile_image_path;
        $newProfile = null;
        if ($request->hasFile('profile_image')) {
            $newProfile = $request->file('profile_image')->store('profile-images/buyer/'.now()->format('Y/m'), 'public');
            if (!$newProfile || !Storage::disk('public')->exists($newProfile)) {
                throw ValidationException::withMessages(['profile_image' => 'The profile photo could not be saved.']);
            }
        }
        // A retry with no uploaded photo keeps any image already stored.
        $profilePath = $newProfile ?? $oldProfile;

        try {
            $application = DB::transaction(function () use ($validated, $email, $mobile, $birthday, $existing, $profilePath): RegistrationApplication {
                $payload = [
                    'role' => 'buyer',
                    'logistics_account_id' => null,
                    'last_name' => $this->cleanName((string) $validated['last_name']),
                    'first_name' => $this->cleanName((string) $validated['first_name']),
                    'middle_initial' => filled($validated['middle_initial'] ?? null) ? mb_strtoupper(trim((string) $validated['middle_initial'])) : null,
                    'sex' => $validated['sex'] ?? null,
                    'email' => $email,
                    'contact_no' => $mobile,
                    'birthday' => $birthday->toDateString(),
                    'age' => $birthday->age,
                    'region_code' => $validated['region_code'] ?? null,
                    'region_name' => $validated['region_name'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'municipality_code' => $validated['municipality_code'] ?? null,
                    'municipality_name' => $validated['municipality_name'] ?? null,
                    'barangay_code' => $validated['barangay_code'] ?? null,
                    'barangay_name' => $validated['barangay_name'] ?? null,
                    'street_address' => $validated['street_address'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'password' => Hash::make((string) $validated['password']),
                    'profile_image_path' => $profilePath,
                    'status' => 'unverified',
                    'admin_note' => null,
                    'reviewed_at' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                ];

                $application = $existing
                    ? RegistrationApplication::query()->whereKey($existing->id)->lockForUpdate()->firstOrFail()
                    : new RegistrationApplication();
                $application->forceFill($payload)->save();

                BuyerAccount::query()->updateOrCreate(['email' => $email], [
                    'registration_application_id' => $application->id,
                    'last_name' => $payload['last_name'],
                    'first_name' => $payload['first_name'],
                    'middle_initial' => $payload['middle_initial'],
                    'sex' => $payload['sex'],
                    'contact_no' => $mobile,
                    'birthday' => $payload['birthday'],
                    'age' => $payload['age'],
                    'province_code' => $payload['province_code'],
                    'province_name' => $payload['province_name'],
                    'municipality_code' => $payload['municipality_code'],
                    'municipality_name' => $payload['municipality_name'],
                    'barangay_code' => $payload['barangay_code'],
                    'barangay_name' => $payload['barangay_name'],
                    'street_address' => $payload['street_address'],
                    'password' => $payload['password'],
                    'profile_image_path' => $profilePath,
                    'account_status' => 'unverified',
                    'approved_at' => null,
                ]);

                return $application;
            });
        } catch (\Throwable $exception) {
            if ($newProfile) {
                Storage::disk('public')->delete($newProfile);
            }
            throw $exception;
        }

        if ($newProfile && $oldProfile && $oldProfile !== $newProfile && str_starts_with((string) $oldProfile, 'profile-images/')) {
            Storage::disk('public')->delete($oldProfile);
        }

        $result = $this->otp->sendCode($email, 'buyer');
        $status = (int) ($result['status'] ?? 200);
        if (!($result['ok'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => (string) ($result['message'] ?? 'Registration saved, but OTP could not be sent.'),
                'data' => ['registration_id' => $application->id, 'email' => $email, 'verification_required' => true],
            ], $status);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration saved. Enter the 6-digit code sent to your email.',
            'data' => [
                'registration_id' => $application->id,
                'email' => $email,
                'verification_required' => true,
                'resend_after' => $result['retry_after'] ?? 45,
            ],
        ], 201);
    }

    public function resend(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => ['required', 'email:rfc', 'max:180']]);
        $email = $this->policy->normalizeEmail($validated['email']);
        $application = RegistrationApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('role', 'buyer')
            ->where('status', 'unverified')
            ->first();
        if (!$application) {
            throw ValidationException::withMessages(['email' => 'Complete Buyer registration before requesting another code.']);
        }

        $result = $this->otp->sendCode($email, 'buyer');
        $status = (int) ($result['status'] ?? 200);
        unset($result['ok'], $result['status']);
        return response()->json(['success' => $status >= 200 && $status < 300, ...$result], $status);
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);
        $email = $this->policy->normalizeEmail($validated['email']);
        $result = $this->otp->verifyCode($email, (string) $validated['otp'], 'buyer');
        $status = (int) ($result['status'] ?? 200);
        if (!($result['ok'] ?? false)) {
            return response()->json(['success' => false, 'message' => (string) ($result['message'] ?? 'OTP verification failed.')], $status);
        }

        DB::transaction(function () use ($email): void {
            $application = RegistrationApplication::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->where('role', 'buyer')
                ->lockForUpdate()
                ->first();
            if (!$application || $application->status !== 'unverified') {
                throw ValidationException::withMessages(['otp' => 'This registration is no longer awaiting verification.']);
            }
            $buyer = BuyerAccount::query()
                ->where('registration_application_id', $application->id)
                ->orWhereRaw('LOWER(email) = ?', [$email])
                ->lockForUpdate()
                ->first();
            if (!$buyer) {
                throw ValidationException::withMessages(['otp' => 'The Buyer account could not be found for this registration.']);
            }
            $buyer->forceFill(['account_status' => 'active', 'approved_at' => now()])->save();
            $application->forceFill([
                'status' => 'approved',
                'reviewed_at' => now(),
                'approved_at' => now(),
                'rejected_at' => null,
            ])->save();
        });

        $this->otp->clear($email, 'buyer');
        return response()->json([
            'success' => true,
            'message' => 'Email verified. Your Buyer account is active.',
            'data' => ['email' => $email, 'role' => 'buyer', 'account_status' => 'active'],
        ]);
    }

    private function cleanName(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?: trim($value);
    }
}
