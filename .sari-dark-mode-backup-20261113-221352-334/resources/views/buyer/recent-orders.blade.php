@extends('layouts.buyer')

@section('title', 'Recent Orders — SARI')
@section('page-title', 'Recent Orders')

@section('content')
@include('components.buyer.header')

@php
    $statusClass = static function (?string $status): string {
        return match ($status) {
            'delivered' => 'is-delivered',
            'cancelled' => 'is-cancelled',
            'in_transit', 'arrived_buyer', 'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'is-shipping',
            default => 'is-active',
        };
    };

    $returnLabel = static function (?string $status): string {
        return match ($status) {
            'requested' => 'Waiting for Seller review',
            'approved' => 'Return approved',
            'rejected' => 'Request rejected',
            'returned' => 'Returned item received',
            'refunded' => 'Refund recorded',
            'closed' => 'Closed',
            default => str((string) $status)->replace('_', ' ')->title()->toString(),
        };
    };

    $totalOrders = method_exists($recentOrders, 'total')
        ? $recentOrders->total()
        : $recentOrders->count();
@endphp

<div data-sari-recent-orders-page class="mx-auto w-full">
    <style>
        [data-sari-recent-orders-page] {
            --sari-gold:#c88912;
            --sari-gold-dark:#a96f0e;
            --sari-text:#252b31;
            --sari-muted:#747d87;
            --sari-line:#e6e9ed;
            --sari-soft:#f5f6f7;
            width:100%;
            max-width:1480px;
            margin-inline:auto;
            padding:22px 24px 44px;
            color:var(--sari-text);
            font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        }

        .sari-orders-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; padding:2px 2px 14px; }
        .sari-orders-eyebrow { margin:0; color:#a87316; font-size:10.5px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
        .sari-orders-title { margin:5px 0 0; color:#2e343b; font-size:clamp(27px,2.25vw,31px); font-weight:760; line-height:1.04; letter-spacing:-.035em; }
        .sari-orders-title span { color:var(--sari-gold); }
        .sari-orders-all-link {
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

        .sari-orders-all-arrow {
            display:inline-block;
            margin-left:0 !important;
            color:currentColor;
            font-size:18px !important;
            font-weight:800 !important;
            line-height:.8 !important;
            transform:translateY(-1px);
        }

        .sari-orders-all-link:hover,
        .sari-orders-all-link:focus-visible {
            color:#c88912 !important;
            background:transparent !important;
            outline:none !important;
            text-decoration:none !important;
            box-shadow:none !important;
        }

        .sari-orders-alert { display:flex; align-items:flex-start; gap:10px; margin:0 0 12px; border:1px solid #dfe4e8; border-radius:12px; background:#fff; padding:12px 14px; color:#4d5863; font-size:11.5px; line-height:1.5; }
        .sari-orders-alert.is-success { border-color:#dbe9df; color:#416f55; }
        .sari-orders-alert.is-error { border-color:#f0dddd; color:#b64040; }

        .sari-orders-shell {
            overflow:visible;
            border:0;
            border-radius:0;
            background:transparent;
            box-shadow:none;
        }

        .sari-orders-toolbar {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-bottom:12px;
            padding:2px 2px 4px;
            border:0;
            border-radius:0;
            background:transparent;
        }

        .sari-orders-toolbar h2 {
            margin:0;
            color:#303740;
            font-size:15px;
            font-weight:800;
            line-height:1.3;
        }

        .sari-orders-count {
            color:#7d8792;
            font-size:10.5px;
            font-weight:650;
            line-height:1.35;
        }
        .sari-orders-list { display:grid; gap:14px; }

        /* Seller-inspired floating surface: top-level order cards only. */
        .sari-order-card {
            position:relative;
            overflow:hidden;
            border:1px solid #e5e7eb;
            border-radius:17px;
            background:#fff;
            box-shadow:
                0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.095),
                inset 0 1px 0 rgba(255,255,255,.92);
            transition:border-color .16s ease;
        }
        .sari-order-card:hover { border-color:#d5dbe0; }

        .sari-order-card-head {
            display:flex;
            min-height:50px;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            padding:0 17px;
            border-bottom:1px solid #edf0f2;
        }
        .sari-order-store { display:flex; min-width:0; align-items:center; gap:8px; color:#252b31; font-size:13px; font-weight:800; }
        .sari-order-store-badge { display:inline-flex; align-items:center; border:1px solid #e4c27d; border-radius:6px; background:#fff8e8; padding:3px 6px; color:#94630f; font-size:8.5px; font-weight:850; letter-spacing:.04em; line-height:1; text-transform:uppercase; }
        .sari-order-store-name { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .sari-order-head-link { display:inline-flex; align-items:center; gap:5px; color:#737d87; font-size:10.5px; font-weight:650; text-decoration:none; }
        .sari-order-head-link:hover,.sari-order-head-link:focus-visible { color:var(--sari-gold-dark); outline:none; }

        .sari-order-status { display:flex; align-items:center; gap:7px; color:#555f69; font-size:12px; font-weight:760; white-space:nowrap; }
        .sari-order-status-dot { width:7px; height:7px; border-radius:999px; background:#b9c0c7; }
        .sari-order-status.is-active .sari-order-status-dot { background:#b7862c; }
        .sari-order-status.is-shipping .sari-order-status-dot { background:#7c8791; }
        .sari-order-status.is-delivered .sari-order-status-dot { background:#5d8c6e; }
        .sari-order-status.is-cancelled .sari-order-status-dot { background:#cf5a5a; }

        .sari-order-progress {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin:12px 16px 0;
            border:1px solid #e7eaed;
            border-radius:12px;
            background:#f7f8f9;
            padding:10px 12px;
        }
        .sari-order-progress-main { display:flex; min-width:0; align-items:center; gap:10px; }
        .sari-order-progress-icon { display:grid; width:30px; height:30px; flex:0 0 30px; place-items:center; color:#68737d; }
        .sari-order-progress-icon svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:1.7; stroke-linecap:round; stroke-linejoin:round; }
        .sari-order-progress-copy { min-width:0; }
        .sari-order-progress-title { margin:0; color:#4b555f; font-size:13px; font-weight:800; line-height:1.4; }
        .sari-order-progress-text { overflow:visible; margin:3px 0 0; color:#747e88; font-size:11.5px; font-weight:520; line-height:1.5; white-space:normal; }
        .sari-order-progress-arrow { color:#8d969f; font-size:18px; line-height:1; }

        .sari-order-product-area { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:24px; align-items:center; padding:15px 17px 13px; }
        .sari-order-product { display:flex; min-width:0; align-items:center; gap:13px; }
        .sari-order-thumb { display:grid; width:72px; height:72px; flex:0 0 72px; place-items:center; overflow:hidden; border:1px solid #e1e5e9; border-radius:11px; background:#f2f4f6; color:#9aa3ad; font-size:9px; font-weight:800; }
        .sari-order-thumb img { width:100%; height:100%; object-fit:cover; }
        .sari-order-main { min-width:0; }
        .sari-order-name { overflow:hidden; margin:0; color:#20262c; font-size:14.5px; font-weight:760; line-height:1.4; text-overflow:ellipsis; white-space:nowrap; }
        .sari-order-meta,.sari-order-submeta { display:flex; min-width:0; flex-wrap:wrap; gap:5px; margin-top:4px; color:#717b86; font-size:11.5px; line-height:1.45; }
        .sari-order-meta span:not(:last-child)::after,.sari-order-submeta span:not(:last-child)::after { content:'·'; margin-left:5px; color:#a9b0b7; }
        .sari-order-submeta { color:#818a94; font-size:10.75px; }
        .sari-order-product-price { text-align:right; }
        .sari-order-product-price span { display:block; color:#7b8590; font-size:11px; font-weight:600; }
        .sari-order-product-price strong { display:block; margin-top:3px; color:#3b4249; font-size:14.5px; font-weight:800; }
        .sari-order-return-state { display:inline-flex; align-items:center; margin-left:7px; color:#8a6a2c; font-size:10.5px; font-weight:700; }

        .sari-order-footer { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; padding:0 17px 14px; }
        .sari-order-number { color:#818a94; font-size:11px; font-weight:600; }
        .sari-order-summary-actions { display:flex; align-items:center; justify-content:flex-end; gap:8px; flex-wrap:wrap; }
        .sari-order-total-wrap { margin-right:5px; text-align:right; }
        .sari-order-total-label { color:#68727c; font-size:11px; font-weight:600; }
        .sari-order-total { margin-left:4px; color:#343b42; font-size:16px; font-weight:850; white-space:nowrap; }
        .sari-order-action { display:inline-flex; min-height:38px; align-items:center; justify-content:center; border:1px solid #dfe3e7; border-radius:11px; background:#f4f5f6; padding:0 15px; color:#363e46; cursor:pointer; font:inherit; font-size:10.5px; font-weight:780; text-decoration:none; transition:background-color .14s ease,border-color .14s ease,color .14s ease; }
        .sari-order-action:hover,.sari-order-action:focus-visible { border-color:#d3d8dd; background:#eceeef; color:#20262c; outline:none; }
        .sari-order-action.is-primary { border-color:var(--sari-gold); background:var(--sari-gold); color:#fff; }
        .sari-order-action.is-primary:hover,.sari-order-action.is-primary:focus-visible { border-color:var(--sari-gold-dark); background:var(--sari-gold-dark); color:#fff; }

        .sari-quick-review { display:flex; align-items:center; justify-content:space-between; gap:14px; margin:0 16px 15px; border:1px solid #e6e9ec; border-radius:12px; background:#f7f8f9; padding:10px 12px; }
        .sari-quick-review-copy { min-width:0; }
        .sari-quick-review-label { margin:0; color:#535e69; font-size:12.5px; font-weight:800; }
        .sari-quick-review-help { margin:2px 0 0; color:#75808a; font-size:11.5px; line-height:1.5; }
        .sari-quick-stars { display:flex; align-items:center; gap:2px; }
        .sari-quick-star { display:grid; width:31px; height:31px; place-items:center; border:0; border-radius:8px; background:transparent; color:#aeb5bc; cursor:pointer; font-size:24px; line-height:1; transition:background-color .12s ease,color .12s ease,transform .12s ease; }
        .sari-quick-star:hover,.sari-quick-star:focus-visible { background:#eceff1; color:var(--sari-gold); outline:none; transform:translateY(-1px); }
        .sari-quick-star.is-filled { color:var(--sari-gold); }

        .sari-orders-pagination { display:flex; align-items:center; justify-content:center; gap:6px; padding:28px 12px 6px; }
        .sari-page-link { display:grid; min-width:39px; height:36px; place-items:center; border:1px solid transparent; border-radius:12px; background:#f3f4f6; color:#252a30; font-size:11px; font-weight:750; text-decoration:none; transition:background-color .14s ease,border-color .14s ease,color .14s ease; }
        .sari-page-link:hover,.sari-page-link:focus-visible { background:#e6e8eb; outline:none; }
        .sari-page-link.is-current { border-color:var(--sari-gold); background:var(--sari-gold); color:#fff; }
        .sari-page-link.is-disabled { color:#a9afb6; cursor:not-allowed; opacity:.7; }
        .sari-page-link svg { width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:1.8; }

        /* The empty list is a standalone floating surface, not a nested card. */
        .sari-empty {
            display:grid;
            min-height:390px;
            place-items:center;
            border:1px solid #e5e7eb;
            border-radius:17px;
            background:#fff;
            padding:60px 24px;
            text-align:center;
            box-shadow:
                0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.095),
                inset 0 1px 0 rgba(255,255,255,.92);
        }
        .sari-empty h2 { margin:0; color:#8f98a1; font-size:20px; }
        .sari-empty p { margin:7px 0 0; color:#abb2b9; font-size:11px; }
        .sari-empty a { display:inline-flex; height:44px; align-items:center; margin-top:20px; border-radius:11px; background:var(--sari-gold); padding:0 22px; color:#fff; font-size:10.5px; font-weight:800; text-decoration:none; }

        .sari-action-dialog {
            position:fixed;
            top:50%;
            left:50%;
            width:min(680px,calc(100vw - 36px));
            max-height:calc(100vh - 36px);
            margin:0;
            overflow-x:hidden;
            overflow-y:auto;
            border:1px solid #dde2e6;
            border-radius:24px;
            background:#fff;
            padding:0;
            color:var(--sari-text);
            box-shadow:0 32px 88px rgba(22,29,37,.24);
            transform:translate(-50%,-50%);
            scrollbar-width:none;
            -ms-overflow-style:none;
        }

        .sari-action-dialog::-webkit-scrollbar {
            width:0;
            height:0;
            display:none;
        }
        .sari-action-dialog::backdrop { background:rgba(22,29,37,.42); backdrop-filter:blur(3px); }
        .sari-dialog-head {
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:18px;
            padding:22px 24px 18px;
            border-bottom:1px solid #eceff2;
        }
        .sari-dialog-eyebrow { margin:0; color:#a87316; font-size:12px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .sari-dialog-title { margin:6px 0 0; font-size:24px; font-weight:820; line-height:1.16; letter-spacing:-.03em; }
        .sari-dialog-copy { margin:7px 0 0; color:#747e88; font-size:14px; line-height:1.5; }
        .sari-dialog-close {
            display:grid;
            width:46px;
            height:46px;
            place-items:center;
            border:0;
            border-radius:14px;
            background:#f2f3f5;
            color:#4f5963;
            cursor:pointer;
            font-size:24px;
        }
        .sari-dialog-body {
            max-height:none;
            overflow:visible;
            padding:20px 24px 24px;
        }
        .sari-review-card,
        .sari-return-card {
            border:0;
            border-radius:0;
            background:transparent;
            padding:0;
        }
        .sari-review-card + .sari-review-card {
            margin-top:20px;
            padding-top:20px;
            border-top:1px solid #eceff2;
        }
        .sari-review-product { display:flex; align-items:center; gap:11px; }
        .sari-review-product-image { width:48px; height:48px; flex:0 0 48px; overflow:hidden; border:1px solid #e3e6e9; border-radius:10px; background:#f4f5f6; }
        .sari-review-product-image img { width:100%; height:100%; object-fit:cover; }
        .sari-review-product-name { margin:0; font-size:14px; font-weight:760; line-height:1.3; }
        .sari-review-product-meta { margin:3px 0 0; color:#7f8892; font-size:11px; line-height:1.35; }
        .sari-review-form { margin-top:13px; padding-top:13px; border-top:1px solid #eceff1; }
        .sari-field-label { display:block; margin-bottom:6px; color:#4b5560; font-size:12px; font-weight:760; line-height:1.3; }
        .sari-field,.sari-textarea,.sari-select { width:100%; border:1px solid #dfe3e7; border-radius:11px; background:#fff; color:#2c333a; font:inherit; font-size:12.5px; outline:none; transition:border-color .14s ease,box-shadow .14s ease; }
        .sari-field,.sari-select { height:42px; padding:0 12px; }
        .sari-textarea { min-height:96px; resize:vertical; padding:11px 12px; line-height:1.5; }
        .sari-field:focus,.sari-textarea:focus,.sari-select:focus { border-color:#d1a14a; box-shadow:0 0 0 3px rgba(200,137,18,.09); }
        .sari-star-picker { display:flex; width:max-content; flex-direction:row-reverse; justify-content:flex-end; gap:2px; }
        .sari-star-picker input { position:absolute; opacity:0; pointer-events:none; }
        .sari-star-picker label { color:#d4d8dc; cursor:pointer; font-size:28px; line-height:1; transition:color .12s ease,transform .12s ease; }
        .sari-star-picker label:hover,.sari-star-picker label:hover ~ label,.sari-star-picker input:checked ~ label { color:var(--sari-gold); }
        .sari-star-picker label:hover { transform:translateY(-1px); }
        .sari-primary-btn { display:inline-flex; min-height:42px; align-items:center; justify-content:center; border:1px solid var(--sari-gold); border-radius:10px; background:var(--sari-gold); padding:0 18px; color:#fff; cursor:pointer; font:inherit; font-size:12px; font-weight:800; }
        .sari-primary-btn:hover,.sari-primary-btn:focus-visible { border-color:var(--sari-gold-dark); background:var(--sari-gold-dark); outline:none; }
        .sari-secondary-btn { display:inline-flex; min-height:42px; align-items:center; justify-content:center; border:1px solid #dfe3e7; border-radius:10px; background:#fff; padding:0 18px; color:#38414a; cursor:pointer; font:inherit; font-size:12px; font-weight:750; }
        .sari-form-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:11px; }
        .sari-seller-reply { margin-top:10px; border-left:3px solid #dfbd77; background:#faf8f3; padding:10px 11px; color:#625b52; font-size:11.5px; line-height:1.5; }
        /* Review composer — light web interpretation of the mobile reference. */
        .sari-review-card { padding:0; overflow:visible; }
        .sari-review-product { padding:14px 15px; border-bottom:1px solid #edf0f2; background:#fff; }
        .sari-review-product-image { width:58px; height:58px; flex:0 0 58px; }
        .sari-review-product-name { font-size:15px; }
        .sari-review-product-meta { margin-top:4px; font-size:11.5px; }
        .sari-review-form { margin:0; padding:16px 15px 15px; border-top:0; }
        .sari-review-rating-block { display:grid; place-items:center; padding:2px 0 16px; text-align:center; }
        .sari-review-rating-title { margin:0 0 9px; color:#4b555f; font-size:12px; font-weight:780; }
        .sari-star-picker { gap:5px; }
        .sari-star-picker label { font-size:38px; color:#cfd4d9; }
        .sari-review-rating-label { min-height:22px; margin:8px 0 0; color:var(--sari-gold-dark); font-size:13px; font-weight:800; }
        .sari-review-section-title { margin:0 0 8px; color:#343b42; font-size:13px; font-weight:800; }
        .sari-review-quality-chip { display:inline-flex; align-items:center; min-height:30px; margin-bottom:9px; border:1px solid #dfe3e7; border-radius:9px; background:#fff; padding:0 10px; color:#59636d; font-size:10.5px; font-weight:700; }
        .sari-review-textarea-wrap { position:relative; }
        .sari-review-textarea-wrap .sari-textarea { min-height:118px; padding:12px 13px 30px; }
        .sari-review-counter { position:absolute; right:11px; bottom:9px; color:#9aa2aa; font-size:9.5px; font-weight:650; pointer-events:none; }
        .sari-review-media-section { margin-top:15px; }
        .sari-review-upload {
            display:grid;
            min-height:96px;
            place-items:center;
            border:1px dashed #d7dde2;
            border-radius:14px;
            background:#f7f8f9;
            padding:0;
            color:#aeb6be;
            cursor:pointer;
            transition:border-color .14s ease,background-color .14s ease,color .14s ease;
        }
        .sari-review-upload:hover,.sari-review-upload:focus-within {
            border-color:#cfd5db;
            background:#f1f3f4;
            color:#939ca5;
        }
        .sari-review-upload-icon { display:contents; }
        .sari-review-upload-icon svg {
            width:30px;
            height:30px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.75;
            stroke-linecap:round;
            stroke-linejoin:round;
        }
        .sari-review-upload-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); clip-path:inset(50%); white-space:nowrap; }
        .sari-review-media-grid { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:8px; margin-top:9px; }
        .sari-review-media-item { position:relative; aspect-ratio:1; overflow:hidden; border:1px solid #e0e4e8; border-radius:10px; background:#f3f5f6; }
        .sari-review-media-item img { width:100%; height:100%; object-fit:cover; }
        .sari-review-media-remove { position:absolute; top:5px; right:5px; display:grid; width:24px; height:24px; place-items:center; border:1px solid rgba(255,255,255,.82); border-radius:999px; background:rgba(28,34,40,.78); color:#fff; cursor:pointer; font-size:14px; line-height:1; box-shadow:0 2px 7px rgba(0,0,0,.14); }
        .sari-review-media-empty { display:none; }
        .sari-review-media-help { margin:7px 0 0; color:#929aa3; font-size:9.5px; line-height:1.45; }
        .sari-review-submit { width:100%; min-height:46px; margin-top:15px; border-radius:11px; }
        .sari-review-verified { display:flex; align-items:center; gap:6px; margin-top:10px; color:#858e97; font-size:9.5px; }
        .sari-review-verified svg { width:15px; height:15px; fill:none; stroke:#5d8c6e; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .sari-return-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; margin-bottom:12px; }
        .sari-return-summary div { border:1px solid #e4e7ea; border-radius:11px; background:#fafbfc; padding:10px; }
        .sari-return-summary span { display:block; color:#858e98; font-size:10px; font-weight:720; text-transform:uppercase; letter-spacing:.055em; }
        .sari-return-summary strong { display:block; margin-top:4px; color:#2f363d; font-size:13px; line-height:1.3; }
        .sari-return-status { display:inline-flex; align-items:center; border:1px solid #dfc78f; border-radius:999px; background:#fffaf0; padding:6px 10px; color:#8d620f; font-size:11px; font-weight:780; }
        .sari-return-timeline { margin-top:14px; border-top:1px solid #eceff1; padding-top:12px; }
        .sari-return-event { position:relative; padding:0 0 12px 18px; color:#6d7781; font-size:11.5px; line-height:1.5; }
        .sari-return-event::before { content:''; position:absolute; top:5px; left:1px; width:7px; height:7px; border-radius:999px; background:#b88a33; }
        .sari-return-event:not(:last-child)::after { content:''; position:absolute; top:13px; bottom:0; left:4px; width:1px; background:#e2e5e8; }
        .sari-return-event strong { display:block; color:#343b42; font-size:12px; line-height:1.35; }
        .sari-help { margin:8px 0 0; color:#818a94; font-size:11.5px; line-height:1.5; }

        .sari-review-card { padding:0; overflow:hidden; }
        .sari-review-product {
            padding:0 0 18px;
            border-bottom:1px solid #eceff2;
            background:transparent;
        }
        .sari-review-product-image { width:64px; height:64px; flex:0 0 64px; border-radius:12px; }
        .sari-review-product-name { font-size:16px; }
        .sari-review-product-meta { margin-top:4px; font-size:12px; }
        .sari-review-form { margin:0; padding:18px 0 0; border-top:0; }
        .sari-review-rating-title { margin:0 0 11px; color:#4b555f; font-size:13px; font-weight:780; }
        .sari-star-picker label { font-size:40px; }
        .sari-review-rating-label { min-height:24px; margin:9px 0 0; color:var(--sari-gold-dark); font-size:14px; font-weight:800; }
        .sari-review-section-title { margin:0 0 10px; color:#343b42; font-size:15px; font-weight:800; }
        .sari-review-quality-chip { min-height:34px; margin-bottom:10px; border-radius:10px; padding:0 12px; font-size:11.5px; }
        .sari-review-textarea-wrap .sari-textarea { min-height:132px; padding:14px 14px 32px; }
        .sari-review-counter { right:12px; bottom:10px; font-size:10.5px; }
        .sari-review-media-section { margin-top:18px; }
        .sari-review-media-grid { gap:10px; margin-top:10px; }
        .sari-review-media-item { border-radius:12px; }
        .sari-review-media-help { margin:8px 0 0; font-size:10px; }
        .sari-review-submit {
            min-height:48px;
            margin-top:18px;
            border-radius:12px;
            background:var(--sari-gold);
            border-color:var(--sari-gold);
            color:#fff;
        }
        .sari-review-verified { margin-top:11px; font-size:10.5px; }

        .sari-return-card { padding:0; }
        .sari-return-product {
            display:grid;
            grid-template-columns:78px minmax(0,1fr) auto;
            gap:16px;
            align-items:center;
            margin-bottom:18px;
            padding:0 0 18px;
            border:0;
            border-bottom:1px solid #eceff2;
            border-radius:0;
            background:transparent;
        }
        .sari-return-product-image {
            width:78px;
            height:78px;
            overflow:hidden;
            border:1px solid #e2e6ea;
            border-radius:14px;
            background:#f3f5f6;
        }
        .sari-return-product-image img {
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .sari-return-product-title {
            margin:0;
            color:#2d343b;
            font-size:16px;
            font-weight:780;
            line-height:1.35;
        }
        .sari-return-product-meta {
            margin:4px 0 0;
            color:#7b848e;
            font-size:12px;
            line-height:1.45;
        }
        .sari-return-product-price {
            text-align:right;
        }
        .sari-return-product-price span {
            display:block;
            color:#8a939d;
            font-size:11px;
            font-weight:650;
        }
        .sari-return-product-price strong {
            display:block;
            margin-top:3px;
            color:#2f363d;
            font-size:18px;
            font-weight:820;
        }
        .sari-return-summary {
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:12px;
            margin-bottom:16px;
        }
        .sari-return-summary div {
            border:1px solid #dfe4e8;
            border-radius:12px;
            background:#fff;
            padding:13px 14px;
        }
        .sari-return-summary span {
            display:block;
            color:#7e8792;
            font-size:12px;
            font-weight:760;
            text-transform:uppercase;
            letter-spacing:.05em;
        }
        .sari-return-summary strong {
            display:block;
            margin-top:6px;
            color:#2f363d;
            font-size:16px;
            line-height:1.32;
        }
        .sari-return-status {
            border:1px solid #e2c98f;
            border-radius:999px;
            background:#fffaf0;
            padding:6px 10px;
            color:#8d620f;
            font-size:12px;
            font-weight:780;
        }
        .sari-field-label {
            margin-bottom:8px;
            font-size:14px;
            font-weight:760;
        }
        .sari-field,.sari-textarea,.sari-select {
            font-size:14px;
        }
        .sari-select,
        .sari-field {
            height:48px;
        }
        .sari-textarea {
            min-height:128px;
            padding:14px 14px;
        }
        .sari-form-actions {
            gap:10px;
            margin-top:20px;
            padding-top:16px;
            border-top:1px solid #eceff2;
        }
        .sari-primary-btn,
        .sari-secondary-btn {
            min-height:48px;
            border-radius:12px;
            padding:0 22px;
            font-size:13px;
        }
        .sari-primary-btn {
            border-color:var(--sari-gold);
            background:var(--sari-gold);
            color:#fff;
            box-shadow:0 4px 12px rgba(200,137,18,.14);
        }
        .sari-primary-btn:hover,
        .sari-primary-btn:focus-visible {
            border-color:var(--sari-gold-dark);
            background:var(--sari-gold-dark);
            color:#fff;
        }
        .sari-secondary-btn {
            border-color:#dfe3e7;
            background:#f5f6f7;
            color:#3e464f;
        }
        .sari-secondary-btn:hover,
        .sari-secondary-btn:focus-visible {
            border-color:#d4d9de;
            background:#eceff1;
            color:#252c33;
        }

        .sari-custom-select {
            position:relative;
        }
        .sari-custom-select-trigger {
            display:flex;
            width:100%;
            min-height:52px;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            border:1px solid #dfe3e7;
            border-radius:14px;
            background:#fff;
            padding:0 14px 0 16px;
            color:#2c333a;
            cursor:pointer;
            font:inherit;
            font-size:14px;
            text-align:left;
            transition:border-color .14s ease,box-shadow .14s ease,background-color .14s ease;
        }
        .sari-custom-select-trigger:hover,
        .sari-custom-select-trigger:focus-visible {
            border-color:#d1a14a;
            outline:none;
            box-shadow:0 0 0 3px rgba(200,137,18,.09);
        }
        .sari-custom-select-trigger.is-invalid {
            border-color:#d26a6a;
            box-shadow:0 0 0 3px rgba(210,106,106,.08);
        }
        .sari-custom-select-value.is-placeholder {
            color:#8c959e;
        }
        .sari-custom-select-trigger svg {
            width:18px;
            height:18px;
            flex:0 0 auto;
            fill:none;
            stroke:#616b75;
            stroke-width:1.9;
            stroke-linecap:round;
            stroke-linejoin:round;
            transition:transform .14s ease;
        }
        .sari-custom-select.is-open .sari-custom-select-trigger svg {
            transform:rotate(180deg);
        }
        .sari-custom-select-menu {
            position:absolute;
            z-index:45;
            top:calc(100% + 8px);
            left:0;
            right:0;
            overflow:hidden;
            border:1px solid #dfe3e7;
            border-radius:14px;
            background:#fff;
            box-shadow:0 18px 42px rgba(22,29,37,.14);
            padding:8px;
        }
        .sari-custom-select-menu[hidden] {
            display:none;
        }
        .sari-custom-select-option {
            display:flex;
            width:100%;
            min-height:44px;
            align-items:center;
            border:0;
            border-radius:10px;
            background:#fff;
            padding:0 12px;
            color:#303740;
            cursor:pointer;
            font:inherit;
            font-size:14px;
            text-align:left;
            transition:background-color .12s ease,color .12s ease;
        }
        .sari-custom-select-option:hover,
        .sari-custom-select-option:focus-visible,
        .sari-custom-select-option.is-selected {
            background:#f4f6f8;
            color:#1e252c;
            outline:none;
        }
        .sari-return-reason-error {
            margin:8px 0 0;
            color:#c05353;
            font-size:11.5px;
            line-height:1.4;
        }

        @media (max-width:980px) {
            .sari-order-product-area { gap:16px; }
            .sari-order-footer { align-items:flex-start; flex-direction:column; }
            .sari-order-summary-actions { width:100%; justify-content:flex-end; }
        }
        @media (max-width:700px) {
            [data-sari-recent-orders-page] { padding:16px 14px 30px; }
            .sari-orders-head { align-items:flex-start; flex-direction:column; }
            .sari-orders-all-link {
                top:-8px !important;
                font-size:10.5px !important;
            }

            .sari-orders-all-arrow {
                font-size:16px !important;
            }
            .sari-orders-toolbar { padding:2px 2px 4px; }
            .sari-orders-list { gap:11px; }
            .sari-order-card,
            .sari-empty {
                border-radius:14px;
                box-shadow:
                    0 1px 3px rgba(15,23,42,.035),
                    0 7px 18px rgba(15,23,42,.06),
                    0 15px 32px rgba(15,23,42,.065),
                    inset 0 1px 0 rgba(255,255,255,.90);
            }
            .sari-order-card-head { min-height:46px; padding:0 13px; }
            .sari-order-store-badge { display:none; }
            .sari-order-status { font-size:11px; }
            .sari-order-progress { margin:10px 12px 0; padding:9px 10px; }
            .sari-order-progress-text { max-width:68vw; }
            .sari-order-product-area { grid-template-columns:1fr; gap:10px; padding:13px 13px 10px; }
            .sari-order-thumb { width:58px; height:58px; flex-basis:58px; border-radius:10px; }
            .sari-order-product-price { padding-left:71px; text-align:left; }
            .sari-order-footer { padding:0 13px 12px; }
            .sari-order-summary-actions { justify-content:flex-start; }
            .sari-order-total-wrap { width:100%; margin:0 0 2px; text-align:left; }
            .sari-order-action { min-height:36px; padding:0 13px; }
            .sari-quick-review { margin:0 12px 12px; align-items:flex-start; flex-direction:column; }
            .sari-quick-stars { width:100%; justify-content:flex-start; }
            .sari-quick-star { width:34px; height:34px; }
            .sari-order-name { font-size:13.5px; }
            .sari-order-meta { font-size:11px; }
            .sari-order-submeta { font-size:10.5px; }
            .sari-return-product {
                grid-template-columns:62px minmax(0,1fr);
                gap:12px;
            }
            .sari-return-product-image {
                width:62px;
                height:62px;
            }
            .sari-return-product-price {
                grid-column:2;
                text-align:left;
            }
            .sari-return-product-price strong {
                font-size:16px;
            }
            .sari-return-summary { grid-template-columns:1fr; }
            .sari-action-dialog {
                width:calc(100vw - 20px);
                max-height:calc(100vh - 20px);
                border-radius:18px;
            }
            .sari-dialog-head,.sari-dialog-body { padding-left:16px; padding-right:16px; }
            .sari-orders-pagination { gap:4px; padding-top:22px; }
            .sari-page-link { min-width:34px; height:34px; border-radius:10px; }
        }
    </style>

    <header class="sari-orders-head">
        <div>
            <p class="sari-orders-eyebrow">SARI Marketplace</p>
            <h1 class="sari-orders-title">Recent <span>Orders</span></h1>
        </div>
        <a href="{{ route('buyer.orders') }}" class="sari-orders-all-link" wire:navigate.hover>
            View all orders <span class="sari-orders-all-arrow" aria-hidden="true">&gt;</span>
        </a>
    </header>

    <section class="sari-orders-shell">
        <header class="sari-orders-toolbar">
            <h2>Latest purchases</h2>
            <span class="sari-orders-count">{{ number_format($totalOrders) }} order{{ $totalOrders === 1 ? '' : 's' }}</span>
        </header>

        @if($recentOrders->isEmpty())
            <div class="sari-empty">
                <div>
                    <h2>No recent orders yet</h2>
                    <p>Your latest purchases will appear here after checkout.</p>
                    <a href="{{ route('buyer.products') }}" wire:navigate.hover>Browse Products</a>
                </div>
            </div>
        @else
            <div class="sari-orders-list">
                @foreach($recentOrders as $order)
                    @php
                        $items = collect($order->items ?? [])->values();
                        $firstItem = $items->first() ?? [];
                        $productId = (int) ($firstItem['product_id'] ?? 0);
                        $productName = trim((string) ($firstItem['name'] ?? 'Ordered product')) ?: 'Ordered product';
                        $productQty = max(1, (int) ($firstItem['qty'] ?? 1));
                        $variantLabel = trim((string) ($firstItem['variant_label'] ?? ''));
                        $moreItems = max(0, $items->count() - 1);
                        $productUnitPrice = (float) (
                            $firstItem['buyer_price']
                            ?? $firstItem['unit_price']
                            ?? $firstItem['price']
                            ?? 0
                        );
                        $storeName = trim((string) ($order->seller?->store_name ?? 'SARI Seller')) ?: 'SARI Seller';
                        $reviewsByProduct = $order->reviews->keyBy('seller_product_id');
                        $reviewableItems = $items
                            ->filter(fn ($item) => (int) ($item['product_id'] ?? 0) > 0)
                            ->unique(fn ($item) => (int) ($item['product_id'] ?? 0))
                            ->values();
                        $reviewedCount = $reviewableItems
                            ->filter(fn ($item) => $reviewsByProduct->has((int) ($item['product_id'] ?? 0)))
                            ->count();
                        $canReview = $order->status === 'delivered' && $reviewableItems->isNotEmpty();
                        $returnRequest = $order->returnRequest;
                        $reviewDialogId = 'review-order-' . $order->getKey();
                        $returnDialogId = 'return-order-' . $order->getKey();
                        $firstReviewableItem = $reviewableItems->first() ?? [];
                        $firstReviewableProductId = (int) ($firstReviewableItem['product_id'] ?? 0);
                        $quickReview = $firstReviewableProductId > 0
                            ? $reviewsByProduct->get($firstReviewableProductId)
                            : null;
                        $quickRating = (int) ($quickReview?->rating ?? 0);
                    @endphp

                    <article
                        class="sari-order-card"
                        data-order-row
                        data-order-url="{{ route('buyer.orders.show', $order) }}"
                    >
                        <header class="sari-order-card-head">
                            <div class="sari-order-store">
                                <span class="sari-order-store-name">{{ $storeName }}</span>
                                <a href="{{ route('buyer.orders.show', $order) }}" class="sari-order-head-link" aria-label="View order {{ $order->order_number }}" wire:navigate.hover>
                                    <span aria-hidden="true">›</span>
                                </a>
                            </div>

                            <div class="sari-order-status {{ $statusClass($order->status) }}">
                                <span class="sari-order-status-dot" aria-hidden="true"></span>
                                <span>{{ $order->statusLabel() }}</span>
                                @if($returnRequest)
                                    <span class="sari-order-return-state">· {{ $returnLabel($returnRequest->status) }}</span>
                                @endif
                            </div>
                        </header>

                        <a href="{{ route('buyer.orders.show', $order) }}" class="sari-order-progress" style="text-decoration:none;" wire:navigate.hover>
                            <div class="sari-order-progress-main">
                                <span class="sari-order-progress-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.4"></circle><circle cx="18" cy="18" r="1.4"></circle></svg>
                                </span>
                                <div class="sari-order-progress-copy">
                                    <p class="sari-order-progress-title">{{ $order->statusLabel() }}</p>
                                    <p class="sari-order-progress-text">
                                        @if($order->status === 'delivered')
                                            Your order has been delivered. You can now review the item or request return/refund support.
                                        @elseif($order->status === 'cancelled')
                                            This order was cancelled. Open the order to review the details.
                                        @else
                                            Your order is moving through fulfillment. Open it to see the latest tracking details.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="sari-order-progress-arrow" aria-hidden="true">›</span>
                        </a>

                        <div class="sari-order-product-area">
                            <div class="sari-order-product">
                                <div class="sari-order-thumb" aria-hidden="true">
                                    @if($productId > 0)
                                        <img src="{{ route('buyer.product.image', ['product' => $productId]) }}" alt="" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                                        <span style="display:none;">SARI</span>
                                    @else
                                        <span>SARI</span>
                                    @endif
                                </div>

                                <div class="sari-order-main">
                                    <h2 class="sari-order-name">{{ $productName }}</h2>
                                    <div class="sari-order-meta">
                                        <span>Qty {{ $productQty }}</span>
                                        @if($variantLabel !== '')<span>{{ $variantLabel }}</span>@endif
                                        @if($moreItems > 0)<span>+{{ $moreItems }} more item{{ $moreItems === 1 ? '' : 's' }}</span>@endif
                                    </div>
                                    <div class="sari-order-submeta">
                                        <span>{{ optional($order->created_at)->format('M d, Y') }}</span>
                                        @if($canReview)<span>{{ $reviewedCount }}/{{ $reviewableItems->count() }} reviewed</span>@endif
                                    </div>
                                </div>
                            </div>

                            <div class="sari-order-product-price">
                                <span>Order amount</span>
                                <strong>₱{{ number_format((float) $order->total, 2) }}</strong>
                            </div>
                        </div>

                        <div class="sari-order-footer">
                            <div class="sari-order-number">Order {{ $order->order_number }}</div>

                            <div class="sari-order-summary-actions">
                                <div class="sari-order-total-wrap">
                                    <span class="sari-order-total-label">Total:</span>
                                    <strong class="sari-order-total">₱{{ number_format((float) $order->total, 2) }}</strong>
                                </div>

                                <a href="{{ route('buyer.orders.show', $order) }}" class="sari-order-action is-primary" wire:navigate.hover>View order</a>

                                @if($order->status === 'delivered')
                                    <button type="button" class="sari-order-action" data-dialog-open="{{ $returnDialogId }}">
                                        {{ $returnRequest ? 'Return status' : 'Request refund' }}
                                    </button>

                                    @if($canReview)
                                        <button type="button" class="sari-order-action is-primary" data-dialog-open="{{ $reviewDialogId }}">
                                            {{ $reviewedCount > 0 ? 'Edit review' : 'Write review' }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($canReview)
                            <div class="sari-quick-review">
                                <div class="sari-quick-review-copy">
                                    <p class="sari-quick-review-label">{{ $quickRating > 0 ? 'Your quick rating' : 'Quick review' }}</p>
                                    <p class="sari-quick-review-help">{{ $quickRating > 0 ? 'Click a star to update your rating and review.' : 'Choose a star to start your review.' }}</p>
                                </div>

                                <div class="sari-quick-stars" aria-label="Quick rating">
                                    @for($quickStar = 1; $quickStar <= 5; $quickStar++)
                                        <button
                                            type="button"
                                            class="sari-quick-star {{ $quickRating >= $quickStar ? 'is-filled' : '' }}"
                                            data-dialog-open="{{ $reviewDialogId }}"
                                            data-quick-rating="{{ $quickStar }}"
                                            data-rating-input="{{ 'rating-' . $order->getKey() . '-' . $firstReviewableProductId . '-' . $quickStar }}"
                                            aria-label="Rate {{ $quickStar }} star{{ $quickStar === 1 ? '' : 's' }}"
                                        >{{ $quickRating >= $quickStar ? '★' : '☆' }}</button>
                                    @endfor
                                </div>
                            </div>
                        @endif
                    </article>

                    @if($canReview)
                        <dialog id="{{ $reviewDialogId }}" class="sari-action-dialog" data-action-dialog>
                            <div class="sari-dialog-head">
                                <div>
                                    <p class="sari-dialog-eyebrow">Verified purchase</p>
                                    <h2 class="sari-dialog-title">Write a review</h2>
                                    <p class="sari-dialog-copy">{{ $order->order_number }} · Rate each purchased product and optionally add photos.</p>
                                </div>
                                <button type="button" class="sari-dialog-close" data-dialog-close aria-label="Close">×</button>
                            </div>

                            <div class="sari-dialog-body">
                                @foreach($reviewableItems as $reviewItem)
                                    @php
                                        $reviewProductId = (int) ($reviewItem['product_id'] ?? 0);
                                        $reviewProductName = trim((string) ($reviewItem['name'] ?? 'Ordered product')) ?: 'Ordered product';
                                        $reviewProductQty = max(1, (int) ($reviewItem['qty'] ?? 1));
                                        $reviewVariant = trim((string) ($reviewItem['variant_label'] ?? ''));
                                        $existingReview = $reviewsByProduct->get($reviewProductId);
                                        $currentRating = (int) ($existingReview?->rating ?? 0);
                                        $existingImagePaths = collect((array) ($existingReview?->image_paths ?? []))
                                            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
                                            ->values();
                                        $starGroup = 'rating-' . $order->getKey() . '-' . $reviewProductId;
                                        $commentId = 'comment-' . $order->getKey() . '-' . $reviewProductId;
                                        $imageInputId = 'review-images-' . $order->getKey() . '-' . $reviewProductId;
                                    @endphp

                                    <div class="sari-review-card">
                                        <div class="sari-review-product">
                                            <div class="sari-review-product-image">
                                                <img src="{{ route('buyer.product.image', ['product' => $reviewProductId]) }}" alt="{{ $reviewProductName }}" loading="lazy">
                                            </div>
                                            <div class="min-w-0">
                                                <p class="sari-review-product-name">{{ $reviewProductName }}</p>
                                                <p class="sari-review-product-meta">Qty {{ $reviewProductQty }}@if($reviewVariant !== '') · {{ $reviewVariant }}@endif</p>
                                            </div>
                                        </div>

                                        <form
                                            method="POST"
                                            action="{{ route('buyer.orders.reviews.store', $order) }}"
                                            class="sari-review-form"
                                            enctype="multipart/form-data"
                                            data-review-form
                                        >
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $reviewProductId }}">

                                            <div class="sari-review-rating-block">
                                                <p class="sari-review-rating-title">How was the product?</p>
                                                <div class="sari-star-picker" aria-label="Rating from 1 to 5 stars" data-rating-picker>
                                                    @for($star = 5; $star >= 1; $star--)
                                                        <input
                                                            id="{{ $starGroup }}-{{ $star }}"
                                                            type="radio"
                                                            name="rating"
                                                            value="{{ $star }}"
                                                            @checked($currentRating === $star)
                                                            @if($star === 1) required @endif
                                                        >
                                                        <label for="{{ $starGroup }}-{{ $star }}" aria-label="{{ $star }} star{{ $star === 1 ? '' : 's' }}">★</label>
                                                    @endfor
                                                </div>
                                                <p class="sari-review-rating-label" data-rating-label data-current-rating="{{ $currentRating }}"></p>
                                            </div>

                                            <section>
                                                <h3 class="sari-review-section-title">Share your thoughts</h3>
                                                <span class="sari-review-quality-chip">Product quality</span>

                                                <div class="sari-review-textarea-wrap">
                                                    <textarea
                                                        id="{{ $commentId }}"
                                                        name="comment"
                                                        maxlength="1500"
                                                        class="sari-textarea"
                                                        placeholder="What did you like or dislike about this product?"
                                                        data-review-comment
                                                    >{{ $existingReview?->comment }}</textarea>
                                                    <span class="sari-review-counter" data-review-counter>0/1500</span>
                                                </div>
                                            </section>

                                            <section class="sari-review-media-section">
                                                <h3 class="sari-review-section-title">Add photos</h3>

                                                <label class="sari-review-upload" for="{{ $imageInputId }}" aria-label="Add review photos">
                                                    <span class="sari-review-upload-icon" aria-hidden="true">
                                                        <svg viewBox="0 0 24 24">
                                                            <path d="M4 8h3l1.4-2h7.2L17 8h3v10H4z"></path>
                                                            <circle cx="12" cy="13" r="3.2"></circle>
                                                        </svg>
                                                    </span>
                                                </label>

                                                <input
                                                    id="{{ $imageInputId }}"
                                                    type="file"
                                                    name="images[]"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    multiple
                                                    class="sari-review-upload-input"
                                                    data-review-images-input
                                                >

                                                <div class="sari-review-media-grid" data-review-media-grid>
                                                    @foreach($existingImagePaths as $imagePath)
                                                        <div class="sari-review-media-item" data-existing-review-image>
                                                            <img
                                                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}"
                                                                alt="Review photo"
                                                                loading="lazy"
                                                            >
                                                            <button
                                                                type="button"
                                                                class="sari-review-media-remove"
                                                                data-existing-review-image-remove
                                                                data-image-path="{{ $imagePath }}"
                                                                aria-label="Remove review photo"
                                                            >×</button>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div data-removed-image-fields></div>
                                                <p class="sari-review-media-help">Photos help the Seller and other Buyers understand your experience. Only images are supported for now.</p>
                                            </section>

                                            @if($existingReview?->sellerReply)
                                                <div class="sari-seller-reply">
                                                    <strong>Seller reply</strong><br>
                                                    {{ $existingReview->sellerReply->reply }}
                                                </div>
                                            @endif

                                            <div class="sari-review-verified">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
                                                <span>Your review is linked to this delivered SARI order.</span>
                                            </div>

                                            <button type="submit" class="sari-primary-btn sari-review-submit">
                                                {{ $existingReview ? 'Update review' : 'Submit review' }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </dialog>
                    @endif

                    @if($order->status === 'delivered')
                        <dialog id="{{ $returnDialogId }}" class="sari-action-dialog" data-action-dialog>
                            <div class="sari-dialog-head">
                                <div>
                                    <p class="sari-dialog-eyebrow">Buyer protection</p>
                                    <h2 class="sari-dialog-title">Return / Refund</h2>
                                    <p class="sari-dialog-copy">{{ $order->order_number }} · This request applies to the Seller order, not a single review item.</p>
                                </div>
                                <button type="button" class="sari-dialog-close" data-dialog-close aria-label="Close">×</button>
                            </div>

                            <div class="sari-dialog-body">
                                @if($returnRequest)
                                    <div class="sari-return-card">
                                        <div class="sari-return-product">
                                            <div class="sari-return-product-image" aria-hidden="true">
                                                @if($productId > 0)
                                                    <img
                                                        src="{{ route('buyer.product.image', ['product' => $productId]) }}"
                                                        alt="{{ $productName }}"
                                                        loading="lazy"
                                                    >
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <p class="sari-return-product-title">{{ $productName }}</p>
                                                <p class="sari-return-product-meta">
                                                    Qty {{ $productQty }}
                                                    @if($variantLabel !== '') · {{ $variantLabel }} @endif
                                                    @if($moreItems > 0) · +{{ $moreItems }} more item{{ $moreItems === 1 ? '' : 's' }} @endif
                                                </p>
                                            </div>

                                            <div class="sari-return-product-price">
                                                <span>Product price</span>
                                                <strong>₱{{ number_format($productUnitPrice > 0 ? $productUnitPrice : (float) $order->subtotal, 2) }}</strong>
                                            </div>
                                        </div>

                                        <div class="sari-return-summary">
                                            <div><span>Status</span><strong>{{ $returnLabel($returnRequest->status) }}</strong></div>
                                            <div><span>Requested</span><strong>₱{{ number_format((float) $returnRequest->requested_amount, 2) }}</strong></div>
                                            <div><span>Approved</span><strong>{{ $returnRequest->approved_amount !== null ? '₱'.number_format((float) $returnRequest->approved_amount, 2) : 'Pending' }}</strong></div>
                                        </div>

                                        <span class="sari-return-status">{{ $returnLabel($returnRequest->status) }}</span>

                                        <p class="sari-help"><strong>Reason:</strong> {{ $returnRequest->reason }}</p>
                                        @if($returnRequest->details)
                                            <p class="sari-help"><strong>Your details:</strong> {{ $returnRequest->details }}</p>
                                        @endif
                                        @if($returnRequest->seller_response)
                                            <div class="sari-seller-reply"><strong>Seller response</strong><br>{{ $returnRequest->seller_response }}</div>
                                        @endif

                                        @php
                                            $returnEvents = $returnRequest->events->sortBy('created_at');
                                        @endphp
                                        @if($returnEvents->isNotEmpty())
                                            <div class="sari-return-timeline">
                                                @foreach($returnEvents as $returnEvent)
                                                    <div class="sari-return-event">
                                                        <strong>{{ $returnEvent->title }}</strong>
                                                        {{ $returnEvent->message }}
                                                        <div>{{ optional($returnEvent->created_at)->format('M d, Y · h:i A') }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <p class="sari-help">A recorded refund uses SARI’s existing internal payment ledger. External bank/e-wallet transfer is not represented by this status unless a payment provider is integrated separately.</p>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('buyer.orders.return-request', $order) }}" class="sari-return-card" data-return-form>
                                        @csrf

                                        <div class="sari-return-product">
                                            <div class="sari-return-product-image" aria-hidden="true">
                                                @if($productId > 0)
                                                    <img
                                                        src="{{ route('buyer.product.image', ['product' => $productId]) }}"
                                                        alt="{{ $productName }}"
                                                        loading="lazy"
                                                    >
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <p class="sari-return-product-title">{{ $productName }}</p>
                                                <p class="sari-return-product-meta">
                                                    Qty {{ $productQty }}
                                                    @if($variantLabel !== '') · {{ $variantLabel }} @endif
                                                    @if($moreItems > 0) · +{{ $moreItems }} more item{{ $moreItems === 1 ? '' : 's' }} @endif
                                                </p>
                                            </div>

                                            <div class="sari-return-product-price">
                                                <span>Product price</span>
                                                <strong>₱{{ number_format($productUnitPrice > 0 ? $productUnitPrice : (float) $order->subtotal, 2) }}</strong>
                                            </div>
                                        </div>

                                        <div class="sari-return-summary">
                                            <div><span>Order</span><strong>{{ $order->order_number }}</strong></div>
                                            <div><span>Seller</span><strong>{{ $storeName }}</strong></div>
                                            <div><span>Requested amount</span><strong>₱{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                                        </div>

                                        <label class="sari-field-label" for="reason-hidden-{{ $order->getKey() }}">Reason</label>

                                        <div class="sari-custom-select" data-custom-select>
                                            <input
                                                id="reason-hidden-{{ $order->getKey() }}"
                                                type="hidden"
                                                name="reason"
                                                value=""
                                                data-return-reason
                                            >

                                            <button
                                                type="button"
                                                class="sari-custom-select-trigger"
                                                data-return-select-trigger
                                                aria-haspopup="listbox"
                                                aria-expanded="false"
                                            >
                                                <span class="sari-custom-select-value is-placeholder" data-return-select-value>
                                                    Select a reason
                                                </span>

                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="m6 9 6 6 6-6"></path>
                                                </svg>
                                            </button>

                                            <div class="sari-custom-select-menu" data-return-select-menu hidden role="listbox">
                                                <button type="button" class="sari-custom-select-option" data-return-select-option data-value="Damaged item">Damaged item</button>
                                                <button type="button" class="sari-custom-select-option" data-return-select-option data-value="Wrong item">Wrong item</button>
                                                <button type="button" class="sari-custom-select-option" data-return-select-option data-value="Missing item">Missing item</button>
                                                <button type="button" class="sari-custom-select-option" data-return-select-option data-value="Item not as described">Item not as described</button>
                                                <button type="button" class="sari-custom-select-option" data-return-select-option data-value="Other">Other</button>
                                            </div>
                                        </div>

                                        <p class="sari-return-reason-error" data-return-reason-error hidden>
                                            Please select a reason before submitting your request.
                                        </p>

                                        <label class="sari-field-label" for="return-details-{{ $order->getKey() }}" style="margin-top:14px;">Details</label>
                                        <textarea
                                            id="return-details-{{ $order->getKey() }}"
                                            name="details"
                                            maxlength="1200"
                                            class="sari-textarea"
                                            placeholder="Describe the issue clearly so the Seller can review it."
                                        ></textarea>

                                        <p class="sari-help">The current backend requests the merchandise subtotal for this Seller order. The Seller must review the request before the return can move to returned/refunded status.</p>

                                        <div class="sari-form-actions">
                                            <button type="button" class="sari-secondary-btn" data-dialog-close>Cancel</button>
                                            <button type="submit" class="sari-primary-btn">Submit request</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </dialog>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    @if(method_exists($recentOrders, 'hasPages') && $recentOrders->hasPages())
        @php
            $currentPage = $recentOrders->currentPage();
            $lastPage = $recentOrders->lastPage();
            $pageStart = max(1, min($currentPage - 4, max(1, $lastPage - 9)));
            $pageEnd = min($lastPage, $pageStart + 9);
        @endphp
        <nav class="sari-orders-pagination" aria-label="Recent orders pages">
            @if($recentOrders->onFirstPage())
                <span class="sari-page-link is-disabled" aria-disabled="true">
                    <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"></path></svg>
                </span>
            @else
                <a href="{{ $recentOrders->previousPageUrl() }}" class="sari-page-link" aria-label="Previous page">
                    <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"></path></svg>
                </a>
            @endif

            @for($page = $pageStart; $page <= $pageEnd; $page++)
                <a href="{{ $recentOrders->url($page) }}" class="sari-page-link {{ $page === $currentPage ? 'is-current' : '' }}" @if($page === $currentPage) aria-current="page" @endif>{{ $page }}</a>
            @endfor

            @if($recentOrders->hasMorePages())
                <a href="{{ $recentOrders->nextPageUrl() }}" class="sari-page-link" aria-label="Next page">
                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"></path></svg>
                </a>
            @else
                <span class="sari-page-link is-disabled" aria-disabled="true">
                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"></path></svg>
                </span>
            @endif
        </nav>
    @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
    if (window.__SARI_RECENT_ORDERS_ACTIONS_V2__) return;
    window.__SARI_RECENT_ORDERS_ACTIONS_V2__ = true;

    const closeMenus = (except = null) => {
        document.querySelectorAll('[data-order-menu]').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;
            menu.closest('.sari-order-menu-wrap')?.querySelector('[data-order-menu-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    };

    const ratingLabels = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very good',
        5: 'Excellent',
    };

    const refreshRatingLabel = (form) => {
        const checked = form?.querySelector('input[name="rating"]:checked');
        const label = form?.querySelector('[data-rating-label]');
        if (!label) return;
        const value = Number(checked?.value || label.dataset.currentRating || 0);
        label.textContent = ratingLabels[value] || 'Select your rating';
    };

    const refreshCommentCounter = (textarea) => {
        const counter = textarea?.closest('.sari-review-textarea-wrap')?.querySelector('[data-review-counter]');
        if (!counter) return;
        const max = Number(textarea.maxLength || 1500);
        counter.textContent = `${textarea.value.length}/${max}`;
    };

    const selectedImageFiles = new WeakMap();

    const closeReturnSelectMenus = (except = null) => {
        document.querySelectorAll('[data-return-select-menu]').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;

            const root = menu.closest('[data-custom-select]');
            root?.classList.remove('is-open');
            root?.querySelector('[data-return-select-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    };

    document.querySelectorAll('[data-return-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const hidden = form.querySelector('[data-return-reason]');
            const trigger = form.querySelector('[data-return-select-trigger]');
            const error = form.querySelector('[data-return-reason-error]');

            if (!hidden?.value) {
                event.preventDefault();
                trigger?.classList.add('is-invalid');
                if (error) error.hidden = false;
                trigger?.focus();
            }
        });
    });

    const renderNewReviewImages = (input) => {
        const form = input.closest('[data-review-form]');
        const grid = form?.querySelector('[data-review-media-grid]');
        if (!form || !grid) return;

        grid.querySelectorAll('[data-new-review-image]').forEach((node) => node.remove());

        const files = selectedImageFiles.get(input) || Array.from(input.files || []);
        files.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'sari-review-media-item';
            item.dataset.newReviewImage = '';

            const image = document.createElement('img');
            image.alt = file.name;
            image.src = URL.createObjectURL(file);
            image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once:true });

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'sari-review-media-remove';
            remove.setAttribute('aria-label', 'Remove selected photo');
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                const nextFiles = (selectedImageFiles.get(input) || []).filter((_, fileIndex) => fileIndex !== index);
                selectedImageFiles.set(input, nextFiles);

                const transfer = new DataTransfer();
                nextFiles.forEach((selected) => transfer.items.add(selected));
                input.files = transfer.files;
                renderNewReviewImages(input);
            });

            item.append(image, remove);
            grid.appendChild(item);
        });
    };

    document.querySelectorAll('[data-review-form]').forEach((form) => {
        refreshRatingLabel(form);

        const textarea = form.querySelector('[data-review-comment]');
        refreshCommentCounter(textarea);
        textarea?.addEventListener('input', () => refreshCommentCounter(textarea));

        form.querySelectorAll('input[name="rating"]').forEach((input) => {
            input.addEventListener('change', () => refreshRatingLabel(form));
        });

        const fileInput = form.querySelector('[data-review-images-input]');
        fileInput?.addEventListener('change', () => {
            const visibleExisting = form.querySelectorAll('[data-existing-review-image]:not([hidden])').length;
            const selected = Array.from(fileInput.files || []);
            const allowed = Math.max(0, 5 - visibleExisting);

            if (selected.length > allowed) {
                window.SariBuyerToast?.error(`You can attach up to 5 photos. You can add ${allowed} more.`);
            }

            const kept = selected.slice(0, allowed);
            selectedImageFiles.set(fileInput, kept);

            const transfer = new DataTransfer();
            kept.forEach((file) => transfer.items.add(file));
            fileInput.files = transfer.files;
            renderNewReviewImages(fileInput);
        });

        form.querySelectorAll('[data-existing-review-image-remove]').forEach((button) => {
            button.addEventListener('click', () => {
                const item = button.closest('[data-existing-review-image]');
                const path = button.dataset.imagePath;
                const fields = form.querySelector('[data-removed-image-fields]');

                if (!item || !path || !fields) return;
                item.hidden = true;

                if (!fields.querySelector(`input[value="${CSS.escape(path)}"]`)) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'remove_images[]';
                    hidden.value = path;
                    fields.appendChild(hidden);
                }
            });
        });
    });

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-order-menu-toggle]');
        if (toggle) {
            const menu = toggle.closest('.sari-order-menu-wrap')?.querySelector('[data-order-menu]');
            if (!menu) return;
            const willOpen = menu.hidden;
            closeMenus(menu);
            menu.hidden = !willOpen;
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            return;
        }

        const returnSelectOption = event.target.closest('[data-return-select-option]');
        if (returnSelectOption) {
            event.preventDefault();

            const root = returnSelectOption.closest('[data-custom-select]');
            const hidden = root?.querySelector('[data-return-reason]');
            const valueNode = root?.querySelector('[data-return-select-value]');
            const trigger = root?.querySelector('[data-return-select-trigger]');
            const error = root?.parentElement?.querySelector('[data-return-reason-error]');
            const menu = root?.querySelector('[data-return-select-menu]');

            if (hidden && valueNode && trigger) {
                hidden.value = returnSelectOption.dataset.value || '';
                valueNode.textContent = hidden.value || 'Select a reason';
                valueNode.classList.toggle('is-placeholder', hidden.value === '');
                trigger.classList.remove('is-invalid');
                if (error) error.hidden = true;

                root.querySelectorAll('[data-return-select-option]').forEach((option) => {
                    option.classList.toggle('is-selected', option === returnSelectOption);
                });
            }

            if (menu) {
                menu.hidden = true;
                root?.classList.remove('is-open');
                trigger?.setAttribute('aria-expanded', 'false');
            }

            return;
        }

        const returnSelectTrigger = event.target.closest('[data-return-select-trigger]');
        if (returnSelectTrigger) {
            event.preventDefault();
            event.stopPropagation();

            const root = returnSelectTrigger.closest('[data-custom-select]');
            const menu = root?.querySelector('[data-return-select-menu]');
            if (!root || !menu) return;

            const willOpen = menu.hidden;
            closeReturnSelectMenus(menu);
            menu.hidden = !willOpen;
            root.classList.toggle('is-open', willOpen);
            returnSelectTrigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            return;
        }

        const dialogTrigger = event.target.closest('[data-dialog-open]');
        if (dialogTrigger) {
            event.preventDefault();
            event.stopPropagation();
            closeMenus();

            const dialog = document.getElementById(dialogTrigger.dataset.dialogOpen);
            const ratingInputId = dialogTrigger.dataset.ratingInput;

            if (ratingInputId) {
                const ratingInput = document.getElementById(ratingInputId);
                if (ratingInput) ratingInput.checked = true;
            }

            dialog?.showModal();
            return;
        }

        const closeTrigger = event.target.closest('[data-dialog-close]');
        if (closeTrigger) {
            event.preventDefault();
            closeTrigger.closest('dialog')?.close();
            return;
        }

        if (!event.target.closest('.sari-order-menu-wrap')) closeMenus();
        if (!event.target.closest('[data-custom-select]')) closeReturnSelectMenus();

        const interactive = event.target.closest('a,button,form,input,select,textarea,label,dialog');
        if (!interactive) {
            const row = event.target.closest('[data-order-row]');
            if (row?.dataset.orderUrl) window.location.href = row.dataset.orderUrl;
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenus();
            closeReturnSelectMenus();
            return;
        }

        if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('[data-order-row]')) {
            event.preventDefault();
            if (event.target.dataset.orderUrl) window.location.href = event.target.dataset.orderUrl;
        }
    });

    document.querySelectorAll('[data-action-dialog]').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            const rect = dialog.getBoundingClientRect();
            const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
            if (!inside) dialog.close();
        });
    });
})();
</script>
@endpush
