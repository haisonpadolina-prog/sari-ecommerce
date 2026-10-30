
@once
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/notifications.css') }}?v=20261030-3"
    >
@endonce

@php
    /*
    |--------------------------------------------------------------------------
    | COURIER HEADER NOTIFICATION DATA
    |--------------------------------------------------------------------------
    | Uses existing view data if provided.
    | No database queries or backend modifications.
    */

    $courierHeaderNotifications = collect($courierNotifications ?? []);

    $courierNotificationIsUnread = static function ($notification): bool {
        $unread = data_get($notification, 'unread');

        if ($unread !== null) {
            return filter_var($unread, FILTER_VALIDATE_BOOLEAN);
        }

        $isRead = data_get($notification, 'is_read');

        if ($isRead !== null) {
            return ! filter_var($isRead, FILTER_VALIDATE_BOOLEAN);
        }

        $readAt = data_get(
            $notification,
            'read_at',
            '__missing__'
        );

        if ($readAt !== '__missing__') {
            return $readAt === null;
        }

        return false;
    };

    $courierUnreadCount = $courierHeaderNotifications
        ->filter($courierNotificationIsUnread)
        ->count();

    $courierHasNotificationsPage =
        \Illuminate\Support\Facades\Route::has(
            'courier.notifications.index'
        );
@endphp

<header class="sticky top-0 z-40 border-b border-[#eee4d3] bg-[#fffdf9]/95 backdrop-blur-md">
    <div class="flex min-h-[72px] w-full items-center justify-between gap-[10px] px-[18px]">

        {{-- =====================================================
             LEFT HEADER SECTION
             Existing design preserved
             ===================================================== --}}

        <div class="flex min-w-0 items-center gap-[10px]">

            {{-- MOBILE MENU --}}
            <button
                id="courierMenuButton"
                aria-controls="courierSidebar"
                aria-expanded="false"
                type="button"
                class="grid h-[38px] w-[38px] shrink-0 place-items-center rounded-[10px] border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] lg:hidden"
                aria-label="Open navigation"
            >
                <svg
                    aria-hidden="true"
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            {{-- DESKTOP SIDEBAR TOGGLE --}}
            <button
                id="courierSidebarToggle"
                aria-controls="courierSidebar"
                type="button"
                class="hidden h-[38px] w-[38px] shrink-0 place-items-center rounded-[10px] border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] active:scale-[.97] lg:grid"
                aria-label="Collapse sidebar"
                aria-expanded="true"
            >
                <svg
                    aria-hidden="true"
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            {{-- PAGE TITLE --}}
            <div class="min-w-0">
                <h1 class="truncate text-[17px] font-bold leading-[1.15] tracking-[-0.025em] text-[#17140e]">
                    @yield('header-title', 'Rider Dashboard')
                </h1>

                <div class="mt-[3px] hidden items-center gap-[5px] text-[9px] text-[#978d7d] sm:flex">
                    <a
                        href="{{ route('courier.dashboard') }}"
                        class="transition hover:text-[#b97805]"
                    >
                        Rider
                    </a>

                    <svg
                        aria-hidden="true"
                        viewBox="0 0 24 24"
                        class="h-[10px] w-[10px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>

                    <span class="truncate font-medium text-[#5c5448]">
                        @yield('header-subtitle', 'Dashboard')
                    </span>
                </div>
            </div>
        </div>

        {{-- =====================================================
             RIGHT HEADER SECTION
             Existing design preserved
             ===================================================== --}}

        <div class="flex shrink-0 items-center gap-2">

            {{-- SEARCH --}}
            <div class="relative hidden md:block">
                <svg
                    aria-hidden="true"
                    viewBox="0 0 24 24"
                    class="pointer-events-none absolute left-[13px] top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-[#9a9184]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-4-4"></path>
                </svg>

                <input
                    type="search"
                    aria-label="Search deliveries and orders"
                    placeholder="Search deliveries, orders..."
                    class="h-[38px] w-[220px] rounded-[11px] border border-[#e6ddcf] bg-white pl-[38px] pr-3 text-[10px] text-[#302a22] outline-none placeholder:text-[#a89f92] focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10 lg:w-[235px] xl:w-[260px] 2xl:w-[285px]"
                >
            </div>

            {{-- =================================================
                 RIDER NOTIFICATION CENTER
                 Same structure as Seller header notifications
                 ================================================= --}}

            <div
                id="courierNotificationCenter"
                class="courier-notification-center"
            >

                {{-- NOTIFICATION BELL --}}
                <button
                    id="notificationBell"
                    type="button"
                    class="courier-notification-bell"
                    aria-label="Open notifications"
                    aria-controls="courierNotificationDropdown"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <svg
                        aria-hidden="true"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                        <path d="M10 21h4"></path>
                    </svg>

                    {{-- UNREAD BADGE --}}
                    <span
                        id="courierNotificationBadge"
                        class="courier-notification-badge"
                        @if($courierUnreadCount === 0) hidden @endif
                    >
                        {{ $courierUnreadCount > 99 ? '99+' : $courierUnreadCount }}
                    </span>
                </button>

                {{-- NOTIFICATION DROPDOWN --}}
                <section
                    id="courierNotificationDropdown"
                    class="courier-notification-dropdown"
                    aria-label="Rider notifications"
                    hidden
                >

                    {{-- DROPDOWN HEADER --}}
                    <div class="courier-notification-heading">

                        <div class="courier-notification-heading-copy">
                            <h2 class="courier-notification-title">
                                Notifications
                            </h2>

                            <p
                                id="courierNotificationUnreadText"
                                class="courier-notification-subtitle"
                                aria-live="polite"
                            >
                                @if($courierUnreadCount > 0)
                                    {{ $courierUnreadCount }}
                                    unread
                                    {{ $courierUnreadCount === 1 ? 'notification' : 'notifications' }}
                                @else
                                    No unread notifications
                                @endif
                            </p>
                        </div>

                        <div class="courier-notification-heading-actions">

                            <button
                                id="courierNotificationMarkAllRead"
                                type="button"
                                class="courier-notification-mark-all"
                                @if($courierUnreadCount === 0) disabled @endif
                            >
                                Mark all read
                            </button>

                            @if($courierHasNotificationsPage)
                                <a
                                    href="{{ route('courier.notifications.index') }}"
                                    class="courier-notification-view-all"
                                >
                                    View all
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- NOTIFICATION LIST --}}
                    <div
                        id="courierNotificationList"
                        class="courier-notification-list"
                    >
                        @forelse($courierHeaderNotifications as $notification)

                            @php
                                $isUnread = $courierNotificationIsUnread(
                                    $notification
                                );

                                $notificationTitle = data_get(
                                    $notification,
                                    'title'
                                ) ?? data_get(
                                    $notification,
                                    'data.title',
                                    'Rider update'
                                );

                                $notificationMessage = data_get(
                                    $notification,
                                    'message'
                                ) ?? data_get(
                                    $notification,
                                    'data.message',
                                    ''
                                );

                                $notificationId = data_get(
                                    $notification,
                                    'id',
                                    $loop->index
                                );

                                $notificationCreatedAt = data_get(
                                    $notification,
                                    'created_at'
                                );

                                $notificationTime = data_get(
                                    $notification,
                                    'time_ago'
                                );

                                if (!$notificationTime) {
                                    if ($notificationCreatedAt instanceof \DateTimeInterface) {
                                        $notificationTime =
                                            \Illuminate\Support\Carbon::instance(
                                                $notificationCreatedAt
                                            )->diffForHumans();
                                    } elseif (
                                        is_string($notificationCreatedAt)
                                        && strtotime($notificationCreatedAt) !== false
                                    ) {
                                        $notificationTime =
                                            \Illuminate\Support\Carbon::parse(
                                                $notificationCreatedAt
                                            )->diffForHumans();
                                    } else {
                                        $notificationTime = 'Recently';
                                    }
                                }
                            @endphp

                            <article
                                class="courier-notification-item"
                                data-courier-notification-item
                                data-notification-id="{{ $notificationId }}"
                                data-unread="{{ $isUnread ? 'true' : 'false' }}"
                            >

                                <div class="courier-notification-item-inner">

                                    {{-- ICON --}}
                                    <div class="courier-notification-item-icon">
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <rect
                                                x="3"
                                                y="7"
                                                width="18"
                                                height="13"
                                                rx="2"
                                            ></rect>

                                            <path d="M3 11h18"></path>
                                            <path d="M8 7V4h8v3"></path>
                                        </svg>
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="courier-notification-item-content">

                                        <div class="courier-notification-item-top">

                                            <h3 class="courier-notification-item-title">
                                                {{ $notificationTitle }}
                                            </h3>

                                            <time class="courier-notification-item-time">
                                                {{ $notificationTime }}
                                            </time>
                                        </div>

                                        @if($notificationMessage)
                                            <p class="courier-notification-item-message">
                                                {{ $notificationMessage }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- UNREAD INDICATOR --}}
                                    <span
                                        class="courier-notification-unread-dot"
                                        aria-hidden="true"
                                    ></span>
                                </div>
                            </article>

                        @empty

                            {{-- EMPTY STATE --}}
                            <div
                                id="courierNotificationEmpty"
                                class="courier-notification-empty"
                            >

                                <div class="courier-notification-empty-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                        <path d="M10 21h4"></path>
                                    </svg>
                                </div>

                                <p class="courier-notification-empty-title">
                                    No notifications yet
                                </p>

                                <p class="courier-notification-empty-message">
                                    Pickup assignments, delivery updates,
                                    and important rider announcements
                                    will appear here.
                                </p>
                            </div>

                        @endforelse
                    </div>

                    {{-- DROPDOWN FOOTER --}}
                    @if($courierHasNotificationsPage)
                        <a
                            href="{{ route('courier.notifications.index') }}"
                            class="courier-notification-footer courier-notification-footer-link"
                        >
                            Open Notification Center

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </a>
                    @else
                        <div class="courier-notification-footer">
                            SARI Rider Notification Center
                        </div>
                    @endif

                </section>
            </div>

            {{-- =================================================
                 EXISTING HEADER DIVIDER
                 ================================================= --}}

            <div class="hidden h-7 w-px bg-[#eee4d5] xl:block"></div>

            {{-- =================================================
                 EXISTING PROFILE
                 ================================================= --}}

            <a
                data-courier-header-profile
                href="{{ route('courier.profile') }}"
                class="hidden items-center gap-2 rounded-[10px] px-1.5 py-1 transition hover:bg-[#fff7e9] xl:flex"
            >
                <div class="grid h-[35px] w-[35px] place-items-center rounded-full bg-[#d9930a] text-[10px] font-bold text-white">
                    {{ $courierInitial ?: 'R' }}
                </div>

                <div>
                    <p class="text-[10.5px] font-semibold text-[#28231c]">
                        {{ $courierName }}
                    </p>

                    <p class="mt-0.5 text-[8px] text-[#908779]">
                        Verified Rider
                    </p>
                </div>
            </a>

        </div>
    </div>
</header>

{{-- ==========================================================
     COURIER NOTIFICATION INTERACTIONS
     Inline in header, same component structure as Seller
     ========================================================== --}}

<script>
(function () {
    function initializeCourierHeaderNotifications() {

        const center = document.getElementById(
            'courierNotificationCenter'
        );

        if (!center) {
            return;
        }

        if (center.dataset.initialized === 'true') {
            return;
        }

        const bell = document.getElementById(
            'notificationBell'
        );

        const dropdown = document.getElementById(
            'courierNotificationDropdown'
        );

        const badge = document.getElementById(
            'courierNotificationBadge'
        );

        const unreadText = document.getElementById(
            'courierNotificationUnreadText'
        );

        const markAllButton = document.getElementById(
            'courierNotificationMarkAllRead'
        );

        if (!bell || !dropdown) {
            return;
        }

        center.dataset.initialized = 'true';

        /*
         * Clean up any previous page instance when
         * navigating with Livewire.
         */
        if (
            typeof window.__sariCourierNotificationsCleanup ===
            'function'
        ) {
            window.__sariCourierNotificationsCleanup();
        }

        const controller = new AbortController();
        const signal = controller.signal;

        function getUnreadItems() {
            return Array.from(
                center.querySelectorAll(
                    '[data-courier-notification-item][data-unread="true"]'
                )
            );
        }

        function updateUnreadCount() {
            const unreadCount = getUnreadItems().length;

            if (badge) {
                badge.textContent = unreadCount > 99
                    ? '99+'
                    : String(unreadCount);

                badge.hidden = unreadCount === 0;
            }

            if (unreadText) {
                unreadText.textContent = unreadCount === 0
                    ? 'No unread notifications'
                    : unreadCount + ' unread ' +
                        (
                            unreadCount === 1
                                ? 'notification'
                                : 'notifications'
                        );
            }

            if (markAllButton) {
                markAllButton.disabled = unreadCount === 0;
            }
        }

        function openDropdown() {
            dropdown.hidden = false;

            dropdown.classList.remove('is-open');

            void dropdown.offsetWidth;

            dropdown.classList.add('is-open');

            bell.setAttribute(
                'aria-expanded',
                'true'
            );
        }

        function closeDropdown() {
            dropdown.hidden = true;

            dropdown.classList.remove('is-open');

            bell.setAttribute(
                'aria-expanded',
                'false'
            );
        }

        function toggleDropdown() {
            if (dropdown.hidden) {
                openDropdown();
            } else {
                closeDropdown();
            }
        }

        function markAllRead() {
            const unreadItems = getUnreadItems();

            if (unreadItems.length === 0) {
                return;
            }

            const notificationIds = unreadItems.map(
                function (item) {
                    return item.dataset.notificationId;
                }
            );

            unreadItems.forEach(function (item) {
                item.dataset.unread = 'false';
            });

            updateUnreadCount();

            /*
             * UI-only event.
             * Does not persist notification read status
             * to the Laravel database.
             */
            window.dispatchEvent(
                new CustomEvent(
                    'sari:courier-notifications-mark-all-read',
                    {
                        detail: {
                            ids: notificationIds
                        }
                    }
                )
            );
        }

        // BELL CLICK
        bell.addEventListener(
            'click',
            toggleDropdown,
            { signal }
        );

        // MARK ALL READ
        if (markAllButton) {
            markAllButton.addEventListener(
                'click',
                markAllRead,
                { signal }
            );
        }

        // CLOSE WHEN CLICKING OUTSIDE
        document.addEventListener(
            'click',
            function (event) {
                if (
                    !dropdown.hidden &&
                    !center.contains(event.target)
                ) {
                    closeDropdown();
                }
            },
            { signal }
        );

        // CLOSE ON ESCAPE
        document.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key === 'Escape' &&
                    !dropdown.hidden
                ) {
                    closeDropdown();
                    bell.focus();
                }
            },
            { signal }
        );

        // OPTIONAL FRONTEND REFRESH EVENT
        window.addEventListener(
            'sari:courier-notifications-refresh',
            updateUnreadCount,
            { signal }
        );

        window.__sariCourierNotificationsCleanup = function () {
            controller.abort();
        };

        updateUnreadCount();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeCourierHeaderNotifications,
            { once: true }
        );
    } else {
        initializeCourierHeaderNotifications();
    }

    document.addEventListener(
        'livewire:navigated',
        initializeCourierHeaderNotifications
    );
})();
</script>
