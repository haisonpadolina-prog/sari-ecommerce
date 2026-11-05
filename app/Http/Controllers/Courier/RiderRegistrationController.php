<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Registration\RegistrationApplication;
use App\Models\Accounts\SellerAccount;
use App\Services\Registration\RegistrationEmailVerificationService;
use App\Services\Registration\AccountRegistrationPolicy;
use App\Services\Accounts\AccountEmailBanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RiderRegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationEmailVerificationService $emailVerification,
        private readonly AccountEmailBanService $emailBanService,
        private readonly AccountRegistrationPolicy $registrationPolicy
    ) {}

    /**
     * Service areas are stored by registration as semicolon-separated region
     * names (not as a geocoded radius). Recommendations are therefore based
     * only on declared coverage and registered locality, never on distance.
     */
    private const SERVICE_REGIONS = [
        'NCR', 'CAR', 'Region I (Ilocos Region)',
        'Region II (Cagayan Valley)', 'Region III (Central Luzon)',
        'Region IV-A (CALABARZON)', 'MIMAROPA Region',
        'Region V (Bicol Region)', 'Region VI (Western Visayas)',
        'Region VII (Central Visayas)', 'Region VIII (Eastern Visayas)',
        'Region IX (Zamboanga Peninsula)', 'Region X (Northern Mindanao)',
        'Region XI (Davao Region)', 'Region XII (SOCCSKSARGEN)',
        'Region XIII (Caraga)', 'BARMM',
    ];

    public function index(Request $request): View
    {
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $location = mb_substr(trim((string) $request->query('location', '')), 0, 100);
        $requestedRegion = trim((string) $request->query('region', ''));
        $region = in_array($requestedRegion, self::SERVICE_REGIONS, true)
            ? $requestedRegion
            : '';

        // Do not filter out non-matching providers: the Rider must remain
        // free to apply to any active Logistics company.
        $logistics = LogisticsAccount::query()
            ->where('account_status', 'active')
            ->withCount([
                'riderApplications as approved_riders_count' => fn ($query) => $query
                    ->where('role', 'rider')
                    ->where('status', 'approved'),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($query) use ($like): void {
                    $query->where('business_name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('business_name')
            ->orderBy('first_name')
            ->get();

        $hasLocationSearch = $location !== '' || $region !== '';
        foreach ($logistics as $provider) {
            [$score, $reason] = $this->riderLocationMatch($provider, $location, $region);
            $provider->setAttribute('rider_recommendation_score', $score);
            $provider->setAttribute('rider_recommendation_reason', $reason);
        }

        if ($hasLocationSearch) {
            $logistics = $logistics->sortByDesc(
                fn (LogisticsAccount $provider) => (int) $provider->rider_recommendation_score
            )->values();
        }

        $recommendedCount = $hasLocationSearch
            ? $logistics->filter(fn (LogisticsAccount $p) => (int) $p->rider_recommendation_score > 0)->count()
            : 0;

        // These suggestions are drawn from existing Logistics address data,
        // never fabricated cities, provinces or barangays.
        $locationSuggestions = $logistics
            ->flatMap(fn (LogisticsAccount $provider) => [
                $provider->municipality_name,
                $provider->province_name,
                $provider->barangay_name,
            ])
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->unique(fn ($value) => mb_strtolower(trim($value)))
            ->sort()
            ->values();
        $serviceRegions = self::SERVICE_REGIONS;

        return view('pages.rider-logistics', compact(
            'logistics', 'search', 'location', 'region', 'serviceRegions',
            'locationSuggestions', 'recommendedCount', 'hasLocationSearch'
        ));
    }

    /** @return array{0:int,1:string} */
    private function riderLocationMatch(LogisticsAccount $provider, string $location, string $region): array
    {
        $needle = mb_strtolower(trim($location));
        $locationPieces = array_values(array_filter(array_map('trim', explode(',', $needle))));
        $coverage = mb_strtolower((string) $provider->service_areas);
        $declaredAreas = array_values(array_filter(array_map(
            fn ($item) => mb_strtolower(trim($item)),
            preg_split('/[;,]/', $coverage) ?: []
        )));
        $regionMatchesCoverage = $region !== '' && in_array(mb_strtolower($region), $declaredAreas, true);

        if ($needle !== '') {
            foreach ([
                'municipality_name' => [120, 'Registered in this city / municipality'],
                'barangay_name' => [110, 'Registered in this barangay'],
                'province_name' => [95, 'Registered in this province'],
            ] as $field => [$score, $reason]) {
                $value = mb_strtolower(trim((string) $provider->{$field}));
                if ($value !== '' && ($needle !== '' && str_contains($value, $needle)
                    || collect($locationPieces)->contains(fn ($piece) => mb_strlen($piece) >= 3 && str_contains($value, $piece)))) {
                    return [$score, $reason];
                }
            }
        }

        if ($regionMatchesCoverage) {
            return [80, 'Lists your selected region as a service area'];
        }
        if ($region !== '') {
            $providerRegion = mb_strtolower(trim((string) $provider->region_name));
            $providerRegionCode = trim((string) $provider->region_code);
            $regionNormalized = mb_strtolower($region);
            if ($providerRegion !== '' && (
                str_contains($providerRegion, $regionNormalized)
                || str_contains($regionNormalized, $providerRegion)
                || ($region === 'NCR' && ($providerRegionCode === '130000000' || str_contains($providerRegion, 'national capital')))
            )) {
                return [65, 'Registered in your selected region'];
            }
        }
        if ($needle !== '') {
            foreach ($declaredAreas as $area) {
                if ($area !== '' && (str_contains($area, $needle) || mb_strlen($needle) >= 5 && str_contains($needle, $area))) {
                    return [55, 'Location matches a declared service area'];
                }
            }
        }

        return [0, ''];
    }

    // Step 2: Show the application directly after choosing a Logistics provider.
    public function create(Request $request, LogisticsAccount $logistics): View|RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);

        // A submitted (but unverified) application continues at the OTP step.
        if ($this->currentDraft($request, $logistics)) {
            return redirect()->route('rider.logistics.email.show', $logistics);
        }

        $logistics->loadCount([
            'riderApplications as approved_riders_count' => fn ($query) => $query
                ->where('role', 'rider')->where('status', 'approved'),
        ]);

        return view('pages.rider-register', compact('logistics'));
    }

    // Step 3: Only an application saved for this session and this Logistics
    // company can request or verify an email code.
    public function showEmailVerification(Request $request, LogisticsAccount $logistics): View|RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);

        $draft = $this->currentDraft($request, $logistics);
        if (!$draft) {
            return redirect()->route('rider.logistics.apply', $logistics)
                ->withErrors(['email' => 'Complete the Rider application before verifying your email.']);
        }

        $email = $draft->email;
        $request->session()->put('rider_registration_otp_email', $email);

        return view('pages.rider-email-verification', compact('logistics', 'email'));
    }

    public function sendEmailCode(Request $request, LogisticsAccount $logistics): RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);
        $draft = $this->currentDraft($request, $logistics);
        if (!$draft) {
            return redirect()->route('rider.logistics.apply', $logistics)
                ->withErrors(['email' => 'Submit your Rider application first.']);
        }

        $email = $draft->email;
        $providedEmail = $this->registrationPolicy->normalizeEmail($request->input('email'));
        if ($providedEmail !== $email) {
            return back()->withErrors(['email' => 'The verification email must match your submitted application.']);
        }
        if ($this->emailBanService->isBanned($email)) {
            return back()->withErrors(['email' => 'This email address cannot be used to register.']);
        }

        $result = $this->emailVerification->sendCode($request, $email);
        if (!($result['ok'] ?? false)) {
            return back()->withErrors([
                'email' => (string) ($result['message'] ?? 'Unable to send verification code.'),
            ]);
        }

        $request->session()->put('rider_registration_otp_email', $email);
        $response = back()->with('success', (string) ($result['message'] ?? 'Verification code sent.'));
        if (!empty($result['debug_code']) && app()->environment(['local', 'testing'])) {
            $response->with('rider_debug_otp', (string) $result['debug_code']);
        }

        return $response;
    }

    public function verifyEmailCode(Request $request, LogisticsAccount $logistics): RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);
        $draft = $this->currentDraft($request, $logistics);
        if (!$draft) {
            return redirect()->route('rider.logistics.apply', $logistics)
                ->withErrors(['otp' => 'Submit your Rider application before email verification.']);
        }

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);
        $email = $this->registrationPolicy->normalizeEmail($validated['email']);
        if ($email !== $draft->email
            || $email !== $this->registrationPolicy->normalizeEmail($request->session()->get('rider_registration_otp_email'))
        ) {
            return back()->withErrors(['otp' => 'Request a code for your submitted Rider application.']);
        }

        $result = $this->emailVerification->verifyCode($request, $email, (string) $validated['otp']);
        if (!($result['ok'] ?? false) || !$this->emailVerification->isVerified($request, $email)) {
            return back()->withErrors([
                'otp' => (string) ($result['message'] ?? 'The code is invalid or expired.'),
            ]);
        }

        // The Logistics review queue filters to status=pending. Until this
        // transaction commits, the unverified draft never enters that queue.
        DB::transaction(function () use ($draft, $logistics, $email): void {
            $locked = RegistrationApplication::query()->lockForUpdate()->findOrFail($draft->id);
            abort_unless(
                $locked->role === 'rider'
                && $locked->status === 'unverified'
                && (int) $locked->logistics_account_id === (int) $logistics->id
                && $locked->email === $email,
                409
            );

            if ($this->registrationPolicy->emailInUse($email, (int) $locked->id)
                || $this->registrationPolicy->mobileInUse($locked->contact_no, (int) $locked->id)
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'This email or contact number is already used by another SARI account.',
                ]);
            }

            $locked->forceFill(['status' => 'pending'])->save();
        });

        $this->emailVerification->clear($request);
        $request->session()->forget([
            'rider_registration_draft_id',
            'rider_registration_draft_logistics_id',
            'rider_registration_otp_email',
            'rider_registration_otp_logistics_id',
            'rider_registration_verified_email',
            'rider_registration_verified_logistics_id',
        ]);
        $request->session()->put([
            'registration_tracking_id' => (int) $draft->id,
            'registration_email' => $email,
            'registration_role' => 'rider',
            'registration_logistics_name' => $logistics->displayName(),
        ]);

        return redirect()->route('registration.pending')
            ->with('success', 'Email verified! Your Rider application is awaiting Logistics review.');
    }

    public function store(Request $request, LogisticsAccount $logistics): RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);

        $request->merge([
            'email' => $this->registrationPolicy->normalizeEmail($request->input('email')),
            'contact_no' => $this->registrationPolicy->normalizeMobile($request->input('contact_no')),
            'license_number' => strtoupper(trim((string) $request->input('license_number'))),
            'plate_number' => strtoupper(trim((string) $request->input('plate_number'))),
        ]);

        $personName = [
            'required',
            'string',
            'min:2',
            'max:50',
            'regex:/^[\pL\s.\'-]+$/u',
        ];

        $validated = $request->validate([
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

            'license_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^[A-Z]\d{2}-\d{2}-\d{6}$/',
            ],
            'license_expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'vehicle_type' => ['required', Rule::in(['Motorcycle', 'Car', 'Van'])],
            'plate_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9 -]+$/',
            ],

            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'id_document' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,application/pdf',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'orcr_document' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,application/pdf',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'password' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'terms' => ['accepted'],
        ], [
            'last_name.min' => 'Last name must contain at least 2 characters.',
            'last_name.max' => 'Last name must not be greater than 50 characters.',
            'last_name.regex' => 'Last name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'first_name.min' => 'First name must contain at least 2 characters.',
            'first_name.max' => 'First name must not be greater than 50 characters.',
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'middle_initial.size' => 'Middle initial must contain exactly one letter.',
            'middle_initial.regex' => 'Middle initial must contain one letter only.',
            'contact_no.regex' => 'Enter a Philippine mobile number as 09XXXXXXXXX or +639XXXXXXXXX.',
            'birthday.before_or_equal' => 'You must be 18 years old or older to register.',
            'birthday.after_or_equal' => 'Enter a valid birthday.',
            'license_number.size' => 'Driver\'s license number must use the format A00-00-000000.',
            'license_number.regex' => 'Driver\'s license number must use the format A00-00-000000.',
            'license_expiry_date.after_or_equal' => 'Driver\'s license must expire today or later.',
            'profile_image.required' => 'A profile photo is required before submitting your Rider application.',
            'profile_image.image' => 'Profile photo must be a valid image.',
            'profile_image.mimes' => 'Profile photo must be a JPG, JPEG, PNG, or WEBP image.',
            'profile_image.max' => 'Profile photo must not be larger than 5 MB.',
            'id_document.mimes' => 'Driver\'s license document must be a JPG, JPEG, PNG, or PDF.',
            'id_document.max' => 'Driver\'s license document must not be larger than 5 MB.',
            'orcr_document.mimes' => 'OR / CR must be a JPG, JPEG, PNG, or PDF.',
            'orcr_document.max' => 'OR / CR must not be larger than 5 MB.',
            'plate_number.regex' => 'Plate number may contain letters, numbers, spaces, and hyphens only.',
        ]);

        $email = $this->registrationPolicy->normalizeEmail($validated['email']);
        $contactNo = $this->registrationPolicy->normalizeMobile($validated['contact_no']);

        if ($this->emailBanService->isBanned($email)) {
            return back()->withErrors(['email' => 'This email address has been banned by SARI and cannot be used to register.'])->withInput();
        }

        $existing = RegistrationApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($existing && $existing->status === 'pending') {
            $provider = $existing->logistics?->displayName() ?? 'a Logistics provider';

            return back()
                ->withErrors([
                    'email' => 'This email already has a Rider application waiting for review by ' . $provider . '.',
                ])
                ->withInput();
        }

        if ($existing && $existing->status === 'approved') {
            return back()
                ->withErrors([
                    'email' => 'This Rider registration was already approved. Please log in instead.',
                ])
                ->withInput();
        }

        // The current browser can resume its own draft; another browser must
        // not overwrite a recent unverified application for the same email.
        if ($existing && $existing->status === 'unverified') {
            if ($existing->role !== 'rider') {
                return back()->withErrors([
                    'email' => 'Another SARI registration is awaiting email verification for this address.',
                ])->withInput();
            }
            $isOwnDraft = (int) $request->session()->get('rider_registration_draft_id', 0) === (int) $existing->id
                && (int) $request->session()->get('rider_registration_draft_logistics_id', 0) === (int) $logistics->id;
            if ($isOwnDraft) {
                return redirect()->route('rider.logistics.email.show', $logistics);
            }
            if ($existing->updated_at && $existing->updated_at->greaterThan(now()->subMinutes(30))) {
                return back()->withErrors([
                    'email' => 'An unverified application is already in progress for this email. Try again later.',
                ])->withInput();
            }
        }

        $exceptApplicationId = $existing?->id;

        if ($this->registrationPolicy->emailInUse($email, $exceptApplicationId)) {
            return back()->withErrors(['email' => 'This email address is already used by a SARI account or active registration.'])->withInput();
        }

        if ($this->registrationPolicy->mobileInUse($contactNo, $exceptApplicationId)) {
            return back()->withErrors(['contact_no' => 'This mobile number is already used by a SARI account or active registration.'])->withInput();
        }

        if ($this->riderLicenseInUse($validated['license_number'], $exceptApplicationId)) {
            return back()->withErrors(['license_number' => 'This driver\'s license number is already registered.'])->withInput();
        }

        if ($this->riderPlateInUse($validated['plate_number'], $exceptApplicationId)) {
            return back()->withErrors(['plate_number' => 'This plate number is already registered.'])->withInput();
        }

        if ($existing && in_array($existing->status, ['rejected', 'unverified'], true)) {
            foreach ([$existing->id_path, $existing->orcr_path] as $oldFile) {
                if ($oldFile) {
                    Storage::disk('local')->delete($oldFile);
                }
            }

            if ($existing->profile_image_path) {
                Storage::disk('public')->delete($existing->profile_image_path);
            }
        }

        $folder = 'registration-documents/rider/' . $logistics->id . '/' . now()->format('Y/m');

        $idPath = $request
            ->file('id_document')
            ->store($folder . '/license', 'local');

        $orcrPath = $request
            ->file('orcr_document')
            ->store($folder . '/orcr', 'local');

        $profileImagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store(
                'profile-images/rider/' . now()->format('Y/m'),
                'public'
            )
            : null;

        if (!$profileImagePath || !Storage::disk('public')->exists($profileImagePath)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'profile_image' => 'Your profile photo could not be saved. Please upload it again.',
            ]);
        }

        $birthday = Carbon::parse($validated['birthday']);

        $payload = [
            'role' => 'rider',
            'logistics_account_id' => $logistics->id,
            'last_name' => $this->cleanSpacing($validated['last_name']),
            'first_name' => $this->cleanSpacing($validated['first_name']),
            'middle_initial' => !empty($validated['middle_initial'])
                ? mb_strtoupper(trim($validated['middle_initial']))
                : null,
            'sex' => $validated['sex'],
            'email' => $email,
            'contact_no' => $contactNo,
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
            'business_name' => null,
            'line_of_business' => null,
            'vehicle_type' => $validated['vehicle_type'],
            'plate_number' => $validated['plate_number'],
            'license_number' => $validated['license_number'],
            'license_expiry_date' => $validated['license_expiry_date'],
            'profile_image_path' => $profileImagePath,
            'id_path' => $idPath,
            'business_permit_path' => null,
            'orcr_path' => $orcrPath,
            'status' => 'unverified',
            'admin_note' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ];

        $application = $existing ?: new RegistrationApplication();
        $application->forceFill($payload);
        $application->save();

        // Application and uploads are saved before OTP, but NOT yet submitted
        // to the Logistics approval queue until the email is verified.
        $this->emailVerification->clear($request);
        $request->session()->forget([
            'rider_registration_verified_email',
            'rider_registration_verified_logistics_id',
            'registration_tracking_id',
            'registration_email',
            'registration_role',
            'registration_logistics_name',
        ]);
        $request->session()->put([
            'rider_registration_draft_id' => (int) $application->id,
            'rider_registration_draft_logistics_id' => (int) $logistics->id,
            'rider_registration_otp_email' => $email,
        ]);

        $result = $this->emailVerification->sendCode($request, $email);
        $response = redirect()->route('rider.logistics.email.show', $logistics);
        if ($result['ok'] ?? false) {
            $response->with('success', 'Application saved. Verify your email to submit it for Logistics review.');
            if (!empty($result['debug_code']) && app()->environment(['local', 'testing'])) {
                $response->with('rider_debug_otp', (string) $result['debug_code']);
            }
        } else {
            $response->withErrors([
                'email' => (string) ($result['message'] ?? 'We could not send the code. Select Send Code to retry.'),
            ]);
        }

        return $response;
    }

    private function currentDraft(Request $request, LogisticsAccount $logistics): ?RegistrationApplication
    {
        $draftId = (int) $request->session()->get('rider_registration_draft_id', 0);
        $draftLogisticsId = (int) $request->session()->get('rider_registration_draft_logistics_id', 0);
        if ($draftId <= 0 || $draftLogisticsId !== (int) $logistics->id) {
            return null;
        }

        return RegistrationApplication::query()
            ->whereKey($draftId)
            ->where('role', 'rider')
            ->where('logistics_account_id', $logistics->id)
            ->where('status', 'unverified')
            ->first();
    }

    private function riderLicenseInUse(string $licenseNumber, ?int $exceptApplicationId = null): bool
    {
        $licenseNumber = strtoupper(trim($licenseNumber));

        return CourierAccount::query()
            ->whereRaw('UPPER(license_number) = ?', [$licenseNumber])
            ->exists()
            || RegistrationApplication::query()
                ->when($exceptApplicationId, fn ($query) => $query->whereKeyNot($exceptApplicationId))
                ->where('role', 'rider')
                ->whereRaw('UPPER(license_number) = ?', [$licenseNumber])
                ->whereIn('status', ['unverified', 'pending', 'approved'])
                ->exists();
    }

    private function riderPlateInUse(string $plateNumber, ?int $exceptApplicationId = null): bool
    {
        $plateNumber = strtoupper(trim($plateNumber));

        return CourierAccount::query()
            ->whereRaw('UPPER(plate_number) = ?', [$plateNumber])
            ->exists()
            || RegistrationApplication::query()
                ->when($exceptApplicationId, fn ($query) => $query->whereKeyNot($exceptApplicationId))
                ->where('role', 'rider')
                ->whereRaw('UPPER(plate_number) = ?', [$plateNumber])
                ->whereIn('status', ['unverified', 'pending', 'approved'])
                ->exists();
    }

    private function approvedAccountUsesEmail(string $email): bool
    {
        return BuyerAccount::query()->where('email', $email)->exists()
            || CourierAccount::query()->where('email', $email)->exists()
            || LogisticsAccount::query()->where('email', $email)->exists()
            || AdminAccount::query()->where('email', $email)->exists()
            || SellerAccount::query()->where('email', $email)->exists();
    }

    private function cleanSpacing(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }
}
