@extends('layouts.admin')

@section('title','Account — SARI Admin')

@section('page-title','Account Management')

@push('styles')

<style>

    .sari-account-page{font-family:'Poppins',sans-serif;color:#26211d;}

    .sari-account-page input:-webkit-autofill,

    .sari-account-page input:-webkit-autofill:hover,

    .sari-account-page input:-webkit-autofill:focus{

        -webkit-box-shadow:0 0 0 1000px #fff inset !important;

        -webkit-text-fill-color:#2f2924 !important;

    }

    .sari-account-page .account-card{box-shadow:0 8px 26px rgba(55,45,32,.035);}

    .sari-account-page .field-input:focus{border-color:#d6b363;box-shadow:0 0 0 4px rgba(201,145,40,.07);outline:none;}

    /* ============================================================

       SELLER VISUAL SYSTEM — FINAL ADMIN ACCOUNT OVERRIDE

       Presentation only. Profile/password routes and JS stay intact.

       ============================================================ */

    .sari-account-page{

        --account-canvas:#F4F5F7;

        --account-surface:#FFFFFF;

        --account-surface-soft:#F9FAFB;

        --account-text:#111827;

        --account-text-secondary:#344054;

        --account-muted:#7C8592;

        --account-gold:#D59617;

        --account-gold-strong:#B97805;

        --account-border:#E5E7EB;

        --account-border-strong:#D1D5DB;

        --account-divider:#ECEFF2;

        --account-radius:14px;

        --account-shadow-main:

            0 2px 5px rgba(15,23,42,.045),

            0 10px 24px rgba(15,23,42,.075),

            0 24px 54px rgba(15,23,42,.095),

            inset 0 1px 0 rgba(255,255,255,.92);

        --account-shadow-soft:

            0 1px 3px rgba(15,23,42,.035),

            0 7px 18px rgba(15,23,42,.06),

            0 15px 32px rgba(15,23,42,.065),

            inset 0 1px 0 rgba(255,255,255,.90);

        --account-shadow-focal:

            0 3px 7px rgba(15,23,42,.055),

            0 14px 32px rgba(15,23,42,.095),

            0 30px 68px rgba(15,23,42,.11);

        width:100% !important;

        max-width:1500px !important;

        margin-inline:auto !important;

        padding-bottom:24px !important;

        color:var(--account-text) !important;

        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif !important;

    }

    .sari-account-page *,

    .sari-account-page *::before,

    .sari-account-page *::after{

        box-sizing:border-box;

    }

    /* Open document-style header */

    .sari-account-page > .mb-5{

        min-height:66px !important;

        margin-bottom:14px !important;

        padding:2px 2px 13px !important;

        border-bottom:1px solid var(--account-divider) !important;

        align-items:flex-end !important;

    }

    .sari-account-page > .mb-5 > div:first-child{

        align-items:flex-start !important;

        gap:0 !important;

    }

    .sari-account-page > .mb-5 > div:first-child > div:first-child{

        display:none !important;

    }

    .sari-account-page > .mb-5 p:first-child{

        margin:0 !important;

        color:var(--account-gold-strong) !important;

        font-size:8px !important;

        font-weight:800 !important;

        line-height:1.15 !important;

        letter-spacing:.16em !important;

        text-transform:uppercase !important;

    }

    .sari-account-page > .mb-5 h1{

        margin-top:5px !important;

        color:var(--account-text) !important;

        font-size:clamp(29px,2.2vw,36px) !important;

        font-weight:700 !important;

        line-height:1 !important;

        letter-spacing:-.035em !important;

    }

    .sari-account-page > .mb-5 h1 span{

        color:var(--account-gold) !important;

    }

    .sari-account-page > .mb-5 h1 + p{

        max-width:760px !important;

        margin-top:7px !important;

        color:var(--account-muted) !important;

        font-size:10px !important;

        font-weight:400 !important;

        line-height:1.55 !important;

    }

    /* Alerts */

    .sari-account-page > .mb-4{

        margin-bottom:14px !important;

        border-radius:14px !important;

        border-color:#D7E7DB !important;

        background:#F5FAF6 !important;

        padding:10px 12px !important;

        color:#4F725A !important;

        font-size:9px !important;

        box-shadow:var(--account-shadow-soft) !important;

    }

    .sari-account-page > .mb-4 > span:first-child{

        width:26px !important;

        height:26px !important;

        background:#fff !important;

        box-shadow:none !important;

    }

    /* Main profile/access workspace */

    .sari-account-page > .grid{

        grid-template-columns:minmax(0,1.55fr) minmax(300px,.72fr) !important;

        gap:14px !important;

        align-items:stretch !important;

    }

    .sari-account-page .account-card{

        border:1px solid var(--account-border) !important;

        border-radius:var(--account-radius) !important;

        background:var(--account-surface) !important;

        box-shadow:var(--account-shadow-main) !important;

    }

    .sari-account-page > .grid > .account-card{

        min-height:100% !important;

        padding:18px !important;

    }

    .sari-account-page > .grid > aside.account-card{

        background:var(--account-surface) !important;

        box-shadow:var(--account-shadow-soft) !important;

    }

    /* Profile identity header */

    .sari-account-page > .grid > section.account-card > div:first-child{

        gap:14px !important;

        padding-bottom:15px !important;

        border-bottom:1px solid var(--account-divider) !important;

    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:first-child{

        gap:11px !important;

    }

    .sari-account-page > .grid > section.account-card > div:first-child .h-12.w-12{

        width:42px !important;

        height:42px !important;

        flex-basis:42px !important;

        border:1px solid #DDE6E2 !important;

        background:#F1F5F3 !important;

        color:#60756E !important;

        font-size:10px !important;

        box-shadow:none !important;

    }

    .sari-account-page > .grid > section.account-card h2{

        color:var(--account-text) !important;

        font-size:14px !important;

        font-weight:700 !important;

        letter-spacing:-.02em !important;

    }

    .sari-account-page > .grid > section.account-card h2 + div{

        margin-top:5px !important;

        gap:6px !important;

    }

    .sari-account-page > .grid > section.account-card h2 + div > span{

        height:21px !important;

        min-height:21px !important;

        padding-inline:7px !important;

        border-radius:999px !important;

        font-size:6.5px !important;

        box-shadow:none !important;

    }

    /* Account ID stays a flat nested tile */

    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child{

        border-color:var(--account-border) !important;

        border-radius:10px !important;

        background:var(--account-surface-soft) !important;

        padding:8px 10px !important;

        box-shadow:none !important;

    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child p:first-child{

        color:var(--account-muted) !important;

        font-size:6.5px !important;

        letter-spacing:.12em !important;

    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child p:last-child{

        margin-top:3px !important;

        color:var(--account-text-secondary) !important;

        font-size:9px !important;

    }

    /* Section headings */

    .sari-account-page .account-card p.font-bold.uppercase{

        color:#8B6B31 !important;

        font-size:8px !important;

        font-weight:800 !important;

        letter-spacing:.14em !important;

    }

    .sari-account-page .account-card p.font-bold.uppercase + p{

        color:var(--account-muted) !important;

        font-size:9px !important;

        line-height:1.5 !important;

    }

    /* Profile form */

    .sari-account-page > .grid > section.account-card > .mt-5{

        margin-top:16px !important;

    }

    .sari-account-page > .grid > section.account-card form{

        gap:13px !important;

    }

    .sari-account-page .field-input{

        height:40px !important;

        margin-top:6px !important;

        border:1px solid var(--account-border-strong) !important;

        border-radius:10px !important;

        background:#fff !important;

        padding-inline:12px !important;

        color:var(--account-text-secondary) !important;

        font-size:10px !important;

        font-weight:500 !important;

        box-shadow:none !important;

        outline:none !important;

        transition:border-color .14s ease,box-shadow .14s ease,background-color .14s ease !important;

    }

    .sari-account-page .field-input:hover{

        border-color:#C7CCD3 !important;

    }

    .sari-account-page .field-input:focus{

        border-color:var(--account-gold) !important;

        background:#fff !important;

        box-shadow:0 0 0 3px rgba(213,150,23,.10) !important;

    }

    .sari-account-page label > span:first-child{

        color:var(--account-text-secondary) !important;

        font-size:9px !important;

        font-weight:600 !important;

    }

    .sari-account-page form button[type="submit"]{

        min-height:40px !important;

        height:40px !important;

        border-radius:10px !important;

        padding-inline:14px !important;

        font-size:9px !important;

        font-weight:700 !important;

        transform:none !important;

    }

    .sari-account-page > .grid > section.account-card form button[type="submit"]{

        border:1px solid var(--account-gold) !important;

        background:var(--account-gold) !important;

        color:#fff !important;

        box-shadow:0 4px 10px rgba(185,120,5,.12) !important;

    }

    .sari-account-page > .grid > section.account-card form button[type="submit"]:hover{

        border-color:var(--account-gold-strong) !important;

        background:var(--account-gold-strong) !important;

    }

    /* Access panel */

    .sari-account-page > .grid > aside.account-card > div:first-child{

        align-items:flex-start !important;

    }

    .sari-account-page > .grid > aside.account-card > div:first-child > span{

        min-height:22px !important;

        height:22px !important;

        padding-inline:8px !important;

        border-radius:999px !important;

        font-size:6.5px !important;

    }

    .sari-account-page > .grid > aside.account-card dl{

        margin-top:16px !important;

    }

    .sari-account-page > .grid > aside.account-card dl > div{

        min-height:36px !important;

        padding-bottom:10px !important;

        border-color:var(--account-divider) !important;

    }

    .sari-account-page > .grid > aside.account-card dl > div:last-child{

        min-height:auto !important;

        padding-bottom:0 !important;

    }

    .sari-account-page > .grid > aside.account-card dt{

        color:var(--account-muted) !important;

        font-size:8px !important;

    }

    .sari-account-page > .grid > aside.account-card dd{

        color:var(--account-text-secondary) !important;

        font-size:8.5px !important;

        font-weight:650 !important;

    }

    /* Security */

    .sari-account-page > section.account-card{

        margin-top:14px !important;

        padding:18px !important;

    }

    .sari-account-page > section.account-card > div:first-child{

        gap:12px !important;

    }

    .sari-account-page > section.account-card > div:first-child > div:first-child > div:first-child{

        gap:7px !important;

    }

    .sari-account-page > section.account-card > div:first-child > div:last-child.rounded-\[10px\]{

        border-color:#E9DEC3 !important;

        background:#FFFBF3 !important;

        color:#8A682A !important;

        box-shadow:none !important;

    }

    .sari-account-page > section.account-card form.mt-5{

        margin-top:16px !important;

        gap:13px !important;

    }

    .sari-account-page [data-password-toggle]{

        width:30px !important;

        height:30px !important;

        border-radius:8px !important;

        color:var(--account-muted) !important;

    }

    .sari-account-page [data-password-toggle]:hover,

    .sari-account-page [data-password-toggle]:focus-visible{

        outline:none !important;

        background:#F5F3EF !important;

        color:#7E5C21 !important;

    }

    .sari-account-page #passwordStrengthBar{

        background:var(--account-gold) !important;

    }

    .sari-account-page > section.account-card form > div:last-child{

        margin-top:2px !important;

        padding-top:13px !important;

        border-color:var(--account-divider) !important;

    }

    .sari-account-page > section.account-card form > div:last-child button[type="submit"]{

        border:1px solid #E1C98D !important;

        background:#FFFAEF !important;

        color:#8B641E !important;

        box-shadow:none !important;

    }

    .sari-account-page > section.account-card form > div:last-child button[type="submit"]:hover{

        border-color:#D8B86A !important;

        background:#FFF5DE !important;

        color:#7B5718 !important;

    }

    /* Activity section + flat nested stats */

    .sari-account-page > section.account-card:last-of-type{

        background:#fff !important;

        box-shadow:var(--account-shadow-main) !important;

    }

    .sari-account-page > section.account-card:last-of-type > div:first-child > span{

        min-height:22px !important;

        height:22px !important;

        padding-inline:8px !important;

        border-radius:999px !important;

        font-size:6.5px !important;

    }

    .sari-account-page > section.account-card:last-of-type > .mt-5{

        margin-top:15px !important;

        gap:10px !important;

    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div{

        min-height:88px !important;

        border:1px solid var(--account-border) !important;

        border-radius:11px !important;

        background:var(--account-surface-soft) !important;

        padding:13px !important;

        box-shadow:none !important;

    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:first-child{

        color:var(--account-muted) !important;

        font-size:6.5px !important;

        font-weight:800 !important;

        letter-spacing:.12em !important;

    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:nth-child(2){

        margin-top:7px !important;

        color:var(--account-text-secondary) !important;

        font-size:9px !important;

        font-weight:700 !important;

        line-height:1.4 !important;

    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:last-child{

        margin-top:4px !important;

        color:var(--account-muted) !important;

        font-size:7px !important;

        line-height:1.45 !important;

    }

    /* Validation text */

    .sari-account-page .text-\[7px\].text-\[\#a65f57\]{

        color:#A65F57 !important;

        font-size:7px !important;

    }

    /* Responsive */

    @media(max-width:1100px){

        .sari-account-page > .grid{

            grid-template-columns:1fr !important;

        }

        .sari-account-page > .grid > aside.account-card{

            min-height:auto !important;

        }

    }

    @media(max-width:700px){

        .sari-account-page{

            padding-bottom:18px !important;

        }

        .sari-account-page > .mb-5{

            min-height:auto !important;

            margin-bottom:12px !important;

            padding:0 0 12px !important;

        }

        .sari-account-page > .mb-5 h1{

            font-size:clamp(28px,8vw,34px) !important;

        }

        .sari-account-page > .grid,

        .sari-account-page > section.account-card:last-of-type > .mt-5{

            gap:10px !important;

        }

        .sari-account-page > .grid > .account-card,

        .sari-account-page > section.account-card{

            padding:14px !important;

            border-radius:13px !important;

        }

        .sari-account-page > .grid > section.account-card > div:first-child{

            align-items:flex-start !important;

        }

        .sari-account-page > .grid > section.account-card > div:first-child > div:last-child{

            width:100% !important;

            text-align:left !important;

        }

        .sari-account-page .field-input{

            height:42px !important;

            font-size:10px !important;

        }

        .sari-account-page form button[type="submit"]{

            min-height:42px !important;

            height:42px !important;

        }

    }

    @media(prefers-reduced-motion:reduce){

        .sari-account-page *,

        .sari-account-page *::before,

        .sari-account-page *::after{

            animation:none !important;

            transition:none !important;

            scroll-behavior:auto !important;

        }

    }


    /* Buyer Account composition — Admin presentation only. */
    .sari-account-page {
        --account-gold: #c88912;
        --account-gold-strong: #aa730f;
        --account-text: #202938;
        --account-text-secondary: #596579;
        --account-muted: #8490a2;
        --account-radius: 17px;
        --account-shadow-main: 0 3px 7px rgba(15,23,42,.055),0 14px 32px rgba(15,23,42,.095),0 30px 68px rgba(15,23,42,.11);
        width: 100% !important;
        max-width: 1380px !important;
        padding: 20px 16px 40px !important;
        font-size: 12.5px;
    }
    .sari-account-page > .mb-5 { min-height: 0 !important; margin-bottom: 0 !important; padding: 2px 2px 14px !important; border-bottom: 0 !important; }
    .sari-account-page > .mb-5 p:first-child { font-size: 10.5px !important; color: #a87316 !important; line-height: 1.2 !important; letter-spacing: .14em !important; }
    .sari-account-page > .mb-5 h1 { font-size: clamp(27px,2.25vw,31px) !important; font-weight: 760 !important; line-height: 1.04 !important; color: #2e343b !important; }
    .sari-account-page > .mb-5 h1 + p { font-size: 11.5px !important; color: #7d8792 !important; }
    .sari-account-page .admin-account-layout { display: grid; grid-template-columns: 286px minmax(0,1fr); gap: 14px; align-items: start; }
    .sari-account-page .admin-account-sidebar { position: sticky; top: 84px; display: grid; gap: 14px; min-width: 0; align-self: start; }
    .sari-account-page .account-card { min-width: 0; border: 1px solid #e5e7eb !important; border-radius: 17px !important; background: #fff !important; box-shadow: var(--account-shadow-main) !important; }
    .sari-account-page .admin-account-profile-card { overflow: hidden; }
    .sari-account-page .admin-account-panel-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 14px 15px; border-bottom: 1px solid #edf1f5; }
    .sari-account-page .admin-account-panel-head h2 { margin: 0; color: #2b3441; font-size: 16px; font-weight: 760; }
    .sari-account-page .admin-account-panel-head > span { font-size: 10px; color: #8490a2; text-transform: uppercase; }
    .sari-account-page .admin-account-identity { padding: 16px; }
    .sari-account-page .admin-account-identity > div { display: flex; flex-direction: column; align-items: center; padding: 0; border: 0; gap: 16px; }
    .sari-account-page .admin-account-identity > div > div:first-child { display: flex; width: 100%; flex-direction: column; align-items: center; gap: 14px; text-align: center; }
    .sari-account-page .admin-account-identity .h-12.w-12 { width: 118px !important; height: 118px !important; flex: 0 0 118px; border: 1px solid #dbe1e8 !important; border-radius: 20px !important; background: #f4f6f8 !important; color: #919ba8 !important; font-size: 30px !important; font-weight: 720; }
    .sari-account-page .admin-account-identity h2 { color: #273140; font-size: 18px; font-weight: 760; white-space: normal; overflow-wrap: anywhere; }
    .sari-account-page .admin-account-identity h2 + div { justify-content: center; gap: 6px; margin-top: 8px; }
    .sari-account-page .admin-account-identity .inline-flex { height: auto; min-height: 25px; padding: 4px 9px; border-color: #e5e7eb !important; background: #f7f9fb !important; color: #596579 !important; font-size: 10px; }
    .sari-account-page .admin-account-identity > div > div:last-child { width: 100%; border-color: #edf1f5; border-radius: 11px; background: #f7f9fb; padding: 11px; text-align: center; }
    .sari-account-page .admin-account-identity > div > div:last-child p:first-child { color: #8490a2; font-size: 10px; }
    .sari-account-page .admin-account-identity > div > div:last-child p:last-child { color: #596579; font-size: 12px; }
    .sari-account-page .admin-account-access { padding: 16px !important; }
    .sari-account-page .admin-account-access > div:first-child { flex-wrap: wrap; gap: 10px; }
    .sari-account-page .admin-account-access p.font-bold.uppercase { color: #273140 !important; font-size: 16px !important; letter-spacing: -.018em !important; text-transform: none; }
    .sari-account-page .admin-account-access p.font-bold.uppercase + p { font-size: 10.5px !important; }
    .sari-account-page .admin-account-access > div:first-child > span { font-size: 9px; }
    .sari-account-page .admin-account-access dl { margin-top: 16px; }
    .sari-account-page .admin-account-access dl > div { gap: 10px; padding-block: 9px; border-color: #edf1f5; }
    .sari-account-page .admin-account-access dt { font-size: 11px; color: #8490a2; }
    .sari-account-page .admin-account-access dd { font-size: 12px; color: #596579; max-width: 60%; overflow-wrap: anywhere; }
    .sari-account-page .admin-account-main { overflow: hidden; padding: 0 !important; }
    .sari-account-page .admin-account-main > section { margin: 0 !important; padding: 17px 18px 18px !important; border: 0 !important; border-radius: 0 !important; background: #fff !important; box-shadow: none !important; }
    .sari-account-page .admin-account-main > section + section { border-top: 1px solid #edf1f5 !important; }
    .sari-account-page .admin-account-profile > .mt-5 { margin-top: 0; }
    .sari-account-page .admin-account-main p.font-bold.uppercase { font-size: 17px !important; font-weight: 760 !important; color: #273140 !important; letter-spacing: -.018em !important; text-transform: none; }
    .sari-account-page .admin-account-main p.font-bold.uppercase + p { font-size: 10.5px !important; line-height: 1.55 !important; }
    .sari-account-page .admin-account-main form { gap: 12px 13px; }
    .sari-account-page label > span:first-child { font-size: 11.5px !important; color: #40506a !important; font-weight: 700 !important; }
    .sari-account-page .field-input { width: 100%; height: 52px !important; margin-top: 5px !important; padding-inline: 14px !important; border: 1px solid #d7dee7 !important; border-radius: 14px !important; font-size: 12.5px !important; color: #2e3948 !important; font-weight: 500 !important; }
    .sari-account-page .field-input::placeholder { color: #a0a9b6; }
    .sari-account-page .field-input:focus { border-color: #c88912 !important; box-shadow: 0 0 0 3px rgba(200,137,18,.08) !important; }
    .sari-account-page .admin-account-security .relative .field-input { margin-top: 0 !important; padding-right: 46px !important; }
    .sari-account-page .admin-account-security > div:first-child { align-items: flex-start; }
    .sari-account-page .admin-account-security > div:first-child > div:last-child { border-color: #dfe5ec !important; background: #f7f9fb !important; color: #596579 !important; font-size: 10px; }
    .sari-account-page .admin-account-security form { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); margin-top: 16px; }
    .sari-account-page .admin-account-security form > div:last-child { grid-column: 1 / -1; padding-top: 14px; border-color: #edf1f5; }
    .sari-account-page .admin-account-security form > div:last-child span { font-size: 10px; }
    .sari-account-page form button[type="submit"] { height: 42px !important; min-height: 42px !important; border-radius: 10px !important; font-size: 11.5px !important; }
    .sari-account-page .admin-account-profile form button[type="submit"] { border: 1px solid #c88912; background: #c88912; color: #fff; }
    .sari-account-page .admin-account-profile form button[type="submit"]:hover { background: #aa730f; }
    .sari-account-page .admin-account-security form button[type="submit"] { border-color: #dfe5ec !important; background: #f3f5f7 !important; color: #475467 !important; }
    .sari-account-page .admin-account-security form button[type="submit"]:hover { background: #e9edf2 !important; }
    .sari-account-page form button[type="submit"] svg { width: 16px; height: 16px; }
    .sari-account-page [data-password-toggle] { width: 34px !important; height: 34px !important; right: 8px; }
    .sari-account-page [data-password-toggle]:hover { background: #f3f5f7 !important; color: #475467 !important; }
    .sari-account-page [data-password-toggle] svg { width: 18px; height: 18px; }
    .sari-account-page .admin-account-activity > div:first-child { flex-wrap: wrap; gap: 14px; }
    .sari-account-page .admin-account-activity > div:first-child > div:last-child { gap: 8px; }
    .sari-account-page .admin-account-activity > div:first-child a { background: #fff !important; border-color: #dfe5ec !important; color: #596579 !important; font-size: 10.5px; min-height: 34px; }
    .sari-account-page .admin-account-activity > div:first-child a:hover { background: #f3f5f7 !important; }
    .sari-account-page .admin-account-activity > div:first-child span { font-size: 9px; }
    .sari-account-page .admin-account-activity > .mt-5 { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; margin-top: 16px; }
    .sari-account-page .admin-account-activity > .mt-5 > div { min-width: 0; padding: 13px; border: 1px solid #dfe5ec; border-radius: 11px; background: #f7f9fb; }
    .sari-account-page .admin-account-activity > .mt-5 p:first-child { font-size: 10px; color: #8490a2; }
    .sari-account-page .admin-account-activity > .mt-5 p:nth-child(2) { font-size: 12px; color: #40506a; overflow-wrap: anywhere; }
    .sari-account-page .admin-account-activity > .mt-5 p:last-child { font-size: 10px; color: #8490a2; }
    .sari-account-page label > span:last-child:not(:first-child) { font-size: 10px; }
    .sari-account-page :is(button,a,input):focus-visible { outline: 2px solid #9b6710 !important; outline-offset: 3px; }
    @media (min-width: 640px) { .sari-account-page { padding-inline: 24px !important; } }
    @media (min-width: 1024px) { .sari-account-page { padding-inline: 32px !important; } }
    @media (max-width: 980px) {
        .sari-account-page .admin-account-layout { grid-template-columns: 240px minmax(0,1fr); }
        .sari-account-page .admin-account-security form { grid-template-columns: 1fr; }
    }
    @media (max-width: 760px) {
        .sari-account-page .admin-account-layout { grid-template-columns: 1fr; }
        .sari-account-page .admin-account-sidebar { position: static; top: auto; }
        .sari-account-page .admin-account-main { order: 2; }
        .sari-account-page .admin-account-sidebar { order: 1; }
        .sari-account-page .account-card { border-radius: 14px !important; }
    }
    @media (max-width: 640px) {
        .sari-account-page > .mb-5 h1 { font-size: 25px !important; }
        .sari-account-page > .mb-5 p:first-child { font-size: 9.5px !important; }
        .sari-account-page .admin-account-main form { grid-template-columns: 1fr; }
        .sari-account-page .admin-account-main > section { padding: 15px !important; }
        .sari-account-page .admin-account-main p.font-bold.uppercase { font-size: 15.5px !important; }
        .sari-account-page .admin-account-activity > .mt-5 { grid-template-columns: 1fr; }
        .sari-account-page .admin-account-profile-card .admin-account-identity > div > div:first-child { flex-direction: column; }
        .sari-account-page .admin-account-panel-head h2 { font-size: 15px; }
    }


    .sari-account-page .admin-field-feedback { display: block; margin-top: 6px; color: #b65a50; font-size: 10.5px; line-height: 1.5; }
    .sari-account-page .admin-field-feedback[hidden] { display: none; }
    .sari-account-page form[aria-busy="true"] button[type="submit"] { opacity: .7; cursor: wait; }

    .sari-account-page .admin-account-identity .h-12.w-12 {
        overflow: hidden;
        padding: 12px;
        background: #fff !important;
    }
    .sari-account-page .admin-account-brand-logo {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>

@endpush

@section('content')

@php

    $roleValue = (string) ($adminAccount->role ?? 'super_admin');

    $roleLabel = match($roleValue) {

        'super_admin' => 'Super Administrator',

        'admin' => 'Administrator',

        default => ucwords(str_replace('_', ' ', $roleValue ?: 'Administrator')),

    };

    $canManageSettings = method_exists($adminAccount, 'canManagePlatformSettings')

        ? (bool) $adminAccount->canManagePlatformSettings()

        : ($roleValue === 'super_admin' || (bool) ($adminAccount->can_manage_platform_settings ?? false));

    $accessLabel = $canManageSettings

        ? 'Full Platform Access'

        : 'Standard Admin Access';

    $toDateTime = static function ($value) {

        if (!$value) {

            return null;

        }

        if ($value instanceof \DateTimeInterface) {

            return \Illuminate\Support\Carbon::instance($value);

        }

        try {

            return \Illuminate\Support\Carbon::parse((string) $value);

        } catch (\Throwable $exception) {

            return null;

        }

    };

    $profileUpdated = $toDateTime($adminAccount->profile_updated_at ?? $adminAccount->updated_at ?? null);

    $passwordChanged = $toDateTime($adminAccount->password_changed_at ?? null);

    $lastLogin = $toDateTime($adminAccount->last_login_at ?? null);

    $sessionBrowser = (string) data_get($sessionInfo ?? [], 'browser', 'Web browser');

    $sessionPlatform = (string) data_get($sessionInfo ?? [], 'platform', 'Unknown platform');

    $hasAdminRecentActivityRoute = \Illuminate\Support\Facades\Route::has('admin.recent-activity');

    $hasUserActivityRoute = \Illuminate\Support\Facades\Route::has('admin.user-activity');

@endphp

<div class="sari-account-page mx-auto max-w-[1180px] pb-8">

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

        <div class="flex items-start gap-3.5">

            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] border border-[#eadfc8] bg-[#fff8e9] text-[#a66f16]">

                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">

                    <circle cx="12" cy="8" r="4"></circle>

                    <path d="M4.8 20c.9-4 3.4-6 7.2-6s6.3 2 7.2 6"></path>

                </svg>

            </div>

            <div>

                <p class="text-[8px] font-bold uppercase tracking-[.18em] text-[#aa7a25]">Administration</p>

                <h1 class="mt-1 text-[28px] font-bold leading-none tracking-[-.035em] text-[#25211d]">Account <span class="text-[#c99128]">Management</span></h1>

                

            </div>

        </div>

    </div>

    @if(session('success'))

        <div class="mb-4 flex items-center gap-2.5 rounded-[13px] border border-[#dbe8df] bg-[#f5faf6] px-4 py-3 text-[9px] font-medium text-[#51715d]">

            <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-[#568269]">

                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>

            </span>

            <span>{{ session('success') }}</span>

        </div>

    @endif

    <div class="admin-account-layout">
        <div class="admin-account-sidebar">
            <aside class="account-card admin-account-profile-card">
                <header class="admin-account-panel-head"><h2>Profile</h2><span>Admin account</span></header>
                <div class="admin-account-identity"><div class="flex flex-col gap-4 border-b border-[#eee9e3] pb-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex min-w-0 items-center gap-3.5">

                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-[#eef3f1] text-[11px] font-bold text-[#60756e]">

                        <img class="admin-account-brand-logo" src="{{ asset('images/sari-main-logo.png') }}" alt="SARI" width="118" height="118">

                    </div>

                    <div class="min-w-0">

                        <h2 class="truncate text-[15px] font-bold text-[#2b2622]">{{ $adminAccount->name }}</h2>

                        <div class="mt-1 flex flex-wrap items-center gap-2">

                            <span class="inline-flex h-5 items-center rounded-full border border-[#ead39d] bg-[#fff8e9] px-2 text-[7px] font-bold text-[#a26e17]">{{ $roleLabel }}</span>

                            <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>

                        </div>

                    </div>

                </div>

                <div class="rounded-[11px] border border-[#ece7e0] bg-[#faf9f7] px-3 py-2 text-right">

                    <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#9a9187]">Account ID</p>

                    <p class="mt-1 text-[9px] font-semibold text-[#4d463f]">ADM-{{ str_pad((string) $adminAccount->id, 4, '0', STR_PAD_LEFT) }}</p>

                </div>

            </div></div>
            </aside>
<aside class="account-card admin-account-access rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5">

            <div class="flex items-center justify-between gap-3">

                <div>

                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Access</p>

                    <p class="mt-1 text-[9px] text-[#91887e]">Current platform authorization.</p>

                </div>

                <span class="inline-flex h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>

            </div>

            <dl class="mt-5 space-y-3">

                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Role</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $roleLabel }}</dd></div>

                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Access level</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $accessLabel }}</dd></div>

                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Authentication</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">Password</dd></div>

                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Platform settings</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $canManageSettings ? 'Allowed' : 'Restricted' }}</dd></div>

                <div class="flex items-center justify-between gap-4"><dt class="text-[8px] text-[#8f867d]">Account created</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ optional($adminAccount->created_at)->format('M d, Y') ?? '—' }}</dd></div>

            </dl>

        </aside>
        </div>
        <section class="account-card admin-account-main">
            <section class="admin-account-profile">


            <div class="mt-5">

                <div class="mb-4">

                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Basic information</p>

                    <p class="mt-1 text-[9px] text-[#91887e]">Update the identity used for your SARI administrator account.</p>

                </div>

                <form method="POST" action="{{ route('admin.account.profile.update') }}" data-admin-profile-form class="grid gap-4 sm:grid-cols-2">

                    @csrf

                    @method('PATCH')

                    <label class="block">

                        <span class="text-[8px] font-semibold text-[#5f574f]">Full name</span>

                        <input maxlength="120" aria-describedby="admin-name-feedback" name="name" value="{{ old('name', $adminAccount->name) }}" autocomplete="name" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>

                        <span id="admin-name-feedback" class="admin-field-feedback" data-account-feedback="name" role="status" hidden></span>
                        @error('name', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror

                    </label>

                    <label class="block">

                        <span class="text-[8px] font-semibold text-[#5f574f]">Email address</span>

                        <input maxlength="255" name="email" type="email" value="{{ old('email', $adminAccount->email) }}" autocomplete="email" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>

                        @error('email', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror

                    </label>

                    <div class="sm:col-span-2 flex justify-end pt-1">

                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] bg-[#c99128] px-4 text-[9px] font-bold text-white shadow-[0_6px_14px_rgba(166,112,18,.14)] transition hover:bg-[#b6811f]">

                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12l4 4L19 6"/></svg>

                            Save profile

                        </button>

                    </div>

                </form>

            </div>

        
            </section>
<section class="admin-account-security mt-4 rounded-[16px] border border-[#e8e3dc] bg-white p-5 sm:p-6">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Security</p>

                    <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Secured</span>

                </div>

                <p class="mt-1.5 text-[9px] text-[#91887e]">Change your administrator password. Your current password is required.</p>

            </div>

            <div class="rounded-[10px] border border-[#eee7da] bg-[#fffaf0] px-3 py-2 text-[7px] text-[#8b6a32]">Minimum 8 characters</div>

        </div>

        <form method="POST" action="{{ route('admin.account.password.update') }}" data-admin-password-form class="mt-5 grid gap-4 lg:grid-cols-3">

            @csrf

            @method('PATCH')

            @foreach([

                ['current_password', 'Current password', 'current-password'],

                ['password', 'New password', 'new-password'],

                ['password_confirmation', 'Confirm new password', 'new-password'],

            ] as [$field, $label, $autocomplete])

                <label class="block">

                    <span class="text-[8px] font-semibold text-[#5f574f]">{{ $label }}</span>

                    <div class="relative mt-2">

                        <input id="{{ $field }}" aria-describedby="admin-{{ $field }}-feedback" name="{{ $field }}" type="password" autocomplete="{{ $autocomplete }}" class="field-input h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 pr-10 text-[9px] font-medium text-[#332d28] transition" required>

                        <button type="button" data-password-toggle="{{ $field }}" class="absolute right-2 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#8b837a] transition hover:bg-[#f5f2ed] hover:text-[#76551d]" aria-label="Show or hide password">

                            <svg data-eye-open viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>

                        </button>

                    </div>

                    <span id="admin-{{ $field }}-feedback" class="admin-field-feedback" data-account-feedback="{{ $field }}" role="status" hidden></span>
                    @error($field, 'password')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror

                </label>

            @endforeach

            <div class="lg:col-span-3 flex flex-col gap-3 border-t border-[#eee9e3] pt-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="min-w-0 flex-1 sm:max-w-[420px]">

                    <div class="flex items-center justify-between gap-3">

                        <span class="text-[7px] font-semibold text-[#756d64]">Password strength</span>

                        <span id="passwordStrengthLabel" class="text-[7px] font-bold text-[#9a9187]">Enter a new password</span>

                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eeeae4]"><div id="passwordStrengthBar" class="h-full w-0 rounded-full bg-[#c99128] transition-all duration-200"></div></div>

                </div>

                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border border-[#d7b86f] bg-[#fff9ec] px-4 text-[9px] font-bold text-[#97691d] transition hover:bg-[#fff3d8]">

                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>

                    Update password

                </button>

            </div>

        </form>

    </section>
<section class="admin-account-activity mt-4 rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5 sm:p-6">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Login & Account Activity</p>

                <p class="mt-1 text-[9px] text-[#91887e]">Security information recorded by the administrator account.</p>

            </div>

            <div class="flex flex-wrap items-center gap-2">

                <span class="inline-flex w-fit h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Current session active</span>

                @if($hasAdminRecentActivityRoute)

                    <a href="{{ route('admin.recent-activity') }}" class="inline-flex h-8 items-center justify-center rounded-[9px] border border-[#d7b86f] bg-[#fff9ec] px-3 text-[7px] font-bold text-[#8f651c] transition hover:bg-[#fff3d8]">Admin Recent Activity</a>

                @endif

                @if($hasUserActivityRoute)

                    <a href="{{ route('admin.user-activity') }}" class="inline-flex h-8 items-center justify-center rounded-[9px] border border-[#ddd7cf] bg-white px-3 text-[7px] font-bold text-[#655e56] transition hover:bg-[#f8f6f2]">User Activity Log</a>

                @endif

            </div>

        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">

                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Current session</p>

                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $sessionBrowser }} · {{ $sessionPlatform }}</p>

                <p class="mt-1 text-[7px] text-[#8f867d]">Active now</p>

            </div>

            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">

                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Last sign-in</p>

                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $lastLogin ? $lastLogin->format('M d, Y · h:i A') : 'Not recorded yet' }}</p>

                <p class="mt-1 text-[7px] text-[#8f867d]">Login count: {{ number_format((int) ($adminAccount->login_count ?? 0)) }}</p>

            </div>

            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">

                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Profile updated</p>

                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $profileUpdated ? $profileUpdated->format('M d, Y') : 'Not recorded' }}</p>

                <p class="mt-1 text-[7px] text-[#8f867d]">Identity and email changes</p>

            </div>

            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">

                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Password changed</p>

                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $passwordChanged ? $passwordChanged->format('M d, Y') : 'Not recorded yet' }}</p>

                <p class="mt-1 text-[7px] text-[#8f867d]">Security credential history</p>

            </div>

        </div>

    </section>
        </section>
    </div>

</div>

@push('scripts')

<script>
(function () {
    function passwordFeedback(current, password, confirmation) {
        return {
            password: password && password.length < 8
                ? 'Use at least 8 characters.'
                : (current && password === current ? 'Choose a different password from your current one.' : ''),
            confirmation: confirmation && confirmation !== password ? 'Passwords do not match.' : '',
        };
    }

    function initAccountPage() {
        const page = document.querySelector('.sari-account-page');
        if (!page || page.dataset.accountBound === '1') return;
        page.dataset.accountBound = '1';

        const passwordForm = page.querySelector('[data-admin-password-form]');
        const profileForm = page.querySelector('[data-admin-profile-form]');
        const current = passwordForm?.querySelector('[name="current_password"]');
        const password = passwordForm?.querySelector('[name="password"]');
        const confirmation = passwordForm?.querySelector('[name="password_confirmation"]');
        const bar = page.querySelector('#passwordStrengthBar');
        const strengthLabel = page.querySelector('#passwordStrengthLabel');

        function showFeedback(input, message) {
            if (!input) return;
            const feedback = page.querySelector(`[data-account-feedback="${input.name}"]`);
            input.setCustomValidity(message);
            if (message) input.setAttribute('aria-invalid', 'true');
            else input.removeAttribute('aria-invalid');
            if (feedback) {
                feedback.textContent = message;
                feedback.hidden = !message;
            }
        }

        function syncPasswords() {
            if (!password || !confirmation || !current) return;
            const feedback = passwordFeedback(current.value, password.value, confirmation.value);
            showFeedback(password, feedback.password);
            showFeedback(confirmation, feedback.confirmation);
            const value = password.value;
            let score = 0;
            if (value.length >= 8) score++;
            if (value.length >= 12) score++;
            if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
            if (/\d/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;
            const strength = !value ? 'Enter a new password' : score <= 2 ? 'Basic' : score <= 4 ? 'Good' : 'Strong';
            const color = !value ? '#8490a2' : score <= 2 ? '#b65a50' : score <= 4 ? '#a87316' : '#4f8060';
            if (bar) {
                bar.style.width = value ? `${Math.max(20, score * 20)}%` : '0%';
                bar.style.setProperty('background-color', color, 'important');
            }
            if (strengthLabel) {
                strengthLabel.textContent = strength;
                strengthLabel.style.color = color;
            }
        }

        [current, password, confirmation].forEach(input => input?.addEventListener('input', syncPasswords));
        [password, confirmation].forEach(input => input?.setAttribute('minlength', '8'));
        syncPasswords();

        page.querySelectorAll('[data-password-toggle]').forEach(button => {
            const input = passwordForm?.querySelector(`[name="${button.dataset.passwordToggle}"]`);
            if (!input) return;
            button.setAttribute('aria-controls', input.id);
            button.setAttribute('aria-pressed', 'false');
            button.setAttribute('aria-label', 'Show password');
            button.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(show));
                button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                button.innerHTML = show
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A11.4 11.4 0 0 1 12 5c6.5 0 10 7 10 7a16 16 0 0 1-3.1 3.8M6.4 6.4A17 17 0 0 0 2 12s3.5 7 10 7a11.2 11.2 0 0 0 5.6-1.6"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>';
            });
        });

        const name = profileForm?.querySelector('[name="name"]');
        function syncName() {
            if (name) showFeedback(name, name.value && !name.value.trim() ? 'Enter your full name.' : '');
        }
        name?.addEventListener('input', syncName);
        syncName();

        [profileForm, passwordForm].forEach(form => {
            if (!form) return;
            form.addEventListener('submit', event => {
                if (form.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }
                if (form === passwordForm) syncPasswords();
                else syncName();
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }
                form.dataset.submitting = '1';
                form.setAttribute('aria-busy', 'true');
                const button = form.querySelector('[type="submit"]');
                if (button) {
                    button.dataset.originalMarkup = button.innerHTML;
                    button.disabled = true;
                    button.textContent = 'Saving…';
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAccountPage, { once: true });
    } else {
        initAccountPage();
    }
    document.addEventListener('livewire:navigated', initAccountPage);
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('[data-admin-profile-form],[data-admin-password-form]').forEach(form => {
            delete form.dataset.submitting;
            form.removeAttribute('aria-busy');
            const button = form.querySelector('[type="submit"]');
            if (button?.dataset.originalMarkup) {
                button.innerHTML = button.dataset.originalMarkup;
                button.disabled = false;
                delete button.dataset.originalMarkup;
            }
        });
        initAccountPage();
    });
})();

</script>

@endpush

@endsection
