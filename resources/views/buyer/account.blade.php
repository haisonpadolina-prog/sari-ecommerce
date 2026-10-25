@extends('layouts.buyer')

@section('title', 'Buyer Account — SARI')
@section('page-title', 'Account')

@section('content')
@include('components.buyer.header')

<main
    class="sari-account-page mx-auto w-full max-w-[1380px] px-4 pb-10 pt-5 sm:px-6 lg:px-8"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        .sari-account-page {
            --sa-gold: #c88912;
            --sa-gold-dark: #aa730f;
            --sa-ink: #202938;
            --sa-body: #596579;
            --sa-muted: #8490a2;
            --sa-line: #dfe5ec;
            --sa-line-soft: #edf1f5;
            --sa-panel: #ffffff;
            --sa-soft: #f7f9fb;
            --sa-danger: #c75a5a;
            color: var(--sa-ink);
        }

        .sari-account-page * {
            box-sizing: border-box;
        }

        /* Page heading — compact like Buyer Cart / Catalog. */
        .sa-page-head {
            padding: 2px 2px 16px;
        }

        .sa-eyebrow {
            margin: 0;
            color: #a87316;
            font-size: 10.5px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sa-page-title {
            margin: 5px 0 0;
            color: #2e343b;
            font-size: clamp(29px, 2.35vw, 34px);
            font-weight: 760;
            line-height: 1.04;
            letter-spacing: -.035em;
        }

        .sa-page-copy {
            max-width: 760px;
            margin: 7px 0 0;
            color: #7d8792;
            font-size: 11.5px;
            font-weight: 450;
            line-height: 1.55;
        }

        /* Feedback */
        .sa-alert {
            margin-bottom: 14px;
            border: 1px solid;
            border-radius: 11px;
            padding: 10px 13px;
            font-size: 10.5px;
            font-weight: 550;
            line-height: 1.5;
        }

        .sa-alert--success {
            border-color: #d5e5db;
            background: #f7fbf8;
            color: #4e765e;
        }

        .sa-alert--error {
            border-color: #edd4d4;
            background: #fff8f8;
            color: #a74e4e;
        }

        /* Main two-column shell: profile LEFT, form RIGHT. */
        .sa-account-layout {
            display: grid;
            grid-template-columns: 286px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
        }

        .sa-card {
            border: 1px solid var(--sa-line);
            border-radius: 17px;
            background: var(--sa-panel);
            box-shadow:
                0 10px 28px rgba(31, 41, 55, .05),
                0 2px 7px rgba(31, 41, 55, .018);
        }

        /* ============================================================
           LEFT PROFILE / PREVIEW
        ============================================================ */
        .sa-profile-card {
            position: sticky;
            top: 84px;
            overflow: hidden;
        }

        .sa-profile-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 15px;
            border-bottom: 1px solid var(--sa-line-soft);
        }

        .sa-profile-card-title {
            margin: 0;
            color: #2b3441;
            font-size: 12.5px;
            font-weight: 760;
            line-height: 1.3;
        }

        .sa-profile-badge {
            color: #99a2ad;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sa-profile-body {
            padding: 16px;
        }

        .sa-avatar-wrap {
            position: relative;
            width: 118px;
            height: 118px;
            margin: 0 auto;
        }

        .sa-avatar {
            display: grid;
            width: 118px;
            height: 118px;
            place-items: center;
            overflow: hidden;
            border: 1px solid #dbe1e8;
            border-radius: 20px;
            background: #f4f6f8;
            color: #919ba8;
            font-size: 30px;
            font-weight: 720;
            letter-spacing: -.05em;
        }

        .sa-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sa-avatar-edit {
            position: absolute;
            right: -7px;
            bottom: -7px;
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 3px solid #fff;
            border-radius: 11px;
            background: var(--sa-gold);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 5px 13px rgba(200, 137, 18, .2);
            transition: background-color .14s ease;
        }

        .sa-avatar-edit:hover,
        .sa-avatar-edit:focus-visible {
            background: var(--sa-gold-dark);
            outline: none;
        }

        .sa-avatar-edit svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-profile-name {
            margin: 17px 0 0;
            color: #242d38;
            font-size: 15px;
            font-weight: 760;
            line-height: 1.35;
            text-align: center;
            letter-spacing: -.018em;
        }

        .sa-profile-email {
            margin: 4px 0 0;
            overflow: hidden;
            color: #8993a0;
            font-size: 9.5px;
            font-weight: 500;
            line-height: 1.4;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-profile-role {
            display: table;
            margin: 10px auto 0;
            border: 1px solid #e6eaf0;
            border-radius: 999px;
            background: #f8f9fb;
            padding: 4px 9px;
            color: #697586;
            font-size: 8px;
            font-weight: 750;
        }

        .sa-upload-block {
            margin-top: 16px;
            border-top: 1px solid var(--sa-line-soft);
            padding-top: 14px;
        }

        .sa-upload-label {
            display: block;
            color: #4f5b6c;
            font-size: 10px;
            font-weight: 720;
            line-height: 1.35;
        }

        .sa-upload-copy {
            margin: 4px 0 0;
            color: #919ba8;
            font-size: 8.5px;
            line-height: 1.5;
        }

        .sa-upload-actions {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 7px;
            margin-top: 10px;
        }

        .sa-upload-button,
        .sa-upload-reset {
            display: inline-flex;
            min-height: 37px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 9px;
            font-size: 9px;
            font-weight: 720;
            cursor: pointer;
            transition:
                border-color .14s ease,
                background-color .14s ease,
                color .14s ease;
        }

        .sa-upload-button {
            border: 1px solid #d9dfe6;
            background: #fff;
            color: #505c6b;
        }

        .sa-upload-button:hover,
        .sa-upload-button:focus-visible {
            border-color: #d4b97e;
            color: #98650f;
            outline: none;
        }

        .sa-upload-reset {
            border: 1px solid #e4e8ed;
            background: #f8f9fa;
            padding-inline: 10px;
            color: #87919d;
        }

        .sa-upload-reset:hover,
        .sa-upload-reset:focus-visible {
            border-color: #d8dde3;
            background: #f3f5f7;
            color: #626d79;
            outline: none;
        }

        .sa-upload-button svg,
        .sa-upload-reset svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-file-status {
            margin: 8px 0 0;
            overflow: hidden;
            color: #949daa;
            font-size: 8.3px;
            line-height: 1.45;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-file-status.is-error {
            color: var(--sa-danger);
            white-space: normal;
        }

        .sa-profile-meta {
            display: grid;
            gap: 9px;
            margin-top: 15px;
            border-top: 1px solid var(--sa-line-soft);
            padding-top: 13px;
        }

        .sa-profile-meta-row {
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .sa-profile-meta-label {
            color: #99a2ae;
            font-size: 8.5px;
            font-weight: 600;
        }

        .sa-profile-meta-value {
            max-width: 150px;
            overflow: hidden;
            color: #4e5968;
            font-size: 9px;
            font-weight: 650;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ============================================================
           RIGHT EDIT FORM — compact screenshot-inspired cards
        ============================================================ */
        .sa-form-stack {
            display: grid;
            gap: 14px;
        }

        .sa-form-card {
            overflow: hidden;
        }

        .sa-form-card-head {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 15px 18px 12px;
        }

        .sa-form-card-heading {
            min-width: 0;
            flex: 0 1 auto;
        }

        .sa-form-card-title {
            margin: 0;
            color: #273140;
            font-size: 15px;
            font-weight: 760;
            line-height: 1.25;
            letter-spacing: -.018em;
        }

        .sa-form-card-copy {
            margin: 3px 0 0;
            color: #7f8a99;
            font-size: 9.5px;
            line-height: 1.5;
        }

        .sa-form-card-line {
            height: 1px;
            min-width: 80px;
            flex: 1;
            margin-top: 16px;
            background: #e2e7ed;
        }

        .sa-form-card-body {
            padding: 0 18px 17px;
        }

        .sa-field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 13px;
        }

        .sa-field-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sa-field--full {
            grid-column: 1 / -1;
        }

        .sa-label {
            display: block;
            margin-bottom: 5px;
            color: #40506a;
            font-size: 10.5px;
            font-weight: 700;
            line-height: 1.35;
        }

        .sa-required {
            color: #d65a5a;
        }

        .sa-control {
            width: 100%;
            min-height: 45px;
            border: 1px solid #d7dee7;
            border-radius: 11px;
            background: #fff;
            padding: 0 12px;
            color: #2e3948;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.4;
            outline: none;
            box-shadow: none;
            transition:
                border-color .14s ease,
                box-shadow .14s ease,
                background-color .14s ease;
        }

        select.sa-control {
            cursor: pointer;
        }

        textarea.sa-control {
            min-height: 78px;
            padding-block: 10px;
            resize: vertical;
            line-height: 1.55;
        }

        .sa-control:hover:not(:disabled):not([readonly]) {
            border-color: #c9d1db;
        }

        .sa-control:focus:not(:disabled):not([readonly]) {
            border-color: var(--sa-gold);
            box-shadow: 0 0 0 3px rgba(200, 137, 18, .08);
        }

        .sa-control[readonly],
        .sa-control:disabled {
            border-color: #e3e7ec;
            background: #f7f9fb;
            color: #6d7887;
            cursor: default;
        }

        .sa-control::placeholder {
            color: #a0a9b6;
        }

        .sa-help {
            margin: 4px 0 0;
            color: #929ca9;
            font-size: 8.4px;
            line-height: 1.45;
        }

        .sa-readonly-box {
            display: flex;
            min-height: 45px;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #e3e7ec;
            border-radius: 11px;
            background: #f7f9fb;
            padding: 0 12px;
        }

        .sa-readonly-value {
            min-width: 0;
            overflow: hidden;
            color: #697586;
            font-size: 11px;
            font-weight: 550;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-readonly-badge {
            flex: 0 0 auto;
            border: 1px solid #e1e5ea;
            border-radius: 999px;
            background: #fff;
            padding: 3px 7px;
            color: #9099a5;
            font-size: 7px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .sa-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-top: 1px solid var(--sa-line-soft);
            background: #fbfcfd;
            padding: 12px 18px;
        }

        .sa-save-note {
            color: #8d97a4;
            font-size: 8.5px;
            line-height: 1.45;
        }

        .sa-save-button {
            display: inline-flex;
            min-width: 168px;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--sa-gold);
            border-radius: 10px;
            background: var(--sa-gold);
            padding: 0 17px;
            color: #fff;
            font-size: 10px;
            font-weight: 760;
            line-height: 1;
            box-shadow: 0 5px 13px rgba(200, 137, 18, .12);
            transition:
                border-color .14s ease,
                background-color .14s ease,
                box-shadow .14s ease;
        }

        .sa-save-button:hover,
        .sa-save-button:focus-visible {
            border-color: var(--sa-gold-dark);
            background: var(--sa-gold-dark);
            box-shadow: 0 5px 13px rgba(170, 115, 15, .15);
            outline: none;
        }

        .sa-save-button svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Support stays secondary and clean. */
        .sa-support-card {
            margin-top: 14px;
            padding: 15px 17px 16px;
        }

        .sa-support-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .sa-support-title {
            margin: 0;
            color: #2f3947;
            font-size: 13px;
            font-weight: 750;
        }

        .sa-support-copy {
            margin: 3px 0 0;
            color: #8d97a4;
            font-size: 9px;
            line-height: 1.5;
        }

        .sa-support-line {
            height: 1px;
            flex: 1;
            margin-top: 9px;
            background: #e5e9ee;
        }

        .sa-support-form {
            display: grid;
            grid-template-columns: minmax(190px, .7fr) minmax(280px, 1.3fr) auto;
            gap: 9px;
            align-items: end;
            margin-top: 12px;
        }

        .sa-support-form .sa-control {
            min-height: 40px;
            font-size: 10px;
        }

        .sa-support-form textarea.sa-control {
            min-height: 40px;
            max-height: 110px;
            padding-block: 9px;
        }

        .sa-support-submit {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            border: 1px solid #343d49;
            border-radius: 9px;
            background: #343d49;
            padding: 0 15px;
            color: #fff;
            font-size: 9.5px;
            font-weight: 740;
            white-space: nowrap;
        }

        .sa-support-submit:hover,
        .sa-support-submit:focus-visible {
            border-color: #252c34;
            background: #252c34;
            outline: none;
        }

        /* Social-account fallback. */
        .sa-social-shell {
            max-width: 780px;
        }

        /* Responsive */
        @media (max-width: 980px) {
            .sa-account-layout {
                grid-template-columns: 240px minmax(0, 1fr);
            }

            .sa-field-grid--three {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sa-support-form {
                grid-template-columns: 1fr 1fr;
            }

            .sa-support-submit {
                grid-column: 1 / -1;
                justify-self: start;
            }
        }

        @media (max-width: 760px) {
            .sa-account-layout {
                grid-template-columns: 1fr;
            }

            .sa-profile-card {
                position: static;
            }

            .sa-profile-body {
                display: grid;
                grid-template-columns: 118px minmax(0, 1fr);
                gap: 16px;
                align-items: center;
            }

            .sa-avatar-wrap,
            .sa-avatar {
                width: 104px;
                height: 104px;
            }

            .sa-profile-name,
            .sa-profile-email {
                text-align: left;
            }

            .sa-profile-role {
                margin-inline: 0;
            }

            .sa-upload-block,
            .sa-profile-meta {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .sari-account-page {
                padding-inline: 14px !important;
            }

            .sa-eyebrow {
                font-size: 9.5px;
            }

            .sa-page-title {
                font-size: 29px;
            }

            .sa-page-copy {
                font-size: 10.5px;
            }

            .sa-card {
                border-radius: 14px;
            }

            .sa-profile-card-head,
            .sa-form-card-head,
            .sa-form-card-body,
            .sa-form-footer {
                padding-inline: 14px;
            }

            .sa-field-grid,
            .sa-field-grid--three {
                grid-template-columns: 1fr;
            }

            .sa-field--full {
                grid-column: auto;
            }

            .sa-label {
                font-size: 10.5px;
            }

            .sa-control,
            .sa-readonly-box {
                min-height: 44px;
            }

            .sa-form-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .sa-save-button {
                width: 100%;
            }

            .sa-support-form {
                grid-template-columns: 1fr;
            }

            .sa-support-submit {
                grid-column: auto;
                width: 100%;
            }
        }



        /* ============================================================
           PROFILE PHOTO — EDIT ICON ONLY
        ============================================================ */
        .sa-upload-block {
            display: none !important;
        }

        .sa-avatar-edit {
            z-index: 3;
        }

        /* ============================================================
           BUYER DELIVERY ADDRESS BOOK
        ============================================================ */
        .sa-address-book {
            margin-top: 14px;
            overflow: hidden;
        }

        .sa-address-book-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--sa-line-soft);
        }

        .sa-address-book-title-row {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .sa-address-book-title {
            margin: 0;
            color: #2b3441;
            font-size: 13px;
            font-weight: 760;
            line-height: 1.3;
            letter-spacing: -.012em;
        }

        .sa-address-count {
            display: inline-flex;
            min-width: 34px;
            min-height: 22px;
            align-items: center;
            justify-content: center;
            border: 1px solid #e1e5e9;
            border-radius: 999px;
            background: #f8f9fa;
            padding: 0 7px;
            color: #7c8793;
            font-size: 8px;
            font-weight: 750;
        }

        .sa-address-book-copy {
            max-width: 650px;
            margin: 4px 0 0;
            color: #8b95a1;
            font-size: 9.5px;
            line-height: 1.5;
        }

        .sa-add-address-button {
            display: inline-flex;
            min-height: 38px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--sa-gold);
            border-radius: 9px;
            background: var(--sa-gold);
            padding: 0 13px;
            color: #fff;
            font-size: 9.5px;
            font-weight: 750;
            cursor: pointer;
            transition: background-color .14s ease, border-color .14s ease;
        }

        .sa-add-address-button:hover,
        .sa-add-address-button:focus-visible {
            border-color: var(--sa-gold-dark);
            background: var(--sa-gold-dark);
            outline: none;
        }

        .sa-add-address-button svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-address-empty {
            display: flex;
            min-height: 116px;
            align-items: center;
            justify-content: center;
            gap: 13px;
            padding: 22px;
            text-align: left;
        }

        .sa-address-empty > svg {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            fill: none;
            stroke: #a8b0b9;
            stroke-width: 1.6;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-address-empty h3 {
            margin: 0;
            color: #6d7782;
            font-size: 11px;
            font-weight: 700;
        }

        .sa-address-empty p {
            margin: 3px 0 0;
            color: #9aa3ad;
            font-size: 8.8px;
            line-height: 1.45;
        }

        .sa-address-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 14px;
        }

        .sa-address-card {
            min-width: 0;
            border: 1px solid #e2e6ea;
            border-radius: 12px;
            background: #fff;
            padding: 13px;
        }

        .sa-address-card.is-default {
            border-color: #d8bc7b;
            box-shadow: 0 0 0 2px rgba(200, 137, 18, .06);
        }

        .sa-address-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .sa-address-label-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
        }

        .sa-address-label-row strong {
            color: #2d3742;
            font-size: 10.5px;
            font-weight: 760;
            line-height: 1.35;
        }

        .sa-address-label-row span {
            display: inline-flex;
            min-height: 20px;
            align-items: center;
            border-radius: 999px;
            background: #fff6df;
            padding: 0 7px;
            color: #9b6812;
            font-size: 7.5px;
            font-weight: 800;
        }

        .sa-address-recipient {
            margin: 4px 0 0;
            color: #697480;
            font-size: 8.8px;
            font-weight: 550;
            line-height: 1.45;
        }

        .sa-address-copy {
            margin: 9px 0 0;
            color: #7b8590;
            font-size: 8.8px;
            line-height: 1.55;
        }

        .sa-address-icon-button {
            display: grid;
            width: 30px;
            height: 30px;
            flex: 0 0 30px;
            place-items: center;
            border: 1px solid #e0e4e8;
            border-radius: 8px;
            background: #fff;
            color: #6f7a86;
            cursor: pointer;
        }

        .sa-address-icon-button:hover,
        .sa-address-icon-button:focus-visible {
            border-color: #d5ba80;
            color: #a46d0e;
            outline: none;
        }

        .sa-address-icon-button svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-address-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 11px;
            padding-top: 10px;
            border-top: 1px solid #f0f2f4;
        }

        .sa-address-text-button {
            border: 0;
            background: transparent;
            padding: 0;
            color: #9b6812;
            font-size: 8.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .sa-address-text-button:hover,
        .sa-address-text-button:focus-visible {
            color: #7f520b;
            outline: none;
        }

        .sa-address-text-button--danger {
            color: #b25a5a;
        }

        .sa-address-text-button--danger:hover,
        .sa-address-text-button--danger:focus-visible {
            color: #934444;
        }

        /* Address modal */
        .sa-address-modal[hidden] {
            display: none !important;
        }

        .sa-address-modal {
            position: fixed;
            inset: 0;
            z-index: 120;
            display: grid;
            place-items: center;
            padding: 16px;
        }

        .sa-address-modal-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(24, 29, 35, .42);
            backdrop-filter: blur(2px);
        }

        .sa-address-modal-panel {
            position: relative;
            z-index: 1;
            width: min(680px, calc(100vw - 28px));
            max-height: calc(100vh - 32px);
            overflow: auto;
            border: 1px solid #dde2e7;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 22px 60px rgba(20, 27, 34, .18);
        }

        .sa-address-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 15px 17px;
            border-bottom: 1px solid #edf0f3;
        }

        .sa-address-modal-eyebrow {
            margin: 0;
            color: #a87316;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sa-address-modal-head h2 {
            margin: 4px 0 0;
            color: #29323c;
            font-size: 15px;
            font-weight: 760;
            line-height: 1.3;
        }

        .sa-address-modal-close {
            display: grid;
            width: 31px;
            height: 31px;
            place-items: center;
            border: 1px solid #e2e6ea;
            border-radius: 8px;
            background: #fff;
            color: #707a85;
            cursor: pointer;
        }

        .sa-address-modal-close svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
        }

        .sa-address-modal-body {
            padding: 16px 17px;
        }

        .sa-address-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 15px;
        }

        .sa-address-modal-eyebrow {
            font-size: 9px;
        }

        .sa-address-modal-head h2 {
            font-size: 18px;
        }

        .sa-address-modal .sa-label {
            font-size: 11px;
            font-weight: 760;
        }

        .sa-address-modal .sa-control {
            min-height: 54px;
            font-size: 12.5px;
            border-radius: 14px;
            padding: 0 16px;
        }

        .sa-address-modal textarea.sa-control {
            min-height: 96px;
            padding: 14px 16px;
        }

        .sa-select-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236a7380' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 44px !important;
        }

        .sa-address-modal .sa-help {
            font-size: 9.5px;
            line-height: 1.55;
        }

        .sa-address-default-check {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 16px;
            border: 1px solid #e4e8ec;
            border-radius: 14px;
            background: #fafbfc;
            padding: 13px 14px;
            cursor: pointer;
        }

        .sa-address-default-input {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            margin-top: 1px;
            flex: 0 0 18px;
            border: 1.5px solid #c88912;
            border-radius: 5px;
            background: #fff;
            cursor: pointer;
            position: relative;
        }

        .sa-address-default-input:checked {
            background-color: #c88912;
            border-color: #c88912;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M3 8.2 6.2 11.2 13 4.8' stroke='white' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }

        .sa-address-default-input:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 3px rgba(200,137,18,.12);
        }

        .sa-address-default-check strong {
            display: block;
            color: #3c4653;
            font-size: 10.5px;
            font-weight: 760;
        }

        .sa-address-default-check small {
            display: block;
            margin-top: 3px;
            color: #8892a0;
            font-size: 9.2px;
            line-height: 1.45;
        }

        .sa-address-modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            border-top: 1px solid #edf0f3;
            background: #fafbfc;
            padding: 12px 17px;
        }

        .sa-address-cancel {
            min-height: 40px;
            border: 1px solid #dce1e6;
            border-radius: 9px;
            background: #fff;
            padding: 0 14px;
            color: #65717d;
            font-size: 9px;
            font-weight: 700;
            cursor: pointer;
        }

        body.sa-address-modal-open {
            overflow: hidden;
        }

        @media (max-width: 760px) {
            .sa-address-book-head {
                align-items: stretch;
                flex-direction: column;
            }

            .sa-add-address-button {
                align-self: flex-start;
            }

            .sa-address-grid,
            .sa-address-form-grid {
                grid-template-columns: 1fr;
            }

            .sa-address-form-grid .sa-field--full {
                grid-column: auto;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .sari-account-page *,
            .sari-account-page *::before,
            .sari-account-page *::after {
                transition-duration: .01ms !important;
            }
        }
    

        /* ============================================================
           FINAL UNIFIED ACCOUNT LAYOUT
           Left: Profile + Support. Right: one account/settings surface.
        ============================================================ */
        .sa-left-column {
            position: sticky;
            top: 84px;
            display: grid;
            gap: 14px;
            align-self: start;
        }

        .sa-left-column .sa-profile-card {
            position: static;
            top: auto;
        }

        .sa-unified-account {
            overflow: hidden;
        }

        .sa-unified-profile-form {
            margin: 0;
        }

        .sa-unified-section {
            background: #fff;
        }

        .sa-unified-section--bordered {
            border-top: 1px solid var(--sa-line-soft);
        }

        .sa-unified-account .sa-form-card-head {
            padding-top: 17px;
        }

        .sa-unified-account .sa-form-card-body {
            padding-bottom: 18px;
        }

        .sa-unified-account .sa-form-footer {
            border-top: 1px solid var(--sa-line-soft);
        }

        .sa-address-book--integrated {
            margin-top: 0 !important;
            border-top: 1px solid var(--sa-line-soft);
            border-radius: 0;
            background: #fff;
        }

        .sa-address-book--integrated .sa-address-book-head {
            padding: 17px 18px 14px;
        }

        .sa-address-book--integrated .sa-address-grid {
            padding-top: 13px;
        }

        .sa-support-card--sidebar {
            margin-top: 0 !important;
            padding: 14px;
        }

        .sa-support-card--sidebar .sa-support-head {
            display: block;
        }

        .sa-support-card--sidebar .sa-support-title {
            font-size: 12.5px;
        }

        .sa-support-card--sidebar .sa-support-copy {
            margin-top: 4px;
            font-size: 8.8px;
            line-height: 1.5;
        }

        .sa-support-card--sidebar .sa-support-form {
            display: grid;
            grid-template-columns: 1fr !important;
            gap: 9px;
            margin-top: 12px;
        }

        .sa-support-card--sidebar .sa-support-form textarea.sa-control {
            min-height: 78px;
            max-height: 150px;
        }

        .sa-support-card--sidebar .sa-support-submit {
            width: 100%;
            min-height: 40px;
        }

        @media (max-width: 980px) {
            .sa-account-layout {
                grid-template-columns: 240px minmax(0, 1fr);
            }
        }

        @media (max-width: 760px) {
            .sa-left-column {
                position: static;
                top: auto;
            }

            .sa-account-layout {
                grid-template-columns: 1fr;
            }

            .sa-unified-account {
                order: 2;
            }

            .sa-left-column {
                order: 1;
            }
        }

        /* FINAL READABILITY + BUYER SUPPORT MESSAGING */
        .sa-page-head{padding-bottom:13px!important}.sa-profile-card-title{font-size:14px!important}.sa-profile-badge{font-size:8.5px!important}.sa-profile-name{font-size:16.5px!important}.sa-profile-email{font-size:10.5px!important}.sa-profile-role{font-size:9px!important;padding:5px 10px!important}.sa-profile-meta-label{font-size:9.5px!important}.sa-profile-meta-value{font-size:10.5px!important;font-weight:650!important}.sa-support-card--sidebar .sa-support-title{font-size:14.5px!important}.sa-support-card--sidebar .sa-support-copy{font-size:10px!important;line-height:1.55!important}.sa-support-card--sidebar .sa-label,.sa-unified-account .sa-label,.sa-address-modal .sa-label{font-size:10px!important}.sa-support-card--sidebar .sa-control,.sa-unified-account .sa-control,.sa-address-modal .sa-control{font-size:11.5px!important}.sa-unified-account .sa-form-card-title,.sa-address-book-title{font-size:15.5px!important}.sa-unified-account .sa-form-card-copy,.sa-address-book-copy{font-size:10.5px!important;line-height:1.55!important}.sa-help,.sa-save-note{font-size:9.3px!important}.sa-readonly-value{font-size:11.5px!important}.sa-readonly-badge{font-size:8.5px!important}.sa-save-button,.sa-add-address-button,.sa-support-submit{font-size:10.5px!important}.sa-address-count{font-size:9px!important}.sa-address-empty h3{font-size:13px!important}.sa-address-empty p{font-size:10px!important}.sa-address-recipient,.sa-address-copy,.sa-address-text-button{font-size:9.5px!important}
        .sa-report-history{margin-top:14px;border-top:1px solid var(--sa-line-soft);padding-top:13px}.sa-report-history-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}.sa-report-history-head h3{margin:0;color:#394351;font-size:11.5px;font-weight:750}.sa-report-history-head>span{display:inline-grid;min-width:23px;height:23px;place-items:center;border:1px solid #e1e5ea;border-radius:999px;background:#f8f9fb;color:#7c8794;padding-inline:6px;font-size:8.5px;font-weight:700}.sa-report-history-list{display:grid;gap:7px}.sa-report-history-item{display:flex;width:100%;align-items:center;justify-content:space-between;gap:10px;border:1px solid #e5e9ee;border-radius:10px;background:#fff;padding:10px;text-align:left;cursor:pointer}.sa-report-history-item:hover,.sa-report-history-item:focus-visible{border-color:#d5b778;background:#fffdfa;outline:none}.sa-report-history-main{min-width:0}.sa-report-history-main strong{display:block;overflow:hidden;color:#3b4552;font-size:9.8px;font-weight:700;line-height:1.35;text-overflow:ellipsis;white-space:nowrap}.sa-report-history-main small{display:block;margin-top:3px;color:#949da8;font-size:8px}.sa-report-status{flex:0 0 auto;border-radius:999px;padding:4px 7px;font-size:7.8px;font-weight:750}.sa-report-status.is-open{background:#fff5df;color:#a86e0b}.sa-report-status.is-resolved{background:#edf8f1;color:#2f8453}
        .sa-support-thread-modal[hidden]{display:none!important}.sa-support-thread-modal{position:fixed;inset:0;z-index:120;display:grid;place-items:center;padding:14px}.sa-support-thread-backdrop{position:absolute;inset:0;border:0;background:rgba(24,29,36,.42)}.sa-support-thread-panel{position:relative;z-index:1;display:flex;width:min(600px,calc(100vw - 28px));max-height:min(720px,calc(100dvh - 28px));flex-direction:column;overflow:hidden;border:1px solid #e0e4e9;border-radius:16px;background:#fff;box-shadow:0 24px 70px rgba(24,29,36,.18)}.sa-support-thread-head{display:flex;align-items:center;justify-content:space-between;gap:14px;border-bottom:1px solid #edf0f3;padding:15px 17px}.sa-support-thread-head p{margin:0;color:#a87316;font-size:8.5px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.sa-support-thread-head h2{margin:4px 0 0;overflow:hidden;color:#2f3945;font-size:14px;font-weight:760;text-overflow:ellipsis;white-space:nowrap}.sa-support-thread-close{display:grid;width:34px;height:34px;place-items:center;border:1px solid #e1e5e9;border-radius:9px;background:#fff;color:#697482}.sa-support-thread-close svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8}.sa-support-thread-messages{display:flex;min-height:280px;flex:1;flex-direction:column;gap:9px;overflow-y:auto;background:#f8f9fb;padding:15px}.sa-support-thread-loading,.sa-support-thread-error,.sa-support-thread-empty{margin:auto;color:#8b95a1;font-size:10.5px;text-align:center}.sa-support-thread-error{color:#b95a5a}.sa-support-message{display:flex;width:100%}.sa-support-message.is-mine{justify-content:flex-end}.sa-support-message.is-admin{justify-content:flex-start}.sa-support-message-bubble{max-width:82%;border:1px solid #e1e5ea;border-radius:13px;background:#fff;padding:10px 12px;color:#46515f;font-size:10.5px;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere}.sa-support-message.is-mine .sa-support-message-bubble{border-color:#c88912;background:#c88912;color:#fff}.sa-support-message-meta{display:block;margin-top:5px;font-size:7.8px;opacity:.66}.sa-support-thread-reply{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:9px;border-top:1px solid #edf0f3;background:#fff;padding:12px}.sa-support-thread-reply textarea{min-height:44px;max-height:120px;resize:vertical;border:1px solid #dce1e7;border-radius:10px;background:#fff;padding:10px 11px;color:#3f4a57;font:inherit;font-size:10.5px;line-height:1.5;outline:none}.sa-support-thread-reply textarea:focus{border-color:#c88912;box-shadow:0 0 0 3px rgba(200,137,18,.08)}.sa-support-thread-reply button{min-width:78px;border:1px solid #c88912;border-radius:10px;background:#c88912;padding:0 14px;color:#fff;font-size:9.5px;font-weight:750}.sa-address-modal-error{margin-bottom:12px;border:1px solid #efd1d1;border-radius:9px;background:#fff7f7;padding:9px 11px;color:#a65050;font-size:9.5px}body.sa-support-thread-open{overflow:hidden}@media(max-width:640px){.sa-profile-card-title{font-size:13px!important}.sa-profile-name{font-size:15px!important}.sa-unified-account .sa-form-card-title,.sa-address-book-title{font-size:14px!important}.sa-support-thread-reply{grid-template-columns:1fr}.sa-support-thread-reply button{min-height:40px}}



        /* ============================================================
           ACTIVE DELIVERY ADDRESS FLOW
        ============================================================ */
        .sa-address-choice {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 15px;
            border-top: 1px solid var(--sa-line-soft);
            padding-top: 15px;
        }

        .sa-address-choice-copy {
            min-width: 0;
        }

        .sa-address-choice-copy strong {
            display: block;
            color: #394451;
            font-size: 10.5px;
            font-weight: 750;
            line-height: 1.4;
        }

        .sa-address-choice-copy span {
            display: block;
            margin-top: 3px;
            color: #929ca7;
            font-size: 9px;
            line-height: 1.45;
        }

        .sa-address-choice-actions {
            display: inline-flex;
            flex: 0 0 auto;
            overflow: hidden;
            border: 1px solid #dfe4e8;
            border-radius: 9px;
            background: #f7f8fa;
            padding: 3px;
        }

        .sa-address-choice-button {
            min-width: 58px;
            min-height: 31px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            padding: 0 11px;
            color: #7b8591;
            font-size: 9.5px;
            font-weight: 750;
            cursor: pointer;
        }

        .sa-address-choice-button.is-active {
            background: #fff;
            color: #9b6812;
            box-shadow: 0 1px 4px rgba(31,41,55,.08);
        }

        .sa-address-active-line {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin: 0 0 13px;
        }

        .sa-address-active-line strong {
            color: #4f5965;
            font-size: 9.5px;
            font-weight: 750;
        }

        .sa-address-active-pill {
            display: inline-flex;
            min-height: 21px;
            align-items: center;
            border-radius: 999px;
            background: #fff6df;
            padding: 0 8px;
            color: #996610;
            font-size: 8px;
            font-weight: 800;
        }

        .sa-address-card .sa-address-active-button {
            min-height: 31px;
            border: 1px solid #dfe4e8;
            border-radius: 8px;
            background: #f4f6f8;
            padding: 0 10px;
            color: #8b949e;
            font-size: 9px;
            font-weight: 750;
            cursor: default;
        }

        .sa-address-card .sa-address-use-button {
            min-height: 31px;
            border: 1px solid #d9be80;
            border-radius: 8px;
            background: #fff;
            padding: 0 10px;
            color: #9b6812;
            font-size: 9px;
            font-weight: 750;
            cursor: pointer;
        }

        .sa-address-card .sa-address-use-button:hover,
        .sa-address-card .sa-address-use-button:focus-visible {
            border-color: var(--sa-gold);
            background: #fffaf0;
            outline: none;
        }

        .sa-address-book[hidden] {
            display: none !important;
        }

        @media (max-width: 640px) {
            .sa-address-choice {
                align-items: flex-start;
                flex-direction: column;
            }

            .sa-address-choice-actions {
                width: 100%;
            }

            .sa-address-choice-button {
                flex: 1 1 50%;
            }
        }

        .sa-form-footer--final {
            margin-top: 0;
            border-top: 1px solid var(--sa-line-soft);
            background: #fbfcfd;
        }

        .sa-add-address-button:disabled {
            border-color: #e1e5e9 !important;
            background: #f5f6f8 !important;
            color: #9aa3ad !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }


        /* ============================================================
           ADDRESS FLOW POLISH — ENTERPRISE STYLE
        ============================================================ */
        .sa-address-choice {
            align-items: flex-start;
            flex-direction: column;
        }

        .sa-address-choice-copy strong {
            font-size: 11.5px;
        }

        .sa-address-choice-copy span {
            font-size: 9.6px;
        }

        .sa-address-choice-actions--cards {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .sa-address-choice-button--card {
            display: flex;
            width: 100%;
            text-decoration: none;
            min-height: 84px;
            align-items: flex-start;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            border: 1px solid #dfe4ea;
            border-radius: 12px;
            background: #fff;
            padding: 14px 15px;
            text-align: left;
            box-shadow: 0 1px 2px rgba(31,41,55,.03);
        }

        .sa-address-choice-button--card.is-active {
            border-color: #d2af67;
            background: #fffaf1;
            box-shadow: 0 0 0 3px rgba(200,137,18,.08);
        }

        .sa-address-choice-title {
            display: block;
            color: #2f3945;
            font-size: 10.5px;
            font-weight: 760;
            line-height: 1.35;
        }

        .sa-address-choice-subtitle {
            display: block;
            color: #8a94a1;
            font-size: 9px;
            line-height: 1.5;
        }

        .sa-address-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .sa-address-card {
            padding: 14px 15px;
            border-radius: 14px;
        }

        .sa-address-card-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .sa-address-card-content {
            min-width: 0;
            flex: 1 1 auto;
        }

        .sa-address-card-summary {
            margin: 5px 0 0;
            overflow: hidden;
            color: #6d7885;
            font-size: 9.4px;
            line-height: 1.5;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-address-card-tools {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sa-address-card .sa-address-use-button,
        .sa-address-card .sa-address-active-button,
        .sa-address-text-button {
            min-height: 34px;
            font-size: 9.4px;
        }

        .sa-address-text-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 74px;
            border: 1px solid #e6d8d8;
            border-radius: 9px;
            padding: 0 12px;
            background: #fff;
        }

        .sa-address-text-button--danger {
            border-color: #efd3d3;
            background: #fffafa;
        }

        .sa-address-modal .sa-control:disabled {
            background: #f7f9fb;
            color: #9aa3ad;
            cursor: not-allowed;
        }

        .sa-address-modal .sa-help {
            margin-top: 5px;
        }

        @media (max-width: 900px) {
            .sa-address-card-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .sa-address-card-summary {
                white-space: normal;
            }

            .sa-address-card-tools {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .sa-address-choice-actions--cards {
                grid-template-columns: 1fr;
            }
        }


        /* ============================================================
           SIMPLE ADDRESS MANAGEMENT — CLEAN ENTERPRISE LAYOUT
        ============================================================ */
        .sa-address-choice--simple {
            gap: 11px;
        }

        .sa-address-choice-actions--simple {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            overflow: visible;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .sa-address-choice-button--simple {
            display: block;
            width: 100%;
            min-height: 58px;
            border: 1px solid #dde3e9;
            border-radius: 9px;
            background: #fff;
            padding: 10px 12px;
            color: #4f5a67;
            text-align: left;
            text-decoration: none;
            box-shadow: none;
        }

        .sa-address-choice-button--simple:hover,
        .sa-address-choice-button--simple:focus-visible {
            border-color: #c8a55f;
            outline: none;
        }

        .sa-address-choice-button--simple.is-active {
            border-color: #c88912;
            background: #fffdf8;
            color: #7d5410;
            box-shadow: none;
        }

        .sa-address-choice-button--simple strong {
            display: block;
            color: #303a46;
            font-size: 10.3px;
            font-weight: 740;
            line-height: 1.35;
        }

        .sa-address-choice-button--simple span {
            display: block;
            margin-top: 2px;
            color: #8a94a0;
            font-size: 8.8px;
            font-weight: 500;
            line-height: 1.4;
        }

        .sa-address-book--integrated .sa-address-book-head {
            align-items: center;
            padding: 15px 18px 12px;
        }

        .sa-address-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            padding: 0 18px 14px;
        }

        .sa-address-row {
            display: grid;
            grid-template-columns: minmax(105px, .7fr) minmax(0, 3.5fr) minmax(118px, .95fr) 72px;
            gap: 14px;
            align-items: center;
            min-width: 0;
            border-top: 1px solid #edf0f3;
            padding: 13px 0;
            background: transparent;
        }

        .sa-address-row:first-child {
            border-top: 0;
        }

        .sa-address-row.is-default {
            background: transparent;
        }

        .sa-address-row-label {
            display: flex;
            min-width: 0;
            align-items: baseline;
            gap: 8px;
        }

        .sa-address-row-label strong {
            overflow: hidden;
            color: #303a46;
            font-size: 10.3px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-address-edit-text,
        .sa-address-use-text,
        .sa-address-remove-text {
            border: 0;
            background: transparent;
            padding: 0;
            font-family: inherit;
            font-size: 9px;
            font-weight: 700;
            line-height: 1.4;
            cursor: pointer;
            white-space: nowrap;
        }

        .sa-address-edit-text {
            color: #8b95a0;
        }

        .sa-address-row-summary {
            min-width: 0;
            margin: 0;
            overflow: hidden;
            color: #6f7a86;
            font-size: 9.4px;
            line-height: 1.5;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-address-row-use,
        .sa-address-row-remove {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .sa-address-use-text {
            color: #9b6812;
        }

        .sa-address-active-text {
            color: #8a651e;
            font-size: 9px;
            font-weight: 750;
            white-space: nowrap;
        }

        .sa-address-remove-text {
            color: #b45151;
        }

        .sa-address-edit-text:hover,
        .sa-address-edit-text:focus-visible,
        .sa-address-use-text:hover,
        .sa-address-use-text:focus-visible,
        .sa-address-remove-text:hover,
        .sa-address-remove-text:focus-visible {
            text-decoration: underline;
            outline: none;
        }

        .sa-add-address-button {
            min-height: 36px;
            border-radius: 8px;
            padding: 0 14px;
            box-shadow: none;
        }

        .sa-address-empty {
            min-height: 92px;
            padding: 20px 18px;
            text-align: left;
        }

        @media (max-width: 900px) {
            .sa-address-row {
                grid-template-columns: minmax(90px, .7fr) minmax(0, 2.7fr) minmax(105px, .9fr) 64px;
                gap: 10px;
            }
        }

        @media (max-width: 700px) {
            .sa-address-choice-actions--simple {
                grid-template-columns: 1fr;
            }

            .sa-address-row {
                grid-template-columns: 1fr auto auto;
                gap: 8px 12px;
            }

            .sa-address-row-label {
                grid-column: 1 / 2;
            }

            .sa-address-row-summary {
                grid-column: 1 / -1;
                grid-row: 2;
                white-space: normal;
            }

            .sa-address-row-use {
                grid-column: 2;
                grid-row: 1;
            }

            .sa-address-row-remove {
                grid-column: 3;
                grid-row: 1;
            }
        }


        /* ============================================================
           ENTERPRISE ADDRESS BOOK LAYOUT
        ============================================================ */
        .sa-address-inline-note {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
            border-top: 1px solid var(--sa-line-soft);
            padding-top: 14px;
            color: #7d8792;
            font-size: 10px;
            line-height: 1.55;
        }

        .sa-address-inline-link {
            border: 0;
            background: transparent;
            padding: 0;
            color: #2f3945;
            font-size: 10px;
            font-weight: 650;
            cursor: pointer;
        }

        .sa-address-book--enterprise {
            margin-top: 4px;
            border-top: 1px solid var(--sa-line-soft);
            padding: 22px 24px 0;
        }

        .sa-address-enterprise-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
        }

        .sa-address-enterprise-title {
            margin: 0;
            color: #2a313b;
            font-size: 19px;
            font-weight: 760;
            line-height: 1.2;
            letter-spacing: -.03em;
        }

        .sa-address-enterprise-copy {
            margin: 6px 0 0;
            color: #8892a0;
            font-size: 10.5px;
            line-height: 1.55;
        }

        .sa-address-enterprise-add {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            border: 1px solid var(--sa-gold);
            border-radius: 12px;
            background: var(--sa-gold);
            padding: 0 16px;
            color: #fff;
            font-size: 11px;
            font-weight: 650;
            cursor: pointer;
            transition: background-color .14s ease, border-color .14s ease, color .14s ease;
            white-space: nowrap;
        }

        .sa-address-enterprise-add:hover,
        .sa-address-enterprise-add:focus-visible {
            border-color: var(--sa-gold-dark);
            background: var(--sa-gold-dark);
            color: #fff;
            outline: none;
        }

        .sa-address-enterprise-add[disabled] {
            opacity: .6;
            cursor: not-allowed;
        }

        .sa-address-enterprise-add-plus {
            display: inline-grid;
            width: 28px;
            height: 28px;
            place-items: center;
            border: 1px solid currentColor;
            border-radius: 999px;
            font-size: 18px;
            line-height: 1;
            font-weight: 500;
        }

        .sa-address-enterprise-divider {
            margin-top: 12px;
            border-top: 1px solid var(--sa-line);
        }

        .sa-address-empty--enterprise {
            margin-top: 18px;
            padding: 6px 0 12px;
            border: 0;
            background: transparent;
        }

        .sa-address-enterprise-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 28px 56px;
            padding: 24px 0 2px;
        }

        .sa-address-enterprise-item {
            min-width: 0;
            padding: 4px 0 0;
        }

        .sa-address-enterprise-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .sa-address-enterprise-item-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .sa-address-enterprise-item-heading strong {
            color: #202735;
            font-size: 13.5px;
            font-weight: 760;
            line-height: 1.3;
            letter-spacing: -.02em;
        }

        .sa-address-enterprise-badge {
            display: inline-flex;
            align-items: center;
            border: 1px solid #ead39a;
            border-radius: 999px;
            padding: 3px 8px;
            color: #a36d0d;
            font-size: 7.9px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sa-address-enterprise-lines {
            margin-top: 14px;
            display: grid;
            gap: 8px;
        }

        .sa-address-enterprise-lines p {
            margin: 0;
            color: #747d88;
            font-size: 10.4px;
            line-height: 1.55;
        }

        .sa-address-enterprise-activate {
            border: 1px solid #e6d4b1;
            border-radius: 10px;
            background: #fff;
            padding: 8px 12px;
            color: #aa730f;
            font-size: 9.5px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .sa-address-enterprise-actions {
            display: flex;
            align-items: center;
            gap: 28px;
            margin-top: 18px;
        }

        .sa-address-enterprise-edit,
        .sa-address-enterprise-delete {
            border: 0;
            background: transparent;
            padding: 0;
            font-size: 10.5px;
            font-weight: 500;
            line-height: 1.2;
            cursor: pointer;
        }

        .sa-address-enterprise-edit {
            color: #1f2430;
        }

        .sa-address-enterprise-delete {
            color: #ff3b30;
        }

        .sa-address-enterprise-edit:hover,
        .sa-address-enterprise-edit:focus-visible {
            color: #000;
            outline: none;
        }

        .sa-address-enterprise-delete:hover,
        .sa-address-enterprise-delete:focus-visible {
            color: #e1261c;
            outline: none;
        }

        @media (max-width: 920px) {
            .sa-address-enterprise-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 640px) {
            .sa-address-inline-note,
            .sa-address-enterprise-head,
            .sa-address-enterprise-item-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .sa-address-book--enterprise {
                padding: 20px 18px 0;
            }

            .sa-address-enterprise-actions {
                gap: 22px;
            }
        }


        /* ============================================================
           SEARCHABLE LOCATION DROPDOWNS + INLINE ADDRESS ACTIONS
           UI-only refinement; keeps existing address API/routes.
        ============================================================ */
        .sa-smart-select {
            position: relative;
            width: 100%;
        }

        .sa-smart-select-trigger {
            display: flex;
            width: 100%;
            min-height: 54px;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #d7dee7;
            border-radius: 14px;
            background: #fff;
            padding: 0 15px 0 16px;
            color: #2e3948;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.4;
            text-align: left;
            cursor: pointer;
            outline: none;
            transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;
        }

        .sa-smart-select-trigger:hover:not(:disabled) {
            border-color: #c5ced8;
        }

        .sa-smart-select.is-open .sa-smart-select-trigger,
        .sa-smart-select-trigger:focus-visible {
            border-color: var(--sa-gold);
            box-shadow: 0 0 0 3px rgba(200,137,18,.08);
        }

        .sa-smart-select-trigger:disabled {
            border-color: #e3e7ec;
            background: #f7f9fb;
            color: #9aa3ad;
            cursor: not-allowed;
        }

        .sa-smart-select-chevron {
            width: 9px;
            height: 9px;
            flex: 0 0 9px;
            border-right: 1.8px solid #687482;
            border-bottom: 1.8px solid #687482;
            transform: rotate(45deg) translateY(-2px);
            transition: transform .14s ease;
        }

        .sa-smart-select.is-open .sa-smart-select-chevron {
            transform: rotate(225deg) translate(-2px, -2px);
        }

        .sa-smart-select-menu {
            position: absolute;
            top: calc(100% + 7px);
            left: 0;
            z-index: 170;
            width: 100%;
            min-width: 260px;
            overflow: hidden;
            border: 1px solid #dfe4ea;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 16px 38px rgba(31,41,55,.13), 0 3px 10px rgba(31,41,55,.06);
        }

        .sa-smart-select-menu[hidden] {
            display: none !important;
        }

        .sa-smart-select-search-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 7px;
            border-bottom: 1px solid #edf0f3;
            padding: 9px;
            background: #fff;
        }

        .sa-smart-select-search {
            min-width: 0;
            height: 38px;
            border: 1px solid #dce2e8;
            border-radius: 9px;
            background: #fff;
            padding: 0 11px;
            color: #35404d;
            font-family: inherit;
            font-size: 10.5px;
            outline: none;
        }

        .sa-smart-select-search::placeholder {
            color: #9ba5b1;
        }

        .sa-smart-select-search:focus {
            border-color: #c8a55f;
            box-shadow: 0 0 0 2px rgba(200,137,18,.06);
        }

        .sa-smart-select-search-button {
            height: 38px;
            border: 1px solid #d7dee6;
            border-radius: 9px;
            background: #fff;
            padding: 0 11px;
            color: #4e5966;
            font-family: inherit;
            font-size: 9.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .sa-smart-select-search-button:hover,
        .sa-smart-select-search-button:focus-visible {
            border-color: #bcc6d0;
            background: #f7f9fb;
            outline: none;
        }

        .sa-smart-select-options {
            max-height: 230px;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 5px;
            scrollbar-width: thin;
            scrollbar-color: #aab2bc transparent;
        }

        .sa-smart-select-option {
            display: block;
            width: 100%;
            min-height: 36px;
            border: 0;
            border-radius: 8px;
            background: #fff;
            padding: 8px 10px;
            color: #37414e;
            font-family: inherit;
            font-size: 10.5px;
            font-weight: 500;
            line-height: 1.35;
            text-align: left;
            cursor: pointer;
        }

        .sa-smart-select-option:hover,
        .sa-smart-select-option:focus-visible {
            background: #f2f4f7;
            color: #222b36;
            outline: none;
        }

        .sa-smart-select-option.is-selected {
            background: #f5f6f8;
            color: #9a6812;
            font-weight: 700;
        }

        .sa-smart-select-empty {
            padding: 13px 11px;
            color: #929ca8;
            font-size: 10px;
            line-height: 1.45;
        }

        /* "Use this address" now lives with Edit/Delete and is text-only. */
        .sa-address-enterprise-actions {
            gap: 22px;
            flex-wrap: wrap;
        }

        .sa-address-enterprise-action-form,
        .sa-address-enterprise-delete-form {
            margin: 0;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-active-text {
            min-height: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            padding: 0;
            color: var(--sa-gold);
            font-family: inherit;
            font-size: 10.5px;
            font-weight: 650;
            line-height: 1.2;
            box-shadow: none;
            white-space: nowrap;
        }

        .sa-address-enterprise-activate {
            cursor: pointer;
        }

        .sa-address-enterprise-activate:hover,
        .sa-address-enterprise-activate:focus-visible {
            color: var(--sa-gold-dark);
            text-decoration: underline;
            outline: none;
        }

        .sa-address-enterprise-active-text {
            color: #9d741f;
            cursor: default;
        }

        @media (max-width: 640px) {
            .sa-smart-select-menu {
                min-width: 100%;
            }
        }


        /* ============================================================
           FINAL ACCOUNT READABILITY PASS
           Typography only; layout and behavior remain unchanged.
        ============================================================ */
        .sa-profile-card-title {
            font-size: 16px !important;
            font-weight: 760 !important;
        }

        .sa-profile-badge {
            font-size: 10px !important;
        }

        .sa-profile-name {
            font-size: 18px !important;
        }

        .sa-profile-email {
            font-size: 12px !important;
        }

        .sa-profile-role {
            font-size: 10.5px !important;
            padding: 5px 11px !important;
        }

        .sa-profile-meta-label {
            font-size: 11px !important;
        }

        .sa-profile-meta-value {
            font-size: 12px !important;
            font-weight: 650 !important;
        }

        .sa-support-card--sidebar .sa-support-title {
            font-size: 16px !important;
        }

        .sa-support-card--sidebar .sa-support-copy {
            font-size: 11.5px !important;
            line-height: 1.6 !important;
        }

        .sa-support-card--sidebar .sa-label,
        .sa-unified-account .sa-label {
            font-size: 11.5px !important;
        }

        .sa-support-card--sidebar .sa-control,
        .sa-unified-account .sa-control {
            font-size: 12.5px !important;
        }

        .sa-support-submit {
            font-size: 11px !important;
        }

        .sa-report-history-head h3 {
            font-size: 13px !important;
        }

        .sa-report-history-head > span {
            font-size: 9.5px !important;
        }

        .sa-report-history-main strong {
            font-size: 11px !important;
        }

        .sa-report-history-main small {
            font-size: 9.3px !important;
        }

        .sa-report-status {
            font-size: 9px !important;
        }

        .sa-unified-account .sa-form-card-title {
            font-size: 17px !important;
        }

        .sa-help,
        .sa-save-note {
            font-size: 10px !important;
            line-height: 1.55 !important;
        }

        .sa-readonly-value {
            font-size: 12.5px !important;
        }

        .sa-readonly-badge {
            font-size: 9px !important;
        }

        .sa-address-active-line strong {
            font-size: 10.8px !important;
        }

        .sa-address-active-pill {
            font-size: 9px !important;
            min-height: 23px !important;
        }

        .sa-address-inline-note,
        .sa-address-inline-link {
            font-size: 10.5px !important;
        }

        .sa-address-enterprise-title {
            font-size: 20px !important;
        }

        .sa-address-enterprise-item-heading strong {
            font-size: 14.5px !important;
        }

        .sa-address-enterprise-badge {
            font-size: 8.5px !important;
        }

        .sa-address-enterprise-lines p {
            font-size: 11.3px !important;
            line-height: 1.6 !important;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-active-text,
        .sa-address-enterprise-edit,
        .sa-address-enterprise-delete {
            font-size: 11px !important;
        }

        .sa-address-enterprise-add {
            font-size: 11.5px !important;
        }

        .sa-save-button {
            font-size: 11.5px !important;
        }

        @media (max-width: 640px) {
            .sa-profile-card-title {
                font-size: 15px !important;
            }

            .sa-profile-name {
                font-size: 17px !important;
            }

            .sa-support-card--sidebar .sa-support-title,
            .sa-unified-account .sa-form-card-title {
                font-size: 15.5px !important;
            }

            .sa-address-enterprise-title {
                font-size: 18px !important;
            }
        }


        /* ============================================================
           FINAL MICRO CLEANUP: plain registered text + icon actions
        ============================================================ */
        .sa-address-active-plain {
            display: inline-flex;
            align-items: center;
            color: var(--sa-gold);
            font-size: 10.8px;
            font-weight: 700;
            line-height: 1.25;
        }

        .sa-address-enterprise-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 18px;
        }

        .sa-address-enterprise-action-form,
        .sa-address-enterprise-delete-form {
            margin: 0;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-edit,
        .sa-address-enterprise-delete,
        .sa-address-enterprise-active-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            line-height: 1;
        }

        .sa-address-enterprise-activate svg,
        .sa-address-enterprise-edit svg,
        .sa-address-enterprise-delete svg,
        .sa-address-enterprise-active-text svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-active-text {
            color: var(--sa-gold);
        }

        .sa-address-enterprise-edit {
            color: #1f2430;
        }

        .sa-address-enterprise-delete {
            color: #ff3b30;
        }

        .sa-address-enterprise-activate:hover,
        .sa-address-enterprise-activate:focus-visible {
            color: var(--sa-gold-dark);
            outline: none;
        }

        .sa-address-enterprise-edit:hover,
        .sa-address-enterprise-edit:focus-visible {
            color: #000;
            outline: none;
        }

        .sa-address-enterprise-delete:hover,
        .sa-address-enterprise-delete:focus-visible {
            color: #e1261c;
            outline: none;
        }


        /* ============================================================
           CLEAN SEX DROPDOWN + ICON ACTIONS POLISH
        ============================================================ */
        .sa-simple-select {
            position: relative;
        }

        .sa-simple-select-trigger {
            width: 100%;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #d8dee6;
            border-radius: 14px;
            background: #fff;
            padding: 0 16px;
            color: #27303b;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
        }

        .sa-simple-select-trigger:hover,
        .sa-simple-select-trigger:focus-visible,
        .sa-simple-select.is-open .sa-simple-select-trigger {
            border-color: #c88912;
            box-shadow: 0 0 0 4px rgba(200,137,18,.08);
            outline: none;
        }

        .sa-simple-select-trigger svg {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            stroke: #5f6975;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-simple-select-menu {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 8px);
            z-index: 40;
            overflow: hidden;
            border: 1px solid #d9e0e7;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(22, 29, 37, .12);
            padding: 8px;
        }

        .sa-simple-select-option {
            width: 100%;
            display: flex;
            align-items: center;
            border: 0;
            border-radius: 10px;
            background: #fff;
            padding: 10px 12px;
            color: #2c3440;
            font-family: inherit;
            font-size: 12px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
        }

        .sa-simple-select-option:hover,
        .sa-simple-select-option:focus-visible {
            background: #f3f5f7;
            outline: none;
        }

        .sa-simple-select-option.is-selected {
            background: #eef2f6;
            color: #1f2937;
            font-weight: 700;
        }

        .sa-address-enterprise-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 18px;
        }

        .sa-address-enterprise-action-form,
        .sa-address-enterprise-delete-form {
            margin: 0;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-edit,
        .sa-address-enterprise-delete,
        .sa-address-enterprise-active-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border: 0;
            background: transparent;
            padding: 0;
            line-height: 1;
            cursor: pointer;
        }

        .sa-address-enterprise-activate svg,
        .sa-address-enterprise-edit svg,
        .sa-address-enterprise-delete svg,
        .sa-address-enterprise-active-text svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sa-address-enterprise-activate,
        .sa-address-enterprise-active-text {
            color: var(--sa-gold);
        }

        .sa-address-enterprise-edit {
            color: #1f2430;
        }

        .sa-address-enterprise-delete {
            color: #ff3b30;
        }


        /* Add Address — outline gold only, no filled background */
        .sa-address-enterprise-add {
            border: 1px solid var(--sa-gold) !important;
            background: #fff !important;
            color: var(--sa-gold) !important;
            box-shadow: none !important;
        }

        .sa-address-enterprise-add:hover,
        .sa-address-enterprise-add:focus-visible {
            border-color: var(--sa-gold-dark) !important;
            background: #fff !important;
            color: var(--sa-gold-dark) !important;
            box-shadow: 0 0 0 3px rgba(200,137,18,.07) !important;
            outline: none !important;
        }

        .sa-address-enterprise-add-plus {
            border-color: currentColor !important;
            background: transparent !important;
            color: inherit !important;
        }


        /* ============================================================
           ACCOUNT HEADER — MATCH BUYER CART TITLE SCALE
           Title-only visual override. No account functionality changed.
        ============================================================ */
        .sa-page-head {
            padding: 2px 2px 14px !important;
        }

        .sa-eyebrow {
            margin: 0 !important;
            color: #a87316 !important;
            font-size: 10.5px !important;
            font-weight: 800 !important;
            line-height: 1.2 !important;
            letter-spacing: .14em !important;
            text-transform: uppercase !important;
        }

        .sa-page-title {
            margin: 5px 0 0 !important;
            color: #2e343b !important;
            font-size: clamp(27px, 2.25vw, 31px) !important;
            font-weight: 760 !important;
            line-height: 1.04 !important;
            letter-spacing: -.035em !important;
        }

        .sa-page-title span {
            color: #c88912 !important;
        }

        @media (max-width: 640px) {
            .sa-eyebrow {
                font-size: 9.5px !important;
            }

            .sa-page-title {
                font-size: 25px !important;
            }
        }

</style>

    @if (session('success'))
        <div class="sa-alert sa-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="sa-alert sa-alert--error">{{ $errors->first() }}</div>
    @endif

    <section class="sa-page-head">
        <p class="sa-eyebrow">Account Management</p>
        <h1 class="sa-page-title">Buyer <span>Profile</span></h1>
    </section>

    @if ($buyerAccount)
        @php
            $buyerInitials = mb_strtoupper(
                mb_substr((string) $buyerAccount->first_name, 0, 1)
                . mb_substr((string) $buyerAccount->last_name, 0, 1)
            );

            $buyerFullName = trim((string) $buyerAccount->first_name . ' ' . (string) $buyerAccount->last_name);

            $buyerPhotoUrl = filled($buyerAccount->profile_image_path)
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($buyerAccount->profile_image_path)
                : null;

            $activeSavedAddress = $addresses->firstWhere('is_default', true);
            $usingSavedAddress = $activeSavedAddress !== null;
            $showAddressBook = request()->boolean('manage_addresses') || $usingSavedAddress || $errors->address->any();
            $activeAreaName = $activeSavedAddress?->municipality_name
                ?: $activeSavedAddress?->province_name
                ?: $buyerAccount->municipality_name
                ?: $buyerAccount->province_name
                ?: 'Not set';
        @endphp

        <div class="sa-account-layout">
            {{-- LEFT: PROFILE + SUPPORT --}}
            <div class="sa-left-column">
                <aside class="sa-card sa-profile-card">
                    <header class="sa-profile-card-head">
                        <h2 class="sa-profile-card-title">Profile</h2>
                        <span class="sa-profile-badge">Buyer account</span>
                    </header>

                    <div class="sa-profile-body">
                        <div class="sa-avatar-wrap">
                            <div class="sa-avatar">
                                @if ($buyerPhotoUrl)
                                    <img
                                        id="buyerProfilePreview"
                                        src="{{ $buyerPhotoUrl }}"
                                        data-original-src="{{ $buyerPhotoUrl }}"
                                        alt="Current buyer profile photo"
                                        loading="eager"
                                        decoding="async"
                                    >
                                    <span id="buyerProfileFallback" hidden>{{ $buyerInitials ?: 'B' }}</span>
                                @else
                                    <img
                                        id="buyerProfilePreview"
                                        src=""
                                        data-original-src=""
                                        alt="Selected buyer profile photo"
                                        hidden
                                    >
                                    <span id="buyerProfileFallback">{{ $buyerInitials ?: 'B' }}</span>
                                @endif
                            </div>

                            <label
                                for="buyerProfileImage"
                                class="sa-avatar-edit"
                                title="Change profile photo"
                                aria-label="Change profile photo"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                            </label>
                        </div>

                        <div>
                            <p class="sa-profile-name" id="buyerProfileNamePreview">{{ $buyerFullName ?: 'SARI Buyer' }}</p>
                            <p class="sa-profile-email">{{ $buyerAccount->email }}</p>
                            <span class="sa-profile-role">Buyer</span>
                        </div>

                        <input
                            id="buyerProfileImage"
                            type="file"
                            name="profile_image"
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                            form="buyerAccountForm"
                        >
                        <span id="buyerProfileFileStatus" class="sr-only" aria-live="polite"></span>

                        <div class="sa-profile-meta">
                            <div class="sa-profile-meta-row">
                                <span class="sa-profile-meta-label">Contact</span>
                                <span class="sa-profile-meta-value" id="buyerProfileContactPreview">{{ $buyerAccount->contact_no ?: 'Not set' }}</span>
                            </div>
                            <div class="sa-profile-meta-row">
                                <span class="sa-profile-meta-label">Area</span>
                                <span class="sa-profile-meta-value">{{ $activeAreaName }}</span>
                            </div>
                        </div>
                    </div>
                </aside>

                
            </div>

            {{-- RIGHT: ONE UNIFIED ACCOUNT CONTAINER --}}
            <section class="sa-card sa-unified-account">
                <form
                    method="POST"
                    action="{{ route('buyer.account.update') }}"
                    enctype="multipart/form-data"
                    id="buyerAccountForm"
                    class="sa-unified-profile-form"
                >
                    @csrf
                    @method('PATCH')

                    <section class="sa-unified-section">
                        <header class="sa-form-card-head">
                            <div class="sa-form-card-heading">
                                <h2 class="sa-form-card-title">Basic information</h2>
                            </div>
                            <span class="sa-form-card-line" aria-hidden="true"></span>
                        </header>

                        <div class="sa-form-card-body">
                            <div class="sa-field-grid">
                                <div>
                                    <label for="buyerFirstName" class="sa-label">First Name <span class="sa-required">*</span></label>
                                    <input
                                        id="buyerFirstName"
                                        name="first_name"
                                        value="{{ old('first_name', $buyerAccount->first_name) }}"
                                        required
                                        maxlength="50"
                                        autocomplete="given-name"
                                        class="sa-control"
                                        data-name-only
                                    >
                                </div>

                                <div>
                                    <label for="buyerLastName" class="sa-label">Last Name <span class="sa-required">*</span></label>
                                    <input
                                        id="buyerLastName"
                                        name="last_name"
                                        value="{{ old('last_name', $buyerAccount->last_name) }}"
                                        required
                                        maxlength="50"
                                        autocomplete="family-name"
                                        class="sa-control"
                                        data-name-only
                                    >
                                </div>

                                <div>
                                    <label for="buyerMiddleInitial" class="sa-label">Middle Initial</label>
                                    <input
                                        id="buyerMiddleInitial"
                                        name="middle_initial"
                                        value="{{ old('middle_initial', $buyerAccount->middle_initial) }}"
                                        maxlength="2"
                                        autocomplete="additional-name"
                                        class="sa-control uppercase"
                                        placeholder="MI"
                                    >
                                    <p class="sa-help">Optional · up to 2 letters.</p>
                                </div>

                                <div>
                                    <label for="buyerSexValue" class="sa-label">Sex</label>
                                    <div class="sa-simple-select" data-simple-select>
                                        <input type="hidden" id="buyerSexValue" name="sex" value="{{ old('sex', $buyerAccount->sex) }}">
                                        <button type="button" class="sa-simple-select-trigger" data-simple-select-trigger aria-haspopup="listbox" aria-expanded="false">
                                            <span data-simple-select-label>{{ old('sex', $buyerAccount->sex) ?: 'Not set' }}</span>
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                        </button>
                                        <div class="sa-simple-select-menu" data-simple-select-menu hidden>
                                            <button type="button" class="sa-simple-select-option {{ old('sex', $buyerAccount->sex) === '' ? 'is-selected' : '' }}" data-value="">Not set</button>
                                            <button type="button" class="sa-simple-select-option {{ old('sex', $buyerAccount->sex) === 'Male' ? 'is-selected' : '' }}" data-value="Male">Male</button>
                                            <button type="button" class="sa-simple-select-option {{ old('sex', $buyerAccount->sex) === 'Female' ? 'is-selected' : '' }}" data-value="Female">Female</button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="buyerBirthday" class="sa-label">Birthday <span class="sa-required">*</span></label>
                                    <input
                                        id="buyerBirthday"
                                        type="date"
                                        name="birthday"
                                        value="{{ old('birthday', optional($buyerAccount->birthday)->format('Y-m-d') ?: $buyerAccount->getRawOriginal('birthday')) }}"
                                        min="1900-01-01"
                                        max="{{ now()->subYears(18)->toDateString() }}"
                                        required
                                        class="sa-control"
                                    >
                                </div>

                                <div>
                                    <label for="buyerAge" class="sa-label">Age</label>
                                    <div class="sa-readonly-box">
                                        <span id="buyerAge" class="sa-readonly-value">{{ old('age', $buyerAccount->age) ?: '—' }}</span>
                                        <span class="sa-readonly-badge">Auto</span>
                                    </div>
                                    <p class="sa-help">Calculated automatically from Birthday.</p>
                                </div>

                                <div>
                                    <label for="buyerEmail" class="sa-label">Email</label>
                                    <div class="sa-readonly-box">
                                        <span id="buyerEmail" class="sa-readonly-value">{{ $buyerAccount->email }}</span>
                                        <span class="sa-readonly-badge">Locked</span>
                                    </div>
                                </div>

                                <div>
                                    <label for="buyerContact" class="sa-label">Contact Number <span class="sa-required">*</span></label>
                                    <input
                                        id="buyerContact"
                                        name="contact_no"
                                        value="{{ old('contact_no', $buyerAccount->contact_no) }}"
                                        required
                                        inputmode="tel"
                                        autocomplete="tel"
                                        minlength="11"
                                        maxlength="13"
                                        pattern="(?:09[0-9]{9}|\+639[0-9]{9})"
                                        class="sa-control"
                                        placeholder="09XXXXXXXXX or +639XXXXXXXXX"
                                    >
                                    <p class="sa-help">Use 09XXXXXXXXX or +639XXXXXXXXX · numbers only.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sa-unified-section sa-unified-section--bordered">
                        <header class="sa-form-card-head">
                            <div class="sa-form-card-heading">
                                <h2 class="sa-form-card-title">Address details</h2>
                            </div>
                            <span class="sa-form-card-line" aria-hidden="true"></span>
                        </header>

                        <div class="sa-form-card-body">
                            <div class="sa-address-active-line">
                                <strong>Active address</strong>
                                @if ($usingSavedAddress)
                                    <span class="sa-address-active-pill">
                                        {{ $activeSavedAddress->label ?: 'Saved address' }}
                                    </span>
                                @else
                                    <span class="sa-address-active-plain">Registered address</span>
                                @endif
                            </div>

                            <div class="sa-field-grid sa-field-grid--three">
                                @if ($usingSavedAddress)
                                    <input
                                        type="hidden"
                                        name="street_address"
                                        value="{{ old('street_address', $buyerAccount->street_address) }}"
                                    >

                                    <div class="sa-field--full">
                                        <label class="sa-label">Street / Unit Address</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $activeSavedAddress->street_address }}</span>
                                            <span class="sa-readonly-badge">Active</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sa-label">Barangay</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $activeSavedAddress->barangay_name ?: 'Not set' }}</span>
                                            <span class="sa-readonly-badge">Active</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sa-label">City / Municipality</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $activeSavedAddress->municipality_name ?: 'Not set' }}</span>
                                            <span class="sa-readonly-badge">Active</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sa-label">Province / Area</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $activeSavedAddress->province_name ?: 'Not set' }}</span>
                                            <span class="sa-readonly-badge">Active</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="sa-field--full">
                                        <label for="buyerStreetAddress" class="sa-label">Street / Unit Address <span class="sa-required">*</span></label>
                                        <textarea
                                            id="buyerStreetAddress"
                                            name="street_address"
                                            rows="2"
                                            maxlength="500"
                                            required
                                            class="sa-control"
                                            placeholder="House, unit, building, subdivision, or street"
                                        >{{ old('street_address', $buyerAccount->street_address) }}</textarea>
                                    </div>

                                    <div>
                                        <label class="sa-label">Barangay</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $buyerAccount->barangay_name ?: 'Not set' }}</span>
                                            
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sa-label">City / Municipality</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $buyerAccount->municipality_name ?: 'Not set' }}</span>
                                            
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sa-label">Province / Area</label>
                                        <div class="sa-readonly-box">
                                            <span class="sa-readonly-value">{{ $buyerAccount->province_name ?: 'Not set' }}</span>
                                            
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($usingSavedAddress)
                                <div class="sa-address-inline-note">
                                    <span>This saved address is active for checkout right now.</span>
                                    <button type="submit" form="useRegistrationAddressForm" class="sa-address-inline-link">Use profile address instead</button>
                                </div>
                            @endif
                        </div>
                    </section>
                </form>

                <form
                    id="useRegistrationAddressForm"
                    method="POST"
                    action="{{ route('buyer.account.addresses.use-registration') }}"
                    hidden
                >
                    @csrf
                </form>

                <section id="buyerDeliveryAddressBook" class="sa-address-book sa-address-book--enterprise">
                    <header class="sa-address-enterprise-head">
                        <div class="sa-address-enterprise-heading">
                            <h2 class="sa-address-enterprise-title">Address</h2>
                        </div>

                        @if ($addresses->count() < 5)
                            <button type="button" class="sa-address-enterprise-add" data-open-address-modal>
                                <span>Add Address</span>
                                <span class="sa-address-enterprise-add-plus" aria-hidden="true">+</span>
                            </button>
                        @else
                            <button type="button" class="sa-address-enterprise-add" disabled aria-disabled="true">
                                <span>Address Limit Reached</span>
                            </button>
                        @endif
                    </header>

                    <div class="sa-address-enterprise-divider" aria-hidden="true"></div>

                    @if ($addresses->isEmpty())
                        <div class="sa-address-empty sa-address-empty--enterprise">
                            <div>
                                <h3>No saved delivery addresses</h3>
                                <p>Add another address for Home, Office, or any preferred delivery location.</p>
                            </div>
                        </div>
                    @else
                        <div class="sa-address-enterprise-grid">
                            @foreach ($addresses as $address)
                                <article class="sa-address-enterprise-item {{ $address->is_default ? 'is-default' : '' }}">
                                    <div class="sa-address-enterprise-item-top">
                                        <div class="sa-address-enterprise-item-heading">
                                            <strong>{{ $address->label }}</strong>
                                            @if ($address->is_default)
                                                <span class="sa-address-enterprise-badge">Active</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="sa-address-enterprise-lines">
                                        <p>{{ $address->recipient_name }}</p>
                                        <p>{{ $address->street_address }}</p>
                                        <p>{{ $address->barangay_name }}, {{ $address->municipality_name }}, {{ $address->province_name }}@if($address->postal_code), {{ $address->postal_code }}@endif, Philippines</p>
                                        <p>{{ $address->contact_no }}</p>
                                    </div>

                                    <div class="sa-address-enterprise-actions">
                                        @if ($address->is_default)
                                            <span class="sa-address-enterprise-active-text" title="Active address" aria-label="Active address">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M20 6 9 17l-5-5"></path>
                                                </svg>
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('buyer.account.addresses.default', $address) }}" class="sa-address-enterprise-action-form">
                                                @csrf
                                                <button type="submit" class="sa-address-enterprise-activate" title="Use this address" aria-label="Use this address">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M20 6 9 17l-5-5"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <button
                                            type="button"
                                            class="sa-address-enterprise-edit"
                                            title="Edit address"
                                            aria-label="Edit address"
                                            data-edit-address
                                            data-url="{{ route('buyer.account.addresses.update', $address) }}"
                                            data-label="{{ $address->label }}"
                                            data-recipient="{{ $address->recipient_name }}"
                                            data-contact="{{ $address->contact_no }}"
                                            data-street="{{ $address->street_address }}"
                                            data-barangay="{{ $address->barangay_name }}"
                                            data-city="{{ $address->municipality_name }}"
                                            data-province="{{ $address->province_name }}"
                                            data-postal="{{ $address->postal_code }}"
                                            data-default="{{ $address->is_default ? '1' : '0' }}"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                            </svg>
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('buyer.account.addresses.destroy', $address) }}"
                                            onsubmit="return confirm('Remove this saved delivery address?');"
                                            class="sa-address-enterprise-delete-form"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sa-address-enterprise-delete" title="Delete address" aria-label="Delete address">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M8 6V4h8v2"></path>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <footer class="sa-form-footer sa-form-footer--final">
                    <p class="sa-save-note">Profile changes are saved securely to your Buyer account.</p>

                    <button type="submit" form="buyerAccountForm" class="sa-save-button">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 4h11l3 3v13H5z"></path>
                            <path d="M8 4v6h8V4"></path>
                            <path d="M8 20v-6h8v6"></path>
                        </svg>
                        Save Account Changes
                    </button>
                </footer>
            </section>
        </div>

        <div id="buyerSupportThreadModal" class="sa-support-thread-modal" hidden aria-hidden="true" data-open-template="{{ url('/messaging/api/reports/__COMPLAINT__/conversation') }}" data-show-template="{{ url('/messaging/api/conversations/__CONVERSATION__') }}" data-send-template="{{ url('/messaging/api/conversations/__CONVERSATION__/messages') }}">
            <button type="button" class="sa-support-thread-backdrop" data-close-support-thread aria-label="Close support conversation"></button>
            <section class="sa-support-thread-panel" role="dialog" aria-modal="true" aria-labelledby="buyerSupportThreadTitle">
                <header class="sa-support-thread-head"><div class="min-w-0"><p>Admin Support</p><h2 id="buyerSupportThreadTitle">Report conversation</h2></div><button type="button" class="sa-support-thread-close" data-close-support-thread aria-label="Close"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg></button></header>
                <div id="buyerSupportThreadMessages" class="sa-support-thread-messages"><p class="sa-support-thread-loading">Loading conversation...</p></div>
                <form id="buyerSupportThreadReply" class="sa-support-thread-reply"><textarea id="buyerSupportThreadBody" maxlength="3000" rows="2" placeholder="Reply to SARI Admin..." required></textarea><button type="submit">Send</button></form>
            </section>
        </div>

        <div id="buyerAddressModal" class="sa-address-modal" hidden aria-hidden="true">
            <button type="button" class="sa-address-modal-backdrop" data-close-address-modal aria-label="Close address dialog"></button>

            <section
                class="sa-address-modal-panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="buyerAddressModalTitle"
            >
                <header class="sa-address-modal-head">
                    <div>
                        <p class="sa-address-modal-eyebrow">Delivery address</p>
                        <h2 id="buyerAddressModalTitle">Add Address</h2>
                    </div>

                    <button type="button" class="sa-address-modal-close" data-close-address-modal aria-label="Close">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m7 7 10 10"></path>
                            <path d="m17 7-10 10"></path>
                        </svg>
                    </button>
                </header>

                <form
                    id="buyerAddressForm"
                    method="POST"
                    action="{{ route('buyer.account.addresses.store') }}"
                    data-store-url="{{ route('buyer.account.addresses.store') }}"
                >
                    @csrf
                    <input id="buyerAddressMethod" type="hidden" name="_method" value="PATCH" disabled>

                    <div class="sa-address-modal-body">
                        @if ($errors->address->any())
                            <div class="sa-address-modal-error">{{ $errors->address->first() }}</div>
                        @endif
                        <div class="sa-address-form-grid">
                            <div>
                                <label for="buyerAddressLabel" class="sa-label">Label <span class="sa-required">*</span></label>
                                <input
                                    id="buyerAddressLabel"
                                    name="label"
                                    maxlength="50"
                                    required
                                    class="sa-control"
                                    data-alpha-only
                                    placeholder="e.g. Home, Office, Apartment, Parents"
                                >
                                <p class="sa-help">You can enter your own address label.</p>
                            </div>

                            <div>
                                <label for="buyerAddressRecipient" class="sa-label">Recipient <span class="sa-required">*</span></label>
                                <input
                                    id="buyerAddressRecipient"
                                    name="recipient_name"
                                    maxlength="150"
                                    required
                                    class="sa-control"
                                    data-alpha-only
                                    value="{{ $buyerFullName }}"
                                >
                            </div>

                            <div>
                                <label for="buyerAddressContact" class="sa-label">Contact Number <span class="sa-required">*</span></label>
                                <input
                                    id="buyerAddressContact"
                                    name="contact_no"
                                    inputmode="tel"
                                    minlength="11"
                                    maxlength="13"
                                    pattern="(?:09[0-9]{9}|\+639[0-9]{9})"
                                    required
                                    class="sa-control"
                                    value="{{ $buyerAccount->contact_no }}"
                                    placeholder="09XXXXXXXXX or +639XXXXXXXXX"
                                >
                            </div>

                            <div>
                                <label for="buyerAddressPostal" class="sa-label">Postal Code</label>
                                <input
                                    id="buyerAddressPostal"
                                    name="postal_code"
                                    inputmode="numeric"
                                    maxlength="4"
                                    pattern="[0-9]{4}"
                                    class="sa-control"
                                    placeholder="0000"
                                >
                            </div>

                            <div class="sa-field--full">
                                <label for="buyerAddressStreet" class="sa-label">Street / Unit Address <span class="sa-required">*</span></label>
                                <textarea
                                    id="buyerAddressStreet"
                                    name="street_address"
                                    rows="2"
                                    maxlength="500"
                                    required
                                    class="sa-control"
                                    placeholder="House, unit, building, subdivision, or street"
                                ></textarea>
                            </div>

                            <div>
                                <label class="sa-label">Province / Area <span class="sa-required">*</span></label>
                                <div class="sa-smart-select" data-smart-select>
                                    <input id="buyerAddressProvince" type="hidden" name="province_name">
                                    <button type="button" class="sa-smart-select-trigger" data-smart-trigger aria-expanded="false">
                                        <span data-smart-label>Select province / area</span>
                                        <span class="sa-smart-select-chevron" aria-hidden="true"></span>
                                    </button>
                                    <div class="sa-smart-select-menu" data-smart-menu hidden>
                                        <div class="sa-smart-select-search-row">
                                            <input type="search" class="sa-smart-select-search" data-smart-search placeholder="Search province / area" autocomplete="off">
                                            <button type="button" class="sa-smart-select-search-button" data-smart-search-button>Search</button>
                                        </div>
                                        <div class="sa-smart-select-options" data-smart-options></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="sa-label">City / Municipality <span class="sa-required">*</span></label>
                                <div class="sa-smart-select" data-smart-select>
                                    <input id="buyerAddressCity" type="hidden" name="municipality_name">
                                    <button type="button" class="sa-smart-select-trigger" data-smart-trigger aria-expanded="false" disabled>
                                        <span data-smart-label>Select city / municipality</span>
                                        <span class="sa-smart-select-chevron" aria-hidden="true"></span>
                                    </button>
                                    <div class="sa-smart-select-menu" data-smart-menu hidden>
                                        <div class="sa-smart-select-search-row">
                                            <input type="search" class="sa-smart-select-search" data-smart-search placeholder="Search city / municipality" autocomplete="off">
                                            <button type="button" class="sa-smart-select-search-button" data-smart-search-button>Search</button>
                                        </div>
                                        <div class="sa-smart-select-options" data-smart-options></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="sa-label">Barangay <span class="sa-required">*</span></label>
                                <div class="sa-smart-select" data-smart-select>
                                    <input id="buyerAddressBarangay" type="hidden" name="barangay_name">
                                    <button type="button" class="sa-smart-select-trigger" data-smart-trigger aria-expanded="false" disabled>
                                        <span data-smart-label>Select barangay</span>
                                        <span class="sa-smart-select-chevron" aria-hidden="true"></span>
                                    </button>
                                    <div class="sa-smart-select-menu" data-smart-menu hidden>
                                        <div class="sa-smart-select-search-row">
                                            <input type="search" class="sa-smart-select-search" data-smart-search placeholder="Search barangay" autocomplete="off">
                                            <button type="button" class="sa-smart-select-search-button" data-smart-search-button>Search</button>
                                        </div>
                                        <div class="sa-smart-select-options" data-smart-options></div>
                                    </div>
                                </div>
                                <p class="sa-help">Address options load automatically from the Philippine address directory.</p>
                            </div>
                        </div>

                        <label class="sa-address-default-check">
                            <input id="buyerAddressDefault" class="sa-address-default-input" type="checkbox" name="is_default" value="1">
                            <span>
                                <strong>Use this address after saving</strong>
                                <small>This address becomes the active checkout address.</small>
                            </span>
                        </label>
                    </div>

                    <footer class="sa-address-modal-footer">
                        <button type="button" class="sa-address-cancel" data-close-address-modal>Cancel</button>
                        <button type="submit" class="sa-save-button" id="buyerAddressSubmit">Save Address</button>
                    </footer>
                </form>
            </section>
        </div>
    @else
        <form method="POST" action="{{ route('buyer.account.update') }}" class="sa-card sa-form-card sa-social-shell">
            @csrf
            @method('PATCH')

            <header class="sa-form-card-head">
                <div class="sa-form-card-heading">
                    <h2 class="sa-form-card-title">Social Buyer Profile</h2>
                    <p class="sa-form-card-copy">Update the display name linked to your social sign-in.</p>
                </div>
                <span class="sa-form-card-line" aria-hidden="true"></span>
            </header>

            <div class="sa-form-card-body">
                <label for="socialBuyerName" class="sa-label">Display Name</label>
                <input
                    id="socialBuyerName"
                    name="name"
                    value="{{ old('name', $socialAccount?->name) }}"
                    required
                    class="sa-control"
                >
                <p class="sa-help">Signed in with {{ ucfirst($socialAccount?->provider ?? 'social login') }}.</p>
            </div>

            <footer class="sa-form-footer">
                <span></span>
                <button class="sa-save-button">Save Display Name</button>
            </footer>
        </form>
    @endif

</main>
@endsection

@push('scripts')
    @vite('resources/js/buyer-account.js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function sanitizePhilippineMobile(value) {
                const raw = String(value || '');
                const wantsPlus = raw.trim().startsWith('+');
                const digits = raw.replace(/\D/g, '');

                if (wantsPlus) {
                    return '+' + digits.slice(0, 12);
                }

                return digits.slice(0, 11);
            }

            const profileForm = document.getElementById('buyerAccountForm');

            if (profileForm) {
                const birthday = document.getElementById('buyerBirthday');
                const age = document.getElementById('buyerAge');
                const firstName = document.getElementById('buyerFirstName');
                const lastName = document.getElementById('buyerLastName');
                const middleInitial = document.getElementById('buyerMiddleInitial');
                const contact = document.getElementById('buyerContact');
                const profileInput = document.getElementById('buyerProfileImage');
                const profilePreview = document.getElementById('buyerProfilePreview');
                const profileFallback = document.getElementById('buyerProfileFallback');
                const profileFileStatus = document.getElementById('buyerProfileFileStatus');
                const profileNamePreview = document.getElementById('buyerProfileNamePreview');
                const profileContactPreview = document.getElementById('buyerProfileContactPreview');
                const nameFields = document.querySelectorAll('[data-name-only]');
                let objectUrl = null;

                function syncAge() {
                    if (!birthday || !age) return;

                    if (!birthday.value) {
                        age.textContent = '—';
                        return;
                    }

                    const birthDate = new Date(birthday.value + 'T00:00:00');
                    const today = new Date();
                    let years = today.getFullYear() - birthDate.getFullYear();
                    const monthDelta = today.getMonth() - birthDate.getMonth();

                    if (
                        monthDelta < 0
                        || (monthDelta === 0 && today.getDate() < birthDate.getDate())
                    ) {
                        years -= 1;
                    }

                    age.textContent = Number.isFinite(years) && years >= 0
                        ? String(years)
                        : '—';
                }

                function syncProfileText() {
                    if (profileNamePreview) {
                        const fullName = [firstName?.value, lastName?.value]
                            .map(value => (value || '').trim())
                            .filter(Boolean)
                            .join(' ');

                        profileNamePreview.textContent = fullName || 'SARI Buyer';
                    }

                    if (profileContactPreview) {
                        profileContactPreview.textContent = contact?.value?.trim() || 'Not set';
                    }
                }

                function clearObjectUrl() {
                    if (!objectUrl) return;
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                birthday?.addEventListener('input', syncAge);
                birthday?.addEventListener('change', syncAge);
                syncAge();

                nameFields.forEach((field) => {
                    field.addEventListener('input', function () {
                        this.value = this.value
                            .replace(/[^\p{L}\s.'-]/gu, '')
                            .replace(/\s{2,}/g, ' ');

                        syncProfileText();
                    });
                });

                middleInitial?.addEventListener('input', function () {
                    this.value = this.value
                        .replace(/[^\p{L}]/gu, '')
                        .slice(0, 2)
                        .toUpperCase();
                });

                contact?.addEventListener('input', function () {
                    this.value = sanitizePhilippineMobile(this.value);
                    syncProfileText();
                });

                syncProfileText();

                profileInput?.addEventListener('change', function () {
                    const file = this.files?.[0] || null;
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    const maxBytes = 5 * 1024 * 1024;

                    if (!file) return;

                    if (!allowedTypes.includes(file.type)) {
                        this.value = '';
                        if (profileFileStatus) {
                            profileFileStatus.textContent = 'Use JPG, PNG, or WEBP only.';
                        }
                        window.alert('Use a JPG, PNG, or WEBP profile image.');
                        return;
                    }

                    if (file.size > maxBytes) {
                        this.value = '';
                        if (profileFileStatus) {
                            profileFileStatus.textContent = 'Image must not be larger than 5 MB.';
                        }
                        window.alert('Profile image must not be larger than 5 MB.');
                        return;
                    }

                    clearObjectUrl();
                    objectUrl = URL.createObjectURL(file);

                    if (profilePreview) {
                        profilePreview.src = objectUrl;
                        profilePreview.hidden = false;
                    }

                    if (profileFallback) {
                        profileFallback.hidden = true;
                    }

                    if (profileFileStatus) {
                        profileFileStatus.textContent = 'New profile image selected.';
                    }
                });

                window.addEventListener('beforeunload', clearObjectUrl, { once: true });
            }

            const supportThreadModal = document.getElementById('buyerSupportThreadModal');
            const supportThreadMessages = document.getElementById('buyerSupportThreadMessages');
            const supportThreadTitle = document.getElementById('buyerSupportThreadTitle');
            const supportThreadReply = document.getElementById('buyerSupportThreadReply');
            const supportThreadBody = document.getElementById('buyerSupportThreadBody');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
            const supportUrl = (template, token, value) => (template || '').replace(token, encodeURIComponent(String(value)));
            function showSupportThread(){if(!supportThreadModal)return;supportThreadModal.hidden=false;supportThreadModal.setAttribute('aria-hidden','false');document.body.classList.add('sa-support-thread-open');}
            function closeSupportThread(){if(!supportThreadModal)return;supportThreadModal.hidden=true;supportThreadModal.setAttribute('aria-hidden','true');document.body.classList.remove('sa-support-thread-open');}
            async function supportFetch(url,options={}){const response=await fetch(url,{credentials:'same-origin',...options,headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest',...(options.method&&options.method!=='GET'?{'X-CSRF-TOKEN':csrfToken}:{}),...(options.headers||{})}});const payload=await response.json().catch(()=>({}));if(!response.ok){const firstError=payload?.errors?Object.values(payload.errors).flat()[0]:null;throw new Error(firstError||payload?.message||'Unable to load this support conversation.');}return payload;}
            function renderSupportMessages(messages=[]){if(!supportThreadMessages)return;supportThreadMessages.innerHTML='';if(!messages.length){const empty=document.createElement('p');empty.className='sa-support-thread-empty';empty.textContent='No messages yet.';supportThreadMessages.appendChild(empty);return;}messages.forEach(message=>{const mine=['buyer','social_buyer'].includes(String(message.sender_role||'').toLowerCase());const row=document.createElement('div');row.className=`sa-support-message ${mine?'is-mine':'is-admin'}`;const bubble=document.createElement('div');bubble.className='sa-support-message-bubble';bubble.textContent=message.body||'';const meta=document.createElement('span');meta.className='sa-support-message-meta';const sender=mine?'You':(message.sender||'SARI Admin');const created=message.created_at?new Date(message.created_at).toLocaleString():'';meta.textContent=created?`${sender} · ${created}`:sender;bubble.appendChild(meta);row.appendChild(bubble);supportThreadMessages.appendChild(row);});supportThreadMessages.scrollTop=supportThreadMessages.scrollHeight;}
            async function loadSupportConversation(uuid){if(!supportThreadModal||!uuid)return;const payload=await supportFetch(supportUrl(supportThreadModal.dataset.showTemplate,'__CONVERSATION__',uuid),{method:'GET'});if(supportThreadTitle)supportThreadTitle.textContent=payload?.conversation?.subject||'Report conversation';if(supportThreadReply)supportThreadReply.dataset.conversation=uuid;renderSupportMessages(payload?.messages||[]);}
            async function openSupportThread(button){if(!supportThreadModal||!button)return;showSupportThread();if(supportThreadMessages)supportThreadMessages.innerHTML='<p class="sa-support-thread-loading">Loading conversation...</p>';try{let uuid=String(button.dataset.conversation||'').trim();if(!uuid){const opened=await supportFetch(supportUrl(supportThreadModal.dataset.openTemplate,'__COMPLAINT__',button.dataset.complaintId),{method:'POST'});uuid=String(opened?.conversation?.uuid||'').trim();button.dataset.conversation=uuid;}if(!uuid)throw new Error('The support conversation could not be opened.');await loadSupportConversation(uuid);}catch(error){if(supportThreadMessages){supportThreadMessages.innerHTML='';const message=document.createElement('p');message.className='sa-support-thread-error';message.textContent=error?.message||'Unable to open this support conversation.';supportThreadMessages.appendChild(message);}}}
            document.querySelectorAll('[data-open-report-thread]').forEach(button=>button.addEventListener('click',()=>openSupportThread(button)));
            document.querySelectorAll('[data-close-support-thread]').forEach(button=>button.addEventListener('click',closeSupportThread));
            supportThreadReply?.addEventListener('submit',async function(event){event.preventDefault();const uuid=String(this.dataset.conversation||'').trim();const body=String(supportThreadBody?.value||'').trim();if(!uuid||!body)return;const submitButton=this.querySelector('button[type="submit"]');if(submitButton){submitButton.disabled=true;submitButton.textContent='Sending...';}try{const formData=new FormData();formData.append('body',body);await supportFetch(supportUrl(supportThreadModal?.dataset.sendTemplate,'__CONVERSATION__',uuid),{method:'POST',body:formData});if(supportThreadBody)supportThreadBody.value='';await loadSupportConversation(uuid);}catch(error){window.alert(error?.message||'Your reply could not be sent.');}finally{if(submitButton){submitButton.disabled=false;submitButton.textContent='Send';}}});

            const modal = document.getElementById('buyerAddressModal');
            const addressForm = document.getElementById('buyerAddressForm');

            if (!modal || !addressForm) return;

            const modalTitle = document.getElementById('buyerAddressModalTitle');
            const methodInput = document.getElementById('buyerAddressMethod');
            const submitButton = document.getElementById('buyerAddressSubmit');
            const labelInput = document.getElementById('buyerAddressLabel');
            const recipientInput = document.getElementById('buyerAddressRecipient');
            const addressContact = document.getElementById('buyerAddressContact');
            const streetInput = document.getElementById('buyerAddressStreet');
            const barangayInput = document.getElementById('buyerAddressBarangay');
            const cityInput = document.getElementById('buyerAddressCity');
            const provinceInput = document.getElementById('buyerAddressProvince');
            const postalInput = document.getElementById('buyerAddressPostal');
            const defaultInput = document.getElementById('buyerAddressDefault');
            const storeUrl = addressForm.dataset.storeUrl;
            const addressBook = document.getElementById('buyerDeliveryAddressBook');

            const provincesUrl = @json(route('address.provinces'));
            const municipalitiesUrlTemplate = @json(url('/address/provinces/__PROVINCE__/municipalities'));
            const barangaysUrlTemplate = @json(url('/address/municipalities/__CITY__/barangays'));

            function resolveItemName(item = {}) {
                return String(item.name || item.description || item.province_name || item.municipality_name || item.city_name || item.barangay_name || '').trim();
            }

            function resolveItemCode(item = {}) {
                return String(item.code || item.province_code || item.municipality_code || item.city_code || item.barangay_code || resolveItemName(item)).trim();
            }

            async function fetchAddressList(url) {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Address options could not be loaded.');
                }

                const payload = await response.json().catch(() => []);
                return Array.isArray(payload) ? payload : [];
            }

            function smartSelectParts(input) {
                const wrapper = input?.closest?.('[data-smart-select]');
                return {
                    wrapper,
                    trigger: wrapper?.querySelector('[data-smart-trigger]') || null,
                    label: wrapper?.querySelector('[data-smart-label]') || null,
                    menu: wrapper?.querySelector('[data-smart-menu]') || null,
                    search: wrapper?.querySelector('[data-smart-search]') || null,
                    searchButton: wrapper?.querySelector('[data-smart-search-button]') || null,
                    options: wrapper?.querySelector('[data-smart-options]') || null,
                };
            }

            function closeSmartSelect(input) {
                const parts = smartSelectParts(input);
                if (!parts.wrapper) return;
                parts.wrapper.classList.remove('is-open');
                if (parts.menu) parts.menu.hidden = true;
                parts.trigger?.setAttribute('aria-expanded', 'false');
            }

            function openSmartSelect(input) {
                const parts = smartSelectParts(input);
                if (!parts.wrapper || parts.trigger?.disabled) return;

                [provinceInput, cityInput, barangayInput].forEach((other) => {
                    if (other && other !== input) closeSmartSelect(other);
                });

                parts.wrapper.classList.add('is-open');
                if (parts.menu) parts.menu.hidden = false;
                parts.trigger?.setAttribute('aria-expanded', 'true');
                if (parts.search) {
                    parts.search.value = '';
                    filterSmartSelectOptions(input, '');
                    window.requestAnimationFrame(() => parts.search?.focus());
                }
            }

            function filterSmartSelectOptions(input, query = '') {
                const parts = smartSelectParts(input);
                if (!parts.options) return;
                const normalized = String(query || '').trim().toLowerCase();
                let visible = 0;

                parts.options.querySelectorAll('[data-smart-option]').forEach((button) => {
                    const matches = !normalized || String(button.dataset.name || '').toLowerCase().includes(normalized);
                    button.hidden = !matches;
                    if (matches) visible += 1;
                });

                let empty = parts.options.querySelector('.sa-smart-select-empty');
                if (!visible) {
                    if (!empty) {
                        empty = document.createElement('div');
                        empty.className = 'sa-smart-select-empty';
                        empty.textContent = 'No matching location found.';
                        parts.options.appendChild(empty);
                    }
                    empty.hidden = false;
                } else if (empty) {
                    empty.hidden = true;
                }
            }

            function chooseSmartSelectOption(input, name, code, dispatch = true) {
                const parts = smartSelectParts(input);
                input.value = name || '';
                input.dataset.code = code || '';
                if (parts.label) parts.label.textContent = name || input.dataset.placeholder || 'Select option';

                parts.options?.querySelectorAll('[data-smart-option]').forEach((button) => {
                    button.classList.toggle('is-selected', button.dataset.name === input.value);
                });

                closeSmartSelect(input);
                if (dispatch) input.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function renderAddressOptions(input, items, placeholder, selectedName = '') {
                if (!input) return;
                const parts = smartSelectParts(input);
                if (!parts.wrapper || !parts.options) return;

                input.dataset.placeholder = placeholder;
                parts.options.innerHTML = '';
                const selectedNormalized = String(selectedName || '').trim().toLowerCase();
                let selectedItem = null;

                items.forEach((item) => {
                    const name = resolveItemName(item);
                    const code = resolveItemCode(item);
                    if (!name) return;

                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'sa-smart-select-option';
                    option.dataset.smartOption = '1';
                    option.dataset.name = name;
                    option.dataset.code = code;
                    option.textContent = name;

                    if (selectedNormalized && name.trim().toLowerCase() === selectedNormalized) {
                        selectedItem = { name, code };
                        option.classList.add('is-selected');
                    }

                    option.addEventListener('click', () => chooseSmartSelectOption(input, name, code, true));
                    parts.options.appendChild(option);
                });

                const enabled = items.length > 0;
                if (parts.trigger) parts.trigger.disabled = !enabled;

                if (selectedItem) {
                    input.value = selectedItem.name;
                    input.dataset.code = selectedItem.code;
                    if (parts.label) parts.label.textContent = selectedItem.name;
                } else {
                    input.value = '';
                    input.dataset.code = '';
                    if (parts.label) parts.label.textContent = placeholder;
                }

                if (parts.search) parts.search.value = '';
                closeSmartSelect(input);
            }

            function selectedCode(input) {
                return input?.dataset?.code || '';
            }

            function bindSmartSelect(input) {
                if (!input) return;
                const parts = smartSelectParts(input);
                if (!parts.wrapper || parts.wrapper.dataset.bound === '1') return;
                parts.wrapper.dataset.bound = '1';

                parts.trigger?.addEventListener('click', () => {
                    if (parts.wrapper.classList.contains('is-open')) {
                        closeSmartSelect(input);
                    } else {
                        openSmartSelect(input);
                    }
                });

                parts.search?.addEventListener('input', () => filterSmartSelectOptions(input, parts.search.value));
                parts.searchButton?.addEventListener('click', () => filterSmartSelectOptions(input, parts.search?.value || ''));

                parts.search?.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        filterSmartSelectOptions(input, parts.search.value);
                        const firstVisible = [...(parts.options?.querySelectorAll('[data-smart-option]') || [])]
                            .find((button) => !button.hidden);
                        firstVisible?.focus();
                    }
                });
            }

            [provinceInput, cityInput, barangayInput].forEach(bindSmartSelect);

            document.addEventListener('click', (event) => {
                [provinceInput, cityInput, barangayInput].forEach((input) => {
                    const wrapper = smartSelectParts(input).wrapper;
                    if (wrapper && !wrapper.contains(event.target)) closeSmartSelect(input);
                });
            });

            async function loadProvinceOptions(selectedName = '') {
                if (!provinceInput) return;
                smartSelectParts(provinceInput).trigger.disabled = true;
                renderAddressOptions(provinceInput, [], 'Loading provinces...', selectedName);
                const items = await fetchAddressList(provincesUrl);
                renderAddressOptions(provinceInput, items, 'Select province / area', selectedName);
            }

            async function loadCityOptions(provinceCode, selectedName = '') {
                if (!cityInput) return;
                if (!provinceCode) {
                    renderAddressOptions(cityInput, [], 'Select city / municipality', selectedName);
                    smartSelectParts(cityInput).trigger.disabled = true;
                    renderAddressOptions(barangayInput, [], 'Select barangay', '');
                    if (barangayInput) smartSelectParts(barangayInput).trigger.disabled = true;
                    return;
                }

                smartSelectParts(cityInput).trigger.disabled = true;
                renderAddressOptions(cityInput, [], 'Loading cities / municipalities...', selectedName);
                const items = await fetchAddressList(municipalitiesUrlTemplate.replace('__PROVINCE__', encodeURIComponent(provinceCode)));
                renderAddressOptions(cityInput, items, 'Select city / municipality', selectedName);
            }

            async function loadBarangayOptions(cityCode, selectedName = '') {
                if (!barangayInput) return;
                if (!cityCode) {
                    renderAddressOptions(barangayInput, [], 'Select barangay', selectedName);
                    smartSelectParts(barangayInput).trigger.disabled = true;
                    return;
                }

                smartSelectParts(barangayInput).trigger.disabled = true;
                renderAddressOptions(barangayInput, [], 'Loading barangays...', selectedName);
                const items = await fetchAddressList(barangaysUrlTemplate.replace('__CITY__', encodeURIComponent(cityCode)));
                renderAddressOptions(barangayInput, items, 'Select barangay', selectedName);
            }

            async function populateAddressSelectors(values = {}) {
                try {
                    await loadProvinceOptions(values.province || '');
                    const provinceCode = selectedCode(provinceInput);
                    await loadCityOptions(provinceCode, values.city || '');
                    const cityCode = selectedCode(cityInput);
                    await loadBarangayOptions(cityCode, values.barangay || '');
                } catch (error) {
                    console.error(error);
                }
            }

            const registrationAddress = {
                recipient: @json($buyerFullName),
                contact: @json((string) $buyerAccount->contact_no),
                street: @json((string) $buyerAccount->street_address),
                barangay: @json((string) $buyerAccount->barangay_name),
                city: @json((string) $buyerAccount->municipality_name),
                province: @json((string) $buyerAccount->province_name),
            };

            const hasAddressErrors = @json($errors->address->any());
            const oldAddressValues = {
                label: @json(old('label', 'Home')),
                recipient: @json(old('recipient_name', $buyerFullName)),
                contact: @json(old('contact_no', (string) $buyerAccount->contact_no)),
                street: @json(old('street_address', (string) $buyerAccount->street_address)),
                barangay: @json(old('barangay_name', (string) $buyerAccount->barangay_name)),
                city: @json(old('municipality_name', (string) $buyerAccount->municipality_name)),
                province: @json(old('province_name', (string) $buyerAccount->province_name)),
                postal: @json(old('postal_code', '')),
                isDefault: @json((bool) old('is_default', $addresses->isEmpty())),
            };

            function fillAddressForm(values = {}) {
                if (labelInput) labelInput.value = values.label || 'Home';
                if (recipientInput) recipientInput.value = values.recipient || '';
                if (addressContact) addressContact.value = values.contact || '';
                if (streetInput) streetInput.value = values.street || '';
                if (postalInput) postalInput.value = values.postal || '';
                if (defaultInput) defaultInput.checked = Boolean(values.isDefault);
            }

            function showAddressModal() {
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('sa-address-modal-open');

                window.requestAnimationFrame(() => {
                    labelInput?.focus();
                });
            }

            function closeAddressModal() {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('sa-address-modal-open');
            }

            function openAddAddress() {
                addressForm.action = storeUrl;
                if (methodInput) { methodInput.value = 'PATCH'; methodInput.disabled = true; }
                if (modalTitle) modalTitle.textContent = 'Add Address';
                if (submitButton) submitButton.textContent = 'Save Address';
                if (defaultInput) defaultInput.disabled = false;

                const values = {
                    label: 'Home',
                    recipient: registrationAddress.recipient,
                    contact: registrationAddress.contact,
                    street: registrationAddress.street,
                    barangay: registrationAddress.barangay,
                    city: registrationAddress.city,
                    province: registrationAddress.province,
                    postal: '',
                    isDefault: !document.querySelector('.sa-address-enterprise-item.is-default'),
                };

                fillAddressForm(values);
                showAddressModal();

                // Load location choices after the modal is already visible so the
                // button always feels instant even when the address API is slow.
                populateAddressSelectors(values).catch((error) => {
                    console.error('Unable to load address options:', error);
                });
            }

            async function openEditAddress(button) {
                addressForm.action = button.dataset.url || storeUrl;
                if (methodInput) { methodInput.value = 'PATCH'; methodInput.disabled = false; }
                if (modalTitle) modalTitle.textContent = 'Edit Address';
                if (submitButton) submitButton.textContent = 'Save Changes';
                if (defaultInput) defaultInput.disabled = false;

                const values = {
                    label: button.dataset.label,
                    recipient: button.dataset.recipient,
                    contact: button.dataset.contact,
                    street: button.dataset.street,
                    barangay: button.dataset.barangay,
                    city: button.dataset.city,
                    province: button.dataset.province,
                    postal: button.dataset.postal,
                    isDefault: button.dataset.default === '1',
                };

                fillAddressForm(values);
                await populateAddressSelectors(values);
                showAddressModal();
            }

            provinceInput?.addEventListener('change', async function () {
                try {
                    await loadCityOptions(selectedCode(provinceInput), '');
                    renderAddressOptions(barangayInput, [], 'Select barangay', '');
                    if (barangayInput) smartSelectParts(barangayInput).trigger.disabled = true;
                } catch (error) {
                    console.error(error);
                }
            });

            cityInput?.addEventListener('change', async function () {
                try {
                    await loadBarangayOptions(selectedCode(cityInput), '');
                } catch (error) {
                    console.error(error);
                }
            });

            document.querySelectorAll('[data-open-address-modal]').forEach((button) => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    openAddAddress();
                });
            });

            document.querySelectorAll('[data-edit-address]').forEach((button) => {
                button.addEventListener('click', () => openEditAddress(button));
            });

            document.querySelectorAll('[data-close-address-modal]').forEach((button) => {
                button.addEventListener('click', closeAddressModal);
            });

            addressContact?.addEventListener('input', function () {
                this.value = sanitizePhilippineMobile(this.value);
            });

            postalInput?.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 4);
            });

            labelInput?.addEventListener('input', function () {
                this.value = this.value
                    .replace(/[^\p{L}\s.'-]/gu, '')
                    .replace(/\s{2,}/g, ' ')
                    .slice(0, 50);
            });

            recipientInput?.addEventListener('input', function () {
                this.value = this.value
                    .replace(/[^\p{L}\s.'-]/gu, '')
                    .replace(/\s{2,}/g, ' ');
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSimpleSexSelect();
                    if (!modal.hidden) closeAddressModal();
                    if (supportThreadModal && !supportThreadModal.hidden) closeSupportThread();
                }
            });

            document.addEventListener('click', function (event) {
                if (simpleSexSelect && !simpleSexSelect.contains(event.target)) {
                    closeSimpleSexSelect();
                }
            });


            const simpleSexSelect = document.querySelector('[data-simple-select]');
            const simpleSexInput = simpleSexSelect?.querySelector('input[name="sex"]');
            const simpleSexTrigger = simpleSexSelect?.querySelector('[data-simple-select-trigger]');
            const simpleSexLabel = simpleSexSelect?.querySelector('[data-simple-select-label]');
            const simpleSexMenu = simpleSexSelect?.querySelector('[data-simple-select-menu]');
            const simpleSexOptions = Array.from(simpleSexSelect?.querySelectorAll('.sa-simple-select-option') || []);

            function closeSimpleSexSelect() {
                if (!simpleSexSelect || !simpleSexTrigger || !simpleSexMenu) return;
                simpleSexSelect.classList.remove('is-open');
                simpleSexTrigger.setAttribute('aria-expanded', 'false');
                simpleSexMenu.hidden = true;
            }

            function openSimpleSexSelect() {
                if (!simpleSexSelect || !simpleSexTrigger || !simpleSexMenu) return;
                simpleSexSelect.classList.add('is-open');
                simpleSexTrigger.setAttribute('aria-expanded', 'true');
                simpleSexMenu.hidden = false;
            }

            function setSimpleSexValue(value, label) {
                if (simpleSexInput) simpleSexInput.value = value;
                if (simpleSexLabel) simpleSexLabel.textContent = label;
                simpleSexOptions.forEach((option) => {
                    option.classList.toggle('is-selected', option.dataset.value === value);
                });
            }

            simpleSexTrigger?.addEventListener('click', function () {
                if (simpleSexMenu?.hidden) openSimpleSexSelect();
                else closeSimpleSexSelect();
            });

            simpleSexOptions.forEach((option) => {
                option.addEventListener('click', function () {
                    setSimpleSexValue(option.dataset.value || '', option.textContent.trim());
                    closeSimpleSexSelect();
                });
            });

            if (hasAddressErrors) {
                addressForm.action = storeUrl;
                if (methodInput) { methodInput.value = 'PATCH'; methodInput.disabled = true; }
                if (modalTitle) modalTitle.textContent = 'Add Address';
                if (submitButton) submitButton.textContent = 'Save Address';
                if (defaultInput) defaultInput.disabled = false;
                fillAddressForm(oldAddressValues);
                populateAddressSelectors(oldAddressValues).then(showAddressModal);
            }
        });
    </script>
@endpush
