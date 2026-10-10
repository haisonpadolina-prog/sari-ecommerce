<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    {{-- SELLER FIRST-PAINT CANVAS — prevents warm/cream route flashes. --}}
    <style id="sariSellerFirstPaintCanvas">
        :root,
        html,
        body {
            min-height: 100%;
            background: #F4F5F7 !important;
            background-color: #F4F5F7 !important;
            background-image: none !important;
        }

        body {
            margin: 0;
        }

        #sellerContent {
            min-height: 100vh;
            background: #F4F5F7 !important;
            background-color: #F4F5F7 !important;
            background-image: none !important;
        }

        #sellerContent > main {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
        }

        #sariSellerInstantSnapshot {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            background: #F4F5F7 !important;
        }

        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none !important;
            mix-blend-mode: normal !important;
            background: #F4F5F7 !important;
        }
    

}



        /* ============================================================
           UNIFIED HEADER NOTIFICATIONS
           Bell is visually icon-only; dropdown is the elevated surface.
           ============================================================ */
        #sellerNotificationBell,
        #sellerNotificationBell:hover,
        #sellerNotificationBell:active {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            transform: none !important;
        }

        #sellerNotificationBell {
            color: #475467 !important;
        }

        #sellerNotificationBell:hover,
        #sellerNotificationBell:focus-visible {
            color: #C9890B !important;
        }

        #sellerNotificationBell:focus-visible {
            outline: 2px solid rgba(213, 150, 23, .20) !important;
            outline-offset: 2px !important;
        }

        .seller-shell-notification-item {
            border-bottom: 1px solid #EEF0F3;
            background: #FFFFFF;
        }

        .seller-shell-notification-item[data-unread="true"] {
            background: #FFFCF5;
        }

        .seller-shell-notification-item:hover {
            background: #F8FAFC !important;
        }

        .seller-shell-notification-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            place-items: center;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            color: #B97805;
            box-shadow: none !important;
        }

        .seller-shell-notification-icon svg {
            width: 15px;
            height: 15px;
        }

        #sellerNotificationMarkAllRead[disabled] {
            cursor: default;
            opacity: .45;
        }

</style>

    <title>@yield('title', 'SARI Seller')</title>

    @php
        /*
         * Fast seller shell assets:
         * Use normal cacheable asset URLs instead of reading and base64-encoding
         * both logo files on every seller page request.
         */
        $sellerFullLogoSrc = asset('images/sari-logo.png');
        $sellerCompactLogoSrc = asset('images/sari-main-logo.png');
    @endphp

    {{-- Restore desktop compact state before first paint. No logo JavaScript is needed. --}}
    <script>
        (function () {
            try {
                if (
                    window.matchMedia('(min-width: 1024px)').matches &&
                    window.localStorage.getItem('sari:seller-sidebar-collapsed') === '1'
                ) {
                    document.documentElement.classList.add('seller-sidebar-collapsed');
                }
            } catch (error) {}
        })();
    </script>

    {{-- Faster first paint: compiled Tailwind CSS instead of the browser CDN compiler. --}}
    <link rel="preload" as="image" href="{{ asset('images/sari-logo.png') }}" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/sari-main-logo.png') }}">
    <link rel="preload" as="script" href="{{ asset('js/seller-shell.js') }}?v=20261007-3">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Seller loads compiled CSS + seller-only realtime JS. --}}
    @vite(['resources/css/app.css', 'resources/js/seller.js'])

    {{-- Livewire Navigate / @persist support --}}
    @livewireStyles

    <link rel="stylesheet" href="{{ asset('css/seller/layout.css') }}?v=20261007-1">

    @stack('styles')

</head>

@php
    /*
    |--------------------------------------------------------------------------
    | SELLER LAYOUT REQUEST CONTEXT
    |--------------------------------------------------------------------------
    |
    | The Seller middleware already resolves and refreshes the account once.
    | Reuse that same model here instead of querying SellerAccount again.
    |
    | Message counters/history are loaded asynchronously after first paint by
    | seller.layout-state, so the sidebar/logo never waits for ChatMessage SQL.
    |
    */

    $sellerLayoutAccount = request()->attributes->get('sellerAccount');

    if (
        !$sellerLayoutAccount
        && session('is_seller')
        && session('seller_account_id')
    ) {
        /*
        | Safe fallback for any Seller view that is intentionally rendered
        | without the normal Seller middleware.
        */
        $sellerLayoutAccount = \App\Models\Accounts\SellerAccount::find(
            session('seller_account_id')
        );
    }

    /*
     * Shared Seller shell profile photo.
     *
     * SellerAccountController already owns the secure profile-photo response.
     * Use the controller action URL directly so the sidebar/header always read
     * from the same persisted source as Account Management.
     *
     * The updated_at query value prevents a replaced image from being stuck in
     * the browser cache after the seller saves Personal Account changes.
     */
    $sellerShellProfilePhotoUrl = null;

    if ($sellerLayoutAccount) {
        try {
            $sellerShellProfilePhotoUrl = action([
                \App\Http\Controllers\Seller\SellerAccountController::class,
                'profilePhoto',
            ]);

            $sellerShellProfilePhotoVersion = optional(
                $sellerLayoutAccount->updated_at
            )->timestamp ?: 0;

            $sellerShellProfilePhotoUrl .= (
                str_contains($sellerShellProfilePhotoUrl, '?') ? '&' : '?'
            ) . 'v=' . $sellerShellProfilePhotoVersion;
        } catch (\Throwable $error) {
            /*
             * Keep the shell resilient if the profile-photo route is not
             * registered in an unusual test/fallback context.
             * The initials fallback remains available.
             */
            $sellerShellProfilePhotoUrl = null;
        }
    }

    $sellerShellProfileInitials = strtoupper(
        mb_substr(
            trim((string) ($sellerLayoutAccount?->store_name ?: 'SARI Seller')),
            0,
            2
        )
    ) ?: 'SS';

    $sellerUnreadMessages = 0;
    $sellerRecentAdminMessages = collect();

    $sellerActiveSuspension = false;

    if ($sellerLayoutAccount && $sellerLayoutAccount->suspended_until) {
        $sellerActiveSuspension = now()->lt(
            \Illuminate\Support\Carbon::parse(
                $sellerLayoutAccount->suspended_until
            )
        );
    }

    $sellerCriticalLocked = $sellerLayoutAccount
        && (
            (int) ($sellerLayoutAccount->warning_count ?? 0) >= 3
            || $sellerActiveSuspension
        );

    $sellerManualSuspension = $sellerCriticalLocked
        && $sellerActiveSuspension
        && (int) ($sellerLayoutAccount->warning_count ?? 0) < 3;
@endphp

<body data-no-progress-bar class="m-0 min-h-screen bg-[#F4F5F7] font-poppins text-[#1f1b16] antialiased {{ $sellerCriticalLocked ? 'overflow-hidden' : '' }}">



    {{-- =========================================================
        CRITICAL SELLER LOCK — 3 WARNINGS / ACTIVE SUSPENSION
        This overlay has no close button by design.
    ========================================================== --}}
    @if ($sellerCriticalLocked)
        <div
            id="sellerCriticalRestriction"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#2a1717]/55 p-4 backdrop-blur-[5px]"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="sellerCriticalRestrictionTitle"
        >
            <div class="w-full max-w-[540px] overflow-hidden rounded-[24px] border border-[#efcaca] bg-white shadow-[0_35px_110px_rgba(72,25,25,.34)]">
                <div class="border-b border-[#f1d9d9] bg-[#fff5f5] px-6 py-5 sm:px-7">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-[#efcccc] bg-white text-[#c55353]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M12 3 3 20h18L12 3Z"></path>
                                <path d="M12 9v5"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <span class="inline-flex rounded-full border border-[#efcccc] bg-white px-2.5 py-1 text-[8px] font-bold uppercase tracking-[.12em] text-[#b95151]">
                                {{ $sellerManualSuspension ? 'Administrator Suspension' : 'Account Restricted' }}
                            </span>

                            <h2 id="sellerCriticalRestrictionTitle" class="mt-2.5 text-[20px] font-bold tracking-[-0.03em] text-[#4d2929]">
                                Selling Access Temporarily Restricted
                            </h2>

                            <p class="mt-2 text-[10px] leading-5 text-[#876565]">
                                @if ($sellerManualSuspension)
                                    Your seller account was <strong class="text-[#a94d4d]">manually suspended by the SARI Administrator</strong>.
                                    Seller tools and page navigation are temporarily locked until the suspension is lifted or the restriction period ends.
                                @else
                                    Your seller account has reached <strong class="text-[#a94d4d]">{{ (int) $sellerLayoutAccount->warning_count }} / 3 compliance warnings</strong>.
                                    Seller tools and page navigation are temporarily locked. Please message the administrator to request a review.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 sm:px-7 sm:py-6">
                    @if ($sellerLayoutAccount->suspended_until)
                        <div class="mb-4 rounded-[14px] border border-[#efd9d9] bg-[#fff9f9] px-4 py-3">
                            <p class="text-[8px] uppercase tracking-[.08em] text-[#a68181]">Restriction period</p>
                            <p class="mt-1 text-[10px] font-semibold text-[#704747]">
                                Until {{ $sellerLayoutAccount->suspended_until->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 rounded-[13px] border border-[#cfe3d7] bg-[#f5faf7] px-4 py-3 text-[9px] font-medium text-[#4f7c60]">
                            Your message was sent to the administrator.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seller.compliance.message') }}">
                        @csrf

                        <label for="sellerRestrictionMessage" class="block text-[9px] font-semibold text-[#5c4141]">
                            Message Administrator
                        </label>

                        <textarea
                            id="sellerRestrictionMessage"
                            name="message"
                            rows="4"
                            required
                            autofocus
                            placeholder="Explain your concern or request an account review..."
                            class="mt-2 w-full resize-none rounded-[14px] border border-[#e8d2d2] bg-[#fffafa] px-4 py-3 text-[10px] leading-5 text-[#514242] outline-none transition placeholder:text-[#b59a9a] focus:border-[#cf7b7b] focus:ring-4 focus:ring-[#cf7b7b]/10"
                        ></textarea>

                        <button
                            type="submit"
                            class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#b95555] px-5 text-[10px] font-semibold text-white transition hover:bg-[#a54848]"
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                            </svg>
                            Message Administrator
                        </button>
                    </form>

                    <p class="mt-4 text-center text-[8px] leading-4 text-[#a58e8e]">
                        Access will remain locked until the account restriction is cleared by the administrator or the suspension period ends.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================
        SELLER SIDEBAR
    ========================================================== --}}
    @persist('seller-sidebar')
    <aside
        id="sellerSidebar"
        class="
            fixed inset-y-0 left-0 z-50
            flex w-[238px] -translate-x-full flex-col
            border-r border-[#eee4d3]
            bg-white
            transition-all duration-300 ease-out
            lg:translate-x-0
        "
    >
        {{-- LOGO --}}
        <div id="sellerSidebarBrand" class="relative flex min-h-[110px] items-center justify-center border-b border-[#eee4d3] px-4">
            <a
                id="sellerSidebarLogo"
                href="{{ route('seller.dashboard') }}"
                data-sari-navigation-style="{{ asset('css/seller/dashboard.css') }}?v=20261007-1"
                class="relative flex h-[64px] w-full items-center justify-center"
                aria-label="SARI Seller Dashboard"
                wire:navigate.hover
            >
                {{-- Same full logo rendering as the Admin sidebar. --}}
                <img
                    id="sellerSidebarFullLogo"
                    src="{{ $sellerFullLogoSrc }}"
                    alt="SARI"
                    width="145"
                    height="52"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    style="width:145px;height:auto;object-fit:contain;filter:brightness(0);"
                    class="h-auto w-[145px] object-contain brightness-0"
                >

                {{-- Compact mark. It is already in the DOM and is shown only in compact mode. --}}
                <img
                    id="sellerSidebarCompactLogo"
                    src="{{ $sellerCompactLogoSrc }}"
                    alt="SARI"
                    width="52"
                    height="52"
                    loading="eager"
                    decoding="async"
                    style="width:52px;height:52px;object-fit:contain;"
                    class="h-[52px] w-[52px] object-contain"
                >
            </a>

            <button
                id="sellerSidebarClose"
                type="button"
                class="
                    absolute right-4 top-1/2
                    grid h-10 w-10 -translate-y-1/2
                    place-items-center rounded-xl
                    text-[#6e6558]
                    transition hover:bg-[#f7eedf] hover:text-[#b97805]
                    lg:hidden
                "
                aria-label="Close seller sidebar"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>
        </div>

        {{-- NAVIGATION --}}
        <nav id="sellerSidebarNav" wire:navigate:scroll class="flex-1 overflow-y-auto">

            <p class="seller-sidebar-section-label">MAIN MENU</p>

            {{-- DASHBOARD --}}
            <a
                href="{{ route('seller.dashboard') }}"
                title="Dashboard Overview"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-semibold transition-all duration-200
                    {{ request()->routeIs('seller.dashboard')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v10h13V10"></path>
                    <path d="M9.5 20v-6h5v6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Dashboard Overview
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">CATALOG</p>

            {{-- PRODUCT MANAGEMENT --}}
            <a
                href="{{ route('seller.products.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/products.css') }}?v=20261007-1"
                data-sari-navigation-script="{{ asset('js/seller-products.js') }}?v=20261007-3"
                title="Product Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.index')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Product Management
                </span>
            </a>

            {{-- ADD PRODUCT --}}
            <a
                href="{{ route('seller.products.create') }}"
                data-sari-navigation-style="{{ asset('css/seller/product-create.css') }}?v=20261007-1"
                title="Add Product"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.create')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                    <path d="M12 11v6"></path>
                    <path d="M9 14h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Add Product
                </span>
            </a>

            {{-- INVENTORY --}}
            <a
                href="{{ route('seller.inventory.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/inventory.css') }}?v=20261007-1"
                title="Inventory"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.inventory.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 5h16v14H4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h8"></path>
                    <path d="M8 17h5"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Inventory
                </span>
            </a>

            {{-- ARCHIVED PRODUCTS --}}
            <a
                href="{{ route('seller.products.archive') }}"
                data-sari-navigation-style="{{ asset('css/seller/archived-products.css') }}?v=20261007-1"
                title="Archived Products"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.archive')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M6 7v12h12V7"></path>
                    <path d="M9 11h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Archived Products
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">ORDERS &amp; FULFILLMENT</p>

            {{-- ORDERS --}}
            <a
                href="{{ route('seller.orders') }}"
                data-sari-navigation-style="{{ asset('css/seller/orders.css') }}?v=20261007-1"
                title="Order Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.orders*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                    <path d="M8 12h8"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Order Management
                </span>
            </a>

            {{-- SHIPPING --}}
            <a
                href="{{ route('seller.shipping.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/shipping.css') }}?v=20261007-1"
                title="Shipping Tracking"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.shipping.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 7h11v10H3z"></path>
                    <path d="M14 10h3l4 4v3h-7z"></path>
                    <circle cx="7" cy="18" r="1.8"></circle>
                    <circle cx="18" cy="18" r="1.8"></circle>
                    <path d="M5 11h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Shipping
                </span>
            </a>

            {{-- RETURNS & REFUNDS --}}
            <a
                href="{{ route('seller.returns.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/returns.css') }}?v=20261007-1"
                title="Returns & Refunds"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.returns.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 7H5v4"></path>
                    <path d="M5 11c1.8-4.5 8-6.2 12-2.8 4.1 3.5 2.4 10.2-2.8 11.2-3.1.6-6.1-.8-7.7-3.2"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Returns &amp; Refunds
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">MARKETING</p>

            {{-- PROMOTIONS & VOUCHERS --}}
            <a
                href="{{ route('seller.vouchers.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/vouchers.css') }}?v=20261007-1"
                title="Promotions & Vouchers"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.vouchers.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 12 12 3h7v7l-9 9-7-7Z"></path>
                    <circle cx="16" cy="7" r="1"></circle>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Promotions &amp; Vouchers
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">BUSINESS</p>

            {{-- FINANCE & EARNINGS --}}
            <a
                href="{{ route('seller.finance.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/finance.css') }}?v=20261007-1"
                title="Finance & Earnings"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.finance.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19h16"></path>
                    <path d="M6 16v-5"></path>
                    <path d="M12 16V6"></path>
                    <path d="M18 16V9"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Finance &amp; Earnings
                </span>
            </a>

            {{-- REVIEWS & RATINGS --}}
            <a
                href="{{ route('seller.reviews-center.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/reviews-center.css') }}?v=20261007-1"
                title="Reviews & Ratings"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.reviews-center.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Reviews &amp; Ratings
                </span>
            </a>

            {{-- REPORTS --}}
            <a
                href="{{ route('seller.reports') }}"
                data-sari-navigation-style="{{ asset('css/seller/reports.css') }}?v=20261007-1"
                title="Generate Report"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.reports*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 3h10l4 4v14H5z"></path>
                    <path d="M15 3v5h5"></path>
                    <path d="M9 13h6"></path>
                    <path d="M9 17h4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Generate Report
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">SUPPORT</p>

            {{-- CHAT / MESSAGING --}}
            <a
                href="{{ route('seller.messages') }}"
                data-sari-navigation-style="{{ asset('css/seller/messages.css') }}?v=20261007-1"
                title="Chat / Messaging"
                data-seller-sidebar-item
                class="
                    flex items-center justify-between gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.messages*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>

                    <span class="seller-sidebar-label whitespace-nowrap">
                        Chat / Messaging
                    </span>
                </span>

                {{-- REAL-TIME SIDEBAR UNREAD BADGE --}}
                <span
                    id="sellerSidebarMessageBadge"
                    data-seller-unread-badge
                    data-has-unread="{{ $sellerUnreadMessages > 0 ? 'true' : 'false' }}"
                    class="
                        seller-sidebar-extra
                        {{ $sellerUnreadMessages > 0 ? 'grid' : 'hidden' }}
                        h-5 min-w-[20px]
                        place-items-center rounded-full
                        px-1.5 text-[9px] font-bold
                        {{ request()->routeIs('seller.messages*')
                            ? 'bg-white text-[#d9930a]'
                            : 'bg-[#d9930a] text-white' }}
                    "
                >
                    {{ $sellerUnreadMessages > 99 ? '99+' : $sellerUnreadMessages }}
                </span>
            </a>

            {{-- NOTIFICATIONS --}}
            <a
                href="{{ route('seller.notifications.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/notifications.css') }}?v=20261007-1"
                title="Notifications"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.notifications.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Notifications
                </span>
            </a>

            {{-- COMPLIANCE CENTER --}}
            <a
                href="{{ route('seller.compliance-center.index') }}"
                title="Compliance Center"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.compliance-center.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 5 6v5c0 4.5 2.8 7.8 7 10 4.2-2.2 7-5.5 7-10V6l-7-3Z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Compliance Center
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">ACCOUNT</p>

            {{-- STORE MANAGEMENT --}}
            <a
                href="{{ route('seller.store.index') }}"
                data-sari-navigation-style="{{ asset('css/seller/store.css') }}?v=20261007-1"
                title="Store Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.store.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 9h16l-1-5H5L4 9Z"></path>
                    <path d="M5 9v11h14V9"></path>
                    <path d="M9 20v-6h6v6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Store Management
                </span>
            </a>

            {{-- ACCOUNT --}}
            <a
                href="{{ route('seller.account') }}"
                data-sari-navigation-style="{{ asset('css/seller/account.css') }}?v=20261007-1"
                title="Account Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.account')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Account Management
                </span>
            </a>

            {{-- LOGOUT --}}
            <form
                id="sellerLogoutForm"
                method="POST"
                action="{{ route('seller.logout') }}"
                data-seller-logout-form
            >
                @csrf

                <button
                    type="submit"
                    title="Logout"
                    data-seller-sidebar-item
                    class="
                        flex w-full items-center gap-3 rounded-xl px-4 py-3.5
                        text-[13px] font-medium text-[#514b42]
                        transition-all duration-200
                        hover:bg-red-50 hover:text-red-600
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>

                    <span class="seller-sidebar-label whitespace-nowrap">
                        Logout
                    </span>
                </button>
            </form>

        </nav>

        {{-- PROFILE --}}
        <div
            id="sellerSidebarProfile"
            class="border-t border-[#eee4d3] bg-white p-4 transition-all duration-300"
        >
            <a
                href="{{ route('seller.account') }}"
                title="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}"
                data-seller-sidebar-item
                class="
                    flex w-full items-center gap-3 rounded-2xl
                    border border-[#eadfca] bg-white p-3.5
                    transition hover:border-[#dbc396] hover:shadow-sm
                "
                wire:navigate.hover
            >
                <div
                    id="sellerSidebarProfileAvatar"
                    class="seller-shell-profile-avatar grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[12px] font-semibold text-white"
                    data-profile-photo-url="{{ $sellerShellProfilePhotoUrl }}"
                >
                    <img
                        class="seller-shell-profile-avatar__image"
                        src="{{ $sellerShellProfilePhotoUrl ?: '' }}"
                        alt="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }} profile photo"
                        loading="eager"
                        decoding="async"
                        @if(!$sellerShellProfilePhotoUrl) hidden @endif
                    >
                    <span
                        class="seller-shell-profile-avatar__fallback"
                        @if($sellerShellProfilePhotoUrl) hidden @endif
                    >
                        {{ $sellerShellProfileInitials }}
                    </span>
                </div>

                <div class="seller-sidebar-label min-w-0 flex-1">
                    <p class="truncate text-[12px] font-semibold text-[#211d17]">
                        {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                    </p>

                    <p id="sellerSidebarStoreStatus" class="mt-0.5 truncate text-[9px] text-[#8d8272]">
                        {{ $sellerActiveSuspension ? 'Suspended Store' : 'Verified Store' }}
                    </p>
                </div>

                <svg
                    viewBox="0 0 24 24"
                    class="seller-sidebar-extra h-4 w-4 shrink-0 text-[#82796b]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>
    </aside>
    @endpersist


    {{-- =========================================================
        MAIN CONTENT WRAPPER
    ========================================================== --}}
    <div id="sellerContent" class="min-h-screen transition-all duration-300 ease-out">

        {{-- =====================================================
            SELLER HEADER
        ====================================================== --}}
        <header class="sticky top-0 z-40 border-b border-[#eee4d3] bg-white">
            <div class="flex min-h-[86px] w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 xl:px-10">

                {{-- LEFT --}}
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">

                    {{-- MOBILE MENU --}}
                    <button
                        id="sellerMenuButton"
                        type="button"
                        class="
                            grid h-11 w-11 shrink-0 place-items-center
                            rounded-xl border border-[#e8dfd0] bg-white
                            text-[#4f473c] shadow-sm transition
                            hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                            lg:hidden
                        "
                        aria-label="Open seller sidebar"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>
                    </button>

                    {{-- DESKTOP COLLAPSE --}}
                    <button
                        id="sellerSidebarToggle"
                        type="button"
                        class="
                            hidden h-11 w-11 shrink-0 place-items-center
                            rounded-xl border border-[#e8dfd0] bg-white
                            text-[#4f473c] shadow-sm transition-all duration-200
                            hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                            active:scale-95 lg:grid
                        "
                        aria-label="Collapse sidebar"
                        aria-expanded="true"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>
                    </button>

                    {{-- PAGE TITLE --}}
                    <div class="min-w-0">
                        <h1 class="truncate text-[18px] font-bold tracking-[-0.025em] text-[#17140e] sm:text-[20px] xl:text-[22px]">
                            @yield('page-title', 'Seller Dashboard')
                        </h1>

                        <div class="mt-1 hidden items-center gap-1.5 text-[11px] text-[#978d7d] sm:flex">
                            <a
                                href="{{ route('seller.dashboard') }}"
                                class="transition hover:text-[#b97805]"
                wire:navigate.hover
            >
                                Seller
                            </a>

                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>

                            <span class="font-medium text-[#5c5448]">
                                @yield('page-title', 'Dashboard')
                            </span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- SEARCH --}}
                    <div class="relative hidden md:block">
                        <svg
                            viewBox="0 0 24 24"
                            class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#9a9184]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            type="search"
                            placeholder="Search orders, products..."
                            class="
                                h-11 w-[220px] rounded-[14px]
                                border border-[#e6ddcf] bg-white
                                pl-11 pr-4 text-[11px] outline-none
                                placeholder:text-[#a89f92]
                                focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10
                                xl:w-[300px]
                            "
                        >
                    </div>

                    {{-- UNIFIED REAL-TIME NOTIFICATION CENTER --}}
                    <div class="relative">
                        <button
                            id="sellerNotificationBell"
                            type="button"
                            aria-label="Open notifications"
                            aria-expanded="false"
                            class="
                                relative grid h-11 w-11 place-items-center
                                border-0 bg-transparent
                                text-[#475467] transition-colors
                                hover:text-[#c9890b]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>

                            <span
                                id="sellerBellNotificationBadge"
                                data-seller-notification-badge
                                data-has-unread="false"
                                class="
                                    absolute -right-1 -top-1 hidden
                                    h-[19px] min-w-[19px]
                                    place-items-center rounded-full
                                    border-2 border-white
                                    bg-[#d9930a]
                                    px-1 text-[8px] font-bold text-white
                                "
                            >0</span>
                        </button>

                        <div
                            id="sellerNotificationDropdown"
                            class="
                                absolute right-0 top-[54px] z-[80]
                                hidden w-[350px] overflow-hidden
                                rounded-[18px] border border-[#e5e7eb]
                                bg-white shadow-[0_18px_50px_rgba(15,23,42,0.16)]
                                sm:w-[390px]
                            "
                        >
                            <div class="flex items-start justify-between gap-4 border-b border-[#eceff2] px-4 py-3.5">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-[#202124]">
                                        Notifications
                                    </p>
                                    <p id="sellerNotificationUnreadText" class="mt-0.5 text-[8px] text-[#8a919b]">
                                        No unread notifications
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-3">
                                    <button
                                        id="sellerNotificationMarkAllRead"
                                        type="button"
                                        class="text-[8px] font-semibold text-[#8a6a2d] transition-colors hover:text-[#b97805]"
                                    >
                                        Mark all read
                                    </button>

                                    <a
                                        href="{{ route('seller.notifications.index') }}"
                                        class="text-[8px] font-semibold text-[#a66f13] transition-colors hover:text-[#7e5007]"
                                        wire:navigate.hover
                                    >
                                        View all
                                    </a>
                                </div>
                            </div>

                            <div
                                id="sellerNotificationList"
                                data-seller-notification-scroll
                                class="max-h-[390px] overflow-y-auto"
                            >
                                <div id="sellerNotificationEmpty" class="px-5 py-9 text-center">
                                    <div class="mx-auto text-[#b47e1e]">
                                        <svg viewBox="0 0 24 24" class="mx-auto h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                            <path d="M10 21h4"></path>
                                        </svg>
                                    </div>

                                    <p class="mt-3 text-[9px] font-semibold text-[#475467]">
                                        No notifications yet
                                    </p>

                                    <p class="mx-auto mt-1 max-w-[250px] text-[8px] leading-4 text-[#98a2b3]">
                                        Buyer/Admin messages, orders, shipping, reviews, returns,
                                        inventory and account activity will appear here.
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('seller.notifications.index') }}"
                                class="
                                    flex items-center justify-center
                                    border-t border-[#eceff2]
                                    bg-[#fafbfc] px-4 py-3
                                    text-[9px] font-semibold text-[#9a6817]
                                    transition-colors hover:bg-[#f8fafc]
                                "
                                wire:navigate.hover
                            >
                                Open Notification Center
                            </a>
                        </div>
                    </div>

                    <div class="hidden h-9 w-px bg-[#eee4d5] xl:block"></div>

                    {{-- ACCOUNT SHORTCUT --}}
                    <a
                        href="{{ route('seller.account') }}"
                        class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex"
                wire:navigate.hover
            >
                        <div
                            id="sellerHeaderProfileAvatar"
                            class="seller-shell-profile-avatar grid h-10 w-10 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white"
                            data-profile-photo-url="{{ $sellerShellProfilePhotoUrl }}"
                        >
                            <img
                                class="seller-shell-profile-avatar__image"
                                src="{{ $sellerShellProfilePhotoUrl ?: '' }}"
                                alt="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }} profile photo"
                                loading="eager"
                                decoding="async"
                                @if(!$sellerShellProfilePhotoUrl) hidden @endif
                            >
                            <span
                                class="seller-shell-profile-avatar__fallback"
                                @if($sellerShellProfilePhotoUrl) hidden @endif
                            >
                                {{ $sellerShellProfileInitials }}
                            </span>
                        </div>

                        <div>
                            <p class="max-w-[160px] truncate text-[11px] font-semibold text-[#28231c]">
                                {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                            </p>

                            <p class="mt-0.5 text-[9px] text-[#908779]">
                                {{ $sellerActiveSuspension ? 'Suspended Store' : 'Verified Store' }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="w-full px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 xl:px-10">
            @yield('content')
        </main>
    </div>



    {{-- =========================================================
        GLOBAL SARI SELLER AI ASSISTANT
        Persistent across Seller Livewire navigation.
    ========================================================== --}}
    @persist('seller-ai-assistant')
        @include('seller.partials.ai-assistant', [
            'sellerAiAccount' => $sellerLayoutAccount,
        ])
    @endpersist

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        id="sellerSidebarOverlay"
        class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-[1px] lg:hidden"
    ></div>


    {{-- =========================================================
        SELLER LOGOUT CONFIRMATION
        Global modal used by the existing seller.logout POST form.
    ========================================================== --}}
    <div
        id="sellerLogoutConfirmModal"
        hidden
        aria-hidden="true"
    >
        <div
            class="seller-logout-modal-backdrop"
            data-seller-logout-cancel
            aria-hidden="true"
        ></div>

        <section
            class="seller-logout-modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerLogoutModalTitle"
            aria-describedby="sellerLogoutModalDescription"
        >
            <div class="seller-logout-modal-body">
                <div class="seller-logout-modal-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>
                </div>

                <h2
                    id="sellerLogoutModalTitle"
                    class="seller-logout-modal-title"
                >
                    Log out of SARI?
                </h2>

                <p
                    id="sellerLogoutModalDescription"
                    class="seller-logout-modal-copy"
                >
                    Are you sure you want to end your Seller session?
                    You will need to sign in again to access Seller Center.
                </p>
            </div>

            <div class="seller-logout-modal-footer">
                <button
                    type="button"
                    class="seller-logout-modal-btn seller-logout-modal-btn-cancel"
                    data-seller-logout-cancel
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="sellerLogoutConfirmButton"
                    class="seller-logout-modal-btn seller-logout-modal-btn-danger"
                    data-seller-logout-confirm
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>
                    <span data-seller-logout-confirm-label>Logout</span>
                </button>
            </div>
        </section>
    </div>


    {{-- REAL-TIME MESSAGE TOAST --}}
    <div
        id="sellerMessageToast"
        class="
            pointer-events-none fixed right-4 top-[100px] z-[100]
            hidden w-[calc(100%-2rem)] max-w-[350px]
            translate-y-2 rounded-[16px]
            border border-[#e8ddca] bg-white
            p-4 opacity-0
            shadow-[0_18px_45px_rgba(55,43,25,0.16)]
            transition-all duration-300
            sm:right-6
        "
    >
        <div class="flex gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[10px] font-bold text-[#a8731f]">
                SA
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[9px] font-bold text-[#3d362d]">
                        New message from Admin
                    </p>

                    <span class="h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>
                </div>

                <p
                    id="sellerMessageToastText"
                    class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#7f766a]"
                ></p>

                <a
                    href="{{ route('seller.messages') }}"
                    class="pointer-events-auto mt-2 inline-flex text-[8px] font-semibold text-[#9e6811] hover:text-[#734803]"
                wire:navigate.hover
            >
                    Open message →
                </a>
            </div>
        </div>
    </div>



    {{-- SELLER ORDER STATUS TOAST --}}
    @if ($sellerLayoutAccount)
        <div id="sellerOrderStatusToast" class="pointer-events-none fixed right-4 top-[100px] z-[170] hidden w-[calc(100%-2rem)] max-w-[390px] translate-y-2 rounded-[18px] border border-[#d4e1eb] bg-white p-4 opacity-0 shadow-[0_18px_50px_rgba(50,66,82,.16)] transition-all duration-300 sm:right-6">
            <div class="flex gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#f1f7fb] text-[#3C6E91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h11v10H3z"></path><path d="M14 10h4l3 3v4h-7z"></path></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p id="sellerOrderToastTitle" class="text-[10px] font-bold text-[#302920]">Order Update</p>
                    <p id="sellerOrderToastMessage" class="mt-1 text-[8px] leading-4 text-[#786f64]"></p>
                    <a href="{{ route('seller.orders') }}" class="pointer-events-auto mt-2 inline-flex text-[8px] font-semibold text-[#3C6E91] hover:text-[#2e5b79]"
                wire:navigate.hover
            >Open Order Management →</a>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================
        PERSISTENT SELLER SHELL
        Sidebar is preserved by Livewire @persist.
        All shared listeners are registered once and work across navigation.
    ========================================================== --}}
    <script data-navigate-once>
        window.__SARI_SELLER_SHELL_CONFIG__ = {
            sellerChannel: @json(
                $sellerLayoutAccount && $sellerLayoutAccount->realtime_token
                    ? 'sari.seller.' . $sellerLayoutAccount->realtime_token
                    : null
            ),
            sellerId: @json((int) ($sellerLayoutAccount?->id ?? 0)),
            dashboardUrl: @json(route('seller.dashboard')),
            ordersUrl: @json(route('seller.orders')),
            shippingUrl: @json(route('seller.shipping.index')),
            messagesUrl: @json(route('seller.messages')),
            loginUrl: @json(route('login')),
            accountStateUrl: @json(route('seller.account-state')),
            layoutStateUrl: @json(route('seller.layout-state')),
            notificationIndexUrl: @json(route('seller.notifications.index')),
            notificationReadAllUrl: @json(route('seller.notifications.read-all')),
            buyerMessagesUrl: @json(route('seller.buyer-messages')),
            ordersLiveStateUrl: @json(route('seller.orders.live-state')),
            csrfToken: @json(csrf_token()),
        };
    </script>
    <script src="{{ asset('js/seller-shell.js') }}?v=20261007-3" data-navigate-once></script>



    {{-- =========================================================
        PERSISTENT SELLER ACTION TOAST
        Stays mounted while Livewire refreshes only page content.
    ========================================================== --}}
    @persist('seller-action-toast')
        <div
            id="sellerActionToast"
            class="seller-action-toast hidden"
            data-toast-type="success"
            aria-live="polite"
            aria-atomic="true"
        >
            <div id="sellerActionToastCard" class="seller-action-toast-card">
                <span id="sellerActionToastIcon" class="seller-action-toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

                <div class="seller-action-toast-copy">
                    <p id="sellerActionToastTitle" class="seller-action-toast-title">
                        Action completed
                    </p>
                    <p id="sellerActionToastMessage" class="seller-action-toast-message"></p>
                </div>

                <button
                    id="sellerActionToastClose"
                    type="button"
                    class="seller-action-toast-close"
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


    {{-- =========================================================
        FAST SELLER PRODUCT ACTION BRIDGE
        Product writes use fetch() so the persistent Seller shell/logo
        never tears down while Add/Edit/Archive/Delete/Restore runs.
    ========================================================== --}}
    <script>
        (function () {
            if (window.__SARI_SELLER_FAST_PRODUCT_ACTIONS__) {
                return;
            }

            window.__SARI_SELLER_FAST_PRODUCT_ACTIONS__ = true;

            let toastTimer = null;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('sellerActionToast');
                const icon = document.getElementById('sellerActionToastIcon');
                const title = document.getElementById('sellerActionToastTitle');
                const body = document.getElementById('sellerActionToastMessage');

                if (!toast || !icon || !title || !body) {
                    return;
                }

                const warning = type === 'warning';
                const error = type === 'error';

                toast.dataset.toastType =
                    error ? 'error' : (warning ? 'warning' : 'success');

                if (error) {
                    title.textContent = 'Action not completed';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16.5h.01"></path></svg>';
                } else if (warning) {
                    title.textContent = 'Review notice';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>';
                } else {
                    title.textContent = 'Action completed';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>';
                }

                body.textContent =
                    message || 'Your seller action was saved.';

                toast.classList.remove('hidden');

                if (toastTimer) {
                    window.clearTimeout(toastTimer);
                }

                toastTimer = window.setTimeout(function () {
                    toast.classList.add('hidden');
                }, error ? 6500 : (warning ? 5600 : 4400));
            }

            function bindToastClose() {
                const close = document.getElementById('sellerActionToastClose');

                if (!close || close.dataset.sariBound === '1') {
                    return;
                }

                close.dataset.sariBound = '1';
                close.addEventListener('click', function () {
                    document.getElementById('sellerActionToast')?.classList.add('hidden');
                });
            }

            function isFastProductAction(form) {
                if (!(form instanceof HTMLFormElement)) return false;
                if (form.hasAttribute('data-no-seller-fast-action')) return false;

                const method = String(form.getAttribute('method') || 'get').toLowerCase();
                if (method !== 'post') return false;

                const action = new URL(form.action || window.location.href, window.location.origin);
                if (action.origin !== window.location.origin) return false;

                const path = action.pathname.replace(/\/+$/, '') || '/';

                if (path === '/seller/products') {
                    return true;
                }

                return /^\/seller\/products\/\d+(?:\/(?:archive|delete|restore))?$/.test(path);
            }

            function firstError(payload) {
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

                return String(payload?.message || 'The action could not be completed.');
            }

            function busyTextFor(form) {
                if (form.id === 'sellerAddProductForm' || form.id === 'sellerCreateProductForm') return 'Submitting...';
                if (form.id === 'sellerEditProductForm') return 'Saving changes...';
                if (form.id === 'productActionForm') return 'Processing...';
                return 'Saving...';
            }

            function setButtonBusy(button, form, busy) {
                if (!button) return;

                if (busy) {
                    if (!button.dataset.sariOriginalHtml) {
                        button.dataset.sariOriginalHtml = button.innerHTML;
                    }

                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');
                    button.classList.add('cursor-wait', 'opacity-70');
                    button.innerHTML = '<span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-r-transparent"></span>' + busyTextFor(form) + '</span>';
                    return;
                }

                button.disabled = false;
                button.removeAttribute('aria-busy');
                button.classList.remove('cursor-wait', 'opacity-70');

                if (button.dataset.sariOriginalHtml) {
                    button.innerHTML = button.dataset.sariOriginalHtml;
                    delete button.dataset.sariOriginalHtml;
                }
            }

            function closeProductModals() {
                [
                    'sellerAddProductModal',
                    'sellerEditProductModal',
                    'sellerProductActionModal',
                    'sellerAllProductsModal'
                ].forEach(function (id) {
                    const modal = document.getElementById(id);
                    if (!modal) return;

                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'seller-modal-visible');
                });

                document.body.classList.remove('overflow-hidden');
            }

            function reviewSubmitFeedback(form) {
                if (!form || form.id !== 'sellerCreateProductForm') {
                    return null;
                }

                return window.__SARI_PRODUCT_REVIEW_FEEDBACK__ || null;
            }

            async function submitFast(form, submitter) {
                if (form.dataset.sariSubmitting === '1') {
                    return;
                }

                form.dataset.sariSubmitting = '1';

                const button = submitter || form.querySelector('button[type="submit"], input[type="submit"]');
                setButtonBusy(button, form, true);

                const reviewFeedback = reviewSubmitFeedback(form);
                reviewFeedback?.start?.();

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.redirected) {
                        const redirected = new URL(response.url, window.location.origin);

                        if (redirected.origin === window.location.origin && redirected.pathname.startsWith('/seller')) {
                            reviewFeedback?.hide?.();
                            showToast('Your seller access or page state changed. Refreshing securely.', 'warning');

                            window.setTimeout(function () {
                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(redirected.toString());
                                } else {
                                    window.location.assign(redirected.toString());
                                }
                            }, 120);

                            return;
                        }

                        window.location.assign(response.url);
                        return;
                    }

                    const contentType = response.headers.get('content-type') || '';
                    let payload = {};

                    if (contentType.includes('application/json')) {
                        payload = await response.json();
                    }

                    if (!response.ok) {
                        reviewFeedback?.hide?.();
                        showToast(firstError(payload), 'error');
                        return;
                    }

                    if (reviewFeedback?.success) {
                        await reviewFeedback.success();
                    }

                    showToast(
                        payload.message || 'Your seller action was saved.',
                        payload.type || 'success'
                    );

                    closeProductModals();

                    window.setTimeout(function () {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(window.location.href);
                        } else {
                            window.location.reload();
                        }
                    }, reviewFeedback ? 700 : 160);
                } catch (error) {
                    reviewFeedback?.hide?.();
                    showToast('Network request failed. Please try again.', 'error');
                } finally {
                    form.dataset.sariSubmitting = '0';
                    setButtonBusy(button, form, false);
                }
            }

            document.addEventListener('submit', function (event) {
                const form = event.target;

                /*
                 * Dashboard validation handlers run on the form itself before
                 * this document-level bubble listener. Respect them first.
                 */
                if (event.defaultPrevented || !isFastProductAction(form)) {
                    return;
                }

                event.preventDefault();
                submitFast(form, event.submitter || null);
            });

            bindToastClose();

            document.addEventListener('livewire:navigated', function () {
                bindToastClose();
            });
        })();
    </script>





    {{-- Neutral-only navigation guard: keeps the persistent Seller canvas stable. --}}
    <script data-navigate-once>
        (function () {
            const SELLER_CANVAS = '#F4F5F7';

            function lockSellerCanvas() {
                document.documentElement.style.backgroundColor = SELLER_CANVAS;

                if (document.body) {
                    document.body.style.backgroundColor = SELLER_CANVAS;
                    document.body.style.backgroundImage = 'none';
                }

                const content = document.getElementById('sellerContent');
                if (content) {
                    content.style.backgroundColor = SELLER_CANVAS;
                    content.style.backgroundImage = 'none';
                }

                document.getElementById('sariSellerInstantSnapshot')?.remove();
            }

            lockSellerCanvas();
            document.addEventListener('livewire:navigate', lockSellerCanvas);
            document.addEventListener('livewire:navigating', lockSellerCanvas);
            document.addEventListener('livewire:navigated', function () {
                lockSellerCanvas();
                window.requestAnimationFrame(lockSellerCanvas);
            });
            window.addEventListener('pageshow', lockSellerCanvas);
        })();
    </script>


    {{-- =========================================================
        SELLER LOGOUT CONFIRMATION CONTROLLER
    ========================================================== --}}
    <script data-navigate-once>
        (function () {
            if (window.__SARI_SELLER_LOGOUT_MODAL_BOUND__) {
                return;
            }

            window.__SARI_SELLER_LOGOUT_MODAL_BOUND__ = true;

            let pendingForm = null;
            let returnFocus = null;
            let previousBodyOverflow = '';

            function modal() {
                return document.getElementById(
                    'sellerLogoutConfirmModal'
                );
            }

            function cancelButton() {
                return modal()?.querySelector(
                    '[data-seller-logout-cancel]'
                );
            }

            function confirmButton() {
                return document.getElementById(
                    'sellerLogoutConfirmButton'
                );
            }

            function openLogoutModal(form, trigger) {
                const element = modal();

                if (!element) {
                    HTMLFormElement.prototype.submit.call(form);
                    return;
                }

                pendingForm = form;
                returnFocus =
                    trigger instanceof HTMLElement
                        ? trigger
                        : document.activeElement;

                previousBodyOverflow =
                    document.body.style.overflow || '';

                document.body.style.overflow = 'hidden';

                element.hidden = false;
                element.setAttribute('aria-hidden', 'false');

                requestAnimationFrame(function () {
                    element.classList.add('is-open');

                    window.setTimeout(function () {
                        cancelButton()?.focus({
                            preventScroll: true
                        });
                    }, 20);
                });
            }

            function closeLogoutModal(restoreFocus = true) {
                const element = modal();

                if (!element || element.hidden) {
                    return;
                }

                element.classList.remove('is-open');
                element.setAttribute('aria-hidden', 'true');

                document.body.style.overflow =
                    previousBodyOverflow;

                window.setTimeout(function () {
                    element.hidden = true;

                    const confirm = confirmButton();
                    const label = confirm?.querySelector(
                        '[data-seller-logout-confirm-label]'
                    );

                    if (confirm) {
                        confirm.disabled = false;
                    }

                    if (label) {
                        label.textContent = 'Logout';
                    }

                    if (
                        restoreFocus
                        && returnFocus instanceof HTMLElement
                    ) {
                        returnFocus.focus({
                            preventScroll: true
                        });
                    }

                    if (restoreFocus) {
                        pendingForm = null;
                        returnFocus = null;
                    }
                }, 150);
            }

            document.addEventListener(
                'submit',
                function (event) {
                    const form = event.target;

                    if (
                        !(form instanceof HTMLFormElement)
                        || !form.matches(
                            '[data-seller-logout-form]'
                        )
                    ) {
                        return;
                    }

                    if (
                        form.dataset.sellerLogoutConfirmed === '1'
                    ) {
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();

                    openLogoutModal(
                        form,
                        event.submitter || document.activeElement
                    );
                },
                true
            );

            document.addEventListener(
                'click',
                function (event) {
                    const cancel = event.target.closest(
                        '[data-seller-logout-cancel]'
                    );

                    if (cancel) {
                        event.preventDefault();
                        closeLogoutModal(true);
                        return;
                    }

                    const confirm = event.target.closest(
                        '[data-seller-logout-confirm]'
                    );

                    if (!confirm) {
                        return;
                    }

                    event.preventDefault();

                    if (
                        !(pendingForm instanceof HTMLFormElement)
                    ) {
                        closeLogoutModal(true);
                        return;
                    }

                    confirm.disabled = true;

                    const label = confirm.querySelector(
                        '[data-seller-logout-confirm-label]'
                    );

                    if (label) {
                        label.textContent = 'Logging out…';
                    }

                    pendingForm.dataset.sellerLogoutConfirmed = '1';

                    /*
                     * Native submit bypasses this confirmation listener
                     * while preserving the existing POST action + CSRF.
                     */
                    HTMLFormElement.prototype.submit.call(
                        pendingForm
                    );
                }
            );

            document.addEventListener(
                'keydown',
                function (event) {
                    const element = modal();

                    if (!element || element.hidden) {
                        return;
                    }

                    if (event.key === 'Escape') {
                        event.preventDefault();
                        closeLogoutModal(true);
                        return;
                    }

                    if (event.key !== 'Tab') {
                        return;
                    }

                    const focusable = Array.from(
                        element.querySelectorAll(
                            'button:not([disabled]), [href], input:not([disabled])'
                        )
                    ).filter(function (node) {
                        return !node.hasAttribute('hidden');
                    });

                    if (!focusable.length) {
                        return;
                    }

                    const first = focusable[0];
                    const last =
                        focusable[focusable.length - 1];

                    if (
                        event.shiftKey
                        && document.activeElement === first
                    ) {
                        event.preventDefault();
                        last.focus();
                    } else if (
                        !event.shiftKey
                        && document.activeElement === last
                    ) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            );

            document.addEventListener(
                'livewire:navigating',
                function () {
                    const element = modal();

                    if (element && !element.hidden) {
                        element.classList.remove('is-open');
                        element.hidden = true;
                        element.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                        document.body.style.overflow =
                            previousBodyOverflow;
                    }

                    pendingForm = null;
                    returnFocus = null;
                }
            );
        })();
    </script>

    {{-- PAGE-SPECIFIC SCRIPTS --}}
    @stack('scripts')

    {{-- Start Livewire after all navigation listeners are registered. --}}
    @livewireScripts
</body>
</html>
