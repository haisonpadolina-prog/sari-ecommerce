<?php

namespace App\Http\Controllers;

use App\Models\LogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsAccountManagementController extends Controller
{
    private function account(Request $request): ?LogisticsAccount
    {
        $id = (int) $request->session()->get('logistics_account_id');
        return $id > 0 ? LogisticsAccount::find($id) : null;
    }

    public function index(Request $request): View
    {
        return view('logistics.account-management', ['account' => $this->account($request)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = $this->account($request);
        if (!$account) {
            return back()->withErrors(['account' => 'The temporary Logistics test login has no database profile. Register and approve a real Logistics account to edit profile data.']);
        }

        $validated = $request->validate([
            'first_name' => ['required','string','max:120'],
            'last_name' => ['required','string','max:120'],
            'contact_no' => ['required','string','max:30'],
            'email' => ['required','email','max:255', Rule::unique('logistics_accounts','email')->ignore($account->id)],
            'street_address' => ['required','string','max:1000'],
            'current_password' => ['nullable','string'],
            'password' => ['nullable','string','min:8','confirmed'],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check((string) ($validated['current_password'] ?? ''), $account->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $account->password = Hash::make($validated['password']);
        }

        $account->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'contact_no' => $validated['contact_no'],
            'email' => strtolower($validated['email']),
            'street_address' => $validated['street_address'],
        ])->save();

        $request->session()->put('logistics_email', $account->email);
        $request->session()->put('logistics_name', trim($account->first_name . ' ' . $account->last_name));

        return back()->with('success', 'Logistics account updated.');
    }
}
