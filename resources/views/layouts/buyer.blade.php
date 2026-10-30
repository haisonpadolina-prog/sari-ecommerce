<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'SARI Buyer')</title>

    {{-- ============================================================
         BUYER SIDEBAR PRE-PAINT STATE
         New V2 storage key avoids the stale/broken old collapse state.
    ============================================================ --}}
    <script>
        (function () {
            try {
                const OLD_KEY = 'sari:buyer-sidebar-collapsed';
                const NEW_KEY = 'sari:buyer-sidebar-mini-v2';

                /*
                 * Remove the old class/state that caused labels to disappear
                 * without the width/logo syncing correctly.
                 */
                document.documentElement.classList.remove(
                    'buyer-sidebar-collapsed'
                );

                window.localStorage.removeItem(OLD_KEY);

                const desktop =
                    window.matchMedia('(min-width: 1024px)').matches;

                const mini =
                    desktop &&
                    window.localStorage.getItem(NEW_KEY) === '1';

                document.documentElement.dataset.buyerSidebarMini =
                    mini ? '1' : '0';

                document.documentElement.style.setProperty(
                    '--buyer-sidebar-width',
                    mini ? '76px' : '238px'
                );
            } catch (_) {
                document.documentElement.dataset.buyerSidebarMini = '0';

                document.documentElement.style.setProperty(
                    '--buyer-sidebar-width',
                    '238px'
                );
            }
        })();
    </script>

    {{-- ============================================================
         GOOGLE FONT
    ============================================================ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- ============================================================
         IMPORTANT PAGE PRELOADS
    ============================================================ --}}
    @stack('preloads')

    {{-- ============================================================
         BUYER VITE ASSETS
    ============================================================ --}}
    @vite([
        'resources/css/app.css',
        'resources/js/buyer.js',
    ])

    {{-- ============================================================
         LIVEWIRE
         Required for @persist + wire:navigate.
    ============================================================ --}}
    @livewireStyles

    {{-- ============================================================
         PAGE-SPECIFIC STYLES
    ============================================================ --}}
    @stack('styles')

    {{-- BUYER FIRST-PAINT CANVAS — matches the Seller shell background. --}}
    <style id="sariBuyerFirstPaintCanvas">
        :root,
        html,
        body,
        #buyerMainContent {
            min-height: 100%;
            background: #F4F5F7 !important;
            background-color: #F4F5F7 !important;
            background-image: none !important;
        }

        body {
            margin: 0;
        }

        #buyerMainContent > main {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
        }
    </style>

    <style>
        :root {
            --buyer-sidebar-width: 238px;
            --buyer-sidebar-expanded: 238px;
            --buyer-sidebar-mini: 76px;
            --buyer-sidebar-ease: cubic-bezier(.22, 1, .36, 1);
        }

        /*
        |--------------------------------------------------------------------------
        | BUYER DESKTOP SIDEBAR — ONE SOURCE OF TRUTH
        |--------------------------------------------------------------------------
        | Width, labels, icon centering, and logo state all follow the same
        | html[data-buyer-sidebar-mini] value. This prevents the "wide sidebar
        | but missing words" bug.
        */
        @media (min-width: 1024px) {
            #buyerSidebar {
                width: var(--buyer-sidebar-width) !important;
                overflow-x: hidden;

                transition:
                    width 220ms var(--buyer-sidebar-ease);
            }

            #buyerMainContent {
                padding-left: var(--buyer-sidebar-width);

                transition:
                    padding-left 220ms var(--buyer-sidebar-ease);
            }

            #buyerSidebarLogoFull,
            #buyerSidebarLogoCompact {
                transition:
                    opacity 140ms ease,
                    visibility 140ms ease;
            }

            /*
             * EXPANDED
             */
            html[data-buyer-sidebar-mini="0"] #buyerSidebarLogoFull {
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            html[data-buyer-sidebar-mini="0"] #buyerSidebarLogoCompact {
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }

            html[data-buyer-sidebar-mini="0"] #buyerSidebar .buyer-sidebar-label {
                display: inline !important;
            }

            html[data-buyer-sidebar-mini="0"] #buyerSidebar .buyer-sidebar-extra {
                /*
                 * Do not force badges visible — their own JS decides whether
                 * they should be hidden/grid.
                 */
            }

            /*
             * MINI / ICON-ONLY
             */
            html[data-buyer-sidebar-mini="1"] #buyerSidebarLogoStage {
                width: 58px !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebarLogoFull {
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebarLogoCompact {
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebar .buyer-sidebar-label,
            html[data-buyer-sidebar-mini="1"] #buyerSidebar .buyer-sidebar-extra {
                display: none !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebarNav {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebar [data-buyer-sidebar-item] {
                justify-content: center !important;
                gap: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebar [data-buyer-sidebar-inner] {
                justify-content: center !important;
                gap: 0 !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebarProfile {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            html[data-buyer-sidebar-mini="1"] #buyerSidebarProfile form,
            html[data-buyer-sidebar-mini="1"] #buyerSidebarProfile button {
                width: 100% !important;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */
        @media (max-width: 1023px) {
            #buyerSidebar {
                width: 238px !important;
            }

            #buyerMainContent {
                padding-left: 0 !important;
            }

            #buyerSidebarLogoFull {
                opacity: 1 !important;
                visibility: visible !important;
            }

            #buyerSidebarLogoCompact {
                opacity: 0 !important;
                visibility: hidden !important;
            }

            #buyerSidebar .buyer-sidebar-label {
                display: inline !important;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STABLE PERSISTENT SHELL
        |--------------------------------------------------------------------------
        */
        #buyerSidebar {
            contain: layout paint;
        }

        #buyerSidebarLogo img {
            display: block;
            animation: none !important;
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVE STATE FOR PERSISTED SIDEBAR
        |--------------------------------------------------------------------------
        */
        #buyerSidebar [data-buyer-sidebar-item][data-buyer-nav-active="true"] {
            background-color: #d9930a !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px rgba(217, 147, 10, .18) !important;
            font-weight: 600 !important;
        }

        #buyerSidebar [data-buyer-sidebar-item][data-buyer-nav-active="false"] {
            background-color: transparent !important;
            color: #514b42 !important;
            box-shadow: none !important;
            font-weight: 500 !important;
        }

        #buyerSidebar [data-buyer-sidebar-item][data-buyer-nav-active="false"]:hover {
            background-color: #f9f1e3 !important;
            color: #a96e05 !important;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER BURGER
        |--------------------------------------------------------------------------
        */
        #buyerSidebarToggle,
        #buyerMobileMenuButton {
            transition:
                background-color 150ms ease,
                border-color 150ms ease,
                color 150ms ease,
                transform 120ms ease;
        }

        #buyerSidebarToggle:active,
        #buyerMobileMenuButton:active {
            transform: scale(.96);
        }

        @media (prefers-reduced-motion: reduce) {
            #buyerSidebar,
            #buyerMainContent,
            #buyerSidebar *,
            #buyerSidebarToggle,
            #buyerMobileMenuButton {
                transition-duration: 1ms !important;
                animation-duration: 1ms !important;
            }
        }
    </style>

    {{-- Buyer shell visual layer: UI-only, modeled after the Seller shell. --}}
    <link rel="stylesheet" href="{{ asset('css/buyer/shell.css') }}?v=20261008-3">
    {{-- Compact Buyer toast design. No Vite manifest dependency. --}}
    <link rel="stylesheet" href="{{ asset('css/buyer/sari-toast.css') }}?v=20261029-buyer-instant-sound-v4">
    {{-- Load the notification engine during document parsing; defer executes after HTML is ready. --}}
    <script defer src="{{ asset('js/buyer/sari-toast.js') }}?v=20261029-buyer-instant-sound-v4" data-sounds-base="{{ asset('sounds/buyer') }}"></script>
</head>

<body
    data-buyer-products-url="{{ route('buyer.products') }}"
    data-buyer-cart-store-url="{{ route('buyer.cart.items.store') }}"
    data-buyer-cart-summary-url="{{ route('buyer.cart.summary') }}"
    data-buyer-notification-summary-url="{{ route('buyer.notifications.summary') }}"
    data-buyer-cart-url="{{ route('buyer.cart') }}"
    class="
        m-0
        min-h-screen
        overflow-x-hidden
        bg-[#F4F5F7]
        font-['Poppins']
        text-[#1f1b16]
        antialiased
    "
>

    <div class="min-h-screen">

        {{-- ========================================================
             PERSISTENT BUYER SIDEBAR
             Logo/sidebar DOM is NOT recreated on Buyer navigation.
        ========================================================= --}}
        @persist('buyer-sidebar')
            @include('components.buyer.sidebar')
        @endpersist


        {{-- ========================================================
             MAIN BUYER CONTENT
             Only this portion changes between Buyer pages.
        ========================================================= --}}
        <div
            id="buyerMainContent"
            class="min-h-screen"
        >
            {{-- Flash messages are rendered as nonblocking toasts; Blade escapes all text. --}}
            <div
                id="sariBuyerToastFlash"
                hidden
                data-success="{{ (string) session('success', '') }}"
                data-error="{{ (string) (session('error') ?: ($errors->first() ?? '')) }}"
                data-cancel-success="{{ (string) session('cancel_success', '') }}"
                data-info="{{ (string) session('info', '') }}"
            ></div>
            @if(session('is_buyer'))
                {{--
                    Platform announcements render as a fixed overlay and must not
                    reserve vertical space above the shared Buyer header.
                --}}
                @include('components.platform-announcements', [
                    'audienceRole' => 'buyer',
                    'identityType' => session('buyer_account_id') ? 'buyer' : 'social_buyer',
                    'accountId' => (int) (session('buyer_account_id') ?: session('buyer_social_account_id', 0)),
                ])
            @endif
            @yield('content')
        </div>


        {{-- ========================================================
             PERSISTENT MOBILE OVERLAY
        ========================================================= --}}
        @persist('buyer-sidebar-overlay')
            <div
                id="buyerSidebarOverlay"
                class="
                    fixed
                    inset-0
                    z-40

                    hidden

                    bg-black/30

                    lg:hidden
                "
                aria-hidden="true"
            ></div>
        @endpersist

    </div>


    {{-- Always show the Buyer AI launcher for signed-in Buyers.
         Missing backend route must not hide the floating robot or cause a 500. --}}
    @if(session('is_buyer'))
        @persist('buyer-ai-assistant')
            @if(view()->exists('buyer.partials.ai-assistant'))
                @include('buyer.partials.ai-assistant')
            @else
                {{-- Deployment-safe fallback: visible launcher, honest setup status. --}}
                <details id="sariBuyerAiInstallationNotice" style="position:fixed;right:24px;bottom:22px;z-index:8500;font-family:Poppins,system-ui,sans-serif">
                    <summary aria-label="Open SARI Buyer AI status" style="display:block;list-style:none;cursor:pointer;width:76px;height:76px">
                        <img src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                             alt="SARI AI" style="display:block;width:76px;height:76px;object-fit:contain" draggable="false">
                    </summary>
                    <div role="status" style="position:absolute;bottom:88px;right:0;width:min(320px,calc(100vw - 40px));border:1px solid #e9dfcf;border-radius:16px;padding:16px;background:white;box-shadow:0 12px 42px rgba(0,0,0,.15);font-size:12px;line-height:1.6;color:#302b24">
                        <strong>SARI Buyer AI needs setup</strong>
                        <p style="margin:6px 0 0">The chatbot view <code>buyer.partials.ai-assistant</code> is missing. Install the Buyer AI partial to enable chatting.</p>
                    </div>
                </details>
            @endif
        @endpersist
    @endif


    {{-- ============================================================
         BUYER LOGOUT MODAL — SELLER DESIGN, BRIGHT RED
         Keeps the existing buyerLogoutForm, POST URL, and CSRF flow.
    ============================================================ --}}
    @persist('buyer-logout-modal')
        <div id="buyerLogoutModal" class="hidden" data-buyer-logout-root>
            <div class="buyer-logout-modal-backdrop" data-buyer-logout-backdrop aria-hidden="true"></div>

            <section
                class="buyer-logout-modal-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="buyerLogoutModalTitle"
                aria-describedby="buyerLogoutModalDescription"
            >
                <div class="buyer-logout-modal-body">
                    <div class="buyer-logout-modal-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H3"></path>
                            <path d="M14 3h7v18h-7"></path>
                        </svg>
                    </div>

                    <h2 id="buyerLogoutModalTitle" class="buyer-logout-modal-title">Log out of SARI?</h2>
                    <p id="buyerLogoutModalDescription" class="buyer-logout-modal-copy">
                        Are you sure you want to end your Buyer session?
                        You will need to sign in again to access your Buyer account.
                    </p>
                </div>

                <div class="buyer-logout-modal-footer">
                    <button id="buyerCancelLogout" type="button"
                        class="buyer-logout-modal-btn buyer-logout-modal-btn-cancel">Cancel</button>

                    <button id="buyerConfirmLogout" type="button"
                        class="buyer-logout-modal-btn buyer-logout-modal-btn-danger">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H3"></path>
                            <path d="M14 3h7v18h-7"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </div>
            </section>
        </div>
    @endpersist

    {{-- Shared Seller modal styles, safely scoped to Buyer logout only. --}}
    <style id="sariBuyerSellerStyleLogoutModal">
/* ============================================================
           BUYER LOGOUT CONFIRMATION MODAL — SAME SELLER DESIGN
           ============================================================ */
        #buyerLogoutModal.hidden,
        #buyerLogoutModal[hidden] {
            display: none !important;
        }

        #buyerLogoutModal {
            position: fixed;
            inset: 0;
            z-index: 11000;
            display: grid;
            place-items: center;
            padding: 22px;
        }

        .buyer-logout-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(24, 28, 34, .54);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            transition: opacity 150ms ease;
        }

        .buyer-logout-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(100%, 390px);
            overflow: hidden;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: #FFFFFF;
            box-shadow:
                0 30px 80px rgba(15, 23, 42, .24),
                0 8px 24px rgba(15, 23, 42, .08);
            opacity: 0;
            transform: translateY(10px) scale(.985);
            transition:
                opacity 160ms ease,
                transform 160ms cubic-bezier(.22, 1, .36, 1);
        }

        #buyerLogoutModal:not(.hidden) .buyer-logout-modal-backdrop {
            opacity: 1;
        }

        #buyerLogoutModal:not(.hidden) .buyer-logout-modal-dialog {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .buyer-logout-modal-body {
            padding: 21px 21px 18px;
        }

        .buyer-logout-modal-icon {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid #FFB7BE;
            border-radius: 8px;
            background: #FFF0F2;
            color: #F04438;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.8);
        }

        .buyer-logout-modal-icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .buyer-logout-modal-title {
            margin: 14px 0 0;
            color: #1F2937;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.28;
            letter-spacing: -.035em;
        }

        .buyer-logout-modal-copy {
            margin: 6px 0 0;
            max-width: 410px;
            color: #667085;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.6;
        }

        .buyer-logout-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #EEF0F3;
            background: #FAFBFC;
            padding: 10px 12px;
        }

        .buyer-logout-modal-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            padding: 0 13px;
            font: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition:
                background-color 120ms ease,
                border-color 120ms ease,
                color 120ms ease,
                box-shadow 120ms ease,
                transform 120ms ease;
        }

        .buyer-logout-modal-btn svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .buyer-logout-modal-btn-cancel {
            min-width: 84px;
            border: 1px solid #D8DEE6;
            background: #FFFFFF;
            color: #475467;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .03);
        }

        .buyer-logout-modal-btn-cancel:hover {
            border-color: #C7CED8;
            background: #F7F8FA;
            color: #253142;
        }

        .buyer-logout-modal-btn-danger {
            min-width: 96px;
            border: 1px solid #ff0b0b;
            background: #ff0b0b;
            color: #FFFFFF;
            box-shadow:
                0 6px 14px rgba(127, 29, 45, .20),
                inset 0 1px 0 rgba(255,255,255,.12);
        }

        .buyer-logout-modal-btn-danger:hover {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 8px 18px rgba(107, 23, 38, .25),
                inset 0 1px 0 rgba(255,255,255,.10);
            transform: translateY(-1px);
        }

        .buyer-logout-modal-btn-danger:active {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 4px 10px rgba(86, 17, 31, .22),
                inset 0 1px 0 rgba(255,255,255,.08);
            transform: translateY(0);
        }

        .buyer-logout-modal-btn-danger:disabled {
            border-color: #9B5360;
            background: #9B5360;
            box-shadow: none;
            opacity: .78;
            cursor: wait;
            transform: none;
        }

        .buyer-logout-modal-btn:focus-visible {
            outline: 3px solid rgba(127, 29, 45, .18);
            outline-offset: 2px;
        }

        @media (max-width: 520px) {
            #buyerLogoutModal {
                align-items: end;
                padding: 10px;
            }

            .buyer-logout-modal-dialog {
                width: 100%;
                border-radius: 8px;
            }

            .buyer-logout-modal-body {
                padding: 20px 18px 17px;
            }

            .buyer-logout-modal-title {
                font-size: 17px;
            }

            .buyer-logout-modal-copy {
                font-size: 12px;
            }

            .buyer-logout-modal-footer {
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 10px;
            }

            .buyer-logout-modal-btn {
                width: 100%;
                min-width: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .buyer-logout-modal-backdrop,
            .buyer-logout-modal-dialog,
            .buyer-logout-modal-btn {
                transition: none !important;
            }
        }
    </style>

    {{-- The existing buyer.js owns open, cancel and confirm. This adds only
         backdrop, Escape, and keyboard-focus support without duplicate submit handlers. --}}
    <script data-navigate-once>
        (function () {
            if (window.__SARI_BUYER_LOGOUT_MODAL_UI_V1__) return;
            window.__SARI_BUYER_LOGOUT_MODAL_UI_V1__ = true;

            const findModal = () => document.getElementById('buyerLogoutModal');
            const isOpen = () => {
                const node = findModal();
                return Boolean(node && !node.classList.contains('hidden'));
            };
            const dismiss = () => {
                const node = findModal();
                if (!node || node.classList.contains('hidden')) return;
                const cancel = document.getElementById('buyerCancelLogout');
                if (cancel) cancel.click();
                else { node.classList.add('hidden'); node.classList.remove('flex'); }
            };

            document.addEventListener('click', function (event) {
                if (event.target.closest?.('[data-buyer-logout-backdrop]')) {
                    dismiss();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (!isOpen()) return;
                if (event.key === 'Escape') {
                    event.preventDefault();
                    dismiss();
                    return;
                }
                if (event.key !== 'Tab') return;
                const focusable = Array.from(findModal().querySelectorAll('button:not(:disabled)'));
                if (!focusable.length) return;
                if (event.shiftKey && document.activeElement === focusable[0]) {
                    event.preventDefault();
                    focusable[focusable.length - 1].focus();
                } else if (!event.shiftKey && document.activeElement === focusable[focusable.length - 1]) {
                    event.preventDefault();
                    focusable[0].focus();
                }
            });

            // The persistent modal remains in the DOM across Livewire navigation.
            document.addEventListener('livewire:navigating', function () {
                const node = findModal();
                if (!node) return;
                node.classList.add('hidden');
                node.classList.remove('flex');
            });
        })();
    </script>


    {{-- ============================================================
         PAGE-SPECIFIC JAVASCRIPT
    ============================================================ --}}
    @stack('scripts')

    {{-- ============================================================
         LIVEWIRE SCRIPTS
    ============================================================ --}}
    @livewireScripts


    {{-- ============================================================
         BUYER PERSISTENT SHELL BRIDGE
         Seller-style desktop mini sidebar + mobile drawer.
    ============================================================ --}}
    {{-- ============================================================
         BUYER PERSISTENT SHELL BRIDGE
         Seller/Admin-style synchronized mini sidebar.
    ============================================================ --}}
    <script>
        (function () {
            if (window.__SARI_BUYER_PERSISTENT_SHELL_V2__) {
                return;
            }

            window.__SARI_BUYER_PERSISTENT_SHELL_V2__ = true;

            const MINI_STORAGE_KEY =
                'sari:buyer-sidebar-mini-v2';

            const DESKTOP_BREAKPOINT = 1024;


            function isDesktop() {
                return window.innerWidth >= DESKTOP_BREAKPOINT;
            }


            function readMiniPreference() {
                try {
                    return (
                        window.localStorage.getItem(
                            MINI_STORAGE_KEY
                        ) === '1'
                    );
                } catch (_) {
                    return false;
                }
            }


            function saveMiniPreference(mini) {
                try {
                    window.localStorage.setItem(
                        MINI_STORAGE_KEY,
                        mini ? '1' : '0'
                    );
                } catch (_) {}
            }


            function isMini() {
                return (
                    document.documentElement.dataset
                        .buyerSidebarMini === '1'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ONE FUNCTION CHANGES EVERYTHING TOGETHER
            |--------------------------------------------------------------------------
            | - sidebar width
            | - main content padding
            | - labels
            | - compact/full logo
            | - burger accessibility
            */
            function setSidebarMini(mini, persist = true) {
                const next =
                    isDesktop() && Boolean(mini);

                document.documentElement.dataset.buyerSidebarMini =
                    next ? '1' : '0';

                document.documentElement.style.setProperty(
                    '--buyer-sidebar-width',
                    next ? '76px' : '238px'
                );

                if (persist) {
                    saveMiniPreference(next);
                }

                const toggle =
                    document.getElementById(
                        'buyerSidebarToggle'
                    );

                toggle?.setAttribute(
                    'aria-expanded',
                    next ? 'false' : 'true'
                );

                toggle?.setAttribute(
                    'aria-label',
                    next
                        ? 'Expand sidebar'
                        : 'Collapse sidebar'
                );

                toggle?.setAttribute(
                    'title',
                    next
                        ? 'Expand sidebar'
                        : 'Collapse sidebar'
                );
            }


            function openMobileSidebar() {
                if (isDesktop()) {
                    return;
                }

                document
                    .getElementById('buyerSidebar')
                    ?.classList.remove(
                        '-translate-x-full'
                    );

                document
                    .getElementById(
                        'buyerSidebarOverlay'
                    )
                    ?.classList.remove('hidden');

                document.body.classList.add(
                    'overflow-hidden'
                );

                document
                    .getElementById(
                        'buyerMobileMenuButton'
                    )
                    ?.setAttribute(
                        'aria-expanded',
                        'true'
                    );
            }


            function closeMobileSidebar() {
                if (!isDesktop()) {
                    document
                        .getElementById('buyerSidebar')
                        ?.classList.add(
                            '-translate-x-full'
                        );
                }

                document
                    .getElementById(
                        'buyerSidebarOverlay'
                    )
                    ?.classList.add('hidden');

                document.body.classList.remove(
                    'overflow-hidden'
                );

                document
                    .getElementById(
                        'buyerMobileMenuButton'
                    )
                    ?.setAttribute(
                        'aria-expanded',
                        'false'
                    );
            }


            function bindOnce(
                element,
                eventName,
                handler,
                marker
            ) {
                if (!element) {
                    return;
                }

                const key =
                    'sariBound' + marker;

                if (element.dataset[key] === '1') {
                    return;
                }

                element.dataset[key] = '1';

                element.addEventListener(
                    eventName,
                    handler
                );
            }


            function bindShellControls() {
                bindOnce(
                    document.getElementById(
                        'buyerSidebarToggle'
                    ),
                    'click',
                    function () {
                        if (!isDesktop()) {
                            return;
                        }

                        setSidebarMini(
                            !isMini(),
                            true
                        );
                    },
                    'DesktopSidebarToggleV2'
                );

                bindOnce(
                    document.getElementById(
                        'buyerMobileMenuButton'
                    ),
                    'click',
                    openMobileSidebar,
                    'MobileMenuV2'
                );

                bindOnce(
                    document.getElementById(
                        'buyerSidebarClose'
                    ),
                    'click',
                    closeMobileSidebar,
                    'MobileCloseV2'
                );

                bindOnce(
                    document.getElementById(
                        'buyerSidebarOverlay'
                    ),
                    'click',
                    closeMobileSidebar,
                    'OverlayV2'
                );
            }


            function normalizePath(url) {
                try {
                    const value =
                        new URL(
                            url,
                            window.location.origin
                        );

                    let path =
                        value.pathname.replace(
                            /\/+$/,
                            ''
                        );

                    return path === '' ? '/' : path;
                } catch (_) {
                    return '';
                }
            }


            function isBuyerProductWorkspace(path) {
                return (
                    path === '/buyer/products' ||
                    path.startsWith(
                        '/buyer/products/'
                    ) ||
                    path.startsWith(
                        '/buyer/shop/'
                    )
                );
            }


            function syncActiveNav() {
                const currentPath =
                    normalizePath(
                        window.location.href
                    );

                document
                    .querySelectorAll(
                        '#buyerSidebar [data-buyer-sidebar-item][href]'
                    )
                    .forEach(function (item) {
                        const itemPath =
                            normalizePath(item.href);

                        let active =
                            itemPath === currentPath;

                        if (
                            itemPath ===
                                '/buyer/products' &&
                            isBuyerProductWorkspace(
                                currentPath
                            )
                        ) {
                            active = true;
                        }

                        item.dataset.buyerNavActive =
                            active
                                ? 'true'
                                : 'false';

                        if (active) {
                            item.setAttribute(
                                'aria-current',
                                'page'
                            );
                        } else {
                            item.removeAttribute(
                                'aria-current'
                            );
                        }
                    });
            }


            function shouldNavigate(
                anchor,
                event
            ) {
                if (!anchor || !anchor.href) {
                    return false;
                }

                // Livewire owns its wire:navigate(.hover) links and prefetch cache.
                // Intercepting them a second time can bypass native navigation.
                if (anchor.hasAttribute('wire:navigate') || anchor.hasAttribute('wire:navigate.hover')
                    || anchor.hasAttribute('data-no-livewire')) {
                    return false;
                }

                const rawHref = (anchor.getAttribute('href') || '').trim();
                if (!rawHref || rawHref.startsWith('#')) return false;

                if (
                    anchor.hasAttribute(
                        'download'
                    )
                ) {
                    return false;
                }

                const target =
                    anchor.getAttribute('target');

                if (
                    target &&
                    target !== '_self'
                ) {
                    return false;
                }

                if (
                    event.defaultPrevented ||
                    event.button !== 0 ||
                    event.metaKey ||
                    event.ctrlKey ||
                    event.shiftKey ||
                    event.altKey
                ) {
                    return false;
                }

                const url =
                    new URL(
                        anchor.href,
                        window.location.origin
                    );

                if (url.origin !== window.location.origin) return false;
                if (url.pathname === window.location.pathname &&
                    url.search === window.location.search && url.hash) return false;

                // HTML pages only: never prefetch or navigate media, JSON,
                // tracking, documents or mutation URLs as page content.
                return /^\/buyer\/(?:dashboard|home|products|cart|checkout|orders|recent-orders|recent-activity|shipping|notifications|messages|account|rewards|shop\/[^/]+|products\/\d+|orders\/\d+)$/.test(url.pathname);
            }


            function installNavigationBridge() {
                if (
                    window
                        .__SARI_BUYER_NAV_BRIDGE_V2__
                ) {
                    return;
                }

                window
                    .__SARI_BUYER_NAV_BRIDGE_V2__ =
                    true;

                document.addEventListener(
                    'click',
                    function (event) {
                        const anchor =
                            event.target.closest?.(
                                'a[href]'
                            );

                        if (
                            !shouldNavigate(
                                anchor,
                                event
                            ) ||
                            !window.Livewire
                                ?.navigate
                        ) {
                            return;
                        }

                        event.preventDefault();

                        closeMobileSidebar();

                        window.Livewire.navigate(
                            anchor.href
                        );
                    }
                );
            }


            function initShell() {
                bindShellControls();

                if (isDesktop()) {
                    setSidebarMini(
                        readMiniPreference(),
                        false
                    );

                    document
                        .getElementById(
                            'buyerSidebar'
                        )
                        ?.classList.remove(
                            '-translate-x-full'
                        );
                } else {
                    document.documentElement.dataset
                        .buyerSidebarMini = '0';

                    document.documentElement.style
                        .setProperty(
                            '--buyer-sidebar-width',
                            '238px'
                        );
                }

                closeMobileSidebar();
                syncActiveNav();
                installNavigationBridge();

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:buyer-page-ready'
                    )
                );
            }


            if (
                !window
                    .__SARI_BUYER_SHELL_GLOBAL_V2__
            ) {
                window
                    .__SARI_BUYER_SHELL_GLOBAL_V2__ =
                    true;

                window.addEventListener(
                    'resize',
                    function () {
                        if (isDesktop()) {
                            setSidebarMini(
                                readMiniPreference(),
                                false
                            );

                            document
                                .getElementById(
                                    'buyerSidebar'
                                )
                                ?.classList.remove(
                                    '-translate-x-full'
                                );

                            document.body.classList.remove(
                                'overflow-hidden'
                            );
                        } else {
                            document.documentElement.dataset
                                .buyerSidebarMini =
                                '0';

                            document.documentElement.style
                                .setProperty(
                                    '--buyer-sidebar-width',
                                    '238px'
                                );
                        }
                    }
                );

                document.addEventListener(
                    'livewire:navigating',
                    closeMobileSidebar
                );

                document.addEventListener(
                    'livewire:navigated',
                    initShell
                );
            }


            initShell();
        })();
    </script>

</body>
</html>
