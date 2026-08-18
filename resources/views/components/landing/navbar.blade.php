<header class="sari-navbar" id="sariNavbar">

    <div class="sari-navbar-inner">

        {{-- =====================================================
             LOGO
        ====================================================== --}}
        <a
            href="{{ route('home') }}"
            class="sari-logo-link"
            aria-label="SARI Home"
        >
            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="sari-navbar-logo"
            >
        </a>


        {{-- =====================================================
             DESKTOP NAVIGATION
        ====================================================== --}}
        <nav
            class="sari-navbar-nav"
            aria-label="Main navigation"
        >

            <a href="{{ route('home') }}#home" class="sari-nav-link">
                Home
            </a>

            <a href="{{ route('home') }}#discover" class="sari-nav-link">
                Discover
            </a>

            <a href="{{ route('home') }}#about" class="sari-nav-link">
                About
            </a>

            <a href="{{ route('home') }}#how-it-works" class="sari-nav-link">
                How It Works
            </a>

        </nav>


        {{-- =====================================================
             DESKTOP ACTIONS
        ====================================================== --}}
        <div class="sari-navbar-actions">

            {{-- SEARCH --}}
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
                    <circle
                        cx="11"
                        cy="11"
                        r="6.5"
                    ></circle>

                    <path
                        d="M16 16L21 21"
                    ></path>
                </svg>
            </button>


            {{-- LOGIN --}}
            <a
                href="{{ route('login') }}"
                class="sari-login"
            >
                Login
            </a>


            {{-- REGISTER --}}
            <a
                href="{{ route('home') }}#register"
                class="sari-register"
            >
                Register
            </a>

        </div>


        {{-- =====================================================
             MOBILE MENU BUTTON
        ====================================================== --}}
        <button
            type="button"
            class="sari-mobile-menu-button"
            id="sariMobileMenuButton"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="sariMobileMenu"
        >
            <span class="sari-menu-lines">
                <span></span>
            </span>
        </button>

    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const navbar = document.getElementById('sariNavbar');

    if (!navbar) return;

    function updateNavbar() {
        navbar.classList.toggle(
            'scrolled',
            window.scrollY > 40
        );
    }

    updateNavbar();

    window.addEventListener('scroll', updateNavbar, {
        passive: true
    });

});
</script>
</header>