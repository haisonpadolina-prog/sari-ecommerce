<div class="relative" id="adminLiveNotifications"
    data-feed-url="{{ route('admin.notifications.feed') }}"
    data-read-all-url="{{ route('admin.notifications.read-all') }}"
    data-read-url="{{ url('/admin/notifications') }}"
    data-admin-id="{{ (int) session('admin_account_id') }}"
    data-csrf="{{ csrf_token() }}">
    <button id="adminLiveNotificationButton" type="button" aria-label="Notifications" aria-expanded="false"
        aria-controls="adminLiveNotificationPanel" class="admin-live-notification-button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
        <span data-admin-live-count hidden></span>
    </button>
    <section id="adminLiveNotificationPanel" hidden aria-label="Admin notifications">
        <div class="admin-live-notification-heading">
            <div><h2>Notifications</h2><p id="adminLiveNotificationSummary" aria-live="polite">Connecting…</p></div>
            <button type="button" id="adminNotificationSound" aria-label="Mute notification sounds" aria-pressed="true" title="Sound on" style="display:grid;place-items:center;width:34px;height:34px;padding:0;flex-shrink:0;">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m11 5-6 4H2v6h3l6 4V5Z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/></svg>
            </button>
        </div>
        <div class="admin-live-notification-tools"><span id="adminNotificationConnection">Connecting</span><button id="adminLiveReadAll" type="button" disabled>Mark all as read</button></div>
        <div id="adminLiveNotificationList"><p class="admin-live-notification-empty">Loading notifications…</p></div>
    </section>
</div>

<style id="adminNotificationSoundIconStyles">
#adminHeader #adminNotificationSound {
    display:grid!important; place-items:center!important;
    width:34px!important; min-width:34px!important; height:34px!important;
    padding:0!important; overflow:hidden!important;
    font-size:0!important; line-height:0!important; color:transparent!important;
    border:1px solid #E4E7EC!important; border-radius:9px!important; background:#fff!important;
}
#adminHeader #adminNotificationSound > * { display:none!important; }
#adminHeader #adminNotificationSound::before {
    content:''; display:block; width:17px; height:17px;
    background-image:url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23475467%22%20stroke-width%3D%221.8%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m11%205-6%204H2v6h3l6%204V5Z%22%2F%3E%3Cpath%20d%3D%22M15.5%208.5a5%205%200%200%201%200%207M19%205a10%2010%200%200%201%200%2014%22%2F%3E%3C%2Fsvg%3E");
    background-position:center; background-size:contain; background-repeat:no-repeat;
}
#adminHeader #adminNotificationSound[aria-pressed="false"]::before {
    background-image:url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23475467%22%20stroke-width%3D%221.8%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m11%205-6%204H2v6h3l6%204V5Z%22%2F%3E%3Cpath%20d%3D%22m16%209%205%206m0-6-5%206%22%2F%3E%3C%2Fsvg%3E");
}
#adminHeader #adminNotificationSound:hover { background-color:#F2F4F7!important; }
#adminHeader #adminNotificationSound:focus-visible { outline:2px solid #64748B; outline-offset:3px; }
</style>
