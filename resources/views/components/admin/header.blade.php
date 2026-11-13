@php
    $adminShellAccount = session('is_admin') && session('admin_account_id')
        ? \App\Models\Accounts\AdminAccount::query()->find((int) session('admin_account_id'))
        : null;
    $adminShellName = trim((string) ($adminShellAccount?->name ?? 'Administrator')) ?: 'Administrator';
    $adminShellEmail = (string) ($adminShellAccount?->email ?? '');
    $adminShellRole = match ($adminShellAccount?->role) {
        'super_admin' => 'Super Administrator',
        'admin' => 'Administrator',
        default => \Illuminate\Support\Str::headline((string) ($adminShellAccount?->role ?? 'Administrator')),
    };
    $adminShellInitials = collect(preg_split('/\s+/u', $adminShellName, -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
@endphp




<style>
    /* ============================================================
       SARI ADMIN NAVBAR — COMPACT SIZING ONLY
       No routes, queries, notification logic, search logic or JS
       behavior are modified.
       ============================================================ */

    #adminMenuButton,
    #adminSidebarToggle,
    #adminMobileSearchButton,
    #adminNotificationButton {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
    }

    #adminMenuButton svg,
    #adminSidebarToggle svg,
    #adminMobileSearchButton svg {
        width: 17px !important;
        height: 17px !important;
    }

    #adminNotificationButton > svg {
        width: 17px !important;
        height: 17px !important;
    }

    header > div:first-child {
        min-height: 72px !important;
        gap: 10px !important;
        padding-left: 18px !important;
        padding-right: 18px !important;
    }

    header > div:first-child > div:first-child {
        gap: 10px !important;
    }

    header h1 {
        font-size: 17px !important;
        line-height: 1.15 !important;
    }

    header h1 + div {
        margin-top: 3px !important;
        gap: 5px !important;
        font-size: 9px !important;
    }

    header h1 + div svg {
        width: 10px !important;
        height: 10px !important;
    }

    header > div:first-child > div:last-child {
        gap: 8px !important;
    }

    /* Desktop search */
    #adminGlobalSearch {
        height: 38px !important;
        width: 220px !important;
        border-radius: 11px !important;
        padding-left: 38px !important;
        padding-right: 34px !important;
        font-size: 10px !important;
    }

    #adminSearchWrapper > svg {
        left: 13px !important;
        width: 15px !important;
        height: 15px !important;
    }

    #adminSearchClear {
        right: 9px !important;
        width: 22px !important;
        height: 22px !important;
    }

    #adminSearchResults {
        top: calc(100% + 8px) !important;
        width: 320px !important;
        border-radius: 14px !important;
    }

    #adminSearchResults > div:first-child {
        padding: 10px 12px !important;
    }

    #adminSearchResults > div:first-child p:first-child {
        font-size: 9.5px !important;
    }

    #adminSearchHint {
        font-size: 8px !important;
    }

    #adminSearchResults [data-admin-search-item] {
        gap: 9px !important;
        border-radius: 9px !important;
        padding: 8px 10px !important;
    }

    #adminSearchResults [data-admin-search-item] > span:first-child {
        width: 30px !important;
        height: 30px !important;
        border-radius: 9px !important;
    }

    #adminSearchResults [data-admin-search-item] > span:first-child svg {
        width: 14px !important;
        height: 14px !important;
    }

    #adminSearchResults [data-admin-search-item] > span:last-child > span:first-child {
        font-size: 9px !important;
    }

    #adminSearchResults [data-admin-search-item] > span:last-child > span:last-child {
        font-size: 7.5px !important;
    }

    #adminSearchItems {
        max-height: 330px !important;
    }

    #adminSearchEmpty {
        padding-top: 22px !important;
        padding-bottom: 22px !important;
    }

    /* Notification panel */
    #adminNotificationBadge {
        right: -5px !important;
        top: -5px !important;
        min-width: 18px !important;
        height: 18px !important;
        padding-inline: 4px !important;
        font-size: 8px !important;
    }

    #adminNotificationPanel {
        top: calc(100% + 8px) !important;
        width: 320px !important;
        border-radius: 14px !important;
    }

    #adminNotificationPanel > div:first-child {
        padding: 11px 12px !important;
    }

    #adminNotificationPanel > div:first-child p:first-child {
        font-size: 10px !important;
    }

    #adminNotificationUnreadText,
    #adminMarkAllRead {
        font-size: 8px !important;
    }

    #adminNotificationList {
        max-height: 330px !important;
    }

    #adminNotificationList [data-admin-notification] {
        gap: 9px !important;
        padding: 10px 12px !important;
    }

    #adminNotificationList [data-admin-notification] > span:nth-of-type(2),
    #adminNotificationList [data-admin-notification] > span:first-of-type:not([data-unread-dot]) {
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
        font-size: 8px !important;
    }

    #adminNotificationList [data-admin-notification] span.block.truncate {
        font-size: 9px !important;
    }

    #adminNotificationList [data-admin-notification] .line-clamp-2 {
        margin-top: 3px !important;
        font-size: 8px !important;
        line-height: 1.45 !important;
    }

    #adminNotificationList [data-admin-notification] .text-\[8px\] {
        margin-top: 5px !important;
        font-size: 7px !important;
    }

    #adminNotificationPanel > div:last-child {
        padding: 9px !important;
    }

    #adminNotificationPanel > div:last-child a {
        height: 32px !important;
        border-radius: 9px !important;
        font-size: 8px !important;
    }

    /* Date area */
    #adminDateButton {
        gap: 8px !important;
        border-radius: 10px !important;
        padding: 4px 6px !important;
    }

    #adminDateButton > div:first-child {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
    }

    #adminDateButton > div:first-child svg {
        width: 17px !important;
        height: 17px !important;
    }

    #adminDateButton > div:nth-child(2) {
        min-width: 92px !important;
    }

    #adminDateButton > div:nth-child(2) p:first-child {
        font-size: 10.5px !important;
    }

    #adminDateButton > div:nth-child(2) p:last-child {
        font-size: 8.5px !important;
    }

    #adminDatePanel {
        top: calc(100% + 8px) !important;
        width: 240px !important;
        border-radius: 14px !important;
        padding: 12px !important;
    }

    #adminDatePanel > div:first-child {
        gap: 9px !important;
    }

    #adminDatePanel > div:first-child > div:first-child {
        width: 40px !important;
        height: 40px !important;
        border-radius: 11px !important;
    }

    #adminDatePanel > div:first-child > div:first-child span {
        font-size: 14px !important;
    }

    #adminDatePanel > div:first-child > div:last-child p:first-child {
        font-size: 9.5px !important;
    }

    #adminDatePanel > div:first-child > div:last-child p:last-child {
        font-size: 8px !important;
    }

    #adminDatePanel > div:last-child {
        margin-top: 10px !important;
        border-radius: 9px !important;
        padding: 9px !important;
    }

    #adminDatePanel > div:last-child p:first-child {
        font-size: 8px !important;
    }

    #adminDatePanel > div:last-child p:last-child {
        font-size: 7.5px !important;
        line-height: 1.45 !important;
    }

    /* Mini admin profile */
    #adminProfileButton {
        gap: 8px !important;
        border-radius: 10px !important;
        padding: 4px 6px !important;
    }

    #adminProfileButton > div:first-child {
        width: 35px !important;
        height: 35px !important;
        font-size: 10px !important;
    }

    #adminProfileButton > div:nth-child(2) p:first-child {
        font-size: 10.5px !important;
    }

    #adminProfileButton > div:nth-child(2) p:last-child {
        font-size: 8px !important;
    }

    #adminProfileButton > svg {
        width: 13px !important;
        height: 13px !important;
    }

    #adminProfilePanel {
        top: calc(100% + 8px) !important;
        width: 230px !important;
        border-radius: 14px !important;
    }

    #adminProfilePanel > div:first-child {
        padding: 11px !important;
    }

    #adminProfilePanel > div:first-child > div {
        gap: 9px !important;
    }

    #adminProfilePanel > div:first-child > div > div:first-child {
        width: 36px !important;
        height: 36px !important;
        font-size: 10px !important;
    }

    #adminProfilePanel > div:first-child p:first-child {
        font-size: 9.5px !important;
    }

    #adminProfilePanel > div:first-child p:last-child {
        font-size: 7.5px !important;
    }

    #adminProfilePanel > div:nth-child(2),
    #adminProfilePanel > div:last-child {
        padding: 6px !important;
    }

    #adminProfilePanel a,
    #adminProfilePanel button[type="submit"] {
        gap: 8px !important;
        border-radius: 9px !important;
        padding: 8px 9px !important;
        font-size: 9px !important;
    }

    #adminProfilePanel a svg,
    #adminProfilePanel button[type="submit"] svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* Mobile search */
    #adminMobileSearchPanel {
        padding: 12px !important;
    }

    #adminMobileSearchInput {
        height: 40px !important;
        border-radius: 11px !important;
        padding-left: 38px !important;
        font-size: 10.5px !important;
    }

    #adminMobileSearchPanel > div:first-child > svg {
        left: 13px !important;
        width: 15px !important;
        height: 15px !important;
    }

    #adminMobileSearchResults {
        gap: 7px !important;
        margin-top: 9px !important;
    }

    #adminMobileSearchResults a {
        border-radius: 9px !important;
        padding: 9px !important;
        font-size: 8.5px !important;
    }

    /* Laptop / desktop widths */
    @media (min-width: 1024px) {
        #adminGlobalSearch {
            width: 235px !important;
        }
    }

    @media (min-width: 1280px) {
        #adminGlobalSearch {
            width: 260px !important;
        }
    }

    @media (min-width: 1536px) {
        #adminGlobalSearch {
            width: 285px !important;
        }
    }

    /* Mobile/tablet keeps touch-friendly controls */
    @media (max-width: 767px) {
        header > div:first-child {
            min-height: 68px !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        #adminMenuButton,
        #adminMobileSearchButton,
        #adminNotificationButton {
            width: 40px !important;
            height: 40px !important;
        }

        header h1 {
            font-size: 16px !important;
        }
    }
</style>

@include('components.admin.shell-styles')

<header id="adminHeader"
    class="
        sticky top-0 z-40
        border-b border-[#E4E7EC]
        bg-[#FFFFFF]/95
        backdrop-blur-md
    "
>

    <div
        class="
            admin-header-main flex min-h-[82px] w-full
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
        <div class="flex min-w-0 items-center gap-2.5">

            {{-- Mobile Menu Button --}}
            <button
                id="adminMenuButton"
                type="button"
                class="
                    grid h-11 w-11 shrink-0
                    place-items-center
                    rounded-xl
                    border border-[#E4E7EC]
                    bg-white
                    text-[#4f473c]
                    shadow-sm
                    transition

                    hover:border-[#d9be8c]
                    hover:bg-[#F2F4F7]
                    hover:text-[#b97805]

                    lg:hidden
                "
                aria-label="Open admin menu" aria-controls="adminSidebar"
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
                    border border-[#E4E7EC]
                    bg-white
                    text-[#4f473c]
                    shadow-sm
                    transition-all duration-200

                    hover:border-[#d9be8c]
                    hover:bg-[#F2F4F7]
                    hover:text-[#b97805]
                    hover:shadow-md

                    active:scale-95

                    lg:grid
                "
                aria-label="Collapse sidebar" aria-controls="adminSidebar"
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
                        admin-header-breadcrumb mt-1 flex min-w-0
                        items-center gap-2
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
        <div class="admin-header-actions flex shrink-0 items-center gap-1.5 sm:gap-2">


            @include('components.admin.notification-menu')


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
                    id="adminProfileButton" aria-controls="adminProfilePanel" aria-label="Open administrator account menu"
                    type="button"
                    class="
                        admin-header-profile flex items-center gap-2.5
                        rounded-xl
                        px-2 py-1.5
                        text-left
                        transition

                        hover:bg-[#F2F4F7]
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
                        <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI" width="39" height="39" class="admin-header-brand-avatar">
                    </div>

                    <div class="admin-header-profile-details min-w-0">

                        <p
                            class="
                                whitespace-nowrap
                                text-[12px] font-semibold
                                text-[#28231c]
                            "
                        >
                            {{ $adminShellName }}
                        </p>

                        <p
                            class="
                                mt-0.5 whitespace-nowrap
                                text-[9px]
                                text-[#908779]
                            "
                        >
                            {{ $adminShellRole }}
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
                        border border-[#E4E7EC]
                        bg-white
                        shadow-[0_18px_50px_rgba(71,55,32,0.14)]
                    "
                >

                    <div
                        class="
                            border-b border-[#EAECF0]
                            bg-[#F9FAFB]
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
                                <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI" width="39" height="39" class="admin-header-brand-avatar">
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-[11px] font-bold text-[#1D2939]">
                                    {{ $adminShellName }}
                                </p>

                                <p class="mt-0.5 truncate text-[8px] text-[#667085]">
                                    {{ $adminShellEmail }}
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
                                text-[#475467]
                                transition

                                hover:bg-[#F2F4F7]
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
                                text-[#475467]
                                transition

                                hover:bg-[#F2F4F7]
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
                                text-[#475467]
                                transition

                                hover:bg-[#F2F4F7]
                                hover:text-[#a8731f]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                            </svg>

                            Messages
                        </a>

                    </div>


                    <div class="border-t border-[#EAECF0] p-2">

                        <form
                            method="POST"
                            action="{{ route('admin.logout') }}" data-admin-logout-form
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
            document.getElementById('adminLiveNotificationPanel')?.setAttribute('hidden', '');
            document.getElementById('adminSidebarNotifications')?.setAttribute('aria-expanded', 'false');

            document.getElementById('adminLiveNotificationButton')?.setAttribute(
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