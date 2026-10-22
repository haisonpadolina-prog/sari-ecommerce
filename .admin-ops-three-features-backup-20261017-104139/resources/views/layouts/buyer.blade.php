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


    {{-- ============================================================
         BUYER AI ASSISTANT — UI PREVIEW ONLY
         Visual shell only. No API call, route, event, or backend behavior.
    ============================================================ --}}
    @persist('buyer-ai-preview')
        <div id="buyerAiPreview" class="buyer-ai-preview" aria-label="SARI AI preview">
            <div class="buyer-ai-preview-card" role="status" aria-live="polite">
                <span class="buyer-ai-preview-eyebrow">SARI AI</span>
                <strong>Shopping assistant</strong>
                <span>Coming soon</span>
            </div>

            <button
                type="button"
                class="buyer-ai-preview-button"
                aria-label="SARI AI shopping assistant — coming soon"
                aria-disabled="true"
                title="SARI AI — Coming soon"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 13.6 8.4 19 10l-5.4 1.6L12 17l-1.6-5.4L5 10l5.4-1.6L12 3Z"></path>
                    <path d="m18.5 16 .7 2.3 2.3.7-2.3.7-.7 2.3-.7-2.3-2.3-.7 2.3-.7.7-2.3Z"></path>
                </svg>
                <span class="buyer-ai-preview-dot"></span>
            </button>
        </div>
    @endpersist


    {{-- ============================================================
         PERSISTENT LOGOUT CONFIRMATION MODAL
    ============================================================ --}}
    @persist('buyer-logout-modal')
        <div
            id="buyerLogoutModal"
            class="
                fixed
                inset-0
                z-[200]

                hidden

                items-center
                justify-center

                bg-black/30

                p-4
            "
            role="dialog"
            aria-modal="true"
            aria-labelledby="buyerLogoutModalTitle"
        >
            <div
                class="
                    w-full
                    max-w-[390px]

                    rounded-[24px]

                    border
                    border-[#e9dfcf]

                    bg-white

                    p-6

                    shadow-[0_30px_90px_rgba(38,30,18,0.20)]
                "
            >
                <div
                    class="
                        mx-auto

                        grid
                        h-14
                        w-14

                        place-items-center

                        rounded-2xl

                        bg-[#fbf5e9]

                        text-[#a8731f]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>
                </div>

                <div class="mt-5 text-center">
                    <h2
                        id="buyerLogoutModalTitle"
                        class="text-[16px] font-semibold text-[#312b25]"
                    >
                        Log out?
                    </h2>

                    <p
                        class="mt-2 text-[9px] leading-5 text-[#91887d]"
                    >
                        Are you sure you want to log out of your account?
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-2">
                    <button
                        id="buyerCancelLogout"
                        type="button"
                        class="
                            h-11
                            rounded-xl
                            border border-[#e1d6c6]
                            bg-[#fcf8f1]
                            text-[9px] font-medium text-[#62594e]
                            transition-colors duration-150
                            hover:border-[#d8c39d]
                            hover:bg-[#f7eddd]
                            hover:text-[#9a6717]
                            focus:outline-none
                            focus:ring-4
                            focus:ring-[#d89a25]/10
                        "
                    >
                        Cancel
                    </button>

                    <button
                        id="buyerConfirmLogout"
                        type="button"
                        class="
                            h-11
                            rounded-xl
                            bg-[#c99128]
                            text-[9px] font-medium text-white
                            shadow-[0_8px_20px_rgba(201,145,40,0.18)]
                            transition-colors duration-150
                            hover:bg-[#b47e1e]
                            focus:outline-none
                            focus:ring-4
                            focus:ring-[#d89a25]/15
                        "
                    >
                        Log Out
                    </button>
                </div>
            </div>
        </div>
    @endpersist


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

                return (
                    url.origin ===
                        window.location.origin &&
                    url.pathname.startsWith(
                        '/buyer'
                    ) &&
                    !url.pathname.includes(
                        '/image'
                    ) &&
                    !url.pathname.includes(
                        '/attachment'
                    ) &&
                    !url.pathname.includes(
                        '/document'
                    )
                );
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
