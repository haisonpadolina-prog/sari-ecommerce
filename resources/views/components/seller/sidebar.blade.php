{{-- Seller sidebar — thinner clean pass: 244px expanded width, compact internal spacing. --}}
<aside
    id="sellerSidebar"
    class="
        fixed inset-y-0 left-0 z-50
        flex -translate-x-full flex-col
        border-r border-[#eee4d3]
        w-[244px] max-w-[86vw]
        bg-[#fffdf8]
        lg:translate-x-0
    "
>
    {{-- =====================================================
        BRAND
        Full sidebar  : sari-logo.png
        Collapsed     : sari-main-logo.png
    ====================================================== --}}
    <div
        id="sellerSidebarBrand"
        class="relative flex min-h-[74px] items-center justify-center border-b border-[#eee4d3] px-3"
    >
        <a
            id="sellerSidebarLogo"
            href="{{ route('seller.dashboard') }}"
            class="relative flex items-center justify-center"
            aria-label="SARI Seller Dashboard"
            title="SARI Seller Dashboard"
            wire:navigate.hover
        >
            <span id="sellerSidebarLogoStage" class="relative block h-[46px] overflow-visible">
                {{-- Normal wordmark --}}
                <img
                    id="sellerSidebarLogoFull"
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    draggable="false"
                    class="absolute left-1/2 top-1/2 h-auto w-[104px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain brightness-0 select-none"
                >

                {{-- Compact logo — shown only while sidebar is collapsed --}}
                <img
                    id="sellerSidebarLogoCompact"
                    src="{{ asset('images/sari-main-logo.png') }}"
                    alt="SARI"
                    draggable="false"
                    class="pointer-events-none absolute left-1/2 top-1/2 h-[40px] w-[40px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain opacity-0 select-none"
                >
            </span>
        </a>

        <button
            id="sellerSidebarClose"
            type="button"
            class="
                absolute right-3 top-1/2
                grid h-8 w-8 -translate-y-1/2
                place-items-center rounded-xl
                text-[#6e6558]
                transition-colors duration-150
                hover:bg-[#f7eedf] hover:text-[#b97805]
                lg:hidden
            "
            aria-label="Close seller sidebar"
        >
            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    {{-- =====================================================
        NAVIGATION
    ====================================================== --}}
    <nav id="sellerSidebarNav" class="flex-1 space-y-0 overflow-y-auto px-2.5 py-3">

        <a
            href="{{ route('seller.dashboard') }}"
            title="Dashboard Overview"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-semibold {{ request()->routeIs('seller.dashboard') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5.5 10v10h13V10"></path>
                <path d="M9.5 20v-6h5v6"></path>
            </svg>
            <span class="seller-sidebar-label whitespace-nowrap">Dashboard Overview</span>
        </a>

        <a
            href="{{ route('seller.orders') }}"
            title="Order Management"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium {{ request()->routeIs('seller.orders') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
                <path d="M8 12h8"></path>
            </svg>
            <span class="seller-sidebar-label whitespace-nowrap">Order Management</span>
        </a>

        <a
            href="{{ route('seller.products.archive') }}"
            title="Archived Products"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium {{ request()->routeIs('seller.products.archive') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 7h16"></path>
                <path d="M6 7v12h12V7"></path>
                <path d="M9 11h6"></path>
            </svg>
            <span class="seller-sidebar-label whitespace-nowrap">Archived Products</span>
        </a>

        <a
            href="{{ route('seller.reports') }}"
            title="Generate Report"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium {{ request()->routeIs('seller.reports') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M5 3h10l4 4v14H5z"></path>
                <path d="M15 3v5h5"></path>
                <path d="M9 13h6"></path>
                <path d="M9 17h4"></path>
            </svg>
            <span class="seller-sidebar-label whitespace-nowrap">Generate Report</span>
        </a>

        <a
            href="{{ route('seller.messages') }}"
            title="Chat / Messaging"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center justify-between min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium {{ request()->routeIs('seller.messages') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <span class="seller-sidebar-nav-main flex items-center gap-2.5">
                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                    <path d="M8 14h5"></path>
                </svg>
                <span class="seller-sidebar-label whitespace-nowrap">Chat / Messaging</span>
            </span>

            <span
                id="sellerSidebarMessageBadge"
                data-seller-unread-badge
                data-has-unread="{{ $sellerUnreadMessages > 0 ? 'true' : 'false' }}"
                class="seller-sidebar-extra {{ $sellerUnreadMessages > 0 ? 'grid' : 'hidden' }} h-[18px] min-w-[18px] place-items-center rounded-full px-[5px] text-[8px] font-bold {{ request()->routeIs('seller.messages') ? 'bg-white text-[#d9930a]' : 'bg-[#d9930a] text-white' }}"
            >
                {{ $sellerUnreadMessages > 99 ? '99+' : $sellerUnreadMessages }}
            </span>
        </a>

        <a
            href="{{ route('seller.account') }}"
            title="Account Management"
            data-seller-sidebar-item
            class="seller-sidebar-item flex items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium {{ request()->routeIs('seller.account') ? 'bg-[#d9930a] text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            wire:navigate.hover
        >
            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="3.5"></circle>
                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
            </svg>
            <span class="seller-sidebar-label whitespace-nowrap">Account Management</span>
        </a>

        <div class="my-2 border-t border-[#eee4d3]"></div>

        <form method="POST" action="{{ route('seller.logout') }}">
            @csrf
            <button
                type="submit"
                title="Logout"
                data-seller-sidebar-item
                class="seller-sidebar-item flex w-full items-center min-h-[38px] gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium text-[#514b42] hover:bg-red-50 hover:text-red-600"
            >
                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>
                <span class="seller-sidebar-label whitespace-nowrap">Logout</span>
            </button>
        </form>
    </nav>

    {{-- =====================================================
        PROFILE
    ====================================================== --}}
    <div id="sellerSidebarProfile" class="border-t border-[#eee4d3] bg-[#fffdf8] px-2.5 py-2.5">
        <a
            href="{{ route('seller.account') }}"
            title="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}"
            data-seller-sidebar-item
            class="seller-sidebar-profile-card flex w-full items-center gap-2 rounded-[10px] border border-[#eadfca] bg-white p-2.5 transition hover:border-[#dbc396] hover:bg-[#fffaf5]"
            wire:navigate.hover
        >
            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[9.5px] font-semibold text-white">
                {{ strtoupper(substr($sellerLayoutAccount?->store_name ?: 'SS', 0, 2)) }}
            </div>

            <div class="seller-sidebar-label min-w-0 flex-1">
                <p class="truncate text-[10px] font-semibold text-[#211d17]">
                    {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                </p>
                <p class="mt-0.5 truncate text-[8px] text-[#8d8272]">
                    {{ $sellerLayoutAccount?->isSuspended() ? 'Suspended Store' : 'Verified Store' }}
                </p>
            </div>

            <svg viewBox="0 0 24 24" class="seller-sidebar-extra h-[13px] w-[13px] shrink-0 text-[#82796b]" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </a>
    </div>
</aside>
