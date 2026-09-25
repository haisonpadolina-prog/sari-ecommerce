@extends('layouts.app')

@section('title', 'Registration Status — SARI')

@section('content')
@php
    $registrationRole = strtolower((string) ($application?->role ?? session('registration_role', 'buyer')));
    $decisionEmail = (string) ($application?->email ?? session('registration_email', ''));
    $initialStatus = strtolower((string) ($application?->status ?? 'pending'));
    $reviewerLabel = $registrationRole === 'rider'
        ? (string) session('registration_logistics_name', 'your selected Logistics / Sorting Center')
        : 'administrator';

    $roleLabels = [
        'buyer' => 'Buyer',
        'seller' => 'Seller',
        'logistics' => 'Logistics',
        'rider' => 'Rider',
    ];

    $roleDescriptions = [
        'buyer' => 'Purchase products from verified SARI sellers.',
        'seller' => 'List and manage products after account approval.',
        'logistics' => 'Manage delivery operations and registered riders.',
        'rider' => 'Deliver orders under an approved logistics provider.',
    ];

    $flows = [
        'buyer' => [
            ['icon' => 'form', 'label' => 'Register'],
            ['icon' => 'mail', 'label' => 'Verify email'],
            ['icon' => 'shield', 'label' => 'Admin review'],
            ['icon' => 'check', 'label' => 'Approved'],
            ['icon' => 'login', 'label' => 'Can log in'],
        ],
        'seller' => [
            ['icon' => 'form', 'label' => 'Register'],
            ['icon' => 'mail', 'label' => 'Verify email'],
            ['icon' => 'document', 'label' => 'Business review'],
            ['icon' => 'shield', 'label' => 'Admin review'],
            ['icon' => 'check', 'label' => 'Approved'],
            ['icon' => 'login', 'label' => 'Can log in'],
        ],
        'logistics' => [
            ['icon' => 'form', 'label' => 'Register'],
            ['icon' => 'mail', 'label' => 'Verify email'],
            ['icon' => 'building', 'label' => 'Business requirements'],
            ['icon' => 'shield', 'label' => 'Admin review'],
            ['icon' => 'check', 'label' => 'Approved'],
            ['icon' => 'login', 'label' => 'Can log in'],
        ],
        'rider' => [
            ['icon' => 'network', 'label' => 'Choose provider'],
            ['icon' => 'form', 'label' => 'Register'],
            ['icon' => 'mail', 'label' => 'Verify email'],
            ['icon' => 'document', 'label' => 'Document review'],
            ['icon' => 'users', 'label' => 'Provider review'],
            ['icon' => 'check', 'label' => 'Approved'],
            ['icon' => 'login', 'label' => 'Can log in'],
        ],
    ];

    $defaultFlowRole = array_key_exists($registrationRole, $flows) ? $registrationRole : 'buyer';
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        --status-gold: #c9870b;
        --status-gold-dark: #a96e06;
        --status-ink: #1f2328;
        --status-body: #666d76;
        --status-muted: #8b9198;
        --status-line: #e7e2da;
        --status-soft: #faf8f4;
        --status-success: #2f7d50;
        --status-danger: #b84040;
    }

    .sari-status-page,
    .sari-status-page * {
        box-sizing: border-box;
    }

    .sari-status-page {
        min-height: 100vh;
        background: #f7f4ee;
        padding: 34px 16px;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        color: var(--status-ink);
    }

    .sari-status-shell {
        width: min(100%, 820px);
        margin: 0 auto;
    }

    .sari-status-card {
        position: relative;
        overflow: hidden;
        border: 1px solid #e6ded1;
        border-radius: 24px;
        background: #fff;
        padding: 32px;
        box-shadow: 0 18px 55px rgba(40, 32, 20, .08);
    }

    .sari-status-logo {
        display: block;
        width: 156px;
        height: auto;
        margin: 0 auto;
        filter: brightness(0);
    }

    .sari-status-hero {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .sari-status-animation-wrap {
        display: grid;
        width: 104px;
        height: 104px;
        margin: 22px auto 0;
        place-items: center;
    }

    .sari-waiting-animation {
        display: block;
        width: 96px;
        height: 96px;
        object-fit: contain;
    }

    .sari-approved-icon,
    .sari-rejected-icon {
        display: none;
        width: 70px;
        height: 70px;
        place-items: center;
        border-radius: 999px;
    }

    .sari-approved-icon {
        border: 1px solid #cde5d5;
        background: #f1faf4;
        color: var(--status-success);
    }

    .sari-rejected-icon {
        border: 1px solid #eed1d1;
        background: #fff6f6;
        color: var(--status-danger);
    }

    .sari-approved-icon svg,
    .sari-rejected-icon svg {
        width: 32px;
        height: 32px;
    }

    .sari-status-eyebrow {
        margin: 14px 0 0;
        font-size: 10px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: var(--status-gold);
    }

    .sari-status-title {
        margin: 10px 0 0;
        font-size: clamp(27px, 4vw, 36px);
        line-height: 1.18;
        font-weight: 700;
        letter-spacing: -.04em;
        color: var(--status-ink);
    }

    .sari-status-copy {
        width: min(100%, 610px);
        margin: 12px auto 0;
        font-size: 13px;
        line-height: 1.75;
        color: var(--status-body);
    }

    .sari-email-card {
        display: flex;
        width: min(100%, 560px);
        margin: 22px auto 0;
        align-items: center;
        gap: 13px;
        border: 1px solid var(--status-line);
        border-radius: 14px;
        background: #fff;
        padding: 13px 15px;
        text-align: left;
    }

    .sari-email-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border-radius: 11px;
        background: #fbf5e9;
        color: #a96f08;
    }

    .sari-email-icon svg {
        width: 19px;
        height: 19px;
    }

    .sari-email-meta {
        min-width: 0;
    }

    .sari-email-label {
        margin: 0;
        font-size: 10px;
        color: var(--status-muted);
    }

    .sari-email-value {
        margin: 3px 0 0;
        overflow-wrap: anywhere;
        font-size: 12.5px;
        font-weight: 600;
        color: #363b42;
    }

    .sari-rejection-note {
        display: none;
        width: min(100%, 610px);
        margin: 18px auto 0;
        border: 1px solid #efd2d2;
        border-radius: 12px;
        background: #fff8f8;
        padding: 12px 14px;
        text-align: left;
    }

    .sari-rejection-note strong {
        display: block;
        font-size: 11px;
        color: #8f3333;
    }

    .sari-rejection-note p {
        margin: 4px 0 0;
        font-size: 11.5px;
        line-height: 1.6;
        color: #7d5555;
    }

    .sari-details {
        margin-top: 24px;
        border-top: 1px solid #eee9e2;
        padding-top: 18px;
    }

    .sari-details-toggle {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border: 0;
        background: transparent;
        padding: 0;
        color: inherit;
        text-align: left;
        cursor: pointer;
    }

    .sari-details-toggle-copy {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .sari-details-toggle-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border-radius: 10px;
        background: #faf6ee;
        color: #a96f08;
    }

    .sari-details-toggle-icon svg {
        width: 18px;
        height: 18px;
    }

    .sari-details-toggle-title {
        display: block;
        font-size: 13px;
        font-weight: 650;
        color: #292d32;
    }

    .sari-details-toggle-subtitle {
        display: block;
        margin-top: 2px;
        font-size: 10.5px;
        line-height: 1.45;
        color: var(--status-muted);
    }

    .sari-details-chevron {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        color: #8e867b;
        transition: transform .22s ease;
    }

    .sari-details.is-open .sari-details-chevron {
        transform: rotate(180deg);
    }

    .sari-details-body {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        transition: grid-template-rows .28s cubic-bezier(.22,1,.36,1), opacity .2s ease;
    }

    .sari-details.is-open .sari-details-body {
        grid-template-rows: 1fr;
        opacity: 1;
    }

    .sari-details-inner {
        min-height: 0;
        overflow: hidden;
    }

    .sari-role-tabs {
        display: flex;
        gap: 8px;
        margin-top: 18px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: thin;
    }

    .sari-role-tab {
        display: inline-flex;
        min-height: 38px;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;
        border: 1px solid #e4dfd7;
        border-radius: 10px;
        background: #fff;
        padding: 8px 12px;
        font: inherit;
        font-size: 11px;
        font-weight: 600;
        color: #60666d;
        cursor: pointer;
        transition: border-color .18s ease, background-color .18s ease, color .18s ease;
    }

    .sari-role-tab:hover {
        border-color: #d8c59d;
        color: #8b5b06;
    }

    .sari-role-tab.is-active {
        border-color: #d2a24a;
        background: #fffaf1;
        color: #875805;
    }

    .sari-role-tab svg {
        width: 16px;
        height: 16px;
    }

    .sari-role-panel {
        display: none;
        margin-top: 14px;
        border: 1px solid #ece7df;
        border-radius: 14px;
        background: #fcfbf9;
        padding: 16px;
    }

    .sari-role-panel.is-active {
        display: block;
        animation: sariStatusPanelIn .2s ease-out both;
    }

    @keyframes sariStatusPanelIn {
        from { opacity: 0; transform: translateY(3px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sari-role-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .sari-role-panel-title {
        margin: 0;
        font-size: 13px;
        font-weight: 650;
        color: #292d32;
    }

    .sari-role-panel-copy {
        margin: 3px 0 0;
        font-size: 10.5px;
        line-height: 1.5;
        color: #7b828a;
    }

    .sari-current-role-badge {
        display: none;
        flex: 0 0 auto;
        border-radius: 999px;
        background: #f7edd9;
        padding: 5px 8px;
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #98640a;
    }

    .sari-role-panel.is-current-role .sari-current-role-badge {
        display: inline-flex;
    }

    .sari-process {
        display: grid;
        grid-template-columns: repeat(var(--step-count), minmax(86px, 1fr));
        gap: 10px;
        margin-top: 17px;
        overflow-x: auto;
        padding: 3px 1px 6px;
    }

    .sari-process-step {
        position: relative;
        min-width: 86px;
        text-align: center;
    }

    .sari-process-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: calc(50% + 25px);
        width: calc(100% - 50px + 10px);
        height: 1px;
        background: #ddd8d0;
    }

    .sari-process-icon {
        position: relative;
        z-index: 1;
        display: grid;
        width: 40px;
        height: 40px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #ded8ce;
        border-radius: 999px;
        background: #fff;
        color: #806f55;
    }

    .sari-process-icon svg {
        width: 17px;
        height: 17px;
    }

    .sari-role-panel.is-current-role .sari-process-step:nth-child(-n+2) .sari-process-icon {
        border-color: #d6ac60;
        background: #fff9ed;
        color: #a76c08;
    }

    .sari-role-panel.is-current-role .sari-process-step:nth-child(-n+2)::after {
        background: #d6ac60;
    }

    .sari-process-label {
        display: block;
        margin-top: 7px;
        font-size: 9.5px;
        line-height: 1.35;
        font-weight: 550;
        color: #5f656c;
    }

    .sari-status-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 26px;
        border-top: 1px solid #eee9e2;
        padding-top: 20px;
    }

    .sari-status-button {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        padding: 0 20px;
        font-size: 11.5px;
        font-weight: 650;
        text-decoration: none;
        transition: transform .18s ease, background-color .18s ease, border-color .18s ease, color .18s ease;
    }

    .sari-status-button:hover {
        transform: translateY(-1px);
    }

    .sari-status-button-primary {
        border: 1px solid #bc7b06;
        background: var(--status-gold);
        color: #fff;
    }

    .sari-status-button-primary:hover {
        background: var(--status-gold-dark);
    }

    .sari-status-button-secondary {
        border: 1px solid #ded8cf;
        background: #fff;
        color: #555b62;
    }

    .sari-status-button-secondary:hover {
        border-color: #cfc7bc;
        background: #fafafa;
    }

    .sari-confetti-canvas {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: none;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .sari-status-card.is-approved .sari-confetti-canvas {
        display: block;
    }

    .sari-status-card.is-approved .sari-waiting-animation,
    .sari-status-card.is-rejected .sari-waiting-animation {
        display: none;
    }

    .sari-status-card.is-approved .sari-approved-icon {
        display: grid;
    }

    .sari-status-card.is-rejected .sari-rejected-icon {
        display: grid;
    }

    .sari-status-card.is-approved .sari-status-eyebrow {
        color: var(--status-success);
    }

    .sari-status-card.is-rejected .sari-status-eyebrow {
        color: var(--status-danger);
    }

    .sari-status-card.is-rejected .sari-rejection-note {
        display: block;
    }

    .sari-status-card.is-approved .sari-role-panel.is-current-role .sari-process-icon {
        border-color: #9ec7ad;
        background: #f2faf5;
        color: var(--status-success);
    }

    .sari-status-card.is-approved .sari-role-panel.is-current-role .sari-process-step:not(:last-child)::after {
        background: #a8cdb5;
    }

    @media (max-width: 640px) {
        .sari-status-page {
            padding: 18px 11px 24px;
        }

        .sari-status-card {
            border-radius: 18px;
            padding: 24px 17px;
        }

        .sari-status-logo {
            width: 140px;
        }

        .sari-status-animation-wrap {
            width: 92px;
            height: 92px;
            margin-top: 18px;
        }

        .sari-waiting-animation {
            width: 86px;
            height: 86px;
        }

        .sari-status-copy {
            font-size: 12px;
        }

        .sari-status-actions {
            flex-direction: column;
        }

        .sari-status-button {
            width: 100%;
        }

    }

    @media (prefers-reduced-motion: reduce) {
        .sari-waiting-animation,
        .sari-confetti-canvas {
            display: none !important;
        }

        .sari-details-body,
        .sari-details-chevron,
        .sari-role-panel,
        .sari-status-button {
            animation: none !important;
            transition: none !important;
        }
    }

    /* ============================================================
       REGISTRATION STATUS V2 — COMPACT / CLEAN / POPPINS
       Visual-only sizing layer. Status polling and Laravel logic
       remain untouched.
       ============================================================ */

    .sari-status-page {
        min-height: 100vh !important;
        min-height: 100dvh !important;
        padding: 18px 14px 22px !important;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
    }

    .sari-status-shell {
        width: min(100%, 720px) !important;
    }

    .sari-status-card {
        border-radius: 18px !important;
        padding: 24px 26px 22px !important;
        box-shadow: 0 14px 42px rgba(40, 32, 20, .07) !important;
    }

    /* Brand */
    .sari-status-logo {
        width: 120px !important;
        filter: brightness(0) !important;
    }

    /* Status animation / state icon */
    .sari-status-animation-wrap {
        width: 78px !important;
        height: 78px !important;
        margin-top: 14px !important;
    }

    .sari-waiting-animation {
        width: 72px !important;
        height: 72px !important;
    }

    .sari-approved-icon,
    .sari-rejected-icon {
        width: 58px !important;
        height: 58px !important;
    }

    .sari-approved-icon svg,
    .sari-rejected-icon svg {
        width: 25px !important;
        height: 25px !important;
    }

    /* Hero typography */
    .sari-status-eyebrow {
        margin-top: 10px !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .15em !important;
    }

    .sari-status-title {
        max-width: 560px !important;
        margin: 7px auto 0 !important;
        font-size: clamp(21px, 2vw, 25px) !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        letter-spacing: -.032em !important;
    }

    .sari-status-copy {
        width: min(100%, 540px) !important;
        margin-top: 8px !important;
        font-size: 10px !important;
        line-height: 1.65 !important;
        color: #777067 !important;
    }

    /* Email notification card */
    .sari-email-card {
        width: min(100%, 500px) !important;
        margin-top: 15px !important;
        gap: 10px !important;
        border-radius: 11px !important;
        padding: 10px 12px !important;
        background: #fffdf9 !important;
    }

    .sari-email-icon {
        width: 32px !important;
        height: 32px !important;
        flex-basis: 32px !important;
        border-radius: 9px !important;
    }

    .sari-email-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-email-label {
        font-size: 8px !important;
        line-height: 1.35 !important;
    }

    .sari-email-value {
        margin-top: 2px !important;
        font-size: 10.5px !important;
        line-height: 1.4 !important;
        font-weight: 600 !important;
    }

    /* Rejection message */
    .sari-rejection-note {
        width: min(100%, 540px) !important;
        margin-top: 13px !important;
        border-radius: 10px !important;
        padding: 10px 12px !important;
    }

    .sari-rejection-note strong {
        font-size: 9px !important;
    }

    .sari-rejection-note p {
        margin-top: 3px !important;
        font-size: 9.5px !important;
        line-height: 1.5 !important;
    }

    /* Details accordion */
    .sari-details {
        margin-top: 17px !important;
        padding-top: 14px !important;
    }

    .sari-details-toggle {
        gap: 10px !important;
    }

    .sari-details-toggle-copy {
        gap: 9px !important;
    }

    .sari-details-toggle-icon {
        width: 31px !important;
        height: 31px !important;
        flex-basis: 31px !important;
        border-radius: 8px !important;
    }

    .sari-details-toggle-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-details-toggle-title {
        font-size: 10.5px !important;
        font-weight: 600 !important;
    }

    .sari-details-toggle-subtitle {
        margin-top: 1px !important;
        font-size: 8.5px !important;
        line-height: 1.4 !important;
    }

    .sari-details-chevron {
        width: 15px !important;
        height: 15px !important;
        flex-basis: 15px !important;
    }

    /* Role tabs */
    .sari-role-tabs {
        gap: 6px !important;
        margin-top: 13px !important;
    }

    .sari-role-tab {
        min-height: 31px !important;
        gap: 6px !important;
        border-radius: 8px !important;
        padding: 6px 9px !important;
        font-size: 9px !important;
        font-weight: 600 !important;
    }

    .sari-role-tab svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Per-role process panel */
    .sari-role-panel {
        margin-top: 10px !important;
        border-radius: 11px !important;
        padding: 12px !important;
    }

    .sari-role-panel-head {
        gap: 9px !important;
    }

    .sari-role-panel-title {
        font-size: 10.5px !important;
        font-weight: 600 !important;
    }

    .sari-role-panel-copy {
        margin-top: 2px !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    .sari-current-role-badge {
        padding: 4px 6px !important;
        font-size: 6.8px !important;
        letter-spacing: .04em !important;
    }

    .sari-process {
        grid-template-columns: repeat(var(--step-count), minmax(70px, 1fr)) !important;
        gap: 7px !important;
        margin-top: 12px !important;
        padding-bottom: 4px !important;
    }

    .sari-process-step {
        min-width: 70px !important;
    }

    .sari-process-step:not(:last-child)::after {
        top: 16px !important;
        left: calc(50% + 20px) !important;
        width: calc(100% - 40px + 7px) !important;
    }

    .sari-process-icon {
        width: 32px !important;
        height: 32px !important;
    }

    .sari-process-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .sari-process-label {
        margin-top: 5px !important;
        font-size: 7.8px !important;
        line-height: 1.3 !important;
        font-weight: 500 !important;
    }

    /* Bottom actions */
    .sari-status-actions {
        gap: 8px !important;
        margin-top: 18px !important;
        padding-top: 15px !important;
    }

    .sari-status-button {
        min-height: 40px !important;
        border-radius: 9px !important;
        padding: 0 16px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 9.8px !important;
        font-weight: 600 !important;
    }

    /* Common laptop screens, e.g. 1366 × 768 at 100% */
    @media (max-height: 820px) and (min-width: 641px) {
        .sari-status-page {
            padding-top: 10px !important;
            padding-bottom: 12px !important;
        }

        .sari-status-shell {
            width: min(100%, 680px) !important;
        }

        .sari-status-card {
            padding: 18px 22px 17px !important;
        }

        .sari-status-logo {
            width: 105px !important;
        }

        .sari-status-animation-wrap {
            width: 66px !important;
            height: 66px !important;
            margin-top: 10px !important;
        }

        .sari-waiting-animation {
            width: 61px !important;
            height: 61px !important;
        }

        .sari-status-eyebrow {
            margin-top: 7px !important;
        }

        .sari-status-title {
            margin-top: 5px !important;
            font-size: 21px !important;
        }

        .sari-status-copy {
            margin-top: 6px !important;
            font-size: 9.3px !important;
            line-height: 1.55 !important;
        }

        .sari-email-card {
            margin-top: 11px !important;
            padding: 8px 10px !important;
        }

        .sari-details {
            margin-top: 13px !important;
            padding-top: 11px !important;
        }

        .sari-status-actions {
            margin-top: 14px !important;
            padding-top: 12px !important;
        }

        .sari-status-button {
            min-height: 36px !important;
            font-size: 9.2px !important;
        }
    }

    /* Mobile remains compact but readable/tappable */
    @media (max-width: 640px) {
        .sari-status-page {
            padding: 12px 9px 18px !important;
        }

        .sari-status-shell {
            width: 100% !important;
        }

        .sari-status-card {
            border-radius: 15px !important;
            padding: 20px 15px 18px !important;
        }

        .sari-status-logo {
            width: 108px !important;
        }

        .sari-status-animation-wrap {
            width: 70px !important;
            height: 70px !important;
            margin-top: 12px !important;
        }

        .sari-waiting-animation {
            width: 65px !important;
            height: 65px !important;
        }

        .sari-status-title {
            font-size: 21px !important;
        }

        .sari-status-copy {
            font-size: 10px !important;
            line-height: 1.6 !important;
        }

        .sari-email-card {
            width: 100% !important;
        }

        .sari-role-tab {
            min-height: 36px !important;
            font-size: 9.5px !important;
        }

        .sari-process {
            grid-template-columns: repeat(var(--step-count), minmax(68px, 1fr)) !important;
        }

        .sari-status-actions {
            flex-direction: column !important;
        }

        .sari-status-button {
            width: 100% !important;
            min-height: 44px !important;
            font-size: 10.5px !important;
        }
    }

</style>

<div class="sari-status-page">
    <div class="sari-status-shell">
        <main
            id="registrationStatusCard"
            class="sari-status-card"
            data-initial-status="{{ $initialStatus }}"
            data-current-role="{{ $defaultFlowRole }}"
        >
            <canvas
                id="confettiCanvas"
                class="sari-confetti-canvas"
                aria-hidden="true"
            ></canvas>

            <section class="sari-status-hero" aria-live="polite">
                <img src="{{ asset('images/sari-logo.png') }}" alt="SARI" class="sari-status-logo">

                <div class="sari-status-animation-wrap" aria-hidden="true">
                    <video
                        id="waitingAnimation"
                        class="sari-waiting-animation"
                        autoplay
                        loop
                        muted
                        playsinline
                        preload="auto"
                    >
                        <source src="{{ asset('animations/clock-time.webm') }}" type="video/webm">
                    </video>

                    <div id="approvedIcon" class="sari-approved-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m8 12 2.5 2.5L16 9"></path>
                        </svg>
                    </div>

                    <div id="rejectedIcon" class="sari-rejected-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M9.5 9.5 14.5 14.5"></path>
                            <path d="m14.5 9.5-5 5"></path>
                        </svg>
                    </div>
                </div>

                <p id="statusEyebrow" class="sari-status-eyebrow">Application Submitted</p>
                <h1 id="statusTitle" class="sari-status-title">Waiting for {{ $reviewerLabel }} review</h1>
                <p id="statusCopy" class="sari-status-copy">
                    Your SARI {{ $roleLabels[$registrationRole] ?? ucfirst($registrationRole) }} registration has been saved successfully.
                    {{ $registrationRole === 'rider'
                        ? 'Your selected Logistics / Sorting Center must review your information and documents before your account can log in.'
                        : 'The administrator must review your information and uploaded documents before your account can log in.' }}
                    We will send the decision to your verified email when the review is completed.
                </p>

                @if ($decisionEmail !== '')
                    <div class="sari-email-card">
                        <span class="sari-email-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3.5" y="5" width="17" height="14" rx="2"></rect>
                                <path d="m4.5 7 7.5 5.5L19.5 7"></path>
                            </svg>
                        </span>
                        <div class="sari-email-meta">
                            <p class="sari-email-label">Decision email will be sent to</p>
                            <p class="sari-email-value">{{ $decisionEmail }}</p>
                        </div>
                    </div>
                @endif

                <div id="rejectionNote" class="sari-rejection-note">
                    <strong>Review note</strong>
                    <p id="rejectionMessage">Please review the administrator note and submit a corrected registration.</p>
                </div>
            </section>

            <section id="statusDetails" class="sari-details">
                <button
                    id="statusDetailsToggle"
                    class="sari-details-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="statusDetailsBody"
                >
                    <span class="sari-details-toggle-copy">
                        <span class="sari-details-toggle-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 6h12"></path>
                                <path d="M8 12h12"></path>
                                <path d="M8 18h12"></path>
                                <circle cx="4" cy="6" r="1"></circle>
                                <circle cx="4" cy="12" r="1"></circle>
                                <circle cx="4" cy="18" r="1"></circle>
                            </svg>
                        </span>
                        <span>
                            <span class="sari-details-toggle-title">See details</span>
                            <span class="sari-details-toggle-subtitle">View how registration review works for each account type.</span>
                        </span>
                    </span>
                    <svg class="sari-details-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>

                <div id="statusDetailsBody" class="sari-details-body">
                    <div class="sari-details-inner">
                        <div class="sari-role-tabs" role="tablist" aria-label="Registration process by account type">
                            @foreach ($flows as $roleKey => $steps)
                                <button
                                    type="button"
                                    class="sari-role-tab {{ $roleKey === $defaultFlowRole ? 'is-active' : '' }}"
                                    data-role-tab="{{ $roleKey }}"
                                    role="tab"
                                    aria-selected="{{ $roleKey === $defaultFlowRole ? 'true' : 'false' }}"
                                >
                                    @if ($roleKey === 'buyer')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3"></circle><path d="M5.5 20c.8-4 3-6 6.5-6s5.7 2 6.5 6"></path></svg>
                                    @elseif ($roleKey === 'seller')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16"></path><path d="m5 10 1-5h12l1 5"></path><path d="M6 10v9h12v-9"></path><path d="M9 19v-5h6v5"></path></svg>
                                    @elseif ($roleKey === 'logistics')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20h18"></path><path d="M5 20V8l7-4 7 4v12"></path><path d="M9 20v-5h6v5"></path><path d="M8 10h.01M12 10h.01M16 10h.01"></path></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="17" r="3"></circle><circle cx="18" cy="17" r="3"></circle><path d="M10 17h5l-2-5H9l-2 5"></path><path d="M13 12h3l2 5"></path><path d="M11 9h3"></path></svg>
                                    @endif
                                    <span>{{ $roleLabels[$roleKey] }}</span>
                                </button>
                            @endforeach
                        </div>

                        @foreach ($flows as $roleKey => $steps)
                            <article
                                class="sari-role-panel {{ $roleKey === $defaultFlowRole ? 'is-active' : '' }} {{ $roleKey === $registrationRole ? 'is-current-role' : '' }}"
                                data-role-panel="{{ $roleKey }}"
                                role="tabpanel"
                            >
                                <div class="sari-role-panel-head">
                                    <div>
                                        <h2 class="sari-role-panel-title">{{ $roleLabels[$roleKey] }} registration flow</h2>
                                        <p class="sari-role-panel-copy">{{ $roleDescriptions[$roleKey] }}</p>
                                    </div>
                                    <span class="sari-current-role-badge">Your account type</span>
                                </div>

                                <div class="sari-process" style="--step-count: {{ count($steps) }};">
                                    @foreach ($steps as $step)
                                        <div class="sari-process-step">
                                            <span class="sari-process-icon" aria-hidden="true">
                                                @switch($step['icon'])
                                                    @case('form')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6z"></path><path d="M14 3v4h4"></path><path d="M9 12h6M9 16h5"></path></svg>
                                                        @break
                                                    @case('mail')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                                                        @break
                                                    @case('shield')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                                        @break
                                                    @case('check')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
                                                        @break
                                                    @case('login')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5h5v14h-5"></path><path d="M10 8 6 12l4 4"></path><path d="M6 12h10"></path></svg>
                                                        @break
                                                    @case('document')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6z"></path><path d="M14 3v4h4"></path><path d="M9 11h6M9 15h6"></path></svg>
                                                        @break
                                                    @case('building')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20h18"></path><path d="M5 20V8l7-4 7 4v12"></path><path d="M9 20v-5h6v5"></path></svg>
                                                        @break
                                                    @case('network')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="2"></circle><circle cx="5" cy="18" r="2"></circle><circle cx="19" cy="18" r="2"></circle><path d="M12 7v4M12 11 6.5 16M12 11l5.5 5"></path></svg>
                                                        @break
                                                    @case('users')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"></circle><circle cx="17" cy="9" r="2"></circle><path d="M3.5 20c.7-4 2.6-6 5.5-6s4.8 2 5.5 6"></path><path d="M14 14c2.7.2 4.5 1.8 5.3 5"></path></svg>
                                                        @break
                                                @endswitch
                                            </span>
                                            <span class="sari-process-label">{{ $step['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="sari-status-actions">
                <a id="primaryStatusAction" href="{{ route('login') }}" class="sari-status-button sari-status-button-primary">Go to Login</a>
                <a href="{{ route('home') }}" class="sari-status-button sari-status-button-secondary">Back to Home</a>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('registrationStatusCard');
    const waitingAnimation = document.getElementById('waitingAnimation');
    const confettiCanvas = document.getElementById('confettiCanvas');
    const eyebrow = document.getElementById('statusEyebrow');
    const title = document.getElementById('statusTitle');
    const copy = document.getElementById('statusCopy');
    const rejectionMessage = document.getElementById('rejectionMessage');
    const primaryAction = document.getElementById('primaryStatusAction');
    const details = document.getElementById('statusDetails');
    const detailsToggle = document.getElementById('statusDetailsToggle');
    const roleTabs = Array.from(document.querySelectorAll('[data-role-tab]'));
    const rolePanels = Array.from(document.querySelectorAll('[data-role-panel]'));

    const statusUrl = @json(route('registration.status'));
    const loginUrl = @json(route('login'));
    const registerUrl = @json(route('register'));
    const currentRole = card?.dataset.currentRole || 'buyer';
    const roleLabel = @json($roleLabels[$registrationRole] ?? ucfirst($registrationRole));
    const reviewer = @json($reviewerLabel);

    let lastStatus = null;
    let pollTimer = null;

    function setDetailsOpen(open) {
        details?.classList.toggle('is-open', open);
        detailsToggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    detailsToggle?.addEventListener('click', function () {
        setDetailsOpen(!details.classList.contains('is-open'));
    });

    function selectRole(role) {
        roleTabs.forEach(function (tab) {
            const active = tab.dataset.roleTab === role;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        rolePanels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.dataset.rolePanel === role);
        });
    }

    roleTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            selectRole(tab.dataset.roleTab || currentRole);
        });
    });

    selectRole(currentRole);

    let confettiFrame = null;

    function playConfetti() {
        if (!confettiCanvas || !card) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        if (confettiFrame) {
            window.cancelAnimationFrame(confettiFrame);
            confettiFrame = null;
        }

        const ctx = confettiCanvas.getContext('2d');
        if (!ctx) return;

        const rect = card.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);

        confettiCanvas.width = Math.max(1, Math.round(rect.width * dpr));
        confettiCanvas.height = Math.max(1, Math.round(rect.height * dpr));
        confettiCanvas.style.width = rect.width + 'px';
        confettiCanvas.style.height = rect.height + 'px';

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, rect.width, rect.height);

        const colors = [
            '#d48f08',
            '#f0c45c',
            '#fff4d8',
            '#1f2328',
            '#ffffff'
        ];

        const pieces = [];
        const total = rect.width < 560 ? 72 : 104;

        function createPiece(side, index) {
            const fromLeft = side === 'left';

            // Start right on the inner left/right edges of the card,
            // then launch toward the center of the container.
            const edgePadding = rect.width < 560 ? 8 : 12;
            const originX = fromLeft
                ? edgePadding
                : rect.width - edgePadding;

            const originY = Math.min(
                rect.height * 0.34,
                rect.width < 560 ? 190 : 225
            );

            // Canvas Y grows downward:
            // left side = up/right, right side = up/left.
            const angleBase = fromLeft
                ? (-Math.PI * 0.25)
                : (-Math.PI * 0.75);

            const angleSpread = (Math.random() - 0.5) * 0.52;
            const angle = angleBase + angleSpread;
            const speed = 4.8 + Math.random() * 3.5;

            return {
                x: originX,
                y: originY + (Math.random() - 0.5) * 28,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                gravity: 0.105 + Math.random() * 0.045,
                drag: 0.992,
                rotation: Math.random() * Math.PI,
                rotationSpeed: (Math.random() - 0.5) * 0.20,
                width: 4 + Math.random() * 4,
                height: 7 + Math.random() * 6,
                color: colors[index % colors.length],
                opacity: 0.96,
                wobble: Math.random() * Math.PI * 2,
                wobbleSpeed: 0.05 + Math.random() * 0.045,
                wobbleAmount: 0.28 + Math.random() * 0.38
            };
        }

        for (let i = 0; i < total; i++) {
            pieces.push(createPiece(i % 2 === 0 ? 'left' : 'right', i));
        }

        const startedAt = performance.now();
        const duration = 2600;

        function drawPiece(piece) {
            ctx.save();
            ctx.globalAlpha = Math.max(0, piece.opacity);
            ctx.translate(piece.x, piece.y);
            ctx.rotate(piece.rotation);

            ctx.fillStyle = piece.color;
            ctx.fillRect(
                -piece.width / 2,
                -piece.height / 2,
                piece.width,
                piece.height
            );

            ctx.restore();
        }

        function tick(now) {
            const elapsed = now - startedAt;
            const progress = Math.min(1, elapsed / duration);

            ctx.clearRect(0, 0, rect.width, rect.height);

            pieces.forEach(function (piece) {
                piece.vx *= piece.drag;
                piece.vy = (piece.vy * piece.drag) + piece.gravity;
                piece.wobble += piece.wobbleSpeed;

                piece.x += piece.vx + Math.sin(piece.wobble) * piece.wobbleAmount;
                piece.y += piece.vy;
                piece.rotation += piece.rotationSpeed;

                if (progress > 0.68) {
                    piece.opacity = Math.max(0, 1 - ((progress - 0.68) / 0.32));
                }

                drawPiece(piece);
            });

            if (elapsed < duration) {
                confettiFrame = window.requestAnimationFrame(tick);
                return;
            }

            ctx.clearRect(0, 0, rect.width, rect.height);
            confettiFrame = null;
        }

        confettiFrame = window.requestAnimationFrame(tick);
    }

    function stopWaitingAnimation() {
        if (!waitingAnimation) return;
        try { waitingAnimation.pause(); } catch (error) {}
    }

    function startWaitingAnimation() {
        if (!waitingAnimation) return;
        try {
            const result = waitingAnimation.play();
            if (result && typeof result.catch === 'function') {
                result.catch(function () {});
            }
        } catch (error) {}
    }

    function applyStatus(status, payload = {}, playEffects = true) {
        const normalized = String(status || 'pending').toLowerCase();
        const previous = lastStatus;
        lastStatus = normalized;

        card.classList.toggle('is-approved', normalized === 'approved');
        card.classList.toggle('is-rejected', normalized === 'rejected');

        if (normalized === 'approved') {
            stopWaitingAnimation();
            eyebrow.textContent = 'Registration Approved';
            title.textContent = 'Your account is ready';
            copy.textContent = 'Your SARI ' + roleLabel + ' registration has been approved. You can now sign in using the email and password you submitted.';
            primaryAction.href = loginUrl;
            primaryAction.textContent = 'Go to Login';

            if (playEffects && previous !== 'approved') {
                playConfetti();
            }

            return;
        }

        if (normalized === 'rejected') {
            stopWaitingAnimation();
            eyebrow.textContent = 'Review Completed';
            title.textContent = 'Registration needs changes';
            copy.textContent = 'Your SARI ' + roleLabel + ' registration was not approved. Review the note below, then submit a corrected registration when you are ready.';
            rejectionMessage.textContent = payload.admin_note || 'Please review your registration information and submitted documents before trying again.';
            primaryAction.href = registerUrl;
            primaryAction.textContent = 'Register Again';
            return;
        }

        card.classList.remove('is-approved', 'is-rejected');
        startWaitingAnimation();
        eyebrow.textContent = 'Application Submitted';
        title.textContent = 'Waiting for ' + reviewer + ' review';
        copy.textContent = roleLabel === 'Rider'
            ? 'Your SARI Rider registration has been saved successfully. Your selected Logistics / Sorting Center must review your information and documents before your account can log in. We will send the decision to your verified email when the review is completed.'
            : 'Your SARI ' + roleLabel + ' registration has been saved successfully. The administrator must review your information and uploaded documents before your account can log in. We will send the decision to your verified email when the review is completed.';
        primaryAction.href = loginUrl;
        primaryAction.textContent = 'Go to Login';
    }

    async function checkStatus() {
        try {
            const response = await fetch(statusUrl, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (response.status === 404 || response.status === 419) {
                window.clearInterval(pollTimer);
                return;
            }

            if (!response.ok) return;

            const data = await response.json();
            applyStatus(data.status, data, true);

            if (data.status === 'approved' || data.status === 'rejected') {
                window.clearInterval(pollTimer);
            }
        } catch (error) {
            // Keep the pending page usable when the network briefly drops.
        }
    }

    applyStatus(card?.dataset.initialStatus || 'pending', {
        admin_note: @json($application?->admin_note)
    }, true);

    checkStatus();
    pollTimer = window.setInterval(checkStatus, 3000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && lastStatus === 'pending') {
            checkStatus();
        }
    });
});
</script>
@endsection
