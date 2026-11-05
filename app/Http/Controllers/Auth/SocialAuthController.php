<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Seller\SellerAccountStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function __construct(private readonly AccountEmailBanService $emailBanService) {}

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

            // Google signs in existing SARI accounts; it never creates a new one.
            // Keep the existing Facebook buyer flow unchanged.
            if ($provider === 'google') {
                return $this->loginWithRegisteredGoogleAccount($request, $providerUser, $providerId);
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
            $banCheckEmail = $incomingEmail !== ''
                ? $incomingEmail
                : strtolower(trim((string) ($account?->email ?? '')));

            if ($banCheckEmail !== '' && $this->emailBanService->isBanned($banCheckEmail)) {
                return redirect()->route('login')->withErrors([
                    'social' => 'This email address has been banned by SARI.',
                ]);
            }

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

            $status = strtolower(
                (string) ($account->account_status ?: 'active')
            );

            if ($status === 'banned') {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' => 'This SARI buyer account has been banned by an administrator.',
                    ]);
            }

            if ($status === 'deactivated') {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' => 'This SARI buyer account is currently suspended. Please contact SARI support.',
                    ]);
            }

            if ($status === 'blocked') {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' => 'This SARI buyer account has been blocked by an administrator.',
                    ]);
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
            | Legacy Facebook sign-in is restricted to the Buyer session.
            | Google role-aware sign-in is handled separately above.
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
    | Registered Google Account Login
    |--------------------------------------------------------------------------
    */

    /**
     * A verified Google identity is a way to enter an existing SARI role,
     * not a replacement for registration and approval.
     */
    private function loginWithRegisteredGoogleAccount(
        Request $request,
        AbstractUser $googleUser,
        string $providerId
    ): RedirectResponse {
        $email = strtolower(trim((string) $googleUser->getEmail()));
        $emailVerified = filter_var(
            $googleUser->getRaw()['email_verified'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if (!$emailVerified || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('login')->withErrors([
                'social' => 'Google could not verify your email address. Please choose a verified Google account or sign in with your email and password.',
            ]);
        }

        if ($this->emailBanService->isBanned($email)) {
            return redirect()->route('login')->withErrors([
                'social' => 'This email address has been banned by SARI.',
            ]);
        }

        // Registration enforces unique email addresses across SARI roles.
        // Reject inconsistent legacy duplicates rather than guessing a role.
        $matches = [];
        foreach ([
            'buyer' => BuyerAccount::class,
            'seller' => SellerAccount::class,
            'logistics' => LogisticsAccount::class,
            'courier' => CourierAccount::class,
        ] as $role => $accountClass) {
            $registered = $accountClass::query()->where('email', $email)->first();
            if ($registered) {
                $matches[$role] = $registered;
            }
        }

        if (count($matches) > 1) {
            return redirect()->route('login')->withErrors([
                'social' => 'Multiple SARI roles use this email. Please contact SARI support to verify the correct account.',
            ]);
        }

        if ($matches === []) {
            // Previously created Google-only Buyer accounts remain usable.
            // Do not create a new SocialAccount for first-time Google users.
            $social = SocialAccount::query()
                ->where('provider', 'google')
                ->where('provider_user_id', $providerId)
                ->first();

            if ($social) {
                return $this->loginWithExistingGoogleBuyer($request, $social, $googleUser, $email);
            }

            $application = RegistrationApplication::query()
                ->where('email', $email)
                ->latest('id')
                ->first();

            if ($application) {
                $message = match ((string) $application->status) {
                    'pending' => 'Your SARI registration is still under review. Please wait until your account is approved before signing in with Google.',
                    'unverified' => 'Your SARI registration is not finished yet. Please complete registration and email verification first.',
                    'rejected' => 'Your SARI application was not approved. Please review and resubmit your registration.',
                    default => 'Your SARI registration is not yet available for login. Please contact SARI support.',
                };

                return redirect()->route('login')->withErrors(['social' => $message]);
            }

            return redirect()->route('login')->withErrors([
                'social_register' => 'No SARI account is registered with this Google email. Please register first, then sign in with Google after your account is approved.',
            ]);
        }

        $role = array_key_first($matches);
        $account = $matches[$role];

        if ($role === 'seller') {
            if ((string) ($account->registration_status ?? 'approved') !== 'approved') {
                return redirect()->route('login')->withErrors([
                    'social' => 'Your seller registration has not been approved yet.',
                ]);
            }

            $account = app(SellerAccountStatusService::class)->refresh($account);
        }

        $status = strtolower((string) ($account->account_status ?: 'active'));
        $isRestricted = $role === 'seller'
            ? in_array($status, ['banned', 'deactivated', 'blocked'], true)
            : $status !== 'active';

        if ($isRestricted) {
            return redirect()->route('login')->withErrors([
                'social' => 'This SARI ' . ($role === 'courier' ? 'Rider' : ucfirst($role)) . ' account is not currently active. Please contact SARI support.',
            ]);
        }

        if ($role === 'courier' && $account->logistics_account_id) {
            if ($account->logistics?->account_status !== 'active') {
                return redirect()->route('login')->withErrors([
                    'social' => 'Your Rider account requires an active Logistics provider.',
                ]);
            }
        }

        if ($role === 'seller') {
            $account->ensureRealtimeToken();
        }

        $request->session()->regenerate();
        $this->clearSariRoleSession($request);

        $name = trim((string) $account->first_name . ' ' . (string) $account->last_name);
        $name = $name !== '' ? $name : 'SARI ' . ucfirst($role);
        $session = ['auth_provider' => 'google'];
        $destination = match ($role) {
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'logistics' => 'logistics.dashboard',
            'courier' => 'courier.dashboard',
        };

        switch ($role) {
            case 'buyer':
                $session += [
                    'is_buyer' => true,
                    'buyer_account_id' => $account->id,
                    'buyer_email' => $account->email,
                    'buyer_name' => $name,
                ];
                break;
            case 'seller':
                $session += [
                    'is_seller' => true,
                    'seller_account_id' => $account->id,
                ];
                break;
            case 'logistics':
                $session += [
                    'is_logistics' => true,
                    'logistics_account_id' => $account->id,
                    'logistics_email' => $account->email,
                    'logistics_name' => $name,
                    'logistics_role' => 'manager',
                ];
                break;
            case 'courier':
                $session += [
                    'is_courier' => true,
                    'courier_account_id' => $account->id,
                    'courier_email' => $account->email,
                    'courier_name' => $name,
                ];
                break;
        }

        $request->session()->put($session);

        $successMessage = 'Welcome back to SARI, ' . $name . '!';
        $request->session()->put('sari_login_success_pending', $successMessage);
        return redirect()->route($destination)
            ->with('sari_login_success', $successMessage);
    }

    /**
     * Keep legacy Google Buyer accounts accessible without creating new ones.
     */
    private function loginWithExistingGoogleBuyer(
        Request $request,
        SocialAccount $account,
        AbstractUser $googleUser,
        string $verifiedEmail
    ): RedirectResponse {
        if ((string) ($account->account_status ?: 'active') !== 'active'
            || $this->emailBanService->isBanned((string) $account->email)) {
            return redirect()->route('login')->withErrors([
                'social' => 'This SARI buyer account is not currently accessible.',
            ]);
        }

        $name = trim((string) ($googleUser->getName() ?? ''));
        $avatar = trim((string) ($googleUser->getAvatar() ?? ''));

        $account->email = $verifiedEmail;
        if ($name !== '') {
            $account->name = $name;
        }
        if ($avatar !== '') {
            $account->avatar_url = $avatar;
        }
        $account->last_login_at = now();
        $account->save();

        $request->session()->regenerate();
        $this->clearSariRoleSession($request);
        $request->session()->put([
            'is_buyer' => true,
            'buyer_social_account_id' => $account->id,
            'buyer_email' => $account->email,
            'buyer_name' => $account->name ?: 'SARI Buyer',
            'buyer_avatar' => $account->avatar_url,
            'auth_provider' => 'google',
        ]);

        $successMessage = 'Welcome back to SARI, ' . ($account->name ?: 'Buyer') . '!';
        $request->session()->put('sari_login_success_pending', $successMessage);
        return redirect()->route('buyer.dashboard')
            ->with('sari_login_success', $successMessage);
    }

    /**
     * SARI uses session flags rather than one Laravel guard for its web roles.
     * Never carry an earlier role into a new Google login.
     */
    private function clearSariRoleSession(Request $request): void
    {
        $request->session()->forget([
            'is_admin', 'admin_account_id',
            'is_buyer', 'buyer_account_id', 'buyer_social_account_id',
            'buyer_email', 'buyer_name', 'buyer_avatar',
            'is_seller', 'seller_account_id',
            'is_courier', 'courier_account_id', 'courier_email', 'courier_name',
            'is_logistics', 'logistics_account_id', 'logistics_email', 'logistics_name',
            'is_logistics_hub_operator', 'logistics_hub_account_id',
            'logistics_facility_id', 'logistics_hub_name', 'logistics_hub_email',
            'logistics_role', 'auth_provider',
        ]);
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
