<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Services\Shared\AccountProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class SellerAccountController extends Controller
{
    private function account(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403);

        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get('seller_account_id')
        );
    }

    public function index(Request $request): View
    {
        $account = $this->account($request);

        return view('seller.account', [
            'account' => $account,
            'profilePhotoUrl' => $this->hasProfilePhoto($account)
                ? route('seller.account.profile-photo')
                : null,
            'documentAvailability' => [
                'id' => $this->documentExists($account->id_path),
                'permit' => $this->documentExists($account->business_permit_path),
            ],
        ]);
    }

    public function update(
        Request $request,
        AccountProfileService $profiles
    ): RedirectResponse {
        $account = $this->account($request);

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:60',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'last_name' => [
                'required',
                'string',
                'max:60',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'middle_initial' => [
                'nullable',
                'string',
                'max:1',
                'regex:/^\pL$/u',
            ],
            'sex' => [
                'required',
                Rule::in(['Male', 'Female']),
            ],
            'contact_no' => [
                'required',
                'string',
                'regex:/^09\d{9}$/',
            ],
            'birthday' => [
                'required',
                'date',
                'before_or_equal:'.now()->subYears(18)->toDateString(),
                'after_or_equal:1900-01-01',
            ],

            'street_address' => [
                'required',
                'string',
                'max:300',
            ],
            'barangay_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\pN\s.,\'()\/-]+$/u',
            ],
            'municipality_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\pN\s.,\'()\/-]+$/u',
            ],
            'province_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\pN\s.,\'()\/-]+$/u',
            ],

            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=120,min_height=120,max_width=6000,max_height=6000',
            ],
            'remove_profile_image' => [
                'nullable',
                'boolean',
            ],

            'government_id' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],
            'business_permit' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],
        ], [
            'first_name.regex' =>
                'First name may contain letters, spaces, apostrophes, periods, and hyphens only.',
            'first_name.max' =>
                'First name must not exceed 60 characters.',
            'last_name.regex' =>
                'Last name may contain letters, spaces, apostrophes, periods, and hyphens only.',
            'last_name.max' =>
                'Last name must not exceed 60 characters.',
            'middle_initial.regex' =>
                'Middle initial must contain one letter only.',
            'middle_initial.max' =>
                'Middle initial must contain one letter only.',
            'contact_no.regex' =>
                'Contact number must contain exactly 11 digits and start with 09.',
            'street_address.max' =>
                'Street address must not exceed 300 characters.',
            'barangay_name.regex' =>
                'Barangay contains unsupported characters.',
            'municipality_name.regex' =>
                'City or municipality contains unsupported characters.',
            'province_name.regex' =>
                'Province contains unsupported characters.',
            'profile_image.max' =>
                'Profile photo must not be larger than 5 MB.',
            'government_id.mimes' =>
                'Government ID must be a PDF, JPG, PNG, or WEBP file.',
            'government_id.max' =>
                'Government ID must not be larger than 10 MB.',
            'business_permit.mimes' =>
                'Business Permit must be a PDF, JPG, PNG, or WEBP file.',
            'business_permit.max' =>
                'Business Permit must not be larger than 10 MB.',
        ]);

        $attributes = [
            'first_name' => trim($validated['first_name']),
            'last_name' => trim($validated['last_name']),
            'middle_initial' => filled($validated['middle_initial'] ?? null)
                ? mb_strtoupper(trim((string) $validated['middle_initial']))
                : null,
            'sex' => $validated['sex'],
            'contact_no' => trim($validated['contact_no']),
            'birthday' => $validated['birthday'],
            'age' => (int) Carbon::parse($validated['birthday'])->age,

            'street_address' => trim($validated['street_address']),
            'barangay_name' => trim($validated['barangay_name']),
            'municipality_name' => trim($validated['municipality_name']),
            'province_name' => trim($validated['province_name']),
        ];

        $oldDocuments = [
            'id_path' => (string) ($account->id_path ?? ''),
            'business_permit_path' =>
                (string) ($account->business_permit_path ?? ''),
        ];

        $newDocuments = [];

        try {
            if ($request->hasFile('government_id')) {
                $path = $request->file('government_id')->store(
                    'account-documents/seller_accounts/'
                    .$account->getKey()
                    .'/government-id',
                    'local'
                );

                if (!$path) {
                    throw ValidationException::withMessages([
                        'government_id' =>
                            'Government ID could not be saved. Please try again.',
                    ]);
                }

                $attributes['id_path'] = $path;
                $newDocuments['id_path'] = $path;
            }

            if ($request->hasFile('business_permit')) {
                $path = $request->file('business_permit')->store(
                    'account-documents/seller_accounts/'
                    .$account->getKey()
                    .'/business-permit',
                    'local'
                );

                if (!$path) {
                    throw ValidationException::withMessages([
                        'business_permit' =>
                            'Business Permit could not be saved. Please try again.',
                    ]);
                }

                $attributes['business_permit_path'] = $path;
                $newDocuments['business_permit_path'] = $path;
            }

            /*
             * AccountProfileService safely stores/replaces the profile image
             * and saves all account attributes in the same account update.
             */
            $profiles->save($account, $attributes, $request);
        } catch (Throwable $exception) {
            foreach ($newDocuments as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        /*
         * Never delete immutable registration evidence. Only remove a
         * previous document if it was uploaded later through Account
         * Management and the replacement save already succeeded.
         */
        foreach ($newDocuments as $column => $newPath) {
            $oldPath = $oldDocuments[$column] ?? '';

            if (
                $oldPath !== ''
                && str_starts_with($oldPath, 'account-documents/')
                && $oldPath !== $newPath
            ) {
                Storage::disk('local')->delete($oldPath);
            }
        }

        return back()->with(
            'success',
            'Seller account information updated successfully.'
        );
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validateWithBag('password', [
            'current_password' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
            ],
        ]);

        if (
            !Hash::check(
                $validated['current_password'],
                (string) $account->password
            )
        ) {
            return back()->withErrors([
                'current_password' =>
                    'Current password is incorrect.',
            ], 'password');
        }

        if (
            Hash::check(
                $validated['password'],
                (string) $account->password
            )
        ) {
            return back()->withErrors([
                'password' =>
                    'Choose a new password that is different from your current password.',
            ], 'password');
        }

        $account->forceFill([
            'password' => Hash::make(
                $validated['password']
            ),
        ])->save();

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Password updated successfully.'
        );
    }

    public function profilePhoto(
        Request $request
    ): BinaryFileResponse {
        $account = $this->account($request);
        $path = $account->profile_image_path;

        abort_unless(
            $this->hasProfilePhoto($account),
            404,
            'Profile photo not found.'
        );

        return response()->file(
            Storage::disk('public')->path($path),
            [
                'Cache-Control' =>
                    'private, max-age=300',
            ]
        );
    }

    public function document(
        Request $request,
        string $document
    ): BinaryFileResponse {
        $account = $this->account($request);

        $column = match ($document) {
            'id' => 'id_path',
            'permit' => 'business_permit_path',
            default => abort(404),
        };

        $path = $account->{$column};

        abort_unless(
            $this->documentExists($path),
            404,
            'Verification document not found.'
        );

        return response()->file(
            Storage::disk('local')->path($path),
            [
                'Cache-Control' =>
                    'private, no-store',
            ]
        );
    }

    private function hasProfilePhoto(
        SellerAccount $account
    ): bool {
        return filled($account->profile_image_path)
            && Storage::disk('public')->exists(
                (string) $account->profile_image_path
            );
    }

    private function documentExists(
        ?string $path
    ): bool {
        return filled($path)
            && Storage::disk('local')->exists(
                (string) $path
            );
    }
}
