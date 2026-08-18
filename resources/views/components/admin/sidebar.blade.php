<aside
    id="adminSidebar"
    class="
        fixed inset-y-0 left-0 z-50
        flex w-[275px] -translate-x-full flex-col
        border-r border-[#eee4d3]
        bg-[#fffdf8]
        transition-all duration-300 ease-out
        lg:translate-x-0
    "
>

    {{-- ======================================================
        BRAND / LOGO
    ======================================================= --}}
    <div
        id="adminSidebarBrand"
        class="
            relative
            flex min-h-[110px]
            items-center justify-center
            border-b border-[#eee4d3]
            px-4
        "
    >

        {{-- Centered SARI Logo --}}
        <a
            id="adminSidebarLogo"
            href="{{ route('admin.dashboard') }}"
            class="
                flex items-center justify-center
                transition-all duration-300
            "
            aria-label="SARI Admin Dashboard"
        >
            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="
                    h-auto w-[145px]
                    object-contain
                    brightness-0
                "
            >
        </a>



        {{-- Mobile Close Button --}}
        <button
            id="adminSidebarClose"
            type="button"
            class="
                absolute right-4 top-1/2
                grid h-10 w-10
                -translate-y-1/2
                place-items-center
                rounded-xl
                text-[#6e6558]
                transition

                hover:bg-[#f7eedf]
                hover:text-[#b97805]

                lg:hidden
            "
            aria-label="Close sidebar"
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


    {{-- ======================================================
        NAVIGATION
    ======================================================= --}}
    <nav
        id="adminSidebarNav"
        class="
            flex-1 space-y-1.5
            overflow-y-auto
            px-4 py-6
        "
    >

        {{-- Dashboard --}}
        <a
            href="{{ route('admin.dashboard') }}"
            title="Dashboard Overview"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-semibold
                transition-all duration-200

                {{ request()->routeIs('admin.dashboard')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5.5 10v10h13V10"></path>
                <path d="M9.5 20v-6h5v6"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Dashboard Overview
            </span>
        </a>


        {{-- Account Registrations --}}
        <a
            href="{{ route('admin.registrations') }}"
            title="Account Registrations"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.registrations')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="10" cy="8" r="3.5"></circle>
                <path d="M3.5 20c.5-4 3-6 6.5-6s6 2 6.5 6"></path>
                <path d="M17 8h4"></path>
                <path d="M19 6v4"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Account Registrations
            </span>
        </a>


        {{-- User Accounts --}}
        <a
            href="{{ route('admin.users') }}"
            title="User Accounts"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.users')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="8" r="3"></circle>
                <circle cx="17" cy="10" r="2.5"></circle>
                <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                <path d="M15 15c3.4 0 5.4 1.8 6 5"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                User Accounts
            </span>
        </a>


        {{-- Seller Compliance --}}
        <a
            href="{{ route('admin.seller-compliance') }}"
            title="Seller Compliance"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.seller-compliance')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M5 8h14l-1-4H6L5 8Z"></path>
                <path d="M6 8v11h12V8"></path>
                <path d="m9 14 2 2 4-4"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Seller Compliance
            </span>
        </a>


        {{-- Complaints --}}
        <a
            href="{{ route('admin.complaints') }}"
            title="Complaints & Disputes"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.complaints')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <path d="M9 8h6"></path>
                <path d="M9 12h6"></path>
                <path d="M9 16h3"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Complaints & Disputes
            </span>
        </a>


        {{-- Commission --}}
        <a
            href="{{ route('admin.commissions') }}"
            title="Commission Management"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.commissions')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M9 9h5a2 2 0 1 1 0 4h-4a2 2 0 1 0 0 4h5"></path>
                <path d="M12 6v12"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Commission Management
            </span>
        </a>


        {{-- Reports --}}
        <a
            href="{{ route('admin.reports') }}"
            title="Reports"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.reports')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 20h16"></path>
                <path d="M7 17v-5"></path>
                <path d="M12 17V8"></path>
                <path d="M17 17V4"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Reports
            </span>
        </a>


        {{-- Platform Settings --}}
        <a
            href="{{ route('admin.platform-settings') }}"
            title="Platform Settings"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.platform-settings')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6a7 7 0 0 0-1.7 1.1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1A7 7 0 0 0 9.2 18l.3 3h5l.3-3a7 7 0 0 0 1.7-1.1l2.4 1 2-3.4-2-1.5c.1-.3.1-.7.1-1Z"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Platform Settings
            </span>
        </a>


        {{-- Chat / Messaging --}}
        <a
            href="{{ route('admin.messages') }}"
            title="Chat / Messaging"
            data-sidebar-item
            class="
                group flex items-center justify-between
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.messages')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <span
                class="flex items-center gap-3"
                data-sidebar-inner
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                    <path d="M8 14h5"></path>
                </svg>

                <span class="sidebar-label whitespace-nowrap">
                    Chat / Messaging
                </span>
            </span>

            <span
                class="
                    sidebar-extra
                    grid h-5 min-w-[20px]
                    place-items-center
                    rounded-full
                    px-1.5
                    text-[9px] font-bold

                    {{ request()->routeIs('admin.messages')
                        ? 'bg-white text-[#c48413]'
                        : 'bg-[#d9930a] text-white' }}
                "
            >
                3
            </span>
        </a>


        {{-- Account Management --}}
        <a
            href="{{ route('admin.account') }}"
            title="Account Management"
            data-sidebar-item
            class="
                group flex items-center gap-3
                rounded-xl px-4 py-3.5
                text-[13px] font-medium
                transition-all duration-200

                {{ request()->routeIs('admin.account')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
            "
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="3.5"></circle>
                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
            </svg>

            <span class="sidebar-label whitespace-nowrap">
                Account Management
            </span>
        </a>


        <div class="my-4 border-t border-[#eee4d3]"></div>


        {{-- Logout --}}
        <form
            method="POST"
            action="{{ route('admin.logout') }}"
        >
            @csrf

            <button
                type="submit"
                title="Logout"
                data-sidebar-item
                class="
                    group flex w-full items-center gap-3
                    rounded-xl px-4 py-3.5
                    text-[13px] font-medium
                    text-[#514b42]
                    transition-all duration-200

                    hover:bg-red-50
                    hover:text-red-600
                "
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>

                <span class="sidebar-label whitespace-nowrap">
                    Logout
                </span>
            </button>
        </form>

    </nav>


    {{-- ======================================================
        ADMIN PROFILE
    ======================================================= --}}
    <div
        id="adminSidebarProfile"
        class="
            border-t border-[#eee4d3]
            bg-[#fffdf8]
            p-4
            transition-all duration-300
        "
    >
        <a
            href="{{ route('admin.account') }}"
            title="Admin User"
            data-sidebar-item
            class="
                flex w-full items-center gap-3
                rounded-2xl
                border border-[#eadfca]
                bg-white
                p-3.5
                text-left
                transition-all duration-200

                hover:border-[#dbc396]
                hover:shadow-sm
            "
        >

            <div
                class="
                    grid h-11 w-11 shrink-0
                    place-items-center
                    rounded-full
                    bg-[#d9930a]
                    text-[13px] font-semibold
                    text-white
                "
            >
                AD
            </div>

            <div class="sidebar-label min-w-0 flex-1">

                <p
                    class="
                        truncate
                        text-[13px] font-semibold
                        text-[#211d17]
                    "
                >
                    Admin User
                </p>

                <p
                    class="
                        mt-0.5 truncate
                        text-[10px]
                        text-[#8d8272]
                    "
                >
                    Super Administrator
                </p>

            </div>

            <svg
                viewBox="0 0 24 24"
                class="
                    sidebar-extra
                    h-4 w-4 shrink-0
                    text-[#82796b]
                "
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="m9 18 6-6-6-6"></path>
            </svg>

        </a>
    </div>

</aside>