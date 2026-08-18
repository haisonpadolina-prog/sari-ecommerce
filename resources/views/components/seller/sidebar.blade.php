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
        >
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    <nav id="sellerSidebarNav" class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">

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
            <span class="seller-sidebar-label whitespace-nowrap">Dashboard Overview</span>
        </a>

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
            <span class="seller-sidebar-label whitespace-nowrap">Order Management</span>
        </a>

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
            <span class="seller-sidebar-label whitespace-nowrap">Archived Products</span>
        </a>

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
            <span class="seller-sidebar-label whitespace-nowrap">Generate Report</span>
        </a>

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
                <span class="seller-sidebar-label whitespace-nowrap">Chat / Messaging</span>
            </span>

            <span class="seller-sidebar-extra grid h-5 min-w-[20px] place-items-center rounded-full bg-[#d9930a] px-1.5 text-[9px] font-bold text-white">
                4
            </span>
        </a>

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
            <span class="seller-sidebar-label whitespace-nowrap">Account Management</span>
        </a>

        <div class="my-4 border-t border-[#eee4d3]"></div>

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
                <span class="seller-sidebar-label whitespace-nowrap">Logout</span>
            </button>
        </form>
    </nav>

    <div id="sellerSidebarProfile" class="border-t border-[#eee4d3] bg-[#fffdf8] p-4 transition-all duration-300">
        <a
            href="{{ route('seller.account') }}"
            title="SARI Seller"
            data-seller-sidebar-item
            class="
                flex w-full items-center gap-3 rounded-2xl
                border border-[#eadfca] bg-white p-3.5
                transition hover:border-[#dbc396] hover:shadow-sm
            "
        >
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[12px] font-semibold text-white">
                SS
            </div>

            <div class="seller-sidebar-label min-w-0 flex-1">
                <p class="truncate text-[12px] font-semibold text-[#211d17]">SARI Seller</p>
                <p class="mt-0.5 truncate text-[9px] text-[#8d8272]">Verified Store</p>
            </div>

            <svg viewBox="0 0 24 24" class="seller-sidebar-extra h-4 w-4 shrink-0 text-[#82796b]" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </a>
    </div>
</aside>
