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
            'seller_product_posting_enabled',
            'platform_campaigns_enabled',
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
        --ps-bg:#f5f6f8;
        --ps-surface:#ffffff;
        --ps-surface-2:#fafbfc;
        --ps-ink:#191b1f;
        --ps-text:#565c65;
        --ps-muted:#8a8f98;
        --ps-line:#e7e9ec;
        --ps-line-strong:#d5d8dd;
        --ps-accent:#a9700f;
        --ps-accent-ink:#8a5c0e;
        --ps-accent-soft:#fbf1de;
        --ps-accent-line:#e7cd97;
        --ps-success:#3f7d52;
        --ps-success-soft:#eef6f0;
        --ps-danger:#b3493f;
        --ps-danger-soft:#fcf1ef;
        --ps-radius-lg:12px;
        --ps-radius-md:9px;
        --ps-radius-sm:7px;
        --ps-shadow:0 1px 2px rgba(20,22,26,.04), 0 6px 16px rgba(20,22,26,.04);
        --ps-shadow-strong:0 12px 32px rgba(20,22,26,.14);
    }

    .platform-settings-page{
        max-width:1360px;
        margin:0 auto;
        padding:20px 24px 56px;
        color:var(--ps-ink);
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        font-size:13px;
    }

    /* ---------- Header ---------- */
    .ps-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:20px;
        margin-bottom:20px;
    }
    .ps-header-left{
        display:flex;
        min-width:0;
        align-items:center;
        gap:16px;
    }
    .ps-header-icon{
        display:grid;
        width:52px;
        height:52px;
        flex:0 0 52px;
        place-items:center;
        border:1px solid var(--ps-accent-line);
        border-radius:16px;
        background:var(--ps-accent-soft);
        color:var(--ps-accent-ink);
    }
    .ps-header-icon svg{width:22px;height:22px}
    .ps-eyebrow{
        margin:0;
        color:var(--ps-accent-ink);
        font-size:10.5px;
        font-weight:750;
        letter-spacing:.06em;
        text-transform:uppercase;
    }
    .ps-title{
        margin:4px 0 0;
        font-size:24px;
        font-weight:700;
        line-height:1.2;
        letter-spacing:-.01em;
    }
    .ps-title-dark{color:var(--ps-ink)}
    .ps-title-accent{color:var(--ps-accent)}
    .ps-subtitle{
        max-width:620px;
        margin:6px 0 0;
        color:var(--ps-text);
        font-size:12.5px;
        line-height:1.6;
    }

    .ps-header-actions{
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
        justify-content:flex-end;
        flex:0 0 auto;
        padding-top:2px;
    }

    .ps-badge{
        display:inline-flex;
        height:30px;
        align-items:center;
        gap:6px;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-sm);
        background:#fff;
        padding:0 11px;
        color:var(--ps-text);
        font-size:11.5px;
        font-weight:600;
        white-space:nowrap;
    }
    .ps-badge::before{
        content:"";
        width:6px;
        height:6px;
        border-radius:50%;
        background:var(--ps-accent);
    }

    .ps-alert{
        margin-bottom:14px;
        border:1px solid #cfe1cf;
        border-radius:var(--ps-radius-md);
        background:var(--ps-success-soft);
        padding:11px 14px;
        color:#375f43;
        font-size:12.5px;
        font-weight:600;
    }
    .ps-errors{
        margin-bottom:14px;
        border:1px solid #e9c3bd;
        border-radius:var(--ps-radius-md);
        background:var(--ps-danger-soft);
        padding:11px 14px;
        color:#8f382e;
        font-size:12px;
        line-height:1.6;
    }

    /* ---------- Cards / policy strip ---------- */
    .ps-card{
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-lg);
        background:var(--ps-surface);
        box-shadow:var(--ps-shadow);
    }

    .ps-policy-strip{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        overflow:hidden;
        margin-bottom:16px;
    }
    .ps-policy-cell{
        min-width:0;
        padding:16px 18px;
        border-right:1px solid var(--ps-line);
    }
    .ps-policy-cell:last-child{border-right:0}
    .ps-kicker{
        color:var(--ps-muted);
        font-size:11px;
        font-weight:600;
    }
    .ps-policy-value{
        margin-top:6px;
        color:var(--ps-ink);
        font-size:19px;
        font-weight:650;
        letter-spacing:-.01em;
    }
    .ps-policy-note{
        margin-top:5px;
        color:var(--ps-muted);
        font-size:11px;
        line-height:1.5;
    }

    /* ---------- Main layout ---------- */
    .ps-grid{
        display:grid;
        grid-template-columns:minmax(0,1.6fr) minmax(340px,.8fr);
        gap:16px;
        align-items:start;
    }
    .ps-stack{display:grid;gap:16px}

    .ps-section{padding:20px}
    .ps-section-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        margin-bottom:18px;
        padding-bottom:14px;
        border-bottom:1px solid var(--ps-line);
    }
    .ps-section-head > div:first-child{min-width:0}
    .ps-section-title{
        margin:0;
        color:var(--ps-ink);
        font-size:14.5px;
        font-weight:650;
        letter-spacing:-.005em;
    }
    .ps-section-copy{
        max-width:520px;
        margin:5px 0 0;
        color:var(--ps-text);
        font-size:11.5px;
        line-height:1.6;
    }
    .ps-head-actions{
        display:flex;
        align-items:center;
        gap:8px;
        flex:0 0 auto;
    }
    .ps-tag{
        display:inline-flex;
        height:26px;
        align-items:center;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-sm);
        background:var(--ps-surface-2);
        padding:0 9px;
        color:var(--ps-text);
        font-size:10.5px;
        font-weight:600;
        white-space:nowrap;
    }

    /* ---------- Form fields ---------- */
    .ps-form-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:16px;
    }
    .ps-field{display:block;min-width:0}
    .ps-field.full{grid-column:1/-1}
    .ps-label-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:6px;
    }
    .ps-label{
        color:var(--ps-ink);
        font-size:12px;
        font-weight:600;
    }
    .ps-current{
        color:var(--ps-muted);
        font-size:11px;
        font-weight:500;
    }

    .ps-input,.ps-textarea{
        width:100%;
        border:1px solid var(--ps-line-strong);
        border-radius:var(--ps-radius-sm);
        background:#fff;
        color:var(--ps-ink);
        font:inherit;
        font-size:12.5px;
        font-weight:500;
        outline:0;
        transition:border-color .12s ease,box-shadow .12s ease;
    }
    .ps-input{height:40px;padding:0 12px}
    .ps-textarea{min-height:88px;padding:10px 12px;resize:vertical;line-height:1.6}
    .ps-input:hover,.ps-textarea:hover{border-color:#c3c7cd}
    .ps-input:focus,.ps-textarea:focus{
        border-color:var(--ps-accent);
        box-shadow:0 0 0 3px rgba(169,112,15,.10);
    }
    .ps-input-wrap{position:relative}
    .ps-input-wrap .ps-input{padding-right:44px}
    .ps-suffix{
        position:absolute;
        top:50%;
        right:8px;
        transform:translateY(-50%);
        display:grid;
        min-width:28px;
        height:24px;
        place-items:center;
        border-radius:6px;
        background:var(--ps-surface-2);
        color:var(--ps-muted);
        font-size:10.5px;
        font-weight:650;
        pointer-events:none;
    }
    .ps-help{
        margin:6px 0 0;
        color:var(--ps-muted);
        font-size:11px;
        line-height:1.55;
    }

    .ps-divider{height:1px;margin:18px 0;background:var(--ps-line)}

    /* ---------- Toggles ---------- */
    .ps-toggle-list{display:grid;gap:8px}
    .ps-toggle-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-md);
        background:var(--ps-surface-2);
        padding:12px 14px;
    }
    .ps-toggle-title{
        color:var(--ps-ink);
        font-size:12px;
        font-weight:600;
    }
    .ps-toggle-copy{
        margin-top:3px;
        color:var(--ps-text);
        font-size:11px;
        line-height:1.5;
    }
    .ps-switch{
        position:relative;
        width:38px;
        height:22px;
        flex:0 0 38px;
    }
    .ps-switch input{position:absolute;opacity:0;pointer-events:none}
    .ps-switch span{
        position:absolute;
        inset:0;
        border:1px solid var(--ps-line-strong);
        border-radius:999px;
        background:#e3e5e8;
        cursor:pointer;
        transition:.15s ease;
    }
    .ps-switch span::after{
        content:"";
        position:absolute;
        top:2px;
        left:2px;
        width:16px;
        height:16px;
        border-radius:50%;
        background:#fff;
        box-shadow:0 1px 3px rgba(0,0,0,.16);
        transition:.15s ease;
    }
    .ps-switch input:checked + span{
        border-color:var(--ps-accent);
        background:var(--ps-accent);
    }
    .ps-switch input:checked + span::after{transform:translateX(16px)}

    /* ---------- Governance / mini stats ---------- */
    .ps-governance{
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-md);
        background:var(--ps-surface-2);
        padding:13px 14px;
    }
    .ps-governance-title{
        color:var(--ps-ink);
        font-size:12px;
        font-weight:650;
    }
    .ps-governance-copy{
        margin-top:5px;
        color:var(--ps-text);
        font-size:11px;
        line-height:1.55;
    }
    .ps-permission{
        display:flex;
        align-items:center;
        gap:9px;
        margin-top:16px;
        border-top:1px solid var(--ps-line);
        padding-top:14px;
        color:var(--ps-text);
        font-size:11px;
        line-height:1.5;
    }
    .ps-permission-dot{
        flex:0 0 auto;
        width:7px;
        height:7px;
        border-radius:50%;
        background:var(--ps-success);
    }
    .ps-permission-dot.no{background:var(--ps-danger)}

    .ps-scheduled{border-color:var(--ps-accent-line)}
    .ps-scheduled-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
    }
    .ps-scheduled-meta{
        margin-top:14px;
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:8px;
    }
    .ps-mini{
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-sm);
        background:var(--ps-surface-2);
        padding:9px 10px;
    }
    .ps-mini span{
        display:block;
        color:var(--ps-muted);
        font-size:10px;
        font-weight:600;
    }
    .ps-mini strong{
        display:block;
        margin-top:3px;
        color:var(--ps-ink);
        font-size:11.5px;
        font-weight:600;
    }

    .ps-diff-list{display:grid;gap:0;margin-top:14px}
    .ps-diff{
        display:grid;
        grid-template-columns:minmax(120px,1fr) 1fr 18px 1fr;
        gap:8px;
        align-items:center;
        border-top:1px solid var(--ps-line);
        padding:9px 0;
        color:var(--ps-text);
        font-size:11px;
    }
    .ps-diff strong{color:var(--ps-ink);font-weight:600}
    .ps-arrow{text-align:center;color:var(--ps-accent)}

    /* ---------- Buttons ---------- */
    .ps-btn{
        display:inline-flex;
        height:38px;
        align-items:center;
        justify-content:center;
        gap:7px;
        border-radius:var(--ps-radius-sm);
        padding:0 14px;
        font:inherit;
        font-size:12px;
        font-weight:600;
        cursor:pointer;
        transition:background .12s ease,border-color .12s ease;
    }
    .ps-btn-secondary{
        border:1px solid var(--ps-line-strong);
        background:#fff;
        color:var(--ps-text);
    }
    .ps-btn-secondary:hover{background:var(--ps-surface-2)}
    .ps-btn-danger{
        border:1px solid #e3b8b1;
        background:#fff;
        color:var(--ps-danger);
    }
    .ps-btn-danger:hover{background:var(--ps-danger-soft)}
    .ps-btn-primary{
        border:1px solid var(--ps-accent-ink);
        background:var(--ps-accent);
        color:#fff;
    }
    .ps-btn-primary:hover{background:var(--ps-accent-ink)}

    .ps-actions{
        position:sticky;
        bottom:14px;
        z-index:25;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-top:16px;
        border:1px solid var(--ps-line-strong);
        border-radius:var(--ps-radius-lg);
        background:#fff;
        padding:12px 14px;
        box-shadow:var(--ps-shadow-strong);
    }
    .ps-change-state{
        display:flex;
        align-items:center;
        gap:8px;
        color:var(--ps-muted);
        font-size:12px;
        font-weight:600;
    }
    .ps-change-dot{
        width:7px;
        height:7px;
        border-radius:50%;
        background:#c3c7cd;
    }
    .ps-actions.is-dirty .ps-change-dot{background:var(--ps-accent)}
    .ps-actions.is-dirty .ps-change-state{color:var(--ps-accent-ink)}
    .ps-actions-right{display:flex;gap:8px}

    /* ---------- Audit table ---------- */
    .ps-audit{margin-top:16px;overflow:hidden}
    .ps-audit-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
        padding:18px 20px;
        border-bottom:1px solid var(--ps-line);
    }
    .ps-table-wrap{overflow:auto}
    .ps-table{width:100%;min-width:960px;border-collapse:collapse}
    .ps-table th{
        padding:11px 16px;
        border-bottom:1px solid var(--ps-line);
        background:var(--ps-surface-2);
        color:var(--ps-muted);
        font-size:10.5px;
        font-weight:650;
        text-align:left;
    }
    .ps-table td{
        padding:12px 16px;
        border-bottom:1px solid var(--ps-line);
        color:var(--ps-text);
        font-size:11.5px;
        line-height:1.5;
        vertical-align:top;
    }
    .ps-table tbody tr:last-child td{border-bottom:0}
    .ps-table tbody tr:hover td{background:var(--ps-surface-2)}
    .ps-audit-setting{color:var(--ps-ink);font-weight:600}
    .ps-audit-value{max-width:220px;white-space:normal;word-break:break-word}
    .ps-empty{
        padding:32px;
        text-align:center;
        color:var(--ps-muted);
        font-size:12px;
        line-height:1.6;
    }

    fieldset{min-width:0;border:0;margin:0;padding:0}
    fieldset:disabled{opacity:.6}

    /* ---------- Help popovers ---------- */
    .ps-help-menu{position:relative}
    .ps-help-menu summary{list-style:none;cursor:pointer}
    .ps-help-menu summary::-webkit-details-marker{display:none}
    .ps-help-trigger{
        display:inline-flex;
        height:26px;
        align-items:center;
        justify-content:center;
        gap:6px;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-sm);
        background:#fff;
        padding:0 10px;
        color:var(--ps-text);
        font-size:10.5px;
        font-weight:600;
        user-select:none;
    }
    .ps-help-trigger:hover{background:var(--ps-surface-2)}
    .ps-help-icon{
        display:grid;
        width:16px;
        height:16px;
        place-items:center;
        border:1px solid var(--ps-line-strong);
        border-radius:50%;
        color:var(--ps-muted);
        font-size:9px;
        font-weight:700;
        line-height:1;
    }
    .ps-help-menu[open] .ps-help-icon{
        border-color:var(--ps-accent-line);
        background:var(--ps-accent-soft);
        color:var(--ps-accent-ink);
    }
    .ps-help-chevron{width:11px;height:11px;transition:transform .12s ease}
    .ps-help-menu[open] .ps-help-chevron{transform:rotate(180deg)}
    .ps-help-panel{
        position:absolute;
        top:calc(100% + 8px);
        right:0;
        z-index:80;
        width:min(360px,calc(100vw - 40px));
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-lg);
        background:#fff;
        padding:14px;
        box-shadow:var(--ps-shadow-strong);
    }
    .ps-help-panel-title{
        display:flex;
        align-items:center;
        gap:9px;
        margin-bottom:10px;
        color:var(--ps-ink);
        font-size:12.5px;
        font-weight:650;
    }
    .ps-help-steps{display:grid;gap:8px}
    .ps-help-step{
        display:grid;
        grid-template-columns:22px 1fr;
        gap:9px;
        align-items:start;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-sm);
        background:var(--ps-surface-2);
        padding:9px;
    }
    .ps-help-step-num{
        display:grid;
        width:22px;
        height:22px;
        place-items:center;
        border-radius:6px;
        background:#eceef0;
        color:var(--ps-text);
        font-size:10px;
        font-weight:650;
    }
    .ps-help-step strong{display:block;color:var(--ps-ink);font-size:11px}
    .ps-help-step p{margin:3px 0 0;color:var(--ps-text);font-size:10.5px;line-height:1.55}
    .ps-help-note{
        margin-top:10px;
        border-left:3px solid var(--ps-accent);
        background:var(--ps-accent-soft);
        padding:9px 10px;
        color:var(--ps-accent-ink);
        font-size:10.5px;
        line-height:1.55;
    }

    /* ---------- Confirm modal ---------- */
    .ps-modal-backdrop{
        position:fixed;
        inset:0;
        z-index:1000;
        display:none;
        place-items:center;
        background:rgba(17,19,23,.42);
        padding:20px;
    }
    .ps-modal-backdrop.show{display:grid}
    .ps-modal{
        width:min(460px,100%);
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-lg);
        background:#fff;
        padding:20px;
        box-shadow:0 24px 64px rgba(17,19,23,.28);
    }
    .ps-modal h3{margin:0;color:var(--ps-ink);font-size:16px;font-weight:650}
    .ps-modal p{margin:6px 0 0;color:var(--ps-text);font-size:12px;line-height:1.55}
    .ps-modal-summary{
        margin-top:14px;
        border:1px solid var(--ps-line);
        border-radius:var(--ps-radius-md);
        background:var(--ps-surface-2);
        padding:11px 12px;
        color:var(--ps-text);
        font-size:11.5px;
        line-height:1.85;
    }
    .ps-modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:16px}

    /* ---------- Responsive ---------- */
    @media(max-width:1200px){
        .ps-grid{grid-template-columns:1fr}
        .ps-policy-strip{grid-template-columns:repeat(2,1fr)}
        .ps-policy-cell:nth-child(-n+2){border-bottom:1px solid var(--ps-line)}
    }

    @media(max-width:700px){
        .platform-settings-page{padding:14px 14px 40px}
        .ps-header{flex-direction:column}
        .ps-header-actions{width:100%;justify-content:flex-start}
        .ps-policy-strip,.ps-form-grid{grid-template-columns:1fr}
        .ps-policy-cell{border-right:0;border-bottom:1px solid var(--ps-line)}
        .ps-policy-cell:last-child{border-bottom:0}
        .ps-actions{flex-direction:column;align-items:stretch}
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
        }
    }

    @media(prefers-reduced-motion:reduce){
        .platform-settings-page *{transition:none !important}
    }

    /* ============================================================
       PLATFORM SETTINGS — HEADER PARITY WITH PLATFORM COMMISSIONS
       Visual-only. Existing settings logic and actions are untouched.
       ============================================================ */

    .platform-settings-page .ps-header{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:20px !important;
        margin-bottom:18px !important;
    }

    .platform-settings-page .ps-header-left{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .platform-settings-page .ps-header-icon{
        display:grid !important;
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        place-items:center !important;
        border:1px solid #eadfc9 !important;
        border-radius:12px !important;
        background:#fff8eb !important;
        color:#b77c18 !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .platform-settings-page .ps-header-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .platform-settings-page .ps-eyebrow{
        margin:0 !important;
        color:#9a6f23 !important;
        -webkit-text-fill-color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
        text-transform:uppercase !important;
    }

    .platform-settings-page .ps-title{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .platform-settings-page .ps-title-dark{
        color:#17130f !important;
        -webkit-text-fill-color:#17130f !important;
    }

    .platform-settings-page .ps-title-accent{
        color:#d99500 !important;
        -webkit-text-fill-color:#d99500 !important;
    }

    .platform-settings-page .ps-subtitle{
        max-width:760px !important;
        margin:7px 0 0 !important;
        color:#7f756a !important;
        -webkit-text-fill-color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
        letter-spacing:0 !important;
    }

    .platform-settings-page .ps-header-actions{
        display:flex !important;
        align-items:center !important;
        justify-content:flex-end !important;
        gap:7px !important;
        flex:0 0 auto !important;
        padding-top:0 !important;
    }

    .platform-settings-page .ps-header-actions .ps-badge{
        height:32px !important;
        min-height:32px !important;
        border-radius:9px !important;
        padding:0 10px !important;
        font-size:8px !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-header-actions .ps-help-trigger{
        height:32px !important;
        min-height:32px !important;
        border-radius:9px !important;
        padding:0 9px !important;
        font-size:8px !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-header-actions .ps-help-icon{
        width:17px !important;
        height:17px !important;
        font-size:8px !important;
    }

    .platform-settings-page .ps-header-actions .ps-help-chevron{
        width:10px !important;
        height:10px !important;
    }

    @media(max-height:850px) and (min-width:768px){
        .platform-settings-page .ps-header{
            margin-bottom:15px !important;
        }

        .platform-settings-page .ps-header-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .platform-settings-page .ps-title{
            font-size:27px !important;
        }

        .platform-settings-page .ps-subtitle{
            font-size:10.5px !important;
        }
    }

    @media(max-width:700px){
        .platform-settings-page .ps-header{
            align-items:flex-start !important;
            gap:12px !important;
        }

        .platform-settings-page .ps-header-left{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .platform-settings-page .ps-header-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .platform-settings-page .ps-title{
            font-size:24px !important;
        }

        .platform-settings-page .ps-subtitle{
            font-size:10px !important;
        }

        .platform-settings-page .ps-header-actions{
            width:100% !important;
            justify-content:flex-start !important;
            padding-left:51px !important;
        }
    }


    /* ============================================================
       SELLER VISUAL SYSTEM — FINAL PLATFORM SETTINGS OVERRIDE
       Visual-only. Settings keys, permissions, scheduling, routes,
       form behavior, audit data and JavaScript remain untouched.
       ============================================================ */

    .platform-settings-page{
        --ps-bg:#f4f5f7 !important;
        --ps-surface:#ffffff !important;
        --ps-surface-2:#f9fafb !important;
        --ps-ink:#111827 !important;
        --ps-text:#475467 !important;
        --ps-muted:#7c8592 !important;
        --ps-line:#e5e7eb !important;
        --ps-line-strong:#d1d5db !important;
        --ps-accent:#d59617 !important;
        --ps-accent-ink:#b97805 !important;
        --ps-accent-soft:#fff8e8 !important;
        --ps-accent-line:#ead7ae !important;
        --ps-radius-lg:14px !important;
        --ps-radius-md:10px !important;
        --ps-radius-sm:9px !important;
        --ps-shadow:
            0 2px 5px rgba(15,23,42,.045),
            0 10px 24px rgba(15,23,42,.075),
            0 24px 54px rgba(15,23,42,.095),
            inset 0 1px 0 rgba(255,255,255,.92) !important;
        --ps-shadow-strong:
            0 3px 7px rgba(15,23,42,.055),
            0 14px 32px rgba(15,23,42,.095),
            0 30px 68px rgba(15,23,42,.11) !important;

        width:100% !important;
        max-width:1500px !important;
        margin:0 auto !important;
        padding:0 0 28px !important;
        color:var(--ps-ink) !important;
        font-size:11px !important;
    }

    /* Open Seller-style page heading. */
    .platform-settings-page .ps-header{
        min-height:66px !important;
        align-items:center !important;
        gap:18px !important;
        margin:0 0 14px !important;
        padding:2px 2px 13px !important;
        border-bottom:1px solid #eceff2 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-header-left{
        align-items:flex-start !important;
        gap:0 !important;
    }

    .platform-settings-page .ps-header-icon{
        display:none !important;
    }

    .platform-settings-page .ps-eyebrow{
        margin:0 !important;
        color:#b97805 !important;
        -webkit-text-fill-color:#b97805 !important;
        font-size:8px !important;
        font-weight:800 !important;
        line-height:1.2 !important;
        letter-spacing:.16em !important;
    }

    .platform-settings-page .ps-title{
        margin:4px 0 0 !important;
        font-size:clamp(29px,2.2vw,36px) !important;
        font-weight:700 !important;
        line-height:1 !important;
        letter-spacing:-.035em !important;
    }

    .platform-settings-page .ps-title-dark{
        color:#111827 !important;
        -webkit-text-fill-color:#111827 !important;
    }

    .platform-settings-page .ps-title-accent{
        color:#d59617 !important;
        -webkit-text-fill-color:#d59617 !important;
    }

    .platform-settings-page .ps-subtitle{
        max-width:760px !important;
        margin:7px 0 0 !important;
        color:#7c8592 !important;
        -webkit-text-fill-color:#7c8592 !important;
        font-size:10px !important;
        font-weight:400 !important;
        line-height:1.55 !important;
    }

    .platform-settings-page .ps-header-actions{
        align-items:center !important;
        justify-content:flex-end !important;
        gap:7px !important;
        padding-top:0 !important;
    }

    .platform-settings-page .ps-header-actions .ps-badge,
    .platform-settings-page .ps-header-actions .ps-help-trigger{
        min-height:36px !important;
        height:36px !important;
        border:1px solid #e5e7eb !important;
        border-radius:9px !important;
        background:#fff !important;
        padding:0 10px !important;
        color:#475467 !important;
        font-size:8.5px !important;
        font-weight:600 !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-header-actions .ps-badge::before{
        background:#d59617 !important;
    }

    /* Main page surfaces carry depth; nested UI remains flat. */
    .platform-settings-page .ps-card{
        border:1px solid #e5e7eb !important;
        border-radius:14px !important;
        background:#fff !important;
        box-shadow:var(--ps-shadow) !important;
        transform:none !important;
    }

    .platform-settings-page .ps-policy-strip{
        grid-template-columns:repeat(4,minmax(0,1fr)) !important;
        margin-bottom:14px !important;
        overflow:hidden !important;
    }

    .platform-settings-page .ps-policy-cell{
        min-height:92px !important;
        padding:14px 16px !important;
        border-right:1px solid #eceff2 !important;
    }

    .platform-settings-page .ps-kicker{
        color:#7c8592 !important;
        font-size:8px !important;
        font-weight:700 !important;
        letter-spacing:.02em !important;
    }

    .platform-settings-page .ps-policy-value{
        margin-top:5px !important;
        color:#111827 !important;
        font-size:21px !important;
        font-weight:700 !important;
        line-height:1.05 !important;
        letter-spacing:-.025em !important;
    }

    .platform-settings-page .ps-policy-note{
        margin-top:6px !important;
        color:#8a93a0 !important;
        font-size:8px !important;
        line-height:1.45 !important;
    }

    .platform-settings-page .ps-grid{
        grid-template-columns:minmax(0,1.6fr) minmax(330px,.78fr) !important;
        gap:14px !important;
    }

    .platform-settings-page .ps-stack{
        gap:14px !important;
    }

    .platform-settings-page .ps-section{
        padding:16px !important;
    }

    .platform-settings-page .ps-section-head{
        gap:12px !important;
        margin-bottom:14px !important;
        padding-bottom:12px !important;
        border-bottom:1px solid #eceff2 !important;
    }

    .platform-settings-page .ps-section-title{
        color:#1f2937 !important;
        font-size:12px !important;
        font-weight:700 !important;
        line-height:1.35 !important;
        letter-spacing:-.015em !important;
    }

    .platform-settings-page .ps-section-copy{
        max-width:560px !important;
        margin-top:4px !important;
        color:#7c8592 !important;
        font-size:8.5px !important;
        line-height:1.5 !important;
    }

    .platform-settings-page .ps-head-actions{
        gap:6px !important;
    }

    .platform-settings-page .ps-tag,
    .platform-settings-page .ps-help-trigger{
        min-height:28px !important;
        height:28px !important;
        border:1px solid #e5e7eb !important;
        border-radius:8px !important;
        background:#fff !important;
        padding:0 8px !important;
        color:#667085 !important;
        font-size:7.5px !important;
        font-weight:650 !important;
        box-shadow:none !important;
    }

    /* Form density matches the Seller admin workspaces. */
    .platform-settings-page .ps-form-grid{
        gap:13px !important;
    }

    .platform-settings-page .ps-label-row{
        margin-bottom:5px !important;
    }

    .platform-settings-page .ps-label{
        color:#344054 !important;
        font-size:9px !important;
        font-weight:650 !important;
    }

    .platform-settings-page .ps-current{
        color:#98a2b3 !important;
        font-size:7.5px !important;
        font-weight:500 !important;
    }

    .platform-settings-page .ps-input,
    .platform-settings-page .ps-textarea{
        border:1px solid #d1d5db !important;
        border-radius:9px !important;
        background:#fff !important;
        color:#344054 !important;
        font-size:9px !important;
        font-weight:500 !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-input{
        height:40px !important;
        padding:0 10px !important;
    }

    .platform-settings-page .ps-textarea{
        min-height:82px !important;
        padding:9px 10px !important;
        line-height:1.55 !important;
    }

    .platform-settings-page .ps-input:hover,
    .platform-settings-page .ps-textarea:hover{
        border-color:#c5cad1 !important;
    }

    .platform-settings-page .ps-input:focus,
    .platform-settings-page .ps-textarea:focus{
        border-color:#d59617 !important;
        box-shadow:0 0 0 3px rgba(213,150,23,.085) !important;
    }

    .platform-settings-page .ps-input-wrap .ps-input{
        padding-right:44px !important;
    }

    .platform-settings-page .ps-suffix{
        min-width:28px !important;
        height:24px !important;
        border-radius:6px !important;
        background:#f3f4f6 !important;
        color:#7c8592 !important;
        font-size:8px !important;
    }

    .platform-settings-page .ps-help{
        margin-top:5px !important;
        color:#8a93a0 !important;
        font-size:7.6px !important;
        line-height:1.5 !important;
    }

    .platform-settings-page .ps-divider{
        margin:14px 0 !important;
        background:#eceff2 !important;
    }

    /* Nested operational blocks stay visually quiet. */
    .platform-settings-page .ps-toggle-list{
        gap:7px !important;
    }

    .platform-settings-page .ps-toggle-row,
    .platform-settings-page .ps-governance,
    .platform-settings-page .ps-mini,
    .platform-settings-page .ps-help-step,
    .platform-settings-page .ps-modal-summary{
        border:1px solid #e5e7eb !important;
        background:#f9fafb !important;
        box-shadow:none !important;
    }

    .platform-settings-page .ps-toggle-row{
        border-radius:10px !important;
        padding:10px 11px !important;
    }

    .platform-settings-page .ps-toggle-title,
    .platform-settings-page .ps-governance-title{
        color:#344054 !important;
        font-size:9px !important;
        font-weight:650 !important;
    }

    .platform-settings-page .ps-toggle-copy,
    .platform-settings-page .ps-governance-copy{
        color:#7c8592 !important;
        font-size:7.8px !important;
        line-height:1.5 !important;
    }

    .platform-settings-page .ps-switch{
        width:38px !important;
        height:22px !important;
        flex-basis:38px !important;
    }

    .platform-settings-page .ps-switch input:checked + span{
        border-color:#d59617 !important;
        background:#d59617 !important;
    }

    .platform-settings-page .ps-governance{
        border-radius:10px !important;
        padding:11px 12px !important;
    }

    .platform-settings-page .ps-permission{
        margin-top:14px !important;
        padding-top:12px !important;
        border-top:1px solid #eceff2 !important;
        color:#667085 !important;
        font-size:8px !important;
    }

    .platform-settings-page .ps-scheduled{
        border-color:#e8d6af !important;
        margin-top:14px !important;
    }

    .platform-settings-page .ps-scheduled-meta{
        gap:7px !important;
        margin-top:12px !important;
    }

    .platform-settings-page .ps-mini{
        padding:8px 9px !important;
    }

    .platform-settings-page .ps-mini span{
        color:#8a93a0 !important;
        font-size:7px !important;
    }

    .platform-settings-page .ps-mini strong{
        color:#344054 !important;
        font-size:8px !important;
    }

    .platform-settings-page .ps-diff{
        border-top:1px solid #eceff2 !important;
        padding:8px 0 !important;
        color:#667085 !important;
        font-size:8px !important;
    }

    /* Buttons: one strong gold action, quiet supporting controls. */
    .platform-settings-page .ps-btn{
        height:38px !important;
        border-radius:9px !important;
        padding:0 12px !important;
        font-size:8.5px !important;
        font-weight:650 !important;
        box-shadow:none !important;
        transform:none !important;
    }

    .platform-settings-page .ps-btn-primary{
        border-color:#d59617 !important;
        background:#d59617 !important;
        color:#fff !important;
        box-shadow:0 4px 10px rgba(213,150,23,.11) !important;
    }

    .platform-settings-page .ps-btn-primary:hover,
    .platform-settings-page .ps-btn-primary:focus-visible{
        border-color:#b97805 !important;
        background:#b97805 !important;
        outline:none !important;
    }

    .platform-settings-page .ps-btn-secondary{
        border-color:#d1d5db !important;
        background:#fff !important;
        color:#475467 !important;
    }

    /* Sticky action bar is the focal workflow surface. */
    .platform-settings-page .ps-actions{
        bottom:12px !important;
        margin-top:14px !important;
        border:1px solid #d1d5db !important;
        border-radius:14px !important;
        background:#fff !important;
        padding:10px 12px !important;
        box-shadow:
            0 2px 5px rgba(15,23,42,.055),
            0 12px 28px rgba(15,23,42,.10),
            0 26px 58px rgba(15,23,42,.11) !important;
    }

    .platform-settings-page .ps-change-state{
        color:#7c8592 !important;
        font-size:8.5px !important;
    }

    .platform-settings-page .ps-actions.is-dirty .ps-change-dot{
        background:#d59617 !important;
    }

    .platform-settings-page .ps-actions.is-dirty .ps-change-state{
        color:#b97805 !important;
    }

    /* Help overlays and confirmation modal receive focal elevation. */
    .platform-settings-page .ps-help-panel{
        width:min(360px,calc(100vw - 40px)) !important;
        border:1px solid #e5e7eb !important;
        border-radius:12px !important;
        background:#fff !important;
        padding:12px !important;
        box-shadow:
            0 3px 7px rgba(15,23,42,.05),
            0 14px 32px rgba(15,23,42,.095),
            0 28px 60px rgba(15,23,42,.10) !important;
    }

    .platform-settings-page .ps-help-panel-title{
        margin-bottom:9px !important;
        color:#344054 !important;
        font-size:9px !important;
    }

    .platform-settings-page .ps-help-steps{
        gap:7px !important;
    }

    .platform-settings-page .ps-help-step{
        border-radius:8px !important;
        padding:8px !important;
    }

    .platform-settings-page .ps-help-step strong{
        color:#344054 !important;
        font-size:8px !important;
    }

    .platform-settings-page .ps-help-step p,
    .platform-settings-page .ps-help-note{
        font-size:7.5px !important;
        line-height:1.5 !important;
    }

    .platform-settings-page .ps-help-note{
        border-left-color:#d59617 !important;
        background:#fff8e8 !important;
        color:#94620a !important;
    }

    .ps-modal-backdrop{
        background:rgba(17,24,39,.48) !important;
        backdrop-filter:none !important;
    }

    .ps-modal{
        width:min(500px,calc(100vw - 28px)) !important;
        border:1px solid #e5e7eb !important;
        border-radius:18px !important;
        background:#fff !important;
        padding:18px !important;
        box-shadow:
            0 3px 7px rgba(15,23,42,.055),
            0 14px 32px rgba(15,23,42,.095),
            0 30px 68px rgba(15,23,42,.11) !important;
    }

    .ps-modal h3{
        color:#1f2937 !important;
        font-size:15px !important;
        font-weight:700 !important;
        letter-spacing:-.02em !important;
    }

    .ps-modal p{
        color:#7c8592 !important;
        font-size:8.5px !important;
        line-height:1.55 !important;
    }

    .ps-modal-summary{
        margin-top:12px !important;
        border-radius:10px !important;
        padding:10px 11px !important;
        color:#475467 !important;
        font-size:8.5px !important;
        line-height:1.7 !important;
    }

    /* Audit history remains a dense data surface. */
    .platform-settings-page .ps-audit{
        margin-top:14px !important;
        overflow:hidden !important;
    }

    .platform-settings-page .ps-audit-head{
        gap:12px !important;
        padding:14px 16px !important;
        border-bottom:1px solid #eceff2 !important;
    }

    .platform-settings-page .ps-table{
        min-width:960px !important;
    }

    .platform-settings-page .ps-table th{
        padding:9px 11px !important;
        border-bottom:1px solid #eceff2 !important;
        background:#f9fafb !important;
        color:#7c8592 !important;
        font-size:7.5px !important;
        font-weight:700 !important;
        letter-spacing:.035em !important;
        text-transform:uppercase !important;
    }

    .platform-settings-page .ps-table td{
        padding:9px 11px !important;
        border-bottom:1px solid #f0f2f4 !important;
        color:#475467 !important;
        font-size:8px !important;
        line-height:1.45 !important;
    }

    .platform-settings-page .ps-table tbody tr:hover td{
        background:#fffcf5 !important;
    }

    .platform-settings-page .ps-audit-setting{
        color:#344054 !important;
        font-weight:650 !important;
    }

    .platform-settings-page .ps-alert,
    .platform-settings-page .ps-errors{
        border-radius:10px !important;
        padding:10px 12px !important;
        font-size:8.5px !important;
        box-shadow:
            0 1px 3px rgba(15,23,42,.035),
            0 7px 18px rgba(15,23,42,.06),
            0 15px 32px rgba(15,23,42,.065) !important;
    }

    @media(max-width:1200px){
        .platform-settings-page .ps-policy-strip{
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        }

        .platform-settings-page .ps-grid{
            grid-template-columns:1fr !important;
        }
    }

    @media(max-width:700px){
        .platform-settings-page{
            padding:0 0 20px !important;
        }

        .platform-settings-page .ps-header{
            align-items:flex-start !important;
            flex-direction:column !important;
            gap:10px !important;
            padding:0 0 12px !important;
        }

        .platform-settings-page .ps-title{
            font-size:clamp(28px,8vw,34px) !important;
        }

        .platform-settings-page .ps-subtitle{
            font-size:9.5px !important;
        }

        .platform-settings-page .ps-header-actions{
            width:100% !important;
            justify-content:flex-start !important;
            padding-left:0 !important;
        }

        .platform-settings-page .ps-policy-strip,
        .platform-settings-page .ps-form-grid{
            grid-template-columns:1fr !important;
        }

        .platform-settings-page .ps-policy-cell{
            min-height:0 !important;
            border-right:0 !important;
            border-bottom:1px solid #eceff2 !important;
        }

        .platform-settings-page .ps-section{
            padding:14px !important;
        }

        .platform-settings-page .ps-section-head{
            align-items:flex-start !important;
            flex-direction:column !important;
        }

        .platform-settings-page .ps-head-actions{
            width:100% !important;
            flex-wrap:wrap !important;
        }

        .platform-settings-page .ps-input{
            height:42px !important;
        }

        .platform-settings-page .ps-actions{
            bottom:8px !important;
            align-items:stretch !important;
            flex-direction:column !important;
        }

        .platform-settings-page .ps-actions-right{
            width:100% !important;
        }

        .platform-settings-page .ps-actions-right .ps-btn{
            min-height:42px !important;
            flex:1 !important;
        }

        .platform-settings-page .ps-help-panel{
            left:10px !important;
            right:10px !important;
            bottom:12px !important;
            width:auto !important;
        }

        .ps-modal{
            width:calc(100vw - 14px) !important;
            border-radius:14px !important;
            padding:14px !important;
        }
    }

    @media(prefers-reduced-motion:reduce){
        .platform-settings-page *,
        .ps-modal,
        .ps-modal-backdrop{
            animation:none !important;
            transition:none !important;
            scroll-behavior:auto !important;
        }
    }
</style>

<div class="platform-settings-page">
    <header class="ps-header">
        <div class="ps-header-left">
            <span class="ps-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1.1-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.65 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.5 4.65a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.04.38.25.73.6 1 .3.23.68.37 1.1.4H21a2 2 0 1 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z"/></svg>
            </span>
            <div>
                <p class="ps-eyebrow">Administration</p>
                <h1 class="ps-title"><span class="ps-title-dark">Platform</span> <span class="ps-title-accent">Settings</span></h1>
                <p class="ps-subtitle">Manage pricing, checkout rules, registration access, finance controls, and scheduled policy changes from one governed workspace.</p>
            </div>
        </div>
        <div class="ps-header-actions">
            <span class="ps-badge">Active policy {{ $activeVersionLabel }}</span>

            <details class="ps-help-menu">
                <summary class="ps-help-trigger" aria-label="Open Platform Settings help">
                    <span class="ps-help-icon">?</span>
                    Help &amp; process
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
            <div class="ps-kicker">Active policy</div>
            <div class="ps-policy-value">{{ $activeVersionLabel }}</div>
            <div class="ps-policy-note">
                @if($activeVersion)
                    Effective {{ $activeVersion->effective_at?->format('M j, Y, h:i A') }}
                @else
                    Existing legacy configuration; first governed save creates v1.
                @endif
            </div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Commission rate</div>
            <div class="ps-policy-value">{{ number_format((float)$settings['commission_rate'],2) }}%</div>
            <div class="ps-policy-note">Historical earned commissions keep their stored rate.</div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Delivery fee / seller</div>
            <div class="ps-policy-value">₱{{ number_format((float)$settings['delivery_fee_per_seller'],2) }}</div>
            <div class="ps-policy-note">Used by cart and checkout fee calculations.</div>
        </div>
        <div class="ps-policy-cell">
            <div class="ps-kicker">Next scheduled policy</div>
            <div class="ps-policy-value">{{ $scheduledVersion ? 'v'.$scheduledVersion->version_number : 'None' }}</div>
            <div class="ps-policy-note">
                {{ $scheduledVersion ? $scheduledVersion->effective_at?->format('M j, Y, h:i A') : 'No future configuration is queued.' }}
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
                                <h2 class="ps-section-title">Commerce &amp; fees</h2>
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
                                <h2 class="ps-section-title">Order &amp; checkout rules</h2>
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
                                <h2 class="ps-section-title">Registration &amp; notifications</h2>
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
                                <h2 class="ps-section-title">Finance rules</h2>
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
                                <h2 class="ps-section-title">Commerce maintenance</h2>
                                <p class="ps-section-copy">Control commerce availability without shutting down the Admin workspace. Existing checkout and registration controls are preserved; Seller posting and platform-campaign delivery are managed here too.</p>
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
                                <div class="ps-help-note">Maintenance mode continues to pause checkout and registration entry points. The separate feature switches below let Admin pause Seller product posting or Buyer-facing platform campaigns without disabling the rest of SARI.</div>
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

                        <div class="ps-toggle-row" style="margin-top:12px">
                            <div>
                                <div class="ps-toggle-title">Seller product posting</div>
                                <div class="ps-toggle-copy">When disabled, Sellers can still manage existing products, but cannot open Add Product or save new product drafts/listings.</div>
                            </div>
                            <label class="ps-switch">
                                <input type="hidden" name="seller_product_posting_enabled" value="0">
                                <input type="checkbox" name="seller_product_posting_enabled" value="1" @checked($boolValue('seller_product_posting_enabled',true))>
                                <span></span>
                            </label>
                        </div>

                        <div class="ps-toggle-row" style="margin-top:12px">
                            <div>
                                <div class="ps-toggle-title">Platform campaigns</div>
                                <div class="ps-toggle-copy">When disabled, Admin event discounts and SARI platform vouchers stop applying to Buyer quotes and checkout. Campaign records stay intact for later reactivation.</div>
                            </div>
                            <label class="ps-switch">
                                <input type="hidden" name="platform_campaigns_enabled" value="0">
                                <input type="checkbox" name="platform_campaigns_enabled" value="1" @checked($boolValue('platform_campaigns_enabled',true))>
                                <span></span>
                            </label>
                        </div>

                        <label class="ps-field" style="margin-top:14px">
                            <div class="ps-label-row"><span class="ps-label">Maintenance message</span></div>
                            <textarea class="ps-textarea" name="maintenance_message" maxlength="500" required>{{ old('maintenance_message',$settings['maintenance_message']) }}</textarea>
                        </label>
                    </section>

                    <section class="ps-card ps-section">
                        <div class="ps-section-head">
                            <div>
                                <h2 class="ps-section-title">Change governance</h2>
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
                            <div class="ps-governance-title">Effective date &amp; traceability</div>
                            <div class="ps-governance-copy">Choose now or a future date. Only one future policy may be pending at a time so overlapping commission windows cannot be created.</div>
                        </div>

                        <label class="ps-field" style="margin-top:14px">
                            <div class="ps-label-row"><span class="ps-label">Effective at</span><span class="ps-current">{{ config('app.timezone') }}</span></div>
                            <input class="ps-input" name="effective_at" type="datetime-local" value="{{ $effectiveDefault }}" required>
                        </label>

                        <label class="ps-field" style="margin-top:14px">
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
                    <button type="submit" class="ps-btn ps-btn-primary">Review &amp; save policy</button>
                </div>
            </div>
        </fieldset>
    </form>

    @if($scheduledVersion)
        <section class="ps-card ps-section ps-scheduled" style="margin-top:20px">
            <div class="ps-scheduled-head">
                <div>
                    <div class="ps-kicker">Pending scheduled policy</div>
                    <h2 class="ps-section-title" style="margin-top:4px">Version {{ $scheduledVersion->version_number }}</h2>
                    <p class="ps-section-copy">Effective {{ $scheduledVersion->effective_at?->format('M j, Y, h:i A') }} — {{ $scheduledVersion->change_reason }}</p>
                </div>

                @if($canManageSettings)
                    <form method="POST" action="{{ route('admin.platform-settings.update') }}" onsubmit="return confirm('Cancel this future platform policy?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="operation" value="cancel_scheduled">
                        <input type="hidden" name="version_id" value="{{ $scheduledVersion->id }}">
                        <button class="ps-btn ps-btn-danger" type="submit">Cancel schedule</button>
                    </form>
                @endif
            </div>

            <div class="ps-scheduled-meta">
                <div class="ps-mini"><span>Scheduled by</span><strong>{{ $scheduledVersion->admin?->name ?: 'Administrator' }}</strong></div>
                <div class="ps-mini"><span>Created</span><strong>{{ $scheduledVersion->created_at?->format('M j, Y, h:i A') }}</strong></div>
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
                <h2 class="ps-section-title">Configuration audit history</h2>
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
                        <tr><th>Setting</th><th>Previous</th><th>New</th><th>Action</th><th>Changed by</th><th>Effective</th><th>Reason</th></tr>
                    </thead>
                    <tbody>
                        @foreach($auditHistory as $audit)
                            <tr>
                                <td class="ps-audit-setting">{{ $audit->setting_key === '*' ? 'Policy Version' : ($labels[$audit->setting_key] ?? str($audit->setting_key)->replace('_',' ')->title()) }}</td>
                                <td class="ps-audit-value">{{ $audit->old_value ?? '—' }}</td>
                                <td class="ps-audit-value">{{ $audit->new_value ?? '—' }}</td>
                                <td>{{ str($audit->action)->replace('_',' ')->title() }}</td>
                                <td>{{ $audit->admin?->name ?: 'System / Legacy Admin' }}</td>
                                <td>{{ $audit->effective_at?->format('M j, Y, h:i A') ?: '—' }}</td>
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
            <button type="button" class="ps-btn ps-btn-primary" id="confirmSettingsSave">Confirm & publish</button>
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

    const initial=new Map(
        controls.map(el=>[el.name,el.type==='checkbox'?el.checked:el.value])
    );

    const dirtyNames=new Set();

    function controlIsDirty(el){
        const before=initial.get(el.name);
        return el.type==='checkbox' ? el.checked!==before : el.value!==before;
    }

    function updateDirtyControl(el){
        if(!el?.name) return;

        if(controlIsDirty(el)) dirtyNames.add(el.name);
        else dirtyNames.delete(el.name);

        const changed=dirtyNames.size>0;

        actionBar?.classList.toggle('is-dirty',changed);

        if(changeText){
            changeText.textContent=changed
                ? 'Unsaved policy changes'
                : 'No unsaved changes';
        }

        if(confirmed) confirmed.value='0';
    }

    function syncDirtyState(){
        dirtyNames.clear();

        controls.forEach(el=>{
            if(controlIsDirty(el)) dirtyNames.add(el.name);
        });

        const changed=dirtyNames.size>0;

        actionBar?.classList.toggle('is-dirty',changed);

        if(changeText){
            changeText.textContent=changed
                ? 'Unsaved policy changes'
                : 'No unsaved changes';
        }

        if(confirmed) confirmed.value='0';
    }

    function debounce(callback,wait=120){
        let timer=0;

        return function(...args){
            window.clearTimeout(timer);
            timer=window.setTimeout(()=>callback.apply(this,args),wait);
        };
    }

    const debouncedTextDirty=debounce(el=>updateDirtyControl(el),120);

    controls.forEach(el=>{
        if(el.type==='checkbox'){
            el.addEventListener('change',()=>updateDirtyControl(el));
            return;
        }

        el.addEventListener('input',()=>debouncedTextDirty(el),{passive:true});
        el.addEventListener('change',()=>updateDirtyControl(el));
    });

    resetButton?.addEventListener('click',()=>{
        controls.forEach(el=>{
            const value=initial.get(el.name);
            if(el.type==='checkbox') el.checked=Boolean(value);
            else el.value=value;
        });
        syncDirtyState();
    });

    form.addEventListener('submit',event=>{
        if(confirmed?.value==='1') return;
        event.preventDefault();

        const commission=form.querySelector('[name="commission_rate"]')?.value || '0';
        const delivery=form.querySelector('[name="delivery_fee_per_seller"]')?.value || '0';
        const effective=form.querySelector('[name="effective_at"]')?.value || '';
        const maintenance=form.querySelector('input[type="checkbox"][name="maintenance_mode"]')?.checked;
        const sellerPosting=form.querySelector('input[type="checkbox"][name="seller_product_posting_enabled"]')?.checked;
        const campaigns=form.querySelector('input[type="checkbox"][name="platform_campaigns_enabled"]')?.checked;

        if(summary){
            summary.innerHTML=
                `<div><strong>Commission:</strong> ${Number(commission).toFixed(2)}%</div>`+
                `<div><strong>Delivery fee / seller:</strong> ₱${Number(delivery).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}</div>`+
                `<div><strong>Effective:</strong> ${effective || 'Now'}</div>`+
                `<div><strong>Maintenance mode:</strong> ${maintenance?'Enabled':'Disabled'}</div>`+
                `<div><strong>Seller product posting:</strong> ${sellerPosting?'Enabled':'Disabled'}</div>`+
                `<div><strong>Platform campaigns:</strong> ${campaigns?'Enabled':'Disabled'}</div>`;
        }

        modal?.classList.add('show');
        modal?.setAttribute('aria-hidden','false');

        window.requestAnimationFrame(()=>{
            cancel?.focus({preventScroll:true});
        });
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

    document.addEventListener('keydown',event=>{
        if(event.key!=='Escape' || !modal?.classList.contains('show')) return;

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden','true');
    });

    document.addEventListener('click', event => {
        document.querySelectorAll('.ps-help-menu[open]').forEach(menu => {
            if (!menu.contains(event.target)) {
                menu.removeAttribute('open');
            }
        });
    });

    document.addEventListener('click',event=>{
        const summaryEl=event.target.closest('.ps-help-menu > summary');

        if(!summaryEl) return;

        const menu=summaryEl.parentElement;

        document.querySelectorAll('.ps-help-menu[open]').forEach(other=>{
            if(other!==menu) other.removeAttribute('open');
        });
    });

    syncDirtyState();
})();
</script>
@endsection