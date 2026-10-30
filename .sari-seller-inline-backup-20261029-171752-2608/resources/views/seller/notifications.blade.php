@extends('layouts.seller')



@section('title', 'Notifications — SARI Seller')

@section('page-title', 'Notifications')



@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/notifications.css') }}?v=20261007-1">
@endpush

@section('content')

@php

    $totalNotifications = method_exists($notifications, 'total')

        ? (int) $notifications->total()

        : (int) $notifications->count();

@endphp







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

        window.__SARI_SELLER_NOTIFICATIONS_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_NOTIFICATIONS_LIFECYCLE__ = lifecycle;



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

        }, { signal });



        document.addEventListener('keydown', (event) => {

            if (event.key === 'Escape') {

                closeMenus();

                if (document.activeElement === search && search.value) {

                    search.value = '';

                    applySearch();

                }

            }

        }, { signal });



        const cleanup = () => {

            search.dataset.initialized = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_NOTIFICATIONS_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_NOTIFICATIONS_LIFECYCLE__ = null;
            }

        };



        document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
        window.addEventListener('beforeunload', cleanup, { once: true, signal });

    }



    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', bootSellerNotificationsFeed, { once: true });

    } else {

        bootSellerNotificationsFeed();

    }

})();

</script>

@endpush
