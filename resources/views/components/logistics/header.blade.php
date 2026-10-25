@php
    /*
    |--------------------------------------------------------------------------
    | Authenticated Logistics Header Identity
    |--------------------------------------------------------------------------
    |
    | The parent provider identifies the Logistics network.
    | A Hub Operator retains their own name, email and private mailbox.
    |
    */

    $headerRequest = request();

    $headerProvider = \App\Support\CurrentLogisticsAccount::resolve(
        $headerRequest
    );

    $headerIsHub = \App\Support\CurrentLogisticsHub::isHubOperator(
        $headerRequest
    );

    $headerHubAccount = $headerIsHub
        ? \App\Support\CurrentLogisticsHub::requireAccount($headerRequest)
        : null;

    $headerHub = $headerHubAccount?->facility;

    $headerOperatingFacility = \App\Support\CurrentLogisticsHub::operatingFacility(
        $headerRequest
    );

    $headerName = $headerIsHub
        ? ($headerHubAccount?->name ?: 'Hub Operator')
        : ($headerProvider->displayName() ?: 'SARI Logistics');

    $headerSubtitle = $headerIsHub
        ? ($headerHub?->name ?: 'Assigned Hub')
        : ($headerOperatingFacility?->name ?: 'Logistics Manager');

    $headerProfileRoute = $headerIsHub
        ? 'logistics.dashboard'
        : 'logistics.account-management';

    $headerInitials = mb_strtoupper(
        mb_substr(trim($headerName), 0, 2)
    ) ?: 'SL';

    /*
    |--------------------------------------------------------------------------
    | Exact Private Notification Mailbox
    |--------------------------------------------------------------------------
    |
    | Manager: hubAccountId = null.
    | Hub Operator: hubAccountId = the signed-in operator's own ID.
    |
    | Never query all notifications using only logistics_account_id.
    |
    */

    $headerMailbox = \App\Models\Platform\LogisticsNotification::query()
        ->forRecipient(
            (int) $headerProvider->id,
            $headerIsHub ? (int) $headerHubAccount->id : null
        );

    $headerUnreadCount = (clone $headerMailbox)
        ->unread()
        ->count();

    $headerRecent = (clone $headerMailbox)
        ->orderByDesc('id')
        ->limit(7)
        ->get();
@endphp

<header
    id="logisticsHeader"
    class="sticky top-0 z-40 border-b border-[#eee4d3] bg-[#fffdf9]/95 backdrop-blur-md"
>
    <div class="flex min-h-[72px] w-full items-center justify-between gap-[10px] px-[18px]">

        {{-- LEFT: EXISTING HEADER / SIDEBAR CONTROLS --}}

        <div class="flex min-w-0 items-center gap-[10px]">

            <button
                id="logisticsMenuButton"
                type="button"
                class="grid h-[38px] w-[38px] shrink-0 place-items-center rounded-[10px] border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] lg:hidden"
                aria-label="Open logistics menu"
                aria-expanded="false"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>

            <button
                id="logisticsSidebarToggle"
                type="button"
                class="hidden h-[38px] w-[38px] shrink-0 place-items-center rounded-[10px] border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] active:scale-[.97] lg:grid"
                aria-label="Collapse sidebar"
                aria-expanded="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-[17px] font-bold leading-[1.15] tracking-[-0.025em] text-[#17140e]">
                    @yield('page-title', 'Dashboard Overview')
                </h1>

                <div class="mt-[3px] hidden items-center gap-[5px] text-[9px] text-[#978d7d] sm:flex">
                    <a
                        href="{{ route('logistics.dashboard') }}"
                        class="transition hover:text-[#b97805]"
                    >
                        {{ $headerIsHub ? 'Assigned Hub' : 'Logistics' }}
                    </a>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[10px] w-[10px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>

                    <span class="truncate font-medium text-[#5c5448]">
                        @yield('page-title', 'Dashboard')
                    </span>
                </div>
            </div>
        </div>

        {{-- RIGHT: SEARCH, PERSISTENT NOTIFICATIONS, PROFILE --}}

        <div class="flex shrink-0 items-center gap-2">

            @if(! $headerIsHub && $headerOperatingFacility)
                <form method="POST" action="{{ route('logistics.branch.change', [], false) }}" class="hidden lg:block">
                    @csrf
                    <button
                        type="submit"
                        class="group flex h-[38px] max-w-[220px] items-center gap-2 rounded-[11px] border border-[#e6ddcf] bg-white px-3 text-left transition hover:border-[#d9be8c] hover:bg-[#fffaf1]"
                        title="Switch operating branch"
                    >
                        <span class="grid h-6 w-6 shrink-0 place-items-center rounded-[8px] bg-[#fbf4e8] text-[#b97805]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3m2 0h3M8 16h8"/></svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-[7px] font-semibold uppercase tracking-[.1em] text-[#a09587]">Operating Branch</span>
                            <span class="block truncate text-[9px] font-semibold text-[#4a4238] group-hover:text-[#9b690e]">{{ $headerOperatingFacility->name }}</span>
                        </span>
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0 text-[#a79d90]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 7h10m-3-3 3 3-3 3M16 17H6m3-3-3 3 3 3"/></svg>
                    </button>
                </form>
            @endif

            <form method="GET" action="{{ route('logistics.delivery-monitoring') }}" class="relative hidden md:block" role="search">
                <svg
                    viewBox="0 0 24 24"
                    class="pointer-events-none absolute left-[13px] top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-[#9a9184]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    aria-label="Search logistics deliveries"
                    value="{{ is_string(request()->query('q')) ? request()->query('q') : '' }}"
                    placeholder="Search logistics..."
                    class="h-[38px] w-[220px] rounded-[11px] border border-[#e6ddcf] bg-white pl-[38px] pr-3 text-[10px] text-[#302a22] outline-none placeholder:text-[#a89f92] focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10 lg:w-[235px] xl:w-[260px]"
                >
            </form>

            {{-- SELLER-STYLE PERSISTENT NOTIFICATION CENTER --}}

            <div
                id="logisticsNotificationCenter"
                class="relative"
                data-summary-url="{{ route('logistics.notifications.summary') }}"
                data-csrf-token="{{ csrf_token() }}"
            >

                {{--
                    Keep this button ID unchanged.
                    layouts/logistics.blade.php already controls its toggle.
                --}}

                <button
                    id="logisticsNotificationButton"
                    type="button"
                    aria-label="Notifications: {{ number_format($headerUnreadCount) }} unread"
                    aria-controls="logisticsNotificationPanel"
                    aria-expanded="false"
                    class="relative grid h-11 w-11 place-items-center border-0 bg-transparent text-[#475467] shadow-none transition-colors hover:text-[#c9890b] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d9950b]"
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <span
                        id="logisticsNotificationBadge"
                        class="pointer-events-none absolute -right-1 -top-1 {{ $headerUnreadCount > 0 ? 'grid' : 'hidden' }} h-[19px] min-w-[19px] place-items-center rounded-full border-2 border-white bg-[#d9930a] px-1 text-[8px] font-bold text-white"
                    >
                        {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                    </span>
                </button>

                {{--
                    Existing layout JS handles opening, closing, outside
                    clicks and aria-expanded through these exact IDs.
                --}}

                <div
                    id="logisticsNotificationPanel"
                    role="region"
                    aria-label="Logistics notification inbox"
                    class="absolute right-[-54px] top-[54px] z-[80] hidden w-[350px] max-w-[calc(100vw-24px)] overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white shadow-[0_18px_50px_rgba(15,23,42,0.16)] sm:right-0 sm:w-[390px]"
                >

                    {{-- DROPDOWN HEADING --}}

                    <div class="flex items-start justify-between gap-3 border-b border-[#eceff2] px-4 py-3.5">

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold text-[#202124]">
                                Notifications
                            </p>

                            <p
                                id="logisticsNotificationSubtitle"
                                class="mt-0.5 text-[8px] text-[#8a919b]"
                            >
                                {{ number_format($headerUnreadCount) }}
                                {{ $headerUnreadCount === 1 ? 'unread notification' : 'unread notifications' }}
                                ·
                                {{ $headerIsHub ? 'Private Hub mailbox' : 'Main Logistics mailbox' }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-3 pt-0.5">

                            <form
                                id="logisticsNotificationReadAllForm"
                                method="POST"
                                action="{{ route('logistics.notifications.read-all') }}"
                                class="{{ $headerUnreadCount > 0 ? '' : 'hidden' }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="text-[8px] font-semibold text-[#8a6a2d] transition-colors hover:text-[#b97805]"
                                >
                                    Mark all read
                                </button>
                            </form>

                            <a
                                href="{{ route('logistics.notifications') }}"
                                class="text-[8px] font-semibold text-[#a66f13] transition-colors hover:text-[#7e5007]"
                            >
                                View all
                            </a>

                        </div>
                    </div>

                    {{-- RECENT PRIVATE NOTIFICATIONS --}}

                    <div
                        id="logisticsNotificationList"
                        class="max-h-[390px] overflow-y-auto"
                        style="scrollbar-width:thin"
                        aria-live="polite"
                    >

                        @forelse($headerRecent as $notification)

                            <div class="flex items-start gap-3 border-b border-[#eef0f3] px-4 py-3.5 {{ $notification->isRead() ? 'bg-white' : 'bg-[#fffcf5]' }}">

                                <span class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[10px] border border-[#e5e7eb] bg-white text-[#b97805]">

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-[16px] w-[16px]"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        aria-hidden="true"
                                    >
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>

                                </span>

                                <div class="min-w-0 flex-1">

                                    <p class="break-words text-[9.5px] font-semibold text-[#27303d]">
                                        {{ $notification->title }}

                                        @unless($notification->isRead())
                                            <span class="ml-1 rounded-full bg-[#fff1d5] px-1.5 py-0.5 text-[7px] font-bold text-[#a16b0d]">
                                                New
                                            </span>
                                        @endunless
                                    </p>

                                    @if(filled($notification->message))
                                        <p class="mt-1 break-words text-[8.5px] leading-[1.55] text-[#8a919b]">
                                            {{ $notification->message }}
                                        </p>
                                    @endif

                                    <p class="mt-1.5 text-[8px] text-[#9da4ad]">
                                        {{ $notification->created_at?->diffForHumans() ?: 'Recently' }}
                                    </p>

                                    <div class="mt-2 flex items-center gap-3">

                                        <form
                                            method="POST"
                                            action="{{ route('logistics.notifications.open', [
                                                'notification' => $notification->id
                                            ]) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="text-[8px] font-semibold text-[#a66f13] hover:text-[#7e5007]"
                                            >
                                                Open →
                                            </button>
                                        </form>

                                        @unless($notification->isRead())
                                            <form
                                                method="POST"
                                                action="{{ route('logistics.notifications.read', [
                                                    'notification' => $notification->id
                                                ]) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="text-[8px] font-medium text-[#7e8794] hover:text-[#a66f13]"
                                                >
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </div>

                            </div>

                        @empty

                            <div class="px-5 py-9 text-center">

                                <div class="mx-auto grid h-9 w-9 place-items-center rounded-full bg-[#fff7e8] text-[#b47e1e]">

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        aria-hidden="true"
                                    >
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>

                                </div>

                                <p class="mt-3 text-[9px] font-semibold text-[#475467]">
                                    No notifications yet
                                </p>

                                <p class="mx-auto mt-1 max-w-[260px] text-[8px] leading-4 text-[#98a2b3]">
                                    New updates addressed to your account will appear here.
                                </p>

                            </div>

                        @endforelse

                    </div>

                    {{-- FOOTER --}}

                    <a
                        href="{{ route('logistics.notifications') }}"
                        class="flex items-center justify-center border-t border-[#eceff2] bg-[#fafbfc] px-4 py-3 text-[9px] font-semibold text-[#9a6817] transition-colors hover:bg-[#f8fafc]"
                    >
                        View All Notifications →
                    </a>

                </div>
            </div>

            {{-- PROFILE --}}

            <div class="hidden h-9 w-px bg-[#eee4d5] xl:block"></div>

            <a
                href="{{ route($headerProfileRoute) }}"
                class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex"
            >
                <span class="grid h-[35px] w-[35px] shrink-0 place-items-center rounded-full bg-[#d9930a] text-[10px] font-bold text-white">
                    {{ $headerInitials }}
                </span>

                <span class="min-w-0">
                    <span class="block max-w-[155px] truncate text-[10.5px] font-semibold text-[#28231c]">
                        {{ $headerName }}
                    </span>

                    <span class="mt-0.5 block max-w-[155px] truncate text-[8px] text-[#908779]">
                        {{ $headerSubtitle }}
                    </span>
                </span>
            </a>

        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const center = document.getElementById(
        'logisticsNotificationCenter'
    );

    if (!center || center.dataset.notificationReady === '1') {
        return;
    }

    center.dataset.notificationReady = '1';

    const button = document.getElementById(
        'logisticsNotificationButton'
    );

    const badge = document.getElementById(
        'logisticsNotificationBadge'
    );

    const subtitle = document.getElementById(
        'logisticsNotificationSubtitle'
    );

    const list = document.getElementById(
        'logisticsNotificationList'
    );

    const readAllForm = document.getElementById(
        'logisticsNotificationReadAllForm'
    );

    const summaryUrl = center.dataset.summaryUrl;
    const csrfToken = center.dataset.csrfToken;

    const scopeLabel = @json(
        $headerIsHub
            ? 'Private Hub mailbox'
            : 'Main Logistics mailbox'
    );

    let loading = false;

    /*
    |--------------------------------------------------------------------------
    | Safe DOM Helpers
    |--------------------------------------------------------------------------
    |
    | Notification titles and messages are rendered using textContent.
    | Server-provided strings are never inserted as raw HTML.
    |
    */

    function makeElement(tag, className = '', content = null) {
        const element = document.createElement(tag);

        element.className = className;

        if (content !== null) {
            element.textContent = String(content);
        }

        return element;
    }

    function createPostForm(url, label, className) {
        const form = document.createElement('form');

        form.method = 'POST';
        form.action = url;

        const token = document.createElement('input');

        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;

        const submit = makeElement(
            'button',
            className,
            label
        );

        submit.type = 'submit';

        form.append(token, submit);

        return form;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Unread Badge
    |--------------------------------------------------------------------------
    */

    function updateUnreadCount(value) {
        const count = Math.max(
            0,
            Number.parseInt(value, 10) || 0
        );

        if (badge) {
            badge.textContent = count > 99 ? '99+' : String(count);

            badge.classList.toggle('hidden', count === 0);
            badge.classList.toggle('grid', count > 0);
        }

        if (button) {
            button.setAttribute(
                'aria-label',
                `Notifications: ${count} unread`
            );
        }

        if (subtitle) {
            subtitle.textContent =
                `${count} unread ${count === 1 ? 'notification' : 'notifications'} · ${scopeLabel}`;
        }

        if (readAllForm) {
            readAllForm.classList.toggle(
                'hidden',
                count === 0
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Render Recent Notifications
    |--------------------------------------------------------------------------
    */

    function renderNotifications(notifications) {
        if (!list || !Array.isArray(notifications)) {
            return;
        }

        if (notifications.length === 0) {
            const empty = makeElement(
                'div',
                'px-5 py-9 text-center'
            );

            const icon = makeElement(
                'div',
                'mx-auto grid h-9 w-9 place-items-center rounded-full bg-[#fff7e8] text-[#b47e1e]',
                '✓'
            );

            const heading = makeElement(
                'p',
                'mt-3 text-[9px] font-semibold text-[#475467]',
                'No notifications yet'
            );

            const detail = makeElement(
                'p',
                'mx-auto mt-1 max-w-[260px] text-[8px] leading-4 text-[#98a2b3]',
                'New updates addressed to your account will appear here.'
            );

            empty.append(icon, heading, detail);

            list.replaceChildren(empty);

            return;
        }

        const fragment = document.createDocumentFragment();

        notifications.forEach(function (item) {
            const row = makeElement(
                'div',
                'flex items-start gap-3 border-b border-[#eef0f3] px-4 py-3.5 ' +
                (item.is_read ? 'bg-white' : 'bg-[#fffcf5]')
            );

            const icon = makeElement(
                'span',
                'grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[10px] border border-[#e5e7eb] bg-white text-[#b97805]',
                '♧'
            );

            const body = makeElement(
                'div',
                'min-w-0 flex-1'
            );

            const heading = makeElement(
                'p',
                'break-words text-[9.5px] font-semibold text-[#27303d]',
                item.title || 'Logistics update'
            );

            if (!item.is_read) {
                const newLabel = makeElement(
                    'span',
                    'ml-1 rounded-full bg-[#fff1d5] px-1.5 py-0.5 text-[7px] font-bold text-[#a16b0d]',
                    'New'
                );

                heading.append(newLabel);
            }

            body.append(heading);

            if (item.message) {
                body.append(
                    makeElement(
                        'p',
                        'mt-1 break-words text-[8.5px] leading-[1.55] text-[#8a919b]',
                        item.message
                    )
                );
            }

            body.append(
                makeElement(
                    'p',
                    'mt-1.5 text-[8px] text-[#9da4ad]',
                    item.time_ago || 'Recently'
                )
            );

            const actions = makeElement(
                'div',
                'mt-2 flex items-center gap-3'
            );

            if (item.open_url) {
                actions.append(
                    createPostForm(
                        item.open_url,
                        'Open →',
                        'text-[8px] font-semibold text-[#a66f13] hover:text-[#7e5007]'
                    )
                );
            }

            if (!item.is_read && item.read_url) {
                actions.append(
                    createPostForm(
                        item.read_url,
                        'Mark as read',
                        'text-[8px] font-medium text-[#7e8794] hover:text-[#a66f13]'
                    )
                );
            }

            body.append(actions);
            row.append(icon, body);
            fragment.append(row);
        });

        list.replaceChildren(fragment);
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Exact Recipient Summary
    |--------------------------------------------------------------------------
    |
    | The controller determines the mailbox from the authenticated session.
    | No provider ID, Hub ID or recipient key is sent by the browser.
    |
    */

    async function refreshSummary() {
        if (
            loading
            || !summaryUrl
            || document.hidden
        ) {
            return;
        }

        loading = true;

        try {
            const response = await fetch(summaryUrl, {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const contentType = response.headers.get(
                'content-type'
            ) || '';

            if (!contentType.includes('application/json')) {
                return;
            }

            const payload = await response.json();

            if (
                payload.ok !== true
                || !Array.isArray(payload.notifications)
            ) {
                return;
            }

            updateUnreadCount(payload.unread_count);

            renderNotifications(payload.notifications);

        } catch (error) {
            /*
             * Keep the server-rendered list available if the request
             * fails. Never replace it with a fabricated empty state.
             */
        } finally {
            loading = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Integrate With Existing Layout Toggle
    |--------------------------------------------------------------------------
    |
    | layouts/logistics.blade.php already handles the actual dropdown
    | toggle. This listener only refreshes its data when opened.
    |
    */

    button?.addEventListener('click', function () {
        window.requestAnimationFrame(function () {
            if (
                button.getAttribute('aria-expanded') === 'true'
            ) {
                refreshSummary();
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Periodic Updates
    |--------------------------------------------------------------------------
    |
    | Check once per minute while the page is visible.
    | POST forms remain responsible for read-state changes.
    |
    */

    window.setInterval(function () {
        if (!document.hidden) {
            refreshSummary();
        }
    }, 60000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshSummary();
        }
    });
});
</script>
@endpush