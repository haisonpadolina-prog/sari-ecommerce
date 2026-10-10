@extends('layouts.buyer')

@section('title', 'My Orders — SARI')

@section('page-title', 'My Orders')

@section('content')

@include('components.buyer.header')

@php

    $statusSteps = [

        ['key' => 'new', 'label' => 'Order Placed', 'icon' => 'shopping_bag'],

        ['key' => 'preparing', 'label' => 'Preparing', 'icon' => 'inventory_2'],

        ['key' => 'courier_accepted', 'label' => 'Courier Assigned', 'icon' => 'local_shipping'],

        ['key' => 'in_transit', 'label' => 'Out for Delivery', 'icon' => 'two_wheeler'],

        ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'home'],

    ];

    $statusIndex = function ($status) {

        return match ($status) {

            'new' => 0,

            'preparing', 'ready_for_pickup' => 1,

            'courier_accepted', 'heading_pickup', 'arrived_pickup' => 2,

            'in_transit', 'arrived_buyer' => 3,

            'delivered' => 4,

            default => 0,

        };

    };

    $trackedOrderId = (int) request()->query('track', 0);

    $featuredOrder = method_exists($orders, 'first')
        ? ($trackedOrderId > 0
            ? ($orders->first(fn ($order) => (int) $order->getKey() === $trackedOrderId) ?: $orders->first())
            : $orders->first())
        : null;

    $featuredStep = $featuredOrder ? $statusIndex($featuredOrder->status) : 0;

    $toShipCount = $orders->filter(fn($o) => in_array($o->status, ['new', 'preparing', 'ready_for_pickup']))->count();

    $inTransitCount = $orders->filter(fn($o) => in_array($o->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']))->count();

    $deliveredCount = $orders->where('status', 'delivered')->count();

@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">

<style>

    .sari-orders-page {

        --sari-gold: #d99a18;

        --sari-gold-dark: #b97905;

        --sari-gold-soft: #fff6df;

        --sari-ink: #1b1b1b;

        --sari-muted: #77736b;

        --sari-line: #eee8dc;

        --sari-surface: #ffffff;

        --sari-page: #ffffff;

    }

    .sari-orders-page {

        background: var(--sari-page);

        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

    }
.sari-card {

        background: var(--sari-surface);

        border: 1px solid #ece7df;

        box-shadow:

            0 16px 36px rgba(44, 39, 33, .060),

            0 4px 12px rgba(44, 39, 33, .030),

            inset 0 1px 0 rgba(255, 255, 255, .96);

    }

    .sari-soft-panel {

        box-shadow:

            0 14px 32px rgba(44, 39, 33, .055),

            0 3px 10px rgba(44, 39, 33, .028),

            inset 0 1px 0 rgba(255, 255, 255, .96);

    }

    .sari-soft-panel-sm {

        box-shadow:

            0 10px 24px rgba(44, 39, 33, .050),

            0 2px 8px rgba(44, 39, 33, .025),

            inset 0 1px 0 rgba(255, 255, 255, .95);

    }

    .sari-order-card {

        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;

    }

    @media (hover: hover) and (pointer: fine) {

        .sari-order-card:hover {

            transform: translateY(-2px);

            box-shadow:

                0 19px 42px rgba(44, 39, 33, .072),

                0 5px 14px rgba(44, 39, 33, .035),

                inset 0 1px 0 rgba(255, 255, 255, .98);

            border-color: #eadfcf;

        }

        .sari-soft-panel,

        .sari-soft-panel-sm,

        .sari-sidebar-card,

        .sari-summary-strip {

            transition: box-shadow .22s ease, border-color .22s ease;

        }

        .sari-sidebar-card:hover,

        .sari-soft-panel:hover {

            box-shadow:

                0 16px 36px rgba(44, 39, 33, .065),

                0 4px 12px rgba(44, 39, 33, .032),

                inset 0 1px 0 rgba(255, 255, 255, .98);

        }

    }

    .sari-gold-btn {

        background: linear-gradient(135deg, #e5ad32 0%, #cf8e0d 100%);

        box-shadow: 0 7px 18px rgba(201, 137, 0, .18);

    }

    .sari-gold-btn:hover {

        background: linear-gradient(135deg, #dca129 0%, #bb7905 100%);

        box-shadow: 0 9px 22px rgba(201, 137, 0, .24);

    }

    .sari-step-line {

        position: absolute;

        top: 19px;

        height: 2px;

        background: #e9e2d6;

        left: 10%;

        right: 10%;

    }

    .sari-step-progress {

        position: absolute;

        top: 19px;

        height: 2px;

        left: 10%;

        background: linear-gradient(90deg, #e4ab2c, #c98605);

        transition: width .45s ease;

    }

    .sari-step {

        position: relative;

        z-index: 2;

        width: 92px;

        flex: 0 0 92px;

    }

    .sari-step-dot {

        width: 40px;

        height: 40px;

        margin-inline: auto;

        border-radius: 9999px;

        display: grid;

        place-items: center;

        border: 2px solid #ddd6ca;

        background: #fff;

        color: #aaa49a;

        transition: .2s ease;

    }

    .sari-step-dot.is-complete {

        border-color: var(--sari-gold);

        background: var(--sari-gold);

        color: #fff;

        box-shadow: 0 0 0 6px rgba(217, 154, 24, .10);

    }

    .sari-step-dot.is-current {

        box-shadow: 0 0 0 6px rgba(217, 154, 24, .12), 0 7px 15px rgba(217, 154, 24, .16);

    }

    .sari-mini-divider {

        width: 1px;

        height: 42px;

        background: var(--sari-line);

    }

    .sari-sidebar {

        position: sticky;

        top: 20px;

    }

    .sari-sidebar-card {

        background: #fff;

        border: 1px solid #ece7df;

        box-shadow:

            0 13px 30px rgba(44, 39, 33, .052),

            0 3px 9px rgba(44, 39, 33, .026),

            inset 0 1px 0 rgba(255, 255, 255, .96);

    }

    .sari-tracking-row {

        position: relative;

        display: flex;

        gap: 12px;

    }

    .sari-tracking-row:not(:last-child)::after {

        content: "";

        position: absolute;

        left: 8px;

        top: 22px;

        width: 1px;

        height: 34px;

        background: #e8e1d5;

    }

    .sari-tracking-row.is-complete:not(:last-child)::after {

        background: #dfaa2d;

    }

    .sari-tracking-icon {

        width: 17px;

        height: 17px;

        flex: 0 0 17px;

        margin-top: 2px;

        border-radius: 9999px;

        border: 1.5px solid #d6d0c5;

        background: #fff;

        color: transparent;

        display: grid;

        place-items: center;

    }

    .sari-tracking-row.is-complete .sari-tracking-icon,

    .sari-tracking-row.is-current .sari-tracking-icon {

        border-color: var(--sari-gold);

        background: var(--sari-gold);

        color: #fff;

    }

    .sari-tracking-row.is-current .sari-tracking-icon {

        box-shadow: 0 0 0 4px rgba(217, 154, 24, .10);

    }

    .sari-fade-in {

        animation: sariFadeIn .35s ease both;

    }

    @keyframes sariFadeIn {

        from { opacity: 0; transform: translateY(5px); }

        to { opacity: 1; transform: translateY(0); }

    }

    .sari-summary-strip {

        background: #fff;

        border: 1px solid #ece7df;

        box-shadow:

            0 13px 30px rgba(44, 39, 33, .052),

            0 3px 9px rgba(44, 39, 33, .026),

            inset 0 1px 0 rgba(255, 255, 255, .96);

    }

    .sari-summary-item {

        position: relative;

        min-width: 115px;

    }

    .sari-summary-item + .sari-summary-item::before {

        content: "";

        position: absolute;

        left: 0;

        top: 50%;

        width: 1px;

        height: 28px;

        transform: translateY(-50%);

        background: #eee8dc;

    }

    .sari-search {

        outline: none;

        transition: .2s ease;

    }

    .sari-search:focus {

        border-color: #d9a33a;

        box-shadow: 0 0 0 4px rgba(217,154,24,.09);

    }

    .sari-filter.is-active {

        background: #e2a52b;

        color: #fff;

        box-shadow: 0 4px 12px rgba(201,137,0,.16);

    }

    .sari-product-strip {

        display: flex;

        align-items: center;

        gap: 9px;

    }

    .sari-product-thumb {

        width: 45px;

        height: 45px;

        flex: 0 0 45px;

        display: grid;

        place-items: center;

        border: 1px solid #eee8dc;

        border-radius: 12px;

        background: linear-gradient(145deg, #fffdf9, #f5f0e7);

        color: #9b958b;

        overflow: hidden;

    }

    .sari-product-thumb:nth-child(2) {

        background: linear-gradient(145deg, #fafafa, #ebe7df);

    }

    .sari-product-more {

        height: 45px;

        min-width: 42px;

        padding: 0 8px;

        display: grid;

        place-items: center;

        border: 1px dashed #d9d1c4;

        border-radius: 12px;

        background: #fffdf9;

        color: #8a8479;

        font-size: 10px;

        font-weight: 700;

    }

    .sari-price-box {

        border-left: 1px solid #eee8dc;

        padding-left: 22px;

        text-align: right;

    }

    .sari-live-dot {

        width: 7px;

        height: 7px;

        border-radius: 999px;

        background: #42a66b;

        box-shadow: 0 0 0 5px rgba(66,166,107,.10);

        animation: sariPulse 1.8s ease-in-out infinite;

    }

    @keyframes sariPulse {

        0%, 100% { box-shadow: 0 0 0 4px rgba(66,166,107,.08); }

        50% { box-shadow: 0 0 0 7px rgba(66,166,107,.16); }

    }

    .sari-mini-map {

        position: relative;

        height: 158px;

        overflow: hidden;

        border: 1px solid #e9e3d9;

        border-radius: 16px;

        box-shadow:

            0 8px 20px rgba(44, 39, 33, .045),

            inset 0 1px 0 rgba(255, 255, 255, .90);

        background:

            linear-gradient(28deg, transparent 48%, rgba(222,214,199,.75) 49%, rgba(222,214,199,.75) 51%, transparent 52%),

            linear-gradient(112deg, transparent 48%, rgba(232,225,213,.9) 49%, rgba(232,225,213,.9) 51%, transparent 52%),

            linear-gradient(#fffdf8, #f7f3ea);

    }

    .sari-mini-map::before,

    .sari-mini-map::after {

        content: "";

        position: absolute;

        border-radius: 999px;

        border: 1px solid rgba(217,154,24,.15);

    }

    .sari-mini-map::before {

        width: 180px;

        height: 180px;

        left: 18%;

        top: -75px;

    }

    .sari-mini-map::after {

        width: 110px;

        height: 110px;

        right: -20px;

        bottom: -55px;

    }

    .sari-route {

        position: absolute;

        left: 23%;

        top: 53%;

        width: 54%;

        height: 2px;

        transform: rotate(-22deg);

        transform-origin: left center;

        background: repeating-linear-gradient(90deg, #dca42a 0 7px, transparent 7px 12px);

    }

    .sari-map-pin {

        position: absolute;

        z-index: 2;

        display: grid;

        place-items: center;

        width: 30px;

        height: 30px;

        border-radius: 999px;

        box-shadow: 0 7px 15px rgba(45,35,15,.14);

    }

    .sari-map-pin.courier {

        left: 48%;

        top: 40%;

        background: #191919;

        color: #e5ad32;

    }

    .sari-map-pin.home {

        right: 13%;

        bottom: 17%;

        background: #e1a52a;

        color: #fff;

    }

    .sari-map-label {

        position: absolute;

        z-index: 3;

        padding: 5px 7px;

        border: 1px solid #eee8dc;

        border-radius: 7px;

        background: rgba(255,255,255,.94);

        font-size: 8px;

        font-weight: 700;

        color: #6f6a61;

        box-shadow: 0 4px 10px rgba(45,35,15,.05);

    }

    .sari-map-label.courier {

        left: 41%;

        top: 15%;

    }

    .sari-map-label.home {

        right: 4%;

        bottom: 2%;

    }

    .sari-live-panel {

        border: 1px solid #ece6dc;

        border-radius: 16px;

        background: #fff;

        box-shadow:

            0 9px 22px rgba(44, 39, 33, .045),

            0 2px 7px rgba(44, 39, 33, .022),

            inset 0 1px 0 rgba(255, 255, 255, .96);

    }

    .sari-order-card.is-hidden {

        display: none;

    }

    .sari-empty-search {

        display: none;

    }

    .sari-delivered-box {

        border: 1px solid #e5eadf;

        background: #f8fbf5;

    }

    .sari-star-row {

        letter-spacing: 2px;

        color: #d6d0c6;

    }

    .sari-star-row span {

        cursor: pointer;

        transition: .15s ease;

    }

    .sari-star-row span:hover {

        color: #d99a18;

        transform: translateY(-1px);

    }

    @media (max-width: 900px) {

        .sari-sidebar {

            position: static;

        }

    }

    @media (max-width: 640px) {

        .sari-step {

            width: 64px;

            flex-basis: 64px;

        }

        .sari-step-line,

        .sari-step-progress {

            left: 8%;

            right: 8%;

        }

        .sari-step-label {

            font-size: 10px;

        }

    }


    /* ============================================================
       SARI MY ORDERS — COMPACT REFERENCE DESIGN
       Clean / modern / formal / professional.
    ============================================================ */

    .sari-orders-reference {
        --sari-gold:#c88912;
        --sari-gold-dark:#a96f0e;
        --sari-text:#303740;
        --sari-muted:#7d8691;
        --sari-border:#e1e6ea;
        --sari-soft:#edf0f3;
        --sari-page:#f5f6f8;
        background:var(--sari-page) !important;
        color:var(--sari-text);
    }

    .sari-orders-shell {
        max-width:1400px !important;
        padding-top:20px !important;
        padding-bottom:38px !important;
    }

    /* PAGE HEADER */
    .sari-orders-shell > .mb-5.flex:first-of-type {
        margin-bottom:14px !important;
        padding:2px 2px 0 !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child {
        margin-bottom:4px !important;
        gap:0 !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child > span:first-child {
        display:none !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child > span:last-child {
        color:#a87316 !important;
        font-size:6.8px !important;
        font-weight:800 !important;
        letter-spacing:.15em !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type h1 {
        color:#222b35 !important;
        font-size:clamp(29px,2.4vw,34px) !important;
        font-weight:800 !important;
        line-height:1.03 !important;
        letter-spacing:-.04em !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type h1 + p {
        margin-top:6px !important;
        color:#7f8994 !important;
        font-size:8.4px !important;
        line-height:1.6 !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a {
        height:39px !important;
        border:1px solid var(--sari-gold) !important;
        border-radius:9px !important;
        background:var(--sari-gold) !important;
        padding-inline:14px !important;
        color:#fff !important;
        font-size:8px !important;
        font-weight:800 !important;
        box-shadow:none !important;
        transform:none !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a:hover {
        border-color:var(--sari-gold-dark) !important;
        background:var(--sari-gold-dark) !important;
        transform:none !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a .material-symbols-rounded {
        font-size:16px !important;
    }

    /* SUMMARY */
    .sari-summary-strip {
        margin-bottom:14px !important;
        border:1px solid var(--sari-border) !important;
        border-radius:14px !important;
        background:#fff !important;
        box-shadow:
            0 12px 30px rgba(31,41,55,.048),
            0 2px 7px rgba(31,41,55,.018) !important;
    }

    .sari-summary-item {
        min-width:0 !important;
        gap:10px !important;
        padding:11px 14px !important;
    }

    .sari-summary-item + .sari-summary-item::before {
        height:32px !important;
        background:#edf0f3 !important;
    }

    .sari-summary-item > div:first-child {
        width:31px !important;
        height:31px !important;
        border:1px solid #eee1c8 !important;
        border-radius:9px !important;
        background:#fffaf1 !important;
        color:#b8780d !important;
    }

    .sari-summary-item:last-child > div:first-child {
        border-color:#dce8e0 !important;
        background:#f5faf7 !important;
        color:#4f7d61 !important;
    }

    .sari-summary-item .material-symbols-rounded { font-size:16px !important; }

    .sari-summary-item p:first-child {
        color:#8b949e !important;
        font-size:6.5px !important;
        font-weight:800 !important;
        letter-spacing:.09em !important;
    }

    .sari-summary-item p:last-child {
        margin-top:1px !important;
        color:#303740 !important;
        font-size:14px !important;
        font-weight:800 !important;
    }

    /* MAIN GRID */
    .sari-orders-shell > .grid.items-start {
        gap:15px !important;
        grid-template-columns:minmax(0,1fr) 300px !important;
    }

    /* SEARCH & FILTERS */
    main > .sari-card.mb-5 {
        margin-bottom:13px !important;
        border:1px solid var(--sari-border) !important;
        border-radius:13px !important;
        background:#fff !important;
        padding:9px 10px !important;
        box-shadow:
            0 9px 22px rgba(31,41,55,.038),
            0 2px 6px rgba(31,41,55,.015) !important;
    }

    #orderSearch {
        height:38px !important;
        border:1px solid #dfe4e8 !important;
        border-radius:9px !important;
        background:#fafbfc !important;
        padding-left:36px !important;
        color:#37404a !important;
        font-size:7.8px !important;
        font-weight:600 !important;
    }

    #orderSearch::placeholder { color:#929ba5 !important; }

    #orderSearch:focus {
        border-color:#c99a48 !important;
        background:#fff !important;
        box-shadow:0 0 0 3px rgba(200,137,18,.08) !important;
    }

    #orderFilters { gap:5px !important; }

    .sari-filter {
        min-height:32px !important;
        border:1px solid transparent !important;
        border-radius:8px !important;
        padding:0 10px !important;
        color:#707a84 !important;
        font-size:7px !important;
        font-weight:700 !important;
        box-shadow:none !important;
    }

    .sari-filter:hover {
        background:#f5f6f8 !important;
        color:#363e47 !important;
    }

    .sari-filter.is-active {
        border-color:#353a40 !important;
        background:#353a40 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    /* ORDER LIST */
    main > section.space-y-5 {
        display:grid !important;
        gap:12px !important;
    }

    .sari-order-card {
        border:1px solid var(--sari-border) !important;
        border-radius:14px !important;
        background:#fff !important;
        box-shadow:
            0 10px 26px rgba(31,41,55,.045),
            0 2px 7px rgba(31,41,55,.016) !important;
        transform:none !important;
    }

    .sari-order-card:hover {
        border-color:#d7dde2 !important;
        box-shadow:
            0 12px 28px rgba(31,41,55,.055),
            0 3px 8px rgba(31,41,55,.02) !important;
        transform:none !important;
    }

    .sari-order-card > div:first-child {
        border-color:var(--sari-soft) !important;
        padding:14px 16px !important;
    }

    .sari-order-card > div:first-child > div:first-child { gap:14px !important; }

    .sari-order-card > div:first-child .h-14.w-14 {
        width:40px !important;
        height:40px !important;
        border:1px solid #eee1c8 !important;
        border-radius:10px !important;
        background:#fffaf1 !important;
        color:#b8780d !important;
    }

    .sari-order-card > div:first-child .h-14.w-14 .material-symbols-rounded {
        font-size:20px !important;
    }

    .sari-order-card h2 {
        color:#2d353e !important;
        font-size:12px !important;
        font-weight:800 !important;
    }

    .copy-order-number {
        border-color:#e0e4e8 !important;
        border-radius:7px !important;
        padding:3px 7px !important;
        color:#68727d !important;
        font-size:6.5px !important;
    }

    .sari-order-card .mt-1.text-\[11px\] {
        color:#87909a !important;
        font-size:6.8px !important;
    }

    .sari-order-card .mt-3.text-sm {
        margin-top:7px !important;
        color:#47505a !important;
        font-size:8px !important;
        font-weight:650 !important;
    }

    .sari-order-card a.mt-1.inline-flex {
        color:#95620f !important;
        font-size:6.8px !important;
    }

    .sari-order-card > div:first-child .flex.items-center.gap-5 {
        gap:13px !important;
    }

    .sari-mini-divider {
        height:32px !important;
        background:#edf0f3 !important;
    }

    .sari-order-card .text-\[10px\].font-bold.uppercase {
        color:#929aa4 !important;
        font-size:6.2px !important;
    }

    .sari-order-card .mt-1.text-\[13px\] {
        color:#b6760b !important;
        font-size:8.5px !important;
    }

    .sari-order-card .text-sm.font-bold.uppercase {
        font-size:8.2px !important;
    }

    /* PRODUCT SUMMARY */
    .sari-order-card .mt-5.flex.flex-col {
        margin-top:11px !important;
        gap:10px !important;
        border-color:var(--sari-soft) !important;
        padding-top:10px !important;
    }

    .sari-product-thumb {
        width:38px !important;
        height:38px !important;
        flex-basis:38px !important;
        border:1px solid #e5e9ed !important;
        border-radius:9px !important;
        background:#f7f8f9 !important;
    }

    .sari-product-thumb .material-symbols-rounded { font-size:18px !important; }

    .sari-product-strip .text-xs {
        color:#4b545e !important;
        font-size:7.4px !important;
    }

    .sari-product-strip .text-\[10px\] {
        color:#9098a1 !important;
        font-size:6.2px !important;
    }

    .sari-price-box {
        border-color:#edf0f3 !important;
        padding-left:16px !important;
    }

    .sari-price-box .text-lg {
        color:#2e353d !important;
        font-size:13px !important;
    }

    /* TRACKING PROGRESS */
    .sari-order-card > .overflow-x-auto {
        padding:16px 16px 17px !important;
    }

    .sari-step {
        width:78px !important;
        flex-basis:78px !important;
    }

    .sari-step-line,
    .sari-step-progress {
        top:15px !important;
        height:2px !important;
    }

    .sari-step-line { background:#e8ecef !important; }
    .sari-step-progress { background:#c88912 !important; }

    .sari-step-dot {
        width:31px !important;
        height:31px !important;
        border:1.5px solid #d9dfe4 !important;
        background:#fff !important;
        color:#9aa2ab !important;
        box-shadow:none !important;
    }

    .sari-step-dot.is-complete {
        border-color:#c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    .sari-step-dot.is-current {
        box-shadow:0 0 0 4px rgba(200,137,18,.09) !important;
    }

    .sari-step-dot .material-symbols-rounded { font-size:15px !important; }

    .sari-step-label {
        margin-top:7px !important;
        font-size:6.2px !important;
        line-height:1.25 !important;
    }

    /* INFO GRID */
    .sari-order-card > .grid.border-t {
        border-color:var(--sari-soft) !important;
    }

    .sari-order-card > .grid.border-t > div {
        padding:12px 15px !important;
    }

    .sari-order-card > .grid.border-t > div + div {
        border-color:var(--sari-soft) !important;
    }

    .sari-order-card > .grid.border-t .text-xs {
        font-size:7.1px !important;
        line-height:1.5 !important;
    }

    .sari-order-card > .grid.border-t .text-sm {
        font-size:8.2px !important;
    }

    .sari-order-card > .grid.border-t .h-10.w-10 {
        width:33px !important;
        height:33px !important;
        background:#fffaf1 !important;
        color:#a96f0e !important;
        box-shadow:none !important;
    }

    /* ACTIONS */
    .sari-order-card > .grid.gap-2\.5 {
        gap:7px !important;
        border-color:var(--sari-soft) !important;
        background:#fafbfc !important;
        padding:10px 12px !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a,
    .sari-order-card > .grid.gap-2\.5 > button {
        height:35px !important;
        border-radius:8px !important;
        font-size:6.8px !important;
        font-weight:750 !important;
        box-shadow:none !important;
        transform:none !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a {
        border-color:#ddd8cb !important;
        background:#fff !important;
        color:#895b0f !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a:hover {
        border-color:#d2b474 !important;
        background:#fffaf1 !important;
    }

    .sari-order-card > .grid.gap-2\.5 > button {
        border:1px solid #c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
    }

    .sari-order-card > .grid.gap-2\.5 > button:hover {
        border-color:#aa710d !important;
        background:#aa710d !important;
    }

    .sari-order-card > .grid.gap-2\.5 .material-symbols-rounded {
        font-size:15px !important;
    }

    /* SUPPORTING CARDS */
    .sari-delivered-box,
    main > section.sari-soft-panel-sm {
        border:1px solid var(--sari-border) !important;
        border-radius:13px !important;
        background:#fff !important;
        padding:13px !important;
        box-shadow:0 8px 20px rgba(31,41,55,.03) !important;
    }

    /* SIDEBAR */
    .sari-sidebar {
        top:18px !important;
        gap:12px !important;
    }

    .sari-sidebar-card {
        border:1px solid var(--sari-border) !important;
        border-radius:14px !important;
        background:#fff !important;
        box-shadow:
            0 10px 26px rgba(31,41,55,.042),
            0 2px 6px rgba(31,41,55,.016) !important;
    }

    .sari-sidebar-card > div:first-child {
        border-color:var(--sari-soft) !important;
        padding:13px 14px !important;
    }

    .sari-sidebar-card h2 {
        color:#303740 !important;
        font-size:12px !important;
        font-weight:800 !important;
    }

    .sari-sidebar-card h2 + p {
        margin-top:2px !important;
        color:#8a939d !important;
        font-size:6.8px !important;
    }

    .sari-sidebar-card .h-10.w-10,
    .sari-sidebar-card .h-11.w-11 {
        width:34px !important;
        height:34px !important;
        border:1px solid #eee1c8 !important;
        border-radius:9px !important;
        background:#fffaf1 !important;
        color:#ad7412 !important;
    }

    .sari-sidebar-card .material-symbols-rounded { font-size:18px !important; }

    .sari-sidebar-card > .px-5.py-5 {
        padding:13px 14px !important;
    }

    .sari-sidebar-card .mb-4.rounded-xl {
        margin-bottom:11px !important;
        border:1px solid #ece7dc !important;
        border-radius:9px !important;
        background:#fafbfc !important;
        padding:8px 9px !important;
    }

    .sari-tracking-row { gap:9px !important; }

    .sari-tracking-row:not(:last-child)::after {
        left:7px !important;
        top:19px !important;
        height:29px !important;
        background:#e6eaee !important;
    }

    .sari-tracking-icon {
        width:15px !important;
        height:15px !important;
        flex-basis:15px !important;
    }

    .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-tracking-row.is-current .sari-tracking-icon {
        border-color:#c88912 !important;
        background:#c88912 !important;
    }

    .sari-tracking-row p.text-xs { font-size:7px !important; }
    .sari-tracking-row .text-\[9px\] { font-size:5.8px !important; }

    .sari-sidebar-card .divide-y {
        margin-top:10px !important;
    }

    .sari-sidebar-card .divide-y > a {
        padding-block:9px !important;
        color:#535c66 !important;
        font-size:7px !important;
    }

    /* VOUCHER CARD */
    .sari-sidebar > .relative.overflow-hidden.rounded-\[20px\] {
        border:1px solid #2f343a !important;
        border-radius:14px !important;
        background:#2f343a !important;
        padding:15px !important;
        box-shadow:0 10px 26px rgba(31,41,55,.08) !important;
    }

    .sari-sidebar > .relative.overflow-hidden.rounded-\[20px\] h3 {
        font-size:14px !important;
    }

    .sari-sidebar > .relative.overflow-hidden.rounded-\[20px\] a {
        height:34px !important;
        border-radius:8px !important;
        background:#c88912 !important;
        font-size:7px !important;
    }

    /* RETURN / REFUND */
    .sari-order-card details {
        border-color:#e1e6ea !important;
        border-radius:10px !important;
    }

    .sari-order-card details summary {
        color:#8d5d0e !important;
        font-size:7px !important;
    }

    .sari-order-card details select,
    .sari-order-card details input,
    .sari-order-card details button {
        height:36px !important;
        border-radius:8px !important;
        font-size:7px !important;
    }

    /* EMPTY STATE */
    main .sari-card.rounded-\[20px\].p-12 {
        border:1px solid var(--sari-border) !important;
        border-radius:14px !important;
        background:#fff !important;
        padding:38px 20px !important;
        box-shadow:0 10px 26px rgba(31,41,55,.04) !important;
    }

    /* RESPONSIVE */
    @media (max-width:1180px) {
        .sari-orders-shell > .grid.items-start {
            grid-template-columns:1fr !important;
        }

        .sari-sidebar {
            position:static !important;
            display:grid !important;
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .sari-sidebar > :last-child {
            grid-column:1 / -1;
        }
    }

    @media (max-width:767px) {
        .sari-orders-shell {
            padding:16px 14px 28px !important;
        }

        .sari-summary-strip {
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        }

        .sari-summary-item:nth-child(3)::before {
            display:none !important;
        }

        .sari-orders-shell > .mb-5.flex:first-of-type h1 {
            font-size:27px !important;
        }

        .sari-sidebar {
            grid-template-columns:1fr !important;
        }

        .sari-sidebar > :last-child {
            grid-column:auto;
        }
    }

    @media (max-width:520px) {
        .sari-summary-item { padding:10px !important; }

        .sari-order-card > div:first-child {
            padding:12px !important;
        }

        .sari-step {
            width:64px !important;
            flex-basis:64px !important;
        }
    }



    /* ============================================================
       CANCEL ORDER — FUNCTIONAL DANGER ACTION
       Backend accepts cancellation only while order status is "new".
    ============================================================ */
    .sari-cancel-order-form {
        display:block;
        width:100%;
        margin:0;
    }

    .sari-cancel-order-btn {
        width:100% !important;
        height:35px !important;
        border:1px solid #ef4444 !important;
        border-radius:8px !important;
        background:#ffffff !important;
        color:#dc2626 !important;
        padding-inline:10px !important;
        font-size:6.8px !important;
        font-weight:800 !important;
        box-shadow:none !important;
        cursor:pointer;
        transform:none !important;
    }

    .sari-cancel-order-btn .material-symbols-rounded {
        color:#dc2626 !important;
        font-size:15px !important;
    }

    .sari-cancel-order-btn:not(.is-disabled):hover,
    .sari-cancel-order-btn:not(.is-disabled):focus-visible {
        border-color:#dc2626 !important;
        background:#fff5f5 !important;
        color:#b91c1c !important;
        outline:none;
        box-shadow:0 0 0 3px rgba(239,68,68,.08) !important;
    }

    .sari-cancel-order-btn:not(.is-disabled):hover .material-symbols-rounded,
    .sari-cancel-order-btn:not(.is-disabled):focus-visible .material-symbols-rounded {
        color:#b91c1c !important;
    }

    .sari-cancel-order-btn.is-disabled,
    .sari-cancel-order-btn:disabled {
        border-color:#e3e6e9 !important;
        background:#f5f6f7 !important;
        color:#a0a7af !important;
        cursor:not-allowed;
        opacity:1 !important;
    }

    .sari-cancel-order-btn.is-disabled .material-symbols-rounded,
    .sari-cancel-order-btn:disabled .material-symbols-rounded {
        color:#a0a7af !important;
    }



    /* Final disabled Cancel Order state */
    .sari-cancel-order-btn.is-disabled,
    .sari-cancel-order-btn:disabled {
        border-color:#dfe3e7 !important;
        background:#f1f3f5 !important;
        color:#a8afb7 !important;
        cursor:not-allowed !important;
        pointer-events:none !important;
        box-shadow:none !important;
        opacity:1 !important;
    }

    .sari-cancel-order-btn.is-disabled .material-symbols-rounded,
    .sari-cancel-order-btn:disabled .material-symbols-rounded {
        color:#a8afb7 !important;
    }

    .sari-cancel-order-btn.is-disabled:hover,
    .sari-cancel-order-btn:disabled:hover,
    .sari-cancel-order-btn.is-disabled:focus,
    .sari-cancel-order-btn:disabled:focus {
        border-color:#dfe3e7 !important;
        background:#f1f3f5 !important;
        color:#a8afb7 !important;
        box-shadow:none !important;
        outline:none !important;
        transform:none !important;
    }



    /* Absolute final disabled state for non-cancellable orders */
    button[disabled][aria-disabled="true"] {
        background-color:#f1f3f5 !important;
        border-color:#dfe3e7 !important;
        color:#a8afb7 !important;
        cursor:not-allowed !important;
        pointer-events:none !important;
        opacity:1 !important;
        box-shadow:none !important;
    }

    button[disabled][aria-disabled="true"] .material-symbols-rounded,
    button[disabled][aria-disabled="true"] span {
        color:#a8afb7 !important;
    }



    /* ============================================================
       REAL SELLER + PRODUCT MEDIA
       Uses SellerAccount.store_logo_path and order item product_id.
    ============================================================ */
    .sari-order-store-avatar {
        position:relative;
        display:grid;
        width:44px;
        height:44px;
        flex:0 0 44px;
        place-items:center;
        overflow:hidden;
        border:1px solid #e1e6ea;
        border-radius:11px;
        background:#fff;
        color:#9a6811;
        text-decoration:none;
        box-shadow:0 3px 10px rgba(31,41,55,.025);
    }

    .sari-order-store-avatar:hover,
    .sari-order-store-avatar:focus-visible {
        border-color:#d7bd8a;
        outline:none;
        box-shadow:0 0 0 3px rgba(200,137,18,.07);
    }

    .sari-order-store-avatar-image {
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .sari-order-store-avatar-fallback {
        display:grid;
        width:100%;
        height:100%;
        place-items:center;
        background:#fffaf1;
        color:#9a6811;
        font-size:9px;
        font-weight:800;
        letter-spacing:.02em;
    }

    .sari-order-store-avatar-fallback.hidden {
        display:none !important;
    }

    .sari-order-product-image {
        position:relative;
        width:42px !important;
        height:42px !important;
        flex-basis:42px !important;
        overflow:hidden;
        border:1px solid #e1e6ea !important;
        border-radius:10px !important;
        background:#f7f8fa !important;
        color:#8b949e !important;
        text-decoration:none !important;
    }

    .sari-order-product-image:hover,
    .sari-order-product-image:focus-visible {
        border-color:#d6dce2 !important;
        outline:none;
        box-shadow:0 0 0 3px rgba(31,41,55,.04);
    }

    .sari-order-product-image img {
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .sari-order-product-fallback {
        display:grid;
        width:100%;
        height:100%;
        place-items:center;
        color:#9ba3ac;
        background:#f7f8fa;
    }

    .sari-order-product-fallback.hidden {
        display:none !important;
    }

    .sari-order-product-fallback svg {
        width:18px;
        height:18px;
    }

    .sari-order-product-more {
        position:absolute;
        right:3px;
        bottom:3px;
        display:grid;
        min-width:18px;
        height:18px;
        place-items:center;
        border:1px solid rgba(255,255,255,.9);
        border-radius:999px;
        background:rgba(47,52,58,.88);
        padding-inline:4px;
        color:#fff;
        font-size:6px;
        font-weight:800;
        line-height:1;
    }

    .sari-order-product-name {
        display:block;
        max-width:100%;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
        color:#414a54 !important;
        font-size:7.6px !important;
        font-weight:750 !important;
        text-decoration:none !important;
    }

    a.sari-order-product-name:hover,
    a.sari-order-product-name:focus-visible {
        color:#9a6811 !important;
        text-decoration:underline !important;
        outline:none;
    }

    .sari-order-product-meta {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:4px;
        margin-top:3px;
        color:#8d96a0;
        font-size:6.3px;
        line-height:1.35;
    }



    /* ============================================================
       SARI MY ORDERS — REFERENCE MATCH
       Clean, modern, compact, professional.
    ============================================================ */
    .sari-orders-reference {
        --ref-gold:#d18a09;
        --ref-gold-dark:#b87505;
        --ref-ink:#111827;
        --ref-muted:#7c8798;
        --ref-border:#dfe5ea;
        --ref-soft:#edf1f4;
        --ref-bg:#f6f8fa;
        background:var(--ref-bg) !important;
    }

    .sari-orders-shell {
        max-width:1460px !important;
        padding-top:18px !important;
        padding-bottom:34px !important;
    }

    /* PAGE HEADER */
    .sari-orders-shell > .mb-5.flex:first-of-type {
        min-height:84px;
        margin-bottom:12px !important;
        padding:0 2px !important;
        align-items:flex-start !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child {
        margin-bottom:5px !important;
        gap:0 !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child > span:first-child {
        display:none !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > div:first-child > div:first-child > span:last-child {
        color:#b87505 !important;
        font-size:7px !important;
        font-weight:800 !important;
        letter-spacing:.12em !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type h1 {
        color:var(--ref-ink) !important;
        font-size:clamp(31px,2.55vw,38px) !important;
        font-weight:800 !important;
        line-height:1 !important;
        letter-spacing:-.045em !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type h1 + p {
        margin-top:7px !important;
        color:#788396 !important;
        font-size:8.7px !important;
        line-height:1.55 !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a {
        height:44px !important;
        border:1px solid #c78106 !important;
        border-radius:11px !important;
        background:linear-gradient(180deg,#e0a10d,#ca8206) !important;
        padding-inline:18px !important;
        color:#fff !important;
        font-size:8.4px !important;
        font-weight:800 !important;
        box-shadow:0 8px 18px rgba(199,130,6,.16) !important;
        transform:none !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a:hover {
        border-color:#b87505 !important;
        background:linear-gradient(180deg,#d59609,#b97405) !important;
        transform:translateY(-1px) !important;
    }

    .sari-orders-shell > .mb-5.flex:first-of-type > a .material-symbols-rounded {
        font-size:17px !important;
    }

    /* SUMMARY */
    .sari-summary-strip {
        min-height:92px !important;
        margin-bottom:14px !important;
        overflow:hidden !important;
        border:1px solid var(--ref-border) !important;
        border-radius:16px !important;
        background:#fff !important;
        box-shadow:0 10px 28px rgba(31,41,55,.042) !important;
    }

    .sari-summary-item {
        min-width:0 !important;
        gap:13px !important;
        padding:14px 20px !important;
    }

    .sari-summary-item + .sari-summary-item::before {
        height:52px !important;
        background:#e8edf1 !important;
    }

    .sari-summary-item > div:first-child {
        width:45px !important;
        height:45px !important;
        border:1px solid #f0dfbd !important;
        border-radius:12px !important;
        background:#fff9ee !important;
        color:#c07b08 !important;
    }

    .sari-summary-item:last-child > div:first-child {
        border-color:#d9ebdf !important;
        background:#f0faf3 !important;
        color:#249044 !important;
    }

    .sari-summary-item .material-symbols-rounded {
        font-size:21px !important;
    }

    .sari-summary-item p:first-child {
        color:#8090a4 !important;
        font-size:7px !important;
        font-weight:800 !important;
        letter-spacing:.04em !important;
    }

    .sari-summary-item p:last-child {
        margin-top:2px !important;
        color:#172033 !important;
        font-size:16px !important;
        font-weight:800 !important;
    }

    /* MAIN GRID */
    .sari-orders-shell > .grid.items-start {
        gap:16px !important;
        grid-template-columns:minmax(0,1fr) 330px !important;
    }

    /* SEARCH + FILTERS */
    main > .sari-card.mb-5 {
        margin-bottom:14px !important;
        border:1px solid var(--ref-border) !important;
        border-radius:15px !important;
        background:#fff !important;
        padding:10px 12px !important;
        box-shadow:0 8px 22px rgba(31,41,55,.032) !important;
    }

    #orderSearch {
        height:43px !important;
        border:1px solid #dfe5ea !important;
        border-radius:10px !important;
        background:#fbfcfd !important;
        padding-left:38px !important;
        color:#394454 !important;
        font-size:8px !important;
        font-weight:600 !important;
    }

    #orderSearch::placeholder {
        color:#8c97a5 !important;
    }

    #orderSearch:focus {
        border-color:#cf9b43 !important;
        background:#fff !important;
        box-shadow:0 0 0 3px rgba(209,138,9,.08) !important;
    }

    #orderFilters {
        gap:7px !important;
    }

    .sari-filter {
        min-height:35px !important;
        border:1px solid #e2e7ec !important;
        border-radius:9px !important;
        background:#f9fafb !important;
        padding:0 12px !important;
        color:#566273 !important;
        font-size:7.2px !important;
        font-weight:700 !important;
        box-shadow:none !important;
    }

    .sari-filter:hover {
        border-color:#d5dce2 !important;
        background:#f4f6f8 !important;
        color:#2f3945 !important;
    }

    .sari-filter.is-active {
        border-color:#343a40 !important;
        background:#343a40 !important;
        color:#fff !important;
    }

    /* ORDER CARDS */
    main > section.space-y-5 {
        display:grid !important;
        gap:14px !important;
    }

    .sari-order-card {
        overflow:hidden !important;
        border:1px solid var(--ref-border) !important;
        border-radius:16px !important;
        background:#fff !important;
        box-shadow:0 10px 28px rgba(31,41,55,.04) !important;
        transform:none !important;
    }

    .sari-order-card:hover {
        border-color:#d6dde3 !important;
        box-shadow:0 12px 30px rgba(31,41,55,.05) !important;
        transform:none !important;
    }

    .sari-order-card > div:first-child {
        border-color:var(--ref-soft) !important;
        padding:16px 18px !important;
    }

    .sari-order-card > div:first-child > div:first-child {
        gap:15px !important;
    }

    .sari-order-store-avatar {
        width:54px !important;
        height:54px !important;
        flex-basis:54px !important;
        border:1px solid #e2e7ec !important;
        border-radius:12px !important;
        box-shadow:none !important;
    }

    .sari-order-store-avatar-fallback {
        background:#fff7e8 !important;
        color:#af7008 !important;
        font-size:12px !important;
    }

    .sari-order-card h2 {
        color:#172033 !important;
        font-size:12.8px !important;
        font-weight:800 !important;
        letter-spacing:-.015em !important;
    }

    .copy-order-number {
        display:inline-flex !important;
        min-height:25px !important;
        align-items:center !important;
        border-color:#dfe5ea !important;
        border-radius:8px !important;
        background:#fff !important;
        padding-inline:8px !important;
        color:#687588 !important;
        font-size:6.6px !important;
    }

    /* STATUS */
    .sari-order-card [class*="rounded-full"][class*="bg-[#fff4d9]"] {
        min-height:27px !important;
        border:1px solid #eddab3 !important;
        border-radius:999px !important;
        background:#fff8e8 !important;
        padding-inline:10px !important;
        color:#a66a0b !important;
        font-size:7px !important;
        font-weight:800 !important;
    }

    .sari-order-card[data-status="cancelled"] [class*="rounded-full"][class*="bg-[#fff4d9]"] {
        border-color:#f0cdcd !important;
        background:#fff1f1 !important;
        color:#dc2626 !important;
    }

    .sari-order-card[data-status="cancelled"] [class*="rounded-full"][class*="bg-[#fff4d9]"] > span:first-child {
        background:#ef4444 !important;
    }

    .sari-order-card .mt-1.text-\[11px\] {
        color:#8090a2 !important;
        font-size:6.9px !important;
    }

    .sari-order-card a.mt-1.inline-flex {
        margin-top:7px !important;
        color:#a96b08 !important;
        font-size:7px !important;
        font-weight:700 !important;
    }

    /* DELIVERY / PAYMENT */
    .sari-order-card > div:first-child .flex.items-center.gap-5 {
        gap:17px !important;
    }

    .sari-mini-divider {
        height:46px !important;
        background:#e7ecf0 !important;
    }

    .sari-order-card .text-\[10px\].font-bold.uppercase {
        color:#8491a2 !important;
        font-size:6.5px !important;
        letter-spacing:.09em !important;
    }

    .sari-order-card .mt-1.text-\[13px\] {
        font-size:8.8px !important;
    }

    .sari-order-card .text-sm.font-bold.uppercase {
        font-size:8.5px !important;
        color:#1f2937 !important;
    }

    /* PRODUCT ROW */
    .sari-order-card .mt-5.flex.flex-col {
        margin-top:13px !important;
        gap:12px !important;
        border-top:1px solid #edf1f4 !important;
        border-top-style:solid !important;
        padding-top:13px !important;
    }

    .sari-order-card .mt-5.flex.flex-col > div:first-child > p:first-child {
        color:#7f8da0 !important;
        font-size:7px !important;
        letter-spacing:.12em !important;
    }

    .sari-product-strip {
        gap:12px !important;
    }

    .sari-order-product-image {
        width:66px !important;
        height:66px !important;
        flex-basis:66px !important;
        border:1px solid #e1e6eb !important;
        border-radius:11px !important;
        background:#fff !important;
    }

    .sari-order-product-name {
        color:#202a38 !important;
        font-size:8.4px !important;
        font-weight:800 !important;
    }

    .sari-order-product-meta {
        margin-top:5px !important;
        color:#7e8a99 !important;
        font-size:6.8px !important;
    }

    .sari-price-box {
        min-width:138px !important;
        border-color:#e6ebef !important;
        padding-left:22px !important;
    }

    .sari-price-box .text-lg {
        color:#111827 !important;
        font-size:16px !important;
        font-weight:800 !important;
    }

    /* TIMELINE */
    .sari-order-card > .overflow-x-auto {
        padding:16px 18px 17px !important;
        border-top:1px solid #edf1f4 !important;
        background:#fff !important;
    }

    .sari-step {
        width:82px !important;
        flex-basis:82px !important;
    }

    .sari-step-line,
    .sari-step-progress {
        top:16px !important;
        height:2px !important;
    }

    .sari-step-line {
        background:#dde4ea !important;
    }

    .sari-step-progress {
        background:var(--ref-gold) !important;
    }

    .sari-step-dot {
        width:34px !important;
        height:34px !important;
        border:1.5px solid #d5dde4 !important;
        background:#fff !important;
        color:#98a3af !important;
        box-shadow:none !important;
    }

    .sari-step-dot.is-complete {
        border-color:var(--ref-gold) !important;
        background:var(--ref-gold) !important;
        color:#fff !important;
        box-shadow:0 0 0 4px rgba(209,138,9,.08) !important;
    }

    .sari-step-label {
        margin-top:8px !important;
        color:#5e6875 !important;
        font-size:6.6px !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-step-progress {
        width:0 !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-step-dot {
        border-color:#d9e0e6 !important;
        background:#fff !important;
        color:#9fa8b2 !important;
        box-shadow:none !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-step:first-of-type .sari-step-dot {
        border-color:var(--ref-gold) !important;
        background:var(--ref-gold) !important;
        color:#fff !important;
        box-shadow:0 0 0 4px rgba(209,138,9,.08) !important;
    }

    /* INFORMATION ROW */
    .sari-order-card > .grid.border-t {
        border-color:#e9eef2 !important;
    }

    .sari-order-card > .grid.border-t > div {
        min-height:116px !important;
        padding:14px 17px !important;
    }

    .sari-order-card > .grid.border-t > div + div {
        border-color:#e9eef2 !important;
    }

    .sari-order-card > .grid.border-t .text-xs {
        color:#647081 !important;
        font-size:7.2px !important;
        line-height:1.55 !important;
    }

    .sari-order-card > .grid.border-t .text-sm {
        color:#222c3a !important;
        font-size:8.5px !important;
    }

    .sari-order-card > .grid.border-t .h-10.w-10 {
        width:38px !important;
        height:38px !important;
        background:#fff7e9 !important;
        color:#bf7b08 !important;
        box-shadow:none !important;
    }

    /* ACTIONS */
    .sari-order-card > .grid.gap-2\.5 {
        gap:9px !important;
        border-color:#e9eef2 !important;
        background:#fff !important;
        padding:11px 14px !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a,
    .sari-order-card > .grid.gap-2\.5 > button,
    .sari-order-card > .grid.gap-2\.5 > form > button {
        height:38px !important;
        border-radius:9px !important;
        font-size:7.2px !important;
        font-weight:800 !important;
        box-shadow:none !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a {
        border-color:#e4c98f !important;
        background:#fff !important;
        color:#9f6509 !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a:hover {
        border-color:#d6aa50 !important;
        background:#fff9ed !important;
    }

    button[disabled][aria-disabled="true"] {
        background:#eef1f4 !important;
        border-color:#dce2e7 !important;
        color:#a7b0ba !important;
        cursor:not-allowed !important;
        pointer-events:none !important;
    }

    /* SIDEBAR */
    .sari-sidebar {
        top:18px !important;
        gap:14px !important;
    }

    .sari-sidebar-card {
        overflow:hidden !important;
        border:1px solid var(--ref-border) !important;
        border-radius:16px !important;
        background:#fff !important;
        box-shadow:0 10px 26px rgba(31,41,55,.038) !important;
    }

    .sari-sidebar-card > div:first-child {
        border-color:#edf1f4 !important;
        padding:16px !important;
    }

    .sari-sidebar-card h2 {
        color:#172033 !important;
        font-size:13.5px !important;
        font-weight:800 !important;
    }

    .sari-sidebar-card h2 + p {
        margin-top:3px !important;
        color:#8290a3 !important;
        font-size:7px !important;
    }

    .sari-sidebar-card .h-10.w-10,
    .sari-sidebar-card .h-11.w-11 {
        width:42px !important;
        height:42px !important;
        border:1px solid #efdcb5 !important;
        border-radius:11px !important;
        background:#fff8e9 !important;
        color:#c07b08 !important;
    }

    .sari-sidebar-card .material-symbols-rounded {
        font-size:21px !important;
    }

    .sari-sidebar-card > .px-5.py-5 {
        padding:14px 16px !important;
    }

    .sari-sidebar-card .mb-4.rounded-xl {
        margin-bottom:13px !important;
        border:1px solid #f1d6d6 !important;
        border-radius:10px !important;
        background:#fff6f6 !important;
        padding:10px 12px !important;
    }

    .sari-sidebar-card .mb-4.rounded-xl p:first-child {
        color:#956d6d !important;
        font-size:6.5px !important;
    }

    .sari-sidebar-card .mb-4.rounded-xl p:last-child {
        color:#172033 !important;
        font-size:8.8px !important;
    }

    .sari-tracking-row {
        gap:11px !important;
    }

    .sari-tracking-row:not(:last-child)::after {
        left:8px !important;
        top:21px !important;
        height:36px !important;
        background:#dde4e9 !important;
    }

    .sari-tracking-icon {
        width:18px !important;
        height:18px !important;
        flex-basis:18px !important;
    }

    .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-tracking-row.is-current .sari-tracking-icon {
        border-color:var(--ref-gold) !important;
        background:var(--ref-gold) !important;
    }

    .sari-tracking-row p.text-xs {
        font-size:7.4px !important;
    }

    .sari-tracking-row .text-\[9px\] {
        font-size:6px !important;
    }

    .sari-sidebar-card h3 {
        color:#172033 !important;
        font-size:12.5px !important;
        font-weight:800 !important;
    }

    .sari-sidebar-card .divide-y {
        margin-top:11px !important;
    }

    .sari-sidebar-card .divide-y > a {
        min-height:44px !important;
        padding-block:10px !important;
        color:#394555 !important;
        font-size:7.2px !important;
    }

    /* Reference doesn't include the voucher card. */
    .sari-sidebar > .relative.overflow-hidden.rounded-\[20px\] {
        display:none !important;
    }

    /* Secondary panels below order list stay quiet. */
    .sari-delivered-box,
    main > section.sari-soft-panel-sm {
        border-color:#e4e9ee !important;
        background:#fff !important;
        box-shadow:0 8px 20px rgba(31,41,55,.025) !important;
    }

    @media (max-width:1180px) {
        .sari-orders-shell > .grid.items-start {
            grid-template-columns:1fr !important;
        }

        .sari-sidebar {
            position:static !important;
            display:grid !important;
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .sari-sidebar > :last-child {
            grid-column:1 / -1;
        }
    }

    @media (max-width:767px) {
        .sari-orders-shell {
            padding:16px 14px 28px !important;
        }

        .sari-orders-shell > .mb-5.flex:first-of-type {
            min-height:0;
        }

        .sari-summary-strip {
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        }

        .sari-order-card > div:first-child {
            padding:14px !important;
        }

        .sari-order-card > .grid.border-t > div {
            min-height:auto !important;
        }

        .sari-order-product-image {
            width:58px !important;
            height:58px !important;
            flex-basis:58px !important;
        }

        .sari-sidebar {
            grid-template-columns:1fr !important;
        }

        .sari-sidebar > :last-child {
            grid-column:auto;
        }
    }



    /* ============================================================
       CANCELLED = BRIGHT RED
       Details remain hidden until "See more details" is opened.
    ============================================================ */
    .sari-order-card[data-status="cancelled"] [class*="rounded-full"][class*="bg-[#fff4d9]"] {
        border-color:#fecaca !important;
        background:#fff1f2 !important;
        color:#ef4444 !important;
    }

    .sari-order-card[data-status="cancelled"] [class*="rounded-full"][class*="bg-[#fff4d9]"] > span:first-child {
        background:#ef4444 !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-delivery-status {
        color:#ef4444 !important;
    }

    .sari-order-more-details {
        border-top:1px solid #e9eef2;
        background:#fff;
    }

    .sari-order-more-summary {
        display:flex;
        min-height:42px;
        align-items:center;
        justify-content:center;
        gap:6px;
        padding:0 16px;
        color:#9a6811;
        font-size:7.2px;
        font-weight:800;
        cursor:pointer;
        list-style:none;
        user-select:none;
        transition:background-color .15s ease,color .15s ease;
    }

    .sari-order-more-summary::-webkit-details-marker {
        display:none;
    }

    .sari-order-more-summary:hover,
    .sari-order-more-summary:focus-visible {
        background:#fffaf1;
        color:#7f540e;
        outline:none;
    }

    .sari-order-more-chevron {
        font-size:16px !important;
        transition:transform .18s ease;
    }

    .sari-order-more-details[open] .sari-order-more-chevron {
        transform:rotate(180deg);
    }

    .sari-order-more-details[open] .sari-order-more-summary {
        border-bottom:1px solid #e9eef2;
    }

    .sari-order-more-content {
        background:#fff;
    }

    .sari-order-more-content > div {
        min-height:116px;
    }

    .sari-order-more-content > div + div {
        border-color:#e9eef2 !important;
    }

    .sari-order-more-content .text-xs {
        color:#647081 !important;
        font-size:7.2px !important;
        line-height:1.55 !important;
    }

    .sari-order-more-content .text-sm {
        color:#222c3a !important;
        font-size:8.5px !important;
    }

    .sari-order-more-content .h-10.w-10 {
        width:38px !important;
        height:38px !important;
        background:#fff7e9 !important;
        color:#bf7b08 !important;
        box-shadow:none !important;
    }

    @media (max-width:767px) {
        .sari-order-more-content > div {
            min-height:auto;
        }

        .sari-order-more-summary {
            min-height:40px;
        }
    }

    @media (prefers-reduced-motion:reduce) {
        .sari-order-more-chevron {
            transition:none !important;
        }
    }



    /* SARI gold monetary hierarchy */
    .sari-order-total-price {
        color:#c98206 !important;
        font-weight:800 !important;
    }

    .sari-order-total-price:hover {
        color:#ad7106 !important;
    }



    /* ============================================================
       MODERN ORDER PROGRESS + DETAILS
       Clean, premium, compact, reference-inspired.
    ============================================================ */

    .sari-order-tracking-panel {
        border-top:1px solid #e8edf1;
        background:#fff;
    }

    .sari-order-progress-wrap {
        padding:22px 22px 18px !important;
    }

    .sari-order-progress {
        min-width:560px !important;
    }

    .sari-order-progress .sari-step-line,
    .sari-order-progress .sari-step-progress {
        top:18px !important;
        height:2px !important;
        left:8.5% !important;
        right:8.5% !important;
        border-radius:999px;
    }

    .sari-order-progress .sari-step-line {
        background:#dfe5ea !important;
    }

    .sari-order-progress .sari-step-progress {
        background:linear-gradient(90deg,#d89a19 0%,#c78308 100%) !important;
        box-shadow:none !important;
    }

    .sari-order-progress .sari-step {
        width:94px !important;
        flex-basis:94px !important;
    }

    .sari-order-progress .sari-step-dot {
        width:38px !important;
        height:38px !important;
        border:1.5px solid #d5dde5 !important;
        background:#fff !important;
        color:#94a0ae !important;
        box-shadow:0 2px 6px rgba(31,41,55,.018) !important;
    }

    .sari-order-progress .sari-step-dot .material-symbols-rounded {
        font-size:17px !important;
    }

    .sari-order-progress .sari-step-dot.is-complete {
        border-color:#d18a09 !important;
        background:linear-gradient(180deg,#dfa21b,#c98508) !important;
        color:#fff !important;
        box-shadow:
            0 0 0 4px rgba(209,138,9,.08),
            0 5px 12px rgba(190,124,8,.11) !important;
    }

    .sari-order-progress .sari-step-dot.is-current {
        border-color:#c98508 !important;
        box-shadow:
            0 0 0 6px rgba(209,138,9,.08),
            0 6px 14px rgba(190,124,8,.12) !important;
    }

    .sari-order-progress .sari-step-label {
        margin-top:8px !important;
        color:#687486 !important;
        font-size:7px !important;
        font-weight:700 !important;
        line-height:1.25 !important;
    }

    .sari-order-progress .sari-step-dot.is-current + .sari-step-label,
    .sari-order-progress .sari-step-dot.is-complete + .sari-step-label {
        color:#9a6208 !important;
    }

    /* Cancelled orders: keep only first milestone active */
    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step-progress {
        width:0 !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step-dot {
        border-color:#d8e0e7 !important;
        background:#fff !important;
        color:#99a4af !important;
        box-shadow:0 2px 6px rgba(31,41,55,.018) !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step:first-of-type .sari-step-dot {
        border-color:#d18a09 !important;
        background:linear-gradient(180deg,#dfa21b,#c98508) !important;
        color:#fff !important;
        box-shadow:
            0 0 0 4px rgba(209,138,9,.08),
            0 5px 12px rgba(190,124,8,.11) !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step:first-of-type .sari-step-label {
        color:#9a6208 !important;
    }

    /* Disclosure */
    .sari-order-more-details {
        border-top:1px solid #e8edf1 !important;
        background:#fff !important;
        border-radius:0 !important;
    }

    .sari-order-more-summary {
        display:flex !important;
        min-height:48px !important;
        align-items:center !important;
        justify-content:center !important;
        gap:7px !important;
        padding:0 18px !important;
        background:#fff !important;
        color:#96610a !important;
        font-size:7.6px !important;
        font-weight:800 !important;
        letter-spacing:.005em;
        cursor:pointer !important;
        list-style:none !important;
        user-select:none;
        transition:background-color .15s ease,color .15s ease !important;
    }

    .sari-order-more-summary::-webkit-details-marker {
        display:none;
    }

    .sari-order-more-summary:hover,
    .sari-order-more-summary:focus-visible {
        background:#fffaf1 !important;
        color:#7c4f08 !important;
        outline:none !important;
    }

    .sari-order-more-label--open {
        display:none;
    }

    .sari-order-more-details[open] .sari-order-more-label--closed {
        display:none;
    }

    .sari-order-more-details[open] .sari-order-more-label--open {
        display:inline;
    }

    .sari-order-more-chevron {
        font-size:16px !important;
        transition:transform .18s ease;
    }

    .sari-order-more-details[open] .sari-order-more-chevron {
        transform:rotate(180deg);
    }

    .sari-order-more-details[open] .sari-order-more-summary {
        border-top:1px solid #eef1f4;
        border-bottom:1px solid #e8edf1 !important;
    }

    /* Expanded information */
    .sari-order-more-content {
        display:grid !important;
        grid-template-columns:repeat(3,minmax(0,1fr));
        background:#fff !important;
    }

    .sari-order-detail-column {
        position:relative;
        min-height:126px;
        padding:22px 24px;
    }

    .sari-order-detail-column + .sari-order-detail-column {
        border-left:1px solid #e8edf1;
    }

    .sari-order-detail-row {
        display:flex;
        align-items:flex-start;
        gap:13px;
    }

    .sari-order-detail-icon {
        display:grid;
        width:42px;
        height:42px;
        flex:0 0 42px;
        place-items:center;
        border:1px solid #f0dfbd;
        border-radius:999px;
        background:#fff8e8;
        color:#bb7808;
    }

    .sari-order-detail-icon--alert {
        border-color:#f5d8d8;
        background:#fff1f1;
        color:#ef4444;
    }

    .sari-order-detail-icon .material-symbols-rounded {
        font-size:19px !important;
    }

    .sari-order-detail-title {
        color:#273142;
        font-size:8.6px;
        font-weight:800;
        line-height:1.25;
    }

    .sari-order-detail-primary {
        margin-top:5px;
        color:#253041;
        font-size:7.7px;
        font-weight:750;
        line-height:1.45;
    }

    .sari-order-detail-secondary {
        margin-top:4px;
        color:#8490a0;
        font-size:7.2px;
        line-height:1.55;
    }

    .sari-order-detail-address {
        max-width:310px;
        margin-top:6px;
    }

    /* More whitespace only when open */
    .sari-order-more-details[open] {
        box-shadow:inset 0 1px 0 rgba(255,255,255,.8);
    }

    @media (max-width:900px) {
        .sari-order-more-content {
            grid-template-columns:1fr !important;
        }

        .sari-order-detail-column {
            min-height:auto;
            padding:18px 20px;
        }

        .sari-order-detail-column + .sari-order-detail-column {
            border-left:0;
            border-top:1px solid #e8edf1;
        }
    }

    @media (max-width:640px) {
        .sari-order-progress-wrap {
            padding:18px 14px 15px !important;
        }

        .sari-order-progress .sari-step {
            width:76px !important;
            flex-basis:76px !important;
        }

        .sari-order-progress .sari-step-dot {
            width:34px !important;
            height:34px !important;
        }

        .sari-order-progress .sari-step-line,
        .sari-order-progress .sari-step-progress {
            top:16px !important;
        }

        .sari-order-more-summary {
            min-height:44px !important;
        }
    }

    @media (prefers-reduced-motion:reduce) {
        .sari-order-more-chevron,
        .sari-order-progress .sari-step-progress {
            transition:none !important;
        }
    }



    /* ============================================================
       COMPACT FLAT PROGRESS — NO GRADIENTS
       Modern, clean, professional.
    ============================================================ */

    .sari-order-progress-wrap {
        padding:14px 18px 12px !important;
    }

    .sari-order-progress {
        min-width:520px !important;
    }

    .sari-order-progress .sari-step-line,
    .sari-order-progress .sari-step-progress {
        top:15px !important;
        height:2px !important;
        left:9% !important;
        right:9% !important;
        border-radius:999px;
    }

    .sari-order-progress .sari-step-line {
        background:#dfe5ea !important;
    }

    .sari-order-progress .sari-step-progress {
        background:#c88912 !important;
        box-shadow:none !important;
    }

    .sari-order-progress .sari-step {
        width:82px !important;
        flex-basis:82px !important;
    }

    .sari-order-progress .sari-step-dot {
        width:32px !important;
        height:32px !important;
        border:1.5px solid #d5dde5 !important;
        background:#fff !important;
        color:#98a3ae !important;
        box-shadow:none !important;
    }

    .sari-order-progress .sari-step-dot .material-symbols-rounded {
        font-size:15px !important;
    }

    .sari-order-progress .sari-step-dot.is-complete,
    .sari-order-progress .sari-step-dot.is-current {
        border-color:#c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    .sari-order-progress .sari-step-label {
        margin-top:6px !important;
        color:#697586 !important;
        font-size:6.4px !important;
        font-weight:700 !important;
        line-height:1.2 !important;
    }

    .sari-order-progress .sari-step-dot.is-current + .sari-step-label,
    .sari-order-progress .sari-step-dot.is-complete + .sari-step-label {
        color:#99620d !important;
    }

    /* Cancelled orders */
    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step-progress {
        width:0 !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step-dot {
        border-color:#d8e0e7 !important;
        background:#fff !important;
        color:#99a4af !important;
        box-shadow:none !important;
    }

    .sari-order-card[data-status="cancelled"] .sari-order-progress .sari-step:first-of-type .sari-step-dot {
        border-color:#c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    /* Compact disclosure */
    .sari-order-more-summary {
        min-height:38px !important;
        gap:5px !important;
        padding:0 14px !important;
        background:#fff !important;
        color:#96610a !important;
        font-size:7px !important;
        font-weight:800 !important;
    }

    .sari-order-more-summary:hover,
    .sari-order-more-summary:focus-visible {
        background:#fffaf2 !important;
        color:#7f530d !important;
    }

    .sari-order-more-chevron {
        font-size:14px !important;
    }

    /* Compact expanded details */
    .sari-order-detail-column {
        min-height:104px !important;
        padding:16px 18px !important;
    }

    .sari-order-detail-row {
        gap:10px !important;
    }

    .sari-order-detail-icon {
        width:34px !important;
        height:34px !important;
        flex-basis:34px !important;
        border-radius:10px !important;
        background:#fff8e9 !important;
        color:#b97908 !important;
    }

    .sari-order-detail-icon--alert {
        background:#fff1f1 !important;
        color:#ef4444 !important;
    }

    .sari-order-detail-icon .material-symbols-rounded {
        font-size:16px !important;
    }

    .sari-order-detail-title {
        font-size:7.8px !important;
    }

    .sari-order-detail-primary {
        margin-top:3px !important;
        font-size:7.2px !important;
    }

    .sari-order-detail-secondary {
        margin-top:3px !important;
        font-size:6.7px !important;
        line-height:1.45 !important;
    }

    .sari-order-detail-address {
        margin-top:4px !important;
    }

    @media (max-width:640px) {
        .sari-order-progress-wrap {
            padding:13px 12px 11px !important;
        }

        .sari-order-progress .sari-step {
            width:70px !important;
            flex-basis:70px !important;
        }

        .sari-order-progress .sari-step-dot {
            width:30px !important;
            height:30px !important;
        }

        .sari-order-progress .sari-step-line,
        .sari-order-progress .sari-step-progress {
            top:14px !important;
        }

        .sari-order-more-summary {
            min-height:36px !important;
        }

        .sari-order-detail-column {
            padding:14px 16px !important;
        }
    }



    /* ============================================================
       PROGRESS CONNECTOR FIX
       Connect first step center to last step center with no gaps.
    ============================================================ */

    .sari-order-progress {
        position:relative !important;
        padding-inline:16px !important;
    }

    .sari-order-progress .sari-step-line,
    .sari-order-progress .sari-step-progress {
        top:15px !important;
        left:calc(16px + 41px) !important;
        right:calc(16px + 41px) !important;
        height:2px !important;
        border-radius:999px !important;
    }

    .sari-order-progress .sari-step-line {
        background:#dfe5ea !important;
    }

    .sari-order-progress .sari-step-progress {
        background:#c88912 !important;
    }

    /* Keep each milestone above the connector */
    .sari-order-progress .sari-step {
        position:relative !important;
        z-index:2 !important;
    }

    .sari-order-progress .sari-step-dot {
        position:relative !important;
        z-index:3 !important;
        background:#fff !important;
    }

    .sari-order-progress .sari-step-dot.is-complete,
    .sari-order-progress .sari-step-dot.is-current {
        background:#c88912 !important;
    }

    @media (max-width:640px) {
        .sari-order-progress {
            padding-inline:12px !important;
        }

        .sari-order-progress .sari-step-line,
        .sari-order-progress .sari-step-progress {
            left:calc(12px + 35px) !important;
            right:calc(12px + 35px) !important;
            top:14px !important;
        }
    }



    /* ============================================================
       RIGHT SIDEBAR CLEANUP
       Align tracking rail + remove decorative icon containers.
    ============================================================ */

    /* Perfectly aligned vertical tracking rail */
    .sari-sidebar .sari-tracking-row {
        position:relative !important;
        display:grid !important;
        grid-template-columns:18px minmax(0,1fr) !important;
        align-items:start !important;
        gap:11px !important;
        min-height:46px !important;
    }

    .sari-sidebar .sari-tracking-row:not(:last-child)::after {
        content:"" !important;
        position:absolute !important;
        left:8.5px !important;
        top:18px !important;
        bottom:-1px !important;
        width:1px !important;
        height:auto !important;
        background:#dfe5ea !important;
        transform:none !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete:not(:last-child)::after {
        background:#c88912 !important;
    }

    .sari-sidebar .sari-tracking-icon {
        position:relative !important;
        z-index:2 !important;
        width:18px !important;
        height:18px !important;
        flex:0 0 18px !important;
        margin:0 !important;
        border:1.5px solid #d3dae1 !important;
        background:#fff !important;
        box-shadow:none !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-sidebar .sari-tracking-row.is-current .sari-tracking-icon {
        border-color:#c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    .sari-sidebar .sari-tracking-row > .min-w-0 {
        padding-bottom:8px !important;
    }

    .sari-sidebar .sari-tracking-row p.text-xs {
        margin:0 !important;
        line-height:1.25 !important;
    }

    .sari-sidebar .sari-tracking-row .text-\[9px\] {
        margin-top:3px !important;
        line-height:1.2 !important;
    }

    /* Remove decorative rounded-square icon containers */
    .sari-sidebar-card > div:first-child .h-10.w-10,
    .sari-sidebar-card > div:first-child .h-11.w-11,
    .sari-sidebar-card .h-10.w-10,
    .sari-sidebar-card .h-11.w-11 {
        width:auto !important;
        height:auto !important;
        min-width:0 !important;
        min-height:0 !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
        padding:0 !important;
    }

    .sari-sidebar-card > div:first-child .h-10.w-10 .material-symbols-rounded,
    .sari-sidebar-card > div:first-child .h-11.w-11 .material-symbols-rounded,
    .sari-sidebar-card .h-10.w-10 .material-symbols-rounded,
    .sari-sidebar-card .h-11.w-11 .material-symbols-rounded {
        color:#c88912 !important;
        font-size:23px !important;
    }

    /* Cleaner spacing after removing icon boxes */
    .sari-sidebar-card > div:first-child {
        padding:15px 16px !important;
    }

    .sari-sidebar-card .flex.items-center.gap-3 {
        gap:10px !important;
    }

    @media (max-width:767px) {
        .sari-sidebar .sari-tracking-row {
            min-height:44px !important;
        }
    }



    /* ============================================================
       FINAL SIDEBAR TRACKING FIX
       Exact alignment for check icon, dots, labels and vertical rail.
       Uses the latest uploaded My Orders file as the base.
    ============================================================ */

    .sari-sidebar .sari-tracking-row {
        position:relative !important;
        display:grid !important;
        grid-template-columns:18px minmax(0,1fr) !important;
        align-items:start !important;
        column-gap:11px !important;
        min-height:46px !important;
        margin:0 !important;
    }

    /* Rail centered exactly on the 18px milestone circle */
    .sari-sidebar .sari-tracking-row:not(:last-child)::after {
        content:"" !important;
        position:absolute !important;
        z-index:0 !important;
        left:8.5px !important;
        top:18px !important;
        bottom:-1px !important;
        width:1px !important;
        height:auto !important;
        background:#dfe5ea !important;
        transform:none !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete:not(:last-child)::after {
        background:#c88912 !important;
    }

    /* Milestone circle */
    .sari-sidebar .sari-tracking-icon {
        position:relative !important;
        z-index:2 !important;

        display:flex !important;
        align-items:center !important;
        justify-content:center !important;

        width:18px !important;
        height:18px !important;
        min-width:18px !important;
        min-height:18px !important;
        flex:0 0 18px !important;

        margin:0 !important;
        padding:0 !important;

        border:1.5px solid #d3dae1 !important;
        border-radius:9999px !important;

        background:#fff !important;
        color:transparent !important;

        line-height:1 !important;
        box-shadow:none !important;
        overflow:hidden !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-sidebar .sari-tracking-row.is-current .sari-tracking-icon {
        border-color:#c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
        box-shadow:none !important;
    }

    /* Check icon: true visual center */
    .sari-sidebar .sari-tracking-icon .material-symbols-rounded {
        display:block !important;

        width:11px !important;
        height:11px !important;

        margin:0 !important;
        padding:0 !important;

        color:currentColor !important;
        font-size:11px !important;
        line-height:11px !important;
        text-align:center !important;

        transform:none !important;

        font-variation-settings:
            'FILL' 0,
            'wght' 600,
            'GRAD' 0,
            'opsz' 20 !important;
    }

    /* Keep text aligned with top of milestone */
    .sari-sidebar .sari-tracking-row > .min-w-0 {
        min-width:0 !important;
        padding:0 0 8px !important;
        margin:0 !important;
    }

    .sari-sidebar .sari-tracking-row p.text-xs {
        margin:0 !important;
        padding:0 !important;
        line-height:1.25 !important;
    }

    .sari-sidebar .sari-tracking-row .text-\[9px\] {
        margin-top:3px !important;
        line-height:1.2 !important;
    }

    /* Keep standalone header/help icons clean — no decorative containers */
    .sari-sidebar-card > div:first-child .h-10.w-10,
    .sari-sidebar-card > div:first-child .h-11.w-11,
    .sari-sidebar-card .h-10.w-10,
    .sari-sidebar-card .h-11.w-11 {
        width:auto !important;
        height:auto !important;
        min-width:0 !important;
        min-height:0 !important;

        border:0 !important;
        border-radius:0 !important;

        background:transparent !important;
        box-shadow:none !important;

        padding:0 !important;
    }

    .sari-sidebar-card > div:first-child .h-10.w-10 .material-symbols-rounded,
    .sari-sidebar-card > div:first-child .h-11.w-11 .material-symbols-rounded,
    .sari-sidebar-card .h-10.w-10 .material-symbols-rounded,
    .sari-sidebar-card .h-11.w-11 .material-symbols-rounded {
        color:#c88912 !important;
        font-size:23px !important;
        line-height:1 !important;
    }

    @media (max-width:767px) {
        .sari-sidebar .sari-tracking-row {
            min-height:44px !important;
        }
    }



    /* ============================================================
       PRODUCT CLICK -> SIDEBAR ORDER TRACKING
       Horizontal progress inside order cards is intentionally removed.
    ============================================================ */

    .sari-track-order-trigger {
        cursor:pointer !important;
    }

    button.sari-order-product-image {
        padding:0 !important;
        appearance:none !important;
        -webkit-appearance:none !important;
        text-align:inherit !important;
    }

    button.sari-order-product-name {
        display:block !important;
        width:auto !important;
        max-width:100% !important;
        padding:0 !important;
        border:0 !important;
        background:transparent !important;
        appearance:none !important;
        -webkit-appearance:none !important;
        cursor:pointer !important;
        text-align:left !important;
    }

    button.sari-order-product-image:hover,
    button.sari-order-product-image:focus-visible {
        border-color:#cfa75b !important;
        box-shadow:0 0 0 3px rgba(200,137,18,.07) !important;
        outline:none !important;
    }

    button.sari-order-product-name:hover,
    button.sari-order-product-name:focus-visible {
        color:#a96f0e !important;
        text-decoration:underline !important;
        outline:none !important;
    }

    .sari-order-card.is-tracking-selected {
        border-color:#dec48f !important;
        box-shadow:
            0 10px 28px rgba(31,41,55,.04),
            inset 3px 0 0 #c88912 !important;
    }

    [data-tracking-sidebar] {
        scroll-margin-top:18px;
    }



    /* Exact selected order tracking */
    a.sari-track-order-trigger {
        cursor:pointer !important;
    }

    a.sari-order-product-image {
        text-decoration:none !important;
    }

    .sari-order-card[data-tracking-selected="true"] {
        border-color:#d9bd82 !important;
        box-shadow:
            0 10px 28px rgba(31,41,55,.04),
            inset 3px 0 0 #c88912 !important;
    }



    /* ============================================================
       FINAL POLISH — INSTANT PRODUCT TRACKING
       Flat, consistent borders + smooth no-reload interaction.
    ============================================================ */

    /* Consistent neutral line system throughout each order card */
    .sari-order-card {
        border:1px solid #dfe5ea !important;
        box-shadow:0 8px 22px rgba(31,41,55,.035) !important;
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease !important;
    }

    .sari-order-card > div:first-child,
    .sari-order-card > .grid.gap-2\.5,
    .sari-order-card > .border-t,
    .sari-order-more-details {
        border-color:#e6ebef !important;
    }

    .sari-order-card .mt-5.flex.flex-col {
        border-top-color:#e6ebef !important;
    }

    .sari-price-box,
    .sari-mini-divider,
    .sari-order-detail-column + .sari-order-detail-column {
        border-color:#e6ebef !important;
        background-color:transparent;
    }

    /* Selected card: quiet neutral border + one precise gold accent */
    .sari-order-card.is-tracking-selected,
    .sari-order-card[data-tracking-selected="true"] {
        border-color:#d6dde4 !important;
        box-shadow:
            0 10px 25px rgba(31,41,55,.045),
            inset 3px 0 0 #c88912 !important;
    }

    /* Avoid the heavy full-card gold outline from earlier iterations */
    .sari-order-card.is-tracking-selected:hover,
    .sari-order-card[data-tracking-selected="true"]:hover {
        border-color:#d1d9e0 !important;
        box-shadow:
            0 12px 28px rgba(31,41,55,.05),
            inset 3px 0 0 #c88912 !important;
    }

    /* Product feels clickable without becoming visually noisy */
    a.sari-track-order-trigger {
        cursor:pointer !important;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            color .16s ease,
            opacity .16s ease !important;
    }

    a.sari-order-product-image:hover,
    a.sari-order-product-image:focus-visible {
        border-color:#c9d1d9 !important;
        box-shadow:0 0 0 3px rgba(31,41,55,.035) !important;
        outline:none !important;
    }

    a.sari-order-product-name:hover,
    a.sari-order-product-name:focus-visible {
        color:#9a6811 !important;
        text-decoration:none !important;
        outline:none !important;
    }

    .sari-order-card.is-tracking-selected a.sari-order-product-image,
    .sari-order-card[data-tracking-selected="true"] a.sari-order-product-image {
        border-color:#d8c08e !important;
    }

    /* Sidebar micro-animation: no spinner, no blocking, no layout shift */
    [data-tracking-sidebar] {
        transform:translateZ(0);
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            opacity .12s ease !important;
    }

    [data-tracking-sidebar].is-tracking-changing {
        opacity:.82;
    }

    [data-tracking-sidebar].is-tracking-updated {
        border-color:#d8dee5 !important;
        box-shadow:0 12px 28px rgba(31,41,55,.05) !important;
    }

    #sidebarTrackingOrderNumber,
    .sari-sidebar .sari-tracking-row,
    .sari-sidebar .sari-tracking-icon,
    .sari-sidebar .sari-tracking-row::after {
        transition:
            color .18s ease,
            background-color .18s ease,
            border-color .18s ease,
            opacity .18s ease !important;
    }

    /* Right sidebar rail uses one consistent neutral + gold system */
    .sari-sidebar .sari-tracking-row:not(:last-child)::after {
        background:#dfe5ea !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete:not(:last-child)::after {
        background:#c88912 !important;
    }

    .sari-sidebar .sari-tracking-icon {
        border-color:#d3dae1 !important;
        background:#fff !important;
        box-shadow:none !important;
    }

    .sari-sidebar .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-sidebar .sari-tracking-row.is-current .sari-tracking-icon {
        border-color:#c88912 !important;
        background:#c88912 !important;
        box-shadow:none !important;
    }

    /* Actions: consistent line weight and calmer hierarchy */
    .sari-order-card > .grid.gap-2\.5 > a {
        border:1px solid #dfe5ea !important;
        background:#fff !important;
        color:#596574 !important;
        box-shadow:none !important;
    }

    .sari-order-card > .grid.gap-2\.5 > a:hover,
    .sari-order-card > .grid.gap-2\.5 > a:focus-visible {
        border-color:#c9d1d9 !important;
        background:#f8fafb !important;
        color:#303a46 !important;
        outline:none !important;
    }

    @media (prefers-reduced-motion:reduce) {
        .sari-order-card,
        a.sari-track-order-trigger,
        [data-tracking-sidebar],
        #sidebarTrackingOrderNumber,
        .sari-sidebar .sari-tracking-row,
        .sari-sidebar .sari-tracking-icon,
        .sari-sidebar .sari-tracking-row::after {
            transition:none !important;
            scroll-behavior:auto !important;
        }
    }



    /* ============================================================
       REALTIME TRACKING — ZERO PAGE RELOAD
       Product selectors are buttons; sidebar updates entirely in-place.
    ============================================================ */

    button.sari-track-order-trigger {
        cursor:pointer !important;
        -webkit-tap-highlight-color:transparent;
    }

    button.sari-order-product-image {
        padding:0 !important;
        border:1px solid #e1e6eb !important;
        background:#fff !important;
        appearance:none !important;
        -webkit-appearance:none !important;
    }

    button.sari-order-product-name {
        display:block !important;
        max-width:100% !important;
        padding:0 !important;
        border:0 !important;
        background:transparent !important;
        appearance:none !important;
        -webkit-appearance:none !important;
        text-align:left !important;
    }

    button.sari-track-order-trigger:focus-visible {
        outline:2px solid rgba(200,137,18,.28) !important;
        outline-offset:2px !important;
    }

    button.sari-track-order-trigger[aria-pressed="true"].sari-order-product-image {
        border-color:#d3b36e !important;
        box-shadow:0 0 0 3px rgba(200,137,18,.06) !important;
    }

    button.sari-track-order-trigger[aria-pressed="true"].sari-order-product-name {
        color:#99620d !important;
    }

    /* Smooth, non-blocking sidebar state change */
    [data-tracking-sidebar] {
        will-change:opacity, transform;
    }

    [data-tracking-sidebar].is-tracking-changing {
        opacity:.78 !important;
        transform:translateY(1px);
    }

    [data-tracking-sidebar].is-tracking-updated {
        opacity:1 !important;
        transform:translateY(0);
    }

    /* Keep rail/dots visually stable during updates */
    .sari-sidebar .sari-tracking-row,
    .sari-sidebar .sari-tracking-icon,
    .sari-sidebar .sari-tracking-row::after {
        transition:
            background-color .16s ease,
            border-color .16s ease,
            color .16s ease,
            opacity .16s ease !important;
    }



    /* ============================================================
       FULL PRODUCT CONTAINER CLICK TARGET
       Entire product preview controls Order Tracking.
    ============================================================ */

    .sari-track-order-container {
        position:relative;
        border-radius:11px;
        cursor:pointer !important;
        transition:
            background-color .16s ease,
            box-shadow .16s ease,
            transform .16s ease !important;
    }

    .sari-track-order-container:hover {
        background:#fafbfc;
    }

    .sari-track-order-container:focus-visible {
        outline:2px solid rgba(200,137,18,.24);
        outline-offset:4px;
    }

    .sari-order-card.is-tracking-selected .sari-track-order-container,
    .sari-order-card[data-tracking-selected="true"] .sari-track-order-container {
        background:#fffdf8;
    }

    .sari-track-order-container .sari-track-order-trigger {
        cursor:pointer !important;
    }

    @media (prefers-reduced-motion:reduce) {
        .sari-track-order-container {
            transition:none !important;
        }
    }



    /* ============================================================
       WHOLE ORDER CARD -> ORDER TRACKING
       Click anywhere on the card except dedicated controls.
    ============================================================ */

    .sari-order-card {
        cursor:pointer !important;
    }

    .sari-order-card a,
    .sari-order-card button,
    .sari-order-card input,
    .sari-order-card select,
    .sari-order-card textarea,
    .sari-order-card summary,
    .sari-order-card label {
        cursor:pointer;
    }

    .sari-order-card:focus-visible {
        outline:2px solid rgba(200,137,18,.24) !important;
        outline-offset:3px !important;
    }

    .sari-order-card:not(.is-tracking-selected):hover {
        border-color:#d4dbe2 !important;
        box-shadow:0 10px 25px rgba(31,41,55,.045) !important;
    }

    /* Keep interactive controls visually independent from card selection. */
    .sari-order-card button:disabled {
        cursor:not-allowed !important;
    }

    @media (prefers-reduced-motion:reduce) {
        .sari-order-card {
            transition:none !important;
        }
    }



    /* ============================================================
       CLEAN DETAIL ICONS
       No icon containers. Latest Update + Courier = black.
       Delivery Address = red.
    ============================================================ */

    .sari-order-detail-icon {
        display:inline-flex !important;
        align-items:center !important;
        justify-content:center !important;

        width:auto !important;
        height:auto !important;
        min-width:0 !important;
        min-height:0 !important;
        flex:0 0 auto !important;

        padding:0 !important;
        margin-top:1px !important;

        border:0 !important;
        border-radius:0 !important;

        background:transparent !important;
        box-shadow:none !important;

        color:#111827 !important;
    }

    .sari-order-detail-icon--alert {
        border:0 !important;
        background:transparent !important;
        color:#111827 !important;
    }

    .sari-order-detail-icon--location {
        color:#ef4444 !important;
    }

    .sari-order-detail-icon .material-symbols-rounded {
        display:block !important;
        font-size:19px !important;
        line-height:1 !important;
        color:currentColor !important;
    }

    .sari-order-detail-row {
        gap:10px !important;
        align-items:flex-start !important;
    }



    /* ============================================================
       MY ORDERS TITLE + SUMMARY STATS POLISH
    ============================================================ */

    .sari-page-title-my {
        color:#111827 !important;
    }

    .sari-page-title-orders {
        color:#c88912 !important;
    }

    .sari-summary-item {
        justify-content:center !important;
        text-align:center !important;
        padding:16px 18px !important;
    }

    .sari-summary-item > div {
        width:100% !important;
    }

    .sari-summary-item p:first-child {
        color:#6f7b88 !important;
        font-size:8.8px !important;
        font-weight:800 !important;
        letter-spacing:.08em !important;
        line-height:1.2 !important;
    }

    .sari-summary-item p:last-child {
        margin-top:5px !important;
        color:#1f2937 !important;
        font-size:22px !important;
        font-weight:800 !important;
        line-height:1 !important;
    }

    .sari-summary-item:last-child p:last-child {
        color:#1f2937 !important;
    }

    @media (max-width:767px) {
        .sari-summary-item {
            padding:14px 12px !important;
        }

        .sari-summary-item p:first-child {
            font-size:8px !important;
        }

        .sari-summary-item p:last-child {
            font-size:20px !important;
        }
    }



    /* ============================================================
       SUMMARY STRIP — CLEAN / FORMAL / MODERN
       Label upper-left, number centered, no colored containers.
    ============================================================ */

    .sari-summary-strip {
        min-height:92px !important;
        border:1px solid #dfe5ea !important;
        border-radius:16px !important;
        background:#ffffff !important;
        box-shadow:0 8px 24px rgba(31,41,55,.035) !important;
        overflow:hidden !important;
    }

    .sari-summary-item {
        position:relative !important;
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
        min-height:92px !important;
        padding:18px 20px !important;
        background:#ffffff !important;
        text-align:left !important;
    }

    .sari-summary-item + .sari-summary-item::before {
        content:"" !important;
        position:absolute !important;
        left:0 !important;
        top:20px !important;
        bottom:20px !important;
        width:1px !important;
        height:auto !important;
        transform:none !important;
        background:#e3e8ed !important;
    }

    .sari-summary-item > div {
        width:100% !important;
        height:100% !important;
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
        position:relative !important;

        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
        padding:0 !important;
    }

    .sari-summary-item p:first-child {
        position:absolute !important;
        top:0 !important;
        left:0 !important;

        margin:0 !important;
        color:#7a8694 !important;
        font-size:8px !important;
        font-weight:800 !important;
        letter-spacing:.08em !important;
        line-height:1.25 !important;
        text-align:left !important;
        text-transform:uppercase !important;
        white-space:normal !important;
    }

    .sari-summary-item p:last-child {
        margin:0 !important;
        color:#111827 !important;
        font-size:25px !important;
        font-weight:800 !important;
        line-height:1 !important;
        letter-spacing:-.03em !important;
        text-align:center !important;
    }

    .sari-summary-item:last-child p:last-child {
        color:#111827 !important;
    }

    /* Kill any leftover summary icon-box styling from older CSS */
    .sari-summary-item > div:first-child:not(:last-child),
    .sari-summary-item .material-symbols-rounded {
        border:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    @media (max-width:767px) {
        .sari-summary-item {
            min-height:82px !important;
            padding:15px 16px !important;
        }

        .sari-summary-item + .sari-summary-item::before {
            top:16px !important;
            bottom:16px !important;
        }

        .sari-summary-item p:first-child {
            font-size:7.4px !important;
        }

        .sari-summary-item p:last-child {
            font-size:22px !important;
        }
    }



    /* ============================================================
       FINAL SUMMARY STRIP — STRUCTURAL CLEANUP
       Dedicated classes beat all legacy icon-box rules.
    ============================================================ */

    .sari-orders-reference .sari-summary-strip {
        min-height:104px !important;
        margin-bottom:16px !important;
        border:1px solid #dfe5ea !important;
        border-radius:16px !important;
        background:#ffffff !important;
        box-shadow:0 8px 24px rgba(31,41,55,.035) !important;
        overflow:hidden !important;
    }

    .sari-orders-reference .sari-summary-item {
        position:relative !important;
        display:block !important;
        min-width:0 !important;
        min-height:104px !important;
        padding:0 !important;
        background:#ffffff !important;
        text-align:left !important;
    }

    /* Remove every legacy icon/container treatment, including higher-specificity rules */
    .sari-orders-reference .sari-summary-item > div:first-child,
    .sari-orders-reference .sari-summary-item > .sari-summary-content {
        position:relative !important;
        display:block !important;
        width:100% !important;
        height:104px !important;
        min-width:0 !important;
        min-height:104px !important;

        margin:0 !important;
        padding:17px 20px !important;

        border:0 !important;
        border-radius:0 !important;
        background:#ffffff !important;
        color:inherit !important;
        box-shadow:none !important;
    }

    .sari-orders-reference .sari-summary-item + .sari-summary-item::before {
        content:"" !important;
        position:absolute !important;
        left:0 !important;
        top:20px !important;
        bottom:20px !important;

        width:1px !important;
        height:auto !important;

        transform:none !important;
        background:#e3e8ed !important;
    }

    .sari-orders-reference .sari-summary-label,
    .sari-orders-reference .sari-summary-item p.sari-summary-label:first-child {
        position:absolute !important;
        top:17px !important;
        left:20px !important;

        margin:0 !important;

        color:#697586 !important;
        font-size:9px !important;
        font-weight:750 !important;
        line-height:1.2 !important;
        letter-spacing:.07em !important;
        text-transform:uppercase !important;
        text-align:left !important;

        background:transparent !important;
        border:0 !important;
        box-shadow:none !important;
    }

    .sari-orders-reference .sari-summary-value,
    .sari-orders-reference .sari-summary-item p.sari-summary-value:last-child {
        position:absolute !important;
        left:50% !important;
        top:57% !important;
        transform:translate(-50%,-50%) !important;

        margin:0 !important;

        color:#111827 !important;
        font-size:28px !important;
        font-weight:800 !important;
        line-height:1 !important;
        letter-spacing:-.04em !important;
        text-align:center !important;

        background:transparent !important;
        border:0 !important;
        box-shadow:none !important;
    }

    /* Never let legacy summary icon styles create boxes again */
    .sari-orders-reference .sari-summary-item .material-symbols-rounded {
        display:none !important;
    }

    @media (max-width:767px) {
        .sari-orders-reference .sari-summary-strip {
            min-height:auto !important;
        }

        .sari-orders-reference .sari-summary-item {
            min-height:92px !important;
        }

        .sari-orders-reference .sari-summary-item > div:first-child,
        .sari-orders-reference .sari-summary-item > .sari-summary-content {
            height:92px !important;
            min-height:92px !important;
            padding:15px 16px !important;
        }

        .sari-orders-reference .sari-summary-label,
        .sari-orders-reference .sari-summary-item p.sari-summary-label:first-child {
            top:15px !important;
            left:16px !important;
            font-size:8px !important;
        }

        .sari-orders-reference .sari-summary-value,
        .sari-orders-reference .sari-summary-item p.sari-summary-value:last-child {
            font-size:25px !important;
        }

        /* 2x2 mobile grid: vertical + horizontal separators */
        .sari-orders-reference .sari-summary-item:nth-child(3)::before {
            display:none !important;
        }

        .sari-orders-reference .sari-summary-item:nth-child(n+3)::after {
            content:"" !important;
            position:absolute !important;
            left:16px !important;
            right:16px !important;
            top:0 !important;
            height:1px !important;
            background:#e3e8ed !important;
        }
    }



    /* ============================================================
       ISOLATED KPI SUMMARY — REFERENCE DESIGN
       New class names avoid all legacy .sari-summary-* cascade rules.
    ============================================================ */

    .sari-orders-reference .sari-kpi-bar {
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        width:100%;
        min-height:128px;
        margin:0 0 16px;

        overflow:hidden;
        border:1px solid #dce3e9;
        border-radius:20px;
        background:#ffffff;

        box-shadow:
            0 14px 34px rgba(15,23,42,.045),
            0 2px 7px rgba(15,23,42,.018);
    }

    .sari-orders-reference .sari-kpi-cell {
        position:relative;
        min-width:0;
        min-height:128px;
        padding:22px 30px;
        background:#ffffff;
    }

    .sari-orders-reference .sari-kpi-cell + .sari-kpi-cell::before {
        content:"";
        position:absolute;
        left:0;
        top:26px;
        bottom:26px;
        width:1px;
        background:#dfe5ea;
    }

    .sari-orders-reference .sari-kpi-label {
        position:absolute;
        top:22px;
        left:30px;

        margin:0;
        color:#607087;

        font-size:10px;
        font-weight:800;
        line-height:1.2;
        letter-spacing:.09em;
        text-transform:uppercase;
        white-space:nowrap;
    }

    .sari-orders-reference .sari-kpi-value {
        position:absolute;
        left:50%;
        top:66%;
        transform:translate(-50%,-50%);

        margin:0;
        color:#0b1424;

        font-size:38px;
        font-weight:800;
        line-height:1;
        letter-spacing:-.05em;
        text-align:center;
    }

    @media (max-width:767px) {
        .sari-orders-reference .sari-kpi-bar {
            grid-template-columns:repeat(2,minmax(0,1fr));
            min-height:auto;
            border-radius:16px;
        }

        .sari-orders-reference .sari-kpi-cell {
            min-height:104px;
            padding:18px 20px;
        }

        .sari-orders-reference .sari-kpi-label {
            top:17px;
            left:20px;
            font-size:8.5px;
        }

        .sari-orders-reference .sari-kpi-value {
            top:67%;
            font-size:31px;
        }

        .sari-orders-reference .sari-kpi-cell + .sari-kpi-cell::before {
            top:20px;
            bottom:20px;
        }

        .sari-orders-reference .sari-kpi-cell:nth-child(3)::before {
            display:none;
        }

        .sari-orders-reference .sari-kpi-cell:nth-child(n+3)::after {
            content:"";
            position:absolute;
            top:0;
            left:20px;
            right:20px;
            height:1px;
            background:#e5eaee;
        }
    }

</style>

<div class="sari-orders-page sari-orders-reference min-h-full">

    <div class="sari-orders-shell mx-auto w-full max-w-[1400px] px-4 py-5 sm:px-6 lg:px-8 lg:py-7">

        @if(session('success'))

            <div class="sari-fade-in mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">

                <span class="material-symbols-rounded text-[20px]">check_circle</span>

                <span>{{ session('success') }}</span>

            </div>

        @endif

        {{-- PREMIUM PAGE HEADING --}}

        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <div class="mb-2 flex items-center gap-2">

                    <span class="h-1 w-7 rounded-full bg-[#d99a18]"></span>

                    <span class="text-[10px] font-bold uppercase tracking-[.2em] text-[#b87908]">SARI Marketplace</span>

                </div>

                <h1 class="text-[28px] font-bold tracking-[-.035em] sm:text-[34px]">
                    <span class="sari-page-title-my">My</span>
                    <span class="sari-page-title-orders">Orders</span>
                </h1>

                <p class="mt-1 text-sm text-[#77736b]">

                    Track, manage, and view all your orders in one place.

                </p>

            </div>

            <a href="{{ route('buyer.products') }}"

               class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#191919] px-5 text-sm font-semibold text-white transition hover:bg-black hover:-translate-y-0.5">

                <span class="material-symbols-rounded text-[19px]">shopping_bag</span>

                Continue Shopping

            </a>

        </div>

        {{-- ORDER SUMMARY --}}
        <section class="sari-kpi-bar" aria-label="Order summary">
            <div class="sari-kpi-cell">
                <p class="sari-kpi-label">Total Orders</p>
                <p class="sari-kpi-value">{{ $orders->count() }}</p>
            </div>

            <div class="sari-kpi-cell">
                <p class="sari-kpi-label">To Ship</p>
                <p class="sari-kpi-value">{{ $toShipCount }}</p>
            </div>

            <div class="sari-kpi-cell">
                <p class="sari-kpi-label">In Transit</p>
                <p class="sari-kpi-value">{{ $inTransitCount }}</p>
            </div>

            <div class="sari-kpi-cell">
                <p class="sari-kpi-label">Delivered</p>
                <p class="sari-kpi-value">{{ $deliveredCount }}</p>
            </div>
        </section>

        <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_310px]">

            {{-- MAIN ORDERS --}}

            <main class="min-w-0">

                {{-- SEARCH + ORDER FILTERS --}}

                <div class="sari-card mb-5 rounded-2xl p-3">

                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

                        <div class="relative min-w-0 flex-1">

                            <span class="material-symbols-rounded pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-[#aaa49a]">search</span>

                            <input id="orderSearch"

                                   type="search"

                                   placeholder="Search order number or status..."

                                   class="sari-search h-10 w-full rounded-xl border border-[#e9e2d6] bg-[#fffdf9] pl-10 pr-4 text-xs font-medium text-[#333] placeholder:text-[#aaa49a]">

                        </div>

                        <div class="flex flex-wrap gap-1.5" id="orderFilters">

                            <button type="button" data-filter="all" class="sari-filter is-active rounded-xl px-3.5 py-2 text-[11px] font-semibold transition">All <span class="opacity-70">{{ $orders->count() }}</span></button>

                            <button type="button" data-filter="to-ship" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">To Ship</button>

                            <button type="button" data-filter="in-transit" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">In Transit</button>

                            <button type="button" data-filter="delivered" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">Delivered</button>

                            <button type="button" data-filter="cancelled" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">Cancelled</button>

                        </div>

                    </div>

                </div>

                <section class="space-y-5">

                    @forelse($orders as $order)

                        @php

                            $currentStep = $statusIndex($order->status);

                        @endphp

                        @php

                            $filterGroup = match (true) {

                                $order->status === 'delivered' => 'delivered',

                                $order->status === 'cancelled' => 'cancelled',

                                in_array($order->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']) => 'in-transit',

                                default => 'to-ship',

                            };

                            $displayTotal = $order->total_amount ?? $order->total ?? null;

                            $latestEvent = $order->events?->sortByDesc('id')->first();

                            $sellerSlug = 'seller-' . (int) $order->seller_account_id;

                            $seller = $order->seller;
                            $storeName = $seller?->store_name ?: 'SARI Seller';
                            $storeLogoUrl = filled($seller?->store_logo_path)
                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($seller->store_logo_path)
                                : null;
                            $storeInitials = collect(preg_split('/\s+/', trim($storeName)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                            $storeInitials = $storeInitials !== '' ? $storeInitials : 'S';

                            $orderItems = collect($order->items ?? [])->values();
                            $firstItem = $orderItems->first() ?? [];
                            $firstProductId = (int) ($firstItem['product_id'] ?? 0);
                            $firstProductImageUrl = $firstProductId > 0
                                ? route('buyer.product.image', ['product' => $firstProductId])
                                : null;
                            $firstProductUrl = $firstProductId > 0
                                ? route('buyer.product.details', ['product' => $firstProductId])
                                : null;
                            $firstProductName = trim((string) ($firstItem['name'] ?? 'Ordered product'));
                            $firstVariantLabel = trim((string) ($firstItem['variant_label'] ?? ''));
                            $firstQty = max(1, (int) ($firstItem['qty'] ?? 1));
                            $extraProductCount = max(0, $orderItems->count() - 1);

                            $trackingUrl = request()->fullUrlWithQuery([
                                'track' => $order->getKey(),
                            ]);

                            $itemCount = $orderItems->sum(fn ($item) => max(1, (int) ($item['qty'] ?? 1)));

                            $statusMessage = match ($order->status) {

                                'new' => 'Your order was placed and is waiting for Seller processing.',

                                'preparing' => 'The Seller is preparing your items.',

                                'ready_for_pickup' => 'Your parcel is ready for Logistics pickup.',

                                'courier_accepted' => 'A rider has been assigned to your parcel.',

                                'heading_pickup' => 'The rider is heading to the Seller pickup point.',

                                'arrived_pickup' => 'The rider has arrived at the Seller pickup point.',

                                'in_transit' => 'Your parcel is on the way.',

                                'arrived_buyer' => 'The rider has arrived at your delivery location.',

                                'delivered' => 'Your order has been delivered.',

                                'cancelled' => 'This order was cancelled.',

                                default => 'Your order status was updated.',

                            };

                        @endphp

                        <article class="sari-card sari-order-card sari-fade-in overflow-hidden rounded-[20px] {{ $featuredOrder && (int) $featuredOrder->getKey() === (int) $order->getKey() ? 'is-tracking-selected' : '' }}"

                                 role="button"

                                 tabindex="0"

                                 aria-label="Show tracking for order {{ $order->order_number }}"

                                 data-status="{{ $filterGroup }}"

                                 data-tracking-selected="{{ $featuredOrder && (int) $featuredOrder->getKey() === (int) $order->getKey() ? 'true' : 'false' }}"

                                 data-order-number="{{ $order->order_number }}"

                                 data-current-step="{{ $currentStep }}"

                                 data-order-status="{{ $order->status }}"

                                 data-search="{{ strtolower($order->order_number . ' ' . $order->statusLabel() . ' ' . $firstProductName . ' ' . $storeName) }}">

                            {{-- ORDER TOP --}}

                            <div class="border-b border-[#eee8dc] px-5 py-5 sm:px-6">

                                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                                    <div class="flex min-w-0 gap-4">

                                        <a
                                            href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}"
                                            class="sari-order-store-avatar"
                                            aria-label="Visit {{ $storeName }}"
                                            title="{{ $storeName }}"
                                        >
                                            @if($storeLogoUrl)
                                                <img
                                                    src="{{ $storeLogoUrl }}"
                                                    alt="{{ $storeName }} store logo"
                                                    width="44"
                                                    height="44"
                                                    loading="lazy"
                                                    decoding="async"
                                                    class="sari-order-store-avatar-image"
                                                    onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                >
                                            @endif

                                            <span class="sari-order-store-avatar-fallback {{ $storeLogoUrl ? 'hidden' : '' }}">
                                                {{ $storeInitials }}
                                            </span>
                                        </a>

                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <h2 class="truncate text-[17px] font-bold tracking-[-.01em] text-[#202020]">

                                                    {{ $order->order_number }}

                                                </h2>

                                                <button type="button"

                                                        class="copy-order-number rounded-lg border border-[#e7e0d5] px-2 py-1 text-[11px] font-medium text-[#77736b] transition hover:border-[#d7b25e] hover:bg-[#fffaf0]"

                                                        data-order-number="{{ $order->order_number }}">

                                                    Copy

                                                </button>

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fff4d9] px-2.5 py-1 text-[11px] font-semibold text-[#ad7204]">

                                                    <span class="h-1.5 w-1.5 rounded-full bg-[#d99a18]"></span>

                                                    {{ $order->statusLabel() }}

                                                </span>

                                            </div>

                                            <p class="mt-1 text-[11px] text-[#929087]">

                                                Placed {{ $order->created_at?->format('M d, Y • h:i A') }}

                                            </p>

                                        </div>

                                    </div>

                                    <div class="flex items-center gap-5 lg:pr-1">

                                        <div>

                                            <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a09b91]">Delivery</p>

                                            <p class="sari-order-delivery-status mt-1 text-[13px] font-bold text-[#c7890b]">{{ $order->statusLabel() }}</p>

                                            <p class="text-[11px] text-[#817d75]">Updated {{ $order->updated_at?->diffForHumans() }}</p>

                                        </div>

                                        <div class="sari-mini-divider"></div>

                                        <div>

                                            <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a09b91]">Payment</p>

                                            <p class="mt-1 text-sm font-bold uppercase text-[#35332f]">{{ $order->payment_method }}</p>

                                        </div>

                                        <button type="button"

                                                class="hidden h-9 w-9 place-items-center rounded-lg text-[#8e887d] transition hover:bg-[#faf7ef] hover:text-[#333] sm:grid"

                                                aria-label="More order options">

                                            <span class="material-symbols-rounded text-[21px]">more_vert</span>

                                        </button>

                                    </div>

                                </div>

                            {{-- PRODUCT PREVIEW + ORDER TOTAL --}}

                            <div class="mt-5 flex flex-col gap-4 border-t border-dashed border-[#eee8dc] pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <div class="min-w-0">

                                    <p class="mb-2 text-[9px] font-bold uppercase tracking-[.14em] text-[#aaa49a]">Items in this order</p>

                                    <div
                                        class="sari-product-strip sari-track-order-container"
                                        role="button"
                                        tabindex="0"
                                        aria-label="Show tracking for {{ $firstProductName }}"
                                        data-track-order="{{ $order->getKey() }}"
                                    >

                                        @if($firstProductUrl)
                                            <button
                                                type="button"
                                                class="sari-product-thumb sari-order-product-image sari-track-order-trigger"
                                                aria-label="Show tracking for {{ $firstProductName }}"
                                                title="Show order tracking"
                                                data-track-order="{{ $order->getKey() }}"
                                            >
                                                @if($firstProductImageUrl)
                                                    <img
                                                        src="{{ $firstProductImageUrl }}"
                                                        alt="{{ $firstProductName }}"
                                                        width="42"
                                                        height="42"
                                                        loading="lazy"
                                                        decoding="async"
                                                        class="h-full w-full object-cover"
                                                        onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                    >
                                                @endif

                                                <span class="sari-order-product-fallback {{ $firstProductImageUrl ? 'hidden' : '' }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                                    </svg>
                                                </span>

                                                @if($extraProductCount > 0)
                                                    <span class="sari-order-product-more">+{{ $extraProductCount }}</span>
                                                @endif
                                            </button>
                                        @else
                                            <div class="sari-product-thumb sari-order-product-image">
                                                <span class="sari-order-product-fallback">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                        @endif

                                        <div class="ml-1 min-w-0">
                                            @if($firstProductUrl)
                                                <button
                                                    type="button"
                                                    class="sari-order-product-name sari-track-order-trigger"
                                                    aria-label="Show tracking for {{ $firstProductName }}"
                                                    title="Show order tracking"
                                                    data-track-order="{{ $order->getKey() }}"
                                                >
                                                    {{ $firstProductName }}
                                                </button>
                                            @else
                                                <p class="sari-order-product-name">{{ $firstProductName }}</p>
                                            @endif

                                            <p class="sari-order-product-meta">
                                                Qty {{ $firstQty }}
                                                @if($firstVariantLabel !== '')
                                                    <span aria-hidden="true">•</span> {{ $firstVariantLabel }}
                                                @endif
                                                @if($extraProductCount > 0)
                                                    <span aria-hidden="true">•</span> +{{ $extraProductCount }} more
                                                @endif
                                            </p>
                                        </div>

                                    </div>

                                </div>

                                <div class="sari-price-box">

                                    <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#aaa49a]">Order Total</p>

                                    @if($displayTotal !== null)

                                        <p class="sari-order-total-price mt-1 text-lg font-bold">₱{{ number_format((float) $displayTotal, 2) }}</p>

                                    @else

                                        <p class="mt-1 text-sm font-semibold text-[#77736b]">See order details</p>

                                    @endif

                                    <p class="mt-0.5 text-[10px] text-[#9a958c]">{{ $order->payment_method }}</p>

                                </div>

                            </div>

                        </div>

                            {{-- ORDER INFORMATION — COLLAPSED BY DEFAULT --}}
                            <details class="sari-order-more-details">
                                <summary class="sari-order-more-summary">
                                    <span class="sari-order-more-label sari-order-more-label--closed">See more details</span>
                                    <span class="sari-order-more-label sari-order-more-label--open">See less details</span>
                                    <span class="material-symbols-rounded sari-order-more-chevron" aria-hidden="true">expand_more</span>
                                </summary>

                                <div class="sari-order-more-content grid lg:grid-cols-3">

                                    <div class="sari-order-detail-column">

                                        <div class="sari-order-detail-row">
                                            <span class="sari-order-detail-icon" aria-hidden="true">
                                                <span class="material-symbols-rounded">notifications</span>
                                            </span>

                                            <div class="min-w-0">
                                                <p class="sari-order-detail-title">Latest Update</p>
                                                <p class="sari-order-detail-primary">
                                                    {{ ($latestEvent?->created_at ?? $order->updated_at)?->format('M d, Y • h:i A') }}
                                                </p>
                                                <p class="sari-order-detail-secondary">
                                                    {{ $latestEvent?->message ?: $statusMessage }}
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="sari-order-detail-column">

                                        <div class="sari-order-detail-row">
                                            <span class="sari-order-detail-icon" aria-hidden="true">
                                                <span class="material-symbols-rounded">local_shipping</span>
                                            </span>

                                            <div class="min-w-0">
                                                <p class="sari-order-detail-title">Courier</p>
                                                <p class="sari-order-detail-primary">
                                                    {{ $order->courier_name ?: 'Waiting for rider assignment' }}
                                                </p>
                                                <p class="sari-order-detail-secondary">
                                                    {{ $order->courier_email ?: 'Courier contact will appear after assignment.' }}
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="sari-order-detail-column">

                                        <div class="sari-order-detail-row">
                                            <span class="sari-order-detail-icon sari-order-detail-icon--location" aria-hidden="true">
                                                <span class="material-symbols-rounded">location_on</span>
                                            </span>

                                            <div class="min-w-0">
                                                <p class="sari-order-detail-title">Delivery Address</p>
                                                <p class="sari-order-detail-secondary sari-order-detail-address">
                                                    {{ $order->buyer_address ?: 'Delivery address unavailable' }}
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </details>

                            {{-- ACTIONS --}}

                            <div class="grid gap-2.5 border-t border-[#eee8dc] bg-[#fffefa] p-4 sm:grid-cols-2 lg:grid-cols-4 sm:p-5">

                                <a href="{{ route('buyer.messages', ['seller' => $order->seller_account_id]) }}"

                                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#dca62d] text-xs font-bold text-[#b87905] transition hover:bg-[#fff8e9]">

                                    <span class="material-symbols-rounded text-[18px]">chat</span>

                                    Message Seller

                                </a>

                                <a href="{{ route('buyer.orders.show', $order) }}"

                                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#dca62d] text-xs font-bold text-[#b87905] transition hover:bg-[#fff8e9]">

                                    <span class="material-symbols-rounded text-[18px]">receipt_long</span>

                                    View Details

                                </a>

                                <a href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}"

                                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#dca62d] text-xs font-bold text-[#b87905] transition hover:bg-[#fff8e9]">

                                    <span class="material-symbols-rounded text-[18px]">storefront</span>

                                    View Seller

                                </a>

                                @if($order->status === 'new')
                                    <form
                                        method="POST"
                                        action="{{ route('buyer.orders.cancel', $order) }}"
                                        class="sari-cancel-order-form"
                                        onsubmit="return confirm('Cancel this order? Reserved stock will be restored and this action cannot be undone.');"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="sari-cancel-order-btn inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl text-xs font-bold transition"
                                        >
                                            <span class="material-symbols-rounded text-[18px]">cancel</span>
                                            Cancel Order
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        class="inline-flex h-[35px] w-full items-center justify-center gap-2 rounded-[8px] border px-3 text-[6.8px] font-extrabold"
                                        disabled
                                        aria-disabled="true"
                                        title="This order can only be cancelled before the seller starts fulfillment."
                                        style="
                                            background-color:#f1f3f5 !important;
                                            border-color:#dfe3e7 !important;
                                            color:#a8afb7 !important;
                                            cursor:not-allowed !important;
                                            pointer-events:none !important;
                                            opacity:1 !important;
                                            box-shadow:none !important;
                                        "
                                    >
                                        <span
                                            class="material-symbols-rounded text-[15px]"
                                            style="color:#a8afb7 !important;"
                                        >cancel</span>
                                        <span style="color:#a8afb7 !important;">Cancel Order</span>
                                    </button>
                                @endif

                            </div>

                            @if($order->status === 'delivered')

                                <div class="border-t border-[#eee8dc] bg-[#fffefa] px-4 pb-4 sm:px-5 sm:pb-5">

                                    @if($order->returnRequest)

                                        <div class="rounded-xl border border-[#eadfc9] bg-[#fffaf2] px-4 py-3">

                                            <p class="text-[9px] font-bold text-[#9a680b]">Return request: {{ ucwords(str_replace('_',' ',$order->returnRequest->status)) }}</p>

                                            <p class="mt-1 text-[8px] leading-4 text-[#7d7468]">{{ $order->returnRequest->seller_response ?: 'Waiting for Seller review.' }}</p>

                                        </div>

                                    @else

                                        <details class="group rounded-xl border border-[#e8dfd0] bg-white">

                                            <summary class="cursor-pointer list-none px-4 py-3 text-[9px] font-bold text-[#a96f06]">Request Return / Refund</summary>

                                            <form method="POST" action="{{ route('buyer.orders.return-request',$order) }}" class="grid gap-2 border-t border-[#eee8dc] p-4 sm:grid-cols-[180px_1fr_auto]">

                                                @csrf

                                                <select name="reason" required class="h-10 rounded-xl border border-[#e4ddd3] px-3 text-[8px]">

                                                    <option value="Damaged item">Damaged item</option>

                                                    <option value="Wrong item">Wrong item</option>

                                                    <option value="Missing item">Missing item</option>

                                                    <option value="Item not as described">Item not as described</option>

                                                    <option value="Other">Other</option>

                                                </select>

                                                <input name="details" maxlength="1200" placeholder="Describe the issue..." class="h-10 rounded-xl border border-[#e4ddd3] px-3 text-[8px]">

                                                <button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">Submit Request</button>

                                            </form>

                                        </details>

                                    @endif

                                </div>

                            @endif

                        </article>

                    @empty

                        <div class="sari-card rounded-[20px] p-12 text-center">

                            <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#fff5dc] text-[#c7890b]">

                                <span class="material-symbols-rounded text-[30px]">shopping_bag</span>

                            </div>

                            <h2 class="mt-4 text-lg font-bold text-[#33312d]">No Orders Yet</h2>

                            <p class="mt-1 text-sm text-[#77736b]">Your purchased products will appear here.</p>

                            <a href="{{ route('buyer.products') }}"

                               class="sari-gold-btn mt-5 inline-flex h-10 items-center rounded-xl px-5 text-sm font-bold text-white">

                                Start Shopping

                            </a>

                        </div>

                    @endforelse

                </section>

                {{-- QUICK FEEDBACK --}}

                @if($featuredOrder && $featuredOrder->status === 'delivered')

                    <section class="sari-delivered-box sari-soft-panel-sm mt-5 rounded-[18px] p-5">

                        <div class="flex items-start gap-3">

                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-[#d99a18] shadow-sm">

                                <span class="material-symbols-rounded text-[21px]">star</span>

                            </div>

                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-[.13em] text-[#8e967f]">Order Complete</p>

                                <h3 class="mt-1 text-sm font-bold text-[#34382f]">How was your experience?</h3>

                                <div class="sari-star-row mt-2 text-lg" aria-label="Rate your order">

                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>

                                </div>

                            </div>

                        </div>

                    </section>

                @endif

                {{-- BUYER PROTECTION --}}

                <section class="sari-soft-panel-sm mt-5 flex flex-col gap-4 rounded-[18px] border border-[#f0dfb7] bg-[#fffaf0] p-5 sm:flex-row sm:items-center">

                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-[#d99a18] shadow-sm">

                        <span class="material-symbols-rounded">verified_user</span>

                    </div>

                    <div class="flex-1">

                        <h3 class="text-sm font-bold text-[#282622]">Buyer Protection</h3>

                        <p class="mt-0.5 text-xs text-[#716d65]">

                            Your order is protected by SARI Buyer Protection.

                        </p>

                    </div>

                    <a href="#" class="text-xs font-bold text-[#b87905] hover:underline">

                        Learn More →

                    </a>

                </section>

            </main>

            {{-- RIGHT SIDEBAR --}}

            <aside class="sari-sidebar space-y-4">

                {{-- ORDER TRACKING --}}

                <div class="sari-sidebar-card overflow-hidden rounded-[20px]" data-tracking-sidebar>

                    <div class="border-b border-[#f0ebe2] px-5 py-5">

                        <div class="flex items-start justify-between">

                            <div>

                                <h2 class="text-[16px] font-bold text-[#25231f]">Order Tracking</h2>

                                <p class="mt-1 text-[11px] text-[#89847b]">Click a product to view its delivery progress</p>

                            </div>

                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-[#fff7e4] text-[#d99a18]">

                                <span class="material-symbols-rounded text-[22px]">location_on</span>

                            </div>

                        </div>

                    </div>

                    @if($featuredOrder)

                        <div class="px-5 py-5">

                            <div class="mb-4 rounded-xl bg-[#fffaf0] px-3 py-2.5">

                                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a39c90]">Current Order</p>

                                <p id="sidebarTrackingOrderNumber" class="mt-1 truncate text-xs font-bold text-[#34312c]">{{ $featuredOrder->order_number }}</p>

                            </div>

                            <div class="space-y-3">

                                @foreach($statusSteps as $index => $step)

                                    <div class="sari-tracking-row {{ $index <= $featuredStep ? 'is-complete' : '' }} {{ $index === $featuredStep ? 'is-current' : '' }}"
                                         data-sidebar-step="{{ $index }}">

                                        <div class="sari-tracking-icon">

                                            <span class="material-symbols-rounded text-[12px]">check</span>

                                        </div>

                                        <div class="min-w-0 pb-2">

                                            <p class="text-xs font-semibold {{ $index === $featuredStep ? 'text-[#a96e03]' : ($index < $featuredStep ? 'text-[#39362f]' : 'text-[#8d887f]') }}">

                                                {{ $step['label'] }}

                                            </p>

                                            <p class="sari-sidebar-step-status mt-0.5 text-[9px] {{ $index <= $featuredStep ? 'text-[#9a958c]' : 'text-[#b1aca3]' }}">
                                                {{ $index < $featuredStep ? 'Completed' : ($index === $featuredStep ? 'Current status' : 'Pending') }}
                                            </p>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                            {{-- LIVE DELIVERY MAP --}}

                            @if($featuredStep >= 2 && $featuredStep < 4)

                                <div class="sari-live-panel mt-5 p-3">

                                    <div class="mb-3 flex items-center justify-between">

                                        <div class="flex items-center gap-2">

                                            <span class="sari-live-dot"></span>

                                            <span class="text-[10px] font-bold uppercase tracking-[.13em] text-[#5f5a51]">Live Delivery</span>

                                        </div>

                                        <span class="text-[9px] font-semibold text-[#9b958b]">Updated now</span>

                                    </div>

                                    <div class="sari-mini-map">

                                        <div class="sari-route"></div>

                                        <div class="sari-map-label courier">Courier</div>

                                        <div class="sari-map-label home">Your location</div>

                                        <div class="sari-map-pin courier">

                                            <span class="material-symbols-rounded text-[16px]">two_wheeler</span>

                                        </div>

                                        <div class="sari-map-pin home">

                                            <span class="material-symbols-rounded text-[16px]">home</span>

                                        </div>

                                    </div>

                                    <div class="mt-3 flex items-center justify-between">

                                        <div>

                                            <p class="text-xs font-bold text-[#34312d]">Juan is on the way</p>

                                            <p class="mt-0.5 text-[10px] text-[#8e887f]">Estimated arrival today</p>

                                        </div>

                                        <a href="#" class="text-[10px] font-bold text-[#b87905] hover:underline">Full Tracking →</a>

                                    </div>

                                </div>

                            @endif

                        </div>

                    @else

                        <div class="px-5 py-8 text-center">

                            <span class="material-symbols-rounded text-3xl text-[#d4b36a]">local_shipping</span>

                            <p class="mt-2 text-xs text-[#77736b]">Your tracking updates will appear here.</p>

                        </div>

                    @endif

                </div>

                {{-- HELP --}}

                <div class="sari-sidebar-card overflow-hidden rounded-[20px]">

                    <div class="px-5 py-5">

                        <div class="flex items-center gap-3">

                            <div class="grid h-11 w-11 place-items-center rounded-xl bg-[#fff5dc] text-[#c7890b]">

                                <span class="material-symbols-rounded text-[23px]">headset_mic</span>

                            </div>

                            <div>

                                <h3 class="text-sm font-bold text-[#292722]">Need Help?</h3>

                                <p class="mt-0.5 text-[10px] text-[#89847b]">We're here for you!</p>

                            </div>

                        </div>

                        <div class="mt-4 divide-y divide-[#f0ebe2]">

                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">

                                <span class="flex items-center gap-2">

                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">chat_bubble_outline</span>

                                    Chat with Support

                                </span>

                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>

                            </a>

                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">

                                <span class="flex items-center gap-2">

                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">assignment_return</span>

                                    Return & Refund Policy

                                </span>

                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>

                            </a>

                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">

                                <span class="flex items-center gap-2">

                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">report_problem</span>

                                    Report an Issue

                                </span>

                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>

                            </a>

                        </div>

                    </div>

                </div>

                {{-- VOUCHER --}}

                <div class="relative overflow-hidden rounded-[20px] bg-[#191919] p-5 text-white shadow-[0_12px_30px_rgba(0,0,0,.10)]">

                    <div class="absolute -right-7 -bottom-9 h-28 w-28 rounded-full border border-[#dca62d]/30"></div>

                    <div class="absolute -right-2 -bottom-4 h-14 w-14 rounded-full border border-[#dca62d]/20"></div>

                    <div class="relative">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-[.16em] text-[#e6b849]">Special Offer</p>

                                <h3 class="mt-2 text-[17px] font-bold">Get 10% Off</h3>

                                <p class="mt-0.5 text-[11px] text-white/55">on your next order.</p>

                            </div>

                            <span class="material-symbols-rounded text-[34px] text-[#e6b849]">confirmation_number</span>

                        </div>

                        <a href="#" class="mt-4 inline-flex h-9 items-center rounded-lg bg-[#e1a62b] px-3.5 text-[11px] font-bold text-white transition hover:bg-[#c88b12]">

                            View Vouchers

                            <span class="material-symbols-rounded ml-1 text-[15px]">arrow_forward</span>

                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </div>

</div>

@push('scripts')

    @vite('resources/js/buyer-orders.js')

    <script>

        document.addEventListener('DOMContentLoaded', () => {

            // Copy order number

            document.querySelectorAll('.copy-order-number').forEach(button => {

                button.addEventListener('click', async () => {

                    const number = button.dataset.orderNumber || '';

                    if (!number) return;

                    try {

                        await navigator.clipboard.writeText(number);

                        const original = button.textContent;

                        button.textContent = '✓ Copied';

                        button.classList.add('bg-[#fff8e9]', 'text-[#b87905]');

                        setTimeout(() => {

                            button.textContent = original;

                            button.classList.remove('bg-[#fff8e9]', 'text-[#b87905]');

                        }, 1200);

                    } catch (error) {}

                });

            });

            // Client-side order search + filtering

            const search = document.getElementById('orderSearch');

            const filters = document.querySelectorAll('.sari-filter');

            const cards = document.querySelectorAll('.sari-order-card');

            let activeFilter = 'all';

            const applyOrderFilter = () => {

                const query = (search?.value || '').trim().toLowerCase();

                cards.forEach(card => {

                    const matchesFilter = activeFilter === 'all' || card.dataset.status === activeFilter;

                    const matchesSearch = !query || (card.dataset.search || '').includes(query);

                    card.classList.toggle('is-hidden', !(matchesFilter && matchesSearch));

                });

            };

            filters.forEach(button => {

                button.addEventListener('click', () => {

                    activeFilter = button.dataset.filter || 'all';

                    filters.forEach(item => {

                        item.classList.toggle('is-active', item === button);

                        if (item !== button) {

                            item.classList.add('text-[#77736b]');

                        } else {

                            item.classList.remove('text-[#77736b]');

                        }

                    });

                    applyOrderFilter();

                });

            });

            search?.addEventListener('input', applyOrderFilter);

            // Product click -> show that order's progress in the right sidebar.
            const trackingSidebar = document.querySelector('[data-tracking-sidebar]');
            const trackingOrderNumber = document.getElementById('sidebarTrackingOrderNumber');
            const trackingRows = [...document.querySelectorAll('[data-sidebar-step]')];

            const selectOrderForTracking = (card, shouldScroll = true) => {
                if (!card || !trackingSidebar || !trackingOrderNumber || !trackingRows.length) return;

                const currentStep = Number(card.dataset.currentStep || 0);
                const orderNumber = card.dataset.orderNumber || '';

                trackingOrderNumber.textContent = orderNumber;

                trackingRows.forEach((row) => {
                    const step = Number(row.dataset.sidebarStep || 0);
                    const isCompleted = step < currentStep;
                    const isCurrent = step === currentStep;
                    const isReached = step <= currentStep;

                    row.classList.toggle('is-complete', isReached);
                    row.classList.toggle('is-current', isCurrent);

                    const label = row.querySelector('p.text-xs');
                    if (label) {
                        label.classList.remove(
                            'text-[#a96e03]',
                            'text-[#39362f]',
                            'text-[#8d887f]'
                        );

                        label.classList.add(
                            isCurrent
                                ? 'text-[#a96e03]'
                                : (isCompleted ? 'text-[#39362f]' : 'text-[#8d887f]')
                        );
                    }

                    const status = row.querySelector('.sari-sidebar-step-status');
                    if (status) {
                        status.textContent = isCompleted
                            ? 'Completed'
                            : (isCurrent ? 'Current status' : 'Pending');

                        status.classList.toggle('text-[#9a958c]', isReached);
                        status.classList.toggle('text-[#b1aca3]', !isReached);
                    }
                });

                cards.forEach((orderCard) => {
                    const isSelected = orderCard === card;
                    orderCard.classList.toggle('is-tracking-selected', isSelected);
                    orderCard.dataset.trackingSelected = isSelected ? 'true' : 'false';

                    orderCard.querySelectorAll('.sari-track-order-trigger').forEach((trigger) => {
                        trigger.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                    });
                });

                // On tablet/mobile the sidebar is below the order list,
                // so bring the selected order tracking into view.
                if (shouldScroll && window.matchMedia('(max-width: 1180px)').matches) {
                    trackingSidebar.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            };

            // Instant product selection: update tracking without reloading the page.
            const activateOrderTracking = (card, shouldScroll = true) => {
                if (!card || !trackingSidebar) return;

                trackingSidebar.classList.remove('is-tracking-updated');
                trackingSidebar.classList.add('is-tracking-changing');

                selectOrderForTracking(card, false);

                requestAnimationFrame(() => {
                    trackingSidebar.classList.remove('is-tracking-changing');
                    trackingSidebar.classList.add('is-tracking-updated');

                    window.setTimeout(() => {
                        trackingSidebar.classList.remove('is-tracking-updated');
                    }, 260);
                });

                if (shouldScroll && window.matchMedia('(max-width: 1180px)').matches) {
                    trackingSidebar.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            };

            // Whole order card selects tracking.
            // Real controls inside the card keep their original actions.
            document.addEventListener('click', (event) => {
                const card = event.target.closest('.sari-order-card');
                if (!card) return;

                const interactive = event.target.closest(
                    'a, button, input, select, textarea, summary, details, form, label'
                );

                // Product image/name are allowed to select tracking.
                // Other buttons/links keep their own behavior and do not select the card.
                if (interactive && !interactive.classList.contains('sari-track-order-trigger')) {
                    return;
                }

                activateOrderTracking(card, true);
            });

            // Keyboard selection when the order card itself has focus.
            document.addEventListener('keydown', (event) => {
                const card = event.target.closest('.sari-order-card');
                if (!card || event.target !== card) return;

                if (event.key !== 'Enter' && event.key !== ' ') return;

                event.preventDefault();
                activateOrderTracking(card, true);
            });

            // Keep the server-selected order visible in Order Tracking.
            const initialTrackingCard =
                document.querySelector('.sari-order-card[data-tracking-selected="true"]') ||
                document.querySelector('.sari-order-card');

            if (initialTrackingCard) {
                selectOrderForTracking(initialTrackingCard, false);
            }

        });

    </script>

@endpush

@endsection
