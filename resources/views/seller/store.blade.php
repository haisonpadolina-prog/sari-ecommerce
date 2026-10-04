@extends('layouts.seller')

@section('title', 'Store Management — SARI Seller')
@section('page-title', 'Store Management')

@section('content')
@php
    $storeName = old(
        'store_name',
        $seller->store_name ?: 'SARI Seller Store'
    );

    $initials = collect(preg_split('/\s+/', $storeName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $storeStatus = old(
        'store_status',
        $seller->store_status ?: 'open'
    );

    $registeredAddress = collect([
        $seller->street_address,
        $seller->barangay_name,
        $seller->municipality_name,
        $seller->province_name,
    ])->filter()->implode(', ');

    $pickupAddress = old(
        'pickup_address',
        $seller->pickup_address ?: $registeredAddress
    );

    $storeLogoUrl = $seller->store_logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_logo_path)
        : null;

    $storeBannerUrl = $seller->store_banner_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_banner_path)
        : null;
@endphp

<style>
    .store-admin {
        --sm-gold: #d59617;
        --sm-gold-dark: #9f6705;
        --sm-soft: #fffaf0;
        --sm-ink: #202124;
        --sm-text: #475467;
        --sm-muted: #7f8895;
        --sm-line: #e3e7ec;
        --sm-soft-line: #eef1f4;
        --sm-canvas: #f7f8fa;
        width:100%;
        max-width:1500px;
        margin:0 auto;
        color:var(--sm-ink);
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif;
    }

    .store-admin *,
    .store-admin *::before,
    .store-admin *::after { box-sizing:border-box; }

    .sm-alert {
        display:flex;
        align-items:flex-start;
        gap:9px;
        margin-bottom:12px;
        border:1px solid;
        border-radius:9px;
        padding:10px 12px;
        font-size:9px;
        font-weight:600;
        line-height:1.5;
    }
    .sm-alert--ok { border-color:#cfe4d7; background:#f4faf6; color:#4f7d63; }
    .sm-alert--err { border-color:#efcece; background:#fff6f6; color:#a65353; }

    .sm-header {
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:18px;
        padding-bottom:15px;
        border-bottom:1px solid var(--sm-line);
    }
    .sm-eyebrow {
        margin:0;
        color:#707986;
        font-size:8px;
        font-weight:800;
        letter-spacing:.15em;
        text-transform:uppercase;
    }
    .sm-title {
        margin:5px 0 0;
        color:var(--sm-ink);
        font-size:clamp(28px,2.2vw,36px);
        font-weight:700;
        letter-spacing:-.045em;
        line-height:1.06;
    }
    .sm-title span { color:var(--sm-gold); }
    .sm-subtitle {
        max-width:780px;
        margin:7px 0 0;
        color:#747d89;
        font-size:9.7px;
        line-height:1.65;
    }
    .sm-account-link {
        display:inline-flex;
        height:36px;
        align-items:center;
        justify-content:center;
        border:1px solid #dfe3e8;
        border-radius:8px;
        background:#fff;
        padding:0 12px;
        color:#475467;
        font-size:8px;
        font-weight:700;
        text-decoration:none;
    }
    .sm-account-link:hover { border-color:#d8bd7e; background:#fffaf0; color:#875700; }

    .sm-layout {
        display:grid;
        grid-template-columns:minmax(0,1.3fr) minmax(320px,.7fr);
        gap:14px;
        margin-top:14px;
        align-items:start;
    }

    .sm-stack { display:grid; gap:12px; }

    .sm-panel {
        overflow:hidden;
        border:1px solid var(--sm-line);
        border-radius:10px;
        background:#fff;
    }
    .sm-panel__head {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        min-height:54px;
        border-bottom:1px solid var(--sm-soft-line);
        padding:11px 14px;
    }
    .sm-panel__title {
        margin:0;
        color:#344054;
        font-size:10.5px;
        font-weight:750;
    }
    .sm-panel__copy {
        margin:3px 0 0;
        color:#77818e;
        font-size:8px;
        line-height:1.55;
    }
    .sm-panel__body { padding:14px; }

    .sm-grid {
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }
    .sm-span-2 { grid-column:1 / -1; }

    .sm-label {
        display:block;
        margin-bottom:6px;
        color:#505968;
        font-size:8.4px;
        font-weight:750;
        letter-spacing:.005em;
    }
    .sm-control {
        width:100%;
        min-height:40px;
        border:1px solid #d4d9e0;
        border-radius:8px;
        background:#fff;
        padding:0 11px;
        color:#2f3743;
        font:inherit;
        font-size:9.2px;
        font-weight:500;
        outline:0;
        transition:border-color .15s ease,box-shadow .15s ease;
    }
    textarea.sm-control {
        min-height:88px;
        resize:vertical;
        padding-top:10px;
        padding-bottom:10px;
        line-height:1.55;
    }
    .sm-control:focus {
        border-color:var(--sm-gold);
        box-shadow:0 0 0 3px rgba(213,150,23,.07);
    }
    .sm-help {
        margin:6px 0 0;
        color:#76808d;
        font-size:7.8px;
        line-height:1.55;
    }

    .sm-media-grid {
        display:grid;
        grid-template-columns:minmax(0,.75fr) minmax(0,1.25fr);
        gap:12px;
    }
    .sm-upload-card {
        border:1px solid #e0e5ea;
        border-radius:9px;
        background:#fff;
        padding:12px;
        box-shadow:0 3px 12px rgba(15,23,42,.025);
    }
    .sm-upload-top {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:9px;
    }
    .sm-upload-title {
        margin:0;
        color:#344054;
        font-size:8.8px;
        font-weight:750;
        letter-spacing:-.01em;
    }
    .sm-media-actions {
        display:flex;
        align-items:center;
        gap:6px;
    }

    .sm-media-action {
        display:grid;
        width:30px;
        height:30px;
        place-items:center;
        flex:0 0 30px;
        border:1px solid #e1e5ea;
        border-radius:7px;
        background:#fff;
        color:#596273;
        cursor:pointer;
        transition:
            border-color .15s ease,
            background .15s ease,
            color .15s ease,
            transform .15s ease,
            box-shadow .15s ease;
    }

    .sm-media-action svg {
        width:13px;
        height:13px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sm-media-action--edit {
        border-color:#e5d5ad;
        color:#9f6705;
    }

    .sm-media-action--edit:hover {
        border-color:#d9b86f;
        background:#fffaf0;
        color:#805100;
        box-shadow:0 4px 10px rgba(159,103,5,.08);
        transform:translateY(-1px);
    }

    .sm-media-action--delete {
        border-color:#f1b7b7;
        background:#fff8f8;
        color:#e5484d;
    }

    .sm-media-action--delete:hover {
        border-color:#e5484d;
        background:#fff0f0;
        color:#d92d20;
        box-shadow:0 4px 10px rgba(229,72,77,.12);
        transform:translateY(-1px);
    }

    .sm-media-action:active {
        transform:translateY(0);
    }

    .sm-media-action:focus-visible {
        outline:2px solid rgba(213,150,23,.28);
        outline-offset:2px;
    }

    .sm-media-action[hidden] {
        display:none !important;
    }

    .sm-sr-only {
        position:absolute !important;
        width:1px !important;
        height:1px !important;
        padding:0 !important;
        margin:-1px !important;
        overflow:hidden !important;
        clip:rect(0,0,0,0) !important;
        white-space:nowrap !important;
        border:0 !important;
    }
    .sm-image {
        position:relative;
        overflow:hidden;
        border:1px dashed #d7dde4;
        background:#fff;
    }
    .sm-image--logo {
        width:100%;
        aspect-ratio:1 / 1;
        max-height:210px;
        border-radius:9px;
    }
    .sm-image--banner {
        width:100%;
        aspect-ratio:16 / 6;
        border-radius:9px;
    }
    .sm-image img {
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }
    .sm-placeholder {
        display:grid;
        width:100%;
        height:100%;
        place-items:center;
        color:#a8731f;
        background:#fffaf0;
        font-size:20px;
        font-weight:800;
        letter-spacing:.04em;
    }
    .sm-banner-placeholder {
        color:#707986;
        background:#f6f8fa;
        font-size:8px;
        font-weight:650;
    }
    .sm-file-input { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }

    .sm-address-readonly {
        margin-top:10px;
        border-left:3px solid #e0b657;
        background:#fffaf3;
        padding:9px 11px;
    }
    .sm-address-readonly strong {
        display:block;
        color:#765311;
        font-size:8px;
    }
    .sm-address-readonly p {
        margin:3px 0 0;
        color:#8f7f62;
        font-size:7.6px;
        line-height:1.55;
    }

    .sm-savebar {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
        border:1px solid var(--sm-line);
        border-radius:9px;
        background:#fff;
        padding:10px 12px;
    }
    .sm-savebar p {
        margin:0;
        color:#7f8895;
        font-size:7.8px;
        line-height:1.5;
    }
    .sm-save {
        display:inline-flex;
        min-height:38px;
        align-items:center;
        justify-content:center;
        gap:7px;
        border:1px solid #c9870c;
        border-radius:8px;
        background:var(--sm-gold);
        padding:0 14px;
        color:#fff;
        font:inherit;
        font-size:8.2px;
        font-weight:750;
        cursor:pointer;
    }
    .sm-save:hover { background:#c9870c; }
    .sm-save[disabled] {
        opacity:.72;
        cursor:wait;
        transform:none;
    }
    .sm-alert[hidden] { display:none !important; }
    .sm-alert--dynamic-ok {
        border-color:#cfe4d7;
        background:#f4faf6;
        color:#4f7d63;
    }
    .sm-alert--dynamic-error {
        border-color:#efcece;
        background:#fff6f6;
        color:#a65353;
    }

    .sm-preview { position:sticky; top:104px; }
    .sm-preview-cover {
        position:relative;
        overflow:hidden;
        width:100%;
        aspect-ratio:16 / 6;
        border-bottom:1px solid var(--sm-soft-line);
        background:#f4f6f8;
    }
    .sm-preview-cover img { width:100%; height:100%; object-fit:cover; }
    .sm-preview-cover__empty {
        display:grid;
        width:100%;
        height:100%;
        place-items:center;
        color:#858e99;
        font-size:8px;
        font-weight:650;
    }
    .sm-preview-body { padding:14px; }
    .sm-preview-identity {
        display:flex;
        align-items:center;
        gap:10px;
    }
    .sm-preview-logo {
        display:grid;
        width:54px;
        height:54px;
        flex:0 0 54px;
        overflow:hidden;
        place-items:center;
        border:3px solid #fff;
        border-radius:12px;
        background:#fffaf0;
        color:#a8731f;
        font-size:14px;
        font-weight:800;
        box-shadow:0 2px 9px rgba(15,23,42,.08);
    }
    .sm-preview-logo img { width:100%; height:100%; object-fit:cover; }
    .sm-preview-name {
        margin:0;
        color:#344054;
        font-size:11px;
        font-weight:750;
    }
    .sm-preview-business {
        margin:3px 0 0;
        color:#7f8895;
        font-size:7.7px;
    }
    .sm-status {
        display:inline-flex;
        min-height:24px;
        align-items:center;
        gap:6px;
        margin-top:12px;
        border:1px solid #dce7df;
        border-radius:999px;
        background:#f4f9f6;
        padding:0 8px;
        color:#4f7d63;
        font-size:7.4px;
        font-weight:750;
    }
    .sm-status::before {
        content:'';
        width:5px;
        height:5px;
        border-radius:999px;
        background:currentColor;
    }
    .sm-preview-desc {
        margin:12px 0 0;
        color:#596273;
        font-size:8.3px;
        line-height:1.62;
    }
    .sm-preview-meta {
        display:grid;
        gap:7px;
        margin-top:12px;
        padding-top:11px;
        border-top:1px solid var(--sm-soft-line);
    }
    .sm-preview-meta p {
        margin:0;
        color:#596273;
        font-size:7.9px;
        line-height:1.55;
        overflow-wrap:anywhere;
    }
    .sm-preview-meta strong { color:#475467; }

    @media (max-width:1050px) {
        .sm-layout { grid-template-columns:1fr; }
        .sm-preview { position:static; }
    }
    @media (max-width:680px) {
        .sm-header { align-items:flex-start; flex-direction:column; }
        .sm-account-link { width:100%; }
        .sm-grid,
        .sm-media-grid { grid-template-columns:1fr; }
        .sm-span-2 { grid-column:auto; }
        .sm-savebar { align-items:stretch; flex-direction:column; }
        .sm-save { width:100%; }
    }

    /* Enterprise V3 interaction layer */
    .sm-header-actions {
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        justify-content:flex-end;
    }

    .sm-header-meta {
        display:flex;
        align-items:center;
        gap:9px;
    }

    .sm-enterprise-status {
        display:inline-flex;
        min-height:29px;
        align-items:center;
        gap:7px;
        border:1px solid #dde3e8;
        border-radius:7px;
        background:#fff;
        padding:0 9px;
        color:#596273;
        font-size:7.8px;
        font-weight:750;
    }

    .sm-enterprise-status__dot {
        width:6px;
        height:6px;
        border-radius:999px;
        background:currentColor;
    }

    .sm-enterprise-status--open {
        border-color:#d6e5dc;
        background:#f6faf7;
        color:#4f7d63;
    }

    .sm-enterprise-status--paused {
        border-color:#ead8ad;
        background:#fffaf0;
        color:#9f6705;
    }

    .sm-last-updated {
        color:#78828e;
        font-size:7.7px;
        font-weight:600;
        white-space:nowrap;
    }

    .sm-section-edit {
        display:inline-flex;
        min-height:30px;
        align-items:center;
        justify-content:center;
        gap:6px;
        border:1px solid #dfe3e8;
        border-radius:7px;
        background:#fff;
        padding:0 9px;
        color:#596273;
        font:inherit;
        font-size:7.7px;
        font-weight:750;
        cursor:pointer;
        transition:border-color .15s ease,background .15s ease,color .15s ease;
    }

    .sm-section-edit svg {
        width:12px;
        height:12px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sm-section-edit:hover,
    .sm-section-edit[aria-pressed="true"] {
        border-color:#dcc27f;
        background:#fffaf0;
        color:#8f5c00;
    }

    [data-editable-section] {
        transition:border-color .15s ease,box-shadow .15s ease;
    }

    [data-editable-section].is-editing {
        border-color:#e0c687;
        box-shadow:0 0 0 3px rgba(213,150,23,.045);
    }

    [data-editable-section]:not(.is-editing) .sm-control {
        border-color:#e7eaee;
        background:#fafbfc;
        color:#505968;
        box-shadow:none;
    }

    [data-editable-section]:not(.is-editing) textarea.sm-control {
        resize:none;
    }

    [data-editable-section]:not(.is-editing) select.sm-control {
        pointer-events:none;
        appearance:none;
        background-image:none;
    }

    [data-editable-section].is-editing .sm-control {
        background:#fff;
    }

    .sm-field-footer {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-top:6px;
        color:#7f8895;
        font-size:7.5px;
        line-height:1.5;
    }

    .sm-field-footer span:last-child {
        flex:0 0 auto;
        font-variant-numeric:tabular-nums;
        color:#697382;
        font-weight:650;
    }

    .sm-preview-chip {
        display:inline-flex;
        min-height:24px;
        align-items:center;
        border:1px solid #e0e5ea;
        border-radius:999px;
        background:#f8fafc;
        padding:0 8px;
        color:#667085;
        font-size:7.2px;
        font-weight:750;
        white-space:nowrap;
    }

    .sm-status--open {
        border-color:#dce7df;
        background:#f4f9f6;
        color:#4f7d63;
    }

    .sm-status--paused {
        border-color:#ead8ad;
        background:#fffaf0;
        color:#9f6705;
    }

    .sm-savebar {
        position:sticky;
        bottom:12px;
        z-index:24;
        min-height:58px;
        border-color:#d9dfe5;
        border-radius:10px;
        box-shadow:0 12px 30px rgba(15,23,42,.10);
    }

    .sm-savebar[hidden] {
        display:none !important;
    }

    .sm-savebar__status {
        display:flex;
        align-items:center;
        gap:10px;
        min-width:0;
    }

    .sm-savebar__status p {
        margin:0;
        color:#344054;
        font-size:8.5px;
        font-weight:750;
    }

    .sm-savebar__status span:not(.sm-unsaved-dot) {
        display:block;
        margin-top:2px;
        color:#7a8491;
        font-size:7.5px;
        line-height:1.4;
    }

    .sm-unsaved-dot {
        width:8px;
        height:8px;
        flex:0 0 8px;
        border-radius:999px;
        background:var(--sm-gold);
        box-shadow:0 0 0 4px rgba(213,150,23,.11);
    }

    .sm-savebar__actions {
        display:flex;
        align-items:center;
        gap:7px;
        flex:0 0 auto;
    }

    .sm-discard {
        display:inline-flex;
        min-height:38px;
        align-items:center;
        justify-content:center;
        border:1px solid #dfe3e8;
        border-radius:8px;
        background:#fff;
        padding:0 12px;
        color:#596273;
        font:inherit;
        font-size:8px;
        font-weight:750;
        cursor:pointer;
    }

    .sm-discard:hover {
        background:#f7f8fa;
        color:#344054;
    }

    .sm-save svg {
        width:13px;
        height:13px;
        fill:none;
        stroke:currentColor;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sm-savebar.is-saving .sm-unsaved-dot {
        animation:smSavePulse 1s ease-in-out infinite;
    }

    @keyframes smSavePulse {
        0%,100% { opacity:.45; transform:scale(.9); }
        50% { opacity:1; transform:scale(1); }
    }

    .sm-image {
        transition:border-color .15s ease,box-shadow .15s ease;
    }

    .sm-upload-card:hover .sm-image {
        border-color:#d3d9e0;
    }

    .sm-media-action--edit,
    .sm-media-action--delete,
    .sm-section-edit,
    .sm-account-link,
    .sm-discard,
    .sm-save {
        -webkit-tap-highlight-color:transparent;
    }

    @media (max-width:680px) {
        .sm-header-actions {
            width:100%;
            justify-content:space-between;
        }

        .sm-header-meta {
            flex-wrap:wrap;
        }

        .sm-savebar {
            bottom:8px;
            padding-right:72px;
        }

        .sm-savebar__actions {
            width:100%;
        }

        .sm-discard,
        .sm-save {
            flex:1 1 0;
        }

        .sm-field-footer {
            flex-direction:column;
            gap:3px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sm-savebar.is-saving .sm-unsaved-dot {
            animation:none;
        }
    }


    /* Enterprise V4 — flat surfaces + custom dropdown + stronger readability */
    .store-admin {
        --sm-line:#dfe3e8;
        --sm-soft-line:#eceff2;
        --sm-muted:#6f7885;
    }

    .sm-title {
        font-size:clamp(30px,2.25vw,38px);
    }

    .sm-subtitle {
        color:#68717e;
        font-size:10.4px;
        line-height:1.65;
    }

    .sm-panel {
        border-color:#dde2e7;
        border-radius:7px;
        box-shadow:none !important;
    }

    .sm-panel__head {
        min-height:58px;
        border-bottom-color:#e8ebee;
        padding:12px 15px;
    }

    .sm-panel__title {
        color:#2f3743;
        font-size:11.5px;
        font-weight:750;
    }

    .sm-panel__copy {
        color:#707986;
        font-size:8.8px;
        line-height:1.55;
    }

    .sm-panel__body {
        padding:15px;
    }

    .sm-upload-card {
        border-color:#dfe3e8;
        border-radius:7px;
        background:#fff;
        padding:13px;
        box-shadow:none !important;
    }

    .sm-upload-title {
        color:#303846;
        font-size:9.5px;
    }

    .sm-image,
    .sm-image--logo,
    .sm-image--banner {
        border-radius:7px;
    }

    .sm-label {
        margin-bottom:7px;
        color:#444d5a;
        font-size:9.4px;
        font-weight:750;
    }

    .sm-control {
        min-height:43px;
        border-color:#d5dbe1;
        border-radius:7px;
        padding:0 12px;
        color:#252c36;
        font-size:10.2px;
        font-weight:500;
    }

    textarea.sm-control {
        min-height:94px;
        padding-top:11px;
        padding-bottom:11px;
        line-height:1.6;
    }

    .sm-help,
    .sm-field-footer {
        color:#707986;
        font-size:8.4px;
        line-height:1.55;
    }

    .sm-address-readonly {
        border-left-width:2px;
        background:#fffaf3;
    }

    .sm-address-readonly strong {
        font-size:8.7px;
    }

    .sm-address-readonly p {
        color:#796d59;
        font-size:8.2px;
    }

    .sm-account-link,
    .sm-section-edit,
    .sm-media-action,
    .sm-discard,
    .sm-save {
        border-radius:6px;
        box-shadow:none !important;
    }

    .sm-media-action--edit:hover,
    .sm-media-action--delete:hover {
        box-shadow:none !important;
    }

    .sm-preview-logo {
        border-radius:8px;
        box-shadow:none !important;
    }

    .sm-preview-name {
        color:#2d3541;
        font-size:11.8px;
    }

    .sm-preview-business {
        color:#707986;
        font-size:8.2px;
    }

    .sm-preview-desc {
        color:#535d6a;
        font-size:8.8px;
        line-height:1.65;
    }

    .sm-preview-meta p {
        color:#535d6a;
        font-size:8.4px;
        line-height:1.6;
    }

    .sm-savebar {
        border-radius:7px;
        box-shadow:none !important;
    }

    [data-editable-section].is-editing {
        border-color:#d8b765;
        box-shadow:none !important;
    }

    [data-editable-section]:not(.is-editing) .sm-control {
        border-color:#e1e5e9;
        background:#f8f9fa;
        color:#4d5663;
    }

    .sm-enterprise-status,
    .sm-preview-chip,
    .sm-status {
        font-size:8px;
    }

    /* Custom Store Status dropdown */
    .sm-select {
        position:relative;
        width:100%;
    }

    .sm-native-select {
        position:absolute !important;
        width:1px !important;
        height:1px !important;
        opacity:0 !important;
        pointer-events:none !important;
        overflow:hidden !important;
    }

    .sm-select__button {
        display:flex;
        width:100%;
        min-height:43px;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        border:1px solid #d5dbe1;
        border-radius:7px;
        background:#fff;
        padding:0 12px;
        color:#252c36;
        font:inherit;
        font-size:10.2px;
        font-weight:550;
        text-align:left;
        cursor:pointer;
        outline:0;
        transition:border-color .15s ease,background .15s ease;
    }

    .sm-select__button:hover:not(:disabled) {
        border-color:#c7cdd4;
        background:#fcfcfd;
    }

    .sm-select__button:focus-visible,
    .sm-select.is-open .sm-select__button {
        border-color:var(--sm-gold);
        box-shadow:0 0 0 3px rgba(213,150,23,.08);
    }

    .sm-select__button:disabled {
        border-color:#e1e5e9;
        background:#f8f9fa;
        color:#4d5663;
        cursor:default;
    }

    .sm-select__value {
        min-width:0;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .sm-select__chevron {
        width:15px;
        height:15px;
        flex:0 0 15px;
        fill:none;
        stroke:#68717e;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
        transition:transform .15s ease;
    }

    .sm-select.is-open .sm-select__chevron {
        transform:rotate(180deg);
    }

    .sm-select__menu {
        position:absolute;
        z-index:60;
        top:calc(100% + 5px);
        left:0;
        right:0;
        overflow:hidden;
        border:1px solid #d7dde3;
        border-radius:7px;
        background:#fff;
        padding:4px;
        box-shadow:0 10px 24px rgba(15,23,42,.10);
    }

    .sm-select__menu[hidden] {
        display:none !important;
    }

    .sm-select__option {
        display:grid;
        width:100%;
        grid-template-columns:9px minmax(0,1fr);
        gap:9px;
        align-items:start;
        border:0;
        border-radius:5px;
        background:transparent;
        padding:9px 10px;
        color:#313946;
        font:inherit;
        text-align:left;
        cursor:pointer;
    }

    .sm-select__option:hover,
    .sm-select__option:focus-visible {
        background:#f6f7f8;
        outline:0;
    }

    .sm-select__option.is-selected {
        background:#fff8e8;
        color:#704900;
    }

    .sm-select__option strong {
        display:block;
        font-size:9.4px;
        font-weight:750;
        line-height:1.35;
    }

    .sm-select__option small {
        display:block;
        margin-top:2px;
        color:#78818d;
        font-size:8px;
        line-height:1.4;
    }

    .sm-select__option.is-selected small {
        color:#8e6b29;
    }

    .sm-select__option-dot {
        width:7px;
        height:7px;
        margin-top:4px;
        border-radius:999px;
    }

    .sm-select__option-dot--open {
        background:#4f8a65;
    }

    .sm-select__option-dot--paused {
        background:#d59617;
    }

    @media (max-width:680px) {
        .sm-panel__title {
            font-size:11px;
        }

        .sm-panel__copy {
            font-size:8.5px;
        }

        .sm-control,
        .sm-select__button {
            font-size:10px;
        }

        .sm-select__menu {
            position:fixed;
            left:12px;
            right:12px;
            top:auto;
            bottom:84px;
        }
    }

</style>

<div class="store-admin">
    <div id="storeDynamicAlert" class="sm-alert" hidden role="status"></div>
    @if(session('success'))
        <div class="sm-alert sm-alert--ok" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sm-alert sm-alert--err" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="sm-header">
        <div>
            <p class="sm-eyebrow">Public Store & Fulfillment</p>
            <h1 class="sm-title">Store <span>Management</span></h1>
            <p class="sm-subtitle">
                Edit the public information buyers see, upload your Store identity media,
                and manage the operational pickup details used for fulfillment.
            </p>
        </div>

        <div class="sm-header-actions">
            <div class="sm-header-meta">
                <span
                    id="storeHeaderStatus"
                    class="sm-enterprise-status {{ $storeStatus === 'open' ? 'sm-enterprise-status--open' : 'sm-enterprise-status--paused' }}"
                >
                    <span class="sm-enterprise-status__dot"></span>
                    <span id="storeHeaderStatusText">{{ ucfirst($storeStatus) }}</span>
                </span>

                <span id="storeLastUpdated" class="sm-last-updated">
                    Updated {{ $seller->updated_at?->diffForHumans() ?: 'recently' }}
                </span>
            </div>

            <a
                href="{{ route('seller.account') }}"
                class="sm-account-link"
            >
                Account Management
            </a>
        </div>
    </header>

    <div class="sm-layout">
        <form
            method="POST"
            action="{{ route('seller.store.update') }}"
            enctype="multipart/form-data"
            class="sm-stack"
            id="sellerStoreForm"
        >
            @csrf
            @method('PATCH')

            <section class="sm-panel">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Store Media</h2>
                        <p class="sm-panel__copy">Upload a Store logo and optional banner for your marketplace identity.</p>
                    </div>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-media-grid">
                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store logo</p>

                                <div class="sm-media-actions" aria-label="Store logo actions">
                                    <label
                                        for="storeLogoInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store logo"
                                        aria-label="Edit or replace Store logo"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store logo</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="logo"
                                        title="Remove Store logo"
                                        aria-label="Remove Store logo"
                                        @if(!$seller->store_logo_path) hidden @endif
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4h8v2"></path>
                                            <path d="M19 6l-1 14H6L5 6"></path>
                                            <path d="M10 11v5"></path>
                                            <path d="M14 11v5"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="sm-image sm-image--logo">
                                <img
                                    id="storeLogoPreview"
                                    src="{{ $storeLogoUrl ?: '' }}"
                                    alt="Store logo preview"
                                    loading="eager"
                                    decoding="async"
                                    fetchpriority="high"
                                    @if(!$storeLogoUrl) hidden @endif
                                >
                                <div
                                    id="storeLogoFallback"
                                    class="sm-placeholder"
                                    @if($storeLogoUrl) hidden @endif
                                >
                                    {{ $initials }}
                                </div>
                            </div>

                            <input
                                id="storeLogoInput"
                                class="sm-file-input"
                                type="file"
                                name="store_logo"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreLogo" type="hidden" name="remove_store_logo" value="0">

                            <p class="sm-help">Square image recommended. JPG, PNG, or WEBP · max 5 MB.</p>
                        </div>

                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store banner</p>

                                <div class="sm-media-actions" aria-label="Store banner actions">
                                    <label
                                        for="storeBannerInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store banner"
                                        aria-label="Edit or replace Store banner"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store banner</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="banner"
                                        title="Remove Store banner"
                                        aria-label="Remove Store banner"
                                        @if(!$seller->store_banner_path) hidden @endif
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4h8v2"></path>
                                            <path d="M19 6l-1 14H6L5 6"></path>
                                            <path d="M10 11v5"></path>
                                            <path d="M14 11v5"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="sm-image sm-image--banner">
                                <img
                                    id="storeBannerPreview"
                                    src="{{ $storeBannerUrl ?: '' }}"
                                    alt="Store banner preview"
                                    loading="eager"
                                    decoding="async"
                                    @if(!$storeBannerUrl) hidden @endif
                                >
                                <div
                                    id="storeBannerFallback"
                                    class="sm-placeholder sm-banner-placeholder"
                                    @if($storeBannerUrl) hidden @endif
                                >
                                    Optional storefront banner
                                </div>
                            </div>

                            <input
                                id="storeBannerInput"
                                class="sm-file-input"
                                type="file"
                                name="store_banner"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreBanner" type="hidden" name="remove_store_banner" value="0">

                            <p class="sm-help">Wide image recommended. JPG, PNG, or WEBP · max 8 MB.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-panel" data-editable-section="identity">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Public Store Information</h2>
                        <p class="sm-panel__copy">Buyer-facing Store details and public contact information.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="identity"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeName">Store name</label>
                            <input
                                id="storeName"
                                data-section-control
                                name="store_name"
                                required
                                maxlength="150"
                                value="{{ old('store_name', $seller->store_name) }}"
                                class="sm-control"
                            >
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="storeDescription">Store description</label>
                            <textarea
                                id="storeDescription"
                                data-section-control
                                name="store_description"
                                rows="5"
                                maxlength="2000"
                                class="sm-control"
                            >{{ old('store_description', $seller->store_description) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Keep it concise and buyer-focused.</span>
                                <span id="storeDescriptionCounter">0 / 2000</span>
                            </div>
                        </div>

                        <div>
                            <label class="sm-label" for="storePhone">Public phone</label>
                            <input
                                id="storePhone"
                                data-section-control
                                name="store_phone"
                                maxlength="40"
                                value="{{ old('store_phone', $seller->store_phone ?: $seller->contact_no) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change your verified account contact number.</p>
                        </div>

                        <div>
                            <label class="sm-label" for="storePublicEmail">Public email</label>
                            <input
                                id="storePublicEmail"
                                data-section-control
                                name="store_public_email"
                                type="email"
                                maxlength="255"
                                value="{{ old('store_public_email', $seller->store_public_email ?: $seller->email) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change the email used to sign in.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-panel" data-editable-section="fulfillment">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Availability & Fulfillment</h2>
                        <p class="sm-panel__copy">Selling status and operational pickup details used for fulfillment.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="fulfillment"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeStatus">Store status</label>
                            <div class="sm-select" data-custom-select>
                                <select
                                    id="storeStatus"
                                    data-section-control
                                    name="store_status"
                                    required
                                    class="sm-native-select"
                                    aria-hidden="true"
                                    tabindex="-1"
                                >
                                    <option value="open" @selected($storeStatus === 'open')>
                                        Open — accepting new orders
                                    </option>
                                    <option value="paused" @selected($storeStatus === 'paused')>
                                        Paused — temporarily stop new orders
                                    </option>
                                </select>

                                <button
                                    type="button"
                                    class="sm-select__button"
                                    data-custom-select-button
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    disabled
                                >
                                    <span class="sm-select__value" data-custom-select-value>
                                        {{ $storeStatus === 'paused'
                                            ? 'Paused — temporarily stop new orders'
                                            : 'Open — accepting new orders' }}
                                    </span>

                                    <svg class="sm-select__chevron" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>

                                <div
                                    class="sm-select__menu"
                                    data-custom-select-menu
                                    role="listbox"
                                    aria-label="Store status"
                                    hidden
                                >
                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'open' ? 'is-selected' : '' }}"
                                        data-custom-select-option="open"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'open' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--open"></span>
                                        <span>
                                            <strong>Open</strong>
                                            <small>Accepting new orders</small>
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'paused' ? 'is-selected' : '' }}"
                                        data-custom-select-option="paused"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'paused' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--paused"></span>
                                        <span>
                                            <strong>Paused</strong>
                                            <small>Temporarily stop new orders</small>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupAddress">Pickup address</label>
                            <textarea
                                id="pickupAddress" data-section-control
                                name="pickup_address"
                                required
                                rows="4"
                                maxlength="1000"
                                class="sm-control"
                            >{{ $pickupAddress }}</textarea>
                            <p class="sm-help">This is the operational pickup address used for fulfillment.</p>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupInstructions">Pickup instructions</label>
                            <textarea
                                id="pickupInstructions" data-section-control
                                name="pickup_instructions"
                                rows="4"
                                maxlength="1200"
                                placeholder="Gate, landmark, receiving hours, contact instructions, etc."
                                class="sm-control"
                            >{{ old('pickup_instructions', $seller->pickup_instructions) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Landmarks, receiving hours, gate instructions, or contact notes.</span>
                                <span id="pickupInstructionsCounter">0 / 1200</span>
                            </div>
                        </div>
                    </div>

                    <div class="sm-address-readonly">
                        <strong>Verified registration address — read only</strong>
                        <p>{{ $registeredAddress ?: 'No verified registration address is stored.' }}</p>
                        <p>
                            Store Management does not modify the address verified during Seller registration.
                        </p>
                    </div>
                </div>
            </section>

            <div class="sm-savebar" id="storeSaveBar" hidden>
                <div class="sm-savebar__status">
                    <span class="sm-unsaved-dot" aria-hidden="true"></span>
                    <div>
                        <p id="storeSaveBarTitle">Unsaved changes</p>
                        <span id="storeSaveState" aria-live="polite">
                            Review your changes before saving.
                        </span>
                    </div>
                </div>

                <div class="sm-savebar__actions">
                    <button
                        type="button"
                        class="sm-discard"
                        id="storeDiscardButton"
                    >
                        Discard
                    </button>

                    <button type="submit" class="sm-save" id="storeSaveButton">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12.5 9.2 17 19 7"></path>
                        </svg>
                        <span id="storeSaveButtonText">Save changes</span>
                    </button>
                </div>
            </div>
        </form>

        <aside class="sm-stack sm-preview">
            <section class="sm-panel">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Live Store Preview</h2>
                        <p class="sm-panel__copy">Buyer-facing preview updates as you edit.</p>
                    </div>

                    <span class="sm-preview-chip">Buyer view</span>
                </div>

                <div class="sm-preview-cover">
                    <img
                        id="storeBannerLivePreview"
                        src="{{ $storeBannerUrl ?: '' }}"
                        alt="Store banner"
                        loading="eager"
                        decoding="async"
                        @if(!$storeBannerUrl) hidden @endif
                    >
                    <div
                        id="storeBannerLiveFallback"
                        class="sm-preview-cover__empty"
                        @if($storeBannerUrl) hidden @endif
                    >
                        No Store banner uploaded
                    </div>
                </div>

                <div class="sm-preview-body">
                    <div class="sm-preview-identity">
                        <div class="sm-preview-logo">
                            <img
                                id="storeLogoLivePreview"
                                src="{{ $storeLogoUrl ?: '' }}"
                                alt="Store logo"
                                loading="eager"
                                decoding="async"
                                @if(!$storeLogoUrl) hidden @endif
                            >
                            <span id="storeLogoLiveFallback" @if($storeLogoUrl) hidden @endif>
                                {{ $initials }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p id="storeNameLivePreview" class="sm-preview-name">{{ $storeName }}</p>
                            <p class="sm-preview-business">
                                {{ $seller->line_of_business ?: 'SARI Marketplace Seller' }}
                            </p>
                        </div>
                    </div>

                    <span
                        id="storeStatusLivePreview"
                        class="sm-status {{ $storeStatus === 'open' ? 'sm-status--open' : 'sm-status--paused' }}"
                    >
                        {{ ucfirst($storeStatus) }}
                    </span>

                    <p id="storeDescriptionLivePreview" class="sm-preview-desc">
                        {{ old('store_description', $seller->store_description) ?: 'Add a concise Store description so buyers understand what your shop offers.' }}
                    </p>

                    <div class="sm-preview-meta">
                        <p>
                            <strong>Public phone:</strong>
                            <span id="storePhoneLivePreview">
                                {{ old('store_phone', $seller->store_phone ?: $seller->contact_no) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Public email:</strong>
                            <span id="storeEmailLivePreview">
                                {{ old('store_public_email', $seller->store_public_email ?: $seller->email) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Pickup:</strong>
                            <span id="pickupAddressLivePreview">{{ $pickupAddress }}</span>
                        </p>
                        <p>
                            <strong>Last saved:</strong>
                            <span id="storePreviewLastSaved">
                                {{ $seller->updated_at?->diffForHumans() ?: 'Recently' }}
                            </span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="sm-panel">
                <div class="sm-panel__body">
                    <p class="sm-panel__title">Store vs. Account identity</p>
                    <p class="sm-panel__copy" style="margin-top:7px;line-height:1.65;">
                        Store logo, banner, public contacts, availability, and pickup details are managed here.
                        Personal identity, login security, and verified registration information remain in Account Management.
                    </p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootStoreManagement() {
        const form = document.getElementById('sellerStoreForm');
        if (!form || form.dataset.initialized === '1') return;
        form.dataset.initialized = '1';

        const byId = (id) => document.getElementById(id);

        const logoInput = byId('storeLogoInput');
        const bannerInput = byId('storeBannerInput');
        const logoPreview = byId('storeLogoPreview');
        const logoFallback = byId('storeLogoFallback');
        const logoLive = byId('storeLogoLivePreview');
        const logoLiveFallback = byId('storeLogoLiveFallback');
        const bannerPreview = byId('storeBannerPreview');
        const bannerFallback = byId('storeBannerFallback');
        const bannerLive = byId('storeBannerLivePreview');
        const bannerLiveFallback = byId('storeBannerLiveFallback');
        const removeLogo = byId('removeStoreLogo');
        const removeBanner = byId('removeStoreBanner');
        const saveButton = byId('storeSaveButton');
        const saveButtonText = byId('storeSaveButtonText');
        const saveState = byId('storeSaveState');
        const saveBar = byId('storeSaveBar');
        const saveBarTitle = byId('storeSaveBarTitle');
        const discardButton = byId('storeDiscardButton');
        const dynamicAlert = byId('storeDynamicAlert');
        const lastUpdated = byId('storeLastUpdated');
        const previewLastSaved = byId('storePreviewLastSaved');
        const headerStatus = byId('storeHeaderStatus');
        const headerStatusText = byId('storeHeaderStatusText');

        const trackedFields = [
            'storeName',
            'storeDescription',
            'storePhone',
            'storePublicEmail',
            'storeStatus',
            'pickupAddress',
            'pickupInstructions',
        ];

        const objectUrls = {
            logo: null,
            banner: null,
        };

        const revokeObjectUrl = (key) => {
            if (objectUrls[key]) {
                URL.revokeObjectURL(objectUrls[key]);
                objectUrls[key] = null;
            }
        };

        const setDynamicAlert = (message = '', type = 'ok') => {
            if (!dynamicAlert) return;

            if (!message) {
                dynamicAlert.hidden = true;
                dynamicAlert.textContent = '';
                dynamicAlert.className = 'sm-alert';
                return;
            }

            dynamicAlert.textContent = message;
            dynamicAlert.className =
                `sm-alert ${type === 'error'
                    ? 'sm-alert--dynamic-error'
                    : 'sm-alert--dynamic-ok'}`;
            dynamicAlert.hidden = false;
        };

        const showImage = (
            key,
            file,
            preview,
            fallback,
            live,
            liveFallback,
            removeInput
        ) => {
            if (!file) return;

            revokeObjectUrl(key);

            const url = URL.createObjectURL(file);
            objectUrls[key] = url;

            preview.src = url;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = url;
            live.hidden = false;
            liveFallback.hidden = true;

            removeInput.value = '0';
        };

        const previewQueue = new Map();
        let previewFrame = 0;

        const queuePreview = (output, value) => {
            if (!output) return;

            previewQueue.set(output, value);

            if (previewFrame) return;

            previewFrame = requestAnimationFrame(() => {
                previewQueue.forEach((value, element) => {
                    element.textContent = value;
                });

                previewQueue.clear();
                previewFrame = 0;
            });
        };

        const syncCounter = (input, output, max) => {
            if (!input || !output) return;

            output.textContent =
                `${String(input.value || '').length} / ${max}`;
        };

        const description = byId('storeDescription');
        const descriptionCounter = byId('storeDescriptionCounter');
        const pickupInstructions = byId('pickupInstructions');
        const pickupCounter = byId('pickupInstructionsCounter');

        syncCounter(description, descriptionCounter, 2000);
        syncCounter(pickupInstructions, pickupCounter, 1200);

        const setSectionEditing = (section, editing) => {
            if (!section) return;

            section.classList.toggle('is-editing', editing);

            const button = section.querySelector('[data-edit-section]');
            const label = button?.querySelector('[data-edit-label]');

            button?.setAttribute(
                'aria-pressed',
                editing ? 'true' : 'false'
            );

            if (label) {
                label.textContent = editing ? 'Done' : 'Edit';
            }

            section
                .querySelectorAll('[data-section-control]')
                .forEach((control) => {
                    if (
                        control instanceof HTMLInputElement
                        || control instanceof HTMLTextAreaElement
                    ) {
                        control.readOnly = !editing;
                    }

                    if (control instanceof HTMLSelectElement) {
                        control.dataset.enterpriseLocked =
                            editing ? '0' : '1';
                    }
                });

            section
                .querySelectorAll('[data-custom-select-button]')
                .forEach((button) => {
                    button.disabled = !editing;

                    if (!editing) {
                        const customSelect =
                            button.closest('[data-custom-select]');

                        customSelect?.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');

                        const menu =
                            customSelect?.querySelector(
                                '[data-custom-select-menu]'
                            );

                        if (menu) {
                            menu.hidden = true;
                        }
                    }
                });
        };

        document
            .querySelectorAll('[data-editable-section]')
            .forEach((section) => {
                setSectionEditing(section, false);
            });

        document
            .querySelectorAll('[data-edit-section]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const section =
                        button.closest('[data-editable-section]');

                    const editing =
                        !section?.classList.contains('is-editing');

                    setSectionEditing(section, editing);

                    if (editing) {
                        section
                            ?.querySelector('[data-section-control]')
                            ?.focus({ preventScroll: true });
                    }
                });
            });

        const status = byId('storeStatus');
        const statusOutput = byId('storeStatusLivePreview');
        const customStatus = status?.closest('[data-custom-select]');
        const customStatusButton =
            customStatus?.querySelector('[data-custom-select-button]');
        const customStatusMenu =
            customStatus?.querySelector('[data-custom-select-menu]');
        const customStatusValue =
            customStatus?.querySelector('[data-custom-select-value]');
        const customStatusOptions = Array.from(
            customStatus?.querySelectorAll(
                '[data-custom-select-option]'
            ) || []
        );

        const statusLabels = {
            open: 'Open — accepting new orders',
            paused: 'Paused — temporarily stop new orders',
        };

        const closeStatusMenu = () => {
            if (!customStatus || !customStatusButton || !customStatusMenu) {
                return;
            }

            customStatus.classList.remove('is-open');
            customStatusButton.setAttribute('aria-expanded', 'false');
            customStatusMenu.hidden = true;
        };

        const openStatusMenu = () => {
            if (
                !customStatus
                || !customStatusButton
                || !customStatusMenu
                || customStatusButton.disabled
            ) {
                return;
            }

            customStatus.classList.add('is-open');
            customStatusButton.setAttribute('aria-expanded', 'true');
            customStatusMenu.hidden = false;

            const selected =
                customStatusMenu.querySelector('.is-selected');

            requestAnimationFrame(() => {
                selected?.focus({ preventScroll: true });
            });
        };

        const syncCustomStatusSelect = () => {
            const value = String(status?.value || 'open');

            if (customStatusValue) {
                customStatusValue.textContent =
                    statusLabels[value] || statusLabels.open;
            }

            customStatusOptions.forEach((option) => {
                const selected =
                    option.dataset.customSelectOption === value;

                option.classList.toggle('is-selected', selected);
                option.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        };

        customStatusButton?.addEventListener('click', () => {
            if (customStatus?.classList.contains('is-open')) {
                closeStatusMenu();
            } else {
                openStatusMenu();
            }
        });

        customStatusOptions.forEach((option) => {
            option.addEventListener('click', () => {
                const value =
                    option.dataset.customSelectOption;

                if (!value || !status) {
                    return;
                }

                status.value = value;
                status.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                syncCustomStatusSelect();
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            });
        });

        customStatus?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            }

            if (
                (event.key === 'Enter' || event.key === ' ')
                && event.target === customStatusButton
            ) {
                event.preventDefault();

                if (customStatus?.classList.contains('is-open')) {
                    closeStatusMenu();
                } else {
                    openStatusMenu();
                }
            }
        });

        document.addEventListener('click', (event) => {
            if (
                customStatus
                && !customStatus.contains(event.target)
            ) {
                closeStatusMenu();
            }
        });

        const syncStatusUI = () => {
            const value = String(status?.value || 'open');
            const label =
                value.charAt(0).toUpperCase() + value.slice(1);
            const open = value === 'open';

            queuePreview(statusOutput, label);
            queuePreview(headerStatusText, label);

            statusOutput?.classList.toggle(
                'sm-status--open',
                open
            );
            statusOutput?.classList.toggle(
                'sm-status--paused',
                !open
            );

            headerStatus?.classList.toggle(
                'sm-enterprise-status--open',
                open
            );
            headerStatus?.classList.toggle(
                'sm-enterprise-status--paused',
                !open
            );
        };

        const bindings = [
            ['storeName', 'storeNameLivePreview', 'SARI Seller Store'],
            [
                'storeDescription',
                'storeDescriptionLivePreview',
                'Add a concise Store description so buyers understand what your shop offers.'
            ],
            ['storePhone', 'storePhoneLivePreview', 'Not provided'],
            ['storePublicEmail', 'storeEmailLivePreview', 'Not provided'],
            ['pickupAddress', 'pickupAddressLivePreview', 'Not provided'],
        ];

        const syncAllPreviewText = () => {
            bindings.forEach(([inputId, outputId, fallback]) => {
                const input = byId(inputId);
                const output = byId(outputId);

                if (!input || !output) return;

                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );
            });

            syncStatusUI();
            syncCustomStatusSelect();
            syncCounter(description, descriptionCounter, 2000);
            syncCounter(pickupInstructions, pickupCounter, 1200);
        };

        bindings.forEach(([inputId, outputId, fallback]) => {
            const input = byId(inputId);
            const output = byId(outputId);

            if (!input || !output) return;

            input.addEventListener('input', () => {
                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );

                if (input === description) {
                    syncCounter(
                        description,
                        descriptionCounter,
                        2000
                    );
                }

                if (input === pickupInstructions) {
                    syncCounter(
                        pickupInstructions,
                        pickupCounter,
                        1200
                    );
                }
            });
        });

        status?.addEventListener('change', () => {
            syncStatusUI();
            syncCustomStatusSelect();
        });

        const mediaDeleteButton = (type) =>
            document.querySelector(
                `[data-remove-media="${type}"]`
            );

        let savedMedia = {
            logo:
                logoPreview && !logoPreview.hidden
                    ? logoPreview.src
                    : '',
            banner:
                bannerPreview && !bannerPreview.hidden
                    ? bannerPreview.src
                    : '',
        };

        const captureState = () => {
            const fields = {};

            trackedFields.forEach((id) => {
                fields[id] = String(byId(id)?.value || '');
            });

            return fields;
        };

        let savedState = captureState();

        const hasUnsavedChanges = () => {
            const current = captureState();

            const fieldsChanged =
                JSON.stringify(current)
                !== JSON.stringify(savedState);

            const mediaChanged =
                Boolean(logoInput?.files?.length)
                || Boolean(bannerInput?.files?.length)
                || removeLogo?.value === '1'
                || removeBanner?.value === '1';

            return fieldsChanged || mediaChanged;
        };

        const updateDirtyState = () => {
            if (!saveBar) return;

            saveBar.hidden = !hasUnsavedChanges();

            if (!saveBar.hidden) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Unsaved changes';

                if (!form.dataset.saving) {
                    saveState.textContent =
                        'Review your changes before saving.';
                }
            }
        };

        trackedFields.forEach((id) => {
            const control = byId(id);

            control?.addEventListener(
                control instanceof HTMLSelectElement
                    ? 'change'
                    : 'input',
                updateDirtyState
            );
        });

        logoInput?.addEventListener('change', () => {
            const file = logoInput.files?.[0];

            showImage(
                'logo',
                file,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback,
                removeLogo
            );

            if (file) {
                mediaDeleteButton('logo')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        bannerInput?.addEventListener('change', () => {
            const file = bannerInput.files?.[0];

            showImage(
                'banner',
                file,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback,
                removeBanner
            );

            if (file) {
                mediaDeleteButton('banner')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        document
            .querySelectorAll('[data-remove-media]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const type = button.dataset.removeMedia;

                    if (type === 'logo') {
                        revokeObjectUrl('logo');
                        logoInput.value = '';
                        removeLogo.value = '1';
                        logoPreview.hidden = true;
                        logoFallback.hidden = false;
                        logoLive.hidden = true;
                        logoLiveFallback.hidden = false;
                    }

                    if (type === 'banner') {
                        revokeObjectUrl('banner');
                        bannerInput.value = '';
                        removeBanner.value = '1';
                        bannerPreview.hidden = true;
                        bannerFallback.hidden = false;
                        bannerLive.hidden = true;
                        bannerLiveFallback.hidden = false;
                    }

                    button.hidden = true;
                    updateDirtyState();
                });
            });

        const restoreMedia = (
            type,
            url,
            preview,
            fallback,
            live,
            liveFallback
        ) => {
            revokeObjectUrl(type);

            const exists = Boolean(url);

            preview.hidden = !exists;
            fallback.hidden = exists;
            live.hidden = !exists;
            liveFallback.hidden = exists;

            if (exists) {
                preview.src = url;
                live.src = url;
            }

            mediaDeleteButton(type).hidden = !exists;
        };

        discardButton?.addEventListener('click', () => {
            trackedFields.forEach((id) => {
                const control = byId(id);
                if (!control) return;

                control.value = savedState[id] ?? '';
            });

            logoInput.value = '';
            bannerInput.value = '';
            removeLogo.value = '0';
            removeBanner.value = '0';

            restoreMedia(
                'logo',
                savedMedia.logo,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback
            );

            restoreMedia(
                'banner',
                savedMedia.banner,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback
            );

            document
                .querySelectorAll('[data-editable-section]')
                .forEach((section) => {
                    setSectionEditing(section, false);
                });

            setDynamicAlert();
            syncAllPreviewText();
            updateDirtyState();
        });

        const loadBitmap = async (file) => {
            if ('createImageBitmap' in window) {
                return createImageBitmap(file);
            }

            return new Promise((resolve, reject) => {
                const image = new Image();
                const url = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };

                image.onerror = () => {
                    URL.revokeObjectURL(url);
                    reject(
                        new Error('Image could not be decoded.')
                    );
                };

                image.src = url;
            });
        };

        const optimizeImage = async (
            file,
            {
                maxWidth,
                maxHeight,
                quality = 0.86,
                minBytes = 450000,
            }
        ) => {
            if (!file || file.size < minBytes) {
                return file;
            }

            try {
                const bitmap = await loadBitmap(file);
                const sourceWidth =
                    bitmap.width || bitmap.naturalWidth;
                const sourceHeight =
                    bitmap.height || bitmap.naturalHeight;

                if (!sourceWidth || !sourceHeight) {
                    bitmap.close?.();
                    return file;
                }

                const scale = Math.min(
                    1,
                    maxWidth / sourceWidth,
                    maxHeight / sourceHeight
                );

                const width = Math.max(
                    1,
                    Math.round(sourceWidth * scale)
                );

                const height = Math.max(
                    1,
                    Math.round(sourceHeight * scale)
                );

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext(
                    '2d',
                    { alpha: true }
                );

                if (!context) {
                    bitmap.close?.();
                    return file;
                }

                context.drawImage(
                    bitmap,
                    0,
                    0,
                    width,
                    height
                );

                bitmap.close?.();

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(
                        resolve,
                        'image/webp',
                        quality
                    );
                });

                if (!blob || blob.size >= file.size) {
                    return file;
                }

                const baseName =
                    file.name.replace(/\.[^.]+$/, '');

                return new File(
                    [blob],
                    `${baseName}.webp`,
                    {
                        type: 'image/webp',
                        lastModified: Date.now(),
                    }
                );
            } catch (error) {
                return file;
            }
        };

        const firstErrorMessage = (payload) => {
            const errors = payload?.errors || {};

            for (const value of Object.values(errors)) {
                if (Array.isArray(value) && value[0]) {
                    return String(value[0]);
                }
            }

            return String(
                payload?.message
                || 'Store changes could not be saved.'
            );
        };

        const applySavedImage = (
            url,
            preview,
            fallback,
            live,
            liveFallback,
            key
        ) => {
            revokeObjectUrl(key);

            if (!url) {
                preview.hidden = true;
                fallback.hidden = false;
                live.hidden = true;
                liveFallback.hidden = false;
                savedMedia[key] = '';
                mediaDeleteButton(key).hidden = true;
                return;
            }

            const freshUrl =
                `${url}${url.includes('?') ? '&' : '?'}v=${Date.now()}`;

            preview.src = freshUrl;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = freshUrl;
            live.hidden = false;
            liveFallback.hidden = true;

            savedMedia[key] = freshUrl;
            mediaDeleteButton(key).hidden = false;
        };

        form.addEventListener('submit', async (event) => {
            if (
                !window.fetch
                || !window.FormData
                || form.dataset.saving === '1'
            ) {
                return;
            }

            event.preventDefault();

            if (!hasUnsavedChanges()) {
                return;
            }

            form.dataset.saving = '1';
            saveButton.disabled = true;
            discardButton.disabled = true;
            saveButtonText.textContent = 'Saving…';
            saveBar.hidden = false;
            saveBar.classList.add('is-saving');
            saveBarTitle.textContent = 'Saving changes';
            saveState.textContent = 'Optimizing media…';
            setDynamicAlert();

            try {
                const formData = new FormData(form);

                const [
                    optimizedLogo,
                    optimizedBanner,
                ] = await Promise.all([
                    optimizeImage(
                        logoInput?.files?.[0],
                        {
                            maxWidth: 1200,
                            maxHeight: 1200,
                            quality: 0.88,
                        }
                    ),
                    optimizeImage(
                        bannerInput?.files?.[0],
                        {
                            maxWidth: 2200,
                            maxHeight: 1200,
                            quality: 0.86,
                        }
                    ),
                ]);

                if (optimizedLogo) {
                    formData.set(
                        'store_logo',
                        optimizedLogo,
                        optimizedLogo.name
                    );
                }

                if (optimizedBanner) {
                    formData.set(
                        'store_banner',
                        optimizedBanner,
                        optimizedBanner.name
                    );
                }

                saveState.textContent = 'Saving securely…';

                const response = await fetch(
                    form.action,
                    {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    }
                );

                const contentType =
                    response.headers.get('content-type') || '';

                const payload =
                    contentType.includes('application/json')
                        ? await response.json()
                        : {
                            message:
                                'The server returned an unexpected response.'
                        };

                if (!response.ok) {
                    throw new Error(
                        firstErrorMessage(payload)
                    );
                }

                const store = payload.store || {};

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_logo_url'
                    )
                ) {
                    applySavedImage(
                        store.store_logo_url,
                        logoPreview,
                        logoFallback,
                        logoLive,
                        logoLiveFallback,
                        'logo'
                    );
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_banner_url'
                    )
                ) {
                    applySavedImage(
                        store.store_banner_url,
                        bannerPreview,
                        bannerFallback,
                        bannerLive,
                        bannerLiveFallback,
                        'banner'
                    );
                }

                logoInput.value = '';
                bannerInput.value = '';
                removeLogo.value = '0';
                removeBanner.value = '0';

                savedState = captureState();

                document
                    .querySelectorAll('[data-editable-section]')
                    .forEach((section) => {
                        setSectionEditing(section, false);
                    });

                syncStatusUI();

                const justNow = 'Updated just now';
                const previewNow = 'Just now';

                if (lastUpdated) {
                    lastUpdated.textContent = justNow;
                }

                if (previewLastSaved) {
                    previewLastSaved.textContent = previewNow;
                }

                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Changes saved';
                saveState.textContent =
                    'Your Store settings are up to date.';

                setDynamicAlert(
                    payload.message
                    || 'Store settings saved successfully.',
                    'ok'
                );

                window.setTimeout(() => {
                    updateDirtyState();
                }, 900);
            } catch (error) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Save failed';
                saveState.textContent =
                    'Review the error above and try again.';

                setDynamicAlert(
                    error?.message
                    || 'Store changes could not be saved.',
                    'error'
                );
            } finally {
                form.dataset.saving = '0';
                saveButton.disabled = false;
                discardButton.disabled = false;
                saveButtonText.textContent = 'Save changes';
            }
        });

        const cleanup = () => {
            revokeObjectUrl('logo');
            revokeObjectUrl('banner');

            if (previewFrame) {
                cancelAnimationFrame(previewFrame);
            }

            form.dataset.initialized = '0';
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true }
        );

        syncCustomStatusSelect();
        syncAllPreviewText();
        updateDirtyState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            bootStoreManagement,
            { once: true }
        );
    } else {
        bootStoreManagement();
    }
})();
</script>
@endpush
