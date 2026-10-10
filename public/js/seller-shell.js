(function () {
            const shellConfig = window.__SARI_SELLER_SHELL_CONFIG__ || {};
            const sellerChannel = shellConfig.sellerChannel ?? null;
            const sellerId = Number(shellConfig.sellerId || 0);
            const dashboardUrl = String(shellConfig.dashboardUrl || '');
            const ordersUrl = String(shellConfig.ordersUrl || '');
            const shippingUrl = String(shellConfig.shippingUrl || '');
            const messagesUrl = String(shellConfig.messagesUrl || '');
            const loginUrl = String(shellConfig.loginUrl || '');
            const accountStateUrl = String(shellConfig.accountStateUrl || '');
            const layoutStateUrl = String(shellConfig.layoutStateUrl || '');
            const notificationIndexUrl = String(shellConfig.notificationIndexUrl || '');
            const notificationReadAllUrl = String(shellConfig.notificationReadAllUrl || '');
            const buyerMessagesUrl = String(shellConfig.buyerMessagesUrl || '');
            const ordersLiveStateUrl = String(shellConfig.ordersLiveStateUrl || '');
            const csrfToken = String(shellConfig.csrfToken || '');

            const desktopBreakpoint = 1024;
            const sidebarStorageKey = 'sari:seller-sidebar-collapsed';

            const pathOf = (url) => {
                try {
                    return new URL(url, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                } catch (_) {
                    return '/';
                }
            };

            const currentPath = () =>
                (window.location.pathname.replace(/\/+$/, '') || '/');

            const dashboardPath = pathOf(dashboardUrl);
            const ordersPath = pathOf(ordersUrl);
            const shippingPath = pathOf(shippingUrl);
            const messagesPath = pathOf(messagesUrl);
            const buyerMessagesPath = pathOf(buyerMessagesUrl);
            const notificationPath = pathOf(notificationIndexUrl);

            window.__SARI_SELLER_SHELL_STATE__ =
                window.__SARI_SELLER_SHELL_STATE__ || {
                    messageUnreadCount: 0,
                    notificationUnreadCount: 0,
                    processedMessageIds: new Set(),
                    processedNotificationIds: new Set(),
                    messageSubscribed: false,
                    notificationSubscribed: false,
                    complianceSubscribed: false,
                    accountSubscribed: false,
                    orderSubscribed: false,
                    lastOrderRealtimeAt: 0,
                    lastOrderRealtimeRevision: null,
                    toastTimer: null,
                    orderToastTimer: null,
                    statusBusy: false,
                    orderBusy: false,
                    navigationActive: false,
                    layoutStateLoadedAt: 0,
                    layoutStateAbort: null,
                    layoutStateRequestSeq: 0,
                    notificationMutationSeq: 0,
                    accountStateAbort: null,
                    orderPollAbort: null,
                    uiAbort: null,
                };

            const state = window.__SARI_SELLER_SHELL_STATE__;

            state.messageUnreadCount = Math.max(
                0,
                Number(
                    state.messageUnreadCount
                    ?? state.unreadCount
                    ?? 0
                )
            );

            state.notificationUnreadCount = Math.max(
                0,
                Number(state.notificationUnreadCount || 0)
            );

            if (!(state.processedNotificationIds instanceof Set)) {
                state.processedNotificationIds = new Set();
            }

            state.layoutStateRequestSeq =
                Math.max(
                    0,
                    Number(state.layoutStateRequestSeq || 0)
                );

            state.notificationMutationSeq =
                Math.max(
                    0,
                    Number(state.notificationMutationSeq || 0)
                );

            function beginNotificationMutation() {
                state.notificationMutationSeq += 1;
                state.layoutStateRequestSeq += 1;
                state.layoutStateLoadedAt = 0;

                try {
                    state.layoutStateAbort?.abort();
                } catch (_) {}

                state.layoutStateAbort = null;

                return state.notificationMutationSeq;
            }

            function syncSellerShellProfileAvatar() {
                const headerAvatar =
                    document.getElementById('sellerHeaderProfileAvatar');

                const sidebarAvatar =
                    document.getElementById('sellerSidebarProfileAvatar');

                const freshUrl =
                    headerAvatar?.dataset.profilePhotoUrl
                    || sidebarAvatar?.dataset.profilePhotoUrl
                    || '';

                [headerAvatar, sidebarAvatar]
                    .filter(Boolean)
                    .forEach(function (avatar) {
                        const image =
                            avatar.querySelector(
                                '.seller-shell-profile-avatar__image'
                            );

                        const fallback =
                            avatar.querySelector(
                                '.seller-shell-profile-avatar__fallback'
                            );

                        if (!image || !fallback) {
                            return;
                        }

                        avatar.dataset.profilePhotoUrl = freshUrl;

                        const showFallback = function () {
                            image.hidden = true;
                            fallback.hidden = false;
                        };

                        const showImage = function () {
                            image.hidden = false;
                            fallback.hidden = true;
                        };

                        image.onload = showImage;
                        image.onerror = showFallback;

                        if (!freshUrl) {
                            image.removeAttribute('src');
                            showFallback();
                            return;
                        }

                        if (image.getAttribute('src') !== freshUrl) {
                            image.setAttribute('src', freshUrl);
                        }

                        if (image.complete) {
                            if (image.naturalWidth > 0) {
                                showImage();
                            } else {
                                showFallback();
                            }
                        }
                    });
            }

            function savedCollapsed() {
                try {
                    return window.localStorage.getItem(sidebarStorageKey) === '1';
                } catch (_) {
                    return false;
                }
            }

            function saveCollapsed(value) {
                try {
                    window.localStorage.setItem(
                        sidebarStorageKey,
                        value ? '1' : '0'
                    );
                } catch (_) {}
            }

            function closeMobileSidebar() {
                document
                    .getElementById('sellerSidebar')
                    ?.classList.add('-translate-x-full');

                document
                    .getElementById('sellerSidebarOverlay')
                    ?.classList.add('hidden');

                if (!document.getElementById('sellerCriticalRestriction')) {
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function syncCollapsedState() {
                const root = document.documentElement;
                const toggle = document.getElementById('sellerSidebarToggle');

                if (window.innerWidth >= desktopBreakpoint) {
                    root.classList.toggle(
                        'seller-sidebar-collapsed',
                        savedCollapsed()
                    );
                } else {
                    root.classList.remove('seller-sidebar-collapsed');
                }

                const collapsed =
                    root.classList.contains('seller-sidebar-collapsed');

                toggle?.setAttribute(
                    'aria-expanded',
                    collapsed ? 'false' : 'true'
                );

                toggle?.setAttribute(
                    'aria-label',
                    collapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );
            }

            function syncSidebarActiveState(pathOverride = null) {
                const activeClasses = [
                    'bg-[#d9930a]',
                    'text-white',
                    'shadow-[0_5px_12px_rgba(217,147,10,0.14)]'
                ];

                const inactiveClasses = [
                    'text-[#514b42]',
                    'hover:bg-[#f9f1e3]',
                    'hover:text-[#a96e05]'
                ];

                const path = pathOverride || currentPath();

                document
                    .querySelectorAll(
                        '#sellerSidebarNav > a[data-seller-sidebar-item]'
                    )
                    .forEach(function (link) {
                        const linkPath = pathOf(link.href);
                        const active =
                            linkPath === path ||
                            (
                                linkPath === shippingPath &&
                                (
                                    path === shippingPath ||
                                    path.startsWith(shippingPath + '/')
                                )
                            );

                        activeClasses.forEach(function (className) {
                            link.classList.toggle(className, active);
                        });

                        inactiveClasses.forEach(function (className) {
                            link.classList.toggle(className, !active);
                        });

                        if (linkPath === messagesPath) {
                            const badge =
                                link.querySelector(
                                    '#sellerSidebarMessageBadge'
                                );

                            if (badge) {
                                badge.classList.toggle(
                                    'bg-white',
                                    active
                                );
                                badge.classList.toggle(
                                    'text-[#d9930a]',
                                    active
                                );
                                badge.classList.toggle(
                                    'bg-[#d9930a]',
                                    !active
                                );
                                badge.classList.toggle(
                                    'text-white',
                                    !active
                                );
                            }
                        }
                    });
            }

            function bindCurrentShellUi() {
                state.uiAbort?.abort();

                const controller = new AbortController();
                state.uiAbort = controller;
                const signal = controller.signal;

                const root = document.documentElement;
                const sidebar = document.getElementById('sellerSidebar');
                const overlay =
                    document.getElementById('sellerSidebarOverlay');
                const mobileMenu =
                    document.getElementById('sellerMenuButton');
                const mobileClose =
                    document.getElementById('sellerSidebarClose');
                const desktopToggle =
                    document.getElementById('sellerSidebarToggle');
                const bell =
                    document.getElementById('sellerNotificationBell');
                const dropdown =
                    document.getElementById('sellerNotificationDropdown');
                const markAllNotifications =
                    document.getElementById('sellerNotificationMarkAllRead');

                syncCollapsedState();
                syncSidebarActiveState();
                syncSellerShellProfileAvatar();

                mobileMenu?.addEventListener(
                    'click',
                    function () {
                        sidebar?.classList.remove('-translate-x-full');
                        overlay?.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    },
                    { signal }
                );

                mobileClose?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                overlay?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                desktopToggle?.addEventListener(
                    'click',
                    function () {
                        if (window.innerWidth < desktopBreakpoint) {
                            return;
                        }

                        const next =
                            !root.classList.contains(
                                'seller-sidebar-collapsed'
                            );

                        root.classList.toggle(
                            'seller-sidebar-collapsed',
                            next
                        );

                        saveCollapsed(next);
                        syncCollapsedState();
                    },
                    { signal }
                );

                let shellResizeFrame = 0;

                const syncSellerShellAfterResize = function () {
                    shellResizeFrame = 0;

                    if (window.innerWidth >= desktopBreakpoint) {
                        overlay?.classList.add('hidden');

                        if (
                            !document.getElementById(
                                'sellerCriticalRestriction'
                            )
                        ) {
                            document.body.classList.remove(
                                'overflow-hidden'
                            );
                        }
                    }

                    syncCollapsedState();
                };

                window.addEventListener(
                    'resize',
                    function () {
                        if (shellResizeFrame) return;
                        shellResizeFrame = window.requestAnimationFrame(
                            syncSellerShellAfterResize
                        );
                    },
                    { passive: true, signal }
                );

                signal.addEventListener(
                    'abort',
                    function () {
                        if (shellResizeFrame) {
                            window.cancelAnimationFrame(shellResizeFrame);
                            shellResizeFrame = 0;
                        }
                    },
                    { once: true }
                );

                document
                    .querySelectorAll('#sellerSidebar a[href]')
                    .forEach(function (link) {
                        link.addEventListener(
                            'click',
                            function () {
                                if (
                                    window.innerWidth <
                                    desktopBreakpoint
                                ) {
                                    closeMobileSidebar();
                                }
                            },
                            { signal }
                        );
                    });

                function closeNotificationDropdown() {
                    dropdown?.classList.add('hidden');
                    bell?.setAttribute('aria-expanded', 'false');
                }

                bell?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();

                        if (!dropdown) {
                            return;
                        }

                        const open =
                            dropdown.classList.contains('hidden');

                        dropdown.classList.toggle('hidden', !open);

                        bell.setAttribute(
                            'aria-expanded',
                            open ? 'true' : 'false'
                        );
                    },
                    { signal }
                );

                dropdown?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();
                    },
                    { signal }
                );

                markAllNotifications?.addEventListener(
                    'click',
                    function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        markAllNotificationsRead();
                    },
                    { signal }
                );

                document.addEventListener(
                    'click',
                    closeNotificationDropdown,
                    { signal }
                );

                document.addEventListener(
                    'keydown',
                    function (event) {
                        if (event.key === 'Escape') {
                            closeNotificationDropdown();
                            closeMobileSidebar();
                        }
                    },
                    { signal }
                );
            }

            function displayCount(count) {
                return count > 99 ? '99+' : String(count);
            }

            function updateBadge(badge, count) {
                if (!badge) {
                    return;
                }

                const safeCount =
                    Math.max(0, Number(count || 0));

                badge.dataset.hasUnread =
                    safeCount > 0 ? 'true' : 'false';

                if (safeCount < 1) {
                    /*
                     * Force-hide at DOM + inline-style level.
                     * This avoids any Tailwind/CSS cascade conflict between
                     * `hidden`, `grid`, and older badge rules.
                     */
                    badge.textContent = '';
                    badge.hidden = true;
                    badge.setAttribute('aria-hidden', 'true');

                    badge.classList.add('hidden');
                    badge.classList.remove('grid', 'flex', 'inline-grid');

                    badge.style.setProperty(
                        'display',
                        'none',
                        'important'
                    );

                    /*
                     * Cancel any Web Animations that may still be attached
                     * to the badge from an earlier unread state.
                     */
                    if (
                        typeof badge.getAnimations === 'function'
                    ) {
                        badge
                            .getAnimations()
                            .forEach(function (animation) {
                                animation.cancel();
                            });
                    }

                    return;
                }

                badge.hidden = false;
                badge.removeAttribute('aria-hidden');
                badge.style.removeProperty('display');

                badge.textContent =
                    displayCount(safeCount);

                badge.classList.remove('hidden');
                badge.classList.add('grid');
            }

            function syncUnreadUi() {
                updateBadge(
                    document.getElementById(
                        'sellerSidebarMessageBadge'
                    ),
                    state.messageUnreadCount
                );

                document
                    .querySelectorAll(
                        '[data-seller-notification-badge]'
                    )
                    .forEach(function (badge) {
                        updateBadge(
                            badge,
                            state.notificationUnreadCount
                        );
                    });

                const unreadText =
                    document.getElementById(
                        'sellerNotificationUnreadText'
                    );

                if (unreadText) {
                    unreadText.textContent =
                        state.notificationUnreadCount > 0
                            ? `${state.notificationUnreadCount} unread notification${state.notificationUnreadCount === 1 ? '' : 's'}`
                            : 'No unread notifications';
                }

                const markAll =
                    document.getElementById(
                        'sellerNotificationMarkAllRead'
                    );

                if (markAll) {
                    markAll.disabled =
                        state.notificationUnreadCount < 1;
                }

                syncSidebarActiveState();
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
                return String(
                    data?.message
                    || data?.body
                    || data?.attachment_name
                    || 'Seller activity updated.'
                );
            }

            function notificationIcon(type) {
                const stroke =
                    'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';

                switch (String(type || '')) {
                    case 'admin_message':
                    case 'buyer_message':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path><path d="M8 10h8"></path><path d="M8 14h5"></path></svg>`;
                    case 'shipment':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M3 7h11v10H3z"></path><path d="M14 10h3l4 4v3h-7z"></path><circle cx="7" cy="18" r="1.6"></circle><circle cx="18" cy="18" r="1.6"></circle></svg>`;
                    case 'review':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path></svg>`;
                    case 'return_request':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M9 7H5v4"></path><path d="M5 11c1.8-4.5 8-6.2 12-2.8 4.1 3.5 2.4 10.2-2.8 11.2-3.1.6-6.1-.8-7.7-3.2"></path></svg>`;
                    case 'compliance':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M12 3 5 6v5c0 4.5 2.8 7.8 7 10 4.2-2.2 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>`;
                    case 'inventory':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M4 5h16v14H4z"></path><path d="M8 9h8"></path><path d="M8 13h8"></path></svg>`;
                    case 'voucher':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M3 12 12 3h7v7l-9 9-7-7Z"></path><circle cx="16" cy="7" r="1"></circle></svg>`;
                    case 'finance':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M4 19h16"></path><path d="M6 16v-5"></path><path d="M12 16V6"></path><path d="M18 16V9"></path></svg>`;
                    case 'new_order':
                    case 'order_status':
                    default:
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>`;
                }
            }

            function isMessageNotification(type) {
                return [
                    'admin_message',
                    'buyer_message',
                ].includes(String(type || ''));
            }

            function isCurrentMessageDestination(data) {
                const type = String(data?.type || '');

                if (type === 'admin_message') {
                    return currentPath() === messagesPath;
                }

                if (type === 'buyer_message') {
                    return currentPath() === buyerMessagesPath;
                }

                return false;
            }

            function renderNotificationEmptyState() {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list) {
                    return;
                }

                list.innerHTML = `
                    <div id="sellerNotificationEmpty" class="px-5 py-9 text-center">
                        <div class="mx-auto text-[#b47e1e]">
                            <svg viewBox="0 0 24 24" class="mx-auto h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>
                        </div>

                        <p class="mt-3 text-[9px] font-semibold text-[#475467]">
                            No notifications yet
                        </p>

                        <p class="mx-auto mt-1 max-w-[250px] text-[8px] leading-4 text-[#98a2b3]">
                            New Buyer/Admin messages, orders, shipping, reviews,
                            returns and account activity will appear here.
                        </p>
                    </div>
                `;
            }

            function removeNotificationItem(item) {
                if (!item) {
                    return;
                }

                item.remove();

                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (
                    list
                    && !list.querySelector(
                        '[data-notification-id]'
                    )
                ) {
                    renderNotificationEmptyState();
                }
            }

            function createNotificationItem(data) {
                if (!data?.id) {
                    return null;
                }

                const item = document.createElement('a');

                item.href =
                    data.action_url || notificationIndexUrl;

                /*
                 * Do not attach wire:navigate here.
                 * We persist the read state first, then navigate.
                 */
                item.dataset.notificationId = String(data.id);
                item.dataset.unread =
                    data.unread !== false ? 'true' : 'false';

                const unread = item.dataset.unread === 'true';

                item.className =
                    'seller-shell-notification-item block px-4 py-3.5 transition-colors';

                item.innerHTML = `
                    <div class="flex gap-3">
                        <div class="seller-shell-notification-icon">
                            ${notificationIcon(data.type)}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="line-clamp-1 text-[9px] font-semibold text-[#344054]">
                                    ${escapeHtml(data.title || 'Seller notification')}
                                </p>

                                <span class="shrink-0 text-[7px] text-[#98a2b3]">
                                    ${escapeHtml(data.time || 'Now')}
                                </span>
                            </div>

                            <p class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#667085]">
                                ${escapeHtml(notificationText(data))}
                            </p>
                        </div>

                        ${unread
                            ? '<span data-notification-unread-dot class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>'
                            : ''}
                    </div>
                `;

                item.addEventListener(
                    'click',
                    async function (event) {
                        if (
                            item.dataset.unread !== 'true'
                        ) {
                            return;
                        }

                        event.preventDefault();

                        const destination =
                            item.href
                            || data.action_url
                            || notificationIndexUrl;

                        const persisted =
                            await markNotificationRead(
                                data,
                                item
                            );

                        /*
                         * Navigate only after the read POST finished.
                         * If persistence failed, we still let the Seller
                         * open the destination, but we do NOT falsely
                         * remove the unread notification locally.
                         */
                        if (destination) {
                            window.location.assign(
                                destination
                            );
                        }

                        return persisted;
                    },
                    { once: true }
                );

                return item;
            }

            function renderRecentNotifications(notifications) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list) {
                    return;
                }

                const unreadNotifications =
                    notifications.filter(function (notification) {
                        return notification?.unread !== false;
                    });

                list.innerHTML = '';

                if (!unreadNotifications.length) {
                    renderNotificationEmptyState();
                    return;
                }

                unreadNotifications
                    .slice(0, 8)
                    .forEach(function (notification) {
                        const item =
                            createNotificationItem(
                                notification
                            );

                        if (item) {
                            list.appendChild(item);
                        }
                    });
            }

            async function markNotificationRead(data, item) {
                if (
                    !data?.read_url
                    || item?.dataset.unread !== 'true'
                ) {
                    return true;
                }

                const mutationSeq =
                    beginNotificationMutation();

                try {
                    const response = await fetch(
                        data.read_url,
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Could not mark notification as read.'
                        );
                    }

                    const payload =
                        await response.json();

                    if (
                        mutationSeq !==
                        state.notificationMutationSeq
                    ) {
                        return false;
                    }

                    item.dataset.unread = 'false';

                    state.notificationUnreadCount =
                        Math.max(
                            0,
                            Number(
                                payload?.unread_count
                                ?? Math.max(
                                    0,
                                    state.notificationUnreadCount - 1
                                )
                            )
                        );

                    removeNotificationItem(item);
                    syncUnreadUi();

                    /*
                     * Read was persisted. The next layout-state response
                     * is authoritative and contains only unread records.
                     */
                    state.layoutStateLoadedAt = 0;

                    return true;
                } catch (_) {
                    /*
                     * Never fake a successful read. Keep the row/count
                     * visible and resync from the database.
                     */
                    if (
                        mutationSeq ===
                        state.notificationMutationSeq
                    ) {
                        state.layoutStateLoadedAt = 0;

                        window.setTimeout(function () {
                            loadLayoutState(true);
                        }, 80);
                    }

                    return false;
                }
            }

            async function markAllNotificationsRead() {
                if (!notificationReadAllUrl) {
                    return;
                }

                if (state.notificationUnreadCount < 1) {
                    syncUnreadUi();
                    return;
                }

                const mutationSeq =
                    beginNotificationMutation();

                const button =
                    document.getElementById(
                        'sellerNotificationMarkAllRead'
                    );

                if (button) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(
                        notificationReadAllUrl,
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Could not mark notifications as read.'
                        );
                    }

                    const payload =
                        await response.json();

                    if (
                        mutationSeq !==
                        state.notificationMutationSeq
                    ) {
                        return;
                    }

                    state.notificationUnreadCount =
                        Math.max(
                            0,
                            Number(
                                payload?.unread_count
                                ?? 0
                            )
                        );

                    /*
                     * The read-all write is now committed.
                     * Header is an unread inbox, so clear the rows.
                     */
                    if (
                        state.notificationUnreadCount < 1
                    ) {
                        renderNotificationEmptyState();
                    }

                    syncUnreadUi();

                    state.layoutStateLoadedAt = 0;

                    /*
                     * Confirm from the dedicated notification state.
                     * Stale requests are blocked by mutation/request seq.
                     */
                    window.setTimeout(function () {
                        if (
                            mutationSeq ===
                            state.notificationMutationSeq
                        ) {
                            loadLayoutState(true);
                        }
                    }, 100);
                } catch (_) {
                    /*
                     * Keep current unread UI if the database write failed.
                     * Then ask the server for authoritative state.
                     */
                    if (
                        mutationSeq ===
                        state.notificationMutationSeq
                    ) {
                        state.layoutStateLoadedAt = 0;

                        window.setTimeout(function () {
                            loadLayoutState(true);
                        }, 80);
                    }
                } finally {
                    if (button) {
                        button.disabled =
                            state.notificationUnreadCount < 1;
                    }
                }
            }

            function prependNotification(data) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (
                    !list
                    || !data?.id
                    || data?.unread === false
                ) {
                    return;
                }

                if (
                    list.querySelector(
                        `[data-notification-id="${data.id}"]`
                    )
                ) {
                    return;
                }

                document
                    .getElementById('sellerNotificationEmpty')
                    ?.remove();

                const item = createNotificationItem(data);

                if (!item) {
                    return;
                }

                list.prepend(item);

                const items =
                    list.querySelectorAll(
                        '[data-notification-id]'
                    );

                if (items.length > 8) {
                    items[items.length - 1].remove();
                }
            }

            function handleUnifiedNotification(data) {
                if (
                    !data
                    || data.id === undefined
                    || data.id === null
                ) {
                    return;
                }

                const id = String(data.id);

                if (state.processedNotificationIds.has(id)) {
                    return;
                }

                state.processedNotificationIds.add(id);

                if (data.unread !== false) {
                    state.notificationUnreadCount += 1;

                    if (
                        isMessageNotification(data.type)
                        && !isCurrentMessageDestination(data)
                    ) {
                        state.messageUnreadCount += 1;
                    }
                }

                prependNotification(data);
                syncUnreadUi();
                ringBell();

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-notification',
                        { detail: data }
                    )
                );
            }

            function ringBell() {
                const bell =
                    document.getElementById(
                        'sellerNotificationBell'
                    );

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

            function showMessageToast(data) {
                if (currentPath() === messagesPath) {
                    return;
                }

                const toast =
                    document.getElementById('sellerMessageToast');
                const text =
                    document.getElementById(
                        'sellerMessageToastText'
                    );

                if (!toast || !text) {
                    return;
                }

                text.textContent = notificationText(data);

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

                window.clearTimeout(state.toastTimer);

                state.toastTimer = window.setTimeout(function () {
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
                state.messageUnreadCount = 0;
                syncUnreadUi();
            }

            function abortSellerBackgroundRequests() {
                [
                    'layoutStateAbort',
                    'accountStateAbort',
                    'orderPollAbort',
                ].forEach(function (key) {
                    try {
                        state[key]?.abort();
                    } catch (_) {}
                    state[key] = null;
                });
            }

            async function loadLayoutState(force = false) {
                if (
                    !layoutStateUrl ||
                    state.navigationActive
                ) {
                    return;
                }

                /*
                 * Realtime Echo is the primary source of message updates.
                 * Avoid hitting /seller/layout-state again on every route swap;
                 * this prevents a background request from competing with the
                 * next Seller navigation on local/single-worker servers.
                 */
                if (
                    !force &&
                    Date.now() - Number(state.layoutStateLoadedAt || 0) < 60000
                ) {
                    return;
                }

                state.layoutStateAbort?.abort();

                const requestSeq =
                    ++state.layoutStateRequestSeq;

                const mutationSeqAtStart =
                    state.notificationMutationSeq;

                const controller = new AbortController();
                state.layoutStateAbort = controller;

                try {
                    const response = await fetch(layoutStateUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store',
                        signal: controller.signal,
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (
                        requestSeq !== state.layoutStateRequestSeq
                        || mutationSeqAtStart !==
                            state.notificationMutationSeq
                    ) {
                        return;
                    }

                    state.layoutStateLoadedAt = Date.now();

                    state.messageUnreadCount =
                        Math.max(
                            0,
                            Number(data?.message_unread_count || 0)
                        );

                    /*
                     * IMPORTANT:
                     * Bell count comes ONLY from SellerNotification state.
                     * Legacy `unread_count` historically represented unread
                     * Admin chat messages and must never drive the bell.
                     */
                    if (
                        Object.prototype.hasOwnProperty.call(
                            data || {},
                            'notification_unread_count'
                        )
                    ) {
                        state.notificationUnreadCount =
                            Math.max(
                                0,
                                Number(
                                    data.notification_unread_count
                                    || 0
                                )
                            );
                    }

                    const messages =
                        Array.isArray(data?.recent_messages)
                            ? data.recent_messages
                            : [];

                    messages.forEach(function (message) {
                        state.processedMessageIds.add(
                            String(message.id)
                        );
                    });

                    const hasNotificationFeed =
                        Array.isArray(
                            data?.recent_notifications
                        );

                    const notifications =
                        hasNotificationFeed
                            ? data.recent_notifications
                            : [];

                    notifications.forEach(function (notification) {
                        state.processedNotificationIds.add(
                            String(notification.id)
                        );
                    });

                    syncUnreadUi();

                    if (hasNotificationFeed) {
                        renderRecentNotifications(
                            notifications
                        );
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI layout notification state unavailable.'
                        );
                    }
                } finally {
                    if (state.layoutStateAbort === controller) {
                        state.layoutStateAbort = null;
                    }
                }
            }

            function handleChatMessage(data) {
                if (
                    !data
                    || data.id === undefined
                    || data.id === null
                ) {
                    return;
                }

                const messageId = String(data.id);

                if (state.processedMessageIds.has(messageId)) {
                    return;
                }

                state.processedMessageIds.add(messageId);

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-chat-message',
                        { detail: data }
                    )
                );

                if (
                    data.sender_role === 'admin'
                    && currentPath() !== messagesPath
                ) {
                    showMessageToast(data);
                }
            }

            function subscribeMessages(attempt = 0) {
                if (!sellerChannel || state.messageSubscribed) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeMessages(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen('.chat.message', handleChatMessage);

                state.messageSubscribed = true;
            }

            function subscribeUnifiedNotifications(attempt = 0) {
                if (
                    !sellerChannel
                    || state.notificationSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeUnifiedNotifications(
                                attempt + 1
                            );
                        }, 250);
                    }

                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.notification.created',
                        handleUnifiedNotification
                    );

                state.notificationSubscribed = true;
            }

            function subscribeCompliance(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.complianceSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeCompliance(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.compliance.alert',
                        function (event) {
                            window.dispatchEvent(
                                new CustomEvent(
                                    'sari:seller-compliance-alert',
                                    { detail: event }
                                )
                            );

                            const warningNumber =
                                Number(
                                    event?.warning_number || 0
                                );

                            const suspended =
                                Boolean(
                                    event?.suspended_until
                                );

                            if (
                                warningNumber >= 3 ||
                                suspended
                            ) {
                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.complianceSubscribed = true;
            }

            function handleRealtimeOrderUpdate(event) {
                if (!event) return;

                state.lastOrderRealtimeAt = Date.now();
                state.lastOrderRealtimeRevision =
                    event.revision || event.updated_at || null;

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-order-update',
                        { detail: event }
                    )
                );

                showOrderToast({
                    title: event.status === 'new'
                        ? 'New order received'
                        : 'Order updated',
                    message: [
                        event.order_number || 'Order',
                        event.status_label || event.status || 'Updated'
                    ].filter(Boolean).join(' · ')
                });
            }

            function subscribeOrders(attempt = 0) {
                if (!sellerChannel || state.orderSubscribed) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeOrders(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.order.updated',
                        handleRealtimeOrderUpdate
                    );

                state.orderSubscribed = true;
            }

            function subscribeAccountStatus(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.accountSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeAccountStatus(
                                attempt + 1
                            );
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.account-status',
                        function (event) {
                            const action =
                                String(event?.action || '');

                            if (
                                action === 'banned' ||
                                action === 'deactivated'
                            ) {
                                window.location.replace(
                                    loginUrl
                                );
                                return;
                            }

                            if (
                                action === 'suspended' ||
                                action === 'suspension_lifted' ||
                                action === 'unbanned' ||
                                action === 'restored'
                            ) {
                                const sidebarStatus =
                                    document.getElementById(
                                        'sellerSidebarStoreStatus'
                                    );

                                if (sidebarStatus) {
                                    sidebarStatus.textContent =
                                        action === 'suspended'
                                            ? 'Suspended Store'
                                            : 'Verified Store';
                                }

                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.accountSubscribed = true;
            }

            async function checkAccountState() {
                if (
                    state.statusBusy ||
                    state.navigationActive ||
                    document.hidden ||
                    document.getElementById(
                        'sellerCriticalRestriction'
                    )
                ) {
                    return;
                }

                state.statusBusy = true;
                state.accountStateAbort?.abort();
                const controller = new AbortController();
                state.accountStateAbort = controller;

                try {
                    const response =
                        await fetch(accountStateUrl, {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                            signal: controller.signal,
                        });

                    if (
                        response.status === 401 ||
                        response.status === 403
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (!response.ok) {
                        return;
                    }

                    const account = await response.json();

                    if (
                        account.account_status === 'banned' ||
                        account.account_status === 'deactivated'
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (account.restricted === true) {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(
                                dashboardUrl
                            );
                        } else {
                            window.location.href =
                                dashboardUrl;
                        }
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI seller status fallback unavailable.'
                        );
                    }
                } finally {
                    if (state.accountStateAbort === controller) {
                        state.accountStateAbort = null;
                    }
                    state.statusBusy = false;
                }
            }

            function showOrderToast(eventData) {
                const toast =
                    document.getElementById(
                        'sellerOrderStatusToast'
                    );
                const title =
                    document.getElementById(
                        'sellerOrderToastTitle'
                    );
                const message =
                    document.getElementById(
                        'sellerOrderToastMessage'
                    );

                if (
                    !toast ||
                    !title ||
                    !message ||
                    !eventData
                ) {
                    return;
                }

                title.textContent =
                    eventData.title || 'Order Update';

                message.textContent =
                    eventData.message ||
                    'Your order status changed.';

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

                window.clearTimeout(
                    state.orderToastTimer
                );

                state.orderToastTimer =
                    window.setTimeout(function () {
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
                    }, 5500);
            }

            async function pollOrders() {
                const path = currentPath();

                if (
                    !sellerId ||
                    state.orderBusy ||
                    state.navigationActive ||
                    document.hidden ||
                    (
                        path !== dashboardPath &&
                        path !== ordersPath
                    )
                ) {
                    return;
                }

                state.orderBusy = true;
                state.orderPollAbort?.abort();
                const controller = new AbortController();
                state.orderPollAbort = controller;

                try {
                    const response =
                        await fetch(
                            ordersLiveStateUrl,
                            {
                                headers: {
                                    'Accept':
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                cache: 'no-store',
                                signal: controller.signal,
                            }
                        );

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const eventData = data.latest_event;

                    if (
                        state.lastOrderRealtimeRevision &&
                        data?.revision &&
                        String(data.revision) ===
                            String(state.lastOrderRealtimeRevision)
                    ) {
                        return;
                    }

                    if (!eventData?.id) {
                        return;
                    }

                    const storageKey =
                        'sari:seller-order-event:' +
                        sellerId;

                    const seen =
                        window.localStorage.getItem(
                            storageKey
                        );

                    if (!seen) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );
                        return;
                    }

                    if (
                        Number(eventData.id) >
                        Number(seen)
                    ) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );

                        window.dispatchEvent(
                            new CustomEvent(
                                'sari:seller-order-update',
                                { detail: eventData }
                            )
                        );

                        showOrderToast(eventData);
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI order polling temporarily unavailable.'
                        );
                    }
                } finally {
                    if (state.orderPollAbort === controller) {
                        state.orderPollAbort = null;
                    }
                    state.orderBusy = false;
                }
            }

            function syncRestrictionUi() {
                const locked =
                    Boolean(
                        document.getElementById(
                            'sellerCriticalRestriction'
                        )
                    );

                if (locked) {
                    document.body.classList.add(
                        'overflow-hidden'
                    );
                }

                if (
                    !window.__SARI_SELLER_LOCK_POPSTATE_BOUND__
                ) {
                    window.__SARI_SELLER_LOCK_POPSTATE_BOUND__ =
                        true;

                    window.addEventListener(
                        'popstate',
                        function () {
                            if (
                                document.getElementById(
                                    'sellerCriticalRestriction'
                                )
                            ) {
                                window.history.pushState(
                                    {
                                        sariSellerLocked:
                                            true
                                    },
                                    '',
                                    window.location.href
                                );
                            }
                        }
                    );
                }

                if (locked) {
                    window.history.replaceState(
                        {
                            ...(window.history.state || {}),
                            sariSellerLocked: true
                        },
                        '',
                        window.location.href
                    );
                }
            }


            /*
             * ============================================================
             * FAST SELLER NAVIGATION — USER INTENT ONLY
             * ============================================================
             * Do not background-prefetch every Seller route.
             *
             * Every sidebar link already uses wire:navigate.hover, so Livewire
             * prefetches the one destination the seller is actually hovering.
             * This avoids keeping Laravel/PHP busy with synthetic requests while
             * a real navigation is waiting.
             */
            state.warmedStyleUrls = state.warmedStyleUrls || new Set();
            state.styleWarmupRequests = state.styleWarmupRequests || new Map();
            state.warmedScriptUrls = state.warmedScriptUrls || new Set();
            state.scriptWarmupRequests = state.scriptWarmupRequests || new Map();

            function warmSellerDestinationStyle(link) {
                const styleUrl = String(
                    link?.dataset?.sariNavigationStyle || ''
                ).trim();

                if (!styleUrl) {
                    return Promise.resolve();
                }

                let absoluteStyleUrl = styleUrl;

                try {
                    absoluteStyleUrl = new URL(
                        styleUrl,
                        window.location.origin
                    ).href;
                } catch (_) {}

                /*
                 * IMPORTANT: Do not insert a <link rel="preload" as="style">
                 * for the destination here.
                 *
                 * Livewire reconciles <head> assets during wire:navigate. A
                 * preload node that uses the exact same href as the incoming
                 * stylesheet can race with that reconciliation and allow the
                 * new body to become visible before the stylesheet is applied
                 * (FOUC). Warm the browser cache without mutating <head>, then
                 * let the real <link rel="stylesheet"> from the destination
                 * page remain the authoritative, blocking head asset.
                 */
                const alreadyApplied = Array.from(
                    document.querySelectorAll('link[rel="stylesheet"][href]')
                ).some(function (node) {
                    return node.href === absoluteStyleUrl;
                });

                if (
                    alreadyApplied ||
                    state.warmedStyleUrls.has(absoluteStyleUrl)
                ) {
                    return Promise.resolve();
                }

                if (state.styleWarmupRequests.has(absoluteStyleUrl)) {
                    return state.styleWarmupRequests.get(absoluteStyleUrl);
                }

                const request = fetch(absoluteStyleUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'force-cache',
                    headers: {
                        Accept: 'text/css,*/*;q=0.1'
                    }
                })
                    .then(function (response) {
                        if (response.ok) {
                            state.warmedStyleUrls.add(absoluteStyleUrl);
                        }
                    })
                    .catch(function () {
                        /*
                         * Navigation must still work if a speculative warm-up
                         * fails. The destination's real stylesheet request is
                         * the source of truth and Livewire will handle it.
                         */
                    })
                    .finally(function () {
                        state.styleWarmupRequests.delete(absoluteStyleUrl);
                    });

                state.styleWarmupRequests.set(absoluteStyleUrl, request);

                return request;
            }

            function warmSellerDestinationScript(link) {
                const scriptUrl = String(
                    link?.dataset?.sariNavigationScript || ''
                ).trim();

                if (!scriptUrl) {
                    return Promise.resolve();
                }

                let absoluteScriptUrl = scriptUrl;

                try {
                    absoluteScriptUrl = new URL(
                        scriptUrl,
                        window.location.origin
                    ).href;
                } catch (_) {}

                const alreadyLoaded = Array.from(
                    document.querySelectorAll('script[src]')
                ).some(function (node) {
                    return node.src === absoluteScriptUrl;
                });

                if (
                    alreadyLoaded ||
                    state.warmedScriptUrls.has(absoluteScriptUrl)
                ) {
                    return Promise.resolve();
                }

                if (state.scriptWarmupRequests.has(absoluteScriptUrl)) {
                    return state.scriptWarmupRequests.get(absoluteScriptUrl);
                }

                const request = fetch(absoluteScriptUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'force-cache',
                    headers: {
                        Accept: 'application/javascript,text/javascript,*/*;q=0.1'
                    }
                })
                    .then(function (response) {
                        if (response.ok) {
                            state.warmedScriptUrls.add(absoluteScriptUrl);
                        }
                    })
                    .catch(function () {
                        /* The real destination script remains the source of truth. */
                    })
                    .finally(function () {
                        state.scriptWarmupRequests.delete(absoluteScriptUrl);
                    });

                state.scriptWarmupRequests.set(absoluteScriptUrl, request);

                return request;
            }

            function warmSellerDestinationAssets(link) {
                void warmSellerDestinationStyle(link);
                void warmSellerDestinationScript(link);
            }

            function prepareSellerNavigation(link) {
                warmSellerDestinationAssets(link);
                abortSellerBackgroundRequests();
            }

            function bindSellerNavigationPriority() {
                document
                    .querySelectorAll(
                        '#sellerSidebarNav a[wire\\:navigate\\.hover], ' +
                        '#sellerSidebarProfile a[wire\\:navigate\\.hover]'
                    )
                    .forEach(function (link) {
                        if (link.dataset.sariPriorityBound === '1') {
                            return;
                        }

                        link.dataset.sariPriorityBound = '1';

                        /*
                         * Warm only static destination assets on user intent.
                         * This creates no Laravel/controller request; Livewire's
                         * own wire:navigate.hover remains responsible for the
                         * document prefetch.
                         */
                        link.addEventListener(
                            'pointerenter',
                            function () {
                                warmSellerDestinationAssets(link);
                            },
                            { passive: true }
                        );

                        link.addEventListener(
                            'focus',
                            function () {
                                warmSellerDestinationAssets(link);
                            },
                            { passive: true }
                        );

                        /*
                         * Stop our own polling/layout requests as soon as the
                         * seller commits to a destination. Static page assets
                         * are also warmed in parallel with the document.
                         */
                        link.addEventListener(
                            'pointerdown',
                            function () {
                                prepareSellerNavigation(link);
                            },
                            { passive: true }
                        );

                        link.addEventListener(
                            'touchstart',
                            function () {
                                prepareSellerNavigation(link);
                            },
                            { passive: true }
                        );

                        link.addEventListener(
                            'keydown',
                            function (event) {
                                if (
                                    event.key === 'Enter' ||
                                    event.key === ' '
                                ) {
                                    prepareSellerNavigation(link);
                                }
                            }
                        );
                    });
            }

            document.addEventListener(
                'livewire:navigate',
                function (event) {
                    state.navigationActive = true;
                    abortSellerBackgroundRequests();

                    /* Optimistic sidebar feedback before the network finishes. */
                    try {
                        const targetPath =
                            event.detail?.url?.pathname?.replace(/\/+$/, '') || '/';

                        if (targetPath) {
                            syncSidebarActiveState(targetPath);

                            const destinationLink = Array.from(
                                document.querySelectorAll(
                                    '[data-sari-navigation-style][href]'
                                )
                            ).find(function (link) {
                                return pathOf(link.href) === targetPath;
                            });

                            warmSellerDestinationAssets(destinationLink);
                        }
                    } catch (_) {}
                }
            );

            document.addEventListener(
                'livewire:navigating',
                function () {
                    abortSellerBackgroundRequests();
                    closeMobileSidebar();

                    document
                        .getElementById(
                            'sellerNotificationDropdown'
                        )
                        ?.classList.add('hidden');
                }
            );

            document.addEventListener(
                'livewire:navigated',
                function () {
                    state.navigationActive = false;

                    bindCurrentShellUi();
                    bindSellerNavigationPriority();
                    syncRestrictionUi();
                    syncUnreadUi();
                    syncSellerShellProfileAvatar();

                    if (
                        [
                            messagesPath,
                            buyerMessagesPath,
                            notificationPath,
                        ].includes(currentPath())
                    ) {
                        window.setTimeout(
                            function () {
                                loadLayoutState(true);
                            },
                            80
                        );
                    } else if ('requestIdleCallback' in window) {
                        window.requestIdleCallback(
                            function () {
                                loadLayoutState();
                            },
                            { timeout: 1800 }
                        );
                    } else {
                        window.setTimeout(
                            loadLayoutState,
                            900
                        );
                    }

                    subscribeMessages();
                    subscribeUnifiedNotifications();
                    subscribeCompliance();
                    subscribeOrders();
                    subscribeAccountStatus();
                }
            );

            if (
                !window.__SARI_SELLER_STATUS_INTERVAL__
            ) {
                window.__SARI_SELLER_STATUS_INTERVAL__ =
                    window.setInterval(
                        checkAccountState,
                        60000
                    );

                window.setTimeout(
                    checkAccountState,
                    12000
                );
            }

            if (
                !window.__SARI_SELLER_ORDER_INTERVAL__
            ) {
                window.__SARI_SELLER_ORDER_INTERVAL__ =
                    window.setInterval(
                        pollOrders,
                        45000
                    );

                window.setTimeout(
                    pollOrders,
                    10000
                );
            }
        })();
