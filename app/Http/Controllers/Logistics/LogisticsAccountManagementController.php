<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Services\Shared\AccountProfileService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        if (!empty($validated['password'])) {
            if (
                !Hash::check(
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

        return back()->with(
            'success',
            'Logistics account updated.'
        );
    }
}
