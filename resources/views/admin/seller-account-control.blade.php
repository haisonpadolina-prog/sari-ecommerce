@extends('layouts.admin')

@section('title', 'Seller Account Control — SARI Admin')
@section('page-title', 'Seller Account Control')

@section('content')

<style>
/* SARI Seller Account Control — consolidated enterprise UI */
.seller-account-control-page{
    --sac-xs:8px;--sac-sm:9px;--sac-md:10px;--sac-lg:12px;
    --sac-gold:#d99500;--sac-gold-dark:#bd8205;--sac-ink:#26211c;
    --sac-text:#514a42;--sac-muted:#8d8479;--sac-line:#e8e1d8;--sac-soft:#faf9f6;
    width:100%;max-width:1640px!important;margin-inline:auto;padding-bottom:20px;
    color:var(--sac-ink);font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
}
.seller-account-control-page *,.seller-account-control-page *::before,.seller-account-control-page *::after{box-sizing:border-box}
.seller-account-control-page button,.seller-account-control-page a,.seller-account-control-page input,
.seller-account-control-page select,.seller-account-control-page textarea{
    transition:color .15s ease,background-color .15s ease,border-color .15s ease,opacity .15s ease,transform .15s ease;
}

/* Header */
.sac-page-header{margin-bottom:12px!important;gap:12px!important;padding:0!important}
.sac-page-header-main{display:flex;min-width:0;align-items:center;gap:10px!important}
.sac-page-header-icon{display:grid;width:36px!important;height:36px!important;flex:0 0 36px!important;place-items:center;
    border:1px solid #eadfc9!important;border-radius:10px!important;background:#fff8eb!important;color:#b77c18!important;
    box-shadow:0 4px 12px rgba(75,54,25,.045)!important}
.sac-page-header-icon svg{width:15px!important;height:15px!important}
.sac-page-eyebrow{margin:0!important;color:#9a7b43!important;font-size:7px!important;font-weight:700!important;line-height:1.2!important;letter-spacing:.13em!important;text-transform:uppercase!important}
.sac-page-heading{margin:3px 0 0!important;font-size:clamp(22px,1.55vw,27px)!important;font-weight:700!important;line-height:1.08!important;letter-spacing:-.035em!important}
.sac-page-heading-base{color:#17130f!important}.sac-page-heading-accent{color:var(--sac-gold)!important}
.sac-page-header-copy{max-width:800px!important;margin:5px 0 0!important;color:#81786c!important;font-size:9.5px!important;font-weight:400!important;line-height:1.55!important}
.sac-header-back{min-height:34px!important;border:0!important;border-radius:9px!important;padding-inline:12px!important;background:var(--sac-gold)!important;color:#fff!important;font-size:8.5px!important;font-weight:600!important;box-shadow:0 5px 14px rgba(217,149,0,.13)!important}
.sac-header-back:hover{background:var(--sac-gold-dark)!important;transform:translateY(-1px)}

/* Flash */
.seller-account-control-page>.mb-5{margin-bottom:10px!important;border-radius:12px!important;padding:9px 11px!important;box-shadow:none!important}
.seller-account-control-page>.mb-5>span{width:30px!important;height:30px!important;border-radius:8px!important}
.seller-account-control-page>.mb-5 p:first-child{font-size:9px!important}
.seller-account-control-page>.mb-5 p:last-child{margin-top:2px!important;font-size:8px!important;line-height:1.45!important}

/* KPI */
.seller-account-control-page>section.grid{gap:9px!important}
.account-control-summary-card{min-height:76px!important;border:1px solid var(--sac-line)!important;border-radius:13px!important;background:#fff!important;padding:11px 50px 11px 13px!important;box-shadow:0 6px 18px rgba(61,43,22,.045)!important;contain:paint}
.account-control-summary-card:hover{border-color:#ddcfbb!important;transform:translateY(-1px);box-shadow:0 8px 22px rgba(61,43,22,.06)!important}
.account-control-summary-card>div{min-height:52px!important;align-items:center!important}
.sac-summary-label{color:#8e857a!important;font-size:8px!important;font-weight:500!important;line-height:1.3!important}
.sac-summary-value{margin-top:4px!important;color:#28221b!important;font-size:19px!important;font-weight:700!important;line-height:1!important;letter-spacing:-.035em!important}
.account-control-summary-card>div>div:last-child{right:12px!important;top:12px!important;width:32px!important;height:32px!important;border-radius:9px!important;box-shadow:none!important}
.account-control-summary-card>div>div:last-child svg{width:14px!important;height:14px!important}

/* Filters */
.sac-filter-surface{position:relative;z-index:20;margin-top:11px!important;padding:9px!important;overflow:visible;border:1px solid var(--sac-line)!important;border-radius:14px!important;background:#fff!important;box-shadow:0 6px 20px rgba(61,43,22,.045)!important}
.sac-filter-grid{display:grid;grid-template-columns:minmax(320px,1fr) 180px 110px 72px;gap:8px!important;align-items:center}
.sac-filter-search,.sac-filter-select-wrap{position:relative;min-width:0}
.sac-filter-search>svg{position:absolute;z-index:2;top:50%;left:12px;width:14px!important;height:14px!important;pointer-events:none;color:#9b9287;transform:translateY(-50%)}
.sac-filter-input,.sac-filter-select{width:100%!important;min-height:38px!important;height:38px!important;border:1px solid #e5ddd2!important;border-radius:9px!important;background:#fff!important;color:#3d3730!important;font-family:'Poppins',sans-serif!important;font-size:9px!important;line-height:1!important;box-shadow:none!important}
.sac-filter-input{padding:0 10px 0 36px!important;font-weight:400!important}
.sac-filter-select{appearance:none;padding:0 34px 0 31px!important;cursor:pointer;font-weight:500!important}
.sac-filter-input::placeholder{color:#a59c91!important;opacity:1}
.sac-filter-input:hover,.sac-filter-select:hover{border-color:#d4c5b4!important}
.sac-filter-input:focus,.sac-filter-select:focus{outline:none!important;border-color:#d49a2b!important;box-shadow:0 0 0 3px rgba(217,149,0,.075)!important}
.sac-filter-status-dot{position:absolute;z-index:2;top:50%;left:12px;width:6px;height:6px;pointer-events:none;border-radius:999px;background:var(--sac-gold);transform:translateY(-50%)}
.sac-filter-select-chevron{position:absolute;z-index:2;top:50%;right:11px;width:12px!important;height:12px!important;pointer-events:none;color:#8b8175;transform:translateY(-50%)}
.sac-filter-apply,.sac-filter-reset{
    display:inline-flex!important;
    width:100%;
    min-height:38px!important;
    height:38px!important;
    align-items:center!important;
    justify-content:center!important;
    gap:6px!important;
    border-radius:9px!important;
    padding:0 10px!important;
    font-family:'Poppins',sans-serif!important;
    font-size:8.2px!important;
    font-weight:600!important;
    line-height:1!important;
    white-space:nowrap;
}
.sac-filter-apply{
    border:1px solid #d99500!important;
    background:#d99500!important;
    color:#fff!important;
    box-shadow:0 4px 10px rgba(217,149,0,.11)!important;
}
.sac-filter-apply:hover,
.sac-filter-apply:focus-visible{
    outline:none!important;
    border-color:#bd8205!important;
    background:#bd8205!important;
    color:#fff!important;
    transform:translateY(-1px);
    box-shadow:0 5px 12px rgba(217,149,0,.14)!important;
}
.sac-filter-apply svg{
    width:12px!important;
    height:12px!important;
    flex:0 0 12px!important;
    stroke:currentColor!important;
}
.sac-filter-reset{
    border:1px solid #e5ddd2!important;
    background:#fff!important;
    color:#6f665b!important;
    box-shadow:none!important;
}
.sac-filter-reset:hover,
.sac-filter-reset:focus-visible{
    outline:none!important;
    border-color:#d4c5b4!important;
    background:#faf8f4!important;
    color:#514940!important;
}

/* Workspace */
.account-control-workspace{margin-top:10px!important;overflow:hidden!important;border:1px solid var(--sac-line)!important;border-radius:14px!important;background:#fff!important;box-shadow:0 6px 20px rgba(61,43,22,.045)!important}
.sac-workspace-topbar{display:flex;min-height:52px;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px!important;border-bottom:1px solid #eee8df!important;background:#fff!important}
.sac-workspace-title{color:#302a24!important;font-size:10.5px!important;font-weight:700!important;line-height:1.3!important}
.sac-workspace-copy,.sac-result-copy{margin-top:2px!important;color:#91887d!important;font-size:7.5px!important;font-weight:400!important;line-height:1.45!important}
.account-control-workspace>.hidden.border-b{min-height:40px;align-items:center;padding:9px 14px!important;background:#faf9f6!important}
.sac-column-label{color:#81786d!important;font-size:8px!important;font-weight:700!important;line-height:1.3!important;letter-spacing:.055em!important;text-transform:uppercase!important}
#sellerAccountList{padding:0!important;background:#fff!important}
.account-control-row{margin:0!important;border:0!important;border-bottom:1px solid #f0ebe4!important;border-radius:0!important;background:#fff!important;box-shadow:none!important;transform:none!important;content-visibility:auto;contain-intrinsic-size:62px}
.account-control-row:last-of-type{border-bottom:0!important}
.account-control-row:hover{background:#fdfbf8!important;box-shadow:none!important;transform:none!important}
@media(min-width:1280px){
 .account-control-row-grid{display:grid!important;grid-template-columns:minmax(285px,1.85fr) 130px 110px 125px 145px 60px!important;align-items:center!important;column-gap:12px!important}
 .account-control-workspace>.hidden.border-b{grid-template-columns:minmax(285px,1.85fr) 130px 110px 125px 145px 60px!important;gap:12px!important}
 .account-control-row-grid>div{min-height:60px!important}
}
.account-control-row-grid>div{padding-top:9px!important;padding-bottom:9px!important}
.account-control-row-grid>div:first-child{padding-left:14px!important}.account-control-row-grid>div:last-child{padding-right:14px!important}
.account-control-row .sac-modern-avatar{width:32px!important;height:32px!important;flex:0 0 32px!important;border:0!important;border-radius:50%!important;background:#f3f1ed!important;color:#655d55!important;font-size:8px!important;font-weight:700!important;box-shadow:none!important}
.sac-seller-name{color:#2e2924!important;font-size:9px!important;font-weight:700!important;line-height:1.3!important}
.sac-muted{color:#978e83!important;font-size:7.2px!important;font-weight:400!important;line-height:1.4!important}
.sac-badge{padding:4px 7px!important;border-radius:999px!important;font-size:7.5px!important;font-weight:600!important;line-height:1!important}
.sac-data-value{color:#514a42!important;font-size:8px!important;font-weight:500!important;line-height:1.4!important}
[data-seller-control-open]{display:inline-grid!important;width:28px!important;min-width:28px!important;height:28px!important;min-height:28px!important;place-items:center!important;padding:0!important;border:0!important;border-radius:7px!important;background:transparent!important;color:#4d4842!important;box-shadow:none!important}
[data-seller-control-open] svg{width:14px!important;height:14px!important}
[data-seller-control-open]:hover,[data-seller-control-open]:focus-visible{outline:none!important;background:#fff7e8!important;color:var(--sac-gold)!important;transform:translateY(-1px)}
#sellerAccountFilterEmpty{border-top:1px solid #eee8df;padding:14px!important}
#sellerAccountFilterEmpty>div{border-radius:11px!important;padding:28px 16px!important}
#sellerAccountEmptyClear{margin-top:10px!important;border-radius:8px!important;padding:8px 11px!important;font-size:8px!important;font-weight:600!important}

/* Seller modal: only one live DOM instance at a time */
[data-seller-control-modal][hidden]{display:none!important}
[data-seller-control-modal]{background:rgba(28,24,20,.44)!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;padding:12px!important}
@keyframes sacModalIn{from{opacity:0;transform:translateY(8px) scale(.99)}to{opacity:1;transform:translateY(0) scale(1)}}
[data-seller-control-modal]:not([hidden]) .sac-modern-modal{animation:sacModalIn .18s cubic-bezier(.22,1,.36,1) both}
.sac-modern-modal{width:min(720px,calc(100vw - 24px))!important;max-width:720px!important;max-height:min(88vh,760px)!important;overflow:hidden!important;border:1px solid #dfd8cf!important;border-radius:16px!important;background:#fff!important;box-shadow:0 24px 64px rgba(31,24,17,.18),0 8px 22px rgba(31,24,17,.07)!important}
.sac-modern-modal::before{display:none!important;content:none!important}
.sac-modern-modal-header{min-height:58px;align-items:center!important;gap:12px!important;padding:10px 14px!important;border-bottom:1px solid #ebe5dd!important;background:#fff!important;box-shadow:none!important}
.sac-modern-modal-header>div:first-child>div:first-child{display:none!important}
.sac-modern-modal-header>div:first-child{gap:0!important}
.sac-modern-modal-header>div:first-child>div:last-child>p:first-child{margin:0 0 3px!important;color:#9a7b43!important;font-size:6.5px!important;font-weight:700!important;line-height:1.2!important;letter-spacing:.11em!important;text-transform:uppercase}
.sac-modern-modal-title{color:#25221e!important;font-size:14px!important;font-weight:700!important;line-height:1.2!important;letter-spacing:-.025em!important}
.sac-modern-modal-header .sac-muted{margin-top:4px!important;font-size:7.5px!important}
.sac-modern-modal-header .sac-badge{padding:4px 7px!important;font-size:6.5px!important}
.sac-modern-modal-close{width:30px!important;height:30px!important;min-height:30px!important;border:1px solid #e4ddd4!important;border-radius:8px!important;background:#fff!important;color:#71685f!important;box-shadow:none!important}
.sac-modern-modal-close:hover{background:#f7f5f2!important;color:#332d27!important;transform:none!important}
.sac-modern-modal-body{min-height:0;padding:12px 14px 14px!important;background:#f8f7f4!important;scrollbar-width:thin;scrollbar-color:#d0c8be transparent}
.sac-modern-modal-body>.mb-3{margin-bottom:7px!important}.sac-modern-modal-body>.mb-3 h4{color:#39332d!important;font-size:10.5px!important;font-weight:700!important}
.sac-modern-modal-body>.mb-3 p{margin-top:3px!important;color:#91887d!important;font-size:7.5px!important;line-height:1.45!important}
.sac-modern-kpis{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:7px!important;padding:0!important;border:0!important}
.sac-modern-kpi{min-height:54px!important;padding:8px 9px!important;border:1px solid #e5ded5!important;border-radius:9px!important;background:#fff!important;box-shadow:none!important}
.sac-modern-kpi::before,.sac-modern-kpi::after{display:none!important;content:none!important}
.sac-modern-kpi:last-child{grid-column:auto!important}
.sac-modern-kpi-label{display:block;margin:0!important;color:#91887d!important;font-size:6.5px!important;font-weight:500!important;line-height:1.3!important}
.sac-modern-kpi-value{display:block!important;min-height:0!important;margin-top:4px!important;padding:0!important;border:0!important;background:transparent!important;color:#35302b!important;font-size:9px!important;font-weight:700!important;line-height:1.2!important;letter-spacing:0!important;box-shadow:none!important}
.sac-modern-kpi-value.is-warning{color:#a27635!important}.sac-modern-kpi-value.is-danger{color:#a65d5d!important}

/* Violations */
.sac-violations{margin-top:9px!important;overflow:hidden;border:1px solid #e4ddd4!important;border-radius:10px!important;background:#fff!important;box-shadow:none!important}
.sac-violations[open]{box-shadow:none!important}
.sac-violations>summary{display:flex;min-height:44px!important;cursor:pointer;list-style:none;align-items:center;justify-content:space-between;gap:10px!important;padding:8px 10px!important;background:#fff!important;outline:none}
.sac-violations>summary::-webkit-details-marker{display:none}
.sac-violations>summary:hover,.sac-violations[open]>summary{background:#faf9f7!important}
.sac-violation-icon{display:grid;width:28px!important;height:28px!important;flex:0 0 28px!important;place-items:center;border:0!important;border-radius:8px!important;background:#fff8e9!important;color:#ad741b!important;box-shadow:none!important}
.sac-violation-icon svg{width:13px!important;height:13px!important}
.sac-violation-summary-title{color:#39393d!important;font-size:7.5px!important;font-weight:700!important}
.sac-violation-summary-copy{margin-top:2px!important;color:#8c8380!important;font-size:6.5px!important;line-height:1.4!important}
.sac-violation-count{display:inline-flex;min-height:22px!important;align-items:center;justify-content:center;border:1px solid #e2e2e5!important;border-radius:999px;background:#f7f7f8!important;padding:0 7px!important;color:#69696f!important;font-size:6.3px!important;font-weight:600!important;box-shadow:none!important;white-space:nowrap}
.sac-violation-chevron{width:13px!important;height:13px!important;color:#958b80;transition:transform .15s ease}
.sac-violations[open] .sac-violation-chevron{transform:rotate(180deg)}
.sac-violation-list{border-top:1px solid #eee7de!important;background:#fff}
.sac-violation-row{display:grid;grid-template-columns:36px minmax(0,1fr) auto;gap:9px!important;align-items:start;padding:9px 10px!important;content-visibility:auto;contain-intrinsic-size:48px}
.sac-violation-row+.sac-violation-row{border-top:1px solid #f0ebe4}
.sac-violation-number{display:inline-flex;min-height:24px!important;align-items:center;justify-content:center;border:1px solid #ecd9bd;border-radius:7px!important;background:#fff8eb;color:#9d6c21;font-size:6.3px!important;font-weight:700}
.sac-violation-reason{color:#494139;font-size:7px!important;font-weight:600;line-height:1.4}
.sac-violation-product,.sac-violation-date{color:#998f84;font-size:6.3px!important;line-height:1.4}.sac-violation-product{margin-top:2px}
.sac-violation-date{padding-top:1px;text-align:right;white-space:nowrap}
.sac-violation-empty{padding:12px 10px!important;color:#91877c;font-size:7px!important;line-height:1.5;text-align:center}

/* Status + actions */
.sac-modern-status-reason{margin-top:9px!important;padding:9px 10px!important;border-radius:9px!important;box-shadow:none!important}
.sac-modern-status-reason p:first-child{font-size:7px!important}.sac-modern-status-reason p:last-child{margin-top:3px!important;font-size:7px!important;line-height:1.45!important}
.sac-modern-actions{margin-top:10px!important;padding:10px 0 0!important;border:0!important;border-top:1px solid #e6e0d9!important;border-radius:0!important;background:transparent!important;box-shadow:none!important}
.sac-modern-actions-head{margin-bottom:7px!important}.sac-modern-actions-title{color:#39332d!important;font-size:8px!important;font-weight:700!important}
.sac-modern-actions-copy{margin-top:2px!important;color:#91887d!important;font-size:6.5px!important;line-height:1.4!important}
.sac-modern-actions-grid{display:grid!important;gap:7px!important;margin-top:0!important;align-items:stretch}
.sac-modern-actions-grid.is-three{grid-template-columns:repeat(3,minmax(0,1fr))!important}.sac-modern-actions-grid.is-two{grid-template-columns:repeat(2,minmax(0,1fr))!important}.sac-modern-actions-grid.is-one{grid-template-columns:minmax(0,1fr)!important}
.sac-modern-actions-grid>form,.sac-modern-actions-grid>button{display:flex;width:100%;min-width:0;margin:0!important}
.sac-modern-action{display:inline-flex!important;width:100%!important;min-width:0!important;height:35px!important;min-height:35px!important;align-items:center!important;justify-content:center!important;gap:6px!important;padding:0 9px!important;border-radius:8px!important;background:#fff!important;color:#3f3933!important;font-size:7.2px!important;font-weight:600!important;line-height:1.2!important;white-space:nowrap;box-shadow:none!important}
.sac-modern-action svg{width:13px!important;height:13px!important;flex:0 0 13px}
.sac-modern-action:hover,.sac-modern-action:focus-visible{outline:none;transform:translateY(-1px);box-shadow:none!important}
.sac-modern-action.is-suspend{border-color:#ead8b6!important;background:#fffaf0!important;color:#96691f!important}.sac-modern-action.is-suspend:hover{border-color:#dfc38e!important;background:#fff4e0!important}
.sac-modern-action.is-ban{border-color:#ebcccc!important;background:#fff7f7!important;color:#a45d5d!important}.sac-modern-action.is-ban:hover{border-color:#dfb7b7!important;background:#fff0f0!important}
.sac-modern-action.is-deactivate{border-color:#e2dbd2!important;background:#f8f6f3!important;color:#655d54!important}.sac-modern-action.is-deactivate:hover{border-color:#d4c8bc!important;background:#f2efea!important}
.sac-modern-action.is-positive{border-color:#cfe1d5!important;background:#f2f8f4!important;color:#5f836b!important}.sac-modern-action.is-positive:hover{background:#eaf5ed!important}
.sac-modern-note{margin-top:9px!important;padding:8px 0 0!important;border:0!important;border-top:1px solid #e6e0d9!important;border-radius:0!important;background:transparent!important;box-shadow:none!important}
.sac-modern-note svg{width:13px!important;height:13px!important;color:#8d857c!important}.sac-modern-note p{color:#7d756d!important;font-size:6.5px!important;line-height:1.45!important}

/* Confirmation modal */
#sellerAccountActionModal{background:rgba(28,24,20,.44)!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
#sellerAccountActionModal>div{width:min(460px,calc(100vw - 24px))!important;max-width:460px!important;border:1px solid #e4d9d5!important;border-radius:14px!important;padding:16px!important;background:#fff!important;box-shadow:0 22px 56px rgba(31,24,17,.17),0 7px 20px rgba(31,24,17,.06)!important}
#sellerAccountActionModal h3{font-size:14px!important}#sellerAccountActionModal label{font-size:8px!important}
.account-control-input{color:#332e28!important;-webkit-text-fill-color:#332e28!important;background:#fff!important;font-family:'Poppins',sans-serif!important;font-size:9px!important;box-shadow:none!important}
.account-control-input::placeholder{color:#aaa196!important;-webkit-text-fill-color:#aaa196!important}
.account-control-input:focus{outline:none;border-color:#c99128!important;box-shadow:0 0 0 3px rgba(201,145,40,.075)!important}
#sellerAccountActionModal textarea.account-control-input{min-height:82px!important;border-radius:9px!important;padding:10px!important}
#sellerDeleteConfirmInput{height:38px!important;min-height:38px!important;border-radius:9px!important}
#sellerAccountActionCancel,#sellerAccountActionSubmit,#sellerAccountActionClose{min-height:35px!important;border-radius:8px!important;font-size:8px!important;box-shadow:none!important}

/* Responsive */
@media(max-height:850px) and (min-width:900px){
 .sac-page-heading{font-size:22px!important}.account-control-summary-card{min-height:70px!important;padding-top:9px!important;padding-bottom:9px!important}
 .sac-summary-value{font-size:18px!important}.sac-modern-modal{max-height:calc(100vh - 24px)!important}.sac-modern-modal-body{padding-top:10px!important}
}
@media(max-width:1023px){
 .sac-filter-grid{grid-template-columns:minmax(0,1fr) 170px}.sac-filter-search{grid-column:1/-1}.sac-filter-apply,.sac-filter-reset{width:100%}
}
@media(max-width:1279px){
 #sellerAccountList{padding:10px!important}.account-control-row{margin-bottom:9px!important;overflow:hidden;border:1px solid var(--sac-line)!important;border-radius:11px!important}
 .account-control-row:last-of-type{margin-bottom:0!important}.account-control-row-grid>div:first-child{padding-left:12px!important}.account-control-row-grid>div:last-child{padding-right:12px!important}
}
@media(max-width:639px){
 .seller-account-control-page{padding-bottom:14px}.sac-page-header-main{align-items:flex-start}.sac-page-header-icon{width:34px!important;height:34px!important;flex-basis:34px!important}
 .sac-page-heading{font-size:22px!important}.sac-page-header-copy{font-size:9px!important}.sac-header-back{width:100%;min-height:40px!important;font-size:9px!important}
 .seller-account-control-page>section.grid{grid-template-columns:1fr 1fr!important}.account-control-summary-card{min-height:74px!important}
 .sac-filter-grid{grid-template-columns:1fr;gap:7px!important}.sac-filter-search{grid-column:auto}
 .sac-filter-input,.sac-filter-select,.sac-filter-apply,.sac-filter-reset{min-height:42px!important;height:42px!important;font-size:9.5px!important}
.sac-filter-apply,.sac-filter-reset{justify-content:center!important}
 .sac-workspace-topbar{align-items:flex-start;flex-direction:column}
 [data-seller-control-modal]{padding:7px!important}.sac-modern-modal{width:calc(100vw - 14px)!important;max-height:calc(100vh - 14px)!important;border-radius:13px!important}
 .sac-modern-modal-header{padding:10px 11px!important}.sac-modern-modal-body{padding:9px!important}
 .sac-modern-kpis{grid-template-columns:repeat(2,minmax(0,1fr))!important}.sac-modern-kpi:last-child{grid-column:1/-1!important}
 .sac-violation-row{grid-template-columns:34px minmax(0,1fr)}.sac-violation-date{grid-column:2;text-align:left;white-space:normal}
 .sac-modern-actions-grid.is-three,.sac-modern-actions-grid.is-two,.sac-modern-actions-grid.is-one{grid-template-columns:1fr!important}
}
@media(prefers-reduced-motion:reduce){
 .seller-account-control-page *,[data-seller-control-modal],.sac-modern-modal{scroll-behavior:auto!important;animation:none!important;transition:none!important;transform:none!important}
}

    /* ============================================================
       SELLER ACCOUNT CONTROL — HEADER SCALE MATCH
       Matches Seller Compliance / Platform Settings / Commissions.
       Visual-only; seller account logic remains untouched.
       ============================================================ */

    .seller-account-control-page .sac-page-header{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:20px !important;
        margin-bottom:16px !important;
        padding:0 !important;
    }

    .seller-account-control-page .sac-page-header-main{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .seller-account-control-page .sac-page-header-icon{
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        border-radius:12px !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .seller-account-control-page .sac-page-header-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .seller-account-control-page .sac-page-eyebrow{
        color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
    }

    .seller-account-control-page .sac-page-heading{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .seller-account-control-page .sac-page-heading-base{
        color:#17130f !important;
    }

    .seller-account-control-page .sac-page-heading-accent{
        color:#d99500 !important;
    }

    .seller-account-control-page .sac-page-header-copy{
        max-width:860px !important;
        margin-top:7px !important;
        color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
    }

    .seller-account-control-page .sac-header-back{
        min-height:42px !important;
        height:42px !important;
        gap:7px !important;
        border-radius:10px !important;
        padding:0 13px !important;
        font-size:8.5px !important;
        box-shadow:0 4px 10px rgba(217,149,0,.11) !important;
    }

    .seller-account-control-page .sac-header-back svg{
        width:13px !important;
        height:13px !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .seller-account-control-page .sac-page-header{
            margin-bottom:14px !important;
        }

        .seller-account-control-page .sac-page-header-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .seller-account-control-page .sac-page-heading{
            font-size:27px !important;
        }

        .seller-account-control-page .sac-page-header-copy{
            font-size:10.5px !important;
        }

        .seller-account-control-page .sac-header-back{
            min-height:40px !important;
            height:40px !important;
        }
    }

    @media(max-width:639px){
        .seller-account-control-page .sac-page-header{
            align-items:flex-start !important;
            gap:12px !important;
        }

        .seller-account-control-page .sac-page-header-main{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .seller-account-control-page .sac-page-header-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .seller-account-control-page .sac-page-heading{
            font-size:24px !important;
        }

        .seller-account-control-page .sac-page-header-copy{
            font-size:10px !important;
        }

        .seller-account-control-page .sac-header-back{
            width:100% !important;
            min-height:40px !important;
            height:40px !important;
            font-size:9px !important;
        }
    }

</style>

<div class="seller-account-control-page mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        FLASH MESSAGES
    ========================================================== --}}
    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#cfe2d5] bg-[#f5faf6] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,.03)] sm:px-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#dce9e1] bg-white text-[#56816a]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 12 3 3 7-7"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[var(--sac-sm)] font-semibold text-[#5f836b]">Action completed</p>
                <p class="mt-1 text-[var(--sac-xs)] leading-5 text-[#607969]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#ead0d0] bg-[#fff7f7] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,.03)] sm:px-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#efdddd] bg-white text-[#a65f5f]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[var(--sac-sm)] font-semibold text-[#a36a6a]">Action required</p>
                <p class="mt-1 text-[var(--sac-xs)] leading-5 text-[#8d6262]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif


    {{-- =========================================================
        PAGE HEADER — SARI MASTER ADMIN STYLE
    ========================================================== --}}
    <section class="sac-page-header flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="sac-page-header-main">
            <span class="sac-page-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="3"></circle>
                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                    <path d="M18 7h4"></path>
                    <path d="M20 5v4"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="sac-page-eyebrow">Marketplace Safety</p>

                <h2 class="sac-page-heading">
                    <span class="sac-page-heading-base">Seller Account</span>
                    <span class="sac-page-heading-accent">Control</span>
                </h2>

                <p class="sac-page-header-copy">
                    Manage seller account access, review warning status, and apply suspension, ban, deactivation, or restoration actions from one control workspace.
                </p>
            </div>
        </div>

        <a
            href="{{ route('admin.seller-compliance') }}"
            wire:navigate
            class="sac-header-back inline-flex items-center justify-center gap-2 self-start border transition"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
            Seller Compliance
        </a>
    </section>

    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    @php
        $sellerControlNow = now();

        $sellerWarningHistoryBySeller = isset($recentWarnings)
            ? collect($recentWarnings)->groupBy(function ($warning) {
                return (string) (
                    $warning->seller?->id
                    ?? $warning->seller_account_id
                    ?? $warning->seller_id
                    ?? ''
                );
            })
            : collect();

        $summaryCards = [
            ['label' => 'Total Sellers', 'value' => $stats['total'], 'tone' => 'border-[#dfe7ec] bg-[#f4f7f9] text-[#657f94]', 'icon' => 'users'],
            ['label' => 'Active', 'value' => $stats['active'], 'tone' => 'border-[#d7e7dd] bg-[#f3f8f5] text-[#56816a]', 'icon' => 'active'],
            ['label' => 'Suspended', 'value' => $stats['suspended'], 'tone' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]', 'icon' => 'suspended'],
            ['label' => 'Banned', 'value' => $stats['banned'], 'tone' => 'border-[#ecdada] bg-[#fff3f3] text-[#a65d5d]', 'icon' => 'banned'],
            ['label' => 'Deactivated', 'value' => $stats['deactivated'], 'tone' => 'border-[#e4e0dc] bg-[#f6f4f2] text-[#746d64]', 'icon' => 'deactivated'],
        ];
    @endphp

    <section class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
        @foreach($summaryCards as $card)
            <article class="account-control-summary-card relative rounded-[17px] border border-[#ebe4da] bg-white p-3.5 pr-20 sm:p-4 sm:pr-20">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="sac-summary-label text-[var(--sac-sm)]">{{ $card['label'] }}</p>
                        <p class="sac-summary-value mt-2 text-[clamp(1.40rem,1.27rem+.30vw,1.72rem)]">{{ $card['value'] }}</p>
                    </div>

                    <div class="absolute right-4 top-4 grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[11px] border {{ $card['tone'] }} sm:right-[18px] sm:top-[18px]">
                        @if($card['icon'] === 'users')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            </svg>
                        @elseif($card['icon'] === 'active')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                            </svg>
                        @elseif($card['icon'] === 'suspended')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M10 8v8"></path>
                                <path d="M14 8v8"></path>
                            </svg>
                        @elseif($card['icon'] === 'banned')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 8 8 8"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h16"></path>
                                <path d="M6 7v12h12V7"></path>
                                <path d="M9 11h6"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    {{-- =========================================================
        USER MANAGEMENT-STYLE FILTER BAR
    ========================================================== --}}
    <section class="sac-filter-surface">
        <div class="sac-filter-grid">
            <div class="sac-filter-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.4-3.4"></path>
                </svg>

                <input
                    id="sellerAccountSearch"
                    type="search"
                    autocomplete="off"
                    placeholder="Search seller name or email..."
                    class="sac-filter-input"
                >
            </div>

            <div class="sac-filter-select-wrap">
                <span class="sac-filter-status-dot" aria-hidden="true"></span>

                <select
                    id="sellerAccountStatusFilter"
                    class="sac-filter-select"
                    aria-label="Filter seller accounts by status"
                >
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="banned">Banned</option>
                    <option value="deactivated">Deactivated</option>
                </select>

                <svg viewBox="0 0 24 24" class="sac-filter-select-chevron" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m7 10 5 5 5-5"></path>
                </svg>
            </div>

            <button
                id="sellerAccountApplyFilters"
                type="button"
                class="sac-filter-apply"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="M4 6h16"></path>
                    <path d="M7 12h10"></path>
                    <path d="M10 18h4"></path>
                </svg>
                Apply Filter
            </button>

            <button
                id="sellerAccountClearFilters"
                type="button"
                class="sac-filter-reset"
            >
                Reset
            </button>
        </div>
    </section>

    {{-- =========================================================
        SELLER ACCOUNT WORKSPACE
    ========================================================== --}}
    <section class="account-control-workspace sac-workspace-separated overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">

        <div class="sac-workspace-topbar">
            <div>
                <h3 class="sac-workspace-title tracking-[-.015em]">Seller Accounts</h3>
                <p class="sac-workspace-copy mt-1">
                    Review seller account status and open controls only when action is required.
                </p>
            </div>

            <p id="sellerAccountResultCount" class="sac-result-copy shrink-0">
                Showing {{ $sellers->count() }} seller{{ $sellers->count() === 1 ? '' : 's' }}
            </p>
        </div>


        {{-- DESKTOP COLUMN LABELS --}}
        <div class="hidden border-b border-[#eee8df] bg-[#fcfbf9] px-5 py-2.5 xl:grid xl:grid-cols-[minmax(270px,1.55fr)_125px_125px_145px_165px_110px] xl:gap-4 sm:px-6">
            <p class="sac-column-label text-[var(--sac-xs)]">Seller</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Status</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Warnings</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Account</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Suspension</p>
            <p class="sac-column-label text-right text-[var(--sac-xs)]">Action</p>
        </div>


        {{-- SELLER LIST --}}
        <div id="sellerAccountList" class="space-y-3 p-4 sm:p-5">
            @forelse($sellers as $seller)
                @php
                    $status = $seller->account_status ?: 'active';

                    $until = $seller->suspended_until
                        ? \Illuminate\Support\Carbon::parse($seller->suspended_until)
                        : null;

                    $isSuspended =
                        $status === 'active'
                        && (
                            ((int) ($seller->warning_count ?? 0)) >= 3
                            || ($until && $sellerControlNow->lt($until))
                        );

                    $displayStatus = match (true) {
                        $status === 'banned' => 'BANNED',
                        $status === 'deactivated' => 'DEACTIVATED',
                        $isSuspended => 'SUSPENDED',
                        default => 'ACTIVE',
                    };

                    $filterStatus = strtolower($displayStatus);

                    $tone = match($displayStatus) {
                        'ACTIVE' => 'border-[#d4e5da] bg-[#f3f8f5] text-[#56816a]',
                        'SUSPENDED' => 'border-[#ecd9d1] bg-[#fff6f2] text-[#a86858]',
                        'BANNED' => 'border-[#e9cece] bg-[#fff3f3] text-[#a65353]',
                        default => 'border-[#dfdcd7] bg-[#f5f4f2] text-[#716a63]',
                    };

                    $warningCount = (int) ($seller->warning_count ?? 0);

                    $warningTone = match (true) {
                        $warningCount >= 3 => 'border-[#ecd2d2] bg-[#fff4f4] text-[#a65353]',
                        $warningCount >= 2 => 'border-[#ead9c8] bg-[#fff7ef] text-[#a86b38]',
                        $warningCount >= 1 => 'border-[#eee0c5] bg-[#fff9ef] text-[#a8731f]',
                        default => 'border-[#dfe5e2] bg-[#f6f8f7] text-[#687a70]',
                    };

                    $sellerName = $seller->store_name ?: 'SARI Seller';
                    $sellerInitials = strtoupper(substr(trim($sellerName), 0, 2));
                    $searchText = strtolower(trim($sellerName . ' ' . $seller->email));
                    $modalId = 'sellerControlModal-' . $seller->id;

                    /*
                     * Warning details are indexed once above. Relationship fallback
                     * is only used when already eager-loaded, preventing N+1 queries.
                     */
                    $sellerViolationHistory = $sellerWarningHistoryBySeller
                        ->get((string) $seller->id, collect())
                        ->values();

                    if ($sellerViolationHistory->isEmpty()) {
                        foreach (['warnings', 'complianceWarnings', 'sellerWarnings'] as $warningRelation) {
                            if (
                                method_exists($seller, $warningRelation)
                                && method_exists($seller, 'relationLoaded')
                                && $seller->relationLoaded($warningRelation)
                            ) {
                                $sellerViolationHistory = collect($seller->getRelation($warningRelation))
                                    ->sortByDesc(function ($warning) {
                                        return $warning->issued_at
                                            ?? $warning->created_at
                                            ?? null;
                                    })
                                    ->values();

                                break;
                            }
                        }
                    }

                    $sellerViolationCount = $sellerViolationHistory->count();
                @endphp

                <article
                    data-seller-account-row
                    data-seller-account-search="{{ $searchText }}"
                    data-seller-account-status="{{ $filterStatus }}"
                    class="account-control-row rounded-[18px] border border-[#ebe4da] bg-white"
                >
                    <div class="account-control-row-grid">

                        {{-- SELLER --}}
                        <div class="flex min-w-0 items-center gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0">
                            <div class="sac-modern-avatar grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#3a342d] text-[var(--sac-xs)] font-bold text-white">
                                {{ $sellerInitials }}
                            </div>

                            <div class="min-w-0">
                                <p class="sac-seller-name truncate text-[var(--sac-md)]">{{ $sellerName }}</p>
                                <p class="sac-muted mt-1 truncate text-[var(--sac-xs)]">{{ $seller->email }}</p>
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Status</span>
                            <span class="sac-badge rounded-full border px-2.5 py-1.5 text-[var(--sac-xs)] {{ $tone }}">{{ $displayStatus }}</span>
                        </div>

                        {{-- WARNINGS --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Warnings</span>
                            <span class="sac-badge rounded-full border px-2.5 py-1.5 text-[var(--sac-xs)] {{ $warningTone }}">
                                {{ $warningCount }} / 3
                            </span>
                        </div>

                        {{-- ACCOUNT --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Account</span>
                            <span class="sac-data-value text-[var(--sac-sm)]">{{ ucfirst($status) }}</span>
                        </div>

                        {{-- SUSPENSION --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Suspension</span>

                            <div class="text-right xl:text-left">
                                <p class="sac-data-value text-[var(--sac-sm)]">
                                    {{ $until ? $until->format('M d, Y') : '—' }}
                                </p>

                                @if($until)
                                    <p class="sac-muted mt-0.5 text-[var(--sac-xs)]">
                                        {{ $until->isFuture() ? $until->diffForHumans() : 'Expired' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex items-center justify-between px-4 py-4 xl:justify-end xl:px-0 xl:pr-4">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Action</span>

                            <button
                                type="button"
                                data-seller-control-open="{{ $modalId }}"
                                title="Manage seller"
                                aria-label="Manage seller"
                                class="seller-control-view-icon"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>


                {{-- =====================================================
                    SELLER MANAGEMENT MODAL
                ====================================================== --}}
                <template data-seller-control-template="{{ $modalId }}">
                <div
                    id="{{ $modalId }}"
                    data-seller-control-modal
                    hidden
                    class="fixed inset-0 z-[190] items-center justify-center bg-black/40 p-4"
                >
                    <div class="sac-modern-modal flex max-h-[90vh] w-full max-w-[760px] flex-col overflow-hidden rounded-[24px] border border-[#e8dfd3] bg-white shadow-[0_30px_90px_rgba(37,29,19,.22)]">

                        {{-- MODAL HEADER --}}
                        <div class="sac-modern-modal-header flex shrink-0 items-start justify-between gap-4 border-b border-[#eee8df] px-5 py-5 sm:px-6">
                            <div class="flex min-w-0 items-start gap-3.5">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#3a342d] text-[var(--sac-xs)] font-bold text-white">
                                    {{ $sellerInitials }}
                                </div>

                                <div class="min-w-0">
                                    <p class="mb-1 text-[10px] font-medium text-[#77777c]">Seller account</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="sac-modern-modal-title sac-modal-title text-[var(--sac-lg)]">{{ $sellerName }}</h3>
                                        <span class="sac-badge rounded-full border px-2.5 py-1 text-[var(--sac-xs)] {{ $tone }}">{{ $displayStatus }}</span>
                                    </div>

                                    <p class="sac-muted mt-1 text-[var(--sac-xs)]">{{ $seller->email }}</p>
                                </div>
                            </div>

                            <button
                                type="button"
                                data-seller-control-close
                                class="sac-modern-modal-close grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
                                aria-label="Close seller account controls"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m7 7 10 10"></path>
                                    <path d="m17 7-10 10"></path>
                                </svg>
                            </button>
                        </div>


                        {{-- MODAL BODY --}}
                        <div class="sac-modern-modal-body min-h-0 flex-1 overflow-y-auto bg-[#fcfbf9] p-5 sm:p-6">

                            {{-- SNAPSHOT --}}
                            <div class="mb-3">
                                <h4 class="text-[12px] font-semibold text-[#38383c]">Account details</h4>
                                <p class="mt-1 text-[10px] leading-4 text-[#85858a]">Review the seller's current account state before applying an action.</p>
                            </div>
                            <div class="sac-modern-kpis grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="sac-modern-kpi rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                    <p class="sac-modern-kpi-label sac-muted text-[var(--sac-xs)]">Warnings</p>
                                    <p class="sac-modern-kpi-value sac-modal-value mt-1.5 text-[var(--sac-md)] {{ $warningCount >= 2 ? 'is-danger text-[#a86b67]' : 'is-warning text-[#a27635]' }}">
                                        {{ $warningCount }} / 3
                                    </p>
                                </div>

                                <div class="sac-modern-kpi rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                    <p class="sac-modern-kpi-label sac-muted text-[var(--sac-xs)]">Account</p>
                                    <p class="sac-modern-kpi-value sac-modal-value mt-1.5 text-[var(--sac-md)]">{{ ucfirst($status) }}</p>
                                </div>

                                <div class="sac-modern-kpi col-span-2 rounded-[14px] border border-[#e8e2d9] bg-white p-3.5 sm:col-span-1">
                                    <p class="sac-modern-kpi-label sac-muted text-[var(--sac-xs)]">Suspended Until</p>
                                    <p class="sac-modern-kpi-value sac-modal-value mt-1.5 text-[var(--sac-md)]">
                                        {{ $until ? $until->format('M d, Y') : '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- VIOLATION / WARNING HISTORY --}}
                            <details class="sac-violations">
                                <summary>
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="sac-violation-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M12 3 4.5 6v5.3c0 4.5 2.9 7.6 7.5 9.7 4.6-2.1 7.5-5.2 7.5-9.7V6L12 3Z"></path>
                                                <path d="M12 8v4"></path>
                                                <path d="M12 15.5h.01"></path>
                                            </svg>
                                        </span>

                                        <span class="min-w-0">
                                            <span class="sac-violation-summary-title block">See violations</span>
                                            <span class="sac-violation-summary-copy block">
                                                Review recorded seller warnings and the policy reason behind each one.
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        <span class="sac-violation-count">
                                            {{ $sellerViolationCount }} record{{ $sellerViolationCount === 1 ? '' : 's' }}
                                        </span>

                                        <svg viewBox="0 0 24 24" class="sac-violation-chevron" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="m7 10 5 5 5-5"></path>
                                        </svg>
                                    </div>
                                </summary>

                                <div class="sac-violation-list">
                                    @forelse($sellerViolationHistory as $violation)
                                        @php
                                            $violationNumber = (int) ($violation->warning_number ?? 0);
                                            $violationReason = $violation->reason
                                                ?? $violation->violation_reason
                                                ?? 'Marketplace policy violation';

                                            $violationProduct = $violation->product?->name
                                                ?? $violation->product_name
                                                ?? null;

                                            $violationIssuedAt = $violation->issued_at
                                                ?? $violation->created_at
                                                ?? null;
                                        @endphp

                                        <div class="sac-violation-row">
                                            <span class="sac-violation-number">
                                                {{ $violationNumber > 0 ? $violationNumber . '/3' : '!' }}
                                            </span>

                                            <div class="min-w-0">
                                                <p class="sac-violation-reason">{{ $violationReason }}</p>

                                                @if($violationProduct)
                                                    <p class="sac-violation-product">
                                                        Related listing: {{ $violationProduct }}
                                                    </p>
                                                @endif
                                            </div>

                                            <p class="sac-violation-date">
                                                {{ $violationIssuedAt ? \Illuminate\Support\Carbon::parse($violationIssuedAt)->format('M d, Y · h:i A') : 'Date unavailable' }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="sac-violation-empty">
                                            @if($warningCount > 0)
                                                This seller has {{ $warningCount }} active warning{{ $warningCount === 1 ? '' : 's' }}, but detailed violation records are not loaded in this view.
                                            @else
                                                No recorded seller violations or warnings yet.
                                            @endif
                                        </div>
                                    @endforelse
                                </div>
                            </details>


                            {{-- STATUS REASON --}}
                            @if($status === 'banned' && $seller->ban_reason)
                                <div class="sac-modern-status-reason mt-4 rounded-[14px] border border-[#ead3d3] bg-[#fff7f7] p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#9d5f5f]">Ban reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#8f6262]">{{ $seller->ban_reason }}</p>
                                </div>
                            @elseif($status === 'deactivated' && $seller->deactivation_reason)
                                <div class="sac-modern-status-reason mt-4 rounded-[14px] border border-[#e1ddd8] bg-white p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#746e67]">Deactivation reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#746e67]">{{ $seller->deactivation_reason }}</p>
                                </div>
                            @elseif($isSuspended && $seller->suspension_reason)
                                <div class="sac-modern-status-reason mt-4 rounded-[14px] border border-[#ead9d4] bg-[#fff9f7] p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#8d665e]">Suspension reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#876a64]">{{ $seller->suspension_reason }}</p>
                                </div>
                            @endif


                            {{-- AVAILABLE ACTIONS --}}
                            <section class="sac-modern-actions mt-4 rounded-[16px] border border-[#e8e2d9] bg-white p-4">
                                <div class="sac-modern-actions-head">
                                    <div>
                                        <p class="sac-modern-actions-title sac-modal-section-title text-[var(--sac-md)]">Available Actions</p>
                                        <p class="sac-modern-actions-copy sac-muted mt-1 text-[var(--sac-xs)] leading-5">
                                            Only actions valid for the seller's current account state are shown.
                                        </p>
                                    </div>
                                </div>

                                <div class="sac-modern-actions-grid {{ $status === 'active' ? 'is-three' : ($status === 'banned' ? 'is-two' : 'is-one') }}">
                                    @if($status === 'active')

                                        @if($isSuspended)
                                            <form method="POST" action="{{ route('admin.compliance.sellers.unsuspend', $seller) }}">
                                                @csrf
                                                <button class="sac-modern-action is-positive inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                        <path d="M3 3v6h6"></path>
                                                    </svg>
                                                    Lift Suspension
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.compliance.sellers.suspend30', $seller) }}">
                                                @csrf
                                                <input type="hidden" name="reason" value="Manual 30-day suspension issued from Seller Account Control.">

                                                <button class="sac-modern-action is-suspend inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#ead8d2] bg-[#fff7f3] px-3 text-[var(--sac-sm)] font-semibold text-[#a96f62] transition hover:bg-[#fff1eb]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <circle cx="12" cy="12" r="8"></circle>
                                                        <path d="M10 9v6"></path>
                                                        <path d="M14 9v6"></path>
                                                    </svg>
                                                    Suspend 30 Days
                                                </button>
                                            </form>
                                        @endif

                                        <button
                                            type="button"
                                            data-account-action="ban"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.ban', $seller) }}"
                                            class="sac-modern-action is-ban inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e6caca] bg-[#fff5f5] px-3 text-[var(--sac-sm)] font-semibold text-[#a86464] transition hover:bg-[#ffeded]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <circle cx="12" cy="12" r="8"></circle>
                                                <path d="m8.5 8.5 7 7"></path>
                                            </svg>
                                            Ban Seller
                                        </button>

                                        <button
                                            type="button"
                                            data-account-action="deactivate"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.deactivate', $seller) }}"
                                            class="sac-modern-action is-deactivate inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e0d8d0] bg-[#f7f5f2] px-3 text-[var(--sac-sm)] font-semibold text-[#71675d] transition hover:bg-[#f0ede8]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="M4 7h16"></path>
                                                <path d="M9 7V4h6v3"></path>
                                                <path d="M7 7l1 13h8l1-13"></path>
                                            </svg>
                                            Deactivate Account
                                        </button>

                                    @elseif($status === 'banned')

                                        <form method="POST" action="{{ route('admin.seller-accounts.unban', $seller) }}">
                                            @csrf
                                            <button class="sac-modern-action is-positive inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                    <path d="M3 3v6h6"></path>
                                                </svg>
                                                Unban Seller
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            data-account-action="deactivate"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.deactivate', $seller) }}"
                                            class="sac-modern-action is-deactivate inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e0d8d0] bg-[#f7f5f2] px-3 text-[var(--sac-sm)] font-semibold text-[#71675d] transition hover:bg-[#f0ede8]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="M4 7h16"></path>
                                                <path d="M9 7V4h6v3"></path>
                                                <path d="M7 7l1 13h8l1-13"></path>
                                            </svg>
                                            Deactivate Account
                                        </button>

                                    @else

                                        <form method="POST" action="{{ route('admin.seller-accounts.restore', $seller) }}">
                                            @csrf
                                            <button class="sac-modern-action is-positive inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                    <path d="M3 3v6h6"></path>
                                                </svg>
                                                Restore Account
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </section>


                            {{-- PRESERVATION NOTE --}}
                            <div class="sac-modern-note mt-4 flex items-start gap-3 rounded-[14px] border border-[#e5ddd1] bg-[#fffaf3] p-4">
                                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 8v4"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>

                                <p class="text-[var(--sac-xs)] leading-5 text-[#88775f]">
                                    Deactivation blocks seller access but preserves compliance evidence, moderation history, and account records.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                </template>

            @empty
                <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl border border-[#ebe4da] bg-white text-[#91887d]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="3"></circle>
                            <path d="M5 20a7 7 0 0 1 14 0"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[var(--sac-md)] font-bold text-[#514a42]">No seller accounts found</p>
                    <p class="mt-1 text-[var(--sac-xs)] text-[#91887d]">Seller accounts will appear here when available.</p>
                </div>
            @endforelse
        </div>


        {{-- FILTER EMPTY --}}
        <div id="sellerAccountFilterEmpty" class="hidden p-5">
            <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                <p class="text-[var(--sac-md)] font-bold text-[#514a42]">No matching sellers</p>
                <p class="mt-1 text-[var(--sac-xs)] text-[#91887d]">Try another seller name or account status.</p>

                <button
                    id="sellerAccountEmptyClear"
                    type="button"
                    class="mt-4 rounded-xl border border-[#e2d8c8] bg-white px-4 py-2.5 text-[var(--sac-sm)] font-bold text-[#9a6817] transition hover:bg-[#fffaf2]"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </section>
</div>


{{-- =============================================================
    BAN / DEACTIVATION CONFIRMATION MODAL
============================================================== --}}
<div
    id="sellerAccountActionModal"
    class="fixed inset-0 z-[220] hidden items-center justify-center bg-black/45 p-4"
>
    <div class="w-full max-w-[520px] rounded-[24px] border border-[#ead8d1] bg-white p-5 shadow-[0_35px_100px_rgba(38,30,18,.24)] sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-[var(--sac-xs)] font-bold uppercase tracking-[.10em] text-[#a8731f]">Account Action</p>
                <h3 id="sellerAccountActionTitle" class="sac-modal-title mt-1.5 text-[var(--sac-lg)]">Confirm Action</h3>
                <p id="sellerAccountActionDescription" class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#81786c]"></p>
            </div>

            <button
                type="button"
                id="sellerAccountActionClose"
                class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e4ddd3] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
                aria-label="Close"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 7 10 10"></path>
                    <path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        <form id="sellerAccountActionForm" method="POST" action="" class="mt-5">
            @csrf

            <label class="text-[var(--sac-sm)] font-semibold text-[#514a41]">Reason *</label>

            <textarea
                name="reason"
                rows="4"
                required
                class="account-control-input mt-2 w-full resize-none rounded-xl border border-[#e4ddd3] px-4 py-3"
                placeholder="Enter the administrator reason..."
            ></textarea>

            <div id="sellerDeleteConfirmWrap" class="mt-4 hidden">
                <label class="text-[var(--sac-sm)] font-semibold text-[#8d5151]">Type DELETE to confirm *</label>

                <input
                    id="sellerDeleteConfirmInput"
                    name="confirmation"
                    type="text"
                    class="account-control-input mt-2 h-11 w-full rounded-xl border border-[#e4caca] bg-[#fffafa] px-4"
                    placeholder="DELETE"
                >
            </div>

            <div class="mt-5 flex justify-end gap-2 border-t border-[#eee8df] pt-4">
                <button
                    type="button"
                    id="sellerAccountActionCancel"
                    class="h-10 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[var(--sac-sm)] font-semibold text-[#62594e]"
                >
                    Cancel
                </button>

                <button
                    id="sellerAccountActionSubmit"
                    type="submit"
                    class="h-10 rounded-xl bg-[#a95656] px-5 text-[var(--sac-sm)] font-semibold text-white transition hover:bg-[#984a4a]"
                >
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
(function () {
    /*
    |--------------------------------------------------------------------------
    | SEARCH + STATUS FILTER
    |--------------------------------------------------------------------------
    */
    const searchInput = document.getElementById('sellerAccountSearch');
    const statusFilter = document.getElementById('sellerAccountStatusFilter');
    const sellerRows = Array.from(document.querySelectorAll('[data-seller-account-row]'));
    const sellerList = document.getElementById('sellerAccountList');
    const filterEmpty = document.getElementById('sellerAccountFilterEmpty');
    const resultCount = document.getElementById('sellerAccountResultCount');
    const applyFiltersButton = document.getElementById('sellerAccountApplyFilters');
    const clearFiltersButton = document.getElementById('sellerAccountClearFilters');
    const emptyClearButton = document.getElementById('sellerAccountEmptyClear');

    const sellerSearchIndex = sellerRows.map(function (row) {
        return {
            row,
            searchable: (row.dataset.sellerAccountSearch || '').toLowerCase(),
            status: (row.dataset.sellerAccountStatus || '').toLowerCase(),
        };
    });

    function debounce(callback, wait = 130) {
        let timer = 0;
        return function (...args) {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback.apply(this, args), wait);
        };
    }

    let sellerFilterFrame = 0;

    function applySellerFilters() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const status = (statusFilter?.value || '').trim().toLowerCase();

        window.cancelAnimationFrame(sellerFilterFrame);

        sellerFilterFrame = window.requestAnimationFrame(function () {
            let visible = 0;

            sellerSearchIndex.forEach(function (item) {
                const matchesQuery = query === '' || item.searchable.includes(query);
                const matchesStatus = status === '' || item.status === status;
                const matches = matchesQuery && matchesStatus;
                const shouldHide = !matches;

                if (item.row.classList.contains('hidden') !== shouldHide) {
                    item.row.classList.toggle('hidden', shouldHide);
                }

                if (matches) visible++;
            });

            if (resultCount) {
                resultCount.textContent =
                    'Showing ' + visible +
                    ' of ' + sellerSearchIndex.length +
                    ' seller' + (visible === 1 ? '' : 's');
            }

            if (sellerSearchIndex.length > 0) {
                sellerList?.classList.toggle('hidden', visible === 0);
                filterEmpty?.classList.toggle('hidden', visible !== 0);
            }
        });
    }

    const debouncedSellerFilters = debounce(applySellerFilters, 130);

    function clearSellerFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        applySellerFilters();
        searchInput?.focus();
    }

    searchInput?.addEventListener('input', debouncedSellerFilters, { passive: true });
    statusFilter?.addEventListener('change', applySellerFilters);
    applyFiltersButton?.addEventListener('click', applySellerFilters);
    clearFiltersButton?.addEventListener('click', clearSellerFilters);
    emptyClearButton?.addEventListener('click', clearSellerFilters);

    searchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            applySellerFilters();
        }
    });

    applySellerFilters();


    /*
    |--------------------------------------------------------------------------
    | SELLER MANAGEMENT MODALS
    |--------------------------------------------------------------------------
    */
    const sellerControlTemplates = new Map(
        Array.from(document.querySelectorAll('[data-seller-control-template]')).map(function (template) {
            return [template.dataset.sellerControlTemplate, template];
        })
    );

    let activeSellerControlModal = null;

    function closeSellerControlModals() {
        if (activeSellerControlModal) {
            activeSellerControlModal.remove();
            activeSellerControlModal = null;
        }

        document.querySelectorAll('body > [data-seller-control-modal]').forEach(function (modal) {
            modal.remove();
        });

        document.body.classList.remove('overflow-hidden');
    }

    function openSellerControlModal(modalId) {
        const template = sellerControlTemplates.get(String(modalId || ''));
        if (!template) return;

        closeSellerControlModals();

        const fragment = template.content.cloneNode(true);
        const modal = fragment.querySelector('[data-seller-control-modal]');
        if (!modal) return;

        modal.hidden = false;
        modal.classList.add('flex');
        document.body.appendChild(fragment);

        activeSellerControlModal = document.getElementById(modalId);
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(function () {
            activeSellerControlModal
                ?.querySelector('[data-seller-control-close]')
                ?.focus({ preventScroll: true });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | BAN / DEACTIVATE CONFIRMATION MODAL
    |--------------------------------------------------------------------------
    */
    const actionModal = document.getElementById('sellerAccountActionModal');
    const actionForm = document.getElementById('sellerAccountActionForm');
    const actionTitle = document.getElementById('sellerAccountActionTitle');
    const actionDescription = document.getElementById('sellerAccountActionDescription');
    const confirmWrap = document.getElementById('sellerDeleteConfirmWrap');
    const confirmInput = document.getElementById('sellerDeleteConfirmInput');

    function closeActionModal() {
        actionModal?.classList.add('hidden');
        actionModal?.classList.remove('flex');
        actionForm?.reset();
        confirmWrap?.classList.add('hidden');

        if (confirmInput) {
            confirmInput.required = false;
        }

        document.body.classList.remove('overflow-hidden');
    }

    function openAccountActionModal(button) {
        if (!button || !actionModal || !actionForm || !actionTitle || !actionDescription || !confirmWrap) {
            return;
        }

        const action = button.dataset.accountAction;
        const seller = button.dataset.seller;

        actionForm.action = button.dataset.url;
        const deactivating = action === 'deactivate';

        actionTitle.textContent =
            deactivating
                ? 'Deactivate Seller Account?'
                : 'Ban Seller Account?';

        actionDescription.textContent =
            deactivating
                ? `${seller} will be blocked from seller access while compliance and moderation history remain preserved.`
                : `${seller} will be blocked from seller login until an administrator removes the ban.`;

        confirmWrap.classList.toggle('hidden', !deactivating);

        if (confirmInput) {
            confirmInput.required = deactivating;
        }

        closeSellerControlModals();
        actionModal.classList.remove('hidden');
        actionModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(function () {
            actionModal.querySelector('textarea[name="reason"]')?.focus({ preventScroll: true });
        });
    }

    document.addEventListener('click', function (event) {
        const openButton = event.target.closest('[data-seller-control-open]');
        if (openButton) {
            openSellerControlModal(openButton.dataset.sellerControlOpen);
            return;
        }

        if (event.target.closest('[data-seller-control-close]')) {
            closeSellerControlModals();
            return;
        }

        const sellerModal = event.target.closest('[data-seller-control-modal]');
        if (sellerModal && event.target === sellerModal) {
            closeSellerControlModals();
            return;
        }

        const actionButton = event.target.closest('[data-account-action]');
        if (actionButton) {
            openAccountActionModal(actionButton);
            return;
        }

        if (
            event.target.closest('#sellerAccountActionCancel')
            || event.target.closest('#sellerAccountActionClose')
        ) {
            closeActionModal();
            return;
        }

        if (actionModal && event.target === actionModal) {
            closeActionModal();
        }
    });


    /*
    |--------------------------------------------------------------------------
    | ESCAPE
    |--------------------------------------------------------------------------
    */
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        if (
            actionModal &&
            !actionModal.classList.contains('hidden')
        ) {
            closeActionModal();
            return;
        }

        closeSellerControlModals();
    });
})();
</script>
@endpush
