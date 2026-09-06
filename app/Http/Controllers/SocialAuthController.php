<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Social providers allowed by SARI.
     */
    private const ALLOWED_PROVIDERS = [
        'google',
        'facebook',
    ];

    /*
    |--------------------------------------------------------------------------
    | Redirect To Provider
    |--------------------------------------------------------------------------
    */

    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProviderIsAllowed($provider);
        $this->ensureProviderIsConfigured($provider);

        try {
            $driver = Socialite::driver($provider);

            if ($provider === 'google') {
                return $driver
                    ->scopes([
                        'openid',
                        'profile',
                        'email',
                    ])
                    ->redirect();
            }

            /*
            |--------------------------------------------------------------------------
            | Facebook
            |--------------------------------------------------------------------------
            */

            return $driver
                ->scopes([
                    'email',
                ])
                ->redirect();

        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        ucfirst($provider)
                        . ' sign-in is temporarily unavailable. Please try again.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Provider Callback
    |--------------------------------------------------------------------------
    */

    public function callback(
        Request $request,
        string $provider
    ): RedirectResponse {
        $this->ensureProviderIsAllowed($provider);
        $this->ensureProviderIsConfigured($provider);

        try {
            $providerUser = Socialite::driver($provider)->user();

            $providerId = trim(
                (string) $providerUser->getId()
            );

            if ($providerId === '') {
                throw new \RuntimeException(
                    'OAuth provider returned no user ID.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Existing Social Account
            |--------------------------------------------------------------------------
            */

            $account = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_user_id', $providerId)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            $incomingEmail = strtolower(
                trim(
                    (string) (
                        $providerUser->getEmail() ?? ''
                    )
                )
            );

            /*
            | We require an email for a new SARI social account.
            | If the user has already signed in before, preserve the saved email.
            */
            if (!$account && $incomingEmail === '') {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' =>
                            ucfirst($provider)
                            . ' did not provide an email address. '
                            . 'Please allow email access or use email/password sign-in.',
                    ]);
            }

            if (!$account) {
                $account = new SocialAccount();

                $account->provider = $provider;
                $account->provider_user_id = $providerId;
            }

            if ($incomingEmail !== '') {
                $account->email = $incomingEmail;
            }

            /*
            |--------------------------------------------------------------------------
            | Name
            |--------------------------------------------------------------------------
            */

            $incomingName = trim(
                (string) (
                    $providerUser->getName() ?? ''
                )
            );

            if ($incomingName !== '') {
                $account->name = $incomingName;
            } elseif (!$account->name && $account->email) {
                $account->name = Str::headline(
                    Str::before(
                        $account->email,
                        '@'
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Avatar
            |--------------------------------------------------------------------------
            */

            $incomingAvatar = trim(
                (string) (
                    $providerUser->getAvatar() ?? ''
                )
            );

            if ($incomingAvatar !== '') {
                $account->avatar_url = $incomingAvatar;
            }

            $account->last_login_at = now();
            $account->save();

            /*
            |--------------------------------------------------------------------------
            | Secure Session Regeneration
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | SARI Role Safety
            |--------------------------------------------------------------------------
            |
            | Google/Facebook authentication verifies an external identity only.
            | It must never automatically grant Admin, Seller, or Courier access.
            |
            | Social sign-in always creates a Buyer session.
            |
            */

            $request->session()->forget([
                'is_admin',

                'is_seller',
                'seller_account_id',

                'is_courier',
                'courier_account_id',
                'courier_email',
                'courier_name',

                'buyer_account_id',
            ]);

            $request->session()->put([
                'is_buyer' => true,

                'buyer_social_account_id' => $account->id,
                'buyer_email' => $account->email,
                'buyer_name' => $account->name ?: 'SARI Buyer',
                'buyer_avatar' => $account->avatar_url,
                'auth_provider' => $provider,
            ]);

            return redirect()
                ->route('buyer.dashboard')
                ->with(
                    'success',
                    'Welcome to SARI, '
                    . ($account->name ?: 'Buyer')
                    . '!'
                );

        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'We could not complete '
                        . ucfirst($provider)
                        . ' sign-in. Please try again.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Provider Allowlist
    |--------------------------------------------------------------------------
    */

    private function ensureProviderIsAllowed(
        string $provider
    ): void {
        abort_unless(
            in_array(
                $provider,
                self::ALLOWED_PROVIDERS,
                true
            ),
            404
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Provider Configuration
    |--------------------------------------------------------------------------
    */

    private function ensureProviderIsConfigured(
        string $provider
    ): void {
        $config = config(
            'services.' . $provider,
            []
        );

        $required = [
            'client_id',
            'client_secret',
            'redirect',
        ];

        foreach ($required as $key) {
            if (blank($config[$key] ?? null)) {
                abort(
                    503,
                    ucfirst($provider)
                    . ' OAuth is not configured yet. Missing: '
                    . $key
                );
            }
        }
    }
}
