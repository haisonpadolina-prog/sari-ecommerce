@extends('layouts.courier')
@section('title', 'Notifications — SARI')
@section('header-title', 'Notifications')
@section('header-subtitle', 'Rider updates')

@push('styles')
<style>
@verbatim
#courierNotificationsBuyerUI { --activity-panel:#fff; --activity-border:#e3e7eb; --activity-border-soft:#edf0f3; --activity-text:#20252b; --activity-muted:#747d87; --activity-gold:#c99128; --activity-gold-dark:#ad781b; width:100%; max-width:1480px; margin-inline:auto; padding:22px 24px 44px; color:var(--activity-text); font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; font-size:13px; line-height:1.55; }
/* SARI-RIDER-NOTIFICATIONS-LIGHT-GRAY-V8: neutral workspace, no cream background */
body:has(#courierNotificationsBuyerUI),
body:has(#courierNotificationsBuyerUI) #courierMain,
body:has(#courierNotificationsBuyerUI) #courierMain > main { background-color:#f5f6f8 !important; }
#courierNotificationsBuyerUI, #courierNotificationsBuyerUI * { box-sizing:border-box; }
#courierNotificationsBuyerUI .sari-activity-header {padding:2px 2px 14px;}
#courierNotificationsBuyerUI .sari-activity-header-inner {display:flex;align-items:flex-end;justify-content:space-between;gap:16px;}
#courierNotificationsBuyerUI .sari-activity-eyebrow {margin:0;color:#a87316;font-size:10.5px;font-weight:800;letter-spacing:.14em;line-height:1.2;text-transform:uppercase;}
#courierMain #courierNotificationsBuyerUI .sari-activity-title {margin:5px 0 0;color:#2e343b;font-size:clamp(27px,2.25vw,31px);font-weight:760;line-height:1.04;letter-spacing:-.035em;}
#courierNotificationsBuyerUI .sari-activity-title span {color:var(--activity-gold);}
#courierNotificationsBuyerUI .sari-activity-header-link {position:relative;top:-12px;display:inline-flex;align-items:center;gap:8px;color:#59636e;font-size:11.5px;font-weight:750;text-decoration:none;}
#courierNotificationsBuyerUI .sari-activity-header-link:hover {color:#c88912;}
#courierNotificationsBuyerUI .sari-activity-header-arrow {font-size:18px;font-weight:800;line-height:.8;}
#courierNotificationsBuyerUI .sari-activity-actions {display:flex;align-items:center;gap:24px;margin:9px 2px 15px;}
#courierNotificationsBuyerUI .sari-activity-filters {display:flex;align-items:center;flex-wrap:wrap;gap:24px;}
#courierNotificationsBuyerUI .sari-activity-filter {display:inline-flex;align-items:center;min-height:36px;padding:6px 0;border:0;background:transparent;color:#7f8995;font-size:12px;font-weight:650;text-decoration:none;transition:color .16s;}
#courierNotificationsBuyerUI .sari-activity-filter:hover,#courierNotificationsBuyerUI .sari-activity-filter[aria-current="page"] {color:#c88912;font-weight:750;}
#courierNotificationsBuyerUI .sari-activity-shell {position:relative;margin-top:0;border:1px solid #e5e7eb;border-radius:22px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.055),0 14px 32px rgba(15,23,42,.095),0 30px 68px rgba(15,23,42,.11);}
#courierNotificationsBuyerUI .sari-activity-toolbar {display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:54px;padding:0 20px;border-bottom:1px solid var(--activity-border-soft);background:#fff;border-radius:22px 22px 0 0;}
#courierNotificationsBuyerUI .sari-activity-toolbar h2 {margin:0;color:#303740;font-size:15px;font-weight:800;line-height:1.2;}
#courierNotificationsBuyerUI .sari-activity-toolbar-right {display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:9px 20px;}
#courierNotificationsBuyerUI .sari-activity-count {color:#7d8792;font-size:10.5px;font-weight:650;}
#courierNotificationsBuyerUI .sari-activity-read-all-form {margin:0;}
#courierNotificationsBuyerUI .sari-activity-read-all {display:inline-flex;align-items:center;border:0;background:transparent;padding:9px 0;color:var(--activity-gold-dark);cursor:pointer;font:inherit;font-size:12px;font-weight:750;white-space:nowrap;}
#courierNotificationsBuyerUI .sari-activity-read-all:hover:not(:disabled) {color:#8e5b0a;}
#courierNotificationsBuyerUI .sari-activity-read-all:disabled {opacity:.48;cursor:default;}
#courierNotificationsBuyerUI .sari-activity-items {position:relative;}
#courierNotificationsBuyerUI .sari-activity-row {position:relative;display:grid;grid-template-columns:48px minmax(0,1fr) auto 34px;gap:14px;align-items:center;min-height:104px;padding:16px 18px;border-bottom:1px solid #e7eaee;background:#fff;transition:background-color .14s;}
#courierNotificationsBuyerUI .sari-activity-row:last-child {border-bottom:0;border-radius:0 0 22px 22px;}
#courierNotificationsBuyerUI .sari-activity-row.is-unread {background:#fff;}
#courierNotificationsBuyerUI .sari-activity-row:hover,#courierNotificationsBuyerUI .sari-activity-row:focus-within {background:#f4f5f6;}
#courierNotificationsBuyerUI .sari-activity-row[data-row-url] {cursor:pointer;}
#courierNotificationsBuyerUI .sari-activity-avatar {display:grid;width:48px;height:48px;flex:0 0 48px;place-items:center;overflow:hidden;border:2px solid #e5c78d;border-radius:50%;background:#151515;padding:0;box-shadow:0 2px 8px rgba(37,43,49,.08);}
#courierNotificationsBuyerUI .sari-activity-avatar img {display:block;width:100%;height:100%;max-width:none;object-fit:cover;border-radius:0;transform:scale(1.45);transform-origin:center;}
#courierNotificationsBuyerUI .sari-activity-main {min-width:0;padding-right:8px;}
#courierNotificationsBuyerUI .sari-activity-event-title {margin:0;overflow:hidden;color:#1e2329;font-size:15px;font-weight:760;line-height:1.3;letter-spacing:-.012em;text-overflow:ellipsis;white-space:nowrap;}
#courierNotificationsBuyerUI .sari-activity-event-message {margin:4px 0 0;overflow:hidden;color:#707985;font-size:12px;font-weight:500;line-height:1.45;text-overflow:ellipsis;white-space:nowrap;}
#courierNotificationsBuyerUI .sari-activity-event-meta {display:flex;min-width:0;flex-wrap:wrap;gap:5px;margin-top:4px;color:#7f8892;font-size:11px;font-weight:520;line-height:1.35;}
#courierNotificationsBuyerUI .sari-activity-event-meta span {display:inline-flex;min-width:0;align-items:center;}
#courierNotificationsBuyerUI .sari-activity-event-meta span:not(:last-child):after {content:"·";margin-left:5px;color:#a7afb7;}
#courierNotificationsBuyerUI .sari-activity-view-wrap {display:flex;align-items:center;justify-content:flex-end;}
#courierNotificationsBuyerUI .sari-activity-view-wrap form {margin:0;}
#courierNotificationsBuyerUI .sari-activity-view {display:inline-flex;min-width:70px;height:36px;align-items:center;justify-content:center;border:0;border-radius:13px;background:#f1f2f4;padding:0 16px;color:#24292f;cursor:pointer;font:inherit;font-size:11px;font-weight:760;line-height:1;text-decoration:none;transition:background-color .14s,color .14s,transform .14s;}
#courierNotificationsBuyerUI .sari-activity-view:hover {background:#e7e9ec;color:#171b20;}
#courierNotificationsBuyerUI .sari-activity-menu-wrap {position:relative;display:flex;justify-content:flex-end;}
#courierNotificationsBuyerUI .sari-activity-menu-toggle {display:grid;width:34px;height:36px;place-items:center;border:0;border-radius:10px;background:transparent;color:#69727c;cursor:pointer;}
#courierNotificationsBuyerUI .sari-activity-menu-toggle:hover,#courierNotificationsBuyerUI .sari-activity-menu-toggle[aria-expanded="true"] {background:rgba(31,41,55,.06);color:#24292f;}
#courierNotificationsBuyerUI .sari-activity-menu-toggle svg {width:18px;height:18px;fill:currentColor;}
#courierNotificationsBuyerUI .sari-activity-menu {position:absolute;z-index:55;top:42px;right:0;width:218px;overflow:hidden;border:1px solid #e0e4e8;border-radius:18px;background:#fff;padding:8px;box-shadow:0 22px 48px rgba(31,41,55,.16),0 5px 14px rgba(31,41,55,.06);}
#courierNotificationsBuyerUI .sari-activity-menu[hidden] {display:none;}
#courierNotificationsBuyerUI .sari-activity-menu-item {display:flex;width:100%;min-height:44px;align-items:center;gap:11px;border:0;border-radius:11px;background:transparent;padding:0 12px;color:#2b3138;cursor:pointer;font:inherit;font-size:12px;font-weight:600;text-align:left;text-decoration:none;}
#courierNotificationsBuyerUI .sari-activity-menu-item:hover,#courierNotificationsBuyerUI .sari-activity-menu-item:focus-visible {background:#f4f5f7;color:#171b20;}
#courierNotificationsBuyerUI .sari-activity-menu-item svg {width:19px;height:19px;flex:0 0 auto;fill:none;stroke:currentColor;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round;}
#courierNotificationsBuyerUI .sari-activity-menu form {margin:0;}
#courierNotificationsBuyerUI .sari-activity-menu-item.is-danger {color:#e12535;}
#courierNotificationsBuyerUI .sari-activity-menu-item.is-danger:hover {background:#fff0f1;color:#c91529;}
#courierNotificationsBuyerUI .sari-activity-menu-divider {height:1px;margin:7px 4px;background:#eceff2;}
#courierNotificationsBuyerUI .sari-activity-menu-item:disabled {opacity:.55;cursor:wait;}
#courierNotificationsBuyerUI .sari-activity-time-mobile {display:none;}
#courierNotificationsBuyerUI .sari-activity-feedback {margin:4px 0 16px;padding:11px 15px;border:1px solid #dfe4eb;border-radius:11px;background:#f1f3f6;color:#475467;font-size:12px;font-weight:550;}
#courierNotificationsBuyerUI .sari-activity-footer {display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);align-items:center;gap:16px;width:100%;margin-top:22px;padding:4px 2px 0;}
#courierNotificationsBuyerUI .sari-activity-range {grid-column:1;grid-row:1;justify-self:start;margin:0;color:#9aa3ae;font-size:12px;font-weight:450;line-height:1.5;white-space:nowrap;}
#courierNotificationsBuyerUI .sari-activity-pagination {grid-column:2;grid-row:1;display:flex;align-items:center;justify-content:center;gap:6px;min-width:0;max-width:100%;}
#courierNotificationsBuyerUI .sari-activity-page-link,#courierNotificationsBuyerUI .sari-activity-page-arrow {display:inline-flex;min-width:40px;height:36px;flex:0 0 auto;align-items:center;justify-content:center;border:1px solid transparent;border-radius:12px;background:#f0f2f6;padding:0 10px;color:#929aa6;font-size:12px;font-weight:600;line-height:1;text-decoration:none;}
#courierNotificationsBuyerUI .sari-activity-page-link:hover,#courierNotificationsBuyerUI .sari-activity-page-arrow:hover {background:#e7ebf1;color:#687381;}
#courierNotificationsBuyerUI .sari-activity-page-link[aria-current="page"] {border-color:#c99128;background:#c99128;color:#fff;box-shadow:0 3px 12px rgba(201,145,40,.24);pointer-events:none;}
#courierNotificationsBuyerUI .sari-activity-page-arrow svg {width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
#courierNotificationsBuyerUI .sari-activity-page-arrow.is-disabled {color:#b5bcc6;cursor:default;}
#courierNotificationsBuyerUI .sari-activity-page-ellipsis {display:inline-flex;min-width:16px;height:36px;align-items:center;justify-content:center;color:#a7afba;font-size:13px;user-select:none;}
#courierNotificationsBuyerUI .sari-activity-empty-state {display:grid;min-height:430px;place-items:center;border:1px solid #e5e7eb;border-radius:18px;background:#fff;padding:72px 24px;text-align:center;box-shadow:0 3px 7px rgba(15,23,42,.055),0 14px 32px rgba(15,23,42,.095),0 30px 68px rgba(15,23,42,.11);}
#courierNotificationsBuyerUI .sari-activity-empty-icon {display:grid;width:82px;height:82px;margin:0 auto;place-items:center;color:#b4bbc3;}
#courierNotificationsBuyerUI .sari-activity-empty-icon svg {width:54px;height:54px;fill:none;stroke:currentColor;stroke-width:1.45;stroke-linecap:round;stroke-linejoin:round;}
#courierNotificationsBuyerUI .sari-activity-empty-title {margin:16px 0 0;color:#9099a3;font-size:21px;font-weight:750;line-height:1.2;letter-spacing:-.025em;}
#courierNotificationsBuyerUI .sari-activity-empty-copy {max-width:520px;margin:7px auto 0;color:#a8b0b8;font-size:12px;font-weight:500;line-height:1.55;}
#courierNotificationsBuyerUI .sari-activity-empty-button {display:inline-flex;min-width:170px;height:46px;align-items:center;justify-content:center;margin-top:22px;border:1px solid var(--activity-gold);border-radius:11px;background:var(--activity-gold);padding:0 24px;color:#fff;font-size:11px;font-weight:800;text-decoration:none;}
#courierNotificationsBuyerUI .sari-activity-empty-button:hover {background:var(--activity-gold-dark);color:#fff;}
#courierNotificationsBuyerUI :is(a,button):focus-visible {outline:2px solid #c99128;outline-offset:3px;}
@media(max-width:900px) {#courierNotificationsBuyerUI .sari-activity-footer {grid-template-columns:minmax(0,1fr);justify-items:center;gap:13px;margin-top:18px;}#courierNotificationsBuyerUI .sari-activity-pagination {grid-column:1;grid-row:1;max-width:100%;overflow-x:auto;justify-content:safe center;padding:3px 2px 8px;}#courierNotificationsBuyerUI .sari-activity-range {grid-column:1;grid-row:2;width:100%;white-space:normal;text-align:left;}#courierNotificationsBuyerUI .sari-activity-row {gap:11px;padding-inline:13px;}#courierNotificationsBuyerUI .sari-activity-avatar {width:46px;height:46px;}}
@media(max-width:700px) {#courierNotificationsBuyerUI {padding:16px 14px 30px;}#courierNotificationsBuyerUI .sari-activity-header-inner {align-items:flex-start;flex-direction:column;gap:8px;}#courierMain #courierNotificationsBuyerUI .sari-activity-title {font-size:25px;}#courierNotificationsBuyerUI .sari-activity-header-link {top:0;font-size:10.5px;}#courierNotificationsBuyerUI .sari-activity-shell {border-radius:16px;}#courierNotificationsBuyerUI .sari-activity-toolbar {min-height:50px;padding:11px 14px;border-radius:16px 16px 0 0;flex-wrap:wrap;}#courierNotificationsBuyerUI .sari-activity-toolbar-right {gap:5px 14px;}#courierNotificationsBuyerUI .sari-activity-row {grid-template-columns:44px minmax(0,1fr) 30px;gap:10px;min-height:96px;padding:13px 12px;}#courierNotificationsBuyerUI .sari-activity-avatar {width:42px;height:42px;flex-basis:42px;}#courierNotificationsBuyerUI .sari-activity-main {padding-right:0;}#courierNotificationsBuyerUI .sari-activity-event-title {font-size:13px;}#courierNotificationsBuyerUI .sari-activity-event-message {font-size:10.5px;}#courierNotificationsBuyerUI .sari-activity-event-meta {font-size:9.5px;}#courierNotificationsBuyerUI .sari-activity-view-wrap {display:none;}#courierNotificationsBuyerUI .sari-activity-menu-wrap {grid-column:3;grid-row:1;}#courierNotificationsBuyerUI .sari-activity-menu-toggle {width:30px;}#courierNotificationsBuyerUI .sari-activity-menu {right:-2px;width:202px;}#courierNotificationsBuyerUI .sari-activity-time-mobile {display:inline;}#courierNotificationsBuyerUI .sari-activity-empty-state {min-height:360px;padding:56px 20px;}#courierNotificationsBuyerUI .sari-activity-pagination {gap:5px;}#courierNotificationsBuyerUI .sari-activity-page-link,#courierNotificationsBuyerUI .sari-activity-page-arrow {min-width:36px;height:35px;padding:0 8px;border-radius:10px;}}
@media(max-width:760px) {#courierNotificationsBuyerUI .sari-activity-shell,#courierNotificationsBuyerUI .sari-activity-empty-state {box-shadow:0 2px 4px rgba(15,23,42,.035),0 8px 18px rgba(15,23,42,.06),0 18px 38px rgba(15,23,42,.07);}}
@media(prefers-reduced-motion:reduce) {#courierNotificationsBuyerUI * {transition:none!important;}}
@endverbatim
</style>
@endpush

@section('content')
<div id="courierNotificationsBuyerUI" data-sari-activity-page>
    <section class="sari-activity-header">
        <div class="sari-activity-header-inner">
            <div><p class="sari-activity-eyebrow">Rider updates</p><h1 class="sari-activity-title">Your <span>Notifications</span></h1></div>
            <a class="sari-activity-header-link" href="{{ route('courier.history') }}">Delivery history <span class="sari-activity-header-arrow" aria-hidden="true">&gt;</span></a>
        </div>
    </section>
    <div class="sari-activity-actions">
        <nav class="sari-activity-filters" aria-label="Notification folders">
            <a class="sari-activity-filter" href="{{ route('courier.notifications') }}" @unless($showArchived) aria-current="page" @endunless>Recent</a>
            <a class="sari-activity-filter" href="{{ route('courier.notifications', ['filter' => 'archived']) }}" @if($showArchived) aria-current="page" @endif>Archived</a>
        </nav>
    </div>
    @if(session('success'))
        <div class="sari-activity-feedback" role="status">{{ session('success') }}</div>
    @endif
    @if($notifications->isEmpty())
        <section class="sari-activity-empty-state" aria-label="No notifications">
            <div>
                <span class="sari-activity-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 8v5l3 2"/><path d="M3.05 11a9 9 0 1 0 .5-3"/><path d="M3 4v4h4"/></svg></span>
                <h2 class="sari-activity-empty-title">{{ $showArchived ? 'No archived notifications' : 'No notifications yet' }}</h2>
                <p class="sari-activity-empty-copy">{{ $showArchived ? 'Archived Rider notifications will appear here. You can restore them anytime.' : 'Pickup assignments, last-mile deliveries, messages and payout updates will appear here.' }}</p>
                <a href="{{ route('courier.notifications', $showArchived ? [] : ['filter' => 'archived']) }}" class="sari-activity-empty-button">{{ $showArchived ? 'Back to Recent' : 'View archived notifications' }}</a>
            </div>
        </section>
    @else
        <section class="sari-activity-shell" aria-label="Rider notification list">
            <header class="sari-activity-toolbar">
                <h2>{{ $showArchived ? 'Archived Notifications' : 'Notifications' }}</h2>
                <div class="sari-activity-toolbar-right">
                    <span class="sari-activity-count">{{ number_format($notifications->total()) }} {{ $notifications->total() === 1 ? 'notification' : 'notifications' }}</span>
                    @unless($showArchived)
                        <form method="POST" action="{{ route('courier.notifications.read-all') }}" class="sari-activity-read-all-form">@csrf
                            <button type="submit" class="sari-activity-read-all" @disabled($unreadCount < 1)>Mark all as read</button>
                        </form>
                    @endunless
                </div>
            </header>
            <div class="sari-activity-items" data-courier-notification-list>
                @foreach($notifications as $event)
                    @php
                        $isRead = $event->read_at !== null;
                        $targetUrl = (string) ($event->target_url ?? '');
                        $hasTarget = $targetUrl !== '' && ((str_starts_with($targetUrl, '/') && !str_starts_with($targetUrl, '//')) || (in_array(parse_url($targetUrl, PHP_URL_SCHEME), ['http', 'https'], true) && strcasecmp((string) parse_url($targetUrl, PHP_URL_HOST), (string) request()->getHost()) === 0));
                        $eventTime = \Illuminate\Support\Carbon::parse($event->occurred_at);
                        $eventLabel = match ($event->category) {
                            'message' => 'Logistics',
                            'delivery' => 'Delivery update',
                            'last_mile' => 'Last-mile delivery',
                            'earnings' => 'Rider earnings',
                            'remittance' => 'COD remittance',
                            default => 'SARI Marketplace',
                        };
                    @endphp
                    <article class="sari-activity-row {{ $isRead ? '' : 'is-unread' }}" data-courier-activity-row
                             data-row-url="{{ $hasTarget ? $targetUrl : '' }}"
                             data-read-url="{{ route('courier.notifications.read', ['notification' => $event->id]) }}"
                             tabindex="{{ $hasTarget ? '0' : '-1' }}"
                             @if($hasTarget) role="link" aria-label="View notification: {{ $event->title }}" @endif>
                        <span class="sari-activity-avatar" aria-hidden="true"><img src="{{ asset('images/sari-main-logo.png') }}" alt="" loading="lazy" width="46" height="46"></span>
                        <div class="sari-activity-main">
                            <h3 class="sari-activity-event-title">{{ $event->title }}</h3>
                            <p class="sari-activity-event-message">{{ $event->message }}</p>
                            <div class="sari-activity-event-meta">
                                <span>{{ $eventLabel }}</span>
                                <span>{{ $eventTime->diffForHumans() }}</span>
                                <span class="sari-activity-time-mobile">{{ $eventTime->setTimezone('Asia/Manila')->format('M d, Y · h:i A') }}</span>
                            </div>
                        </div>
                        <div class="sari-activity-view-wrap">
                            @if($hasTarget)
                                @if($isRead)
                                    <a href="{{ $targetUrl }}" class="sari-activity-view">View</a>
                                @else
                                    <form method="POST" action="{{ route('courier.notifications.read', ['notification' => $event->id]) }}">@csrf
                                        <button type="submit" class="sari-activity-view">View</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                        <div class="sari-activity-menu-wrap">
                            <button type="button" class="sari-activity-menu-toggle" data-courier-activity-menu-toggle aria-label="More actions for {{ $event->title }}" aria-expanded="false" aria-haspopup="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                            </button>
                            <div class="sari-activity-menu" data-courier-activity-menu hidden>
                                @unless($isRead)
                                    <form method="POST" action="{{ route('courier.notifications.read', ['notification' => $event->id]) }}">@csrf
                                        <input type="hidden" name="stay" value="1">
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.3 12.2 2.4 2.5 5.2-5.4"/></svg><span>Mark read</span>
                                        </button>
                                    </form>
                                    <div class="sari-activity-menu-divider"></div>
                                @endunless
                                @if($hasTarget)
                                    @unless($isRead)
                                        <form method="POST" action="{{ route('courier.notifications.read', ['notification' => $event->id]) }}">@csrf
                                            <button type="submit" class="sari-activity-menu-item"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"/><circle cx="12" cy="12" r="2.4"/></svg><span>View update</span></button>
                                        </form>
                                    @else
                                        <a href="{{ $targetUrl }}" class="sari-activity-menu-item"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"/><circle cx="12" cy="12" r="2.4"/></svg><span>View update</span></a>
                                    @endunless
                                    <div class="sari-activity-menu-divider"></div>
                                @endif
                                @if($showArchived)
                                    <form method="POST" action="{{ route('courier.notifications.restore', ['notification' => $event->id]) }}">@csrf @method('PATCH')
                                        <button type="submit" class="sari-activity-menu-item"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l3 2"/></svg><span>Restore</span></button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('courier.notifications.archive', ['notification' => $event->id]) }}">@csrf @method('PATCH')
                                        <button type="submit" class="sari-activity-menu-item"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v11h14V9M10 13h4"/></svg><span>Archive</span></button>
                                    </form>
                                @endif
                                <div class="sari-activity-menu-divider"></div>
                                <form method="POST" action="{{ route('courier.notifications.destroy', ['notification' => $event->id]) }}" data-courier-activity-delete>@csrf @method('DELETE')
                                    <button type="submit" class="sari-activity-menu-item is-danger"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M5 6l1 14h12l1-14M10 10v7M14 10v7"/></svg><span>Delete</span></button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        <footer class="sari-activity-footer">
            <p class="sari-activity-range" aria-live="polite">Showing <strong>{{ number_format($notifications->firstItem()) }}–{{ number_format($notifications->lastItem()) }}</strong> of <strong>{{ number_format($notifications->total()) }}</strong> {{ $notifications->total() === 1 ? 'notification' : 'notifications' }}</p>
            @if($notifications->hasPages())
                <nav class="sari-activity-pagination" aria-label="Notification pages">
                    @if($notifications->onFirstPage())
                        <span class="sari-activity-page-arrow is-disabled" aria-disabled="true" aria-label="Previous page"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="sari-activity-page-arrow" rel="prev" aria-label="Previous page"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></a>
                    @endif
                    @php
                        $currentPage = $notifications->currentPage(); $lastPage = $notifications->lastPage();
                        $pages = collect([1, $lastPage])->merge(range(max(1, $currentPage-1), min($lastPage, $currentPage+1)))->unique()->sort()->values();
                    @endphp
                    @foreach($pages as $i => $page)
                        @if($i > 0 && $page - $pages[$i-1] > 1)
                            <span class="sari-activity-page-ellipsis" aria-hidden="true">&hellip;</span>
                        @endif
                        <a href="{{ $notifications->url($page) }}" class="sari-activity-page-link" aria-label="Page {{ $page }}" @if($page === $currentPage) aria-current="page" @endif>{{ $page }}</a>
                    @endforeach
                    @if($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="sari-activity-page-arrow" aria-label="Next page" rel="next"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></a>
                    @else
                        <span class="sari-activity-page-arrow is-disabled" aria-disabled="true" aria-label="Next page"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></span>
                    @endif
                </nav>
            @endif
        </footer>
    @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
  'use strict';
  // SARI-COURIER-BUYER-NOTIFICATIONS-UI-V7: page-only actions; header sound/polling remain untouched.
  if (window.__SARI_COURIER_NOTIFICATIONS_BUYER_UI_V7__) return;
  window.__SARI_COURIER_NOTIFICATIONS_BUYER_UI_V7__ = true;
  const root = document.getElementById('courierNotificationsBuyerUI');
  if (!root) return;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const closeMenus = except => {
    root.querySelectorAll('[data-courier-activity-menu]').forEach(menu => {
      if (menu === except) return;
      menu.hidden = true;
      menu.closest('.sari-activity-menu-wrap')?.querySelector('[data-courier-activity-menu-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  };
  // Only navigate to same-origin Rider links; never trust a crafted cross-domain target.
  const localUrl = value => {
    try { const url = new URL(value, location.origin); return url.origin === location.origin && /^https?:$/.test(url.protocol) ? url.href : null; }
    catch (_) { return null; }
  };
  const markReadThenOpen = async row => {
    const destination = localUrl(row?.dataset.rowUrl);
    if (!destination || row.dataset.loading === 'true') return;
    const endpoint = row.dataset.readUrl;
    if (row.classList.contains('is-unread') && endpoint) {
      row.dataset.loading = 'true';
      try {
        const response = await fetch(endpoint, {method:'POST',credentials:'same-origin',cache:'no-store',headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
        if (!response.ok) throw new Error('Unable to mark notification as read.');
      } catch (error) {
        row.dataset.loading = '';
        window.SariCourierToast?.error?.(error.message);
        if (!window.SariCourierToast) window.alert(error.message);
        return;
      }
    }
    window.location.assign(destination);
  };
  root.addEventListener('click', async event => {
    const toggle = event.target.closest('[data-courier-activity-menu-toggle]');
    if (toggle) {
      const menu = toggle.closest('.sari-activity-menu-wrap')?.querySelector('[data-courier-activity-menu]');
      if (!menu) return;
      const opening = menu.hidden;
      closeMenus(menu);
      menu.hidden = !opening;
      toggle.setAttribute('aria-expanded', opening ? 'true' : 'false');
      return;
    }
    if (event.target.closest('a,button,form,input,select,textarea,[data-courier-activity-menu]')) {
      if (!event.target.closest('.sari-activity-menu-wrap')) closeMenus();
      return;
    }
    const row = event.target.closest('[data-courier-activity-row]');
    if (row) await markReadThenOpen(row);
    else closeMenus();
  });
  root.addEventListener('keydown', async event => {
    if (event.key === 'Escape') { closeMenus(); return; }
    if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('[data-courier-activity-row]')) {
      event.preventDefault();
      await markReadThenOpen(event.target);
    }
  });
  root.addEventListener('submit', event => {
    const form = event.target.closest('[data-courier-activity-delete]');
    if (!form) return;
    if (!window.confirm('Delete this notification from your history? This cannot be undone. Your delivery records will not be deleted.')) {
      event.preventDefault(); return;
    }
    form.querySelector('button[type="submit"]')?.setAttribute('disabled', 'disabled');
  });
  document.addEventListener('click', event => {
    if (!event.target.closest('#courierNotificationsBuyerUI .sari-activity-menu-wrap')) closeMenus();
  });
})();
</script>
@endpush
