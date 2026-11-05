@extends('layouts.rider-registration')

@section('title', 'Rider Application — SARI')
@section('body-class', 'rider-application-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/sari-toast.css') }}?v=20261102-register-validation-1">
<style>

    :root {
        --rider-gold: #d48f08;
        --rider-gold-dark: #bd7d05;
        --rider-ink: #211d17;
        --rider-muted: #81786c;
        --rider-line: #e5ded4;
    }

    .rider-form-card,
    .rider-provider-summary {
        border: 1px solid var(--rider-line);
        background: #fff;
        box-shadow:
            12px 14px 32px rgba(52, 40, 25, .075),
            -4px -4px 14px rgba(255, 255, 255, .9);
    }

    .rider-control {
        min-height: 50px;
        width: 100%;
        border: 1px solid #e2dbd1;
        border-radius: 13px;
        background: #fff;
        padding: 0 14px;
        color: #211d17;
        font-size: 13px;
        outline: none;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    textarea.rider-control {
        min-height: 112px;
        padding-top: 13px;
        padding-bottom: 13px;
        resize: vertical;
    }

    .rider-control:focus {
        border-color: var(--rider-gold);
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .09);
    }

    .rider-control:disabled {
        background: #f4f1ed;
        color: #a0998f;
    }

    .rider-label {
        margin-bottom: 7px;
        display: block;
        font-size: 11px;
        font-weight: 650;
        color: #514a42;
    }

    .rider-help {
        margin-top: 5px;
        font-size: 9px;
        line-height: 1.5;
        color: #989084;
    }

    .rider-section {
        border-top: 1px solid #eee8df;
        padding-top: 24px;
    }

    .rider-location-list {
        max-height: 220px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    [hidden] { display: none !important; }

    .rider-profile-row {
        display: grid;
        grid-template-columns: 76px minmax(0, 1fr);
        gap: 15px;
        align-items: center;
        margin-top: 20px;
        border-top: 1px solid #eee8df;
        padding-top: 19px;
    }

    .rider-profile-preview {
        display: grid;
        width: 72px;
        height: 72px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e2dbd1;
        border-radius: 18px;
        background: #faf8f4;
        color: #b97805;
    }

    .rider-profile-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rider-profile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 9px;
    }

    .rider-profile-button {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        border: 1px solid #d7bd8c;
        border-radius: 9px;
        background: #fff;
        padding: 8px 12px;
        font-size: 10px;
        font-weight: 650;
        color: #9b6607;
    }

    .rider-profile-error {
        margin-top: 6px;
        font-size: 10px;
        font-weight: 650;
        color: #d31324;
    }

    @media (max-width: 639px) {
        .rider-profile-row {
            grid-template-columns: 60px minmax(0, 1fr);
        }

        .rider-profile-preview {
            width: 58px;
            height: 58px;
            border-radius: 15px;
        }
    }


    /* ============================================================
       RIDER PROFILE V2 — LARGE DRAG & DROP PHOTO AREA
       ============================================================ */
    .rider-profile-row {
        display: block !important;
        margin-top: 22px !important;
        border-top: 1px solid #eee8df !important;
        padding-top: 20px !important;
    }

    .rider-profile-zone {
        position: relative;
        display: grid;
        width: 100%;
        min-height: 200px;
        margin-top: 13px;
        place-items: center;
        overflow: hidden;
        border: 1.5px dashed #d7b76f;
        border-radius: 16px;
        background: #fffdf9;
        color: #b97805;
        cursor: pointer;
        outline: none;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    .rider-profile-zone:hover,
    .rider-profile-zone:focus-visible {
        border-color: #c88a16;
        background: #fffaf0;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .07);
    }

    .rider-profile-zone.is-dragging {
        border-style: solid;
        border-color: #c98208;
        background: #fff8e9;
        box-shadow: 0 0 0 5px rgba(212, 143, 8, .09);
        transform: translateY(-1px);
    }

    .rider-profile-zone.has-image {
        min-height: 230px;
        border-style: solid;
        border-color: #ddd7cf;
        background: #f6f4f0;
        cursor: default;
        box-shadow: 0 10px 28px rgba(31, 27, 22, .045);
    }

    .rider-profile-zone.has-error {
        border: 2px solid #e11d2e;
        box-shadow: 0 0 0 4px rgba(225, 29, 46, .09);
    }

    .rider-profile-zone img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .rider-profile-empty {
        display: flex;
        max-width: 420px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 26px 20px;
        text-align: center;
    }

    .rider-profile-camera {
        display: grid;
        width: 52px;
        height: 52px;
        place-items: center;
        border-radius: 15px;
        background: #fff4dc;
        color: #b97805;
    }

    .rider-profile-camera svg {
        width: 25px;
        height: 25px;
    }

    .rider-profile-drop-title {
        margin-top: 13px;
        font-size: 12px;
        line-height: 1.4;
        font-weight: 700;
        color: #39342e;
    }

    .rider-profile-drop-copy {
        margin-top: 5px;
        font-size: 9.5px;
        line-height: 1.55;
        color: #91887d;
    }

    .rider-profile-drop-browse {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        margin-top: 12px;
        border: 1px solid #d4b16d;
        border-radius: 9px;
        background: #fff;
        padding: 8px 13px;
        font-size: 10px;
        font-weight: 650;
        color: #9b6607;
    }

    .rider-profile-drop-meta {
        margin-top: 9px;
        font-size: 9px;
        color: #a29a91;
    }

    .rider-profile-image-actions {
        position: absolute;
        right: 11px;
        bottom: 11px;
        z-index: 3;
        display: flex;
        gap: 7px;
    }

    .rider-profile-image-action {
        display: inline-flex;
        min-height: 33px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.72);
        border-radius: 9px;
        background: rgba(32, 29, 25, .82);
        padding: 7px 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 9.5px;
        font-weight: 650;
        color: #fff;
        backdrop-filter: blur(8px);
    }

    .rider-profile-image-action.is-remove {
        background: rgba(255,255,255,.92);
        border-color: rgba(255,255,255,.92);
        color: #5e574f;
    }

    .rider-profile-error {
        margin-top: 8px;
        font-size: 10px;
        font-weight: 650;
        color: #d31324;
    }

    @media (max-width: 639px) {
        .rider-profile-zone {
            min-height: 170px;
            border-radius: 14px;
        }

        .rider-profile-zone.has-image {
            min-height: 200px;
        }
    }


    /* RIDER PROFILE DROPZONE V3 — ULTRA CLEAN */
    .rider-profile-drop-copy,
    .rider-profile-drop-meta {
        display: none !important;
    }

    .rider-profile-drop-title {
        margin-top: 12px !important;
        font-size: 12px !important;
    }

    .rider-profile-drop-browse {
        margin-top: 10px !important;
        min-height: 32px !important;
        padding: 7px 12px !important;
        font-size: 9.5px !important;
    }

    .rider-profile-image-action {
        width: 36px !important;
        height: 36px !important;
        min-height: 36px !important;
        padding: 0 !important;
        border-radius: 10px !important;
    }

    .rider-profile-image-action svg {
        width: 16px !important;
        height: 16px !important;
    }

    .rider-profile-image-action.is-remove {
        color: #8a403d !important;
    }


/* Rider application / Seller registration visual parity. Scoped to this page. */
.rider-application-page .rider-wizard-step {border:0!important;padding:0!important;margin:0!important;}
body.sari-rider-registration.rider-application-page {
    --rider-accent: #c57c00;
    --rider-accent-hover: #a86600;
    --rider-ink: #20242c;
    --rider-muted: #697282;
    --rider-line: #e4e7ec;
    background: #f5f7fa url("{{ asset('images/login-bg.png') }}") center center / cover fixed no-repeat;
    color: var(--rider-ink);
}
.rider-application-page .rider-application-shell {width:100%;max-width:900px;margin:0 auto;padding:0 18px 34px;box-sizing:border-box;}
.rider-application-page .rider-application-main {padding:15px 0 25px!important;}
.rider-application-page .rider-application-top {display:flex;justify-content:space-between;gap:16px;align-items:center;padding:6px 4px 14px;}
.rider-application-page .rider-application-return {color:#694b24;text-decoration:none;font-size:12px;font-weight:600;}
.rider-application-page .rider-application-return:hover {color:var(--rider-accent);}
.rider-application-page .rider-application-top-note {color:#777e8a;font-size:11px;font-weight:500;}
.rider-application-page .rider-application-card {overflow:visible;background:#fff;border:1px solid #e4e7ec;border-radius:16px;box-shadow:0 16px 46px rgba(25,37,54,.10);}
.rider-application-page .rider-application-header {padding:27px 30px 22px;border-radius:16px 16px 0 0;border-bottom:1px solid #edf0f3;}
.rider-application-page .rider-application-eyebrow {margin:0;color:var(--rider-accent);font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;}
.rider-application-page .rider-application-title {margin:7px 0 0;font-size:clamp(26px,3vw,33px);letter-spacing:-.043em;line-height:1.2;color:#191c22;font-weight:700;}
.rider-application-page .rider-application-intro {margin:9px 0 0;color:#667080;font-size:12px;line-height:1.65;}
.rider-application-page .rider-provider-strip {margin-top:22px;display:flex;align-items:center;gap:14px;border:1px solid #e7eaf0;border-radius:12px;background:#f9fafc;padding:14px 16px;}
.rider-application-page .rider-provider-avatar {display:grid;place-items:center;flex:0 0 48px;width:48px;height:48px;border-radius:11px;background:#fff;border:1px solid #e0e4eb;overflow:hidden;color:#a66b14;}
.rider-application-page .rider-provider-avatar img {width:100%;height:100%;object-fit:cover;}
.rider-application-page .rider-provider-avatar svg {width:23px;height:23px;}
.rider-application-page .rider-provider-copy {min-width:0;flex:1 1 auto;}
.rider-application-page .rider-provider-copy small {display:block;color:#8b929d;font-size:10px;line-height:1.4;}
.rider-application-page .rider-provider-copy strong {display:block;color:#262b34;font-weight:700;font-size:14px;line-height:1.4;}
.rider-application-page .rider-provider-copy span {display:block;color:#7a8290;font-size:11px;line-height:1.45;}
.rider-application-page .rider-provider-status {display:flex;flex:0 0 auto;flex-direction:column;gap:6px;align-items:flex-end;color:#656f7d;font-size:10px;}
.rider-application-page .rider-provider-status .rider-active {display:inline-flex;align-items:center;gap:6px;color:#277449;font-size:10px;font-weight:700;background:#e8f5ed;padding:5px 9px;border-radius:99px;}
.rider-application-page .rider-active::before {content:'';width:6px;height:6px;border-radius:50%;background:#28a065;}
.rider-application-page .rider-progress {padding:15px 30px 18px;border-bottom:1px solid #edf0f3;}
.rider-application-page .rider-progress-summary {display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;}
.rider-application-page .rider-progress-summary strong {color:#313640;font-size:12px;font-weight:650;}
.rider-application-page .rider-progress-summary span {color:#858e9b;font-size:11px;font-weight:600;}
.rider-application-page .rider-progress-list {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;list-style:none;margin:0;padding:0;}
.rider-application-page .rider-progress-item {display:flex;gap:8px;align-items:center;color:#959ca7;font-size:10px;font-weight:600;min-width:0;}
.rider-application-page .rider-progress-number {flex:0 0 26px;width:26px;height:26px;border-radius:50%;border:1px solid #dfe4eb;display:grid;place-items:center;color:#858e9b;background:#fff;font-size:11px;font-weight:700;}
.rider-application-page .rider-progress-item.is-current,.rider-application-page .rider-progress-item.is-complete {color:#262e3a;}
.rider-application-page .rider-progress-item.is-current .rider-progress-number,.rider-application-page .rider-progress-item.is-complete .rider-progress-number {color:#fff;border-color:var(--rider-accent);background:var(--rider-accent);}
.rider-application-page .rider-progress-item.is-current .rider-progress-number {box-shadow:0 0 0 3px rgba(197,124,0,.12);}
.rider-application-page .rider-application-form-wrap {padding:27px 30px 30px;}
.rider-application-page .rider-wizard-step[hidden],.rider-application-page .rider-wizard-step:not(.is-active) {display:none!important;}
.rider-application-page .rider-wizard-step.is-active {animation:riderStepFade .26s ease-out both;}
@keyframes riderStepFade {from{opacity:.15;transform:translateY(5px)}to{opacity:1;transform:none}}
.rider-application-page .rider-wizard-step > p:first-child {margin:0!important;font-size:10px!important;line-height:1.5;color:var(--rider-accent)!important;font-weight:700;letter-spacing:.13em;}
.rider-application-page .rider-wizard-step h2 {margin:7px 0 0!important;color:#222832!important;letter-spacing:-.035em;font-size:24px!important;font-weight:700;line-height:1.3;}
.rider-application-page .rider-wizard-step h2 + p {margin:6px 0 0!important;color:#7b8593!important;font-size:12px!important;line-height:1.65!important;}
.rider-application-page .rider-fields-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px 18px;margin-top:24px!important;}
.rider-application-page [data-rider-step="0"] .rider-fields-grid {grid-template-columns:repeat(3,minmax(0,1fr));}
.rider-application-page .rider-fields-grid > div {min-width:0;}
.rider-application-page .rider-page-shell .rider-label {color:#323843!important;font-size:12px!important;line-height:1.55;font-weight:600;margin-bottom:6px!important;}
.rider-application-page .rider-page-shell .rider-control {min-height:44px!important;height:auto;border:1px solid #d4dce6!important;border-radius:9px!important;background:#fff!important;padding:10px 12px!important;color:#252c36!important;font-size:13px!important;font-weight:500;line-height:1.45;box-shadow:none!important;}
.rider-application-page .rider-page-shell select.rider-control {min-height:44px!important;padding-right:24px!important;}
.rider-application-page .rider-page-shell .rider-control::placeholder {color:#97a1af!important;}
.rider-application-page .rider-page-shell .rider-control:focus {outline:none;border-color:var(--rider-accent)!important;box-shadow:0 0 0 3px rgba(197,124,0,.12)!important;}
.rider-application-page .rider-page-shell .rider-control:disabled {background:#f4f6f8!important;color:#919aa6!important;}
.rider-application-page .rider-page-shell #agePreview {background:#f3f5f8!important;}
.rider-application-page .rider-page-shell textarea.rider-control {min-height:112px!important;resize:vertical;}
.rider-application-page .rider-page-shell .rider-help {color:#7d8795!important;font-size:10.5px!important;line-height:1.55!important;margin:5px 0 0!important;}
.rider-application-page .rider-page-shell .rider-profile-row {margin-top:24px!important;padding-top:22px!important;border-top:1px solid #eef0f3!important;}
.rider-application-page .rider-page-shell .rider-profile-zone {min-height:130px!important;max-height:180px;border:1.5px dashed #c7a16b!important;border-radius:12px!important;background:#f9fafc!important;box-shadow:none!important;}
.rider-application-page .rider-page-shell .rider-profile-zone:hover,.rider-application-page .rider-page-shell .rider-profile-zone:focus-visible {border-color:var(--rider-accent)!important;background:#fffdfa!important;box-shadow:0 0 0 3px rgba(197,124,0,.09)!important;}
.rider-application-page .rider-profile-zone.has-image {min-height:180px!important;}
.rider-application-page .rider-profile-zone.has-error {border:2px solid #c7393e!important;}
.rider-application-page .rider-profile-empty {padding:14px;}
.rider-application-page .rider-profile-drop-title {font-size:12px!important;color:#505b6d!important;}
.rider-application-page .rider-profile-camera {background:#fff3df;color:var(--rider-accent);}
.rider-application-page .rider-profile-drop-browse {color:#966111;border-color:#dfc390;}
.rider-application-page .rider-location-list {border-color:#d9dfe8!important;box-shadow:0 14px 30px rgba(28,39,55,.12);}
.rider-application-page .rider-page-shell input[type="file"]:not(#riderProfileInput) {border:1px solid #d4dce6!important;border-radius:9px!important;min-height:46px!important;padding:9px 11px!important;background:#fff!important;color:#3c4553!important;font-family:inherit;font-size:12px!important;max-width:100%;}
.rider-application-page .rider-page-shell input[type="file"]::file-selector-button {border:1px solid #d6dce6;border-radius:7px;padding:7px 10px;background:#f6f8fb;color:#354253;font:inherit;font-weight:600;margin-right:10px;cursor:pointer;}
.rider-application-page .rider-page-shell [aria-invalid="true"].rider-control,.rider-application-page .rider-page-shell input[aria-invalid="true"]:not([type="hidden"]) {border-color:#cc4245!important;box-shadow:0 0 0 3px rgba(204,66,69,.08)!important;}
.rider-application-page .rider-page-shell #addressStatus {background:#f6f8fb!important;color:#647284!important;font-size:10px!important;border:1px solid #e7ebf1;}
.rider-application-page .rider-account-notice {margin-top:20px;border-left:3px solid var(--rider-accent);padding:12px 16px;border-radius:0 8px 8px 0;background:#fffaf1;}
.rider-application-page .rider-account-notice strong {color:#66451a;font-size:12px;}
.rider-application-page .rider-account-notice p {margin:5px 0 0;color:#775c37;line-height:1.65;font-size:11px;}
.rider-application-page .rider-application-actions {display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-top:27px;padding-top:22px;border-top:1px solid #ebedf1;}
.rider-application-page .rider-application-nav {display:flex;align-items:center;flex-wrap:wrap;gap:10px;}
.rider-application-page .rider-action {display:inline-flex;align-items:center;justify-content:center;min-height:43px;border-radius:9px;padding:10px 18px;font:inherit;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;transition:background .17s ease,border-color .17s ease,box-shadow .17s ease;}
.rider-application-page .rider-action-primary {border:1px solid var(--rider-accent);background:var(--rider-accent);color:#fff;box-shadow:0 7px 18px rgba(197,124,0,.12);}
.rider-application-page .rider-action-primary:hover {background:var(--rider-accent-hover);border-color:var(--rider-accent-hover);color:#fff;}
.rider-application-page .rider-action-secondary {border:1px solid #d9dfe6;background:#fff;color:#4d5868;}
.rider-application-page .rider-action-secondary:hover {border-color:#c7b291;color:#85561b;}
.rider-application-page .rider-application-footer {margin:17px 0 0;text-align:center;color:#858e9c;font-size:11px;}
.rider-application-page .rider-application-return:focus-visible,.rider-application-page .rider-action:focus-visible {outline:3px solid rgba(197,124,0,.34);outline-offset:3px;}
.rider-application-page #sariBuyerToastStack {top:16px;}
@media(max-width:740px){
    .rider-application-page .rider-application-header {padding:23px 21px 19px;}
    .rider-application-page .rider-progress {padding:14px 20px 17px;}
    .rider-application-page .rider-application-form-wrap {padding:23px 20px;}
    .rider-application-page [data-rider-step="0"] .rider-fields-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
    .rider-application-page .rider-progress-list {gap:5px;}
    .rider-application-page .rider-progress-item {gap:6px;font-size:9px;}
    .rider-application-page .rider-progress-number {width:23px;height:23px;flex-basis:23px;}
}
@media(max-width:520px){
    .rider-application-page .rider-application-shell {padding:0 12px 23px;}
    .rider-application-page .rider-application-card {border-radius:13px;}
    .rider-application-page .rider-application-header {padding:20px 17px 18px;}
    .rider-application-page .rider-progress {padding:13px 16px 16px;}
    .rider-application-page .rider-application-form-wrap {padding:20px 17px 23px;}
    .rider-application-page .rider-fields-grid,.rider-application-page [data-rider-step="0"] .rider-fields-grid {grid-template-columns:minmax(0,1fr);gap:15px;}
    .rider-application-page .rider-provider-strip {align-items:flex-start;gap:10px;padding:11px;}
    .rider-application-page .rider-provider-avatar {width:42px;height:42px;flex-basis:42px;}
    .rider-application-page .rider-provider-status {font-size:9px;}
    .rider-application-page .rider-progress-item {flex-direction:column;gap:5px;text-align:center;justify-content:center;font-size:8px;}
    .rider-application-page .rider-progress-number {flex-basis:23px;}
    .rider-application-page .rider-application-actions {align-items:stretch;}
    .rider-application-page .rider-application-nav {flex:1 1 auto;}
    .rider-application-page .rider-action {min-height:44px;}
    .rider-application-page .rider-action[data-rider-next],.rider-application-page .rider-action[type=submit] {width:100%;}
}
@media(prefers-reduced-motion:reduce){.rider-application-page .rider-wizard-step.is-active {animation:none;}.rider-application-page .rider-action {transition:none;}}


/* ====================================================================
   RIDER WIZARD: RUNTIME-INDEPENDENT BASE STYLES
   The Rider layout intentionally does not load a Tailwind CDN. This
   scoped fallback makes all interactive fields, popovers and uploads
   display correctly even when external styles/scripts are blocked.
   ==================================================================== */
body.sari-rider-registration.rider-application-page .rider-application-shell,
body.sari-rider-registration.rider-application-page .rider-application-shell * {
    box-sizing: border-box;
}
body.sari-rider-registration.rider-application-page .rider-application-shell .sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}
body.sari-rider-registration.rider-application-page .rider-application-shell [hidden] {display:none!important;}
body.sari-rider-registration.rider-application-page .rider-application-shell .relative {position:relative;}
body.sari-rider-registration.rider-application-page .rider-location-list {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 5px);
    z-index: 30;
    padding: 6px;
    border: 1px solid #d9dfe8;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 14px 30px rgba(28,39,55,.12);
}
body.sari-rider-registration.rider-application-page .rider-application-shell .block {display:block;}
body.sari-rider-registration.rider-application-page .rider-application-shell .flex {display:flex;}
body.sari-rider-registration.rider-application-page .rider-application-shell .inline-flex {display:inline-flex;}
body.sari-rider-registration.rider-application-page .rider-application-shell .grid {display:grid;}
body.sari-rider-registration.rider-application-page .rider-application-shell .items-center {align-items:center;}
body.sari-rider-registration.rider-application-page .rider-application-shell .items-start {align-items:flex-start;}
body.sari-rider-registration.rider-application-page .rider-application-shell .gap-2 {gap:8px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .gap-3 {gap:12px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .gap-4 {gap:16px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-1 {margin-top:4px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-2 {margin-top:8px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-3 {margin-top:12px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-4 {margin-top:16px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-5 {margin-top:20px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mt-6 {margin-top:24px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .mb-5 {margin-bottom:20px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .p-3 {padding:12px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .p-4 {padding:16px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .font-bold {font-weight:700;}
body.sari-rider-registration.rider-application-page .rider-application-shell .font-semibold {font-weight:600;}
body.sari-rider-registration.rider-application-page .rider-application-shell .font-medium {font-weight:500;}
body.sari-rider-registration.rider-application-page .rider-application-shell .uppercase {text-transform:uppercase;}
body.sari-rider-registration.rider-application-page .rider-application-shell .leading-5 {line-height:20px;}
body.sari-rider-registration.rider-application-page .rider-application-shell .rounded-full {border-radius:999px;}
body.sari-rider-registration.rider-application-page .rider-application-shell #agePreview {background:#f5f2ee!important;}
body.sari-rider-registration.rider-application-page .rider-application-shell #addressStatus {
    align-items:center;
    gap:8px;
    border-radius:999px;
    padding:8px 12px;
}
body.sari-rider-registration.rider-application-page .rider-application-shell #addressStatusDot {
    flex:0 0 6px;
    width:6px;
    height:6px;
    border-radius:50%;
    background:#d29b2d;
}
body.sari-rider-registration.rider-application-page .rider-application-shell #manualAddressFallback {
    padding:16px;
    margin-top:16px;
    border:1px solid #eadfc9;
    border-radius:14px;
    background:#fffaf2;
}
body.sari-rider-registration.rider-application-page .rider-application-shell #manualAddressFallback > div {
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:12px;
    margin-top:12px;
}
body.sari-rider-registration.rider-application-page .rider-application-shell label:has(> input[name="terms"]) {
    display:flex;
    align-items:flex-start;
    gap:12px;
    margin-top:20px;
    border-radius:14px;
    padding:16px;
    background:#faf8f4;
    color:#746c62;
    font-size:12px;
    line-height:1.6;
}
body.sari-rider-registration.rider-application-page .rider-application-shell input[name="terms"] {
    flex:0 0 17px;
    width:17px;
    height:17px;
    accent-color:#c57c00;
}
body.sari-rider-registration.rider-application-page .rider-application-shell label:has(> input[name="terms"]) a {
    color:#a96f06;
    font-weight:600;
}
body.sari-rider-registration.rider-application-page .rider-application-shell [role="alert"] {
    margin-bottom:20px;
    padding:13px 15px;
    border:1px solid #f5c3c6;
    border-radius:10px;
    background:#fff4f4;
    color:#9c2c35;
    font-size:12px;
}
body.sari-rider-registration.rider-application-page .rider-application-shell [role="alert"] strong,
body.sari-rider-registration.rider-application-page .rider-application-shell [role="alert"] span {display:block;}
body.sari-rider-registration.rider-application-page .rider-application-shell [role="alert"] span {margin-top:5px;}
@media (max-width:520px) {
    body.sari-rider-registration.rider-application-page .rider-application-shell #manualAddressFallback > div {
        grid-template-columns:1fr;
    }
}
</style>
@endpush

@section('content')
<script src="{{ asset('js/buyer/sari-toast.js') }}?v=20261102-register-validation-1" data-sounds-base="{{ asset('sounds/buyer') }}" defer></script>
@php
    $providerName = $logistics->displayName();
    $providerLocation = collect([$logistics->municipality_name, $logistics->province_name])->filter()->implode(', ');
@endphp
<div class="rider-page-shell rider-application-shell">
    <main class="rider-application-main">
        <nav class="rider-application-top" aria-label="Rider application navigation">
            <a class="rider-application-return" href="{{ route('rider.logistics.index') }}">← Choose another Logistics</a>
            <span class="rider-application-top-note">Rider registration</span>
        </nav>
        <div class="rider-application-card">
            <header class="rider-application-header">
                <p class="rider-application-eyebrow">Join SARI · Rider application</p>
                <h1 class="rider-application-title">Apply to {{ $providerName }}</h1>
                <p class="rider-application-intro">Complete four steps, then verify your email before your Logistics team reviews your application.</p>
                <div class="rider-provider-strip" aria-label="Selected Logistics provider">
                    <div class="rider-provider-avatar" aria-hidden="true">
                        @if($logistics->profile_image_path)
                            <img src="{{ asset('storage/' . $logistics->profile_image_path) }}" alt="">
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V7l8-4 8 4v12M8 19v-5h8v5M8 9h.01M12 9h.01M16 9h.01"/></svg>
                        @endif
                    </div>
                    <div class="rider-provider-copy">
                        <small>Applying to</small><strong>{{ $providerName }}</strong>
                        <span>{{ $providerLocation ?: 'SARI Logistics network' }}</span>
                    </div>
                    <div class="rider-provider-status">
                        <span class="rider-active">Active</span>
                        <span>{{ number_format((int) ($logistics->approved_riders_count ?? 0)) }} riders</span>
                    </div>
                </div>
            </header>
            <div class="rider-progress" aria-label="Application progress">
                <div class="rider-progress-summary"><strong id="riderProgressTitle">Personal information</strong><span id="riderProgressCounter" aria-live="polite">Step 1 of 4</span></div>
                <ol class="rider-progress-list" id="riderProgressSteps">
                    <li class="rider-progress-item is-current" aria-current="step"><span class="rider-progress-number">1</span><span>Personal</span></li>
                    <li class="rider-progress-item"><span class="rider-progress-number">2</span><span>Address</span></li>
                    <li class="rider-progress-item"><span class="rider-progress-number">3</span><span>Documents</span></li>
                    <li class="rider-progress-item"><span class="rider-progress-number">4</span><span>Account</span></li>
                </ol>
            </div>
            <div class="rider-application-form-wrap">
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3" role="alert">
                        <strong class="block text-[12px] text-red-800">Please review your application</strong>
                        <span class="mt-1 block text-[11px] leading-5 text-red-700">{{ $errors->first() }}</span>
                    </div>
                @endif
                <form id="riderApplicationForm" method="POST" action="{{ route('rider.logistics.submit', $logistics) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" id="provinceCode" name="province_code" value="{{ old('province_code') }}">
                    <input type="hidden" id="provinceName" name="province_name" value="{{ old('province_name') }}">
                    <input type="hidden" id="municipalityCode" name="municipality_code" value="{{ old('municipality_code') }}">
                    <input type="hidden" id="municipalityName" name="municipality_name" value="{{ old('municipality_name') }}">
                    <input type="hidden" id="barangayCode" name="barangay_code" value="{{ old('barangay_code') }}">
                    <input type="hidden" id="barangayName" name="barangay_name" value="{{ old('barangay_name') }}">
                    <section class="rider-wizard-step" data-rider-step="0" data-step-title="Personal information">
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">01 · Personal</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Personal information</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Use the same details shown on your valid ID or driver's license.</p>

                        <div class="rider-fields-grid mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label" for="riderField-last_name">Last Name *</label>
                                <input id="riderField-last_name" name="last_name" value="{{ old('last_name') }}" required minlength="2" maxlength="50" placeholder="Dela Cruz" class="rider-control" data-person-name>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-first_name">First Name *</label>
                                <input id="riderField-first_name" name="first_name" value="{{ old('first_name') }}" required minlength="2" maxlength="50" placeholder="Juan" class="rider-control" data-person-name>
                            </div>
                            <div>
                                <label class="rider-label" for="middleInitial">Middle Initial</label>
                                <input id="middleInitial" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="1" placeholder="M" class="rider-control uppercase">
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-sex">Sex *</label>
                                <select id="riderField-sex" name="sex" required class="rider-control">
                                    <option value="">Select sex</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-email">Email *</label>
                                <input id="riderField-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@email.com" class="rider-control">
                            </div>
                            <div>
                                <label class="rider-label" for="contactNumber">Contact No. *</label>
                                <input id="contactNumber" type="tel" name="contact_no" value="{{ old('contact_no') }}" required inputmode="tel" maxlength="13" pattern="(?:09[0-9]{9}|\+639[0-9]{9})" autocomplete="tel" placeholder="09XXXXXXXXX or +639XXXXXXXXX" class="rider-control">
                                <p id="contactHint" class="rider-help">Use 09XXXXXXXXX or +639XXXXXXXXX.</p>
                            </div>
                            <div>
                                <label class="rider-label" for="birthday">Birthday *</label>
                                <input id="birthday" type="date" name="birthday" value="{{ old('birthday') }}" required min="1900-01-01" max="{{ now()->subYears(18)->toDateString() }}" class="rider-control">
                                <p id="ageRequirement" class="rider-help">You must be 18 years old or older to register.</p>
                            </div>
                            <div>
                                <label class="rider-label">Age</label>
                                <input id="agePreview" readonly placeholder="Auto-generated" class="rider-control !bg-[#f5f2ee]">
                            </div>
                        </div>
                        <div class="rider-profile-row">
                            <p class="rider-label !mb-0">
                                Profile Photo
                                <span class="font-medium text-[#999086]">(Required)</span>
                            </p>
                            <p class="sr-only">
                                A Rider profile photo is required. JPG, JPEG, PNG, or WEBP, up to 5 MB.
                            </p>

                            <div
                                id="riderProfileZone"
                                class="rider-profile-zone"
                                role="button"
                                tabindex="0"
                                aria-label="Upload Rider profile photo"
                            >
                                <img id="riderProfilePreviewImage" src="" alt="Selected Rider profile photo" hidden>

                                <div id="riderProfileFallback" class="rider-profile-empty">
                                    <span class="rider-profile-camera" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 8h4l1.6-2.5h4.8L16 8h4v10H4z"></path>
                                            <circle cx="12" cy="13" r="3.2"></circle>
                                        </svg>
                                    </span>

                                    <p class="rider-profile-drop-title">Drag photo here</p>
                                    <span class="rider-profile-drop-browse">Browse</span>
                                </div>

                                <div id="riderProfileActions" class="rider-profile-image-actions" hidden>
                                    <button
                                        id="riderProfileBrowse"
                                        type="button"
                                        class="rider-profile-image-action"
                                        aria-label="Change photo"
                                        title="Change photo"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2.8 2.8 0 0 0-4-4L4 16v4Z"></path>
                                            <path d="m13.5 6.5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button
                                        id="riderProfileRemove"
                                        type="button"
                                        class="rider-profile-image-action is-remove"
                                        aria-label="Remove photo"
                                        title="Remove photo"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                            <path d="M4 7h16"></path>
                                            <path d="M9 7V4h6v3"></path>
                                            <path d="m8 10 .6 8h6.8l.6-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <input
                                id="riderProfileInput"
                                type="file"
                                name="profile_image" required
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                class="sr-only"
                            >
                            <p id="riderProfileFile" class="sr-only" hidden></p>
                            <p id="riderProfileError" class="rider-profile-error" hidden></p>
                        </div>
                    </section>

                    <section class="rider-wizard-step" data-rider-step="1" data-step-title="Home address" hidden>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">02 · Address</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Home address</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Choose your Philippine address from the directory.</p>

                        <div id="addressStatus" class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#faf8f4] px-3 py-2 text-[9px] font-semibold text-[#867c70]">
                            <span id="addressStatusDot" class="h-1.5 w-1.5 rounded-full bg-[#d29b2d]"></span>
                            <span id="addressStatusText">Loading Philippine address directory...</span>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="relative">
                                <label class="rider-label">Province / Area *</label>
                                <input id="provinceSearch" autocomplete="off" value="{{ old('province_name') }}" placeholder="Type Laguna, Cebu, Batangas..." class="rider-control">
                                <div id="provinceResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="relative">
                                <label class="rider-label">City / Municipality *</label>
                                <input id="municipalitySearch" autocomplete="off" value="{{ old('municipality_name') }}" disabled placeholder="Choose a province first" class="rider-control">
                                <div id="municipalityResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="relative">
                                <label class="rider-label">Barangay *</label>
                                <input id="barangaySearch" autocomplete="off" value="{{ old('barangay_name') }}" disabled placeholder="Choose a city / municipality first" class="rider-control">
                                <div id="barangayResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="sm:row-span-2">
                                <label class="rider-label" for="riderField-street_address">Street / House No. / Subdivision *</label>
                                <textarea id="riderField-street_address" name="street_address" required maxlength="500" placeholder="House number, street, subdivision, landmark, etc." class="rider-control">{{ old('street_address') }}</textarea>
                            </div>
                        </div>

                        <div id="manualAddressFallback" hidden class="mt-4 rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] p-4">
                            <p class="text-[10px] font-bold text-[#805d20]">Manual address entry</p>
                            <p class="mt-1 text-[9px] leading-5 text-[#887451]">The online directory could not be reached. You can still complete the application.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <input id="manualProvince" placeholder="Province / Area" class="rider-control">
                                <input id="manualMunicipality" placeholder="City / Municipality" class="rider-control">
                                <input id="manualBarangay" placeholder="Barangay" class="rider-control">
                            </div>
                        </div>
                    </section>

                    <section class="rider-wizard-step" data-rider-step="2" data-step-title="Delivery requirements" hidden>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">03 · Vehicle & Verification</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Delivery requirements</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Your selected Logistics provider will review these details before approval.</p>

                        <div class="rider-fields-grid mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label" for="licenseNumber">Driver's License No. *</label>
                                <input id="licenseNumber" name="license_number" value="{{ old('license_number') }}" required maxlength="13" pattern="[A-Za-z][0-9]{2}-[0-9]{2}-[0-9]{6}" placeholder="A00-00-000000" class="rider-control uppercase">
                                <p class="rider-help">Format: A00-00-000000.</p>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-license_expiry_date">License Expiry *</label>
                                <input id="riderField-license_expiry_date" type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}" required min="{{ now()->toDateString() }}" class="rider-control">
                                <p class="rider-help">Expiry date must be today or later.</p>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-vehicle_type">Vehicle *</label>
                                <select id="riderField-vehicle_type" name="vehicle_type" required class="rider-control">
                                    <option value="">Choose vehicle</option>
                                    @foreach(['Motorcycle','Car','Van'] as $vehicle)
                                        <option value="{{ $vehicle }}" {{ old('vehicle_type') === $vehicle ? 'selected' : '' }}>{{ $vehicle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="rider-label" for="plateNumber">Plate Number *</label>
                                <input id="plateNumber" name="plate_number" value="{{ old('plate_number') }}" required maxlength="20" placeholder="ABC 1234" class="rider-control uppercase">
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-id_document">Driver's License Document *</label>
                                <input id="riderField-id_document" type="file" name="id_document" required accept=".jpg,.jpeg,.png,.pdf" class="block min-h-[50px] w-full rounded-[13px] border border-[#e2dbd1] bg-white px-3 py-3 text-[12px] text-[#484039]">
                                <p class="rider-help">JPG, PNG, or PDF. Maximum 5 MB.</p>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-orcr_document">OR / CR *</label>
                                <input id="riderField-orcr_document" type="file" name="orcr_document" required accept=".jpg,.jpeg,.png,.pdf" class="block min-h-[50px] w-full rounded-[13px] border border-[#e2dbd1] bg-white px-3 py-3 text-[12px] text-[#484039]">
                                <p class="rider-help">JPG, PNG, or PDF. Maximum 5 MB.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rider-wizard-step" data-rider-step="3" data-step-title="Login details" hidden>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">04 · Account</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Create login credentials</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">These credentials become usable only after {{ $providerName }} approves your Rider application.</p>

                        <div class="rider-fields-grid mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label" for="riderField-password">Password *</label>
                                <input id="riderField-password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters" class="rider-control">
                                <p class="rider-help">Use at least 8 characters with uppercase, lowercase, number, and special character.</p>
                            </div>
                            <div>
                                <label class="rider-label" for="riderField-password_confirmation">Confirm Password *</label>
                                <input id="riderField-password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Repeat password" class="rider-control">
                            </div>
                        </div>

                        <label class="mt-5 flex items-start gap-3 rounded-[14px] bg-[#faf8f4] p-4 text-[10px] leading-5 text-[#746c62]">
                            <input type="checkbox" name="terms" value="1" required class="mt-0.5 h-4 w-4 shrink-0 accent-[#c98a08]">
                            <span>I agree to the <a href="#" class="font-semibold text-[#a96f06]">Terms of Service</a> and <a href="#" class="font-semibold text-[#a96f06]">Privacy Policy</a>.</span>
                        </label>
                        <div class="rider-account-notice">
                            <strong>What happens next?</strong>
                            <p>Verify your email after submitting. Your selected Logistics team reviews the application before account activation.</p>
                        </div>
                    </section>
                    <div class="rider-application-actions">
                        <a class="rider-action rider-action-secondary" href="{{ route('rider.logistics.index') }}" data-rider-change>← Change Logistics</a>
                        <div class="rider-application-nav">
                            <button class="rider-action rider-action-secondary" type="button" data-rider-back hidden>← Back</button>
                            <button class="rider-action rider-action-primary" type="button" data-rider-next>Continue →</button>
                            <button class="rider-action rider-action-primary" type="submit" data-rider-submit hidden>Continue to Email Verification →</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <p class="rider-application-footer">© {{ date('Y') }} SARI · Rider registration</p>
    </main>
</div>
<noscript><style>.rider-application-page .rider-wizard-step[hidden],.rider-application-page .rider-wizard-step:not(.is-active){display:block!important;margin-bottom:28px;}.rider-application-page [data-rider-next],.rider-application-page [data-rider-back]{display:none!important}.rider-application-page [data-rider-submit]{display:inline-flex!important}</style></noscript>
<script>

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('riderApplicationForm');
    const contactNumber = document.getElementById('contactNumber');
    const contactHint = document.getElementById('contactHint');
    const birthday = document.getElementById('birthday');
    const agePreview = document.getElementById('agePreview');
    const middleInitial = document.getElementById('middleInitial');
    const plateNumber = document.getElementById('plateNumber');
    const licenseNumber = document.getElementById('licenseNumber');

    const riderProfileInput = document.getElementById('riderProfileInput');
    const riderProfileZone = document.getElementById('riderProfileZone');
    const riderProfileActions = document.getElementById('riderProfileActions');
    const riderProfileBrowse = document.getElementById('riderProfileBrowse');
    const riderProfileRemove = document.getElementById('riderProfileRemove');
    const riderProfilePreviewImage = document.getElementById('riderProfilePreviewImage');
    const riderProfileFallback = document.getElementById('riderProfileFallback');
    const riderProfileFile = document.getElementById('riderProfileFile');
    const riderProfileError = document.getElementById('riderProfileError');
    let riderProfileObjectUrl = null;

    function clearRiderProfileError() {
        riderProfileZone.classList.remove('has-error');
        riderProfileError.hidden = true;
        riderProfileError.textContent = '';
    }

    function showRiderProfileError(message) {
        riderProfileZone.classList.add('has-error');
        riderProfileError.textContent = message;
        riderProfileError.hidden = false;
        window.SariBuyerToast?.error(message);
    }

    function setRiderProfileEmptyState() {
        riderProfileZone.classList.remove('has-image');
        riderProfilePreviewImage.src = '';
        riderProfilePreviewImage.hidden = true;
        riderProfileFallback.hidden = false;
        riderProfileActions.hidden = true;
        riderProfileFile.hidden = true;
        riderProfileFile.textContent = '';
    }

    function resetRiderProfile() {
        if (riderProfileObjectUrl) {
            URL.revokeObjectURL(riderProfileObjectUrl);
            riderProfileObjectUrl = null;
        }

        riderProfileInput.value = '';
        riderProfileInput.setCustomValidity('A profile photo is required.');
        setRiderProfileEmptyState();
        clearRiderProfileError();
    }

    function validateRiderProfile(file) {
        if (!file) {
            resetRiderProfile();
            riderProfileInput.setCustomValidity('A profile photo is required.');
            showRiderProfileError('Upload your profile photo before submitting.');
            return false;
        }

        const extension = String(file.name || '').toLowerCase().split('.').pop();
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (
            !allowedExtensions.includes(extension)
            || !allowedTypes.includes(String(file.type || '').toLowerCase())
        ) {
            riderProfileInput.value = '';
            setRiderProfileEmptyState();
            riderProfileInput.setCustomValidity('Choose a JPG, JPEG, PNG, or WEBP image.');
            showRiderProfileError('Choose a JPG, JPEG, PNG, or WEBP image.');
            return false;
        }

        if (file.size > 5 * 1024 * 1024) {
            riderProfileInput.value = '';
            setRiderProfileEmptyState();
            riderProfileInput.setCustomValidity('Profile photo must not be larger than 5 MB.');
            showRiderProfileError('Profile photo must not be larger than 5 MB.');
            return false;
        }

        riderProfileInput.setCustomValidity('');
        clearRiderProfileError();

        if (riderProfileObjectUrl) {
            URL.revokeObjectURL(riderProfileObjectUrl);
        }

        riderProfileObjectUrl = URL.createObjectURL(file);
        riderProfilePreviewImage.src = riderProfileObjectUrl;
        riderProfilePreviewImage.hidden = false;
        riderProfileFallback.hidden = true;
        riderProfileActions.hidden = false;
        riderProfileZone.classList.add('has-image');

        return true;
    }

    function assignRiderDroppedFile(file) {
        try {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            riderProfileInput.files = transfer.files;
            return true;
        } catch (error) {
            console.warn('SARI Rider profile upload: dropped file could not be assigned.', error);
            return false;
        }
    }

    function openRiderProfilePicker() {
        riderProfileInput.click();
    }

    riderProfileZone.addEventListener('click', function (event) {
        if (event.target.closest('.rider-profile-image-actions')) return;
        if (!riderProfileZone.classList.contains('has-image')) {
            openRiderProfilePicker();
        }
    });

    riderProfileZone.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        if (event.target.closest('.rider-profile-image-actions')) return;

        event.preventDefault();

        if (!riderProfileZone.classList.contains('has-image')) {
            openRiderProfilePicker();
        }
    });

    riderProfileBrowse.addEventListener('click', function (event) {
        event.stopPropagation();
        openRiderProfilePicker();
    });

    riderProfileRemove.addEventListener('click', function (event) {
        event.stopPropagation();
        resetRiderProfile();
    });

    form.addEventListener('formdata', function (event) {
        const photo = riderProfileInput.files?.[0];
        if (photo) event.formData.set('profile_image', photo, photo.name);
    });
    riderProfileInput.addEventListener('invalid', function () {
        showRiderProfileError('Select a valid profile photo before submitting.');
    });

    riderProfileInput.addEventListener('change', function () {
        validateRiderProfile(riderProfileInput.files?.[0] || null);
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        riderProfileZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
            riderProfileZone.classList.add('is-dragging');
        });
    });

    ['dragleave', 'dragend'].forEach(function (eventName) {
        riderProfileZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
            riderProfileZone.classList.remove('is-dragging');
        });
    });

    riderProfileZone.addEventListener('drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        riderProfileZone.classList.remove('is-dragging');

        const files = Array.from(event.dataTransfer?.files || []);

        if (files.length !== 1) {
            showRiderProfileError('Drop one image only.');
            return;
        }

        const file = files[0];

        if (!assignRiderDroppedFile(file)) {
            showRiderProfileError('Use Browse photo to select this image.');
            return;
        }

        validateRiderProfile(riderProfileInput.files?.[0] || null);
    });

    document.querySelectorAll('[data-person-name]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^\p{L}\s.'-]/gu, '').replace(/\s{2,}/g, ' ');
        });
    });

    middleInitial.addEventListener('input', function () {
        this.value = this.value.replace(/[^\p{L}]/gu, '').slice(0, 1).toUpperCase();
    });

    plateNumber.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z0-9 -]/g, '').toUpperCase().slice(0, 20);
    });

    licenseNumber?.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z0-9-]/g, '').toUpperCase().slice(0, 13);
    });

    function syncPhone() {
        let value = contactNumber.value.replace(/\s+/g, '');

        if (value.startsWith('+')) {
            value = '+' + value.slice(1).replace(/\D/g, '').slice(0, 12);
        } else {
            value = value.replace(/\D/g, '').slice(0, 11);
        }

        contactNumber.value = value;

        if (!value) {
            contactNumber.setCustomValidity('');
            contactHint.textContent = 'Use 09XXXXXXXXX or +639XXXXXXXXX.';
            contactHint.style.color = '#989084';
            return;
        }

        const valid = /^(?:09\d{9}|\+639\d{9})$/.test(value);
        contactNumber.setCustomValidity(valid ? '' : 'Enter a Philippine mobile number as 09XXXXXXXXX or +639XXXXXXXXX.');
        contactHint.textContent = valid
            ? 'Valid Philippine mobile number.'
            : 'Enter 09XXXXXXXXX or +639XXXXXXXXX.';
        contactHint.style.color = valid ? '#56816a' : '#b25a5a';
    }

    function calculateAge() {
        if (!birthday.value) {
            agePreview.value = '';
            return;
        }
        const birth = new Date(birthday.value + 'T00:00:00');
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
        agePreview.value = Math.max(0, age);
    }

    contactNumber.addEventListener('beforeinput', function (event) {
        if (!event.data || event.inputType.startsWith('delete')) return;
        if (/^\d+$/.test(event.data)) return;
        if (/^\+\d+$/.test(event.data) && contactNumber.selectionStart === 0 && !contactNumber.value.includes('+')) return;

        const canInsertPlus = event.data === '+'
            && contactNumber.selectionStart === 0
            && !contactNumber.value.includes('+');

        if (canInsertPlus) return;
        event.preventDefault();
    });

    contactNumber.addEventListener('input', syncPhone);
    contactNumber.addEventListener('paste', function () {
        window.setTimeout(syncPhone, 0);
    });
    birthday.addEventListener('change', calculateAge);

    const provinceSearch = document.getElementById('provinceSearch');
    const municipalitySearch = document.getElementById('municipalitySearch');
    const barangaySearch = document.getElementById('barangaySearch');
    const provinceResults = document.getElementById('provinceResults');
    const municipalityResults = document.getElementById('municipalityResults');
    const barangayResults = document.getElementById('barangayResults');
    const provinceCode = document.getElementById('provinceCode');
    const provinceName = document.getElementById('provinceName');
    const municipalityCode = document.getElementById('municipalityCode');
    const municipalityName = document.getElementById('municipalityName');
    const barangayCode = document.getElementById('barangayCode');
    const barangayName = document.getElementById('barangayName');
    const statusDot = document.getElementById('addressStatusDot');
    const statusText = document.getElementById('addressStatusText');
    const manualFallback = document.getElementById('manualAddressFallback');
    const manualProvince = document.getElementById('manualProvince');
    const manualMunicipality = document.getElementById('manualMunicipality');
    const manualBarangay = document.getElementById('manualBarangay');

    let provinces = [];
    let municipalities = [];
    let barangays = [];
    let manualMode = false;

    function normalizeList(payload) {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        if (Array.isArray(payload?.data?.data)) return payload.data.data;
        if (Array.isArray(payload?.results)) return payload.results;
        if (Array.isArray(payload?.items)) return payload.items;
        return [];
    }

    function locationCode(item) { return String(item.code ?? item.psgc_code ?? item.psgcCode ?? item.id ?? ''); }
    function locationName(item) { return String(item.name ?? item.area_name ?? item.areaName ?? item.label ?? ''); }

    function setAddressStatus(type, text) {
        statusText.textContent = text;
        statusDot.className = 'h-1.5 w-1.5 rounded-full ' + (type === 'ready' ? 'bg-[#56816a]' : type === 'error' ? 'bg-[#b25a5a]' : 'bg-[#d29b2d]');
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        });
        if (!response.ok) throw new Error('Address request failed: ' + response.status);
        return response.json();
    }

    function renderResults(container, items, searchValue, onSelect) {
        container.innerHTML = '';
        const query = searchValue.trim().toLocaleLowerCase();
        const matches = items.filter(item => locationName(item).toLocaleLowerCase().includes(query)).slice(0, 30);

        if (!matches.length) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-3 text-[10px] text-[#91887c]';
            empty.textContent = 'No matching location found.';
            container.appendChild(empty);
            container.hidden = false;
            return;
        }

        matches.forEach(function (item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'block w-full rounded-lg px-3 py-3 text-left text-[11px] text-[#403930] hover:bg-[#fffaf1] hover:text-[#9a6817]';
            option.textContent = locationName(item);
            option.addEventListener('mousedown', function (event) {
                event.preventDefault();
                onSelect(item);
            });
            container.appendChild(option);
        });
        container.hidden = false;
    }

    function bindAutocomplete(input, container, getItems, onSelect) {
        input.addEventListener('focus', function () {
            if (!input.disabled) renderResults(container, getItems(), input.value, onSelect);
        });
        input.addEventListener('input', function () {
            if (!input.disabled) renderResults(container, getItems(), input.value, onSelect);
        });
        input.addEventListener('blur', function () {
            window.setTimeout(() => container.hidden = true, 120);
        });
    }

    async function loadProvinces() {
        setAddressStatus('loading', 'Loading Philippine address directory...');
        try {
            provinces = normalizeList(await fetchJson(@json(route('address.provinces'))));
            if (!provinces.length) throw new Error('No provinces returned.');
            setAddressStatus('ready', 'Address directory ready. Type a province or area.');
        } catch (error) {
            console.error('SARI address directory:', error);
            enableManualAddress();
        }
    }

    async function loadMunicipalities(selectedProvinceCode) {
        municipalitySearch.disabled = true;
        barangaySearch.disabled = true;
        municipalitySearch.value = '';
        barangaySearch.value = '';
        municipalityCode.value = '';
        municipalityName.value = '';
        barangayCode.value = '';
        barangayName.value = '';
        municipalitySearch.placeholder = 'Loading cities / municipalities...';
        try {
            const url = @json(url('/address/provinces')) + '/' + encodeURIComponent(selectedProvinceCode) + '/municipalities';
            municipalities = normalizeList(await fetchJson(url));
            if (!municipalities.length) throw new Error('No cities returned.');
            municipalitySearch.disabled = false;
            municipalitySearch.placeholder = 'Type city or municipality';
        } catch (error) {
            console.error('SARI city directory:', error);
            enableManualAddress();
        }
    }

    async function loadBarangays(selectedMunicipalityCode) {
        barangaySearch.disabled = true;
        barangaySearch.value = '';
        barangayCode.value = '';
        barangayName.value = '';
        barangaySearch.placeholder = 'Loading barangays...';
        try {
            const url = @json(url('/address/municipalities')) + '/' + encodeURIComponent(selectedMunicipalityCode) + '/barangays';
            barangays = normalizeList(await fetchJson(url));
            if (!barangays.length) throw new Error('No barangays returned.');
            barangaySearch.disabled = false;
            barangaySearch.placeholder = 'Type barangay';
        } catch (error) {
            console.error('SARI barangay directory:', error);
            enableManualAddress();
        }
    }

    function selectProvince(item) {
        provinceCode.value = locationCode(item);
        provinceName.value = locationName(item);
        provinceSearch.value = locationName(item);
        provinceResults.hidden = true;
        loadMunicipalities(locationCode(item));
    }

    function selectMunicipality(item) {
        municipalityCode.value = locationCode(item);
        municipalityName.value = locationName(item);
        municipalitySearch.value = locationName(item);
        municipalityResults.hidden = true;
        loadBarangays(locationCode(item));
    }

    function selectBarangay(item) {
        barangayCode.value = locationCode(item);
        barangayName.value = locationName(item);
        barangaySearch.value = locationName(item);
        barangayResults.hidden = true;
    }

    bindAutocomplete(provinceSearch, provinceResults, () => provinces, selectProvince);
    bindAutocomplete(municipalitySearch, municipalityResults, () => municipalities, selectMunicipality);
    bindAutocomplete(barangaySearch, barangayResults, () => barangays, selectBarangay);

    provinceSearch.addEventListener('input', function () {
        if (provinceSearch.value !== provinceName.value) {
            provinceCode.value = '';
            provinceName.value = '';
            municipalityCode.value = '';
            municipalityName.value = '';
            municipalitySearch.value = '';
            municipalitySearch.disabled = true;
            barangayCode.value = '';
            barangayName.value = '';
            barangaySearch.value = '';
            barangaySearch.disabled = true;
        }
    });

    municipalitySearch.addEventListener('input', function () {
        if (municipalitySearch.value !== municipalityName.value) {
            municipalityCode.value = '';
            municipalityName.value = '';
            barangayCode.value = '';
            barangayName.value = '';
            barangaySearch.value = '';
            barangaySearch.disabled = true;
        }
    });

    barangaySearch.addEventListener('input', function () {
        if (barangaySearch.value !== barangayName.value) {
            barangayCode.value = '';
            barangayName.value = '';
        }
    });

    function enableManualAddress() {
        manualMode = true;
        provinceSearch.disabled = true;
        municipalitySearch.disabled = true;
        barangaySearch.disabled = true;
        manualFallback.hidden = false;
        manualProvince.required = true;
        manualMunicipality.required = true;
        manualBarangay.required = true;
        setAddressStatus('error', 'Online address directory unavailable — manual entry enabled.');
        syncManualAddress();
    }

    function syncManualAddress() {
        if (!manualMode) return;
        provinceCode.value = 'MANUAL';
        municipalityCode.value = 'MANUAL';
        barangayCode.value = 'MANUAL';
        provinceName.value = manualProvince.value.trim();
        municipalityName.value = manualMunicipality.value.trim();
        barangayName.value = manualBarangay.value.trim();
    }

    [manualProvince, manualMunicipality, manualBarangay].forEach(input => input.addEventListener('input', syncManualAddress));

    const wizardSteps = Array.from(form.querySelectorAll('[data-rider-step]'));
    const progressItems = Array.from(document.querySelectorAll('#riderProgressSteps .rider-progress-item'));
    const progressTitle = document.getElementById('riderProgressTitle');
    const progressCounter = document.getElementById('riderProgressCounter');
    const nextButton = form.querySelector('[data-rider-next]');
    const backButton = form.querySelector('[data-rider-back]');
    const submitButton = form.querySelector('[data-rider-submit]');
    const changeLink = form.querySelector('[data-rider-change]');
    const stepLabels = ['Personal information', 'Home address', 'Delivery requirements', 'Login details'];
    const serverErrorFields = @json(array_keys($errors->getBag('default')->messages()));
    const fieldStep = {
        last_name:0, first_name:0, middle_initial:0, sex:0, email:0,
        contact_no:0, birthday:0, profile_image:0,
        province_code:1, province_name:1, municipality_code:1, municipality_name:1,
        barangay_code:1, barangay_name:1, street_address:1,
        license_number:2, license_expiry_date:2, vehicle_type:2,
        plate_number:2, id_document:2, orcr_document:2,
        password:3, password_confirmation:3, terms:3
    };
    let currentStep = Math.max(0, Math.min(3, fieldStep[serverErrorFields[0]] ?? 0));
    let submitting = false;

    function toastError(message) {
        const text = String(message || '').trim();
        if (!text) return;
        window.SariBuyerToast?.error(text);
    }
    function showStep(index, announce) {
        currentStep = Math.max(0, Math.min(wizardSteps.length - 1, index));
        wizardSteps.forEach((section, position) => {
            const visible = position === currentStep;
            section.hidden = !visible;
            section.classList.toggle('is-active', visible);
        });
        progressItems.forEach((item, position) => {
            item.classList.toggle('is-current', position === currentStep);
            item.classList.toggle('is-complete', position < currentStep);
            if (position === currentStep) item.setAttribute('aria-current', 'step');
            else item.removeAttribute('aria-current');
        });
        progressTitle.textContent = stepLabels[currentStep];
        progressCounter.textContent = `Step ${currentStep + 1} of ${wizardSteps.length}`;
        backButton.hidden = currentStep === 0;
        changeLink.hidden = currentStep !== 0;
        nextButton.hidden = currentStep === wizardSteps.length - 1;
        submitButton.hidden = currentStep !== wizardSteps.length - 1;
        if (announce) {
            window.SariBuyerToast?.info(`${stepLabels[currentStep]}.`);
            const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            document.querySelector('.rider-progress')?.scrollIntoView({ behavior: reduced ? 'instant' : 'smooth', block: 'start' });
        }
    }
    function inputLabel(field) {
        const label = field.labels?.[0]?.textContent
            || field.closest('div')?.querySelector('label')?.textContent
            || field.getAttribute('aria-label') || field.name || 'this field';
        return String(label).replace(/\*/g, '').replace(/\s+/g, ' ').trim();
    }
    function markInvalid(field, message) {
        if (field === riderProfileInput) {
            showRiderProfileError(message);
            riderProfileZone.focus({ preventScroll: true });
            riderProfileZone.scrollIntoView({ behavior:'smooth', block:'center' });
            return false;
        }
        field.setAttribute('aria-invalid', 'true');
        field.focus({ preventScroll: true });
        field.scrollIntoView({ behavior:'smooth', block:'center' });
        toastError(message);
        return false;
    }
    function checkStep(index) {
        syncPhone();
        if (manualMode) syncManualAddress();
        const section = wizardSteps[index];
        for (const input of section.querySelectorAll('input:not([type="hidden"]),select,textarea')) {
            if (input.disabled || input.readOnly) continue;
            if (!input.checkValidity()) {
                const message = input.validity.customError ? input.validationMessage
                    : input.validity.valueMissing ? `Please complete ${inputLabel(input)}.`
                    : input.validity.typeMismatch && input.type === 'email' ? 'Enter a valid email address.'
                    : input.validity.patternMismatch ? `Check the format of ${inputLabel(input)}.`
                    : input.validationMessage || `Please correct ${inputLabel(input)}.`;
                return markInvalid(input, message);
            }
            if (input.type === 'file' && input.files.length) {
                const file = input.files[0];
                const allowed = input.name === 'profile_image'
                    ? /\.(jpe?g|png|webp)$/i : /\.(jpe?g|png|pdf)$/i;
                if (!allowed.test(file.name || '') || file.size > 5 * 1024 * 1024) {
                    return markInvalid(input, `${inputLabel(input)}: select a supported file under 5 MB.`);
                }
            }
        }
        if (index === 1) {
            if (!provinceName.value.trim() || !municipalityName.value.trim() || !barangayName.value.trim()) {
                const target = !provinceName.value.trim() ? (manualMode ? manualProvince : provinceSearch)
                    : !municipalityName.value.trim() ? (manualMode ? manualMunicipality : municipalitySearch)
                    : (manualMode ? manualBarangay : barangaySearch);
                return markInvalid(target, 'Select a complete Province / Area, City / Municipality, and Barangay.');
            }
        }
        if (index === 3) {
            const password = form.elements.namedItem('password');
            const confirmation = form.elements.namedItem('password_confirmation');
            const pass = String(password.value || '');
            if (pass.length < 8 || pass.length > 255 || !/[a-z]/.test(pass) || !/[A-Z]/.test(pass)
                || !/\d/.test(pass) || !/[^A-Za-z0-9]/.test(pass)) {
                return markInvalid(password, 'Password must be at least 8 characters with uppercase, lowercase, number and special character.');
            }
            if (pass !== confirmation.value) {
                return markInvalid(confirmation, 'Passwords do not match.');
            }
        }
        return true;
    }
    form.addEventListener('input', (event) => {
        if (event.target.hasAttribute?.('aria-invalid')) event.target.removeAttribute('aria-invalid');
    });
    form.addEventListener('change', (event) => {
        if (event.target.hasAttribute?.('aria-invalid')) event.target.removeAttribute('aria-invalid');
    });
    nextButton.addEventListener('click', () => {
        if (!checkStep(currentStep)) return;
        showStep(currentStep + 1, true);
    });
    backButton.addEventListener('click', () => showStep(currentStep - 1, false));
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (submitting) return;
        syncPhone();
        if (manualMode) syncManualAddress();
        for (let i = 0; i < wizardSteps.length; i++) {
            if (i !== currentStep) showStep(i, false);
            if (!checkStep(i)) return;
        }
        showStep(wizardSteps.length - 1, false);
        // Controller validates canonical 09 mobile numbers, but the UI also accepts +639.
        if (/^\+639\d{9}$/.test(contactNumber.value)) {
            contactNumber.value = '0' + contactNumber.value.slice(3);
        }
        submitting = true;
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting application…';
        // Native POST preserves existing multipart fields, CSRF, uploads and Laravel flow.
        form.submit();
    });
    showStep(currentStep, false);
    if (serverErrorFields.length) {
        window.SariBuyerToast?.error(@json($errors->first()));
    }

    syncPhone();
    calculateAge();
    loadProvinces();
});
</script>
@endsection
