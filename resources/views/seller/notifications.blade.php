@extends('layouts.seller')

@section('title', 'Notifications — SARI Seller')
@section('page-title', 'Notifications')

@section('content')
@php
    $totalNotifications = method_exists($notifications, 'total')
        ? (int) $notifications->total()
        : (int) $notifications->count();
@endphp

<style>
    .seller-notifications-feed {
        --nf-gold: #d59617;
        --nf-gold-dark: #a96f06;
        --nf-gold-soft: #fffaf0;
        --nf-ink: #202124;
        --nf-text: #475467;
        --nf-muted: #8f96a3;
        --nf-line: #e4e7eb;
        --nf-soft-line: #eef0f2;
        --nf-canvas: #f5f6f8;
        --nf-panel: #ffffff;
        --nf-green: #32c36c;

        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        color: var(--nf-ink);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-notifications-feed *,
    .seller-notifications-feed *::before,
    .seller-notifications-feed *::after {
        box-sizing: border-box;
    }

    .nf-alert {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-bottom: 12px;
        border: 1px solid;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 9px;
        font-weight: 600;
        line-height: 1.5;
    }

    .nf-alert--success {
        border-color: #cfe4d7;
        background: #f3faf5;
        color: #4f7d63;
    }

    .nf-alert--error {
        border-color: #efcece;
        background: #fff5f5;
        color: #a65353;
    }

    .nf-alert__icon {
        display: grid;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        place-items: center;
        border-radius: 7px;
        background: rgba(255,255,255,.78);
    }

    .nf-alert__icon svg {
        width: 12px;
        height: 12px;
    }

    .nf-top {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
    }

    .nf-eyebrow {
        margin: 0;
        color: #8e949d;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .nf-title {
        margin: 5px 0 0;
        color: #202124;
        font-size: clamp(28px, 2.2vw, 36px);
        font-weight: 700;
        letter-spacing: -.045em;
        line-height: 1.06;
    }

    .nf-subtitle {
        margin: 7px 0 0;
        color: var(--nf-muted);
        font-size: 9.5px;
        line-height: 1.55;
    }

    .nf-subtitle strong {
        color: #4b5563;
        font-weight: 700;
    }

    .nf-mark-all {
        display: inline-flex;
        height: 38px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        flex: 0 0 auto;
        border: 1px solid #dfe3e8;
        border-radius: 10px;
        background: #fff;
        padding: 0 13px;
        color: #475467;
        font: inherit;
        font-size: 8.5px;
        font-weight: 700;
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease,
            transform .15s ease;
    }

    .nf-mark-all:hover {
        border-color: #d9c391;
        background: var(--nf-gold-soft);
        color: #8f5d00;
        transform: translateY(-1px);
    }

    .nf-mark-all svg {
        width: 13px;
        height: 13px;
    }

    .nf-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
    }

    .nf-search {
        position: relative;
        width: min(520px, 100%);
    }

    .nf-search__icon {
        position: absolute;
        left: 13px;
        top: 50%;
        width: 15px;
        height: 15px;
        color: #98a2b3;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .nf-search__input {
        width: 100%;
        height: 44px;
        border: 1px solid #d9dde3;
        border-radius: 11px;
        background: #fff;
        padding: 0 38px 0 38px;
        color: #344054;
        font: inherit;
        font-size: 9.5px;
        outline: none;
        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    .nf-search__input::placeholder {
        color: #9ca3af;
    }

    .nf-search__input:focus {
        border-color: var(--nf-gold);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(213,150,23,.08);
    }

    .nf-search__clear {
        position: absolute;
        right: 9px;
        top: 50%;
        display: none;
        width: 26px;
        height: 26px;
        place-items: center;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #98a2b3;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .nf-search__clear:hover {
        background: #f3f4f6;
        color: #475467;
    }

    .nf-search__clear[data-visible="1"] {
        display: grid;
    }

    .nf-search__clear svg {
        width: 13px;
        height: 13px;
    }

    .nf-search-count {
        color: #98a2b3;
        font-size: 8px;
        white-space: nowrap;
    }

    .nf-panel {
        margin-top: 16px;
        overflow: hidden;
        border: 1px solid #dfe3e8;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(15,23,42,.055);
    }

    .nf-row {
        position: relative;
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        min-height: 92px;
        border-bottom: 1px solid var(--nf-soft-line);
        background: #fff;
        padding: 16px 20px;
        transition:
            background .14s ease,
            transform .14s ease;
    }

    .nf-row:last-child {
        border-bottom: 0;
    }

    .nf-row:hover {
        background: #fbfcfd;
    }

    .nf-row--unread {
        background: #fffefb;
    }

    .nf-row--unread:hover {
        background: #fffaf0;
    }

    .nf-row--unread::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: var(--nf-gold);
    }

    .nf-avatar {
        position: relative;
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        overflow: hidden;
        border: 3px solid #34cc70;
        border-radius: 999px;
        background: #202329;
        box-shadow:
            0 0 0 2px #fff,
            0 3px 10px rgba(15,23,42,.08);
    }

    .nf-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nf-row--unread .nf-avatar::after {
        content: '';
        position: absolute;
        right: -1px;
        bottom: -1px;
        width: 9px;
        height: 9px;
        border: 2px solid #fff;
        border-radius: 999px;
        background: var(--nf-gold);
    }

    .nf-main {
        min-width: 0;
    }

    .nf-row__title {
        margin: 0;
        color: #25272b;
        font-size: 10.5px;
        font-weight: 750;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .nf-row--unread .nf-row__title {
        color: #15171a;
    }

    .nf-row__message {
        margin: 3px 0 0;
        color: #7b828d;
        font-size: 8.5px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .nf-row__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        margin-top: 3px;
        color: #949ba5;
        font-size: 8px;
        line-height: 1.4;
    }

    .nf-meta-dot {
        width: 3px;
        height: 3px;
        border-radius: 999px;
        background: #c2c7ce;
    }

    .nf-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-self: end;
    }

    .nf-view {
        display: inline-flex;
        height: 31px;
        min-width: 60px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e4e7eb;
        border-radius: 9px;
        background: #f7f8fa;
        padding: 0 12px;
        color: #30343a;
        font-size: 8.5px;
        font-weight: 750;
        text-decoration: none;
        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .nf-view:hover {
        border-color: #dfc68f;
        background: #fff8e8;
        color: #8b5a00;
    }

    .nf-view--disabled {
        opacity: .52;
        pointer-events: none;
    }

    .nf-menu {
        position: relative;
    }

    .nf-menu__button {
        display: grid;
        width: 31px;
        height: 31px;
        place-items: center;
        border: 1px solid transparent;
        border-radius: 9px;
        background: transparent;
        color: #7d8590;
        cursor: pointer;
        transition:
            border-color .15s ease,
            background .15s ease,
            color .15s ease;
    }

    .nf-menu__button:hover,
    .nf-menu__button[aria-expanded="true"] {
        border-color: #e4e7eb;
        background: #f7f8fa;
        color: #344054;
    }

    .nf-menu__button svg {
        width: 15px;
        height: 15px;
    }

    .nf-menu__panel {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        z-index: 30;
        display: none;
        min-width: 150px;
        overflow: hidden;
        border: 1px solid #e2e6eb;
        border-radius: 10px;
        background: #fff;
        padding: 5px;
        box-shadow: 0 12px 28px rgba(15,23,42,.14);
    }

    .nf-menu__panel[data-open="1"] {
        display: block;
    }

    .nf-menu__action {
        display: flex;
        width: 100%;
        min-height: 32px;
        align-items: center;
        gap: 8px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        padding: 0 9px;
        color: #475467;
        font: inherit;
        font-size: 8px;
        font-weight: 650;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
    }

    .nf-menu__action:hover {
        background: #f7f8fa;
        color: #202124;
    }

    .nf-menu__action svg {
        width: 12px;
        height: 12px;
        flex: 0 0 12px;
    }

    .nf-empty,
    .nf-search-empty {
        display: grid;
        min-height: 320px;
        place-items: center;
        padding: 34px 20px;
        text-align: center;
    }

    .nf-empty__icon {
        display: grid;
        width: 50px;
        height: 50px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #e4e7eb;
        border-radius: 15px;
        background: #f8fafc;
        color: #98a2b3;
    }

    .nf-empty__icon svg {
        width: 21px;
        height: 21px;
    }

    .nf-empty__title {
        margin: 11px 0 0;
        color: #475467;
        font-size: 10px;
        font-weight: 750;
    }

    .nf-empty__copy {
        margin: 4px 0 0;
        color: #98a2b3;
        font-size: 8.3px;
        line-height: 1.55;
    }

    .nf-pagination {
        margin-top: 12px;
        overflow: hidden;
        border: 1px solid #e4e7eb;
        border-radius: 12px;
        background: #fff;
        padding: 10px 12px;
    }

    .nf-mark-all:focus-visible,
    .nf-search__clear:focus-visible,
    .nf-view:focus-visible,
    .nf-menu__button:focus-visible,
    .nf-menu__action:focus-visible {
        outline: 2px solid rgba(213,150,23,.3);
        outline-offset: 2px;
    }

    @media (max-width: 760px) {
        .nf-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .nf-mark-all {
            width: 100%;
        }

        .nf-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .nf-search {
            width: 100%;
        }

        .nf-row {
            grid-template-columns: 40px minmax(0, 1fr);
            gap: 11px;
            min-height: 0;
            padding: 14px 14px;
        }

        .nf-avatar {
            width: 38px;
            height: 38px;
        }

        .nf-actions {
            grid-column: 2;
            justify-self: start;
            margin-top: 7px;
        }

        .nf-row__title {
            font-size: 9.8px;
        }
    }

    @media (max-width: 480px) {
        .nf-panel {
            border-radius: 15px;
        }

        .nf-row {
            padding: 13px 12px;
        }

        .nf-view {
            min-width: 54px;
            height: 29px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .nf-mark-all,
        .nf-search__input,
        .nf-row,
        .nf-view,
        .nf-menu__button {
            transition: none !important;
        }
    }
</style>

<div class="seller-notifications-feed">
    @if(session('success'))
        <div class="nf-alert nf-alert--success" role="status">
            <span class="nf-alert__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m7.5 12 3 3 6-6"></path>
                </svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="nf-alert nf-alert--error" role="alert">
            <span class="nf-alert__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v6"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <header class="nf-top">
        <div>
            <p class="nf-eyebrow">Activity</p>
            <h1 class="nf-title">Notifications</h1>
            <p class="nf-subtitle">
                Seller alerts and SARI activity updates.
                <strong>{{ (int) $unread }} unread</strong>.
            </p>
        </div>

        @if($unread > 0)
            <form method="POST" action="{{ route('seller.notifications.read-all') }}">
                @csrf
                <button type="submit" class="nf-mark-all">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="m5 12 4 4L19 6"></path>
                        <path d="m12 16 2 2 5-5"></path>
                    </svg>
                    Mark all as read
                </button>
            </form>
        @endif
    </header>

    <div class="nf-toolbar">
        <div class="nf-search">
            <svg class="nf-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="m16 16 4 4"></path>
            </svg>

            <input
                id="sellerNotificationSearch"
                type="search"
                class="nf-search__input"
                autocomplete="off"
                placeholder="Search notifications..."
                aria-label="Search notifications"
            >

            <button
                id="sellerNotificationSearchClear"
                type="button"
                class="nf-search__clear"
                aria-label="Clear notification search"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m7 7 10 10"></path>
                    <path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        <span id="sellerNotificationSearchCount" class="nf-search-count">
            {{ $notifications->count() }} shown
        </span>
    </div>

    <section class="nf-panel" aria-label="Seller notifications">
        <div id="sellerNotificationList">
            @forelse($notifications as $notification)
                <article
                    class="nf-row {{ $notification->read_at ? '' : 'nf-row--unread' }}"
                    data-notification-item
                    data-search="{{ strtolower(trim(($notification->title ?? '') . ' ' . ($notification->message ?? '') . ' ' . optional($notification->created_at)->diffForHumans())) }}"
                >
                    <div class="nf-avatar" aria-hidden="true">
                        <img src="{{ asset('images/sari-main-logo.png') }}" alt="">
                    </div>

                    <div class="nf-main">
                        <p class="nf-row__title">{{ $notification->title }}</p>
                        <p class="nf-row__message">{{ $notification->message }}</p>

                        <div class="nf-row__meta">
                            <span>{{ $notification->read_at ? 'Notification' : 'New notification' }}</span>
                            <span class="nf-meta-dot" aria-hidden="true"></span>
                            <time datetime="{{ optional($notification->created_at)->toIso8601String() }}">
                                {{ $notification->created_at?->diffForHumans() }}
                            </time>
                        </div>
                    </div>

                    <div class="nf-actions">
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="nf-view">View</a>
                        @else
                            <span class="nf-view nf-view--disabled" aria-disabled="true">View</span>
                        @endif

                        <div class="nf-menu" data-notification-menu>
                            <button
                                type="button"
                                class="nf-menu__button"
                                data-notification-menu-toggle
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-label="Notification options"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <circle cx="12" cy="5" r="1.7"></circle>
                                    <circle cx="12" cy="12" r="1.7"></circle>
                                    <circle cx="12" cy="19" r="1.7"></circle>
                                </svg>
                            </button>

                            <div class="nf-menu__panel" data-notification-menu-panel>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="nf-menu__action">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12h14"></path>
                                            <path d="m13 6 6 6-6 6"></path>
                                        </svg>
                                        Open notification
                                    </a>
                                @endif

                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('seller.notifications.read', $notification) }}">
                                        @csrf
                                        <button type="submit" class="nf-menu__action">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="m5 12 4 4L19 6"></path>
                                            </svg>
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="nf-empty">
                    <div>
                        <div class="nf-empty__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>
                        </div>
                        <p class="nf-empty__title">No notifications yet</p>
                        <p class="nf-empty__copy">Seller alerts and SARI activity updates will appear here.</p>
                    </div>
                </div>
            @endforelse

            <div id="sellerNotificationSearchEmpty" class="nf-search-empty" hidden>
                <div>
                    <div class="nf-empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="m16 16 4 4"></path>
                        </svg>
                    </div>
                    <p class="nf-empty__title">No matching notifications</p>
                    <p class="nf-empty__copy">Try a different keyword.</p>
                </div>
            </div>
        </div>
    </section>

    @if($notifications->hasPages())
        <div class="nf-pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootSellerNotificationsFeed() {
        const search = document.getElementById('sellerNotificationSearch');
        const clear = document.getElementById('sellerNotificationSearchClear');
        const count = document.getElementById('sellerNotificationSearchCount');
        const empty = document.getElementById('sellerNotificationSearchEmpty');
        const items = Array.from(document.querySelectorAll('[data-notification-item]'));

        if (!search || search.dataset.initialized === '1') return;
        search.dataset.initialized = '1';

        const closeMenus = (except = null) => {
            document.querySelectorAll('[data-notification-menu-panel]').forEach((panel) => {
                if (panel === except) return;
                panel.dataset.open = '0';
                panel.closest('[data-notification-menu]')
                    ?.querySelector('[data-notification-menu-toggle]')
                    ?.setAttribute('aria-expanded', 'false');
            });
        };

        const applySearch = () => {
            const query = String(search.value || '').trim().toLowerCase();
            let visible = 0;

            items.forEach((item) => {
                const haystack = String(item.dataset.search || '').toLowerCase();
                const matches = query === '' || haystack.includes(query);

                item.hidden = !matches;
                if (matches) visible += 1;
            });

            if (clear) clear.dataset.visible = query === '' ? '0' : '1';
            if (count) count.textContent = `${visible} shown`;
            if (empty) empty.hidden = query === '' || visible > 0;

            closeMenus();
        };

        search.addEventListener('input', applySearch);

        clear?.addEventListener('click', () => {
            search.value = '';
            applySearch();
            search.focus();
        });

        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-notification-menu-toggle]');

            if (toggle) {
                event.preventDefault();

                const wrap = toggle.closest('[data-notification-menu]');
                const panel = wrap?.querySelector('[data-notification-menu-panel]');
                if (!panel) return;

                const opening = panel.dataset.open !== '1';
                closeMenus(panel);
                panel.dataset.open = opening ? '1' : '0';
                toggle.setAttribute('aria-expanded', opening ? 'true' : 'false');
                return;
            }

            if (!event.target.closest('[data-notification-menu]')) {
                closeMenus();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenus();
                if (document.activeElement === search && search.value) {
                    search.value = '';
                    applySearch();
                }
            }
        });

        const cleanup = () => {
            search.dataset.initialized = '0';
        };

        document.addEventListener('livewire:navigating', cleanup, { once: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSellerNotificationsFeed, { once: true });
    } else {
        bootSellerNotificationsFeed();
    }
})();
</script>
@endpush
