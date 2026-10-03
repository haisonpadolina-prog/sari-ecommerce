<div
    id="sariMarketplaceLoginModal"
    class="sari-auth-modal"
    aria-hidden="true"
    data-auto-open="{{ $errors->any() && old('redirect_to') ? '1' : '0' }}"
>
    <button
        type="button"
        class="sari-auth-modal__backdrop"
        data-market-auth-close
        aria-label="Close sign in dialog"
    ></button>

    <section
        class="sari-auth-modal__panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sariMarketplaceLoginTitle"
    >
        <aside class="sari-auth-modal__context">
            <div>
                <strong class="sari-auth-modal__brand">SARI</strong>
                <span class="sari-auth-modal__brand-sub">Marketplace</span>
            </div>

            <div class="sari-auth-modal__context-copy">
                <span>Secure buyer access</span>
                <h3>Your purchase is waiting.</h3>
                <p>
                    Sign in only when you're ready to continue.
                    Your marketplace browsing stays open to everyone.
                </p>
            </div>

            <ul>
                <li>
                    <span>✓</span>
                    Buyer protection on every order
                </li>
                <li>
                    <span>✓</span>
                    Approved SARI sellers
                </li>
                <li>
                    <span>✓</span>
                    Continue your original Buy Now action
                </li>
            </ul>
        </aside>

        <div class="sari-auth-modal__main">
            <button
                type="button"
                class="sari-auth-modal__close"
                data-market-auth-close
                aria-label="Close"
            >
                ×
            </button>

            <span class="sari-auth-modal__eyebrow">Buyer access</span>

            <h2 id="sariMarketplaceLoginTitle">Sign in to continue</h2>

            <p class="sari-auth-modal__lead">
                Sign in to complete your purchase securely.
            </p>

            @if ($errors->any() && old('redirect_to'))
                <div class="sari-auth-modal__error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('marketplace.login') }}"
                class="sari-auth-modal__form"
            >
                @csrf

                <input
                    id="sariMarketplaceRedirectTo"
                    type="hidden"
                    name="redirect_to"
                    value="{{ old('redirect_to') }}"
                >

                <label>
                    <span>Email</span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="you@example.com"
                        required
                    >
                </label>

                <label>
                    <span>Password</span>

                    <div class="sari-auth-modal__password">
                        <input
                            id="sariMarketplacePassword"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        >

                        <button
                            type="button"
                            data-market-auth-password-toggle
                            aria-label="Show password"
                            aria-pressed="false"
                        >
                            Show
                        </button>
                    </div>
                </label>

                <button type="submit" class="sari-auth-modal__submit">
                    Sign In &amp; Continue
                </button>
            </form>

            <div class="sari-auth-modal__divider">
                <span>New to SARI?</span>
            </div>

            <a
                href="{{ route('register', ['role' => 'buyer']) }}"
                class="sari-auth-modal__register"
            >
                Create a Buyer Account
            </a>
        </div>
    </section>
</div>

<style>
.sari-auth-modal,
.sari-auth-modal * {
    box-sizing: border-box;
}

.sari-auth-modal {
    position: fixed;
    z-index: 14000;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 22px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    font-family: 'Poppins', Arial, sans-serif;
    transition: opacity .22s ease, visibility .22s ease;
}

.sari-auth-modal.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.sari-auth-modal__backdrop {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
    background: rgba(35, 30, 25, .52);
    -webkit-backdrop-filter: blur(4px);
    backdrop-filter: blur(4px);
}

.sari-auth-modal__panel {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: .9fr 1.1fr;
    width: min(100%, 760px);
    max-height: calc(100dvh - 34px);
    overflow: hidden;
    border: 1px solid #e6dfd6;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 30px 90px rgba(38, 31, 24, .22);
    transform: translateY(24px);
    transition: transform .32s cubic-bezier(.22, 1, .36, 1);
}

.sari-auth-modal.is-open .sari-auth-modal__panel {
    transform: translateY(0);
}

.sari-auth-modal__context {
    display: flex;
    flex-direction: column;
    padding: 31px 28px;
    border-right: 1px solid #e5ddd2;
    background: #f2eee7;
}

.sari-auth-modal__brand {
    display: block;
    color: #29241f;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 24px;
    letter-spacing: -.03em;
}

.sari-auth-modal__brand-sub {
    display: block;
    margin-top: 2px;
    color: #b17a1b;
    font-size: 7px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
}

.sari-auth-modal__context-copy {
    margin-top: 56px;
}

.sari-auth-modal__context-copy > span,
.sari-auth-modal__eyebrow {
    color: #ac7619;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
}

.sari-auth-modal__context-copy h3 {
    margin: 10px 0 0;
    color: #28231e;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 27px;
    font-weight: 600;
    line-height: 1.08;
    letter-spacing: -.035em;
}

.sari-auth-modal__context-copy p {
    margin: 12px 0 0;
    color: #766e65;
    font-size: 10px;
    line-height: 1.65;
}

.sari-auth-modal__context ul {
    display: grid;
    gap: 10px;
    margin: auto 0 0;
    padding: 28px 0 0;
    list-style: none;
}

.sari-auth-modal__context li {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #696158;
    font-size: 8.5px;
}

.sari-auth-modal__context li > span {
    color: #ad7619;
    font-weight: 800;
}

.sari-auth-modal__main {
    position: relative;
    overflow-y: auto;
    padding: 42px 36px 30px;
}

.sari-auth-modal__close {
    position: absolute;
    top: 14px;
    right: 14px;
    display: grid;
    width: 32px;
    height: 32px;
    place-items: center;
    border: 1px solid #e7e0d8;
    border-radius: 8px;
    color: #6f675f;
    background: #fff;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
}

.sari-auth-modal__main h2 {
    margin: 9px 0 0;
    color: #28231e;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 29px;
    font-weight: 600;
    letter-spacing: -.04em;
}

.sari-auth-modal__lead {
    margin: 8px 0 0;
    color: #837b73;
    font-size: 10px;
    line-height: 1.6;
}

.sari-auth-modal__error {
    margin-top: 15px;
    border: 1px solid #ebcaca;
    border-radius: 8px;
    padding: 10px;
    color: #9c4f4f;
    background: #fff7f7;
    font-size: 9px;
}

.sari-auth-modal__form {
    display: grid;
    gap: 14px;
    margin-top: 22px;
}

.sari-auth-modal__form label > span {
    display: block;
    margin-bottom: 6px;
    color: #49423b;
    font-size: 9px;
    font-weight: 600;
}

.sari-auth-modal__form input {
    width: 100%;
    height: 44px;
    border: 1px solid #ddd6cd;
    border-radius: 8px;
    outline: 0;
    padding: 0 12px;
    color: #2e2924;
    background: #fff;
    font: inherit;
    font-size: 10px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sari-auth-modal__form input:focus {
    border-color: rgba(197, 138, 38, .75);
    box-shadow: 0 0 0 3px rgba(197, 138, 38, .09);
}

.sari-auth-modal__password {
    position: relative;
}

.sari-auth-modal__password input {
    padding-right: 56px;
}

.sari-auth-modal__password button {
    position: absolute;
    top: 50%;
    right: 8px;
    border: 0;
    color: #837b73;
    background: transparent;
    font: inherit;
    font-size: 8px;
    font-weight: 600;
    cursor: pointer;
    transform: translateY(-50%);
}

.sari-auth-modal__submit,
.sari-auth-modal__register {
    display: inline-flex;
    width: 100%;
    min-height: 45px;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font: inherit;
    font-size: 9.5px;
    font-weight: 700;
    text-decoration: none;
}

.sari-auth-modal__submit {
    border: 1px solid #2d2823;
    color: #fff;
    background: #2d2823;
    cursor: pointer;
}

.sari-auth-modal__divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0 12px;
    color: #958d85;
    font-size: 8px;
}

.sari-auth-modal__divider::before,
.sari-auth-modal__divider::after {
    content: "";
    flex: 1;
    height: 1px;
    background: #ebe5de;
}

.sari-auth-modal__register {
    border: 1px solid rgba(197, 138, 38, .48);
    color: #3e372f;
    background: #f6ecd9;
}

body.sari-auth-modal-open {
    overflow: hidden;
}

@media (hover: hover) and (pointer: fine) {
    .sari-auth-modal__submit:hover {
        background: #bd8420;
        border-color: #bd8420;
    }

    .sari-auth-modal__register:hover {
        background: #f1e3ca;
    }
}

@media (max-width: 650px) {
    .sari-auth-modal {
        align-items: flex-end;
        padding: 7px;
    }

    .sari-auth-modal__panel {
        grid-template-columns: 1fr;
        width: 100%;
        border-radius: 18px 18px 12px 12px;
        transform: translateY(34px);
    }

    .sari-auth-modal__context {
        display: none;
    }

    .sari-auth-modal__main {
        padding: 30px 17px 18px;
    }

    .sari-auth-modal__main h2 {
        font-size: 26px;
    }

    .sari-auth-modal__form input {
        height: 44px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-auth-modal,
    .sari-auth-modal__panel {
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

    let lastFocusedElement = null;

    function getFocusableElements() {
        return Array.from(
            modal.querySelectorAll(
                'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            )
        ).filter(function (element) {
            return element.offsetParent !== null;
        });
    }

    function openModal(trigger) {
        lastFocusedElement = trigger || document.activeElement || null;

        if (trigger?.dataset?.redirectTo) {
            redirectInput.value = trigger.dataset.redirectTo;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('sari-auth-modal-open');

        window.setTimeout(function () {
            modal.querySelector('input[name="email"]')?.focus();
        }, 30);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('sari-auth-modal-open');

        window.setTimeout(function () {
            lastFocusedElement?.focus?.();
        }, 20);
    }

    document.addEventListener('click', function (event) {
        const authTrigger = event.target.closest('[data-market-auth-trigger]');

        if (authTrigger) {
            event.preventDefault();
            openModal(authTrigger);
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

        const elements = getFocusableElements();

        if (!elements.length) {
            return;
        }

        const first = elements[0];
        const last = elements[elements.length - 1];

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

        const currentlyVisible = passwordInput.type === 'text';

        passwordInput.type = currentlyVisible ? 'password' : 'text';
        passwordToggle.textContent = currentlyVisible ? 'Show' : 'Hide';
        passwordToggle.setAttribute(
            'aria-label',
            currentlyVisible ? 'Show password' : 'Hide password'
        );
        passwordToggle.setAttribute(
            'aria-pressed',
            currentlyVisible ? 'false' : 'true'
        );
    });

    if (modal.dataset.autoOpen === '1') {
        openModal(null);
    }
})();
</script>
