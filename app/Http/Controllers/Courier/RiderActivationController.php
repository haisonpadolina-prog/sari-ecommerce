<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RiderActivationController extends Controller
{
    public function show(string $token): View
    {
        $rider = $this->validRider($token);
        return view('pages.rider-activate', compact('rider', 'token'));
    }

    public function activate(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/',
            ],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        DB::transaction(function () use ($token, $validated): void {
            $rider = CourierAccount::query()->where('activation_token_hash', hash('sha256', $token))->lockForUpdate()->firstOrFail();
            abort_unless($rider->account_status === 'pending_activation' && $rider->activation_expires_at?->isFuture() && !$rider->activated_at, 410, 'This activation link is invalid, expired, or already used.');
            $rider->forceFill([
                'password' => Hash::make($validated['password']), 'account_status' => 'active', 'availability_status' => 'online',
                'activated_at' => now(), 'approved_at' => now(), 'activation_token_hash' => null, 'activation_expires_at' => null,
            ])->save();
        });

        return redirect()->route('login')->with('success', 'Your Rider account is active. You can now log in.');
    }

    private function validRider(string $token): CourierAccount
    {
        $rider = CourierAccount::query()->where('activation_token_hash', hash('sha256', $token))->firstOrFail();
        abort_unless($rider->account_status === 'pending_activation' && $rider->activation_expires_at?->isFuture() && !$rider->activated_at, 410, 'This activation link is invalid, expired, or already used.');
        return $rider;
    }
}
