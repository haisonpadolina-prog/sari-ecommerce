@extends('layouts.admin')

@section('title','Platform Settings — SARI Admin')
@section('page-title','Platform Settings')

@section('content')
@php
    $boolValue = function (string $key, bool $default = false) use ($settings): bool {
        $value = old($key, ($settings[$key] ?? $default) ? 1 : 0);
        return (string) $value === '1';
    };

    $formatSetting = function (string $key, mixed $value): string {
        if (in_array($key, [
            'checkout_enabled',
            'registrations_enabled',
            'registration_decision_email_enabled',
            'maintenance_mode',
        ], true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Disabled';
        }

        if ($key === 'commission_rate') {
            return number_format((float) $value, 2) . '%';
        }

        if (in_array($key, ['delivery_fee_per_seller','rider_payout_minimum'], true)) {
            return '₱' . number_format((float) $value, 2);
        }

        if ($key === 'buyer_cancellation_window_minutes') {
            return (int) $value === 0 ? 'No time limit' : number_format((int) $value) . ' min';
        }

        if ($key === 'max_sellers_per_checkout') {
            return (int) $value === 0 ? 'Unlimited' : number_format((int) $value);
        }

        if ($key === 'seller_settlement_hold_days') {
            return number_format((int) $value) . ' days';
        }

        return (string) $value;
    };

    $activeVersionLabel = $activeVersion
        ? 'v' . $activeVersion->version_number
        : 'Legacy';

    $effectiveDefault = old(
        'effective_at',
        now()->format('Y-m-d\TH:i')
    );
@endphp

<style>
    :root{
        --ps-bg:#f6f7f9;
        --ps-surface:#ffffff;
        --ps-surface-2:#fafafa;
        --ps-ink:#171717;
        --ps-text:#4b5563;
        --ps-muted:#7a7f87;
        --ps-line:#e5e7eb;
        --ps-line-strong:#d7d9de;
        --ps-gold:#c58d20;
        --ps-gold-strong:#a97012;
        --ps-gold-soft:#fff8e8;
        --ps-success:#5f7d63;
        --ps-danger:#b65f52;
        --ps-shadow:0 8px 24px rgba(17,24,39,.045);
        --ps-shadow-strong:0 18px 48px rgba(17,24,39,.10);
    }

    .platform-settings-page{
        width:100%;
        max-width:none;
        margin:0;
        padding:2px 20px 44px;
        color:var(--ps-ink);
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    }

    .ps-header{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:20px;
        margin-bottom:18px;
    }
    .ps-header-left{
        display:flex;
        min-width:0;
        align-items:center;
        gap:14px;
    }
    .ps-header-icon{
        display:grid;
        width:48px;
        height:48px;
        flex:0 0 48px;
        place-items:center;
        border:1px solid var(--ps-line);
        border-radius:14px;
        background:#fff;
        color:var(--ps-gold-strong);
        box-shadow:var(--ps-shadow);
    }
    .ps-header-icon svg{width:22px;height:22px}
    .ps-eyebrow{
        margin:0;
        color:#7c8188;
        font-size:10px;
        font-weight:750;
        letter-spacing:.10em;
        text-transform:uppercase;
    }
    .ps-title{
        margin:3px 0 0;
        font-size:clamp(1.9rem,1.7rem + .55vw,2.35rem);
        font-weight:750;
        line-height:1.08;
        letter-spacing:-.04em;
    }
    .ps-title-platform{color:#171717}
    .ps-title-gold{color:var(--ps-gold)}
    .ps-subtitle{
        max-width:830px;
        margin:8px 0 0;
        color:#666c74;
        font-size:13px;
        line-height:1.65;
    }

    .ps-header-actions{
        display:flex;
        align-items:center;
        gap:9px;
        flex-wrap:wrap;
        justify-content:flex-end;
    }

    .ps-badge{
        display:inline-flex;
        min-height:34px;
        align-items:center;
        gap:7px;
        border:1px solid var(--ps-line);
        border-radius:999px;
        background:#fff;
        padding:0 11px;
        color:#5f6368;
        font-size:9.5px;
        font-weight:700;
        white-space:nowrap;
        box-shadow:0 3px 10px rgba(17,24,39,.035);
    }
    .ps-badge::before{
        content:"";
        width:7px;
        height:7px;
        border-radius:50%;
        background:var(--ps-gold);
        box-shadow:0 0 0 3px rgba(197,141,32,.10);
    }

    .ps-alert{
        margin-bottom:14px;
        border:1px solid #d7e5d6;
        border-radius:12px;
        background:#f6faf5;
        padding:11px 13px;
        color:#55705a;
        font-size:10.5px;
        font-weight:650;
    }
    .ps-errors{
        margin-bottom:14px;
        border:1px solid #edc9c3;
        border-radius:12px;
        background:#fff7f5;
        padding:11px 13px;
        color:#a45247;
        font-size:10px;
        line-height:1.6;
    }

    .ps-card{
        border:1px solid var(--ps-line);
        border-radius:16px;
        background:var(--ps-surface);
        box-shadow:var(--ps-shadow);
    }

    .ps-policy-strip{
        display:grid;
        grid-template-columns:minmax(240px,1.1fr) repeat(3,minmax(180px,.82fr));
        overflow:hidden;
        margin-bottom:16px;
    }
    .ps-policy-cell{
        min-width:0;
        padding:18px 20px;
        border-right:1px solid var(--ps-line);
        background:#fff;
    }
    .ps-policy-cell:last-child{border-right:0}
    .ps-kicker{
        color:#8a8f96;
        font-size:9.5px;
        font-weight:750;
        letter-spacing:.08em;
        text-transform:uppercase;
    }
    .ps-policy-value{
        margin-top:6px;
        color:#1f2937;
        font-size:20px;
        font-weight:750;
        letter-spacing:-.025em;
    }
    .ps-policy-note{
        margin-top:5px;
        color:#777d85;
        font-size:10.5px;
        line-height:1.55;
    }

    .ps-grid{
        display:grid;
        grid-template-columns:minmax(0,1.6fr) minmax(370px,.78fr);
        gap:16px;
        align-items:start;
    }
    .ps-stack{display:grid;gap:16px}

    .ps-section{
        padding:24px;
        background:#fff;
    }
    .ps-section-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:18px;
        margin-bottom:20px;
    }
    .ps-section-head > div:first-child{min-width:0}
    .ps-section-title{
        margin:0;
        color:#171717;
        font-size:18px;
        font-weight:750;
        letter-spacing:-.02em;
    }
    .ps-section-copy{
        max-width:720px;
        margin:6px 0 0;
        color:#70767e;
        font-size:11.5px;
        line-height:1.65;
    }
    .ps-head-actions{
        display:flex;
        align-items:center;
        gap:8px;
        flex:0 0 auto;
    }
    .ps-tag{
        display:inline-flex;
        min-height:29px;
        align-items:center;
        border:1px solid #ead9b4;
        border-radius:999px;
        background:var(--ps-gold-soft);
        padding:0 10px;
        color:#8c6721;
        font-size:8.5px;
        font-weight:750;
        white-space:nowrap;
    }

    .ps-form-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:15px;
    }
    .ps-field{display:block;min-width:0}
    .ps-field.full{grid-column:1/-1}
    .ps-label-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:7px;
    }
    .ps-label{
        color:#34383d;
        font-size:11.5px;
        font-weight:700;
    }
    .ps-current{
        color:#8a8f96;
        font-size:9.5px;
        font-weight:600;
    }

    .ps-input,.ps-textarea{
        width:100%;
        border:1px solid var(--ps-line-strong);
        border-radius:11px;
        background:#fff;
        color:#1f2937;
        font:inherit;
        font-size:12px;
        font-weight:600;
        outline:0;
        transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;
    }
    .ps-input{height:50px;padding:0 13px}
    .ps-textarea{min-height:106px;padding:12px 13px;resize:vertical;line-height:1.6}
    .ps-input:hover,.ps-textarea:hover{border-color:#cfd2d7}
    .ps-input:focus,.ps-textarea:focus{
        border-color:#c89a3f;
        background:#fff;
        box-shadow:0 0 0 3px rgba(197,141,32,.08);
    }
    .ps-input-wrap{position:relative}
    .ps-input-wrap .ps-input{padding-right:52px}
    .ps-suffix{
        position:absolute;
        top:50%;
        right:10px;
        transform:translateY(-50%);
        display:grid;
        min-width:30px;
        height:26px;
        place-items:center;
        border-radius:7px;
        background:#f2f3f5;
        color:#676d75;
        font-size:9px;
        font-weight:750;
        pointer-events:none;
    }
    .ps-help{
        margin:7px 0 0;
        color:#7f858d;
        font-size:10px;
        line-height:1.6;
    }

    .ps-divider{height:1px;margin:18px 0;background:#eceef0}

    .ps-toggle-list{display:grid;gap:10px}
    .ps-toggle-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        border:1px solid #e8eaed;
        border-radius:12px;
        background:#fafbfc;
        padding:12px 13px;
        transition:border-color .15s ease,background .15s ease;
    }
    .ps-toggle-row:hover{
        border-color:#dfe2e6;
        background:#fff;
    }
    .ps-toggle-title{
        color:#2f3439;
        font-size:11.5px;
        font-weight:750;
    }
    .ps-toggle-copy{
        margin-top:4px;
        color:#747a82;
        font-size:10px;
        line-height:1.55;
    }
    .ps-switch{
        position:relative;
        width:42px;
        height:24px;
        flex:0 0 42px;
    }
    .ps-switch input{position:absolute;opacity:0;pointer-events:none}
    .ps-switch span{
        position:absolute;
        inset:0;
        border:1px solid #d5d8dc;
        border-radius:999px;
        background:#e6e8eb;
        cursor:pointer;
        transition:.18s ease;
    }
    .ps-switch span::after{
        content:"";
        position:absolute;
        top:3px;
        left:3px;
        width:16px;
        height:16px;
        border-radius:50%;
        background:#fff;
        box-shadow:0 2px 5px rgba(0,0,0,.12);
        transition:.18s ease;
    }
    .ps-switch input:checked + span{
        border-color:#c58d20;
        background:#c58d20;
    }
    .ps-switch input:checked + span::after{transform:translateX(18px)}

    .ps-governance{
        border:1px solid #e8eaed;
        border-radius:12px;
        background:#fafbfc;
        padding:13px;
    }
    .ps-governance-title{
        color:#34383d;
        font-size:11.5px;
        font-weight:750;
    }
    .ps-governance-copy{
        margin-top:5px;
        color:#747a82;
        font-size:10px;
        line-height:1.6;
    }
    .ps-permission{
        display:flex;
        align-items:center;
        gap:9px;
        margin-top:14px;
        border-top:1px solid #eceef0;
        padding-top:12px;
        color:#626870;
        font-size:10px;
        line-height:1.5;
    }
    .ps-permission-dot{
        width:8px;
        height:8px;
        border-radius:50%;
        background:#6f826a;
        box-shadow:0 0 0 3px rgba(111,130,106,.10);
    }
    .ps-permission-dot.no{
        background:#b86556;
        box-shadow:0 0 0 3px rgba(184,101,86,.10);
    }

    .ps-scheduled{
        border-color:#e1d0ad;
        background:#fff;
    }
    .ps-scheduled-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
    }
    .ps-scheduled-meta{
        margin-top:10px;
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:8px;
    }
    .ps-mini{
        border:1px solid #e8eaed;
        border-radius:10px;
        background:#fafbfc;
        padding:9px;
    }
    .ps-mini span{
        display:block;
        color:#8a8f96;
        font-size:7.5px;
        text-transform:uppercase;
        font-weight:750;
    }
    .ps-mini strong{
        display:block;
        margin-top:4px;
        color:#3c4146;
        font-size:9px;
    }

    .ps-diff-list{display:grid;gap:6px;margin-top:10px}
    .ps-diff{
        display:grid;
        grid-template-columns:minmax(130px,1fr) 1fr 20px 1fr;
        gap:7px;
        align-items:center;
        border-top:1px solid #eceef0;
        padding-top:8px;
        color:#6d737b;
        font-size:8.5px;
    }
    .ps-diff strong{color:#34383d}
    .ps-arrow{text-align:center;color:#a97012}

    .ps-btn{
        display:inline-flex;
        height:40px;
        align-items:center;
        justify-content:center;
        gap:7px;
        border-radius:10px;
        padding:0 13px;
        font:inherit;
        font-size:9.5px;
        font-weight:750;
        cursor:pointer;
        transition:transform .15s ease,box-shadow .15s ease,border-color .15s ease,background .15s ease;
    }
    .ps-btn:hover{transform:translateY(-1px)}
    .ps-btn-secondary{
        border:1px solid #dfe2e6;
        background:#fff;
        color:#5f656d;
    }
    .ps-btn-secondary:hover{background:#f8f9fa}
    .ps-btn-danger{
        border:1px solid #e3b8b1;
        background:#fff;
        color:#a95448;
    }
    .ps-btn-danger:hover{background:#fff7f5}
    .ps-btn-primary{
        border:1px solid #b77a17;
        background:#c58d20;
        color:#fff;
        box-shadow:0 7px 16px rgba(163,112,27,.16);
    }
    .ps-btn-primary:hover{
        background:#b98118;
        box-shadow:0 9px 20px rgba(163,112,27,.20);
    }

    .ps-actions{
        position:sticky;
        bottom:12px;
        z-index:25;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-top:16px;
        border:1px solid #dfe2e6;
        border-radius:14px;
        background:rgba(255,255,255,.96);
        padding:10px 11px;
        box-shadow:0 14px 32px rgba(17,24,39,.10);
        backdrop-filter:blur(10px);
    }
    .ps-change-state{
        display:flex;
        align-items:center;
        gap:8px;
        color:#7a8088;
        font-size:9.5px;
        font-weight:650;
    }
    .ps-change-dot{
        width:7px;
        height:7px;
        border-radius:50%;
        background:#b8bdc4;
    }
    .ps-actions.is-dirty .ps-change-dot{
        background:#c58d20;
        box-shadow:0 0 0 3px rgba(197,141,32,.10);
    }
    .ps-actions.is-dirty .ps-change-state{color:#77591d}
    .ps-actions-right{display:flex;gap:8px}

    .ps-audit{
        margin-top:16px;
        overflow:hidden;
        background:#fff;
    }
    .ps-audit-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
        padding:18px 20px;
        border-bottom:1px solid var(--ps-line);
    }
    .ps-table-wrap{overflow:auto}
    .ps-table{
        width:100%;
        min-width:980px;
        border-collapse:collapse;
    }
    .ps-table th{
        padding:12px 14px;
        border-bottom:1px solid #e7e9ec;
        background:#f7f8f9;
        color:#6f757d;
        font-size:9px;
        font-weight:750;
        text-transform:uppercase;
        letter-spacing:.04em;
        text-align:left;
    }
    .ps-table td{
        padding:13px 14px;
        border-bottom:1px solid #eceef0;
        color:#50565e;
        font-size:10px;
        line-height:1.5;
        vertical-align:top;
        background:#fff;
    }
    .ps-table tbody tr:hover td{background:#fbfcfd}
    .ps-table tbody tr:last-child td{border-bottom:0}
    .ps-audit-setting{color:#25292e;font-weight:750}
    .ps-audit-value{max-width:220px;white-space:normal;word-break:break-word}
    .ps-empty{
        padding:30px;
        text-align:center;
        color:#868c94;
        font-size:10.5px;
        line-height:1.6;
        background:#fff;
    }

    /* Modern help menu */
    .ps-help-menu{position:relative}
    .ps-help-menu summary{list-style:none}
    .ps-help-menu summary::-webkit-details-marker{display:none}
    .ps-help-trigger{
        display:inline-flex;
        min-height:32px;
        align-items:center;
        justify-content:center;
        gap:7px;
        border:1px solid #dde0e4;
        border-radius:9px;
        background:#fff;
        padding:0 10px;
        color:#616870;
        font-size:9px;
        font-weight:750;
        cursor:pointer;
        user-select:none;
        box-shadow:0 2px 7px rgba(17,24,39,.035);
        transition:border-color .15s ease,background .15s ease,box-shadow .15s ease;
    }
    .ps-help-trigger:hover{
        border-color:#d0d4d9;
        background:#f8f9fa;
        box-shadow:0 5px 12px rgba(17,24,39,.06);
    }
    .ps-help-trigger:focus-visible{
        outline:3px solid rgba(197,141,32,.14);
        outline-offset:2px;
    }
    .ps-help-icon{
        display:grid;
        width:19px;
        height:19px;
        place-items:center;
        border:1px solid #d8dbe0;
        border-radius:50%;
        background:#f8f9fa;
        color:#7a8088;
        font-size:10px;
        font-weight:800;
        line-height:1;
    }
    .ps-help-menu[open] .ps-help-icon{
        border-color:#e2c98f;
        background:var(--ps-gold-soft);
        color:var(--ps-gold-strong);
    }
    .ps-help-chevron{
        width:12px;
        height:12px;
        transition:transform .15s ease;
    }
    .ps-help-menu[open] .ps-help-chevron{transform:rotate(180deg)}
    .ps-help-panel{
        position:absolute;
        top:calc(100% + 9px);
        right:0;
        z-index:80;
        width:min(390px,calc(100vw - 40px));
        border:1px solid #dfe2e6;
        border-radius:14px;
        background:#fff;
        padding:14px;
        box-shadow:var(--ps-shadow-strong);
    }
    .ps-help-panel::before{
        content:"";
        position:absolute;
        top:-6px;
        right:24px;
        width:10px;
        height:10px;
        border-left:1px solid #dfe2e6;
        border-top:1px solid #dfe2e6;
        background:#fff;
        transform:rotate(45deg);
    }
    .ps-help-panel-title{
        display:flex;
        align-items:center;
        gap:9px;
        margin-bottom:10px;
        color:#25292e;
        font-size:11.5px;
        font-weight:800;
    }
    .ps-help-panel-title .ps-help-icon{width:21px;height:21px}
    .ps-help-steps{display:grid;gap:8px}
    .ps-help-step{
        display:grid;
        grid-template-columns:24px 1fr;
        gap:9px;
        align-items:start;
        border:1px solid #e8eaed;
        border-radius:10px;
        background:#fafbfc;
        padding:9px;
    }
    .ps-help-step-num{
        display:grid;
        width:24px;
        height:24px;
        place-items:center;
        border-radius:7px;
        background:#f1f3f5;
        color:#656b73;
        font-size:9px;
        font-weight:800;
    }
    .ps-help-step strong{
        display:block;
        color:#363b40;
        font-size:9.5px;
    }
    .ps-help-step p{
        margin:3px 0 0;
        color:#737981;
        font-size:9px;
        line-height:1.55;
    }
    .ps-help-note{
        margin-top:9px;
        border-left:3px solid var(--ps-gold);
        background:#fffaf0;
        padding:8px 9px;
        color:#6b5c43;
        font-size:8.5px;
        line-height:1.55;
    }

    .ps-modal-backdrop{
        position:fixed;
        inset:0;
        z-index:1000;
        display:none;
        place-items:center;
        background:rgba(17,24,39,.38);
        padding:20px;
        backdrop-filter:blur(3px);
    }
    .ps-modal-backdrop.show{display:grid}
    .ps-modal{
        width:min(470px,100%);
        border:1px solid #dfe2e6;
        border-radius:16px;
        background:#fff;
        padding:19px;
        box-shadow:0 28px 80px rgba(17,24,39,.24);
    }
    .ps-modal h3{margin:0;color:#20242a;font-size:15px}
    .ps-modal p{
        margin:6px 0 0;
        color:#6d737b;
        font-size:9.5px;
        line-height:1.55;
    }
    .ps-modal-summary{
        margin-top:12px;
        border:1px solid #e8eaed;
        border-radius:10px;
        background:#fafbfc;
        padding:10px;
        color:#555b62;
        font-size:8.5px;
        line-height:1.8;
    }
    .ps-modal-actions{
        display:flex;
        justify-content:flex-end;
        gap:8px;
        margin-top:15px;
    }

    fieldset{min-width:0;border:0;margin:0;padding:0}
    fieldset:disabled{opacity:.66}

    @media(max-width:1200px){
        .ps-grid{grid-template-columns:1fr}
        .ps-policy-strip{grid-template-columns:repeat(2,1fr)}
        .ps-policy-cell:nth-child(2){border-right:0}
        .ps-policy-cell:nth-child(-n+2){border-bottom:1px solid var(--ps-line)}
    }

    @media(max-width:700px){
        .platform-settings-page{padding:0 8px 32px}
        .ps-header{align-items:flex-start;flex-direction:column}
        .ps-header-actions,.ps-head-actions{width:100%;justify-content:flex-start;flex-wrap:wrap}
        .ps-policy-strip,.ps-form-grid{grid-template-columns:1fr}
        .ps-policy-cell{border-right:0;border-bottom:1px solid var(--ps-line)}
        .ps-policy-cell:last-child{border-bottom:0}
        .ps-field.full{grid-column:auto}
        .ps-actions{align-items:stretch;flex-direction:column}
        .ps-actions-right{width:100%}
        .ps-actions-right .ps-btn{flex:1}
        .ps-diff{grid-template-columns:1fr}
        .ps-arrow{display:none}
        .ps-help-menu{position:static}
        .ps-help-panel{
            position:fixed;
            left:14px;
            right:14px;
            top:auto;
            bottom:18px;
            width:auto;
            max-height:70vh;
            overflow:auto;
            z-index:120;
        }
        .ps-help-panel::before{display:none}
    }
</style>

<div class="platform-settings-page">
    <header class="ps-header">
        <div class="ps-header-left">
            <span class="ps-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1.1-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.65 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.5 4.65a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.04.38.25.73.6 1 .3.23.68.37 1.1.4H21a2 2 0 1 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z"/></svg>
            </span>
            <div>
                <p class="ps-eyebrow">Administration / Governance</p>
                <h1 class="ps-title"><span class="ps-title-platform">Platform</span> <span class="ps-title-gold">Settings</span></h1>
                <p class="ps-subtitle">Manage pricing, checkout rules, registration access, finance controls, and scheduled policy changes from one governed workspace.</p>
            </div>
        </div>
        <div class="ps-header-actions">
            <span class="ps-badge">Active policy {{ $activeVersionLabel }}</span>

            <details class="ps-help-menu">
                <summary class="ps-help-trigger" aria-label="Open Platform Settings help">
                    <span class="ps-help-icon">?</span>
                    Help & Process
                    <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                </summary>
                <div class="ps-help-panel">
                    <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> How Platform Settings works</div>
                    <div class="ps-help-steps">
                        <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Change only the policy you need</strong><p>Edit a fee, rule, toggle, maintenance control, or finance threshold. Unchanged values stay part of the same policy snapshot.</p></div></div>
                        <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Set when it becomes effective</strong><p>Use the current time for an immediate policy, or choose a future date/time to schedule one policy ahead.</p></div></div>
                        <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Write a clear change reason</strong><p>The reason is stored with the policy version and setting-level audit history for traceability.</p></div></div>
                        <div class="ps-help-step"><span class="ps-help-step-num">4</span><div><strong>Review and publish</strong><p>The confirmation dialog summarizes critical values. After confirmation, the server validates and saves the new version.</p></div></div>
                    </div>
                    <div class="ps-help-note">Historical commission snapshots and previously stored transactions are not recalculated when you change the current policy.</div>
                </div>
            </details>
        </div>
    </header>

    @if(session('success'))
        <div class="ps-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="ps-errors">
            <strong>Settings were not saved.</strong>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <section class="ps-card ps-policy-strip">
        <div class="ps-policy-cell">
            <div class="ps-kicker">Active Policy</div>
            <div class="ps-policy-value">{{ $activeVersionLabel }}</div>
            <div class="ps-policy-note">
                @if($activeVersion)
                    Effective {{ $activeVersion->effective_at?->format('M j, Y · h:i A') }}
                @else
                    Existing legacy configuration; first governed save creates v1.
                @endif
            </div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Commission Rate</div>
            <div class="ps-policy-value">{{ number_format((float)$settings['commission_rate'],2) }}%</div>
            <div class="ps-policy-note">Historical earned commissions keep their stored rate.</div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Delivery Fee / Seller</div>
            <div class="ps-policy-value">₱{{ number_format((float)$settings['delivery_fee_per_seller'],2) }}</div>
            <div class="ps-policy-note">Used by cart and checkout fee calculations.</div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Next Scheduled Policy</div>
            <div class="ps-policy-value">{{ $scheduledVersion ? 'v'.$scheduledVersion->version_number : 'None' }}</div>
            <div class="ps-policy-note">
                {{ $scheduledVersion ? $scheduledVersion->effective_at?->format('M j, Y · h:i A') : 'No future configuration is queued.' }}
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.platform-settings.update') }}" id="platformSettingsForm">
        @csrf
        @method('PATCH')
        <input type="hidden" name="material_change_confirmed" id="materialChangeConfirmed" value="0">

        <fieldset @disabled(!$canManageSettings)>
            <div class="ps-grid">
                <div class="ps-stack">
                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Commerce & Fees</h2>
                                <p class="ps-section-copy">Pricing rules currently used by checkout and commission calculations.</p>
                            </div>
                            <div class="ps-head-actions">
                                <span class="ps-tag">Financial policy</span>
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Commerce & Fees">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Commerce & Fees guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Commission rate</strong><p>Set the platform percentage used for new eligible delivered-and-paid commissions after the policy becomes effective.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Delivery fee per seller</strong><p>Set the default seller-group delivery fee used by cart and checkout when the whole group is not free-shipping.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Publish carefully</strong><p>Choose an effective date and enter a reason before saving. A future commission rate is scheduled instead of replacing the current one immediately.</p></div></div>
                                        </div>
                                <div class="ps-help-note">Changing the rate does not rewrite previously earned commission snapshots.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-form-grid">
                            <label class="ps-field">
                                <div class="ps-label-row">
                                    <span class="ps-label">Commission rate</span>
                                    <span class="ps-current">Current {{ number_format((float)$settings['commission_rate'],2) }}%</span>
                                </div>
                                <div class="ps-input-wrap">
                                    <input class="ps-input" name="commission_rate" type="number" step="0.01" min="0" max="100" value="{{ old('commission_rate',$settings['commission_rate']) }}" required>
                                    <span class="ps-suffix">%</span>
                                </div>
                                <p class="ps-help">New delivered-and-paid commissions use the effective rate. Existing order commission snapshots are not rewritten.</p>
                            </label>

                            <label class="ps-field">
                                <div class="ps-label-row">
                                    <span class="ps-label">Delivery fee per seller</span>
                                    <span class="ps-current">Current ₱{{ number_format((float)$settings['delivery_fee_per_seller'],2) }}</span>
                                </div>
                                <div class="ps-input-wrap">
                                    <input class="ps-input" name="delivery_fee_per_seller" type="number" step="0.01" min="0" max="100000" value="{{ old('delivery_fee_per_seller',$settings['delivery_fee_per_seller']) }}" required>
                                    <span class="ps-suffix">₱</span>
                                </div>
                                <p class="ps-help">Default per-seller delivery charge where the seller group is not fully free-shipping.</p>
                            </label>
                        </div>
                    </section>

                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Order & Checkout Rules</h2>
                                <p class="ps-section-copy">Set the rules buyers must satisfy before checkout or cancellation is allowed.</p>
                            </div>
                            <div class="ps-head-actions">
                                <span class="ps-tag">Operations</span>
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Order & Checkout Rules">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Order & Checkout guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Checkout enabled</strong><p>Turn this off to stop buyers from entering or submitting checkout while keeping carts and Admin access available.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Seller limit</strong><p>Use 0 for unlimited, or enter the maximum number of seller groups allowed in one checkout.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Cancellation window</strong><p>Use 0 to keep the existing New-order rule, or set a minute limit counted from order creation.</p></div></div>
                                        </div>
                                <div class="ps-help-note">If a buyer exceeds the configured rule, the action is blocked server-side and a validation message is returned.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-toggle-list">
                            <div class="ps-toggle-row">
                                <div>
                                    <div class="ps-toggle-title">Checkout enabled</div>
                                    <div class="ps-toggle-copy">When disabled, buyers can still view their cart but cannot enter or submit checkout.</div>
                                </div>
                                <label class="ps-switch">
                                    <input type="hidden" name="checkout_enabled" value="0">
                                    <input type="checkbox" name="checkout_enabled" value="1" @checked($boolValue('checkout_enabled',true))>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="ps-divider"></div>

                        <div class="ps-form-grid">
                            <label class="ps-field">
                                <div class="ps-label-row"><span class="ps-label">Maximum sellers per checkout</span></div>
                                <input class="ps-input" name="max_sellers_per_checkout" type="number" min="0" max="50" value="{{ old('max_sellers_per_checkout',$settings['max_sellers_per_checkout']) }}" required>
                                <p class="ps-help">Set 0 for unlimited. Any positive value limits how many seller groups can be submitted in one checkout.</p>
                            </label>

                            <label class="ps-field">
                                <div class="ps-label-row"><span class="ps-label">Buyer cancellation window</span></div>
                                <div class="ps-input-wrap">
                                    <input class="ps-input" name="buyer_cancellation_window_minutes" type="number" min="0" max="10080" value="{{ old('buyer_cancellation_window_minutes',$settings['buyer_cancellation_window_minutes']) }}" required>
                                    <span class="ps-suffix">min</span>
                                </div>
                                <p class="ps-help">Set 0 to preserve the existing rule: buyer may cancel while order status is still New.</p>
                            </label>
                        </div>
                    </section>

                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Registration & Notifications</h2>
                                <p class="ps-section-copy">Manage who can register and whether approval or rejection emails are sent.</p>
                            </div>
                            <div class="ps-head-actions">
                                <span class="ps-tag">Access + email</span>
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Registration & Notifications">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Registration & Notification guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Registration access</strong><p>Turn registrations off to temporarily block new Buyer, Seller, Logistics, and Rider applications.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Decision emails</strong><p>Turn email notifications off if you want approval/rejection decisions to remain database-only for the moment.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Existing accounts stay intact</strong><p>These controls do not delete approved accounts or existing registration history.</p></div></div>
                                        </div>
                                <div class="ps-help-note">Admin registration decisions still save even when decision-email sending is disabled.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-toggle-list">
                            <div class="ps-toggle-row">
                                <div>
                                    <div class="ps-toggle-title">New registrations enabled</div>
                                    <div class="ps-toggle-copy">Applies to Buyer, Seller, Logistics, and Rider registration submission flows.</div>
                                </div>
                                <label class="ps-switch">
                                    <input type="hidden" name="registrations_enabled" value="0">
                                    <input type="checkbox" name="registrations_enabled" value="1" @checked($boolValue('registrations_enabled',true))>
                                    <span></span>
                                </label>
                            </div>

                            <div class="ps-toggle-row">
                                <div>
                                    <div class="ps-toggle-title">Registration decision emails</div>
                                    <div class="ps-toggle-copy">Controls the existing queued approval/rejection email job. Database review decisions still save even when email is off.</div>
                                </div>
                                <label class="ps-switch">
                                    <input type="hidden" name="registration_decision_email_enabled" value="0">
                                    <input type="checkbox" name="registration_decision_email_enabled" value="1" @checked($boolValue('registration_decision_email_enabled',true))>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="ps-stack">
                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Finance Rules</h2>
                                <p class="ps-section-copy">Control when internal seller settlements become eligible and when riders may request payout.</p>
                            </div>
                            <div class="ps-head-actions">
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Finance Rules">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Finance rules guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Settlement hold</strong><p>Enter how many days after delivery a newly created seller settlement becomes internally eligible. 0 means no added hold.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Minimum rider payout</strong><p>Set the minimum available rider earnings required before the existing payout-request action can be submitted.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Internal controls only</strong><p>These values control eligibility and request rules inside SARI; they do not represent an external bank or e-wallet transfer.</p></div></div>
                                        </div>
                                <div class="ps-help-note">Previously stored finance records are not retroactively rewritten by these controls.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-form-grid" style="grid-template-columns:1fr">
                            <label class="ps-field">
                                <div class="ps-label-row"><span class="ps-label">Seller settlement hold</span></div>
                                <div class="ps-input-wrap">
                                    <input class="ps-input" name="seller_settlement_hold_days" type="number" min="0" max="90" value="{{ old('seller_settlement_hold_days',$settings['seller_settlement_hold_days']) }}" required>
                                    <span class="ps-suffix">days</span>
                                </div>
                                <p class="ps-help">Moves the internal seller settlement eligible_at date forward from delivery. 0 means immediately eligible internally.</p>
                            </label>

                            <label class="ps-field">
                                <div class="ps-label-row"><span class="ps-label">Minimum rider payout request</span></div>
                                <div class="ps-input-wrap">
                                    <input class="ps-input" name="rider_payout_minimum" type="number" step="0.01" min="0" max="100000" value="{{ old('rider_payout_minimum',$settings['rider_payout_minimum']) }}" required>
                                    <span class="ps-suffix">₱</span>
                                </div>
                                <p class="ps-help">Riders cannot submit the existing internal payout request unless available earnings meet this amount. 0 disables the minimum.</p>
                            </label>
                        </div>
                    </section>

                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Commerce Maintenance</h2>
                                <p class="ps-section-copy">Temporarily pause buyer checkout and new registrations without locking the Admin workspace.</p>
                            </div>
                            <div class="ps-head-actions">
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Commerce Maintenance">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Maintenance guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Enable maintenance mode</strong><p>Use this when checkout and new registrations need to be temporarily stopped.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Write the public message</strong><p>Enter a clear maintenance message explaining why the action is unavailable.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Admin remains available</strong><p>The maintenance switch does not lock the Admin settings page, so an administrator can turn it off again.</p></div></div>
                                        </div>
                                <div class="ps-help-note">Current implementation blocks checkout and new registration entry points; it does not shut down the entire Laravel application.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-toggle-row">
                            <div>
                                <div class="ps-toggle-title">Maintenance mode</div>
                                <div class="ps-toggle-copy">Temporarily blocks checkout and new registration actions.</div>
                            </div>
                            <label class="ps-switch">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input type="checkbox" name="maintenance_mode" value="1" @checked($boolValue('maintenance_mode',false))>
                                <span></span>
                            </label>
                        </div>

                        <label class="ps-field" style="margin-top:12px">
                            <div class="ps-label-row"><span class="ps-label">Maintenance message</span></div>
                            <textarea class="ps-textarea" name="maintenance_message" maxlength="500" required>{{ old('maintenance_message',$settings['maintenance_message']) }}</textarea>
                        </label>
                    </section>

                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Change Governance</h2>
                                <p class="ps-section-copy">Define when changes take effect and keep every policy update traceable.</p>
                            </div>
                            <div class="ps-head-actions">
                                <details class="ps-help-menu">
                                    <summary class="ps-help-trigger" aria-label="Help for Change Governance">
                                        <span class="ps-help-icon">?</span>
                                        Help
                                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="ps-help-panel">
                                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Publishing & governance guide</div>
                                        <div class="ps-help-steps">
                                    <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Effective at</strong><p>Use the current time to publish now, or a future time to schedule the complete next policy snapshot.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Change reason</strong><p>Explain why the policy changed. This reason is stored in the database-backed audit history.</p></div></div>
                                    <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Review & Save Policy</strong><p>The review dialog appears first. After confirmation, server validation creates the next policy version.</p></div></div>
                                        </div>
                                <div class="ps-help-note">Only one future policy can be pending at a time. Cancel the pending schedule before creating another one.</div>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="ps-governance">
                            <div class="ps-governance-title">Effective date & traceability</div>
                            <div class="ps-governance-copy">Choose now or a future date. Only one future policy may be pending at a time so overlapping commission windows cannot be created.</div>
                        </div>

                        <label class="ps-field" style="margin-top:12px">
                            <div class="ps-label-row"><span class="ps-label">Effective at</span><span class="ps-current">{{ config('app.timezone') }}</span></div>
                            <input class="ps-input" name="effective_at" type="datetime-local" value="{{ $effectiveDefault }}" required>
                        </label>

                        <label class="ps-field" style="margin-top:12px">
                            <div class="ps-label-row"><span class="ps-label">Change reason</span></div>
                            <textarea class="ps-textarea" name="change_reason" minlength="10" maxlength="1000" placeholder="Example: Updated logistics pricing policy for the next operating period." required>{{ old('change_reason') }}</textarea>
                            <p class="ps-help">Required for every settings version and written into the immutable audit trail.</p>
                        </label>

                        <div class="ps-permission">
                            <span class="ps-permission-dot {{ $canManageSettings ? '' : 'no' }}"></span>
                            <span>
                                @if($canManageSettings)
                                    {{ $admin?->name ?: 'Legacy Admin session' }} can publish platform settings.
                                @else
                                    This Admin account has read-only Platform Settings access.
                                @endif
                            </span>
                        </div>
                    </section>
                </aside>
            </div>

            <div class="ps-actions" id="settingsActionBar">
                <div class="ps-change-state"><span class="ps-change-dot"></span><span id="settingsChangeText">No unsaved changes</span></div>
                <div class="ps-actions-right">
                    <button type="button" class="ps-btn ps-btn-secondary" id="resetSettingsBtn">Reset</button>
                    <button type="submit" class="ps-btn ps-btn-primary">Review & Save Policy</button>
                </div>
            </div>
        </fieldset>
    </form>

    @if($scheduledVersion)
        <section class="ps-card ps-section ps-scheduled" style="margin-top:16px">
            <div class="ps-scheduled-head">
                <div>
                    <div class="ps-kicker">Pending Scheduled Policy</div>
                    <h2 class="ps-section-title" style="margin-top:4px">Version {{ $scheduledVersion->version_number }}</h2>
                    <p class="ps-section-copy">Effective {{ $scheduledVersion->effective_at?->format('M j, Y · h:i A') }} · {{ $scheduledVersion->change_reason }}</p>
                </div>

                @if($canManageSettings)
                    <form method="POST" action="{{ route('admin.platform-settings.update') }}" onsubmit="return confirm('Cancel this future platform policy?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="operation" value="cancel_scheduled">
                        <input type="hidden" name="version_id" value="{{ $scheduledVersion->id }}">
                        <button class="ps-btn ps-btn-danger" type="submit">Cancel Schedule</button>
                    </form>
                @endif
            </div>

            <div class="ps-scheduled-meta">
                <div class="ps-mini"><span>Scheduled By</span><strong>{{ $scheduledVersion->admin?->name ?: 'Administrator' }}</strong></div>
                <div class="ps-mini"><span>Created</span><strong>{{ $scheduledVersion->created_at?->format('M j, Y · h:i A') }}</strong></div>
            </div>

            <div class="ps-diff-list">
                @foreach((array)$scheduledVersion->settings as $key => $newValue)
                    @php $oldValue = $settings[$key] ?? null; @endphp
                    @if((string)$oldValue !== (string)$newValue)
                        <div class="ps-diff">
                            <strong>{{ $labels[$key] ?? str($key)->replace('_',' ')->title() }}</strong>
                            <span>{{ $formatSetting($key,$oldValue) }}</span>
                            <span class="ps-arrow">→</span>
                            <span>{{ $formatSetting($key,$newValue) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <section class="ps-card ps-audit">
        <div class="ps-audit-head">
            <div>
                <h2 class="ps-section-title">Configuration Audit History</h2>
                <p class="ps-section-copy">Database-backed record of published, scheduled, and cancelled settings changes.</p>
            </div>
            <div class="ps-head-actions">
                <span class="ps-tag">Latest {{ $auditHistory->count() }}</span>
                <details class="ps-help-menu">
                    <summary class="ps-help-trigger" aria-label="Help for audit history">
                        <span class="ps-help-icon">?</span>
                        Help
                        <svg class="ps-help-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                    </summary>
                    <div class="ps-help-panel">
                        <div class="ps-help-panel-title"><span class="ps-help-icon">?</span> Audit history guide</div>
                        <div class="ps-help-steps">
                            <div class="ps-help-step"><span class="ps-help-step-num">1</span><div><strong>Setting</strong><p>Shows which platform rule was changed.</p></div></div>
                            <div class="ps-help-step"><span class="ps-help-step-num">2</span><div><strong>Previous → New</strong><p>Shows the before-and-after values saved for that policy version.</p></div></div>
                            <div class="ps-help-step"><span class="ps-help-step-num">3</span><div><strong>Changed by / Effective / Reason</strong><p>Shows who changed it, when the policy takes effect, and why the change was made.</p></div></div>
                        </div>
                        <div class="ps-help-note">The page displays the latest audit rows, while the database remains the source of truth for the stored history.</div>
                    </div>
                </details>
            </div>
        </div>

        <div class="ps-table-wrap">
            @if($auditHistory->isEmpty())
                <div class="ps-empty">No governed setting changes have been recorded yet. Your first save will create policy version 1.</div>
            @else
                <table class="ps-table">
                    <thead>
                        <tr><th>Setting</th><th>Previous</th><th>New</th><th>Action</th><th>Changed By</th><th>Effective</th><th>Reason</th></tr>
                    </thead>
                    <tbody>
                        @foreach($auditHistory as $audit)
                            <tr>
                                <td class="ps-audit-setting">{{ $audit->setting_key === '*' ? 'Policy Version' : ($labels[$audit->setting_key] ?? str($audit->setting_key)->replace('_',' ')->title()) }}</td>
                                <td class="ps-audit-value">{{ $audit->old_value ?? '—' }}</td>
                                <td class="ps-audit-value">{{ $audit->new_value ?? '—' }}</td>
                                <td>{{ str($audit->action)->replace('_',' ')->title() }}</td>
                                <td>{{ $audit->admin?->name ?: 'System / Legacy Admin' }}</td>
                                <td>{{ $audit->effective_at?->format('M j, Y · h:i A') ?: '—' }}</td>
                                <td class="ps-audit-value">{{ $audit->reason ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>
</div>

<div class="ps-modal-backdrop" id="settingsConfirmModal" aria-hidden="true">
    <div class="ps-modal" role="dialog" aria-modal="true">
        <h3>Publish platform policy?</h3>
        <p>Confirm the live/scheduled configuration before it is versioned and written to the audit trail.</p>
        <div class="ps-modal-summary" id="settingsConfirmSummary"></div>
        <div class="ps-modal-actions">
            <button type="button" class="ps-btn ps-btn-secondary" id="cancelSettingsSave">Cancel</button>
            <button type="button" class="ps-btn ps-btn-primary" id="confirmSettingsSave">Confirm & Publish</button>
        </div>
    </div>
</div>

<script>
(function(){
    const form=document.getElementById('platformSettingsForm');
    if(!form) return;

    const controls=Array.from(form.querySelectorAll('input:not([type="hidden"]), textarea'));
    const actionBar=document.getElementById('settingsActionBar');
    const changeText=document.getElementById('settingsChangeText');
    const resetButton=document.getElementById('resetSettingsBtn');
    const modal=document.getElementById('settingsConfirmModal');
    const summary=document.getElementById('settingsConfirmSummary');
    const cancel=document.getElementById('cancelSettingsSave');
    const confirmButton=document.getElementById('confirmSettingsSave');
    const confirmed=document.getElementById('materialChangeConfirmed');

    const initial=new Map(controls.map(el=>[el.name,el.type==='checkbox'?el.checked:el.value]));

    const dirty=()=>controls.some(el=>{
        const before=initial.get(el.name);
        return el.type==='checkbox' ? el.checked!==before : el.value!==before;
    });

    function updateDirty(){
        const changed=dirty();
        actionBar?.classList.toggle('is-dirty',changed);
        if(changeText) changeText.textContent=changed?'Unsaved policy changes':'No unsaved changes';
        if(confirmed) confirmed.value='0';
    }

    controls.forEach(el=>el.addEventListener(el.type==='checkbox'?'change':'input',updateDirty));

    resetButton?.addEventListener('click',()=>{
        controls.forEach(el=>{
            const value=initial.get(el.name);
            if(el.type==='checkbox') el.checked=Boolean(value);
            else el.value=value;
        });
        updateDirty();
    });

    form.addEventListener('submit',event=>{
        if(confirmed?.value==='1') return;
        event.preventDefault();

        const commission=form.querySelector('[name="commission_rate"]')?.value || '0';
        const delivery=form.querySelector('[name="delivery_fee_per_seller"]')?.value || '0';
        const effective=form.querySelector('[name="effective_at"]')?.value || '';
        const maintenance=form.querySelector('input[type="checkbox"][name="maintenance_mode"]')?.checked;

        if(summary){
            summary.innerHTML=
                `<div><strong>Commission:</strong> ${Number(commission).toFixed(2)}%</div>`+
                `<div><strong>Delivery fee / seller:</strong> ₱${Number(delivery).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}</div>`+
                `<div><strong>Effective:</strong> ${effective || 'Now'}</div>`+
                `<div><strong>Maintenance mode:</strong> ${maintenance?'Enabled':'Disabled'}</div>`;
        }

        modal?.classList.add('show');
        modal?.setAttribute('aria-hidden','false');
    });

    cancel?.addEventListener('click',()=>{
        modal?.classList.remove('show');
        modal?.setAttribute('aria-hidden','true');
    });

    confirmButton?.addEventListener('click',()=>{
        if(confirmed) confirmed.value='1';
        modal?.classList.remove('show');
        modal?.setAttribute('aria-hidden','true');
        form.requestSubmit();
    });

    modal?.addEventListener('click',event=>{
        if(event.target===modal){
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden','true');
        }
    });

    document.addEventListener('click', event => {
        document.querySelectorAll('.ps-help-menu[open]').forEach(menu => {
            if (!menu.contains(event.target)) {
                menu.removeAttribute('open');
            }
        });
    });

    document.querySelectorAll('.ps-help-menu').forEach(menu => {
        menu.addEventListener('toggle', () => {
            if (!menu.open) return;
            document.querySelectorAll('.ps-help-menu[open]').forEach(other => {
                if (other !== menu) other.removeAttribute('open');
            });
        });
    });

    updateDirty();
})();
</script>
@endsection
