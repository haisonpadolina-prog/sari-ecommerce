<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SARI Seller')</title>

    @php
        /*
        |--------------------------------------------------------------------------
        | ZERO-REQUEST SELLER LOGOS
        |--------------------------------------------------------------------------
        | Embed the two tiny sidebar logo assets directly into the HTML on the
        | first authenticated response. This removes the extra image request
        | that can make the logo visibly "arrive" after a fresh login.
        |
        | Falls back to the normal asset URL if the file cannot be read.
        */
        $sellerFullLogoPath = public_path('images/sari-logo.png');
        $sellerCompactLogoPath = public_path('images/sari-main-logo.png');

        $sellerFullLogoSrc = is_file($sellerFullLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($sellerFullLogoPath))
            : asset('images/sari-logo.png');

        $sellerCompactLogoSrc = is_file($sellerCompactLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($sellerCompactLogoPath))
            : asset('images/sari-main-logo.png');
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

    <style>
        :root {
            --seller-sidebar-expanded: 275px;
            --seller-sidebar-collapsed: 88px;
            --seller-sidebar-width: var(--seller-sidebar-expanded);
            --seller-sidebar-ease: cubic-bezier(.22, 1, .36, 1);
        }

        /*
         * Critical first-paint shell sizing.
         * This prevents the sidebar/logo/header from flashing at the wrong size
         * during a fresh login while the compiled stylesheet is being parsed.
         */
        html,
        body {
            min-height: 100%;
            background: #faf9f6;
        }

        #sellerSidebarFullLogo,
        #sellerSidebarCompactLogo {
            object-fit: contain;
        }


        @media (min-width: 1024px) {
            html.seller-sidebar-collapsed {
                --seller-sidebar-width: var(--seller-sidebar-collapsed);
            }

            #sellerSidebar {
                width: var(--seller-sidebar-width);
                overflow-x: hidden;
                transition: width 220ms var(--seller-sidebar-ease);
            }

            #sellerContent {
                padding-left: var(--seller-sidebar-width);
                transition: padding-left 220ms var(--seller-sidebar-ease);
            }

            #sellerSidebarBrand {
                min-height: 110px;
                transition: min-height 220ms var(--seller-sidebar-ease), padding 220ms var(--seller-sidebar-ease);
            }

            /*
             * Admin-style logo behavior:
             * - no load handlers
             * - no opacity/cross-fade
             * - no waiting for DOMContentLoaded
             * The browser decides which already-present image to paint from the
             * root class that was restored in <head>.
             */
            #sellerSidebarFullLogo {
                display: block;
            }

            #sellerSidebarCompactLogo {
                display: none;
            }

            .seller-sidebar-label {
                display: inline-block;
                max-width: 190px;
                overflow: hidden;
                opacity: 1;
                transform: translateX(0);
                transition: max-width 180ms var(--seller-sidebar-ease), opacity 110ms ease, transform 180ms var(--seller-sidebar-ease);
            }

            .seller-sidebar-extra {
                max-width: 72px;
                overflow: hidden;
                opacity: 1;
                transition: max-width 160ms var(--seller-sidebar-ease), opacity 100ms ease, padding 160ms var(--seller-sidebar-ease);
            }

            [data-seller-sidebar-item] {
                transition: padding 190ms var(--seller-sidebar-ease), gap 190ms var(--seller-sidebar-ease), background-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
            }

            #sellerSidebarNav,
            #sellerSidebarProfile {
                transition: padding 190ms var(--seller-sidebar-ease);
            }

            html.seller-sidebar-collapsed #sellerSidebarBrand {
                min-height: 88px;
                padding-left: 12px;
                padding-right: 12px;
            }

            html.seller-sidebar-collapsed #sellerSidebarFullLogo {
                display: none;
            }

            html.seller-sidebar-collapsed #sellerSidebarCompactLogo {
                display: block;
            }

            html.seller-sidebar-collapsed .seller-sidebar-label {
                max-width: 0;
                opacity: 0;
                transform: translateX(-6px);
                pointer-events: none;
            }

            html.seller-sidebar-collapsed .seller-sidebar-extra {
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0;
                padding-left: 0 !important;
                padding-right: 0 !important;
                opacity: 0;
                pointer-events: none;
            }

            html.seller-sidebar-collapsed [data-seller-sidebar-item] {
                justify-content: center !important;
                gap: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html.seller-sidebar-collapsed [data-seller-sidebar-item] > span:first-child {
                justify-content: center !important;
                gap: 0 !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav {
                padding-left: 12px;
                padding-right: 12px;
            }

            html.seller-sidebar-collapsed #sellerSidebarProfile {
                padding: 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            #sellerSidebar,
            #sellerContent,
            #sellerSidebarBrand,
            .seller-sidebar-label,
            .seller-sidebar-extra,
            [data-seller-sidebar-item] {
                transition-duration: 1ms !important;
            }
        }

        [data-seller-notification-scroll] {
            scrollbar-width: thin;
            scrollbar-color: #d8c7a7 transparent;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar {
            width: 5px;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar-track {
            background: transparent;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar-thumb {
            background: #d8c7a7;
            border-radius: 999px;
        }

        /* Persistent Seller shell */
        #sellerSidebar {
            contain: layout paint;
        }

        [data-seller-shell-loading="true"] #sellerContent main {
            opacity: .72;
            transition: opacity 120ms ease;
        }
    </style>

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
        $sellerLayoutAccount = \App\Models\SellerAccount::find(
            session('seller_account_id')
        );
    }

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

<body class="m-0 min-h-screen bg-[#faf9f6] font-poppins text-[#1f1b16] antialiased {{ $sellerCriticalLocked ? 'overflow-hidden' : '' }}">



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
            flex w-[275px] -translate-x-full flex-col
            border-r border-[#eee4d3]
            bg-[#fffdf8]
            transition-all duration-300 ease-out
            lg:translate-x-0
        "
    >
        {{-- LOGO --}}
        <div id="sellerSidebarBrand" class="relative flex min-h-[110px] items-center justify-center border-b border-[#eee4d3] px-4">
            <a
                id="sellerSidebarLogo"
                href="{{ route('seller.dashboard') }}"
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
                    decoding="sync"
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
                    decoding="sync"
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
        <nav id="sellerSidebarNav" wire:navigate:scroll class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">

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

            {{-- PRODUCTS --}}
            <a
                href="{{ route('seller.products.index') }}"
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

            {{-- ORDERS --}}
            <a
                href="{{ route('seller.orders') }}"
                title="Order Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.orders')
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

            {{-- ARCHIVED PRODUCTS --}}
            <a
                href="{{ route('seller.products.archive') }}"
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

            {{-- REPORTS --}}
            <a
                href="{{ route('seller.reports') }}"
                title="Generate Report"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.reports')
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

            {{-- CHAT / MESSAGING --}}
            <a
                href="{{ route('seller.messages') }}"
                title="Chat / Messaging"
                data-seller-sidebar-item
                class="
                    flex items-center justify-between gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.messages')
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
                        {{ request()->routeIs('seller.messages')
                            ? 'bg-white text-[#d9930a]'
                            : 'bg-[#d9930a] text-white' }}
                    "
                >
                    {{ $sellerUnreadMessages > 99 ? '99+' : $sellerUnreadMessages }}
                </span>
            </a>

            {{-- ACCOUNT --}}
            <a
                href="{{ route('seller.account') }}"
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

            <div class="my-4 border-t border-[#eee4d3]"></div>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('seller.logout') }}">
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
            class="border-t border-[#eee4d3] bg-[#fffdf8] p-4 transition-all duration-300"
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
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[12px] font-semibold text-white">
                    {{ strtoupper(substr($sellerLayoutAccount?->store_name ?: 'SS', 0, 2)) }}
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
        <header class="sticky top-0 z-40 border-b border-[#eee4d3] bg-[#fffdf9]/95 backdrop-blur-md">
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
                wire:navigate
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

                    {{-- REAL-TIME NOTIFICATION BELL --}}
                    <div class="relative">
                        <button
                            id="sellerNotificationBell"
                            type="button"
                            aria-label="Open notifications"
                            aria-expanded="false"
                            class="
                                relative grid h-11 w-11 place-items-center
                                rounded-xl border border-[#e8dfd0] bg-white
                                text-[#443d33] shadow-sm transition
                                hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>

                            <span
                                id="sellerBellMessageBadge"
                                data-seller-unread-badge
                                data-has-unread="{{ $sellerUnreadMessages > 0 ? 'true' : 'false' }}"
                                class="
                                    absolute -right-1.5 -top-1.5
                                    {{ $sellerUnreadMessages > 0 ? 'grid' : 'hidden' }}
                                    h-[21px] min-w-[21px]
                                    place-items-center rounded-full
                                    border-2 border-[#fffdf9]
                                    bg-[#d9930a]
                                    px-1 text-[9px] font-bold text-white
                                "
                            >
                                {{ $sellerUnreadMessages > 99 ? '99+' : $sellerUnreadMessages }}
                            </span>
                        </button>

                        {{-- NOTIFICATION DROPDOWN --}}
                        <div
                            id="sellerNotificationDropdown"
                            class="
                                absolute right-0 top-[54px] z-[80]
                                hidden w-[330px] overflow-hidden
                                rounded-[18px] border border-[#e8dfd0]
                                bg-white shadow-[0_18px_50px_rgba(54,43,28,0.14)]
                                sm:w-[360px]
                            "
                        >
                            <div class="flex items-center justify-between border-b border-[#eee7dc] px-4 py-3.5">
                                <div>
                                    <p class="text-[11px] font-bold text-[#302920]">
                                        Notifications
                                    </p>

                                    <p id="sellerNotificationUnreadText" class="mt-0.5 text-[8px] text-[#91887b]">
                                        {{ $sellerUnreadMessages > 0
                                            ? $sellerUnreadMessages . ' unread message' . ($sellerUnreadMessages === 1 ? '' : 's')
                                            : 'No unread messages' }}
                                    </p>
                                </div>

                                <a
                                    href="{{ route('seller.messages') }}"
                                    class="text-[8px] font-semibold text-[#a66f13] transition hover:text-[#7e5007]"
                wire:navigate
            >
                                    Open inbox
                                </a>
                            </div>

                            <div
                                id="sellerNotificationList"
                                data-seller-notification-scroll
                                class="max-h-[360px] overflow-y-auto"
                            >
                                @forelse ($sellerRecentAdminMessages as $notification)
                                    <a
                                        href="{{ route('seller.messages') }}"
                                        data-notification-message-id="{{ $notification->id }}"
                                        class="
                                            block border-b border-[#f1ece4]
                                            px-4 py-3.5 transition
                                            hover:bg-[#fdf9f2]
                                            {{ $notification->read_by_seller_at ? 'bg-white' : 'bg-[#fffaf1]' }}
                                        "
                wire:navigate
            >
                                        <div class="flex gap-3">
                                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[9px] font-bold text-[#a8731f]">
                                                SA
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-3">
                                                    <p class="text-[9px] font-semibold text-[#3f382f]">
                                                        SARI Admin Support
                                                    </p>

                                                    <span class="shrink-0 text-[7px] text-[#9a9185]">
                                                        {{ $notification->created_at?->format('h:i A') }}
                                                    </span>
                                                </div>

                                                <p class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#786f64]">
                                                    {{ $notification->body ?: ($notification->attachment_name ?: 'Sent an attachment') }}
                                                </p>
                                            </div>

                                            @if (!$notification->read_by_seller_at)
                                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div
                                        id="sellerNotificationEmpty"
                                        class="px-5 py-9 text-center"
                                    >
                                        <div class="mx-auto grid h-11 w-11 place-items-center rounded-2xl bg-[#fbf6ec] text-[#b47e1e]">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                                <path d="M10 21h4"></path>
                                            </svg>
                                        </div>

                                        <p class="mt-3 text-[9px] font-semibold text-[#50483e]">
                                            No notifications yet
                                        </p>

                                        <p class="mt-1 text-[8px] text-[#978e82]">
                                            New Admin messages will appear here.
                                        </p>
                                    </div>
                                @endforelse
                            </div>

                            <a
                                href="{{ route('seller.messages') }}"
                                class="
                                    flex items-center justify-center
                                    border-t border-[#eee7dc]
                                    bg-[#fcfaf6] px-4 py-3
                                    text-[9px] font-semibold text-[#9a6817]
                                    transition hover:bg-[#faf4e8]
                                "
                wire:navigate
            >
                                View all messages
                            </a>
                        </div>
                    </div>

                    <div class="hidden h-9 w-px bg-[#eee4d5] xl:block"></div>

                    {{-- ACCOUNT SHORTCUT --}}
                    <a
                        href="{{ route('seller.account') }}"
                        class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex"
                wire:navigate
            >
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white">
                            {{ strtoupper(substr($sellerLayoutAccount?->store_name ?: 'SS', 0, 2)) }}
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


    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        id="sellerSidebarOverlay"
        class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-[1px] lg:hidden"
    ></div>


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
                wire:navigate
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
                wire:navigate
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
        (function () {
            const sellerChannel = @json(
                $sellerLayoutAccount && $sellerLayoutAccount->realtime_token
                    ? 'sari.seller.' . $sellerLayoutAccount->realtime_token
                    : null
            );

            const sellerId = @json((int) ($sellerLayoutAccount?->id ?? 0));
            const dashboardUrl = @json(route('seller.dashboard'));
            const ordersUrl = @json(route('seller.orders'));
            const messagesUrl = @json(route('seller.messages'));
            const loginUrl = @json(route('login'));
            const accountStateUrl = @json(route('seller.account-state'));
            const layoutStateUrl = @json(route('seller.layout-state'));

            const desktopBreakpoint = 1024;
            const sidebarStorageKey = 'sari:seller-sidebar-collapsed';

            const pathOf = (url) => {
                try {
                    return new URL(url, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                } catch (_) {
                    return '/';
                }
            };

            const currentPath = () =>
                (window.location.pathname.replace(/\/+$/, '') || '/');

            const dashboardPath = pathOf(dashboardUrl);
            const ordersPath = pathOf(ordersUrl);
            const messagesPath = pathOf(messagesUrl);

            window.__SARI_SELLER_SHELL_STATE__ =
                window.__SARI_SELLER_SHELL_STATE__ || {
                    unreadCount: 0,
                    processedMessageIds: new Set(),
                    messageSubscribed: false,
                    complianceSubscribed: false,
                    accountSubscribed: false,
                    toastTimer: null,
                    orderToastTimer: null,
                    statusBusy: false,
                    orderBusy: false,
                    uiAbort: null,
                };

            const state = window.__SARI_SELLER_SHELL_STATE__;

            function savedCollapsed() {
                try {
                    return window.localStorage.getItem(sidebarStorageKey) === '1';
                } catch (_) {
                    return false;
                }
            }

            function saveCollapsed(value) {
                try {
                    window.localStorage.setItem(
                        sidebarStorageKey,
                        value ? '1' : '0'
                    );
                } catch (_) {}
            }

            function closeMobileSidebar() {
                document
                    .getElementById('sellerSidebar')
                    ?.classList.add('-translate-x-full');

                document
                    .getElementById('sellerSidebarOverlay')
                    ?.classList.add('hidden');

                if (!document.getElementById('sellerCriticalRestriction')) {
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function syncCollapsedState() {
                const root = document.documentElement;
                const toggle = document.getElementById('sellerSidebarToggle');

                if (window.innerWidth >= desktopBreakpoint) {
                    root.classList.toggle(
                        'seller-sidebar-collapsed',
                        savedCollapsed()
                    );
                } else {
                    root.classList.remove('seller-sidebar-collapsed');
                }

                const collapsed =
                    root.classList.contains('seller-sidebar-collapsed');

                toggle?.setAttribute(
                    'aria-expanded',
                    collapsed ? 'false' : 'true'
                );

                toggle?.setAttribute(
                    'aria-label',
                    collapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );
            }

            function syncSidebarActiveState() {
                const activeClasses = [
                    'bg-[#d9930a]',
                    'text-white',
                    'shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                ];

                const inactiveClasses = [
                    'text-[#514b42]',
                    'hover:bg-[#f9f1e3]',
                    'hover:text-[#a96e05]'
                ];

                const path = currentPath();

                document
                    .querySelectorAll(
                        '#sellerSidebarNav > a[data-seller-sidebar-item]'
                    )
                    .forEach(function (link) {
                        const linkPath = pathOf(link.href);
                        const active = linkPath === path;

                        activeClasses.forEach(function (className) {
                            link.classList.toggle(className, active);
                        });

                        inactiveClasses.forEach(function (className) {
                            link.classList.toggle(className, !active);
                        });

                        if (linkPath === messagesPath) {
                            const badge =
                                link.querySelector(
                                    '#sellerSidebarMessageBadge'
                                );

                            if (badge) {
                                badge.classList.toggle(
                                    'bg-white',
                                    active
                                );
                                badge.classList.toggle(
                                    'text-[#d9930a]',
                                    active
                                );
                                badge.classList.toggle(
                                    'bg-[#d9930a]',
                                    !active
                                );
                                badge.classList.toggle(
                                    'text-white',
                                    !active
                                );
                            }
                        }
                    });
            }

            function bindCurrentShellUi() {
                state.uiAbort?.abort();

                const controller = new AbortController();
                state.uiAbort = controller;
                const signal = controller.signal;

                const root = document.documentElement;
                const sidebar = document.getElementById('sellerSidebar');
                const overlay =
                    document.getElementById('sellerSidebarOverlay');
                const mobileMenu =
                    document.getElementById('sellerMenuButton');
                const mobileClose =
                    document.getElementById('sellerSidebarClose');
                const desktopToggle =
                    document.getElementById('sellerSidebarToggle');
                const bell =
                    document.getElementById('sellerNotificationBell');
                const dropdown =
                    document.getElementById('sellerNotificationDropdown');

                syncCollapsedState();
                syncSidebarActiveState();

                mobileMenu?.addEventListener(
                    'click',
                    function () {
                        sidebar?.classList.remove('-translate-x-full');
                        overlay?.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    },
                    { signal }
                );

                mobileClose?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                overlay?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                desktopToggle?.addEventListener(
                    'click',
                    function () {
                        if (window.innerWidth < desktopBreakpoint) {
                            return;
                        }

                        const next =
                            !root.classList.contains(
                                'seller-sidebar-collapsed'
                            );

                        root.classList.toggle(
                            'seller-sidebar-collapsed',
                            next
                        );

                        saveCollapsed(next);
                        syncCollapsedState();
                    },
                    { signal }
                );

                window.addEventListener(
                    'resize',
                    function () {
                        if (window.innerWidth >= desktopBreakpoint) {
                            overlay?.classList.add('hidden');

                            if (
                                !document.getElementById(
                                    'sellerCriticalRestriction'
                                )
                            ) {
                                document.body.classList.remove(
                                    'overflow-hidden'
                                );
                            }
                        }

                        syncCollapsedState();
                    },
                    { signal }
                );

                document
                    .querySelectorAll('#sellerSidebar a[href]')
                    .forEach(function (link) {
                        link.addEventListener(
                            'click',
                            function () {
                                if (
                                    window.innerWidth <
                                    desktopBreakpoint
                                ) {
                                    closeMobileSidebar();
                                }
                            },
                            { signal }
                        );
                    });

                function closeNotificationDropdown() {
                    dropdown?.classList.add('hidden');
                    bell?.setAttribute('aria-expanded', 'false');
                }

                bell?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();

                        if (!dropdown) {
                            return;
                        }

                        const open =
                            dropdown.classList.contains('hidden');

                        dropdown.classList.toggle('hidden', !open);

                        bell.setAttribute(
                            'aria-expanded',
                            open ? 'true' : 'false'
                        );
                    },
                    { signal }
                );

                dropdown?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();
                    },
                    { signal }
                );

                document.addEventListener(
                    'click',
                    closeNotificationDropdown,
                    { signal }
                );

                document.addEventListener(
                    'keydown',
                    function (event) {
                        if (event.key === 'Escape') {
                            closeNotificationDropdown();
                            closeMobileSidebar();
                        }
                    },
                    { signal }
                );
            }

            function displayCount(count) {
                return count > 99 ? '99+' : String(count);
            }

            function updateBadge(badge, count) {
                if (!badge) {
                    return;
                }

                badge.textContent = displayCount(count);
                badge.dataset.hasUnread =
                    count > 0 ? 'true' : 'false';

                badge.classList.toggle('hidden', count < 1);
                badge.classList.toggle('grid', count > 0);
            }

            function syncUnreadUi() {
                updateBadge(
                    document.getElementById(
                        'sellerSidebarMessageBadge'
                    ),
                    state.unreadCount
                );

                updateBadge(
                    document.getElementById(
                        'sellerBellMessageBadge'
                    ),
                    state.unreadCount
                );

                const unreadText =
                    document.getElementById(
                        'sellerNotificationUnreadText'
                    );

                if (unreadText) {
                    unreadText.textContent =
                        state.unreadCount > 0
                            ? `${state.unreadCount} unread message${state.unreadCount === 1 ? '' : 's'}`
                            : 'No unread messages';
                }

                syncSidebarActiveState();
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function notificationText(data) {
                return (
                    data?.body ||
                    data?.attachment_name ||
                    'Admin sent an attachment.'
                );
            }

            function createNotificationItem(data) {
                if (!data?.id) {
                    return null;
                }

                const item = document.createElement('a');
                item.href = messagesUrl;
                item.setAttribute('wire:navigate', '');
                item.dataset.notificationMessageId = data.id;

                const unread = data.unread !== false;

                item.className =
                    'block border-b border-[#f1ece4] px-4 py-3.5 transition hover:bg-[#fdf9f2] ' +
                    (unread ? 'bg-[#fffaf1]' : 'bg-white');

                item.innerHTML = `
                    <div class="flex gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[9px] font-bold text-[#a8731f]">
                            SA
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-[9px] font-semibold text-[#3f382f]">
                                    SARI Admin Support
                                </p>

                                <span class="shrink-0 text-[7px] text-[#9a9185]">
                                    ${escapeHtml(data.time || 'Now')}
                                </span>
                            </div>

                            <p class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#786f64]">
                                ${escapeHtml(notificationText(data))}
                            </p>
                        </div>

                        ${unread
                            ? '<span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>'
                            : ''}
                    </div>
                `;

                return item;
            }

            function renderRecentNotifications(messages) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list) {
                    return;
                }

                list.innerHTML = '';

                if (!messages.length) {
                    list.innerHTML = `
                        <div id="sellerNotificationEmpty" class="px-5 py-9 text-center">
                            <div class="mx-auto grid h-11 w-11 place-items-center rounded-2xl bg-[#fbf6ec] text-[#b47e1e]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                    <path d="M10 21h4"></path>
                                </svg>
                            </div>
                            <p class="mt-3 text-[9px] font-semibold text-[#50483e]">
                                No notifications yet
                            </p>
                            <p class="mt-1 text-[8px] text-[#978e82]">
                                New Admin messages will appear here.
                            </p>
                        </div>
                    `;
                    return;
                }

                messages.slice(0, 5).forEach(function (message) {
                    const item = createNotificationItem(message);
                    if (item) {
                        list.appendChild(item);
                    }
                });
            }

            function prependNotification(data) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list || !data?.id) {
                    return;
                }

                if (
                    list.querySelector(
                        `[data-notification-message-id="${data.id}"]`
                    )
                ) {
                    return;
                }

                document
                    .getElementById('sellerNotificationEmpty')
                    ?.remove();

                const item = createNotificationItem(data);

                if (!item) {
                    return;
                }

                list.prepend(item);

                const items =
                    list.querySelectorAll(
                        '[data-notification-message-id]'
                    );

                if (items.length > 5) {
                    items[items.length - 1].remove();
                }
            }

            function ringBell() {
                const bell =
                    document.getElementById(
                        'sellerNotificationBell'
                    );

                if (!bell || typeof bell.animate !== 'function') {
                    return;
                }

                bell.animate(
                    [
                        { transform: 'rotate(0deg) scale(1)' },
                        { transform: 'rotate(-11deg) scale(1.08)' },
                        { transform: 'rotate(11deg) scale(1.08)' },
                        { transform: 'rotate(-7deg) scale(1.05)' },
                        { transform: 'rotate(7deg) scale(1.03)' },
                        { transform: 'rotate(0deg) scale(1)' }
                    ],
                    {
                        duration: 560,
                        easing: 'ease-out'
                    }
                );
            }

            function showMessageToast(data) {
                if (currentPath() === messagesPath) {
                    return;
                }

                const toast =
                    document.getElementById('sellerMessageToast');
                const text =
                    document.getElementById(
                        'sellerMessageToastText'
                    );

                if (!toast || !text) {
                    return;
                }

                text.textContent = notificationText(data);

                toast.classList.remove('hidden');

                requestAnimationFrame(function () {
                    toast.classList.remove(
                        'translate-y-2',
                        'opacity-0'
                    );
                    toast.classList.add(
                        'translate-y-0',
                        'opacity-100'
                    );
                });

                window.clearTimeout(state.toastTimer);

                state.toastTimer = window.setTimeout(function () {
                    toast.classList.remove(
                        'translate-y-0',
                        'opacity-100'
                    );
                    toast.classList.add(
                        'translate-y-2',
                        'opacity-0'
                    );

                    window.setTimeout(function () {
                        toast.classList.add('hidden');
                    }, 300);
                }, 4500);
            }

            function markLayoutAsRead() {
                state.unreadCount = 0;
                syncUnreadUi();

                document
                    .querySelectorAll(
                        '#sellerNotificationList [data-notification-message-id]'
                    )
                    .forEach(function (item) {
                        item.classList.remove('bg-[#fffaf1]');
                        item.classList.add('bg-white');

                        item
                            .querySelector(
                                '.h-2.w-2.shrink-0.rounded-full.bg-\\[\\#d9930a\\]'
                            )
                            ?.remove();
                    });
            }

            async function loadLayoutState() {
                if (
                    !layoutStateUrl ||
                    currentPath() === messagesPath
                ) {
                    return;
                }

                try {
                    const response = await fetch(layoutStateUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    state.unreadCount =
                        Math.max(
                            0,
                            Number(data?.unread_count || 0)
                        );

                    const messages =
                        Array.isArray(data?.recent_messages)
                            ? data.recent_messages
                            : [];

                    messages.forEach(function (message) {
                        state.processedMessageIds.add(
                            String(message.id)
                        );
                    });

                    syncUnreadUi();
                    renderRecentNotifications(messages);
                } catch (_) {
                    console.debug(
                        'SARI layout notification state unavailable.'
                    );
                }
            }

            function handleChatMessage(data) {
                if (
                    !data ||
                    data.id === undefined ||
                    data.id === null
                ) {
                    return;
                }

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-chat-message',
                        { detail: data }
                    )
                );

                if (data.sender_role !== 'admin') {
                    return;
                }

                const messageId = String(data.id);

                if (
                    state.processedMessageIds.has(messageId)
                ) {
                    return;
                }

                state.processedMessageIds.add(messageId);
                prependNotification(data);

                if (currentPath() === messagesPath) {
                    markLayoutAsRead();
                    return;
                }

                state.unreadCount += 1;
                syncUnreadUi();
                ringBell();
                showMessageToast(data);
            }

            function subscribeMessages(attempt = 0) {
                if (!sellerChannel || state.messageSubscribed) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeMessages(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen('.chat.message', handleChatMessage);

                state.messageSubscribed = true;
            }

            function subscribeCompliance(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.complianceSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeCompliance(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.compliance.alert',
                        function (event) {
                            window.dispatchEvent(
                                new CustomEvent(
                                    'sari:seller-compliance-alert',
                                    { detail: event }
                                )
                            );

                            const warningNumber =
                                Number(
                                    event?.warning_number || 0
                                );

                            const suspended =
                                Boolean(
                                    event?.suspended_until
                                );

                            if (
                                warningNumber >= 3 ||
                                suspended
                            ) {
                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.complianceSubscribed = true;
            }

            function subscribeAccountStatus(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.accountSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeAccountStatus(
                                attempt + 1
                            );
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.account-status',
                        function (event) {
                            const action =
                                String(event?.action || '');

                            if (
                                action === 'banned' ||
                                action === 'deactivated'
                            ) {
                                window.location.replace(
                                    loginUrl
                                );
                                return;
                            }

                            if (
                                action === 'suspended' ||
                                action === 'suspension_lifted' ||
                                action === 'unbanned' ||
                                action === 'restored'
                            ) {
                                const sidebarStatus =
                                    document.getElementById(
                                        'sellerSidebarStoreStatus'
                                    );

                                if (sidebarStatus) {
                                    sidebarStatus.textContent =
                                        action === 'suspended'
                                            ? 'Suspended Store'
                                            : 'Verified Store';
                                }

                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.accountSubscribed = true;
            }

            async function checkAccountState() {
                if (
                    state.statusBusy ||
                    document.hidden ||
                    document.getElementById(
                        'sellerCriticalRestriction'
                    )
                ) {
                    return;
                }

                state.statusBusy = true;

                try {
                    const response =
                        await fetch(accountStateUrl, {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store'
                        });

                    if (
                        response.status === 401 ||
                        response.status === 403
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (!response.ok) {
                        return;
                    }

                    const account = await response.json();

                    if (
                        account.account_status === 'banned' ||
                        account.account_status === 'deactivated'
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (account.restricted === true) {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(
                                dashboardUrl
                            );
                        } else {
                            window.location.href =
                                dashboardUrl;
                        }
                    }
                } catch (_) {
                    console.debug(
                        'SARI seller status fallback unavailable.'
                    );
                } finally {
                    state.statusBusy = false;
                }
            }

            function showOrderToast(eventData) {
                const toast =
                    document.getElementById(
                        'sellerOrderStatusToast'
                    );
                const title =
                    document.getElementById(
                        'sellerOrderToastTitle'
                    );
                const message =
                    document.getElementById(
                        'sellerOrderToastMessage'
                    );

                if (
                    !toast ||
                    !title ||
                    !message ||
                    !eventData
                ) {
                    return;
                }

                title.textContent =
                    eventData.title || 'Order Update';

                message.textContent =
                    eventData.message ||
                    'Your order status changed.';

                toast.classList.remove('hidden');

                requestAnimationFrame(function () {
                    toast.classList.remove(
                        'translate-y-2',
                        'opacity-0'
                    );
                    toast.classList.add(
                        'translate-y-0',
                        'opacity-100'
                    );
                });

                window.clearTimeout(
                    state.orderToastTimer
                );

                state.orderToastTimer =
                    window.setTimeout(function () {
                        toast.classList.remove(
                            'translate-y-0',
                            'opacity-100'
                        );
                        toast.classList.add(
                            'translate-y-2',
                            'opacity-0'
                        );

                        window.setTimeout(function () {
                            toast.classList.add('hidden');
                        }, 300);
                    }, 5500);
            }

            async function pollOrders() {
                const path = currentPath();

                if (
                    !sellerId ||
                    state.orderBusy ||
                    document.hidden ||
                    (
                        path !== dashboardPath &&
                        path !== ordersPath
                    )
                ) {
                    return;
                }

                state.orderBusy = true;

                try {
                    const response =
                        await fetch(
                            @json(route('seller.orders.live-state')),
                            {
                                headers: {
                                    'Accept':
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                cache: 'no-store'
                            }
                        );

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const eventData = data.latest_event;

                    if (!eventData?.id) {
                        return;
                    }

                    const storageKey =
                        'sari:seller-order-event:' +
                        sellerId;

                    const seen =
                        window.localStorage.getItem(
                            storageKey
                        );

                    if (!seen) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );
                        return;
                    }

                    if (
                        Number(eventData.id) >
                        Number(seen)
                    ) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );

                        showOrderToast(eventData);
                    }
                } catch (_) {
                    console.debug(
                        'SARI order polling temporarily unavailable.'
                    );
                } finally {
                    state.orderBusy = false;
                }
            }

            function syncRestrictionUi() {
                const locked =
                    Boolean(
                        document.getElementById(
                            'sellerCriticalRestriction'
                        )
                    );

                if (locked) {
                    document.body.classList.add(
                        'overflow-hidden'
                    );
                }

                if (
                    !window.__SARI_SELLER_LOCK_POPSTATE_BOUND__
                ) {
                    window.__SARI_SELLER_LOCK_POPSTATE_BOUND__ =
                        true;

                    window.addEventListener(
                        'popstate',
                        function () {
                            if (
                                document.getElementById(
                                    'sellerCriticalRestriction'
                                )
                            ) {
                                window.history.pushState(
                                    {
                                        sariSellerLocked:
                                            true
                                    },
                                    '',
                                    window.location.href
                                );
                            }
                        }
                    );
                }

                if (locked) {
                    window.history.replaceState(
                        {
                            ...(window.history.state || {}),
                            sariSellerLocked: true
                        },
                        '',
                        window.location.href
                    );
                }
            }

            document.addEventListener(
                'livewire:navigating',
                function () {
                    document.documentElement.dataset
                        .sellerShellLoading = 'true';

                    closeMobileSidebar();

                    document
                        .getElementById(
                            'sellerNotificationDropdown'
                        )
                        ?.classList.add('hidden');
                }
            );

            document.addEventListener(
                'livewire:navigated',
                function () {
                    delete document.documentElement.dataset
                        .sellerShellLoading;

                    bindCurrentShellUi();
                    syncRestrictionUi();
                    syncUnreadUi();

                    if (currentPath() === messagesPath) {
                        markLayoutAsRead();
                    } else {
                        window.setTimeout(
                            loadLayoutState,
                            80
                        );
                    }

                    subscribeMessages();
                    subscribeCompliance();
                    subscribeAccountStatus();
                }
            );

            if (
                !window.__SARI_SELLER_STATUS_INTERVAL__
            ) {
                window.__SARI_SELLER_STATUS_INTERVAL__ =
                    window.setInterval(
                        checkAccountState,
                        15000
                    );

                window.setTimeout(
                    checkAccountState,
                    1800
                );
            }

            if (
                !window.__SARI_SELLER_ORDER_INTERVAL__
            ) {
                window.__SARI_SELLER_ORDER_INTERVAL__ =
                    window.setInterval(
                        pollOrders,
                        10000
                    );

                window.setTimeout(
                    pollOrders,
                    1400
                );
            }
        })();
    </script>



    {{-- =========================================================
        PERSISTENT SELLER ACTION TOAST
        Stays mounted while Livewire refreshes only page content.
    ========================================================== --}}
    @persist('seller-action-toast')
        <div
            id="sellerActionToast"
            class="pointer-events-none fixed right-4 top-4 z-[10000] hidden w-[min(420px,calc(100vw-2rem))]"
            aria-live="polite"
            aria-atomic="true"
        >
            <div
                id="sellerActionToastCard"
                class="pointer-events-auto flex items-start gap-3 rounded-[16px] border border-[#d7e7dd] bg-white px-4 py-3.5 shadow-[0_18px_50px_rgba(38,30,18,.16)]"
            >
                <span
                    id="sellerActionToastIcon"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f1f8f4] text-[#56816a]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

                <div class="min-w-0 flex-1">
                    <p id="sellerActionToastTitle" class="text-[10px] font-bold text-[#3f6f52]">
                        Action completed
                    </p>
                    <p id="sellerActionToastMessage" class="mt-1 text-[9px] leading-5 text-[#65776c]"></p>
                </div>

                <button
                    id="sellerActionToastClose"
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
                const card = document.getElementById('sellerActionToastCard');
                const icon = document.getElementById('sellerActionToastIcon');
                const title = document.getElementById('sellerActionToastTitle');
                const body = document.getElementById('sellerActionToastMessage');

                if (!toast || !card || !icon || !title || !body) {
                    return;
                }

                const warning = type === 'warning';
                const error = type === 'error';

                card.className = 'pointer-events-auto flex items-start gap-3 rounded-[16px] border bg-white px-4 py-3.5 shadow-[0_18px_50px_rgba(38,30,18,.16)]';
                icon.className = 'grid h-9 w-9 shrink-0 place-items-center rounded-xl';
                title.className = 'text-[10px] font-bold';
                body.className = 'mt-1 text-[9px] leading-5';

                if (error) {
                    card.classList.add('border-[#ecd3d3]');
                    icon.classList.add('bg-[#fff3f3]', 'text-[#a65f5f]');
                    title.classList.add('text-[#9d5555]');
                    body.classList.add('text-[#8d6666]');
                    title.textContent = 'Action not completed';
                    icon.innerHTML = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16.5h.01"></path></svg>';
                } else if (warning) {
                    card.classList.add('border-[#ead8b9]');
                    icon.classList.add('bg-[#fff8ec]', 'text-[#a8731f]');
                    title.classList.add('text-[#946516]');
                    body.classList.add('text-[#806d4c]');
                    title.textContent = 'Review notice';
                    icon.innerHTML = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>';
                } else {
                    card.classList.add('border-[#d7e7dd]');
                    icon.classList.add('bg-[#f1f8f4]', 'text-[#56816a]');
                    title.classList.add('text-[#3f6f52]');
                    body.classList.add('text-[#65776c]');
                    title.textContent = 'Action completed';
                    icon.innerHTML = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>';
                }

                body.textContent = message || 'Your seller action was saved.';
                toast.classList.remove('hidden');

                if (toastTimer) {
                    window.clearTimeout(toastTimer);
                }

                toastTimer = window.setTimeout(function () {
                    toast.classList.add('hidden');
                }, error ? 6500 : 4500);
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
                if (form.id === 'sellerAddProductForm') return 'Screening & saving...';
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

            async function submitFast(form, submitter) {
                if (form.dataset.sariSubmitting === '1') {
                    return;
                }

                form.dataset.sariSubmitting = '1';

                const button = submitter || form.querySelector('button[type="submit"], input[type="submit"]');
                setButtonBusy(button, form, true);

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
                        showToast(firstError(payload), 'error');
                        return;
                    }

                    showToast(payload.message || 'Your seller action was saved.', payload.type || 'success');
                    closeProductModals();

                    window.setTimeout(function () {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(window.location.href);
                        } else {
                            window.location.reload();
                        }
                    }, 160);
                } catch (error) {
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


    {{-- PAGE-SPECIFIC SCRIPTS --}}
    @stack('scripts')

    {{-- Start Livewire after all navigation listeners are registered. --}}
    @livewireScripts
</body>
</html>