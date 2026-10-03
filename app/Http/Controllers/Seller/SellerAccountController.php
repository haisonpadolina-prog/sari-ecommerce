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
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Update seller identity/contact information only.
     * Store-facing details intentionally live in SellerStoreController.
     */
    public function update(Request $request, AccountProfileService $profiles): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'sex' => ['required', Rule::in(['Male', 'Female'])],
            'contact_no' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'birthday' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
                'after_or_equal:1900-01-01',
            ],
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=120,min_height=120,max_width=6000,max_height=6000',
            ],
            'remove_profile_image' => ['nullable', 'boolean'],
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
        ];

        $profiles->save($account, $attributes, $request);

        return back()->with('success', 'Seller profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], (string) $account->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ], 'password');
        }

        if (Hash::check($validated['password'], (string) $account->password)) {
            return back()->withErrors([
                'password' => 'Choose a new password that is different from your current password.',
            ], 'password');
        }

        $account->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        // Rotate the session identifier after a credential change without
        // signing the seller out of the current device.
        $request->session()->regenerate();

        return back()->with('success', 'Password updated successfully.');
    }

    public function profilePhoto(Request $request): BinaryFileResponse
    {
        $account = $this->account($request);
        $path = $account->profile_image_path;

        abort_unless($this->hasProfilePhoto($account), 404, 'Profile photo not found.');

        return response()->file(
            Storage::disk('public')->path($path),
            ['Cache-Control' => 'private, max-age=300']
        );
    }

    public function document(Request $request, string $document): BinaryFileResponse
    {
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
            ['Cache-Control' => 'private, no-store']
        );
    }

    private function hasProfilePhoto(SellerAccount $account): bool
    {
        return filled($account->profile_image_path)
            && Storage::disk('public')->exists((string) $account->profile_image_path);
    }

    private function documentExists(?string $path): bool
    {
        return filled($path) && Storage::disk('local')->exists((string) $path);
    }
}
