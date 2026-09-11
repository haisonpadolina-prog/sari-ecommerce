@extends('layouts.admin')

@section('title', 'Seller Account Control — SARI Admin')
@section('page-title', 'Seller Account Control')

@section('content')

<style>
    .seller-account-control-page {
        --sac-xs: clamp(0.74rem, 0.70rem + 0.08vw, 0.82rem);
        --sac-sm: clamp(0.80rem, 0.75rem + 0.10vw, 0.90rem);
        --sac-md: clamp(0.88rem, 0.82rem + 0.14vw, 0.98rem);
        --sac-lg: clamp(1rem, 0.93rem + 0.18vw, 1.14rem);
        --sac-title: clamp(1.45rem, 1.28rem + 0.38vw, 1.85rem);
    }

    .seller-account-control-page .account-control-surface {
        box-shadow: 0 10px 28px rgba(45, 37, 28, .035);
    }

    .seller-account-control-page .account-control-summary-card {
        min-height: 104px;
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .seller-account-control-page .account-control-summary-card:hover {
        transform: translateY(-1px);
        border-color: #ddd3c7;
        box-shadow: 0 12px 28px rgba(45, 37, 28, .045);
    }

    .seller-account-control-page .account-control-workspace {
        box-shadow: 0 12px 30px rgba(45, 37, 28, .035);
    }

    .seller-account-control-page .account-control-row {
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background-color .18s ease;
    }

    .seller-account-control-page .account-control-row:hover {
        border-color: #ddd4c7;
        background: #fffdfa;
        box-shadow: 0 11px 24px rgba(45, 37, 28, .042);
    }

    .seller-account-control-page .account-control-input {
        color: #332e28 !important;
        -webkit-text-fill-color: #332e28 !important;
        background: #fff !important;
        font-size: var(--sac-sm) !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .seller-account-control-page .account-control-input::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
    }

    .seller-account-control-page .account-control-input:focus {
        outline: none;
        border-color: #c99128 !important;
        box-shadow: 0 0 0 4px rgba(201, 145, 40, .08);
    }

    .seller-account-control-page .account-control-action {
        min-height: 40px;
        font-size: var(--sac-sm) !important;
    }

    [data-seller-control-modal][hidden] {
        display: none !important;
    }

    @media (min-width: 1280px) {
        .seller-account-control-page .account-control-row-grid {
            display: grid;
            grid-template-columns:
                minmax(260px, 1.6fr)
                130px
                120px
                140px
                165px
                92px;
            align-items: center;
            column-gap: 1rem;
        }
    }

    @media (max-width: 639px) {
        .seller-account-control-page {
            --sac-xs: .76rem;
            --sac-sm: .82rem;
            --sac-md: .90rem;
            --sac-lg: 1rem;
        }

        .seller-account-control-page .account-control-summary-card {
            min-height: 100px;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LIGHTER SELLER-COMPLIANCE TYPOGRAPHY
    |--------------------------------------------------------------------------
    | Match the visual weight from the Seller Compliance reference:
    | less black, less bold, softer brown/gray hierarchy.
    */
    .seller-account-control-page {
        color: #4b433b;
    }

    .seller-account-control-page .sac-page-title {
        color: #2f2923 !important;
        font-weight: 650 !important;
        letter-spacing: -0.025em !important;
    }

    .seller-account-control-page .sac-page-subtitle {
        color: #8a8075 !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-summary-label {
        color: #8b7568 !important;
        font-weight: 500 !important;
    }

    .seller-account-control-page .sac-summary-value {
        color: #332c25 !important;
        font-weight: 650 !important;
        letter-spacing: -0.025em !important;
    }

    .seller-account-control-page .sac-workspace-title {
        color: #3a332c !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-workspace-copy,
    .seller-account-control-page .sac-result-copy {
        color: #948a7f !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-column-label {
        color: #8f7d6d !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-seller-name {
        color: #3a332d !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-muted {
        color: #9a9085 !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-badge {
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-data-value {
        color: #514940 !important;
        font-weight: 500 !important;
    }

    .seller-account-control-page .sac-action-button {
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-modal-title {
        color: #3a332c !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-modal-section-title {
        color: #514940 !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-modal-value {
        color: #514940 !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .account-control-surface {
        box-shadow: 0 8px 22px rgba(45, 37, 28, .025);
    }

    .seller-account-control-page .account-control-summary-card {
        box-shadow: 0 6px 16px rgba(45, 37, 28, .022);
    }

    .seller-account-control-page .account-control-summary-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 9px 20px rgba(45, 37, 28, .032);
    }

    .seller-account-control-page .account-control-workspace {
        box-shadow: 0 9px 24px rgba(45, 37, 28, .025);
    }

    .seller-account-control-page .account-control-row {
        box-shadow: none;
    }

    .seller-account-control-page .account-control-row:hover {
        background: #fffdfa;
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
    }

    /*
    |--------------------------------------------------------------------------
    | COMPACT SELLER ACCOUNT WORKSPACE
    |--------------------------------------------------------------------------
    | Keep the summary cards readable, but make the actual Seller Accounts
    | workspace closer to the lighter/compact Seller Compliance reference.
    */
    .seller-account-control-page .account-control-workspace {
        --sac-list-xs: clamp(.67rem, .64rem + .05vw, .72rem);
        --sac-list-sm: clamp(.72rem, .68rem + .07vw, .78rem);
        --sac-list-md: clamp(.78rem, .73rem + .08vw, .84rem);
        --sac-list-title: clamp(.84rem, .79rem + .10vw, .92rem);
    }

    .seller-account-control-page .account-control-workspace .sac-workspace-title {
        font-size: var(--sac-list-title) !important;
        font-weight: 600 !important;
        color: #453d35 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-workspace-copy,
    .seller-account-control-page .account-control-workspace .sac-result-copy {
        font-size: var(--sac-list-xs) !important;
        line-height: 1.45 !important;
        color: #988e83 !important;
    }

    .seller-account-control-page .account-control-workspace .account-control-input {
        min-height: 40px !important;
        height: 40px !important;
        font-size: var(--sac-list-sm) !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .account-control-workspace select.account-control-input {
        font-weight: 500 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-column-label {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
        color: #917f70 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-seller-name {
        font-size: var(--sac-list-md) !important;
        font-weight: 600 !important;
        color: #443b33 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-muted {
        font-size: var(--sac-list-xs) !important;
        color: #9e9489 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-badge {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-data-value {
        font-size: var(--sac-list-sm) !important;
        font-weight: 500 !important;
        color: #595047 !important;
    }

    .seller-account-control-page .account-control-workspace [class*="xl:hidden"] {
        font-size: var(--sac-list-xs) !important;
    }

    .seller-account-control-page .account-control-workspace #sellerAccountClearFilters,
    .seller-account-control-page .account-control-workspace #sellerAccountEmptyClear {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
    }

    @media (min-width: 1280px) {
        .seller-account-control-page .account-control-workspace .account-control-row-grid {
            grid-template-columns:
                minmax(260px, 1.65fr)
                120px
                115px
                130px
                150px
                90px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-account-control-page .account-control-summary-card,
        .seller-account-control-page .account-control-row,
        .seller-account-control-page .account-control-input {
            transition: none !important;
            transform: none !important;
        }
    }

    /* =========================================================
       SARI MASTER ADMIN HEADER — SELLER ACCOUNT CONTROL
       Matches Seller Compliance / Approved Accounts hierarchy.
       Header-only final override; existing workspace logic is untouched.
       ========================================================= */

    .seller-account-control-page .sac-page-header {
        margin-bottom: 16px !important;
        padding: 0 !important;
    }

    .seller-account-control-page .sac-page-header-main {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .seller-account-control-page .sac-page-header-icon {
        display: grid;
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 14px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow:
            0 2px 5px rgba(75,54,25,.03),
            0 9px 20px rgba(75,54,25,.06) !important;
    }

    .seller-account-control-page .sac-page-header-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .seller-account-control-page .sac-page-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        -webkit-text-fill-color: #9a7b43 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .seller-account-control-page .sac-page-heading {
        margin: 4px 0 0 !important;
        font-size: clamp(1.75rem, 1.55rem + .5vw, 2.15rem) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    .seller-account-control-page .sac-page-heading-base {
        color: #17130f !important;
        -webkit-text-fill-color: #17130f !important;
    }

    .seller-account-control-page .sac-page-heading-accent {
        color: #d99500 !important;
        -webkit-text-fill-color: #d99500 !important;
    }

    .seller-account-control-page .sac-page-header-copy {
        max-width: 840px !important;
        margin: 6px 0 0 !important;
        color: #81786c !important;
        -webkit-text-fill-color: #81786c !important;
        font-size: clamp(.73rem, .70rem + .08vw, .81rem) !important;
        font-weight: 400 !important;
        line-height: 1.65 !important;
        letter-spacing: 0 !important;
    }

    .seller-account-control-page .sac-header-back {
        min-height: 39px !important;
        border-radius: 10px !important;
        padding-inline: 16px !important;
        font-size: clamp(.72rem, .69rem + .06vw, .78rem) !important;
        font-weight: 600 !important;
        border-color: #e0bd76 !important;
        background: #d99500 !important;
        color: #fff !important;
        box-shadow:
            0 3px 7px rgba(183,124,0,.09),
            0 12px 26px rgba(217,149,0,.20) !important;
    }

    .seller-account-control-page .sac-header-back:hover {
        transform: translateY(-1px);
        border-color: #bd8205 !important;
        background: #bd8205 !important;
        box-shadow:
            0 4px 8px rgba(183,124,0,.11),
            0 15px 32px rgba(217,149,0,.23) !important;
    }

    @media (max-width: 767px) {
        .seller-account-control-page .sac-page-header-main {
            align-items: flex-start;
            gap: 12px;
        }

        .seller-account-control-page .sac-page-header-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
            border-radius: 13px !important;
        }

        .seller-account-control-page .sac-page-eyebrow {
            font-size: 8.5px !important;
        }

        .seller-account-control-page .sac-page-heading {
            font-size: 1.65rem !important;
        }

        .seller-account-control-page .sac-page-header-copy {
            font-size: .72rem !important;
        }

        .seller-account-control-page .sac-header-back {
            width: 100%;
        }
    }


    /* =========================================================
       SELLER ACCOUNT MODAL — MODERN / CLEAN / VIOLATION-AWARE
       Visual + disclosure layer only.
       Existing moderation forms, routes, status logic, and JS are retained.
       ========================================================= */

    .seller-account-control-page [data-seller-control-modal] {
        background: rgba(28, 24, 20, .48) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .seller-account-control-page .sac-modern-modal {
        position: relative;
        width: min(860px, calc(100vw - 32px)) !important;
        max-width: 860px !important;
        max-height: min(92vh, 900px) !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 24px !important;
        background: #fff !important;
        box-shadow:
            0 12px 28px rgba(32, 25, 18, .10),
            0 36px 86px rgba(32, 25, 18, .20),
            0 72px 140px rgba(32, 25, 18, .10) !important;
    }

    .seller-account-control-page .sac-modern-modal::before {
        content: "";
        position: absolute;
        z-index: 5;
        top: 0;
        left: 24px;
        width: 64px;
        height: 3px;
        border-radius: 0 0 999px 999px;
        background: #d99500;
    }

    .seller-account-control-page .sac-modern-modal-header {
        position: relative;
        z-index: 2;
        padding: 20px 22px !important;
        border-bottom-color: #eee7de !important;
        background: #fff !important;
    }

    .seller-account-control-page .sac-modern-avatar {
        width: 46px !important;
        height: 46px !important;
        border-radius: 13px !important;
        background: #373129 !important;
        box-shadow:
            0 3px 7px rgba(37,29,19,.08),
            0 11px 24px rgba(37,29,19,.14) !important;
    }

    .seller-account-control-page .sac-modern-modal-title {
        color: #302a24 !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
    }

    .seller-account-control-page .sac-modern-modal-close {
        border-radius: 11px !important;
        background: #fff !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 8px 18px rgba(61,43,22,.045) !important;
    }

    .seller-account-control-page .sac-modern-modal-body {
        padding: 22px !important;
        background: #fcfbf8 !important;
    }

    /* KPI strip — deliberately unboxed */
    .seller-account-control-page .sac-modern-kpis {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 0 !important;
        padding: 2px 0 18px;
        border-bottom: 1px solid #e9e1d7;
    }

    .seller-account-control-page .sac-modern-kpi {
        position: relative;
        min-width: 0;
        padding: 5px 20px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-kpi:first-child {
        padding-left: 2px !important;
    }

    .seller-account-control-page .sac-modern-kpi:last-child {
        padding-right: 2px !important;
    }

    .seller-account-control-page .sac-modern-kpi + .sac-modern-kpi::before {
        content: "";
        position: absolute;
        top: 3px;
        bottom: 3px;
        left: 0;
        width: 1px;
        background: #eae2d8;
    }

    .seller-account-control-page .sac-modern-kpi-label {
        color: #978d82 !important;
        font-size: .7rem !important;
        font-weight: 500 !important;
        line-height: 1.3 !important;
    }

    .seller-account-control-page .sac-modern-kpi-value {
        margin-top: 8px !important;
        color: #302a24 !important;
        font-size: 1.48rem !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.045em !important;
    }

    .seller-account-control-page .sac-modern-kpi-value.is-warning {
        color: #9c691e !important;
    }

    .seller-account-control-page .sac-modern-kpi-value.is-danger {
        color: #a65d5d !important;
    }

    /* Violation disclosure */
    .seller-account-control-page .sac-violations {
        margin-top: 16px;
        overflow: hidden;
        border: 1px solid #e7dfd4;
        border-radius: 16px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61,43,22,.02),
            0 10px 24px rgba(61,43,22,.035);
    }

    .seller-account-control-page .sac-violations > summary {
        display: flex;
        min-height: 58px;
        cursor: pointer;
        list-style: none;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 13px 15px;
        outline: none;
        transition: background-color .16s ease;
    }

    .seller-account-control-page .sac-violations > summary::-webkit-details-marker {
        display: none;
    }

    .seller-account-control-page .sac-violations > summary:hover,
    .seller-account-control-page .sac-violations[open] > summary {
        background: #fffdf9;
    }

    .seller-account-control-page .sac-violation-icon {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 1px solid #eedfbe;
        border-radius: 10px;
        background: #fff8e9;
        color: #aa741e;
    }

    .seller-account-control-page .sac-violation-summary-title {
        color: #3d362f;
        font-size: .82rem;
        font-weight: 650;
        letter-spacing: -.015em;
    }

    .seller-account-control-page .sac-violation-summary-copy {
        margin-top: 2px;
        color: #958b80;
        font-size: .68rem;
        line-height: 1.45;
    }

    .seller-account-control-page .sac-violation-count {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        justify-content: center;
        border: 1px solid #eadfce;
        border-radius: 999px;
        background: #faf8f4;
        padding: 0 9px;
        color: #756a5f;
        font-size: .65rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .seller-account-control-page .sac-violation-chevron {
        width: 16px;
        height: 16px;
        color: #958b80;
        transition: transform .18s ease;
    }

    .seller-account-control-page .sac-violations[open] .sac-violation-chevron {
        transform: rotate(180deg);
    }

    .seller-account-control-page .sac-violation-list {
        border-top: 1px solid #eee7de;
        background: #fff;
    }

    .seller-account-control-page .sac-violation-row {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: start;
        padding: 13px 15px;
    }

    .seller-account-control-page .sac-violation-row + .sac-violation-row {
        border-top: 1px solid #f0ebe4;
    }

    .seller-account-control-page .sac-violation-number {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        justify-content: center;
        border: 1px solid #ecd9bd;
        border-radius: 9px;
        background: #fff8eb;
        color: #9d6c21;
        font-size: .65rem;
        font-weight: 700;
    }

    .seller-account-control-page .sac-violation-reason {
        color: #494139;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.45;
    }

    .seller-account-control-page .sac-violation-product,
    .seller-account-control-page .sac-violation-date {
        color: #998f84;
        font-size: .65rem;
        line-height: 1.45;
    }

    .seller-account-control-page .sac-violation-product {
        margin-top: 3px;
    }

    .seller-account-control-page .sac-violation-date {
        padding-top: 2px;
        text-align: right;
        white-space: nowrap;
    }

    .seller-account-control-page .sac-violation-empty {
        padding: 18px 15px;
        color: #91877c;
        font-size: .7rem;
        line-height: 1.55;
        text-align: center;
    }

    /* Status reason — not a competing card */
    .seller-account-control-page .sac-modern-status-reason {
        margin-top: 14px !important;
        padding: 12px 14px !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    /* Actions — clean section, equal buttons */
    .seller-account-control-page .sac-modern-actions {
        margin-top: 16px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-actions-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 11px;
    }

    .seller-account-control-page .sac-modern-actions-title {
        color: #3c352e !important;
        font-size: .82rem !important;
        font-weight: 650 !important;
        letter-spacing: -.015em;
    }

    .seller-account-control-page .sac-modern-actions-copy {
        margin-top: 3px !important;
        color: #958b80 !important;
        font-size: .67rem !important;
        line-height: 1.45 !important;
    }

    .seller-account-control-page .sac-modern-actions-grid {
        display: grid !important;
        gap: 10px !important;
        margin-top: 0 !important;
        align-items: stretch;
    }

    .seller-account-control-page .sac-modern-actions-grid.is-three {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .seller-account-control-page .sac-modern-actions-grid.is-two {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-account-control-page .sac-modern-actions-grid.is-one {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    .seller-account-control-page .sac-modern-actions-grid > form,
    .seller-account-control-page .sac-modern-actions-grid > button {
        display: flex;
        width: 100%;
        min-width: 0;
        margin: 0 !important;
    }

    .seller-account-control-page .sac-modern-action {
        display: inline-flex !important;
        width: 100% !important;
        min-width: 0 !important;
        height: 50px !important;
        min-height: 50px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        padding: 0 12px !important;
        border-radius: 12px !important;
        font-size: .77rem !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        white-space: nowrap;
        box-shadow:
            0 2px 4px rgba(61,43,22,.018),
            0 8px 18px rgba(61,43,22,.035),
            inset 0 1px 0 rgba(255,255,255,.92) !important;
        transition:
            transform .16s ease,
            border-color .16s ease,
            background-color .16s ease,
            box-shadow .16s ease !important;
    }

    .seller-account-control-page .sac-modern-action svg {
        width: 14px !important;
        height: 14px !important;
        flex: 0 0 14px;
    }

    .seller-account-control-page .sac-modern-action:hover {
        transform: translateY(-1px);
        box-shadow:
            0 3px 6px rgba(61,43,22,.025),
            0 11px 24px rgba(61,43,22,.055) !important;
    }

    .seller-account-control-page .sac-modern-action.is-suspend {
        border-color: #ead8b6 !important;
        background: #fff9ee !important;
        color: #96691f !important;
    }

    .seller-account-control-page .sac-modern-action.is-suspend:hover {
        border-color: #dfc38e !important;
        background: #fff4e0 !important;
    }

    .seller-account-control-page .sac-modern-action.is-ban {
        border-color: #ebcccc !important;
        background: #fff6f6 !important;
        color: #a45d5d !important;
    }

    .seller-account-control-page .sac-modern-action.is-ban:hover {
        border-color: #dfb7b7 !important;
        background: #fff0f0 !important;
    }

    .seller-account-control-page .sac-modern-action.is-deactivate {
        border-color: #e2dbd2 !important;
        background: #f8f6f3 !important;
        color: #655d54 !important;
    }

    .seller-account-control-page .sac-modern-action.is-deactivate:hover {
        border-color: #d4c8bc !important;
        background: #f2efea !important;
    }

    .seller-account-control-page .sac-modern-action.is-positive {
        border-color: #cfe1d5 !important;
        background: #f2f8f4 !important;
        color: #5f836b !important;
    }

    .seller-account-control-page .sac-modern-action.is-positive:hover {
        background: #eaf5ed !important;
    }

    /* Quiet policy note */
    .seller-account-control-page .sac-modern-note {
        margin-top: 15px !important;
        padding: 11px 13px !important;
        border: 1px solid #eadcc4 !important;
        border-radius: 12px !important;
        background: #fffaf2 !important;
        box-shadow: inset 3px 0 0 #d99500 !important;
    }

    .seller-account-control-page .sac-modern-note p {
        color: #776957 !important;
        font-size: .69rem !important;
        line-height: 1.55 !important;
    }

    @media (max-width: 639px) {
        .seller-account-control-page .sac-modern-modal {
            width: calc(100vw - 16px) !important;
            max-height: 95vh !important;
            border-radius: 19px !important;
        }

        .seller-account-control-page .sac-modern-modal-header,
        .seller-account-control-page .sac-modern-modal-body {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .seller-account-control-page .sac-modern-kpis {
            grid-template-columns: 1fr !important;
            padding-bottom: 4px;
        }

        .seller-account-control-page .sac-modern-kpi {
            padding: 11px 2px !important;
        }

        .seller-account-control-page .sac-modern-kpi + .sac-modern-kpi::before {
            top: 0;
            right: 0;
            bottom: auto;
            left: 0;
            width: auto;
            height: 1px;
        }

        .seller-account-control-page .sac-modern-kpi-value {
            font-size: 1.3rem !important;
        }

        .seller-account-control-page .sac-violation-row {
            grid-template-columns: 38px minmax(0, 1fr);
        }

        .seller-account-control-page .sac-violation-date {
            grid-column: 2;
            text-align: left;
            white-space: normal;
        }

        .seller-account-control-page .sac-modern-actions-grid.is-three,
        .seller-account-control-page .sac-modern-actions-grid.is-two,
        .seller-account-control-page .sac-modern-actions-grid.is-one {
            grid-template-columns: 1fr !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-account-control-page .sac-violation-chevron,
        .seller-account-control-page .sac-modern-action {
            transition: none !important;
        }

        .seller-account-control-page .sac-modern-action:hover {
            transform: none !important;
        }
    }


    /* =========================================================
       SARI FLOATING DEPTH SYSTEM — SELLER ACCOUNT CONTROL
       Same warm layered elevation used across the admin previews.
       Visual override only; no routes, forms, state logic, or JS changed.
       ========================================================= */

    /* Top-level page containers */
    .seller-account-control-page .account-control-summary-card {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .040),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-account-control-page .account-control-summary-card:hover {
        transform: translateY(-3px) !important;
        border-color: #d9c9b1 !important;
        box-shadow:
            0 4px 9px rgba(61, 43, 22, .050),
            0 21px 46px rgba(61, 43, 22, .115),
            0 40px 76px rgba(61, 43, 22, .048),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-account-control-page .account-control-workspace {
        border-color: #e6ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    /* Seller records — lighter than the main workspace so hierarchy stays clean */
    .seller-account-control-page .account-control-row {
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .050),
            0 20px 38px rgba(61, 43, 22, .020) !important;
    }

    .seller-account-control-page .account-control-row:hover {
        transform: translateY(-2px);
        border-color: #dacbbb !important;
        background: #fffdfa !important;
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .035),
            0 15px 32px rgba(61, 43, 22, .075),
            0 27px 48px rgba(61, 43, 22, .026) !important;
    }

    /* Search + native filter */
    .seller-account-control-page .account-control-input {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .020),
            0 7px 16px rgba(61, 43, 22, .040),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-account-control-page .account-control-input:hover {
        border-color: #d8c9b7 !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .026),
            0 9px 20px rgba(61, 43, 22, .050),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-account-control-page .account-control-input:focus {
        border-color: #c99128 !important;
        box-shadow:
            0 0 0 4px rgba(201,145,40,.08),
            0 10px 24px rgba(61,43,22,.065),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    /* View/manage icon */
    .seller-account-control-page [data-seller-control-open] {
        box-shadow:
            0 2px 4px rgba(52, 41, 27, .030),
            0 8px 18px rgba(52, 41, 27, .055),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-account-control-page [data-seller-control-open]:hover {
        transform: translateY(-2px);
        box-shadow:
            0 3px 6px rgba(52, 41, 27, .040),
            0 12px 26px rgba(88, 64, 31, .100) !important;
    }

    /* Main seller-management modal */
    .seller-account-control-page .sac-modern-modal {
        box-shadow:
            0 10px 24px rgba(31, 24, 17, .090),
            0 34px 82px rgba(31, 24, 17, .220),
            0 70px 135px rgba(31, 24, 17, .130) !important;
    }

    .seller-account-control-page .sac-modern-modal-header {
        box-shadow:
            0 1px 0 rgba(61,43,22,.025),
            0 9px 24px rgba(61,43,22,.035) !important;
    }

    .seller-account-control-page .sac-modern-modal-close {
        box-shadow:
            0 2px 4px rgba(61,43,22,.028),
            0 9px 20px rgba(61,43,22,.055) !important;
    }

    .seller-account-control-page .sac-modern-modal-close:hover {
        transform: translateY(-1px);
        box-shadow:
            0 3px 6px rgba(61,43,22,.035),
            0 12px 24px rgba(61,43,22,.075) !important;
    }

    /* Violation disclosure as a floating surface */
    .seller-account-control-page .sac-violations {
        border-color: #e6ddd1 !important;
        box-shadow:
            0 3px 7px rgba(61,43,22,.032),
            0 14px 32px rgba(61,43,22,.072),
            0 26px 48px rgba(61,43,22,.026) !important;
    }

    .seller-account-control-page .sac-violations[open] {
        box-shadow:
            0 4px 9px rgba(61,43,22,.038),
            0 18px 38px rgba(61,43,22,.090),
            0 32px 58px rgba(61,43,22,.030) !important;
    }

    .seller-account-control-page .sac-violation-icon,
    .seller-account-control-page .sac-violation-count {
        box-shadow:
            0 2px 4px rgba(61,43,22,.020),
            0 7px 15px rgba(61,43,22,.035) !important;
    }

    /* Moderation action buttons */
    .seller-account-control-page .sac-modern-action {
        box-shadow:
            0 2px 5px rgba(61,43,22,.025),
            0 9px 20px rgba(61,43,22,.055),
            inset 0 1px 0 rgba(255,255,255,.94) !important;
    }

    .seller-account-control-page .sac-modern-action:hover {
        transform: translateY(-2px);
        box-shadow:
            0 3px 7px rgba(61,43,22,.035),
            0 14px 28px rgba(61,43,22,.080),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-account-control-page .sac-modern-action.is-suspend {
        box-shadow:
            0 2px 5px rgba(163,109,23,.035),
            0 10px 22px rgba(217,149,0,.100),
            inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    .seller-account-control-page .sac-modern-action.is-ban {
        box-shadow:
            0 2px 5px rgba(154,79,79,.030),
            0 10px 22px rgba(166,93,93,.090),
            inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    .seller-account-control-page .sac-modern-action.is-deactivate {
        box-shadow:
            0 2px 5px rgba(61,43,22,.022),
            0 10px 22px rgba(61,43,22,.050),
            inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    .seller-account-control-page .sac-modern-action.is-positive {
        box-shadow:
            0 2px 5px rgba(61,107,76,.025),
            0 10px 22px rgba(95,131,107,.080),
            inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    /* Preservation/status messages */
    .seller-account-control-page .sac-modern-note,
    .seller-account-control-page .sac-modern-status-reason {
        box-shadow:
            0 2px 5px rgba(61,43,22,.022),
            0 9px 22px rgba(61,43,22,.045) !important;
    }

    /* Header CTA retains the same stronger depth system */
    .seller-account-control-page .sac-header-back {
        box-shadow:
            0 3px 7px rgba(183,124,0,.100),
            0 13px 28px rgba(217,149,0,.230) !important;
    }

    .seller-account-control-page .sac-header-back:hover {
        box-shadow:
            0 4px 8px rgba(183,124,0,.120),
            0 17px 36px rgba(217,149,0,.270) !important;
    }

    /* Confirmation modal also floats at a higher elevation */
    #sellerAccountActionModal > div {
        border-color: #e6d8ce !important;
        box-shadow:
            0 10px 24px rgba(31,24,17,.100),
            0 34px 82px rgba(31,24,17,.220),
            0 68px 125px rgba(31,24,17,.120) !important;
    }

    #sellerAccountActionClose,
    #sellerAccountActionCancel {
        box-shadow:
            0 2px 4px rgba(61,43,22,.024),
            0 8px 18px rgba(61,43,22,.050) !important;
    }

    #sellerAccountActionSubmit {
        box-shadow:
            0 3px 7px rgba(146,67,67,.090),
            0 13px 28px rgba(169,86,86,.200) !important;
    }

    @media (max-width: 767px) {
        .seller-account-control-page .account-control-summary-card,
        .seller-account-control-page .account-control-workspace {
            box-shadow:
                0 3px 7px rgba(61,43,22,.035),
                0 14px 32px rgba(61,43,22,.075),
                0 24px 46px rgba(61,43,22,.025) !important;
        }

        .seller-account-control-page .account-control-row {
            box-shadow:
                0 2px 5px rgba(61,43,22,.025),
                0 9px 20px rgba(61,43,22,.050) !important;
        }

        .seller-account-control-page .account-control-summary-card:hover,
        .seller-account-control-page .account-control-row:hover {
            transform: translateY(-1px) !important;
        }

        .seller-account-control-page .sac-violations {
            box-shadow:
                0 2px 5px rgba(61,43,22,.028),
                0 10px 24px rgba(61,43,22,.060) !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-account-control-page .account-control-summary-card:hover,
        .seller-account-control-page .account-control-row:hover,
        .seller-account-control-page [data-seller-control-open]:hover,
        .seller-account-control-page .sac-modern-modal-close:hover,
        .seller-account-control-page .sac-modern-action:hover {
            transform: none !important;
        }
    }


    /* =========================================================
       SELLER ACCOUNT CONTROL SUMMARY — USER MANAGEMENT SIZE PARITY
       Matches User Management metric-card size and typography.
       Summary visuals only; all stats, routes, modals, and JS stay intact.
       ========================================================= */

    .seller-account-control-page .account-control-summary-card {
        min-height: 110px !important;
        padding: 18px !important;
        border-radius: 18px !important;
    }

    .seller-account-control-page .account-control-summary-card > div {
        min-height: 72px;
        align-items: center !important;
        gap: 16px !important;
    }

    .seller-account-control-page .account-control-summary-card > div > div:first-child {
        min-width: 0;
    }

    .seller-account-control-page .account-control-summary-card .sac-summary-label {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    .seller-account-control-page .account-control-summary-card .sac-summary-value {
        margin-top: 4px !important;
        font-size: 26px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
    }

    .seller-account-control-page .account-control-summary-card > div > div:last-child {
        width: 48px !important;
        height: 48px !important;
        flex: 0 0 48px !important;
        border-radius: 12px !important;
    }

    .seller-account-control-page .account-control-summary-card > div > div:last-child svg {
        width: 20px !important;
        height: 20px !important;
    }

    @media (max-width: 639px) {
        .seller-account-control-page .account-control-summary-card {
            min-height: 110px !important;
            padding: 16px !important;
        }

        .seller-account-control-page .account-control-summary-card > div {
            min-height: 76px;
            gap: 14px !important;
        }

        .seller-account-control-page .account-control-summary-card .sac-summary-label {
            font-size: 11px !important;
        }

        .seller-account-control-page .account-control-summary-card .sac-summary-value {
            font-size: 25px !important;
        }

        .seller-account-control-page .account-control-summary-card > div > div:last-child {
            width: 44px !important;
            height: 44px !important;
            flex-basis: 44px !important;
        }

        .seller-account-control-page .account-control-summary-card > div > div:last-child svg {
            width: 19px !important;
            height: 19px !important;
        }
    }


    /* =========================================================
       SELLER ACCOUNT CONTROL — USER MANAGEMENT STYLE FILTER BAR
       Search + Status + Apply Filter + Reset in one floating toolbar.
       Existing client-side filtering logic is retained.
       ========================================================= */

    .seller-account-control-page .sac-filter-surface {
        position: relative;
        z-index: 30;
        margin-top: 16px;
        padding: 12px;
        overflow: visible;
        border: 1px solid #e7ddd1;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 3px 8px rgba(61,43,22,.045),
            0 18px 42px rgba(61,43,22,.095),
            0 38px 78px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .seller-account-control-page .sac-filter-grid {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 220px 124px 82px;
        gap: 12px;
        align-items: center;
    }

    .seller-account-control-page .sac-filter-search {
        position: relative;
        min-width: 0;
    }

    .seller-account-control-page .sac-filter-search > svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 16px;
        width: 16px;
        height: 16px;
        pointer-events: none;
        color: #9d8f7e;
        transform: translateY(-50%);
    }

    .seller-account-control-page .sac-filter-input,
    .seller-account-control-page .sac-filter-select {
        width: 100% !important;
        height: 44px !important;
        min-height: 44px !important;
        border: 1px solid #e8e0d5 !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #332c25 !important;
        -webkit-text-fill-color: #332c25 !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        line-height: 1 !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            background-color .16s ease;
    }

    .seller-account-control-page .sac-filter-input {
        padding: 0 16px 0 44px !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-filter-input::placeholder {
        color: #a69c91 !important;
        -webkit-text-fill-color: #a69c91 !important;
    }

    .seller-account-control-page .sac-filter-input:hover,
    .seller-account-control-page .sac-filter-select:hover {
        border-color: #d8c8b1 !important;
    }

    .seller-account-control-page .sac-filter-input:focus,
    .seller-account-control-page .sac-filter-select:focus {
        outline: none !important;
        border-color: #d9a33a !important;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 10px 24px rgba(61,43,22,.07),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-account-control-page .sac-filter-select-wrap {
        position: relative;
        min-width: 0;
    }

    .seller-account-control-page .sac-filter-select {
        appearance: none;
        padding: 0 42px 0 36px !important;
        cursor: pointer;
    }

    .seller-account-control-page .sac-filter-status-dot {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 15px;
        width: 8px;
        height: 8px;
        pointer-events: none;
        border-radius: 999px;
        background: #3f9a61;
        transform: translateY(-50%);
    }

    .seller-account-control-page .sac-filter-select-chevron {
        position: absolute;
        z-index: 2;
        top: 50%;
        right: 14px;
        width: 14px;
        height: 14px;
        pointer-events: none;
        color: #8b8175;
        transform: translateY(-50%);
    }

    .seller-account-control-page .sac-filter-apply {
        display: inline-flex;
        width: 100%;
        height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 12px;
        padding: 0 16px;
        background: #d99500;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow:
            0 3px 7px rgba(183,124,0,.10),
            0 13px 28px rgba(217,149,0,.23);
        transition:
            transform .16s ease,
            background-color .16s ease,
            box-shadow .16s ease;
    }

    .seller-account-control-page .sac-filter-apply:hover {
        background: #bd8205;
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(183,124,0,.12),
            0 16px 34px rgba(217,149,0,.26);
    }

    .seller-account-control-page .sac-filter-reset {
        display: inline-flex;
        width: 100%;
        height: 44px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e6ddd2;
        border-radius: 12px;
        padding: 0 14px;
        background: #fff;
        color: #6f665b;
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045);
        transition:
            border-color .16s ease,
            color .16s ease,
            background-color .16s ease;
    }

    .seller-account-control-page .sac-filter-reset:hover {
        border-color: #d8c8b1;
        background: #faf8f4;
        color: #51483f;
    }

    .seller-account-control-page .sac-filter-apply svg {
        width: 14px;
        height: 14px;
    }

    /* Table/workspace starts as a separate card under the filter bar. */
    .seller-account-control-page .account-control-workspace.sac-workspace-separated {
        margin-top: 16px !important;
    }

    .seller-account-control-page .sac-workspace-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 15px 20px;
        border-bottom: 1px solid #eee8df;
        background: #fff;
    }

    .seller-account-control-page .sac-workspace-topbar .sac-workspace-title {
        font-size: .92rem !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-workspace-topbar .sac-workspace-copy,
    .seller-account-control-page .sac-workspace-topbar .sac-result-copy {
        font-size: .68rem !important;
        line-height: 1.45 !important;
    }

    @media (max-width: 1023px) {
        .seller-account-control-page .sac-filter-grid {
            grid-template-columns: minmax(0, 1fr) 190px;
        }

        .seller-account-control-page .sac-filter-search {
            grid-column: 1 / -1;
        }

        .seller-account-control-page .sac-filter-apply,
        .seller-account-control-page .sac-filter-reset {
            width: 100%;
        }
    }

    @media (max-width: 639px) {
        .seller-account-control-page .sac-filter-surface {
            padding: 10px;
            border-radius: 16px;
        }

        .seller-account-control-page .sac-filter-grid {
            grid-template-columns: 1fr;
            gap: 9px;
        }

        .seller-account-control-page .sac-filter-search {
            grid-column: auto;
        }

        .seller-account-control-page .sac-workspace-topbar {
            align-items: flex-start;
            flex-direction: column;
            padding: 14px 16px;
        }
    }


    /* =========================================================
       SELLER ACCOUNT CONTROL — CLEAN TABLE + EDIT-TASK INSPIRED VIEW MODAL
       Visual refinement only. Existing backend routes, seller status logic,
       violation data, action forms, and modal JS hooks remain intact.
       ========================================================= */

    /* ---------------------------------------------------------
       Seller list: clean sheet / row density
       --------------------------------------------------------- */
    .seller-account-control-page .account-control-workspace {
        overflow: hidden !important;
        border-color: #e7ddd1 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow:
            0 3px 8px rgba(61,43,22,.045),
            0 18px 42px rgba(61,43,22,.095),
            0 38px 78px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-account-control-page #sellerAccountList {
        padding: 0 !important;
        background: #fff;
    }

    .seller-account-control-page .account-control-row {
        margin: 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #f0ebe4 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        transform: none !important;
        transition: background-color .14s ease !important;
    }

    .seller-account-control-page .account-control-row:last-of-type {
        border-bottom: 0 !important;
    }

    .seller-account-control-page .account-control-row:hover {
        border-color: #f0ebe4 !important;
        background: #fdfbf7 !important;
        box-shadow: none !important;
        transform: none !important;
    }

    @media (min-width: 1280px) {
        .seller-account-control-page .account-control-row-grid {
            grid-template-columns:
                minmax(290px, 1.65fr)
                128px
                118px
                135px
                165px
                82px !important;
            column-gap: 16px !important;
        }

        .seller-account-control-page .account-control-row-grid > div {
            min-height: 72px;
        }
    }

    .seller-account-control-page .account-control-workspace .sac-column-label {
        font-size: 10px !important;
        line-height: 1.35 !important;
        font-weight: 700 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase;
        color: #847b70 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-seller-name {
        font-size: 12px !important;
        line-height: 1.35 !important;
        font-weight: 700 !important;
        color: #2e2924 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-muted {
        font-size: 9px !important;
        line-height: 1.4 !important;
        color: #958c80 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-badge {
        font-size: 9.5px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-data-value {
        font-size: 10px !important;
        line-height: 1.4 !important;
        font-weight: 500 !important;
        color: #514a42 !important;
    }

    /* Seller avatar in rows — same visual rhythm as Users table. */
    .seller-account-control-page .account-control-row .sac-modern-avatar {
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #f3f1ed !important;
        color: #655d55 !important;
        font-size: 10px !important;
        box-shadow: none !important;
    }

    /* ---------------------------------------------------------
       View icon: icon-only, no container/shadow
       --------------------------------------------------------- */
    .seller-account-control-page [data-seller-control-open] {
        display: inline-grid !important;
        width: 30px !important;
        min-width: 30px !important;
        height: 30px !important;
        min-height: 30px !important;
        place-items: center !important;
        padding: 0 !important;

        border: 0 !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;

        color: #3f3b37 !important;
        outline: none !important;

        transition:
            color .15s ease,
            transform .15s ease !important;
    }

    .seller-account-control-page [data-seller-control-open] svg {
        width: 15px !important;
        height: 15px !important;
        color: currentColor !important;
        stroke: currentColor !important;
        filter: none !important;
    }

    .seller-account-control-page [data-seller-control-open]:hover,
    .seller-account-control-page [data-seller-control-open]:focus-visible {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #e09a00 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    /* ---------------------------------------------------------
       Main seller modal — clean form-like layout inspired by reference
       --------------------------------------------------------- */
    .seller-account-control-page [data-seller-control-modal] {
        background: rgba(31,29,26,.46) !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        padding: 18px !important;
    }

    .seller-account-control-page .sac-modern-modal {
        width: min(760px, calc(100vw - 28px)) !important;
        max-width: 760px !important;
        max-height: min(92vh, 860px) !important;
        overflow: hidden !important;
        border: 1px solid #dedbd6 !important;
        border-radius: 22px !important;
        background: #fff !important;
        box-shadow:
            0 18px 44px rgba(24,22,19,.13),
            0 44px 100px rgba(24,22,19,.20) !important;
    }

    .seller-account-control-page .sac-modern-modal::before {
        display: none !important;
        content: none !important;
    }

    .seller-account-control-page .sac-modern-modal-header {
        align-items: center !important;
        padding: 22px 22px 18px !important;
        border-bottom: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    /* Hide the chunky avatar in the modal header for a cleaner form-like top. */
    .seller-account-control-page .sac-modern-modal-header > div:first-child > div:first-child {
        display: none !important;
    }

    .seller-account-control-page .sac-modern-modal-header > div:first-child {
        gap: 0 !important;
    }

    .seller-account-control-page .sac-modern-modal-title {
        font-size: 1.32rem !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        letter-spacing: -.035em !important;
        color: #252525 !important;
    }

    .seller-account-control-page .sac-modern-modal-header .sac-muted {
        margin-top: 7px !important;
        font-size: .75rem !important;
        color: #7d7d82 !important;
    }

    .seller-account-control-page .sac-modern-modal-header .sac-badge {
        padding: 5px 9px !important;
        font-size: .63rem !important;
    }

    .seller-account-control-page .sac-modern-modal-close {
        width: 36px !important;
        height: 36px !important;
        border: 0 !important;
        border-radius: 10px !important;
        background: #f7f7f8 !important;
        color: #636363 !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-modal-close:hover {
        background: #eeeeef !important;
        color: #2d2d2d !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .seller-account-control-page .sac-modern-modal-body {
        padding: 0 22px 22px !important;
        background: #fff !important;
    }

    /* Snapshot becomes clean read-only form fields. */
    .seller-account-control-page .sac-modern-kpis {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 14px !important;
        padding: 0 !important;
        border: 0 !important;
    }

    .seller-account-control-page .sac-modern-kpi {
        min-height: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-kpi + .sac-modern-kpi::before,
    .seller-account-control-page .sac-modern-kpi::after {
        display: none !important;
        content: none !important;
    }

    .seller-account-control-page .sac-modern-kpi:last-child {
        grid-column: 1 / -1;
    }

    .seller-account-control-page .sac-modern-kpi-label {
        display: block;
        margin-bottom: 7px !important;
        color: #626268 !important;
        font-size: .72rem !important;
        font-weight: 500 !important;
        line-height: 1.3 !important;
    }

    .seller-account-control-page .sac-modern-kpi-value {
        display: flex;
        min-height: 48px;
        align-items: center;
        margin: 0 !important;
        padding: 0 14px !important;
        border: 1px solid #dcdde1;
        border-radius: 10px;
        background: #fff;
        color: #303034 !important;
        font-size: .88rem !important;
        font-weight: 500 !important;
        line-height: 1.2 !important;
        letter-spacing: 0 !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-kpi-value.is-warning,
    .seller-account-control-page .sac-modern-kpi-value.is-danger {
        color: #303034 !important;
    }

    /* Violations become a clean details field/section. */
    .seller-account-control-page .sac-violations {
        margin-top: 16px !important;
        border: 1px solid #dcdde1 !important;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-violations[open] {
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-violations > summary {
        min-height: 52px !important;
        padding: 12px 14px !important;
        background: #fff !important;
    }

    .seller-account-control-page .sac-violations > summary:hover,
    .seller-account-control-page .sac-violations[open] > summary {
        background: #fafafa !important;
    }

    .seller-account-control-page .sac-violation-icon {
        width: 30px !important;
        height: 30px !important;
        flex: 0 0 30px !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: #fff8e9 !important;
        color: #ad741b !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-violation-summary-title {
        color: #39393d !important;
        font-size: .78rem !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-violation-summary-copy {
        color: #87878c !important;
        font-size: .66rem !important;
    }

    .seller-account-control-page .sac-violation-count {
        border-color: #e2e2e5 !important;
        background: #f7f7f8 !important;
        color: #69696f !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-violation-list {
        border-top-color: #e8e8ea !important;
    }

    .seller-account-control-page .sac-violation-row {
        padding: 13px 14px !important;
    }

    /* Status reason styled like a Details textarea/read-only field. */
    .seller-account-control-page .sac-modern-status-reason {
        margin-top: 16px !important;
        padding: 14px !important;
        border: 1px solid #dcdde1 !important;
        border-radius: 10px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    /* Actions: quiet lower section similar to modal footer form controls. */
    .seller-account-control-page .sac-modern-actions {
        margin-top: 18px !important;
        padding-top: 16px !important;
        border-top: 1px solid #ececef !important;
        background: transparent !important;
    }

    .seller-account-control-page .sac-modern-actions-head {
        margin-bottom: 10px !important;
    }

    .seller-account-control-page .sac-modern-actions-title {
        color: #303034 !important;
        font-size: .78rem !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-modern-actions-copy {
        color: #85858b !important;
        font-size: .66rem !important;
    }

    .seller-account-control-page .sac-modern-actions-grid {
        gap: 10px !important;
    }

    .seller-account-control-page .sac-modern-action {
        height: 44px !important;
        min-height: 44px !important;
        border-radius: 10px !important;
        font-size: .73rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-action:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-action.is-suspend {
        border-color: #e7cfa1 !important;
        background: #fffaf0 !important;
        color: #95671e !important;
    }

    .seller-account-control-page .sac-modern-action.is-suspend:hover {
        border-color: #d8b875 !important;
        background: #fff4df !important;
    }

    .seller-account-control-page .sac-modern-action.is-ban {
        border-color: #efcaca !important;
        background: #fff7f7 !important;
        color: #a45151 !important;
    }

    .seller-account-control-page .sac-modern-action.is-ban:hover {
        border-color: #e3aaaa !important;
        background: #fff0f0 !important;
    }

    .seller-account-control-page .sac-modern-action.is-deactivate {
        border-color: #dcdde1 !important;
        background: #f8f8f8 !important;
        color: #55555a !important;
    }

    .seller-account-control-page .sac-modern-action.is-deactivate:hover {
        border-color: #cfcfd3 !important;
        background: #f1f1f2 !important;
    }

    .seller-account-control-page .sac-modern-action.is-positive {
        border-color: #cfe2d5 !important;
        background: #f5faf6 !important;
        color: #4d7b5c !important;
    }

    /* Quiet helper copy at the bottom, no card/shadow. */
    .seller-account-control-page .sac-modern-note {
        margin-top: 14px !important;
        padding: 10px 0 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        border-top: 1px solid #ececef !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-account-control-page .sac-modern-note svg {
        color: #8d8d92 !important;
    }

    .seller-account-control-page .sac-modern-note p {
        color: #7d7d82 !important;
        font-size: .66rem !important;
        line-height: 1.5 !important;
    }

    @media (max-width: 1279px) {
        .seller-account-control-page #sellerAccountList {
            padding: 12px !important;
        }

        .seller-account-control-page .account-control-row {
            margin-bottom: 12px !important;
            border: 1px solid #e9e1d7 !important;
            border-radius: 16px !important;
        }

        .seller-account-control-page .account-control-row:last-of-type {
            margin-bottom: 0 !important;
        }
    }

    @media (max-width: 639px) {
        .seller-account-control-page .sac-modern-modal {
            width: calc(100vw - 16px) !important;
            border-radius: 18px !important;
        }

        .seller-account-control-page .sac-modern-modal-header {
            padding: 18px 16px 14px !important;
        }

        .seller-account-control-page .sac-modern-modal-body {
            padding: 0 16px 18px !important;
        }

        .seller-account-control-page .sac-modern-kpis {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        .seller-account-control-page .sac-modern-kpi:last-child {
            grid-column: auto;
        }

        .seller-account-control-page .sac-modern-actions-grid.is-three,
        .seller-account-control-page .sac-modern-actions-grid.is-two,
        .seller-account-control-page .sac-modern-actions-grid.is-one {
            grid-template-columns: 1fr !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-account-control-page [data-seller-control-open],
        .seller-account-control-page [data-seller-control-open]:hover,
        .seller-account-control-page [data-seller-control-open]:focus-visible {
            transform: none !important;
            transition: none !important;
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
            <article class="account-control-summary-card rounded-[17px] border border-[#ebe4da] bg-white p-3.5 sm:p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="sac-summary-label text-[var(--sac-sm)]">{{ $card['label'] }}</p>
                        <p class="sac-summary-value mt-2 text-[clamp(1.40rem,1.27rem+.30vw,1.72rem)]">{{ $card['value'] }}</p>
                    </div>

                    <div class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[11px] border {{ $card['tone'] }}">
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
                            || ($until && now()->lt($until))
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
                     * Violation/warning detail source.
                     * The Seller Compliance page already uses $recentWarnings.
                     * If this view also receives that collection, filter it here.
                     * Otherwise try common seller warning relations without
                     * requiring a controller change.
                     */
                    $sellerViolationHistory = collect();

                    if (isset($recentWarnings)) {
                        $sellerViolationHistory = collect($recentWarnings)
                            ->filter(function ($warning) use ($seller) {
                                $warningSellerId = $warning->seller?->id
                                    ?? $warning->seller_account_id
                                    ?? $warning->seller_id
                                    ?? null;

                                return (string) $warningSellerId === (string) $seller->id;
                            })
                            ->values();
                    }

                    if ($sellerViolationHistory->isEmpty()) {
                        foreach (['warnings', 'complianceWarnings', 'sellerWarnings'] as $warningRelation) {
                            if (method_exists($seller, $warningRelation)) {
                                $sellerViolationHistory = collect($seller->{$warningRelation})
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
                <div
                    id="{{ $modalId }}"
                    data-seller-control-modal
                    hidden
                    class="fixed inset-0 z-[190] items-center justify-center bg-black/40 p-4 backdrop-blur-[3px]"
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
    class="fixed inset-0 z-[220] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-[3px]"
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

    function applySellerFilters() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const status = (statusFilter?.value || '').trim().toLowerCase();

        let visible = 0;

        sellerRows.forEach(function (row) {
            const searchable = (row.dataset.sellerAccountSearch || '').toLowerCase();
            const rowStatus = (row.dataset.sellerAccountStatus || '').toLowerCase();

            const matchesQuery = query === '' || searchable.includes(query);
            const matchesStatus = status === '' || rowStatus === status;
            const matches = matchesQuery && matchesStatus;

            row.classList.toggle('hidden', !matches);

            if (matches) {
                visible++;
            }
        });

        if (resultCount) {
            resultCount.textContent =
                'Showing ' + visible +
                ' of ' + sellerRows.length +
                ' seller' + (visible === 1 ? '' : 's');
        }

        if (sellerRows.length > 0) {
            sellerList?.classList.toggle('hidden', visible === 0);
            filterEmpty?.classList.toggle('hidden', visible !== 0);
        }
    }

    function clearSellerFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        applySellerFilters();
        searchInput?.focus();
    }

    searchInput?.addEventListener('input', applySellerFilters);
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
    const sellerControlModals = Array.from(
        document.querySelectorAll('[data-seller-control-modal]')
    );

    function closeSellerControlModals() {
        sellerControlModals.forEach(function (modal) {
            modal.hidden = true;
            modal.classList.remove('flex');
        });

        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-seller-control-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const modal = document.getElementById(this.dataset.sellerControlOpen);

            if (!modal) return;

            closeSellerControlModals();

            modal.hidden = false;
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('[data-seller-control-close]').forEach(function (button) {
        button.addEventListener('click', closeSellerControlModals);
    });

    sellerControlModals.forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeSellerControlModals();
            }
        });
    });


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

    document.querySelectorAll('[data-account-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const action = this.dataset.accountAction;
            const seller = this.dataset.seller;

            actionForm.action = this.dataset.url;

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
        });
    });

    document.getElementById('sellerAccountActionCancel')?.addEventListener('click', closeActionModal);
    document.getElementById('sellerAccountActionClose')?.addEventListener('click', closeActionModal);

    actionModal?.addEventListener('click', function (event) {
        if (event.target === actionModal) {
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
