<aside
    id="courierSidebar"
    aria-label="Rider navigation"
    class="fixed inset-y-0 left-0 z-50 flex w-[275px] -translate-x-full flex-col border-r border-[#e5e7eb] bg-white transition-all duration-300 ease-out lg:translate-x-0"
>

    {{-- BRAND — full and compact logos follow the existing Rider collapse state. --}}
    <div
        class="relative flex min-h-[110px] items-center justify-center border-b border-[#e5e7eb] px-4"
    >

        <a
            id="courierSidebarLogo"
            href="{{ route('courier.dashboard') }}"
            class="flex items-center justify-center"
            aria-label="SARI Rider Dashboard"
        >

            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="courier-full-logo h-auto w-[145px] object-contain brightness-0"
            >

            <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI" class="courier-compact-logo h-auto object-contain">
        </a>

        <button
            id="courierSidebarClose"
            type="button"
            class="absolute right-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805] lg:hidden"
            aria-label="Close navigation"
        >

            <svg
                viewBox="0 0 24 24"
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>

        </button>

    </div>

    {{-- NAVIGATION --}}
    <nav
        id="courierSidebarNav"
        aria-label="Rider workspace"
        class="flex-1 overflow-y-auto px-4 py-5"
    >

        {{-- =================================================
            MAIN MENU
        ================================================== --}}

        <p
            class="courier-sidebar-section mb-2 px-2 text-[11px] font-semibold uppercase tracking-[.16em] text-[#667085]"
        >
            Main Menu
        </p>

        <div class="space-y-1.5">

            {{-- Dashboard --}}
            <a
                href="{{ route('courier.dashboard') }}"
                title="Dashboard Overview"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-semibold transition-all duration-200 {{ request()->routeIs( 'courier.dashboard' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v10h13V10"></path>
                    <path d="M9.5 20v-6h5v6"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Dashboard Overview
                </span>

            </a>

        </div>

        <div class="my-4 border-t border-[#e5e7eb]"></div>

        {{-- =================================================
            RIDER OPERATIONS
        ================================================== --}}

        <p
            class="courier-sidebar-section mb-2 px-2 text-[11px] font-semibold uppercase tracking-[.16em] text-[#667085]"
        >
            Rider Operations
        </p>

        <div class="space-y-1.5">

            {{-- Pickup Assignments --}}
            <a
                href="{{ route('courier.requests') }}"
                title="Pickup Assignments"
                data-courier-sidebar-item
                class="flex items-center justify-between gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.requests' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <span class="flex min-w-0 items-center gap-3">

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M6 3h9l3 3v15H6z"></path>
                        <path d="M9 11h6"></path>
                        <path d="M9 15h4"></path>
                        <path d="M15 3v4h4"></path>
                    </svg>

                    <span
                        class="courier-sidebar-label truncate whitespace-nowrap"
                    >
                        Pickup Assignments
                    </span>

                </span>

                @if($pickupBadge > 0)

                    <span
                        class="courier-sidebar-extra grid h-5 min-w-[20px] place-items-center rounded-full px-1.5 text-[13px] font-bold {{ request()->routeIs( 'courier.requests' ) ? 'bg-white/20 text-white' : 'bg-[#fff0cf] text-[#aa6d00]' }}"
                    >
                        {{ $pickupBadge }}
                    </span>

                @endif

            </a>

            {{-- Seller Pickups --}}
            <a
                href="{{ route('courier.pickups') }}"
                title="Seller Pickups"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.pickups' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M5 8h14l-1 12H6L5 8Z"></path>
                    <path d="M8 8a4 4 0 0 1 8 0"></path>
                    <path d="M9 13h6"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Seller Pickups
                </span>

            </a>

            {{-- Last-Mile Deliveries --}}
            <a
                href="{{ route('courier.last-mile') }}"
                title="Last-Mile Deliveries"
                data-courier-sidebar-item
                class="flex items-center justify-between gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.last-mile*' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <span class="flex min-w-0 items-center gap-3">

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 17h3"></path>
                        <path d="M17 17h3"></path>
                        <path d="M7 17h10"></path>
                        <circle cx="8" cy="17" r="2"></circle>
                        <circle cx="18" cy="17" r="2"></circle>
                        <path d="M6 15V8h8l3 4h2v3"></path>
                        <path d="M9 8V5h5v3"></path>
                    </svg>

                    <span
                        class="courier-sidebar-label truncate whitespace-nowrap"
                    >
                        Last-Mile Deliveries
                    </span>

                </span>

                @if($lastMileBadge > 0)

                    <span
                        class="courier-sidebar-extra grid h-5 min-w-[20px] place-items-center rounded-full px-1.5 text-[13px] font-bold {{ request()->routeIs( 'courier.last-mile*' ) ? 'bg-white/20 text-white' : 'bg-[#eaf2f7] text-[#3C6E91]' }}"
                    >
                        {{ $lastMileBadge }}
                    </span>

                @endif

            </a>

            {{-- Active Deliveries --}}
            <a
                href="{{ route('courier.deliveries') }}"
                title="Active Deliveries"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.deliveries' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 32 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="8" cy="18" r="4"></circle>
                    <circle cx="24" cy="18" r="4"></circle>
                    <path d="M8 18h6l4-8h4"></path>
                    <path d="M14 18l-4-9h5"></path>
                    <path d="M18 10l6 8"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Active Deliveries
                </span>

            </a>

            {{-- Parcel QR Scanner --}}
            <a
                href="{{ route('courier.parcel-scanner') }}"
                title="Parcel QR Scanner"
                aria-label="Parcel QR Scanner"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('courier.parcel-scanner*') ? 'bg-[#d9930a] text-white' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
                @if(request()->routeIs('courier.parcel-scanner*')) aria-current="page" @endif
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M8 21H5a2 2 0 0 1-2-2v-3"/>
                    <rect x="7" y="7" width="3" height="3" rx=".5"/>
                    <rect x="14" y="7" width="3" height="3" rx=".5"/>
                    <rect x="7" y="14" width="3" height="3" rx=".5"/>
                    <path d="M14 14h3v3h-3z"/>
                </svg>
                <span class="courier-sidebar-label whitespace-nowrap">Parcel QR Scanner</span>
            </a>

        </div>

        <div class="my-4 border-t border-[#e5e7eb]"></div>

        {{-- =================================================
            MANAGEMENT
        ================================================== --}}

        <p
            class="courier-sidebar-section mb-2 px-2 text-[11px] font-semibold uppercase tracking-[.16em] text-[#667085]"
        >
            Management
        </p>

        <div class="space-y-1.5">

            {{-- Earnings --}}
            <a
                href="{{ route('courier.earnings') }}"
                title="Earnings"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.earnings*' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M12 2v20"></path>
                    <path d="M17 6.5c0-1.7-2.2-3-5-3s-5 1.3-5 3 2.2 3 5 3 5 1.3 5 3-2.2 3-5 3-5-1.3-5-3"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Earnings
                </span>

            </a>

            {{-- COD Remittance --}}
            <a
                href="{{ route('courier.remittances.index') }}"
                title="COD Remittance"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('courier.remittances.*') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M7 9h.01M17 15h.01"></path>
                </svg>
                <span class="courier-sidebar-label whitespace-nowrap">COD Remittance</span>
            </a>

            {{-- History --}}
            <a
                href="{{ route('courier.history') }}"
                title="Delivery History"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.history*' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path>
                    <path d="M3 3v5h5"></path>
                    <path d="M12 7v5l3 2"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Delivery History
                </span>

            </a>

            {{-- Messages --}}
            <a
                href="{{ route('courier.messages') }}"
                title="Chat / Messaging"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.messages*' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                    <path d="M8 14h5"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Chat / Messaging
                </span>

            </a>

            {{-- Profile --}}
            <a
                href="{{ route('courier.profile') }}"
                title="Account Management"
                data-courier-sidebar-item
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs( 'courier.profile*' ) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Account Management
                </span>

            </a>

        </div>

        <div class="my-4 border-t border-[#e5e7eb]"></div>

        {{-- Logout --}}
        <form
            method="POST"
            action="{{ route('courier.logout') }}"
        >
            @csrf

            <button
                type="submit"
                title="Logout"
                data-courier-sidebar-item
                class="flex w-full items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium text-[#514b42] transition-all duration-200 hover:bg-red-50 hover:text-red-600"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>

                <span class="courier-sidebar-label whitespace-nowrap">
                    Logout
                </span>

            </button>
        </form>

    </nav>

    {{-- =====================================================
        SIDEBAR PROFILE
    ====================================================== --}}

    <div
        id="courierSidebarProfile"
        class="border-t border-[#e5e7eb] bg-white p-4 transition-all duration-300"
    >

        <a
            href="{{ route('courier.profile') }}"
            title="{{ $courierName }}"
            data-courier-sidebar-item
            class="flex w-full items-center gap-3 rounded-2xl border border-[#eadfca] bg-white p-3.5 transition hover:border-[#dbc396] hover:shadow-sm"
        >

            <div
                class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#3C6E91] text-[12px] font-semibold text-white"
            >
                {{ $courierInitial ?: 'R' }}
            </div>

            <div class="courier-sidebar-label min-w-0 flex-1">

                <p
                    class="truncate text-[12px] font-semibold text-[#211d17]"
                >
                    {{ $courierName }}
                </p>

                <p
                    class="mt-0.5 truncate text-[13px] text-[#667085]"
                >
                    {{ $courierVehicle }}
                    · Verified Rider
                </p>

            </div>

            <svg
                viewBox="0 0 24 24"
                class="courier-sidebar-extra h-4 w-4 shrink-0 text-[#82796b]"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="m9 18 6-6-6-6"></path>
            </svg>

        </a>

    </div>

</aside>
