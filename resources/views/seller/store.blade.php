@extends('layouts.seller')

@section('title', 'Store Management — SARI Seller')
@section('page-title', 'Store Management')

@push('styles')
<style id="sariSellerEmbeddedCss-store" data-sari-inline-css="store">
@verbatim
/* SARI Seller Store Management — Buyer Profile layout parity.
   One floating Store Profile panel on the left, one unified Store Settings
   panel on the right. White/cool-gray surfaces; gold is reserved for actions. */
.store-admin {
    --sm-gold: #c88912;
    --sm-gold-dark: #a9710e;
    --sm-ink: #202938;
    --sm-text: #596579;
    --sm-muted: #8490a2;
    --sm-line: #dfe5ec;
    --sm-soft-line: #edf1f5;
    --sm-panel: #fff;
    --sm-canvas: #f7f9fb;
    /* Exact Buyer Cart floating shadows, retained across top-level panels only. */
    --sm-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);
    --sm-shadow-mobile:
        0 1px 3px rgba(15, 23, 42, .035),
        0 6px 16px rgba(15, 23, 42, .055),
        0 13px 28px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);
    width: 100%;
    max-width: 1480px;
    margin: 0 auto;
    color: var(--sm-ink);
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
}
.store-admin *, .store-admin *::before, .store-admin *::after { box-sizing: border-box; }
.store-admin [hidden] { display: none !important; }

/* Page heading mirrors the Buyer Profile heading. */
.sm-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 18px;
    padding: 2px 2px 15px;
    border-bottom: 1px solid var(--sm-line);
}
.sm-eyebrow {
    margin: 0;
    color: #a87316;
    font-size: 10.5px;
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: .14em;
    text-transform: uppercase;
}
.sm-title {
    margin: 5px 0 0;
    color: #2e343b;
    font-size: clamp(27px, 2.25vw, 31px);
    font-weight: 760;
    line-height: 1.04;
    letter-spacing: -.035em;
}
.sm-title span { color: var(--sm-gold); }
.sm-subtitle {
    max-width: 790px;
    margin: 8px 0 0;
    color: #7d8792;
    font-size: 11.5px;
    line-height: 1.6;
}
.sm-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 10px; }
.sm-header-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.sm-enterprise-status {
    display: inline-flex;
    min-height: 30px;
    align-items: center;
    gap: 7px;
    border: 1px solid #dde3e8;
    border-radius: 9px;
    background: #fff;
    padding: 0 10px;
    color: #596273;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}
.sm-enterprise-status__dot { width: 6px; height: 6px; flex: 0 0 6px; border-radius: 50%; background: currentColor; }
.sm-enterprise-status--open { color: #47765b; border-color: #d7e5dc; }
.sm-enterprise-status--paused { color: #99690d; border-color: #e4d5b2; }
.sm-last-updated { color: #8a94a1; font-size: 10px; font-weight: 550; white-space: nowrap; }
.sm-account-link {
    display: inline-flex;
    min-height: 38px;
    align-items: center;
    justify-content: center;
    border: 1px solid #dfe4ea;
    border-radius: 10px;
    background: #fff;
    padding: 0 13px;
    color: #4f5b6c;
    font-size: 10.5px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: border-color .15s ease, color .15s ease;
}
.sm-account-link:hover, .sm-account-link:focus-visible { border-color: #d4b97e; color: var(--sm-gold-dark); outline: none; }

/* Profile sidebar LEFT, unified settings RIGHT. */
.sm-layout {
    display: grid;
    grid-template-columns: 286px minmax(0, 1fr);
    align-items: start;
    gap: 16px;
    margin-top: 16px;
}
.sm-stack { display: grid; min-width: 0; gap: 14px; }
.sm-preview { position: sticky; top: 84px; align-self: start; }
.sm-panel {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: var(--sm-shadow);
}
/* Rounded unified Store Settings panel without clipping its custom dropdown. */
.sm-unified-account {
    overflow: visible;
    border-radius: 20px;
}
.sm-unified-section { min-width: 0; background: #fff; }
.sm-unified-section + .sm-unified-section { border-top: 1px solid var(--sm-soft-line); }
.sm-unified-account > .sm-unified-section:first-child {
    border-radius: 19px 19px 0 0;
}
.sm-unified-account > .sm-unified-section:first-child > .sm-panel__head {
    border-radius: 19px 19px 0 0;
}
.sm-unified-account > .sm-unified-section:last-child,
.sm-unified-account > .sm-unified-section:last-child > .sm-panel__body {
    border-radius: 0 0 19px 19px;
}
.sm-panel__head {
    display: flex;
    min-height: 59px;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    border-bottom: 1px solid var(--sm-soft-line);
    background: #fff;
    padding: 17px 20px 14px;
}
.sm-panel__title { margin: 0; color: #273140; font-size: 16px; font-weight: 760; line-height: 1.3; letter-spacing: -.018em; }
.sm-panel__copy { margin: 4px 0 0; color: #7f8a99; font-size: 10.5px; line-height: 1.55; }
.sm-panel__body { padding: 18px 20px 22px; }

/* Store profile uses the Buyer Account profile-card hierarchy. */
.sm-preview .sm-panel__head { padding: 15px 16px; min-height: 53px; align-items: center; }
.sm-preview .sm-panel__title { font-size: 14px; }
.sm-preview .sm-panel__copy { font-size: 10px; }
.sm-preview-cover {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 7;
    overflow: hidden;
    border-bottom: 1px solid var(--sm-soft-line);
    background: #f4f6f8;
}
.sm-preview-cover img { display: block; width: 100%; height: 100%; object-fit: cover; }
.sm-preview-cover__empty {
    display: grid;
    width: 100%; height: 100%;
    place-items: center;
    padding: 14px;
    color: #919ba8;
    font-size: 10px;
    text-align: center;
}
.sm-preview-body { padding: 19px 16px 18px; }
.sm-preview-identity { display: flex; align-items: center; flex-direction: column; gap: 12px; text-align: center; }
.sm-preview-logo {
    position: relative;
    display: grid;
    width: 110px; height: 110px;
    flex: 0 0 110px;
    place-items: center;
    border: 1px solid #dbe1e8;
    border-radius: 20px;
    background: #f4f6f8;
    color: #9b6b18;
    font-size: 25px;
    font-weight: 750;
    letter-spacing: -.04em;
}
.sm-preview-logo img { display: block; width: 100%; height: 100%; object-fit: cover; border-radius: 19px; }
.sm-preview-logo-edit {
    position: absolute;
    right: -7px; bottom: -7px;
    display: grid;
    width: 34px; height: 34px;
    place-items: center;
    border: 3px solid #fff;
    border-radius: 11px;
    background: var(--sm-gold);
    color: #fff;
    cursor: pointer;
    transition: background-color .15s ease;
}
.sm-preview-logo-edit:hover, .sm-preview-logo-edit:focus-visible { background: var(--sm-gold-dark); outline: none; }
.sm-preview-logo-edit svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linejoin: round; stroke-linecap: round; }
.sm-preview-name { margin: 0; color: #242d38; font-size: 16px; font-weight: 760; line-height: 1.35; overflow-wrap: anywhere; }
.sm-preview-business { margin: 4px 0 0; color: #8993a0; font-size: 10.5px; line-height: 1.45; }
.sm-preview-chip { color: #949fab; font-size: 9px; font-weight: 750; letter-spacing: .04em; white-space: nowrap; }
.sm-status {
    display: table;
    margin: 14px auto 0;
    border: 1px solid #dce7df;
    border-radius: 999px;
    background: #f8faf9;
    padding: 5px 12px;
    color: #4f7d63;
    font-size: 10px;
    font-weight: 750;
}
.sm-status--paused { border-color: #e4d5b2; background: #fff; color: #976b17; }
.sm-preview-desc { margin: 16px 0 0; color: #667280; font-size: 11.5px; line-height: 1.65; overflow-wrap: anywhere; }
.sm-preview-meta { display: grid; gap: 12px; margin-top: 17px; padding-top: 15px; border-top: 1px solid var(--sm-soft-line); }
.sm-preview-meta p { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin: 0; color: #697586; font-size: 10.5px; line-height: 1.5; overflow-wrap: anywhere; }
.sm-preview-meta strong { flex: 0 0 auto; color: #909ba8; font-weight: 600; }
.sm-preview-meta p span { min-width: 0; text-align: right; color: #4e5968; font-weight: 650; }
.sm-preview .sm-panel:last-child .sm-panel__body { padding: 17px; }
.sm-preview .sm-panel:last-child .sm-panel__copy { margin-top: 8px !important; color: #7f8a99; font-size: 10.5px; line-height: 1.65 !important; }

/* Unified form fields and editable sections. */
.sm-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 15px; }
.sm-span-2 { grid-column: 1 / -1; }
.sm-label { display: block; margin-bottom: 7px; color: #40506a; font-size: 11.5px; font-weight: 700; line-height: 1.35; }
.sm-control {
    display: block;
    width: 100%; min-height: 48px;
    border: 1px solid #d7dee7;
    border-radius: 12px;
    background: #fff;
    padding: 0 14px;
    color: #2e3948;
    font: inherit;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.45;
    outline: 0;
    box-shadow: none;
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
}
.sm-control:hover:not([readonly]):not(:disabled) { border-color: #c9d1db; }
.sm-control:focus:not([readonly]):not(:disabled) { border-color: var(--sm-gold); box-shadow: 0 0 0 3px rgba(200,137,18,.08); }
.sm-control::placeholder { color: #a0a9b6; }
textarea.sm-control { min-height: 110px; padding: 13px 14px; resize: vertical; line-height: 1.6; }
.sm-help { margin: 7px 0 0; color: #929ca9; font-size: 10px; line-height: 1.55; }
.sm-field-footer { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; margin-top: 7px; color: #929ca9; font-size: 10px; line-height: 1.55; }
.sm-field-footer span:last-child { color: #75808e; font-variant-numeric: tabular-nums; font-weight: 650; white-space: nowrap; }
.sm-section-edit {
    display: inline-flex;
    min-width: 72px; min-height: 34px;
    align-items: center; justify-content: center; gap: 6px;
    border: 1px solid #e1e6eb;
    border-radius: 9px;
    background: #fff;
    padding: 0 11px;
    color: #596578;
    font: inherit;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    transition: border-color .15s ease, color .15s ease;
}
.sm-section-edit svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.sm-section-edit:hover, .sm-section-edit:focus-visible, .sm-section-edit[aria-pressed='true'] { border-color: #d4b97e; color: var(--sm-gold-dark); outline: none; }
[data-editable-section]:not(.is-editing) .sm-control { border-color: #e3e7ec; background: #f7f9fb; color: #6d7887; }
[data-editable-section]:not(.is-editing) textarea.sm-control { resize: none; }
[data-editable-section].is-editing { background: #fff; }
[data-editable-section].is-editing .sm-control { background: #fff; }

/* Store media stays within the right form, with flat inner tiles. */
.sm-media-grid { display: grid; grid-template-columns: minmax(0,.85fr) minmax(0,1.15fr); align-items: start; gap: 14px; }
.sm-upload-card { min-width: 0; border: 1px solid #e2e7ed; border-radius: 12px; background: #fff; padding: 14px; }
.sm-upload-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 11px; }
.sm-upload-title { margin: 0; color: #344054; font-size: 11.5px; font-weight: 750; }
.sm-media-actions { display: inline-flex; align-items: center; gap: 7px; }
.sm-media-action {
    display: grid;
    width: 32px; height: 32px;
    flex: 0 0 32px;
    place-items: center;
    border: 1px solid #e1e5ea;
    border-radius: 9px;
    background: #fff;
    color: #596273;
    cursor: pointer;
    transition: border-color .15s ease, color .15s ease, background-color .15s ease;
}
.sm-media-action svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.sm-media-action--edit { color: var(--sm-gold-dark); border-color: #e5d5ad; }
.sm-media-action--edit:hover, .sm-media-action--edit:focus-visible { border-color: #d4b97e; color: #875b10; outline: none; }
.sm-media-action--delete { color: #c34d56; border-color: #ead1d4; }
.sm-media-action--delete:hover, .sm-media-action--delete:focus-visible { border-color: #c34d56; background: #fff8f8; outline: none; }
.sm-image { position: relative; overflow: hidden; width: 100%; border: 1px solid #dce2e9; border-radius: 11px; background: #f7f9fb; }
.sm-image--logo { max-height: 220px; aspect-ratio: 1 / 1; }
.sm-image--banner { aspect-ratio: 16 / 6; }
.sm-image img { display: block; width: 100%; height: 100%; object-fit: cover; }
.sm-placeholder { display: grid; width: 100%; height: 100%; place-items: center; background: #f5f7f9; color: #9b6d1c; font-size: 24px; font-weight: 750; }
.sm-banner-placeholder { color: #8c97a4; font-size: 11px; font-weight: 600; text-align: center; padding: 12px; }
.sm-file-input { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
.sm-sr-only { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0,0,0,0) !important; white-space: nowrap !important; border: 0 !important; }

/* Store status custom dropdown — retains current JS and native form select. */
.sm-select { position: relative; width: 100%; }
.sm-native-select { position: absolute !important; width: 1px !important; height: 1px !important; opacity: 0 !important; pointer-events: none !important; overflow: hidden !important; }
.sm-select__button {
    display: flex; width: 100%; min-height: 48px;
    align-items: center; justify-content: space-between; gap: 12px;
    border: 1px solid #d7dee7; border-radius: 12px;
    background: #fff; padding: 0 14px;
    color: #2e3948; font: inherit; font-size: 12px; font-weight: 550;
    text-align: left; cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.sm-select__button:disabled { border-color: #e3e7ec; background: #f7f9fb; color: #6d7887; cursor: default; }
.sm-select__button:focus-visible, .sm-select.is-open .sm-select__button { border-color: var(--sm-gold); outline: 0; box-shadow: 0 0 0 3px rgba(200,137,18,.08); }
.sm-select__value { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sm-select__chevron { width: 17px; height: 17px; flex: 0 0 17px; fill: none; stroke: #68717e; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; transition: transform .15s ease; }
.sm-select.is-open .sm-select__chevron { transform: rotate(180deg); }
.sm-select__menu {
    position: absolute; z-index: 90;
    top: calc(100% + 7px); left: 0; right: 0;
    border: 1px solid #dfe4ea; border-radius: 13px;
    background: #fff; padding: 7px;
    box-shadow: 0 16px 38px rgba(31,41,55,.13), 0 3px 10px rgba(31,41,55,.06);
}
.sm-select__option {
    display: flex; width: 100%; align-items: flex-start; gap: 11px;
    border: 0; border-radius: 9px; background: #fff;
    padding: 11px 12px; color: #37414e;
    font: inherit; text-align: left; cursor: pointer;
}
.sm-select__option:hover, .sm-select__option:focus-visible { background: #f3f5f7; outline: none; }
.sm-select__option.is-selected { background: #f5f6f8; color: #9a6812; }
.sm-select__option strong { display: block; font-size: 12px; font-weight: 750; line-height: 1.35; }
.sm-select__option small { display: block; margin-top: 3px; color: #85909c; font-size: 10px; line-height: 1.45; }
.sm-select__option-dot { width: 7px; height: 7px; flex: 0 0 7px; margin-top: 5px; border-radius: 50%; }
.sm-select__option-dot--open { background: #4f8a65; }
.sm-select__option-dot--paused { background: var(--sm-gold); }

/* Neutral verified-address note; no cream-colored panels. */
.sm-address-readonly { margin-top: 16px; border: 1px solid #e2e7ec; border-left: 3px solid var(--sm-gold); border-radius: 10px; background: #f8fafc; padding: 13px 14px; }
.sm-address-readonly strong { display: block; color: #46505e; font-size: 11px; font-weight: 750; }
.sm-address-readonly p { margin: 5px 0 0; color: #788390; font-size: 10px; line-height: 1.6; }

/* Save / discard remains reactive and visible only on edits. */
.sm-savebar {
    position: sticky; bottom: 12px; z-index: 24;
    display: flex; min-height: 68px;
    align-items: center; justify-content: space-between; gap: 14px;
    border: 1px solid #e1e5ea; border-radius: 13px;
    background: #fff; padding: 12px 15px;
    box-shadow: 0 8px 23px rgba(15,23,42,.085);
}
.sm-savebar__status { display: flex; align-items: center; gap: 11px; min-width: 0; }
.sm-savebar__status p { margin: 0; color: #344054; font-size: 11.5px; font-weight: 750; }
.sm-savebar__status span:not(.sm-unsaved-dot) { display: block; margin-top: 3px; color: #8490a0; font-size: 10px; line-height: 1.5; }
.sm-unsaved-dot { width: 8px; height: 8px; flex: 0 0 8px; border-radius: 50%; background: var(--sm-gold); }
.sm-savebar__actions { display: flex; align-items: center; gap: 9px; flex: 0 0 auto; }
.sm-discard, .sm-save {
    display: inline-flex; min-height: 42px;
    align-items: center; justify-content: center; gap: 7px;
    border: 1px solid #dfe4e9; border-radius: 10px;
    background: #fff; padding: 0 14px; color: #586473;
    font: inherit; font-size: 11px; font-weight: 720;
    cursor: pointer;
    transition: background-color .15s ease, border-color .15s ease, color .15s ease;
}
.sm-discard:hover, .sm-discard:focus-visible { background: #f6f8fa; outline: none; }
.sm-save { min-width: 140px; border-color: var(--sm-gold); background: var(--sm-gold); color: #fff; }
.sm-save:hover, .sm-save:focus-visible { border-color: var(--sm-gold-dark); background: var(--sm-gold-dark); outline: none; }
.sm-save svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.sm-save:disabled, .sm-discard:disabled { opacity: .7; cursor: wait; }
.sm-savebar.is-saving .sm-unsaved-dot { animation: smSavePulse 1s ease-in-out infinite; }
@keyframes smSavePulse { 0%, 100% { opacity: .45; } 50% { opacity: 1; } }

/* Feedback states. */
.sm-alert { margin-bottom: 14px; border: 1px solid; border-radius: 11px; padding: 12px 14px; font-size: 11px; font-weight: 550; line-height: 1.55; }
.sm-alert--ok, .sm-alert--dynamic-ok { border-color: #d5e5db; background: #f7fbf8; color: #4e765e; }
.sm-alert--err, .sm-alert--dynamic-error { border-color: #edd4d4; background: #fff8f8; color: #a74e4e; }

@media (max-width: 1080px) {
    .sm-layout { grid-template-columns: 260px minmax(0,1fr); }
    .sm-media-grid { grid-template-columns: 1fr; }
    .sm-image--logo { max-width: 220px; }
}
@media (max-width: 800px) {
    .sm-layout { grid-template-columns: 1fr; }
    .sm-preview { position: static; }
    .sm-preview-cover { aspect-ratio: 16 / 5; }
    .sm-preview-body { padding-bottom: 20px; }
    .sm-media-grid { grid-template-columns: minmax(0,.85fr) minmax(0,1.15fr); }
}
@media (max-width: 640px) {
    .sm-header { align-items: flex-start; flex-direction: column; }
    .sm-title { font-size: 25px; }
    .sm-header-actions { width: 100%; justify-content: space-between; }
    .sm-header-meta { gap: 8px; }
    .sm-last-updated { white-space: normal; }
    .sm-layout { gap: 14px; margin-top: 14px; }
    .sm-panel { border-radius: 16px; }
    .sm-unified-account { border-radius: 16px; }
    .sm-unified-account > .sm-unified-section:first-child,
    .sm-unified-account > .sm-unified-section:first-child > .sm-panel__head {
        border-radius: 15px 15px 0 0;
    }
    .sm-unified-account > .sm-unified-section:last-child,
    .sm-unified-account > .sm-unified-section:last-child > .sm-panel__body {
        border-radius: 0 0 15px 15px;
    }
    .sm-panel__head { padding: 15px 14px 12px; }
    .sm-panel__body { padding: 15px 14px 18px; }
    .sm-panel__title { font-size: 15px; }
    .sm-grid, .sm-media-grid { grid-template-columns: 1fr; }
    .sm-span-2 { grid-column: auto; }
    .sm-image--logo { max-width: 220px; }
    .sm-field-footer { flex-direction: column; gap: 3px; }
    .sm-savebar { position: sticky; bottom: 8px; align-items: stretch; flex-direction: column; gap: 12px; }
    .sm-savebar__actions { width: 100%; }
    .sm-discard, .sm-save { flex: 1 1 0; }
}
/* Match the Buyer Cart's reduced elevation on all mobile breakpoints. */
@media (max-width: 767px) {
    .store-admin .sm-panel { box-shadow: var(--sm-shadow-mobile); }
}
@media (prefers-reduced-motion: reduce) {
    .store-admin *, .store-admin *::before, .store-admin *::after { transition: none !important; animation: none !important; }
}

@endverbatim
</style>
@endpush

@section('content')
@php
    $storeName = old(
        'store_name',
        $seller->store_name ?: 'SARI Seller Store'
    );

    $initials = collect(preg_split('/\s+/', $storeName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $storeStatus = old(
        'store_status',
        $seller->store_status ?: 'open'
    );

    $registeredAddress = collect([
        $seller->street_address,
        $seller->barangay_name,
        $seller->municipality_name,
        $seller->province_name,
    ])->filter()->implode(', ');

    $pickupAddress = old(
        'pickup_address',
        $seller->pickup_address ?: $registeredAddress
    );

    $storeLogoUrl = $seller->store_logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_logo_path)
        : null;

    $storeBannerUrl = $seller->store_banner_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_banner_path)
        : null;
@endphp



<div class="store-admin">
    <div id="storeDynamicAlert" class="sm-alert" hidden role="status"></div>
    @if(session('success'))
        <div class="sm-alert sm-alert--ok" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sm-alert sm-alert--err" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="sm-header">
        <div>
            <p class="sm-eyebrow">Public Store & Fulfillment</p>
            <h1 class="sm-title">Store <span>Management</span></h1>
       
        </div>

        <div class="sm-header-actions">
            <div class="sm-header-meta">
                <span
                    id="storeHeaderStatus"
                    class="sm-enterprise-status {{ $storeStatus === 'open' ? 'sm-enterprise-status--open' : 'sm-enterprise-status--paused' }}"
                >
                    <span class="sm-enterprise-status__dot"></span>
                    <span id="storeHeaderStatusText">{{ ucfirst($storeStatus) }}</span>
                </span>

                <span id="storeLastUpdated" class="sm-last-updated">
                    Updated {{ $seller->updated_at?->diffForHumans() ?: 'recently' }}
                </span>
            </div>

            <a
                href="{{ route('seller.account') }}"
                class="sm-account-link"
            >
                Account Management
            </a>
        </div>
    </header>

    <div class="sm-layout">
        <aside class="sm-stack sm-preview">
            <section class="sm-panel">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Store Profile</h2>
                        <p class="sm-panel__copy">Buyer-facing store identity and live preview.</p>
                    </div>

                    <span class="sm-preview-chip">Buyer view</span>
                </div>

                <div class="sm-preview-cover">
                    <img
                        id="storeBannerLivePreview"
                        src="{{ $storeBannerUrl ?: '' }}"
                        alt="Store banner"
                        loading="eager"
                        decoding="async"
                        @if(!$storeBannerUrl) hidden @endif
                    >
                    <div
                        id="storeBannerLiveFallback"
                        class="sm-preview-cover__empty"
                        @if($storeBannerUrl) hidden @endif
                    >
                        No Store banner uploaded
                    </div>
                </div>

                <div class="sm-preview-body">
                    <div class="sm-preview-identity">
                        <div class="sm-preview-logo">
                            <img
                                id="storeLogoLivePreview"
                                src="{{ $storeLogoUrl ?: '' }}"
                                alt="Store logo"
                                loading="eager"
                                decoding="async"
                                @if(!$storeLogoUrl) hidden @endif
                            >
                            <span id="storeLogoLiveFallback" @if($storeLogoUrl) hidden @endif>
                                {{ $initials }}
                            </span>
                            <label for="storeLogoInput" class="sm-preview-logo-edit" title="Change store logo" aria-label="Change store logo">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                            </label>
                        </div>

                        <div class="min-w-0">
                            <p id="storeNameLivePreview" class="sm-preview-name">{{ $storeName }}</p>
                            <p class="sm-preview-business">
                                {{ $seller->line_of_business ?: 'SARI Marketplace Seller' }}
                            </p>
                        </div>
                    </div>

                    <span
                        id="storeStatusLivePreview"
                        class="sm-status {{ $storeStatus === 'open' ? 'sm-status--open' : 'sm-status--paused' }}"
                    >
                        {{ ucfirst($storeStatus) }}
                    </span>

                    <p id="storeDescriptionLivePreview" class="sm-preview-desc">
                        {{ old('store_description', $seller->store_description) ?: 'Add a concise Store description so buyers understand what your shop offers.' }}
                    </p>

                    <div class="sm-preview-meta">
                        <p>
                            <strong>Public phone:</strong>
                            <span id="storePhoneLivePreview">
                                {{ old('store_phone', $seller->store_phone ?: $seller->contact_no) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Public email:</strong>
                            <span id="storeEmailLivePreview">
                                {{ old('store_public_email', $seller->store_public_email ?: $seller->email) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Pickup:</strong>
                            <span id="pickupAddressLivePreview">{{ $pickupAddress }}</span>
                        </p>
                        <p>
                            <strong>Last saved:</strong>
                            <span id="storePreviewLastSaved">
                                {{ $seller->updated_at?->diffForHumans() ?: 'Recently' }}
                            </span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="sm-panel">
                <div class="sm-panel__body">
                    <p class="sm-panel__title">Store vs. Account identity</p>
                    <p class="sm-panel__copy" style="margin-top:7px;line-height:1.65;">
                        Store logo, banner, public contacts, availability, and pickup details are managed here.
                        Personal identity, login security, and verified registration information remain in Account Management.
                    </p>
                </div>
            </section>
        </aside>

        <form
            method="POST"
            action="{{ route('seller.store.update') }}"
            enctype="multipart/form-data"
            class="sm-stack"
            id="sellerStoreForm"
        >
            @csrf
            @method('PATCH')

            <div class="sm-panel sm-unified-account">
            <section class="sm-unified-section">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Store Media</h2>
                        <p class="sm-panel__copy">Upload a Store logo and optional banner for your marketplace identity.</p>
                    </div>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-media-grid">
                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store logo</p>

                                <div class="sm-media-actions" aria-label="Store logo actions">
                                    <label
                                        for="storeLogoInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store logo"
                                        aria-label="Edit or replace Store logo"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store logo</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="logo"
                                        title="Remove Store logo"
                                        aria-label="Remove Store logo"
                                        @if(!$seller->store_logo_path) hidden @endif
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
                            </div>

                            <div class="sm-image sm-image--logo">
                                <img
                                    id="storeLogoPreview"
                                    src="{{ $storeLogoUrl ?: '' }}"
                                    alt="Store logo preview"
                                    loading="eager"
                                    decoding="async"
                                    fetchpriority="high"
                                    @if(!$storeLogoUrl) hidden @endif
                                >
                                <div
                                    id="storeLogoFallback"
                                    class="sm-placeholder"
                                    @if($storeLogoUrl) hidden @endif
                                >
                                    {{ $initials }}
                                </div>
                            </div>

                            <input
                                id="storeLogoInput"
                                class="sm-file-input"
                                type="file"
                                name="store_logo"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreLogo" type="hidden" name="remove_store_logo" value="0">

                            <p class="sm-help">Square image recommended. JPG, PNG, or WEBP · max 5 MB.</p>
                        </div>

                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store banner</p>

                                <div class="sm-media-actions" aria-label="Store banner actions">
                                    <label
                                        for="storeBannerInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store banner"
                                        aria-label="Edit or replace Store banner"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store banner</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="banner"
                                        title="Remove Store banner"
                                        aria-label="Remove Store banner"
                                        @if(!$seller->store_banner_path) hidden @endif
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
                            </div>

                            <div class="sm-image sm-image--banner">
                                <img
                                    id="storeBannerPreview"
                                    src="{{ $storeBannerUrl ?: '' }}"
                                    alt="Store banner preview"
                                    loading="eager"
                                    decoding="async"
                                    @if(!$storeBannerUrl) hidden @endif
                                >
                                <div
                                    id="storeBannerFallback"
                                    class="sm-placeholder sm-banner-placeholder"
                                    @if($storeBannerUrl) hidden @endif
                                >
                                    Optional storefront banner
                                </div>
                            </div>

                            <input
                                id="storeBannerInput"
                                class="sm-file-input"
                                type="file"
                                name="store_banner"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreBanner" type="hidden" name="remove_store_banner" value="0">

                            <p class="sm-help">Wide image recommended. JPG, PNG, or WEBP · max 8 MB.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-unified-section" data-editable-section="identity">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Public Store Information</h2>
                        <p class="sm-panel__copy">Buyer-facing Store details and public contact information.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="identity"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeName">Store name</label>
                            <input
                                id="storeName"
                                data-section-control
                                name="store_name"
                                required
                                maxlength="150"
                                value="{{ old('store_name', $seller->store_name) }}"
                                class="sm-control"
                            >
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="storeDescription">Store description</label>
                            <textarea
                                id="storeDescription"
                                data-section-control
                                name="store_description"
                                rows="5"
                                maxlength="2000"
                                class="sm-control"
                            >{{ old('store_description', $seller->store_description) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Keep it concise and buyer-focused.</span>
                                <span id="storeDescriptionCounter">0 / 2000</span>
                            </div>
                        </div>

                        <div>
                            <label class="sm-label" for="storePhone">Public phone</label>
                            <input
                                id="storePhone"
                                data-section-control
                                name="store_phone"
                                maxlength="40"
                                value="{{ old('store_phone', $seller->store_phone ?: $seller->contact_no) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change your verified account contact number.</p>
                        </div>

                        <div>
                            <label class="sm-label" for="storePublicEmail">Public email</label>
                            <input
                                id="storePublicEmail"
                                data-section-control
                                name="store_public_email"
                                type="email"
                                maxlength="255"
                                value="{{ old('store_public_email', $seller->store_public_email ?: $seller->email) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change the email used to sign in.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-unified-section" data-editable-section="fulfillment">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Availability & Fulfillment</h2>
                        <p class="sm-panel__copy">Selling status and operational pickup details used for fulfillment.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="fulfillment"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeStatus">Store status</label>
                            <div class="sm-select" data-custom-select>
                                <select
                                    id="storeStatus"
                                    data-section-control
                                    name="store_status"
                                    required
                                    class="sm-native-select"
                                    aria-hidden="true"
                                    tabindex="-1"
                                >
                                    <option value="open" @selected($storeStatus === 'open')>
                                        Open — accepting new orders
                                    </option>
                                    <option value="paused" @selected($storeStatus === 'paused')>
                                        Paused — temporarily stop new orders
                                    </option>
                                </select>

                                <button
                                    type="button"
                                    class="sm-select__button"
                                    data-custom-select-button
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    disabled
                                >
                                    <span class="sm-select__value" data-custom-select-value>
                                        {{ $storeStatus === 'paused'
                                            ? 'Paused — temporarily stop new orders'
                                            : 'Open — accepting new orders' }}
                                    </span>

                                    <svg class="sm-select__chevron" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>

                                <div
                                    class="sm-select__menu"
                                    data-custom-select-menu
                                    role="listbox"
                                    aria-label="Store status"
                                    hidden
                                >
                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'open' ? 'is-selected' : '' }}"
                                        data-custom-select-option="open"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'open' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--open"></span>
                                        <span>
                                            <strong>Open</strong>
                                            <small>Accepting new orders</small>
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'paused' ? 'is-selected' : '' }}"
                                        data-custom-select-option="paused"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'paused' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--paused"></span>
                                        <span>
                                            <strong>Paused</strong>
                                            <small>Temporarily stop new orders</small>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupAddress">Pickup address</label>
                            <textarea
                                id="pickupAddress" data-section-control
                                name="pickup_address"
                                required
                                rows="4"
                                maxlength="1000"
                                class="sm-control"
                            >{{ $pickupAddress }}</textarea>
                            <p class="sm-help">This is the operational pickup address used for fulfillment.</p>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupInstructions">Pickup instructions</label>
                            <textarea
                                id="pickupInstructions" data-section-control
                                name="pickup_instructions"
                                rows="4"
                                maxlength="1200"
                                placeholder="Gate, landmark, receiving hours, contact instructions, etc."
                                class="sm-control"
                            >{{ old('pickup_instructions', $seller->pickup_instructions) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Landmarks, receiving hours, gate instructions, or contact notes.</span>
                                <span id="pickupInstructionsCounter">0 / 1200</span>
                            </div>
                        </div>
                    </div>

                    <div class="sm-address-readonly">
                        <strong>Verified registration address — read only</strong>
                        <p>{{ $registeredAddress ?: 'No verified registration address is stored.' }}</p>
                        <p>
                            Store Management does not modify the address verified during Seller registration.
                        </p>
                    </div>
                </div>
            </section>

            </div>

            <div class="sm-savebar" id="storeSaveBar" hidden>
                <div class="sm-savebar__status">
                    <span class="sm-unsaved-dot" aria-hidden="true"></span>
                    <div>
                        <p id="storeSaveBarTitle">Unsaved changes</p>
                        <span id="storeSaveState" aria-live="polite">
                            Review your changes before saving.
                        </span>
                    </div>
                </div>

                <div class="sm-savebar__actions">
                    <button
                        type="button"
                        class="sm-discard"
                        id="storeDiscardButton"
                    >
                        Discard
                    </button>

                    <button type="submit" class="sm-save" id="storeSaveButton">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12.5 9.2 17 19 7"></path>
                        </svg>
                        <span id="storeSaveButtonText">Save changes</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootStoreManagement() {
        const form = document.getElementById('sellerStoreForm');
        if (!form || form.dataset.initialized === '1') return;
        form.dataset.initialized = '1';

        window.__SARI_SELLER_STORE_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_STORE_LIFECYCLE__ = lifecycle;

        const byId = (id) => document.getElementById(id);

        const logoInput = byId('storeLogoInput');
        const bannerInput = byId('storeBannerInput');
        const logoPreview = byId('storeLogoPreview');
        const logoFallback = byId('storeLogoFallback');
        const logoLive = byId('storeLogoLivePreview');
        const logoLiveFallback = byId('storeLogoLiveFallback');
        const bannerPreview = byId('storeBannerPreview');
        const bannerFallback = byId('storeBannerFallback');
        const bannerLive = byId('storeBannerLivePreview');
        const bannerLiveFallback = byId('storeBannerLiveFallback');
        const removeLogo = byId('removeStoreLogo');
        const removeBanner = byId('removeStoreBanner');
        const saveButton = byId('storeSaveButton');
        const saveButtonText = byId('storeSaveButtonText');
        const saveState = byId('storeSaveState');
        const saveBar = byId('storeSaveBar');
        const saveBarTitle = byId('storeSaveBarTitle');
        const discardButton = byId('storeDiscardButton');
        const dynamicAlert = byId('storeDynamicAlert');
        const lastUpdated = byId('storeLastUpdated');
        const previewLastSaved = byId('storePreviewLastSaved');
        const headerStatus = byId('storeHeaderStatus');
        const headerStatusText = byId('storeHeaderStatusText');

        const trackedFields = [
            'storeName',
            'storeDescription',
            'storePhone',
            'storePublicEmail',
            'storeStatus',
            'pickupAddress',
            'pickupInstructions',
        ];

        const objectUrls = {
            logo: null,
            banner: null,
        };

        const revokeObjectUrl = (key) => {
            if (objectUrls[key]) {
                URL.revokeObjectURL(objectUrls[key]);
                objectUrls[key] = null;
            }
        };

        const setDynamicAlert = (message = '', type = 'ok') => {
            if (!dynamicAlert) return;

            if (!message) {
                dynamicAlert.hidden = true;
                dynamicAlert.textContent = '';
                dynamicAlert.className = 'sm-alert';
                return;
            }

            dynamicAlert.textContent = message;
            dynamicAlert.className =
                `sm-alert ${type === 'error'
                    ? 'sm-alert--dynamic-error'
                    : 'sm-alert--dynamic-ok'}`;
            dynamicAlert.hidden = false;
        };

        const showImage = (
            key,
            file,
            preview,
            fallback,
            live,
            liveFallback,
            removeInput
        ) => {
            if (!file) return;

            revokeObjectUrl(key);

            const url = URL.createObjectURL(file);
            objectUrls[key] = url;

            preview.src = url;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = url;
            live.hidden = false;
            liveFallback.hidden = true;

            removeInput.value = '0';
        };

        const previewQueue = new Map();
        let previewFrame = 0;

        const queuePreview = (output, value) => {
            if (!output) return;

            previewQueue.set(output, value);

            if (previewFrame) return;

            previewFrame = requestAnimationFrame(() => {
                previewQueue.forEach((value, element) => {
                    element.textContent = value;
                });

                previewQueue.clear();
                previewFrame = 0;
            });
        };

        const syncCounter = (input, output, max) => {
            if (!input || !output) return;

            output.textContent =
                `${String(input.value || '').length} / ${max}`;
        };

        const description = byId('storeDescription');
        const descriptionCounter = byId('storeDescriptionCounter');
        const pickupInstructions = byId('pickupInstructions');
        const pickupCounter = byId('pickupInstructionsCounter');

        syncCounter(description, descriptionCounter, 2000);
        syncCounter(pickupInstructions, pickupCounter, 1200);

        const setSectionEditing = (section, editing) => {
            if (!section) return;

            section.classList.toggle('is-editing', editing);

            const button = section.querySelector('[data-edit-section]');
            const label = button?.querySelector('[data-edit-label]');

            button?.setAttribute(
                'aria-pressed',
                editing ? 'true' : 'false'
            );

            if (label) {
                label.textContent = editing ? 'Done' : 'Edit';
            }

            section
                .querySelectorAll('[data-section-control]')
                .forEach((control) => {
                    if (
                        control instanceof HTMLInputElement
                        || control instanceof HTMLTextAreaElement
                    ) {
                        control.readOnly = !editing;
                    }

                    if (control instanceof HTMLSelectElement) {
                        control.dataset.enterpriseLocked =
                            editing ? '0' : '1';
                    }
                });

            section
                .querySelectorAll('[data-custom-select-button]')
                .forEach((button) => {
                    button.disabled = !editing;

                    if (!editing) {
                        const customSelect =
                            button.closest('[data-custom-select]');

                        customSelect?.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');

                        const menu =
                            customSelect?.querySelector(
                                '[data-custom-select-menu]'
                            );

                        if (menu) {
                            menu.hidden = true;
                        }
                    }
                });
        };

        document
            .querySelectorAll('[data-editable-section]')
            .forEach((section) => {
                setSectionEditing(section, false);
            });

        document
            .querySelectorAll('[data-edit-section]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const section =
                        button.closest('[data-editable-section]');

                    const editing =
                        !section?.classList.contains('is-editing');

                    setSectionEditing(section, editing);

                    if (editing) {
                        section
                            ?.querySelector('[data-section-control]')
                            ?.focus({ preventScroll: true });
                    }
                });
            });

        const status = byId('storeStatus');
        const statusOutput = byId('storeStatusLivePreview');
        const customStatus = status?.closest('[data-custom-select]');
        const customStatusButton =
            customStatus?.querySelector('[data-custom-select-button]');
        const customStatusMenu =
            customStatus?.querySelector('[data-custom-select-menu]');
        const customStatusValue =
            customStatus?.querySelector('[data-custom-select-value]');
        const customStatusOptions = Array.from(
            customStatus?.querySelectorAll(
                '[data-custom-select-option]'
            ) || []
        );

        const statusLabels = {
            open: 'Open — accepting new orders',
            paused: 'Paused — temporarily stop new orders',
        };

        const closeStatusMenu = () => {
            if (!customStatus || !customStatusButton || !customStatusMenu) {
                return;
            }

            customStatus.classList.remove('is-open');
            customStatusButton.setAttribute('aria-expanded', 'false');
            customStatusMenu.hidden = true;
        };

        const openStatusMenu = () => {
            if (
                !customStatus
                || !customStatusButton
                || !customStatusMenu
                || customStatusButton.disabled
            ) {
                return;
            }

            customStatus.classList.add('is-open');
            customStatusButton.setAttribute('aria-expanded', 'true');
            customStatusMenu.hidden = false;

            const selected =
                customStatusMenu.querySelector('.is-selected');

            requestAnimationFrame(() => {
                selected?.focus({ preventScroll: true });
            });
        };

        const syncCustomStatusSelect = () => {
            const value = String(status?.value || 'open');

            if (customStatusValue) {
                customStatusValue.textContent =
                    statusLabels[value] || statusLabels.open;
            }

            customStatusOptions.forEach((option) => {
                const selected =
                    option.dataset.customSelectOption === value;

                option.classList.toggle('is-selected', selected);
                option.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        };

        customStatusButton?.addEventListener('click', () => {
            if (customStatus?.classList.contains('is-open')) {
                closeStatusMenu();
            } else {
                openStatusMenu();
            }
        });

        customStatusOptions.forEach((option) => {
            option.addEventListener('click', () => {
                const value =
                    option.dataset.customSelectOption;

                if (!value || !status) {
                    return;
                }

                status.value = value;
                status.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                syncCustomStatusSelect();
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            });
        });

        customStatus?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            }

            if (
                (event.key === 'Enter' || event.key === ' ')
                && event.target === customStatusButton
            ) {
                event.preventDefault();

                if (customStatus?.classList.contains('is-open')) {
                    closeStatusMenu();
                } else {
                    openStatusMenu();
                }
            }
        });

        document.addEventListener('click', (event) => {
            if (
                customStatus
                && !customStatus.contains(event.target)
            ) {
                closeStatusMenu();
            }
        }, { signal });

        const syncStatusUI = () => {
            const value = String(status?.value || 'open');
            const label =
                value.charAt(0).toUpperCase() + value.slice(1);
            const open = value === 'open';

            queuePreview(statusOutput, label);
            queuePreview(headerStatusText, label);

            statusOutput?.classList.toggle(
                'sm-status--open',
                open
            );
            statusOutput?.classList.toggle(
                'sm-status--paused',
                !open
            );

            headerStatus?.classList.toggle(
                'sm-enterprise-status--open',
                open
            );
            headerStatus?.classList.toggle(
                'sm-enterprise-status--paused',
                !open
            );
        };

        const bindings = [
            ['storeName', 'storeNameLivePreview', 'SARI Seller Store'],
            [
                'storeDescription',
                'storeDescriptionLivePreview',
                'Add a concise Store description so buyers understand what your shop offers.'
            ],
            ['storePhone', 'storePhoneLivePreview', 'Not provided'],
            ['storePublicEmail', 'storeEmailLivePreview', 'Not provided'],
            ['pickupAddress', 'pickupAddressLivePreview', 'Not provided'],
        ];

        const syncAllPreviewText = () => {
            bindings.forEach(([inputId, outputId, fallback]) => {
                const input = byId(inputId);
                const output = byId(outputId);

                if (!input || !output) return;

                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );
            });

            syncStatusUI();
            syncCustomStatusSelect();
            syncCounter(description, descriptionCounter, 2000);
            syncCounter(pickupInstructions, pickupCounter, 1200);
        };

        bindings.forEach(([inputId, outputId, fallback]) => {
            const input = byId(inputId);
            const output = byId(outputId);

            if (!input || !output) return;

            input.addEventListener('input', () => {
                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );

                if (input === description) {
                    syncCounter(
                        description,
                        descriptionCounter,
                        2000
                    );
                }

                if (input === pickupInstructions) {
                    syncCounter(
                        pickupInstructions,
                        pickupCounter,
                        1200
                    );
                }
            });
        });

        status?.addEventListener('change', () => {
            syncStatusUI();
            syncCustomStatusSelect();
        });

        const mediaDeleteButton = (type) =>
            document.querySelector(
                `[data-remove-media="${type}"]`
            );

        let savedMedia = {
            logo:
                logoPreview && !logoPreview.hidden
                    ? logoPreview.src
                    : '',
            banner:
                bannerPreview && !bannerPreview.hidden
                    ? bannerPreview.src
                    : '',
        };

        const captureState = () => {
            const fields = {};

            trackedFields.forEach((id) => {
                fields[id] = String(byId(id)?.value || '');
            });

            return fields;
        };

        let savedState = captureState();

        const hasUnsavedChanges = () => {
            const current = captureState();

            const fieldsChanged =
                JSON.stringify(current)
                !== JSON.stringify(savedState);

            const mediaChanged =
                Boolean(logoInput?.files?.length)
                || Boolean(bannerInput?.files?.length)
                || removeLogo?.value === '1'
                || removeBanner?.value === '1';

            return fieldsChanged || mediaChanged;
        };

        const updateDirtyState = () => {
            if (!saveBar) return;

            saveBar.hidden = !hasUnsavedChanges();

            if (!saveBar.hidden) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Unsaved changes';

                if (!form.dataset.saving) {
                    saveState.textContent =
                        'Review your changes before saving.';
                }
            }
        };

        trackedFields.forEach((id) => {
            const control = byId(id);

            control?.addEventListener(
                control instanceof HTMLSelectElement
                    ? 'change'
                    : 'input',
                updateDirtyState
            );
        });

        logoInput?.addEventListener('change', () => {
            const file = logoInput.files?.[0];

            showImage(
                'logo',
                file,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback,
                removeLogo
            );

            if (file) {
                mediaDeleteButton('logo')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        bannerInput?.addEventListener('change', () => {
            const file = bannerInput.files?.[0];

            showImage(
                'banner',
                file,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback,
                removeBanner
            );

            if (file) {
                mediaDeleteButton('banner')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        document
            .querySelectorAll('[data-remove-media]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const type = button.dataset.removeMedia;

                    if (type === 'logo') {
                        revokeObjectUrl('logo');
                        logoInput.value = '';
                        removeLogo.value = '1';
                        logoPreview.hidden = true;
                        logoFallback.hidden = false;
                        logoLive.hidden = true;
                        logoLiveFallback.hidden = false;
                    }

                    if (type === 'banner') {
                        revokeObjectUrl('banner');
                        bannerInput.value = '';
                        removeBanner.value = '1';
                        bannerPreview.hidden = true;
                        bannerFallback.hidden = false;
                        bannerLive.hidden = true;
                        bannerLiveFallback.hidden = false;
                    }

                    button.hidden = true;
                    updateDirtyState();
                });
            });

        const restoreMedia = (
            type,
            url,
            preview,
            fallback,
            live,
            liveFallback
        ) => {
            revokeObjectUrl(type);

            const exists = Boolean(url);

            preview.hidden = !exists;
            fallback.hidden = exists;
            live.hidden = !exists;
            liveFallback.hidden = exists;

            if (exists) {
                preview.src = url;
                live.src = url;
            }

            mediaDeleteButton(type).hidden = !exists;
        };

        discardButton?.addEventListener('click', () => {
            trackedFields.forEach((id) => {
                const control = byId(id);
                if (!control) return;

                control.value = savedState[id] ?? '';
            });

            logoInput.value = '';
            bannerInput.value = '';
            removeLogo.value = '0';
            removeBanner.value = '0';

            restoreMedia(
                'logo',
                savedMedia.logo,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback
            );

            restoreMedia(
                'banner',
                savedMedia.banner,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback
            );

            document
                .querySelectorAll('[data-editable-section]')
                .forEach((section) => {
                    setSectionEditing(section, false);
                });

            setDynamicAlert();
            syncAllPreviewText();
            updateDirtyState();
        });

        const loadBitmap = async (file) => {
            if ('createImageBitmap' in window) {
                return createImageBitmap(file);
            }

            return new Promise((resolve, reject) => {
                const image = new Image();
                const url = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };

                image.onerror = () => {
                    URL.revokeObjectURL(url);
                    reject(
                        new Error('Image could not be decoded.')
                    );
                };

                image.src = url;
            });
        };

        const optimizeImage = async (
            file,
            {
                maxWidth,
                maxHeight,
                quality = 0.86,
                minBytes = 450000,
            }
        ) => {
            if (!file || file.size < minBytes) {
                return file;
            }

            try {
                const bitmap = await loadBitmap(file);
                const sourceWidth =
                    bitmap.width || bitmap.naturalWidth;
                const sourceHeight =
                    bitmap.height || bitmap.naturalHeight;

                if (!sourceWidth || !sourceHeight) {
                    bitmap.close?.();
                    return file;
                }

                const scale = Math.min(
                    1,
                    maxWidth / sourceWidth,
                    maxHeight / sourceHeight
                );

                const width = Math.max(
                    1,
                    Math.round(sourceWidth * scale)
                );

                const height = Math.max(
                    1,
                    Math.round(sourceHeight * scale)
                );

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext(
                    '2d',
                    { alpha: true }
                );

                if (!context) {
                    bitmap.close?.();
                    return file;
                }

                context.drawImage(
                    bitmap,
                    0,
                    0,
                    width,
                    height
                );

                bitmap.close?.();

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(
                        resolve,
                        'image/webp',
                        quality
                    );
                });

                if (!blob || blob.size >= file.size) {
                    return file;
                }

                const baseName =
                    file.name.replace(/\.[^.]+$/, '');

                return new File(
                    [blob],
                    `${baseName}.webp`,
                    {
                        type: 'image/webp',
                        lastModified: Date.now(),
                    }
                );
            } catch (error) {
                return file;
            }
        };

        const firstErrorMessage = (payload) => {
            const errors = payload?.errors || {};

            for (const value of Object.values(errors)) {
                if (Array.isArray(value) && value[0]) {
                    return String(value[0]);
                }
            }

            return String(
                payload?.message
                || 'Store changes could not be saved.'
            );
        };

        const applySavedImage = (
            url,
            preview,
            fallback,
            live,
            liveFallback,
            key
        ) => {
            revokeObjectUrl(key);

            if (!url) {
                preview.hidden = true;
                fallback.hidden = false;
                live.hidden = true;
                liveFallback.hidden = false;
                savedMedia[key] = '';
                mediaDeleteButton(key).hidden = true;
                return;
            }

            const freshUrl =
                `${url}${url.includes('?') ? '&' : '?'}v=${Date.now()}`;

            preview.src = freshUrl;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = freshUrl;
            live.hidden = false;
            liveFallback.hidden = true;

            savedMedia[key] = freshUrl;
            mediaDeleteButton(key).hidden = false;
        };

        form.addEventListener('submit', async (event) => {
            if (
                !window.fetch
                || !window.FormData
                || form.dataset.saving === '1'
            ) {
                return;
            }

            event.preventDefault();

            if (!hasUnsavedChanges()) {
                return;
            }

            form.dataset.saving = '1';
            saveButton.disabled = true;
            discardButton.disabled = true;
            saveButtonText.textContent = 'Saving…';
            saveBar.hidden = false;
            saveBar.classList.add('is-saving');
            saveBarTitle.textContent = 'Saving changes';
            saveState.textContent = 'Optimizing media…';
            setDynamicAlert();

            try {
                const formData = new FormData(form);

                const [
                    optimizedLogo,
                    optimizedBanner,
                ] = await Promise.all([
                    optimizeImage(
                        logoInput?.files?.[0],
                        {
                            maxWidth: 1200,
                            maxHeight: 1200,
                            quality: 0.88,
                        }
                    ),
                    optimizeImage(
                        bannerInput?.files?.[0],
                        {
                            maxWidth: 2200,
                            maxHeight: 1200,
                            quality: 0.86,
                        }
                    ),
                ]);

                if (optimizedLogo) {
                    formData.set(
                        'store_logo',
                        optimizedLogo,
                        optimizedLogo.name
                    );
                }

                if (optimizedBanner) {
                    formData.set(
                        'store_banner',
                        optimizedBanner,
                        optimizedBanner.name
                    );
                }

                saveState.textContent = 'Saving securely…';

                const response = await fetch(
                    form.action,
                    {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    }
                );

                const contentType =
                    response.headers.get('content-type') || '';

                const payload =
                    contentType.includes('application/json')
                        ? await response.json()
                        : {
                            message:
                                'The server returned an unexpected response.'
                        };

                if (!response.ok) {
                    throw new Error(
                        firstErrorMessage(payload)
                    );
                }

                const store = payload.store || {};

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_logo_url'
                    )
                ) {
                    applySavedImage(
                        store.store_logo_url,
                        logoPreview,
                        logoFallback,
                        logoLive,
                        logoLiveFallback,
                        'logo'
                    );
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_banner_url'
                    )
                ) {
                    applySavedImage(
                        store.store_banner_url,
                        bannerPreview,
                        bannerFallback,
                        bannerLive,
                        bannerLiveFallback,
                        'banner'
                    );
                }

                logoInput.value = '';
                bannerInput.value = '';
                removeLogo.value = '0';
                removeBanner.value = '0';

                savedState = captureState();

                document
                    .querySelectorAll('[data-editable-section]')
                    .forEach((section) => {
                        setSectionEditing(section, false);
                    });

                syncStatusUI();

                const justNow = 'Updated just now';
                const previewNow = 'Just now';

                if (lastUpdated) {
                    lastUpdated.textContent = justNow;
                }

                if (previewLastSaved) {
                    previewLastSaved.textContent = previewNow;
                }

                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Changes saved';
                saveState.textContent =
                    'Your Store settings are up to date.';

                setDynamicAlert(
                    payload.message
                    || 'Store settings saved successfully.',
                    'ok'
                );

                window.setTimeout(() => {
                    updateDirtyState();
                }, 900);
            } catch (error) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Save failed';
                saveState.textContent =
                    'Review the error above and try again.';

                setDynamicAlert(
                    error?.message
                    || 'Store changes could not be saved.',
                    'error'
                );
            } finally {
                form.dataset.saving = '0';
                saveButton.disabled = false;
                discardButton.disabled = false;
                saveButtonText.textContent = 'Save changes';
            }
        });

        const cleanup = () => {
            revokeObjectUrl('logo');
            revokeObjectUrl('banner');

            if (previewFrame) {
                cancelAnimationFrame(previewFrame);
            }

            form.dataset.initialized = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_STORE_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_STORE_LIFECYCLE__ = null;
            }
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true, signal }
        );

        window.addEventListener('beforeunload', cleanup, { once: true, signal });

        syncCustomStatusSelect();
        syncAllPreviewText();
        updateDirtyState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            bootStoreManagement,
            { once: true }
        );
    } else {
        bootStoreManagement();
    }
})();
</script>
@endpush
