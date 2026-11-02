/* Rider notification center. No fabricated entries or browser-only unread counts.
   Backend owns identity, source events, persistent read state, and URLs. */
(() => {
    'use strict';
    const script = document.querySelector('script[data-courier-notification-script]');
    const summaryUrl = script?.dataset.summaryUrl;
    const readUrlPattern = script?.dataset.readUrl;
    const readAllUrl = script?.dataset.readAllUrl;
    if (!summaryUrl || !readUrlPattern || !readAllUrl) return;

    const bell = document.getElementById('notificationBell');
    const popover = document.getElementById('courierNotificationPopover');
    const wrap = document.getElementById('courierNotificationWrap');
    const preview = document.getElementById('courierNotificationPreview');
    const badgeHeader = document.getElementById('courierHeaderNotificationCount');
    const badgeSidebar = document.getElementById('courierSidebarNotificationCount');
    const badgeMessages = document.getElementById('courierSidebarMessagesCount');
    const markAll = document.getElementById('courierNotificationMarkAll');
    const pageMarkAll = document.getElementById('courierNotificationsPageMarkAll');
    const pageItems = document.getElementById('courierNotificationsPageList');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let items = [];
    let started = false;
    let lastUnreadKeys = new Set();
    let requestInFlight = false;
    let activeMutation = false;

    const setBadge = (node, count) => {
        if (!node) return;
        const value = Math.max(0, Number(count) || 0);
        node.hidden = value < 1;
        node.textContent = value > 99 ? '99+' : String(value);
        node.setAttribute('aria-label', `${value} unread ${node === badgeMessages ? 'messages' : 'notifications'}`);
    };

    const safeUrl = (value) => {
        try {
            const uri = new URL(value, window.location.origin);
            if (uri.origin !== window.location.origin || !/^https?:$/.test(uri.protocol)) return null;
            return uri.href;
        } catch (_) { return null; }
    };

    const post = async url => {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        if (!response.ok) throw new Error(`Notification update failed (${response.status})`);
        return response.json();
    };

    const mkText = (tag, className, value) => {
        const node = document.createElement(tag);
        node.className = className;
        node.textContent = String(value ?? '');
        return node;
    };

    const renderPreview = () => {
        if (!preview) return;
        preview.replaceChildren();
        if (items.length === 0) {
            preview.appendChild(mkText('div', 'courier-popover-empty', 'No notifications yet.'));
            return;
        }
        items.slice(0, 6).forEach(item => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'courier-popover-item' + (item.unread ? ' is-unread' : '');
            button.setAttribute('role', 'listitem');
            button.setAttribute('aria-label', `Open notification: ${item.title}`);
            button.append(
                mkText('span', 'courier-popover-item-title', item.title),
                mkText('span', 'courier-popover-item-text', item.message),
                mkText('span', 'courier-popover-item-time', item.time)
            );
            button.addEventListener('click', () => openNotification(item, button));
            preview.appendChild(button);
        });
    };

    const openNotification = async (item, control) => {
        if (activeMutation) return;
        const target = safeUrl(item.url);
        if (!target) return;
        if (!item.unread) { window.location.assign(target); return; }
        activeMutation = true;
        if (control) control.disabled = true;
        try {
            const url = readUrlPattern.replace('__notification__', String(item.id));
            await post(url);
            window.location.assign(target);
        } catch (error) {
            activeMutation = false;
            if (control) control.disabled = false;
            // Never silently claim read or navigate away on a failed write.
            window.alert('Could not mark this notification as read. Please try again.');
        }
    };

    async function refresh(announce = false) {
        if (requestInFlight || activeMutation || document.hidden) return;
        requestInFlight = true;
        try {
            const response = await fetch(summaryUrl, {
                credentials: 'same-origin',
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                cache: 'no-store',
            });
            if (!response.ok) throw new Error(`Notification fetch failed (${response.status})`);
            const data = await response.json();
            const nextItems = Array.isArray(data.items) ? data.items : [];
            const unreadKeys = new Set(nextItems.filter(item => item.unread).map(item => String(item.id)));
            const newlyUnread = nextItems.find(item => {
                if (!item.unread || lastUnreadKeys.has(String(item.id))) return false;
                // Newly visible old records must never trigger a false fresh alert.
                const stamp = Date.parse(item.occurred_at || '');
                return Number.isFinite(stamp) && stamp <= Date.now() + 60000 && stamp >= Date.now() - 3600000;
            });
            items = nextItems;
            setBadge(badgeHeader, data.unread_count);
            setBadge(badgeSidebar, data.unread_count);
            setBadge(badgeMessages, data.messages_unread_count);
            renderPreview();
            if (markAll) markAll.disabled = !(Number(data.unread_count) > 0);
            if (pageMarkAll) pageMarkAll.disabled = !(Number(data.unread_count) > 0);
            if (started && announce && newlyUnread) {
                // Cross-tab dedupe: a single rider should hear each event once.
                const key = 'sari_rider_seen_notification_v5_' + String(newlyUnread.id);
                let alreadyPlayed = false;
                try {
                    const previous = Number(localStorage.getItem(key) || 0);
                    alreadyPlayed = previous > 0;
                    if (!alreadyPlayed) localStorage.setItem(key, String(Date.now()));
                } catch (_) {}
                if (!alreadyPlayed) {
                    bell?.classList.remove('bell-ring');
                    if (bell) void bell.offsetWidth;
                    bell?.classList.add('bell-ring');
                    window.SariCourierNotify?.incoming(newlyUnread);
                }
            }
            started = true;
            lastUnreadKeys = unreadKeys;
        } catch (error) {
            // Keep last successfully verified badge state; no fake zero on network failure.
            if (preview && !started) {
                preview.replaceChildren(mkText('div', 'courier-popover-empty', 'Notifications are unavailable. Please retry.'));
            }
        } finally {
            requestInFlight = false;
        }
    }

    function setOpen(open) {
        if (!bell || !popover) return;
        popover.hidden = !open;
        bell.setAttribute('aria-expanded', String(open));
        if (open) refresh(false);
    }

    bell?.addEventListener('click', () => setOpen(!!popover?.hidden));
    document.addEventListener('pointerdown', event => {
        if (popover && !popover.hidden && wrap && !wrap.contains(event.target)) setOpen(false);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && popover && !popover.hidden) {
            setOpen(false);
            bell?.focus();
        }
    });

    const markAllRead = async button => {
        if (activeMutation) return;
        activeMutation = true;
        if (button) button.disabled = true;
        try {
            await post(readAllUrl);
            activeMutation = false;
            await refresh(false);
            if (pageItems) window.location.reload();
        } catch (_) {
            activeMutation = false;
            if (button) button.disabled = false;
            window.alert('Could not mark notifications as read. Please try again.');
        }
    };
    markAll?.addEventListener('click', () => markAllRead(markAll));
    pageMarkAll?.addEventListener('click', () => markAllRead(pageMarkAll));

    // "View assignment" inside the urgent modal also uses the secure read endpoint.
    document.querySelector('[data-scn-link]')?.addEventListener('click', event => {
        const id = Number(event.currentTarget.dataset.notificationId);
        const item = items.find(entry => Number(entry.id) === id);
        if (!item) return;
        event.preventDefault();
        openNotification(item, event.currentTarget);
    });

    pageItems?.addEventListener('click', event => {
        const button = event.target.closest('[data-courier-open-notification]');
        if (!button || !pageItems.contains(button)) return;
        const id = Number(button.dataset.courierOpenNotification);
        const item = items.find(entry => Number(entry.id) === id);
        if (item) openNotification(item, button);
    });

    refresh(false);
    const poll = window.setInterval(() => refresh(true), 12000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(true); });
    window.addEventListener('pagehide', () => window.clearInterval(poll), {once:true});
})();
