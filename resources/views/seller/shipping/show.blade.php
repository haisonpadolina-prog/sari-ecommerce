@extends('layouts.seller')



@section('title', 'Track ' . $order->order_number . ' — SARI Seller')



@section('page-title', 'Shipping Tracking')



@push('styles')



<style id="sariSellerShippingDetailProfessionalStyles">



    .seller-shipping-detail-page {



        --ship-gold: #d59617;



        --ship-gold-hover: #c9890f;



        --ship-gold-soft: #fffaf0;



        --ship-ink: #111827;



        --ship-text: #344054;



        --ship-muted: #7c8592;



        --ship-soft-muted: #98a2b3;



        --ship-line: #e5e7eb;



        width: 100%;



        max-width: 1440px;



        margin-inline: auto;



        padding-bottom: 28px;



        color: var(--ship-ink);



        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;



    }



    .seller-shipping-detail-page a,



    .seller-shipping-detail-page button {



        font-family: inherit;



    }



    .seller-shipping-detail-header {



        display: flex;



        min-height: 72px;



        align-items: flex-start;



        justify-content: space-between;



        gap: 24px;



        margin: 0 0 16px;



        padding: 2px 2px 16px;



        border-bottom: 1px solid #e7ebf0;



    }



    .seller-shipping-detail-heading { min-width: 0; }



    .seller-shipping-detail-eyebrow {



        margin: 0;



        color: #b87605;



        font-size: 8px;



        font-weight: 800;



        line-height: 1;



        letter-spacing: .16em;



        text-transform: uppercase;



    }



    .seller-shipping-detail-title {



        margin: 6px 0 0;



        color: var(--ship-ink);



        font-size: clamp(30px, 2.35vw, 38px);



        font-weight: 650;



        line-height: 1;



        letter-spacing: -.035em;



    }



    .seller-shipping-detail-title span { color: #C9890B; }



    .seller-shipping-detail-meta {



        display: flex;



        flex-wrap: wrap;



        align-items: center;



        gap: 8px;



        margin-top: 8px;



    }



    .seller-shipping-back {



        display: inline-flex;



        align-items: center;



        gap: 5px;



        color: #7c8592;



        font-size: 8px;



        font-weight: 600;



        text-decoration: none;



    }



    .seller-shipping-back:hover { color: #a56b05; }



    .seller-shipping-order-id {



        color: #475467;



        font-size: 9px;



        font-weight: 700;



    }



    .seller-shipping-detail-subtitle {



        max-width: 760px;



        margin: 8px 0 0;



        color: var(--ship-muted);



        font-size: 10.5px;



        line-height: 1.55;



    }



    .seller-shipping-detail-actions {



        display: flex;



        flex: 0 0 auto;



        flex-wrap: wrap;



        justify-content: flex-end;



        gap: 8px;



        padding-top: 2px;



    }



    .seller-shipping-live,



    .seller-shipping-waybill-top {



        display: inline-flex;



        height: 42px;



        align-items: center;



        justify-content: center;



        gap: 8px;



        border-radius: 12px;



        padding: 0 14px;



        font-size: 9px;



        font-weight: 650;



        white-space: nowrap;



    }



    .seller-shipping-live {



        border: 1px solid #d4e7da;



        background: #f5faf7;



        color: #4f7d63;



    }



    .seller-shipping-waybill-top {



        border: 1px solid #dfe3e8;



        background: #fff;



        color: #667085;



        text-decoration: none;



        transition: border-color .14s ease, background-color .14s ease, color .14s ease;



    }



    .seller-shipping-waybill-top:hover {



        border-color: #efd9a7;



        background: #fffaf0;



        color: #9b6505;



    }



    .seller-shipping-progress-card,



    .seller-shipping-detail-card {



        border: 1px solid var(--ship-line);



        border-radius: 16px;



        background: #fff;



        box-shadow: 0 8px 22px rgba(15,23,42,.035);



    }



    .seller-shipping-progress-card {



        margin-top: 0;



        padding: 17px 18px;



    }



    .seller-shipping-progress-card .progress-title {



        color: #475467;



        font-size: 10px;



        font-weight: 650;



    }



    .seller-shipping-progress-card .progress-subtitle {



        margin-top: 3px;



        color: #98a2b3;



        font-size: 8px;



    }



    .seller-shipping-progress-card .progress-value {



        color: #b97805;



        font-size: 14px;



        font-weight: 750;



    }



    .seller-shipping-progress-track {



        height: 6px;



        overflow: hidden;



        margin-top: 13px;



        border-radius: 999px;



        background: #edf0f3;



    }



    .seller-shipping-progress-track > div {



        height: 100%;



        border-radius: inherit;



        background: var(--ship-gold);



    }



    .seller-shipping-milestones {



        display: grid;



        grid-template-columns: repeat(5, minmax(0,1fr));



        gap: 7px;



        margin-top: 12px;



    }



    .seller-shipping-detail-grid {



        display: grid;



        grid-template-columns: minmax(0, 1fr) 360px;



        gap: 14px;



        margin-top: 14px;



        align-items: start;



    }



    .seller-shipping-main-stack,



    .seller-shipping-side-stack {



        display: grid;



        gap: 14px;



    }



    .seller-shipping-detail-card {



        padding: 16px;



    }



    .seller-shipping-section-eyebrow {



        color: #b87605;



        font-size: 8px;



        font-weight: 800;



        line-height: 1;



        letter-spacing: .14em;



        text-transform: uppercase;



    }



    .seller-shipping-section-title {



        margin-top: 5px;



        color: #1f2937;



        font-size: 15px;



        font-weight: 700;



        letter-spacing: -.02em;



    }



    .seller-shipping-timeline {



        margin-top: 18px;



    }



    .seller-shipping-timeline-entry {



        position: relative;



        display: flex;



        gap: 13px;



    }



    .seller-shipping-item {



        display: flex;



        align-items: flex-start;



        justify-content: space-between;



        gap: 14px;



        border: 1px solid #e7e9ee;



        border-radius: 12px;



        background: #fafbfc;



        padding: 11px 12px;



    }



    .seller-shipping-detail-label {



        color: #98a2b3;



        font-size: 7px;



        font-weight: 800;



        letter-spacing: .1em;



        text-transform: uppercase;



    }



    .seller-shipping-detail-value {



        margin-top: 4px;



        color: #344054;



        font-size: 9.5px;



        font-weight: 650;



        line-height: 1.45;



    }



    .seller-shipping-detail-secondary {



        margin-top: 3px;



        color: #98a2b3;



        font-size: 8px;



        line-height: 1.5;



    }



    .seller-shipping-hub-icon {



        display: grid;



        width: 36px;



        height: 36px;



        place-items: center;



        border: 1px solid #e4e4f0;



        border-radius: 10px;



        background: #f7f7fc;



        color: #63638d;



    }



    .seller-shipping-hub-icon svg { width: 15px; height: 15px; }



    .seller-shipping-hub-field {



        border: 1px solid #e7e9ee;



        border-radius: 12px;



        background: #fafbfc;



        padding: 11px 12px;



    }



    .seller-shipping-value-row {



        display: flex;



        align-items: center;



        justify-content: space-between;



        gap: 14px;



        color: #667085;



        font-size: 9px;



    }



    .seller-shipping-value-row + .seller-shipping-value-row { margin-top: 9px; }



    .seller-shipping-value-row strong { color: #344054; font-weight: 650; }



    .seller-shipping-total-row {



        display: flex;



        align-items: flex-end;



        justify-content: space-between;



        gap: 14px;



        margin-top: 11px;



        border-top: 1px solid #edf0f3;



        padding-top: 11px;



    }



    .seller-shipping-total-row span { color: #344054; font-size: 10px; font-weight: 650; }



    .seller-shipping-total-row strong { color: #b97805; font-size: 17px; font-weight: 800; letter-spacing: -.03em; }



    @media (max-width: 1023px) {



        .seller-shipping-detail-header { flex-direction: column; gap: 13px; }



        .seller-shipping-detail-actions { width: 100%; justify-content: flex-start; padding-top: 0; }



        .seller-shipping-detail-grid { grid-template-columns: 1fr; }



        .seller-shipping-side-stack { grid-template-columns: repeat(2, minmax(0,1fr)); }



        .seller-shipping-side-stack > :last-child { grid-column: 1 / -1; }



    }



    @media (max-width: 640px) {



        .seller-shipping-detail-title { font-size: clamp(28px, 8vw, 34px); }



        .seller-shipping-detail-actions { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); }



        .seller-shipping-live, .seller-shipping-waybill-top { width: 100%; }



        .seller-shipping-milestones { gap: 4px; }



        .seller-shipping-side-stack { grid-template-columns: 1fr; }



        .seller-shipping-side-stack > :last-child { grid-column: auto; }



        .seller-shipping-detail-card { padding: 14px; }



    }



    @media (prefers-reduced-motion: reduce) {



        .seller-shipping-detail-page * { transition-duration: .01ms !important; scroll-behavior: auto !important; }



    }



/* ======================================================================

   SHIPPING DETAIL — DASHBOARD-STYLE FLOATING CONTAINERS

   Visual-only enhancement. No shipping data, live polling, routes,

   waybill actions, logistics state, database or backend behavior changed.

   ====================================================================== */



.seller-shipping-detail-page {

    --ship-detail-float-border: #E5E7EB;

    --ship-detail-float-border-strong: #D1D5DB;

    --ship-detail-divider: #ECEFF2;

    --ship-detail-shadow:

        0 1px 2px rgba(15, 23, 42, .035),

        0 10px 26px rgba(15, 23, 42, .075),

        inset 0 1px 0 rgba(255, 255, 255, .86);

    --ship-detail-shadow-strong:

        0 1px 2px rgba(15, 23, 42, .04),

        0 14px 34px rgba(15, 23, 42, .085),

        inset 0 1px 0 rgba(255, 255, 255, .88);

    --ship-detail-shadow-soft:

        0 1px 2px rgba(15, 23, 42, .025),

        0 6px 16px rgba(15, 23, 42, .05),

        inset 0 1px 0 rgba(255, 255, 255, .82);

}



.seller-shipping-detail-page .seller-shipping-detail-header {

    border-bottom-color: var(--ship-detail-divider) !important;

}



/* Main containers — dashboard-style floating hierarchy. */

.seller-shipping-detail-page .seller-shipping-progress-card,

.seller-shipping-detail-page .seller-shipping-detail-card {

    border: 1px solid var(--ship-detail-float-border) !important;

    background: #FFFFFF !important;

    box-shadow: var(--ship-detail-shadow) !important;

    transform: none !important;

}



.seller-shipping-detail-page .seller-shipping-progress-card {

    border-radius: 15px !important;

    box-shadow: var(--ship-detail-shadow-strong) !important;

}



.seller-shipping-detail-page .seller-shipping-detail-card {

    border-radius: 14px !important;

}



/* Static elevation only — no hover lift or movement. */

.seller-shipping-detail-page .seller-shipping-progress-card:hover,

.seller-shipping-detail-page .seller-shipping-detail-card:hover {

    border-color: var(--ship-detail-float-border) !important;

    transform: none !important;

}



.seller-shipping-detail-page .seller-shipping-progress-card:hover {

    box-shadow: var(--ship-detail-shadow-strong) !important;

}



.seller-shipping-detail-page .seller-shipping-detail-card:hover {

    box-shadow: var(--ship-detail-shadow) !important;

}



/* Inner information blocks: softer elevation. */

.seller-shipping-detail-page .seller-shipping-item,

.seller-shipping-detail-page .seller-shipping-hub-field {

    border-color: #E7EAEE !important;

    background: #FFFFFF !important;

    box-shadow: var(--ship-detail-shadow-soft) !important;

    transform: none !important;

}



.seller-shipping-detail-page .seller-shipping-detail-card .border-t {

    border-color: var(--ship-detail-divider) !important;

}



/* Empty / waiting states stay secondary. */

.seller-shipping-detail-page .seller-shipping-timeline .border-dashed,

.seller-shipping-detail-page .seller-shipping-detail-card .border-dashed {

    border-color: #D9DEE5 !important;

    background: #FAFBFC !important;

    box-shadow: 0 4px 12px rgba(15, 23, 42, .03) !important;

}



/* Logistics icon shell: neutral structure, semantic icon color preserved. */

.seller-shipping-detail-page .seller-shipping-hub-icon {

    border-color: #E5E7EB !important;

    background: #F8FAFC !important;

    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;

}



/* Neutral progress / total dividers. */

.seller-shipping-detail-page .seller-shipping-progress-track {

    background: #ECEFF2 !important;

}



.seller-shipping-detail-page .seller-shipping-total-row {

    border-top-color: var(--ship-detail-divider) !important;

}



/* Waybill remains a quiet secondary action; no cream structural hover. */

.seller-shipping-detail-page .seller-shipping-waybill-top {

    border-color: var(--ship-detail-float-border) !important;

    background: #FFFFFF !important;

    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;

    transform: none !important;

}



.seller-shipping-detail-page .seller-shipping-waybill-top:hover,

.seller-shipping-detail-page .seller-shipping-waybill-top:focus-visible {

    border-color: var(--ship-detail-float-border-strong) !important;

    background: #F8FAFC !important;

    color: #9B6505 !important;

    transform: none !important;

}



/* Live state remains semantic but gains restrained depth. */

.seller-shipping-detail-page .seller-shipping-live {

    box-shadow: 0 4px 12px rgba(15, 23, 42, .03) !important;

    transform: none !important;

}



/* Timeline structure remains neutral. */

.seller-shipping-detail-page .seller-shipping-timeline-entry > .absolute {

    background: #E5E7EB !important;

}



.seller-shipping-detail-page .seller-shipping-timeline-entry > .relative {

    box-shadow: 0 0 0 1px #E1E5EA !important;

}



.seller-shipping-detail-page .seller-shipping-item:hover,

.seller-shipping-detail-page .seller-shipping-hub-field:hover,

.seller-shipping-detail-page .seller-shipping-hub-icon:hover {

    transform: none !important;

}



.seller-shipping-detail-page .seller-shipping-progress-card,

.seller-shipping-detail-page .seller-shipping-detail-card,

.seller-shipping-detail-page .seller-shipping-item,

.seller-shipping-detail-page .seller-shipping-hub-field {

    outline: 0 !important;

}



/* Mobile: lighter depth for stacked cards. */

@media (max-width: 640px) {

    .seller-shipping-detail-page .seller-shipping-progress-card,

    .seller-shipping-detail-page .seller-shipping-detail-card {

        box-shadow:

            0 1px 2px rgba(15, 23, 42, .03),

            0 7px 18px rgba(15, 23, 42, .06),

            inset 0 1px 0 rgba(255, 255, 255, .84) !important;

    }



    .seller-shipping-detail-page .seller-shipping-item,

    .seller-shipping-detail-page .seller-shipping-hub-field {

        box-shadow:

            0 1px 2px rgba(15, 23, 42, .02),

            0 5px 13px rgba(15, 23, 42, .045) !important;

    }

}





/* ======================================================================

   SHIPPING DETAIL — MODERN JOURNEY PROGRESS

   Replaces the previous duplicated progress bars with one clear metric,

   one exact meter, and a professional milestone stepper.

   ====================================================================== */



.seller-shipping-detail-page .seller-shipping-progress-card--modern {

    position: relative;

    overflow: hidden;

    padding: 20px 22px 19px !important;

    border: 1px solid #E5E7EB !important;

    border-radius: 16px !important;

    background: #FFFFFF !important;

    box-shadow:

        0 1px 2px rgba(15, 23, 42, .035),

        0 12px 30px rgba(15, 23, 42, .075) !important;

}



.seller-shipping-progress-modern-head {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 24px;

}



.seller-shipping-progress-modern-copy {

    min-width: 0;

}



.seller-shipping-progress-modern-eyebrow {

    margin: 0;

    color: #B97805;

    font-size: 7.5px;

    font-weight: 800;

    line-height: 1;

    letter-spacing: .15em;

    text-transform: uppercase;

}



.seller-shipping-progress-modern-title-row {

    display: flex;

    flex-wrap: wrap;

    align-items: center;

    gap: 8px;

    margin-top: 7px;

}



.seller-shipping-progress-modern-title-row h2 {

    margin: 0;

    color: #172033;

    font-size: 17px;

    font-weight: 750;

    line-height: 1.15;

    letter-spacing: -.025em;

}



.seller-shipping-progress-modern-status {

    display: inline-flex;

    min-height: 24px;

    align-items: center;

    border: 1px solid #E6E8EC;

    border-radius: 999px;

    background: #F8FAFC;

    padding: 0 9px;

    color: #667085;

    font-size: 7.5px;

    font-weight: 700;

    line-height: 1;

    white-space: nowrap;

}



.seller-shipping-progress-modern-route {

    margin: 7px 0 0;

    color: #98A2B3;

    font-size: 8.5px;

    font-weight: 500;

    line-height: 1.45;

}



.seller-shipping-progress-modern-route span {

    margin: 0 3px;

    color: #C3C9D1;

}



.seller-shipping-progress-modern-metric {

    display: grid;

    min-width: 86px;

    justify-items: end;

    flex: 0 0 auto;

}



.seller-shipping-progress-modern-metric strong {

    color: #B97805;

    font-size: 27px;

    font-weight: 800;

    line-height: .95;

    letter-spacing: -.045em;

}



.seller-shipping-progress-modern-metric span {

    margin-top: 6px;

    color: #98A2B3;

    font-size: 7.5px;

    font-weight: 650;

    line-height: 1;

    text-transform: uppercase;

    letter-spacing: .07em;

}



/* One exact progress meter only. */

.seller-shipping-progress-modern-meter {

    height: 5px;

    overflow: hidden;

    margin-top: 18px;

    border-radius: 999px;

    background: #ECEFF2;

}



.seller-shipping-progress-modern-meter > span {

    display: block;

    height: 100%;

    border-radius: inherit;

    background: #D59617;

}



/* Milestone stepper */

.seller-shipping-progress-modern-steps {

    display: grid;

    grid-template-columns: repeat(5, minmax(0, 1fr));

    gap: 0;

    margin: 19px 0 0;

    padding: 0;

    list-style: none;

}



.seller-shipping-progress-modern-step {

    position: relative;

    display: grid;

    grid-template-columns: 30px minmax(0, 1fr);

    align-items: start;

    gap: 9px;

    min-width: 0;

    padding-right: 18px;

}



/* Connector begins after the node and ends before the next node. */

.seller-shipping-progress-modern-step:not(:last-child)::after {

    content: "";

    position: absolute;

    top: 14px;

    left: 30px;

    right: 0;

    height: 1px;

    background: #E4E7EC;

}



.seller-shipping-progress-modern-step.is-segment-complete:not(:last-child)::after {

    background: #D59617;

}



.seller-shipping-progress-modern-node {

    position: relative;

    z-index: 1;

    display: grid;

    width: 29px;

    height: 29px;

    place-items: center;

    border: 1px solid #DDE2E8;

    border-radius: 999px;

    background: #FFFFFF;

    color: #98A2B3;

    box-shadow: 0 2px 7px rgba(15, 23, 42, .035);

}



.seller-shipping-progress-modern-node span {

    font-size: 6.8px;

    font-weight: 750;

    letter-spacing: .03em;

}



.seller-shipping-progress-modern-node svg {

    width: 13px;

    height: 13px;

}



.seller-shipping-progress-modern-step.is-complete .seller-shipping-progress-modern-node {

    border-color: #D59617;

    background: #D59617;

    color: #FFFFFF;

    box-shadow: 0 4px 10px rgba(213, 150, 23, .14);

}



.seller-shipping-progress-modern-step.is-current:not(.is-complete) .seller-shipping-progress-modern-node {

    border-color: #D59617;

    color: #B97805;

    box-shadow:

        0 0 0 4px rgba(213, 150, 23, .08),

        0 2px 7px rgba(15, 23, 42, .035);

}



.seller-shipping-progress-modern-step.is-current.is-complete .seller-shipping-progress-modern-node {

    box-shadow:

        0 0 0 4px rgba(213, 150, 23, .08),

        0 4px 10px rgba(213, 150, 23, .14);

}



.seller-shipping-progress-modern-step-copy {

    min-width: 0;

    padding-top: 1px;

}



.seller-shipping-progress-modern-step-kicker {

    display: block;

    color: #B1B8C3;

    font-size: 6.2px;

    font-weight: 750;

    line-height: 1;

    letter-spacing: .09em;

    text-transform: uppercase;

}



.seller-shipping-progress-modern-step-copy strong {

    display: block;

    overflow: hidden;

    margin-top: 5px;

    color: #667085;

    font-size: 8.5px;

    font-weight: 700;

    line-height: 1.2;

    text-overflow: ellipsis;

    white-space: nowrap;

}



.seller-shipping-progress-modern-step-copy small {

    display: block;

    overflow: hidden;

    margin-top: 3px;

    color: #A4ABB6;

    font-size: 6.8px;

    font-weight: 450;

    line-height: 1.35;

    text-overflow: ellipsis;

    white-space: nowrap;

}



.seller-shipping-progress-modern-step.is-complete .seller-shipping-progress-modern-step-copy strong,

.seller-shipping-progress-modern-step.is-current .seller-shipping-progress-modern-step-copy strong {

    color: #344054;

}



.seller-shipping-progress-modern-step.is-current .seller-shipping-progress-modern-step-kicker {

    color: #B97805;

}



/* Static enterprise card: no physical hover movement. */

.seller-shipping-detail-page .seller-shipping-progress-card--modern:hover {

    border-color: #E5E7EB !important;

    background: #FFFFFF !important;

    box-shadow:

        0 1px 2px rgba(15, 23, 42, .035),

        0 12px 30px rgba(15, 23, 42, .075) !important;

    transform: none !important;

}



@media (max-width: 900px) {

    .seller-shipping-progress-modern-step {

        grid-template-columns: 28px minmax(0, 1fr);

        gap: 7px;

        padding-right: 10px;

    }



    .seller-shipping-progress-modern-node {

        width: 27px;

        height: 27px;

    }



    .seller-shipping-progress-modern-step:not(:last-child)::after {

        top: 13px;

        left: 28px;

    }



    .seller-shipping-progress-modern-step-copy small {

        display: none;

    }

}



@media (max-width: 640px) {

    .seller-shipping-detail-page .seller-shipping-progress-card--modern {

        padding: 16px !important;

    }



    .seller-shipping-progress-modern-head {

        gap: 14px;

    }



    .seller-shipping-progress-modern-title-row h2 {

        font-size: 15px;

    }



    .seller-shipping-progress-modern-metric {

        min-width: 68px;

    }



    .seller-shipping-progress-modern-metric strong {

        font-size: 22px;

    }



    .seller-shipping-progress-modern-route {

        display: none;

    }



    .seller-shipping-progress-modern-meter {

        margin-top: 15px;

    }



    .seller-shipping-progress-modern-steps {

        grid-template-columns: 1fr;

        gap: 0;

        margin-top: 16px;

    }



    .seller-shipping-progress-modern-step {

        grid-template-columns: 30px minmax(0, 1fr);

        gap: 10px;

        min-height: 51px;

        padding: 0 0 10px;

    }



    .seller-shipping-progress-modern-step:not(:last-child)::after {

        top: 29px;

        bottom: 0;

        left: 14px;

        right: auto;

        width: 1px;

        height: auto;

    }



    .seller-shipping-progress-modern-step-copy small {

        display: block;

    }

}





/* ======================================================================

   SHIPPING PROGRESS — STEPPER ALIGNMENT FIX

   Keeps connector lines on the node rail and places all text below it,

   so labels never intersect the progress line.

   ====================================================================== */



.seller-shipping-progress-modern-steps {

    align-items: start !important;

    margin-top: 20px !important;

}



.seller-shipping-progress-modern-step {

    position: relative !important;

    display: block !important;

    min-width: 0 !important;

    padding: 0 18px 0 0 !important;

}



/* Dedicated node rail */

.seller-shipping-progress-modern-node {

    position: relative !important;

    z-index: 2 !important;

    width: 30px !important;

    height: 30px !important;

}



/* Connector now lives only across the icon rail. */

.seller-shipping-progress-modern-step:not(:last-child)::after {

    content: "" !important;

    position: absolute !important;

    z-index: 0 !important;

    top: 14.5px !important;

    left: 30px !important;

    right: -15px !important;

    width: auto !important;

    height: 1px !important;

    background: #E4E7EC !important;

}



.seller-shipping-progress-modern-step.is-segment-complete:not(:last-child)::after {

    background: #D59617 !important;

}



/* Copy sits clearly below the rail. */

.seller-shipping-progress-modern-step-copy {

    position: relative !important;

    z-index: 1 !important;

    min-width: 0 !important;

    margin-top: 10px !important;

    padding: 0 10px 0 0 !important;

}



.seller-shipping-progress-modern-step-kicker {

    margin: 0 !important;

    line-height: 1 !important;

}



.seller-shipping-progress-modern-step-copy strong {

    display: block !important;

    margin-top: 5px !important;

    line-height: 1.25 !important;

    white-space: normal !important;

    overflow: visible !important;

    text-overflow: clip !important;

}



.seller-shipping-progress-modern-step-copy small {

    display: block !important;

    margin-top: 4px !important;

    line-height: 1.4 !important;

    white-space: normal !important;

    overflow: visible !important;

    text-overflow: clip !important;

}



/* Keep the final step visually aligned with the rest. */

.seller-shipping-progress-modern-step:last-child {

    padding-right: 0 !important;

}



/* Slightly tighten long labels without sacrificing readability. */

@media (min-width: 901px) {

    .seller-shipping-progress-modern-step-copy strong {

        max-width: 120px !important;

    }



    .seller-shipping-progress-modern-step-copy small {

        max-width: 125px !important;

    }

}



@media (max-width: 900px) and (min-width: 641px) {

    .seller-shipping-progress-modern-step {

        padding-right: 12px !important;

    }



    .seller-shipping-progress-modern-node {

        width: 28px !important;

        height: 28px !important;

    }



    .seller-shipping-progress-modern-step:not(:last-child)::after {

        top: 13.5px !important;

        left: 28px !important;

        right: -14px !important;

    }



    .seller-shipping-progress-modern-step-copy {

        margin-top: 9px !important;

    }



    .seller-shipping-progress-modern-step-copy small {

        display: none !important;

    }

}



/* Mobile remains vertical, with line at the node column only. */

@media (max-width: 640px) {

    .seller-shipping-progress-modern-steps {

        gap: 0 !important;

    }



    .seller-shipping-progress-modern-step {

        display: grid !important;

        grid-template-columns: 30px minmax(0, 1fr) !important;

        gap: 10px !important;

        min-height: 54px !important;

        padding: 0 0 12px !important;

    }



    .seller-shipping-progress-modern-step-copy {

        margin-top: 1px !important;

        padding-right: 0 !important;

    }



    .seller-shipping-progress-modern-step:not(:last-child)::after {

        top: 30px !important;

        bottom: 0 !important;

        left: 14.5px !important;

        right: auto !important;

        width: 1px !important;

        height: auto !important;

    }



    .seller-shipping-progress-modern-step-copy small {

        display: block !important;

    }

}



/* ======================================================================
   SHIPPING DETAIL — OPEN BUYER / RIDER / LOGISTICS HUB
   Removes nested card/container styling from the requested information
   while preserving all shipping data, payment, logistics fields, routes,
   live polling and backend behavior.
   ====================================================================== */

/* Buyer + Assigned Rider become open metadata rows inside Delivery Details. */
.seller-shipping-detail-page .seller-shipping-delivery-meta {
    display: grid !important;
    gap: 0 !important;
}

.seller-shipping-detail-page .seller-shipping-open-meta {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 0 0 13px !important;
    box-shadow: none !important;
    outline: 0 !important;
    transform: none !important;
}

.seller-shipping-detail-page .seller-shipping-open-meta--rider {
    margin-top: 0 !important;
    border-top: 1px solid #ECEFF2 !important;
    padding-top: 13px !important;
}

/* Payment remains its own existing row, but align its separator with the
   new open metadata treatment. */
.seller-shipping-detail-page .seller-shipping-delivery-meta > .border-t {
    border-top-color: #ECEFF2 !important;
    padding-top: 13px !important;
}

/* Logistics Hub: remove the entire outer card shell. */
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub,
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub:hover {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 2px 0 !important;
    box-shadow: none !important;
    outline: 0 !important;
    transform: none !important;
}

/* Keep the hub heading open/document-like instead of card-like. */
.seller-shipping-detail-page .seller-shipping-open-hub-head {
    padding: 0 2px 10px !important;
    border-bottom: 1px solid #ECEFF2 !important;
}

/* The individual parcel fields are also flattened so the whole Logistics
   Hub area no longer reads as nested cards. */
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .seller-shipping-hub-field,
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .seller-shipping-hub-field:hover {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 10px 2px !important;
    box-shadow: none !important;
    outline: 0 !important;
    transform: none !important;
}

.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .seller-shipping-hub-field + .seller-shipping-hub-field {
    border-top: 1px solid #ECEFF2 !important;
}

/* Two-column Sorting Zone / Received still read as columns, just without
   box shells. */
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 {
    gap: 0 !important;
    border-top: 1px solid #ECEFF2 !important;
    border-bottom: 1px solid #ECEFF2 !important;
}

.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 > .seller-shipping-hub-field + .seller-shipping-hub-field {
    border-top: 0 !important;
    border-left: 1px solid #ECEFF2 !important;
    padding-left: 12px !important;
}

.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 > .seller-shipping-hub-field:first-child {
    padding-right: 12px !important;
}

/* Empty hub state is also open: no dashed mini-container. */
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .border-dashed {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 16px 2px !important;
    box-shadow: none !important;
}

/* Icon remains a small semantic marker, not a container. */
.seller-shipping-detail-page .seller-shipping-detail-card--open-hub .seller-shipping-hub-icon {
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

/* Mobile: use simple horizontal separation only. */
@media (max-width: 640px) {
    .seller-shipping-detail-page .seller-shipping-open-meta {
        padding-bottom: 12px !important;
    }

    .seller-shipping-detail-page .seller-shipping-open-meta--rider {
        padding-top: 12px !important;
    }

    .seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 {
        grid-template-columns: 1fr !important;
    }

    .seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 > .seller-shipping-hub-field + .seller-shipping-hub-field {
        border-left: 0 !important;
        border-top: 1px solid #ECEFF2 !important;
        padding-left: 2px !important;
    }

    .seller-shipping-detail-page .seller-shipping-detail-card--open-hub .grid.grid-cols-2 > .seller-shipping-hub-field:first-child {
        padding-right: 2px !important;
    }
}
</style>



@endpush



@section('content')



@php



    $parcel = $order->logisticsParcel;



    $toneClasses = [



        'success' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',



        'info' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',



        'hub' => 'border-[#d8d8ea] bg-[#f5f5fb] text-[#63638d]',



        'warning' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',



    ];



    $tone = $toneClasses[$shippingState['tone']] ?? $toneClasses['warning'];



    $kindStyle = fn (string $kind) => match ($kind) {



        'success' => ['bg-[#4f7d63]', 'text-[#4f7d63]'],



        'logistics' => ['bg-[#63638d]', 'text-[#63638d]'],



        'rider' => ['bg-[#3c6e91]', 'text-[#3c6e91]'],



        'seller' => ['bg-[#d89b10]', 'text-[#b97805]'],



        default => ['bg-[#8a919b]', 'text-[#6b7280]'],



    };



    $items = collect($order->items ?? []);



@endphp



<div class="seller-shipping-detail-page">



    <section class="seller-shipping-detail-header">



        <div class="seller-shipping-detail-heading">



            <p class="seller-shipping-detail-eyebrow">Fulfillment & Logistics</p>



            <h1 class="seller-shipping-detail-title">Shipment <span>Tracking</span></h1>



            <div class="seller-shipping-detail-meta">



                <a href="{{ route('seller.shipping.index') }}" class="seller-shipping-back">



                    <span aria-hidden="true">←</span>



                    Back to Shipping



                </a>



                <span class="text-[#d0d5dd]" aria-hidden="true">•</span>



                <strong class="seller-shipping-order-id">{{ $order->order_number }}</strong>



                <span class="inline-flex rounded-full border px-2.5 py-1 text-[7.5px] font-bold uppercase tracking-[.08em] {{ $tone }}">



                    {{ $shippingState['label'] }}



                </span>



            </div>



            <p class="seller-shipping-detail-subtitle">{{ $shippingState['detail'] }}</p>



        </div>



        <div class="seller-shipping-detail-actions">



            <span id="sellerShippingLiveIndicator" class="seller-shipping-live">



                <span class="h-1.5 w-1.5 rounded-full bg-[#4f7d63]"></span>



                Live tracking



            </span>



            @if(Route::has('seller.orders.waybill'))



                <a href="{{ route('seller.orders.waybill', $order) }}" class="seller-shipping-waybill-top">



                    View Waybill



                </a>



            @endif



        </div>



    </section>



    @php

        $progressPercent = max(0, min(100, (int) ($shippingState['progress'] ?? 0)));



        $shipmentMilestones = [

            ['label' => 'Ready', 'caption' => 'Seller prepared', 'threshold' => 10],

            ['label' => 'Rider Pickup', 'caption' => 'Handoff to rider', 'threshold' => 35],

            ['label' => 'Logistics Hub', 'caption' => 'Hub processing', 'threshold' => 65],

            ['label' => 'Final Mile', 'caption' => 'Out for delivery', 'threshold' => 85],

            ['label' => 'Delivered', 'caption' => 'Buyer received', 'threshold' => 100],

        ];



        $currentMilestoneIndex = count($shipmentMilestones) - 1;



        foreach ($shipmentMilestones as $milestoneIndex => $milestone) {

            if ($progressPercent < $milestone['threshold']) {

                $currentMilestoneIndex = $milestoneIndex;

                break;

            }

        }

    @endphp



    <section class="seller-shipping-progress-card seller-shipping-progress-card--modern">

        <div class="seller-shipping-progress-modern-head">

            <div class="seller-shipping-progress-modern-copy">

                <p class="seller-shipping-progress-modern-eyebrow">Shipment progress</p>



                <div class="seller-shipping-progress-modern-title-row">

                    <h2>Delivery journey</h2>



                    <span class="seller-shipping-progress-modern-status">

                        {{ $progressPercent >= 100 ? 'Completed' : $shippingState['label'] }}

                    </span>

                </div>



                <p class="seller-shipping-progress-modern-route">

                    Seller <span>→</span> Rider <span>→</span> Logistics Hub <span>→</span> Buyer

                </p>

            </div>



            <div class="seller-shipping-progress-modern-metric" aria-label="{{ $progressPercent }} percent complete">

                <strong>{{ $progressPercent }}%</strong>

                <span>{{ $progressPercent >= 100 ? 'Complete' : 'In progress' }}</span>

            </div>

        </div>



        <div

            class="seller-shipping-progress-modern-meter"

            role="progressbar"

            aria-valuemin="0"

            aria-valuemax="100"

            aria-valuenow="{{ $progressPercent }}"

            aria-label="Shipment completion"

        >

            <span style="width: {{ $progressPercent }}%"></span>

        </div>



        <ol class="seller-shipping-progress-modern-steps" aria-label="Shipment milestones">

            @foreach($shipmentMilestones as $milestone)

                @php

                    $isComplete = $progressPercent >= $milestone['threshold'];

                    $isCurrent = $loop->index === $currentMilestoneIndex;

                    $nextThreshold = $shipmentMilestones[$loop->index + 1]['threshold'] ?? null;

                    $segmentComplete = $nextThreshold !== null && $progressPercent >= $nextThreshold;

                @endphp



                <li

                    class="seller-shipping-progress-modern-step

                        {{ $isComplete ? 'is-complete' : '' }}

                        {{ $isCurrent ? 'is-current' : '' }}

                        {{ $segmentComplete ? 'is-segment-complete' : '' }}"

                >

                    <div class="seller-shipping-progress-modern-node" aria-hidden="true">

                        @if($isComplete)

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">

                                <path d="m7 12 3 3 7-7"></path>

                            </svg>

                        @else

                            <span>{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>

                        @endif

                    </div>



                    <div class="seller-shipping-progress-modern-step-copy">

                        <span class="seller-shipping-progress-modern-step-kicker">

                            Step {{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}

                        </span>

                        <strong>{{ $milestone['label'] }}</strong>

                        <small>{{ $milestone['caption'] }}</small>

                    </div>

                </li>

            @endforeach

        </ol>

    </section>



    <div class="seller-shipping-detail-grid">



        <div class="seller-shipping-main-stack">



            <section class="seller-shipping-detail-card">



                <div class="flex items-center justify-between gap-4">



                    <div>



                        <p class="seller-shipping-section-eyebrow">



                            Shipment Activity



                        </p>



                        <h2 class="seller-shipping-section-title">Tracking Timeline</h2>



                    </div>



                    <span class="text-[8px] text-[#9aa0a6]">Newest first</span>



                </div>



                <div class="seller-shipping-timeline">



                    @forelse($timeline as $index => $entry)



                        @php



                            [$dotClass, $textClass] = $kindStyle($entry['kind']);



                        @endphp



                        <div class="seller-shipping-timeline-entry {{ !$loop->last ? 'pb-6' : '' }}">



                            @if(!$loop->last)



                                <span class="absolute left-[7px] top-5 bottom-0 w-px bg-[#e5e8ec]"></span>



                            @endif



                            <span class="relative z-10 mt-1.5 h-[15px] w-[15px] shrink-0 rounded-full border-[4px] border-white {{ $dotClass }} shadow-[0_0_0_1px_#e1e4e8]"></span>



                            <div class="min-w-0 flex-1">



                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">



                                    <p class="text-[10px] font-semibold {{ $textClass }}">



                                        {{ $entry['title'] }}



                                    </p>



                                    <time class="text-[7px] text-[#9aa0a6]">



                                        {{ $entry['time']->format('M d, Y · h:i A') }}



                                    </time>



                                </div>



                                <p class="mt-1.5 text-[9px] leading-5 text-[#7b8089]">



                                    {{ $entry['detail'] }}



                                </p>



                            </div>



                        </div>



                    @empty



                        <div class="rounded-[15px] border border-dashed border-[#dfe3e8] bg-[#fafafb] px-4 py-9 text-center">



                            <p class="text-[9px] font-semibold text-[#6b7280]">No tracking activity yet.</p>



                        </div>



                    @endforelse



                </div>



            </section>



            <section class="seller-shipping-detail-card">



                <p class="seller-shipping-section-eyebrow">Order Contents</p>



                <h2 class="seller-shipping-section-title">Items in this shipment</h2>



                <div class="mt-5 space-y-2.5">



                    @forelse($items as $item)



                        <div class="seller-shipping-item">



                            <div class="min-w-0">



                                <p class="truncate text-[9px] font-semibold text-[#374151]">



                                    {{ $item['name'] ?? 'Marketplace item' }}



                                </p>



                                @if(!empty($item['variant_label']))



                                    <p class="seller-shipping-detail-secondary">



                                        {{ $item['variant_label'] }}



                                    </p>



                                @endif



                            </div>



                            <div class="shrink-0 text-right">



                                <p class="text-[9px] font-semibold text-[#374151]">



                                    × {{ (int) ($item['qty'] ?? 1) }}



                                </p>



                                <p class="seller-shipping-detail-secondary">



                                    ₱{{ number_format((float) ($item['line_total'] ?? 0), 2) }}



                                </p>



                            </div>



                        </div>



                    @empty



                        <p class="text-[9px] text-[#8a919b]">No item snapshot available.</p>



                    @endforelse



                </div>



            </section>



        </div>



        <aside class="seller-shipping-side-stack">



            <section class="seller-shipping-detail-card">



                <p class="seller-shipping-section-eyebrow">Delivery Details</p>



                <div class="mt-5 space-y-4">



                    <div>



                        <p class="seller-shipping-detail-label">Buyer</p>



                        <p class="seller-shipping-detail-value">{{ $order->buyer_name ?: 'SARI Buyer' }}</p>



                        <p class="seller-shipping-detail-secondary">{{ $order->buyer_address }}</p>



                    </div>



                    <div class="border-t border-[#eceef1] pt-4">



                        <p class="seller-shipping-detail-label">Assigned Rider</p>



                        <p class="seller-shipping-detail-value">{{ $order->courier_name ?: 'Not assigned yet' }}</p>



                        <p class="seller-shipping-detail-secondary">{{ $order->courier_email ?: 'Waiting for Logistics assignment' }}</p>



                    </div>



                    <div class="border-t border-[#eceef1] pt-4">



                        <div class="flex items-center justify-between gap-4">



                            <div>



                                <p class="seller-shipping-detail-label">Payment</p>



                                <p class="seller-shipping-detail-value">{{ $order->payment_method }}</p>



                            </div>



                            <span class="rounded-full bg-[#f5f6f7] px-2.5 py-1 text-[7px] font-bold uppercase text-[#6b7280]">



                                {{ $order->payment_status }}



                            </span>



                        </div>



                    </div>



                </div>



            </section>



            <section class="seller-shipping-detail-card">



                <div class="flex items-center justify-between gap-4">



                    <div>



                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#63638d]">Logistics Hub</p>



                        <h2 class="seller-shipping-section-title">Parcel Processing</h2>



                    </div>



                    <span class="seller-shipping-hub-icon">



                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">



                            <path d="M4 19V7l8-4 8 4v12"></path>



                            <path d="M8 19v-5h8v5"></path>



                            <path d="M8 9h.01M12 9h.01M16 9h.01"></path>



                        </svg>



                    </span>



                </div>



                @if($parcel)



                    <div class="mt-5 space-y-3">



                        <div class="seller-shipping-hub-field">



                            <p class="seller-shipping-detail-label">Parcel Status</p>



                            <p class="seller-shipping-detail-value">



                                {{ ucwords(str_replace('_', ' ', $parcel->status)) }}



                            </p>



                        </div>



                        <div class="grid grid-cols-2 gap-3">



                            <div class="seller-shipping-hub-field">



                                <p class="seller-shipping-detail-label">Sorting Zone</p>



                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">{{ $parcel->sorting_zone ?: '—' }}</p>



                            </div>



                            <div class="seller-shipping-hub-field">



                                <p class="seller-shipping-detail-label">Received</p>



                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">



                                    {{ $parcel->received_at?->format('M d, h:i A') ?: '—' }}



                                </p>



                            </div>



                        </div>



                        @if($parcel->notes)



                            <div class="seller-shipping-hub-field">



                                <p class="seller-shipping-detail-label">Hub Notes</p>



                                <p class="mt-1.5 text-[8px] leading-4 text-[#7b8089]">{{ $parcel->notes }}</p>



                            </div>



                        @endif



                    </div>



                @else



                    <div class="mt-5 rounded-[14px] border border-dashed border-[#dfe3e8] bg-[#fafafb] px-4 py-7 text-center">



                        <p class="text-[9px] font-semibold text-[#6b7280]">Parcel has not entered Logistics processing yet.</p>



                    </div>



                @endif



            </section>



            <section class="seller-shipping-detail-card">



                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">Order Value</p>



                <div class="mt-4">



                    <div class="seller-shipping-value-row">



                        <span class="text-[#8a919b]">Subtotal</span>



                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->subtotal, 2) }}</span>



                    </div>



                    <div class="seller-shipping-value-row">



                        <span class="text-[#8a919b]">Delivery</span>



                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->delivery_fee, 2) }}</span>



                    </div>



                    <div class="seller-shipping-total-row">



                        <span class="font-semibold text-[#374151]">Total</span>



                        <span class="text-[13px] font-bold text-[#b97805]">₱{{ number_format((float) $order->total, 2) }}</span>



                    </div>



                </div>



            </section>



        </aside>



    </div>



</div>



@push('scripts')



<script>



(() => {



    const endpoint = @json(route('seller.shipping.live-state', $order));



    let revision = @json($revision);



    const indicator = document.getElementById('sellerShippingLiveIndicator');



    async function checkShipment() {



        if (document.visibilityState !== 'visible') return;



        try {



            const response = await fetch(endpoint, {



                headers: {



                    'Accept': 'application/json',



                    'X-Requested-With': 'XMLHttpRequest',



                },



                credentials: 'same-origin',



                cache: 'no-store',



            });



            if (!response.ok) return;



            const payload = await response.json();



            if (payload.revision && payload.revision !== revision) {



                if (indicator) {



                    indicator.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-[#d89b10]"></span> Updating…';



                }



                window.location.reload();



            }



        } catch (error) {



            if (indicator) {



                indicator.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-[#8a919b]"></span> Refresh to update';



                indicator.className = 'seller-shipping-live';



            }



        }



    }



    window.setInterval(checkShipment, 8000);



})();



</script>



@endpush



@endsection
