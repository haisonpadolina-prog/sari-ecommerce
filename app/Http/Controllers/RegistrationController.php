<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class RegistrationController extends Controller
{
    private const OTP_SESSION_KEY = 'registration_email_otp';
    private const OTP_EXPIRES_MINUTES = 10;
    private const OTP_RESEND_SECONDS = 45;
    private const OTP_MAX_ATTEMPTS = 5;
    private const OTP_VERIFIED_MINUTES = 20;

    public function create()
    {
        return view('pages.register');
    }

    public function store(Request $request)
    {
        $registrationAction = (string) $request->input('_registration_action', '');

        if ($registrationAction === 'otp_send') {
            return $this->sendOtp($request);
        }

        if ($registrationAction === 'otp_verify') {
            return $this->verifyOtp($request);
        }

        $role = (string) $request->input('role');

        $personName = [
            'required',
            'string',
            'max:100',
            'regex:/^[\pL\s.\'-]+$/u',
        ];

        $rules = [
            'role' => ['required', Rule::in(['buyer', 'seller', 'logistics'])],
            'last_name' => $personName,
            'first_name' => $personName,
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^\pL$/u'],
            'sex' => ['required', Rule::in(['Male', 'Female'])],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
                'after_or_equal:1900-01-01',
            ],

            'province_code' => ['required', 'string', 'max:30'],
            'province_name' => ['required', 'string', 'max:150'],
            'municipality_code' => ['required', 'string', 'max:30'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'barangay_code' => ['required', 'string', 'max:30'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'street_address' => ['required', 'string', 'max:500'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],

            'profile_image' => [
                $role === 'logistics' ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'id_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ];

        if (in_array($role, ['seller', 'logistics'], true)) {
            $rules = array_merge($rules, [
                'business_name' => [
                    'required',
                    'string',
                    'max:180',
                    'regex:/^[\pL\pN\s.&\'()\-]+$/u',
                ],
                'business_permit' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:8192',
                ],
            ]);
        }

        if ($role === 'seller') {
            $rules['line_of_business'] = ['required', 'string', 'max:120'];
        }

        $messages = [
            'last_name.regex' =>
                'Last name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'first_name.regex' =>
                'First name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'middle_initial.size' =>
                'Middle initial must contain exactly one letter.',
            'middle_initial.regex' =>
                'Middle initial must contain one letter only.',
            'contact_no.regex' =>
                'Enter an 11-digit Philippine mobile number starting with 09.',
            'birthday.before_or_equal' =>
                'You must be 18 years old or older to register.',
            'birthday.after_or_equal' =>
                'Enter a valid birthday.',
            'business_name.regex' =>
                'Business name may contain letters, numbers, spaces, and common punctuation.',
            'profile_image.required' =>
                'A Logistics / Sorting Center business image is required.',
            'profile_image.image' =>
                'Profile image must be a valid image file.',
            'profile_image.mimes' =>
                'Profile image must be a JPG, JPEG, PNG, or WEBP file.',
            'profile_image.max' =>
                'Profile image must not be larger than 5 MB.',
            'id_document.mimes' =>
                'Valid ID must be a JPG, JPEG, PNG, or PDF file.',
            'business_permit.mimes' =>
                'Business permit must be a JPG, JPEG, PNG, or PDF file.',
        ];

        $validated = $request->validate($rules, $messages);

        $email = strtolower(trim($validated['email']));

        if (!$this->emailOtpIsVerified($request, $email)) {
            return back()
                ->withErrors([
                    'email' => 'Verify this email address with the 6-digit code before submitting your registration.',
                ])
                ->withInput();
        }

        if ($this->approvedAccountUsesEmail($email)) {
            return back()
                ->withErrors([
                    'email' => 'An approved SARI account already uses this email address.',
                ])
                ->withInput();
        }

        $existing = RegistrationApplication::query()
            ->where('email', $email)
            ->first();

        if ($existing && $existing->status === 'pending') {
            return back()
                ->withErrors([
                    'email' => 'This email already has a registration waiting for review.',
                ])
                ->withInput();
        }

        if ($existing && $existing->status === 'approved') {
            return back()
                ->withErrors([
                    'email' => 'This registration was already approved. Please log in instead.',
                ])
                ->withInput();
        }

        if ($existing && $existing->status === 'rejected') {
            foreach ([
                $existing->id_path,
                $existing->business_permit_path,
                $existing->orcr_path,
            ] as $oldFile) {
                if ($oldFile) {
                    Storage::disk('local')->delete($oldFile);
                }
            }

            if ($existing->profile_image_path) {
                Storage::disk('public')->delete($existing->profile_image_path);
            }
        }

        $folder = 'registration-documents/' . $role . '/' . now()->format('Y/m');

        $idPath = $request
            ->file('id_document')
            ->store($folder . '/id', 'local');

        $permitPath = $request->hasFile('business_permit')
            ? $request->file('business_permit')
                ->store($folder . '/business-permit', 'local')
            : null;

        $profileImagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store(
                'profile-images/' . $role . '/' . now()->format('Y/m'),
                'public'
            )
            : null;

        $birthday = Carbon::parse($validated['birthday']);

        $payload = [
            'role' => $role,
            'logistics_account_id' => null,
            'last_name' => $this->cleanSpacing($validated['last_name']),
            'first_name' => $this->cleanSpacing($validated['first_name']),
            'middle_initial' => !empty($validated['middle_initial'])
                ? mb_strtoupper(trim($validated['middle_initial']))
                : null,
            'sex' => $validated['sex'],
            'email' => $email,
            'contact_no' => $validated['contact_no'],
            'birthday' => $birthday->toDateString(),
            'age' => $birthday->age,

            'province_code' => $validated['province_code'],
            'province_name' => trim($validated['province_name']),
            'municipality_code' => $validated['municipality_code'],
            'municipality_name' => trim($validated['municipality_name']),
            'barangay_code' => $validated['barangay_code'],
            'barangay_name' => trim($validated['barangay_name']),
            'street_address' => trim($validated['street_address']),

            'password' => Hash::make($validated['password']),

            'business_name' => in_array($role, ['seller', 'logistics'], true)
                ? $this->cleanSpacing($validated['business_name'])
                : null,
            'line_of_business' => $role === 'seller'
                ? trim($validated['line_of_business'])
                : null,

            'vehicle_type' => null,
            'plate_number' => null,

            'profile_image_path' => $profileImagePath,
            'id_path' => $idPath,
            'business_permit_path' => $permitPath,
            'orcr_path' => null,

            'status' => 'pending',
            'admin_note' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ];

        $application = $existing ?: new RegistrationApplication();
        $application->forceFill($payload);
        $application->save();

        $request->session()->forget(self::OTP_SESSION_KEY);

        return redirect()
            ->route('registration.pending')
            ->with([
                'registration_email' => $application->email,
                'registration_role' => $application->role,
            ]);
    }

    public function pending()
    {
        return view('pages.registration-pending');
    }

    private function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
        ]);

        $email = strtolower(trim($validated['email']));

        if ($this->approvedAccountUsesEmail($email)) {
            return response()->json([
                'message' => 'An approved SARI account already uses this email address.',
                'errors' => [
                    'email' => ['An approved SARI account already uses this email address.'],
                ],
            ], 422);
        }

        $existing = RegistrationApplication::query()
            ->where('email', $email)
            ->first();

        if ($existing && $existing->status === 'pending') {
            return response()->json([
                'message' => 'This email already has a registration waiting for review.',
                'errors' => [
                    'email' => ['This email already has a registration waiting for review.'],
                ],
            ], 422);
        }

        if ($existing && $existing->status === 'approved') {
            return response()->json([
                'message' => 'This registration was already approved. Please log in instead.',
                'errors' => [
                    'email' => ['This registration was already approved. Please log in instead.'],
                ],
            ], 422);
        }

        $mailer = (string) config('mail.default', '');

        if ($mailer === '' || in_array($mailer, ['log', 'array'], true)) {
            return response()->json([
                'message' => 'Email OTP is not configured yet. Configure a real Laravel mailer before enabling registration verification.',
            ], 503);
        }

        $current = $request->session()->get(self::OTP_SESSION_KEY, []);
        $lastSentAt = isset($current['last_sent_at'])
            ? (int) $current['last_sent_at']
            : 0;

        if (
            ($current['email'] ?? null) === $email
            && $lastSentAt > 0
        ) {
            $secondsSinceLastSend = now()->timestamp - $lastSentAt;
            $remaining = self::OTP_RESEND_SECONDS - $secondsSinceLastSend;

            if ($remaining > 0) {
                return response()->json([
                    'message' => 'Please wait before requesting another verification code.',
                    'retry_after' => $remaining,
                ], 429);
            }
        }

        $code = (string) random_int(100000, 999999);
        $hash = $this->hashOtp($code);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRES_MINUTES);

        try {
            Mail::raw(
                "Your SARI email verification code is {$code}.\n\n"
                . 'This code expires in ' . self::OTP_EXPIRES_MINUTES . " minutes.\n"
                . "If you did not request this code, you can ignore this email.",
                function ($message) use ($email): void {
                    $message
                        ->to($email)
                        ->subject('SARI registration verification code');
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'We could not send the verification code right now. Please check the mail configuration and try again.',
            ], 503);
        }

        $request->session()->put(self::OTP_SESSION_KEY, [
            'email' => $email,
            'hash' => $hash,
            'expires_at' => $expiresAt->timestamp,
            'last_sent_at' => now()->timestamp,
            'attempts' => 0,
            'verified_at' => null,
        ]);

        return response()->json([
            'message' => 'Verification code sent.',
            'masked_email' => $this->maskEmail($email),
            'expires_in' => self::OTP_EXPIRES_MINUTES * 60,
            'retry_after' => self::OTP_RESEND_SECONDS,
        ]);
    }

    private function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = (string) $validated['otp'];
        $state = $request->session()->get(self::OTP_SESSION_KEY);

        if (!is_array($state) || ($state['email'] ?? null) !== $email) {
            return response()->json([
                'message' => 'Request a new verification code for this email address.',
                'errors' => [
                    'otp' => ['Request a new verification code for this email address.'],
                ],
            ], 422);
        }

        if (!empty($state['verified_at'])) {
            return response()->json([
                'message' => 'Email already verified.',
                'verified' => true,
            ]);
        }

        $expiresAt = (int) ($state['expires_at'] ?? 0);

        if ($expiresAt <= now()->timestamp) {
            $request->session()->forget(self::OTP_SESSION_KEY);

            return response()->json([
                'message' => 'The verification code has expired. Request a new code.',
                'errors' => [
                    'otp' => ['The verification code has expired. Request a new code.'],
                ],
            ], 422);
        }

        $attempts = (int) ($state['attempts'] ?? 0);

        if ($attempts >= self::OTP_MAX_ATTEMPTS) {
            $request->session()->forget(self::OTP_SESSION_KEY);

            return response()->json([
                'message' => 'Too many incorrect attempts. Request a new verification code.',
                'errors' => [
                    'otp' => ['Too many incorrect attempts. Request a new verification code.'],
                ],
            ], 429);
        }

        $expectedHash = (string) ($state['hash'] ?? '');
        $actualHash = $this->hashOtp($otp);

        if ($expectedHash === '' || !hash_equals($expectedHash, $actualHash)) {
            $state['attempts'] = $attempts + 1;
            $request->session()->put(self::OTP_SESSION_KEY, $state);

            $remainingAttempts = max(
                0,
                self::OTP_MAX_ATTEMPTS - (int) $state['attempts']
            );

            return response()->json([
                'message' => 'The verification code is incorrect.',
                'errors' => [
                    'otp' => [
                        $remainingAttempts > 0
                            ? "Incorrect code. {$remainingAttempts} attempt(s) remaining."
                            : 'Incorrect code. Request a new verification code.',
                    ],
                ],
            ], 422);
        }

        $state['verified_at'] = now()->timestamp;
        $state['attempts'] = 0;
        $request->session()->put(self::OTP_SESSION_KEY, $state);

        return response()->json([
            'message' => 'Email verified successfully.',
            'verified' => true,
        ]);
    }

    private function emailOtpIsVerified(Request $request, string $email): bool
    {
        $state = $request->session()->get(self::OTP_SESSION_KEY);

        if (!is_array($state)) {
            return false;
        }

        if (($state['email'] ?? null) !== $email) {
            return false;
        }

        $verifiedAt = (int) ($state['verified_at'] ?? 0);

        if ($verifiedAt <= 0) {
            return false;
        }

        return $verifiedAt >= now()->subMinutes(self::OTP_VERIFIED_MINUTES)->timestamp;
    }

    private function approvedAccountUsesEmail(string $email): bool
    {
        return BuyerAccount::query()->where('email', $email)->exists()
            || CourierAccount::query()->where('email', $email)->exists()
            || LogisticsAccount::query()->where('email', $email)->exists()
            || AdminAccount::query()->where('email', $email)->exists()
            || SellerAccount::query()->where('email', $email)->exists();
    }

    private function hashOtp(string $otp): string
    {
        return hash_hmac('sha256', $otp, (string) config('app.key'));
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return $email;
        }

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
        $maskedCount = max(3, mb_strlen($local) - mb_strlen($visible));

        return $visible . str_repeat('•', $maskedCount) . '@' . $domain;
    }

    private function cleanSpacing(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }
}
