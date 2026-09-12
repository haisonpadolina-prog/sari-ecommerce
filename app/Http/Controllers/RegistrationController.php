<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use App\Services\RegistrationEmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationEmailVerificationService $emailVerification
    ) {}

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

        if (!$this->emailVerification->isVerified($request, $email)) {
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

        $this->emailVerification->clear($request);

        // Keep the submitted application in the current browser session so the
        // pending page can safely poll only this registration's review status.
        $request->session()->put([
            'registration_tracking_id' => (int) $application->id,
            'registration_email' => $application->email,
            'registration_role' => $application->role,
        ]);
        $request->session()->forget('registration_logistics_name');

        return redirect()->route('registration.pending');
    }

    public function pending(Request $request)
    {
        $application = $this->trackedApplication($request);

        if ($application) {
            $request->session()->put([
                'registration_tracking_id' => (int) $application->id,
                'registration_email' => $application->email,
                'registration_role' => $application->role,
            ]);

            if ($application->role === 'rider' && $application->logistics) {
                $request->session()->put(
                    'registration_logistics_name',
                    $application->logistics->displayName()
                );
            }
        }

        return view('pages.registration-pending', compact('application'));
    }

    public function status(Request $request): JsonResponse
    {
        $application = $this->trackedApplication($request);

        if (!$application) {
            return response()->json([
                'message' => 'No registration is being tracked in this browser session.',
            ], 404);
        }

        $reviewer = $application->role === 'rider'
            ? ($application->logistics?->displayName() ?? 'SARI Logistics / Sorting Center')
            : 'administrator';

        return response()->json([
            'id' => (int) $application->id,
            'status' => (string) $application->status,
            'role' => (string) $application->role,
            'email' => (string) $application->email,
            'reviewer' => $reviewer,
            'admin_note' => $application->status === 'rejected'
                ? (string) ($application->admin_note ?? '')
                : null,
            'reviewed_at' => $application->reviewed_at?->toIso8601String(),
            'approved_at' => $application->approved_at?->toIso8601String(),
            'rejected_at' => $application->rejected_at?->toIso8601String(),
        ]);
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

        $result = $this->emailVerification->sendCode($request, $email);
        $status = (int) ($result['status'] ?? 200);
        unset($result['ok'], $result['status']);

        return response()->json($result, $status);
    }

    private function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'otp' => ['required', 'digits:6'],
        ]);

        $result = $this->emailVerification->verifyCode(
            $request,
            strtolower(trim($validated['email'])),
            (string) $validated['otp']
        );

        $status = (int) ($result['status'] ?? 200);
        unset($result['ok'], $result['status']);

        return response()->json($result, $status);
    }

    private function trackedApplication(Request $request): ?RegistrationApplication
    {
        $trackingId = (int) $request->session()->get('registration_tracking_id', 0);
        $email = strtolower(trim((string) $request->session()->get('registration_email', '')));

        if ($trackingId > 0) {
            $query = RegistrationApplication::query()->with('logistics')->whereKey($trackingId);

            if ($email !== '') {
                $query->where('email', $email);
            }

            $application = $query->first();
            if ($application) {
                return $application;
            }
        }

        // Backward-compatible fallback for a registration submitted before this
        // tracking update was installed, as long as its email is still in session.
        if ($email !== '') {
            return RegistrationApplication::query()
                ->with('logistics')
                ->where('email', $email)
                ->latest('id')
                ->first();
        }

        return null;
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
