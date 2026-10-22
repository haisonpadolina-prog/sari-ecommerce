<?php

namespace App\Services\Registration;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Accounts\AccountEmailBanService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistrationApplicationService
{
    public function __construct(
        private readonly RegistrationEmailVerificationService $emailVerification,
        private readonly AccountEmailBanService $emailBanService,
        private readonly AccountRegistrationPolicy $policy,
    ) {}

    public function store(Request $request, string $role, array $roleRules): JsonResponse|RedirectResponse
    {
        $request->merge([
            'role' => $role,
            'email' => $this->policy->normalizeEmail($request->input('email')),
            'contact_no' => $this->policy->normalizeMobile($request->input('contact_no')),
        ]);

        $registrationAction = (string) $request->input('_registration_action', '');
        if ($registrationAction === 'otp_send') {
            return $this->sendOtp($request);
        }
        if ($registrationAction === 'otp_verify') {
            return $this->verifyOtp($request);
        }

        $emailInput = $this->policy->normalizeEmail($request->input('email'));
        $existing = $emailInput !== ''
            ? RegistrationApplication::query()->whereRaw('LOWER(email) = ?', [$emailInput])->first()
            : null;
        $isRejectedResubmission = $existing?->status === 'rejected' && $existing->role === $role;

        if ($isRejectedResubmission) {
            $roleRules = $this->relaxExistingDocumentRules($roleRules, $existing);
        }

        $personName = ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s.\'\-]+$/u'];
        $requiresAddress = in_array($role, ['seller', 'logistics'], true);
        $requiresId = in_array($role, ['seller', 'logistics'], true) && !$isRejectedResubmission;

        $rules = [
            'role' => ['required', Rule::in([$role])],
            'last_name' => $personName,
            'first_name' => $personName,
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^\pL$/u'],
            'sex' => ['nullable', Rule::in(['Male', 'Female'])],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => $role === 'buyer'
                ? ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString(), 'after_or_equal:1900-01-01']
                : ['nullable', 'date', 'after_or_equal:1900-01-01'],
            'region_code' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:30'],
            'region_name' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:150'],
            'province_code' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:30'],
            'province_name' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:150'],
            'municipality_code' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:30'],
            'municipality_name' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:150'],
            'barangay_code' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:30'],
            'barangay_name' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:150'],
            'street_address' => [$requiresAddress ? 'required' : 'nullable', 'string', 'max:500'],
            'postal_code' => [$requiresAddress ? 'required' : 'nullable', 'regex:/^\d{4}$/'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password'],
            'terms' => ['accepted'],
            'profile_image_selected' => ['nullable', 'boolean'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'id_document' => [$requiresId ? 'required' : 'nullable', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];

        $validated = $request->validate(array_merge($rules, $roleRules, ['profile_image' => $rules['profile_image']]), [
            'profile_image.required' => 'A profile photo is required. Choose a JPG, JPEG, PNG, or WEBP image up to 5 MB.',
            'last_name.regex' => 'Last name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'contact_no.regex' => 'Enter a Philippine mobile number as 09XXXXXXXXX or +639XXXXXXXXX.',
            'birthday.before_or_equal' => 'You must be 18 years old or older to register.',
            'postal_code.regex' => 'Postal code must contain exactly four digits.',
            'password_confirmation.same' => 'Confirm Password must match Password.',
        ]);

        $email = $this->policy->normalizeEmail($validated['email']);
        $mobile = $this->policy->normalizeMobile($validated['contact_no']);
        $existing = RegistrationApplication::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        $isExistingUnverified = $existing?->status === 'unverified' && $existing->role === $role;
        $isRejectedResubmission = $existing?->status === 'rejected' && $existing->role === $role;
        $exceptId = ($isExistingUnverified || $isRejectedResubmission) ? (int) $existing->id : null;

        if ($this->emailBanService->isBanned($email)) {
            return back()->withErrors(['email' => 'This email address has been banned by SARI and cannot be used to register.'])->withInput();
        }

        if ($existing && $existing->role !== $role) {
            return back()->withErrors(['email' => 'This email address is already used by another SARI account role.'])->withInput();
        }

        if (!$isExistingUnverified && !$isRejectedResubmission && $this->policy->emailInUse($email)) {
            return back()->withErrors(['email' => 'This email address is already registered or awaiting review.'])->withInput();
        }

        if (!$this->sameExistingMobile($existing, $mobile) && $this->policy->mobileInUse($mobile, $exceptId)) {
            return back()->withErrors(['contact_no' => 'This mobile number is already registered or awaiting review.'])->withInput();
        }
        if (!empty($validated['tin']) && !$this->sameExistingTin($existing, (string) $validated['tin']) && $this->policy->tinInUse((string) $validated['tin'], $exceptId)) {
            return back()->withErrors(['tin' => 'This TIN is already used by a Seller or Logistics account.'])->withInput();
        }

        $this->validateAddressHierarchy($validated, $requiresAddress);

        if ($role === 'seller' && !empty($validated['business_name'])) {
            $businessName = mb_strtolower(trim((string) $validated['business_name']));
            $used = SellerAccount::query()->whereRaw('LOWER(store_name) = ?', [$businessName])->exists()
                || RegistrationApplication::query()
                    ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
                    ->where('role', 'seller')
                    ->whereRaw('LOWER(business_name) = ?', [$businessName])
                    ->whereIn('status', ['unverified', 'pending', 'approved'])
                    ->exists();
            if ($used) {
                return back()->withErrors(['business_name' => 'This shop name is already registered.'])->withInput();
            }
        }

        if ($role === 'logistics' && !empty($validated['business_name'])) {
            $businessName = mb_strtolower(trim((string) $validated['business_name']));
            $used = LogisticsAccount::query()->whereRaw('LOWER(business_name) = ?', [$businessName])->exists()
                || RegistrationApplication::query()
                    ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
                    ->where('role', 'logistics')
                    ->whereRaw('LOWER(business_name) = ?', [$businessName])
                    ->whereIn('status', ['unverified', 'pending', 'approved'])
                    ->exists();
            if ($used) {
                return back()->withErrors(['business_name' => 'This company name is already registered.'])->withInput();
            }
        }

        if (!empty($validated['sec_registration_no'])) {
            $sec = strtoupper(trim((string) $validated['sec_registration_no']));
            $sameExistingSec = $existing && strtoupper((string) $existing->sec_registration_no) === $sec;
            $secUsed = !$sameExistingSec && (
                RegistrationApplication::query()
                    ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
                    ->where('sec_registration_no', $sec)
                    ->whereIn('status', ['unverified', 'pending', 'approved'])
                    ->exists()
                || SellerAccount::query()->where('sec_registration_no', $sec)->exists()
                || LogisticsAccount::query()->where('sec_registration_no', $sec)->exists()
            );
            if ($secUsed) {
                return back()->withErrors(['sec_registration_no' => 'This SEC registration number is already registered.'])->withInput();
            }
            $validated['sec_registration_no'] = $sec;
        }

        // Rapid repeated submissions are idempotent. Once the same unverified
        // application exists, keep the original persisted record/files and OTP.
        if ($isExistingUnverified) {
            if ((string) $existing->contact_no !== $mobile) {
                return back()->withErrors(['email' => 'An unverified registration already exists for this email address.'])->withInput();
            }

            // A retry may supply a photo missing from the original submission.
            // Require the original password before changing an existing application.
            if ($request->hasFile('profile_image') && !$existing->profile_image_path) {
                if (!Hash::check($validated['password'], (string) $existing->password)) {
                    throw ValidationException::withMessages(['password' => 'Enter the original registration password to attach this photo.']);
                }

                DB::transaction(function () use ($existing, $request): void {
                    $locked = RegistrationApplication::query()->whereKey($existing->id)->lockForUpdate()->firstOrFail();
                    if ($locked->status !== 'unverified' || $locked->profile_image_path) {
                        return;
                    }
                    $path = $request->file('profile_image')->store('profile-images/' . $locked->role . '/' . now()->format('Y/m'), 'public');
                    if (!$path || !Storage::disk('public')->exists($path)) {
                        throw ValidationException::withMessages(['profile_image' => 'The profile photo could not be saved. Please try again.']);
                    }
                    try {
                        $locked->forceFill(['profile_image_path' => $path])->save();
                        if ($locked->role === 'buyer') {
                            BuyerAccount::query()->where('registration_application_id', $locked->id)->update(['profile_image_path' => $path]);
                        }
                    } catch (\Throwable $exception) {
                        Storage::disk('public')->delete($path);
                        throw $exception;
                    }
                });
                $existing->refresh();
            }

            $this->track($request, $existing, true);
            if (!$this->emailVerification->hasActiveChallenge($request, $email, $role)) {
                $send = $this->emailVerification->sendCode($request, $email, $role);
                if (!($send['ok'] ?? false)) {
                    return back()->withErrors(['email' => (string) ($send['message'] ?? 'Unable to send the verification code.')])->withInput();
                }
            }

            return redirect()->route('register.' . $role)->with('success', 'Registration saved. Enter the 6-digit code sent to your email.');
        }

        $folder = 'registration-documents/' . $role . '/' . now()->format('Y/m');
        $newFiles = [];
        $stored = [
            'id_path' => $existing?->id_path,
            'business_permit' => $existing?->business_permit_path,
            'bir_document' => $existing?->bir_path,
            'sec_document' => $existing?->sec_path,
            'profile_image_path' => $existing?->profile_image_path,
        ];

        try {
            foreach ([
                'id_document' => ['key' => 'id_path', 'subfolder' => 'id', 'disk' => 'local'],
                'business_permit' => ['key' => 'business_permit', 'subfolder' => 'business-permit', 'disk' => 'local'],
                'bir_document' => ['key' => 'bir_document', 'subfolder' => 'bir', 'disk' => 'local'],
                'sec_document' => ['key' => 'sec_document', 'subfolder' => 'sec', 'disk' => 'local'],
            ] as $field => $config) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($folder . '/' . $config['subfolder'], $config['disk']);
                    $stored[$config['key']] = $path;
                    $newFiles[] = [$config['disk'], $path];
                }
            }

            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile-images/' . $role . '/' . now()->format('Y/m'), 'public');
                if (!$path || !Storage::disk('public')->exists($path)) {
                    throw ValidationException::withMessages(['profile_image' => 'The profile photo could not be saved. Please try uploading it again.']);
                }
                $stored['profile_image_path'] = $path;
                $newFiles[] = ['public', $path];
            }

            $birthday = !empty($validated['birthday']) ? Carbon::parse($validated['birthday']) : null;
            $serviceAreas = $this->normalizeServiceAreas($validated['service_areas'] ?? null);

            $application = DB::transaction(function () use ($existing, $isRejectedResubmission, $validated, $role, $email, $mobile, $birthday, $serviceAreas, $stored): RegistrationApplication {
                if ($existing) {
                    $existing = RegistrationApplication::query()->whereKey($existing->id)->lockForUpdate()->firstOrFail();
                    if ($existing->status !== 'rejected') {
                        throw ValidationException::withMessages(['email' => 'This registration can no longer be resubmitted.']);
                    }
                }

                $payload = [
                    'role' => $role,
                    'logistics_account_id' => null,
                    'last_name' => $this->cleanSpacing($validated['last_name']),
                    'first_name' => $this->cleanSpacing($validated['first_name']),
                    'middle_initial' => !empty($validated['middle_initial']) ? mb_strtoupper(trim((string) $validated['middle_initial'])) : null,
                    'sex' => $validated['sex'] ?? null,
                    'email' => $email,
                    'contact_no' => $mobile,
                    'birthday' => $birthday?->toDateString(),
                    'age' => $birthday?->age,
                    'region_code' => $validated['region_code'] ?? null,
                    'region_name' => isset($validated['region_name']) ? trim((string) $validated['region_name']) : null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => isset($validated['province_name']) ? trim((string) $validated['province_name']) : null,
                    'municipality_code' => $validated['municipality_code'] ?? null,
                    'municipality_name' => isset($validated['municipality_name']) ? trim((string) $validated['municipality_name']) : null,
                    'barangay_code' => $validated['barangay_code'] ?? null,
                    'barangay_name' => isset($validated['barangay_name']) ? trim((string) $validated['barangay_name']) : null,
                    'street_address' => isset($validated['street_address']) ? trim((string) $validated['street_address']) : null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'business_name' => isset($validated['business_name']) ? $this->cleanSpacing((string) $validated['business_name']) : null,
                    'seller_type' => $validated['seller_type'] ?? null,
                    'tin' => $validated['tin'] ?? null,
                    'sec_registration_no' => $validated['sec_registration_no'] ?? null,
                    'line_of_business' => isset($validated['line_of_business']) ? trim((string) $validated['line_of_business']) : null,
                    'position' => isset($validated['position']) ? trim((string) $validated['position']) : null,
                    'service_areas' => $serviceAreas,
                    'vehicle_type' => null,
                    'plate_number' => null,
                    'profile_image_path' => $stored['profile_image_path'],
                    'id_path' => $stored['id_path'],
                    'business_permit_path' => $stored['business_permit'],
                    'bir_path' => $stored['bir_document'],
                    'sec_path' => $stored['sec_document'],
                    'orcr_path' => null,
                    'status' => $isRejectedResubmission ? 'pending' : 'unverified',
                    'admin_note' => null,
                    'reviewed_at' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                ];

                $application = $existing ?: new RegistrationApplication();
                $oldPaths = $existing ? [
                    $existing->id_path,
                    $existing->business_permit_path,
                    $existing->bir_path,
                    $existing->sec_path,
                    $existing->profile_image_path,
                ] : [];

                $application->forceFill($payload)->save();

                if ($role === 'buyer') {
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
                        'id_path' => $payload['id_path'],
                        'profile_image_path' => $payload['profile_image_path'],
                        'account_status' => 'unverified',
                        'approved_at' => null,
                    ]);
                }

                if ($existing) {
                    $newPaths = [
                        $payload['id_path'],
                        $payload['business_permit_path'],
                        $payload['bir_path'],
                        $payload['sec_path'],
                        $payload['profile_image_path'],
                    ];
                    foreach ($oldPaths as $oldPath) {
                        if ($oldPath && !in_array($oldPath, $newPaths, true)) {
                            Storage::disk(str_contains((string) $oldPath, 'profile-images/') ? 'public' : 'local')->delete($oldPath);
                        }
                    }
                }

                return $application;
            });
        } catch (QueryException $e) {
            foreach ($newFiles as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
            throw ValidationException::withMessages(['email' => 'This registration conflicts with an existing account. Please review the unique fields and try again.']);
        }

        $this->track($request, $application, !$isRejectedResubmission);

        if ($isRejectedResubmission) {
            $this->emailVerification->clear($request);
            return redirect()->route('registration.pending')->with('success', 'Your corrected registration has been resubmitted for administrator review.');
        }

        $send = $this->emailVerification->sendCode($request, $email, $role);
        if (!($send['ok'] ?? false)) {
            return redirect()->route('register.' . $role)
                ->withErrors(['email' => (string) ($send['message'] ?? 'The registration was saved, but the verification email could not be sent. Use Resend to try again.')]);
        }

        return redirect()->route('register.' . $role)
            ->with('success', 'Registration saved. Enter the 6-digit verification code sent to your email.');
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $request->merge(['email' => $this->policy->normalizeEmail($request->input('email'))]);

        // Backward compatibility for the original generic /register OTP probe.
        // Older account-management flows use this endpoint with only an email
        // address to verify that a banned/deleted address is (un)available.
        // Role-specific registration still follows the strict workbook flow:
        // persist Unverified application first, then send/verify its OTP.
        if (!$request->filled('role')) {
            $validated = $request->validate([
                'email' => ['required', 'email:rfc', 'max:180'],
            ]);

            $email = $validated['email'];

            if ($this->emailBanService->isBanned($email)) {
                return response()->json([
                    'message' => 'This email address is unavailable.',
                    'errors' => ['email' => ['This email address is unavailable.']],
                ], 422);
            }

            if ($this->policy->emailInUse($email)) {
                return response()->json([
                    'message' => 'This email address is already registered or awaiting review.',
                    'errors' => ['email' => ['This email address is already registered or awaiting review.']],
                ], 422);
            }

            $result = $this->emailVerification->sendCode($request, $email, 'registration');
            $status = (int) ($result['status'] ?? 200);
            unset($result['ok'], $result['status']);

            return response()->json($result, $status);
        }

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'role' => ['required', Rule::in(['buyer', 'seller', 'logistics'])],
        ]);
        $email = $validated['email'];
        $purpose = (string) $validated['role'];

        if ($this->emailBanService->isBanned($email)) {
            return response()->json(['message' => 'This email address is unavailable.', 'errors' => ['email' => ['This email address is unavailable.']]], 422);
        }

        $application = RegistrationApplication::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$application || $application->role !== $purpose || $application->status !== 'unverified') {
            return response()->json([
                'message' => 'Complete registration before requesting a verification code.',
                'errors' => ['email' => ['Complete registration before requesting a verification code.']],
            ], 422);
        }

        $result = $this->emailVerification->sendCode($request, $email, $purpose);
        $status = (int) ($result['status'] ?? 200);
        unset($result['ok'], $result['status']);
        return response()->json($result, $status);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->merge(['email' => $this->policy->normalizeEmail($request->input('email'))]);
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
            'role' => ['required', Rule::in(['buyer', 'seller', 'logistics'])],
        ]);
        $purpose = (string) $validated['role'];
        $email = $validated['email'];

        $result = $this->emailVerification->verifyCode($request, $email, (string) $validated['otp'], $purpose);
        $status = (int) ($result['status'] ?? 200);
        if (!($result['ok'] ?? false)) {
            unset($result['ok'], $result['status']);
            return response()->json($result, $status);
        }

        $application = DB::transaction(function () use ($email, $purpose): RegistrationApplication {
            $application = RegistrationApplication::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->where('role', $purpose)
                ->lockForUpdate()
                ->first();

            if (!$application || $application->status !== 'unverified') {
                throw ValidationException::withMessages(['otp' => 'This registration is no longer awaiting email verification.']);
            }

            if ($purpose === 'buyer') {
                $buyer = BuyerAccount::query()
                    ->where('registration_application_id', $application->id)
                    ->orWhere('email', $email)
                    ->lockForUpdate()
                    ->first();

                if (!$buyer) {
                    throw ValidationException::withMessages(['otp' => 'The Buyer account could not be found for this registration.']);
                }

                $buyer->forceFill([
                    'account_status' => 'active',
                    'approved_at' => now(),
                ])->save();

                $application->forceFill([
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'approved_at' => now(),
                    'rejected_at' => null,
                ])->save();
            } else {
                $application->forceFill([
                    'status' => 'pending',
                    'admin_note' => null,
                    'reviewed_at' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                ])->save();
            }

            return $application;
        });

        $this->emailVerification->clear($request);
        $this->track($request, $application, false);

        $redirect = $purpose === 'buyer'
            ? route('login')
            : route('registration.pending');

        return response()->json([
            'message' => $purpose === 'buyer'
                ? 'Email verified. Your Buyer account is active.'
                : 'Email verified. Your application is now pending administrator approval.',
            'verified' => true,
            'redirect' => $redirect,
        ]);
    }

    private function relaxExistingDocumentRules(array $roleRules, RegistrationApplication $existing): array
    {
        $pathMap = [
            'business_permit' => 'business_permit_path',
            'bir_document' => 'bir_path',
            'sec_document' => 'sec_path',
        ];

        foreach ($pathMap as $field => $pathColumn) {
            if (empty($existing->{$pathColumn}) || !isset($roleRules[$field])) {
                continue;
            }
            $roleRules[$field] = array_values(array_filter(
                $roleRules[$field],
                fn ($rule) => !is_string($rule) || (!str_starts_with($rule, 'required') && $rule !== 'required')
            ));
            array_unshift($roleRules[$field], 'nullable');
        }

        return $roleRules;
    }

    private function validateAddressHierarchy(array $validated, bool $required): void
    {
        if (!$required) {
            return;
        }

        $region = (string) ($validated['region_code'] ?? '');
        $province = (string) ($validated['province_code'] ?? '');
        $municipality = (string) ($validated['municipality_code'] ?? '');
        $barangay = (string) ($validated['barangay_code'] ?? '');

        foreach (['region_code' => $region, 'province_code' => $province, 'municipality_code' => $municipality, 'barangay_code' => $barangay] as $field => $value) {
            if ($value === 'MANUAL') {
                continue;
            }
            if (!preg_match('/^\d{9}$/', $value)) {
                throw ValidationException::withMessages([$field => 'Select a valid Philippine address from the linked address fields.']);
            }
        }

        if (in_array('MANUAL', [$region, $province, $municipality, $barangay], true)) {
            return;
        }

        $provinceMatchesRegion = $region === '130000000'
            ? $province === '130000000'
            : substr($province, 0, 2) === substr($region, 0, 2);
        $municipalityMatchesProvince = $province === '130000000'
            ? substr($municipality, 0, 2) === '13'
            : substr($municipality, 0, 4) === substr($province, 0, 4);
        $barangayMatchesMunicipality = substr($barangay, 0, 6) === substr($municipality, 0, 6);

        if (!$provinceMatchesRegion) {
            throw ValidationException::withMessages(['province_code' => 'The selected province does not belong to the selected region.']);
        }
        if (!$municipalityMatchesProvince) {
            throw ValidationException::withMessages(['municipality_code' => 'The selected city or municipality does not belong to the selected province.']);
        }
        if (!$barangayMatchesMunicipality) {
            throw ValidationException::withMessages(['barangay_code' => 'The selected barangay does not belong to the selected city or municipality.']);
        }
    }

    private function normalizeServiceAreas(mixed $value): ?string
    {
        if (is_array($value)) {
            $items = array_values(array_unique(array_filter(array_map(
                fn ($item) => trim((string) $item),
                $value
            ), fn ($item) => $item !== '')));
            return $items === [] ? null : implode('; ', $items);
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private function sameExistingMobile(?RegistrationApplication $existing, string $mobile): bool
    {
        return $existing && (string) $existing->contact_no === $mobile;
    }

    private function sameExistingTin(?RegistrationApplication $existing, string $tin): bool
    {
        return $existing && (string) $existing->tin === $tin;
    }

    private function track(Request $request, RegistrationApplication $application, bool $otpPending): void
    {
        $request->session()->put([
            'registration_tracking_id' => (int) $application->id,
            'registration_email' => $application->email,
            'registration_role' => $application->role,
            'registration_otp_pending' => $otpPending,
        ]);
        $request->session()->forget('registration_logistics_name');
    }

    private function cleanSpacing(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }
}
