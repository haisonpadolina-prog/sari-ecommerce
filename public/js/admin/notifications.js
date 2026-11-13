/* Admin notification feed: private Echo invalidation with authenticated polling fallback. */
(function () {
    'use strict';
    if (window.__SARI_ADMIN_NOTIFICATIONS_V1__) return;
    window.__SARI_ADMIN_NOTIFICATIONS_V1__ = true;
    const config = () => document.getElementById('adminLiveNotifications');
    const panel = () => document.getElementById('adminLiveNotificationPanel');
    const button = () => document.getElementById('adminLiveNotificationButton');
    let timer, busy = false, pending = false, stopped = false, initialized = false;
    let cursor = 0, ceiling = 0, channelBound = false, channelLive = false;
    const announced = new Set();
    const snapshotIds = new Set();
    let hasSnapshot = false;
    let retry = 0;
    const toast = (message, tone = 'info') => window.SariAdminToast?.show(message, tone);
    const key = () => 'sari:admin:' + config()?.dataset.adminId + ':notification-cursor';
    function connection(label) {
        const node = document.getElementById('adminNotificationConnection');
        if (node) node.textContent = label;
    }
    function close() {
        if (panel()) panel().hidden = true;
        button()?.setAttribute('aria-expanded', 'false');
        document.getElementById('adminSidebarNotifications')?.setAttribute('aria-expanded', 'false');
    }
    function open() {
        if (!panel()) return;
        if (!panel().hidden) { close(); return; }
        ['adminProfilePanel', 'adminSearchResults', 'adminMobileSearchPanel'].forEach(id => document.getElementById(id)?.classList.add('hidden'));
        document.getElementById('adminProfileButton')?.setAttribute('aria-expanded', 'false');
        // The sidebar overlay must not cover the notification panel on phones.
        document.getElementById('adminSidebarClose')?.click();
        panel().hidden = false;
        button()?.setAttribute('aria-expanded', 'true');
        document.getElementById('adminSidebarNotifications')?.setAttribute('aria-expanded', 'true');
        refresh();
    }
    function countBadge(node, count) {
        node.textContent = count > 99 ? '99+' : String(count);
        node.hidden = count < 1;
        node.setAttribute('aria-label', `${count} unread notifications`);
    }
    function paint(data) {
        const count = Math.max(0, Number(data.unread_count) || 0);
        document.querySelectorAll('[data-admin-live-count]').forEach(node => countBadge(node, count));
        button()?.setAttribute('aria-label', `Notifications, ${count} unread`);
        const summary = document.getElementById('adminLiveNotificationSummary');
        if (summary) summary.textContent = count ? `${count} unread notification${count === 1 ? '' : 's'}` : 'You’re all caught up';
        const readAll = document.getElementById('adminLiveReadAll');
        if (readAll) readAll.disabled = !count;
        const messageBadge = document.getElementById('adminSidebarMessageBadge');
        if (messageBadge) {
            const messages = Math.max(0, Number(data.unread_messages_count) || 0);
            messageBadge.textContent = messages > 99 ? '99+' : String(messages);
            messageBadge.classList.toggle('hidden', !messages);
            messageBadge.classList.toggle('grid', !!messages);
        }
        const list = document.getElementById('adminLiveNotificationList');
        if (!list) return;
        list.replaceChildren();
        const rows = Array.isArray(data.notifications) ? data.notifications : [];
        if (!rows.length) {
            const empty = document.createElement('p');
            empty.className = 'admin-live-notification-empty';
            empty.textContent = 'No notifications yet.';
            list.appendChild(empty);
        }
        rows.forEach(item => {
            const row = document.createElement('a');
            row.className = 'admin-live-notification-row';
            row.dataset.notificationId = String(item.id);
            row.dataset.unread = String(!item.read);
            try {
                const url = new URL(item.url || '#', location.href);
                row.href = url.origin === location.origin ? url.href : '#';
            } catch (_) { row.href = '#'; }
            const title = document.createElement('strong'); title.textContent = item.title;
            const message = document.createElement('p'); message.textContent = item.message;
            const date = document.createElement('time'); date.dateTime = item.created_at || '';
            const parsed = new Date(item.created_at);
            date.textContent = Number.isNaN(parsed.getTime()) ? '' : new Intl.DateTimeFormat('en-PH', { dateStyle:'medium', timeStyle:'short', timeZone:'Asia/Manila' }).format(parsed);
            row.append(title, message, date); list.appendChild(row);
        });
    }
    function announce(items) {
        let unseen = [];
        // Best-effort cross-tab sound deduplication; IDs also prevent repeat polling alerts.
        let stored = [];
        const storageKey = key() + ':announced';
        try { stored = JSON.parse(localStorage.getItem(storageKey) || '[]'); if (!Array.isArray(stored)) stored = []; } catch (_) {}
        items.forEach(item => {
            const id = Number(item.id);
            if (!id || announced.has(id) || stored.includes(id)) return;
            announced.add(id); stored.push(id); unseen.push(item);
        });
        try { localStorage.setItem(storageKey, JSON.stringify(stored.slice(-200))); } catch (_) {}
        if (!unseen.length) return;
        if (unseen.length === 1) toast(unseen[0].title + ': ' + unseen[0].message);
        else toast(`You have ${unseen.length} new notifications.`);
    }
    function schedule(delay) {
        clearTimeout(timer);
        if (!stopped) timer = setTimeout(refresh, delay ?? (document.hidden ? 30000 : channelLive ? 30000 : 5000));
    }
    async function refresh() {
        if (!config() || stopped) return;
        if (busy) { pending = true; return; }
        busy = true; clearTimeout(timer);
        const abort = new AbortController();
        const timeout = setTimeout(() => abort.abort(), 12000);
        try {
            const url = new URL(config().dataset.feedUrl, location.href);
            if (initialized) url.searchParams.set('after', String(cursor));
            const response = await fetch(url, { credentials:'same-origin', headers:{Accept:'application/json'}, cache:'no-store', signal:abort.signal });
            if ([401,403,419].includes(response.status)) { stopped = true; close(); connection('Session expired'); return; }
            if (!response.ok || !response.headers.get('content-type')?.includes('json')) throw new Error('Feed unavailable');
            const data = await response.json();
            paint(data);
            const visible = Array.isArray(data.notifications) ? data.notifications : [];
            if (initialized) {
                const incoming = Array.isArray(data.new_notifications) ? data.new_notifications : [];
                // Transactions may commit out of ID order. Also detect newly visible unread rows.
                const late = hasSnapshot ? visible.filter(item => !item.read && Number(item.id) <= cursor && !snapshotIds.has(Number(item.id))) : [];
                announce([...incoming, ...late]);
            }
            visible.forEach(item => snapshotIds.add(Number(item.id)));
            hasSnapshot = true;
            cursor = Number(data.cursor) || 0; ceiling = Number(data.latest_id) || 0; initialized = true;
            try { sessionStorage.setItem(key(), String(cursor)); } catch (_) {}
            retry = 0; connection(channelLive ? 'Live' : 'Auto-refresh');
            if (data.has_more) pending = true;
        } catch (_) {
            retry = Math.min(retry + 1, 4); connection('Reconnecting…');
        } finally {
            clearTimeout(timeout); busy = false;
            if (pending && !stopped) { pending = false; schedule(150); }
            else schedule(retry ? Math.min(60000, 5000 * 2 ** retry) : undefined);
        }
    }
    async function markRead(id = null) {
        const node = config(); if (!node) return false;
        const url = id ? `${node.dataset.readUrl}/${encodeURIComponent(id)}/read` : node.dataset.readAllUrl;
        try {
            const response = await fetch(url, {
                method:'POST', credentials:'same-origin',
                headers:{Accept:'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN':node.dataset.csrf},
                body:JSON.stringify(id ? {} : {through:ceiling}),
            });
            if (!response.ok) throw new Error('Unable to update notifications.');
            refresh(); return true;
        } catch (_) { toast('Unable to mark notifications as read. Please try again.', 'error'); return false; }
    }
    function bindChannel() {
        if (!config() || channelBound) return;
        if (!window.Echo?.private) { setTimeout(bindChannel, 1000); return; }
        channelBound = true;
        const channel = window.Echo.private('sari.admin.notifications');
        channel.listen('.admin.notification.created', () => refresh());
        channel.subscribed?.(() => { channelLive = true; connection('Live'); refresh(); });
        channel.error?.(() => { channelLive = false; connection('Auto-refresh'); schedule(5000); });
    }
    document.addEventListener('click', async event => {
        if (event.target.closest('#adminLiveNotificationButton, #adminSidebarNotifications')) { open(); return; }
        if (event.target.closest('#adminNotificationSound')) {
            const sound = window.SariAdminToast;
            if (sound) { sound.setSoundEnabled(!sound.isSoundEnabled()); syncSound(); if (sound.isSoundEnabled()) sound.previewSound(); }
            return;
        }
        const all = event.target.closest('#adminLiveReadAll');
        if (all) { all.disabled = true; await markRead(); return; }
        const row = event.target.closest('[data-notification-id]');
        if (row) {
            // Ordinary clicks persist read state before navigating; modified clicks retain browser behavior.
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
            event.preventDefault();
            if (row.dataset.reading === '1') return;
            row.dataset.reading = '1';
            const ok = await markRead(row.dataset.notificationId);
            row.dataset.reading = '0';
            if (ok && row.getAttribute('href') !== '#') location.assign(row.href);
            return;
        }
        if (!event.target.closest('#adminLiveNotifications')) close();
    }, true);
    document.addEventListener('keydown', event => { if (event.key === 'Escape') { const opened = panel() && !panel().hidden; close(); if (opened) button()?.focus(); } });
    function syncSound() {
        const node = document.getElementById('adminNotificationSound'), enabled = window.SariAdminToast?.isSoundEnabled() !== false;
        if (node) { node.innerHTML = '<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m11 5-6 4H2v6h3l6 4V5Z"/>' + (enabled ? '<path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/>' : '<path d="m16 9 5 6m0-6-5 6"/>') + '</svg>'; node.title = enabled ? 'Sound on' : 'Sound off'; node.setAttribute('aria-pressed', String(enabled)); node.setAttribute('aria-label', enabled ? 'Mute notification sounds' : 'Enable notification sounds'); }
    }
    function boot() {
        if (!config()) return;
        if (!initialized) {
            try { const saved = sessionStorage.getItem(key()); if (saved !== null && /^\d+$/.test(saved)) { cursor = Number(saved); initialized = true; } } catch (_) {}
        }
        syncSound(); bindChannel(); refresh();
    }
    window.addEventListener('sari:realtime-state', event => { if (event.detail?.state !== 'live') channelLive = false; refresh(); });
    window.addEventListener('storage', event => { if (event.key?.includes(':announced')) refresh(); syncSound(); });
    window.addEventListener('online', () => { if (!stopped) refresh(); });
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
    document.addEventListener('livewire:navigating', close);
    document.addEventListener('livewire:navigated', boot);
    window.addEventListener('pageshow', () => { if (!stopped) boot(); });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, {once:true}); else boot();
})();
