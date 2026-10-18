
@extends('layouts.logistics')

@section('title', 'Notifications | SARI Logistics')
@section('page-title', 'Notifications')

@php
    /*
    |--------------------------------------------------------------------------
    | Server-Side Notification State
    |--------------------------------------------------------------------------
    |
    | All these values come from LogisticsNotificationController@index.
    | Search and status filtering happen before database pagination.
    |
    */

    $totalCount = (int) ($totalCount ?? $notifications->total());
    $filteredCount = (int) ($filteredCount ?? $notifications->total());
    $unreadCount = (int) ($unreadCount ?? 0);

    $activeFilter = ($activeFilter ?? 'all') === 'unread'
        ? 'unread'
        : 'all';

    $search = trim((string) ($search ?? ''));

    $isHubOperator = (bool) ($isHubOperator ?? false);
    $hubFacility = $hubFacility ?? null;

    /*
    |--------------------------------------------------------------------------
    | GET Filter Links
    |--------------------------------------------------------------------------
    |
    | Preserve the search keyword while switching tabs.
    | Reset pagination whenever the filter changes.
    |
    */

    $searchParams = $search !== ''
        ? ['q' => $search]
        : [];

    $allUrl = route(
        'logistics.notifications',
        array_merge(['filter' => 'all'], $searchParams)
    );

    $unreadUrl = route(
        'logistics.notifications',
        array_merge(['filter' => 'unread'], $searchParams)
    );

    $clearSearchUrl = route(
        'logistics.notifications',
        ['filter' => $activeFilter]
    );
@endphp

@push('styles')
<style>
    /*
    |--------------------------------------------------------------------------
    | Seller-Inspired Logistics Notification Center
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter {
        --ln-amber: #d9950b;
        --ln-amber-dark: #a9710c;
        --ln-ink: #29241e;
        --ln-muted: #8d8378;
        --ln-line: #e9e3da;

        width: 100%;
        max-width: 1450px;
        margin: 0 auto;
        padding: 4px 0 35px;

        font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
        font-size: 10px;
        color: var(--ln-ink);
    }

    #lgNotificationCenter *,
    #lgNotificationCenter *::before,
    #lgNotificationCenter *::after {
        box-sizing: border-box;
    }

    #lgNotificationCenter [hidden] {
        display: none !important;
    }

    #lgNotificationCenter button,
    #lgNotificationCenter input {
        font-family: inherit;
    }

    #lgNotificationCenter :is(a, button, input):focus-visible {
        outline: 2px solid rgba(217, 149, 11, .7);
        outline-offset: 3px;
    }

    /*
    |--------------------------------------------------------------------------
    | Page Heading
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;

        padding: 7px 2px 18px;
        margin-bottom: 17px;
        border-bottom: 1px solid #e5dfd6;
    }

    #lgNotificationCenter .ln-eyebrow {
        margin: 0 0 5px;
        color: #a77722;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    #lgNotificationCenter .ln-title {
        margin: 0;
        color: #252019;
        font-size: clamp(23px, 2.2vw, 30px);
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.045em;
    }

    #lgNotificationCenter .ln-title span {
        color: var(--ln-amber);
    }

    #lgNotificationCenter .ln-subtitle {
        margin: 7px 0 0;
        color: var(--ln-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    #lgNotificationCenter .ln-subtitle strong {
        color: #a36d11;
        font-weight: 700;
    }

    #lgNotificationCenter .ln-mark-all {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 39px;
        padding: 9px 13px;

        border: 1px solid #e6d3b2;
        border-radius: 10px;
        background: #fff8ed;
        color: #a36d11;

        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }

    #lgNotificationCenter .ln-mark-all:hover {
        background: #ffefd3;
        border-color: #dcb878;
    }

    #lgNotificationCenter .ln-mark-all svg {
        width: 15px;
        height: 15px;
    }

    /*
    |--------------------------------------------------------------------------
    | Recipient Context
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-context {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    #lgNotificationCenter .ln-context span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 10px;

        border: 1px solid #ebdfc8;
        border-radius: 999px;
        background: #fff9ef;
        color: #8e6e37;

        font-size: 9px;
        line-height: 1.3;
    }

    #lgNotificationCenter .ln-context strong {
        color: #75501a;
    }

    /*
    |--------------------------------------------------------------------------
    | Flash Messages
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-flash {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 12px 13px;
        margin-bottom: 14px;
        border-radius: 11px;
        font-size: 10px;
        line-height: 1.5;
    }

    #lgNotificationCenter .ln-flash.success {
        border: 1px solid #d5e9dc;
        background: #f1faf4;
        color: #457456;
    }

    #lgNotificationCenter .ln-flash.error {
        border: 1px solid #efd3d3;
        background: #fff5f5;
        color: #a64f4f;
    }

    /*
    |--------------------------------------------------------------------------
    | Server-Side Toolbar
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        padding: 13px 15px;
        margin-bottom: 14px;

        border: 1px solid var(--ln-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(33, 27, 20, .025);
    }

    #lgNotificationCenter .ln-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 300px;
        min-width: 0;
    }

    #lgNotificationCenter .ln-search-field {
        position: relative;
        flex: 1;
        min-width: 0;
        max-width: 350px;
    }

    #lgNotificationCenter .ln-search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        width: 15px;
        height: 15px;
        color: #9b9287;
        pointer-events: none;
    }

    #lgNotificationCenter .ln-search input {
        display: block;
        width: 100%;
        height: 39px;
        padding: 0 12px 0 37px;

        border: 1px solid #e0e2e6;
        border-radius: 10px;
        background: #fff;
        outline: none;

        color: #4d4439;
        font-size: 10px;
    }

    #lgNotificationCenter .ln-search input:focus {
        border-color: var(--ln-amber);
        box-shadow: 0 0 0 3px rgba(217,149,11,.08);
    }

    #lgNotificationCenter .ln-search-submit {
        min-height: 39px;
        padding: 8px 13px;

        border: 1px solid #d9950b;
        border-radius: 10px;
        background: #d9950b;
        color: #fff;

        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s;
    }

    #lgNotificationCenter .ln-search-submit:hover {
        background: #bd8109;
    }

    #lgNotificationCenter .ln-search-clear {
        color: #9a7135;
        font-size: 9px;
        font-weight: 600;
        text-decoration: none;
    }

    #lgNotificationCenter .ln-search-clear:hover {
        text-decoration: underline;
    }

    #lgNotificationCenter .ln-toolbar-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    /*
    |--------------------------------------------------------------------------
    | All / Unread Tabs
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-tabs {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 4px;

        border: 1px solid #e7e4de;
        border-radius: 10px;
        background: #f8f8fa;
    }

    #lgNotificationCenter .ln-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;

        min-height: 29px;
        padding: 6px 12px;

        border: 1px solid transparent;
        border-radius: 7px;
        background: transparent;
        color: #7e7469;

        font-size: 9px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s, color .15s;
    }

    #lgNotificationCenter .ln-tab:hover {
        color: #a16c11;
    }

    #lgNotificationCenter .ln-tab.is-active {
        border-color: #e9dfcf;
        background: #fff;
        color: #a16c11;
        box-shadow: 0 1px 4px rgba(35, 28, 19, .035);
    }

    #lgNotificationCenter .ln-tab-count {
        padding: 2px 6px;
        border-radius: 999px;
        background: #fff0d6;
        color: #a16b10;
        font-size: 8px;
        font-weight: 700;
    }

    #lgNotificationCenter .ln-result-count {
        color: #92887d;
        font-size: 9px;
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Panel
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-panel {
        border: 1px solid var(--ln-line);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(33, 27, 20, .03);
    }

    #lgNotificationCenter .ln-panel-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 9px;

        padding: 14px 16px;
        border-bottom: 1px solid #eee9e3;
    }

    #lgNotificationCenter .ln-panel-heading h2 {
        margin: 0;
        color: #373028;
        font-size: 12px;
        font-weight: 700;
    }

    #lgNotificationCenter .ln-panel-heading p {
        margin: 4px 0 0;
        color: #958a7f;
        font-size: 9px;
    }

    #lgNotificationCenter .ln-total-badge {
        padding: 6px 9px;
        border: 1px solid #eee0c6;
        border-radius: 999px;
        background: #fff9ef;
        color: #a16d14;
        font-size: 9px;
        font-weight: 600;
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Cards
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-row {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 13px;
        min-width: 0;
        padding: 16px;

        border-bottom: 1px solid #f0ece7;
        background: #fff;
        transition: background .15s;
    }

    #lgNotificationCenter .ln-row:last-child {
        border-bottom: 0;
    }

    #lgNotificationCenter .ln-row:hover {
        background: #fdfbf7;
    }

    #lgNotificationCenter .ln-row.is-unread {
        background: #fffdf7;
    }

    #lgNotificationCenter .ln-row.is-unread::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: #d9950b;
    }

    #lgNotificationCenter .ln-avatar {
        display: grid;
        place-items: center;
        flex: 0 0 39px;
        width: 39px;
        height: 39px;

        border: 1px solid #eee1c6;
        border-radius: 12px;
        background: #fff5e2;
        color: #af791b;
    }

    #lgNotificationCenter .ln-avatar svg {
        width: 19px;
        height: 19px;
    }

    #lgNotificationCenter .ln-body {
        flex: 1;
        min-width: 0;
    }

    #lgNotificationCenter .ln-row-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin: 0;

        color: #393026;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    #lgNotificationCenter .ln-new {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 6px;

        border-radius: 999px;
        background: #fff1d6;
        color: #a16c0d;

        font-size: 8px;
        font-weight: 700;
    }

    #lgNotificationCenter .ln-new::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    #lgNotificationCenter .ln-message {
        margin: 5px 0 0;
        color: #797065;
        font-size: 9.5px;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    #lgNotificationCenter .ln-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;

        color: #9a9085;
        font-size: 8.5px;
    }

    #lgNotificationCenter .ln-dot {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #b5a99a;
    }

    #lgNotificationCenter .ln-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-shrink: 0;
    }

    #lgNotificationCenter .ln-open {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        min-height: 30px;
        padding: 6px 12px;

        border: 1px solid #e7d4b2;
        border-radius: 8px;
        background: #fff8ed;
        color: #a16c12;

        font-size: 9px;
        font-weight: 600;
        cursor: pointer;
    }

    #lgNotificationCenter .ln-open:hover {
        background: #ffefd6;
    }

    #lgNotificationCenter .ln-open-disabled {
        border-color: #e8e7e5;
        background: #f8f8f8;
        color: #9c948a;
        cursor: default;
    }

    /*
    |--------------------------------------------------------------------------
    | Individual Read Action Menu
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-menu {
        position: relative;
    }

    #lgNotificationCenter .ln-menu-toggle {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;

        border: 1px solid transparent;
        border-radius: 8px;
        background: transparent;
        color: #81786d;
        cursor: pointer;
    }

    #lgNotificationCenter .ln-menu-toggle:hover,
    #lgNotificationCenter .ln-menu-toggle[aria-expanded="true"] {
        border-color: #e8e2d8;
        background: #f7f6f3;
        color: #a16c12;
    }

    #lgNotificationCenter .ln-menu-toggle svg {
        width: 15px;
        height: 15px;
    }

    #lgNotificationCenter .ln-menu-panel {
        position: absolute;
        right: 0;
        top: 35px;
        z-index: 30;

        display: none;
        width: 175px;
        padding: 5px;

        border: 1px solid #e9e5dd;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(30, 23, 15, .12);
    }

    #lgNotificationCenter .ln-menu-panel[data-open="true"] {
        display: block;
    }

    #lgNotificationCenter .ln-menu-action {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 9px;

        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #62574b;

        text-align: left;
        font-size: 9px;
        font-weight: 600;
        cursor: pointer;
    }

    #lgNotificationCenter .ln-menu-action:hover {
        background: #fff7e9;
        color: #a16c12;
    }

    #lgNotificationCenter .ln-menu-action svg {
        width: 14px;
        height: 14px;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty State and Footer
    |--------------------------------------------------------------------------
    */

    #lgNotificationCenter .ln-empty {
        display: grid;
        place-items: center;
        min-height: 235px;
        padding: 32px 18px;
        text-align: center;
    }

    #lgNotificationCenter .ln-empty-icon {
        display: grid;
        place-items: center;
        width: 43px;
        height: 43px;
        margin: 0 auto 12px;

        border-radius: 13px;
        background: #fff6e6;
        color: #b47c19;
    }

    #lgNotificationCenter .ln-empty-icon svg {
        width: 21px;
        height: 21px;
    }

    #lgNotificationCenter .ln-empty strong {
        display: block;
        color: #4b4136;
        font-size: 11px;
    }

    #lgNotificationCenter .ln-empty p {
        max-width: 310px;
        margin: 6px auto 0;
        color: #93887c;
        font-size: 9px;
        line-height: 1.7;
    }

    #lgNotificationCenter .ln-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;

        padding: 12px 16px;
        border-top: 1px solid #eee9e3;

        color: #968b7f;
        font-size: 9px;
    }

    #lgNotificationCenter .ln-pagination {
        margin-top: 15px;
        font-size: 10px;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive Layout
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {
        #lgNotificationCenter .ln-heading {
            flex-direction: column;
            align-items: stretch;
        }

        #lgNotificationCenter .ln-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        #lgNotificationCenter .ln-search {
            width: 100%;
            flex-basis: auto;
        }

        #lgNotificationCenter .ln-search-field {
            max-width: none;
        }

        #lgNotificationCenter .ln-toolbar-right {
            justify-content: space-between;
        }

        #lgNotificationCenter .ln-row {
            gap: 10px;
            padding: 14px 11px;
        }

        #lgNotificationCenter .ln-avatar {
            flex-basis: 34px;
            width: 34px;
            height: 34px;
        }
    }

    @media (max-width: 480px) {
        #lgNotificationCenter .ln-row {
            flex-wrap: wrap;
        }

        #lgNotificationCenter .ln-body {
            flex: 1 1 calc(100% - 48px);
        }

        #lgNotificationCenter .ln-actions {
            width: 100%;
            justify-content: flex-end;
            padding-left: 44px;
        }

        #lgNotificationCenter .ln-search {
            flex-wrap: wrap;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #lgNotificationCenter * {
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div id="lgNotificationCenter" data-logistics-notifications>

    {{-- PAGE HEADING --}}

    <header class="ln-heading">
        <div class="min-w-0">
            <p class="ln-eyebrow">
                {{ $isHubOperator ? 'Assigned Hub · Activity' : 'Network · Activity' }}
            </p>

            <h1 class="ln-title">
                Notifi<span>cations</span>
            </h1>

            <p class="ln-subtitle">
                {{ $isHubOperator
                    ? 'Updates delivered specifically to your Hub Operator account.'
                    : 'Updates and activity delivered to Main Logistics.' }}

                <strong>
                    {{ number_format($unreadCount) }}
                    {{ $unreadCount === 1
                        ? 'unread notification'
                        : 'unread notifications' }}.
                </strong>
            </p>
        </div>

        @if($unreadCount > 0)
            <form
                method="POST"
                action="{{ route('logistics.notifications.read-all') }}"
            >
                @csrf

                <button type="submit" class="ln-mark-all">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m5 12 4 4L19 6"/>
                        <path d="m12 16 2 2 5-5"/>
                    </svg>

                    Mark all as read
                </button>
            </form>
        @endif
    </header>

    {{-- PRIVATE MAILBOX CONTEXT --}}

    <div class="ln-context">
        @if($isHubOperator)
            <span>
                Assigned Hub:
                <strong>
                    {{ $hubFacility?->name ?: 'Municipal Hub' }}
                </strong>
            </span>

            <span>
                {{ $hubFacility?->municipality_name ?: 'Assigned municipality' }}

                @if($hubFacility?->province_name)
                    · {{ $hubFacility->province_name }}
                @endif
            </span>

            <span>Private Operator Mailbox</span>
        @else
            <span>Main Logistics Mailbox</span>
            <span>Network Administration</span>
        @endif
    </div>

    {{-- SUCCESS / ERROR FEEDBACK --}}

    @if(session('success'))
        <div class="ln-flash success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="ln-flash error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- SERVER-SIDE SEARCH AND FILTERS --}}

    <section class="ln-toolbar" aria-label="Notification search and filters">

        <form
            method="GET"
            action="{{ route('logistics.notifications') }}"
            class="ln-search"
            role="search"
        >
            <input
                type="hidden"
                name="filter"
                value="{{ $activeFilter }}"
            >

            <div class="ln-search-field">
                <svg
                    class="ln-search-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="6.5"/>
                    <path d="m16 16 4 4"/>
                </svg>

                <input
                    id="lnSearch"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    maxlength="100"
                    autocomplete="off"
                    placeholder="Search all notifications..."
                    aria-label="Search all notifications"
                >
            </div>

            <button
                type="submit"
                class="ln-search-submit"
            >
                Search
            </button>

            @if($search !== '')
                <a
                    href="{{ $clearSearchUrl }}"
                    class="ln-search-clear"
                    aria-label="Clear notification search"
                >
                    Clear
                </a>
            @endif
        </form>

        <div class="ln-toolbar-right">

            {{-- REAL SERVER-SIDE ALL / UNREAD TABS --}}

            <nav
                class="ln-tabs"
                aria-label="Notification status"
            >
                <a
                    href="{{ $allUrl }}"
                    class="ln-tab {{ $activeFilter === 'all' ? 'is-active' : '' }}"
                    @if($activeFilter === 'all')
                        aria-current="page"
                    @endif
                >
                    All

                    <span class="ln-tab-count">
                        {{ number_format($totalCount) }}
                    </span>
                </a>

                <a
                    href="{{ $unreadUrl }}"
                    class="ln-tab {{ $activeFilter === 'unread' ? 'is-active' : '' }}"
                    @if($activeFilter === 'unread')
                        aria-current="page"
                    @endif
                >
                    Unread

                    <span class="ln-tab-count">
                        {{ number_format($unreadCount) }}
                    </span>
                </a>
            </nav>

            <span class="ln-result-count">
                {{ number_format($filteredCount) }}
                {{ $filteredCount === 1 ? 'result' : 'results' }}
            </span>

        </div>
    </section>

    {{-- NOTIFICATION HISTORY --}}

    <section class="ln-panel" aria-labelledby="lnListTitle">

        <header class="ln-panel-heading">
            <div>
                <h2 id="lnListTitle">
                    {{ $activeFilter === 'unread'
                        ? 'Unread Notifications'
                        : 'Notification History' }}
                </h2>

                <p>
                    @if($search !== '')
                        Search results for “{{ $search }}”
                    @elseif($activeFilter === 'unread')
                        Showing unread updates across your entire mailbox.
                    @else
                        Your latest updates and activity notifications.
                    @endif
                </p>
            </div>

            <span class="ln-total-badge">
                {{ number_format($filteredCount) }}
                {{ $filteredCount === 1 ? 'record' : 'records' }}
            </span>
        </header>

        {{-- NOTIFICATION ROWS --}}

        <div id="lnList">

            @forelse($notifications as $notification)
                @php
                    $unread = ! $notification->isRead();
                    $type = strtolower((string) $notification->type);

                    $isTransfer = str_contains($type, 'transfer')
                        || str_contains($type, 'linehaul');

                    $isParcel = str_contains($type, 'parcel')
                        || str_contains($type, 'pickup')
                        || str_contains($type, 'sorting');

                    $isRider = str_contains($type, 'rider')
                        || str_contains($type, 'last_mile');
                @endphp

                <article class="ln-row {{ $unread ? 'is-unread' : '' }}">

                    {{-- CATEGORY ICON --}}

                    <div class="ln-avatar" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            @if($isTransfer)
                                <path d="M4 7h13m-4-4 4 4-4 4M20 17H7m4-4-4 4 4 4"/>
                            @elseif($isRider)
                                <circle cx="9" cy="8" r="3"/>
                                <path d="M3 20c.6-4 2.8-6 6-6M16 8h5m-2.5-2.5V11"/>
                            @elseif($isParcel)
                                <path d="m4 7 8-4 8 4-8 4-8-4ZM4 7v10l8 4 8-4V7M12 11v10"/>
                            @else
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                <path d="M10 21h4"/>
                            @endif
                        </svg>
                    </div>

                    {{-- NOTIFICATION CONTENT --}}

                    <div class="ln-body">
                        <h3 class="ln-row-title">
                            {{ $notification->title }}

                            @if($unread)
                                <span class="ln-new">
                                    New
                                </span>
                            @endif
                        </h3>

                        @if(filled($notification->message))
                            <p class="ln-message">
                                {{ $notification->message }}
                            </p>
                        @endif

                        <div class="ln-meta">
                            <span>
                                {{ $unread ? 'Unread' : 'Read' }}
                            </span>

                            <span class="ln-dot" aria-hidden="true"></span>

                            <time datetime="{{ $notification->created_at?->toIso8601String() }}">
                                {{ $notification->created_at?->diffForHumans() ?: 'Recently' }}
                            </time>

                            @if($notification->read_at)
                                <span class="ln-dot" aria-hidden="true"></span>

                                <span>
                                    Read {{ $notification->read_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- NOTIFICATION ACTIONS --}}

                    <div class="ln-actions">

                        @if(filled($notification->action_route))
                            <form
                                method="POST"
                                action="{{ route('logistics.notifications.open', [
                                    'notification' => $notification->id
                                ]) }}"
                            >
                                @csrf

                                <button type="submit" class="ln-open">
                                    Open
                                    <span aria-hidden="true">→</span>
                                </button>
                            </form>
                        @else
                            <span
                                class="ln-open ln-open-disabled"
                                aria-disabled="true"
                            >
                                No link
                            </span>
                        @endif

                        {{-- SELLER-STYLE THREE-DOT ACTION MENU --}}

                        @if($unread)
                            <div class="ln-menu" data-ln-menu>
                                <button
                                    type="button"
                                    class="ln-menu-toggle"
                                    aria-label="Options for {{ $notification->title }}"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    data-ln-menu-toggle
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="5" r="1.7"/>
                                        <circle cx="12" cy="12" r="1.7"/>
                                        <circle cx="12" cy="19" r="1.7"/>
                                    </svg>
                                </button>

                                <div
                                    class="ln-menu-panel"
                                    data-ln-menu-panel
                                    data-open="false"
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('logistics.notifications.read', [
                                            'notification' => $notification->id
                                        ]) }}"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="ln-menu-action"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                                aria-hidden="true"
                                            >
                                                <path d="m5 12 4 4L19 6"/>
                                            </svg>

                                            Mark as read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                </article>

            @empty

                {{-- SERVER-SIDE EMPTY STATE --}}

                <div class="ln-empty">
                    <div>
                        <span class="ln-empty-icon" aria-hidden="true">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                @if($activeFilter === 'unread' && $search === '')
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m8 12 2.5 2.5L16 9"/>
                                @else
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                    <path d="M10 21h4"/>
                                @endif
                            </svg>
                        </span>

                        @if($search !== '')
                            <strong>
                                No matching notifications
                            </strong>

                            <p>
                                No {{ $activeFilter === 'unread' ? 'unread ' : '' }}
                                notifications matched “{{ $search }}”
                                in your mailbox.
                            </p>

                            <a
                                href="{{ $clearSearchUrl }}"
                                class="mt-3 inline-block text-[9px] font-semibold text-[#a16c12] hover:underline"
                            >
                                Clear search
                            </a>

                        @elseif($activeFilter === 'unread')
                            <strong>
                                All caught up!
                            </strong>

                            <p>
                                You have no unread notifications in your mailbox.
                                New updates will appear here automatically
                                after they are recorded.
                            </p>

                            <a
                                href="{{ $allUrl }}"
                                class="mt-3 inline-block text-[9px] font-semibold text-[#a16c12] hover:underline"
                            >
                                View all notifications
                            </a>

                        @else
                            <strong>
                                No notifications yet
                            </strong>

                            <p>
                                {{ $isHubOperator
                                    ? 'Updates addressed to your Hub Operator account will appear here.'
                                    : 'Logistics network updates and activity notifications will appear here.' }}
                            </p>
                        @endif
                    </div>
                </div>

            @endforelse
        </div>

        {{-- DATABASE PAGINATION DETAILS --}}

        <footer class="ln-footer">
            <span>
                @if($filteredCount > 0)
                    Displaying
                    {{ $notifications->firstItem() }}
                    –
                    {{ $notifications->lastItem() }}
                    of {{ number_format($filteredCount) }}
                    matching records
                @else
                    No matching records
                @endif
            </span>

            <span>
                {{ number_format($totalCount) }}
                total notifications in your mailbox
            </span>
        </footer>
    </section>

    {{-- PAGINATION: CONTROLLER ALREADY PRESERVES FILTER + SEARCH --}}

    @if($notifications->hasPages())
        <div class="ln-pagination">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const root = document.querySelector(
        '[data-logistics-notifications]'
    );

    if (!root || root.dataset.initialized === '1') {
        return;
    }

    root.dataset.initialized = '1';

    /*
    |--------------------------------------------------------------------------
    | Individual Notification Menus
    |--------------------------------------------------------------------------
    |
    | Search, tabs and pagination are server-side.
    | JavaScript is only needed for the three-dot action menu.
    |
    */

    function closeMenus(except = null) {
        root.querySelectorAll('[data-ln-menu-panel]')
            .forEach(function (panel) {
                if (panel === except) {
                    return;
                }

                panel.dataset.open = 'false';

                const toggle = panel
                    .closest('[data-ln-menu]')
                    ?.querySelector('[data-ln-menu-toggle]');

                toggle?.setAttribute(
                    'aria-expanded',
                    'false'
                );
            });
    }

    root.addEventListener('click', function (event) {
        const toggle = event.target.closest(
            '[data-ln-menu-toggle]'
        );

        if (!toggle) {
            if (!event.target.closest('[data-ln-menu]')) {
                closeMenus();
            }

            return;
        }

        event.preventDefault();

        const menu = toggle.closest('[data-ln-menu]');

        const panel = menu?.querySelector(
            '[data-ln-menu-panel]'
        );

        if (!panel) {
            return;
        }

        const willOpen = panel.dataset.open !== 'true';

        closeMenus(panel);

        panel.dataset.open = willOpen
            ? 'true'
            : 'false';

        toggle.setAttribute(
            'aria-expanded',
            String(willOpen)
        );
    });

    document.addEventListener('click', function (event) {
        if (!root.contains(event.target)) {
            closeMenus();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenus();
        }
    });
});
</script>
@endpush
