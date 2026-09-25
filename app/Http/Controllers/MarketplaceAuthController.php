<?php

namespace App\Http\Controllers;

use App\Models\BuyerAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MarketplaceAuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'redirect_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $email = strtolower(trim($validated['email']));
        $buyer = BuyerAccount::query()->where('email', $email)->first();

        if (!$buyer || !Hash::check($validated['password'], $buyer->password)) {
            return back()
                ->withErrors(['email' => 'Invalid buyer email or password.'])
                ->withInput($request->only('email', 'redirect_to'));
        }

        if (($buyer->account_status ?? 'active') !== 'active') {
            return back()
                ->withErrors(['email' => 'This buyer account is not currently active.'])
                ->withInput($request->only('email', 'redirect_to'));
        }

        $request->session()->regenerate();
        $request->session()->put([
            'is_buyer' => true,
            'buyer_account_id' => $buyer->id,
            'buyer_email' => $buyer->email,
            'buyer_name' => trim($buyer->first_name . ' ' . $buyer->last_name) ?: 'SARI Buyer',
        ]);

        $request->session()->forget([
            'buyer_social_account_id',
            'buyer_avatar',
            'auth_provider',
            'is_admin',
            'admin_account_id',
            'is_seller',
            'seller_account_id',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        $redirectTo = $this->safeBuyerRedirect($validated['redirect_to'] ?? null);

        return $redirectTo
            ? redirect()->to($redirectTo)
            : redirect()->route('buyer.dashboard');
    }

    private function safeBuyerRedirect(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $parts = parse_url($value);

        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');

        if (!str_starts_with($path, '/buyer/')) {
            return null;
        }

        return $value;
    }
}
