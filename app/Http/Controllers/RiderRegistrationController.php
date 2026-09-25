<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use App\Services\RegistrationEmailVerificationService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RiderRegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationEmailVerificationService $emailVerification
    ) {}

    public function index(Request $request): View
    {
        CurrentLogisticsAccount::ensureDevelopmentProvider();

        $search = trim((string) $request->query('q', ''));

        $logistics = LogisticsAccount::query()
            ->where('account_status', 'active')
            ->withCount([
                'riderApplications as approved_riders_count' => fn ($query) => $query
                    ->where('role', 'rider')
                    ->where('status', 'approved'),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like): void {
                    $query
                        ->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('province_name', 'like', $like)
                        ->orWhere('municipality_name', 'like', $like)
                        ->orWhere('barangay_name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('province_name')
            ->orderBy('municipality_name')
            ->orderBy('first_name')
            ->get();

        return view('pages.rider-logistics', compact('logistics', 'search'));
    }

    public function create(Request $request, LogisticsAccount $logistics): View
    {
        abort_unless($logistics->account_status === 'active', 404);

        $logistics->loadCount([
            'riderApplications as approved_riders_count' => fn ($query) => $query
                ->where('role', 'rider')
                ->where('status', 'approved'),
        ]);

        $verifiedEmail = strtolower(trim(
            (string) $request->session()->get('rider_registration_verified_email', '')
        ));

        $verifiedLogisticsId = (int) $request->session()->get(
            'rider_registration_verified_logistics_id',
            0
        );

        if (
            $verifiedEmail === ''
            || $verifiedLogisticsId !== (int) $logistics->id
            || !$this->emailVerification->isVerified($request, $verifiedEmail)
        ) {
            $request->session()->forget([
                'rider_registration_verified_email',
                'rider_registration_verified_logistics_id',
            ]);

            return view('pages.rider-email-verification', compact('logistics'));
        }

        // The existing Rider form already uses old('email'), so flash the verified
        // address into old input for this request without replacing the big form.
        $oldInput = (array) $request->session()->getOldInput();
        if (empty($oldInput['email'])) {
            $request->session()->flashInput(array_merge($oldInput, [
                'email' => $verifiedEmail,
            ]));
        }

        return view('pages.rider-register', compact('logistics'));
    }

    public function sendEmailCode(
        Request $request,
        LogisticsAccount $logistics
    ): RedirectResponse {
        abort_unless($logistics->account_status === 'active', 404);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
        ]);

        $email = strtolower(trim($validated['email']));

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

        $result = $this->emailVerification->sendCode($request, $email);

        if (!($result['ok'] ?? false)) {
            return back()
                ->withErrors([
                    'email' => (string) ($result['message'] ?? 'Unable to send verification code.'),
                ])
                ->withInput();
        }

        $request->session()->put([
            'rider_registration_otp_email' => $email,
            'rider_registration_otp_logistics_id' => (int) $logistics->id,
        ]);

        $response = back()
            ->with('success', (string) ($result['message'] ?? 'Verification code sent.'))
            ->withInput(['email' => $email]);

        if (!empty($result['debug_code']) && app()->environment('local')) {
            $response->with('rider_debug_otp', (string) $result['debug_code']);
        }

        return $response;
    }

    public function verifyEmailCode(
        Request $request,
        LogisticsAccount $logistics
    ): RedirectResponse {
        abort_unless($logistics->account_status === 'active', 404);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = strtolower(trim($validated['email']));

        $sessionEmail = strtolower(trim(
            (string) $request->session()->get('rider_registration_otp_email', '')
        ));

        $sessionLogisticsId = (int) $request->session()->get(
            'rider_registration_otp_logistics_id',
            0
        );

        if (
            $sessionEmail !== $email
            || $sessionLogisticsId !== (int) $logistics->id
        ) {
            return back()
                ->withErrors([
                    'otp' => 'Request a new verification code for this Rider application.',
                ])
                ->withInput(['email' => $email]);
        }

        $result = $this->emailVerification->verifyCode(
            $request,
            $email,
            (string) $validated['otp']
        );

        if (!($result['ok'] ?? false)) {
            return back()
                ->withErrors([
                    'otp' => (string) ($result['message'] ?? 'The verification code is incorrect.'),
                ])
                ->withInput([
                    'email' => $email,
                ]);
        }

        $request->session()->put([
            'rider_registration_verified_email' => $email,
            'rider_registration_verified_logistics_id' => (int) $logistics->id,
        ]);

        $request->session()->forget([
            'rider_registration_otp_email',
            'rider_registration_otp_logistics_id',
        ]);

        return redirect()
            ->route('rider.logistics.apply', $logistics)
            ->with('success', 'Email verified. Complete your Rider application.')
            ->withInput(['email' => $email]);
    }

    public function store(Request $request, LogisticsAccount $logistics): RedirectResponse
    {
        abort_unless($logistics->account_status === 'active', 404);

        $personName = [
            'required',
            'string',
            'max:100',
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

            'vehicle_type' => [
                'required',
                Rule::in(['Motorcycle', 'Bicycle', 'Car', 'Van', 'Truck']),
            ],
            'plate_number' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Za-z0-9 -]+$/',
            ],

            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'id_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
            'orcr_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:8192',
            ],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'last_name.regex' => 'Last name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'middle_initial.size' => 'Middle initial must contain exactly one letter.',
            'middle_initial.regex' => 'Middle initial must contain one letter only.',
            'contact_no.regex' => 'Enter an 11-digit Philippine mobile number starting with 09.',
            'birthday.before_or_equal' => 'You must be 18 years old or older to register.',
            'birthday.after_or_equal' => 'Enter a valid birthday.',
            'profile_image.image' => 'Profile photo must be a valid image.',
            'profile_image.mimes' => 'Profile photo must be a JPG, JPEG, PNG, or WEBP image.',
            'profile_image.max' => 'Profile photo must not be larger than 5 MB.',
            'plate_number.regex' => 'Plate number may contain letters, numbers, spaces, and hyphens only.',
        ]);

        $email = strtolower(trim($validated['email']));

        $verifiedEmail = strtolower(trim(
            (string) $request->session()->get('rider_registration_verified_email', '')
        ));

        $verifiedLogisticsId = (int) $request->session()->get(
            'rider_registration_verified_logistics_id',
            0
        );

        if (
            $verifiedEmail !== $email
            || $verifiedLogisticsId !== (int) $logistics->id
            || !$this->emailVerification->isVerified($request, $email)
        ) {
            return redirect()
                ->route('rider.logistics.apply', $logistics)
                ->withErrors([
                    'email' => 'Verify this email address with the 6-digit code before submitting your Rider application.',
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

        if ($existing && $existing->status === 'rejected') {
            foreach ([$existing->id_path, $existing->orcr_path] as $oldFile) {
                if ($oldFile) {
                    Storage::disk('local')->delete($oldFile);
                }
            }

            if ($existing->profile_image_path) {
                Storage::disk('public')->delete($existing->profile_image_path);
            }
        }

        $folder = 'registration-documents/rider/' . now()->format('Y/m');

        $idPath = $request
            ->file('id_document')
            ->store($folder . '/id', 'local');

        $orcrPath = $request
            ->file('orcr_document')
            ->store($folder . '/orcr', 'local');

        $profileImagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store(
                'profile-images/rider/' . now()->format('Y/m'),
                'public'
            )
            : null;

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
            'business_name' => null,
            'line_of_business' => null,
            'vehicle_type' => $validated['vehicle_type'],
            'plate_number' => strtoupper(trim($validated['plate_number'])),
            'profile_image_path' => $profileImagePath,
            'id_path' => $idPath,
            'business_permit_path' => null,
            'orcr_path' => $orcrPath,
            'status' => 'pending',
            'admin_note' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ];

        $application = $existing ?: new RegistrationApplication();
        $application->forceFill($payload);
        $application->save();

        $this->emailVerification->clear($request);

        $request->session()->forget([
            'rider_registration_verified_email',
            'rider_registration_verified_logistics_id',
            'rider_registration_otp_email',
            'rider_registration_otp_logistics_id',
        ]);

        $request->session()->put([
            'registration_tracking_id' => (int) $application->id,
            'registration_email' => $application->email,
            'registration_role' => 'rider',
            'registration_logistics_name' => $logistics->displayName(),
        ]);

        return redirect()->route('registration.pending');
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
