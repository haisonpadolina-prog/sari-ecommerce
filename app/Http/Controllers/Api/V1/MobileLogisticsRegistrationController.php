<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Registration\RegistrationApplication;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Registration\AccountRegistrationPolicy;
use App\Services\Registration\MobileRegistrationOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/** Uses the same registration_applications table and Admin approval workflow as web. */
class MobileLogisticsRegistrationController extends Controller
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
            'contact_no' => $this->policy->normalizeMobile($request->input('contact_no')),
        ]);
        $validated = $request->validate([
            'first_name' => ['required','string','min:2','max:50',"regex:/^[\pL\s.'\-]+$/u"],
            'last_name' => ['required','string','min:2','max:50',"regex:/^[\pL\s.'\-]+$/u"],
            'middle_initial' => ['nullable','string','size:1','regex:/^\pL$/u'],
            'sex' => ['nullable','in:Male,Female'], 'birthday' => ['nullable','date','after_or_equal:1900-01-01'],
            'email' => ['required','email:rfc','max:180'], 'contact_no' => ['required','regex:/^09\d{9}$/'],
            'region_code' => ['required','string','max:30'], 'region_name' => ['required','string','max:150'],
            'province_code' => ['required','string','max:30'], 'province_name' => ['required','string','max:150'],
            'municipality_code' => ['required','string','max:30'], 'municipality_name' => ['required','string','max:150'],
            'barangay_code' => ['required','string','max:30'], 'barangay_name' => ['required','string','max:150'],
            'street_address' => ['required','string','max:500'], 'postal_code' => ['required','regex:/^\d{4}$/'],
            'business_name' => ['required','string','max:180',"regex:/^[\pL\pN\s.&'()\-]+$/u"],
            'tin' => ['required','regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/'],
            'sec_registration_no' => ['required','regex:/^[A-Za-z]{2}\d{9}$/'],
            'position' => ['required','string','max:120'],
            'service_areas' => ['required','array','min:1'],
            'service_areas.*' => ['required','string','max:120'],
            'profile_image' => ['required','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'id_document' => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'business_permit' => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'bir_document' => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'sec_document' => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'password' => ['required','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()],
            'terms' => ['accepted'],
        ]);
        $email = (string) $validated['email'];
        if ($this->emailBans->isBanned($email) || $this->policy->emailInUse($email)) {
            throw ValidationException::withMessages(['email'=>'This email is unavailable or awaiting review.']);
        }
        if ($this->policy->mobileInUse($validated['contact_no'])) {
            throw ValidationException::withMessages(['contact_no'=>'This number is already registered.']);
        }
        if ($this->policy->tinInUse($validated['tin'])) {
            throw ValidationException::withMessages(['tin'=>'This TIN is already registered.']);
        }
        if (RegistrationApplication::query()->where('sec_registration_no', strtoupper($validated['sec_registration_no']))
            ->whereIn('status',['unverified','pending','approved'])->exists()) {
            throw ValidationException::withMessages(['sec_registration_no'=>'This SEC number is already registered.']);
        }
        if (RegistrationApplication::query()->whereRaw('LOWER(business_name) = ?', [mb_strtolower(trim($validated['business_name']))])
            ->where('role','logistics')->whereIn('status',['unverified','pending','approved'])->exists()) {
            throw ValidationException::withMessages(['business_name'=>'Business name is already registered.']);
        }
        // Avoid spoofed PSGC names/codes: same common registration policy used by web.
        $files = [];
        try {
            foreach (['profile_image','id_document','business_permit','bir_document','sec_document'] as $field) {
                $files[$field] = $request->file($field)->store('registration/logistics/'.now()->format('Y/m'), 'public');
                if (!$files[$field]) throw ValidationException::withMessages([$field=>'Upload failed.']);
            }
            $application = DB::transaction(function () use ($validated, $files) {
                if (RegistrationApplication::query()->whereRaw('LOWER(email) = ?', [$validated['email']])->exists()) {
                    throw ValidationException::withMessages(['email'=>'A registration with this email already exists.']);
                }
                $birthday = isset($validated['birthday']) ? \Illuminate\Support\Carbon::parse($validated['birthday']) : null;
                $app = new RegistrationApplication();
                $app->forceFill([
                    'role'=>'logistics','logistics_account_id'=>null,
                    'first_name'=>trim($validated['first_name']), 'last_name'=>trim($validated['last_name']),
                    'middle_initial'=>$validated['middle_initial'] ?? null,'sex'=>$validated['sex'] ?? null,
                    'birthday'=>$birthday?->toDateString(),'age'=>$birthday?->age,
                    'email'=>$validated['email'],'contact_no'=>$validated['contact_no'],
                    'region_code'=>$validated['region_code'],'region_name'=>$validated['region_name'],
                    'province_code'=>$validated['province_code'],'province_name'=>$validated['province_name'],
                    'municipality_code'=>$validated['municipality_code'],'municipality_name'=>$validated['municipality_name'],
                    'barangay_code'=>$validated['barangay_code'],'barangay_name'=>$validated['barangay_name'],
                    'street_address'=>$validated['street_address'],'postal_code'=>$validated['postal_code'],
                    'business_name'=>trim($validated['business_name']),'tin'=>$validated['tin'],
                    'sec_registration_no'=>strtoupper($validated['sec_registration_no']),
                    'position'=>trim($validated['position']),
                    'service_areas'=>implode('; ', array_unique($validated['service_areas'])),
                    'password'=>Hash::make($validated['password']),
                    'profile_image_path'=>$files['profile_image'],'id_path'=>$files['id_document'],
                    'business_permit_path'=>$files['business_permit'],'bir_path'=>$files['bir_document'],
                    'sec_path'=>$files['sec_document'],'status'=>'unverified',
                ])->save();
                return $app;
            }, 3);
            $sent = $this->otp->sendCode($email, 'logistics');
            return response()->json([
                'success'=>true, 'data'=>['application_id'=>$application->id,'status'=>'unverified'],
                'message'=>$sent['ok'] ? 'Application saved. Enter the emailed OTP.' :
                    'Application saved. Use resend OTP if the email did not arrive.',
            ], 201);
        } catch (\Throwable $e) {
            foreach ($files as $file) Storage::disk('public')->delete($file);
            throw $e;
        }
    }

    public function resend(Request $request): JsonResponse
    {
        $params = $request->validate(['email'=>['required','email','max:180']]);
        $email = $this->policy->normalizeEmail($params['email']);
        RegistrationApplication::query()->where('role','logistics')->where('status','unverified')
            ->whereRaw('LOWER(email) = ?',[$email])->firstOrFail();
        $result = $this->otp->sendCode($email,'logistics');
        return response()->json(['success'=>(bool)$result['ok'],'message'=>$result['message']],$result['status']);
    }

    public function verify(Request $request): JsonResponse
    {
        $params = $request->validate(['email'=>['required','email','max:180'], 'otp'=>['required','digits:6']]);
        $email = $this->policy->normalizeEmail($params['email']);
        $application = RegistrationApplication::query()->where('role','logistics')->where('status','unverified')
            ->whereRaw('LOWER(email) = ?',[$email])->firstOrFail();
        $result = $this->otp->verifyCode($email, $params['otp'],'logistics');
        if (!$result['ok']) return response()->json(['success'=>false,'message'=>$result['message']],$result['status']);
        DB::transaction(function () use ($application) {
            $locked = RegistrationApplication::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'unverified') throw ValidationException::withMessages(['otp'=>'Application no longer awaits OTP.']);
            $locked->forceFill(['status'=>'pending'])->save();
        });
        return response()->json(['success'=>true,'message'=>'Email verified; application pending Admin approval.',
            'data'=>['status'=>'pending','application_id'=>$application->id]]);
    }
}
