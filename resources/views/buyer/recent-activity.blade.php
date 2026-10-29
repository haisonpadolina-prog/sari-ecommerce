@extends('layouts.buyer')

@section('title', 'Recent Activity — SARI')
@section('page-title', 'Recent Activity')

@section('content')
@include('components.buyer.header')

@php
    $isPaginated = $recentActivity instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $activityItems = $isPaginated ? collect($recentActivity->items()) : collect($recentActivity);
@endphp

<div
    data-sari-activity-page
    class="mx-auto w-full"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        [data-sari-activity-page] {
            --activity-page:#f5f6f8;
            --activity-panel:#ffffff;
            --activity-border:#e3e7eb;
            --activity-border-soft:#edf0f3;
            --activity-text:#20252b;
            --activity-muted:#747d87;
            --activity-gold:#c99128;
            --activity-gold-dark:#ad781b;
            width:100%;
            max-width:1480px;
            margin-inline:auto;
            padding:22px 24px 44px;
            color:var(--activity-text);
        }

        .sari-activity-header {
            padding:2px 2px 14px;
        }

        .sari-activity-header-inner {
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:16px;
        }

        .sari-activity-eyebrow {
            margin:0;
            color:#a87316;
            font-size:10.5px;
            font-weight:800;
            letter-spacing:.14em;
            line-height:1.2;
            text-transform:uppercase;
        }

        .sari-activity-title {
            margin:5px 0 0;
            color:#2e343b;
            font-size:clamp(27px,2.25vw,31px);
            font-weight:760;
            line-height:1.04;
            letter-spacing:-.035em;
        }

        .sari-activity-title span {
            color:var(--activity-gold);
        }

        /* Match the Cart page's Continue Shopping text + arrow styling. */
        .sari-activity-notifications-link {
            position:relative !important;
            top:-12px !important;
            display:inline-flex !important;
            align-items:center !important;
            gap:8px !important;
            border:0 !important;
            border-radius:0 !important;
            background:transparent !important;
            padding:0 !important;
            color:#59636e !important;
            font-size:11.5px !important;
            font-weight:750 !important;
            line-height:1.35 !important;
            text-decoration:none !important;
            box-shadow:none !important;
            transition:color .14s ease !important;
        }

        .sari-activity-notifications-arrow {
            display:inline-block;
            margin-left:0 !important;
            color:currentColor;
            font-size:18px !important;
            font-weight:800 !important;
            line-height:.8 !important;
            transform:translateY(-1px);
        }

        .sari-activity-notifications-link:hover,
        .sari-activity-notifications-link:focus-visible {
            color:#c88912 !important;
            background:transparent !important;
            outline:none !important;
            text-decoration:none !important;
            box-shadow:none !important;
        }

        /* Reference-image notification list shell */
        .sari-activity-shell {
            position:relative;
            overflow:visible;
            margin-top:0;
            border:1px solid #dfe4e9;
            border-radius:22px;
            background:#fff;
            box-shadow:
                0 18px 46px rgba(31,41,55,.08),
                0 3px 10px rgba(31,41,55,.035);
        }

        .sari-activity-toolbar {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            min-height:54px;
            padding:0 20px;
            border-bottom:1px solid var(--activity-border-soft);
            background:#fff;
            border-radius:22px 22px 0 0;
        }

        .sari-activity-toolbar h2 {
            margin:0;
            color:#303740;
            font-size:15px;
            font-weight:800;
            line-height:1.2;
        }

        /* Keep the read-all action inside the activity card header. */
        .sari-activity-toolbar-right {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            flex-wrap:wrap;
            min-width:0;
            gap:9px 20px;
        }

        .sari-activity-count {
            color:#7d8792;
            font-size:10.5px;
            font-weight:650;
        }

        .sari-activity-items {
            position:relative;
            overflow:visible;
        }

        .sari-activity-row {
            position:relative;
            display:grid;
            grid-template-columns:48px minmax(0,1fr) auto 34px;
            gap:14px;
            align-items:center;
            min-height:104px;
            padding:16px 18px;
            border-bottom:1px solid #e7eaee;
            background:#fff;
            cursor:pointer;
            transition:background-color .14s ease;
        }

        .sari-activity-row:first-child {
            border-radius:0;
        }

        .sari-activity-row:last-child {
            border-bottom:0;
        }

        .sari-activity-row:hover,
        .sari-activity-row:focus-within {
            background:#f4f5f6;
        }

        /* Fill and crop the SARI art into a true circle, not a rounded-square inset. */
        .sari-activity-avatar {
            display:grid;
            width:48px;
            height:48px;
            flex:0 0 48px;
            place-items:center;
            overflow:hidden;
            border:2px solid #e5c78d;
            border-radius:50%;
            background:#f4f0e7;
            padding:0;
            box-shadow:0 2px 8px rgba(37,43,49,.08);
        }

        .sari-activity-avatar img {
            display:block;
            width:100%;
            height:100%;
            max-width:none;
            object-fit:cover;
            border-radius:0;
            transform:none;
            transform-origin:center;
        }

        .sari-activity-avatar-initials {
            font-size:14px;
            font-weight:800;
            color:#9d761f;
            letter-spacing:.03em;
        }
        .sari-activity-main {
            min-width:0;
            padding-right:8px;
        }

        .sari-activity-event-title {
            margin:0;
            overflow:hidden;
            color:#1e2329;
            font-size:15px;
            font-weight:760;
            line-height:1.3;
            letter-spacing:-.012em;
            text-overflow:ellipsis;
            white-space:nowrap;
        }


        .sari-activity-event-message {
            margin:4px 0 0;
            overflow:hidden;
            color:#707985;
            font-size:12px;
            font-weight:500;
            line-height:1.45;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .sari-activity-event-meta {
            display:flex;
            min-width:0;
            flex-wrap:wrap;
            gap:5px;
            margin-top:4px;
            color:#7f8892;
            font-size:11px;
            font-weight:520;
            line-height:1.35;
        }

        .sari-activity-event-meta span {
            display:inline-flex;
            min-width:0;
            align-items:center;
        }

        .sari-activity-event-meta span:not(:last-child)::after {
            content:"·";
            margin-left:5px;
            color:#a7afb7;
        }

        .sari-activity-view-wrap {
            display:flex;
            align-items:center;
            justify-content:flex-end;
        }

        .sari-activity-view-wrap form {
            margin:0;
        }

        .sari-activity-view {
            display:inline-flex;
            min-width:70px;
            height:36px;
            align-items:center;
            justify-content:center;
            border:0;
            border-radius:13px;
            background:#f1f2f4;
            padding:0 16px;
            color:#24292f;
            cursor:pointer;
            font:inherit;
            font-size:11px;
            font-weight:760;
            line-height:1;
            text-decoration:none;
            transition:background-color .14s ease,color .14s ease,transform .14s ease;
        }

        .sari-activity-view:hover,
        .sari-activity-view:focus-visible {
            background:#e7e9ec;
            color:#171b20;
            outline:none;
        }

        .sari-activity-view:active {
            transform:translateY(1px);
        }

        .sari-activity-menu-wrap {
            position:relative;
            display:flex;
            justify-content:flex-end;
        }

        .sari-activity-menu-toggle {
            display:grid;
            width:34px;
            height:36px;
            place-items:center;
            border:0;
            border-radius:10px;
            background:transparent;
            color:#69727c;
            cursor:pointer;
            transition:background-color .14s ease,color .14s ease;
        }

        .sari-activity-menu-toggle:hover,
        .sari-activity-menu-toggle:focus-visible,
        .sari-activity-menu-toggle[aria-expanded="true"] {
            background:rgba(31,41,55,.06);
            color:#24292f;
            outline:none;
        }

        .sari-activity-menu-toggle svg {
            width:18px;
            height:18px;
            fill:currentColor;
        }

        .sari-activity-menu {
            position:absolute;
            z-index:40;
            top:42px;
            right:0;
            width:218px;
            overflow:hidden;
            border:1px solid #e0e4e8;
            border-radius:18px;
            background:#fff;
            padding:8px;
            box-shadow:
                0 22px 48px rgba(31,41,55,.16),
                0 5px 14px rgba(31,41,55,.06);
        }

        .sari-activity-menu[hidden] {
            display:none;
        }

        .sari-activity-menu-item {
            display:flex;
            width:100%;
            min-height:44px;
            align-items:center;
            gap:11px;
            border:0;
            border-radius:11px;
            background:transparent;
            padding:0 12px;
            color:#2b3138;
            cursor:pointer;
            font:inherit;
            font-size:12px;
            font-weight:600;
            text-align:left;
            text-decoration:none;
            transition:background-color .12s ease,color .12s ease;
        }

        .sari-activity-menu-item:hover,
        .sari-activity-menu-item:focus-visible {
            background:#f4f5f7;
            color:#171b20;
            outline:none;
        }

        .sari-activity-menu-item svg {
            width:19px;
            height:19px;
            flex:0 0 auto;
            fill:none;
            stroke:currentColor;
            stroke-width:1.75;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-activity-menu form { margin:0; }
        .sari-activity-menu-item.is-danger { color:#e12535; }
        .sari-activity-menu-item.is-danger svg { stroke:#e12535; }
        .sari-activity-menu-item.is-danger:hover,
        .sari-activity-menu-item.is-danger:focus-visible {
            background:#fff0f1;
            color:#c91529;
        }
        .sari-activity-menu-item.is-danger:hover svg,
        .sari-activity-menu-item.is-danger:focus-visible svg { stroke:#c91529; }
        .sari-activity-menu-item:disabled { opacity:.55; cursor:wait; }
        /* Gold folder tabs, without an underline. */
        .sari-activity-actions {
            display:flex;
            align-items:center;
            justify-content:flex-start;
            flex-wrap:wrap;
            gap:12px 24px;
            margin:9px 2px 15px;
        }
        .sari-activity-filters {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:8px;
        }
        .sari-activity-filter {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:36px;
            border:1px solid transparent;
            border-radius:9px;
            background:transparent;
            padding:8px 13px;
            color:#7f8995;
            font-size:12px;
            font-weight:650;
            line-height:1.25;
            text-decoration:none;
            transition:background-color .16s ease,border-color .16s ease,color .16s ease;
        }
        .sari-activity-filter:hover,
        .sari-activity-filter:focus-visible {
            background:#faf5eb;
            color:#a96d07;
        }
        .sari-activity-filter[aria-current="page"] {
            border-color:#b67a10;
            background:#c88912;
            color:#ffffff;
            font-weight:750;
            box-shadow:0 4px 12px rgba(200,137,18,.15);
        }
        .sari-activity-filter[aria-current="page"]:hover,
        .sari-activity-filter[aria-current="page"]:focus-visible {
            border-color:#a66b0b;
            background:#b9780d;
            color:#ffffff;
        }
        .sari-activity-filter::after { content:none !important; display:none !important; }
        .sari-activity-filter:focus-visible,
        .sari-activity-read-all:focus-visible {
            outline:2px solid #c99128;
            outline-offset:4px;
        }
        .sari-activity-read-all-form { margin:0; }
        .sari-activity-read-all {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border:0;
            border-radius:7px;
            background:transparent;
            padding:9px 0;
            color:var(--activity-gold-dark);
            cursor:pointer;
            font:inherit;
            font-size:12px;
            font-weight:750;
            line-height:1.35;
            white-space:nowrap;
            transition:color .15s ease;
        }
        .sari-activity-read-all:not(:disabled):hover { color:#8e5b0a; }
        .sari-activity-read-all:disabled {
            color:var(--activity-gold-dark);
            opacity:.48;
            cursor:default;
        }
        .sari-activity-alert {
            margin:4px 0 16px; padding:11px 15px; border:1px solid #e8dcc4;
            border-radius:11px; background:#fffaf0; color:#94702b;
            font-size:12px; font-weight:550;
        }
        .sari-activity-empty-archive {
            display:inline-block; margin-top:16px; color:#a57421;
            font-size:12px; font-weight:650; text-decoration:none;
        }
        .sari-activity-menu-divider {
            height:1px;
            margin:7px 4px;
            background:#eceff2;
        }

        .sari-activity-time-mobile {
            display:none;
        }


        /* Page navigation sits below (and outside) the activity card. */
        .sari-activity-footer {
            display:grid;
            grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);
            align-items:center;
            gap:16px;
            width:100%;
            margin-top:22px;
            padding:4px 2px 0;
        }

        .sari-activity-range {
            grid-column:1;
            grid-row:1;
            justify-self:start;
            margin:0;
            color:#9aa3ae;
            font-size:12px;
            font-weight:450;
            line-height:1.5;
            white-space:nowrap;
        }

        .sari-activity-range strong {
            color:inherit;
            font-weight:500;
        }

        .sari-activity-pagination {
            grid-column:2;
            grid-row:1;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            min-width:0;
            max-width:100%;
        }

        .sari-activity-page-link,
        .sari-activity-page-arrow {
            display:inline-flex;
            min-width:40px;
            height:36px;
            flex:0 0 auto;
            align-items:center;
            justify-content:center;
            border:1px solid transparent;
            border-radius:12px;
            background:#f0f2f6;
            padding:0 10px;
            color:#929aa6;
            font-size:12px;
            font-weight:600;
            line-height:1;
            text-decoration:none;
            transition:background-color .15s ease,color .15s ease,box-shadow .15s ease;
        }

        .sari-activity-page-link:hover,
        .sari-activity-page-arrow:hover {
            background:#e7ebf1;
            color:#687381;
        }

        .sari-activity-page-link:focus-visible,
        .sari-activity-page-arrow:focus-visible {
            outline:2px solid #c99128;
            outline-offset:3px;
        }

        .sari-activity-page-link[aria-current="page"] {
            border-color:#c99128;
            background:#c99128;
            color:#fff;
            box-shadow:0 3px 12px rgba(201,145,40,.24);
            pointer-events:none;
        }

        .sari-activity-page-arrow svg {
            width:16px;
            height:16px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.9;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-activity-page-arrow.is-disabled {
            background:#f0f2f6;
            color:#b5bcc6;
            cursor:default;
        }

        .sari-activity-page-ellipsis {
            display:inline-flex;
            min-width:16px;
            height:36px;
            flex:0 0 auto;
            align-items:center;
            justify-content:center;
            color:#a7afba;
            font-size:13px;
            user-select:none;
        }

        @media (max-width:900px) {
            .sari-activity-footer {
                grid-template-columns:minmax(0,1fr);
                justify-items:center;
                gap:13px;
                margin-top:18px;
            }

            .sari-activity-pagination {
                grid-column:1;
                grid-row:1;
                max-width:100%;
                overflow-x:auto;
                justify-content:safe center;
                padding:3px 2px 8px;
                scrollbar-width:thin;
            }

            .sari-activity-range {
                grid-column:1;
                grid-row:2;
                width:100%;
                white-space:normal;
                text-align:left;
            }
        }

        @media (max-width:700px) {
            .sari-activity-toolbar { flex-wrap:wrap; padding-block:12px; }
            .sari-activity-actions { margin:8px 2px 12px; gap:8px 16px; }
            .sari-activity-filters { gap:7px; }
            .sari-activity-read-all { font-size:11px; }
            .sari-activity-pagination { gap:5px; }
            .sari-activity-page-link,
            .sari-activity-page-arrow {
                min-width:36px;
                height:35px;
                padding:0 8px;
                border-radius:10px;
            }
            .sari-activity-page-edge:not([aria-current="page"]),
            .sari-activity-page-ellipsis { display:none; }
        }

        @media (prefers-reduced-motion:reduce) {
            .sari-activity-page-link,
            .sari-activity-page-arrow { transition:none; }
        }

        /* Empty state kept compact and consistent with Buyer pages */
        .sari-activity-empty-state {
            display:grid;
            min-height:430px;
            margin-top:0;
            place-items:center;
            border:1px solid #e3e7eb;
            border-radius:18px;
            background:#fff;
            padding:72px 24px;
            text-align:center;
            box-shadow:
                0 10px 28px rgba(31,41,55,.045),
                0 2px 7px rgba(31,41,55,.018);
        }

        .sari-activity-empty-icon {
            display:grid;
            width:82px;
            height:82px;
            margin:0 auto;
            place-items:center;
            color:#b4bbc3;
        }

        .sari-activity-empty-icon svg {
            width:54px;
            height:54px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.45;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-activity-empty-title {
            margin:16px 0 0;
            color:#9099a3;
            font-size:21px;
            font-weight:750;
            line-height:1.2;
            letter-spacing:-.025em;
        }

        .sari-activity-empty-copy {
            max-width:520px;
            margin:7px auto 0;
            color:#a8b0b8;
            font-size:12px;
            font-weight:500;
            line-height:1.55;
        }

        .sari-activity-empty-button {
            display:inline-flex;
            min-width:170px;
            height:46px;
            align-items:center;
            justify-content:center;
            margin-top:22px;
            border:1px solid var(--activity-gold);
            border-radius:11px;
            background:var(--activity-gold);
            padding:0 24px;
            color:#fff;
            font-size:11px;
            font-weight:800;
            line-height:1;
            text-decoration:none;
            transition:background-color .14s ease,border-color .14s ease;
        }

        .sari-activity-empty-button:hover,
        .sari-activity-empty-button:focus-visible {
            border-color:var(--activity-gold-dark);
            background:var(--activity-gold-dark);
            color:#fff;
            outline:none;
        }

        @media (max-width:900px) {
            .sari-activity-row {
                grid-template-columns:48px minmax(0,1fr) auto 34px;
                gap:11px;
                padding-inline:13px;
            }

            .sari-activity-avatar {
                width:46px;
                height:46px;
                flex-basis:46px;
            }

            .sari-activity-event-message {
                max-width:54vw;
            }
        }

        @media (max-width:700px) {
            [data-sari-activity-page] {
                padding:16px 14px 30px;
            }

            .sari-activity-header-inner {
                align-items:flex-start;
                flex-direction:column;
            }

            .sari-activity-title {
                font-size:25px;
            }

            .sari-activity-notifications-link {
                top:-8px !important;
                font-size:10.5px !important;
            }

            .sari-activity-notifications-arrow {
                font-size:16px !important;
            }

            .sari-activity-shell {
                border-radius:16px;
            }

            .sari-activity-toolbar {
                min-height:50px;
                padding:11px 14px;
                border-radius:16px 16px 0 0;
            }
            .sari-activity-toolbar-right {
                gap:5px 14px;
            }

            .sari-activity-row {
                grid-template-columns:44px minmax(0,1fr) 30px;
                gap:10px;
                min-height:96px;
                padding:13px 12px;
            }

            .sari-activity-avatar {
                width:42px;
                height:42px;
                flex-basis:42px;
                border-radius:50%;
                padding:0;
            }

            .sari-activity-main {
                padding-right:0;
            }

            .sari-activity-event-title {
                font-size:13px;
            }

            .sari-activity-event-message {
                max-width:none;
                font-size:10.5px;
            }

            .sari-activity-event-meta {
                font-size:9.5px;
            }

            .sari-activity-view-wrap {
                display:none;
            }

            .sari-activity-menu-wrap {
                grid-column:3;
                grid-row:1;
            }

            .sari-activity-menu-toggle {
                width:30px;
            }

            .sari-activity-menu {
                right:-2px;
                width:202px;
            }

            .sari-activity-time-mobile {
                display:inline;
            }

            .sari-activity-empty-state {
                min-height:360px;
                padding:56px 20px;
            }
        }
    </style>

    <section class="sari-activity-header">
        <div class="sari-activity-header-inner">
            <div>
                <p class="sari-activity-eyebrow">Your Buyer Account</p>
                <h1 class="sari-activity-title">Recent <span>Activity</span></h1>
            </div>

            <a
                href="{{ route('buyer.notifications') }}"
                class="sari-activity-notifications-link"
                wire:navigate.hover
            >
                View all notifications <span class="sari-activity-notifications-arrow" aria-hidden="true">&gt;</span>
            </a>
        </div>
    </section>

    <div class="sari-activity-actions">
        <nav class="sari-activity-filters" aria-label="Recent activity folders">
            <a href="{{ route('buyer.recent-activity') }}"
               class="sari-activity-filter" @unless ($showArchived) aria-current="page" @endunless wire:navigate.hover>Recent</a>
            <a href="{{ route('buyer.recent-activity', ['filter' => 'archived']) }}"
               class="sari-activity-filter" @if ($showArchived) aria-current="page" @endif wire:navigate.hover>Archived</a>
        </nav>
    </div>

    @if ($activityItems->isEmpty())
        <section class="sari-activity-empty-state">
            <div>
                <span class="sari-activity-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 8v5l3 2"></path>
                        <path d="M3.05 11a9 9 0 1 0 .5-3"></path>
                        <path d="M3 4v4h4"></path>
                    </svg>
                </span>

                <h2 class="sari-activity-empty-title">{{ $showArchived ? 'No archived activity' : 'No recent activity yet' }}</h2>

                <p class="sari-activity-empty-copy">
                    {{ $showArchived ? 'Items you archive will appear here. You can restore them anytime.' : 'Your account actions, sign-ins, profile changes and shopping activity will appear here.' }}
                </p>

                <a
                    href="{{ route('buyer.products') }}"
                    class="sari-activity-empty-button"
                    wire:navigate.hover
                >
                    Browse Products
                </a>
                <div><a href="{{ route('buyer.recent-activity', $showArchived ? [] : ['filter' => 'archived']) }}" class="sari-activity-empty-archive" wire:navigate.hover>
                    {{ $showArchived ? 'Back to Recent Activity' : 'View archived activity' }}
                </a></div>
            </div>
        </section>
    @else
        <section class="sari-activity-shell">
            <header class="sari-activity-toolbar">
                <h2>{{ $showArchived ? 'Archived Activity' : 'Your Account Activity' }}</h2>

                <div class="sari-activity-toolbar-right">
                    <span class="sari-activity-count">
                        {{ number_format($isPaginated ? $recentActivity->total() : $activityItems->count()) }} {{ ($isPaginated ? $recentActivity->total() : $activityItems->count()) === 1 ? 'update' : 'updates' }}
                    </span>
                    @unless ($showArchived)
                        <form method="POST" action="{{ route('buyer.recent-activity.read-all') }}" class="sari-activity-read-all-form">
                            @csrf
                            <button type="submit" class="sari-activity-read-all"
                                @if (($unreadCount ?? 0) < 1) disabled aria-disabled="true" @endif>
                                Mark all as read
                            </button>
                        </form>
                    @endunless
                </div>
            </header>

            <div class="sari-activity-items">
                @foreach ($activityItems as $event)
                    @php
                        $isRead = (bool) ($event->is_read ?? false);
                        $destination = (string) ($event->destination_url ?? route('buyer.account'));
                    @endphp
                    <article class="sari-activity-row {{ $isRead ? '' : 'is-unread' }}" data-activity-row>
                        <span class="sari-activity-avatar" aria-label="{{ $buyerProfile['name'] }} profile photo" role="img">
                            @if ($buyerProfile['image'])
                                <img src="{{ $buyerProfile['image'] }}" alt="" loading="lazy" width="48" height="48">
                            @else
                                <span class="sari-activity-avatar-initials">{{ $buyerProfile['initials'] }}</span>
                            @endif
                        </span>
                        <div class="sari-activity-main">
                            <h2 class="sari-activity-event-title">{{ $event->display_title }}</h2>
                            <p class="sari-activity-event-message">{{ $event->display_message }}</p>
                            <div class="sari-activity-event-meta">
                                <span>{{ $buyerProfile['name'] }}</span>
                                <span>{{ $event->created_at?->diffForHumans() }}</span>
                                <span class="sari-activity-time-mobile">{{ $event->created_at?->format('M d, Y · h:i A') }}</span>
                            </div>
                        </div>
                        <div class="sari-activity-view-wrap">
                            <a class="sari-activity-view" href="{{ $destination }}" wire:navigate.hover>View</a>
                        </div>
                        <div class="sari-activity-menu-wrap">
                            <button type="button" class="sari-activity-menu-toggle" aria-label="More actions for {{ $event->display_title }}" aria-expanded="false" aria-controls="activity-menu-{{ $event->id }}" data-activity-menu-toggle>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="1.6"></circle><circle cx="12" cy="12" r="1.6"></circle><circle cx="12" cy="19" r="1.6"></circle></svg>
                            </button>
                            <div class="sari-activity-menu" id="activity-menu-{{ $event->id }}" data-activity-menu hidden>
                                @unless ($isRead)
                                    <form method="POST" action="{{ route('buyer.recent-activity.read', $event) }}">
                                        @csrf
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8.3 12.2 2.4 2.5 5.2-5.4"></path></svg>
                                            <span>Mark read</span>
                                        </button>
                                    </form>
                                @endunless
                                <a href="{{ $destination }}" class="sari-activity-menu-item" wire:navigate.hover>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"></path><circle cx="12" cy="12" r="2.4"></circle></svg>
                                    <span>View activity</span>
                                </a>
                                <div class="sari-activity-menu-divider"></div>
                                @if ($showArchived)
                                    <form method="POST" action="{{ route('buyer.recent-activity.restore', $event) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 4v5h5"></path></svg>
                                            <span>Restore</span>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('buyer.recent-activity.archive', $event) }}">
                                        @csrf
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="5" rx="1"></rect><path d="M5 9v11h14V9M10 13h4"></path></svg>
                                            <span>Archive</span>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('buyer.recent-activity.destroy', $event) }}" data-activity-delete-form>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sari-activity-menu-item is-danger">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M5 6l1 14h12l1-14M10 10v7M14 10v7"></path></svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

            @if ($isPaginated)
                <footer class="sari-activity-footer">
                    @if ($recentActivity->hasPages())
                        @php
                            $currentPage = $recentActivity->currentPage();
                            $lastPage = $recentActivity->lastPage();
                            $startPage = max(2, min($currentPage - 1, $lastPage - 3));
                            $endPage = min($lastPage - 1, max($currentPage + 1, 4));
                        @endphp

                        <nav class="sari-activity-pagination" aria-label="Recent activity pages">
                            @if ($recentActivity->onFirstPage())
                                <span class="sari-activity-page-arrow is-disabled" aria-disabled="true" aria-label="Previous page">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                                </span>
                            @else
                                <a href="{{ $recentActivity->previousPageUrl() }}" class="sari-activity-page-arrow" aria-label="Previous page" rel="prev" wire:navigate.hover>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                                </a>
                            @endif

                            <a href="{{ $recentActivity->url(1) }}"
                                class="sari-activity-page-link {{ $lastPage > 5 ? 'sari-activity-page-edge' : '' }}"
                                aria-label="Page 1"
                                @if ($currentPage === 1) aria-current="page" @endif
                                wire:navigate.hover>1</a>

                            @if ($startPage > 2)
                                <span class="sari-activity-page-ellipsis" aria-hidden="true">&hellip;</span>
                            @endif

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                <a href="{{ $recentActivity->url($page) }}"
                                    class="sari-activity-page-link"
                                    aria-label="Page {{ $page }}"
                                    @if ($page === $currentPage) aria-current="page" @endif
                                    wire:navigate.hover>{{ $page }}</a>
                            @endfor

                            @if ($endPage < $lastPage - 1)
                                <span class="sari-activity-page-ellipsis" aria-hidden="true">&hellip;</span>
                            @endif

                            <a href="{{ $recentActivity->url($lastPage) }}"
                                class="sari-activity-page-link {{ $lastPage > 5 ? 'sari-activity-page-edge' : '' }}"
                                aria-label="Page {{ $lastPage }}"
                                @if ($currentPage === $lastPage) aria-current="page" @endif
                                wire:navigate.hover>{{ $lastPage }}</a>

                            @if ($recentActivity->hasMorePages())
                                <a href="{{ $recentActivity->nextPageUrl() }}" class="sari-activity-page-arrow" aria-label="Next page" rel="next" wire:navigate.hover>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                                </a>
                            @else
                                <span class="sari-activity-page-arrow is-disabled" aria-disabled="true" aria-label="Next page">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                                </span>
                            @endif
                        </nav>
                    @endif
                    <p class="sari-activity-range" aria-live="polite">
                        Showing <strong>{{ number_format($recentActivity->firstItem()) }}–{{ number_format($recentActivity->lastItem()) }}</strong>
                        of <strong>{{ number_format($recentActivity->total()) }}</strong> {{ $recentActivity->total() === 1 ? 'update' : 'updates' }}
                    </p>
                </footer>
            @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
    if (window.__SARI_BUYER_ACCOUNT_ACTIVITY_MENU__) return;
    window.__SARI_BUYER_ACCOUNT_ACTIVITY_MENU__ = true;

    const closeMenus = (except = null) => {
        document.querySelectorAll('[data-sari-activity-page] [data-activity-menu]').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;
            menu.closest('.sari-activity-menu-wrap')?.querySelector('[data-activity-menu-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-sari-activity-page] [data-activity-menu-toggle]');
        if (toggle) {
            const menu = toggle.closest('.sari-activity-menu-wrap')?.querySelector('[data-activity-menu]');
            if (!menu) return;
            const open = menu.hidden;
            closeMenus(menu);
            menu.hidden = !open;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            return;
        }
        if (!event.target.closest('[data-sari-activity-page] .sari-activity-menu-wrap')) closeMenus();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenus();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-sari-activity-page] [data-activity-delete-form]');
        if (!form) return;
        if (!window.confirm('Remove this activity from your list? The audit record will be retained.')) {
            event.preventDefault();
            return;
        }
        const submit = form.querySelector('button[type="submit"]');
        if (submit) submit.disabled = true;
    });
})();
</script>
@endpush
