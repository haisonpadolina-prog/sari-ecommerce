@php
    /*
    |--------------------------------------------------------------------------
    | BUYER SHELL DISPLAY DATA
    |--------------------------------------------------------------------------
    | UI-only values sourced from the existing session. No database query or
    | backend behavior is introduced by the compact Buyer shell.
    */
    $buyerSidebarLogoSrc = asset('images/sari-logo.png');
    $buyerSidebarCompactLogoSrc = asset('images/sari-main-logo.png');
    $buyerSidebarEmail = (string) session('buyer_email', 'buyer@gmail.com');
    $buyerSidebarHandle = trim((string) \Illuminate\Support\Str::before($buyerSidebarEmail, '@'));
    $buyerSidebarName = trim((string) session('buyer_name', ''));

    if ($buyerSidebarName === '') {
        $buyerSidebarName = \Illuminate\Support\Str::of($buyerSidebarHandle ?: 'Buyer Account')
            ->replace(['.', '_', '-'], ' ')
            ->title()
            ->toString();
    }

    $buyerSidebarInitials = strtoupper(
        mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $buyerSidebarName) ?: 'BU', 0, 2)
    );
@endphp

<aside
    id="buyerSidebar"
    class="fixed inset-y-0 left-0 z-50 flex -translate-x-full flex-col border-r border-[#ece5da] bg-white transition-transform duration-200 ease-out lg:translate-x-0"
    aria-label="Buyer navigation"
>
    {{-- BRAND --}}
    <div id="buyerSidebarBrand" class="relative flex items-center justify-center border-b border-[#eee7dc] bg-white px-3">
        <a
            id="buyerSidebarLogo"
            href="{{ route('buyer.home') }}"
            wire:navigate.hover
            class="relative flex w-full items-center justify-center"
            aria-label="SARI Buyer Home"
            title="SARI Buyer Home"
        >
            <span id="buyerSidebarLogoStage" class="relative block h-[44px] w-full">
                <img
                    id="buyerSidebarLogoFull"
                    src="{{ $buyerSidebarLogoSrc }}"
                    alt="SARI"
                    width="104"
                    height="42"
                    loading="eager"
                    decoding="async"
                    draggable="false"
                    class="absolute left-1/2 top-1/2 h-auto w-[104px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain brightness-0"
                >

                <img
                    id="buyerSidebarLogoCompact"
                    src="{{ $buyerSidebarCompactLogoSrc }}"
                    alt="SARI"
                    width="38"
                    height="38"
                    loading="eager"
                    decoding="async"
                    draggable="false"
                    class="pointer-events-none absolute left-1/2 top-1/2 h-[38px] w-[38px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain opacity-0"
                    style="visibility:hidden;"
                >
            </span>
        </a>

        <button
            id="buyerSidebarClose"
            type="button"
            class="absolute right-3 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-[10px] text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805] focus:outline-none focus:ring-4 focus:ring-[#d89a25]/10 lg:hidden"
            aria-label="Close buyer sidebar"
        >
            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    {{-- NAVIGATION --}}
    <nav id="buyerSidebarNav" class="flex-1 overflow-y-auto" data-buyer-scroll>
        <p class="buyer-sidebar-section-label">Main menu</p>

        <a
            href="{{ route('buyer.home') }}"
            wire:navigate.hover
            title="Home"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.home') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5.5 10v10h13V10"></path>
                <path d="M9.5 20v-6h5v6"></path>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">Home</span>
        </a>

        <div class="buyer-sidebar-section-divider"></div>
        <p class="buyer-sidebar-section-label">Shopping</p>

        <a
            href="{{ route('buyer.products') }}"
            wire:navigate.hover
            title="Shop Products"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.products') || request()->routeIs('buyer.product.details') || request()->routeIs('buyer.shop') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
                <path d="M8 12h8"></path>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">Shop Products</span>
        </a>

        <a
            href="{{ route('buyer.cart') }}"
            wire:navigate.hover
            title="Shopping Cart"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.cart') || request()->routeIs('buyer.checkout*') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center justify-between gap-3"
        >
            <span class="buyer-sidebar-nav-main flex items-center gap-3" data-buyer-sidebar-inner>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 4h2l2 12h10l2-8H7"></path>
                    <circle cx="9" cy="20" r="1.5"></circle>
                    <circle cx="17" cy="20" r="1.5"></circle>
                </svg>
                <span class="buyer-sidebar-label whitespace-nowrap">Shopping Cart</span>
            </span>
            <span id="buyerSidebarCartCount" class="buyer-sidebar-extra hidden place-items-center rounded-full bg-[#d9950b] px-1.5 text-[8px] font-bold text-white" aria-label="Cart item count">0</span>
        </a>

        <a
            href="{{ route('buyer.rewards') }}"
            wire:navigate.hover
            title="Rewards & Vouchers"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.rewards') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M20 12a2 2 0 0 0 0-4h-2.2a3.8 3.8 0 0 0-5.8-3 3.8 3.8 0 0 0-5.8 3H4a2 2 0 0 0 0 4h16Z"></path>
                <path d="M12 5v15"></path>
                <path d="M4 12h16v7H4z"></path>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">Rewards / Vouchers</span>
        </a>

        <div class="buyer-sidebar-section-divider"></div>
        <p class="buyer-sidebar-section-label">Orders & delivery</p>

        <a
            href="{{ route('buyer.orders') }}"
            wire:navigate.hover
            title="My Orders"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.orders*') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">My Orders</span>
        </a>

        <a
            href="{{ route('buyer.shipping') }}"
            wire:navigate.hover
            title="Shipping"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.shipping') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M3 7h11v9H3z"></path>
                <path d="M14 10h4l3 3v3h-7z"></path>
                <circle cx="7" cy="18" r="1.5"></circle>
                <circle cx="18" cy="18" r="1.5"></circle>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">Shipping</span>
        </a>

        <div class="buyer-sidebar-section-divider"></div>
        <p class="buyer-sidebar-section-label">Support</p>

        <a
            href="{{ route('buyer.notifications') }}"
            wire:navigate.hover
            title="Notifications"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.notifications*') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center justify-between gap-3"
        >
            <span class="buyer-sidebar-nav-main flex items-center gap-3" data-buyer-sidebar-inner>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>
                <span class="buyer-sidebar-label whitespace-nowrap">Notifications</span>
            </span>
            <span id="buyerSidebarNotificationCount" class="buyer-sidebar-extra hidden place-items-center rounded-full bg-[#d9950b] px-1.5 text-[8px] font-bold text-white" aria-label="Unread notification count">0</span>
        </a>

        <a
            href="{{ route('buyer.messages') }}"
            wire:navigate.hover
            title="Chat / Messaging"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.messages') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center justify-between gap-3"
        >
            <span class="buyer-sidebar-nav-main flex items-center gap-3" data-buyer-sidebar-inner>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                </svg>
                <span class="buyer-sidebar-label whitespace-nowrap">Chat / Messaging</span>
            </span>
            <span id="buyerSidebarMessageCount" class="buyer-sidebar-extra hidden place-items-center rounded-full bg-[#d9950b] px-1.5 text-[8px] font-bold text-white" aria-label="Unread message count">0</span>
        </a>

        <div class="buyer-sidebar-section-divider"></div>
        <p class="buyer-sidebar-section-label">Account</p>

        <a
            href="{{ route('buyer.account') }}"
            wire:navigate.hover
            title="Account Management"
            data-buyer-sidebar-item
            data-buyer-nav-active="{{ request()->routeIs('buyer.account') ? 'true' : 'false' }}"
            class="buyer-sidebar-item flex items-center gap-3"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="8" r="3.5"></circle>
                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
            </svg>
            <span class="buyer-sidebar-label whitespace-nowrap">Account Management</span>
        </a>

        <form id="buyerLogoutForm" method="POST" action="{{ route('buyer.logout') }}">
            @csrf
            <button
                type="submit"
                title="Logout"
                data-buyer-sidebar-item
                class="buyer-sidebar-item flex w-full items-center gap-3 text-left text-[#514b42] hover:border-red-100 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-4 focus:ring-red-500/10"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>
                <span class="buyer-sidebar-label whitespace-nowrap">Logout</span>
            </button>
        </form>
    </nav>

    {{-- PROFILE --}}
    <div id="buyerSidebarProfile" class="border-t border-[#eee7dc] bg-white">
        <a
            href="{{ route('buyer.account') }}"
            wire:navigate.hover
            title="{{ $buyerSidebarName }}"
            class="buyer-sidebar-profile-card flex w-full items-center"
        >
            <span class="buyer-shell-avatar grid shrink-0 place-items-center rounded-full bg-[#d9950b] font-semibold text-white">
                {{ $buyerSidebarInitials ?: 'BU' }}
            </span>

            <span class="buyer-sidebar-label min-w-0 flex-1">
                <span class="block truncate font-semibold text-[#28231c]">{{ $buyerSidebarName ?: 'Buyer Account' }}</span>
                <span class="mt-0.5 block truncate text-[#908779]">Buyer Account</span>
            </span>

            <svg viewBox="0 0 24 24" class="buyer-sidebar-extra shrink-0 text-[#82796b]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </a>
    </div>
</aside>
