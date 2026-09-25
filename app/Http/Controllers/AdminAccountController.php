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
        $account = $this->account($request);

        return view('admin.account', [
            'adminAccount' => $account,
            'sessionInfo' => $this->sessionInfo($request),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admin_accounts', 'email')->ignore($account->id),
            ],
        ]);

        $account->name = trim($validated['name']);
        $account->email = strtolower(trim($validated['email']));
        $account->profile_updated_at = now();
        $account->save();

        $request->session()->put('admin_account_id', $account->id);

        return back()->with('success', 'Administrator profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $account->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ], 'password');
        }

        if (Hash::check($validated['password'], $account->password)) {
            return back()->withErrors([
                'password' => 'New password must be different from your current password.',
            ], 'password');
        }

        $account->password = Hash::make($validated['password']);
        $account->password_changed_at = now();
        $account->save();

        // Keep the current Admin signed in while rotating the session identifier.
        $request->session()->regenerate();
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_account_id', $account->id);

        return back()->with('success', 'Administrator password updated successfully.');
    }

    /**
     * Backward-compatible endpoint for the previous combined Account form.
     */
    public function update(Request $request): RedirectResponse
    {
        $account = $this->account($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admin_accounts', 'email')->ignore($account->id),
            ],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check((string) ($validated['current_password'] ?? ''), $account->password)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.',
                ]);
            }

            if (Hash::check($validated['password'], $account->password)) {
                return back()->withErrors([
                    'password' => 'New password must be different from your current password.',
                ]);
            }

            $account->password = Hash::make($validated['password']);
            $account->password_changed_at = now();
        }

        $account->name = trim($validated['name']);
        $account->email = strtolower(trim($validated['email']));
        $account->profile_updated_at = now();
        $account->save();

        $request->session()->put('admin_account_id', $account->id);

        return back()->with('success', 'Administrator account updated successfully.');
    }

    private function sessionInfo(Request $request): array
    {
        $userAgent = (string) ($request->userAgent() ?? '');
        $agent = strtolower($userAgent);

        $browser = match (true) {
            str_contains($agent, 'edg/') => 'Microsoft Edge',
            str_contains($agent, 'opr/') || str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'chrome/') => 'Google Chrome',
            str_contains($agent, 'firefox/') => 'Mozilla Firefox',
            str_contains($agent, 'safari/') => 'Safari',
            default => 'Web browser',
        };

        $platform = match (true) {
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'iphone') || str_contains($agent, 'ipad') => 'iOS',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'mac os') || str_contains($agent, 'macintosh') => 'macOS',
            str_contains($agent, 'linux') => 'Linux',
            default => 'Unknown platform',
        };

        return [
            'browser' => $browser,
            'platform' => $platform,
            'ip' => $request->ip(),
        ];
    }
}
