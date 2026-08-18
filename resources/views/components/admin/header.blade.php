<header
    class="
        sticky top-0 z-40
        border-b border-[#eee4d3]
        bg-[#fffdf9]/95
        backdrop-blur-md
    "
>

    <div
        class="
            flex min-h-[86px] w-full
            items-center justify-between
            gap-3
            px-4

            sm:px-6
            lg:px-8
            xl:px-10
        "
    >

        {{-- ======================================================
            LEFT SIDE
        ======================================================= --}}
        <div class="flex min-w-0 items-center gap-3 sm:gap-4">

            {{-- Mobile Menu Button --}}
            <button
                id="adminMenuButton"
                type="button"
                class="
                    grid h-11 w-11 shrink-0
                    place-items-center
                    rounded-xl
                    border border-[#e8dfd0]
                    bg-white
                    text-[#4f473c]
                    shadow-sm
                    transition

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]

                    lg:hidden
                "
                aria-label="Open admin menu"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>


            {{-- Desktop Sidebar Toggle --}}
            <button
                id="adminSidebarToggle"
                type="button"
                class="
                    hidden h-11 w-11 shrink-0
                    place-items-center
                    rounded-xl
                    border border-[#e8dfd0]
                    bg-white
                    text-[#4f473c]
                    shadow-sm
                    transition-all duration-200

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]
                    hover:shadow-md

                    active:scale-95

                    lg:grid
                "
                aria-label="Collapse sidebar"
                aria-expanded="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-5 w-5
                        transition-transform duration-300
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>


            {{-- Page Title --}}
            <div class="min-w-0">

                <h1
                    class="
                        truncate
                        text-[18px] font-bold
                        tracking-[-0.025em]
                        text-[#17140e]

                        sm:text-[20px]
                        xl:text-[22px]
                    "
                >
                    @yield('page-title', 'Dashboard Overview')
                </h1>


                {{-- Breadcrumb --}}
                <div
                    class="
                        mt-1 hidden
                        items-center gap-1.5
                        text-[11px]
                        text-[#978d7d]

                        sm:flex
                    "
                >
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="
                            transition
                            hover:text-[#b97805]
                        "
                    >
                        Home
                    </a>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-3 w-3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>

                    <span class="font-medium text-[#5c5448]">
                        @yield('page-title', 'Dashboard')
                    </span>
                </div>

            </div>

        </div>


        {{-- ======================================================
            RIGHT SIDE
        ======================================================= --}}
        <div class="flex shrink-0 items-center gap-2 sm:gap-3">


            {{-- ==================================================
                DESKTOP SEARCH
            =================================================== --}}
            <div
                id="adminSearchWrapper"
                class="relative hidden md:block"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute left-4 top-1/2
                        h-[18px] w-[18px]
                        -translate-y-1/2
                        text-[#9a9184]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-4-4"></path>
                </svg>

                <input
                    id="adminGlobalSearch"
                    type="search"
                    placeholder="Search admin pages..."
                    autocomplete="off"
                    class="
                        h-11
                        w-[210px]
                        rounded-[14px]
                        border border-[#e6ddcf]
                        bg-white
                        pl-11 pr-10
                        text-[12px]
                        text-[#302a22]
                        outline-none
                        transition

                        placeholder:text-[#a89f92]

                        focus:border-[#d89a25]
                        focus:ring-4
                        focus:ring-[#d89a25]/10

                        lg:w-[250px]
                        xl:w-[300px]
                        2xl:w-[340px]
                    "
                >

                {{-- Clear Search --}}
                <button
                    id="adminSearchClear"
                    type="button"
                    class="
                        absolute right-3 top-1/2
                        hidden h-6 w-6
                        -translate-y-1/2
                        place-items-center
                        rounded-md
                        text-[#9b9285]
                        transition

                        hover:bg-[#f7efe2]
                        hover:text-[#a96e05]
                    "
                    aria-label="Clear search"
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M6 6l12 12"></path>
                        <path d="M18 6 6 18"></path>
                    </svg>
                </button>


                {{-- Search Results Dropdown --}}
                <div
                    id="adminSearchResults"
                    class="
                        absolute right-0 top-[calc(100%+12px)]
                        hidden w-[360px]
                        overflow-hidden
                        rounded-[18px]
                        border border-[#e9e0d3]
                        bg-white
                        shadow-[0_18px_50px_rgba(71,55,32,0.14)]
                    "
                >

                    <div
                        class="
                            flex items-center justify-between
                            border-b border-[#eee7dc]
                            px-4 py-3.5
                        "
                    >
                        <div>
                            <p class="text-[11px] font-bold text-[#302a22]">
                                Quick Search
                            </p>

                            <p
                                id="adminSearchHint"
                                class="mt-0.5 text-[9px] text-[#948a7c]"
                            >
                                Search pages and admin tools
                            </p>
                        </div>

                        <span
                            class="
                                rounded-full
                                bg-[#fbf4e7]
                                px-2 py-1
                                text-[8px] font-semibold
                                text-[#a8731f]
                            "
                        >
                            SARI Admin
                        </span>
                    </div>


                    <div
                        id="adminSearchItems"
                        class="max-h-[390px] overflow-y-auto p-2"
                    >

                        <a
                            href="{{ route('admin.dashboard') }}"
                            data-admin-search-item
                            data-search-keywords="dashboard overview home analytics summary statistics"
                            class="
                                flex items-center gap-3
                                rounded-xl
                                px-3 py-3
                                transition

                                hover:bg-[#fbf6ed]
                            "
                        >
                            <span
                                class="
                                    grid h-9 w-9 shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-[#f3f6f8]
                                    text-[#657f94]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 11.5 12 4l9 7.5"></path>
                                    <path d="M5.5 10v10h13V10"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Dashboard Overview
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Platform summary and analytics
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.registrations') }}"
                            data-admin-search-item
                            data-search-keywords="registration account registrations buyer seller courier applications approval pending"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[#ad791f]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="10" cy="8" r="3"></circle>
                                    <path d="M4 20c.5-4 2.7-6 6-6s5.5 2 6 6"></path>
                                    <path d="M17 8h4"></path>
                                    <path d="M19 6v4"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Account Registrations
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Review buyer, seller, courier applications
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.users') }}"
                            data-admin-search-item
                            data-search-keywords="users user accounts buyer seller courier activate suspend delete accounts"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="9" cy="8" r="3"></circle>
                                    <circle cx="17" cy="10" r="2.4"></circle>
                                    <path d="M3 20c.5-4 2.7-6 6-6s5.5 2 6 6"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    User Accounts
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Manage marketplace users
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.seller-compliance') }}"
                            data-admin-search-item
                            data-search-keywords="seller compliance products prohibited warning suspended listings policy"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f6f2f8] text-[#7d6a8c]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 8h14l-1-4H6L5 8Z"></path>
                                    <path d="M6 8v11h12V8"></path>
                                    <path d="m9 14 2 2 4-4"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Seller Compliance
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Review listings and policy violations
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.complaints') }}"
                            data-admin-search-item
                            data-search-keywords="complaints disputes cases evidence refund delivery issues resolve investigation"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#faf0f0] text-[#a96565]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                                    <path d="M9 8h6"></path>
                                    <path d="M9 12h6"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Complaints & Disputes
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Investigate and resolve marketplace cases
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.commissions') }}"
                            data-admin-search-item
                            data-search-keywords="commission management revenue platform rate sales seller net payment"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[#ad791f]">
                                <span class="text-[13px] font-semibold">₱</span>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Commission Management
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Review platform commission records
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.reports') }}"
                            data-admin-search-item
                            data-search-keywords="reports analytics sales commission users generated report download"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f3f6f8] text-[#657f94]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 20h16"></path>
                                    <path d="M7 17v-5"></path>
                                    <path d="M12 17V8"></path>
                                    <path d="M17 17V4"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Reports
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Sales, users, commission and dispute reports
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.platform-settings') }}"
                            data-admin-search-item
                            data-search-keywords="platform settings announcements policies maintenance marketplace settings configuration"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f6f2f8] text-[#7d6a8c]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6a7 7 0 0 0-1.7 1.1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1A7 7 0 0 0 9.2 18l.3 3h5l.3-3"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Platform Settings
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Announcements, policies and controls
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.messages') }}"
                            data-admin-search-item
                            data-search-keywords="chat messaging messages inbox conversations support users"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Chat / Messaging
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Open administrator conversations
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.account') }}"
                            data-admin-search-item
                            data-search-keywords="account management admin profile password security settings administrator"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-[#fbf6ed]"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#f3f6f8] text-[#657f94]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="8" r="3.5"></circle>
                                    <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold text-[#3a342e]">
                                    Account Management
                                </span>

                                <span class="mt-0.5 block text-[8px] text-[#91887c]">
                                    Admin profile and security settings
                                </span>
                            </span>
                        </a>

                    </div>


                    {{-- No Results --}}
                    <div
                        id="adminSearchEmpty"
                        class="
                            hidden
                            px-5 py-8
                            text-center
                        "
                    >
                        <div
                            class="
                                mx-auto
                                grid h-11 w-11
                                place-items-center
                                rounded-xl
                                bg-[#faf5ec]
                                text-[#aa7721]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="11" cy="11" r="6"></circle>
                                <path d="m16 16 4 4"></path>
                            </svg>
                        </div>

                        <p class="mt-3 text-[10px] font-semibold text-[#514a41]">
                            No matching admin page
                        </p>

                        <p class="mt-1 text-[8px] text-[#968d81]">
                            Try another keyword.
                        </p>
                    </div>

                </div>

            </div>


            {{-- ==================================================
                MOBILE SEARCH BUTTON
            =================================================== --}}
            <button
                id="adminMobileSearchButton"
                type="button"
                class="
                    grid h-11 w-11
                    place-items-center
                    rounded-xl
                    border border-[#e8dfd0]
                    bg-white
                    text-[#51493d]
                    transition

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]

                    md:hidden
                "
                aria-label="Search"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-4-4"></path>
                </svg>
            </button>


            {{-- ==================================================
                NOTIFICATION
            =================================================== --}}
            <div class="relative">

                <button
                    id="adminNotificationButton"
                    type="button"
                    class="
                        relative
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#e8dfd0]
                        bg-white
                        text-[#443d33]
                        shadow-sm
                        transition

                        hover:border-[#d9be8c]
                        hover:bg-[#fff9ef]
                        hover:text-[#b97805]
                    "
                    aria-label="Notifications"
                    aria-expanded="false"
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[20px] w-[20px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                        <path d="M10 21h4"></path>
                    </svg>


                    {{-- Notification Count --}}
                    <span
                        id="adminNotificationBadge"
                        class="
                            absolute -right-1.5 -top-1.5
                            grid h-[21px] min-w-[21px]
                            place-items-center
                            rounded-full
                            border-2 border-[#fffdf9]
                            bg-[#d9930a]
                            px-1
                            text-[9px] font-bold
                            leading-none
                            text-white
                        "
                    >
                        3
                    </span>

                </button>


                {{-- Notification Dropdown --}}
                <div
                    id="adminNotificationPanel"
                    class="
                        absolute right-0 top-[calc(100%+12px)]
                        hidden w-[340px]
                        overflow-hidden
                        rounded-[18px]
                        border border-[#e9e0d3]
                        bg-white
                        shadow-[0_18px_50px_rgba(71,55,32,0.14)]

                        sm:w-[380px]
                    "
                >

                    <div
                        class="
                            flex items-center justify-between
                            border-b border-[#eee7dc]
                            px-4 py-4
                        "
                    >
                        <div>
                            <p class="text-[12px] font-bold text-[#302a22]">
                                Notifications
                            </p>

                            <p class="mt-0.5 text-[9px] text-[#948a7c]">
                                You have 3 unread updates
                            </p>
                        </div>

                        <button
                            id="adminMarkAllRead"
                            type="button"
                            class="
                                text-[9px] font-semibold
                                text-[#a8731f]
                                transition
                                hover:text-[#805513]
                            "
                        >
                            Mark all as read
                        </button>
                    </div>


                    <div class="max-h-[390px] overflow-y-auto">

                        <a
                            href="{{ route('admin.registrations') }}"
                            data-admin-notification
                            class="
                                relative
                                flex gap-3
                                border-b border-[#f0ebe4]
                                bg-[#fdfaf5]
                                px-4 py-4
                                transition

                                hover:bg-[#fbf6ed]
                            "
                        >
                            <span
                                class="
                                    absolute right-4 top-4
                                    h-2 w-2
                                    rounded-full
                                    bg-[#d9930a]
                                "
                                data-unread-dot
                            ></span>

                            <span
                                class="
                                    grid h-10 w-10 shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-[#fbf3e4]
                                    text-[#ae791f]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="10" cy="8" r="3"></circle>
                                    <path d="M4 20c.5-4 2.7-6 6-6s5.5 2 6 6"></path>
                                    <path d="M17 8h4"></path>
                                    <path d="M19 6v4"></path>
                                </svg>
                            </span>

                            <span class="min-w-0 pr-4">
                                <span class="block text-[10px] font-semibold text-[#37312a]">
                                    New seller application
                                </span>

                                <span class="mt-1 block text-[9px] leading-4 text-[#81786d]">
                                    Maria Santos submitted documents for seller verification.
                                </span>

                                <span class="mt-2 block text-[8px] font-medium text-[#aa7721]">
                                    5 minutes ago
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.complaints') }}"
                            data-admin-notification
                            class="
                                relative
                                flex gap-3
                                border-b border-[#f0ebe4]
                                bg-[#fdfaf5]
                                px-4 py-4
                                transition

                                hover:bg-[#fbf6ed]
                            "
                        >
                            <span
                                class="
                                    absolute right-4 top-4
                                    h-2 w-2
                                    rounded-full
                                    bg-[#d9930a]
                                "
                                data-unread-dot
                            ></span>

                            <span
                                class="
                                    grid h-10 w-10 shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-[#faf0f0]
                                    text-[#a96565]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3 3 20h18L12 3Z"></path>
                                    <path d="M12 9v5"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </span>

                            <span class="min-w-0 pr-4">
                                <span class="block text-[10px] font-semibold text-[#37312a]">
                                    Urgent dispute requires review
                                </span>

                                <span class="mt-1 block text-[9px] leading-4 text-[#81786d]">
                                    Case #CMP-1032 received additional buyer evidence.
                                </span>

                                <span class="mt-2 block text-[8px] font-medium text-[#aa7721]">
                                    18 minutes ago
                                </span>
                            </span>
                        </a>


                        <a
                            href="{{ route('admin.messages') }}"
                            data-admin-notification
                            class="
                                relative
                                flex gap-3
                                bg-[#fdfaf5]
                                px-4 py-4
                                transition

                                hover:bg-[#fbf6ed]
                            "
                        >
                            <span
                                class="
                                    absolute right-4 top-4
                                    h-2 w-2
                                    rounded-full
                                    bg-[#d9930a]
                                "
                                data-unread-dot
                            ></span>

                            <span
                                class="
                                    grid h-10 w-10 shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-[#f1f7f3]
                                    text-[#56816a]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                                </svg>
                            </span>

                            <span class="min-w-0 pr-4">
                                <span class="block text-[10px] font-semibold text-[#37312a]">
                                    New admin message
                                </span>

                                <span class="mt-1 block text-[9px] leading-4 text-[#81786d]">
                                    Pedro Reyes sent proof of delivery for an open case.
                                </span>

                                <span class="mt-2 block text-[8px] font-medium text-[#aa7721]">
                                    1 hour ago
                                </span>
                            </span>
                        </a>

                    </div>


                    <div
                        class="
                            border-t border-[#eee7dc]
                            bg-[#fcfaf7]
                            p-3
                        "
                    >
                        <a
                            href="{{ route('admin.messages') }}"
                            class="
                                flex h-9
                                items-center justify-center
                                rounded-xl
                                border border-[#e4dacb]
                                bg-white
                                text-[9px] font-semibold
                                text-[#675e52]
                                transition

                                hover:border-[#d3bd98]
                                hover:text-[#a8731f]
                            "
                        >
                            Open Message Center
                        </a>
                    </div>

                </div>

            </div>


            {{-- Vertical Divider --}}
            <div
                class="
                    hidden h-9 w-px
                    bg-[#eee4d5]

                    xl:block
                "
            ></div>


            {{-- ==================================================
                DATE
            =================================================== --}}
            <div
                id="adminDateWrapper"
                class="
                    relative
                    hidden

                    xl:block
                "
            >

                <button
                    id="adminDateButton"
                    type="button"
                    class="
                        flex items-center gap-3
                        rounded-xl
                        px-2 py-1.5
                        text-left
                        transition

                        hover:bg-[#fff7e9]
                    "
                    aria-expanded="false"
                >

                    {{-- Calendar Icon --}}
                    <div
                        class="
                            grid h-11 w-11
                            place-items-center
                            rounded-xl
                            bg-[#fff6e5]
                            text-[#bc7a07]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[20px] w-[20px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4"></path>
                            <path d="M8 3v4"></path>
                            <path d="M3 10h18"></path>
                        </svg>
                    </div>


                    {{-- Current Date --}}
                    <div class="min-w-[105px]">

                        <p
                            class="
                                text-[12px] font-semibold
                                text-[#29241d]
                            "
                        >
                            {{ now()->format('M d, Y') }}
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-[10px]
                                text-[#8e8578]
                            "
                        >
                            {{ now()->format('l') }}
                        </p>

                    </div>

                </button>


                {{-- Date Dropdown --}}
                <div
                    id="adminDatePanel"
                    class="
                        absolute right-0 top-[calc(100%+12px)]
                        hidden w-[270px]
                        rounded-[18px]
                        border border-[#e9e0d3]
                        bg-white
                        p-4
                        shadow-[0_18px_50px_rgba(71,55,32,0.14)]
                    "
                >
                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid h-12 w-12
                                shrink-0 place-items-center
                                rounded-2xl
                                bg-[#fbf3e4]
                                text-[#ae791f]
                            "
                        >
                            <span class="text-[17px] font-bold">
                                {{ now()->format('d') }}
                            </span>
                        </div>

                        <div>
                            <p class="text-[11px] font-bold text-[#332d26]">
                                {{ now()->format('l') }}
                            </p>

                            <p class="mt-1 text-[9px] text-[#91887c]">
                                {{ now()->format('F d, Y') }}
                            </p>
                        </div>

                    </div>

                    <div
                        class="
                            mt-4
                            rounded-xl
                            border border-[#eee7dd]
                            bg-[#fcfaf7]
                            p-3
                        "
                    >
                        <p class="text-[9px] font-semibold text-[#5d554b]">
                            Admin reminder
                        </p>

                        <p class="mt-1 text-[8px] leading-4 text-[#91887c]">
                            Review pending registrations and urgent disputes before end of day.
                        </p>
                    </div>
                </div>

            </div>


            {{-- Vertical Divider --}}
            <div
                class="
                    hidden h-9 w-px
                    bg-[#eee4d5]

                    2xl:block
                "
            ></div>


            {{-- ==================================================
                ADMIN MINI PROFILE
            =================================================== --}}
            <div
                id="adminProfileWrapper"
                class="
                    relative
                    hidden

                    2xl:block
                "
            >

                <button
                    id="adminProfileButton"
                    type="button"
                    class="
                        flex items-center gap-2.5
                        rounded-xl
                        px-2 py-1.5
                        text-left
                        transition

                        hover:bg-[#fff7e9]
                    "
                    aria-expanded="false"
                >

                    <div
                        class="
                            grid h-10 w-10
                            shrink-0 place-items-center
                            rounded-full
                            bg-[#d9930a]
                            text-[12px] font-bold
                            text-white
                        "
                    >
                        AD
                    </div>

                    <div class="min-w-0">

                        <p
                            class="
                                whitespace-nowrap
                                text-[12px] font-semibold
                                text-[#28231c]
                            "
                        >
                            Admin User
                        </p>

                        <p
                            class="
                                mt-0.5 whitespace-nowrap
                                text-[9px]
                                text-[#908779]
                            "
                        >
                            Super Administrator
                        </p>

                    </div>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4 text-[#8f8678]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="m8 10 4 4 4-4"></path>
                    </svg>

                </button>


                {{-- Profile Dropdown --}}
                <div
                    id="adminProfilePanel"
                    class="
                        absolute right-0 top-[calc(100%+12px)]
                        hidden w-[260px]
                        overflow-hidden
                        rounded-[18px]
                        border border-[#e9e0d3]
                        bg-white
                        shadow-[0_18px_50px_rgba(71,55,32,0.14)]
                    "
                >

                    <div
                        class="
                            border-b border-[#eee7dc]
                            bg-[#fcfaf7]
                            p-4
                        "
                    >
                        <div class="flex items-center gap-3">

                            <div
                                class="
                                    grid h-11 w-11 shrink-0
                                    place-items-center
                                    rounded-full
                                    bg-[#d9930a]
                                    text-[12px] font-bold
                                    text-white
                                "
                            >
                                AD
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-[11px] font-bold text-[#302a22]">
                                    Admin User
                                </p>

                                <p class="mt-0.5 truncate text-[8px] text-[#948a7c]">
                                    admin@gmail.com
                                </p>
                            </div>

                        </div>
                    </div>


                    <div class="p-2">

                        <a
                            href="{{ route('admin.account') }}"
                            class="
                                flex items-center gap-3
                                rounded-xl
                                px-3 py-3
                                text-[10px] font-medium
                                text-[#625a50]
                                transition

                                hover:bg-[#fbf6ed]
                                hover:text-[#a8731f]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="8" r="3.5"></circle>
                                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                            </svg>

                            Account Management
                        </a>


                        <a
                            href="{{ route('admin.platform-settings') }}"
                            class="
                                flex items-center gap-3
                                rounded-xl
                                px-3 py-3
                                text-[10px] font-medium
                                text-[#625a50]
                                transition

                                hover:bg-[#fbf6ed]
                                hover:text-[#a8731f]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6"></path>
                            </svg>

                            Platform Settings
                        </a>


                        <a
                            href="{{ route('admin.messages') }}"
                            class="
                                flex items-center gap-3
                                rounded-xl
                                px-3 py-3
                                text-[10px] font-medium
                                text-[#625a50]
                                transition

                                hover:bg-[#fbf6ed]
                                hover:text-[#a8731f]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                            </svg>

                            Messages
                        </a>

                    </div>


                    <div class="border-t border-[#eee7dc] p-2">

                        <form
                            method="POST"
                            action="{{ route('admin.logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="
                                    flex w-full items-center gap-3
                                    rounded-xl
                                    px-3 py-3
                                    text-left
                                    text-[10px] font-medium
                                    text-[#a45f5f]
                                    transition

                                    hover:bg-red-50
                                    hover:text-red-600
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M10 17l5-5-5-5"></path>
                                    <path d="M15 12H3"></path>
                                    <path d="M14 3h7v18h-7"></path>
                                </svg>

                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ==========================================================
        MOBILE SEARCH PANEL
    =========================================================== --}}
    <div
        id="adminMobileSearchPanel"
        class="
            absolute inset-x-0 top-full
            hidden
            border-t border-[#eee5d8]
            bg-[#fffdf9]
            p-4
            shadow-[0_15px_35px_rgba(71,55,32,0.12)]

            md:hidden
        "
    >

        <div class="relative">

            <svg
                viewBox="0 0 24 24"
                class="
                    pointer-events-none
                    absolute left-4 top-1/2
                    h-[18px] w-[18px]
                    -translate-y-1/2
                    text-[#9a9184]
                "
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m20 20-4-4"></path>
            </svg>

            <input
                id="adminMobileSearchInput"
                type="search"
                autocomplete="off"
                placeholder="Search admin pages..."
                class="
                    h-11 w-full
                    rounded-[14px]
                    border border-[#e6ddcf]
                    bg-white
                    pl-11 pr-4
                    text-[12px]
                    outline-none

                    focus:border-[#d89a25]
                    focus:ring-4
                    focus:ring-[#d89a25]/10
                "
            >

        </div>


        <div
            id="adminMobileSearchResults"
            class="
                mt-3
                grid grid-cols-2
                gap-2
            "
        >

            <a
                href="{{ route('admin.dashboard') }}"
                data-mobile-search-item
                data-search-keywords="dashboard overview home analytics"
                class="
                    rounded-xl
                    border border-[#ebe4da]
                    bg-white
                    p-3
                    text-[9px] font-semibold
                    text-[#625a50]
                "
            >
                Dashboard
            </a>

            <a
                href="{{ route('admin.registrations') }}"
                data-mobile-search-item
                data-search-keywords="registration applications buyer seller courier"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Registrations
            </a>

            <a
                href="{{ route('admin.users') }}"
                data-mobile-search-item
                data-search-keywords="users accounts buyer seller courier"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                User Accounts
            </a>

            <a
                href="{{ route('admin.seller-compliance') }}"
                data-mobile-search-item
                data-search-keywords="seller compliance products policy"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Compliance
            </a>

            <a
                href="{{ route('admin.complaints') }}"
                data-mobile-search-item
                data-search-keywords="complaints disputes cases"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Complaints
            </a>

            <a
                href="{{ route('admin.commissions') }}"
                data-mobile-search-item
                data-search-keywords="commission management revenue"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Commission
            </a>

            <a
                href="{{ route('admin.reports') }}"
                data-mobile-search-item
                data-search-keywords="reports analytics sales"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Reports
            </a>

            <a
                href="{{ route('admin.platform-settings') }}"
                data-mobile-search-item
                data-search-keywords="settings announcements policies"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Settings
            </a>

            <a
                href="{{ route('admin.messages') }}"
                data-mobile-search-item
                data-search-keywords="chat messaging messages"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                Messages
            </a>

            <a
                href="{{ route('admin.account') }}"
                data-mobile-search-item
                data-search-keywords="account profile admin security"
                class="rounded-xl border border-[#ebe4da] bg-white p-3 text-[9px] font-semibold text-[#625a50]"
            >
                My Account
            </a>

        </div>

        <div
            id="adminMobileSearchEmpty"
            class="
                hidden
                py-6
                text-center
                text-[10px]
                text-[#91887c]
            "
        >
            No matching page found.
        </div>

    </div>

</header>


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const searchWrapper = document.getElementById('adminSearchWrapper');
        const searchInput = document.getElementById('adminGlobalSearch');
        const searchResults = document.getElementById('adminSearchResults');
        const searchClear = document.getElementById('adminSearchClear');
        const searchItems = Array.from(
            document.querySelectorAll('[data-admin-search-item]')
        );
        const searchEmpty = document.getElementById('adminSearchEmpty');
        const searchHint = document.getElementById('adminSearchHint');


        const mobileSearchButton = document.getElementById(
            'adminMobileSearchButton'
        );

        const mobileSearchPanel = document.getElementById(
            'adminMobileSearchPanel'
        );

        const mobileSearchInput = document.getElementById(
            'adminMobileSearchInput'
        );

        const mobileSearchItems = Array.from(
            document.querySelectorAll('[data-mobile-search-item]')
        );

        const mobileSearchEmpty = document.getElementById(
            'adminMobileSearchEmpty'
        );


        const notificationButton = document.getElementById(
            'adminNotificationButton'
        );

        const notificationPanel = document.getElementById(
            'adminNotificationPanel'
        );

        const notificationBadge = document.getElementById(
            'adminNotificationBadge'
        );

        const markAllReadButton = document.getElementById(
            'adminMarkAllRead'
        );


        const dateButton = document.getElementById('adminDateButton');
        const datePanel = document.getElementById('adminDatePanel');


        const profileButton = document.getElementById(
            'adminProfileButton'
        );

        const profilePanel = document.getElementById(
            'adminProfilePanel'
        );


        /*
        |--------------------------------------------------------------------------
        | HELPERS
        |--------------------------------------------------------------------------
        */

        function closeDesktopSearch() {
            searchResults?.classList.add('hidden');
        }


        function closeNotifications() {
            notificationPanel?.classList.add('hidden');

            notificationButton?.setAttribute(
                'aria-expanded',
                'false'
            );
        }


        function closeDatePanel() {
            datePanel?.classList.add('hidden');

            dateButton?.setAttribute(
                'aria-expanded',
                'false'
            );
        }


        function closeProfilePanel() {
            profilePanel?.classList.add('hidden');

            profileButton?.setAttribute(
                'aria-expanded',
                'false'
            );
        }


        function closeMobileSearch() {
            mobileSearchPanel?.classList.add('hidden');
        }


        function closeAllExcept(except) {

            if (except !== 'search') {
                closeDesktopSearch();
            }

            if (except !== 'notification') {
                closeNotifications();
            }

            if (except !== 'date') {
                closeDatePanel();
            }

            if (except !== 'profile') {
                closeProfilePanel();
            }

            if (except !== 'mobile-search') {
                closeMobileSearch();
            }

        }


        /*
        |--------------------------------------------------------------------------
        | DESKTOP SEARCH
        |--------------------------------------------------------------------------
        */

        function filterDesktopSearch() {

            if (!searchInput) {
                return;
            }

            const query = searchInput.value
                .trim()
                .toLowerCase();

            let visibleCount = 0;


            searchItems.forEach(function (item) {

                const text = (
                    item.textContent +
                    ' ' +
                    (item.dataset.searchKeywords || '')
                ).toLowerCase();


                const matches =
                    query === '' ||
                    text.includes(query);


                item.classList.toggle(
                    'hidden',
                    !matches
                );


                if (matches) {
                    visibleCount++;
                }

            });


            searchEmpty?.classList.toggle(
                'hidden',
                visibleCount !== 0
            );


            if (searchHint) {

                if (query === '') {
                    searchHint.textContent =
                        'Search pages and admin tools';
                } else {
                    searchHint.textContent =
                        visibleCount +
                        (visibleCount === 1
                            ? ' result found'
                            : ' results found');
                }

            }


            searchClear?.classList.toggle(
                'hidden',
                query === ''
            );


            searchResults?.classList.remove('hidden');

        }


        searchInput?.addEventListener('focus', function () {

            closeAllExcept('search');
            filterDesktopSearch();

        });


        searchInput?.addEventListener(
            'input',
            filterDesktopSearch
        );


        searchClear?.addEventListener('click', function () {

            if (!searchInput) {
                return;
            }

            searchInput.value = '';
            searchInput.focus();

            filterDesktopSearch();

        });


        /*
        |--------------------------------------------------------------------------
        | MOBILE SEARCH
        |--------------------------------------------------------------------------
        */

        mobileSearchButton?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                const willOpen =
                    mobileSearchPanel?.classList.contains('hidden');


                closeAllExcept('mobile-search');


                if (willOpen) {

                    mobileSearchPanel?.classList.remove('hidden');

                    window.setTimeout(function () {
                        mobileSearchInput?.focus();
                    }, 50);

                } else {
                    closeMobileSearch();
                }

            }
        );


        mobileSearchInput?.addEventListener(
            'input',
            function () {

                const query =
                    this.value
                        .trim()
                        .toLowerCase();

                let visibleCount = 0;


                mobileSearchItems.forEach(function (item) {

                    const text = (
                        item.textContent +
                        ' ' +
                        (item.dataset.searchKeywords || '')
                    ).toLowerCase();


                    const matches =
                        query === '' ||
                        text.includes(query);


                    item.classList.toggle(
                        'hidden',
                        !matches
                    );


                    if (matches) {
                        visibleCount++;
                    }

                });


                mobileSearchEmpty?.classList.toggle(
                    'hidden',
                    visibleCount !== 0
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        notificationButton?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                const willOpen =
                    notificationPanel?.classList.contains('hidden');


                closeAllExcept('notification');


                if (willOpen) {

                    notificationPanel?.classList.remove('hidden');

                    notificationButton.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                } else {
                    closeNotifications();
                }

            }
        );


        markAllReadButton?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();


                document
                    .querySelectorAll('[data-unread-dot]')
                    .forEach(function (dot) {
                        dot.classList.add('hidden');
                    });


                document
                    .querySelectorAll('[data-admin-notification]')
                    .forEach(function (notification) {
                        notification.classList.remove('bg-[#fdfaf5]');
                    });


                notificationBadge?.classList.add('hidden');


                const notificationSubtitle =
                    notificationPanel?.querySelector(
                        '.text-\\[9px\\].text-\\[\\#948a7c\\]'
                    );


                if (notificationSubtitle) {
                    notificationSubtitle.textContent =
                        'You are all caught up';
                }


                markAllReadButton.textContent = 'All read';
                markAllReadButton.disabled = true;

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DATE PANEL
        |--------------------------------------------------------------------------
        */

        dateButton?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                const willOpen =
                    datePanel?.classList.contains('hidden');


                closeAllExcept('date');


                if (willOpen) {

                    datePanel?.classList.remove('hidden');

                    dateButton.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                } else {
                    closeDatePanel();
                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PROFILE MENU
        |--------------------------------------------------------------------------
        */

        profileButton?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                const willOpen =
                    profilePanel?.classList.contains('hidden');


                closeAllExcept('profile');


                if (willOpen) {

                    profilePanel?.classList.remove('hidden');

                    profileButton.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                } else {
                    closeProfilePanel();
                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | OUTSIDE CLICK
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function (event) {

                if (
                    searchWrapper &&
                    !searchWrapper.contains(event.target)
                ) {
                    closeDesktopSearch();
                }


                if (
                    notificationPanel &&
                    notificationButton &&
                    !notificationPanel.contains(event.target) &&
                    !notificationButton.contains(event.target)
                ) {
                    closeNotifications();
                }


                if (
                    datePanel &&
                    dateButton &&
                    !datePanel.contains(event.target) &&
                    !dateButton.contains(event.target)
                ) {
                    closeDatePanel();
                }


                if (
                    profilePanel &&
                    profileButton &&
                    !profilePanel.contains(event.target) &&
                    !profileButton.contains(event.target)
                ) {
                    closeProfilePanel();
                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ESCAPE KEY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {
                    closeAllExcept(null);
                }

            }
        );

    });
</script>

@endpush