<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SARI Seller')</title>

    {{-- Tailwind CDN — keep your current styling setup --}}
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
            --seller-sidebar-width: 275px;
        }

        @media (min-width: 1024px) {
            #sellerSidebar {
                width: var(--seller-sidebar-width);
            }

            #sellerContent {
                padding-left: var(--seller-sidebar-width);
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
    </style>

    @stack('styles')
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | SELLER LAYOUT REAL-TIME MESSAGE DATA
    |--------------------------------------------------------------------------
    |
    | Temporary project setup:
    | - Seller authentication is session-based.
    | - Messages are stored in chat_messages.
    | - Admin -> Seller unread messages use read_by_seller_at.
    |
    */

    $sellerLayoutAccount = null;
    $sellerUnreadMessages = 0;
    $sellerRecentAdminMessages = collect();

    if (session('is_seller') && session('seller_account_id')) {
        $sellerLayoutAccount = \App\Models\SellerAccount::find(
            session('seller_account_id')
        );

        if ($sellerLayoutAccount) {
            // The token should already exist after login / seller controller resolution.
            // This fallback keeps the layout safe if an older seller record has no token yet.
            if (!$sellerLayoutAccount->realtime_token) {
                $sellerLayoutAccount->ensureRealtimeToken();
                $sellerLayoutAccount->refresh();
            }

            $sellerUnreadMessages = \App\Models\ChatMessage::query()
                ->where('seller_account_id', $sellerLayoutAccount->id)
                ->where('sender_role', 'admin')
                ->whereNull('read_by_seller_at')
                ->count();

            $sellerRecentAdminMessages = \App\Models\ChatMessage::query()
                ->where('seller_account_id', $sellerLayoutAccount->id)
                ->where('sender_role', 'admin')
                ->latest('created_at')
                ->limit(5)
                ->get();
        }
    }
@endphp

<body class="m-0 min-h-screen bg-[#faf9f6] font-poppins text-[#1f1b16] antialiased">

    {{-- =========================================================
        SELLER SIDEBAR
    ========================================================== --}}
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
        <div class="relative flex min-h-[110px] items-center justify-center border-b border-[#eee4d3] px-4">
            <a
                id="sellerSidebarLogo"
                href="{{ route('seller.dashboard') }}"
                class="flex items-center justify-center"
                aria-label="SARI Seller Dashboard"
            >
                <img
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    class="h-auto w-[145px] object-contain brightness-0"
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
        <nav id="sellerSidebarNav" class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">

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
            >
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[12px] font-semibold text-white">
                    {{ strtoupper(substr($sellerLayoutAccount?->store_name ?: 'SS', 0, 2)) }}
                </div>

                <div class="seller-sidebar-label min-w-0 flex-1">
                    <p class="truncate text-[12px] font-semibold text-[#211d17]">
                        {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                    </p>

                    <p class="mt-0.5 truncate text-[9px] text-[#8d8272]">
                        {{ $sellerLayoutAccount?->isSuspended() ? 'Suspended Store' : 'Verified Store' }}
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
                    >
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white">
                            {{ strtoupper(substr($sellerLayoutAccount?->store_name ?: 'SS', 0, 2)) }}
                        </div>

                        <div>
                            <p class="max-w-[160px] truncate text-[11px] font-semibold text-[#28231c]">
                                {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                            </p>

                            <p class="mt-0.5 text-[9px] text-[#908779]">
                                {{ $sellerLayoutAccount?->isSuspended() ? 'Suspended Store' : 'Verified Store' }}
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
                >
                    Open message →
                </a>
            </div>
        </div>
    </div>


    {{-- =========================================================
        SIDEBAR / HEADER UI SCRIPT
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sellerSidebar');
            const overlay = document.getElementById('sellerSidebarOverlay');
            const mobileMenu = document.getElementById('sellerMenuButton');
            const mobileClose = document.getElementById('sellerSidebarClose');
            const desktopToggle = document.getElementById('sellerSidebarToggle');

            const labels = document.querySelectorAll('.seller-sidebar-label');
            const extras = document.querySelectorAll('.seller-sidebar-extra');
            const items = document.querySelectorAll('[data-seller-sidebar-item]');
            const sidebarLogo = document.getElementById('sellerSidebarLogo');
            const nav = document.getElementById('sellerSidebarNav');
            const profile = document.getElementById('sellerSidebarProfile');

            const bell = document.getElementById('sellerNotificationBell');
            const dropdown = document.getElementById('sellerNotificationDropdown');

            let collapsed = false;

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                overlay?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function setCollapsed(value) {
                collapsed = value;

                document.documentElement.style.setProperty(
                    '--seller-sidebar-width',
                    value ? '88px' : '275px'
                );

                sidebarLogo?.classList.toggle('hidden', value);

                labels.forEach(function (element) {
                    element.classList.toggle('hidden', value);
                });

                extras.forEach(function (element) {
                    const isUnreadBadge =
                        element.hasAttribute('data-seller-unread-badge');

                    if (value) {
                        element.classList.add('hidden');
                        element.classList.remove('grid');
                        return;
                    }

                    if (isUnreadBadge) {
                        const hasUnread =
                            element.dataset.hasUnread === 'true';

                        element.classList.toggle('hidden', !hasUnread);

                        if (hasUnread) {
                            element.classList.add('grid');
                        } else {
                            element.classList.remove('grid');
                        }

                        return;
                    }

                    element.classList.remove('hidden');
                });

                items.forEach(function (item) {
                    if (value) {
                        item.style.justifyContent = 'center';
                        item.style.paddingLeft = '0';
                        item.style.paddingRight = '0';
                        item.style.columnGap = '0';
                    } else {
                        item.style.justifyContent = '';
                        item.style.paddingLeft = '';
                        item.style.paddingRight = '';
                        item.style.columnGap = '';
                    }
                });

                if (nav) {
                    nav.style.paddingLeft = value ? '12px' : '';
                    nav.style.paddingRight = value ? '12px' : '';
                }

                if (profile) {
                    profile.style.padding = value ? '12px' : '';
                }

                desktopToggle?.setAttribute(
                    'aria-expanded',
                    value ? 'false' : 'true'
                );

                desktopToggle?.setAttribute(
                    'aria-label',
                    value ? 'Expand sidebar' : 'Collapse sidebar'
                );
            }

            mobileMenu?.addEventListener('click', openSidebar);
            mobileClose?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            desktopToggle?.addEventListener('click', function () {
                if (window.innerWidth >= 1024) {
                    setCollapsed(!collapsed);
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth < 1024 && collapsed) {
                    setCollapsed(false);
                }

                if (window.innerWidth >= 1024) {
                    overlay?.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Notification dropdown
            |--------------------------------------------------------------------------
            */

            function closeNotificationDropdown() {
                dropdown?.classList.add('hidden');
                bell?.setAttribute('aria-expanded', 'false');
            }

            bell?.addEventListener('click', function (event) {
                event.stopPropagation();

                if (!dropdown) {
                    return;
                }

                const willOpen = dropdown.classList.contains('hidden');

                dropdown.classList.toggle('hidden', !willOpen);
                bell.setAttribute(
                    'aria-expanded',
                    willOpen ? 'true' : 'false'
                );
            });

            dropdown?.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('click', closeNotificationDropdown);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeNotificationDropdown();
                    closeSidebar();
                }
            });
        });
    </script>


    {{-- =========================================================
        VITE: Laravel Echo / Reverb
    ========================================================== --}}
    @vite('resources/js/app.js')


    {{-- =========================================================
        REAL-TIME SELLER MESSAGE NOTIFICATIONS
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sellerChannel = @json(
                $sellerLayoutAccount && $sellerLayoutAccount->realtime_token
                    ? 'sari.seller.' . $sellerLayoutAccount->realtime_token
                    : null
            );

            const isMessagesPage = @json(
                request()->routeIs('seller.messages')
            );

            const sidebarBadge =
                document.getElementById('sellerSidebarMessageBadge');

            const bellBadge =
                document.getElementById('sellerBellMessageBadge');

            const unreadText =
                document.getElementById('sellerNotificationUnreadText');

            const bell =
                document.getElementById('sellerNotificationBell');

            const notificationList =
                document.getElementById('sellerNotificationList');

            const notificationEmpty =
                document.getElementById('sellerNotificationEmpty');

            const toast =
                document.getElementById('sellerMessageToast');

            const toastText =
                document.getElementById('sellerMessageToastText');

            let unreadCount = {{ (int) $sellerUnreadMessages }};

            /*
            |--------------------------------------------------------------------------
            | Duplicate protection
            |--------------------------------------------------------------------------
            |
            | A single database message must only increment the notification counter
            | one time, even if the same broadcast is received more than once.
            |
            | The Set is stored on window so it also survives accidental duplicate
            | listener setup during development / Vite HMR.
            |
            */

            window.__SARI_SELLER_PROCESSED_MESSAGE_IDS__ =
                window.__SARI_SELLER_PROCESSED_MESSAGE_IDS__ || new Set();

            const processedMessageIds =
                window.__SARI_SELLER_PROCESSED_MESSAGE_IDS__;

            const existingNotificationIds = @json(
                $sellerRecentAdminMessages
                    ->pluck('id')
                    ->map(fn ($id) => (string) $id)
                    ->values()
            );

            existingNotificationIds.forEach(function (id) {
                processedMessageIds.add(String(id));
            });

            let toastTimer = null;

            function displayCount(count) {
                return count > 99 ? '99+' : String(count);
            }

            function updateUnreadText() {
                if (!unreadText) {
                    return;
                }

                unreadText.textContent =
                    unreadCount > 0
                        ? `${unreadCount} unread message${unreadCount === 1 ? '' : 's'}`
                        : 'No unread messages';
            }

            function updateBadge(badge, count) {
                if (!badge) {
                    return;
                }

                badge.textContent = displayCount(count);
                badge.dataset.hasUnread = count > 0 ? 'true' : 'false';

                if (count > 0) {
                    badge.classList.remove('hidden');
                    badge.classList.add('grid');
                } else {
                    badge.classList.remove('grid');
                    badge.classList.add('hidden');
                }
            }

            function updateNotificationBadges(count) {
                unreadCount = Math.max(0, Number(count) || 0);

                updateBadge(sidebarBadge, unreadCount);
                updateBadge(bellBadge, unreadCount);
                updateUnreadText();
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
                return data.body ||
                    data.attachment_name ||
                    'Admin sent an attachment.';
            }

            function prependNotification(data) {
                if (!notificationList || !data?.id) {
                    return;
                }

                if (
                    notificationList.querySelector(
                        `[data-notification-message-id="${data.id}"]`
                    )
                ) {
                    return;
                }

                notificationEmpty?.remove();

                const item = document.createElement('a');

                item.href = @json(route('seller.messages'));

                item.dataset.notificationMessageId = data.id;

                item.className =
                    'block border-b border-[#f1ece4] bg-[#fffaf1] px-4 py-3.5 transition hover:bg-[#fdf9f2]';

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

                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>
                    </div>
                `;

                notificationList.prepend(item);

                const items =
                    notificationList.querySelectorAll(
                        '[data-notification-message-id]'
                    );

                if (items.length > 5) {
                    items[items.length - 1].remove();
                }
            }

            function ringBell() {
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

            function showToast(data) {
                if (!toast || !toastText || isMessagesPage) {
                    return;
                }

                toastText.textContent = notificationText(data);

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

                window.clearTimeout(toastTimer);

                toastTimer = window.setTimeout(function () {
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
                updateNotificationBadges(0);

                document
                    .querySelectorAll(
                        '#sellerNotificationList [data-notification-message-id]'
                    )
                    .forEach(function (item) {
                        item.classList.remove('bg-[#fffaf1]');
                        item.classList.add('bg-white');

                        item
                            .querySelector('.h-2.w-2.shrink-0.rounded-full.bg-\\[\\#d9930a\\]')
                            ?.remove();
                    });
            }

            function handleIncomingMessage(data) {
                if (!data || data.id === undefined || data.id === null) {
                    return;
                }

                if (data.sender_role !== 'admin') {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT: Count every message ID only once
                |--------------------------------------------------------------------------
                */

                const messageId = String(data.id);

                if (processedMessageIds.has(messageId)) {
                    return;
                }

                processedMessageIds.add(messageId);

                prependNotification(data);

                /*
                |--------------------------------------------------------------------------
                | If seller is already inside Chat / Messaging:
                | - The chat page itself displays the message.
                | - The chat page also calls seller.messages.read.
                | - Do not increase unread badges.
                |--------------------------------------------------------------------------
                */

                if (isMessagesPage) {
                    markLayoutAsRead();
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Seller is on another page:
                | - Exactly ONE unique Admin message = +1 notification.
                | - Same event/message ID received again = ignored.
                |--------------------------------------------------------------------------
                */

                updateNotificationBadges(unreadCount + 1);
                ringBell();
                showToast(data);
            }

            function subscribeToSellerChannel(attempt = 0) {
                if (!sellerChannel) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 20) {
                        window.setTimeout(function () {
                            subscribeToSellerChannel(attempt + 1);
                        }, 250);
                    }

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate channel listeners
                |--------------------------------------------------------------------------
                */

                window.__SARI_SELLER_NOTIFICATION_SUBSCRIPTIONS__ =
                    window.__SARI_SELLER_NOTIFICATION_SUBSCRIPTIONS__ || {};

                if (
                    window.__SARI_SELLER_NOTIFICATION_SUBSCRIPTIONS__[
                        sellerChannel
                    ]
                ) {
                    return;
                }

                window.__SARI_SELLER_NOTIFICATION_SUBSCRIPTIONS__[
                    sellerChannel
                ] = true;

                window.Echo
                    .channel(sellerChannel)
                    .listen('.chat.message', handleIncomingMessage);
            }

            updateNotificationBadges(unreadCount);

            if (isMessagesPage) {
                markLayoutAsRead();
            }

            subscribeToSellerChannel();
        });
    </script>


    {{-- PAGE-SPECIFIC SCRIPTS --}}
    @stack('scripts')
</body>
</html>