@extends('layouts.seller')

@section('title', 'Promotions & Vouchers — SARI Seller')

@section('page-title', 'Promotions & Vouchers')

@push('styles')

<style id="sariSellerVouchersProfessionalStyles">

    .seller-vouchers-page {

        --voucher-gold: #d59617;

        --voucher-gold-hover: #c9890f;

        --voucher-gold-soft: #fffaf0;

        --voucher-ink: #111827;

        --voucher-text: #344054;

        --voucher-muted: #7c8592;

        --voucher-soft-muted: #98a2b3;

        --voucher-line: #e5e7eb;

        width: 100%;

        max-width: 1440px;

        margin-inline: auto;

        padding-bottom: 24px;

        color: var(--voucher-ink);

        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;

    }

    .seller-vouchers-page *,

    .seller-vouchers-page *::before,

    .seller-vouchers-page *::after {

        box-sizing: border-box;

    }

    .seller-vouchers-page button,

    .seller-vouchers-page input,

    .seller-vouchers-page select,

    .seller-vouchers-page a {

        font-family: inherit;

    }

    /* ------------------------------------------------------------

       Feedback

       ------------------------------------------------------------ */

    .seller-voucher-alert {

        display: flex;

        align-items: flex-start;

        gap: 9px;

        margin-bottom: 10px;

        border: 1px solid;

        border-radius: 12px;

        padding: 9px 11px;

        font-size: 9px;

        font-weight: 500;

        line-height: 1.5;

    }

    .seller-voucher-alert--success {

        border-color: #d5e7dc;

        background: #f5faf7;

        color: #4f7d63;

    }

    .seller-voucher-alert--error {

        border-color: #efdada;

        background: #fff7f7;

        color: #a65353;

    }

    .seller-voucher-alert-icon {

        display: grid;

        width: 27px;

        height: 27px;

        flex: 0 0 27px;

        place-items: center;

        border: 1px solid currentColor;

        border-radius: 8px;

        background: #fff;

        opacity: .82;

    }

    .seller-voucher-alert-icon svg {

        width: 13px;

        height: 13px;

    }

    /* ------------------------------------------------------------

       Header — same family as recent Seller pages

       ------------------------------------------------------------ */

    .seller-vouchers-header {

        min-height: 66px;

        padding: 2px 2px 13px;

        border-bottom: 1px solid #e7ebf0;

    }

    .seller-vouchers-eyebrow {

        margin: 0;

        color: #b87605;

        font-size: 8px;

        font-weight: 800;

        line-height: 1;

        letter-spacing: .16em;

        text-transform: uppercase;

    }

    .seller-vouchers-title {

        margin: 5px 0 0;

        color: var(--voucher-ink);

        font-size: clamp(29px, 2.2vw, 36px);

        font-weight: 650;

        line-height: 1;

        letter-spacing: -.035em;

    }

    .seller-vouchers-title span:last-child {

        margin-left: .18em;

        color: #C9890B;

    }

    .seller-vouchers-subtitle {

        max-width: 760px;

        margin: 7px 0 0;

        color: var(--voucher-muted);

        font-size: 10px;

        line-height: 1.5;

    }

    /* ------------------------------------------------------------

       Main layout

       ------------------------------------------------------------ */

    .seller-vouchers-layout {

        display: grid;

        grid-template-columns: minmax(330px, 380px) minmax(0, 1fr);

        gap: 10px;

        align-items: start;

        margin-top: 12px;

    }

    .seller-voucher-panel {

        overflow: hidden;

        border: 1px solid var(--voucher-line);

        border-radius: 13px;

        background: #fff;

        box-shadow: 0 5px 16px rgba(15, 23, 42, .028);

    }

    .seller-voucher-panel-head {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        border-bottom: 1px solid #edf0f3;

        padding: 11px 13px;

        background: #fff;

    }

    .seller-voucher-panel-title-group {

        display: flex;

        min-width: 0;

        align-items: center;

        gap: 8px;

    }

    .seller-voucher-panel-icon {

        display: grid;

        width: 28px;

        height: 28px;

        flex: 0 0 28px;

        place-items: center;

        border: 1px solid #efe3c7;

        border-radius: 8px;

        background: var(--voucher-gold-soft);

        color: #b97805;

    }

    .seller-voucher-panel-icon svg {

        width: 13px;

        height: 13px;

    }

    .seller-voucher-panel-title {

        margin: 0;

        color: #1f2937;

        font-size: 11px;

        font-weight: 700;

        line-height: 1.3;

    }

    .seller-voucher-panel-subtitle {

        margin: 2px 0 0;

        color: var(--voucher-soft-muted);

        font-size: 8px;

        line-height: 1.35;

    }

    .seller-voucher-count {

        display: inline-flex;

        min-height: 23px;

        align-items: center;

        border: 1px solid #e5e7eb;

        border-radius: 999px;

        background: #fafbfc;

        padding: 0 8px;

        color: #667085;

        font-size: 8px;

        font-weight: 700;

        white-space: nowrap;

    }

    /* ------------------------------------------------------------

       Create form

       ------------------------------------------------------------ */

    .seller-voucher-form {

        padding: 12px 13px 13px;

    }

    .seller-voucher-form-grid {

        display: grid;

        gap: 9px;

    }

    .seller-voucher-field-grid {

        display: grid;

        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 8px;

    }

    .seller-voucher-field {

        min-width: 0;

    }

    .seller-voucher-label {

        display: block;

        margin: 0 0 5px;

        color: #475467;

        font-size: 8.8px;

        font-weight: 650;

        line-height: 1.3;

    }

    .seller-voucher-label small {

        color: var(--voucher-soft-muted);

        font-size: 7.5px;

        font-weight: 400;

    }

    .seller-voucher-input,

    .seller-voucher-select {

        width: 100%;

        height: 40px;

        min-height: 40px;

        border: 1px solid #d8dee6;

        border-radius: 9px;

        background: #fff;

        padding: 0 10px;

        color: #344054;

        font-family: inherit;

        font-size: 9.5px;

        outline: none;

        box-shadow: 0 1px 2px rgba(15, 23, 42, .015);

        transition: border-color .14s ease, box-shadow .14s ease;

    }

    .seller-voucher-input::placeholder {

        color: #98a2b3;

    }

    .seller-voucher-input:focus,

    .seller-voucher-select:focus {

        border-color: var(--voucher-gold);

        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08);

    }

    .seller-voucher-code {

        text-transform: uppercase;

        letter-spacing: .04em;

        font-weight: 700;

    }

    .seller-voucher-form-divider {

        height: 1px;

        margin-block: 2px;

        background: #edf0f3;

    }

    .seller-voucher-section-label {

        color: #98a2b3;

        font-size: 7.6px;

        font-weight: 700;

        letter-spacing: .08em;

        text-transform: uppercase;

    }

    .seller-voucher-submit {

        display: inline-flex;

        width: 100%;

        height: 40px;

        min-height: 40px;

        align-items: center;

        justify-content: center;

        gap: 7px;

        border: 1px solid var(--voucher-gold);

        border-radius: 9px;

        background: var(--voucher-gold);

        padding: 0 13px;

        color: #fff;

        font-size: 9.5px;

        font-weight: 700;

        cursor: pointer;

        box-shadow: 0 6px 14px rgba(213, 150, 23, .14);

        transition: border-color .14s ease, background-color .14s ease;

    }

    .seller-voucher-submit:hover,

    .seller-voucher-submit:focus-visible {

        outline: none;

        border-color: var(--voucher-gold-hover);

        background: var(--voucher-gold-hover);

    }

    .seller-voucher-submit svg {

        width: 13px;

        height: 13px;

    }

    .seller-voucher-form-note {

        margin: 0;

        color: var(--voucher-soft-muted);

        font-size: 7.8px;

        line-height: 1.45;

        text-align: center;

    }

    /* ------------------------------------------------------------

       Voucher list

       ------------------------------------------------------------ */

    .seller-voucher-list {

        display: grid;

    }

    .seller-voucher-row {

        display: grid;

        grid-template-columns: minmax(0, 1fr) auto;

        gap: 14px;

        align-items: center;

        border-bottom: 1px solid #edf0f3;

        padding: 11px 13px;

        background: #fff;

        transition: background-color .14s ease;

    }

    .seller-voucher-row:last-child {

        border-bottom: 0;

    }

    .seller-voucher-row:hover {

        background: #fffdf8;

    }

    .seller-voucher-main {

        min-width: 0;

    }

    .seller-voucher-heading {

        display: flex;

        min-width: 0;

        flex-wrap: wrap;

        align-items: center;

        gap: 7px;

    }

    .seller-voucher-code-badge {

        display: inline-flex;

        min-height: 25px;

        align-items: center;

        border: 1px solid #f0dbad;

        border-radius: 8px;

        background: #fff8e8;

        padding: 0 8px;

        color: #a96f06;

        font-size: 8.8px;

        font-weight: 800;

        letter-spacing: .035em;

    }

    .seller-voucher-name {

        overflow: hidden;

        color: #1f2937;

        font-size: 9.8px;

        font-weight: 700;

        line-height: 1.35;

        text-overflow: ellipsis;

        white-space: nowrap;

    }

    .seller-voucher-status {

        display: inline-flex;

        min-height: 22px;

        align-items: center;

        gap: 5px;

        border: 1px solid;

        border-radius: 999px;

        padding: 0 7px;

        font-size: 7.5px;

        font-weight: 700;

        white-space: nowrap;

    }

    .seller-voucher-status::before {

        content: "";

        width: 5px;

        height: 5px;

        border-radius: 999px;

        background: currentColor;

    }

    .seller-voucher-status--active {

        border-color: #cfe4d7;

        background: #f1f8f4;

        color: #4f7d63;

    }

    .seller-voucher-status--inactive {

        border-color: #e1e4e8;

        background: #f8fafc;

        color: #7c8592;

    }

    .seller-voucher-meta {

        display: flex;

        flex-wrap: wrap;

        align-items: center;

        gap: 4px 8px;

        margin-top: 6px;

        color: #7c8592;

        font-size: 8.4px;

        line-height: 1.4;

    }

    .seller-voucher-meta strong {

        color: #475467;

        font-weight: 650;

    }

    .seller-voucher-meta-separator {

        color: #d4d8de;

    }

    .seller-voucher-actions {

        display: flex;

        flex: 0 0 auto;

        align-items: center;

        gap: 6px;

    }

    .seller-voucher-action-button {

        display: inline-flex;

        height: 34px;

        min-height: 34px;

        align-items: center;

        justify-content: center;

        gap: 5px;

        border: 1px solid;

        border-radius: 8px;

        background: #fff;

        padding: 0 9px;

        font-size: 8.3px;

        font-weight: 650;

        cursor: pointer;

        transition: border-color .14s ease, background-color .14s ease, color .14s ease;

    }

    .seller-voucher-action-button svg {

        width: 12px;

        height: 12px;

    }

    .seller-voucher-action-button--toggle {

        border-color: #dfe3e8;

        color: #667085;

    }

    .seller-voucher-action-button--toggle:hover,

    .seller-voucher-action-button--toggle:focus-visible {

        outline: none;

        border-color: #efd9a7;

        background: #fffaf0;

        color: #9b6505;

    }

    .seller-voucher-action-button--delete {

        border-color: #efcece;

        color: #a65353;

    }

    .seller-voucher-action-button--delete:hover,

    .seller-voucher-action-button--delete:focus-visible {

        outline: none;

        border-color: #e7bcbc;

        background: #fff7f7;

        color: #944646;

    }

    /* ------------------------------------------------------------

       Empty + pagination

       ------------------------------------------------------------ */

    .seller-voucher-empty {

        display: grid;

        min-height: 170px;

        place-items: center;

        padding: 28px 18px;

        text-align: center;

    }

    .seller-voucher-empty-icon {

        display: grid;

        width: 38px;

        height: 38px;

        margin-inline: auto;

        place-items: center;

        border: 1px solid #e5e7eb;

        border-radius: 10px;

        background: #fafbfc;

        color: #98a2b3;

    }

    .seller-voucher-empty-icon svg {

        width: 17px;

        height: 17px;

    }

    .seller-voucher-empty-title {

        margin-top: 9px;

        color: #344054;

        font-size: 10.5px;

        font-weight: 650;

    }

    .seller-voucher-empty-copy {

        margin-top: 4px;

        color: var(--voucher-soft-muted);

        font-size: 8.8px;

        line-height: 1.5;

    }

    .seller-voucher-pagination {

        border-top: 1px solid #edf0f3;

        padding: 10px 13px;

        background: #fff;

    }

    /* ------------------------------------------------------------

       Responsive

       ------------------------------------------------------------ */

    @media (max-width: 1100px) {

        .seller-vouchers-layout {

            grid-template-columns: 340px minmax(0, 1fr);

        }

        .seller-voucher-row {

            grid-template-columns: 1fr;

        }

        .seller-voucher-actions {

            justify-content: flex-start;

        }

    }

    @media (max-width: 820px) {

        .seller-vouchers-layout {

            grid-template-columns: 1fr;

        }

    }

    @media (max-width: 640px) {

        .seller-vouchers-title {

            font-size: clamp(28px, 8vw, 34px);

        }

        .seller-voucher-field-grid {

            grid-template-columns: 1fr;

        }

        .seller-voucher-input,

        .seller-voucher-select,

        .seller-voucher-submit {

            height: 42px;

            min-height: 42px;

            font-size: 10px;

        }

        .seller-voucher-row {

            padding: 10px;

        }

        .seller-voucher-actions {

            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));

        }

        .seller-voucher-action-button {

            width: 100%;

            height: 36px;

            min-height: 36px;

        }

    }

    @media (prefers-reduced-motion: reduce) {

        .seller-vouchers-page * {

            transition-duration: .01ms !important;

            scroll-behavior: auto !important;

        }

    }

/* ======================================================================
   PROMOTIONS & VOUCHERS — DASHBOARD FLOATING CONTAINERS FINAL PASS
   Front-end/CSS only. Voucher create/toggle/delete routes, CSRF,
   validation, pagination, and backend behavior remain unchanged.
   ====================================================================== */

.seller-vouchers-page {
    --voucher-dashboard-border: #E5E7EB;
    --voucher-dashboard-border-strong: #D1D5DB;
    --voucher-dashboard-divider: #ECEFF2;

    /* Same layered depth language used on the Seller dashboard:
       contact shadow + middle separation + broad ambient float. */
    --voucher-dashboard-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);

    --voucher-dashboard-shadow-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);

    --voucher-dashboard-shadow-inner:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 14px rgba(15, 23, 42, .045);
}

/* Header remains flat; only its divider is neutral. */
.seller-vouchers-page .seller-vouchers-header {
    border-bottom-color: var(--voucher-dashboard-divider) !important;
}

/* ------------------------------------------------------------------
   PRIMARY PANELS
   Both Create Voucher and Seller Vouchers become true floating surfaces.
   ------------------------------------------------------------------ */
.seller-vouchers-page .seller-voucher-panel {
    border: 1px solid var(--voucher-dashboard-border) !important;
    border-radius: 14px !important;
    background: #FFFFFF !important;
    box-shadow: var(--voucher-dashboard-shadow) !important;
    transform: none !important;
    overflow: hidden !important;
}

.seller-vouchers-page .seller-voucher-panel:hover {
    border-color: var(--voucher-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--voucher-dashboard-shadow) !important;
    transform: none !important;
}

/* Panel headers stay visually attached to the floating shell. */
.seller-vouchers-page .seller-voucher-panel-head {
    border-bottom-color: var(--voucher-dashboard-divider) !important;
    background: #FFFFFF !important;
}

/* Icon shells use neutral structure; gold is kept only as icon accent. */
.seller-vouchers-page .seller-voucher-panel-icon {
    border-color: var(--voucher-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* Count chip is a quiet secondary surface. */
.seller-vouchers-page .seller-voucher-count {
    border-color: var(--voucher-dashboard-border) !important;
    background: #F8FAFC !important;
    box-shadow: 0 2px 7px rgba(15, 23, 42, .025) !important;
}

/* ------------------------------------------------------------------
   FORM SURFACES
   Controls receive restrained depth only; parent panel remains dominant.
   ------------------------------------------------------------------ */
.seller-vouchers-page .seller-voucher-input,
.seller-vouchers-page .seller-voucher-select {
    border-color: var(--voucher-dashboard-border-strong) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .02),
        0 4px 10px rgba(15, 23, 42, .03) !important;
    transform: none !important;
}

.seller-vouchers-page .seller-voucher-input:hover,
.seller-vouchers-page .seller-voucher-select:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
}

.seller-vouchers-page .seller-voucher-input:focus,
.seller-vouchers-page .seller-voucher-select:focus {
    border-color: #BFC5CC !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(15, 23, 42, .045),
        0 5px 12px rgba(15, 23, 42, .035) !important;
}

.seller-vouchers-page .seller-voucher-form-divider {
    background: var(--voucher-dashboard-divider) !important;
}

/* Primary CTA keeps gold semantics but does not physically move. */
.seller-vouchers-page .seller-voucher-submit,
.seller-vouchers-page .seller-voucher-submit:hover,
.seller-vouchers-page .seller-voucher-submit:focus-visible {
    transform: none !important;
}

/* ------------------------------------------------------------------
   VOUCHER LIST
   Rows stay flat inside one floating parent, improving scanability.
   ------------------------------------------------------------------ */
.seller-vouchers-page .seller-voucher-list {
    background: #FFFFFF !important;
}

.seller-vouchers-page .seller-voucher-row {
    border-bottom-color: var(--voucher-dashboard-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Replace old cream hover with neutral light gray. */
.seller-vouchers-page .seller-voucher-row:hover {
    background: #F8FAFC !important;
    border-bottom-color: var(--voucher-dashboard-divider) !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Voucher code keeps gold as semantic campaign accent, not structural border. */
.seller-vouchers-page .seller-voucher-code-badge {
    box-shadow: 0 2px 7px rgba(15, 23, 42, .025) !important;
}

/* Action buttons stay still; only normal color feedback is allowed. */
.seller-vouchers-page .seller-voucher-action-button,
.seller-vouchers-page .seller-voucher-action-button:hover,
.seller-vouchers-page .seller-voucher-action-button:focus-visible {
    transform: none !important;
}

/* Toggle action uses neutral structure instead of cream structural hover. */
.seller-vouchers-page .seller-voucher-action-button--toggle {
    border-color: var(--voucher-dashboard-border) !important;
    background: #FFFFFF !important;
}

.seller-vouchers-page .seller-voucher-action-button--toggle:hover,
.seller-vouchers-page .seller-voucher-action-button--toggle:focus-visible {
    border-color: var(--voucher-dashboard-border-strong) !important;
    background: #F8FAFC !important;
    color: #7A5A18 !important;
}

/* Delete retains its semantic red treatment. */
.seller-vouchers-page .seller-voucher-action-button--delete {
    box-shadow: 0 2px 7px rgba(15, 23, 42, .02) !important;
}

/* ------------------------------------------------------------------
   ALERTS / EMPTY / PAGINATION
   ------------------------------------------------------------------ */
.seller-vouchers-page .seller-voucher-alert {
    box-shadow: var(--voucher-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-vouchers-page .seller-voucher-empty {
    background: #FFFFFF !important;
}

.seller-vouchers-page .seller-voucher-empty-icon {
    border-color: var(--voucher-dashboard-border) !important;
    background: #F8FAFC !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

.seller-vouchers-page .seller-voucher-pagination {
    border-top-color: var(--voucher-dashboard-divider) !important;
    background: #FFFFFF !important;
}

.seller-vouchers-page .seller-voucher-pagination nav a,
.seller-vouchers-page .seller-voucher-pagination nav span {
    border-color: var(--voucher-dashboard-border) !important;
}

/* Main structural surfaces never get colored outlines. */
.seller-vouchers-page .seller-voucher-panel,
.seller-vouchers-page .seller-voucher-alert,
.seller-vouchers-page .seller-voucher-input,
.seller-vouchers-page .seller-voucher-select {
    outline-color: transparent !important;
}

/* Mobile: retain the float while reducing ambient spread. */
@media (max-width: 640px) {
    .seller-vouchers-page .seller-voucher-panel {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    .seller-vouchers-page .seller-voucher-alert {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }
}
</style>

@endpush

@section('content')

<div class="seller-vouchers-page">

    @if(session('success'))

        <div class="seller-voucher-alert seller-voucher-alert--success">

            <span class="seller-voucher-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-voucher-alert seller-voucher-alert--error">

            <span class="seller-voucher-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-vouchers-header">

        <p class="seller-vouchers-eyebrow">Marketing</p>

        <h1 class="seller-vouchers-title">

            <span>Promotions</span><span>& Vouchers</span>

        </h1>

        <p class="seller-vouchers-subtitle">

            Create server-validated Seller vouchers that Buyers can apply during checkout.

        </p>

    </header>

    <div class="seller-vouchers-layout">

        <section class="seller-voucher-panel">

            <div class="seller-voucher-panel-head">

                <div class="seller-voucher-panel-title-group">

                    <span class="seller-voucher-panel-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M4 7h16v10H4z"></path>

                            <path d="M8 7V5h8v2"></path>

                            <path d="M12 10v4"></path>

                            <path d="M10 12h4"></path>

                        </svg>

                    </span>

                    <div>

                        <h2 class="seller-voucher-panel-title">Create Voucher</h2>

                        <p class="seller-voucher-panel-subtitle">Configure a checkout discount campaign.</p>

                    </div>

                </div>

            </div>

            <form method="POST" action="{{ route('seller.vouchers.store') }}" class="seller-voucher-form">

                @csrf

                <div class="seller-voucher-form-grid">

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-code">Voucher code</label>

                        <input

                            id="voucher-code"

                            name="code"

                            required

                            value="{{ old('code') }}"

                            placeholder="e.g. SARI10"

                            autocomplete="off"

                            class="seller-voucher-input seller-voucher-code"

                        >

                    </div>

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-name">Campaign name</label>

                        <input

                            id="voucher-name"

                            name="name"

                            required

                            value="{{ old('name') }}"

                            placeholder="e.g. October Payday Sale"

                            class="seller-voucher-input"

                        >

                    </div>

                    <div class="seller-voucher-form-divider"></div>

                    <div class="seller-voucher-section-label">Discount</div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-discount-type">Discount type</label>

                            <select

                                id="voucher-discount-type"

                                name="discount_type"

                                class="seller-voucher-select"

                            >

                                <option value="percentage" @selected(old('discount_type', 'percentage') === 'percentage')>

                                    Percentage

                                </option>

                                <option value="fixed" @selected(old('discount_type') === 'fixed')>

                                    Fixed Amount

                                </option>

                            </select>

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-discount-value">Discount value</label>

                            <input

                                id="voucher-discount-value"

                                name="discount_value"

                                type="number"

                                step=".01"

                                min=".01"

                                required

                                value="{{ old('discount_value') }}"

                                placeholder="Value"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-minimum-spend">

                                Minimum spend <small>optional</small>

                            </label>

                            <input

                                id="voucher-minimum-spend"

                                name="minimum_spend"

                                type="number"

                                step=".01"

                                min="0"

                                value="{{ old('minimum_spend') }}"

                                placeholder="0.00"

                                class="seller-voucher-input"

                            >

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-max-discount">

                                Max discount <small>optional</small>

                            </label>

                            <input

                                id="voucher-max-discount"

                                name="maximum_discount"

                                type="number"

                                step=".01"

                                min="0"

                                value="{{ old('maximum_discount') }}"

                                placeholder="0.00"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-usage-limit">

                            Usage limit <small>optional</small>

                        </label>

                        <input

                            id="voucher-usage-limit"

                            name="usage_limit"

                            type="number"

                            min="1"

                            value="{{ old('usage_limit') }}"

                            placeholder="Unlimited if left blank"

                            class="seller-voucher-input"

                        >

                    </div>

                    <div class="seller-voucher-form-divider"></div>

                    <div class="seller-voucher-section-label">Schedule</div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-starts-at">

                                Starts at <small>optional</small>

                            </label>

                            <input

                                id="voucher-starts-at"

                                name="starts_at"

                                type="datetime-local"

                                value="{{ old('starts_at') }}"

                                class="seller-voucher-input"

                            >

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-ends-at">

                                Ends at <small>optional</small>

                            </label>

                            <input

                                id="voucher-ends-at"

                                name="ends_at"

                                type="datetime-local"

                                value="{{ old('ends_at') }}"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <button type="submit" class="seller-voucher-submit">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                            <path d="M12 5v14"></path>

                            <path d="M5 12h14"></path>

                        </svg>

                        Create Voucher

                    </button>

                    <p class="seller-voucher-form-note">

                        Voucher rules are validated by the server before they become available at checkout.

                    </p>

                </div>

            </form>

        </section>

        <section class="seller-voucher-panel">

            <div class="seller-voucher-panel-head">

                <div class="seller-voucher-panel-title-group">

                    <span class="seller-voucher-panel-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M4 7h16v10H4z"></path>

                            <path d="M9 10h6"></path>

                            <path d="M9 14h4"></path>

                        </svg>

                    </span>

                    <div>

                        <h2 class="seller-voucher-panel-title">Seller Vouchers</h2>

                        <p class="seller-voucher-panel-subtitle">Manage active and inactive checkout campaigns.</p>

                    </div>

                </div>

                <span class="seller-voucher-count">

                    {{ number_format($vouchers->total()) }} total

                </span>

            </div>

            <div class="seller-voucher-list">

                @forelse($vouchers as $voucher)

                    <article class="seller-voucher-row">

                        <div class="seller-voucher-main">

                            <div class="seller-voucher-heading">

                                <span class="seller-voucher-code-badge">{{ $voucher->code }}</span>

                                <span class="seller-voucher-name">{{ $voucher->name }}</span>

                                <span class="seller-voucher-status {{ $voucher->is_active ? 'seller-voucher-status--active' : 'seller-voucher-status--inactive' }}">

                                    {{ $voucher->is_active ? 'Active' : 'Inactive' }}

                                </span>

                            </div>

                            <div class="seller-voucher-meta">

                                <span>

                                    <strong>

                                        {{ $voucher->discount_type === 'percentage'

                                            ? rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') . '%'

                                            : '₱' . number_format((float) $voucher->discount_value, 2) }}

                                    </strong>

                                    off

                                </span>

                                <span class="seller-voucher-meta-separator">•</span>

                                <span>

                                    Used

                                    <strong>

                                        {{ number_format((int) $voucher->used_count) }}{{ $voucher->usage_limit ? '/'.number_format((int) $voucher->usage_limit) : '' }}

                                    </strong>

                                </span>

                            </div>

                        </div>

                        <div class="seller-voucher-actions">

                            <form method="POST" action="{{ route('seller.vouchers.toggle', $voucher) }}">

                                @csrf

                                <button type="submit" class="seller-voucher-action-button seller-voucher-action-button--toggle">

                                    @if($voucher->is_active)

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                            <circle cx="12" cy="12" r="8"></circle>

                                            <path d="M8 12h8"></path>

                                        </svg>

                                        Deactivate

                                    @else

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                            <circle cx="12" cy="12" r="8"></circle>

                                            <path d="m8 12 2.5 2.5L16 9"></path>

                                        </svg>

                                        Activate

                                    @endif

                                </button>

                            </form>

                            <form method="POST" action="{{ route('seller.vouchers.destroy', $voucher) }}">

                                @csrf

                                @method('DELETE')

                                <button type="submit" class="seller-voucher-action-button seller-voucher-action-button--delete">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                        <path d="M5 7h14"></path>

                                        <path d="M9 7V5h6v2"></path>

                                        <path d="M8 10v7"></path>

                                        <path d="M12 10v7"></path>

                                        <path d="M16 10v7"></path>

                                        <path d="M7 7l1 12h8l1-12"></path>

                                    </svg>

                                    Delete

                                </button>

                            </form>

                        </div>

                    </article>

                @empty

                    <div class="seller-voucher-empty">

                        <div>

                            <span class="seller-voucher-empty-icon" aria-hidden="true">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                    <path d="M4 7h16v10H4z"></path>

                                    <path d="M8 7V5h8v2"></path>

                                    <path d="M12 10v4"></path>

                                    <path d="M10 12h4"></path>

                                </svg>

                            </span>

                            <div class="seller-voucher-empty-title">No vouchers created yet</div>

                            <div class="seller-voucher-empty-copy">

                                Create your first campaign using the voucher form.

                            </div>

                        </div>

                    </div>

                @endforelse

            </div>

            @if($vouchers->hasPages())

                <div class="seller-voucher-pagination">

                    {{ $vouchers->links() }}

                </div>

            @endif

        </section>

    </div>

</div>

@endsection
