@extends('layouts.buyer')

@section('title', 'Notifications — SARI')
@section('page-title', 'Notifications')

@section('content')
@include('components.buyer.header')

@php
    /*
     * Keep this view compatible with both the old Collection-based controller
     * and the paginated controller. This prevents method-call 500s when only
     * one side of the UI patch has been deployed/cached.
     */
    $isPaginated = $notifications instanceof \Illuminate\Contracts\Pagination\Paginator;
    $notificationItems = $isPaginated
        ? collect($notifications->items())
        : collect($notifications);

    // Allow the page to render during rolling deploys when action routes are not yet registered.
    // Once the routes are installed, the existing secure POST/PATCH/DELETE forms are enabled.
    $notificationArchiveReady = \Illuminate\Support\Facades\Route::has('buyer.notifications.archive');
    $notificationRestoreReady = \Illuminate\Support\Facades\Route::has('buyer.notifications.restore');
    $notificationDeleteReady = \Illuminate\Support\Facades\Route::has('buyer.notifications.destroy');


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

        /* Match the Cart page's Continue Shopping text and arrow. */
        .sari-activity-notifications-link {
            position:relative;
            top:-12px;
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:0;
            border-radius:0;
            background:transparent;
            padding:0;
            color:#59636e;
            font-size:11.5px;
            font-weight:750;
            line-height:1.35;
            text-decoration:none;
            box-shadow:none;
            transition:color .14s ease;
        }

        .sari-activity-notifications-arrow {
            display:inline-block;
            margin-left:0;
            color:currentColor;
            font-size:18px;
            font-weight:800;
            line-height:.8;
            transform:translateY(-1px);
        }

        .sari-activity-notifications-link:hover,
        .sari-activity-notifications-link:focus-visible {
            color:#c88912;
            background:transparent;
            outline:none;
            text-decoration:none;
            box-shadow:none;
        }

        /* Seller Notifications reference: one elevated workspace, flat feed rows. */
        .sari-activity-shell {
            position:relative;
            overflow:visible;
            margin-top:0;
            border:1px solid #e5e7eb;
            border-radius:22px;
            background:#fff;
            box-shadow:
                0 3px 7px rgba(15,23,42,.055),
                0 14px 32px rgba(15,23,42,.095),
                0 30px 68px rgba(15,23,42,.11);
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

        .sari-activity-toolbar-right {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            flex-wrap:wrap;
            gap:9px 20px;
            min-width:0;
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

        /* Read and unread notifications share white, neutral row surfaces. */
        .sari-activity-row.is-unread { background:#fff; }

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
            background:#151515;
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
            transform:scale(1.45);
            transform-origin:center;
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
        /* Filters and bulk action are outside the white activity card. */
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
            gap:24px;
        }
        /* Recent / Archived are text links only; active text uses the SARI gold palette. */
        .sari-activity-filter {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:36px;
            border:0;
            border-radius:0;
            background:transparent;
            padding:6px 0;
            color:#7f8995;
            font-size:12px;
            font-weight:650;
            line-height:1.25;
            text-decoration:none;
            box-shadow:none;
            transition:color .16s ease;
        }
        .sari-activity-filter:hover,
        .sari-activity-filter:focus-visible {
            background:transparent;
            color:#c88912;
            text-decoration:none;
            box-shadow:none;
        }
        .sari-activity-filter[aria-current="page"],
        .sari-activity-filter[aria-current="page"]:hover,
        .sari-activity-filter[aria-current="page"]:focus-visible {
            border:0;
            background:transparent;
            color:#c88912;
            font-weight:750;
            box-shadow:none;
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
            .sari-activity-filters { gap:18px; }
            .sari-activity-read-all { font-size:11.5px; }
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

        /* The empty state floats like the main notification workspace. */
        .sari-activity-empty-state {
            display:grid;
            min-height:430px;
            margin-top:0;
            place-items:center;
            border:1px solid #e5e7eb;
            border-radius:18px;
            background:#fff;
            padding:72px 24px;
            text-align:center;
            box-shadow:
                0 3px 7px rgba(15,23,42,.055),
                0 14px 32px rgba(15,23,42,.095),
                0 30px 68px rgba(15,23,42,.11);
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

        @media (max-width:640px) {
            .sari-activity-notifications-link { font-size:10.5px; }
            .sari-activity-notifications-arrow { font-size:16px; }
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
                top:0;
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

        /* Match Seller Notifications' tighter, static elevation on smaller screens. */
        @media (max-width:760px) {
            .sari-activity-shell,
            .sari-activity-empty-state {
                box-shadow:
                    0 2px 4px rgba(15,23,42,.035),
                    0 8px 18px rgba(15,23,42,.06),
                    0 18px 38px rgba(15,23,42,.07);
            }
        }
    </style>

    <section class="sari-activity-header">
        <div class="sari-activity-header-inner">
            <div>
                <p class="sari-activity-eyebrow">Buyer updates</p>
                <h1 class="sari-activity-title">Your <span>Notifications</span></h1>
            </div>

            <a
                href="{{ route('buyer.recent-activity') }}"
                class="sari-activity-notifications-link"
                wire:navigate.hover
            >
                Recent activity <span class="sari-activity-notifications-arrow" aria-hidden="true">&gt;</span>
            </a>
        </div>
    </section>

    <div class="sari-activity-actions">
        <nav class="sari-activity-filters" aria-label="Notification folders">
            <a href="{{ route('buyer.notifications') }}"
               class="sari-activity-filter" @unless ($showArchived) aria-current="page" @endunless wire:navigate.hover>Recent</a>
            <a href="{{ route('buyer.notifications', ['filter' => 'archived']) }}"
               class="sari-activity-filter" @if ($showArchived) aria-current="page" @endif wire:navigate.hover>Archived</a>
        </nav>
    </div>

    @if ($notificationItems->isEmpty())
        <section class="sari-activity-empty-state">
            <div>
                <span class="sari-activity-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 8v5l3 2"></path>
                        <path d="M3.05 11a9 9 0 1 0 .5-3"></path>
                        <path d="M3 4v4h4"></path>
                    </svg>
                </span>

                <h2 class="sari-activity-empty-title">{{ $showArchived ? 'No archived notifications' : 'No notifications yet' }}</h2>

                <p class="sari-activity-empty-copy">
                    {{ $showArchived ? 'Archived notifications will appear here. You can restore them anytime.' : 'Order and shipping updates will appear here as your purchases move through fulfillment and delivery.' }}
                </p>

                <a
                    href="{{ route('buyer.products') }}"
                    class="sari-activity-empty-button"
                    wire:navigate.hover
                >
                    Browse Products
                </a>
                <div><a href="{{ route('buyer.notifications', $showArchived ? [] : ['filter' => 'archived']) }}" class="sari-activity-empty-archive" wire:navigate.hover>
                    {{ $showArchived ? 'Back to Recent' : 'View archived notifications' }}
                </a></div>
            </div>
        </section>
    @else
        <section class="sari-activity-shell">
            <header class="sari-activity-toolbar">
                <h2>{{ $showArchived ? 'Archived Notifications' : 'Notifications' }}</h2>

                <div class="sari-activity-toolbar-right">
                    <span class="sari-activity-count">
                        {{ number_format($isPaginated ? $notifications->total() : $notificationItems->count()) }} {{ ($isPaginated ? $notifications->total() : $notificationItems->count()) === 1 ? 'notification' : 'notifications' }}
                    </span>
                    @unless ($showArchived)
                        <form method="POST" action="{{ route('buyer.notifications.read-all') }}" class="sari-activity-read-all-form">
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
                @foreach ($notificationItems as $event)
                    @php
                        $order = $event->order;
                        $seller = $event->seller;
                        $storeName = trim((string) ($seller?->store_name ?? 'SARI Seller')) ?: 'SARI Seller';
                        $isRead = (bool) ($event->is_read ?? false);
                    @endphp

                    <article
                        class="sari-activity-row {{ $isRead ? '' : 'is-unread' }}"
                        data-activity-row
                        data-event-id="{{ $event->id }}"
                        @if ($order)
                            data-order-url="{{ route('buyer.orders.show', $order) }}"
                        @endif
                        @unless ($isRead)
                            data-mark-read-url="{{ route('buyer.notifications.read', $event) }}"
                        @endunless
                        tabindex="{{ $order ? '0' : '-1' }}"
                        @if ($order)
                            role="link"
                            aria-label="View notification: {{ $event->title }}"
                        @endif
                    >

                        <span class="sari-activity-avatar" aria-hidden="true">
                            <img src="{{ asset('images/sari-main-logo.png') }}" alt="" loading="lazy" width="46" height="46">
                        </span>

                        <div class="sari-activity-main">
                            <h2 class="sari-activity-event-title">
                                {{ $event->title }}
                            </h2>

                            <p class="sari-activity-event-message">
                                {{ $event->message }}
                            </p>

                            <div class="sari-activity-event-meta">
                                @if ($order)
                                    <span>{{ $order->order_number }}</span>
                                @endif

                                <span>{{ $storeName }}</span>

                                <span>
                                    {{ optional($event->created_at)->diffForHumans() }}
                                </span>

                                <span class="sari-activity-time-mobile">
                                    {{ optional($event->created_at)->format('M d, Y · h:i A') }}
                                </span>
                            </div>
                        </div>

                        <div class="sari-activity-view-wrap">
                            @if ($order)
                                @if ($isRead)
                                    <a
                                        href="{{ route('buyer.orders.show', $order) }}"
                                        class="sari-activity-view"
                                        wire:navigate.hover
                                    >
                                        View
                                    </a>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('buyer.notifications.read', $event) }}"
                                    >
                                        @csrf
                                        <button type="submit" class="sari-activity-view">
                                            View
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        <div class="sari-activity-menu-wrap">
                            <button
                                type="button"
                                class="sari-activity-menu-toggle"
                                aria-label="More actions"
                                aria-expanded="false"
                                data-activity-menu-toggle
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="5" r="1.6"></circle>
                                    <circle cx="12" cy="12" r="1.6"></circle>
                                    <circle cx="12" cy="19" r="1.6"></circle>
                                </svg>
                            </button>

                            <div class="sari-activity-menu" data-activity-menu hidden>
                                @unless ($isRead)
                                    <button
                                        type="button"
                                        class="sari-activity-menu-item"
                                        data-mark-read
                                        data-mark-read-url="{{ route('buyer.notifications.read', $event) }}"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="m8.3 12.2 2.4 2.5 5.2-5.4"></path>
                                        </svg>
                                        <span>Mark read</span>
                                    </button>

                                    <div class="sari-activity-menu-divider"></div>
                                @endunless

                                @if ($order)
                                    <a
                                        href="{{ route('buyer.orders.show', $order) }}"
                                        class="sari-activity-menu-item"
                                        wire:navigate.hover
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"></path>
                                            <circle cx="12" cy="12" r="2.4"></circle>
                                        </svg>
                                        <span>View order</span>
                                    </a>
                                @endif

                                <div class="sari-activity-menu-divider"></div>

                                @if ($showArchived)
                                    @if ($notificationRestoreReady)
                                    <form method="POST" action="{{ route('buyer.notifications.restore', $event) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 4v5h5"></path><path d="M12 7v5l3 2"></path></svg>
                                            <span>Restore</span>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" class="sari-activity-menu-item" disabled title="Restore is temporarily unavailable while notification routes are being deployed">Restore (unavailable)</button>
                                    @endif
                                @else
                                    @if ($notificationArchiveReady)
                                    <form method="POST" action="{{ route('buyer.notifications.archive', $event) }}">
                                        @csrf
                                        <button type="submit" class="sari-activity-menu-item">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="5" rx="1"></rect><path d="M5 9v11h14V9M10 13h4"></path></svg>
                                            <span>Archive</span>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" class="sari-activity-menu-item" disabled title="Archive is temporarily unavailable while notification routes are being deployed">Archive (unavailable)</button>
                                    @endif
                                @endif

                                @if ($notificationDeleteReady)
                                <form method="POST" action="{{ route('buyer.notifications.destroy', $event) }}"
                                      data-activity-delete-form>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sari-activity-menu-item is-danger">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M5 6l1 14h12l1-14M10 10v7M14 10v7"></path></svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="sari-activity-menu-item is-danger" disabled title="Delete is temporarily unavailable while notification routes are being deployed">Delete (unavailable)</button>
                                @endif

                                <div class="sari-activity-menu-divider"></div>

                                <a
                                    href="{{ route('buyer.recent-activity') }}"
                                    class="sari-activity-menu-item"
                                    wire:navigate.hover
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                        <path d="M10 21h4"></path>
                                    </svg>
                                    <span>Recent activity</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

            @if ($isPaginated)
                <footer class="sari-activity-footer">
                    @if ($notifications->hasPages())
                        @php
                            $currentPage = $notifications->currentPage();
                            $lastPage = $notifications->lastPage();
                            $startPage = max(2, min($currentPage - 1, $lastPage - 3));
                            $endPage = min($lastPage - 1, max($currentPage + 1, 4));
                        @endphp

                        <nav class="sari-activity-pagination" aria-label="Notification pages">
                            @if ($notifications->onFirstPage())
                                <span class="sari-activity-page-arrow is-disabled" aria-disabled="true" aria-label="Previous page">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                                </span>
                            @else
                                <a href="{{ $notifications->previousPageUrl() }}" class="sari-activity-page-arrow" aria-label="Previous page" rel="prev" wire:navigate.hover>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                                </a>
                            @endif

                            <a href="{{ $notifications->url(1) }}"
                                class="sari-activity-page-link {{ $lastPage > 5 ? 'sari-activity-page-edge' : '' }}"
                                aria-label="Page 1"
                                @if ($currentPage === 1) aria-current="page" @endif
                                wire:navigate.hover>1</a>

                            @if ($startPage > 2)
                                <span class="sari-activity-page-ellipsis" aria-hidden="true">&hellip;</span>
                            @endif

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                <a href="{{ $notifications->url($page) }}"
                                    class="sari-activity-page-link"
                                    aria-label="Page {{ $page }}"
                                    @if ($page === $currentPage) aria-current="page" @endif
                                    wire:navigate.hover>{{ $page }}</a>
                            @endfor

                            @if ($endPage < $lastPage - 1)
                                <span class="sari-activity-page-ellipsis" aria-hidden="true">&hellip;</span>
                            @endif

                            <a href="{{ $notifications->url($lastPage) }}"
                                class="sari-activity-page-link {{ $lastPage > 5 ? 'sari-activity-page-edge' : '' }}"
                                aria-label="Page {{ $lastPage }}"
                                @if ($currentPage === $lastPage) aria-current="page" @endif
                                wire:navigate.hover>{{ $lastPage }}</a>

                            @if ($notifications->hasMorePages())
                                <a href="{{ $notifications->nextPageUrl() }}" class="sari-activity-page-arrow" aria-label="Next page" rel="next" wire:navigate.hover>
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
                        Showing <strong>{{ number_format($notifications->firstItem()) }}–{{ number_format($notifications->lastItem()) }}</strong>
                        of <strong>{{ number_format($notifications->total()) }}</strong> {{ $notifications->total() === 1 ? 'notification' : 'notifications' }}
                    </p>
                </footer>
            @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    (() => {
        if (window.__SARI_RECENT_ACTIVITY_MENU_V1__) {
            return;
        }

        window.__SARI_RECENT_ACTIVITY_MENU_V1__ = true;

        // The shared Buyer shell polls unread-count changes on every page.
        // Refresh the Notifications list itself when a new unread event arrives,
        // without a manual browser reload. Never interrupt a focused control.
        let lastNotificationListRefresh = 0;
        document.addEventListener('sari:buyer-notification-count-changed', (event) => {
            if (!(Number(event.detail?.count) > Number(event.detail?.previous))) return;
            if (document.visibilityState === 'hidden') return;
            if (!document.querySelector('[data-sari-activity-page]')) return;
            if (new URLSearchParams(window.location.search).get('filter') === 'archived') return;
            if (document.activeElement?.closest?.('[data-sari-activity-page]')) return;
            if (Date.now() - lastNotificationListRefresh < 12000) return;
            if (typeof window.Livewire?.navigate !== 'function') return;
            lastNotificationListRefresh = Date.now();
            window.Livewire.navigate(window.location.href);
        });

        const closeMenus = (except = null) => {
            document
                .querySelectorAll('[data-activity-menu]')
                .forEach((menu) => {
                    if (menu === except) {
                        return;
                    }

                    menu.hidden = true;

                    const toggle = menu
                        .closest('.sari-activity-menu-wrap')
                        ?.querySelector('[data-activity-menu-toggle]');

                    toggle?.setAttribute('aria-expanded', 'false');
                });
        };

        const openActivityRow = async (row) => {
            const orderUrl = row?.dataset.orderUrl;

            if (!orderUrl) {
                return;
            }

            const markReadUrl = row.dataset.markReadUrl;
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (markReadUrl && token) {
                try {
                    await fetch(markReadUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                    });
                } catch (error) {
                    console.error(error);
                }
            }

            window.location.href = orderUrl;
        };

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('[data-activity-delete-form]');
            if (!form) return;
            if (!window.confirm('Delete this notification from your history? This cannot be undone. Your order will not be deleted.')) {
                event.preventDefault();
                return;
            }
            const submit = form.querySelector('button[type="submit"]');
            if (submit) submit.disabled = true;
        });

        document.addEventListener('click', async (event) => {
            const toggle = event.target.closest('[data-activity-menu-toggle]');

            if (toggle) {
                const wrap = toggle.closest('.sari-activity-menu-wrap');
                const menu = wrap?.querySelector('[data-activity-menu]');

                if (!menu) {
                    return;
                }

                const willOpen = menu.hidden;

                closeMenus(menu);
                menu.hidden = !willOpen;
                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                return;
            }

            const markRead = event.target.closest('[data-mark-read]');

            if (markRead) {
                event.preventDefault();

                const url = markRead.dataset.markReadUrl;
                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content');

                if (!url || !token || markRead.disabled) {
                    return;
                }

                markRead.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Unable to mark activity as read.');
                    }

                    const row = markRead.closest('[data-activity-row]');

                    row?.classList.remove('is-unread');

                    const divider = markRead.nextElementSibling;
                    if (divider?.classList.contains('sari-activity-menu-divider')) {
                        divider.remove();
                    }

                    markRead.remove();
                    closeMenus();
                    document.dispatchEvent(new CustomEvent('sari:notification-updated'));
                    window.SariBuyerToast?.success('Notification marked as read.');
                } catch (error) {
                    markRead.disabled = false;
                    window.SariBuyerToast?.error(error?.message || 'Unable to mark notification as read.');
                    console.error(error);
                }

                return;
            }

            const clickedInteractive = event.target.closest(
                'a, button, form, input, select, textarea, [data-activity-menu]'
            );

            if (!clickedInteractive) {
                const row = event.target.closest('[data-activity-row]');

                if (row?.dataset.orderUrl) {
                    await openActivityRow(row);
                    return;
                }
            }

            if (!event.target.closest('.sari-activity-menu-wrap')) {
                closeMenus();
            }
        });

        document.addEventListener('keydown', async (event) => {
            if (event.key === 'Escape') {
                closeMenus();
                return;
            }

            if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('[data-activity-row]')) {
                event.preventDefault();
                await openActivityRow(event.target);
            }
        });
    })();
</script>
@endpush
