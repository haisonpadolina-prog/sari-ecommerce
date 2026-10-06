@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

@section('content')
@php
    $fullName = trim(collect([
        $account->first_name,
        $account->middle_initial ? $account->middle_initial . '.' : null,
        $account->last_name,
    ])->filter()->implode(' '));

    $fullName = $fullName !== ''
        ? $fullName
        : ($account->store_name ?: 'SARI Seller');

    $initials = collect(preg_split('/\s+/', $fullName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $registrationAddress = collect([
        $account->street_address,
        $account->barangay_name,
        $account->municipality_name,
        $account->province_name,
    ])->filter()->implode(', ');

    $accountStatus = strtolower((string) ($account->account_status ?: 'active'));
    $registrationStatus = strtolower((string) ($account->registration_status ?: 'approved'));
    $sexValue = old('sex', $account->sex);
@endphp

<style>
    .seller-account-enterprise {
        --sa-gold:#d59617;
        --sa-gold-dark:#9c6506;
        --sa-gold-soft:#fffaf0;
        --sa-ink:#202124;
        --sa-text:#46505d;
        --sa-muted:#74808d;
        --sa-line:#dfe3e8;
        --sa-soft-line:#eceff2;
        --sa-surface:#ffffff;
        --sa-canvas:#f7f8fa;
        --sa-green:#4f7d63;
        --sa-green-soft:#f4f9f6;
        --sa-red:#e5484d;
        --sa-red-soft:#fff6f6;
        width:100%;
        max-width:1500px;
        margin:0 auto;
        color:var(--sa-ink);
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif;
    }

    .seller-account-enterprise *,
    .seller-account-enterprise *::before,
    .seller-account-enterprise *::after {
        box-sizing:border-box;
    }

    .sa-alert {
        display:flex;
        align-items:flex-start;
        gap:9px;
        margin-bottom:12px;
        border:1px solid;
        border-radius:8px;
        padding:10px 12px;
        font-size:9px;
        font-weight:600;
        line-height:1.5;
    }

    .sa-alert--success {
        border-color:#cfe4d7;
        background:#f4faf6;
        color:#4f7d63;
    }

    .sa-alert--error {
        border-color:#efcece;
        background:#fff6f6;
        color:#a65353;
    }

    .sa-page-head {
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:18px;
        padding-bottom:15px;
        border-bottom:1px solid var(--sa-line);
    }

    .sa-eyebrow {
        margin:0;
        color:#717b87;
        font-size:8px;
        font-weight:800;
        letter-spacing:.15em;
        text-transform:uppercase;
    }

    .sa-page-title {
        margin:5px 0 0;
        color:var(--sa-ink);
        font-size:clamp(29px,2.2vw,37px);
        font-weight:700;
        letter-spacing:-.045em;
        line-height:1.06;
    }

    .sa-page-title span {
        color:var(--sa-gold);
    }

    .sa-page-copy {
        max-width:760px;
        margin:7px 0 0;
        color:#697481;
        font-size:10px;
        line-height:1.65;
    }

    .sa-profile-strip {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        margin-top:14px;
        border:1px solid var(--sa-line);
        border-radius:8px;
        background:#fff;
        padding:14px 16px;
    }

    .sa-profile-main {
        display:flex;
        min-width:0;
        align-items:center;
        gap:13px;
    }

    .sa-avatar {
        position:relative;
        display:grid;
        width:66px;
        height:66px;
        flex:0 0 66px;
        overflow:hidden;
        place-items:center;
        border:1px solid #e2d5b7;
        border-radius:10px;
        background:#fffaf0;
        color:#a36b08;
        font-size:16px;
        font-weight:800;
    }

    .sa-avatar img {
        width:100%;
        height:100%;
        object-fit:cover;
    }

    .sa-profile-name {
        margin:0;
        overflow:hidden;
        color:#28303a;
        font-size:18px;
        font-weight:750;
        letter-spacing:-.025em;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .sa-profile-meta {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:7px;
        margin-top:4px;
        color:#707a87;
        font-size:8.5px;
        line-height:1.45;
    }

    .sa-meta-dot {
        width:3px;
        height:3px;
        border-radius:999px;
        background:#b8bec6;
    }

    .sa-statuses {
        display:flex;
        flex-wrap:wrap;
        justify-content:flex-end;
        gap:7px;
    }

    .sa-status {
        display:inline-flex;
        min-height:28px;
        align-items:center;
        gap:6px;
        border:1px solid #dfe3e8;
        border-radius:999px;
        background:#fff;
        padding:0 9px;
        color:#5d6673;
        font-size:7.8px;
        font-weight:750;
        white-space:nowrap;
    }

    .sa-status__dot {
        width:6px;
        height:6px;
        border-radius:999px;
        background:currentColor;
    }

    .sa-status--active {
        border-color:#d5e5db;
        background:#f5faf7;
        color:#4f7d63;
    }

    .sa-status--registration {
        border-color:#ead8ad;
        background:#fffaf0;
        color:#96610a;
    }

    .sa-layout {
        display:grid;
        grid-template-columns:minmax(0,1.35fr) minmax(320px,.65fr);
        gap:14px;
        margin-top:14px;
        align-items:start;
    }

    .sa-stack {
        display:grid;
        gap:12px;
        min-width:0;
    }

    .sa-panel {
        min-width:0;
        overflow:hidden;
        border:1px solid var(--sa-line);
        border-radius:8px;
        background:#fff;
    }

    .sa-panel__head {
        display:flex;
        min-height:58px;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        border-bottom:1px solid var(--sa-soft-line);
        padding:11px 14px;
    }

    .sa-panel__title {
        margin:0;
        color:#303844;
        font-size:11px;
        font-weight:750;
        letter-spacing:-.01em;
    }

    .sa-panel__copy {
        margin:3px 0 0;
        color:#74808d;
        font-size:8.4px;
        line-height:1.55;
    }

    .sa-panel__body {
        padding:14px;
    }

    .sa-grid {
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }

    .sa-field label,
    .sa-label {
        display:block;
        margin-bottom:6px;
        color:#4b5563;
        font-size:8.8px;
        font-weight:750;
    }

    .sa-control {
        width:100%;
        min-height:42px;
        border:1px solid #d6dce2;
        border-radius:7px;
        background:#fff;
        padding:0 11px;
        color:#29313b;
        font:inherit;
        font-size:9.8px;
        font-weight:500;
        outline:0;
        transition:border-color .15s ease,box-shadow .15s ease;
    }

    .sa-control:focus {
        border-color:var(--sa-gold);
        box-shadow:0 0 0 3px rgba(213,150,23,.07);
    }

    .sa-control::placeholder {
        color:#a0a8b2;
    }

    .sa-help {
        margin:5px 0 0;
        color:#77828f;
        font-size:7.8px;
        line-height:1.55;
    }

    .sa-photo-editor {
        display:grid;
        grid-template-columns:86px minmax(0,1fr);
        gap:13px;
        align-items:center;
        margin-bottom:14px;
        border-bottom:1px solid var(--sa-soft-line);
        padding-bottom:14px;
    }

    .sa-photo {
        position:relative;
        display:grid;
        width:86px;
        height:86px;
        overflow:hidden;
        place-items:center;
        border:1px solid #ded5c2;
        border-radius:9px;
        background:#fffaf0;
        color:#a36b08;
        font-size:18px;
        font-weight:800;
    }

    .sa-photo img {
        width:100%;
        height:100%;
        object-fit:cover;
    }

    .sa-photo-actions {
        display:flex;
        align-items:center;
        gap:6px;
        margin-top:9px;
    }

    .sa-icon-action {
        display:grid;
        width:31px;
        height:31px;
        place-items:center;
        border:1px solid #dfe3e8;
        border-radius:6px;
        background:#fff;
        color:#5f6976;
        cursor:pointer;
        transition:border-color .15s ease,background .15s ease,color .15s ease;
    }

    .sa-icon-action svg {
        width:13px;
        height:13px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sa-icon-action--edit {
        border-color:#e5d3a5;
        color:#9d6504;
    }

    .sa-icon-action--edit:hover {
        border-color:#d9b864;
        background:#fffaf0;
        color:#7f5000;
    }

    .sa-icon-action--delete {
        border-color:#f0b4b5;
        background:#fff8f8;
        color:#e5484d;
    }

    .sa-icon-action--delete:hover {
        border-color:#e5484d;
        background:#fff0f0;
        color:#d92d20;
    }

    .sa-icon-action:focus-visible {
        outline:2px solid rgba(213,150,23,.28);
        outline-offset:2px;
    }

    .sa-file-input,
    .sa-sr-only {
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

    .sa-form-footer {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-top:14px;
        border-top:1px solid var(--sa-soft-line);
        padding-top:12px;
    }

    .sa-form-note {
        margin:0;
        color:#7a8490;
        font-size:7.7px;
        line-height:1.5;
    }

    .sa-primary {
        display:inline-flex;
        min-height:38px;
        align-items:center;
        justify-content:center;
        gap:7px;
        border:1px solid #c9870c;
        border-radius:7px;
        background:var(--sa-gold);
        padding:0 14px;
        color:#fff;
        font:inherit;
        font-size:8.2px;
        font-weight:750;
        cursor:pointer;
        transition:background .15s ease,border-color .15s ease;
    }

    .sa-primary:hover {
        border-color:#a96f06;
        background:#c9870c;
    }

    .sa-primary svg {
        width:13px;
        height:13px;
        fill:none;
        stroke:currentColor;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sa-doc-list {
        display:grid;
    }

    .sa-doc-row {
        display:grid;
        grid-template-columns:36px minmax(0,1fr) auto auto;
        gap:10px;
        align-items:center;
        border-bottom:1px solid var(--sa-soft-line);
        padding:12px 14px;
    }

    .sa-doc-row:last-child {
        border-bottom:0;
    }

    .sa-doc-icon {
        display:grid;
        width:36px;
        height:36px;
        place-items:center;
        border:1px solid #e0e4e8;
        border-radius:7px;
        background:#f8f9fa;
        color:#626d7a;
    }

    .sa-doc-icon svg {
        width:15px;
        height:15px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.7;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sa-doc-name {
        margin:0;
        color:#38414d;
        font-size:9.2px;
        font-weight:750;
    }

    .sa-doc-copy {
        margin:2px 0 0;
        color:#7b8692;
        font-size:7.7px;
        line-height:1.45;
    }

    .sa-doc-state {
        display:inline-flex;
        min-height:24px;
        align-items:center;
        border:1px solid;
        border-radius:999px;
        padding:0 8px;
        font-size:7.2px;
        font-weight:750;
        white-space:nowrap;
    }

    .sa-doc-state--available {
        border-color:#d7e5dc;
        background:#f5faf7;
        color:#4f7d63;
    }

    .sa-doc-state--missing {
        border-color:#efcece;
        background:#fff6f6;
        color:#a65353;
    }

    .sa-doc-link {
        display:inline-flex;
        min-height:30px;
        align-items:center;
        justify-content:center;
        gap:5px;
        border:1px solid #dfe3e8;
        border-radius:6px;
        background:#fff;
        padding:0 9px;
        color:#505b68;
        font-size:7.6px;
        font-weight:750;
        text-decoration:none;
    }

    .sa-doc-link:hover {
        border-color:#dbc17f;
        background:#fffaf0;
        color:#8d5a00;
    }

    .sa-doc-link svg {
        width:11px;
        height:11px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
    }

    .sa-address {
        border-top:1px solid var(--sa-soft-line);
        background:#fffaf3;
        padding:11px 14px;
    }

    .sa-address strong {
        display:block;
        color:#765311;
        font-size:8.4px;
    }

    .sa-address p {
        margin:3px 0 0;
        color:#786e5e;
        font-size:8px;
        line-height:1.55;
    }

    .sa-side {
        position:sticky;
        top:104px;
    }

    .sa-detail-list {
        display:grid;
    }

    .sa-detail-row {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
        border-bottom:1px solid var(--sa-soft-line);
        padding:10px 14px;
    }

    .sa-detail-row:last-child {
        border-bottom:0;
    }

    .sa-detail-row dt {
        color:#78838f;
        font-size:7.9px;
        line-height:1.5;
    }

    .sa-detail-row dd {
        max-width:60%;
        margin:0;
        color:#39424e;
        font-size:8px;
        font-weight:700;
        line-height:1.5;
        text-align:right;
        overflow-wrap:anywhere;
    }

    .sa-info-note {
        border-top:1px solid var(--sa-soft-line);
        background:#f8f9fa;
        padding:10px 14px;
        color:#737e8a;
        font-size:7.7px;
        line-height:1.55;
    }

    .sa-password-stack {
        display:grid;
        gap:10px;
    }

    .sa-password-wrap {
        position:relative;
    }

    .sa-password-wrap .sa-control {
        padding-right:42px;
    }

    .sa-password-toggle {
        position:absolute;
        right:5px;
        bottom:5px;
        display:grid;
        width:31px;
        height:31px;
        place-items:center;
        border:0;
        border-radius:6px;
        background:transparent;
        color:#76818e;
        cursor:pointer;
    }

    .sa-password-toggle:hover {
        background:#f3f5f7;
        color:#4b5563;
    }

    .sa-password-toggle svg {
        width:14px;
        height:14px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
    }

    .sa-store-link {
        display:inline-flex;
        width:100%;
        min-height:38px;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        margin-top:11px;
        border:1px solid #e3cf99;
        border-radius:7px;
        background:#fffaf0;
        padding:0 11px;
        color:#865700;
        font-size:8px;
        font-weight:750;
        text-decoration:none;
    }

    .sa-store-link:hover {
        border-color:#d5b565;
        background:#fff6df;
    }

    .sa-store-link svg {
        width:13px;
        height:13px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
    }

    /* Custom Sex selector — progressive enhancement */
    .sa-sex {
        position:relative;
    }

    .sa-sex__button,
    .sa-sex__menu {
        display:none;
    }

    .sa-sex.is-ready > .sa-control {
        position:absolute !important;
        width:1px !important;
        height:1px !important;
        opacity:0 !important;
        pointer-events:none !important;
    }

    .sa-sex.is-ready .sa-sex__button {
        display:flex;
        width:100%;
        min-height:42px;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        border:1px solid #d6dce2;
        border-radius:7px;
        background:#fff;
        padding:0 11px;
        color:#29313b;
        font:inherit;
        font-size:9.8px;
        font-weight:500;
        cursor:pointer;
    }

    .sa-sex.is-open .sa-sex__button,
    .sa-sex__button:focus-visible {
        border-color:var(--sa-gold);
        box-shadow:0 0 0 3px rgba(213,150,23,.07);
        outline:0;
    }

    .sa-sex__button svg {
        width:14px;
        height:14px;
        fill:none;
        stroke:#68727e;
        stroke-width:1.8;
        transition:transform .15s ease;
    }

    .sa-sex.is-open .sa-sex__button svg {
        transform:rotate(180deg);
    }

    .sa-sex.is-ready .sa-sex__menu {
        position:absolute;
        z-index:40;
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

    .sa-sex__menu[hidden] {
        display:none !important;
    }

    .sa-sex__option {
        display:block;
        width:100%;
        border:0;
        border-radius:5px;
        background:transparent;
        padding:8px 9px;
        color:#39424e;
        font:inherit;
        font-size:8.8px;
        font-weight:650;
        text-align:left;
        cursor:pointer;
    }

    .sa-sex__option:hover,
    .sa-sex__option:focus-visible {
        background:#f5f6f7;
        outline:0;
    }

    .sa-sex__option.is-selected {
        background:#fff8e8;
        color:#815300;
    }

    @media (max-width:1050px) {
        .sa-layout {
            grid-template-columns:1fr;
        }

        .sa-side {
            position:static;
        }
    }

    @media (max-width:680px) {
        .sa-page-head {
            align-items:flex-start;
            flex-direction:column;
        }

        .sa-profile-strip {
            align-items:flex-start;
            flex-direction:column;
        }

        .sa-statuses {
            justify-content:flex-start;
        }

        .sa-grid {
            grid-template-columns:1fr;
        }

        .sa-photo-editor {
            grid-template-columns:72px minmax(0,1fr);
        }

        .sa-photo {
            width:72px;
            height:72px;
        }

        .sa-form-footer {
            align-items:stretch;
            flex-direction:column;
        }

        .sa-primary {
            width:100%;
        }

        .sa-doc-row {
            grid-template-columns:36px minmax(0,1fr) auto;
        }

        .sa-doc-link {
            grid-column:2 / -1;
            justify-self:start;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sa-control,
        .sa-icon-action,
        .sa-primary,
        .sa-sex__button svg {
            transition:none !important;
        }
    }

    .sa-field--full {
        grid-column:1 / -1;
    }

    .sa-control--textarea {
        min-height:82px;
        resize:vertical;
        padding-top:10px;
        padding-bottom:10px;
        line-height:1.55;
    }

    .sa-subsection {
        margin-top:14px;
        border-top:1px solid var(--sa-soft-line);
        padding-top:14px;
    }

    .sa-subsection__head {
        margin-bottom:11px;
    }

    .sa-subsection__head h3 {
        margin:0;
        color:#333c48;
        font-size:9.8px;
        font-weight:750;
    }

    .sa-subsection__head p {
        margin:3px 0 0;
        color:#74808d;
        font-size:8px;
        line-height:1.55;
    }

    #sellerContactCounter {
        float:right;
        color:#596573;
        font-weight:700;
        font-variant-numeric:tabular-nums;
    }

    .sa-doc-row {
        grid-template-columns:36px minmax(0,1fr) auto auto;
    }

    .sa-doc-actions {
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:6px;
    }

    .sa-doc-selected {
        grid-column:2 / -1;
        margin:-3px 0 0;
        color:#78838f;
        font-size:7.4px;
        line-height:1.45;
    }

    .sa-doc-selected.is-ready {
        color:#8a5a02;
        font-weight:700;
    }

    .sa-verification-footer {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        border-top:1px solid var(--sa-soft-line);
        padding:10px 14px;
        color:#77828e;
        font-size:7.7px;
        line-height:1.5;
    }

    @media (max-width:680px) {
        .sa-field--full {
            grid-column:auto;
        }

        .sa-doc-row {
            grid-template-columns:36px minmax(0,1fr) auto;
        }

        .sa-doc-actions,
        .sa-doc-selected {
            grid-column:2 / -1;
            justify-content:flex-start;
        }

        .sa-verification-footer {
            align-items:stretch;
            flex-direction:column;
        }
    }



/* ======================================================================
   ACCOUNT MANAGEMENT — MAIN CONTAINERS ONLY FLOATING
   Visual-only hierarchy pass.
   - Main account surfaces float.
   - Nested content/controls remain flat.
   - No layout, sizing, route, upload, password, document, or JS changes.
   ====================================================================== */

.seller-account-enterprise {
    --sa-float-border: #E5E7EB;
    --sa-float-divider: #ECEFF2;

    --sa-float-main:
        0 3px 7px rgba(15, 23, 42, .055),
        0 14px 32px rgba(15, 23, 42, .095),
        0 30px 68px rgba(15, 23, 42, .11);

    --sa-float-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065);
}

/* ------------------------------------------------------------
   PAGE HEADER STAYS OPEN
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-page-head,
.seller-account-enterprise .sa-page-head:hover {
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   MAIN ACCOUNT SURFACES FLOAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-profile-strip,
.seller-account-enterprise .sa-panel,
.seller-account-enterprise .sa-profile-strip:hover,
.seller-account-enterprise .sa-panel:hover {
    border-color: var(--sa-float-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--sa-float-main) !important;
    transform: none !important;
    outline: 0 !important;
}

/* Feedback surfaces are lighter than primary work panels. */
.seller-account-enterprise .sa-alert,
.seller-account-enterprise .sa-alert:hover {
    box-shadow: var(--sa-float-soft) !important;
    transform: none !important;
}

/* Panel headers belong to the parent panel. */
.seller-account-enterprise .sa-panel__head {
    border-bottom-color: var(--sa-float-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

/* ------------------------------------------------------------
   PROFILE STRIP CONTENT REMAINS FLAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-profile-main,
.seller-account-enterprise .sa-avatar,
.seller-account-enterprise .sa-statuses,
.seller-account-enterprise .sa-status,
.seller-account-enterprise .sa-meta-dot {
    box-shadow: none !important;
    transform: none !important;
}

.seller-account-enterprise .sa-avatar {
    border-color: #E5E7EB !important;
}

.seller-account-enterprise .sa-status {
    box-shadow: none !important;
}

/* ------------------------------------------------------------
   PROFILE PHOTO EDITOR / FORM CONTENT — FLAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-photo-editor,
.seller-account-enterprise .sa-photo,
.seller-account-enterprise .sa-photo-actions,
.seller-account-enterprise .sa-icon-action,
.seller-account-enterprise .sa-form-footer,
.seller-account-enterprise .sa-subsection,
.seller-account-enterprise .sa-subsection__head {
    box-shadow: none !important;
    transform: none !important;
}

.seller-account-enterprise .sa-photo-editor,
.seller-account-enterprise .sa-form-footer,
.seller-account-enterprise .sa-subsection {
    border-color: var(--sa-float-divider) !important;
}

.seller-account-enterprise .sa-photo {
    border-color: #E5E7EB !important;
    background: #FFFAF0 !important;
}

/* Icon actions are controls, not floating mini-cards. */
.seller-account-enterprise .sa-icon-action,
.seller-account-enterprise .sa-icon-action:hover {
    box-shadow: none !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   FORM CONTROLS — FLAT AT REST
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-control,
.seller-account-enterprise .sa-sex__button,
.seller-account-enterprise .sa-password-toggle,
.seller-account-enterprise .sa-primary,
.seller-account-enterprise .sa-doc-link,
.seller-account-enterprise .sa-store-link {
    box-shadow: none !important;
    transform: none !important;
}

.seller-account-enterprise .sa-control,
.seller-account-enterprise .sa-sex__button {
    border-color: #D1D5DB !important;
    background: #FFFFFF !important;
}

.seller-account-enterprise .sa-control:hover,
.seller-account-enterprise .sa-sex__button:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
}

/* Focus ring only; no resting elevation. */
.seller-account-enterprise .sa-control:focus,
.seller-account-enterprise .sa-sex.is-open .sa-sex__button,
.seller-account-enterprise .sa-sex__button:focus-visible {
    border-color: #C4A35B !important;
    box-shadow: 0 0 0 3px rgba(196, 163, 91, .08) !important;
}

/* Primary actions keep color emphasis, not floating depth. */
.seller-account-enterprise .sa-primary,
.seller-account-enterprise .sa-primary:hover,
.seller-account-enterprise .sa-doc-link,
.seller-account-enterprise .sa-doc-link:hover,
.seller-account-enterprise .sa-store-link,
.seller-account-enterprise .sa-store-link:hover,
.seller-account-enterprise .sa-password-toggle,
.seller-account-enterprise .sa-password-toggle:hover {
    box-shadow: none !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   VERIFICATION DOCUMENTS — ROWS / ICONS / BADGES STAY FLAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-doc-list,
.seller-account-enterprise .sa-doc-row,
.seller-account-enterprise .sa-doc-row:hover,
.seller-account-enterprise .sa-doc-icon,
.seller-account-enterprise .sa-doc-state,
.seller-account-enterprise .sa-doc-actions,
.seller-account-enterprise .sa-doc-selected,
.seller-account-enterprise .sa-verification-footer {
    box-shadow: none !important;
    transform: none !important;
}

.seller-account-enterprise .sa-doc-row,
.seller-account-enterprise .sa-verification-footer {
    border-color: var(--sa-float-divider) !important;
}

.seller-account-enterprise .sa-doc-row {
    background: #FFFFFF !important;
}

.seller-account-enterprise .sa-doc-row:hover {
    background: #F8FAFC !important;
}

.seller-account-enterprise .sa-doc-icon {
    border-color: #E5E7EB !important;
    background: #F8FAFC !important;
}

/* Verification rules block belongs to the parent panel. */
.seller-account-enterprise .sa-address {
    border-top-color: var(--sa-float-divider) !important;
    box-shadow: none !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   ACCOUNT OVERVIEW — DETAIL ROWS STAY FLAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-detail-list,
.seller-account-enterprise .sa-detail-row,
.seller-account-enterprise .sa-info-note {
    box-shadow: none !important;
    transform: none !important;
}

.seller-account-enterprise .sa-detail-row,
.seller-account-enterprise .sa-info-note {
    border-color: var(--sa-float-divider) !important;
}

.seller-account-enterprise .sa-detail-row {
    background: #FFFFFF !important;
}

.seller-account-enterprise .sa-info-note {
    background: #F8FAFC !important;
}

/* ------------------------------------------------------------
   PASSWORD & SECURITY CONTENT — FLAT
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-password-stack,
.seller-account-enterprise .sa-password-wrap,
.seller-account-enterprise .sa-password-toggle {
    box-shadow: none !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   STORE OPERATIONS LINK — CONTROL, NOT NESTED FLOATING CARD
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-store-link {
    box-shadow: none !important;
}

/* ------------------------------------------------------------
   CUSTOM SEX MENU MAY FLOAT AS AN OVERLAY
   ------------------------------------------------------------ */
.seller-account-enterprise .sa-sex__menu {
    border-color: #E1E5EA !important;
    background: #FFFFFF !important;
    box-shadow:
        0 4px 10px rgba(15, 23, 42, .05),
        0 18px 42px rgba(15, 23, 42, .14),
        0 32px 70px rgba(15, 23, 42, .08) !important;
}

/* No physical hover lift on persistent surfaces. */
.seller-account-enterprise .sa-profile-strip:hover,
.seller-account-enterprise .sa-panel:hover,
.seller-account-enterprise .sa-alert:hover {
    transform: none !important;
}

/* ------------------------------------------------------------
   MOBILE — SAME HIERARCHY, TIGHTER SHADOW SPREAD
   ------------------------------------------------------------ */
@media (max-width: 680px) {
    .seller-account-enterprise .sa-profile-strip,
    .seller-account-enterprise .sa-panel {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    .seller-account-enterprise .sa-alert {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .seller-account-enterprise .sa-photo,
    .seller-account-enterprise .sa-doc-row,
    .seller-account-enterprise .sa-detail-row,
    .seller-account-enterprise .sa-address,
    .seller-account-enterprise .sa-info-note {
        box-shadow: none !important;
    }
}
</style>

<div class="seller-account-enterprise">
    @if(session('success'))
        <div class="sa-alert sa-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sa-alert sa-alert--error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="sa-page-head">
        <div>
            <p class="sa-eyebrow">Seller Identity & Security</p>
            <h1 class="sa-page-title">Account <span>Management</span></h1>
            <p class="sa-page-copy">
                Manage your Seller identity, contact details, account address, profile photo, verification documents, and account security.
            </p>
        </div>
    </header>

    <section class="sa-profile-strip">
        <div class="sa-profile-main">
            <div class="sa-avatar">
                @if($profilePhotoUrl)
                    <img
                        id="sellerHeroAvatarImage"
                        src="{{ $profilePhotoUrl }}"
                        alt="{{ $fullName }} profile photo"
                        loading="eager"
                        decoding="async"
                    >
                    <span id="sellerHeroAvatarFallback" hidden>{{ $initials }}</span>
                @else
                    <img
                        id="sellerHeroAvatarImage"
                        src=""
                        alt="{{ $fullName }} profile photo"
                        hidden
                    >
                    <span id="sellerHeroAvatarFallback">{{ $initials }}</span>
                @endif
            </div>

            <div class="min-w-0">
                <h2 class="sa-profile-name">{{ $fullName }}</h2>
                <div class="sa-profile-meta">
                    <span>{{ $account->email }}</span>
                    <span class="sa-meta-dot"></span>
                    <span>{{ $account->store_name ?: 'Store name not set' }}</span>
                </div>
            </div>
        </div>

        <div class="sa-statuses">
            <span class="sa-status sa-status--active">
                <span class="sa-status__dot"></span>
                {{ ucfirst($accountStatus) }}
            </span>

            <span class="sa-status sa-status--registration">
                Registration {{ ucfirst($registrationStatus) }}
            </span>
        </div>
    </section>

    <div class="sa-layout">
        <main class="sa-stack">
            <form
                method="POST"
                action="{{ route('seller.account.update') }}"
                enctype="multipart/form-data"
                class="sa-panel"
                id="sellerAccountForm"
            >
                @csrf
                @method('PATCH')

                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Personal Information</h2>
                        <p class="sa-panel__copy">
                            Seller identity and contact details attached to your account.
                        </p>
                    </div>
                </div>

                <div class="sa-panel__body">
                    <div class="sa-photo-editor">
                        <div class="sa-photo">
                            @if($profilePhotoUrl)
                                <img
                                    id="sellerProfilePreview"
                                    src="{{ $profilePhotoUrl }}"
                                    alt="Current profile photo"
                                    loading="eager"
                                    decoding="async"
                                >
                                <span id="sellerProfileFallback" hidden>{{ $initials }}</span>
                            @else
                                <img
                                    id="sellerProfilePreview"
                                    src=""
                                    alt="Selected profile preview"
                                    hidden
                                >
                                <span id="sellerProfileFallback">{{ $initials }}</span>
                            @endif
                        </div>

                        <div>
                            <p class="sa-label">Profile photo</p>
                            <p class="sa-help" style="margin-top:0;">
                                JPG, PNG, or WEBP · maximum 5 MB. Your approved registration photo
                                remains available as Seller identity context.
                            </p>

                            <div class="sa-photo-actions">
                                <label
                                    for="sellerProfileImage"
                                    class="sa-icon-action sa-icon-action--edit"
                                    title="Edit profile photo"
                                    aria-label="Edit profile photo"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                    </svg>
                                    <span class="sa-sr-only">Edit profile photo</span>
                                </label>

                                <button
                                    type="button"
                                    id="sellerProfileRemoveButton"
                                    class="sa-icon-action sa-icon-action--delete"
                                    title="Remove profile photo"
                                    aria-label="Remove profile photo"
                                    @if(!$profilePhotoUrl) hidden @endif
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

                            <input
                                id="sellerProfileImage"
                                type="file"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-has-original="{{ $profilePhotoUrl ? '1' : '0' }}"
                            >

                            <input
                                id="sellerRemoveProfileImage"
                                type="hidden"
                                name="remove_profile_image"
                                value="0"
                            >
                        </div>
                    </div>

                    <div class="sa-grid">
                        <div class="sa-field">
                            <label for="sellerFirstName">First name</label>
                            <input
                                id="sellerFirstName"
                                name="first_name"
                                required
                                maxlength="60"
                                autocomplete="given-name"
                                data-name-only
                                value="{{ old('first_name', $account->first_name) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">Letters only · maximum 60 characters.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerLastName">Last name</label>
                            <input
                                id="sellerLastName"
                                name="last_name"
                                required
                                maxlength="60"
                                autocomplete="family-name"
                                data-name-only
                                value="{{ old('last_name', $account->last_name) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">Letters only · maximum 60 characters.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerMiddleInitial">Middle initial</label>
                            <input
                                id="sellerMiddleInitial"
                                name="middle_initial"
                                maxlength="1"
                                data-middle-initial"
                                value="{{ old('middle_initial', $account->middle_initial) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">One letter only.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerSex">Sex</label>

                            <div class="sa-sex" id="sellerSexControl">
                                <select
                                    id="sellerSex"
                                    name="sex"
                                    required
                                    class="sa-control"
                                >
                                    <option value="Male" @selected($sexValue === 'Male')>Male</option>
                                    <option value="Female" @selected($sexValue === 'Female')>Female</option>
                                </select>

                                <button
                                    type="button"
                                    class="sa-sex__button"
                                    id="sellerSexButton"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                >
                                    <span id="sellerSexValue">{{ $sexValue ?: 'Select' }}</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>

                                <div
                                    class="sa-sex__menu"
                                    id="sellerSexMenu"
                                    role="listbox"
                                    hidden
                                >
                                    <button
                                        type="button"
                                        class="sa-sex__option {{ $sexValue === 'Male' ? 'is-selected' : '' }}"
                                        data-sex-option="Male"
                                        role="option"
                                        aria-selected="{{ $sexValue === 'Male' ? 'true' : 'false' }}"
                                    >
                                        Male
                                    </button>

                                    <button
                                        type="button"
                                        class="sa-sex__option {{ $sexValue === 'Female' ? 'is-selected' : '' }}"
                                        data-sex-option="Female"
                                        role="option"
                                        aria-selected="{{ $sexValue === 'Female' ? 'true' : 'false' }}"
                                    >
                                        Female
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sa-field">
                            <label for="sellerContact">Contact number</label>
                            <input
                                id="sellerContact"
                                name="contact_no"
                                required
                                inputmode="numeric"
                                autocomplete="tel"
                                maxlength="11"
                                minlength="11"
                                pattern="09[0-9]{9}"
                                aria-describedby="sellerContactHelp"
                                data-digits-only
                                value="{{ old('contact_no', $account->contact_no) }}"
                                class="sa-control"
                            >
                            <p id="sellerContactHelp" class="sa-help">
                                11 digits only · must start with 09.
                                <span id="sellerContactCounter">0 / 11</span>
                            </p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerBirthday">Birthday</label>
                            <input
                                id="sellerBirthday"
                                type="date"
                                name="birthday"
                                required
                                value="{{ old('birthday', optional($account->birthday)->format('Y-m-d') ?: $account->getRawOriginal('birthday')) }}"
                                class="sa-control"
                            >
                        </div>
                    </div>

                    <div class="sa-subsection">
                        <div class="sa-subsection__head">
                            <div>
                                <h3>Account Address</h3>
                                <p>
                                    Current address details linked to this Seller account.
                                    Store pickup location remains separate in Store Management.
                                </p>
                            </div>
                        </div>

                        <div class="sa-grid">
                            <div class="sa-field sa-field--full">
                                <label for="sellerStreetAddress">Street / building / subdivision</label>
                                <textarea
                                    id="sellerStreetAddress"
                                    name="street_address"
                                    rows="3"
                                    maxlength="300"
                                    required
                                    class="sa-control sa-control--textarea"
                                >{{ old('street_address', $account->street_address) }}</textarea>
                                <p class="sa-help">Maximum 300 characters.</p>
                            </div>

                            <div class="sa-field">
                                <label for="sellerBarangay">Barangay</label>
                                <input
                                    id="sellerBarangay"
                                    name="barangay_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('barangay_name', $account->barangay_name) }}"
                                    class="sa-control"
                                >
                            </div>

                            <div class="sa-field">
                                <label for="sellerMunicipality">City / Municipality</label>
                                <input
                                    id="sellerMunicipality"
                                    name="municipality_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('municipality_name', $account->municipality_name) }}"
                                    class="sa-control"
                                >
                            </div>

                            <div class="sa-field">
                                <label for="sellerProvince">Province</label>
                                <input
                                    id="sellerProvince"
                                    name="province_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('province_name', $account->province_name) }}"
                                    class="sa-control"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="sa-form-footer">
                        <p class="sa-form-note">
                            Saving updates your Seller identity, address, profile photo, and any
                            replacement verification files selected below.
                        </p>

                        <button type="submit" class="sa-primary">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12.5 9.2 17 19 7"></path>
                            </svg>
                            Save personal information
                        </button>
                    </div>
                </div>

            <section class="sa-panel">
                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Verification Documents</h2>
                        <p class="sa-panel__copy">
                            View or replace the verification files associated with your Seller account. Replacements remain private.
                        </p>
                    </div>
                </div>

                <div class="sa-doc-list">
                    <div class="sa-doc-row">
                        <div class="sa-doc-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 16h5"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="sa-doc-name">Government Valid ID</p>
                            <p class="sa-doc-copy">Identity requirement submitted with Seller registration.</p>
                        </div>

                        <span class="sa-doc-state {{ $documentAvailability['id'] ? 'sa-doc-state--available' : 'sa-doc-state--missing' }}">
                            {{ $documentAvailability['id'] ? 'Available' : 'Missing' }}
                        </span>

                        <div class="sa-doc-actions">
                            @if($documentAvailability['id'])
                                <a
                                    href="{{ route('seller.account.documents.show', 'id') }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="sa-doc-link"
                                >
                                    View
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7 17 17 7"></path>
                                        <path d="M8 7h9v9"></path>
                                    </svg>
                                </a>
                            @endif

                            <label
                                for="sellerGovernmentId"
                                class="sa-icon-action sa-icon-action--edit"
                                title="Replace Government ID"
                                aria-label="Replace Government ID"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                                <span class="sa-sr-only">Replace Government ID</span>
                            </label>

                            <input
                                id="sellerGovernmentId"
                                type="file"
                                name="government_id"
                                accept="application/pdf,image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-document-input="government-id"
                            >
                        </div>

                        <p class="sa-doc-selected" data-document-name="government-id">
                            No replacement selected
                        </p>
                    </div>

                    <div class="sa-doc-row">
                        <div class="sa-doc-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 16h5"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="sa-doc-name">Business Permit</p>
                            <p class="sa-doc-copy">Business requirement used for Seller approval.</p>
                        </div>

                        <span class="sa-doc-state {{ $documentAvailability['permit'] ? 'sa-doc-state--available' : 'sa-doc-state--missing' }}">
                            {{ $documentAvailability['permit'] ? 'Available' : 'Missing' }}
                        </span>

                        <div class="sa-doc-actions">
                            @if($documentAvailability['permit'])
                                <a
                                    href="{{ route('seller.account.documents.show', 'permit') }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="sa-doc-link"
                                >
                                    View
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7 17 17 7"></path>
                                        <path d="M8 7h9v9"></path>
                                    </svg>
                                </a>
                            @endif

                            <label
                                for="sellerBusinessPermit"
                                class="sa-icon-action sa-icon-action--edit"
                                title="Replace Business Permit"
                                aria-label="Replace Business Permit"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                                <span class="sa-sr-only">Replace Business Permit</span>
                            </label>

                            <input
                                id="sellerBusinessPermit"
                                type="file"
                                name="business_permit"
                                accept="application/pdf,image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-document-input="business-permit"
                            >
                        </div>

                        <p class="sa-doc-selected" data-document-name="business-permit">
                            No replacement selected
                        </p>
                    </div>
                </div>

                <div class="sa-address">
                    <strong>Verification file rules</strong>
                    <p>
                        PDF, JPG, PNG, or WEBP · maximum 10 MB each.
                        Selecting a new file replaces the active account copy after a successful save.
                    </p>
                    <p>
                        Original registration evidence is preserved when the current file came from the registration record.
                    </p>
                </div>

                <div class="sa-verification-footer">
                    <span>Only selected verification files will be replaced.</span>
                    <button type="submit" class="sa-primary">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12.5 9.2 17 19 7"></path>
                        </svg>
                        Save verification changes
                    </button>
                </div>
            </section>
            </form>
        </main>

        <aside class="sa-stack sa-side">
            <section class="sa-panel">
                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Account Overview</h2>
                        <p class="sa-panel__copy">Protected Seller account information.</p>
                    </div>
                </div>

                <dl class="sa-detail-list">
                    <div class="sa-detail-row">
                        <dt>Login email</dt>
                        <dd>{{ $account->email }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Seller ID</dt>
                        <dd>SLR-{{ str_pad((string) $account->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Line of business</dt>
                        <dd>{{ $account->line_of_business ?: '—' }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Approved</dt>
                        <dd>{{ optional($account->approved_at)->format('M d, Y') ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="sa-info-note">
                    Login email and approved business classification are protected fields.
                    Contact SARI Administration if verified information requires correction.
                </div>
            </section>

            <form
                method="POST"
                action="{{ route('seller.account.password.update') }}"
                class="sa-panel"
            >
                @csrf
                @method('PATCH')

                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Password & Security</h2>
                        <p class="sa-panel__copy">Update the password used to access your Seller account.</p>
                    </div>
                </div>

                <div class="sa-panel__body">
                    <div class="sa-password-stack">
                        <div class="sa-password-wrap">
                            <label class="sa-label" for="currentPassword">Current password</label>
                            <input
                                id="currentPassword"
                                type="password"
                                name="current_password"
                                autocomplete="current-password"
                                required
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="currentPassword"
                                aria-label="Show current password"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                            @error('current_password', 'password')
                                <p class="sa-help" style="color:#a65353;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sa-password-wrap">
                            <label class="sa-label" for="newPassword">New password</label>
                            <input
                                id="newPassword"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="newPassword"
                                aria-label="Show new password"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                            @error('password', 'password')
                                <p class="sa-help" style="color:#a65353;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sa-password-wrap">
                            <label class="sa-label" for="newPasswordConfirmation">Confirm new password</label>
                            <input
                                id="newPasswordConfirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="newPasswordConfirmation"
                                aria-label="Show password confirmation"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="sa-primary" style="width:100%;margin-top:12px;">
                        Update password
                    </button>
                </div>
            </form>

            <section class="sa-panel">
                <div class="sa-panel__body">
                    <p class="sa-panel__title">Store Operations</p>
                    <p class="sa-panel__copy" style="margin-top:5px;">
                        Public Store identity, contacts, Open/Paused status, pickup address,
                        and fulfillment instructions are managed separately.
                    </p>

                    <a
                        href="{{ route('seller.store.index') }}"
                        wire:navigate.hover
                        class="sa-store-link"
                    >
                        <span>Open Store Management</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSellerAccountEnterprise() {
        const form = document.getElementById('sellerAccountForm');
        if (!form || form.dataset.enterpriseBound === '1') return;
        form.dataset.enterpriseBound = '1';

        const profileInput = document.getElementById('sellerProfileImage');
        const removeInput = document.getElementById('sellerRemoveProfileImage');
        const removeButton = document.getElementById('sellerProfileRemoveButton');
        const profilePreview = document.getElementById('sellerProfilePreview');
        const profileFallback = document.getElementById('sellerProfileFallback');
        const heroPreview = document.getElementById('sellerHeroAvatarImage');
        const heroFallback = document.getElementById('sellerHeroAvatarFallback');

        let profileObjectUrl = null;

        const revokeProfileUrl = () => {
            if (profileObjectUrl) {
                URL.revokeObjectURL(profileObjectUrl);
                profileObjectUrl = null;
            }
        };


        const firstNameInput = document.getElementById('sellerFirstName');
        const lastNameInput = document.getElementById('sellerLastName');
        const middleInitialInput = document.getElementById('sellerMiddleInitial');
        const contactInput = document.getElementById('sellerContact');
        const contactCounter = document.getElementById('sellerContactCounter');

        const cleanName = (value, maxLength) => {
            try {
                return value
                    .replace(/[^\p{L}\s.'-]/gu, '')
                    .slice(0, maxLength);
            } catch (error) {
                return value
                    .replace(/[0-9]/g, '')
                    .slice(0, maxLength);
            }
        };

        [firstNameInput, lastNameInput].forEach((input) => {
            input?.addEventListener('input', () => {
                const cleaned = cleanName(input.value, 60);

                if (input.value !== cleaned) {
                    input.value = cleaned;
                }
            });
        });

        middleInitialInput?.addEventListener('input', () => {
            const cleaned = cleanName(
                middleInitialInput.value,
                1
            )
                .replace(/[\s.'-]/g, '')
                .slice(0, 1);

            middleInitialInput.value = cleaned;
        });

        const syncContact = () => {
            if (!contactInput) return;

            const digits = contactInput.value
                .replace(/\D/g, '')
                .slice(0, 11);

            contactInput.value = digits;

            if (contactCounter) {
                contactCounter.textContent =
                    `${digits.length} / 11`;
            }

            const valid =
                digits.length === 0
                || /^09\d{9}$/.test(digits);

            contactInput.setCustomValidity(
                valid
                    ? ''
                    : 'Use exactly 11 digits starting with 09.'
            );
        };

        contactInput?.addEventListener('input', syncContact);
        syncContact();

        document
            .querySelectorAll('[data-document-input]')
            .forEach((input) => {
                input.addEventListener('change', () => {
                    const key = input.dataset.documentInput;
                    const output = document.querySelector(
                        `[data-document-name="${key}"]`
                    );

                    if (!output) return;

                    const file = input.files?.[0];

                    output.textContent = file
                        ? `Ready to replace: ${file.name}`
                        : 'No replacement selected';

                    output.classList.toggle(
                        'is-ready',
                        Boolean(file)
                    );
                });
            });

        profileInput?.addEventListener('change', function () {
            const file = this.files?.[0];

            if (!file || !file.type.startsWith('image/')) return;

            revokeProfileUrl();
            profileObjectUrl = URL.createObjectURL(file);

            if (profilePreview) {
                profilePreview.src = profileObjectUrl;
                profilePreview.hidden = false;
            }

            if (heroPreview) {
                heroPreview.src = profileObjectUrl;
                heroPreview.hidden = false;
            }

            profileFallback?.setAttribute('hidden', '');
            heroFallback?.setAttribute('hidden', '');

            if (removeInput) {
                removeInput.value = '0';
            }

            removeButton?.removeAttribute('hidden');
        });

        removeButton?.addEventListener('click', () => {
            revokeProfileUrl();

            if (profileInput) {
                profileInput.value = '';
            }

            const hadOriginal =
                profileInput?.dataset.hasOriginal === '1';

            if (removeInput) {
                removeInput.value = hadOriginal ? '1' : '0';
            }

            if (profilePreview) {
                profilePreview.hidden = true;
            }

            if (heroPreview) {
                heroPreview.hidden = true;
            }

            profileFallback?.removeAttribute('hidden');
            heroFallback?.removeAttribute('hidden');
            removeButton.hidden = true;
        });

        const sexWrap = document.getElementById('sellerSexControl');
        const sexSelect = document.getElementById('sellerSex');
        const sexButton = document.getElementById('sellerSexButton');
        const sexMenu = document.getElementById('sellerSexMenu');
        const sexValue = document.getElementById('sellerSexValue');
        const sexOptions = Array.from(
            document.querySelectorAll('[data-sex-option]')
        );

        const syncSex = () => {
            if (!sexSelect || !sexValue) return;

            sexValue.textContent = sexSelect.value || 'Select';

            sexOptions.forEach((option) => {
                const selected =
                    option.dataset.sexOption === sexSelect.value;

                option.classList.toggle('is-selected', selected);
                option.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        };

        const closeSex = () => {
            if (!sexWrap || !sexButton || !sexMenu) return;

            sexWrap.classList.remove('is-open');
            sexButton.setAttribute('aria-expanded', 'false');
            sexMenu.hidden = true;
        };

        const openSex = () => {
            if (!sexWrap || !sexButton || !sexMenu) return;

            sexWrap.classList.add('is-open');
            sexButton.setAttribute('aria-expanded', 'true');
            sexMenu.hidden = false;

            requestAnimationFrame(() => {
                sexMenu.querySelector('.is-selected')
                    ?.focus({ preventScroll: true });
            });
        };

        if (sexWrap && sexSelect && sexButton && sexMenu) {
            sexWrap.classList.add('is-ready');
            syncSex();

            sexButton.addEventListener('click', () => {
                if (sexWrap.classList.contains('is-open')) {
                    closeSex();
                } else {
                    openSex();
                }
            });

            sexOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    sexSelect.value =
                        option.dataset.sexOption || '';

                    sexSelect.dispatchEvent(
                        new Event('change', { bubbles: true })
                    );

                    syncSex();
                    closeSex();
                    sexButton.focus({ preventScroll: true });
                });
            });

            sexWrap.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeSex();
                    sexButton.focus({ preventScroll: true });
                }
            });

            document.addEventListener('click', (event) => {
                if (!sexWrap.contains(event.target)) {
                    closeSex();
                }
            });
        }

        document
            .querySelectorAll('[data-password-toggle]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(
                        button.dataset.passwordToggle
                    );

                    if (!input) return;

                    const showing = input.type === 'text';
                    input.type = showing ? 'password' : 'text';

                    button.setAttribute(
                        'aria-label',
                        showing ? 'Show password' : 'Hide password'
                    );
                });
            });

        document.addEventListener(
            'livewire:navigating',
            () => {
                revokeProfileUrl();
                form.dataset.enterpriseBound = '0';
            },
            { once: true }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initSellerAccountEnterprise,
            { once: true }
        );
    } else {
        initSellerAccountEnterprise();
    }

    document.addEventListener(
        'livewire:navigated',
        initSellerAccountEnterprise
    );
})();
</script>
@endpush
