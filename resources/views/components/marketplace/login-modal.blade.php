<div
    id="sariMarketplaceLoginModal"
    class="sari-market-auth"
    aria-hidden="true"
    data-auto-open="{{ $errors->any() && old('redirect_to') ? '1' : '0' }}"
>
    <button
        type="button"
        class="sari-market-auth__backdrop"
        data-market-auth-close
        aria-label="Close sign in dialog"
    ></button>

    <section
        class="sari-market-auth__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sariMarketplaceLoginTitle"
    >
        {{-- Brand / confidence panel --}}
        <aside class="sari-market-auth__aside">
            <div class="sari-market-auth__aside-brand">
                <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                <div>
                    <strong>SARI</strong>
                    <span>Marketplace access</span>
                </div>
            </div>

            <div class="sari-market-auth__aside-copy">
                <span class="sari-market-auth__kicker">READY WHEN YOU ARE</span>
                <h3>Sign in only when it matters.</h3>
                <p>
                    Keep browsing freely. We only ask you to sign in when you are
                    ready to continue with your purchase.
                </p>
            </div>

            <ul class="sari-market-auth__benefits">
                <li>
                    <span aria-hidden="true">01</span>
                    <div>
                        <strong>Continue your Buy Now action</strong>
                        <small>Return directly to the product you selected.</small>
                    </div>
                </li>

                <li>
                    <span aria-hidden="true">02</span>
                    <div>
                        <strong>Use your existing Buyer account</strong>
                        <small>Your normal SARI buyer session is used.</small>
                    </div>
                </li>

                <li>
                    <span aria-hidden="true">03</span>
                    <div>
                        <strong>New to SARI?</strong>
                        <small>Create an account through the existing registration flow.</small>
                    </div>
                </li>
            </ul>

            <div class="sari-market-auth__aside-footer">
                <i aria-hidden="true"></i>
                Secure buyer access
            </div>
        </aside>

        {{-- Authentication panel --}}
        <div class="sari-market-auth__main">
            <button
                type="button"
                class="sari-market-auth__close"
                data-market-auth-close
                aria-label="Close"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m7 7 10 10M17 7 7 17"></path>
                </svg>
            </button>

            <div class="sari-market-auth__mobile-brand">
                <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                <span>Buyer access</span>
            </div>

            <span class="sari-market-auth__eyebrow">BUYER CHECKOUT ACCESS</span>

            <h2 id="sariMarketplaceLoginTitle">Sign in to continue.</h2>

            <p class="sari-market-auth__lead">
                Your selected product is waiting. Sign in and SARI will continue
                from where you left off.
            </p>

            @if ($errors->any() && old('redirect_to'))
                <div class="sari-market-auth__error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v5M12 16h.01"></path>
                    </svg>

                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('marketplace.login') }}" class="sari-market-auth__form">
                @csrf

                <input
                    id="sariMarketplaceRedirectTo"
                    type="hidden"
                    name="redirect_to"
                    value="{{ old('redirect_to') }}"
                >

                <label class="sari-market-auth__field">
                    <span>Email address</span>

                    <div class="sari-market-auth__input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <rect x="4" y="6" width="16" height="12" rx="2"></rect>
                            <path d="m5 8 7 5 7-5"></path>
                        </svg>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="you@example.com"
                            required
                        >
                    </div>
                </label>

                <label class="sari-market-auth__field">
                    <span>Password</span>

                    <div class="sari-market-auth__input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>

                        <input
                            id="sariMarketplacePassword"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="sari-market-auth__password-toggle"
                            data-market-auth-password-toggle
                            aria-label="Show password"
                            aria-pressed="false"
                        >
                            <svg class="is-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M3.5 12s3-5 8.5-5 8.5 5 8.5 5-3 5-8.5 5-8.5-5-8.5-5Z"></path>
                                <circle cx="12" cy="12" r="2.3"></circle>
                            </svg>

                            <svg class="is-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="m4 4 16 16"></path>
                                <path d="M9.8 7.3A8.8 8.8 0 0 1 12 7c5.5 0 8.5 5 8.5 5a12.8 12.8 0 0 1-2.3 2.8"></path>
                                <path d="M6.2 8.2C4.4 9.6 3.5 12 3.5 12s3 5 8.5 5c.8 0 1.5-.1 2.2-.3"></path>
                            </svg>
                        </button>
                    </div>
                </label>

                <button type="submit" class="sari-market-auth__submit">
                    <span>Sign In & Continue</span>
                    <span aria-hidden="true">→</span>
                </button>
            </form>

            <div class="sari-market-auth__divider">
                <span>or create your buyer account</span>
            </div>

            <a
                href="{{ route('register', ['role' => 'buyer']) }}"
                class="sari-market-auth__register"
            >
                <span>
                    <strong>Create a Buyer Account</strong>
                    <small>Continue to the existing SARI registration form</small>
                </span>

                <b aria-hidden="true">↗</b>
            </a>

            <p class="sari-market-auth__note">
                New Buyer accounts continue through SARI's existing verification
                and approval process.
            </p>
        </div>
    </section>
</div>

<style>
/* ==========================================================
   SARI MARKETPLACE AUTH MODAL
   Modern split dialog + compact mobile bottom sheet
   ========================================================== */

.sari-market-auth,
.sari-market-auth * {
    box-sizing: border-box;
}

.sari-market-auth {
    position: fixed;
    z-index: 12000;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    font-family: 'Poppins', sans-serif;
    transition:
        opacity .24s ease,
        visibility .24s ease;
}

.sari-market-auth.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.sari-market-auth__backdrop {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
    background: rgba(21, 18, 14, .52);
    -webkit-backdrop-filter: blur(5px);
    backdrop-filter: blur(5px);
    cursor: default;
}

.sari-market-auth__dialog {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(250px, .78fr) minmax(340px, 1.22fr);
    width: min(100%, 790px);
    max-height: calc(100dvh - 36px);
    overflow: hidden;
    border: 1px solid rgba(232, 224, 213, .95);
    border-radius: 24px;
    background: #ffffff;
    box-shadow: 0 34px 100px rgba(31, 25, 17, .24);
    transform: translateY(18px) scale(.985);
    transition: transform .32s cubic-bezier(.22, 1, .36, 1);
}

.sari-market-auth.is-open .sari-market-auth__dialog {
    transform: translateY(0) scale(1);
}

/* ---------- ASIDE ---------- */

.sari-market-auth__aside {
    display: flex;
    min-width: 0;
    flex-direction: column;
    padding: 30px 27px 24px;
    color: #2b251e;
    background:
        linear-gradient(rgba(255,255,255,.12), rgba(255,255,255,.12)),
        #f3ecdf;
    border-right: 1px solid #e5dac8;
}

.sari-market-auth__aside-brand {
    display: flex;
    align-items: center;
    gap: 11px;
}

.sari-market-auth__aside-brand img {
    display: block;
    width: 43px;
    height: 43px;
    object-fit: contain;
    border-radius: 8px;
}

.sari-market-auth__aside-brand strong,
.sari-market-auth__aside-brand span {
    display: block;
}

.sari-market-auth__aside-brand strong {
    color: #252019;
    font-size: 13px;
    letter-spacing: .12em;
}

.sari-market-auth__aside-brand span {
    margin-top: 2px;
    color: #8e806e;
    font-size: 7.5px;
    letter-spacing: .07em;
    text-transform: uppercase;
}

.sari-market-auth__aside-copy {
    margin-top: 52px;
}

.sari-market-auth__kicker,
.sari-market-auth__eyebrow {
    color: #a56f12;
    font-size: 8px;
    font-weight: 800;
    letter-spacing: .15em;
    text-transform: uppercase;
}

.sari-market-auth__aside-copy h3 {
    margin: 10px 0 0;
    color: #211c17;
    font-size: 26px;
    line-height: 1.05;
    letter-spacing: -.045em;
}

.sari-market-auth__aside-copy p {
    margin: 12px 0 0;
    color: #73695c;
    font-size: 10px;
    line-height: 1.65;
}

.sari-market-auth__benefits {
    display: grid;
    gap: 13px;
    margin: 25px 0 0;
    padding: 0;
    list-style: none;
}

.sari-market-auth__benefits li {
    display: grid;
    grid-template-columns: 27px minmax(0, 1fr);
    gap: 10px;
    align-items: start;
}

.sari-market-auth__benefits li > span {
    display: grid;
    width: 27px;
    height: 27px;
    place-items: center;
    border: 1px solid #d8c4a2;
    border-radius: 8px;
    color: #a56f12;
    background: rgba(255, 255, 255, .5);
    font-size: 7px;
    font-weight: 800;
}

.sari-market-auth__benefits strong,
.sari-market-auth__benefits small {
    display: block;
}

.sari-market-auth__benefits strong {
    color: #3a3128;
    font-size: 9px;
    line-height: 1.35;
}

.sari-market-auth__benefits small {
    margin-top: 3px;
    color: #817566;
    font-size: 7.5px;
    line-height: 1.45;
}

.sari-market-auth__aside-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: auto;
    padding-top: 28px;
    color: #827565;
    font-size: 7.5px;
}

.sari-market-auth__aside-footer i {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #61856a;
    box-shadow: 0 0 0 4px rgba(97, 133, 106, .10);
}

/* ---------- MAIN ---------- */

.sari-market-auth__main {
    position: relative;
    overflow-y: auto;
    padding: 42px 38px 30px;
}

.sari-market-auth__close {
    position: absolute;
    z-index: 2;
    top: 15px;
    right: 15px;
    display: grid;
    width: 34px;
    height: 34px;
    place-items: center;
    border: 1px solid #ebe4db;
    border-radius: 9px;
    color: #71695f;
    background: #ffffff;
    cursor: pointer;
    transition:
        transform .2s ease,
        background-color .2s ease,
        border-color .2s ease;
}

.sari-market-auth__close svg {
    width: 15px;
    height: 15px;
}

.sari-market-auth__mobile-brand {
    display: none;
}

.sari-market-auth__main h2 {
    margin: 10px 0 0;
    color: #211c17;
    font-size: 31px;
    line-height: 1.08;
    letter-spacing: -.045em;
}

.sari-market-auth__lead {
    max-width: 430px;
    margin: 10px 0 0;
    color: #7d746a;
    font-size: 10.5px;
    line-height: 1.65;
}

.sari-market-auth__error {
    display: grid;
    grid-template-columns: 18px minmax(0, 1fr);
    gap: 8px;
    align-items: start;
    margin-top: 16px;
    border: 1px solid #efcccc;
    border-radius: 10px;
    padding: 10px 11px;
    color: #a35454;
    background: #fff7f7;
    font-size: 9.5px;
    line-height: 1.5;
}

.sari-market-auth__error svg {
    width: 17px;
    height: 17px;
}

.sari-market-auth__form {
    display: grid;
    gap: 13px;
    margin-top: 21px;
}

.sari-market-auth__field > span {
    display: block;
    margin-bottom: 6px;
    color: #433b33;
    font-size: 9.5px;
    font-weight: 650;
}

.sari-market-auth__input-wrap {
    position: relative;
}

.sari-market-auth__input-wrap > svg {
    position: absolute;
    z-index: 1;
    top: 50%;
    left: 13px;
    width: 16px;
    height: 16px;
    color: #988e84;
    pointer-events: none;
    transform: translateY(-50%);
}

.sari-market-auth__field input {
    width: 100%;
    height: 46px;
    border: 1px solid #ddd5cb;
    border-radius: 10px;
    outline: none;
    padding: 0 42px 0 40px;
    color: #28221c;
    background: #ffffff;
    font: inherit;
    font-size: 11px;
    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

.sari-market-auth__field input:focus {
    border-color: #c98a18;
    box-shadow: 0 0 0 3px rgba(201, 138, 24, .09);
}

.sari-market-auth__password-toggle {
    position: absolute;
    z-index: 2;
    top: 50%;
    right: 8px;
    display: grid;
    width: 32px;
    height: 32px;
    place-items: center;
    border: 0;
    border-radius: 8px;
    color: #8b8278;
    background: transparent;
    cursor: pointer;
    transform: translateY(-50%);
}

.sari-market-auth__password-toggle svg {
    width: 16px;
    height: 16px;
}

.sari-market-auth__password-toggle .is-hide {
    display: none;
}

.sari-market-auth__password-toggle.is-visible .is-show {
    display: none;
}

.sari-market-auth__password-toggle.is-visible .is-hide {
    display: block;
}

.sari-market-auth__submit {
    display: inline-flex;
    width: 100%;
    min-height: 48px;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 2px;
    border: 1px solid #c98a18;
    border-radius: 10px;
    padding: 0 16px;
    color: #ffffff;
    background: #c98a18;
    font: inherit;
    font-size: 10.5px;
    font-weight: 750;
    cursor: pointer;
    transition:
        transform .22s cubic-bezier(.22, 1, .36, 1),
        border-color .2s ease,
        background-color .2s ease,
        box-shadow .22s ease;
}

.sari-market-auth__divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 19px 0 13px;
    color: #9a9187;
    font-size: 7.5px;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.sari-market-auth__divider::before,
.sari-market-auth__divider::after {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee7de;
}

.sari-market-auth__register {
    display: flex;
    width: 100%;
    min-height: 58px;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    border: 1px solid #d9d0c5;
    border-radius: 11px;
    padding: 10px 13px;
    color: #41382e;
    background: #ffffff;
    text-decoration: none;
    transition:
        transform .22s cubic-bezier(.22, 1, .36, 1),
        border-color .2s ease,
        background-color .2s ease;
}

.sari-market-auth__register strong,
.sari-market-auth__register small {
    display: block;
}

.sari-market-auth__register strong {
    color: #362f28;
    font-size: 9.5px;
}

.sari-market-auth__register small {
    margin-top: 3px;
    color: #938a80;
    font-size: 7.5px;
    line-height: 1.4;
}

.sari-market-auth__register b {
    color: #b77c15;
    font-size: 15px;
}

.sari-market-auth__note {
    margin: 11px 0 0;
    color: #9a9187;
    font-size: 7.5px;
    line-height: 1.5;
    text-align: center;
}

body.sari-market-auth-open {
    overflow: hidden;
}

/* ---------- HOVER / FOCUS ---------- */

.sari-market-auth__close:focus-visible,
.sari-market-auth__password-toggle:focus-visible,
.sari-market-auth__submit:focus-visible,
.sari-market-auth__register:focus-visible,
.sari-market-auth__field input:focus-visible {
    outline: 2px solid #c98a18;
    outline-offset: 3px;
}

@media (hover: hover) and (pointer: fine) {
    .sari-market-auth__close:hover {
        transform: rotate(4deg);
        border-color: #d8cdbf;
        background: #faf8f4;
    }

    .sari-market-auth__password-toggle:hover {
        background: #f6f2ec;
    }

    .sari-market-auth__submit:hover {
        transform: translateY(-1px);
        border-color: #ad7311;
        background: #ad7311;
        box-shadow: 0 10px 24px rgba(173, 115, 17, .17);
    }

    .sari-market-auth__register:hover {
        transform: translateY(-1px);
        border-color: #cbbb9f;
        background: #fcfaf7;
    }
}

/* ---------- MOBILE BOTTOM SHEET ---------- */

@media (max-width: 650px) {
    .sari-market-auth {
        align-items: flex-end;
        padding: 8px;
    }

    .sari-market-auth__dialog {
        grid-template-columns: 1fr;
        width: 100%;
        max-height: calc(100dvh - 16px);
        border-radius: 21px 21px 15px 15px;
        transform: translateY(26px);
    }

    .sari-market-auth.is-open .sari-market-auth__dialog {
        transform: translateY(0);
    }

    .sari-market-auth__aside {
        display: none;
    }

    .sari-market-auth__main {
        padding: 24px 17px 18px;
    }

    .sari-market-auth__close {
        top: 13px;
        right: 13px;
        width: 32px;
        height: 32px;
    }

    .sari-market-auth__mobile-brand {
        display: flex;
        align-items: center;
        gap: 9px;
        padding-right: 43px;
        margin-bottom: 25px;
    }

    .sari-market-auth__mobile-brand img {
        display: block;
        width: 39px;
        height: 39px;
        object-fit: contain;
        border-radius: 7px;
    }

    .sari-market-auth__mobile-brand span {
        color: #9b6b17;
        font-size: 7.5px;
        font-weight: 750;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .sari-market-auth__main h2 {
        margin-top: 8px;
        font-size: 26px;
    }

    .sari-market-auth__lead {
        font-size: 10px;
    }

    .sari-market-auth__field input {
        height: 45px;
    }

    .sari-market-auth__submit {
        min-height: 47px;
    }

    .sari-market-auth__register {
        min-height: 55px;
    }
}

/* ---------- REDUCED MOTION ---------- */

@media (prefers-reduced-motion: reduce) {
    .sari-market-auth,
    .sari-market-auth__dialog,
    .sari-market-auth__close,
    .sari-market-auth__field input,
    .sari-market-auth__submit,
    .sari-market-auth__register {
        transition: none !important;
    }
}
</style>

<script>
(function () {
    const modal = document.getElementById('sariMarketplaceLoginModal');
    const redirectInput = document.getElementById('sariMarketplaceRedirectTo');
    const passwordInput = document.getElementById('sariMarketplacePassword');
    const passwordToggle = modal?.querySelector('[data-market-auth-password-toggle]');

    if (!modal || !redirectInput) {
        return;
    }

    let lastTrigger = null;

    function focusableElements() {
        return Array.from(
            modal.querySelectorAll(
                'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            )
        ).filter(function (element) {
            return element.offsetParent !== null;
        });
    }

    function openModal(trigger) {
        lastTrigger = trigger || document.activeElement || null;

        if (trigger?.dataset?.redirectTo) {
            redirectInput.value = trigger.dataset.redirectTo;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('sari-market-auth-open');

        window.setTimeout(function () {
            modal.querySelector('input[name="email"]')?.focus();
        }, 40);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('sari-market-auth-open');

        window.setTimeout(function () {
            lastTrigger?.focus?.();
        }, 20);
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-market-auth-trigger]');

        if (trigger) {
            event.preventDefault();
            openModal(trigger);
            return;
        }

        if (event.target.closest('[data-market-auth-close]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusable = focusableElements();

        if (!focusable.length) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    passwordToggle?.addEventListener('click', function () {
        if (!passwordInput) {
            return;
        }

        const showing = passwordInput.type === 'text';

        passwordInput.type = showing ? 'password' : 'text';
        passwordToggle.classList.toggle('is-visible', !showing);
        passwordToggle.setAttribute('aria-pressed', showing ? 'false' : 'true');
        passwordToggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });

    if (modal.dataset.autoOpen === '1') {
        openModal(null);
    }
})();
</script>
