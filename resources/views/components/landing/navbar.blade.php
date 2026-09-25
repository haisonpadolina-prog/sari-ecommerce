<script>
/* SARI — LIGHT MODE ONLY */
(function () {
    const root = document.documentElement;

    root.classList.remove('dark', 'dark-mode');
    root.classList.add('light-mode');
    root.setAttribute('data-theme', 'light');
    root.style.colorScheme = 'light';

    if (document.body) {
        document.body.classList.remove('dark', 'dark-mode');
        document.body.classList.add('light-mode');
    }

    try {
        localStorage.setItem('sari-theme', 'light');
    } catch (error) {
        /* Storage may be unavailable; the page still remains light. */
    }
})();
</script>

<header class="sari-navbar" id="sariNavbar">
    <div class="sari-navbar-inner">

        {{-- MAIN BRAND ICON --}}
        <a
            href="{{ route('home') }}"
            class="sari-logo-link"
            aria-label="SARI Home"
        >
            <img
                src="{{ asset('images/sari-main-logo.png') }}"
                alt="SARI"
                class="sari-navbar-main-logo"
            >
        </a>

        {{-- DESKTOP NAVIGATION --}}
        <nav
            class="sari-navbar-nav"
            aria-label="Main navigation"
        >
            <a href="{{ route('home') }}#home" class="sari-nav-link">Home</a>
            <a href="{{ route('home') }}#discover" class="sari-nav-link">Discover</a>
            <a href="{{ route('home') }}#about" class="sari-nav-link">About</a>
            <a href="{{ route('home') }}#how-it-works" class="sari-nav-link">How It Works</a>
        </nav>

        {{-- DESKTOP ACTIONS --}}
        <div class="sari-navbar-actions">
            <button
                type="button"
                class="sari-search-button"
                id="sariSearchButton"
                aria-label="Open search"
            >
                <svg
                    class="sari-search-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="6.5"></circle>
                    <path d="M16 16L21 21"></path>
                </svg>
            </button>

            <a href="{{ route('login') }}" class="sari-login">Login</a>
            <a href="{{ route('home') }}#register" class="sari-register">Register</a>
        </div>

        {{-- MOBILE MENU BUTTON --}}
        <button
            type="button"
            class="sari-mobile-menu-button"
            id="sariMobileMenuButton"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="sariMobileMenu"
        >
            <span class="sari-menu-lines" aria-hidden="true">
                <span></span>
            </span>
        </button>

    </div>
</header>

<style>
/* Navbar-only refinements */
.sari-navbar {
    height: 86px !important;
}

.sari-navbar-inner {
    min-height: 86px !important;
}

.sari-navbar .sari-navbar-main-logo {
    display: block;
    width: 60px !important;
    height: 60px !important;
    object-fit: contain;
    border-radius: 10px;
    filter: none !important;
    -webkit-filter: none !important;
    opacity: 1 !important;
}

.sari-navbar .sari-nav-link {
    font-size: 14px !important;
    font-weight: 500 !important;
    text-transform: none !important;
    letter-spacing: .01em !important;
}

.sari-navbar .sari-login,
.sari-navbar .sari-register {
    font-size: 13.5px !important;
    text-transform: none !important;
    letter-spacing: .01em !important;
}

.sari-navbar .sari-register {
    min-height: 41px !important;
    padding-inline: 17px !important;
    border-radius: 7px !important;
}

.sari-navbar .sari-search-button,
.sari-navbar .sari-mobile-menu-button {
    border-radius: 7px !important;
}

@media (max-width: 900px) {
    .sari-navbar,
    .sari-navbar-inner {
        height: 76px !important;
        min-height: 76px !important;
    }

    .sari-navbar .sari-navbar-main-logo {
        width: 52px !important;
        height: 52px !important;
        border-radius: 7px;
    }
}

@media (max-width: 600px) {
    .sari-navbar,
    .sari-navbar-inner {
        height: 70px !important;
        min-height: 70px !important;
    }

    .sari-navbar .sari-navbar-main-logo {
        width: 46px !important;
        height: 46px !important;
        border-radius: 6px;
    }
}



/* Main icon is intentionally isolated from the generic wordmark scroll filter. */
.sari-navbar.scrolled .sari-navbar-main-logo,
.sari-navbar .sari-navbar-main-logo {
    filter: none !important;
    -webkit-filter: none !important;
    opacity: 1 !important;
}

.sari-navbar .sari-navbar-main-logo:hover,
.sari-navbar.scrolled .sari-navbar-main-logo:hover {
    filter: none !important;
    -webkit-filter: none !important;
    opacity: 1 !important;
    transform: translateY(-1px);
}

</style>
