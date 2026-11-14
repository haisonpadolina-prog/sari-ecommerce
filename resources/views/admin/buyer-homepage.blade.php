@extends('layouts.admin')

@section('title', 'Buyer Homepage Studio — SARI Admin')
@section('page-title', 'Buyer Homepage Studio')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/buyer-homepage.css') }}?v=6">

<style>
    /* ============================================================
       SARI ADMIN — BUYER HOMEPAGE STUDIO  (WPS-style, compact)

       Layout:  [ toolbar ........................ save / publish ]
                [ slides | banner canvas (hero)                   ]
                [        | status bar: info ........ zoom         ]

       No outer container. The layout background is the workspace.
       Only 3 panels have a card: toolbar, slides, status bar.

       VISUAL ONLY: every ID, name, data attribute, form field and the
       editor script are unchanged. Banner canvas internals (stage,
       drag handles, copy/button positions) are not restyled so the
       drag / zoom math keeps working.
       ============================================================ */

    .bhm-page {
        --sh-gold: #c88912;
        --sh-gold-dark: #a96f0e;
        --sh-gold-text: var(--sari-dark-4b8b580e58, #89580c);
        --sh-ink: var(--sari-dark-e23e64dd1e, #20252b);
        --sh-text: var(--sari-dark-e23e64dd1e, #2e343b);
        --sh-body: #29323b;
        --sh-muted: var(--sari-dark-a63aa16d7c, #707b86);
        --sh-border: var(--sari-dark-d8c2716225, #e5e7eb);
        --sh-control-border: var(--sari-dark-d8c2716225, #dfe3e8);
        --sh-divider: var(--sari-dark-d8c2716225, #eceef1);
        --sh-radius: 14px;
        --sh-ctl: 32px;
        --sh-shadow:
            0 1px 2px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)),
            0 6px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06));

        width: 100%;
        max-width: 1600px;
        margin: 0 auto !important;
        padding: 0 0 24px !important;
        color: var(--sh-text);
        font-family: 'Poppins', 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    .bhm-page *,
    .bhm-page *::before,
    .bhm-page *::after { box-sizing: border-box; }

    /* ---------------- Workspace: no card ---------------- */
    .bhm-page .bhm-workspace {
        display: flex !important;
        flex-direction: column;
        gap: 12px !important;
        overflow: visible;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* The three cards */
    .bhm-page .bhm-ribbon,
    .bhm-page .bhm-filmstrip,
    .bhm-page .bhm-stage-footer,
    .bhm-page .bhm-alert {
        border: 1px solid var(--sh-border) !important;
        border-radius: var(--sh-radius) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--sh-shadow) !important;
    }

    /* ---------------- Alerts ---------------- */
    .bhm-page .bhm-alert {
        margin: 0 !important;
        padding: 10px 16px !important;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }
    .bhm-page .bhm-alert--success { border-color: var(--sari-dark-d8c2716225, #d5e6da) !important; background: var(--sari-dark-950baec030, #f4faf6) !important; color: var(--sari-dark-e23e64dd1e, #36805a) !important; }
    .bhm-page .bhm-alert--error { border-color: var(--sari-dark-d8c2716225, #f1d3d3) !important; background: var(--sari-dark-950baec030, #fff5f5) !important; color: var(--sari-dark-2a778702e9, #b04545) !important; }
    .bhm-page .bhm-alert ul { margin: 4px 0 0; padding-left: 18px; font-weight: 500; }

    /* ---------------- Toolbar (one compact row, wraps on small screens) ---------------- */
    .bhm-page .bhm-ribbon {
        position: static !important;
        display: flex !important;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 0 !important;
        margin: 0 !important;
        padding: 9px 10px !important;
    }

    .bhm-page .bhm-ribbon-group {
        display: flex !important;
        flex: 0 0 auto;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        width: auto !important;
        min-width: 0;
        margin: 0 !important;
        padding: 0 12px !important;
        border: 0 !important;
        border-right: 1px solid var(--sh-divider) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Captions are tooltips-only now (WPS style) */
    .bhm-page .bhm-caption { display: none !important; }

    .bhm-ribbon-row,
    .bhm-page .bhm-inline-numbers,
    .bhm-page .bhm-color-tools,
    .bhm-page .bhm-more-actions {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
    }
    .bhm-page .bhm-color-tools { gap: 12px; }
    .bhm-page .bhm-more-actions { flex-wrap: wrap; }

    .bhm-page .bhm-keyhint { display: none; }

    /* Right end: save status + view + publish */
    .bhm-page .bhm-ribbon-group--publish {
        margin-left: auto !important;
        padding-right: 4px !important;
        border-right: 0 !important;
        border-left: 1px solid var(--sh-divider) !important;
        padding-left: 14px !important;
    }

    .bhm-page .bhm-save-status {
        display: inline-flex;
        height: 26px;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--sari-dark-d8c2716225, #d6e9dc);
        border-radius: 999px;
        background: var(--sari-dark-4d21eb669b, #eef8f1);
        padding: 0 10px;
        color: var(--sari-dark-e23e64dd1e, #36805a);
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .bhm-page .bhm-save-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
    }
    .bhm-page .bhm-save-status.is-dirty,
    .bhm-page .bhm-save-status.is-unsaved,
    .bhm-page .bhm-save-status.has-changes,
    .bhm-page .bhm-save-status[data-state="dirty"] {
        border-color: var(--sari-dark-d8c2716225, #efd9b0);
        background: var(--sari-dark-4d21eb669b, #fff8e9);
        color: var(--sari-dark-f382da8334, #a87019);
    }

    .bhm-page .bhm-view-link {
        display: inline-flex;
        height: var(--sh-ctl);
        align-items: center;
        border: 1px solid var(--sh-control-border);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 12px;
        color: var(--sari-dark-9b2ca2d764, #865709);
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }
    .bhm-page .bhm-view-link:hover,
    .bhm-page .bhm-view-link:focus-visible {
        outline: none;
        border-color: var(--sari-dark-d8c2716225, #eadfc9);
        background: var(--sari-dark-950baec030, #fffaf1);
        color: var(--sh-gold);
    }

    .bhm-page .bhm-publish {
        display: inline-flex;
        height: var(--sh-ctl);
        align-items: center;
        justify-content: center;
        border: 1px solid #d79a28 !important;
        border-radius: 8px !important;
        background: var(--sh-gold) !important;
        padding: 0 16px;
        color: #fff !important;
        font-family: inherit;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: 0 4px 10px var(--sari-dark-b5c2a2270e, rgba(200, 137, 18, .16)) !important;
        cursor: pointer;
        transition: background-color .16s ease, border-color .16s ease;
    }
    .bhm-page .bhm-publish:hover,
    .bhm-page .bhm-publish:focus-visible {
        outline: none;
        border-color: var(--sh-gold-dark) !important;
        background: var(--sh-gold-dark) !important;
    }
    .bhm-page .bhm-publish:disabled { opacity: .6; cursor: not-allowed; }

    /* Toolbar buttons */
    .bhm-page .bhm-tool-btn {
        display: inline-flex;
        height: var(--sh-ctl) !important;
        min-height: 0 !important;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 10px !important;
        color: var(--sari-dark-e23e64dd1e, #3f4852) !important;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        box-shadow: none !important;
        cursor: pointer;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }
    .bhm-page .bhm-tool-btn:hover,
    .bhm-page .bhm-tool-btn:focus-visible {
        outline: none;
        border-color: var(--sari-dark-d8c2716225, #cfd5dc) !important;
        background: var(--sari-dark-950baec030, #f5f6f8) !important;
        color: var(--sari-dark-e23e64dd1e, #1f252c) !important;
    }
    .bhm-page .bhm-tool-btn.is-active,
    .bhm-page .bhm-tool-btn[aria-pressed="true"] {
        border-color: var(--sari-dark-d8c2716225, #2f2f2f) !important;
        background: linear-gradient(180deg, var(--sari-dark-fbb35de0d1, #363636) 0%, var(--sari-dark-fbb35de0d1, #292929) 100%) !important;
        color: #fff !important;
    }
    .bhm-page .bhm-tool-btn.bhm-reset {
        border-color: transparent !important;
        background: transparent !important;
        color: var(--sari-dark-9b2ca2d764, #865709) !important;
    }
    .bhm-page .bhm-tool-btn.bhm-reset:hover {
        background: var(--sari-dark-950baec030, #fffaf1) !important;
        color: var(--sh-gold) !important;
    }

    /* Toolbar inputs — locked to one height so global admin styles can't bloat them */
    .bhm-page .bhm-ribbon input[type="text"],
    .bhm-page .bhm-ribbon input[type="number"],
    .bhm-page .bhm-ribbon input[list] {
        height: var(--sh-ctl) !important;
        min-height: 0 !important;
        margin: 0 !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 10px !important;
        color: var(--sh-body);
        font-family: inherit;
        font-size: 12px !important;
        font-weight: 600;
        line-height: 1;
        box-shadow: none !important;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .bhm-page .bhm-ribbon input::placeholder { color: var(--sari-dark-a63aa16d7c, #79838e); font-size: 11.5px; font-weight: 450; opacity: 1; }
    .bhm-page .bhm-ribbon input:hover { border-color: var(--sari-dark-d8c2716225, #cfd5dc) !important; }
    .bhm-page .bhm-ribbon input:focus {
        outline: none;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200, 137, 18, .08)) !important;
    }

    .bhm-page #bhmFont { width: 170px; }

    .bhm-page .bhm-inline-numbers label,
    .bhm-page .bhm-link-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0 !important;
        color: var(--sari-dark-e23e64dd1e, #4b5560);
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }
    .bhm-page .bhm-inline-numbers input { width: 54px; padding: 0 6px !important; text-align: center; }
    .bhm-page #bhmButtonUrl { width: 140px; }

    /* Colour controls:  LABEL [swatch] #HEX ⌖  — all on one line */
    .bhm-page .bhm-color-control {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }
    .bhm-page .bhm-color-meta { display: contents; }
    .bhm-page .bhm-color-meta > span {
        order: 1;
        color: var(--sari-dark-a63aa16d7c, #818995);
        font-size: 9.5px;
        font-weight: 750;
        line-height: 1;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .bhm-page .bhm-color-swatch {
        order: 2;
        display: block;
        width: var(--sh-ctl);
        height: var(--sh-ctl);
        flex: 0 0 var(--sh-ctl);
        overflow: hidden;
        margin: 0 !important;
        border: 1px solid var(--sh-control-border);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        cursor: pointer;
    }
    .bhm-page .bhm-color-swatch input[type="color"] {
        display: block;
        width: 100%;
        height: 100%;
        padding: 0;
        border: 0;
        background: none;
        cursor: pointer;
    }
    .bhm-page .bhm-color-meta input { order: 3; width: 74px; text-transform: uppercase; }
    .bhm-page .bhm-eyedropper {
        order: 4;
        display: grid;
        width: var(--sh-ctl);
        height: var(--sh-ctl);
        place-items: center;
        border: 1px solid var(--sh-control-border);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #596470);
        font-size: 15px;
        line-height: 1;
        cursor: pointer;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }
    .bhm-page .bhm-eyedropper:hover,
    .bhm-page .bhm-eyedropper:focus-visible {
        outline: none;
        border-color: var(--sari-dark-d8c2716225, #eadfc9);
        background: var(--sari-dark-950baec030, #fffaf1);
        color: var(--sh-gold);
    }

    /* ---------------- Body: slides + canvas ---------------- */
    .bhm-editor-body {
        display: grid;
        grid-template-columns: 148px minmax(0, 1fr);
        align-items: start;
        gap: 12px;
        min-height: 0;
    }

    .bhm-page .bhm-filmstrip {
        position: sticky !important;
        top: 12px;
        display: flex !important;
        flex-direction: column;
        align-items: stretch;
        gap: 8px !important;
        width: auto !important;
        max-height: calc(100vh - 160px);
        margin: 0 !important;
        overflow-y: auto;
        padding: 10px !important;
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }

    .bhm-page .bhm-filmstrip-caption {
        display: block;
        padding: 2px 2px 2px;
        color: var(--sari-dark-e23e64dd1e, #4b5560);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }
    .bhm-page .bhm-filmstrip-caption strong { color: var(--sh-gold); font-weight: 800; }

    .bhm-page .bhm-slot {
        display: flex !important;
        flex: 0 0 auto;
        flex-direction: column;
        gap: 5px;
        width: 100% !important;
        padding: 5px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        text-align: left;
        box-shadow: none !important;
        cursor: pointer;
        transition: border-color .16s ease, background-color .16s ease, box-shadow .16s ease;
    }
    .bhm-page .bhm-slot img {
        display: block;
        width: 100% !important;
        height: auto !important;
        aspect-ratio: 16 / 7;
        border-radius: 7px;
        object-fit: cover;
        background: var(--sari-dark-4d21eb669b, #f3f4f5);
    }
    .bhm-page .bhm-slot > span {
        color: var(--sari-dark-e23e64dd1e, #3f4852);
        font-size: 10px;
        font-weight: 650;
        line-height: 1.3;
    }
    .bhm-page .bhm-slot:hover { border-color: var(--sari-dark-d8c2716225, #cfd5dc) !important; background: var(--sari-dark-950baec030, #fbfcfd) !important; }
    .bhm-page .bhm-slot:focus-visible {
        outline: none;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200, 137, 18, .08)) !important;
    }
    .bhm-page .bhm-slot[aria-pressed="true"],
    .bhm-page .bhm-slot.is-active {
        border-color: var(--sh-gold) !important;
        background: var(--sari-dark-4d21eb669b, #fff8eb) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-8eadf403a5, rgba(200, 137, 18, .10)) !important;
    }
    .bhm-page .bhm-slot[aria-pressed="true"] > span,
    .bhm-page .bhm-slot.is-active > span { color: var(--sh-gold-text); font-weight: 800; }

    /* Canvas column: transparent — banner floats on the layout background */
    .bhm-page .bhm-canvas-wrap {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 12px;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .bhm-page .bhm-preview-scroller {
        background: transparent !important;
        scrollbar-width: thin;
        scrollbar-color: #c4cad1 transparent;
    }

    .bhm-page .bhm-stage-frame {
        box-shadow:
            0 2px 5px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            0 12px 28px var(--sari-dark-3b2338cbf0, rgba(15, 23, 42, .12));
    }

    /* ---------------- Status bar: info on the left, zoom on the right ---------------- */
    .bhm-page .bhm-stage-footer {
        display: flex !important;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px 16px;
        margin: 0 !important;
        padding: 8px 14px !important;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        font-weight: 500;
    }

    .bhm-page .bhm-status-left,
    .bhm-page .bhm-status-right {
        display: flex;
        min-width: 0;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px 14px;
    }

    .bhm-page .bhm-current-label { color: var(--sh-gold-text); font-weight: 800; white-space: nowrap; }
    .bhm-page .bhm-file-info { color: var(--sari-dark-a63aa16d7c, #59636f); font-weight: 600; }
    .bhm-page .bhm-status-hint { color: var(--sh-muted); }

    .bhm-page .bhm-view-options { display: inline-flex; align-items: center; gap: 6px; }

    .bhm-page .bhm-preview-size,
    .bhm-page .bhm-zoom-readout {
        color: var(--sari-dark-a63aa16d7c, #59636f);
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }
    .bhm-page .bhm-zoom-readout {
        min-width: 40px;
        color: var(--sh-ink);
        font-weight: 800;
        text-align: right;
    }

    .bhm-page .bhm-zoom-button {
        height: 28px;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 11px;
        color: var(--sari-dark-e23e64dd1e, #3f4852) !important;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        box-shadow: none !important;
        cursor: pointer;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }
    .bhm-page .bhm-zoom-button:hover { border-color: var(--sari-dark-d8c2716225, #cfd5dc) !important; background: var(--sari-dark-950baec030, #f5f6f8) !important; }
    .bhm-page .bhm-zoom-button[aria-pressed="true"] {
        border-color: var(--sari-dark-d8c2716225, #2f2f2f) !important;
        background: linear-gradient(180deg, var(--sari-dark-fbb35de0d1, #363636) 0%, var(--sari-dark-fbb35de0d1, #292929) 100%) !important;
        color: #fff !important;
    }

    /* ---------------- Responsive ---------------- */
    @media (max-width: 1279px) {
        .bhm-page .bhm-ribbon-group { padding: 0 9px !important; }
        .bhm-page #bhmFont { width: 140px; }
        .bhm-page .bhm-ribbon-group--publish { margin-left: 0 !important; border-left: 0 !important; }
    }

    @media (max-width: 1023px) {
        .bhm-editor-body { grid-template-columns: minmax(0, 1fr); }

        .bhm-page .bhm-filmstrip {
            position: static !important;
            flex-direction: row;
            align-items: stretch;
            max-height: none;
            overflow-x: auto;
            overflow-y: hidden;
        }
        .bhm-page .bhm-filmstrip-caption { display: none; }
        .bhm-page .bhm-slot { width: 128px !important; flex: 0 0 128px; }
    }

    @media (max-width: 767px) {
        .bhm-page .bhm-ribbon-group {
            width: 100% !important;
            padding: 8px 4px !important;
            border-right: 0 !important;
            border-bottom: 1px solid var(--sh-divider) !important;
        }
        .bhm-page .bhm-ribbon-group:last-child { border-bottom: 0 !important; }
        .bhm-page .bhm-ribbon-group > *,
        .bhm-page .bhm-color-tools,
        .bhm-page .bhm-inline-numbers { flex-wrap: wrap; }
        .bhm-page #bhmFont { width: 100%; }
        .bhm-page .bhm-publish { flex: 1 1 auto; }
        .bhm-page .bhm-stage-footer { justify-content: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="bhm-page" id="buyerHomepageEditor">
    <form id="bhmForm" class="bhm-workspace" action="{{ route('admin.buyer-homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="slot" id="bhmSlot" value="{{ old('slot', request('slot', 'hero_1')) }}">
        <input type="file" name="image" id="bhmImage" accept="image/jpeg,image/png,image/webp" class="bhm-visually-hidden" tabindex="-1">
        {{-- Form data is edited directly on the canvas, not inside visible side panels. --}}
        <div class="bhm-visually-hidden" aria-hidden="true">
            <input name="kicker" id="bhmKicker" type="hidden">
            <input name="title" id="bhmTitle" type="hidden">
            <textarea name="description" id="bhmDescription" maxlength="250" tabindex="-1"></textarea>
            <input name="button_text" id="bhmButtonText" type="hidden">
            <input name="text_x" id="bhmTextX" type="hidden">
            <input name="text_y" id="bhmTextY" type="hidden">
            <input name="button_x" id="bhmButtonX" type="hidden">
            <input name="button_y" id="bhmButtonY" type="hidden">
        </div>

        @if (session('success'))
            <div class="bhm-alert bhm-alert--success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bhm-alert bhm-alert--error" role="alert"><strong>Unable to publish.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        {{-- Toolbar: one compact row. Save / view / publish live at the right end. --}}
        <section class="bhm-ribbon" aria-label="Text and banner editing tools">
            <div class="bhm-ribbon-group" title="Image">
                <span class="bhm-caption">IMAGE</span>
                <div class="bhm-ribbon-row">
                    <button type="button" class="bhm-tool-btn" id="bhmReplaceBtn" title="Replace selected banner image (or drop an image directly on a banner)">▧ <span>Replace</span></button>
                    <span class="bhm-keyhint">or drop directly</span>
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--font" title="Font family">
                <span class="bhm-caption">FONT FAMILY</span>
                <input list="bhmFontOptions" type="text" name="font_family" id="bhmFont" maxlength="32" spellcheck="false" autocomplete="off" placeholder="Search or type any font" aria-label="Font family — search or type a custom font" required>
                <datalist id="bhmFontOptions">
                    @foreach ($fonts as $value => $css)<option value="{{ ucwords($value) }}"></option>@endforeach
                </datalist>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--sizes" title="Text size (px)">
                <span class="bhm-caption">TEXT SIZE</span>
                <div class="bhm-inline-numbers">
                    <label>Title <input name="title_size" id="bhmTitleSize" type="number" min="12" max="52" required aria-label="Title font size in pixels"></label>
                    <label>Body <input name="description_size" id="bhmDescriptionSize" type="number" min="8" max="26" required aria-label="Description font size in pixels"></label>
                    <label>Button <input name="button_size" id="bhmButtonSize" type="number" min="8" max="24" required aria-label="Button font size in pixels"></label>
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--colors" title="Color and pick from screen">
                <span class="bhm-caption">COLOR &amp; PICK FROM SCREEN</span>
                <div class="bhm-color-tools">
                    @foreach (['text_color' => ['bhmTextColor', 'Text'], 'button_bg' => ['bhmButtonBg', 'Button'], 'button_color' => ['bhmButtonColor', 'CTA text']] as $key => $color)
                        <div class="bhm-color-control">
                            <label class="bhm-color-swatch" title="Choose {{ $color[1] }} color"><input type="color" name="{{ $key }}" id="{{ $color[0] }}" aria-label="Choose {{ $color[1] }} color" required></label>
                            <div class="bhm-color-meta"><span>{{ $color[1] }}</span><input type="text" data-hex-for="{{ $color[0] }}" maxlength="7" spellcheck="false" aria-label="{{ $color[1] }} hex color" placeholder="#FFFFFF"></div>
                            <button type="button" class="bhm-eyedropper" data-pick-for="{{ $color[0] }}" aria-label="Pick {{ $color[1] }} color from screen" title="Eyedropper: pick color from screen">⌖</button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--more" title="Add elements and set button link">
                <span class="bhm-caption">ACTION</span>
                <div class="bhm-more-actions">
                    <button type="button" class="bhm-tool-btn" id="bhmAddKicker">+ Label</button>
                    <button type="button" class="bhm-tool-btn" id="bhmAddDescription">+ Description</button>
                    <button type="button" class="bhm-tool-btn" id="bhmAddButton">+ Button</button>
                    <label class="bhm-link-edit" title="Internal destination for the selected banner button">Link <input name="button_url" id="bhmButtonUrl" type="text" maxlength="255" required placeholder="/buyer/products" aria-label="Button link (internal path)"></label>
                    <button type="button" class="bhm-tool-btn bhm-reset" id="bhmReset">Reset</button>
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--publish">
                <span class="bhm-save-status" id="bhmUnsaved" role="status" aria-live="polite">All changes saved</span>
                <a href="{{ route('buyer.home') }}" class="bhm-view-link" target="_blank" rel="noopener noreferrer">View buyer page ↗</a>
                <button type="submit" class="bhm-publish" id="bhmPublish">Save &amp; Publish</button>
            </div>
        </section>

        {{-- Body: slides on the left, banner canvas (hero) on the right. --}}
        <div class="bhm-editor-body">
            <nav class="bhm-filmstrip" id="bhmSlots" aria-label="Choose a homepage banner">
                <span class="bhm-filmstrip-caption">Banners <strong>06</strong></span>
                @foreach ($banners as $slot => $banner)
                    <button type="button" class="bhm-slot" data-slot="{{ $slot }}" aria-pressed="false" aria-label="Edit {{ $banner['label'] }}">
                        <img src="{{ $banner['image_url'] }}" alt="" loading="lazy">
                        <span>{{ $banner['label'] }}</span>
                    </button>
                @endforeach
            </nav>

            <section class="bhm-canvas-wrap" aria-label="Full-width visual homepage banner editor">
                    <div class="bhm-preview-scroller" id="bhmPreviewScroller" aria-label="Buyer-size visual preview; horizontally scroll at 100% zoom">
                      <div class="bhm-stage-frame" id="bhmStageFrame">
                        <div class="bhm-stage" id="bhmStage">
                        <div class="bhm-stage-main bhm-stage-card" data-preview-slot="hero_1" tabindex="0" aria-label="Main carousel banner canvas">
                            <img id="bhmHeroImage" alt="Main promotional banner" draggable="false">
                            <div class="bhm-stage-shade"></div>
                            <div class="bhm-stage-copy" id="bhmHeroCopy" data-drag="text">
                                <button type="button" class="bhm-move-handle" data-move="text" aria-label="Move main banner text using drag or arrow keys" title="Drag to move text">✥ <span>Move text</span></button>
                                <span class="bhm-stage-kicker bhm-editable" id="bhmHeroKicker" data-content-field="kicker" contenteditable="true" role="textbox" aria-label="Edit small heading" spellcheck="true"></span>
                                <strong class="bhm-stage-title bhm-editable" id="bhmHeroTitle" data-content-field="title" contenteditable="true" role="textbox" aria-label="Edit main title" spellcheck="true"></strong>
                                <span class="bhm-stage-description bhm-editable" id="bhmHeroDescription" data-content-field="description" contenteditable="true" role="textbox" aria-label="Edit description" spellcheck="true"></span>
                            </div>
                            <div class="bhm-stage-button" id="bhmHeroButton" data-drag="button"><button type="button" class="bhm-move-handle" data-move="button" aria-label="Move main banner button using drag or arrow keys" title="Drag to move button">✥</button><span class="bhm-editable bhm-button-label" data-content-field="button_text" contenteditable="true" role="textbox" aria-label="Edit button text"></span><span class="bhm-button-arrow" aria-hidden="true">→</span></div>
                            <button type="button" class="bhm-image-trigger" data-replace-image aria-label="Replace main banner image">▧ <span>Replace image</span></button>
                            <div class="bhm-stage-drop-indicator" aria-hidden="true"><span>↓</span> Drop image to replace</div>
                            <div class="bhm-carousel-previous" aria-hidden="true">‹</div>
                            <div class="bhm-carousel-next" aria-hidden="true">›</div>
                            <div class="bhm-carousel-dots" aria-hidden="true"><i class="is-active"></i><i></i><i></i><i></i></div>
                        </div>
                        <div class="bhm-stage-side">
                            @foreach (['side_beauty', 'side_flash'] as $slot)
                                <div class="bhm-stage-card bhm-stage-side-card" data-preview-slot="{{ $slot }}" tabindex="0" aria-label="{{ $banners[$slot]['label'] }} canvas">
                                    <img data-side-image="{{ $slot }}" alt="" draggable="false">
                                    <div class="bhm-stage-shade"></div>
                                    <div class="bhm-stage-copy bhm-stage-side-copy" data-side-copy="{{ $slot }}" data-drag="text">
                                        <button type="button" class="bhm-move-handle" data-move="text" aria-label="Move side banner text using drag or arrow keys">✥</button>
                                        <span class="bhm-stage-kicker bhm-editable" data-side-kicker="{{ $slot }}" data-content-field="kicker" contenteditable="true" role="textbox" aria-label="Edit small heading"></span>
                                        <strong class="bhm-stage-title bhm-editable" data-side-title="{{ $slot }}" data-content-field="title" contenteditable="true" role="textbox" aria-label="Edit title"></strong>
                                        <span class="bhm-stage-description bhm-editable" data-side-description="{{ $slot }}" data-content-field="description" contenteditable="true" role="textbox" aria-label="Edit description"></span>
                                    </div>
                                    <div class="bhm-stage-button bhm-stage-side-button" data-side-button="{{ $slot }}" data-drag="button"><button type="button" class="bhm-move-handle" data-move="button" aria-label="Move side banner button using drag or arrow keys">✥</button><span class="bhm-editable bhm-button-label" data-content-field="button_text" contenteditable="true" role="textbox" aria-label="Edit button text"></span><span class="bhm-button-arrow" aria-hidden="true">→</span></div>
                                    <button type="button" class="bhm-image-trigger" data-replace-image aria-label="Replace {{ $banners[$slot]['label'] }} image">▧ <span>Replace image</span></button>
                                    <div class="bhm-stage-drop-indicator" aria-hidden="true"><span>↓</span> Drop image to replace</div>
                                </div>
                            @endforeach
                        </div>
                        </div>
                      </div>
                    </div>

                {{-- Status bar: current banner + hints on the left, zoom on the right. --}}
                <footer class="bhm-stage-footer">
                    <div class="bhm-status-left">
                        <span class="bhm-current-label" id="bhmCurrentLabel">Main banner · Slide 1</span>
                        <span class="bhm-file-info" id="bhmFileInfo" role="status" aria-live="polite">Drop JPG, PNG or WebP (max 5 MB, min 400 × 200) over a banner to replace its image.</span>
                    </div>
                    <div class="bhm-status-right">
                        <span class="bhm-status-hint">Click text to type · drag ✥ to position</span>
                        <div class="bhm-view-options" aria-label="Buyer canvas zoom">
                            <span class="bhm-preview-size" id="bhmBuyerDimensions">Buyer-sized canvas</span>
                            <button type="button" class="bhm-zoom-button" id="bhmFitView" aria-pressed="true" title="Fit the exact buyer-sized canvas within the admin content width">Fit</button>
                            <button type="button" class="bhm-zoom-button" id="bhmActualView" aria-pressed="false" title="Show real CSS pixels at 100%; scroll horizontally if necessary">100%</button>
                            <span class="bhm-zoom-readout" id="bhmZoomReadout" aria-live="polite">100%</span>
                        </div>
                    </div>
                </footer>
            </section>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.SARI_HOMEPAGE_BANNERS = @json($banners, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    window.SARI_HOMEPAGE_FONTS = @json($fonts);
</script>
<script src="{{ asset('js/admin/buyer-homepage-editor.js') }}?v=6" defer></script>
@endpush