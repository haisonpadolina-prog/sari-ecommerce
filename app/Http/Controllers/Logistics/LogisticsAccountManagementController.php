<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Services\Shared\AccountProfileService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsAccountManagementController extends Controller
{
    public function index(Request $request): View
    {
        $account = CurrentLogisticsAccount::resolve($request);

        return view(
            'logistics.account-management',
            compact('account')
        );
    }

    public function update(
        Request $request,
        AccountProfileService $profiles
    ): RedirectResponse {
        $account = CurrentLogisticsAccount::resolve($request);

        /*
        |--------------------------------------------------------------------------
        | Request Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'first_name' => [
                'required',
                'string',
                'max:120',
            ],

            'last_name' => [
                'required',
                'string',
                'max:120',
            ],

            'contact_no' => [
                'required',
                'string',
                'max:30',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'logistics_accounts',
                    'email'
                )->ignore($account->id),
            ],

            'street_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'current_password' => [
                'nullable',
                'string',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Activity Logs — Capture Previous State
        |--------------------------------------------------------------------------
        |
        | Compare meaningful account attributes before and after saving.
        |
        | These values are used only in memory for change detection.
        | Nothing from this snapshot, including the password hash,
        | is passed to Activity Logs or included in the response.
        |
        */

        $trackedFields = [
            'first_name',
            'last_name',
            'contact_no',
            'email',
            'street_address',
            'profile_image_path',
            'password',
        ];

        $before = Arr::only(
            $account->getAttributes(),
            $trackedFields
        );

        /*
        |--------------------------------------------------------------------------
        | Password Update
        |--------------------------------------------------------------------------
        |
        | Preserve existing current-password verification.
        | An incorrect password does not modify the account.
        |
        */

        if (! empty($validated['password'])) {

            if (
                ! Hash::check(
                    (string) (
                        $validated['current_password']
                        ?? ''
                    ),
                    $account->password
                )
            ) {

                return back()->withErrors([
                    'current_password' =>
                        'Current password is incorrect.',
                ]);
            }

            $account->password = Hash::make(
                $validated['password']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Save Profile
        |--------------------------------------------------------------------------
        |
        | Continue using the existing AccountProfileService.
        |
        | This handles:
        | - Profile information
        | - Profile picture uploads
        | - Profile picture replacement
        | - Profile picture removal
        |
        | If saving fails, execution stops before the audit hook.
        |
        */

        $profiles->save(
            $account,
            [
                'first_name' => $validated['first_name'],

                'last_name' => $validated['last_name'],

                'contact_no' => $validated['contact_no'],

                'email' => strtolower(
                    $validated['email']
                ),

                'street_address' =>
                    $validated['street_address'],
            ],
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logs — Detect Actual Changes
        |--------------------------------------------------------------------------
        |
        | Saving an identical form must not create a misleading
        | Account Updated activity.
        |
        | updated_at is intentionally excluded from comparison.
        |
        | The shared logging middleware will create the activity
        | record only when activity_changed is true.
        |
        */

        $after = Arr::only(
            $account->getAttributes(),
            $trackedFields
        );

        $request->attributes->set(
            'activity_changed',
            $before !== $after
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Logistics Session
        |--------------------------------------------------------------------------
        |
        | Preserve the existing session synchronization behavior.
        |
        */

        $request->session()->put(
            'logistics_account_id',
            $account->id
        );

        $request->session()->put(
            'logistics_email',
            $account->email
        );

        $request->session()->put(
            'logistics_name',
            $account->displayName()
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Logistics account updated.'
        );
    }
}
