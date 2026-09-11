<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    private function account(Request $request): AdminAccount
    {
        abort_unless($request->session()->get('is_admin'), 403);
        $id = (int) $request->session()->get('admin_account_id');
        if ($id > 0 && ($account = AdminAccount::find($id))) {
            return $account;
        }

        return AdminAccount::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'SARI Administrator', 'password' => Hash::make('admin123')]
        );
    }

    public function index(Request $request): View
    {
        return view('admin.account', ['adminAccount' => $this->account($request)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = $this->account($request);
        $validated = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:255', Rule::unique('admin_accounts','email')->ignore($account->id)],
            'current_password' => ['nullable','string'],
            'password' => ['nullable','string','min:8','confirmed'],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check((string) ($validated['current_password'] ?? ''), $account->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $account->password = Hash::make($validated['password']);
        }

        $account->name = $validated['name'];
        $account->email = strtolower($validated['email']);
        $account->save();

        $request->session()->put('admin_account_id', $account->id);
        return back()->with('success', 'Administrator account updated.');
    }
}
