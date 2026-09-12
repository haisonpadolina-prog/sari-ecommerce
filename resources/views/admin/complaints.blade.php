@extends('layouts.admin')

@section('title','Complaints — SARI Admin')
@section('page-title','Complaints')

@section('content')
<style>
    .complaints-page{font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#211c16}
    .complaints-surface{background:#fff;border:1px solid #e7ddd1;border-radius:18px;box-shadow:0 3px 8px rgba(61,43,22,.045),0 18px 42px rgba(61,43,22,.095),0 38px 78px rgba(61,43,22,.045),inset 0 1px 0 rgba(255,255,255,.98)}
    .complaints-header-main{display:flex;align-items:center;gap:14px}.complaints-header-icon{display:grid;width:44px;height:44px;flex:0 0 44px;place-items:center;border:1px solid #eadfc9;border-radius:14px;background:#fff8eb;color:#b77c18;box-shadow:0 2px 5px rgba(75,54,25,.03),0 9px 20px rgba(75,54,25,.06)}.complaints-header-icon svg{width:18px;height:18px}
    .complaints-eyebrow{color:#9a7b43;font-size:9px;font-weight:600;line-height:1.2;letter-spacing:.14em;text-transform:uppercase}.complaints-title{margin-top:4px;font-size:clamp(1.75rem,1.55rem + .5vw,2.15rem);font-weight:700;line-height:1.08;letter-spacing:-.04em}.complaints-title-base{color:#17130f}.complaints-title-accent{color:#d99500}.complaints-subtitle{max-width:900px;margin-top:6px;color:#81786c;font-size:clamp(.73rem,.70rem + .08vw,.81rem);line-height:1.65}
    .complaint-stat{min-height:110px;padding:18px;border:1px solid #e7ddd1;border-radius:18px;background:#fff;text-align:left;box-shadow:0 3px 7px rgba(61,43,22,.04),0 15px 34px rgba(61,43,22,.085),0 30px 58px rgba(61,43,22,.038),inset 0 1px 0 rgba(255,255,255,.98);transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease}.complaint-stat:hover{transform:translateY(-3px);border-color:#d9c9b1;box-shadow:0 4px 9px rgba(61,43,22,.05),0 21px 46px rgba(61,43,22,.115),0 40px 76px rgba(61,43,22,.048),inset 0 1px 0 rgba(255,255,255,.98)}.complaint-stat.is-active{border-color:#dfbd78}.complaint-stat-icon{display:grid;width:48px;height:48px;flex:0 0 48px;place-items:center;border-radius:12px}.complaint-stat-label{color:#7d746a;font-size:11.5px;font-weight:500;line-height:1.35}.complaint-stat-value{margin-top:4px;color:#1c1712;font-size:26px;font-weight:700;line-height:1;letter-spacing:-.04em}.complaint-stat-helper{margin-top:8px;color:#9b9288;font-size:9px;line-height:1.35}
    .complaints-filter{position:relative;z-index:20;padding:12px}.complaints-filter-grid{display:grid;grid-template-columns:minmax(360px,1fr) 190px 124px 82px;gap:12px;align-items:center}.complaints-search{position:relative;min-width:0}.complaints-search svg{position:absolute;top:50%;left:16px;width:16px;height:16px;color:#9d8f7e;transform:translateY(-50%);pointer-events:none}.complaints-control{width:100%;height:44px;border:1px solid #e8e0d5;border-radius:12px;background:#fff;color:#332c25;font-size:11px;box-shadow:0 2px 4px rgba(61,43,22,.025),0 7px 16px rgba(61,43,22,.045),inset 0 1px 0 rgba(255,255,255,.96);transition:border-color .16s ease,box-shadow .16s ease}.complaints-control:hover{border-color:#d8c8b1}.complaints-control:focus{outline:none;border-color:#d9a33a;box-shadow:0 0 0 4px rgba(217,149,0,.08),0 10px 24px rgba(61,43,22,.07)}.complaints-search input{padding:0 16px 0 44px}.complaints-search input::placeholder{color:#a69c91}.complaints-select-wrap{position:relative}.complaints-select-wrap select{appearance:none;padding:0 40px 0 36px;font-weight:500;cursor:pointer}.complaints-status-dot{position:absolute;top:50%;left:15px;width:8px;height:8px;border-radius:999px;background:#3f9a61;transform:translateY(-50%);pointer-events:none}.complaints-select-chevron{position:absolute;top:50%;right:14px;width:14px;height:14px;color:#8b8175;transform:translateY(-50%);pointer-events:none}
    .complaints-apply,.complaints-reset{display:inline-flex;width:100%;height:44px;align-items:center;justify-content:center;gap:8px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;transition:transform .16s ease,background-color .16s ease,border-color .16s ease}.complaints-apply{border:0;background:#d99500;color:#fff;box-shadow:0 3px 7px rgba(183,124,0,.10),0 13px 28px rgba(217,149,0,.23)}.complaints-apply:hover{background:#bd8205;transform:translateY(-1px)}.complaints-reset{border:1px solid #e6ddd2;background:#fff;color:#6f665b}.complaints-reset:hover{border-color:#d8c8b1;background:#faf8f4;color:#51483f}
    .complaints-table-head,.complaint-row{display:grid;grid-template-columns:minmax(260px,1.5fr) 210px 170px 120px 90px;gap:16px;align-items:center}.complaints-table-head{min-height:50px;padding:13px 20px;border-bottom:1px solid #eee8df;background:#fcfbf8;color:#847b70;font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase}.complaint-row{min-height:82px;padding:14px 20px;border-bottom:1px solid #f0ebe4;background:#fff;transition:background-color .14s ease}.complaint-row:hover{background:#fdfbf7}.complaint-row:last-child{border-bottom:0}.complaint-subject{color:#2e2924;font-size:12px;font-weight:700;line-height:1.35}.complaint-preview{max-width:540px;margin-top:5px;overflow:hidden;color:#8c8379;font-size:9px;line-height:1.5;text-overflow:ellipsis;white-space:nowrap}.complaint-meta-main{color:#514a42;font-size:10px;font-weight:500;line-height:1.4}.complaint-meta-sub{margin-top:3px;color:#958c80;font-size:9px;line-height:1.4}.complaint-status{display:inline-flex;align-items:center;gap:7px;width:fit-content;border-radius:999px;padding:6px 10px;font-size:9.5px;font-weight:600;line-height:1}.complaint-status::before{content:"";width:6px;height:6px;border-radius:999px;background:currentColor}.complaint-status-open{border:1px solid #efd9b0;background:#fff8e9;color:#a87019}.complaint-status-resolved{border:1px solid #d6e9dc;background:#eef8f1;color:#36805a}.complaint-view{display:inline-grid;width:30px;height:30px;place-items:center;border:0;background:transparent;color:#3f3b37;transition:color .15s ease,transform .15s ease}.complaint-view svg{width:16px;height:16px;stroke:currentColor}.complaint-view:hover,.complaint-view:focus-visible{color:#e09a00;transform:translateY(-1px) scale(1.08);outline:none}.complaints-footer{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:64px;padding:14px 20px;border-top:1px solid #eee8df;color:#756d63;font-size:10px}.complaints-empty{padding:54px 20px;text-align:center;color:#918677;font-size:10px}
    #complaintModalBackdrop{opacity:0;visibility:hidden;pointer-events:none;transition:opacity .16s ease,visibility .16s ease}#complaintModalBackdrop.is-open{opacity:1;visibility:visible;pointer-events:auto}#complaintModal{opacity:0;visibility:hidden;pointer-events:none;transform:translate(-50%,-46%) scale(.985);transition:opacity .16s ease,transform .20s cubic-bezier(.22,.61,.36,1),visibility .16s ease}#complaintModal.is-open{opacity:1;visibility:visible;pointer-events:auto;transform:translate(-50%,-50%) scale(1)}.complaint-modal-shell{width:min(720px,calc(100vw - 28px));max-height:min(92vh,860px);overflow:hidden;border:1px solid #dedbd6;border-radius:22px;background:#fff;box-shadow:0 18px 44px rgba(24,22,19,.13),0 44px 100px rgba(24,22,19,.20)}.complaint-modal-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:22px 22px 16px}.complaint-modal-eyebrow{color:#77777c;font-size:.66rem;font-weight:500}.complaint-modal-title{margin-top:5px;color:#252525;font-size:1.3rem;font-weight:700;line-height:1.2;letter-spacing:-.035em}.complaint-modal-close{display:grid;width:36px;height:36px;flex:0 0 36px;place-items:center;border:0;border-radius:10px;background:#f7f7f8;color:#636363}.complaint-modal-close:hover{background:#eeeeef;color:#2d2d2d}.complaint-modal-body{max-height:calc(92vh - 90px);overflow-y:auto;padding:0 22px 22px}.complaint-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.complaint-field-label{margin-bottom:7px;color:#626268;font-size:.7rem;font-weight:500}.complaint-field-value{display:flex;min-height:46px;align-items:center;padding:0 13px;border:1px solid #dcdde1;border-radius:10px;background:#fff;color:#303034;font-size:.76rem;font-weight:500}.complaint-detail-block{margin-top:16px}.complaint-detail-copy{min-height:96px;padding:12px 13px;border:1px solid #dcdde1;border-radius:10px;background:#fff;color:#4d4d52;font-size:.74rem;line-height:1.6;white-space:pre-wrap}.complaint-admin-note{background:#fafafa}.complaint-action-section{margin-top:18px;padding-top:18px;border-top:1px solid #ececef}.complaint-action-title{color:#303034;font-size:.8rem;font-weight:600}.complaint-action-copy{margin-top:3px;color:#85858b;font-size:.66rem;line-height:1.5}.complaint-action-form{display:flex;gap:10px;margin-top:12px}.complaint-action-input{height:44px;flex:1;min-width:0;border:1px solid #dcdde1;border-radius:10px;padding:0 13px;background:#fff;color:#303034;font-size:.73rem}.complaint-action-input:focus{outline:none;border-color:#1683ff;box-shadow:0 0 0 3px rgba(22,131,255,.10)}.complaint-resolve,.complaint-reopen{display:inline-flex;height:44px;align-items:center;justify-content:center;gap:8px;border-radius:10px;padding:0 16px;font-size:.72rem;font-weight:600;box-shadow:none}.complaint-resolve{border:1px solid #cfe2d5;background:#f5faf6;color:#4d7b5c}.complaint-resolve:hover{border-color:#bdd8c5;background:#edf7ef;color:#2f7c48}.complaint-reopen{border:1px solid #e7d6b7;background:#fffaf1;color:#94671f}.complaint-reopen:hover{border-color:#d8bd86;background:#fff4df;color:#b77400}
    @media(max-width:1023px){.complaints-filter-grid{grid-template-columns:minmax(0,1fr) 180px}.complaints-search{grid-column:1/-1}.complaints-table-head{display:none}.complaint-row{grid-template-columns:1fr auto;gap:14px;align-items:start}.complaint-row>div:nth-child(2),.complaint-row>div:nth-child(3),.complaint-row>div:nth-child(4){grid-column:1}.complaint-row>div:last-child{grid-column:2;grid-row:1}}
    @media(max-width:639px){.complaints-header-main{align-items:flex-start;gap:12px}.complaints-filter-grid{grid-template-columns:1fr;gap:9px}.complaints-search{grid-column:auto}.complaint-row{padding:14px 16px}.complaints-footer{align-items:flex-start;flex-direction:column}.complaint-detail-grid{grid-template-columns:1fr}.complaint-action-form{flex-direction:column}.complaint-resolve,.complaint-reopen{width:100%}}

    /* =========================================================
       COMPLAINTS SUMMARY — USER MANAGEMENT SIZE PARITY
       Same desktop card width, height, icon size, and typography
       as the Approved Accounts summary cards.
       ========================================================= */

    .complaints-page .complaints-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .complaints-page .complaint-stat {
        min-height: 110px !important;
        padding: 18px !important;
        border-radius: 18px !important;
    }

    .complaints-page .complaint-stat > div {
        min-height: 72px;
        align-items: center !important;
        gap: 16px !important;
    }

    .complaints-page .complaint-stat-icon {
        width: 48px !important;
        height: 48px !important;
        flex: 0 0 48px !important;
        border-radius: 12px !important;
    }

    .complaints-page .complaint-stat-icon svg {
        width: 20px !important;
        height: 20px !important;
    }

    .complaints-page .complaint-stat-label {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    .complaints-page .complaint-stat-value {
        margin-top: 4px !important;
        font-size: 26px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
    }

    .complaints-page .complaint-stat-helper {
        margin-top: 8px !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
    }

    /* Summary icons: upper-right, same 48x48 size. */
    .complaints-page .complaint-stat {
        position: relative !important;
        padding-right: 82px !important;
    }

    .complaints-page .complaint-stat-icon {
        position: absolute !important;
        top: 18px !important;
        right: 18px !important;
        width: 48px !important;
        height: 48px !important;
        flex: 0 0 48px !important;
    }

    @media (min-width: 1536px) {
        .complaints-page .complaint-stat-label {
            font-size: 12px !important;
        }

        .complaints-page .complaint-stat-value {
            font-size: 27px !important;
        }
    }

    @media (max-width: 1279px) {
        .complaints-page .complaints-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 767px) {
        .complaints-page .complaints-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 479px) {
        .complaints-page .complaints-summary-grid {
            grid-template-columns: 1fr !important;
        }

        .complaints-page .complaint-stat {
            min-height: 110px !important;
            padding: 16px !important;
        }
    }


    /* =========================================================
       COMPLAINTS — NEUTRAL SUMMARY + PREMIUM STATUS DROPDOWN
       ========================================================= */

    .complaints-page .complaint-stat.is-active {
        border-color: #e7ddd1 !important;
        background: #fff !important;
        box-shadow:
            0 3px 7px rgba(61,43,22,.04),
            0 15px 34px rgba(61,43,22,.085),
            0 30px 58px rgba(61,43,22,.038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .complaints-page .complaint-stat.is-active:hover {
        border-color: #d9c9b1 !important;
        background: #fff !important;
    }

    .complaints-status-dropdown {
        position: relative;
        min-width: 0;
    }

    .complaints-status-trigger {
        display: flex;
        width: 100%;
        height: 44px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e8e0d5;
        border-radius: 12px;
        background: #fff;
        padding: 0 14px;
        color: #332c25;
        font-size: 11px;
        font-weight: 500;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.96);
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .complaints-status-trigger:hover {
        border-color: #d8c8b1;
        box-shadow:
            0 2px 5px rgba(61,43,22,.03),
            0 9px 20px rgba(61,43,22,.055),
            inset 0 1px 0 rgba(255,255,255,.96);
    }

    .complaints-status-dropdown.is-open .complaints-status-trigger,
    .complaints-status-trigger:focus-visible {
        outline: none;
        border-color: #d9a33a;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 10px 24px rgba(61,43,22,.07);
    }

    .complaints-status-trigger-main {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 9px;
    }

    .complaints-status-trigger-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 8px;
        border-radius: 999px;
        background: #858078;
    }

    .complaints-status-trigger[data-current-status="open"] .complaints-status-trigger-dot {
        background: #d99500;
    }

    .complaints-status-trigger[data-current-status="resolved"] .complaints-status-trigger-dot {
        background: #3f9a61;
    }

    .complaints-status-trigger-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .complaints-status-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        color: #8b8175;
        transition: transform .16s ease;
    }

    .complaints-status-dropdown.is-open .complaints-status-chevron {
        transform: rotate(180deg);
    }

    .complaints-status-menu {
        position: absolute;
        z-index: 50;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        padding: 6px;
        border: 1px solid #e7dfd4;
        border-radius: 14px;
        background: #fff;
        box-shadow:
            0 8px 18px rgba(47,37,25,.09),
            0 24px 52px rgba(47,37,25,.15);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-5px) scale(.985);
        transform-origin: top;
        transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
    }

    .complaints-status-dropdown.is-open .complaints-status-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .complaints-status-option {
        display: flex;
        width: 100%;
        min-height: 38px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        padding: 0 10px;
        color: #5c534a;
        font-size: 10px;
        font-weight: 500;
        text-align: left;
    }

    .complaints-status-option:hover,
    .complaints-status-option:focus-visible,
    .complaints-status-option.is-selected {
        outline: none;
        background: #fff7e8;
        color: #a8731f;
    }

    .complaints-status-option.is-selected {
        font-weight: 600;
    }

    .complaints-status-option-left {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .complaints-status-option-dot {
        width: 7px;
        height: 7px;
        flex: 0 0 7px;
        border-radius: 999px;
        background: #8e857a;
    }

    .complaints-status-option[data-status-value="open"] .complaints-status-option-dot {
        background: #d99500;
    }

    .complaints-status-option[data-status-value="resolved"] .complaints-status-option-dot {
        background: #3f9a61;
    }

    .complaints-status-check {
        width: 14px;
        height: 14px;
        color: #d99500;
        opacity: 0;
    }

    .complaints-status-option.is-selected .complaints-status-check {
        opacity: 1;
    }


    /* =========================================================
       COMPLAINTS TABLE — SELLER COMPLIANCE STYLE
       One floating outer card; header stays integrated inside.
       ========================================================= */
    .complaints-page .complaints-table-surface {
        overflow: hidden !important;
        border: 1px solid #e7ddd1 !important;
        border-radius: 20px !important;
        background: #fff !important;
        box-shadow:
            0 3px 8px rgba(61,43,22,.045),
            0 18px 42px rgba(61,43,22,.095),
            0 38px 78px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .complaints-page .complaints-table-head {
        position: relative !important;
        z-index: 2 !important;
        margin: 0 !important;
        min-height: 50px !important;
        padding: 13px 20px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee8df !important;
        border-radius: 0 !important;
        background: #fcfbf8 !important;
        color: #847b70 !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1.35 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
        box-shadow: none !important;
    }

    .complaints-page .complaints-table-head > div {
        display: flex;
        min-height: 24px;
        align-items: center;
    }

    .complaints-page .complaints-table-head > div:last-child {
        justify-content: flex-end;
    }

    .complaints-page #complaintRows {
        background: #fff !important;
        border-top: 0 !important;
    }

    .complaints-page .complaint-row {
        min-height: 72px !important;
        padding: 14px 20px !important;
        border-bottom: 1px solid #f0ebe4 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .complaints-page .complaint-row:hover {
        background: #fdfbf7 !important;
    }

    .complaints-page .complaint-row:last-child {
        border-bottom: 0 !important;
    }

    .complaints-page .complaints-footer {
        min-height: 68px !important;
        padding: 14px 20px !important;
        border-top: 1px solid #eee8df !important;
        background: #fff !important;
    }

    /* Keep summary icons upper-right, same card/icon dimensions. */
    .complaints-page .complaint-stat {
        position: relative !important;
        padding-right: 82px !important;
    }

    .complaints-page .complaint-stat-icon {
        position: absolute !important;
        top: 18px !important;
        right: 18px !important;
        width: 48px !important;
        height: 48px !important;
        flex: 0 0 48px !important;
    }

    @media (max-width: 1023px) {
        .complaints-page .complaints-table-head {
            display: none !important;
        }
    }

</style>

@php
    $complaintCount = $complaints->count();
    $statusTone = fn ($status) => strtolower((string) $status) === 'resolved' ? 'complaint-status-resolved' : 'complaint-status-open';
@endphp

<div class="complaints-page mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[9px] text-[#426b50]">{{ session('success') }}</div>
    @endif

    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="complaints-header-main">
            <span class="complaints-header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span>
            <div class="min-w-0"><p class="complaints-eyebrow">Platform Support</p><h2 class="complaints-title"><span class="complaints-title-base">Platform</span> <span class="complaints-title-accent">Complaints</span></h2><p class="complaints-subtitle">Review submitted complaints, inspect reporter details, record resolution notes, and reopen cases when additional action is required.</p></div>
        </div>
    </section>

    <section class="complaints-summary-grid mt-4">
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="all"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#efdfbf] bg-[#fff7e8] text-[#b98112]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span><div><p class="complaint-stat-label">Total Complaints</p><p class="complaint-stat-value">{{ $stats['total'] ?? $complaintCount }}</p><p class="complaint-stat-helper">All submitted complaints</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="open"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#f0dfd0] bg-[#fff5ed] text-[#c26f20]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5"></path><path d="M12 16h.01"></path></svg></span><div><p class="complaint-stat-label">Open Cases</p><p class="complaint-stat-value">{{ $stats['open'] ?? 0 }}</p><p class="complaint-stat-helper">Needs admin resolution</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="resolved"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#d8ebde] bg-[#eef8f1] text-[#298b53]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg></span><div><p class="complaint-stat-label">Resolved</p><p class="complaint-stat-value">{{ $stats['resolved'] ?? 0 }}</p><p class="complaint-stat-helper">Completed complaint cases</p></div></div></button>
    </section>

    <section class="complaints-surface complaints-filter mt-4"><div class="complaints-filter-grid">
        <div class="complaints-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg><input id="complaintSearch" type="search" autocomplete="off" class="complaints-control" placeholder="Search subject, reporter, description, or note..."></div>
        <div class="complaints-status-dropdown" id="complaintStatusDropdown">
            <input id="complaintStatusFilter" type="hidden" value="all">
            <button id="complaintStatusTrigger" type="button" class="complaints-status-trigger" data-current-status="all" aria-haspopup="listbox" aria-expanded="false">
                <span class="complaints-status-trigger-main">
                    <span class="complaints-status-trigger-dot" aria-hidden="true"></span>
                    <span id="complaintStatusLabel" class="complaints-status-trigger-label">All Status</span>
                </span>
                <svg viewBox="0 0 24 24" class="complaints-status-chevron" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg>
            </button>
            <div id="complaintStatusMenu" class="complaints-status-menu" role="listbox" aria-label="Filter complaints by status">
                <button type="button" class="complaints-status-option is-selected" data-status-value="all" role="option" aria-selected="true"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>All Status</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="open" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Open</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="resolved" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Resolved</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
            </div>
        </div>
        <button id="complaintApplyFilter" type="button" class="complaints-apply"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>Apply Filter</button>
        <button id="complaintResetFilter" type="button" class="complaints-reset">Reset</button>
    </div></section>

    <section class="complaints-surface complaints-table-surface mt-4 overflow-hidden">
        <div class="complaints-table-head"><div>Complaint</div><div>Reporter</div><div>Submitted</div><div>Status</div><div class="text-right">Action</div></div>
        <div id="complaintRows">
        @forelse($complaints as $complaint)
            @php
                $complaintStatus = strtolower((string) $complaint->status);
                $reporterRole = strtoupper((string) $complaint->reporter_role);
                $reporter = $complaint->reporter_identifier ?: 'Account';
            @endphp
            <article class="complaint-row" data-complaint-row data-subject="{{ $complaint->subject }}" data-description="{{ $complaint->description }}" data-reporter-role="{{ $reporterRole }}" data-reporter="{{ $reporter }}" data-status="{{ $complaintStatus }}" data-submitted="{{ $complaint->created_at?->format('M d, Y h:i A') }}" data-admin-note="{{ $complaint->admin_note ?? '' }}" data-resolve-url="{{ $complaintStatus === 'open' ? route('admin.complaints.resolve',$complaint) : '' }}" data-reopen-url="{{ $complaintStatus !== 'open' ? route('admin.complaints.reopen',$complaint) : '' }}">
                <div class="min-w-0"><p class="complaint-subject truncate">{{ $complaint->subject }}</p><p class="complaint-preview">{{ $complaint->description }}</p></div>
                <div><p class="complaint-meta-main">{{ $reporter }}</p><p class="complaint-meta-sub">{{ $reporterRole ?: 'ACCOUNT' }}</p></div>
                <div><p class="complaint-meta-main">{{ $complaint->created_at?->format('M d, Y') ?: '—' }}</p><p class="complaint-meta-sub">{{ $complaint->created_at?->format('h:i A') }}</p></div>
                <div><span class="complaint-status {{ $statusTone($complaintStatus) }}">{{ ucfirst($complaintStatus) }}</span></div>
                <div class="flex justify-end"><button type="button" class="complaint-view" data-view-complaint title="View complaint" aria-label="View complaint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg></button></div>
            </article>
        @empty
            <div class="complaints-empty">No complaints submitted.</div>
        @endforelse
        </div>
        <div class="complaints-footer"><p id="complaintResultCount">Showing {{ $complaintCount }} of {{ $complaintCount }} complaints</p><p class="text-[#9a9187]">Select a complaint to review complete details and take action.</p></div>
    </section>
</div>

<div id="complaintModalBackdrop" class="fixed inset-0 z-[90] bg-[#1f1d1a]/45" aria-hidden="true"></div>
<div id="complaintModal" class="fixed left-1/2 top-1/2 z-[100]" role="dialog" aria-modal="true" aria-labelledby="complaintModalTitle">
    <div class="complaint-modal-shell">
        <div class="complaint-modal-header"><div class="min-w-0"><p class="complaint-modal-eyebrow">Complaint details</p><div class="mt-1 flex flex-wrap items-center gap-2"><h3 id="complaintModalTitle" class="complaint-modal-title">Complaint</h3><span id="complaintModalStatus" class="complaint-status complaint-status-open">Open</span></div></div><button id="complaintModalClose" type="button" class="complaint-modal-close" aria-label="Close"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg></button></div>
        <div class="complaint-modal-body">
            <div class="complaint-detail-grid"><div><p class="complaint-field-label">Reporter</p><div id="complaintModalReporter" class="complaint-field-value">—</div></div><div><p class="complaint-field-label">Submitted</p><div id="complaintModalSubmitted" class="complaint-field-value">—</div></div></div>
            <div class="complaint-detail-block"><p class="complaint-field-label">Description</p><div id="complaintModalDescription" class="complaint-detail-copy">—</div></div>
            <div id="complaintModalNoteWrap" class="complaint-detail-block hidden"><p class="complaint-field-label">Admin resolution note</p><div id="complaintModalNote" class="complaint-detail-copy complaint-admin-note">—</div></div>
            <section class="complaint-action-section"><p class="complaint-action-title">Case action</p><p id="complaintActionCopy" class="complaint-action-copy">Record a resolution note before closing this complaint.</p>
                <form id="complaintResolveForm" method="POST" class="complaint-action-form">@csrf<input id="complaintResolutionNote" name="admin_note" required placeholder="Resolution note..." class="complaint-action-input"><button type="submit" class="complaint-resolve">Resolve Complaint</button></form>
                <form id="complaintReopenForm" method="POST" class="mt-3 hidden">@csrf<button type="submit" class="complaint-reopen">Reopen Complaint</button></form>
            </section>
        </div>
    </div>
</div>

<script>
(function(){
    const rows=[...document.querySelectorAll('[data-complaint-row]')],search=document.getElementById('complaintSearch'),statusFilter=document.getElementById('complaintStatusFilter'),resultCount=document.getElementById('complaintResultCount'),summaryCards=[...document.querySelectorAll('[data-summary-status]')];
    const statusDropdown=document.getElementById('complaintStatusDropdown'),statusTrigger=document.getElementById('complaintStatusTrigger'),statusLabel=document.getElementById('complaintStatusLabel'),statusOptions=[...document.querySelectorAll('[data-status-value]')];
    const modal=document.getElementById('complaintModal'),backdrop=document.getElementById('complaintModalBackdrop'),resolveForm=document.getElementById('complaintResolveForm'),reopenForm=document.getElementById('complaintReopenForm');
    const normalize=v=>(v||'').toString().trim().toLowerCase();
    function applyFilters(){const q=normalize(search?.value),status=normalize(statusFilter?.value||'all');let visible=0;rows.forEach(row=>{const haystack=[row.dataset.subject,row.dataset.description,row.dataset.reporterRole,row.dataset.reporter,row.dataset.adminNote].join(' '),show=(status==='all'||normalize(row.dataset.status)===status)&&(!q||normalize(haystack).includes(q));row.hidden=!show;if(show)visible++});if(resultCount)resultCount.textContent=`Showing ${visible} of ${rows.length} complaints`;summaryCards.forEach(card=>card.classList.toggle('is-active',normalize(card.dataset.summaryStatus)===status))}
    function syncStatusDropdown(value){const target=(value||'all').toString().toLowerCase(),active=statusOptions.find(option=>(option.dataset.statusValue||'all').toLowerCase()===target)||statusOptions[0];if(statusFilter)statusFilter.value=active?.dataset.statusValue||'all';if(statusLabel)statusLabel.textContent=active?.querySelector('.complaints-status-option-left span:last-child')?.textContent?.trim()||'All Status';if(statusTrigger)statusTrigger.dataset.currentStatus=active?.dataset.statusValue||'all';statusOptions.forEach(option=>{const selected=option===active;option.classList.toggle('is-selected',selected);option.setAttribute('aria-selected',selected?'true':'false')})}
    function closeStatusDropdown(){statusDropdown?.classList.remove('is-open');statusTrigger?.setAttribute('aria-expanded','false')}
    function resetFilters(){if(search)search.value='';syncStatusDropdown('all');applyFilters()}
    statusTrigger?.addEventListener('click',event=>{event.stopPropagation();const open=!statusDropdown?.classList.contains('is-open');statusDropdown?.classList.toggle('is-open',open);statusTrigger.setAttribute('aria-expanded',open?'true':'false')});
    statusOptions.forEach(option=>option.addEventListener('click',()=>{syncStatusDropdown(option.dataset.statusValue||'all');closeStatusDropdown();applyFilters()}));
    document.addEventListener('click',event=>{if(!event.target.closest('#complaintStatusDropdown'))closeStatusDropdown()});
    search?.addEventListener('input',applyFilters);document.getElementById('complaintApplyFilter')?.addEventListener('click',applyFilters);document.getElementById('complaintResetFilter')?.addEventListener('click',resetFilters);summaryCards.forEach(card=>card.addEventListener('click',()=>{syncStatusDropdown(card.dataset.summaryStatus||'all');applyFilters()}));
    function openModal(row){if(!row)return;const status=normalize(row.dataset.status);document.getElementById('complaintModalTitle').textContent=row.dataset.subject||'Complaint';document.getElementById('complaintModalReporter').textContent=`${row.dataset.reporter||'Account'} · ${row.dataset.reporterRole||'ACCOUNT'}`;document.getElementById('complaintModalSubmitted').textContent=row.dataset.submitted||'—';document.getElementById('complaintModalDescription').textContent=row.dataset.description||'No description provided.';const noteWrap=document.getElementById('complaintModalNoteWrap'),note=document.getElementById('complaintModalNote');if(row.dataset.adminNote){note.textContent=row.dataset.adminNote;noteWrap.classList.remove('hidden')}else{noteWrap.classList.add('hidden')}const badge=document.getElementById('complaintModalStatus');badge.textContent=status==='resolved'?'Resolved':'Open';badge.className='complaint-status '+(status==='resolved'?'complaint-status-resolved':'complaint-status-open');if(status==='open'){resolveForm.action=row.dataset.resolveUrl||'';resolveForm.classList.remove('hidden');reopenForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Record a resolution note before closing this complaint.'}else{reopenForm.action=row.dataset.reopenUrl||'';reopenForm.classList.remove('hidden');resolveForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Reopen this complaint if the case requires additional review.'}modal.classList.add('is-open');backdrop.classList.add('is-open');document.body.style.overflow='hidden'}
    function closeModal(){modal?.classList.remove('is-open');backdrop?.classList.remove('is-open');document.body.style.overflow=''}
    document.querySelectorAll('[data-view-complaint]').forEach(btn=>btn.addEventListener('click',()=>openModal(btn.closest('[data-complaint-row]'))));document.getElementById('complaintModalClose')?.addEventListener('click',closeModal);backdrop?.addEventListener('click',closeModal);document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});syncStatusDropdown(statusFilter?.value||'all');applyFilters();
})();
</script>
@endsection
