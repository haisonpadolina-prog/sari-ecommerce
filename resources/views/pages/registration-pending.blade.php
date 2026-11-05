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
            ['icon' => 'check', 'label' => 'Account activated'],
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
    /* Registration status — centered SARI card, shared across account roles. */
    :root {
        --status-gold: #c88100;
        --status-gold-dark: #9c6500;
        --status-ink: #23252b;
        --status-body: #5a6270;
        --status-muted: #7e8794;
        --status-line: #e5e9ef;
        --status-soft: #f7f9fc;
        --status-success: #24764a;
        --status-danger: #b53c42;
    }

    .sari-status-page,
    .sari-status-page * { box-sizing: border-box; }

    .sari-status-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        min-height: 100dvh;
        width: 100%;
        padding: 32px 18px;
        margin: 0;
        background-color: #e7e2dd;
        background-image: url('{{ asset('images/login-bg.png') }}');
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        color: var(--status-ink);
    }

    .sari-status-shell {
        width: min(100%, 640px);
        margin: 0 auto;
    }

    .sari-status-card {
        position: relative;
        overflow: hidden;
        width: 100%;
        padding: 30px 38px 27px;
        border: 1px solid #e7e9ed;
        border-radius: 19px;
        background: #ffffff;
        box-shadow: 0 21px 58px rgba(28, 34, 43, 0.13), 0 2px 8px rgba(28, 34, 43, 0.04);
    }

    .sari-status-hero {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    /* No logo box; the existing PNG is shown on its own. */
    .sari-status-logo {
        display: block;
        width: 127px;
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        filter: brightness(0);
    }

    .sari-status-animation-wrap {
        display: grid;
        place-items: center;
        width: 78px;
        height: 78px;
        margin: 18px auto 0;
    }

    .sari-waiting-animation {
        display: block;
        width: 76px;
        height: 76px;
        object-fit: contain;
    }

    .sari-approved-icon,
    .sari-rejected-icon {
        display: none;
        width: 62px;
        height: 62px;
        place-items: center;
        border-radius: 50%;
    }

    .sari-approved-icon {
        border: 1px solid #cae7d3;
        background: #edf8f1;
        color: var(--status-success);
    }

    .sari-rejected-icon {
        border: 1px solid #f0d2d4;
        background: #fff4f4;
        color: var(--status-danger);
    }

    .sari-approved-icon svg,
    .sari-rejected-icon svg { width: 30px; height: 30px; }

    .sari-status-eyebrow {
        margin: 13px 0 0;
        color: var(--status-gold-dark);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .14em;
        line-height: 1.5;
        text-transform: uppercase;
    }

    .sari-status-title {
        margin: 7px auto 0;
        max-width: 530px;
        color: #20242a;
        font-size: clamp(23px, 3.3vw, 29px);
        font-weight: 700;
        letter-spacing: -.035em;
        line-height: 1.27;
    }

    .sari-status-copy {
        max-width: 505px;
        margin: 12px auto 0;
        color: var(--status-body);
        font-size: 12.5px;
        line-height: 1.75;
    }

    /* Email information stays visible without an icon box or colored panel. */
    .sari-email-card {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: min(100%, 470px);
        margin: 20px auto 0;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        text-align: left;
    }

    .sari-email-icon {
        display: flex;
        flex: 0 0 20px;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        box-shadow: none;
        color: var(--status-gold-dark);
    }

    .sari-email-icon svg { display: block; width: 20px; height: 20px; }
    .sari-email-meta { min-width: 0; }

    .sari-email-label {
        margin: 0;
        color: #687384;
        font-size: 11px;
        line-height: 1.45;
    }

    .sari-email-value {
        margin: 3px 0 0;
        color: #222931;
        font-size: 13px;
        font-weight: 650;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .sari-rejection-note {
        display: none;
        width: min(100%, 505px);
        margin: 15px auto 0;
        padding: 12px 14px;
        border: 1px solid #f1cdcf;
        border-radius: 11px;
        background: #fff7f7;
        text-align: left;
    }

    .sari-rejection-note strong {
        display: block;
        color: #963036;
        font-size: 12px;
    }

    .sari-rejection-note p {
        margin: 4px 0 0;
        color: #75464a;
        font-size: 11.5px;
        line-height: 1.6;
    }

    /* Clean, accessible registration-process accordion. */
    .sari-details {
        margin-top: 23px;
        padding-top: 16px;
        border-top: 1px solid var(--status-line);
    }

    .sari-details-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e6eaf0;
        border-radius: 11px;
        background: #f8fafc;
        color: inherit;
        font: inherit;
        text-align: left;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease;
    }

    .sari-details-toggle:hover,
    .sari-details.is-open .sari-details-toggle {
        border-color: #dae1eb;
        background: #f4f7fa;
    }

    .sari-details-toggle-copy {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sari-details-toggle-icon {
        display: flex;
        flex: 0 0 20px;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        color: #677385;
    }

    .sari-details-toggle-icon svg { display: block; width: 19px; height: 19px; }

    .sari-details-toggle-title {
        display: block;
        color: #252a33;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }

    .sari-details-toggle-subtitle {
        display: block;
        margin-top: 2px;
        color: #748090;
        font-size: 11px;
        line-height: 1.55;
    }

    .sari-details-chevron {
        flex: 0 0 18px;
        width: 18px;
        height: 18px;
        color: #768393;
        transition: transform .2s ease;
    }

    .sari-details.is-open .sari-details-chevron { transform: rotate(180deg); }

    .sari-details-body {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        transition: grid-template-rows .25s ease, opacity .2s ease;
    }

    .sari-details.is-open .sari-details-body {
        grid-template-rows: 1fr;
        opacity: 1;
    }

    .sari-details-inner { min-height: 0; overflow: hidden; }

    /* Four equal-width role buttons; two-by-two on small phones. */
    .sari-role-tabs {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 7px;
        margin-top: 14px;
    }

    .sari-role-tab {
        display: inline-flex;
        min-width: 0;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 7px 9px;
        border: 1px solid #e2e7ef;
        border-radius: 9px;
        background: #fff;
        color: #5b6573;
        font: inherit;
        font-size: 11px;
        font-weight: 650;
        line-height: 1.3;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }

    .sari-role-tab:hover { border-color: #d7aa5b; color: #8c5b08; }
    .sari-role-tab.is-active {
        border-color: #d6a659;
        background: #fff8ec;
        color: #825300;
    }

    .sari-role-tab svg {
        display: block;
        flex: 0 0 16px;
        width: 16px;
        height: 16px;
    }

    .sari-role-panel {
        display: none;
        margin-top: 11px;
        padding: 18px;
        border: 1px solid #e6eaf0;
        border-radius: 11px;
        background: #fff;
    }

    .sari-role-panel.is-active { display: block; }

    .sari-role-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .sari-role-panel-title {
        margin: 0;
        color: #252b33;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.45;
    }

    .sari-role-panel-copy {
        margin: 4px 0 0;
        color: #6c7685;
        font-size: 11.5px;
        line-height: 1.55;
    }

    .sari-current-role-badge {
        display: none;
        flex: 0 0 auto;
        padding: 5px 9px;
        border: 1px solid #f0d7a4;
        border-radius: 999px;
        background: #fff9ee;
        color: #8c5b08;
        font-size: 10px;
        font-weight: 650;
        white-space: nowrap;
    }

    .sari-role-panel.is-current-role .sari-current-role-badge { display: inline-flex; }

    /* Connected timeline on desktops. Steps are labels, not clickable actions. */
    .sari-process {
        display: grid;
        grid-template-columns: repeat(var(--step-count), minmax(0, 1fr));
        gap: 0;
        margin-top: 19px;
        padding: 0 0 3px;
    }

    .sari-process-step {
        position: relative;
        min-width: 0;
        padding: 0 2px;
        text-align: center;
    }

    .sari-process-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 16px;
        left: calc(50% + 18px);
        width: calc(100% - 36px);
        height: 2px;
        background: #e2e6ed;
    }

    .sari-process-icon {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        width: 33px;
        height: 33px;
        margin: 0 auto;
        border: 1px solid #dbe2e9;
        border-radius: 50%;
        background: #fff;
        color: #748091;
    }

    .sari-process-icon svg { width: 16px; height: 16px; }

    .sari-process-label {
        display: block;
        margin-top: 8px;
        color: #566171;
        font-size: 10px;
        font-weight: 550;
        line-height: 1.45;
        overflow-wrap: normal;
    }

    .sari-role-panel.is-current-role .sari-process-step:nth-child(-n+2) .sari-process-icon {
        border-color: #e5bd79;
        background: #fff8eb;
        color: #99620a;
    }

    .sari-role-panel.is-current-role .sari-process-step:nth-child(-n+2)::after { background: #e4bf84; }

    .sari-status-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
        padding-top: 19px;
        border-top: 1px solid var(--status-line);
    }

    .sari-status-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 142px;
        min-height: 42px;
        padding: 0 17px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.35;
        text-align: center;
        text-decoration: none;
        transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
    }

    .sari-status-button:hover { transform: translateY(-1px); }

    .sari-status-button-primary {
        border: 1px solid #bf7b00;
        background: var(--status-gold);
        color: #fff;
    }

    .sari-status-button-primary:hover { background: var(--status-gold-dark); color: #fff; }

    .sari-status-button-secondary {
        border: 1px solid #d7dee7;
        background: #fff;
        color: #404a57;
    }

    .sari-status-button-secondary:hover { border-color: #b7c2ce; background: #f7f9fc; }

    .sari-details-toggle:focus-visible,
    .sari-role-tab:focus-visible,
    .sari-status-button:focus-visible {
        outline: 3px solid #d69c37;
        outline-offset: 3px;
    }


    /* Approval result modal - independent of the review-details panel. */
    .sari-approval-overlay[hidden] { display: none !important; }
    .sari-approval-overlay,
    .sari-approval-overlay * { box-sizing: border-box; }
    .sari-approval-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(20, 27, 37, .57);
        backdrop-filter: blur(3px);
    }
    .sari-approval-dialog {
        width: min(100%, 405px);
        border: 1px solid #e2e7ec;
        border-radius: 17px;
        background: #fff;
        padding: 27px 27px 25px;
        box-shadow: 0 22px 68px rgba(0, 0, 0, .24);
        color: #20252b;
        text-align: center;
    }
    .sari-approval-check {
        display: grid;
        place-items: center;
        width: 65px;
        height: 65px;
        margin: 0 auto 16px;
        border-radius: 50%;
        background: #edf8f1;
        color: #24764a;
    }
    .sari-approval-check svg { width: 34px; height: 34px; }
    .sari-approval-overline {
        margin: 0 0 7px;
        color: #217348;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }
    .sari-approval-dialog h2 {
        margin: 0;
        font-size: clamp(22px, 5vw, 27px);
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.025em;
    }
    .sari-approval-description {
        margin: 11px 0 23px;
        color: #586272;
        font-size: 13px;
        line-height: 1.75;
    }
    /* Compact, content-sized modal actions instead of two stretched blocks. */
    .sari-approval-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .sari-approval-actions a,
    .sari-approval-actions button {
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 1 130px;
        width: auto;
        min-width: 110px;
        max-width: 145px;
        height: 38px;
        min-height: 38px;
        margin: 0;
        padding: 0 12px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 600;
        text-decoration: none;
        box-shadow: none;
        appearance: none;
        cursor: pointer;
    }
    .sari-approval-dismiss { border: 1px solid #dbe2eb; background: #fff; color: #405063; }
    .sari-approval-login { border: 1px solid #c88100; background: #c88100; color: #fff !important; }
    .sari-approval-login:hover { background: #a76800; border-color: #a76800; }
    .sari-approval-actions :focus-visible { outline: 2px solid #24764a; outline-offset: 2px; }
    @media (max-width: 420px) {
        .sari-approval-dialog { padding: 24px 18px 22px; }
        .sari-approval-actions { flex-wrap: nowrap; gap: 8px; }
        .sari-approval-actions a,
        .sari-approval-actions button { flex: 1 1 0; min-width: 0; max-width: none; }
    }

    /* Optional sound unlock: icon-only on the WAITING page, not in the approval modal. */
    .sari-approval-sound-enable {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid #e1e7ee;
        border-radius: 9px;
        background: #fff;
        color: #637184;
        cursor: pointer;
    }
    .sari-approval-sound-enable svg { width: 17px; height: 17px; }
    .sari-approval-sound-enable:hover { background: #f5f8fa; }
    .sari-approval-sound-enable[aria-pressed="true"] {
        border-color: #b9dcca;
        background: #eef8f2;
        color: #24764a;
    }
    .sari-approval-sound-enable:focus-visible { outline: 2px solid #24764a; outline-offset: 2px; }
    .sari-status-card.is-approved .sari-approval-sound-enable,
    .sari-status-card.is-rejected .sari-approval-sound-enable { display: none; }
    @media (prefers-reduced-motion: reduce) {
        .sari-approval-overlay { backdrop-filter: none; }
    }

    /* Existing confetti / approval and rejection status behavior. */
    .sari-confetti-canvas {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: none;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .sari-status-card.is-approved .sari-confetti-canvas { display: block; }
    .sari-status-card.is-approved .sari-waiting-animation,
    .sari-status-card.is-rejected .sari-waiting-animation { display: none; }
    .sari-status-card.is-approved .sari-approved-icon { display: grid; }
    .sari-status-card.is-rejected .sari-rejected-icon { display: grid; }
    .sari-status-card.is-approved .sari-status-eyebrow { color: var(--status-success); }
    .sari-status-card.is-rejected .sari-status-eyebrow { color: var(--status-danger); }
    .sari-status-card.is-rejected .sari-rejection-note { display: block; }
    .sari-status-card.is-approved .sari-role-panel.is-current-role .sari-process-icon {
        border-color: #a7d1b7;
        background: #effaf3;
        color: var(--status-success);
    }
    .sari-status-card.is-approved .sari-role-panel.is-current-role .sari-process-step:not(:last-child)::after {
        background: #add2ba;
    }

    @media (max-width: 640px) {
        .sari-status-page { padding: 22px 12px; background-position: 62% center; }
        .sari-status-shell { width: 100%; }
        .sari-status-card { padding: 24px 19px; border-radius: 16px; }
        .sari-status-logo { width: 115px; }
        .sari-status-animation-wrap { width: 68px; height: 68px; margin-top: 14px; }
        .sari-waiting-animation { width: 67px; height: 67px; }
        .sari-status-title { font-size: clamp(21px, 6vw, 26px); }
        .sari-status-copy { font-size: 12px; }
        .sari-email-card { width: 100%; }
        .sari-role-panel-head { flex-wrap: wrap; }
        .sari-role-tabs { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sari-role-panel { padding: 16px; }
        .sari-process {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-top: 17px;
            padding: 0;
        }
        .sari-process-step {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding: 0 0 13px;
            text-align: left;
        }
        .sari-process-step:last-child { padding-bottom: 0; min-height: 33px; }
        .sari-process-step:not(:last-child)::after {
            top: 34px;
            left: 16px;
            width: 2px;
            height: calc(100% - 33px);
        }
        .sari-process-icon { flex: 0 0 33px; margin: 0; }
        .sari-process-label { margin: 0; font-size: 11.5px; }
        .sari-status-actions { gap: 9px; }
        .sari-status-button { flex: 1 1 130px; min-width: 0; min-height: 43px; }
    }

    @media (max-width: 390px) {
        .sari-status-card { padding: 22px 15px; }
        .sari-role-tab { padding: 7px 6px; gap: 5px; font-size: 10.5px; }
        .sari-role-panel-head { display: block; }
        .sari-current-role-badge { margin-top: 9px; }

        .sari-status-actions { flex-direction: column; }
        .sari-status-button { flex: initial; width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-waiting-animation,
        .sari-confetti-canvas { display: none !important; }
        .sari-details-body,
        .sari-details-chevron,
        .sari-role-tab,
        .sari-status-button { animation: none !important; transition: none !important; }
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
            <button
                id="sariApprovalSoundEnable"
                type="button"
                class="sari-approval-sound-enable"
                aria-label="Enable approval notification sound"
                title="Enable approval sound"
                aria-pressed="false"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>
            </button>

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
                                    id="sari-tab-{{ $roleKey }}"
                                    aria-controls="sari-panel-{{ $roleKey }}"
                                    tabindex="{{ $roleKey === $defaultFlowRole ? '0' : '-1' }}"
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
                                id="sari-panel-{{ $roleKey }}"
                                aria-labelledby="sari-tab-{{ $roleKey }}"
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


<!-- Shared Seller/Logistics/Rider registration approval confirmation. -->
<div id="sariApprovalModal" class="sari-approval-overlay" hidden>
    <section
        id="sariApprovalDialog"
        class="sari-approval-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sariApprovalTitle"
        aria-describedby="sariApprovalDescription"
        tabindex="-1"
    >
        <span class="sari-approval-check" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m7.5 12 3 3 6-6"></path>
            </svg>
        </span>
        <p class="sari-approval-overline">Registration Approved</p>
        <h2 id="sariApprovalTitle">Your account is ready!</h2>
        <p id="sariApprovalDescription" class="sari-approval-description">
            Your SARI {{ $roleLabels[$registrationRole] ?? ucfirst($registrationRole) }} account has been approved.
            You can now log in with your registered email and password.
        </p>
        <div class="sari-approval-actions">
            <button id="sariApprovalClose" type="button" class="sari-approval-dismiss">Close</button>
            <a id="sariApprovalLogin" class="sari-approval-login" href="{{ route('login') }}">Go to Login</a>
        </div>
    </section>
</div>
<audio id="sariApprovalSound" preload="auto" src="{{ asset('sounds/buyer/notification-success.wav') }}"></audio>

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
    const approvalModal = document.getElementById('sariApprovalModal');
    const approvalDialog = document.getElementById('sariApprovalDialog');
    const approvalClose = document.getElementById('sariApprovalClose');
    const approvalLogin = document.getElementById('sariApprovalLogin');
    const approvalSound = document.getElementById('sariApprovalSound');
    const approvalSoundEnable = document.getElementById('sariApprovalSoundEnable');
    const trackedApplicationId = @json($application?->id);
    let approvalModalPreviousFocus = null;
    let approvalSoundPlayed = false;
    let approvalSoundBlocked = false;
    let approvalAudioContext = null;
    let approvalAudioBufferPromise = null;
    let approvalAudioBuffer = null;


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
            tab.setAttribute('tabindex', active ? '0' : '-1');
        });

        rolePanels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.dataset.rolePanel === role);
        });
    }

    roleTabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            selectRole(tab.dataset.roleTab || currentRole);
        });

        // Arrow-key navigation for the account-type tabs.
        tab.addEventListener('keydown', function (event) {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            const nextIndex = event.key === 'Home' ? 0
                : event.key === 'End' ? roleTabs.length - 1
                : (index + (event.key === 'ArrowRight' ? 1 : -1) + roleTabs.length) % roleTabs.length;
            const nextTab = roleTabs[nextIndex];
            selectRole(nextTab.dataset.roleTab || currentRole);
            nextTab.focus();
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

    // The page polls every three seconds. This popup and sound are one-time per
    // approved application in this browser tab, not on every poll or refresh.
    function approvalAlreadyShown(applicationId) {
        if (!applicationId) return false;
        try {
            return window.sessionStorage.getItem('sari_approval_shown_' + applicationId) === '1';
        } catch (error) {
            return false;
        }
    }

    function rememberApprovalShown(applicationId) {
        if (!applicationId) return;
        try {
            window.sessionStorage.setItem('sari_approval_shown_' + applicationId, '1');
        } catch (error) {
            // Keep the modal functional in privacy-restricted browsers.
        }
    }

    // Browsers often block sound triggered by an automatic status poll. A user
    // gesture on this page unlocks Web Audio so the success WAV can play later.
    // The small bell is optional; interacting with the page also unlocks it.
    function armApprovalSound() {
        if (approvalAudioContext || lastStatus === 'approved' || lastStatus === 'rejected') return;

        const Context = window.AudioContext || window.webkitAudioContext;
        if (!Context || !approvalSound) return;
        try {
            approvalAudioContext = new Context();
            // Must be called directly inside the click/key event, before awaits.
            const resumed = approvalAudioContext.resume();
            if (resumed && typeof resumed.catch === 'function') {
                resumed.catch(function () { /* Automatic HTML audio is still attempted. */ });
            }

            approvalSoundEnable?.setAttribute('aria-pressed', 'true');
            approvalSoundEnable?.setAttribute('title', 'Approval sound enabled');
            approvalSoundEnable?.setAttribute('aria-label', 'Approval sound enabled');

            // Decode the existing file once so it is ready before Admin approves.
            approvalAudioBufferPromise = fetch(approvalSound.src, {
                credentials: 'same-origin',
                cache: 'force-cache'
            }).then(function (response) {
                if (!response.ok) throw new Error('Approval audio not found');
                return response.arrayBuffer();
            }).then(function (bytes) {
                return approvalAudioContext.decodeAudioData(bytes);
            }).then(function (buffer) {
                approvalAudioBuffer = buffer;
                return buffer;
            }).catch(function () {
                approvalAudioBufferPromise = null;
                return null;
            });
        } catch (error) {
            approvalAudioContext = null;
        }
    }

    approvalSoundEnable?.addEventListener('click', armApprovalSound);
    // Any intentional user interaction can enable future approval audio.
    document.addEventListener('pointerdown', armApprovalSound, { once: true, capture: true });
    document.addEventListener('keydown', armApprovalSound, { once: true, capture: true });

    function playUnlockedApprovalSound() {
        if (approvalSoundPlayed || !approvalAudioContext || !approvalAudioBuffer ||
                approvalAudioContext.state !== 'running') return false;
        try {
            const source = approvalAudioContext.createBufferSource();
            source.buffer = approvalAudioBuffer;
            source.connect(approvalAudioContext.destination);
            source.start();
            approvalSoundPlayed = true;
            approvalSoundBlocked = false;
            return true;
        } catch (error) {
            return false;
        }
    }

    function playApprovalSound() {
        if (approvalSoundPlayed || !approvalSound) return;
        if (playUnlockedApprovalSound()) return;

        if (approvalAudioContext && approvalAudioBufferPromise &&
                approvalAudioContext.state === 'running') {
            // Handle approval arriving while the WAV is still decoding.
            approvalAudioBufferPromise.then(function (buffer) {
                if (!approvalSoundPlayed && buffer && !approvalModal.hidden) {
                    playUnlockedApprovalSound();
                }
            });
            return;
        }

        // Best effort if the user did not enable audio: browsers may block it.
        try {
            approvalSound.currentTime = 0;
            const playing = approvalSound.play();
            if (playing && typeof playing.then === 'function') {
                playing.then(function () {
                    approvalSoundPlayed = true;
                    approvalSoundBlocked = false;
                }).catch(function () {
                    approvalSoundBlocked = true;
                });
            } else {
                approvalSoundPlayed = true;
            }
        } catch (error) {
            approvalSoundBlocked = true;
        }
    }

    function closeApprovalModal() {
        if (!approvalModal || approvalModal.hidden) return;
        approvalModal.hidden = true;
        approvalModalPreviousFocus?.focus?.();
        approvalModalPreviousFocus = null;
    }

    function showApprovalModal(payload) {
        if (!approvalModal) return;
        const applicationId = payload.id || trackedApplicationId;
        if (approvalAlreadyShown(applicationId)) return;
        rememberApprovalShown(applicationId);

        approvalModalPreviousFocus = document.activeElement;
        approvalModal.hidden = false;
        // Focus the dialog, not the Login button (avoids the thick focus ring).
        approvalDialog?.focus({ preventScroll: true });
        playApprovalSound();
    }

    // If the browser blocked autoplay, retry on the first gesture in the modal.
    // No visible replay button or sound caption is added to the dialog.
    approvalModal?.addEventListener('pointerdown', function () {
        if (approvalSoundBlocked && !approvalSoundPlayed) playApprovalSound();
    }, { capture: true });
    approvalModal?.addEventListener('keydown', function () {
        if (approvalSoundBlocked && !approvalSoundPlayed) playApprovalSound();
    }, { capture: true });

    approvalClose?.addEventListener('click', closeApprovalModal);
    approvalModal?.addEventListener('click', function (event) {
        if (event.target === approvalModal) closeApprovalModal();
    });
    document.addEventListener('keydown', function (event) {
        if (!approvalModal || approvalModal.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeApprovalModal();
            return;
        }
        if (event.key !== 'Tab') return;
        const focusables = [approvalClose, approvalLogin].filter(Boolean);
        if (!focusables.length) return;
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

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
                if (!approvalAlreadyShown(payload.id || trackedApplicationId)) {
                    playConfetti();
                    showApprovalModal(payload);
                }
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
