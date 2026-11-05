{{-- Buyer-style authentication notification: same card/sizing as public/css/buyer/sari-toast.css. --}}
@php
    $onLoginPage = request()->routeIs('login') || request()->is('login');
    $authenticatedDestination = ! $onLoginPage && (
        (session()->get('is_buyer') && request()->is('buyer/*')) ||
        (session()->get('is_seller') && request()->is('seller/*')) ||
        (session()->get('is_courier') && request()->is('courier/*')) ||
        (session()->get('is_logistics') && request()->is('logistics/*'))
    );
    if ($onLoginPage) {
        session()->forget('sari_login_success_pending');
    }
    $successfulLogin = $onLoginPage
        ? ''
        : (string) (session()->pull('sari_login_success_pending') ?: session('sari_login_success', ''));
    $loginError = $onLoginPage ? (string) ($errors->first() ?? '') : '';
    $loginStatus = $onLoginPage ? (string) session('status', '') : '';
    $registrationRequired = $onLoginPage && $errors->has('social_register');
    $noticeMessage = $successfulLogin !== '' ? $successfulLogin : ($loginError !== '' ? $loginError : $loginStatus);
    $noticeType = $authenticatedDestination && $noticeMessage === ''
        ? 'success'
        : ($successfulLogin !== '' || ($loginError === '' && $loginStatus !== '') ? 'success' : 'error');
    $noticeTitle = $authenticatedDestination && $noticeMessage === ''
        ? 'Login successful'
        : ($successfulLogin !== '' ? 'Login successful' : ($registrationRequired ? 'Registration required' : ($loginError !== '' ? 'Unable to log in' : 'Success')));
@endphp

{{-- Render a hidden destination toast shell even when an intermediate redirect
     consumed the flash. The JS requires a fresh same-tab login attempt and
     an authenticated, server-rendered role destination before showing fallback. --}}
@if ($noticeMessage !== '' || $authenticatedDestination)
    {{-- Reuse the REAL Buyer toast stylesheet, not approximated modal tokens. --}}
    @unless(request()->is('buyer/*'))
        <link rel="stylesheet" href="{{ asset('css/buyer/sari-toast.css') }}?v=20261101-buyer-unified-alert-v8">
    @endunless
    <link rel="stylesheet" href="{{ asset('css/auth/login-feedback-modal.css') }}?v=20261102-buyer-parity-1">

    <div
        id="sariLoginFeedback"
        class="sari-login-feedback"
        data-sari-login-feedback
        data-sari-sound-url="{{ asset('sounds/auth/notification-info.mp3') }}"
        data-notice-type="{{ $noticeType }}"
        data-has-server-notice="{{ $noticeMessage !== '' ? '1' : '0' }}"
        data-authenticated-destination="{{ $authenticatedDestination ? '1' : '0' }}"
        data-login-page="{{ $onLoginPage ? '1' : '0' }}"
        data-registration-required="{{ $registrationRequired ? '1' : '0' }}"
        hidden
    >
        <div
            class="sari-modern-toast sari-login-feedback__toast"
            data-tone="{{ $noticeType }}"
            role="{{ $noticeType === 'error' ? 'alert' : 'status' }}"
            aria-live="{{ $noticeType === 'error' ? 'assertive' : 'polite' }}"
            aria-atomic="true"
        >
            <span class="sari-modern-toast-icon" aria-hidden="true" data-sari-login-icon>
                @if ($noticeType === 'success')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4.5 4.5L19 7"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
                @endif
            </span>
            <div class="sari-modern-toast-copy">
                <p class="sari-login-feedback__title" data-sari-login-title>{{ $noticeTitle }}</p>
                <p class="sari-login-feedback__message" data-sari-login-message>{{ $noticeMessage }}</p>
                @if ($registrationRequired)
                    <a href="{{ route('register') }}" class="sari-login-feedback__register">Register for SARI</a>
                @endif
            </div>
            <button type="button" class="sari-modern-toast-close" data-sari-modal-dismiss aria-label="Dismiss notification">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>
@endif
<script src="{{ asset('js/auth/login-feedback-modal.js') }}?v=20261102-buyer-parity-1" defer data-navigate-once></script>
