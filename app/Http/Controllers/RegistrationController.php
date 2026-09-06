<?php

namespace App\Http\Controllers;

use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('pages.register');
    }

    public function store(Request $request)
    {
        $role = (string) $request->input('role');

        $personName = [
            'required',
            'string',
            'max:100',
            'regex:/^[\pL\s.\'-]+$/u',
        ];

        $rules = [
            'role' => ['required', Rule::in(['buyer', 'seller', 'courier'])],
            'last_name' => $personName,
            'first_name' => $personName,
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^\pL$/u'],
            'sex' => ['required', Rule::in(['Male', 'Female'])],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],

            'province_code' => ['required', 'string', 'max:30'],
            'province_name' => ['required', 'string', 'max:150'],
            'municipality_code' => ['required', 'string', 'max:30'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'barangay_code' => ['required', 'string', 'max:30'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'street_address' => ['required', 'string', 'max:500'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],

            'id_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
        ];

        if ($role === 'seller') {
            $rules = array_merge($rules, [
                'business_name' => [
                    'required',
                    'string',
                    'max:180',
                    'regex:/^[\pL\s.&\'()\-]+$/u',
                ],
                'line_of_business' => ['required', 'string', 'max:120'],
                'business_permit' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,webp,pdf',
                    'max:8192',
                ],
            ]);
        }

        if ($role === 'courier') {
            $rules = array_merge($rules, [
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
                'orcr_document' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,webp,pdf',
                    'max:8192',
                ],
            ]);
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
            'business_name.regex' =>
                'Business name may contain letters and common punctuation, but numbers are not allowed.',
            'plate_number.regex' =>
                'Plate number may contain letters, numbers, spaces, and hyphens only.',
        ];

        $validated = $request->validate($rules, $messages);

        $email = strtolower(trim($validated['email']));

        if (
            BuyerAccount::query()->where('email', $email)->exists()
            || CourierAccount::query()->where('email', $email)->exists()
            || SellerAccount::query()->where('email', $email)->exists()
        ) {
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
                    'email' => 'This email already has a registration waiting for administrator approval.',
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
        }

        $folder = 'registration-documents/' . $role . '/' . now()->format('Y/m');

        $idPath = $request
            ->file('id_document')
            ->store($folder . '/id', 'local');

        $permitPath = $request->hasFile('business_permit')
            ? $request->file('business_permit')
                ->store($folder . '/business-permit', 'local')
            : null;

        $orcrPath = $request->hasFile('orcr_document')
            ? $request->file('orcr_document')
                ->store($folder . '/orcr', 'local')
            : null;

        $birthday = Carbon::parse($validated['birthday']);

        $payload = [
            'role' => $role,
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

            'business_name' => $role === 'seller'
                ? $this->cleanSpacing($validated['business_name'])
                : null,
            'line_of_business' => $role === 'seller'
                ? trim($validated['line_of_business'])
                : null,

            'vehicle_type' => $role === 'courier'
                ? $validated['vehicle_type']
                : null,
            'plate_number' => $role === 'courier'
                ? strtoupper(trim($validated['plate_number']))
                : null,

            'id_path' => $idPath,
            'business_permit_path' => $permitPath,
            'orcr_path' => $orcrPath,

            'status' => 'pending',
            'admin_note' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ];

        $application = $existing ?: new RegistrationApplication();
        $application->fill($payload);
        $application->save();

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

    private function cleanSpacing(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }
}
