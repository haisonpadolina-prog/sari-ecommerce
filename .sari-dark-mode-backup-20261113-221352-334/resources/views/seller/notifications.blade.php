@extends('layouts.seller')

@section('title', 'Notifications — SARI Seller')
@section('page-title', 'Notifications')

@push('styles')
<style id="sariSellerEmbeddedCss-notifications" data-sari-inline-css="notifications">
@verbatim
/* Seller notification inbox — scoped, single-source UI rules.
   Visual reference: Buyer Notifications. No global layout overrides. */
.seller-notifications-feed {
    --sn-gold: #c88912;
    --sn-gold-hover: #a9700c;
    --sn-ink: #252c36;
    --sn-muted: #737f8c;
    --sn-line: #e6e9ee;
    --sn-shadow: 0 3px 7px rgba(15,23,42,.045),
                 0 13px 30px rgba(15,23,42,.075),
                 0 25px 54px rgba(15,23,42,.07);
    width: 100%;
    max-width: 1480px;
    margin: 0 auto;
    padding: 10px 0 34px;
    color: var(--sn-ink);
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
}
.seller-notifications-feed *,
.seller-notifications-feed *::before,
.seller-notifications-feed *::after { box-sizing: border-box; }
.seller-notifications-feed button,
.seller-notifications-feed input,
.seller-notifications-feed a { font-family: inherit; }

/* Open header: no nested title card or decorative icon. */
.sn-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    padding: 0 2px 15px;
    border-bottom: 1px solid var(--sn-line);
}
.sn-eyebrow {
    margin: 0;
    color: #a3731b;
    font-size: 10.5px;
    font-weight: 750;
    letter-spacing: .13em;
    text-transform: uppercase;
}
.sn-heading {
    margin: 6px 0 0;
    font-size: clamp(27px, 2.5vw, 32px);
    font-weight: 750;
    line-height: 1.12;
    letter-spacing: -.04em;
}
.sn-heading span { color: var(--sn-gold); }
.sn-subtitle {
    margin: 7px 0 0;
    color: var(--sn-muted);
    font-size: 11.5px;
    line-height: 1.55;
}
.sn-subtitle strong { color: #475467; font-weight: 650; }
.sn-mark-all {
    border: 0;
    border-radius: 0;
    background: transparent;
    padding: 5px 0;
    color: var(--sn-gold-hover);
    cursor: pointer;
    font-size: 11.5px;
    font-weight: 700;
    white-space: nowrap;
}
.sn-mark-all:hover { color: #875707; }
.sn-mark-all:disabled { opacity: .45; cursor: default; }

/* Folder tabs and search remain OUTSIDE the floating feed. */
.sn-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 20px;
    margin: 14px 2px 17px;
}
.sn-tabs { display: flex; align-items: center; gap: 25px; }
.sn-tab {
    display: inline-flex;
    align-items: center;
    min-height: 36px;
    border: 0;
    background: transparent;
    padding: 5px 0;
    color: #7b8793;
    font-size: 12px;
    font-weight: 650;
    text-decoration: none;
}
.sn-tab:hover,
.sn-tab[aria-current="page"] { color: var(--sn-gold); }
.sn-search { position: relative; width: min(100%, 340px); }
.sn-search input {
    display: block;
    width: 100%;
    height: 42px;
    border: 1px solid #dce2e8;
    border-radius: 12px;
    outline: 0;
    background: #fff;
    padding: 0 38px 0 14px;
    color: #364152;
    font-size: 11.5px;
}
.sn-search input::placeholder { color: #9ca5af; }
.sn-search input:focus {
    border-color: #c6a267;
    box-shadow: 0 0 0 3px rgba(200,137,18,.09);
}
.sn-search-clear {
    position: absolute;
    top: 50%;
    right: 10px;
    display: none;
    width: 26px;
    height: 26px;
    transform: translateY(-50%);
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: #89939f;
    cursor: pointer;
    font-size: 19px;
    line-height: 1;
}
.sn-search-clear[data-visible="1"] { display: block; }
.sn-search-clear:hover { color: var(--sn-ink); background: #f1f3f5; }

/* One elevated workspace; notification rows are flat. */
.sn-shell {
    position: relative;
    isolation: isolate;
    overflow: visible;
    border: 1px solid var(--sn-line);
    border-radius: 20px;
    background: #fff;
    box-shadow: var(--sn-shadow);
}
.sn-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    min-height: 58px;
    padding: 13px 20px;
    border-bottom: 1px solid #edf0f3;
    border-radius: 20px 20px 0 0;
}
.sn-toolbar h2 { margin: 0; color: #303740; font-size: 15px; font-weight: 750; }
.sn-count { color: #85909c; font-size: 11px; font-weight: 600; }
.sn-row {
    position: relative;
    display: grid;
    grid-template-columns: 48px minmax(0, 1fr) auto 34px;
    align-items: center;
    gap: 14px;
    min-height: 104px;
    padding: 17px 19px;
    border-bottom: 1px solid #e8ebef;
    background: #fff;
    transition: background-color .14s ease;
}
.sn-row:last-child { border-bottom: 0; }
.sn-row:hover, .sn-row:focus-within { background: #f6f7f9; }
.sn-row[hidden] { display: none !important; }
.sn-avatar {
    display: grid;
    width: 48px;
    height: 48px;
    overflow: hidden;
    place-items: center;
    border: 2px solid #e4c489;
    border-radius: 50%;
    background: #161616;
}
.sn-avatar img {
    display: block;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
    transform: scale(1.4);
}
.sn-main { min-width: 0; }
.sn-title {
    margin: 0;
    overflow: hidden;
    color: #242a32;
    font-size: 14px;
    font-weight: 750;
    line-height: 1.4;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.sn-message {
    margin: 4px 0 0;
    overflow: hidden;
    color: #717b87;
    font-size: 11.5px;
    line-height: 1.55;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.sn-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;
    color: #929ca6;
    font-size: 10.5px;
    font-weight: 550;
}
.sn-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #aeb6bf; }
.sn-unread-label { color: #a3731b; font-weight: 700; }
.sn-view {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 68px;
    height: 36px;
    border: 0;
    border-radius: 11px;
    background: #f0f2f4;
    padding: 0 14px;
    color: #30363d;
    font-size: 11px;
    font-weight: 750;
    text-decoration: none;
}
.sn-view:hover { background: #e7e9ec; color: #20252b; }
.sn-view--disabled { opacity: .5; cursor: default; }

/* Vertical 3-dots menu (same interaction scheme as Buyer Notifications). */
.sn-menu-wrap { position: relative; display: flex; justify-content: flex-end; }
.sn-menu-toggle {
    display: grid;
    width: 34px;
    height: 36px;
    place-items: center;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #6b7480;
    cursor: pointer;
}
.sn-menu-toggle:hover,
.sn-menu-toggle[aria-expanded="true"] { color: #27303a; background: #f0f2f5; }
.sn-menu-toggle svg { width: 18px; height: 18px; fill: currentColor; }
.sn-menu {
    position: absolute;
    z-index: 80;
    top: 42px;
    right: 0;
    width: 224px;
    padding: 7px;
    border: 1px solid #e0e5ea;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 18px 42px rgba(15,23,42,.16), 0 3px 10px rgba(15,23,42,.06);
}
.sn-menu[hidden] { display: none !important; }
.sn-menu form { margin: 0; }
.sn-menu-item {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 43px;
    gap: 10px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    padding: 8px 11px;
    color: #374151;
    cursor: pointer;
    font-size: 11.5px;
    font-weight: 600;
    text-align: left;
    text-decoration: none;
}
.sn-menu-item:hover { background: #f2f4f7; color: #20252b; }
.sn-menu-item svg { width: 18px; height: 18px; flex: 0 0 18px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.sn-menu-item--danger { color: #cc2936; }
.sn-menu-item--danger:hover { color: #b91c30; background: #fff0f2; }
.sn-menu-divider { height: 1px; margin: 5px 5px; background: #eceff2; }

/* Empty feed, search feedback and pagination. */
.sn-empty {
    display: grid;
    min-height: 315px;
    place-items: center;
    padding: 48px 22px;
    text-align: center;
}
.sn-empty[hidden] { display: none !important; }
.sn-empty h3 { margin: 0; color: #576270; font-size: 17px; font-weight: 700; }
.sn-empty p { margin: 8px 0 0; color: #8a94a1; font-size: 11.5px; line-height: 1.6; }
.sn-empty a { display: inline-block; margin-top: 15px; color: var(--sn-gold-hover); font-size: 11.5px; font-weight: 700; text-decoration: none; }
.sn-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 20px;
    margin: 17px 2px 0;
}
.sn-range { margin: 0; color: #909aa6; font-size: 11px; }
.sn-pagination { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; }
.sn-page {
    display: inline-flex;
    min-width: 36px;
    height: 36px;
    align-items: center;
    justify-content: center;
    border: 1px solid #e8ebef;
    border-radius: 10px;
    background: #fff;
    padding: 0 8px;
    color: #6e7a87;
    font-size: 11px;
    font-weight: 650;
    text-decoration: none;
}
.sn-page:hover { background: #f5f6f8; color: #3b4551; }
.sn-page[aria-current="page"] { border-color: var(--sn-gold); background: var(--sn-gold); color: #fff; }
.sn-page--disabled { background: #f3f4f6; color: #b0b8c2; }
.sn-page-ellipsis { padding: 0 5px; color: #9ca6af; font-size: 12px; }
/* Server action feedback is presented only by the shared Seller toast engine. */
.seller-notifications-feed button:focus-visible,
.seller-notifications-feed a:focus-visible { outline: 2px solid var(--sn-gold); outline-offset: 3px; }

@media (max-width: 700px) {
    .sn-header { align-items: flex-start; flex-direction: column; }
    .sn-controls { align-items: stretch; flex-direction: column; }
    .sn-search { width: 100%; }
    .sn-shell { border-radius: 16px; }
    .sn-toolbar { border-radius: 16px 16px 0 0; padding: 12px 14px; }
    .sn-row { grid-template-columns: 43px minmax(0, 1fr) 32px; gap: 10px; min-height: 96px; padding: 14px 12px; }
    .sn-avatar { width: 43px; height: 43px; }
    .sn-title { font-size: 12.5px; }
    .sn-message { font-size: 10.5px; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .sn-meta { font-size: 9.5px; }
    .sn-view { display: none; }
    .sn-menu-wrap { grid-column: 3; grid-row: 1; }
    .sn-menu { right: -2px; width: 210px; }
    .sn-footer { align-items: flex-start; flex-direction: column; }
}
@media (prefers-reduced-motion: reduce) {
    .sn-row { transition: none; }
}
@media print {
    .sn-controls, .sn-menu-wrap, .sn-view, .sn-mark-all, .sn-footer { display: none !important; }
    .sn-shell { box-shadow: none; }
}
@endverbatim
</style>
@endpush

@section('content')
@php
    $totalNotifications = (int) $notifications->total();
@endphp
<div class="seller-notifications-feed" data-seller-notification-page>

    <header class="sn-header">
        <div>
            <p class="sn-eyebrow">Seller updates</p>
            <h1 class="sn-heading">Your <span>Notifications</span></h1>
            <p class="sn-subtitle">Orders, fulfillment and marketplace updates. <strong>{{ number_format((int) $unread) }} unread</strong>.</p>
        </div>
        @if(!$showArchived)
            <form method="POST" action="{{ route('seller.notifications.read-all') }}">
                @csrf
                <button type="submit" class="sn-mark-all" @disabled((int) $unread === 0)>Mark all as read</button>
            </form>
        @endif
    </header>

    <div class="sn-controls">
        <nav class="sn-tabs" aria-label="Notification folders">
            <a href="{{ route('seller.notifications.index') }}" class="sn-tab" @unless($showArchived) aria-current="page" @endunless wire:navigate.hover>Recent</a>
            <a href="{{ route('seller.notifications.index', ['filter' => 'archived']) }}" class="sn-tab" @if($showArchived) aria-current="page" @endif wire:navigate.hover>Archived</a>
        </nav>
        <div class="sn-search">
            <input id="sellerNotificationSearch" type="search" placeholder="Search notifications on this page..." aria-label="Search notifications on this page" autocomplete="off">
            <button type="button" id="sellerNotificationSearchClear" class="sn-search-clear" aria-label="Clear search" data-visible="0">&times;</button>
        </div>
    </div>

    <section class="sn-shell" aria-label="Seller notifications">
        <div class="sn-toolbar">
            <h2>{{ $showArchived ? 'Archived Notifications' : 'Notifications' }}</h2>
            <span id="sellerNotificationSearchCount" class="sn-count">{{ number_format($totalNotifications) }} {{ $totalNotifications === 1 ? 'notification' : 'notifications' }}</span>
        </div>

        <div id="sellerNotificationFeedItems">
            @forelse($notifications as $notification)
                <article class="sn-row" data-notification-item data-search="{{ strtolower(trim(($notification->title ?? '') . ' ' . ($notification->message ?? '') . ' ' . optional($notification->created_at)->diffForHumans())) }}">
                    <span class="sn-avatar" aria-hidden="true"><img src="{{ asset('images/sari-main-logo.png') }}" alt="" loading="lazy"></span>
                    <div class="sn-main">
                        <h3 class="sn-title">{{ $notification->title }}</h3>
                        <p class="sn-message">{{ $notification->message }}</p>
                        <div class="sn-meta">
                            <span @class(['sn-unread-label' => !$notification->read_at && !$showArchived])>{{ $showArchived ? 'Archived' : ($notification->read_at ? 'Notification' : 'New notification') }}</span>
                            <span class="sn-meta-dot" aria-hidden="true"></span>
                            <time datetime="{{ optional($notification->created_at)->toIso8601String() }}">{{ $notification->created_at?->diffForHumans() }}</time>
                        </div>
                    </div>
                    @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="sn-view">View</a>
                    @else
                        <span class="sn-view sn-view--disabled" aria-disabled="true">View</span>
                    @endif
                    <div class="sn-menu-wrap" data-notification-menu>
                        <button type="button" class="sn-menu-toggle" data-notification-menu-toggle aria-label="More actions for {{ $notification->title }}" aria-haspopup="true" aria-expanded="false" aria-controls="sn-notification-menu-{{ $notification->id }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="1.65"/><circle cx="12" cy="12" r="1.65"/><circle cx="12" cy="19" r="1.65"/></svg>
                        </button>
                        <div id="sn-notification-menu-{{ $notification->id }}" class="sn-menu" data-notification-menu-panel hidden>
                            @if(!$notification->read_at && !$showArchived)
                                <form method="POST" action="{{ route('seller.notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="sn-menu-item">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5 5.5-5.5"/></svg>
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                            @if($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="sn-menu-item">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                    View details
                                </a>
                            @endif
                            <div class="sn-menu-divider" aria-hidden="true"></div>
                            @if($showArchived)
                                <form method="POST" action="{{ route('seller.notifications.restore', $notification) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="sn-menu-item">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l3 2"/></svg>
                                        Restore
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('seller.notifications.archive', $notification) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="sn-menu-item">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v11h14V9M10 13h4"/></svg>
                                        Archive
                                    </button>
                                </form>
                            @endif
                            <div class="sn-menu-divider" aria-hidden="true"></div>
                            <form method="POST" action="{{ route('seller.notifications.destroy', $notification) }}" data-seller-notification-delete>
                                @csrf @method('DELETE')
                                <button type="submit" class="sn-menu-item sn-menu-item--danger">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M5 6l1 14h12l1-14M10 10v7M14 10v7"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="sn-empty">
                    <div>
                        <h3>{{ $showArchived ? 'No archived notifications' : 'No notifications yet' }}</h3>
                        <p>{{ $showArchived ? 'Archived notifications will appear here. You can restore them anytime.' : 'Order and marketplace updates will appear here as they arrive.' }}</p>
                        @if($showArchived)
                            <a href="{{ route('seller.notifications.index') }}" wire:navigate.hover>Back to Recent</a>
                        @endif
                    </div>
                </div>
            @endforelse
            <div class="sn-empty" id="sellerNotificationSearchEmpty" hidden>
                <div><h3>No matching notifications</h3><p>Try a different keyword or another page.</p></div>
            </div>
        </div>
    </section>

    @if($totalNotifications > 0)
        <footer class="sn-footer">
            <p class="sn-range">Showing {{ number_format($notifications->firstItem()) }}–{{ number_format($notifications->lastItem()) }} of {{ number_format($totalNotifications) }}</p>
            @if($notifications->hasPages())
                @php
                    $currentPage = $notifications->currentPage();
                    $lastPage = $notifications->lastPage();
                    $firstMiddle = max(2, min($currentPage - 1, $lastPage - 3));
                    $lastMiddle = min($lastPage - 1, max($currentPage + 1, 4));
                @endphp
                <nav class="sn-pagination" aria-label="Notification pages">
                    @if($notifications->onFirstPage())
                        <span class="sn-page sn-page--disabled" aria-disabled="true" aria-label="Previous page">‹</span>
                    @else
                        <a class="sn-page" href="{{ $notifications->previousPageUrl() }}" aria-label="Previous page" rel="prev" wire:navigate.hover>‹</a>
                    @endif
                    <a class="sn-page" href="{{ $notifications->url(1) }}" aria-label="Page 1" @if($currentPage === 1) aria-current="page" @endif wire:navigate.hover>1</a>
                    @if($firstMiddle > 2)<span class="sn-page-ellipsis" aria-hidden="true">…</span>@endif
                    @for($page = $firstMiddle; $page <= $lastMiddle; $page++)
                        <a class="sn-page" href="{{ $notifications->url($page) }}" aria-label="Page {{ $page }}" @if($page === $currentPage) aria-current="page" @endif wire:navigate.hover>{{ $page }}</a>
                    @endfor
                    @if($lastMiddle < $lastPage - 1)<span class="sn-page-ellipsis" aria-hidden="true">…</span>@endif
                    @if($lastPage > 1)
                        <a class="sn-page" href="{{ $notifications->url($lastPage) }}" aria-label="Page {{ $lastPage }}" @if($currentPage === $lastPage) aria-current="page" @endif wire:navigate.hover>{{ $lastPage }}</a>
                    @endif
                    @if($notifications->hasMorePages())
                        <a class="sn-page" href="{{ $notifications->nextPageUrl() }}" aria-label="Next page" rel="next" wire:navigate.hover>›</a>
                    @else
                        <span class="sn-page sn-page--disabled" aria-disabled="true" aria-label="Next page">›</span>
                    @endif
                </nav>
            @endif
        </footer>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    // Delegated listeners survive Livewire navigation; never bind twice.
    if (window.__SARI_SELLER_NOTIFICATION_UI_BOUND__) return;
    window.__SARI_SELLER_NOTIFICATION_UI_BOUND__ = true;

    const root = () => document.querySelector('[data-seller-notification-page]');
    const closeMenus = (except = null) => {
        root()?.querySelectorAll('[data-notification-menu-panel]').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;
            menu.closest('[data-notification-menu]')
                ?.querySelector('[data-notification-menu-toggle]')
                ?.setAttribute('aria-expanded', 'false');
        });
    };
    const updateSearch = () => {
        const page = root();
        if (!page) return;
        const input = page.querySelector('#sellerNotificationSearch');
        const clear = page.querySelector('#sellerNotificationSearchClear');
        const count = page.querySelector('#sellerNotificationSearchCount');
        const empty = page.querySelector('#sellerNotificationSearchEmpty');
        const query = String(input?.value || '').trim().toLowerCase();
        const rows = Array.from(page.querySelectorAll('[data-notification-item]'));
        let visible = 0;
        rows.forEach((row) => {
            const matches = !query || String(row.dataset.search || '').includes(query);
            row.hidden = !matches;
            if (matches) visible++;
        });
        if (clear) clear.dataset.visible = query ? '1' : '0';
        if (count && query) count.textContent = `${visible} matching on this page`;
        else if (count) count.textContent = count.dataset.totalLabel || count.textContent;
        if (empty) empty.hidden = !query || visible !== 0 || rows.length === 0;
        closeMenus();
    };
    const setup = () => {
        const count = root()?.querySelector('#sellerNotificationSearchCount');
        if (count) count.dataset.totalLabel = count.textContent;
    };
    document.addEventListener('input', (event) => {
        if (event.target?.id === 'sellerNotificationSearch' && root()?.contains(event.target)) updateSearch();
    });
    document.addEventListener('click', (event) => {
        const page = root();
        if (!page || !page.contains(event.target)) return;
        if (event.target.closest('#sellerNotificationSearchClear')) {
            const input = page.querySelector('#sellerNotificationSearch');
            if (input) { input.value = ''; updateSearch(); input.focus(); }
            return;
        }
        const toggle = event.target.closest('[data-notification-menu-toggle]');
        if (toggle) {
            event.preventDefault();
            const menu = toggle.closest('[data-notification-menu]')?.querySelector('[data-notification-menu-panel]');
            if (!menu) return;
            const shouldOpen = menu.hidden;
            closeMenus();
            menu.hidden = !shouldOpen;
            toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            return;
        }
        if (!event.target.closest('[data-notification-menu]')) closeMenus();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && root()) closeMenus();
    });
    // Keep the notification-action toast and its sound in the same browser
    // document as the seller's click. Native POST -> redirect loses user
    // activation, so the browser may block audio on the redirect page.
    // We still let Laravel authenticate, authorize and mutate the records.
    if (!window.__SARI_SELLER_NOTIFICATION_ACTION_TOAST_BOUND__) {
        window.__SARI_SELLER_NOTIFICATION_ACTION_TOAST_BOUND__ = true;
        document.addEventListener('submit', async (event) => {
            const form = event.target;
            const page = root();
            if (!page || !form || !page.contains(form) || form.tagName !== 'FORM'
                || form.method.toUpperCase() !== 'POST') return;

            if (form.matches('[data-seller-notification-delete]')
                && !window.confirm('Delete this notification from your history? This cannot be undone. Orders and other marketplace records will not be deleted.')) {
                event.preventDefault();
                return;
            }

            // Progressive enhancement: on old browsers use Laravel redirects
            // and the global flash-to-toast bridge instead.
            if (!window.fetch || !window.SariSellerToast?.show || !window.DOMParser) return;
            event.preventDefault();
            if (form.dataset.notificationSubmitting === '1') return;

            const action = new URL(form.action, window.location.href);
            if (action.origin !== window.location.origin) {
                window.SariSellerToast.error('Unable to submit this notification action safely.');
                return;
            }

            form.dataset.notificationSubmitting = '1';
            const submit = form.querySelector('button[type="submit"]');
            if (submit) submit.disabled = true;

            try {
                const response = await fetch(action.href, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    redirect: 'follow',
                    headers: { Accept: 'text/html' },
                    body: new FormData(form),
                });
                const html = await response.text();
                const destination = new URL(response.url, window.location.href);
                const documentResult = new DOMParser().parseFromString(html, 'text/html');
                const flash = documentResult.getElementById('sariSellerToastFlash');
                const errorText = (flash?.dataset.error || '').trim();
                const successText = (flash?.dataset.success || '').trim();

                // The expected successful response is Laravel's redirect back
                // to the *same notification page*. Do not turn login, CSRF,
                // 403, validation errors, or unexpected HTML into success.
                if (!response.ok || !response.redirected
                    || destination.origin !== window.location.origin
                    || destination.pathname !== window.location.pathname
                    || errorText) {
                    throw new Error(errorText || 'Unable to complete this notification action. Please try again.');
                }

                const feedback = successText || (
                    action.pathname.includes('/read-all') ? 'Notifications marked as read.'
                    : action.pathname.endsWith('/read') ? 'Notification marked as read.'
                    : 'Notification updated.'
                );
                window.SariSellerToast.success(feedback);

                // Livewire changes the page without interrupting the audible
                // toast. The Seller shell already refreshes badges on navigate.
                if (typeof window.Livewire?.navigate === 'function') {
                    window.Livewire.navigate(destination.href);
                } else {
                    // Audio is initiated *before* fallback full navigation.
                    window.setTimeout(() => window.location.assign(destination.href), 550);
                }
            } catch (error) {
                window.SariSellerToast.error(error?.message || 'Unable to complete this notification action.');
                form.dataset.notificationSubmitting = '0';
                if (submit) submit.disabled = false;
            }
        });
    }
    document.addEventListener('livewire:navigated', setup);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup, { once: true });
    else setup();
})();
</script>
@endpush
