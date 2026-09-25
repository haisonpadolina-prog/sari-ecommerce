<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SARI Admin')</title>

    {{-- Restore collapsed desktop sidebar before first paint. --}}
    <script>
        (function () {
            try {
                if (
                    window.matchMedia('(min-width: 1024px)').matches &&
                    window.localStorage.getItem('sari:admin-sidebar-collapsed') === '1'
                ) {
                    document.documentElement.classList.add('admin-sidebar-collapsed');
                }
            } catch (_) {}
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --admin-sidebar-expanded: 275px;
            --admin-sidebar-collapsed-width: 88px;
            --admin-sidebar-width: var(--admin-sidebar-expanded);
            --admin-sidebar-ease: cubic-bezier(.22, 1, .36, 1);
        }

        @media (min-width: 1024px) {
            html.admin-sidebar-collapsed {
                --admin-sidebar-width: var(--admin-sidebar-collapsed-width);
            }

            #adminSidebar {
                width: var(--admin-sidebar-width) !important;
                overflow-x: hidden;
                transition: width 220ms var(--admin-sidebar-ease);
            }

            #adminContent {
                padding-left: var(--admin-sidebar-width);
                transition: padding-left 220ms var(--admin-sidebar-ease);
            }

            html.admin-sidebar-collapsed #adminSidebarLogo {
                display: none !important;
            }

            html.admin-sidebar-collapsed #adminSidebar .sidebar-label {
                display: none !important;
            }

            html.admin-sidebar-collapsed #adminSidebar .sidebar-extra {
                display: none !important;
            }

            html.admin-sidebar-collapsed #adminSidebar [data-sidebar-item] {
                justify-content: center !important;
                gap: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html.admin-sidebar-collapsed #adminSidebar [data-sidebar-inner] {
                justify-content: center !important;
                gap: 0 !important;
            }

            html.admin-sidebar-collapsed #adminSidebarNav {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            html.admin-sidebar-collapsed #adminSidebarProfile {
                padding: 12px !important;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PERSISTENT SIDEBAR ACTIVE STATE
        |--------------------------------------------------------------------------
        |
        | Because the sidebar DOM is persisted by Livewire, its original
        | Blade request()->routeIs() classes do not get rebuilt. These rules
        | let JavaScript update the active item after every navigation.
        */
        #adminSidebar [data-sidebar-item][data-admin-nav-active="true"] {
            background-color: #d9930a !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px rgba(217, 147, 10, .18) !important;
        }

        #adminSidebar [data-sidebar-item][data-admin-nav-active="false"] {
            background-color: transparent !important;
            color: #514b42 !important;
            box-shadow: none !important;
        }

        #adminSidebar [data-sidebar-item][data-admin-nav-active="false"]:hover {
            background-color: #f9f1e3 !important;
            color: #a96e05 !important;
        }

        #adminSidebar [data-sidebar-item][data-admin-nav-active="true"] [data-admin-unread-badge] {
            background-color: #ffffff !important;
            color: #c48413 !important;
        }

        @media (prefers-reduced-motion: reduce) {
            #adminSidebar,
            #adminContent {
                transition-duration: 1ms !important;
            }
        }

        /* ============================================================
           ADMIN CANVAS — MATCH SELLER
           Soft neutral gray background used by Seller: #F4F5F7
           ============================================================ */
        html,
        body,
        #adminContent {
            background: #F4F5F7 !important;
        }

        #adminPageContent {
            background: transparent !important;
        }

    </style>

    @livewireStyles

    {{-- Keep page-specific styles working --}}
    @stack('styles')

    {{--
        Keep Vite / Echo loaded once in the document head.
        Livewire navigation does not recreate this connection.
    --}}
    @vite('resources/js/app.js')
</head>

<body class="m-0 min-h-screen bg-[#F4F5F7] font-poppins text-[#1f1b16] antialiased">

    <div class="min-h-screen">

        {{--
            PERSISTENT ADMIN SIDEBAR
            The exact existing sidebar component is reused unchanged.
        --}}
        @persist('admin-sidebar')
            @include('components.admin.sidebar')
        @endpersist

        <div
            id="adminContent"
            class="min-h-screen transition-all duration-300 ease-out"
        >
            {{--
                Keep the Admin header alive as well.
                This preserves search, notification bell state, and its
                existing realtime listeners. The page title is synchronized
                from the new <main> after navigation.
            --}}
            @persist('admin-header')
                @include('components.admin.header')
            @endpersist

            <main
                id="adminPageContent"
                data-admin-page-title="{{ trim($__env->yieldContent('page-title', 'Dashboard Overview')) }}"
                data-admin-browser-title="{{ trim($__env->yieldContent('title', 'SARI Admin')) }}"
                class="
                    w-full
                    px-4 py-5
                    sm:px-6 sm:py-6
                    lg:px-8 lg:py-7
                    xl:px-10
                "
            >
                @yield('content')
            </main>
        </div>

        <div
            id="adminSidebarOverlay"
            class="
                fixed inset-0 z-40
                hidden
                bg-black/30
                backdrop-blur-[1px]
                lg:hidden
            "
        ></div>

    </div>


    @persist('admin-action-toast')
        <div
            id="adminActionToast"
            class="pointer-events-none fixed right-4 top-4 z-[10000] hidden w-[min(420px,calc(100vw-2rem))]"
            aria-live="polite"
            aria-atomic="true"
        >
            <div
                id="adminActionToastCard"
                class="pointer-events-auto flex items-start gap-3 rounded-[16px] border border-[#d7e7dd] bg-white px-4 py-3.5 shadow-[0_18px_50px_rgba(38,30,18,.16)]"
            >
                <span
                    id="adminActionToastIcon"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f1f8f4] text-[#56816a]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

                <div class="min-w-0 flex-1">
                    <p
                        id="adminActionToastTitle"
                        class="text-[10px] font-bold text-[#3f6f52]"
                    >
                        Action completed
                    </p>

                    <p
                        id="adminActionToastMessage"
                        class="mt-1 text-[9px] leading-5 text-[#65776c]"
                    ></p>
                </div>

                <button
                    id="adminActionToastClose"
                    type="button"
                    class="pointer-events-auto grid h-7 w-7 shrink-0 place-items-center rounded-lg text-[#8f877c] transition hover:bg-[#f5f2ed] hover:text-[#514a42]"
                    aria-label="Close notification"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m7 7 10 10"></path>
                        <path d="m17 7-10 10"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endpersist

    @stack('scripts')

    @livewireScripts

    <script>
        (function () {
            const ROOT_CLASS = 'admin-sidebar-collapsed';
            const STORAGE_KEY = 'sari:admin-sidebar-collapsed';
            const DESKTOP_BREAKPOINT = 1024;

            function readCollapsedPreference() {
                try {
                    return window.localStorage.getItem(STORAGE_KEY) === '1';
                } catch (_) {
                    return false;
                }
            }

            function saveCollapsedPreference(collapsed) {
                try {
                    window.localStorage.setItem(
                        STORAGE_KEY,
                        collapsed ? '1' : '0'
                    );
                } catch (_) {}
            }

            function isDesktop() {
                return window.innerWidth >= DESKTOP_BREAKPOINT;
            }

            function isCollapsed() {
                return document.documentElement.classList.contains(ROOT_CLASS);
            }

            function setCollapsed(collapsed, persist = true) {
                const next = isDesktop() && Boolean(collapsed);

                document.documentElement.classList.toggle(
                    ROOT_CLASS,
                    next
                );

                if (persist) {
                    saveCollapsedPreference(next);
                }

                syncToggleAccessibility();
            }

            function syncToggleAccessibility() {
                const toggle = document.getElementById('adminSidebarToggle');

                if (!toggle) return;

                const collapsed = isCollapsed();

                toggle.setAttribute(
                    'aria-expanded',
                    collapsed ? 'false' : 'true'
                );

                toggle.setAttribute(
                    'aria-label',
                    collapsed
                        ? 'Expand sidebar'
                        : 'Collapse sidebar'
                );
            }

            function openMobileSidebar() {
                if (isDesktop()) return;

                document
                    .getElementById('adminSidebar')
                    ?.classList.remove('-translate-x-full');

                document
                    .getElementById('adminSidebarOverlay')
                    ?.classList.remove('hidden');

                document.body.classList.add('overflow-hidden');
            }

            function closeMobileSidebar() {
                if (!isDesktop()) {
                    document
                        .getElementById('adminSidebar')
                        ?.classList.add('-translate-x-full');
                }

                document
                    .getElementById('adminSidebarOverlay')
                    ?.classList.add('hidden');

                document.body.classList.remove('overflow-hidden');
            }

            function bindElementOnce(element, eventName, handler, key) {
                if (!element) return;

                const marker = 'sariBound' + key;

                if (element.dataset[marker] === '1') {
                    return;
                }

                element.dataset[marker] = '1';
                element.addEventListener(eventName, handler);
            }

            function bindShellControls() {
                bindElementOnce(
                    document.getElementById('adminMenuButton'),
                    'click',
                    openMobileSidebar,
                    'MobileMenu'
                );

                bindElementOnce(
                    document.getElementById('adminSidebarClose'),
                    'click',
                    closeMobileSidebar,
                    'MobileClose'
                );

                bindElementOnce(
                    document.getElementById('adminSidebarOverlay'),
                    'click',
                    closeMobileSidebar,
                    'Overlay'
                );

                bindElementOnce(
                    document.getElementById('adminSidebarToggle'),
                    'click',
                    function () {
                        if (!isDesktop()) return;
                        setCollapsed(!isCollapsed(), true);
                    },
                    'DesktopToggle'
                );
            }

            function normalizePath(url) {
                try {
                    const value = new URL(url, window.location.origin);
                    let path = value.pathname.replace(/\/+$/, '');
                    return path === '' ? '/' : path;
                } catch (_) {
                    return '';
                }
            }

            function syncSidebarActiveState() {
                const currentPath = normalizePath(window.location.href);

                document
                    .querySelectorAll('#adminSidebar [data-sidebar-item][href]')
                    .forEach(function (item) {
                        const itemPath = normalizePath(item.href);

                        let active = itemPath === currentPath;

                        /*
                         * Seller Account Control is a child workspace of
                         * Seller Compliance, so keep Compliance highlighted.
                         */
                        if (
                            currentPath === '/admin/seller-account-control' &&
                            itemPath === '/admin/seller-compliance'
                        ) {
                            active = true;
                        }

                        item.dataset.adminNavActive =
                            active ? 'true' : 'false';

                        if (active) {
                            item.setAttribute('aria-current', 'page');
                        } else {
                            item.removeAttribute('aria-current');
                        }
                    });
            }

            function syncPersistentHeaderTitle() {
                const main = document.getElementById('adminPageContent');
                const header = document.querySelector('#adminContent > header');

                if (!main || !header) return;

                const pageTitle =
                    main.dataset.adminPageTitle || 'Dashboard Overview';

                const browserTitle =
                    main.dataset.adminBrowserTitle || 'SARI Admin';

                document.title = browserTitle;

                const heading = header.querySelector('h1');

                if (heading) {
                    heading.textContent = pageTitle;
                }

                const homeLink = Array.from(
                    header.querySelectorAll('a[href]')
                ).find(function (link) {
                    return link.textContent.trim() === 'Home';
                });

                const breadcrumb =
                    homeLink?.parentElement?.querySelector(
                        'span.font-medium'
                    );

                if (breadcrumb) {
                    breadcrumb.textContent = pageTitle;
                }
            }

            function shouldUseAdminNavigate(anchor, event) {
                if (!anchor || !anchor.href) return false;
                if (anchor.hasAttribute('download')) return false;

                const target = anchor.getAttribute('target');

                if (target && target !== '_self') return false;

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

                const url = new URL(
                    anchor.href,
                    window.location.origin
                );

                if (url.origin !== window.location.origin) {
                    return false;
                }

                if (!url.pathname.startsWith('/admin')) {
                    return false;
                }

                /*
                 * Protected files should remain normal browser requests.
                 */
                if (
                    url.pathname.includes('/document/') ||
                    url.pathname.includes('/image') ||
                    url.pathname.includes('/attachments/')
                ) {
                    return false;
                }

                return true;
            }

            function installNavigationBridge() {
                if (window.__SARI_ADMIN_NAVIGATION_BRIDGE__) {
                    return;
                }

                window.__SARI_ADMIN_NAVIGATION_BRIDGE__ = true;

                document.addEventListener('click', function (event) {
                    const anchor = event.target.closest?.('a[href]');

                    if (!shouldUseAdminNavigate(anchor, event)) {
                        return;
                    }

                    if (!window.Livewire?.navigate) {
                        return;
                    }

                    event.preventDefault();

                    closeMobileSidebar();
                    window.Livewire.navigate(anchor.href);
                });

                /*
                 * GET filter forms can also navigate without tearing down
                 * the persistent Admin shell. POST/PUT/DELETE forms remain
                 * normal Laravel requests on purpose.
                 */
                document.addEventListener('submit', function (event) {
                    const form = event.target;

                    if (!(form instanceof HTMLFormElement)) {
                        return;
                    }

                    const method =
                        String(form.method || 'get').toLowerCase();

                    if (method !== 'get') {
                        return;
                    }

                    const action = new URL(
                        form.action || window.location.href,
                        window.location.origin
                    );

                    if (
                        action.origin !== window.location.origin ||
                        !action.pathname.startsWith('/admin') ||
                        !window.Livewire?.navigate
                    ) {
                        return;
                    }

                    event.preventDefault();

                    const params = new URLSearchParams(
                        new FormData(form)
                    );

                    action.search = params.toString();

                    closeMobileSidebar();
                    window.Livewire.navigate(action.toString());
                });
            }

            function initAdminShell() {
                bindShellControls();

                if (isDesktop()) {
                    setCollapsed(
                        readCollapsedPreference(),
                        false
                    );
                } else {
                    document.documentElement.classList.remove(ROOT_CLASS);
                }

                closeMobileSidebar();
                syncToggleAccessibility();
                syncSidebarActiveState();
                syncPersistentHeaderTitle();
                installNavigationBridge();
                bindAdminActionToast();
                installFastAdminActionBridge();
            }

            if (!window.__SARI_ADMIN_SHELL_GLOBAL_EVENTS__) {
                window.__SARI_ADMIN_SHELL_GLOBAL_EVENTS__ = true;

                window.addEventListener('resize', function () {
                    if (isDesktop()) {
                        setCollapsed(
                            readCollapsedPreference(),
                            false
                        );

                        document
                            .getElementById('adminSidebarOverlay')
                            ?.classList.add('hidden');

                        document.body.classList.remove('overflow-hidden');
                    } else {
                        document.documentElement.classList.remove(ROOT_CLASS);
                    }

                    syncToggleAccessibility();
                });

                document.addEventListener(
                    'livewire:navigating',
                    function () {
                        closeMobileSidebar();
                    }
                );

                document.addEventListener(
                    'livewire:navigated',
                    function () {
                        initAdminShell();
                    }
                );
            }



            /*
            |--------------------------------------------------------------------------
            | FAST ADMIN ACTION BRIDGE
            |--------------------------------------------------------------------------
            |
            | Important Admin POST actions are sent with fetch() instead of a
            | full browser form navigation. The controller returns JSON, then
            | Livewire refreshes only the page content.
            |
            | Result:
            | - Sidebar stays mounted.
            | - Logo stays mounted.
            | - Header stays mounted.
            | - Collapse state stays unchanged.
            | - Validation errors stay on the current page.
            */
            let adminActionToastTimer = null;

            function showAdminActionToast(message, type = 'success') {
                const toast = document.getElementById('adminActionToast');
                const card = document.getElementById('adminActionToastCard');
                const icon = document.getElementById('adminActionToastIcon');
                const title = document.getElementById('adminActionToastTitle');
                const body = document.getElementById('adminActionToastMessage');

                if (!toast || !card || !icon || !title || !body) {
                    return;
                }

                const isError = type === 'error';

                card.classList.remove(
                    'border-[#d7e7dd]',
                    'border-[#ecd3d3]'
                );

                icon.classList.remove(
                    'bg-[#f1f8f4]',
                    'text-[#56816a]',
                    'bg-[#fff3f3]',
                    'text-[#a65f5f]'
                );

                title.classList.remove(
                    'text-[#3f6f52]',
                    'text-[#9d5555]'
                );

                body.classList.remove(
                    'text-[#65776c]',
                    'text-[#8d6666]'
                );

                if (isError) {
                    card.classList.add('border-[#ecd3d3]');
                    icon.classList.add('bg-[#fff3f3]', 'text-[#a65f5f]');
                    title.classList.add('text-[#9d5555]');
                    body.classList.add('text-[#8d6666]');
                    title.textContent = 'Action not completed';

                    icon.innerHTML = `
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 8v5"></path>
                            <path d="M12 16.5h.01"></path>
                        </svg>
                    `;
                } else {
                    card.classList.add('border-[#d7e7dd]');
                    icon.classList.add('bg-[#f1f8f4]', 'text-[#56816a]');
                    title.classList.add('text-[#3f6f52]');
                    body.classList.add('text-[#65776c]');
                    title.textContent = 'Action completed';

                    icon.innerHTML = `
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m8 12 2.5 2.5L16 9"></path>
                        </svg>
                    `;
                }

                body.textContent = message || (
                    isError
                        ? 'Please check the form and try again.'
                        : 'The administrator action was saved successfully.'
                );

                toast.classList.remove('hidden');

                if (adminActionToastTimer) {
                    window.clearTimeout(adminActionToastTimer);
                }

                adminActionToastTimer = window.setTimeout(function () {
                    toast.classList.add('hidden');
                }, isError ? 6500 : 4200);
            }

            function bindAdminActionToast() {
                bindElementOnce(
                    document.getElementById('adminActionToastClose'),
                    'click',
                    function () {
                        document
                            .getElementById('adminActionToast')
                            ?.classList.add('hidden');
                    },
                    'ActionToastClose'
                );
            }

            function isFastAdminActionUrl(url) {
                const path = url.pathname;

                return (
                    path.startsWith('/admin/registrations/') ||
                    path.startsWith('/admin/seller-compliance/') ||
                    path.startsWith('/admin/seller-account-control/')
                );
            }

            function getAdminActionError(payload) {
                if (payload?.errors && typeof payload.errors === 'object') {
                    for (const value of Object.values(payload.errors)) {
                        if (Array.isArray(value) && value.length) {
                            return String(value[0]);
                        }

                        if (typeof value === 'string' && value.trim()) {
                            return value;
                        }
                    }
                }

                return String(
                    payload?.message ||
                    'The action could not be completed.'
                );
            }

            function setAdminActionButtonBusy(button, busy) {
                if (!button) return;

                if (busy) {
                    if (!button.dataset.adminOriginalHtml) {
                        button.dataset.adminOriginalHtml = button.innerHTML;
                    }

                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');
                    button.classList.add('cursor-wait', 'opacity-70');

                    button.innerHTML = `
                        <span class="inline-flex items-center gap-2">
                            <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-r-transparent"></span>
                            Processing...
                        </span>
                    `;

                    return;
                }

                button.disabled = false;
                button.removeAttribute('aria-busy');
                button.classList.remove('cursor-wait', 'opacity-70');

                if (button.dataset.adminOriginalHtml) {
                    button.innerHTML = button.dataset.adminOriginalHtml;
                    delete button.dataset.adminOriginalHtml;
                }
            }

            async function submitFastAdminAction(form, submitter) {
                if (form.dataset.adminActionBusy === '1') {
                    return;
                }

                form.dataset.adminActionBusy = '1';

                const button =
                    submitter ||
                    form.querySelector(
                        'button[type="submit"], input[type="submit"]'
                    );

                setAdminActionButtonBusy(button, true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    let payload = {};

                    try {
                        payload = await response.json();
                    } catch (_) {
                        payload = {};
                    }

                    if (!response.ok) {
                        showAdminActionToast(
                            getAdminActionError(payload),
                            'error'
                        );

                        return;
                    }

                    showAdminActionToast(
                        payload?.message ||
                        'The administrator action was saved successfully.',
                        'success'
                    );

                    document.body.classList.remove('overflow-hidden');

                    /*
                     * Give the success toast one paint frame before refreshing
                     * the dynamic page body. The persisted Admin shell does not
                     * disappear during this navigation.
                     */
                    window.setTimeout(function () {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(
                                window.location.href
                            );
                        } else {
                            window.location.reload();
                        }
                    }, 70);

                } catch (error) {
                    showAdminActionToast(
                        'Network request failed. Please try the action again.',
                        'error'
                    );
                } finally {
                    form.dataset.adminActionBusy = '0';
                    setAdminActionButtonBusy(button, false);
                }
            }

            function installFastAdminActionBridge() {
                if (window.__SARI_FAST_ADMIN_ACTION_BRIDGE__) {
                    return;
                }

                window.__SARI_FAST_ADMIN_ACTION_BRIDGE__ = true;

                document.addEventListener(
                    'submit',
                    function (event) {
                        const form = event.target;

                        if (!(form instanceof HTMLFormElement)) {
                            return;
                        }

                        const method =
                            String(
                                form.getAttribute('method') || 'get'
                            ).toLowerCase();

                        if (method !== 'post') {
                            return;
                        }

                        const action = new URL(
                            form.action || window.location.href,
                            window.location.origin
                        );

                        if (
                            action.origin !== window.location.origin ||
                            !isFastAdminActionUrl(action)
                        ) {
                            return;
                        }

                        /*
                         * Logout, protected document routes, Chat sending,
                         * and unrelated forms are intentionally untouched.
                         */
                        event.preventDefault();

                        submitFastAdminAction(
                            form,
                            event.submitter || null
                        );
                    },
                    true
                );
            }

            /*
             * Initial full-page load.
             * Subsequent pages are handled by livewire:navigated.
             */
            bindAdminActionToast();
            installFastAdminActionBridge();
            initAdminShell();
        })();
    </script>

</body>
</html>
