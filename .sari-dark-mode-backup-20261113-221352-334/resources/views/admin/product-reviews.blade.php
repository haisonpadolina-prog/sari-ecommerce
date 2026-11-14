@extends('layouts.admin')

@section('title', 'Product Reviews — SARI Admin')
@section('page-title', 'Product Reviews')

@section('content')
@php
    // The existing, unmodified AdminSellerComplianceController loads the authoritative queue.
    $reviewCount = $reviewProducts->count();
    $pendingCount = $reviewProducts->where('moderation_status', 'pending')->count();
    $flaggedCount = $reviewProducts->where('moderation_status', 'flagged')->count();
    $reReviewCount = $reviewProducts->where('requires_re_review', true)->count();
    $highRiskCount = $reviewProducts->filter(fn ($p) => strtolower((string) $p->screening_risk) === 'high')->count();
@endphp
<style>
.pr-page {--pr-border:#e5e7eb;--pr-text:#101828;--pr-muted:#667085;--pr-gold:#d59617;--pr-gray:#f8fafc;max-width:1800px;margin:0 auto;padding-bottom:28px;font-family:'Poppins',sans-serif;color:var(--pr-text)}
.pr-page *{box-sizing:border-box}
.pr-head{display:flex;align-items:end;justify-content:space-between;gap:20px;border-bottom:1px solid #e7e9ec;padding-bottom:17px;margin-bottom:16px}
.pr-eye{font-size:9px;font-weight:700;letter-spacing:.13em;color:#9a6f23;text-transform:uppercase}
.pr-head h1{margin:5px 0 0;font-size:clamp(29px,2.1vw,36px);font-weight:700;letter-spacing:-.045em;line-height:1.02}
.pr-head h1 span{color:var(--pr-gold)}
.pr-head p{color:var(--pr-muted);font-size:11px;margin-top:8px}
.pr-kpis{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:17px}
.pr-kpi{position:relative;min-height:92px;padding:15px 46px 15px 16px;border:1px solid var(--pr-border);border-radius:14px;background:#fff;box-shadow:0 2px 7px rgba(15,23,42,.035),0 12px 28px rgba(15,23,42,.045)}
.pr-kpi small{display:block;color:var(--pr-muted);font-size:10px;font-weight:600}.pr-kpi strong{display:block;font-size:24px;line-height:1.05;margin-top:9px;font-weight:700}
.pr-kpi svg{position:absolute;right:16px;top:16px;width:24px;height:24px;color:#64748b;stroke-width:1.8;fill:none;stroke:currentColor}
.pr-filters{display:grid;grid-template-columns:minmax(230px,1fr) minmax(140px,190px) minmax(140px,180px) auto auto;gap:12px;align-items:end;margin-bottom:14px}
.pr-control{display:block;width:100%;height:42px;border:1px solid var(--pr-border);border-radius:10px;background:#fff;color:#344054;padding:0 13px;font:500 11px 'Poppins',sans-serif;outline:none}
.pr-control:focus{border-color:#98a2b3;box-shadow:0 0 0 3px rgba(148,163,184,.15)}
.pr-control::placeholder{color:#98a2b3}.pr-label{display:block;font-size:10px;color:#667085;font-weight:600;margin-bottom:7px}
.pr-btn{height:42px;border-radius:10px;padding:0 17px;border:1px solid var(--pr-border);background:#fff;color:#344054;font:600 11px 'Poppins',sans-serif;cursor:pointer}
.pr-btn:hover{background:#f3f4f6}.pr-btn-primary{background:var(--pr-gold);color:#fff;border-color:var(--pr-gold)}.pr-btn-primary:hover{background:#b97805}
.pr-surface{border:1px solid var(--pr-border);border-radius:14px;background:#fff;box-shadow:0 2px 5px rgba(15,23,42,.045),0 12px 28px rgba(15,23,42,.055);overflow:hidden}
.pr-surface-head{display:flex;justify-content:space-between;gap:10px;align-items:center;padding:16px 19px;border-bottom:1px solid #eceff2}
.pr-surface-head h2{font-size:14px;font-weight:650;color:#252a32}.pr-surface-head span{font-size:11px;color:#667085}
.pr-table-scroll{overflow-x:auto;scrollbar-width:none}.pr-table-scroll::-webkit-scrollbar{display:none}
.pr-table{width:100%;min-width:970px;border-collapse:collapse}
.pr-table th{padding:14px 17px;text-align:left;font-size:10px;font-weight:650;letter-spacing:.04em;text-transform:uppercase;color:#78818f;background:#f8fafc;border-bottom:1px solid #edf0f3}
.pr-table td{padding:13px 17px;border-bottom:1px solid #f0f2f4;font-size:11px;line-height:1.5;color:#455267;vertical-align:middle}
.pr-table tbody tr:hover td{background:#fafbfc}.pr-table tbody tr:last-child td{border-bottom:0}
.pr-identity{display:flex;gap:11px;align-items:center;min-width:230px}.pr-thumbnail{width:48px;height:48px;flex-shrink:0;overflow:hidden;border:1px solid var(--pr-border);border-radius:9px;background:#f3f4f6}
.pr-thumbnail img{width:100%;height:100%;object-fit:cover}.pr-name{font-size:12px;font-weight:650;color:#263244}.pr-sub{font-size:10px;color:#86909f;margin-top:2px}.pr-badge{display:inline-flex;align-items:center;min-height:24px;border-radius:7px;border:1px solid #e4e7ec;background:#f8fafc;color:#475467;padding:3px 8px;font-size:10px;font-weight:600}
.pr-badge.flagged,.pr-badge.high{color:#b42318;background:#fef3f2;border-color:#fecdca}.pr-badge.pending,.pr-badge.medium{color:#946200;background:#fffbeb;border-color:#fde68a}.pr-badge.low{color:#067647;background:#ecfdf3;border-color:#abefc6}
.pr-view{min-height:34px;padding:0 13px;border:1px solid #d0d5dd;border-radius:8px;background:#fff;color:#344054;font:650 10px 'Poppins',sans-serif;cursor:pointer;white-space:nowrap}.pr-view:hover{background:#f3f4f6}
.pr-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:15px 20px;border-top:1px solid #eef0f3}.pr-pager p{font-size:11px;color:#667085}
.pr-pages{display:flex;gap:6px;align-items:center;flex-wrap:wrap}.pr-pages button{min-width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#344054;font-size:11px;font-weight:600;padding:0 8px}.pr-pages button[aria-current='page']{background:#d59617;color:#fff;border-color:#d59617}.pr-pages button:disabled{opacity:.35;cursor:not-allowed}
.pr-empty{text-align:center;padding:55px 18px;color:#667085;font-size:12px}.pr-empty svg{display:block;width:70px;height:70px;color:#98a2b3;margin:0 auto 14px;stroke-width:1.15}.pr-empty h3{font-size:15px;color:#344054;font-weight:650;margin-bottom:5px}
.pr-dialog[hidden],.pr-warning[hidden]{display:none!important}.pr-dialog,.pr-warning{position:fixed;inset:0;z-index:210;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.46)}
.pr-dialog-box{width:min(980px,100%);max-height:90vh;display:flex;flex-direction:column;overflow:hidden;border-radius:16px;background:#fff;box-shadow:0 28px 75px rgba(15,23,42,.26)}
.pr-dialog-head{flex-shrink:0;display:flex;gap:12px;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #eef0f3}
.pr-dialog-head h2{font-size:16px;font-weight:700;color:#252a32}.pr-dialog-head p{font-size:11px;color:#667085;margin-top:5px}.pr-close{border:1px solid #e5e7eb;border-radius:8px;width:34px;height:34px;background:#fff;font-size:20px;line-height:1;color:#667085;cursor:pointer}
.pr-dialog-body{padding:20px;overflow-y:auto;background:#f8fafc;min-height:0}
.pr-dialog-section{border:1px solid #e5e7eb;border-radius:12px;background:#fff;margin-bottom:14px;overflow:hidden}
.pr-dialog-section h3{font-size:12px;font-weight:700;padding:14px 16px;border-bottom:1px solid #eef0f3}
.pr-dialog-section-body{padding:16px;font-size:11px;line-height:1.65;color:#475467}
.pr-photo-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.pr-photo{display:block;border:1px solid #e5e7eb;border-radius:9px;overflow:hidden;background:#fff;color:#475467;text-decoration:none}.pr-photo img{display:block;height:130px;width:100%;object-fit:contain;background:#f8fafc}.pr-photo span{display:block;padding:8px 10px;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.pr-mini-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.pr-mini{border:1px solid #e5e7eb;border-radius:9px;padding:12px}.pr-mini small{display:block;font-size:10px;color:#667085}.pr-mini strong{display:block;font-size:12px;margin-top:4px;color:#344054}
.pr-data-table{width:100%;border-collapse:collapse}.pr-data-table th,.pr-data-table td{text-align:left;border-bottom:1px solid #eef0f3;padding:9px;font-size:10px}
.pr-dialog-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px;padding:15px 20px;border-top:1px solid #eef0f3;background:#fff}
.pr-action{min-width:112px;height:39px;padding:0 13px;border:1px solid #d0d5dd;border-radius:9px;background:#fff;color:#344054;font:600 11px 'Poppins',sans-serif;cursor:pointer}.pr-action.approve{background:#067647;border-color:#067647;color:#fff}.pr-action.reject{background:#b42318;border-color:#b42318;color:#fff}.pr-action.warning{color:#a14d4d;border-color:#e6b8b4}.pr-action:hover{filter:brightness(.96)}
.pr-warning-box{width:min(510px,100%);background:#fff;border-radius:14px;padding:20px;box-shadow:0 28px 75px rgba(15,23,42,.24)}
.pr-form-label{display:block;font-weight:600;font-size:11px;color:#475467;margin:13px 0 7px}.pr-warning textarea{min-height:100px;padding:12px;resize:vertical}
.pr-modal-open{overflow:hidden}.pr-visually-hidden{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(max-width:1250px){.pr-kpis{grid-template-columns:repeat(3,minmax(0,1fr))}.pr-filters{grid-template-columns:repeat(2,minmax(0,1fr))}.pr-filters .pr-search{grid-column:1/-1}}
@media(max-width:650px){.pr-head{align-items:flex-start}.pr-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.pr-filters{grid-template-columns:1fr}.pr-filters .pr-search{grid-column:auto}.pr-photo-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.pr-mini-grid{grid-template-columns:1fr}.pr-dialog-body{padding:12px}.pr-dialog-head{padding:14px}.pr-dialog-actions{justify-content:stretch}.pr-dialog-actions form,.pr-dialog-actions button{flex:1}.pr-head h1{font-size:26px}}
@media(prefers-reduced-motion:reduce){.pr-page *{scroll-behavior:auto!important;animation:none!important;transition:none!important}}
/* Seller Orders UI reference — main page only; dialogs retain their workflow. */
#sariProductReviews{--pr-gold:#C88912;--pr-text:#2F3742;--pr-muted:#667383;--pr-border:#E5E7EB;max-width:1520px;width:100%;margin:0 auto;padding-bottom:28px}
#sariProductReviews .pr-head{gap:14px 24px;padding:0 0 14px;margin:0 0 20px;border-bottom:1px solid #E9EDF1;align-items:flex-end}
#sariProductReviews .pr-eye{font-size:10.5px;font-weight:800;line-height:1.4;letter-spacing:.14em;color:#A87316}
#sariProductReviews .pr-head h1{margin:6px 0 0!important;font-size:clamp(27px,2.3vw,31px)!important;font-weight:760!important;line-height:1.08!important;letter-spacing:-.04em!important;color:#2E343B!important}
#sariProductReviews .pr-head h1>span{font-size:inherit!important;font-weight:inherit!important;line-height:inherit!important;letter-spacing:inherit!important}
#sariProductReviews .pr-head p{font-size:11.5px;line-height:1.5;margin-top:6px;color:#8793A1}
#sariProductReviews .pr-head-links{display:flex;flex-wrap:wrap;gap:10px 24px;justify-content:flex-end}
#sariProductReviews .pr-head-links a{display:inline-flex;align-items:center;gap:8px;padding:0;border:0;background:transparent;color:#59636E;font-size:11.5px;font-weight:750;line-height:1.35;text-decoration:none;white-space:nowrap}
#sariProductReviews .pr-head-links a:hover{color:#C88912}#sariProductReviews .pr-head-links span{font-size:18px;font-weight:800}
#sariProductReviews .pr-kpis{grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:0}
#sariProductReviews .pr-kpi{min-height:110px;padding:14px 52px 14px 16px;border-radius:18px;border:1px solid #E5E7EB;background:#FFF;box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)}
#sariProductReviews .pr-kpi small{font-size:13px;font-weight:650;line-height:1.4;color:#637081}
#sariProductReviews .pr-kpi strong{margin-top:7px;font-size:clamp(25px,1.9vw,30px);font-weight:650;line-height:1.12;color:#1F2937;font-variant-numeric:tabular-nums}
#sariProductReviews .pr-kpi svg{width:27px;height:27px;right:16px;top:14px;background:transparent;border:0;border-radius:0;box-shadow:none}
#sariProductReviews .pr-filters{grid-template-columns:minmax(0,1fr) minmax(185px,210px) minmax(155px,185px) auto auto;gap:12px;align-items:center;margin:20px 0;padding:0;border:0;background:transparent;box-shadow:none}
#sariProductReviews .pr-filters .pr-label{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap}
#sariProductReviews .pr-search{position:relative}#sariProductReviews .pr-search>svg{position:absolute;left:16px;top:50%;transform:translateY(-50%);width:21px;height:21px;color:#97A3B2;pointer-events:none}
#sariProductReviews .pr-control,#sariProductReviews .pr-btn,#sariProductReviews .pr-dropdown-trigger{height:48px;min-height:48px;border-radius:14px;border:1px solid #DCE2E8;background:#FFF;color:#3F4B59;box-shadow:none;font:600 11.5px Poppins,system-ui,sans-serif;transform:none}
#sariProductReviews #prSearch{padding:0 14px 0 46px}#sariProductReviews .pr-control::placeholder{font-size:11.5px;color:#A0AAB6}
#sariProductReviews .pr-btn{padding:0 18px;font-size:12px;font-weight:700}#sariProductReviews .pr-btn-primary{background:#C88912;border-color:#C88912;color:#FFF}#sariProductReviews .pr-btn-primary:hover{background:#B47B0E;border-color:#B47B0E}#sariProductReviews #prReset{min-width:86px}
#sariProductReviews .pr-control:focus,#sariProductReviews .pr-dropdown-trigger:focus-visible,#sariProductReviews .pr-dropdown-trigger[aria-expanded=true]{border-color:#C88912;box-shadow:0 0 0 1px rgba(200,137,18,.12);outline:none}
#sariProductReviews .pr-dropdown{position:relative;min-width:0}#sariProductReviews .pr-dropdown-trigger{display:flex;width:100%;padding:0 16px;align-items:center;justify-content:space-between;gap:8px;cursor:pointer;text-align:left}#sariProductReviews .pr-dropdown-trigger svg{width:17px;height:17px;flex-shrink:0}
#sariProductReviews .pr-dropdown-menu{position:absolute;top:calc(100% + 8px);left:0;right:0;z-index:50;padding:8px;border:1px solid #E2E8F0;border-radius:14px;background:#FFF;box-shadow:0 16px 32px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.05)}
#sariProductReviews .pr-dropdown-option{display:block;width:100%;min-height:38px;padding:0 12px;border:0;border-radius:10px;background:transparent;text-align:left;color:#475569;font:600 11.5px Poppins,sans-serif;cursor:pointer}#sariProductReviews .pr-dropdown-option:hover,#sariProductReviews .pr-dropdown-option:focus-visible,#sariProductReviews .pr-dropdown-option[aria-selected=true]{background:#F8FAFC;color:#A56D0A}
#sariProductReviews .pr-surface{border-radius:18px;background:#FFF;box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)}
#sariProductReviews .pr-surface-head{padding:21px 23px;gap:20px;border-color:#E9EDF1}#sariProductReviews .pr-surface-head h2{margin:0;font-size:18px;font-weight:780;color:#2F3742}#sariProductReviews .pr-surface-head p{margin:4px 0 0;font-size:11.5px;color:#8B96A4}#sariProductReviews .pr-surface-head>span{font-size:12px;font-weight:600;color:#7B8794}
#sariProductReviews .pr-table-scroll{overflow-x:auto;scrollbar-width:thin}#sariProductReviews .pr-table-scroll::-webkit-scrollbar{display:block;height:5px}#sariProductReviews .pr-table{min-width:1180px;table-layout:fixed}
#sariProductReviews .pr-table th{height:48px;padding:13px 16px;background:#F8FAFC;color:#8591A0;font-size:11px;font-weight:750;line-height:1.4;letter-spacing:.075em;border-bottom:1px solid #E9EDF1}#sariProductReviews .pr-table td{padding:17px 16px;border-bottom:1px solid #EDF0F3;font-size:11.5px;line-height:1.5;color:#667383;overflow-wrap:anywhere}
#sariProductReviews .pr-table th:first-child{width:58px}#sariProductReviews .pr-table th:nth-child(2){width:30%}#sariProductReviews .pr-table th:nth-child(3){width:21%}#sariProductReviews .pr-table th:nth-child(4),#sariProductReviews .pr-table th:nth-child(5){width:110px}#sariProductReviews .pr-table th:nth-child(6){width:100px}#sariProductReviews .pr-table th:nth-child(7){width:130px}#sariProductReviews .pr-table th:last-child{width:70px}
#sariProductReviews .pr-table tbody tr:hover td,#sariProductReviews .pr-table tbody tr.is-selected td{background:#F8FAFC}#sariProductReviews .pr-identity{min-width:0;gap:12px}#sariProductReviews .pr-thumbnail{width:44px;height:44px;flex:0 0 44px;background:#F8FAFC}#sariProductReviews .pr-name{font-size:12.5px;font-weight:750;line-height:1.5;color:#293440}#sariProductReviews .pr-sub{font-size:11px;margin-top:3px;line-height:1.5;color:#8793A1}
#sariProductReviews .pr-badge{min-height:27px;padding:5px 10px;border-radius:999px;font-size:10.5px;font-weight:700;line-height:1.3}#sariProductReviews .pr-badge.pending,#sariProductReviews .pr-badge.medium{background:#F8FAFC;border-color:#E2D3B1;color:#A56D0A}
#sariProductReviews .pr-view{display:grid;place-items:center;width:32px;min-width:32px;height:32px;min-height:32px;padding:0;border-radius:10px;background:#FFF;color:#59636E;border-color:#DCE2E8}#sariProductReviews .pr-view svg{width:17px;height:17px}#sariProductReviews .pr-view:hover{background:#F8FAFC;color:#C88912;border-color:#C88912}
#sariProductReviews .pr-pager{min-height:64px;padding:13px 20px;border-color:#E9EDF1}#sariProductReviews .pr-pager p{font-size:11px;font-weight:550;color:#7B8794}#sariProductReviews .pr-pages{gap:6px}#sariProductReviews .pr-pages button{width:34px;min-width:34px;height:34px;padding:0;border-radius:10px;color:#7B8794;font-size:11.5px}#sariProductReviews .pr-pages button[aria-current=page]{background:#C88912;border-color:#C88912;color:#FFF}#sariProductReviews .pr-pages button:disabled{opacity:.42}
#sariProductReviews .pr-check{display:inline-flex;align-items:center;justify-content:center;min-height:32px;cursor:pointer}#sariProductReviews .pr-check input{appearance:none;width:18px;height:18px;min-width:18px;margin:0;border:1.5px solid #CBD5E1;border-radius:5px;background:#FFF;display:inline-grid;place-items:center;cursor:pointer}#sariProductReviews .pr-check input::after{content:"";width:4px;height:8px;border:solid #FFF;border-width:0 2px 2px 0;transform:translateY(-1px) rotate(45deg);opacity:0}#sariProductReviews .pr-check input:checked,#sariProductReviews .pr-check input:indeterminate{background:#C88912;border-color:#C88912}#sariProductReviews .pr-check input:checked::after{opacity:1}#sariProductReviews .pr-check input:indeterminate::after{opacity:1;width:9px;height:2px;border:0;background:#FFF;transform:none}
#sariProductReviews .pr-selection{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:10px 19px;border-bottom:1px solid #E5E7EB;background:#F8FAFC;font-size:11.5px;font-weight:600;color:#465362}#sariProductReviews .pr-selection>div{display:flex;gap:6px;flex-wrap:wrap}#sariProductReviews .pr-selection button{min-height:32px;padding:0 12px;border:1px solid #DCE2E8;border-radius:9px;background:#FFF;color:#465362;font:650 11px Poppins,sans-serif;cursor:pointer}#sariProductReviews #prReviewSelected{background:#C88912;border-color:#C88912;color:#FFF}
#sariProductReviews [hidden]{display:none!important}#sariProductReviews :focus-visible{outline:2px solid #C88912;outline-offset:3px}#sariProductReviews .pr-empty svg{width:72px;height:72px;color:#9CA3AF;background:transparent;border:0;box-shadow:none}
@media(max-width:1180px){#sariProductReviews .pr-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1100px){#sariProductReviews .pr-filters{grid-template-columns:minmax(0,1fr) 185px 155px}#sariProductReviews .pr-search{grid-column:1/-1}}
@media(max-width:900px){#sariProductReviews .pr-filters{grid-template-columns:repeat(2,minmax(0,1fr))}#sariProductReviews .pr-filters>.pr-search,#sariProductReviews .pr-filters>.pr-dropdown{grid-column:1/-1}#sariProductReviews .pr-filters>.pr-btn{width:100%}}
@media(max-width:767px){#sariProductReviews .pr-head{flex-direction:column;align-items:flex-start;gap:10px}#sariProductReviews .pr-head h1{font-size:25px!important}#sariProductReviews .pr-eye{font-size:9.5px}#sariProductReviews .pr-head-links a{font-size:10.5px}}
@media(max-width:540px){#sariProductReviews .pr-kpis{grid-template-columns:1fr;gap:11px}#sariProductReviews .pr-kpi{min-height:100px}#sariProductReviews .pr-surface-head{padding:17px 16px;flex-direction:column;align-items:flex-start;gap:6px}#sariProductReviews .pr-pager{flex-direction:column;align-items:flex-start}#sariProductReviews .pr-pages{align-self:flex-end}#sariProductReviews .pr-selection{padding:10px 13px}}

</style>

<main class="pr-page" id="sariProductReviews" data-ui-version="orders-parity-v2">
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-xs text-green-800" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-800" role="alert">{{ $errors->first() }}</div>
    @endif
    <header class="pr-head">
        <div><div class="pr-eye">Marketplace Safety</div><h1>Product <span>Reviews</span></h1></div>
        <nav class="pr-head-links" aria-label="Related admin pages"><a href="{{ route('admin.seller-compliance') }}" wire:navigate>Seller Compliance <span aria-hidden="true">&gt;</span></a><a href="{{ route('admin.seller-accounts.control') }}" wire:navigate>Seller Account Control <span aria-hidden="true">&gt;</span></a></nav>
    </header>
    <section class="pr-kpis" aria-label="Product moderation summary">
        @foreach([
            ['label'=>'Total for Review','count'=>$reviewCount,'icon'=>'list'],
            ['label'=>'Pending Products','count'=>$pendingCount,'icon'=>'clock'],
            ['label'=>'Flagged Products','count'=>$flaggedCount,'icon'=>'alert'],
            ['label'=>'Re-review Required','count'=>$reReviewCount,'icon'=>'repeat'],
            ['label'=>'High-risk Products','count'=>$highRiskCount,'icon'=>'shield']
        ] as $card)
            <div class="pr-kpi"><small>{{ $card['label'] }}</small><strong>{{ number_format($card['count']) }}</strong>
                @if($card['icon'] === 'list')<svg viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                @elseif($card['icon'] === 'clock')<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                @elseif($card['icon'] === 'alert')<svg viewBox="0 0 24 24"><path d="m12 3 10 18H2L12 3ZM12 9v5M12 17h.01"/></svg>
                @elseif($card['icon'] === 'repeat')<svg viewBox="0 0 24 24"><path d="M20 7H4l3-3M4 7l3 3M4 17h16l-3-3m3 3-3 3"/></svg>
                @else<svg viewBox="0 0 24 24"><path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3Z"/><path d="M12 8v5M12 16h.01"/></svg>@endif
            </div>
        @endforeach
    </section>
    <form class="pr-filters" id="productReviewFilters" onsubmit="return false;">
        <div class="pr-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><label class="pr-label" for="prSearch">Search products</label><input class="pr-control" id="prSearch" type="search" placeholder="Product, seller, brand, SKU..." autocomplete="off"></div>
        <div><label class="pr-label" for="prStatus">Review status</label><select class="pr-control" id="prStatus"><option value="">All statuses</option><option value="pending">Pending</option><option value="flagged">Flagged</option><option value="rereview">Re-review</option></select></div>
        <div><label class="pr-label" for="prRisk">Risk level</label><select class="pr-control" id="prRisk"><option value="">All risk levels</option><option value="high">High risk</option><option value="medium">Medium risk</option><option value="low">Low risk</option><option value="review">Needs review</option></select></div>
        <button class="pr-btn pr-btn-primary" id="prApply" type="button">Apply Filter</button>
        <button class="pr-btn" id="prReset" type="button">Reset</button>
    </form>
    <section class="pr-surface" aria-label="Products awaiting administrator review">
        <div class="pr-surface-head"><div><h2>Review Queue</h2><p>Pending and flagged listings awaiting your review.</p></div><span id="prQueueCount">{{ number_format($reviewCount) }} records</span></div>
        <div class="pr-selection" id="prSelection" hidden aria-live="polite"><span id="prSelectedCount">0 selected</span><div><button type="button" id="prReviewSelected">Review selected</button><button type="button" id="prOnlySelected" aria-pressed="false">Show selected only</button><button type="button" id="prClearSelected">Clear selection</button></div></div>
        <div class="pr-table-scroll" tabindex="0" aria-label="Product reviews table; scroll horizontally for all columns"><table class="pr-table"><thead><tr><th scope="col"><label class="pr-check"><input type="checkbox" id="prSelectAll" aria-label="Select products on this page"></label></th><th>Product</th><th>Seller</th><th>Status</th><th>Risk</th><th>Price</th><th>Submitted</th><th>Action</th></tr></thead><tbody id="prRows">
            @foreach($reviewProducts as $product)
                @php
                    $status = strtolower((string)($product->moderation_status ?: 'pending'));
                    $risk = strtolower((string)($product->screening_risk ?: 'review'));
                    $sellerName = $product->seller?->store_name ?: ($product->seller?->email ?: 'Unknown seller');
                    $media = $reviewMedia->get($product->id, ['cover_url'=>null,'images'=>[]]);
                    $productVariants = $product->activeVariants ?? collect();
                    $specs = $product->specifications ?? [];
                    if (!is_array($specs)) $specs = json_decode((string)$specs, true) ?: [];
                    $searchText = strtolower(trim($product->name.' '.$sellerName.' '.($product->seller?->email ?? '').' '.($product->brand ?? '').' '.($product->sku ?? '').' '.($product->category ?? '')));
                @endphp
                <tr data-pr-row data-search="{{ $searchText }}" data-status="{{ $status }}" data-risk="{{ $risk }}" data-rereview="{{ $product->requires_re_review ? '1' : '0' }}">
                    <td><label class="pr-check"><input type="checkbox" data-pr-select aria-label="Select {{ $product->name }}"></label></td>
                    <td><div class="pr-identity"><div class="pr-thumbnail">
                        @if($media['cover_url'])<img src="{{ $media['cover_url'] }}" alt="{{ $product->name }} cover" loading="lazy" decoding="async">@else<div class="grid h-full place-items-center text-[10px] text-gray-400">No image</div>@endif
                    </div><div class="min-w-0"><div class="pr-name">{{ $product->name }}</div><div class="pr-sub">{{ $product->category }}{{ $product->brand ? ' · '.$product->brand : '' }}</div></div></div></td>
                    <td><span class="font-semibold">{{ $sellerName }}</span><div class="pr-sub">{{ $product->seller?->email ?: '—' }}</div></td>
                    <td><span class="pr-badge {{ $status }}">{{ $product->requires_re_review ? 'RE-REVIEW' : strtoupper($status) }}</span></td>
                    <td><span class="pr-badge {{ $risk }}">{{ strtoupper($risk) }}</span></td>
                    <td>₱{{ number_format((float)$product->price, 2) }}</td>
                    <td>{{ $product->created_at?->format('M d, Y') ?: '—' }}</td>
                    <td><button type="button" class="pr-view" data-pr-open="prProduct-{{ $product->id }}" aria-label="Review {{ $product->name }}" title="Review product"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></td>
                </tr>
            @endforeach
        </tbody></table></div>
        <div class="pr-empty" id="prNoResults" hidden>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h7"/></svg>
            <h3>No products found</h3><p>Try another search, status, or risk filter.</p>
        </div>
        <div class="pr-pager"><p id="prPagerText"></p><nav class="pr-pages" id="prPagerButtons" aria-label="Product Reviews pagination"></nav></div>
    </section>
</main>

{{-- Dialogs are outside the table. Every record remains accessible on every page. --}}
@foreach($reviewProducts as $product)
    @php
        $media = $reviewMedia->get($product->id, ['cover_url'=>null,'images'=>[]]);
        $sellerName = $product->seller?->store_name ?: ($product->seller?->email ?: 'Unknown seller');
        $variants = $product->activeVariants ?? collect();
        $specs = $product->specifications ?? [];
        if (!is_array($specs)) $specs = json_decode((string)$specs, true) ?: [];
        $signals = $product->ai_signals ?? [];
        if (!is_array($signals)) $signals = json_decode((string)$signals, true) ?: [];
        $matchedTerms = $product->matched_terms ?? [];
        if (!is_array($matchedTerms)) $matchedTerms = json_decode((string)$matchedTerms, true) ?: [];
    @endphp
    <div class="pr-dialog" id="prProduct-{{ $product->id }}" role="dialog" aria-modal="true" aria-labelledby="prTitle-{{ $product->id }}" hidden>
        <div class="pr-dialog-box" tabindex="-1">
            <div class="pr-dialog-head"><div><h2 id="prTitle-{{ $product->id }}">{{ $product->name }}</h2><p>{{ $sellerName }} · {{ $product->category }} · {{ strtoupper((string)$product->moderation_status) }} · {{ strtoupper((string)($product->screening_risk ?: 'review')) }} risk</p></div><button type="button" class="pr-close" data-pr-close aria-label="Close product review">×</button></div>
            <div class="pr-dialog-body">
                <section class="pr-dialog-section"><h3>Listing information</h3><div class="pr-dialog-section-body">
                    <div class="pr-mini-grid"><div class="pr-mini"><small>Price</small><strong>₱{{ number_format((float)$product->price,2) }}</strong></div><div class="pr-mini"><small>Stock</small><strong>{{ number_format((int)$product->stock) }}</strong></div><div class="pr-mini"><small>Variants</small><strong>{{ $variants->count() }}</strong></div></div>
                    @if(($product->seller?->store_status ?: 'open') !== 'open')<p class="mt-3 text-red-700">Seller store is paused. An approved listing may remain hidden from Buyers.</p>@endif
                    @if($product->description)<p class="mt-3 whitespace-pre-line">{{ $product->description }}</p>@endif
                </div></section>
                <section class="pr-dialog-section"><h3>Product images · Cover, gallery, variants &amp; size guide</h3><div class="pr-dialog-section-body">
                    @if(!empty($media['images']))<div class="pr-photo-grid">@foreach($media['images'] as $photo)
                        <a href="{{ $photo['url'] }}" target="_blank" rel="noopener noreferrer" class="pr-photo" title="Open {{ $photo['label'] }} in full resolution"><img data-pr-media="{{ $photo['url'] }}" alt="{{ $photo['label'] }} for {{ $product->name }}" style="object-fit:{{ $photo['fit'] === 'contain' ? 'contain' : 'cover' }}"><span>{{ $photo['label'] }}</span></a>
                    @endforeach</div>@else<p>No uploaded images found. Verify the listing before approving.</p>@endif
                </div></section>
                <section class="pr-dialog-section"><h3>Screening &amp; AI inspector</h3><div class="pr-dialog-section-body">
                    <div class="pr-mini-grid"><div class="pr-mini"><small>Local screening</small><strong>{{ $product->screening_risk ?: 'Needs review' }} · {{ $product->risk_score ?? '—' }}/100</strong></div><div class="pr-mini"><small>AI status</small><strong>{{ str((string)($product->ai_status ?: 'pending'))->replace('_',' ')->title() }}</strong></div><div class="pr-mini"><small>AI decision</small><strong>{{ str((string)($product->ai_decision ?: 'manual review'))->replace('_',' ')->title() }}</strong></div></div>
                    <p class="mt-3"><strong>Local reason:</strong> {{ $product->screening_reason ?: 'No local reason provided.' }}</p>
                    <p class="mt-2"><strong>AI reason:</strong> {{ $product->ai_reason ?: 'AI screening has not completed; human review required.' }}</p>
                    @if(!empty($matchedTerms))<p class="mt-2"><strong>Matched terms:</strong> {{ implode(', ', array_map('strval', $matchedTerms)) }}</p>@endif
                    @if(!empty($signals))<p class="mt-2"><strong>Signals:</strong> {{ implode(', ', array_map('strval', $signals)) }}</p>@endif
                </div></section>
                <section class="pr-dialog-section"><h3>Specifications &amp; variants</h3><div class="pr-dialog-section-body">
                    @if(!empty($specs))<div class="pr-mini-grid">@foreach($specs as $key=>$spec)
                        @php $specName = is_array($spec) ? ($spec['name'] ?? 'Specification') : (is_string($key) ? $key : 'Specification'); $specValue = is_array($spec) ? ($spec['value'] ?? '—') : $spec; @endphp
                        <div class="pr-mini"><small>{{ $specName }}</small><strong>{{ is_scalar($specValue) ? $specValue : json_encode($specValue) }}{{ is_array($spec) && !empty($spec['unit']) ? ' '.$spec['unit'] : '' }}</strong></div>
                    @endforeach</div>@else<p>No additional specifications recorded.</p>@endif
                    @if($variants->isNotEmpty())<div class="mt-3 overflow-x-auto"><table class="pr-data-table"><thead><tr><th>Options</th><th>SKU</th><th>Price</th><th>Stock</th></tr></thead><tbody>@foreach($variants as $variant)
                        @php $optionValues = $variant->option_values ?? []; if(!is_array($optionValues)) $optionValues=json_decode((string)$optionValues,true)?:[]; @endphp
                        <tr><td>{{ collect($optionValues)->map(fn($v,$k) => $k.': '.(is_scalar($v)?$v:json_encode($v)))->implode(' · ') ?: 'Default' }}</td><td>{{ $variant->sku ?: '—' }}</td><td>₱{{ number_format((float)$variant->price,2) }}</td><td>{{ number_format((int)$variant->stock) }}</td></tr>
                    @endforeach</tbody></table></div>@endif
                    @if($product->requires_re_review && $product->latestVersion)
                        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3"><strong>Previous vs current:</strong> {{ $product->latestVersion->name }} → {{ $product->name }}@if(!empty($product->latestVersion->changed_fields))<p class="mt-2">Changed fields: {{ implode(', ', $product->latestVersion->changed_fields) }}</p>@endif</div>
                    @endif
                </div></section>
            </div>
            <div class="pr-dialog-actions">
                <button type="button" class="pr-action warning" data-pr-warn data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-seller-name="{{ $sellerName }}" data-warning-count="{{ (int)($product->seller?->warning_count ?? 0) }}">Issue Warning</button>
                <form method="POST" action="{{ route('admin.compliance.products.reject',$product) }}" onsubmit="return confirm('Reject this product?');">@csrf<input type="hidden" name="reason" value="Product rejected after administrator review."><button class="pr-action reject" type="submit">Reject</button></form>
                <form method="POST" action="{{ route('admin.compliance.products.approve',$product) }}" onsubmit="return confirm('Approve this product for the marketplace?');">@csrf<button class="pr-action approve" type="submit">Approve</button></form>
            </div>
        </div>
    </div>
@endforeach

<div class="pr-warning" id="prWarning" role="dialog" aria-modal="true" aria-labelledby="prWarningTitle" hidden>
    <div class="pr-warning-box"><div class="flex items-center justify-between"><h2 id="prWarningTitle" class="text-base font-semibold">Issue Seller Warning</h2><button type="button" class="pr-close" data-pr-warning-close aria-label="Close warning">×</button></div>
        <p class="mt-2 text-xs text-slate-600" id="prWarningMeta"></p><p class="mt-3 hidden text-xs text-red-700" id="prThirdWarning">This will be warning 3 of 3 and triggers an automatic 30-day suspension.</p>
        <form method="POST" id="prWarningForm">@csrf
            <label class="pr-form-label" for="prWarningReason">Violation reason</label><select class="pr-control" id="prWarningReason" name="reason" required><option value="">Select violation</option><option>Prohibited product listing</option><option>Restricted product listing</option><option>Counterfeit or deceptive listing</option><option>Repeated marketplace policy violation</option><option>Other marketplace compliance violation</option></select>
            <label class="pr-form-label" for="prWarningNote">Admin note</label><textarea class="pr-control" id="prWarningNote" name="admin_note" maxlength="1500" placeholder="Explain the policy violation..."></textarea>
            <div class="mt-5 flex justify-end gap-2"><button class="pr-action" type="button" data-pr-warning-close>Cancel</button><button class="pr-action reject" type="submit" onclick="return confirm('Issue this official seller warning?');">Issue Warning</button></div>
        </form>
    </div>
</div>
<script data-sari-product-reviews-ui="v2">
(function () {
    if (window.__sariProductReviewsInit) { window.__sariProductReviewsInit(); return; }
    function initProductReviews() {
    const root = document.getElementById('sariProductReviews');
    if (!root || root.dataset.uiBound === '1') return;
    window.__sariProductReviewsEvents?.abort();
    const eventController = new AbortController(); window.__sariProductReviewsEvents = eventController;
    root.dataset.uiBound = '1';
    const rows = Array.from(root.querySelectorAll('[data-pr-row]'));
    const search = document.getElementById('prSearch');
    const status = document.getElementById('prStatus');
    const risk = document.getElementById('prRisk');
    const pages = document.getElementById('prPagerButtons');
    const pagerText = document.getElementById('prPagerText');
    const countText = document.getElementById('prQueueCount');
    const empty = document.getElementById('prNoResults');
    const pageSize = 10;
    let page = 1, filtered = rows.slice(), lastFocus = null, onlySelected = false;
    const selected = new Set();
    const selectAll = document.getElementById('prSelectAll');
    function render() {
        const total = filtered.length;
        const maxPage = Math.max(1, Math.ceil(total / pageSize));
        page = Math.max(1, Math.min(page, maxPage));
        rows.forEach(row => { row.hidden = true; });
        filtered.slice((page - 1) * pageSize, page * pageSize).forEach(row => { row.hidden = false; });
        const visible = filtered.slice((page - 1) * pageSize, page * pageSize);
        rows.forEach(row => { row.classList.toggle('is-selected', selected.has(row)); row.querySelector('[data-pr-select]').checked = selected.has(row); });
        const selectedVisible = visible.filter(row => selected.has(row)).length;
        selectAll.checked = visible.length > 0 && selectedVisible === visible.length;
        selectAll.indeterminate = selectedVisible > 0 && selectedVisible < visible.length; selectAll.disabled = visible.length === 0;
        document.getElementById('prSelection').hidden = selected.size === 0;
        document.getElementById('prSelectedCount').textContent = selected.size + ' selected';
        const onlyButton = document.getElementById('prOnlySelected'); onlyButton.textContent = onlySelected ? 'Show all products' : 'Show selected only'; onlyButton.setAttribute('aria-pressed', String(onlySelected));
        empty.hidden = total > 0;
        countText.textContent = total + ' record' + (total === 1 ? '' : 's');
        pagerText.textContent = total ? `Showing ${(page - 1) * pageSize + 1} to ${Math.min(page * pageSize,total)} of ${total} results` : 'Showing 0 results';
        pages.replaceChildren();
        if (maxPage < 2) return;
        function button(label, n, disabled, current) {
            const b = document.createElement('button'); b.type = 'button'; b.textContent = label;
            b.setAttribute('aria-label', label === '‹' ? 'Previous page' : label === '›' ? 'Next page' : 'Page ' + label);
            b.disabled = disabled; if (current) b.setAttribute('aria-current', 'page');
            b.addEventListener('click', () => { page = n; render(); pages.querySelector('[aria-current="page"]')?.focus(); });
            pages.append(b);
        }
        button('‹',page-1,page<=1,false);
        const begin = Math.max(1,Math.min(page-2,maxPage-4));
        for (let n=begin;n<=Math.min(maxPage,begin+4);n++) button(String(n),n,false,n===page);
        button('›',page+1,page>=maxPage,false);
    }
    function apply() {
        const q = search.value.trim().toLowerCase(), s = status.value, r = risk.value;
        filtered = rows.filter(row => (!onlySelected || selected.has(row)) && (!q || row.dataset.search.includes(q)) && (!s || (s==='rereview' ? row.dataset.rereview==='1' : row.dataset.status===s)) && (!r || row.dataset.risk===r));
        page=1;render();
    }
    document.getElementById('prApply')?.addEventListener('click',apply);
    document.getElementById('prReset')?.addEventListener('click',()=>{search.value='';status.value='';risk.value='';apply();});
    search?.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();apply();}});
    search.addEventListener('input', apply); status.addEventListener('change', apply); risk.addEventListener('change', apply);
    rows.forEach(row => row.querySelector('[data-pr-select]').addEventListener('change', event => { event.target.checked ? selected.add(row) : selected.delete(row); if (!selected.size) onlySelected = false; if (onlySelected) apply(); else render(); }));
    selectAll.addEventListener('change', () => { filtered.slice((page - 1) * pageSize, page * pageSize).forEach(row => { selectAll.checked ? selected.add(row) : selected.delete(row); }); if (!selected.size) onlySelected = false; if (onlySelected) apply(); else render(); });
    document.getElementById('prOnlySelected').addEventListener('click', () => { onlySelected = !onlySelected; apply(); });
    document.getElementById('prClearSelected').addEventListener('click', () => { selected.clear(); onlySelected = false; apply(); });
    document.getElementById('prReviewSelected').addEventListener('click', () => rows.find(row => selected.has(row))?.querySelector('[data-pr-open]')?.click());
    function enhanceSelect(select, title) {
        const wrapper = select.parentElement; wrapper.classList.add('pr-dropdown');
        const trigger = document.createElement('button'); trigger.type = 'button'; trigger.className = 'pr-dropdown-trigger'; trigger.setAttribute('aria-label', title); trigger.setAttribute('aria-haspopup', 'listbox'); trigger.setAttribute('aria-expanded', 'false');
        const label = document.createElement('span'); trigger.append(label);
        trigger.insertAdjacentHTML('beforeend', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>');
        const menu = document.createElement('div'); menu.className = 'pr-dropdown-menu'; menu.id = select.id + 'Menu'; menu.role = 'listbox'; menu.setAttribute('aria-label', title); menu.hidden = true; trigger.setAttribute('aria-controls', menu.id);
        function close(focus = false) { menu.hidden = true; trigger.setAttribute('aria-expanded', 'false'); if (focus) trigger.focus(); }
        [...select.options].forEach(option => { const button = document.createElement('button'); button.type = 'button'; button.className = 'pr-dropdown-option'; button.role = 'option'; button.dataset.value = option.value; button.textContent = option.textContent; button.addEventListener('click', () => { select.value = option.value; select.dispatchEvent(new Event('change')); close(true); }); menu.append(button); });
        function sync() { label.textContent = select.selectedOptions[0].textContent; menu.querySelectorAll('button').forEach(button => button.setAttribute('aria-selected', String(button.dataset.value === select.value))); }
        select.addEventListener('change', sync); document.getElementById('prReset').addEventListener('click', sync);
        trigger.addEventListener('click', () => { const opening = menu.hidden; root.querySelectorAll('.pr-dropdown-trigger').forEach(button => button.setAttribute('aria-expanded', 'false')); root.querySelectorAll('.pr-dropdown-menu').forEach(list => list.hidden = true); menu.hidden = !opening; trigger.setAttribute('aria-expanded', String(opening)); if (opening) menu.querySelector('[aria-selected="true"]')?.focus(); });
        wrapper.addEventListener('keydown', event => { if (event.key === 'Escape' && !menu.hidden) { event.preventDefault(); event.stopPropagation(); close(true); } if (event.key === 'Tab') close(); if (['ArrowDown','ArrowUp','Home','End'].includes(event.key)) { event.preventDefault(); menu.hidden = false; trigger.setAttribute('aria-expanded', 'true'); const options = [...menu.querySelectorAll('button')], index = options.indexOf(document.activeElement); options[event.key === 'Home' ? 0 : event.key === 'End' ? options.length - 1 : (index + (event.key === 'ArrowUp' ? -1 : 1) + options.length) % options.length].focus(); } });
        document.addEventListener('click', event => { if (!wrapper.contains(event.target)) close(); }, { signal: eventController.signal });
        select.hidden = true; wrapper.append(trigger, menu); sync();
    }
    enhanceSelect(status, 'Review status'); enhanceSelect(risk, 'Risk level');
    render();
    function loadImages(dialog){ dialog.querySelectorAll('img[data-pr-media]').forEach(img=>{if(!img.src){img.src=img.dataset.prMedia;img.loading='eager';}}); }
    function closeDialogs(){document.querySelectorAll('.pr-dialog,.pr-warning').forEach(d=>d.hidden=true);document.body.classList.remove('pr-modal-open');lastFocus?.focus({preventScroll:true});}
    function openDialog(id, trigger){const d=document.getElementById(id);if(!d)return;lastFocus=trigger;closeDialogs();d.hidden=false;document.body.classList.add('pr-modal-open');loadImages(d);d.querySelector('[data-pr-close]')?.focus({preventScroll:true});}
    document.querySelectorAll('[data-pr-open]').forEach(b=>b.addEventListener('click',()=>openDialog(b.dataset.prOpen,b)));
    document.querySelectorAll('[data-pr-close]').forEach(b=>b.addEventListener('click',closeDialogs));
    document.querySelectorAll('.pr-dialog').forEach(d=>d.addEventListener('click',e=>{if(e.target===d)closeDialogs();}));
    const warning = document.getElementById('prWarning');
    document.querySelectorAll('[data-pr-warn]').forEach(b=>b.addEventListener('click',()=>{
        const form = document.getElementById('prWarningForm');
        // No raw path or untrusted user input is supplied: product ID comes from the server-rendered dataset.
        form.action = @json(url('/admin/seller-compliance/products')) + '/' + encodeURIComponent(b.dataset.productId) + '/warn';
        document.getElementById('prWarningMeta').textContent = b.dataset.sellerName + ' · ' + b.dataset.productName + ' · Current warnings: ' + b.dataset.warningCount + '/3';
        document.getElementById('prThirdWarning').classList.toggle('hidden',Number(b.dataset.warningCount)<2);
        closeDialogs();lastFocus=b;warning.hidden=false;document.body.classList.add('pr-modal-open');document.getElementById('prWarningReason').focus();
    }));
    document.querySelectorAll('[data-pr-warning-close]').forEach(b=>b.addEventListener('click',closeDialogs));
    warning.addEventListener('click',e=>{if(e.target===warning)closeDialogs();});
    document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDialogs();}, { signal: eventController.signal });
    }
    window.__sariProductReviewsInit = initProductReviews;
    document.addEventListener('livewire:navigated', initProductReviews);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initProductReviews, { once: true });
    else initProductReviews();
})();
</script>
@endsection
