@extends('layouts.app')

@section('title', 'Register — SARI')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        --sari-yellow: #d48f08;
        --sari-yellow-dark: #bd7d05;
        --sari-text: #1f1b16;
        --sari-muted: #81786c;
        --sari-border: #e4ddd3;
    }

    @keyframes sariWizardEnter {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes sariWizardLeave {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-6px);
        }
    }

    .sari-wizard-step.is-entering {
        animation: sariWizardEnter .30s cubic-bezier(.22,1,.36,1) both;
    }

    .sari-wizard-step.is-leaving {
        animation: sariWizardLeave .15s ease both;
    }

    .sari-control {
        color: #1f1b16 !important;
        -webkit-text-fill-color: #1f1b16 !important;
        caret-color: #1f1b16 !important;
        background: #fff !important;
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .sari-control::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
        opacity: 1;
    }

    .sari-control:focus {
        border-color: var(--sari-yellow) !important;
        box-shadow: 0 0 0 4px rgba(212,143,8,.08);
        outline: none;
    }

    .sari-control:-webkit-autofill,
    .sari-control:-webkit-autofill:hover,
    .sari-control:-webkit-autofill:focus {
        -webkit-text-fill-color: #1f1b16 !important;
        box-shadow: 0 0 0 1000px #fff inset !important;
    }

    .sari-location-list {
        max-height: 210px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d9d1c7 transparent;
    }

    .sari-location-option {
        transition:
            background-color .16s ease,
            color .16s ease;
    }

    .sari-location-option:hover {
        background: #fff;
        color: #9a6817;
    }

    .sari-register-action {
        transition:
            background-color .18s ease,
            border-color .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .sari-register-primary {
        border: 1px solid var(--sari-yellow);
        background: var(--sari-yellow);
        color: #fff;
        box-shadow: 0 7px 18px rgba(212,143,8,.13);
    }

    .sari-register-primary:hover {
        border-color: var(--sari-yellow-dark);
        background: var(--sari-yellow-dark);
        box-shadow: 0 8px 20px rgba(189,125,5,.16);
    }

    .sari-register-secondary {
        border: 1px solid #dfd8ce;
        background: #fff;
        color: #625a50;
    }

    .sari-register-secondary:hover {
        border-color: #d6bf95;
        background: #fff;
        color: #9a6817;
    }

    .sari-register-card {
        animation: sariRegisterCardIn .38s ease-out both;
    }

    @keyframes sariRegisterCardIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    [hidden] {
        display: none !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-wizard-step,
        .sari-control,
        .sari-register-action,
        .sari-register-card {
            animation: none !important;
            transition: none !important;
            transform: none !important;
        }
    }

    /* ============================================================
       REGISTRATION V2 — READABLE, CLEAN, MODERN
       Keeps the existing SARI palette and wizard functionality.
    ============================================================ */
    .sari-register-shell {
        max-width: 1120px;
    }

    .sari-register-card {
        max-width: 920px !important;
        border-radius: 26px !important;
        box-shadow: 0 18px 46px rgba(31, 27, 22, .08) !important;
    }

    .sari-register-card [data-step] h2 {
        font-size: 22px !important;
        line-height: 1.25 !important;
    }

    .sari-register-card [data-step] > p,
    .sari-register-card [data-step] .sari-step-copy {
        font-size: 11px !important;
        line-height: 1.7 !important;
    }

    .sari-register-card label {
        font-size: 11px !important;
        line-height: 1.35 !important;
    }

    .sari-register-card .sari-control {
        min-height: 50px !important;
        font-size: 13px !important;
        border-radius: 13px !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

    .sari-register-card textarea.sari-control {
        min-height: 112px !important;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
    }

    .sari-register-card input[type="file"] {
        min-height: 50px;
        font-size: 11px !important;
        border-radius: 13px !important;
    }

    .sari-register-card [data-next],
    .sari-register-card [data-back],
    .sari-register-card button[type="submit"] {
        min-height: 44px !important;
        padding-left: 18px !important;
        padding-right: 18px !important;
        font-size: 10px !important;
        border-radius: 12px !important;
    }

    .sari-register-card [data-step] .text-\[7px\],
    .sari-register-card [data-step] .text-\[7\.5px\],
    .sari-register-card [data-step] .text-\[8px\] {
        font-size: 9.5px !important;
        line-height: 1.55 !important;
    }

    /* ============================================================
       ROLE SELECTOR — CLEAN VERTICAL LIST
       ============================================================ */
    .sari-role-stage {
        width: 100%;
        max-width: 780px;
        margin-inline: auto;
    }

    .sari-role-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 10px;
    }

    .sari-role-option {
        position: relative;
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 16px;
        width: 100%;
        min-height: 72px;
        border: 1px solid #e6e0d8;
        border-radius: 15px;
        background: #fff;
        padding: 10px 16px;
        text-align: left;
        box-shadow:
            0 1px 2px rgba(42, 31, 18, .025),
            0 8px 24px rgba(42, 31, 18, .025);
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    .sari-role-option:hover {
        transform: translateY(-1px);
        border-color: #d8d8d4;
        background: #fff;
        box-shadow:
            0 2px 4px rgba(42, 31, 18, .035),
            0 12px 28px rgba(42, 31, 18, .05);
    }

    .sari-role-option:focus-visible {
        outline: none;
        border-color: #d49a22;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .10);
    }

    .sari-role-option.is-selected {
        border-color: #d39a2b;
        background: #fff;
        box-shadow: 0 8px 24px rgba(31, 27, 22, .045);
    }

    .sari-role-check {
        display: grid;
        height: 26px;
        width: 26px;
        flex: 0 0 auto;
        place-items: center;
        border: 1.5px solid #c9c2b8;
        border-radius: 999px;
        background: #fff;
        color: transparent;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .sari-role-option.is-selected .sari-role-check {
        border-color: #d08e08;
        background: #d08e08;
        color: #fff;
        box-shadow: 0 5px 12px rgba(208, 142, 8, .20);
    }

    .sari-role-icon {
        display: grid;
        height: 46px;
        width: 46px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 13px;
        background: #f7f7f5;
        color: #b47a0d;
    }

    .sari-role-option.is-rider .sari-role-icon {
        background: #f5f7f6;
        color: #387b5b;
    }

    .sari-role-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .sari-role-name {
        display: block;
        font-size: 13.5px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.01em;
        color: #27231e;
    }

    .sari-role-description {
        display: block;
        margin-top: 4px;
        font-size: 10.5px;
        line-height: 1.45;
        color: #857d73;
    }

    .sari-role-arrow {
        display: grid;
        height: 28px;
        width: 28px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        color: #9a9186;
        transition: transform .18s ease, color .18s ease, background-color .18s ease;
    }

    .sari-role-option:hover .sari-role-arrow,
    .sari-role-option.is-selected .sari-role-arrow {
        transform: translateX(2px);
        color: #b87906;
        background: rgba(212, 143, 8, .07);
    }

    .sari-role-preview {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 14px;
        border: 1px solid #e7e7e3;
        border-radius: 15px;
        background: #fff;
        padding: 14px 16px;
        box-shadow: 0 8px 24px rgba(31, 27, 22, .035);
    }

    .sari-role-preview-icon {
        display: grid;
        height: 48px;
        width: 48px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 13px;
        background: #f7f7f5;
        color: #b97905;
    }

    .sari-role-preview-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .sari-role-preview-label {
        font-size: 8px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #8d8479;
    }

    .sari-role-preview-title {
        margin-top: 5px;
        font-size: 14px;
        line-height: 1.25;
        font-weight: 700;
        color: #29241e;
    }

    .sari-role-preview-text {
        margin-top: 3px;
        font-size: 10px;
        line-height: 1.5;
        color: #82766a;
    }

    .sari-role-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .sari-role-continue {
        min-width: 150px;
    }

    .sari-role-continue:disabled {
        cursor: not-allowed;
        opacity: .46;
        box-shadow: none;
    }

    .sari-progress-wrap {
        padding: 13px 24px 15px;
        border-bottom: 1px solid #eee8df;
        background: #fff;
    }

    .sari-progress-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .sari-progress-title {
        font-size: 9px;
        font-weight: 700;
        color: #4c453c;
    }

    .sari-progress-counter {
        font-size: 8px;
        font-weight: 700;
        color: #8f867a;
    }

    .sari-progress-segments {
        display: grid;
        gap: 6px;
        margin-top: 10px;
    }

    .sari-progress-segment {
        height: 4px;
        overflow: hidden;
        border-radius: 999px;
        background: #e8e3dc;
        transition: background-color .2s ease;
    }

    .sari-progress-segment.is-complete,
    .sari-progress-segment.is-current {
        background: #d48f08;
    }

    @media (max-width: 639px) {
        .sari-role-option {
            min-height: 68px;
            gap: 12px;
            padding: 10px 12px;
        }

        .sari-role-icon {
            height: 42px;
            width: 42px;
        }

        .sari-role-name {
            font-size: 12.5px;
        }

        .sari-role-description {
            font-size: 9.5px;
        }

        .sari-role-preview {
            align-items: flex-start;
        }

        .sari-role-continue {
            width: 100%;
        }
    }



    /* ============================================================
       VERIFICATION UPLOAD — CLEAN DRAG & DROP
       ============================================================ */
    .sari-document-step {
        font-family: 'Poppins', sans-serif;
    }

    .sari-document-step > h2 {
        font-size: 26px !important;
        line-height: 1.2 !important;
        letter-spacing: -.03em;
    }

    .sari-document-step .sari-document-label {
        margin-bottom: 10px;
        font-size: 14px !important;
        line-height: 1.4 !important;
        font-weight: 600;
        color: #3b342d;
    }

    .sari-upload-zone {
        position: relative;
        display: flex;
        min-height: 168px;
        width: 100%;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px dashed #d89a20;
        border-radius: 18px;
        background: #fff;
        padding: 26px 22px;
        text-align: center;
        outline: none;
        box-shadow: 0 8px 24px rgba(31, 27, 22, .025);
        transition:
            border-color .22s ease,
            background-color .22s ease,
            box-shadow .22s ease,
            transform .22s cubic-bezier(.22,1,.36,1);
    }

    .sari-upload-zone:hover,
    .sari-upload-zone:focus-visible {
        border-color: #c18108;
        background: #fffdf9;
        box-shadow: 0 12px 30px rgba(102, 72, 20, .07);
        transform: translateY(-1px);
    }

    .sari-upload-zone.is-dragging {
        border-color: #b87500;
        background: #fff9ed;
        box-shadow:
            0 0 0 5px rgba(212, 143, 8, .08),
            0 16px 34px rgba(102, 72, 20, .09);
        transform: translateY(-2px) scale(1.002);
    }

    .sari-upload-zone.has-file {
        border-style: solid;
        border-color: #6f9b7d;
        background: #fbfefc;
        box-shadow: 0 10px 28px rgba(62, 112, 78, .07);
    }

    .sari-upload-zone.has-error {
        border-style: solid;
        border-color: #c46f6f;
        background: #fffafa;
        box-shadow: 0 10px 28px rgba(150, 67, 67, .06);
    }

    .sari-upload-input {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        min-height: 1px !important;
        margin: -1px !important;
        padding: 0 !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .sari-upload-content {
        display: flex;
        width: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .sari-upload-icon {
        display: grid;
        height: 48px;
        width: 48px;
        place-items: center;
        border-radius: 14px;
        background: #fff7e8;
        color: #b97805;
        transition:
            background-color .22s ease,
            color .22s ease,
            transform .22s cubic-bezier(.22,1,.36,1);
    }

    .sari-upload-zone:hover .sari-upload-icon,
    .sari-upload-zone.is-dragging .sari-upload-icon {
        transform: translateY(-2px);
    }

    .sari-upload-zone.has-file .sari-upload-icon {
        background: #eef7f1;
        color: #4f8060;
    }

    .sari-upload-title {
        margin-top: 13px;
        font-size: 16px;
        line-height: 1.35;
        font-weight: 600;
        letter-spacing: -.015em;
        color: #27221d;
    }

    .sari-upload-browse {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        margin-top: 11px;
        border: 1px solid #d89a20;
        border-radius: 999px;
        background: #fff;
        padding: 8px 18px;
        font-size: 13px;
        line-height: 1;
        font-weight: 600;
        color: #a86f05;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease,
            transform .18s ease;
    }

    .sari-upload-zone:hover .sari-upload-browse {
        border-color: #c18108;
        background: #fff9ee;
        color: #8e5d04;
    }

    .sari-upload-meta {
        margin-top: 10px;
        font-size: 11px;
        line-height: 1.45;
        font-weight: 500;
        color: #8b8278;
    }

    .sari-upload-file {
        max-width: min(520px, 92%);
        margin-top: 11px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 12px;
        line-height: 1.4;
        font-weight: 600;
        color: #4f8060;
    }

    .sari-upload-error {
        margin-top: 9px;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 600;
        color: #a85656;
    }

    .sari-upload-zone.has-file .sari-upload-title {
        color: #31583d;
    }

    .sari-document-actions {
        margin-top: 26px !important;
    }

    .sari-document-actions .sari-register-action {
        min-height: 46px !important;
        font-size: 12px !important;
        padding-inline: 20px !important;
    }

    @media (max-width: 639px) {
        .sari-document-step > h2 {
            font-size: 23px !important;
        }

        .sari-document-step .sari-document-label {
            font-size: 13px !important;
        }

        .sari-upload-zone {
            min-height: 154px;
            border-radius: 16px;
            padding: 22px 16px;
        }

        .sari-upload-title {
            font-size: 15px;
        }

        .sari-upload-browse {
            font-size: 12px;
        }

        .sari-upload-meta,
        .sari-upload-file,
        .sari-upload-error {
            font-size: 11px;
        }
    }


    /* ============================================================
       REGISTRATION V3 — UNIFIED, RESPONSIVE, HIGH-READABILITY
       ============================================================ */
    html {
        font-family: 'Poppins', sans-serif;
    }

    .sari-register-shell {
        max-width: 1280px !important;
    }

    .sari-register-card {
        max-width: 1080px !important;
        border-radius: 28px !important;
        box-shadow: 0 22px 60px rgba(31, 27, 22, .10) !important;
    }

    .sari-register-card,
    .sari-register-card button,
    .sari-register-card input,
    .sari-register-card select,
    .sari-register-card textarea {
        font-family: 'Poppins', sans-serif !important;
    }

    .sari-register-card > div:first-child h1 {
        font-size: clamp(30px, 3.2vw, 40px) !important;
        line-height: 1.12 !important;
        letter-spacing: -.045em !important;
    }

    .sari-register-card > div:first-child > p:last-child {
        max-width: 620px !important;
        font-size: clamp(12px, 1.4vw, 14px) !important;
        line-height: 1.7 !important;
    }

    .sari-progress-wrap {
        padding: 16px 28px 18px !important;
    }

    .sari-progress-title {
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    .sari-progress-counter {
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .sari-progress-segments {
        gap: 8px !important;
        margin-top: 12px !important;
    }

    .sari-progress-segment {
        height: 5px !important;
    }

    .sari-register-card [data-step] h2 {
        font-size: clamp(25px, 2.5vw, 31px) !important;
        line-height: 1.18 !important;
        letter-spacing: -.035em !important;
    }

    .sari-register-card [data-step] > p,
    .sari-register-card [data-step] .sari-step-copy {
        margin-top: 5px;
        font-size: 13px !important;
        line-height: 1.65 !important;
        color: #7d7469;
    }

    .sari-details-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .sari-section-icon {
        display: grid;
        width: 50px;
        height: 50px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 15px;
        background: #fff7e8;
        color: #b97805;
        box-shadow: inset 0 0 0 1px rgba(212, 143, 8, .08);
    }

    .sari-section-icon svg {
        width: 23px;
        height: 23px;
    }

    .sari-form-section {
        border: 1px solid #ece6de;
        border-radius: 20px;
        background: #fff;
        padding: 20px;
        box-shadow: 0 8px 28px rgba(31, 27, 22, .035);
    }

    .sari-form-section-head {
        display: flex;
        align-items: center;
        gap: 11px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0ebe5;
    }

    .sari-form-section-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 12px;
        background: #faf6ef;
        color: #b97805;
    }

    .sari-form-section-icon svg {
        width: 18px;
        height: 18px;
    }

    .sari-form-section-head h3 {
        font-size: 17px;
        line-height: 1.3;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #2d2822;
    }

    .sari-form-section-head p {
        margin-top: 2px;
        font-size: 11.5px;
        line-height: 1.55;
        color: #8b8278;
    }

    .sari-role-specific-label {
        margin-bottom: 2px !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase;
        color: #b97805 !important;
    }

    .sari-field-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .sari-address-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .sari-field-label,
    .sari-register-card label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        color: #4a433b !important;
    }

    .sari-register-card .sari-control {
        min-height: 50px !important;
        border-radius: 13px !important;
        padding: 0 14px !important;
        font-size: 14px !important;
        line-height: 1.3 !important;
    }

    .sari-register-card textarea.sari-control {
        min-height: 88px !important;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
        line-height: 1.55 !important;
    }

    .sari-readonly-control {
        background: #f7f5f2 !important;
        color: #5c554d !important;
        -webkit-text-fill-color: #5c554d !important;
    }

    .sari-field-hint {
        margin-top: 7px;
        font-size: 11px !important;
        line-height: 1.5 !important;
        color: #938a80;
    }

    .sari-input-with-icon {
        position: relative;
    }

    .sari-input-with-icon > span {
        pointer-events: none;
        position: absolute;
        left: 14px;
        top: 50%;
        z-index: 2;
        display: grid;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        place-items: center;
        color: #9a9185;
    }

    .sari-input-with-icon > span svg {
        width: 17px;
        height: 17px;
    }

    .sari-input-with-icon .sari-control {
        padding-left: 42px !important;
    }

    .sari-address-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #eee8df;
        border-radius: 999px;
        background: #faf9f7;
        padding: 8px 11px;
        font-size: 11px;
        line-height: 1.3;
        font-weight: 500;
        color: #7d7469;
    }

    .sari-location-popover {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 7px);
        z-index: 40;
        border: 1px solid #e3ddd4;
        border-radius: 13px;
        background: #fff;
        padding: 6px;
        box-shadow: 0 18px 42px rgba(44,38,31,.13);
    }

    .sari-location-option {
        font-size: 12px !important;
        line-height: 1.4 !important;
        padding: 10px 11px !important;
    }

    .sari-manual-address {
        border: 1px solid #eadfc9;
        border-radius: 15px;
        background: #fffaf2;
        padding: 15px;
        font-size: 11.5px;
        line-height: 1.55;
        color: #806f54;
    }

    .sari-mini-notice-icon {
        display: grid;
        width: 24px;
        height: 24px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #f2c96d;
        color: #704a04;
        font-weight: 700;
    }

    .sari-step-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .sari-register-card [data-next],
    .sari-register-card [data-back],
    .sari-register-card button[type="submit"],
    .sari-role-continue,
    .sari-document-actions .sari-register-action {
        min-height: 48px !important;
        border-radius: 13px !important;
        padding-inline: 20px !important;
        font-size: 14px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
    }

    .sari-register-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
    }

    .sari-register-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .sari-role-stage {
        max-width: 820px !important;
    }

    .sari-role-option {
        min-height: 78px !important;
        padding: 12px 17px !important;
    }

    .sari-role-name {
        font-size: 15px !important;
    }

    .sari-role-description {
        font-size: 11.5px !important;
        line-height: 1.5 !important;
    }

    .sari-role-preview-label {
        font-size: 9px !important;
    }

    .sari-role-preview-title {
        font-size: 15px !important;
    }

    .sari-role-preview-text {
        font-size: 11.5px !important;
    }

    .sari-document-step .sari-document-label {
        font-size: 14px !important;
    }

    .sari-upload-title {
        font-size: 17px !important;
    }

    .sari-upload-browse {
        font-size: 13px !important;
    }

    .sari-upload-meta,
    .sari-upload-file,
    .sari-upload-error {
        font-size: 11.5px !important;
    }

    .sari-signin-shortcut {
        margin-top: 18px;
        border: 1px solid rgba(231, 225, 217, .95);
        border-radius: 999px;
        background: rgba(255, 255, 255, .88);
        padding: 10px 16px;
        text-align: center;
        font-size: clamp(13px, 1.6vw, 15px);
        line-height: 1.5;
        font-weight: 500;
        color: #5f574e;
        box-shadow: 0 8px 24px rgba(31, 27, 22, .05);
        backdrop-filter: blur(8px);
    }

    .sari-signin-shortcut a {
        margin-left: 4px;
        font-weight: 700;
        color: #a96f06;
        text-decoration-thickness: 1px;
        text-underline-offset: 4px;
    }

    .sari-signin-shortcut a:hover {
        color: #7f5104;
        text-decoration-line: underline;
    }

    @media (max-width: 899px) {
        .sari-field-grid,
        .sari-address-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sari-address-grid > .md\:col-span-3 {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 639px) {
        .sari-register-shell {
            padding-inline: 12px !important;
            padding-top: 22px !important;
            padding-bottom: 28px !important;
        }

        .sari-register-card {
            border-radius: 20px !important;
        }

        .sari-register-card > div:first-child {
            padding-inline: 18px !important;
        }

        .sari-progress-wrap {
            padding-inline: 18px !important;
        }

        .sari-progress-title {
            font-size: 11px !important;
        }

        .sari-progress-counter {
            font-size: 10px !important;
        }

        .sari-field-grid,
        .sari-address-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .sari-field-grid > .sm\:col-span-2,
        .sari-address-grid > .md\:col-span-3 {
            grid-column: auto;
        }

        .sari-form-section {
            border-radius: 16px;
            padding: 16px;
        }

        .sari-form-section-head {
            align-items: flex-start;
        }

        .sari-form-section-head h3 {
            font-size: 16px;
        }

        .sari-form-section-head p {
            font-size: 11px;
        }

        .sari-register-card .sari-control {
            min-height: 50px !important;
            font-size: 14px !important;
        }

        .sari-step-actions,
        .sari-document-actions {
            align-items: stretch !important;
            flex-direction: column-reverse;
        }

        .sari-step-actions .sari-register-action,
        .sari-document-actions .sari-register-action,
        .sari-register-card [data-step="credentials"] .sari-register-action {
            width: 100%;
        }

        .sari-role-option {
            min-height: 76px !important;
        }

        .sari-role-name {
            font-size: 14px !important;
        }

        .sari-role-description {
            font-size: 11px !important;
        }

        .sari-role-actions {
            margin-top: 16px !important;
        }

        .sari-role-continue {
            width: 100% !important;
            min-height: 50px !important;
            font-size: 14px !important;
        }

        .sari-signin-shortcut {
            width: calc(100% - 20px);
            max-width: 430px;
            border-radius: 16px;
            padding: 11px 14px;
            font-size: 13px;
        }
    }


    /* ============================================================
       REGISTRATION V4 — FORMAL / PROFESSIONAL UI SYSTEM
       Visual-only overrides. Existing Laravel names, routes and JS stay intact.
       ============================================================ */
    :root {
        --sari-gold: #c98208;
        --sari-gold-dark: #aa6905;
        --sari-ink: #17191d;
        --sari-body: #4f5660;
        --sari-subtle: #7b828c;
        --sari-line: #e4e7eb;
        --sari-input-line: #d7dce2;
        --sari-soft: #f7f8f9;
    }

    html,
    body {
        font-family: 'Poppins', sans-serif !important;
    }

    .sari-register-shell {
        max-width: 1240px !important;
        padding-top: 30px !important;
        padding-bottom: 34px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] {
        margin-bottom: 18px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] img {
        width: 164px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] span {
        margin-top: 5px !important;
        font-size: 8.5px !important;
        letter-spacing: .19em !important;
        color: #8b8f95 !important;
    }

    .sari-register-card {
        max-width: 1080px !important;
        overflow: hidden !important;
        border: 1px solid rgba(224, 226, 229, .96) !important;
        border-radius: 24px !important;
        background: rgba(255,255,255,.985) !important;
        box-shadow: 0 22px 65px rgba(30, 34, 40, .10) !important;
    }

    .sari-register-card > div:first-child {
        padding: 30px 34px 27px !important;
        border-bottom: 1px solid #eceef1 !important;
        background: #fff !important;
    }

    .sari-register-card > div:first-child > p:first-child {
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .16em !important;
        color: var(--sari-gold) !important;
    }

    .sari-register-card > div:first-child h1 {
        margin-top: 9px !important;
        font-size: clamp(31px, 3.1vw, 40px) !important;
        line-height: 1.08 !important;
        font-weight: 700 !important;
        letter-spacing: -.045em !important;
        color: var(--sari-ink) !important;
    }

    .sari-register-card > div:first-child > p:last-child {
        max-width: 610px !important;
        margin-top: 9px !important;
        font-size: 13px !important;
        line-height: 1.65 !important;
        color: #747a82 !important;
    }

    .sari-progress-wrap {
        padding: 15px 34px 17px !important;
        border-bottom: 1px solid #eceef1 !important;
        background: #fff !important;
    }

    .sari-progress-title {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        letter-spacing: -.01em !important;
        color: #34383e !important;
    }

    .sari-progress-counter {
        font-size: 10.5px !important;
        font-weight: 600 !important;
        color: #8c9198 !important;
    }

    .sari-progress-segments {
        gap: 7px !important;
        margin-top: 10px !important;
    }

    .sari-progress-segment {
        height: 3px !important;
        border-radius: 999px !important;
        background: #e8eaed !important;
    }

    .sari-progress-segment.is-complete,
    .sari-progress-segment.is-current {
        background: var(--sari-gold) !important;
    }

    .sari-register-card > .px-5,
    .sari-register-card > div.px-5 {
        padding: 28px 34px 32px !important;
    }

    .sari-wizard-step {
        width: 100%;
    }

    .sari-register-card [data-step] h2 {
        font-size: clamp(25px, 2.4vw, 30px) !important;
        line-height: 1.18 !important;
        font-weight: 700 !important;
        letter-spacing: -.035em !important;
        color: var(--sari-ink) !important;
    }

    .sari-register-card [data-step] > p,
    .sari-register-card [data-step] .sari-step-copy {
        margin-top: 5px !important;
        font-size: 12.5px !important;
        line-height: 1.65 !important;
        color: #747a82 !important;
    }

    /* Role selection — quiet enterprise rows */
    .sari-role-stage {
        max-width: 820px !important;
    }

    .sari-role-grid {
        gap: 9px !important;
    }

    .sari-role-option {
        min-height: 76px !important;
        gap: 15px !important;
        border: 1px solid #e2e5e9 !important;
        border-radius: 13px !important;
        background: #fff !important;
        padding: 12px 16px !important;
        box-shadow: none !important;
        transform: none !important;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease !important;
    }

    .sari-role-option:hover {
        border-color: #cfd4da !important;
        background: #fcfcfc !important;
        box-shadow: 0 5px 16px rgba(30,34,40,.045) !important;
        transform: none !important;
    }

    .sari-role-option.is-selected {
        border-color: #d79a35 !important;
        background: #fffdfa !important;
        box-shadow: 0 0 0 3px rgba(201,130,8,.07) !important;
    }

    .sari-role-check {
        width: 24px !important;
        height: 24px !important;
        border-color: #c7ccd2 !important;
        box-shadow: none !important;
    }

    .sari-role-option.is-selected .sari-role-check {
        border-color: var(--sari-gold) !important;
        background: var(--sari-gold) !important;
        box-shadow: none !important;
    }

    .sari-role-icon {
        width: 42px !important;
        height: 42px !important;
        border: 1px solid #ece4d8 !important;
        border-radius: 11px !important;
        background: #fbf8f3 !important;
        color: #a86c0b !important;
    }

    .sari-role-option.is-rider .sari-role-icon {
        border-color: #e0e9e3 !important;
        background: #f7faf8 !important;
        color: #4b745f !important;
    }

    .sari-role-name {
        font-size: 14.5px !important;
        font-weight: 600 !important;
        color: #202329 !important;
    }

    .sari-role-description {
        margin-top: 3px !important;
        font-size: 11.5px !important;
        line-height: 1.5 !important;
        color: #7a8088 !important;
    }

    .sari-role-arrow {
        color: #9ca1a8 !important;
        background: transparent !important;
    }

    .sari-role-option:hover .sari-role-arrow,
    .sari-role-option.is-selected .sari-role-arrow {
        color: var(--sari-gold) !important;
        background: transparent !important;
        transform: translateX(2px) !important;
    }

    .sari-role-preview {
        gap: 12px !important;
        margin-top: 18px !important;
        border: 0 !important;
        border-top: 1px solid #eceef1 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 18px 0 0 !important;
        box-shadow: none !important;
    }

    .sari-role-preview-icon {
        width: 42px !important;
        height: 42px !important;
        border: 1px solid #ece4d8 !important;
        border-radius: 11px !important;
        background: #fbf8f3 !important;
        color: #a86c0b !important;
    }

    .sari-role-preview-label {
        font-size: 8.5px !important;
        color: #93979d !important;
    }

    .sari-role-preview-title {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #2b2f35 !important;
    }

    .sari-role-preview-text {
        font-size: 11.5px !important;
        color: #777d85 !important;
    }

    /* One continuous formal form — section dividers, not nested cards */
    .sari-details-heading {
        gap: 12px !important;
        margin-bottom: 4px !important;
    }

    .sari-section-icon {
        width: 46px !important;
        height: 46px !important;
        border: 1px solid #eadfcf !important;
        border-radius: 12px !important;
        background: #fbf8f2 !important;
        color: #a96e0b !important;
        box-shadow: none !important;
    }

    .sari-form-section {
        margin: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 26px 0 28px !important;
        box-shadow: none !important;
    }

    .sari-form-section + .sari-form-section {
        border-top: 1px solid #e8eaed !important;
    }

    .sari-form-section-head {
        gap: 11px !important;
        padding: 0 0 20px !important;
        border: 0 !important;
    }

    .sari-form-section-icon {
        width: 38px !important;
        height: 38px !important;
        border: 1px solid #e8dfd1 !important;
        border-radius: 10px !important;
        background: #fbf8f3 !important;
        color: #a96e0b !important;
    }

    .sari-form-section-head h3 {
        font-size: 17px !important;
        line-height: 1.3 !important;
        font-weight: 600 !important;
        letter-spacing: -.018em !important;
        color: #24272c !important;
    }

    .sari-form-section-head p {
        margin-top: 2px !important;
        font-size: 11.5px !important;
        line-height: 1.55 !important;
        color: #7d838a !important;
    }

    .sari-role-specific-label {
        font-size: 8.5px !important;
        letter-spacing: .11em !important;
        color: #a96e0b !important;
    }

    .sari-field-grid,
    .sari-address-grid {
        gap: 17px 16px !important;
    }

    .sari-field-label,
    .sari-register-card label {
        margin-bottom: 7px !important;
        font-size: 12.5px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        color: #363a40 !important;
    }

    .sari-control {
        min-height: 52px !important;
        border: 1px solid var(--sari-input-line) !important;
        border-radius: 11px !important;
        background: #fff !important;
        color: #23262b !important;
        -webkit-text-fill-color: #23262b !important;
        font-size: 13.5px !important;
        line-height: 1.35 !important;
        box-shadow: 0 1px 2px rgba(20,24,29,.018) !important;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease !important;
    }

    .sari-control::placeholder {
        color: #a2a7ae !important;
        -webkit-text-fill-color: #a2a7ae !important;
    }

    .sari-control:hover:not(:disabled):not([readonly]) {
        border-color: #c5cbd2 !important;
    }

    .sari-control:focus {
        border-color: #c98a1c !important;
        box-shadow: 0 0 0 3px rgba(201,130,8,.09) !important;
    }

    .sari-control:disabled {
        opacity: 1 !important;
        background: #f5f6f7 !important;
    }

    .sari-readonly-control {
        background: #f5f6f7 !important;
        color: #697079 !important;
        -webkit-text-fill-color: #697079 !important;
    }

    .sari-register-card textarea.sari-control {
        min-height: 84px !important;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
        line-height: 1.55 !important;
    }

    .sari-field-hint {
        margin-top: 6px !important;
        font-size: 10.5px !important;
        line-height: 1.5 !important;
        color: #8b9198 !important;
    }

    .sari-input-with-icon > span {
        color: #8f959d !important;
    }

    .sari-address-status {
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        font-size: 10.5px !important;
        color: #838991 !important;
    }

    .sari-location-popover {
        border-color: #dfe3e7 !important;
        border-radius: 11px !important;
        padding: 5px !important;
        box-shadow: 0 18px 45px rgba(23,28,34,.12) !important;
    }

    .sari-location-option {
        border-radius: 8px !important;
        font-size: 12px !important;
        color: #3d4248 !important;
    }

    .sari-location-option:hover {
        background: #f7f8f9 !important;
        color: #8f5e08 !important;
    }

    .sari-manual-address {
        border-color: #eadfce !important;
        border-radius: 12px !important;
        background: #fffaf2 !important;
    }

    /* Uploads — quiet dashed zones */
    .sari-document-step > h2 {
        font-size: clamp(25px, 2.4vw, 30px) !important;
        color: var(--sari-ink) !important;
    }

    .sari-document-step .sari-document-label {
        margin-bottom: 8px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #363a40 !important;
    }

    .sari-upload-zone {
        min-height: 174px !important;
        border: 1.5px dashed #cfd4da !important;
        border-radius: 13px !important;
        background: #fbfcfc !important;
        padding: 27px 20px !important;
        box-shadow: none !important;
    }

    .sari-upload-zone:hover,
    .sari-upload-zone:focus-visible {
        border-color: #c98a1c !important;
        background: #fffdf8 !important;
        box-shadow: 0 0 0 3px rgba(201,130,8,.06) !important;
        transform: none !important;
    }

    .sari-upload-zone.is-dragging {
        border-color: var(--sari-gold) !important;
        background: #fffaf1 !important;
        box-shadow: 0 0 0 4px rgba(201,130,8,.08) !important;
        transform: none !important;
    }

    .sari-upload-zone.has-file {
        border-style: solid !important;
        border-color: #7ca68a !important;
        background: #fbfefc !important;
        box-shadow: none !important;
    }

    .sari-upload-zone.has-error {
        border-style: solid !important;
        border-color: #c87575 !important;
        background: #fffafa !important;
        box-shadow: none !important;
    }

    .sari-upload-icon {
        width: 44px !important;
        height: 44px !important;
        border: 1px solid #eadfce !important;
        border-radius: 11px !important;
        background: #fffaf1 !important;
        color: #a96e0b !important;
    }

    .sari-upload-title {
        margin-top: 11px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #2e3237 !important;
    }

    .sari-upload-browse {
        min-height: 36px !important;
        margin-top: 10px !important;
        border-color: #d7b36e !important;
        border-radius: 10px !important;
        padding: 8px 15px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #98630a !important;
    }

    .sari-upload-meta,
    .sari-upload-file,
    .sari-upload-error {
        font-size: 10.5px !important;
    }

    /* Buttons */
    .sari-register-card [data-next],
    .sari-register-card [data-back],
    .sari-register-card button[type="submit"],
    .sari-role-continue,
    .sari-document-actions .sari-register-action {
        min-height: 50px !important;
        border-radius: 11px !important;
        padding-inline: 21px !important;
        font-size: 13.5px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        letter-spacing: -.01em !important;
    }

    .sari-register-primary {
        border-color: var(--sari-gold) !important;
        background: var(--sari-gold) !important;
        box-shadow: 0 7px 16px rgba(201,130,8,.16) !important;
    }

    .sari-register-primary:hover {
        border-color: var(--sari-gold-dark) !important;
        background: var(--sari-gold-dark) !important;
        box-shadow: 0 8px 18px rgba(170,105,5,.18) !important;
    }

    .sari-register-secondary {
        border-color: #d9dde2 !important;
        background: #fff !important;
        color: #4e545c !important;
        box-shadow: none !important;
    }

    .sari-register-secondary:hover {
        border-color: #c7cdd4 !important;
        background: #fafbfb !important;
        color: #25292e !important;
    }

    /* Credential notices */
    .sari-register-card [data-step="credentials"] label.flex {
        border-color: #e3e6e9 !important;
        border-radius: 11px !important;
        background: #fafbfb !important;
        color: #626971 !important;
        font-size: 11.5px !important;
    }

    .sari-register-card [data-step="credentials"] .rounded-\[14px\] {
        border-radius: 11px !important;
    }

    /* Plain, always-visible sign-in shortcut — intentionally no container */
    .sari-signin-shortcut {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        width: auto !important;
        max-width: none !important;
        margin-top: 18px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        font-size: clamp(13px, 1.3vw, 14px) !important;
        line-height: 1.5 !important;
        font-weight: 500 !important;
        color: #5f646b !important;
    }

    .sari-signin-shortcut a {
        margin-left: 0 !important;
        font-weight: 700 !important;
        color: #a96e0b !important;
        text-decoration: none !important;
        transition: color .18s ease !important;
    }

    .sari-signin-shortcut a:hover {
        color: #7f4f04 !important;
        text-decoration: underline !important;
        text-underline-offset: 4px !important;
    }

    .sari-register-footer {
        margin-top: 14px !important;
        text-align: center !important;
        font-size: 9px !important;
        line-height: 1.5 !important;
        color: #92979e !important;
    }

    @media (max-width: 899px) {
        .sari-register-card {
            max-width: 860px !important;
        }

        .sari-field-grid,
        .sari-address-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .sari-address-grid > .md\:col-span-3 {
            grid-column: 1 / -1 !important;
        }
    }

    @media (max-width: 639px) {
        .sari-register-shell {
            padding: 18px 11px 26px !important;
        }

        .sari-register-shell > a[aria-label="Back to SARI home"] {
            margin-bottom: 14px !important;
        }

        .sari-register-shell > a[aria-label="Back to SARI home"] img {
            width: 142px !important;
        }

        .sari-register-card {
            border-radius: 17px !important;
        }

        .sari-register-card > div:first-child {
            padding: 24px 18px 21px !important;
        }

        .sari-register-card > div:first-child h1 {
            font-size: 29px !important;
        }

        .sari-register-card > div:first-child > p:last-child {
            font-size: 12px !important;
        }

        .sari-progress-wrap {
            padding: 13px 18px 15px !important;
        }

        .sari-register-card > .px-5,
        .sari-register-card > div.px-5 {
            padding: 23px 18px 25px !important;
        }

        .sari-field-grid,
        .sari-address-grid {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .sari-field-grid > .sm\:col-span-2,
        .sari-address-grid > .md\:col-span-3 {
            grid-column: auto !important;
        }

        .sari-form-section {
            padding: 24px 0 25px !important;
        }

        .sari-form-section-head {
            align-items: flex-start !important;
        }

        .sari-form-section-head h3 {
            font-size: 16px !important;
        }

        .sari-control {
            min-height: 50px !important;
            font-size: 13.5px !important;
        }

        .sari-role-option {
            min-height: 72px !important;
            gap: 12px !important;
            padding: 11px 12px !important;
        }

        .sari-role-check {
            width: 22px !important;
            height: 22px !important;
        }

        .sari-role-icon {
            width: 40px !important;
            height: 40px !important;
        }

        .sari-role-name {
            font-size: 14px !important;
        }

        .sari-role-description {
            font-size: 10.5px !important;
        }

        .sari-step-actions,
        .sari-document-actions {
            flex-direction: column-reverse !important;
            align-items: stretch !important;
        }

        .sari-step-actions .sari-register-action,
        .sari-document-actions .sari-register-action,
        .sari-register-card [data-step="credentials"] .sari-register-action,
        .sari-role-continue {
            width: 100% !important;
        }

        .sari-upload-zone {
            min-height: 155px !important;
            padding: 22px 15px !important;
        }

        .sari-signin-shortcut {
            flex-wrap: wrap !important;
            margin-top: 15px !important;
            padding-inline: 6px !important;
            font-size: 13px !important;
        }

        .sari-register-footer {
            font-size: 8.5px !important;
        }
    }



    /* ============================================================
       INLINE VALIDATION — HIGH-VISIBILITY, ACCESSIBLE ERROR STATES
       ============================================================ */
    .sari-control.is-invalid {
        border-color: #dc2626 !important;
        background: #fffafa !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .10) !important;
    }

    .sari-control.is-invalid:hover,
    .sari-control.is-invalid:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, .12) !important;
    }

    .sari-inline-error {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin-top: 7px;
        font-size: 11px;
        line-height: 1.45;
        font-weight: 600;
        color: #d11a2a;
        animation: sariValidationIn .18s ease-out both;
    }

    .sari-inline-error::before {
        content: '!';
        display: inline-grid;
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        place-items: center;
        margin-top: 0;
        border-radius: 999px;
        background: #d11a2a;
        color: #fff;
        font-size: 10px;
        line-height: 1;
        font-weight: 700;
    }

    input[type="checkbox"].is-invalid {
        outline: 2px solid #dc2626;
        outline-offset: 2px;
        border-radius: 3px;
    }

    .sari-upload-zone.has-error {
        border: 2px solid #dc2626 !important;
        background: #fffafa !important;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, .09) !important;
    }

    .sari-upload-zone.has-error .sari-upload-error {
        color: #d11a2a !important;
        font-weight: 700 !important;
    }

    @keyframes sariValidationIn {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 639px) {
        .sari-inline-error {
            font-size: 10.5px;
        }
    }


    /* ============================================================
       REGISTRATION V5 — PREMIUM ACTIONS + POLISHED VALIDATION
       Final visual layer: restrained, professional, responsive.
       ============================================================ */
    .sari-register-action {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        min-height: 52px !important;
        border-radius: 12px !important;
        padding: 0 22px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 14px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        letter-spacing: -.012em !important;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transform: translateY(0);
        transition:
            transform .18s cubic-bezier(.22,1,.36,1),
            box-shadow .18s ease,
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease !important;
    }

    .sari-button-icon {
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        transition: transform .18s cubic-bezier(.22,1,.36,1);
    }

    .sari-register-primary {
        border: 1px solid #c98208 !important;
        background: #d48f08 !important;
        color: #fff !important;
        box-shadow:
            0 8px 18px rgba(164, 100, 3, .16),
            inset 0 1px 0 rgba(255,255,255,.20) !important;
    }

    .sari-register-primary:hover:not(:disabled) {
        border-color: #b97405 !important;
        background: #c98208 !important;
        box-shadow:
            0 11px 24px rgba(143, 87, 3, .20),
            inset 0 1px 0 rgba(255,255,255,.18) !important;
        transform: translateY(-1px);
    }

    .sari-register-primary:hover:not(:disabled) .sari-button-icon {
        transform: translateX(2px);
    }

    .sari-register-secondary {
        border: 1px solid #d9dce0 !important;
        background: #fff !important;
        color: #33383e !important;
        box-shadow: 0 3px 10px rgba(25, 30, 35, .035) !important;
    }

    .sari-register-secondary:hover:not(:disabled) {
        border-color: #c6cbd1 !important;
        background: #fafafa !important;
        color: #1f2328 !important;
        box-shadow: 0 6px 16px rgba(25, 30, 35, .06) !important;
        transform: translateY(-1px);
    }

    .sari-register-secondary:hover:not(:disabled) .sari-button-icon {
        transform: translateX(-2px);
    }

    .sari-register-action:active:not(:disabled) {
        transform: translateY(0) !important;
        box-shadow: none !important;
    }

    .sari-register-action:focus-visible {
        outline: none !important;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .14) !important;
    }

    .sari-register-action:disabled {
        cursor: not-allowed !important;
        opacity: .46 !important;
        transform: none !important;
        box-shadow: none !important;
    }

    .sari-control.is-invalid {
        border-color: #e11d2e !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(225, 29, 46, .10) !important;
    }

    .sari-control.is-invalid:focus {
        border-color: #d31324 !important;
        box-shadow: 0 0 0 4px rgba(225, 29, 46, .13) !important;
    }

    .sari-inline-error {
        margin-top: 7px !important;
        font-size: 11.5px !important;
        line-height: 1.45 !important;
        font-weight: 600 !important;
        color: #d31324 !important;
    }

    .sari-inline-error::before {
        background: #d31324 !important;
    }

    .sari-control:not(.is-invalid):focus {
        border-color: #c98a1c !important;
        box-shadow: 0 0 0 4px rgba(201, 138, 28, .09) !important;
    }

    .sari-field-hint {
        min-height: 16px;
    }

    .sari-step-actions,
    .sari-document-actions,
    .sari-role-actions {
        padding-top: 2px;
    }

    @media (max-width: 639px) {
        .sari-register-action,
        .sari-role-continue,
        .sari-document-actions .sari-register-action {
            width: 100% !important;
            min-height: 52px !important;
            font-size: 14px !important;
            padding-inline: 18px !important;
        }

        .sari-button-icon {
            width: 16px;
            height: 16px;
            flex-basis: 16px;
        }
    }



    /* ============================================================
       REGISTRATION V6 — CLEAN DETAILS + SOLID GOLD ACTIONS
       ============================================================ */
    .sari-details-heading-clean {
        display: block !important;
        margin: 0 !important;
        padding: 2px 0 8px !important;
    }

    .sari-details-heading-clean h2 {
        margin: 0 !important;
        font-size: clamp(25px, 2.4vw, 30px) !important;
        line-height: 1.2 !important;
        font-weight: 650 !important;
        letter-spacing: -.032em !important;
        color: #202329 !important;
    }

    .sari-form-section {
        padding-top: 22px !important;
        padding-bottom: 26px !important;
    }

    .sari-form-section-head {
        align-items: center !important;
        min-height: 38px !important;
        padding-bottom: 18px !important;
    }

    .sari-form-section-head h3 {
        margin: 0 !important;
        font-size: 17px !important;
        font-weight: 600 !important;
        color: #25282d !important;
    }

    .sari-form-section-icon {
        width: 36px !important;
        height: 36px !important;
        border: 1px solid #eadfce !important;
        border-radius: 10px !important;
        background: #fdfaf5 !important;
        color: #b67709 !important;
    }

    .sari-register-primary {
        border: 1px solid #c78508 !important;
        background: #d48f08 !important;
        background-image: none !important;
        color: #fff !important;
        box-shadow: 0 7px 18px rgba(166, 103, 4, .14) !important;
    }

    .sari-register-primary:hover:not(:disabled) {
        border-color: #b97805 !important;
        background: #c98208 !important;
        background-image: none !important;
        box-shadow: 0 9px 22px rgba(149, 90, 3, .17) !important;
        transform: translateY(-1px) !important;
    }

    .sari-register-primary:active:not(:disabled) {
        border-color: #ad7004 !important;
        background: #b97805 !important;
        background-image: none !important;
        box-shadow: 0 4px 10px rgba(149, 90, 3, .12) !important;
        transform: translateY(0) !important;
    }

    .sari-register-secondary {
        border: 1px solid #ddc391 !important;
        background: #fff !important;
        color: #9a6507 !important;
        box-shadow: none !important;
    }

    .sari-register-secondary:hover:not(:disabled) {
        border-color: #c99025 !important;
        background: #fffaf1 !important;
        color: #805204 !important;
        box-shadow: 0 5px 14px rgba(166, 103, 4, .07) !important;
    }

    .sari-register-action:focus-visible {
        outline: none !important;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .16) !important;
    }

    @media (max-width: 639px) {
        .sari-details-heading-clean {
            padding-top: 0 !important;
            padding-bottom: 4px !important;
        }

        .sari-details-heading-clean h2 {
            font-size: 24px !important;
        }

        .sari-form-section {
            padding-top: 20px !important;
            padding-bottom: 23px !important;
        }
    }

    /* ============================================================
       REGISTRATION V7 — CLEAN SELECTS + CUSTOM CONSENT + EMAIL OTP
       Solid SARI gold palette. No gradients.
       ============================================================ */

    /* Native selects: one quiet, consistent chevron */
    .sari-register-card select.sari-control {
        -webkit-appearance: none !important;
        appearance: none !important;
        cursor: pointer !important;
        padding-right: 48px !important;
        background-color: #fff !important;
        background-image:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%236d665d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-size: 17px 17px !important;
    }

    .sari-register-card select.sari-control:hover:not(:disabled) {
        border-color: #cfb67f !important;
    }

    .sari-register-card select.sari-control:disabled {
        cursor: not-allowed !important;
        background-color: #f6f4f1 !important;
        background-image:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%23aaa39a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E") !important;
    }

    /* Terms — custom square check, accessible native checkbox stays in the DOM */
    .sari-terms-row {
        position: relative !important;
        display: flex !important;
        align-items: flex-start !important;
        gap: 12px !important;
        width: 100% !important;
        margin: 0 !important;
        border: 1px solid #e8e1d8 !important;
        border-radius: 12px !important;
        background: #fff !important;
        padding: 14px 15px !important;
        cursor: pointer !important;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease !important;
    }

    .sari-terms-row:hover {
        border-color: #d8c397 !important;
        background: #fffdf9 !important;
    }

    .sari-terms-input {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        margin: -1px !important;
        padding: 0 !important;
        overflow: hidden !important;
        clip: rect(0 0 0 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
        opacity: 0 !important;
    }

    .sari-terms-box {
        display: grid !important;
        width: 22px !important;
        height: 22px !important;
        flex: 0 0 22px !important;
        place-items: center !important;
        margin-top: 1px !important;
        border: 1.5px solid #bdb5aa !important;
        border-radius: 6px !important;
        background: #fff !important;
        color: transparent !important;
        transition:
            border-color .16s ease,
            background-color .16s ease,
            box-shadow .16s ease,
            color .16s ease !important;
    }

    .sari-terms-box svg {
        width: 14px !important;
        height: 14px !important;
        transform: scale(.82);
        transition: transform .16s cubic-bezier(.22,1,.36,1) !important;
    }

    .sari-terms-input:checked + .sari-terms-box {
        border-color: #c68106 !important;
        background: #d48f08 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(168, 103, 4, .15) !important;
    }

    .sari-terms-input:checked + .sari-terms-box svg {
        transform: scale(1);
    }

    .sari-terms-input:focus-visible + .sari-terms-box {
        border-color: #c68106 !important;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .14) !important;
    }

    .sari-terms-input.is-invalid + .sari-terms-box {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .10) !important;
    }

    .sari-terms-copy {
        min-width: 0 !important;
        font-size: 12px !important;
        line-height: 1.65 !important;
        font-weight: 500 !important;
        color: #615b53 !important;
    }

    .sari-terms-copy a {
        font-weight: 600 !important;
        color: #a96f06 !important;
        text-decoration: none !important;
    }

    .sari-terms-copy a:hover {
        color: #7f5104 !important;
        text-decoration: underline !important;
        text-underline-offset: 3px !important;
    }

    .sari-review-note {
        display: flex !important;
        align-items: flex-start !important;
        gap: 10px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 2px 2px 0 !important;
        color: #7b7369 !important;
    }

    .sari-review-note svg {
        width: 17px !important;
        height: 17px !important;
        flex: 0 0 17px !important;
        margin-top: 1px !important;
        color: #b57908 !important;
    }

    .sari-review-note p {
        margin: 0 !important;
        font-size: 11.5px !important;
        line-height: 1.55 !important;
    }

    .sari-otp-launch-error {
        margin-top: 12px !important;
        font-size: 11.5px !important;
        line-height: 1.5 !important;
        font-weight: 600 !important;
        color: #d31324 !important;
    }

    /* OTP stage */
    .sari-otp-stage {
        width: min(100%, 650px) !important;
        margin-inline: auto !important;
        padding: 8px 0 2px !important;
        text-align: center !important;
    }

    .sari-otp-panel {
        animation: sariOtpPanelIn .26s cubic-bezier(.22,1,.36,1) both;
    }

    .sari-otp-icon {
        display: grid !important;
        width: 70px !important;
        height: 70px !important;
        margin: 0 auto !important;
        place-items: center !important;
        border: 1px solid #efdfbd !important;
        border-radius: 22px !important;
        background: #fff9ed !important;
        color: #b97805 !important;
    }

    .sari-otp-icon svg {
        width: 31px !important;
        height: 31px !important;
    }

    .sari-otp-eyebrow {
        margin-top: 22px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        color: #b97805 !important;
    }

    .sari-otp-stage h2 {
        margin-top: 8px !important;
        font-size: clamp(27px, 3vw, 34px) !important;
        line-height: 1.18 !important;
        font-weight: 700 !important;
        letter-spacing: -.038em !important;
        color: #202329 !important;
    }

    .sari-otp-copy {
        max-width: 500px !important;
        margin: 9px auto 0 !important;
        font-size: 13px !important;
        line-height: 1.65 !important;
        color: #777168 !important;
    }

    .sari-otp-copy strong {
        font-weight: 600 !important;
        color: #4e4942 !important;
    }

    .sari-otp-code {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        margin-top: 28px !important;
    }

    .sari-otp-digit {
        width: 58px !important;
        height: 62px !important;
        border: 1.5px solid #d9d4cd !important;
        border-radius: 12px !important;
        background: #fff !important;
        padding: 0 !important;
        text-align: center !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 23px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #24272b !important;
        caret-color: #c98208 !important;
        outline: none !important;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            transform .16s ease !important;
    }

    .sari-otp-digit:hover {
        border-color: #cbb98f !important;
    }

    .sari-otp-digit:focus {
        border-color: #d48f08 !important;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .11) !important;
        transform: translateY(-1px);
    }

    .sari-otp-code.has-error .sari-otp-digit {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .08) !important;
    }

    .sari-otp-error {
        min-height: 18px !important;
        margin-top: 10px !important;
        font-size: 11.5px !important;
        line-height: 1.5 !important;
        font-weight: 600 !important;
        color: #d31324 !important;
    }

    .sari-otp-resend {
        margin-top: 19px !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        color: #767068 !important;
    }

    .sari-otp-resend button {
        margin-left: 4px !important;
        border: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        font: inherit !important;
        font-weight: 650 !important;
        color: #a96f06 !important;
        cursor: pointer !important;
        text-decoration: underline !important;
        text-decoration-thickness: 1px !important;
        text-underline-offset: 4px !important;
    }

    .sari-otp-resend button:hover:not(:disabled) {
        color: #7f5104 !important;
    }

    .sari-otp-resend button:disabled {
        cursor: default !important;
        color: #a29b92 !important;
        text-decoration: none !important;
    }

    .sari-otp-main-action {
        width: min(100%, 430px) !important;
        margin-top: 22px !important;
    }

    .sari-otp-status {
        display: grid !important;
        grid-template-columns: 1fr auto 1fr auto 1fr !important;
        align-items: center !important;
        width: min(100%, 470px) !important;
        margin: 28px auto 0 !important;
    }

    .sari-otp-status-line {
        width: 48px !important;
        height: 1px !important;
        background: #ded9d1 !important;
        transition: background-color .2s ease !important;
    }

    .sari-otp-status-node {
        display: flex !important;
        min-width: 0 !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 7px !important;
        color: #9a948c !important;
        transition: color .2s ease !important;
    }

    .sari-otp-status-dot {
        display: grid !important;
        width: 28px !important;
        height: 28px !important;
        place-items: center !important;
        border: 1.5px solid #cfc9c1 !important;
        border-radius: 999px !important;
        background: #fff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #9a948c !important;
        transition:
            border-color .2s ease,
            background-color .2s ease,
            color .2s ease,
            transform .2s cubic-bezier(.22,1,.36,1) !important;
    }

    .sari-otp-status-label {
        font-size: 10px !important;
        line-height: 1.3 !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    .sari-otp-status-node.is-complete,
    .sari-otp-status-node.is-current {
        color: #9d6809 !important;
    }

    .sari-otp-status-node.is-complete .sari-otp-status-dot {
        border-color: #d48f08 !important;
        background: #d48f08 !important;
        color: #fff !important;
    }

    .sari-otp-status-node.is-current .sari-otp-status-dot {
        border-color: #d48f08 !important;
        background: #fff8e9 !important;
        color: #b97805 !important;
        transform: scale(1.06);
    }

    .sari-otp-status-line.is-complete {
        background: #d48f08 !important;
    }

    .sari-otp-back {
        margin-top: 22px !important;
    }

    .sari-otp-verifying {
        padding: 58px 0 34px !important;
    }

    .sari-otp-spinner {
        width: 54px !important;
        height: 54px !important;
        margin: 0 auto !important;
        border: 4px solid #f0e2c5 !important;
        border-top-color: #d48f08 !important;
        border-radius: 999px !important;
        animation: sariOtpSpin .72s linear infinite;
    }

    .sari-otp-verifying h2 {
        margin-top: 24px !important;
        font-size: 27px !important;
    }

    .sari-otp-success {
        padding: 34px 0 14px !important;
    }

    .sari-otp-success-icon {
        display: grid !important;
        width: 82px !important;
        height: 82px !important;
        margin: 0 auto !important;
        place-items: center !important;
        border: 1px solid #efdfbd !important;
        border-radius: 999px !important;
        background: #fff8e9 !important;
        color: #fff !important;
        animation: sariOtpSuccessPop .34s cubic-bezier(.22,1,.36,1) both;
    }

    .sari-otp-success-icon > span {
        display: grid !important;
        width: 58px !important;
        height: 58px !important;
        place-items: center !important;
        border-radius: 999px !important;
        background: #d48f08 !important;
    }

    .sari-otp-success-icon svg {
        width: 31px !important;
        height: 31px !important;
    }

    .sari-otp-success h2 {
        margin-top: 22px !important;
    }

    .sari-otp-success-animation-wrap {
        position: relative;
        display: grid;
        width: 118px;
        height: 118px;
        margin: 0 auto;
        place-items: center;
        overflow: visible;
        isolation: isolate;
    }

    .sari-otp-success-animation {
        position: relative;
        z-index: 2;
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        border-radius: 999px;
        background: #fffaf0;
        box-shadow:
            0 0 0 1px rgba(212,143,8,.14),
            0 13px 34px rgba(130,86,5,.12);
        transform-origin: center;
        will-change: transform, opacity;
    }

    .sari-otp-success-animation::before {
        content: '';
        position: absolute;
        inset: 7px;
        border-radius: inherit;
        border: 1px solid rgba(212,143,8,.16);
        pointer-events: none;
    }

    .sari-otp-success-svg {
        display: block;
        width: 70px;
        height: 70px;
        overflow: visible;
    }

    .sari-otp-success-ring {
        fill: #d48f08;
        stroke: #d48f08;
        stroke-width: 1;
    }

    .sari-otp-success-check {
        fill: none;
        stroke: #fff;
        stroke-width: 3.2;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 30;
        stroke-dashoffset: 30;
    }

    .sari-otp-success-glow {
        position: absolute;
        z-index: 0;
        width: 92px;
        height: 92px;
        border-radius: 999px;
        background: rgba(212,143,8,.16);
        opacity: 0;
        transform: scale(.68);
        pointer-events: none;
        will-change: transform, opacity;
    }

    .sari-otp-success-particles {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
    }

    .sari-otp-success-particle {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        border-radius: 999px;
        opacity: 0;
        transform: translate(-50%, -50%);
        will-change: transform, opacity;
    }

    .sari-otp-success h2,
    .sari-otp-success .sari-otp-copy {
        opacity: 0;
        transform: translateY(8px);
        will-change: transform, opacity;
    }

    .sari-button-busy {
        pointer-events: none !important;
    }

    .sari-button-busy::before {
        content: '' !important;
        width: 16px !important;
        height: 16px !important;
        border: 2px solid rgba(255,255,255,.45) !important;
        border-top-color: #fff !important;
        border-radius: 999px !important;
        animation: sariOtpSpin .68s linear infinite !important;
    }

    @keyframes sariOtpSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes sariOtpPanelIn {
        from { opacity: 0; transform: translateY(7px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes sariOtpSuccessPop {
        from { opacity: 0; transform: scale(.88); }
        to { opacity: 1; transform: scale(1); }
    }

    @media (max-width: 639px) {
        .sari-otp-stage {
            padding-top: 2px !important;
        }

        .sari-otp-icon {
            width: 62px !important;
            height: 62px !important;
            border-radius: 19px !important;
        }

        .sari-otp-code {
            gap: 6px !important;
            margin-top: 24px !important;
        }

        .sari-otp-digit {
            width: min(13vw, 48px) !important;
            height: 54px !important;
            border-radius: 10px !important;
            font-size: 20px !important;
        }

        .sari-otp-main-action {
            width: 100% !important;
        }

        .sari-otp-status {
            width: 100% !important;
        }

        .sari-otp-status-line {
            width: 24px !important;
        }

        .sari-otp-status-label {
            font-size: 9px !important;
        }

        .sari-terms-row {
            padding: 13px !important;
        }

        .sari-terms-copy {
            font-size: 11.5px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-otp-panel,
        .sari-otp-spinner,
        .sari-otp-success-icon,
        .sari-button-busy::before {
            animation: none !important;
        }
    }


    /* ============================================================
       REGISTRATION V8 — CLEAN ROLE CONFIRMATION + PLAIN TERMS ROW
       ============================================================ */

    /* Selected role: compact confirmation row, not another card */
    .sari-role-preview {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        margin-top: 18px !important;
        border: 0 !important;
        border-top: 1px solid #ece7df !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 16px 2px 0 !important;
        box-shadow: none !important;
    }

    .sari-role-preview-icon {
        display: grid !important;
        width: 34px !important;
        height: 34px !important;
        flex: 0 0 34px !important;
        place-items: center !important;
        border: 1px solid #eadfc9 !important;
        border-radius: 10px !important;
        background: #fffaf0 !important;
        color: #b97805 !important;
        box-shadow: none !important;
    }

    .sari-role-preview-icon svg {
        width: 17px !important;
        height: 17px !important;
    }

    .sari-role-preview-copy {
        display: flex !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
        align-items: baseline !important;
        gap: 8px !important;
    }

    .sari-role-preview-label {
        margin: 0 !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
        color: #a49b90 !important;
        white-space: nowrap !important;
    }

    .sari-role-preview-title {
        margin: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-size: 13px !important;
        line-height: 1.35 !important;
        font-weight: 650 !important;
        letter-spacing: -.01em !important;
        color: #38332d !important;
    }

    .sari-role-preview-confirm {
        display: grid !important;
        width: 24px !important;
        height: 24px !important;
        flex: 0 0 24px !important;
        place-items: center !important;
        border-radius: 999px !important;
        background: #fff7e6 !important;
        color: #b97805 !important;
    }

    .sari-role-preview-confirm svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Terms: no card/container — just a clean checkbox line */
    .sari-terms-row {
        display: flex !important;
        align-items: flex-start !important;
        gap: 11px !important;
        width: 100% !important;
        margin: 22px 0 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        cursor: pointer !important;
    }

    .sari-terms-row:hover {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .sari-terms-copy {
        padding-top: 1px !important;
        font-size: 12px !important;
        line-height: 1.65 !important;
        color: #605a52 !important;
    }

    .sari-terms-box {
        width: 21px !important;
        height: 21px !important;
        flex: 0 0 21px !important;
        margin-top: 0 !important;
        border-radius: 6px !important;
    }

    .sari-review-note {
        margin-top: 17px !important;
        padding: 0 !important;
    }

    .sari-review-note p {
        font-size: 11px !important;
        color: #81796f !important;
    }

    /* Give credentials breathing room so password fields and consent never feel attached */
    [data-step="credentials"] > .mt-6.space-y-4 {
        row-gap: 16px !important;
    }

    [data-step="credentials"] .sari-step-actions {
        margin-top: 30px !important;
    }

    @media (max-width: 639px) {
        .sari-role-preview {
            gap: 9px !important;
            margin-top: 16px !important;
            padding-top: 14px !important;
        }

        .sari-role-preview-copy {
            display: block !important;
        }

        .sari-role-preview-label {
            display: block !important;
            margin-bottom: 4px !important;
        }

        .sari-role-preview-title {
            display: block !important;
            font-size: 12.5px !important;
        }

        .sari-terms-row {
            margin-top: 20px !important;
        }
    }


    /* ============================================================
       REGISTRATION V9 — SELECTED ROLE PROCESS DROPDOWN
       ============================================================ */
    .sari-role-preview-wrap {
        margin-top: 18px !important;
        border: 1px solid #e5e0d8 !important;
        border-radius: 15px !important;
        background: #fff !important;
        overflow: hidden !important;
        box-shadow: 0 8px 24px rgba(31, 27, 22, .035) !important;
    }

    .sari-role-preview-wrap .sari-role-preview {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 14px 16px !important;
        box-shadow: none !important;
    }

    .sari-role-preview-wrap .sari-role-preview-icon {
        display: grid !important;
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
        place-items: center !important;
        border: 1px solid #eadfc9 !important;
        border-radius: 11px !important;
        background: #fffaf0 !important;
        color: #b97805 !important;
        box-shadow: none !important;
    }

    .sari-role-preview-wrap .sari-role-preview-icon svg {
        width: 19px !important;
        height: 19px !important;
    }

    .sari-role-preview-wrap .sari-role-preview-copy {
        display: block !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    .sari-role-preview-wrap .sari-role-preview-label {
        display: block !important;
        margin: 0 0 4px !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #9a9187 !important;
    }

    .sari-role-preview-wrap .sari-role-preview-title {
        display: block !important;
        margin: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        font-weight: 650 !important;
        letter-spacing: -.015em !important;
        color: #2c2823 !important;
    }

    .sari-role-process-toggle {
        display: inline-flex !important;
        min-height: 38px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        border-radius: 9px !important;
        background: transparent !important;
        padding: 8px 10px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 12px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        color: #a86f05 !important;
        cursor: pointer !important;
        transition:
            background-color .18s ease,
            color .18s ease !important;
    }

    .sari-role-process-toggle:hover {
        background: #fff8ea !important;
        color: #8f5c03 !important;
    }

    .sari-role-process-toggle:focus-visible {
        outline: none !important;
        background: #fff8ea !important;
        box-shadow: 0 0 0 3px rgba(212, 143, 8, .12) !important;
    }

    .sari-role-process-toggle svg {
        width: 17px !important;
        height: 17px !important;
        transition: transform .22s cubic-bezier(.22,1,.36,1) !important;
    }

    .sari-role-process-toggle[aria-expanded="true"] svg {
        transform: rotate(180deg) !important;
    }

    .sari-role-process-panel {
        display: grid !important;
        grid-template-rows: 0fr !important;
        opacity: 0 !important;
        border-top: 1px solid transparent !important;
        background: #fcfbf9 !important;
        transition:
            grid-template-rows .26s cubic-bezier(.22,1,.36,1),
            opacity .18s ease,
            border-color .18s ease !important;
    }

    .sari-role-process-panel.is-open {
        grid-template-rows: 1fr !important;
        opacity: 1 !important;
        border-top-color: #ece7df !important;
    }

    .sari-role-process-panel-inner {
        min-height: 0 !important;
        overflow: hidden !important;
    }

    .sari-role-process-panel.is-open .sari-role-process-panel-inner {
        padding: 17px 16px 18px !important;
    }

    .sari-role-process-heading {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 18px !important;
        margin-bottom: 17px !important;
    }

    .sari-role-process-eyebrow {
        margin: 0 !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
        color: #a96f06 !important;
    }

    .sari-role-process-heading h3 {
        margin: 5px 0 0 !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        font-weight: 650 !important;
        letter-spacing: -.015em !important;
        color: #2e2a25 !important;
    }

    .sari-role-process-summary {
        max-width: 390px !important;
        margin: 0 !important;
        text-align: right !important;
        font-size: 10.5px !important;
        line-height: 1.55 !important;
        color: #847c72 !important;
    }

    .sari-role-process-steps {
        display: grid !important;
        grid-template-columns: repeat(var(--role-process-columns, 6), minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .sari-role-process-step {
        position: relative !important;
        min-width: 0 !important;
        padding-right: 13px !important;
    }

    .sari-role-process-step:not(:last-child)::after {
        content: '' !important;
        position: absolute !important;
        left: 31px !important;
        right: 5px !important;
        top: 14px !important;
        height: 1px !important;
        background: #ded7cd !important;
    }

    .sari-role-process-step-top {
        position: relative !important;
        z-index: 1 !important;
        display: flex !important;
        align-items: center !important;
    }

    .sari-role-process-number {
        display: grid !important;
        width: 29px !important;
        height: 29px !important;
        flex: 0 0 29px !important;
        place-items: center !important;
        border: 1px solid #dfd4bf !important;
        border-radius: 999px !important;
        background: #fff !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #a96f06 !important;
        box-shadow: 0 2px 7px rgba(50, 39, 23, .035) !important;
    }

    .sari-role-process-step:last-child .sari-role-process-number {
        border-color: #d9a542 !important;
        background: #fff7e8 !important;
        color: #9d6503 !important;
    }

    .sari-role-process-step-title {
        display: block !important;
        margin-top: 9px !important;
        padding-right: 4px !important;
        font-size: 10.5px !important;
        line-height: 1.4 !important;
        font-weight: 650 !important;
        color: #48423b !important;
    }

    .sari-role-process-step-copy {
        display: block !important;
        margin-top: 3px !important;
        padding-right: 6px !important;
        font-size: 9px !important;
        line-height: 1.45 !important;
        color: #928a80 !important;
    }

    @media (max-width: 899px) {
        .sari-role-process-heading {
            display: block !important;
        }

        .sari-role-process-summary {
            max-width: none !important;
            margin-top: 6px !important;
            text-align: left !important;
        }

        .sari-role-process-steps {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px 10px !important;
        }

        .sari-role-process-step:not(:last-child)::after {
            display: none !important;
        }
    }

    @media (max-width: 639px) {
        .sari-role-preview-wrap .sari-role-preview {
            padding: 13px !important;
        }

        .sari-role-process-toggle {
            min-height: 36px !important;
            padding-inline: 8px !important;
            font-size: 11px !important;
        }

        .sari-role-process-panel.is-open .sari-role-process-panel-inner {
            padding: 15px 13px 16px !important;
        }

        .sari-role-process-steps {
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 0 !important;
        }

        .sari-role-process-step {
            display: grid !important;
            grid-template-columns: 30px minmax(0, 1fr) !important;
            column-gap: 10px !important;
            padding: 0 0 15px !important;
        }

        .sari-role-process-step:not(:last-child)::after {
            display: block !important;
            left: 14px !important;
            right: auto !important;
            top: 29px !important;
            bottom: 0 !important;
            width: 1px !important;
            height: auto !important;
        }

        .sari-role-process-step-top {
            grid-column: 1 !important;
            grid-row: 1 / span 2 !important;
            align-items: flex-start !important;
        }

        .sari-role-process-step-title,
        .sari-role-process-step-copy {
            grid-column: 2 !important;
            padding-right: 0 !important;
        }

        .sari-role-process-step-title {
            margin-top: 1px !important;
            font-size: 11px !important;
        }

        .sari-role-process-step-copy {
            margin-top: 2px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-role-process-panel,
        .sari-role-process-toggle svg {
            transition: none !important;
        }
    }


    /* ============================================================
       REGISTRATION V10 — ROLE-AWARE PROFILE / BUSINESS IMAGE
       ============================================================ */
    .sari-profile-row {
        display: grid !important;
        grid-template-columns: 76px minmax(0, 1fr) !important;
        gap: 16px !important;
        align-items: center !important;
        margin-top: 22px !important;
        border-top: 1px solid #eee9e2 !important;
        padding-top: 20px !important;
    }

    .sari-profile-preview {
        position: relative !important;
        display: grid !important;
        width: 72px !important;
        height: 72px !important;
        place-items: center !important;
        overflow: hidden !important;
        border: 1px solid #e4ddd3 !important;
        border-radius: 18px !important;
        background: #faf8f4 !important;
        color: #b97805 !important;
    }

    .sari-profile-preview img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    .sari-profile-preview-fallback {
        display: grid !important;
        place-items: center !important;
    }

    .sari-profile-preview-fallback svg {
        width: 27px !important;
        height: 27px !important;
    }

    .sari-profile-copy {
        min-width: 0 !important;
    }

    .sari-profile-title {
        display: flex !important;
        align-items: baseline !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }

    .sari-profile-title strong {
        font-size: 13px !important;
        line-height: 1.4 !important;
        font-weight: 650 !important;
        color: #403a34 !important;
    }

    .sari-profile-requirement {
        font-size: 10px !important;
        line-height: 1.4 !important;
        font-weight: 600 !important;
        color: #9a9187 !important;
    }

    .sari-profile-help {
        margin-top: 4px !important;
        font-size: 10.5px !important;
        line-height: 1.55 !important;
        color: #8b8379 !important;
    }

    .sari-profile-actions {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
        margin-top: 10px !important;
    }

    .sari-profile-button {
        display: inline-flex !important;
        min-height: 36px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 1px solid #d7bd8c !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 8px 12px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 11px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        color: #9b6607 !important;
        cursor: pointer !important;
        transition:
            border-color .16s ease,
            background-color .16s ease,
            color .16s ease,
            box-shadow .16s ease !important;
    }

    .sari-profile-button:hover,
    .sari-profile-button:focus-visible {
        border-color: #c99434 !important;
        background: #fff9ed !important;
        color: #805104 !important;
        outline: none !important;
    }

    .sari-profile-remove {
        border-color: transparent !important;
        background: transparent !important;
        color: #81786e !important;
    }

    .sari-profile-remove:hover {
        background: #f7f4ef !important;
        color: #5e574f !important;
    }

    .sari-profile-file {
        margin-top: 8px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-size: 10px !important;
        line-height: 1.45 !important;
        font-weight: 600 !important;
        color: #5f7f68 !important;
    }

    .sari-profile-error {
        margin-top: 7px !important;
        font-size: 11px !important;
        line-height: 1.45 !important;
        font-weight: 650 !important;
        color: #d31324 !important;
    }

    .sari-profile-preview.is-invalid {
        border: 2px solid #e11d2e !important;
        box-shadow: 0 0 0 4px rgba(225, 29, 46, .09) !important;
    }

    @media (max-width: 639px) {
        .sari-profile-row {
            grid-template-columns: 62px minmax(0, 1fr) !important;
            gap: 13px !important;
        }

        .sari-profile-preview {
            width: 60px !important;
            height: 60px !important;
            border-radius: 16px !important;
        }
    }


    /* ============================================================
       REGISTRATION V11 — LARGE PROFILE PHOTO DROP ZONE
       ============================================================ */
    .sari-profile-row {
        display: block !important;
        margin-top: 22px !important;
        border-top: 1px solid #eee9e2 !important;
        padding-top: 20px !important;
    }

    .sari-profile-copy {
        min-width: 0 !important;
    }

    .sari-profile-title {
        display: flex !important;
        align-items: baseline !important;
        gap: 7px !important;
        flex-wrap: wrap !important;
    }

    .sari-profile-title strong {
        font-size: 13px !important;
        line-height: 1.4 !important;
        font-weight: 650 !important;
        color: #403a34 !important;
    }

    .sari-profile-requirement {
        font-size: 10px !important;
        line-height: 1.4 !important;
        font-weight: 600 !important;
        color: #9a9187 !important;
    }

    .sari-profile-help {
        margin-top: 4px !important;
        font-size: 10.5px !important;
        line-height: 1.55 !important;
        color: #8b8379 !important;
    }

    .sari-profile-preview {
        position: relative !important;
        display: grid !important;
        width: 100% !important;
        min-height: 210px !important;
        margin-top: 14px !important;
        place-items: center !important;
        overflow: hidden !important;
        border: 1.5px dashed #d7b76f !important;
        border-radius: 16px !important;
        background: #fffdf9 !important;
        color: #b97805 !important;
        cursor: pointer !important;
        outline: none !important;
        box-shadow: none !important;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease !important;
    }

    .sari-profile-preview:hover,
    .sari-profile-preview:focus-visible {
        border-color: #c88a16 !important;
        background: #fffaf0 !important;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .07) !important;
    }

    .sari-profile-preview.is-dragging {
        border-style: solid !important;
        border-color: #c98208 !important;
        background: #fff8e9 !important;
        box-shadow: 0 0 0 5px rgba(212, 143, 8, .09) !important;
        transform: translateY(-1px) !important;
    }

    .sari-profile-preview.has-image {
        min-height: 240px !important;
        border-style: solid !important;
        border-color: #ddd7cf !important;
        background: #f6f4f0 !important;
        cursor: default !important;
        box-shadow: 0 10px 28px rgba(31, 27, 22, .045) !important;
    }

    .sari-profile-preview.has-image:hover,
    .sari-profile-preview.has-image:focus-visible {
        border-color: #d6cec4 !important;
        background: #f6f4f0 !important;
        box-shadow: 0 10px 28px rgba(31, 27, 22, .045) !important;
    }

    .sari-profile-preview img {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
    }

    .sari-profile-preview-fallback {
        display: flex !important;
        max-width: 420px !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 26px 20px !important;
        text-align: center !important;
    }

    .sari-profile-camera {
        display: grid !important;
        width: 52px !important;
        height: 52px !important;
        place-items: center !important;
        border-radius: 15px !important;
        background: #fff4dc !important;
        color: #b97805 !important;
    }

    .sari-profile-camera svg {
        width: 25px !important;
        height: 25px !important;
    }

    .sari-profile-drop-title {
        margin-top: 13px !important;
        font-size: 13px !important;
        line-height: 1.4 !important;
        font-weight: 650 !important;
        color: #39342e !important;
    }

    .sari-profile-drop-copy {
        margin-top: 5px !important;
        font-size: 10.5px !important;
        line-height: 1.55 !important;
        color: #91887d !important;
    }

    .sari-profile-drop-browse {
        display: inline-flex !important;
        min-height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        margin-top: 12px !important;
        border: 1px solid #d4b16d !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 8px 13px !important;
        font-size: 10.5px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        color: #9b6607 !important;
    }

    .sari-profile-drop-meta {
        margin-top: 9px !important;
        font-size: 9.5px !important;
        line-height: 1.45 !important;
        color: #a29a91 !important;
    }

    .sari-profile-image-actions {
        position: absolute !important;
        right: 12px !important;
        bottom: 12px !important;
        z-index: 3 !important;
        display: flex !important;
        align-items: center !important;
        gap: 7px !important;
    }

    .sari-profile-image-action {
        display: inline-flex !important;
        min-height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        border: 1px solid rgba(255,255,255,.72) !important;
        border-radius: 9px !important;
        background: rgba(32, 29, 25, .82) !important;
        padding: 8px 11px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        color: #fff !important;
        cursor: pointer !important;
        backdrop-filter: blur(8px) !important;
        transition: background-color .16s ease, transform .16s ease !important;
    }

    .sari-profile-image-action:hover {
        background: rgba(22, 20, 17, .94) !important;
        transform: translateY(-1px) !important;
    }

    .sari-profile-image-action.is-remove {
        background: rgba(255,255,255,.92) !important;
        border-color: rgba(255,255,255,.92) !important;
        color: #5e574f !important;
    }

    .sari-profile-image-action.is-remove:hover {
        background: #fff !important;
        color: #3f3933 !important;
    }

    .sari-profile-file {
        display: none !important;
    }

    .sari-profile-error {
        margin-top: 8px !important;
        font-size: 11px !important;
        line-height: 1.45 !important;
        font-weight: 650 !important;
        color: #d31324 !important;
    }

    .sari-profile-preview.is-invalid {
        border: 2px solid #e11d2e !important;
        box-shadow: 0 0 0 4px rgba(225, 29, 46, .09) !important;
    }

    @media (max-width: 639px) {
        .sari-profile-preview {
            min-height: 175px !important;
            border-radius: 14px !important;
        }

        .sari-profile-preview.has-image {
            min-height: 205px !important;
        }

        .sari-profile-camera {
            width: 46px !important;
            height: 46px !important;
            border-radius: 13px !important;
        }

        .sari-profile-image-actions {
            right: 9px !important;
            bottom: 9px !important;
        }

        .sari-profile-image-action {
            min-height: 32px !important;
            padding: 7px 9px !important;
            font-size: 9.5px !important;
        }
    }


    /* PROFILE DROPZONE V2 — ULTRA CLEAN */
    .sari-profile-help:not(.sr-only),
    .sari-profile-drop-copy,
    .sari-profile-drop-meta {
        display: none !important;
    }

    .sari-profile-drop-title {
        margin-top: 12px !important;
        font-size: 13px !important;
    }

    .sari-profile-drop-browse {
        margin-top: 10px !important;
        min-height: 32px !important;
        padding: 7px 12px !important;
        font-size: 10px !important;
    }

    .sari-profile-image-action {
        width: 36px !important;
        height: 36px !important;
        min-height: 36px !important;
        padding: 0 !important;
        border-radius: 10px !important;
    }

    .sari-profile-image-action svg {
        width: 16px !important;
        height: 16px !important;
    }

    .sari-profile-image-action.is-remove {
        color: #8a403d !important;
    }


    /* ============================================================
       REGISTRATION V12 — COMPACT / READABLE / UI-ONLY
       Final visual override layer.
       No form names, routes, validation logic, OTP logic, or JS changed.
       ============================================================ */

    :root {
        --sari-compact-gold: #c98208;
        --sari-compact-gold-dark: #aa6905;
        --sari-compact-ink: #1d2025;
        --sari-compact-body: #555c64;
        --sari-compact-muted: #818890;
        --sari-compact-line: #e5e7ea;
        --sari-compact-input: #d8dce1;
    }

    /* Page / card scale */
    .sari-register-shell {
        max-width: 1120px !important;
        padding-top: 22px !important;
        padding-bottom: 24px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] {
        margin-bottom: 12px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] img {
        width: 142px !important;
    }

    .sari-register-shell > a[aria-label="Back to SARI home"] span {
        margin-top: 4px !important;
        font-size: 8.5px !important;
        letter-spacing: .18em !important;
    }

    .sari-register-card {
        max-width: 960px !important;
        border-radius: 20px !important;
        border-color: rgba(219, 222, 226, .98) !important;
        box-shadow: 0 16px 44px rgba(28, 32, 37, .085) !important;
    }

    /* Header */
    .sari-register-card > div:first-child {
        padding: 21px 26px 19px !important;
    }

    .sari-register-card > div:first-child > p:first-child {
        font-size: 8.5px !important;
        letter-spacing: .14em !important;
    }

    .sari-register-card > div:first-child h1 {
        margin-top: 7px !important;
        font-size: clamp(29px, 2.3vw, 35px) !important;
        line-height: 1.1 !important;
    }

    .sari-register-card > div:first-child > p:last-child {
        max-width: 580px !important;
        margin-top: 7px !important;
        font-size: 12px !important;
        line-height: 1.55 !important;
    }

    /* Progress */
    .sari-progress-wrap {
        padding: 11px 26px 13px !important;
    }

    .sari-progress-title {
        font-size: 11px !important;
    }

    .sari-progress-counter {
        font-size: 10px !important;
    }

    .sari-progress-segments {
        gap: 6px !important;
        margin-top: 8px !important;
    }

    .sari-progress-segment {
        height: 3px !important;
    }

    /* Main content padding */
    .sari-register-card > .px-5,
    .sari-register-card > div.px-5 {
        padding: 22px 26px 24px !important;
    }

    .sari-register-card [data-step] h2 {
        font-size: clamp(23px, 2vw, 27px) !important;
        line-height: 1.18 !important;
    }

    .sari-register-card [data-step] > p,
    .sari-register-card [data-step] .sari-step-copy {
        font-size: 12px !important;
        line-height: 1.55 !important;
    }

    /* Role step */
    .sari-role-stage {
        max-width: 760px !important;
    }

    .sari-role-stage > .text-center h2 {
        font-size: clamp(23px, 2vw, 27px) !important;
    }

    .sari-role-stage > .text-center p {
        margin-top: 6px !important;
        font-size: 11.5px !important;
        line-height: 1.55 !important;
    }

    .sari-role-stage > .mt-7 {
        margin-top: 18px !important;
    }

    .sari-role-grid {
        gap: 7px !important;
    }

    .sari-role-option {
        min-height: 64px !important;
        gap: 12px !important;
        border-radius: 11px !important;
        padding: 9px 13px !important;
    }

    .sari-role-check {
        width: 22px !important;
        height: 22px !important;
    }

    .sari-role-icon {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
    }

    .sari-role-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .sari-role-name {
        font-size: 14px !important;
        line-height: 1.25 !important;
    }

    .sari-role-description {
        margin-top: 2px !important;
        font-size: 11px !important;
        line-height: 1.4 !important;
    }

    .sari-role-arrow {
        width: 26px !important;
        height: 26px !important;
    }

    .sari-role-preview-wrap {
        margin-top: 12px !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    .sari-role-preview-wrap .sari-role-preview {
        gap: 10px !important;
        padding: 11px 13px !important;
    }

    .sari-role-preview-wrap .sari-role-preview-icon {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        border-radius: 9px !important;
    }

    .sari-role-preview-wrap .sari-role-preview-title {
        font-size: 13px !important;
    }

    .sari-role-process-toggle {
        min-height: 34px !important;
        padding: 7px 8px !important;
        font-size: 11px !important;
    }

    .sari-role-process-panel.is-open .sari-role-process-panel-inner {
        padding: 13px 13px 14px !important;
    }

    .sari-role-process-heading {
        margin-bottom: 13px !important;
    }

    .sari-role-process-heading h3 {
        font-size: 13px !important;
    }

    .sari-role-process-summary {
        font-size: 10px !important;
    }

    .sari-role-process-number {
        width: 27px !important;
        height: 27px !important;
        flex-basis: 27px !important;
    }

    .sari-role-process-step:not(:last-child)::after {
        left: 29px !important;
        top: 13px !important;
    }

    .sari-role-process-step-title {
        margin-top: 7px !important;
        font-size: 10px !important;
    }

    .sari-role-process-step-copy {
        margin-top: 2px !important;
        font-size: 8.8px !important;
    }

    .sari-role-actions {
        margin-top: 13px !important;
    }

    /* Details step */
    .sari-details-heading-clean {
        padding: 0 0 2px !important;
    }

    .sari-details-heading-clean h2 {
        font-size: clamp(23px, 2vw, 27px) !important;
    }

    .sari-wizard-step[data-step="details"] > .mt-6 {
        margin-top: 12px !important;
    }

    .sari-wizard-step[data-step="details"] > .mt-6.space-y-5 {
        row-gap: 0 !important;
    }

    .sari-form-section {
        padding: 17px 0 19px !important;
    }

    .sari-form-section-head {
        min-height: 34px !important;
        gap: 9px !important;
        padding-bottom: 13px !important;
    }

    .sari-form-section-icon {
        width: 33px !important;
        height: 33px !important;
        border-radius: 9px !important;
    }

    .sari-form-section-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    .sari-form-section-head h3 {
        font-size: 15.5px !important;
    }

    .sari-role-specific-label {
        font-size: 8px !important;
    }

    .sari-field-grid,
    .sari-address-grid {
        gap: 13px 14px !important;
    }

    .sari-field-grid.mt-5,
    .sari-address-grid.mt-5 {
        margin-top: 14px !important;
    }

    .sari-field-label,
    .sari-register-card label {
        margin-bottom: 6px !important;
        font-size: 12px !important;
        line-height: 1.35 !important;
    }

    .sari-control {
        min-height: 47px !important;
        border-radius: 10px !important;
        padding-inline: 13px !important;
        font-size: 13px !important;
    }

    .sari-input-with-icon .sari-control {
        padding-left: 40px !important;
    }

    .sari-input-with-icon > span {
        left: 13px !important;
    }

    .sari-register-card textarea.sari-control {
        min-height: 72px !important;
        padding-top: 11px !important;
        padding-bottom: 11px !important;
    }

    .sari-field-hint {
        min-height: 0 !important;
        margin-top: 5px !important;
        font-size: 10px !important;
        line-height: 1.4 !important;
    }

    .sari-inline-error {
        margin-top: 5px !important;
        font-size: 10.5px !important;
    }

    .sari-address-status {
        margin-top: 10px !important;
        font-size: 10px !important;
    }

    .sari-location-option {
        font-size: 11.5px !important;
        padding: 8px 9px !important;
    }

    .sari-manual-address {
        margin-top: 11px !important;
        padding: 12px !important;
        font-size: 10.5px !important;
    }

    /* Profile image */
    .sari-profile-row {
        margin-top: 16px !important;
        padding-top: 15px !important;
    }

    .sari-profile-title strong {
        font-size: 12.5px !important;
    }

    .sari-profile-requirement {
        font-size: 9.5px !important;
    }

    .sari-profile-preview {
        min-height: 145px !important;
        margin-top: 10px !important;
        border-radius: 13px !important;
    }

    .sari-profile-preview.has-image {
        min-height: 170px !important;
    }

    .sari-profile-preview-fallback {
        padding: 18px 16px !important;
    }

    .sari-profile-camera {
        width: 44px !important;
        height: 44px !important;
        border-radius: 12px !important;
    }

    .sari-profile-camera svg {
        width: 21px !important;
        height: 21px !important;
    }

    .sari-profile-drop-title {
        margin-top: 9px !important;
        font-size: 12px !important;
    }

    .sari-profile-drop-browse {
        min-height: 30px !important;
        margin-top: 8px !important;
        padding: 6px 11px !important;
        font-size: 9.8px !important;
    }

    .sari-profile-image-action {
        width: 34px !important;
        height: 34px !important;
        min-height: 34px !important;
        border-radius: 9px !important;
    }

    /* Document step */
    .sari-document-step > h2 {
        font-size: clamp(23px, 2vw, 27px) !important;
    }

    .sari-document-step > .mt-7 {
        margin-top: 18px !important;
    }

    .sari-document-step > .mt-7.space-y-6 {
        row-gap: 16px !important;
    }

    .sari-document-step .sari-document-label {
        margin-bottom: 6px !important;
        font-size: 12px !important;
    }

    .sari-upload-zone {
        min-height: 138px !important;
        border-radius: 12px !important;
        padding: 18px 16px !important;
    }

    .sari-upload-icon {
        width: 40px !important;
        height: 40px !important;
        border-radius: 10px !important;
    }

    .sari-upload-icon svg {
        width: 20px !important;
        height: 20px !important;
    }

    .sari-upload-title {
        margin-top: 8px !important;
        font-size: 13.5px !important;
    }

    .sari-upload-browse {
        min-height: 32px !important;
        margin-top: 8px !important;
        padding: 7px 13px !important;
        font-size: 11px !important;
    }

    .sari-upload-meta,
    .sari-upload-file,
    .sari-upload-error {
        margin-top: 7px !important;
        font-size: 10px !important;
    }

    .sari-document-actions {
        margin-top: 18px !important;
    }

    /* Credentials */
    [data-step="credentials"] > .mt-6 {
        margin-top: 17px !important;
    }

    [data-step="credentials"] > .mt-6.space-y-4 {
        row-gap: 13px !important;
    }

    .sari-terms-row {
        margin-top: 13px !important;
        gap: 9px !important;
    }

    .sari-terms-box {
        width: 20px !important;
        height: 20px !important;
        flex-basis: 20px !important;
    }

    .sari-terms-copy {
        font-size: 11.5px !important;
        line-height: 1.55 !important;
    }

    .sari-review-note {
        margin-top: 11px !important;
        gap: 8px !important;
    }

    .sari-review-note p {
        font-size: 10.5px !important;
    }

    [data-step="credentials"] .sari-step-actions {
        margin-top: 20px !important;
    }

    /* Buttons */
    .sari-register-action,
    .sari-role-continue,
    .sari-document-actions .sari-register-action {
        min-height: 45px !important;
        border-radius: 10px !important;
        padding-inline: 18px !important;
        font-size: 12.5px !important;
        gap: 8px !important;
    }

    .sari-button-icon {
        width: 15px !important;
        height: 15px !important;
        flex-basis: 15px !important;
    }

    .sari-register-primary {
        box-shadow: 0 5px 13px rgba(166, 103, 4, .13) !important;
    }

    .sari-register-primary:hover:not(:disabled) {
        box-shadow: 0 7px 16px rgba(149, 90, 3, .16) !important;
    }

    /* OTP */
    .sari-otp-stage {
        width: min(100%, 570px) !important;
        padding: 2px 0 0 !important;
    }

    .sari-otp-icon {
        width: 58px !important;
        height: 58px !important;
        border-radius: 17px !important;
    }

    .sari-otp-icon svg {
        width: 26px !important;
        height: 26px !important;
    }

    .sari-otp-eyebrow {
        margin-top: 15px !important;
        font-size: 9px !important;
    }

    .sari-otp-stage h2 {
        margin-top: 6px !important;
        font-size: clamp(24px, 2.2vw, 29px) !important;
    }

    .sari-otp-copy {
        margin-top: 6px !important;
        font-size: 12px !important;
        line-height: 1.55 !important;
    }

    .sari-otp-code {
        gap: 8px !important;
        margin-top: 20px !important;
    }

    .sari-otp-digit {
        width: 50px !important;
        height: 54px !important;
        border-radius: 10px !important;
        font-size: 20px !important;
    }

    .sari-otp-error {
        margin-top: 7px !important;
        font-size: 10.5px !important;
    }

    .sari-otp-resend {
        margin-top: 13px !important;
        font-size: 11px !important;
    }

    .sari-otp-main-action {
        width: min(100%, 390px) !important;
        margin-top: 16px !important;
    }

    .sari-otp-status {
        width: min(100%, 430px) !important;
        margin-top: 20px !important;
    }

    .sari-otp-status-dot {
        width: 25px !important;
        height: 25px !important;
    }

    .sari-otp-status-label {
        font-size: 9.5px !important;
    }

    .sari-otp-status-line {
        width: 42px !important;
    }

    .sari-otp-back {
        margin-top: 16px !important;
    }

    .sari-otp-verifying {
        padding: 38px 0 24px !important;
    }

    .sari-otp-spinner {
        width: 46px !important;
        height: 46px !important;
    }

    .sari-otp-verifying h2 {
        margin-top: 18px !important;
        font-size: 24px !important;
    }

    .sari-otp-success {
        padding: 22px 0 8px !important;
    }

    .sari-otp-success-icon {
        width: 68px !important;
        height: 68px !important;
    }

    .sari-otp-success-icon > span {
        width: 48px !important;
        height: 48px !important;
    }

    .sari-otp-success h2 {
        margin-top: 16px !important;
    }

    /* Sign in + footer — always readable, still minimal */
    .sari-signin-shortcut {
        margin-top: 12px !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
    }

    .sari-register-footer {
        margin-top: 8px !important;
        font-size: 9px !important;
    }

    /* Large desktop: compact card, readable typography */
    @media (min-width: 1440px) {
        .sari-register-shell {
            max-width: 1180px !important;
        }

        .sari-register-card {
            max-width: 980px !important;
        }

        .sari-register-card > div:first-child h1 {
            font-size: 35px !important;
        }

        .sari-register-card [data-step] h2,
        .sari-details-heading-clean h2,
        .sari-document-step > h2 {
            font-size: 27px !important;
        }

        .sari-field-label,
        .sari-register-card label {
            font-size: 12.5px !important;
        }

        .sari-control {
            font-size: 13.5px !important;
        }

        .sari-register-action,
        .sari-role-continue,
        .sari-document-actions .sari-register-action {
            font-size: 13px !important;
        }

        .sari-role-name {
            font-size: 14px !important;
        }

        .sari-role-description {
            font-size: 11px !important;
        }
    }

    /* Tablet */
    @media (max-width: 899px) {
        .sari-register-card {
            max-width: 820px !important;
        }

        .sari-register-card > div:first-child {
            padding-inline: 22px !important;
        }

        .sari-progress-wrap {
            padding-inline: 22px !important;
        }

        .sari-register-card > .px-5,
        .sari-register-card > div.px-5 {
            padding-inline: 22px !important;
        }

        .sari-profile-preview {
            min-height: 135px !important;
        }

        .sari-upload-zone {
            min-height: 130px !important;
        }
    }

    /* Mobile: keep text visible; compact vertically, not tiny */
    @media (max-width: 639px) {
        .sari-register-shell {
            padding: 14px 10px 22px !important;
        }

        .sari-register-shell > a[aria-label="Back to SARI home"] {
            margin-bottom: 10px !important;
        }

        .sari-register-shell > a[aria-label="Back to SARI home"] img {
            width: 124px !important;
        }

        .sari-register-card {
            border-radius: 16px !important;
        }

        .sari-register-card > div:first-child {
            padding: 19px 16px 17px !important;
        }

        .sari-register-card > div:first-child h1 {
            font-size: 27px !important;
        }

        .sari-register-card > div:first-child > p:last-child {
            font-size: 11.5px !important;
        }

        .sari-progress-wrap {
            padding: 10px 16px 12px !important;
        }

        .sari-register-card > .px-5,
        .sari-register-card > div.px-5 {
            padding: 19px 16px 21px !important;
        }

        .sari-register-card [data-step] h2,
        .sari-details-heading-clean h2,
        .sari-document-step > h2 {
            font-size: 22px !important;
        }

        .sari-role-option {
            min-height: 62px !important;
            padding: 9px 10px !important;
        }

        .sari-role-name {
            font-size: 13.5px !important;
        }

        .sari-role-description {
            font-size: 10.5px !important;
        }

        .sari-form-section {
            padding: 16px 0 18px !important;
        }

        .sari-form-section-head h3 {
            font-size: 15px !important;
        }

        .sari-field-label,
        .sari-register-card label {
            font-size: 12px !important;
        }

        .sari-control {
            min-height: 47px !important;
            font-size: 13px !important;
        }

        .sari-profile-preview {
            min-height: 130px !important;
        }

        .sari-profile-preview.has-image {
            min-height: 155px !important;
        }

        .sari-upload-zone {
            min-height: 126px !important;
            padding: 16px 13px !important;
        }

        .sari-register-action,
        .sari-role-continue,
        .sari-document-actions .sari-register-action {
            min-height: 47px !important;
            font-size: 13px !important;
        }

        .sari-otp-code {
            gap: 5px !important;
        }

        .sari-otp-digit {
            width: min(13.2vw, 45px) !important;
            height: 50px !important;
            font-size: 19px !important;
        }

        .sari-otp-status-line {
            width: 20px !important;
        }

        .sari-signin-shortcut {
            font-size: 12.5px !important;
        }
    }


    @media (max-width: 639px) {
        .sari-otp-success-animation-wrap {
            width: 108px;
            height: 108px;
        }

        .sari-otp-success-animation {
            width: 84px;
            height: 84px;
        }

        .sari-otp-success-svg {
            width: 64px;
            height: 64px;
        }

        .sari-otp-success-glow {
            width: 84px;
            height: 84px;
        }
    }

</style>

<div class="relative min-h-screen min-h-[100dvh] overflow-x-hidden bg-[#f7f4ee] font-['Poppins',sans-serif] text-[#17140e]">

    {{-- SAME BACKGROUND PHOTO AS LOGIN --}}
    <div class="fixed inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt=""
            class="h-full w-full object-cover object-center"
        >
        <div class="absolute inset-0 bg-white/68"></div>
        <div class="absolute inset-0 bg-[#fffaf1]/18"></div>
    </div>

    <div class="sari-register-shell relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-12">

        {{-- CENTERED BRAND --}}
        <a
            href="{{ route('home') }}"
            class="mb-5 inline-flex flex-col items-center sm:mb-6"
            aria-label="Back to SARI home"
        >
            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="h-auto w-[150px] object-contain brightness-0 sm:w-[175px] lg:w-[190px]"
            >

            <span class="mt-2 text-center text-[8px] font-semibold uppercase tracking-[0.22em] text-[#9a9185] sm:text-[9px]">
                Elevated Everyday
            </span>
        </a>


        {{-- SINGLE REGISTRATION CARD --}}
        <div class="sari-register-card w-full overflow-visible border border-[#e7e7e4] bg-white">

            {{-- CARD HEADER --}}
            <div class="border-b border-[#ededeb] px-5 pb-4 pt-5 text-center sm:px-7 sm:pb-5 sm:pt-6">
                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b97805]">
                    Join SARI
                </p>

                <h1 class="mt-2 text-[25px] font-bold tracking-[-.04em] text-[#1f1b16] sm:text-[30px]">
                    Create your account
                </h1>

                <p class="mx-auto mt-2.5 max-w-[430px] text-[10px] leading-5 text-[#81786c] sm:text-[11px]">
                    Complete one step at a time. We only show the information needed for the role you choose.
                </p>
            </div>


            {{-- CLEAN SEGMENTED STEP STATUS --}}
            <div class="sari-progress-wrap">
                <div class="sari-progress-meta">
                    <p id="wizardStepTitle" class="sari-progress-title">Choose account type</p>
                    <p id="wizardCounter" class="sari-progress-counter">Step 1 of 5</p>
                </div>
                <div
                    id="wizardProgressSegments"
                    class="sari-progress-segments"
                    style="grid-template-columns:repeat(5,minmax(0,1fr))"
                    aria-label="Registration progress"
                ></div>
            </div>


            {{-- SERVER VALIDATION --}}
            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-[13px] border border-[#ead0d0] bg-[#fff7f7] px-3.5 py-3 sm:mx-7">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a55555]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v5"></path>
                        <path d="M12 16.5h.01"></path>
                    </svg>
                    <div>
                        <p class="text-[12px] font-semibold text-[#a55555]">Please review your registration</p>
                        <p class="mt-1 text-[11px] leading-5 text-[#a55555]">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <div class="px-5 py-5 sm:px-7 sm:py-6">
        <form
            id="registrationWizard"
            method="POST"
            action="{{ route('register.submit') }}"
            enctype="multipart/form-data"
            novalidate
        >
            @csrf

            <input type="hidden" id="provinceCode" name="province_code" value="{{ old('province_code') }}">
            <input type="hidden" id="provinceName" name="province_name" value="{{ old('province_name') }}">
            <input type="hidden" id="municipalityCode" name="municipality_code" value="{{ old('municipality_code') }}">
            <input type="hidden" id="municipalityName" name="municipality_name" value="{{ old('municipality_name') }}">
            <input type="hidden" id="barangayCode" name="barangay_code" value="{{ old('barangay_code') }}">
            <input type="hidden" id="barangayName" name="barangay_name" value="{{ old('barangay_name') }}">

            {{-- ROLE --}}
            <section
                class="sari-wizard-step is-entering"
                data-step="role"
            >
                <div class="sari-role-stage">
                    <div class="text-center">
                        <h2 class="text-[18px] font-bold tracking-[-.025em] text-[#28231d]">
                            What will you use SARI for?
                        </h2>
                        <p class="mx-auto mt-2 max-w-[520px] text-[9px] leading-5 text-[#81786d]">
                            Choose the role that best matches how you want to use SARI.
                        </p>
                    </div>

                    <div class="mt-7">
                        <select id="roleSelect" name="role" required class="sr-only" aria-label="Register as">
                            <option value="">Select account type</option>
                            <option value="buyer" {{ old('role') === 'buyer' ? 'selected' : '' }}>Buyer</option>
                            <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                            <option value="logistics" {{ old('role') === 'logistics' ? 'selected' : '' }}>Logistics</option>
                            <option value="rider" {{ old('role') === 'rider' ? 'selected' : '' }}>Rider</option>
                        </select>

                        <div class="sari-role-grid" role="radiogroup" aria-label="Choose an account type">
                            <button type="button" class="sari-role-option" data-role-option="buyer" role="radio" aria-checked="false">
                                <span class="sari-role-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-icon">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="8" r="3"></circle>
                                        <path d="M5 21c.5-4.5 3-7 7-7s6.5 2.5 7 7"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-copy">
                                    <span class="sari-role-name">Buyer</span>
                                    <span class="sari-role-description">Shop, track orders, and manage purchases.</span>
                                </span>
                                <span class="sari-role-arrow" aria-hidden="true">›</span>
                            </button>

                            <button type="button" class="sari-role-option" data-role-option="seller" role="radio" aria-checked="false">
                                <span class="sari-role-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-icon">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 9h16M5 9l2-5h10l2 5M6 9v11h12V9M9 20v-6h6v6"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-copy">
                                    <span class="sari-role-name">Seller</span>
                                    <span class="sari-role-description">List products and manage your SARI store.</span>
                                </span>
                                <span class="sari-role-arrow" aria-hidden="true">›</span>
                            </button>

                            <button type="button" class="sari-role-option" data-role-option="logistics" role="radio" aria-checked="false">
                                <span class="sari-role-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-icon">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 19V7l8-4 8 4v12"></path>
                                        <path d="M8 19v-5h8v5M8 9h.01M12 9h.01M16 9h.01"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-copy">
                                    <span class="sari-role-name">Logistics</span>
                                    <span class="sari-role-description">Operate a Logistics team and manage Riders.</span>
                                </span>
                                <span class="sari-role-arrow" aria-hidden="true">›</span>
                            </button>

                            <button type="button" class="sari-role-option is-rider" data-role-option="rider" role="radio" aria-checked="false">
                                <span class="sari-role-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-icon">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="7" cy="17" r="3"></circle>
                                        <circle cx="17" cy="17" r="3"></circle>
                                        <path d="M7 17l4-8h3l3 8M9 13h7M11 9l-2-2M14 6h3"></path>
                                    </svg>
                                </span>
                                <span class="sari-role-copy">
                                    <span class="sari-role-name">Rider</span>
                                    <span class="sari-role-description">Choose a Logistics provider first, then apply directly to their team.</span>
                                </span>
                                <span class="sari-role-arrow" aria-hidden="true">›</span>
                            </button>
                        </div>

                        <div id="rolePreview" hidden class="sari-role-preview-wrap" aria-live="polite">
                            <div class="sari-role-preview">
                                <span id="rolePreviewIcon" class="sari-role-preview-icon" aria-hidden="true"></span>

                                <span class="sari-role-preview-copy">
                                    <span class="sari-role-preview-label">Selected role</span>
                                    <span id="rolePreviewTitle" class="sari-role-preview-title"></span>
                                    <span id="rolePreviewText" class="sr-only"></span>
                                </span>

                                <button
                                    id="roleProcessToggle"
                                    type="button"
                                    class="sari-role-process-toggle"
                                    aria-expanded="false"
                                    aria-controls="roleProcessPanel"
                                >
                                    <span id="roleProcessToggleLabel">View steps</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>
                            </div>

                            <div id="roleProcessPanel" class="sari-role-process-panel" aria-hidden="true">
                                <div class="sari-role-process-panel-inner">
                                    <div class="sari-role-process-heading">
                                        <div>
                                            <p class="sari-role-process-eyebrow">Registration process</p>
                                            <h3 id="roleProcessTitle">What happens next</h3>
                                        </div>
                                        <p id="roleProcessSummary" class="sari-role-process-summary"></p>
                                    </div>

                                    <ol id="roleProcessSteps" class="sari-role-process-steps"></ol>
                                </div>
                            </div>
                        </div>

                        <div class="sari-role-actions">
                            <button
                                id="roleContinueButton"
                                type="button"
                                class="sari-register-action sari-register-primary sari-role-continue"
                                disabled
                            >
                                <span>Continue</span>
                                <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 12h14"></path>
                                    <path d="m13 6 6 6-6 6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DETAILS — PERSONAL + CONTACT + ADDRESS + ROLE-SPECIFIC INFO --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="details"
            >
                <div class="sari-details-heading sari-details-heading-clean">
                    <h2>Your Information</h2>
                </div>

                <div class="mt-6 space-y-5">
                    {{-- PERSONAL + CONTACT --}}
                    <section class="sari-form-section" aria-labelledby="personalSectionTitle">
                        <div class="sari-form-section-head">
                            <span class="sari-form-section-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="8" r="3"></circle>
                                    <path d="M5 20c.5-4 3-6.5 7-6.5s6.5 2.5 7 6.5"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 id="personalSectionTitle">Personal & Contact</h3>
                            </div>
                        </div>

                        <div class="sari-field-grid mt-5">
                            <div>
                                <label class="sari-field-label">Last Name *</label>
                                <input
                                    type="text"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    required
                                    maxlength="100"
                                    data-person-name
                                    autocomplete="family-name"
                                    placeholder="Dela Cruz"
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                            </div>

                            <div>
                                <label class="sari-field-label">First Name *</label>
                                <input
                                    type="text"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    required
                                    maxlength="100"
                                    data-person-name
                                    autocomplete="given-name"
                                    placeholder="Juan"
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                            </div>

                            <div>
                                <label class="sari-field-label">Middle Initial</label>
                                <input
                                    id="middleInitial"
                                    type="text"
                                    name="middle_initial"
                                    value="{{ old('middle_initial') }}"
                                    maxlength="1"
                                    autocomplete="additional-name"
                                    placeholder="M"
                                    class="sari-control w-full border border-[#e3ddd4] uppercase"
                                >
                                <p class="sari-field-hint">One letter only.</p>
                            </div>

                            <div>
                                <label class="sari-field-label">Sex *</label>
                                <select
                                    name="sex"
                                    required
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                                    <option value="">Select sex</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div>
                                <label class="sari-field-label">Birthday *</label>
                                <input
                                    id="birthday"
                                    type="date"
                                    name="birthday"
                                    value="{{ old('birthday') }}"
                                    required
                                    min="1900-01-01"
                                    max="{{ now()->subYears(18)->toDateString() }}"
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                                <p id="ageRequirement" class="sari-field-hint">You must be 18 years old or older to register.</p>
                            </div>

                            <div>
                                <label class="sari-field-label">Age</label>
                                <input
                                    id="agePreview"
                                    type="text"
                                    readonly
                                    aria-readonly="true"
                                    placeholder="Auto-calculated"
                                    class="sari-control sari-readonly-control w-full border border-[#e3ddd5]"
                                >
                            </div>

                            <div class="sm:col-span-2">
                                <label class="sari-field-label">Email Address *</label>
                                <div class="sari-input-with-icon">
                                    <span aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 6h16v12H4z"></path>
                                            <path d="m4 7 8 6 8-6"></path>
                                        </svg>
                                    </span>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        maxlength="190"
                                        autocomplete="email"
                                        placeholder="name@email.com"
                                        class="sari-control w-full border border-[#e3ddd4]"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="sari-field-label">Contact Number *</label>
                                <div class="sari-input-with-icon">
                                    <span aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M7 4h3l1 4-2 1c1 3 3 5 6 6l1-2 4 1v3c0 1.1-.9 2-2 2C10.8 19 5 13.2 5 6c0-1.1.9-2 2-2Z"></path>
                                        </svg>
                                    </span>
                                    <input
                                        id="contactNumber"
                                        type="tel"
                                        name="contact_no"
                                        value="{{ old('contact_no') }}"
                                        required
                                        inputmode="numeric"
                                        maxlength="11"
                                        pattern="09[0-9]{9}"
                                        autocomplete="tel"
                                        placeholder="09XXXXXXXXX"
                                        class="sari-control w-full border border-[#e3ddd4]"
                                    >
                                </div>
                                <p id="contactHint" class="sari-field-hint">11 digits only, starting with 09.</p>
                            </div>
                        </div>
                        <div class="sari-profile-row">
                            <div class="sari-profile-copy">
                                <div class="sari-profile-title">
                                    <strong id="profileImageLabel">Profile Photo</strong>
                                    <span id="profileImageRequirement" class="sari-profile-requirement">(Optional)</span>
                                </div>

                                <p id="profileImageHelp" class="sr-only">
                                    Add a clear profile image, or continue with the default SARI avatar.
                                </p>
                            </div>

                            <div
                                id="profileImagePreview"
                                class="sari-profile-preview"
                                role="button"
                                tabindex="0"
                                aria-label="Upload profile image"
                                data-profile-drop-zone
                            >
                                <img id="profileImagePreviewImage" src="" alt="Selected profile image" hidden>

                                <div id="profileImageFallback" class="sari-profile-preview-fallback">
                                    <span class="sari-profile-camera" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 8h4l1.6-2.5h4.8L16 8h4v10H4z"></path>
                                            <circle cx="12" cy="13" r="3.2"></circle>
                                        </svg>
                                    </span>

                                    <p class="sari-profile-drop-title">Drag photo here</p>
                                    <span class="sari-profile-drop-browse">Browse</span>
                                </div>

                                <div id="profileImageActions" class="sari-profile-image-actions" hidden>
                                    <button
                                        id="profileImageBrowse"
                                        type="button"
                                        class="sari-profile-image-action"
                                        aria-label="Change photo"
                                        title="Change photo"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2.8 2.8 0 0 0-4-4L4 16v4Z"></path>
                                            <path d="m13.5 6.5 4 4"></path>
                                        </svg>
                                    </button>

                                    <button
                                        id="profileImageRemove"
                                        type="button"
                                        class="sari-profile-image-action is-remove"
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
                                id="profileImageInput"
                                type="file"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                class="sr-only"
                                data-profile-input
                            >

                            <p id="profileImageFile" class="sari-profile-file" hidden></p>
                            <p id="profileImageError" class="sari-profile-error" role="alert" hidden></p>
                        </div>
                    </section>

                    {{-- ADDRESS --}}
                    <section class="sari-form-section" aria-labelledby="addressSectionTitle">
                        <div class="sari-form-section-head">
                            <span class="sari-form-section-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="2.5"></circle>
                                </svg>
                            </span>
                            <div>
                                <h3 id="addressSectionTitle">Address</h3>
                            </div>
                        </div>

                        <div id="addressStatus" class="sari-address-status mt-4">
                            <span id="addressStatusDot" class="h-2 w-2 rounded-full bg-[#d29b2d]"></span>
                            <span id="addressStatusText">Address directory will load when this form opens.</span>
                        </div>

                        <div class="sari-address-grid mt-5">
                            <div class="relative">
                                <label class="sari-field-label">Province / Area *</label>
                                <input
                                    id="provinceSearch"
                                    type="text"
                                    autocomplete="off"
                                    placeholder="Search province"
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                                <div id="provinceResults" hidden class="sari-location-list sari-location-popover"></div>
                            </div>

                            <div class="relative">
                                <label class="sari-field-label">City / Municipality *</label>
                                <input
                                    id="municipalitySearch"
                                    type="text"
                                    autocomplete="off"
                                    disabled
                                    placeholder="Choose province first"
                                    class="sari-control w-full border border-[#e3ddd4] disabled:bg-[#f5f3ef] disabled:!text-[#a59c90] disabled:[-webkit-text-fill-color:#a59c90]"
                                >
                                <div id="municipalityResults" hidden class="sari-location-list sari-location-popover"></div>
                            </div>

                            <div class="relative">
                                <label class="sari-field-label">Barangay *</label>
                                <input
                                    id="barangaySearch"
                                    type="text"
                                    autocomplete="off"
                                    disabled
                                    placeholder="Choose city first"
                                    class="sari-control w-full border border-[#e3ddd4] disabled:bg-[#f5f3ef] disabled:!text-[#a59c90] disabled:[-webkit-text-fill-color:#a59c90]"
                                >
                                <div id="barangayResults" hidden class="sari-location-list sari-location-popover"></div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="sari-field-label">Street / House No. / Subdivision *</label>
                                <textarea
                                    name="street_address"
                                    required
                                    rows="2"
                                    maxlength="500"
                                    placeholder="House number, street, subdivision, landmark, etc."
                                    class="sari-control w-full resize-none border border-[#e3ddd4]"
                                >{{ old('street_address') }}</textarea>
                            </div>
                        </div>

                        <div id="manualAddressFallback" hidden class="sari-manual-address mt-4">
                            <div class="flex items-start gap-3">
                                <span class="sari-mini-notice-icon" aria-hidden="true">!</span>
                                <div>
                                    <p class="font-semibold">Manual address entry</p>
                                    <p class="mt-1">The online address directory is unavailable. Enter your location below.</p>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                                <input id="manualProvince" type="text" maxlength="100" placeholder="Province / Area" class="sari-control border border-[#e3ddd4]">
                                <input id="manualMunicipality" type="text" maxlength="100" placeholder="City / Municipality" class="sari-control border border-[#e3ddd4]">
                                <input id="manualBarangay" type="text" maxlength="100" placeholder="Barangay" class="sari-control border border-[#e3ddd4]">
                            </div>
                        </div>
                    </section>

                    {{-- ROLE-SPECIFIC BUSINESS INFO --}}
                    <section id="businessDetailsSection" hidden class="sari-form-section" aria-labelledby="businessStepTitle">
                        <div class="sari-form-section-head">
                            <span class="sari-form-section-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 9h16"></path>
                                    <path d="M5 9l2-5h10l2 5"></path>
                                    <path d="M6 9v11h12V9"></path>
                                    <path d="M9 20v-6h6v6"></path>
                                </svg>
                            </span>
                            <div>
                                <p id="businessStepEyebrow" class="sari-role-specific-label">Business requirement</p>
                                <h3 id="businessStepTitle">Business Information</h3>
                                <p id="businessStepDescription" class="sr-only">Enter the registered business information for this account.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label id="businessNameLabel" class="sari-field-label">Business Name *</label>
                                <input
                                    id="businessName"
                                    type="text"
                                    name="business_name"
                                    value="{{ old('business_name') }}"
                                    maxlength="180"
                                    data-business-required
                                    disabled
                                    placeholder="Registered business name"
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                                <p id="businessNameHint" class="sari-field-hint">Use the name shown on your permit or registration.</p>
                            </div>

                            <div id="sellerLineOfBusinessGroup" hidden>
                                <label class="sari-field-label">Line of Business *</label>
                                <select
                                    name="line_of_business"
                                    data-seller-only-required
                                    disabled
                                    class="sari-control w-full border border-[#e3ddd4]"
                                >
                                    <option value="">Select business category</option>
                                    @foreach([
                                        'Electronics','Fashion','Home & Living','Beauty',
                                        'Books','Food & Beverage','Jewelry & Watches',
                                        'Furniture & Office','Others'
                                    ] as $category)
                                        <option value="{{ $category }}" {{ old('line_of_business') === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="sari-step-actions mt-6">
                    <button type="button" data-back class="sari-register-action sari-register-secondary">
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M19 12H5"></path>
                            <path d="m11 18-6-6 6-6"></path>
                        </svg>
                        <span>Back</span>
                    </button>
                    <button type="button" data-next class="sari-register-action sari-register-primary">
                        <span>Continue to Verification</span>
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </button>
                </div>
            </section>

            {{-- DOCUMENTS --}}
            <section
                hidden
                class="sari-wizard-step sari-document-step"
                data-step="documents"
            >
                <h2 class="font-bold text-[#28231d]">Verification Documents</h2>

                <div class="mt-7 space-y-6">
                    <div>
                        <label id="idDocumentLabel" class="sari-document-label block">Valid ID *</label>

                        <div
                            class="sari-upload-zone"
                            data-upload-zone="idDocumentInput"
                            role="button"
                            tabindex="0"
                            aria-label="Upload Valid ID"
                        >
                            <input
                                id="idDocumentInput"
                                type="file"
                                name="id_document"
                                required
                                accept="image/jpeg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf"
                                class="sari-upload-input"
                                data-upload-input
                            >

                            <div class="sari-upload-content">
                                <span class="sari-upload-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3.5h7l4 4V20H7z"></path>
                                        <path d="M14 3.5V8h4"></path>
                                        <path d="M12.5 16V10.5"></path>
                                        <path d="m10 13 2.5-2.5L15 13"></path>
                                    </svg>
                                </span>

                                <p class="sari-upload-title" data-upload-title>Drag file here</p>
                                <span class="sari-upload-browse">Browse</span>
                                <p class="sari-upload-meta">JPG, PNG or PDF</p>
                                <p class="sari-upload-file" data-upload-file hidden></p>
                                <p class="sari-upload-error" data-upload-error hidden></p>
                            </div>
                        </div>
                    </div>

                    <div id="businessPermitGroup" hidden>
                        <label id="businessPermitLabel" class="sari-document-label block">Business Permit *</label>

                        <div
                            class="sari-upload-zone"
                            data-upload-zone="businessPermitInput"
                            role="button"
                            tabindex="0"
                            aria-label="Upload Business Permit"
                        >
                            <input
                                id="businessPermitInput"
                                type="file"
                                name="business_permit"
                                data-business-permit-required
                                disabled
                                accept="image/jpeg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf"
                                class="sari-upload-input"
                                data-upload-input
                            >

                            <div class="sari-upload-content">
                                <span class="sari-upload-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3.5h7l4 4V20H7z"></path>
                                        <path d="M14 3.5V8h4"></path>
                                        <path d="M12.5 16V10.5"></path>
                                        <path d="m10 13 2.5-2.5L15 13"></path>
                                    </svg>
                                </span>

                                <p class="sari-upload-title" data-upload-title>Drag file here</p>
                                <span class="sari-upload-browse">Browse</span>
                                <p class="sari-upload-meta">JPG, PNG or PDF</p>
                                <p class="sari-upload-file" data-upload-file hidden></p>
                                <p class="sari-upload-error" data-upload-error hidden></p>
                            </div>
                        </div>

                        <p id="businessPermitHint" class="sr-only">
                            Upload a clear copy of the required business registration document.
                        </p>
                    </div>
                </div>

                <div class="sari-document-actions flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary">
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M19 12H5"></path>
                            <path d="m11 18-6-6 6-6"></path>
                        </svg>
                        <span>Back</span>
                    </button>
                    <button type="button" data-next class="sari-register-action sari-register-primary">
                        <span>Continue to Login Setup</span>
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </button>
                </div>
            </section>

            {{-- CREDENTIALS --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="credentials"
            >
                <h2 class="font-bold text-[#28231d]">Login Credentials</h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="sari-field-label">Password *</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            class="sari-control w-full border border-[#e3ddd4]"
                        >
                    </div>

                    <div>
                        <label class="sari-field-label">Confirm Password *</label>
                        <input
                            id="passwordConfirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Repeat password"
                            class="sari-control w-full border border-[#e3ddd4]"
                        >
                    </div>

                    <label class="sari-terms-row" for="termsAgreement">
                        <input
                            id="termsAgreement"
                            type="checkbox"
                            name="terms"
                            value="1"
                            required
                            class="sari-terms-input"
                        >
                        <span class="sari-terms-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m5 12 4 4L19 6"></path>
                            </svg>
                        </span>
                        <span class="sari-terms-copy">
                            I agree to the <a href="#" onclick="event.stopPropagation()">Terms of Service</a>
                            and <a href="#" onclick="event.stopPropagation()">Privacy Policy</a>.
                        </span>
                    </label>

                    <div class="sari-review-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 3 5 6v5c0 4.6 2.8 8.1 7 10 4.2-1.9 7-5.4 7-10V6l-7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        <p>After email verification, your registration will be submitted for account review.</p>
                    </div>
                </div>

                <p id="otpLaunchError" class="sari-otp-launch-error" role="alert" hidden></p>

                <div class="sari-step-actions mt-6">
                    <button type="button" data-back class="sari-register-action sari-register-secondary">
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M19 12H5"></path>
                            <path d="m11 18-6-6 6-6"></path>
                        </svg>
                        <span>Back</span>
                    </button>

                    <button id="otpLaunchButton" type="button" class="sari-register-action sari-register-primary">
                        <span id="otpLaunchButtonLabel">Continue to Email Verification</span>
                        <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </button>
                </div>
            </section>

            {{-- EMAIL OTP --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="otp"
            >
                <div class="sari-otp-stage">
                    <div id="otpEntryPanel" class="sari-otp-panel">
                        <div class="sari-otp-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3.5" y="5" width="17" height="14" rx="2"></rect>
                                <path d="m4.5 7 7.5 5.5L19.5 7"></path>
                            </svg>
                        </div>

                        <p class="sari-otp-eyebrow">Email Verification</p>
                        <h2>Enter your verification code</h2>
                        <p class="sari-otp-copy">
                            We sent a 6-digit code to <strong id="otpDestination">your email</strong>.
                        </p>

                        <div id="otpCodeGroup" class="sari-otp-code" aria-label="6-digit verification code">
                            @for ($otpIndex = 1; $otpIndex <= 6; $otpIndex++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="1"
                                    autocomplete="{{ $otpIndex === 1 ? 'one-time-code' : 'off' }}"
                                    class="sari-otp-digit"
                                    data-otp-digit
                                    aria-label="Verification code digit {{ $otpIndex }}"
                                >
                            @endfor
                        </div>

                        <p id="otpError" class="sari-otp-error" role="alert" aria-live="polite"></p>

                        <p class="sari-otp-resend">
                            Didn't receive the code?
                            <button id="otpResendButton" type="button" disabled>
                                <span id="otpResendLabel">Resend code</span>
                            </button>
                        </p>

                        <button id="otpVerifyButton" type="button" class="sari-register-action sari-register-primary sari-otp-main-action">
                            <span id="otpVerifyButtonLabel">Verify Code</span>
                            <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M5 12h14"></path>
                                <path d="m13 6 6 6-6 6"></path>
                            </svg>
                        </button>

                        <div class="sari-otp-status" aria-label="Verification progress">
                            <div class="sari-otp-status-node is-complete" data-otp-progress="sent">
                                <span class="sari-otp-status-dot">✓</span>
                                <span class="sari-otp-status-label">Code sent</span>
                            </div>
                            <span class="sari-otp-status-line" data-otp-line="sent"></span>
                            <div class="sari-otp-status-node" data-otp-progress="verifying">
                                <span class="sari-otp-status-dot">2</span>
                                <span class="sari-otp-status-label">Verifying</span>
                            </div>
                            <span class="sari-otp-status-line" data-otp-line="verified"></span>
                            <div class="sari-otp-status-node" data-otp-progress="verified">
                                <span class="sari-otp-status-dot">3</span>
                                <span class="sari-otp-status-label">Verified</span>
                            </div>
                        </div>

                        <button type="button" data-back class="sari-register-action sari-register-secondary sari-otp-back">
                            <svg class="sari-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M19 12H5"></path>
                                <path d="m11 18-6-6 6-6"></path>
                            </svg>
                            <span>Back</span>
                        </button>
                    </div>

                    <div id="otpVerifyingPanel" class="sari-otp-panel sari-otp-verifying" hidden aria-live="polite">
                        <div class="sari-otp-spinner" aria-hidden="true"></div>
                        <h2>Verifying your code</h2>
                        <p class="sari-otp-copy">Please wait while we securely verify your email.</p>
                    </div>

                    <div id="otpSuccessPanel" class="sari-otp-panel sari-otp-success" hidden aria-live="polite">
                        <div class="sari-otp-success-animation-wrap" aria-hidden="true">
                            <div id="otpSuccessGlow" class="sari-otp-success-glow"></div>
                            <div id="otpSuccessParticles" class="sari-otp-success-particles"></div>

                            <div id="otpSuccessAnimation" class="sari-otp-success-animation">
                                <svg
                                    class="sari-otp-success-svg"
                                    viewBox="0 0 72 72"
                                    fill="none"
                                >
                                    <circle
                                        class="sari-otp-success-ring"
                                        cx="36"
                                        cy="36"
                                        r="29"
                                    ></circle>

                                    <path
                                        id="otpSuccessCheck"
                                        class="sari-otp-success-check"
                                        d="M23.5 36.5 31.5 44.5 49.5 26.5"
                                    ></path>
                                </svg>
                            </div>
                        </div>

                        <h2>Verification successful</h2>
                        <p class="sari-otp-copy">Your email is verified. Submitting your registration now.</p>
                    </div>
                </div>
            </section>

            <button id="finalRegistrationSubmit" type="submit" hidden>Submit</button>
        </form>

            </div>
        </div>

        {{-- ACCOUNT SHORTCUT --}}
        <p class="sari-signin-shortcut">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}">Sign in</a>
        </p>

        <p class="sari-register-footer">
            © {{ date('Y') }} SARI. All rights reserved.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationWizard');
    const allSteps = Array.from(document.querySelectorAll('[data-step]'));
    const roleSelect = document.getElementById('roleSelect');

    const progressSegments = document.getElementById('wizardProgressSegments');
    const stepTitle = document.getElementById('wizardStepTitle');
    const counter = document.getElementById('wizardCounter');

    let currentStepName = 'role';
    let addressLoaded = false;

    const stepTitles = {
        role: 'Choose your role',
        details: 'Your information',
        documents: 'Verification documents',
        credentials: 'Login credentials',
        otp: 'Email verification'
    };

    function selectedRole() {
        return roleSelect.value || '';
    }

    function flow() {
        return ['role', 'details', 'documents', 'credentials', 'otp'];
    }

    function activeStep() {
        return allSteps.find(step => step.dataset.step === currentStepName);
    }

    function updateProgress() {
        const steps = flow();
        const index = Math.max(0, steps.indexOf(currentStepName));

        stepTitle.textContent = stepTitles[currentStepName] || '';
        counter.textContent = 'Step ' + (index + 1) + ' of ' + steps.length;

        progressSegments.innerHTML = '';
        progressSegments.style.gridTemplateColumns = 'repeat(' + steps.length + ', minmax(0, 1fr))';

        steps.forEach(function (_, stepIndex) {
            const segment = document.createElement('span');
            segment.className = 'sari-progress-segment';

            if (stepIndex < index) {
                segment.classList.add('is-complete');
            } else if (stepIndex === index) {
                segment.classList.add('is-current');
            }

            progressSegments.appendChild(segment);
        });
    }

    function showStep(stepName) {
        const current = activeStep();
        const target = allSteps.find(step => step.dataset.step === stepName);

        if (!target || target === current) return;

        if (current) {
            current.classList.remove('is-entering');
            current.classList.add('is-leaving');

            window.setTimeout(function () {
                current.hidden = true;
                current.classList.remove('is-leaving');
            }, 180);
        }

        window.setTimeout(function () {
            target.hidden = false;
            target.classList.add('is-entering');
            currentStepName = stepName;
            updateProgress();

            const card = target.closest('.sari-register-card');

            if (card) {
                const cardTop = card.getBoundingClientRect().top + window.scrollY - 24;

                if (window.scrollY > cardTop + 120 || window.scrollY < cardTop - 120) {
                    window.scrollTo({
                        top: Math.max(0, cardTop),
                        behavior: 'smooth'
                    });
                }
            }

            if (stepName === 'details' && !addressLoaded) {
                addressLoaded = true;
                loadProvinces();
            }
        }, current ? 185 : 0);
    }

    function validationFieldLabel(field) {
        const directLabel = field.id
            ? document.querySelector('label[for="' + field.id + '"]')
            : null;
        const fieldWrapper = field.closest('.sari-input-with-icon')?.parentElement || field.parentElement;
        const nearbyLabel = fieldWrapper?.querySelector('label');
        const label = directLabel || nearbyLabel;

        return String(label?.textContent || 'This field')
            .replace(/\*/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function validationMessageFor(field) {
        const label = validationFieldLabel(field);

        if (field.validity.customError && field.validationMessage) {
            return field.validationMessage;
        }

        if (field.validity.valueMissing) {
            if (field.type === 'checkbox') {
                return 'Please confirm that you agree before continuing.';
            }
            return label + ' is required.';
        }

        if (field.validity.typeMismatch && field.type === 'email') {
            return 'Enter a valid email address, for example name@email.com.';
        }

        if (field.validity.patternMismatch && field.id === 'contactNumber') {
            return 'Enter exactly 11 digits starting with 09.';
        }

        if (field.validity.tooShort) {
            return label + ' must contain at least ' + field.minLength + ' characters.';
        }

        if (field.validity.tooLong) {
            return label + ' is too long.';
        }

        if (field.validity.rangeUnderflow || field.validity.rangeOverflow) {
            return label + ' is outside the allowed range.';
        }

        if (field.validationMessage) {
            return field.validationMessage;
        }

        return 'Please check ' + label.toLowerCase() + '.';
    }

    function validationAnchor(field) {
        if (field.type === 'checkbox') {
            return field.closest('label') || field;
        }

        return field.closest('.sari-input-with-icon') || field;
    }

    function errorIdFor(field) {
        const raw = field.id || field.name || 'field';
        return 'sari-error-' + raw.replace(/[^a-zA-Z0-9_-]/g, '-');
    }

    function clearFieldError(field) {
        if (!field) return;

        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');

        const errorId = errorIdFor(field);
        document.getElementById(errorId)?.remove();

        if (Object.prototype.hasOwnProperty.call(field.dataset, 'sariOriginalDescribedby')) {
            const original = field.dataset.sariOriginalDescribedby;
            if (original) {
                field.setAttribute('aria-describedby', original);
            } else {
                field.removeAttribute('aria-describedby');
            }
        }
    }

    function showFieldError(field, message = '') {
        if (!field) return;

        if (!Object.prototype.hasOwnProperty.call(field.dataset, 'sariOriginalDescribedby')) {
            field.dataset.sariOriginalDescribedby = field.getAttribute('aria-describedby') || '';
        }

        clearFieldError(field);

        const errorId = errorIdFor(field);
        const error = document.createElement('p');
        error.id = errorId;
        error.className = 'sari-inline-error';
        error.setAttribute('role', 'alert');
        error.textContent = message || validationMessageFor(field);

        const anchor = validationAnchor(field);
        anchor.insertAdjacentElement('afterend', error);

        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');

        const original = field.dataset.sariOriginalDescribedby || '';
        field.setAttribute(
            'aria-describedby',
            [original, errorId].filter(Boolean).join(' ')
        );
    }

    function syncAddressFieldValidity() {
        if (typeof manualMode !== 'undefined' && manualMode) return;

        const provinceField = document.getElementById('provinceSearch');
        const municipalityField = document.getElementById('municipalitySearch');
        const barangayField = document.getElementById('barangaySearch');
        const provinceValue = document.getElementById('provinceName')?.value || '';
        const municipalityValue = document.getElementById('municipalityName')?.value || '';
        const barangayValue = document.getElementById('barangayName')?.value || '';

        provinceField?.setCustomValidity(
            provinceValue ? '' : 'Please select a Province / Area from the suggestions.'
        );

        if (municipalityField && !municipalityField.disabled) {
            municipalityField.setCustomValidity(
                municipalityValue ? '' : 'Please select a City / Municipality from the suggestions.'
            );
        }

        if (barangayField && !barangayField.disabled) {
            barangayField.setCustomValidity(
                barangayValue ? '' : 'Please select a Barangay from the suggestions.'
            );
        }
    }

    function focusInvalidField(field) {
        const target = field.matches('[data-upload-input]')
            ? document.querySelector('[data-upload-zone="' + field.id + '"]')
            : field.matches('[data-profile-input]')
                ? profileImageBrowse
                : field;

        window.setTimeout(function () {
            target?.focus({ preventScroll: true });
            target?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 230);
    }

    function validateCurrentStep() {
        const step = activeStep();
        if (!step) return true;

        if (currentStepName === 'details') {
            syncAddressFieldValidity();
        }

        const fields = Array.from(
            step.querySelectorAll('input, select, textarea')
        ).filter(field => !field.disabled && field.type !== 'hidden');

        let firstInvalid = null;

        fields.forEach(function (field) {
            if (field.checkValidity()) {
                if (!field.matches('[data-upload-input]')) {
                    clearFieldError(field);
                }
                return;
            }

            if (!firstInvalid) firstInvalid = field;

            if (field.matches('[data-upload-input]')) {
                const parts = uploadElements(field.id);

                if (parts) {
                    parts.zone.classList.add('has-error');
                    parts.title.textContent = field.files?.length ? 'File not accepted' : 'File required';
                    parts.error.textContent = field.files?.length
                        ? validationMessageFor(field)
                        : 'Please add the required file.';
                    parts.error.hidden = false;
                }
            } else if (field.matches('[data-profile-input]')) {
                showProfileImageError(
                    field.files?.length
                        ? validationMessageFor(field)
                        : 'A Logistics / Sorting Center business image is required.'
                );
            } else {
                showFieldError(field);
            }
        });

        if (firstInvalid) {
            focusInvalidField(firstInvalid);
            return false;
        }

        return true;
    }

    function goNext() {
        if (!validateCurrentStep()) return;

        const steps = flow();
        const index = steps.indexOf(currentStepName);

        if (index >= 0 && index < steps.length - 1) {
            showStep(steps[index + 1]);
        }
    }

    function goBack() {
        const steps = flow();
        const index = steps.indexOf(currentStepName);

        if (index > 0) {
            showStep(steps[index - 1]);
        }
    }

    document.querySelectorAll('[data-next]').forEach(
        button => button.addEventListener('click', goNext)
    );

    document.querySelectorAll('[data-back]').forEach(
        button => button.addEventListener('click', goBack)
    );

    /* ROLE */
    const rolePreview = document.getElementById('rolePreview');
    const rolePreviewIcon = document.getElementById('rolePreviewIcon');
    const rolePreviewTitle = document.getElementById('rolePreviewTitle');
    const rolePreviewText = document.getElementById('rolePreviewText');
    const roleContinueButton = document.getElementById('roleContinueButton');
    const roleProcessToggle = document.getElementById('roleProcessToggle');
    const roleProcessToggleLabel = document.getElementById('roleProcessToggleLabel');
    const roleProcessPanel = document.getElementById('roleProcessPanel');
    const roleProcessTitle = document.getElementById('roleProcessTitle');
    const roleProcessSummary = document.getElementById('roleProcessSummary');
    const roleProcessSteps = document.getElementById('roleProcessSteps');

    const businessDetailsSection = document.getElementById('businessDetailsSection');
    const sellerLineOfBusinessGroup = document.getElementById('sellerLineOfBusinessGroup');
    const businessPermitGroup = document.getElementById('businessPermitGroup');
    const businessPermitLabel = document.getElementById('businessPermitLabel');
    const businessPermitHint = document.getElementById('businessPermitHint');
    const businessStepEyebrow = document.getElementById('businessStepEyebrow');
    const businessStepTitle = document.getElementById('businessStepTitle');
    const businessStepDescription = document.getElementById('businessStepDescription');

    const profileImageInput = document.getElementById('profileImageInput');
    const profileImageBrowse = document.getElementById('profileImageBrowse');
    const profileImageRemove = document.getElementById('profileImageRemove');
    const profileImageActions = document.getElementById('profileImageActions');
    const profileImagePreview = document.getElementById('profileImagePreview');
    const profileImagePreviewImage = document.getElementById('profileImagePreviewImage');
    const profileImageFallback = document.getElementById('profileImageFallback');
    const profileImageLabel = document.getElementById('profileImageLabel');
    const profileImageRequirement = document.getElementById('profileImageRequirement');
    const profileImageHelp = document.getElementById('profileImageHelp');
    const profileImageFile = document.getElementById('profileImageFile');
    const profileImageError = document.getElementById('profileImageError');

    function setRoleProcessOpen(open) {
        if (!roleProcessToggle || !roleProcessPanel) return;

        const isOpen = Boolean(open);
        roleProcessToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        roleProcessPanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        roleProcessPanel.classList.toggle('is-open', isOpen);

        if (roleProcessToggleLabel) {
            roleProcessToggleLabel.textContent = isOpen ? 'Hide steps' : 'View steps';
        }
    }

    roleProcessToggle?.addEventListener('click', function () {
        const open = roleProcessToggle.getAttribute('aria-expanded') === 'true';
        setRoleProcessOpen(!open);
    });

    roleProcessToggle?.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setRoleProcessOpen(false);
            roleProcessToggle.focus();
        }
    });

    function syncRoleRequirements() {
        const role = selectedRole();
        const isSeller = role === 'seller';
        const isLogistics = role === 'logistics';
        const needsBusiness = isSeller || isLogistics;

        document.querySelectorAll('[data-business-required]').forEach(function (field) {
            field.disabled = !needsBusiness;
            field.required = needsBusiness;
        });

        document.querySelectorAll('[data-seller-only-required]').forEach(function (field) {
            field.disabled = !isSeller;
            field.required = isSeller;
        });

        document.querySelectorAll('[data-business-permit-required]').forEach(function (field) {
            field.disabled = !needsBusiness;
            field.required = needsBusiness;
        });

        businessDetailsSection.hidden = !needsBusiness;
        sellerLineOfBusinessGroup.hidden = !isSeller;
        businessPermitGroup.hidden = !needsBusiness;

        const requiresProfileImage = isLogistics;
        profileImageInput.required = requiresProfileImage;

        if (isLogistics) {
            profileImageLabel.textContent = 'Logistics / Business Photo';
            profileImageRequirement.textContent = '* Required';
            profileImageHelp.textContent = 'Upload a clear Logistics / Sorting Center image.';
        } else if (isSeller) {
            profileImageLabel.textContent = 'Store / Business Photo';
            profileImageRequirement.textContent = '(Optional)';
            profileImageHelp.textContent = 'Optional store image.';
        } else {
            profileImageLabel.textContent = 'Profile Photo';
            profileImageRequirement.textContent = '(Optional)';
            profileImageHelp.textContent = 'Optional profile image.';
        }

        if (!requiresProfileImage && !profileImageInput.files?.length) {
            profileImageInput.setCustomValidity('');
            clearProfileImageError();
        }

        if (!needsBusiness) {
            resetUploadField('businessPermitInput');
        }

        if (isLogistics) {
            businessStepEyebrow.textContent = 'Logistics / Sorting Center Requirement';
            businessStepTitle.textContent = 'Logistics Business Information';
            businessStepDescription.textContent =
                'Enter the registered Logistics or Sorting Center business name used for your application.';
            businessPermitLabel.textContent = 'Business / DTI Permit *';
            businessPermitHint.textContent =
                'Upload a clear copy of your business registration or DTI permit.';
        } else {
            businessStepEyebrow.textContent = 'Seller Requirement';
            businessStepTitle.textContent = 'Business Information';
            businessStepDescription.textContent =
                'Enter the registered business information for your SARI seller account.';
            businessPermitLabel.textContent = 'Business Permit *';
            businessPermitHint.textContent =
                'Upload a clear copy of your business permit.';
        }

        const details = {
            buyer: {
                title: 'Buyer',
                text: 'Buyer registration selected.',
                summary: 'Complete your account details, verify your email, then wait for administrator review.',
                steps: [
                    ['Personal information', 'Basic identity and contact details.'],
                    ['Address', 'Province, city / municipality, barangay, and exact address.'],
                    ['Valid ID', 'Upload one accepted verification document.'],
                    ['Login credentials', 'Create your password and accept the terms.'],
                    ['Email verification', 'Enter the 6-digit code sent to your email.'],
                    ['Administrator review', 'Your application is reviewed before account access.']
                ]
            },
            seller: {
                title: 'Seller',
                text: 'Seller registration selected.',
                summary: 'Provide your personal and business information, verify your email, then submit for administrator review.',
                steps: [
                    ['Personal information', 'Basic identity and contact details.'],
                    ['Address', 'Business owner location and exact address.'],
                    ['Business information', 'Business name and line of business.'],
                    ['Verification documents', 'Valid ID and Business Permit.'],
                    ['Login credentials', 'Create your password and accept the terms.'],
                    ['Email verification', 'Enter the 6-digit code sent to your email.'],
                    ['Administrator review', 'Your Seller application is reviewed before activation.']
                ]
            },
            logistics: {
                title: 'Logistics / Sorting Center',
                text: 'Logistics registration selected.',
                summary: 'Complete your Logistics profile and verification requirements before administrator approval.',
                steps: [
                    ['Personal information', 'Representative identity and contact details.'],
                    ['Address', 'Sorting Center location and exact address.'],
                    ['Business information', 'Registered Logistics / Sorting Center name.'],
                    ['Verification documents', 'Valid ID and Business / DTI Permit.'],
                    ['Login credentials', 'Create your password and accept the terms.'],
                    ['Email verification', 'Enter the 6-digit code sent to your email.'],
                    ['Administrator review', 'Your Logistics application is reviewed before activation.']
                ]
            },
            rider: {
                title: 'Rider',
                text: 'You will choose a Logistics provider next.',
                summary: 'Riders apply directly to a selected Logistics / Sorting Center instead of the administrator registration flow.',
                steps: [
                    ['Choose Logistics', 'Select the Logistics / Sorting Center you want to join.'],
                    ['Rider application', 'Provide personal, address, vehicle, and document details.'],
                    ['Login credentials', 'Create the credentials for your Rider account.'],
                    ['Logistics review', 'Your selected Logistics provider reviews the application.'],
                    ['Rider activation', 'Approved Riders can access the Rider dashboard.']
                ]
            }
        };

        if (!details[role]) {
            rolePreview.hidden = true;
            roleContinueButton.disabled = true;
            rolePreviewIcon.innerHTML = '';
            setRoleProcessOpen(false);
            roleProcessSteps.innerHTML = '';
            return;
        }

        setRoleProcessOpen(false);

        roleProcessTitle.textContent = details[role].title + ' registration';
        roleProcessSummary.textContent = details[role].summary;
        roleProcessSteps.innerHTML = '';
        roleProcessSteps.style.setProperty(
            '--role-process-columns',
            String(Math.min(details[role].steps.length, 7))
        );

        details[role].steps.forEach(function (step, index) {
            const item = document.createElement('li');
            item.className = 'sari-role-process-step';

            const top = document.createElement('span');
            top.className = 'sari-role-process-step-top';

            const number = document.createElement('span');
            number.className = 'sari-role-process-number';
            number.textContent = String(index + 1);

            const title = document.createElement('span');
            title.className = 'sari-role-process-step-title';
            title.textContent = step[0];

            const copy = document.createElement('span');
            copy.className = 'sari-role-process-step-copy';
            copy.textContent = step[1];

            top.appendChild(number);
            item.appendChild(top);
            item.appendChild(title);
            item.appendChild(copy);
            roleProcessSteps.appendChild(item);
        });

        const selectedCard = document.querySelector('[data-role-option="' + role + '"]');
        const selectedIcon = selectedCard?.querySelector('.sari-role-icon svg');

        rolePreviewIcon.innerHTML = selectedIcon ? selectedIcon.outerHTML : '';
        rolePreviewTitle.textContent = details[role].title;
        rolePreviewText.textContent = details[role].text;
        rolePreview.hidden = false;
        roleContinueButton.disabled = false;
    }

    function syncRoleCards() {
        document.querySelectorAll('[data-role-option]').forEach(function (button) {
            const isSelected = button.dataset.roleOption === roleSelect.value;
            button.classList.toggle('is-selected', isSelected);
            button.setAttribute('aria-checked', isSelected ? 'true' : 'false');
        });
    }

    document.querySelectorAll('[data-role-option]').forEach(function (button) {
        button.addEventListener('click', function () {
            const role = button.dataset.roleOption || '';
            roleSelect.value = role;
            roleSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    roleSelect.addEventListener('change', function () {
        syncRoleRequirements();
        syncRoleCards();
        updateProgress();
    });

    roleContinueButton.addEventListener('click', function () {
        if (!roleSelect.value) {
            roleSelect.reportValidity();
            return;
        }

        if (roleSelect.value === 'rider') {
            window.location.href = @json(route('rider.logistics.index'));
            return;
        }

        showStep('details');
    });

    /* PERSONAL VALIDATION */
    document.querySelectorAll('[data-person-name]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value
                .replace(/[^\p{L}\s.'-]/gu, '')
                .replace(/\s{2,}/g, ' ');
        });
    });

    const middleInitial = document.getElementById('middleInitial');

    middleInitial.addEventListener('input', function () {
        this.value = this.value
            .replace(/[^\p{L}]/gu, '')
            .slice(0, 1)
            .toUpperCase();
    });

    const contactNumber = document.getElementById('contactNumber');
    const contactHint = document.getElementById('contactHint');

    function syncPhone() {
        contactNumber.value = contactNumber.value.replace(/\D/g, '').slice(0, 11);

        if (!contactNumber.value) {
            contactHint.textContent = '11 digits only, starting with 09.';
            contactHint.className = 'sari-field-hint';
            contactNumber.setCustomValidity('');
            contactHint.style.color = '#938a80';
            return;
        }

        const valid = /^09\d{9}$/.test(contactNumber.value);

        if (valid) {
            contactHint.textContent = 'Valid Philippine mobile number.';
            contactHint.className = 'sari-field-hint';
            contactHint.style.color = '#56816a';
            contactNumber.setCustomValidity('');
        } else {
            contactHint.textContent = 'Enter exactly 11 digits starting with 09.';
            contactHint.className = 'sari-field-hint';
            contactHint.style.color = '#b25a5a';
            contactNumber.setCustomValidity(
                'Enter an 11-digit Philippine mobile number starting with 09.'
            );
        }
    }

    contactNumber.addEventListener('input', function () {
        syncPhone();
        if (contactNumber.dataset.sariTouched === 'true') {
            contactNumber.checkValidity() ? clearFieldError(contactNumber) : showFieldError(contactNumber);
        }
    });

    const birthday = document.getElementById('birthday');
    const agePreview = document.getElementById('agePreview');

    const ageRequirement = document.getElementById('ageRequirement');

    function calculateAge() {
        if (!birthday.value) {
            agePreview.value = '';
            birthday.setCustomValidity('');
            if (ageRequirement) {
                ageRequirement.textContent = 'You must be 18 years old or older to register.';
                ageRequirement.style.color = '#938a80';
            }
            return;
        }

        const birth = new Date(birthday.value + 'T00:00:00');
        const today = new Date();

        let age = today.getFullYear() - birth.getFullYear();

        if (
            today.getMonth() < birth.getMonth()
            || (
                today.getMonth() === birth.getMonth()
                && today.getDate() < birth.getDate()
            )
        ) {
            age--;
        }

        agePreview.value = Math.max(0, age);

        if (age < 18) {
            birthday.setCustomValidity('You must be 18 years old or older to register.');
            if (ageRequirement) {
                ageRequirement.textContent = 'Registration is available to users age 18 and above only.';
                ageRequirement.style.color = '#b25a5a';
            }
        } else {
            birthday.setCustomValidity('');
            if (ageRequirement) {
                ageRequirement.textContent = 'Age requirement met.';
                ageRequirement.style.color = '#56816a';
            }
        }
    }

    birthday.addEventListener('input', function () {
        calculateAge();
        if (birthday.dataset.sariTouched === 'true') {
            birthday.checkValidity() ? clearFieldError(birthday) : showFieldError(birthday);
        }
    });
    birthday.addEventListener('change', function () {
        calculateAge();
        birthday.dataset.sariTouched = 'true';
        birthday.checkValidity() ? clearFieldError(birthday) : showFieldError(birthday);
    });

    /* BUSINESS */
    const businessName = document.getElementById('businessName');

    businessName.addEventListener('input', function () {
        this.value = this.value
            .replace(/[^\p{L}\p{N}\s.&'()\-]/gu, '')
            .replace(/\s{2,}/g, ' ');
    });

    /* PROFILE / BUSINESS IMAGE */
    const profileImageAllowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    const profileImageAllowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const profileImageMaxBytes = 5 * 1024 * 1024;
    let profileImageObjectUrl = null;

    function clearProfileImageError() {
        profileImagePreview?.classList.remove('is-invalid');

        if (profileImageError) {
            profileImageError.textContent = '';
            profileImageError.hidden = true;
        }
    }

    function showProfileImageError(message) {
        profileImagePreview?.classList.add('is-invalid');

        if (profileImageError) {
            profileImageError.textContent = message;
            profileImageError.hidden = false;
        }
    }

    function setProfileDropEmptyState() {
        profileImagePreview?.classList.remove('has-image');

        if (profileImagePreviewImage) {
            profileImagePreviewImage.src = '';
            profileImagePreviewImage.hidden = true;
        }

        if (profileImageFallback) {
            profileImageFallback.hidden = false;
        }

        if (profileImageActions) {
            profileImageActions.hidden = true;
        }

        if (profileImageFile) {
            profileImageFile.textContent = '';
            profileImageFile.hidden = true;
        }
    }

    function resetProfileImagePreview(clearInput = true) {
        if (profileImageObjectUrl) {
            URL.revokeObjectURL(profileImageObjectUrl);
            profileImageObjectUrl = null;
        }

        if (clearInput && profileImageInput) {
            profileImageInput.value = '';
        }

        setProfileDropEmptyState();
        profileImageInput?.setCustomValidity('');
        clearProfileImageError();
    }

    function profileImageExtension(fileName) {
        const parts = String(fileName || '').toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function validateProfileImage(file) {
        if (!file) {
            resetProfileImagePreview(false);

            if (profileImageInput?.required) {
                profileImageInput.setCustomValidity(
                    'A Logistics / Sorting Center business image is required.'
                );
                showProfileImageError(
                    'A Logistics / Sorting Center business image is required.'
                );
                return false;
            }

            return true;
        }

        const extension = profileImageExtension(file.name);
        const type = String(file.type || '').toLowerCase();

        if (
            !profileImageAllowedExtensions.includes(extension)
            || !profileImageAllowedTypes.includes(type)
        ) {
            profileImageInput.value = '';
            setProfileDropEmptyState();
            profileImageInput.setCustomValidity(
                'Choose a JPG, JPEG, PNG, or WEBP image.'
            );
            showProfileImageError(
                'Choose a JPG, JPEG, PNG, or WEBP image.'
            );
            return false;
        }

        if (file.size > profileImageMaxBytes) {
            profileImageInput.value = '';
            setProfileDropEmptyState();
            profileImageInput.setCustomValidity(
                'Profile image must not be larger than 5 MB.'
            );
            showProfileImageError(
                'Profile image must not be larger than 5 MB.'
            );
            return false;
        }

        profileImageInput.setCustomValidity('');
        clearProfileImageError();

        if (profileImageObjectUrl) {
            URL.revokeObjectURL(profileImageObjectUrl);
        }

        profileImageObjectUrl = URL.createObjectURL(file);
        profileImagePreviewImage.src = profileImageObjectUrl;
        profileImagePreviewImage.hidden = false;
        profileImageFallback.hidden = true;
        profileImageActions.hidden = false;
        profileImagePreview.classList.add('has-image');

        if (profileImageFile) {
            profileImageFile.textContent = file.name;
            profileImageFile.hidden = true;
        }

        return true;
    }

    function assignProfileDroppedFile(file) {
        try {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            profileImageInput.files = transfer.files;
            return true;
        } catch (error) {
            console.warn('SARI profile upload: dropped file could not be assigned.', error);
            return false;
        }
    }

    function openProfilePicker() {
        if (!profileImageInput?.disabled) {
            profileImageInput.click();
        }
    }

    profileImagePreview?.addEventListener('click', function (event) {
        if (event.target.closest('.sari-profile-image-actions')) return;
        if (!profileImagePreview.classList.contains('has-image')) {
            openProfilePicker();
        }
    });

    profileImagePreview?.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        if (event.target.closest('.sari-profile-image-actions')) return;

        event.preventDefault();

        if (!profileImagePreview.classList.contains('has-image')) {
            openProfilePicker();
        }
    });

    profileImageBrowse?.addEventListener('click', function (event) {
        event.stopPropagation();
        openProfilePicker();
    });

    profileImageInput?.addEventListener('change', function () {
        validateProfileImage(profileImageInput.files?.[0] || null);
    });

    profileImageRemove?.addEventListener('click', function (event) {
        event.stopPropagation();
        resetProfileImagePreview(true);

        if (profileImageInput.required) {
            profileImageInput.setCustomValidity(
                'A Logistics / Sorting Center business image is required.'
            );
            showProfileImageError(
                'A Logistics / Sorting Center business image is required.'
            );
        }
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        profileImagePreview?.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (!profileImageInput?.disabled) {
                profileImagePreview.classList.add('is-dragging');
            }
        });
    });

    ['dragleave', 'dragend'].forEach(function (eventName) {
        profileImagePreview?.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
            profileImagePreview.classList.remove('is-dragging');
        });
    });

    profileImagePreview?.addEventListener('drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        profileImagePreview.classList.remove('is-dragging');

        if (profileImageInput?.disabled) return;

        const files = Array.from(event.dataTransfer?.files || []);

        if (files.length !== 1) {
            showProfileImageError('Drop one image only.');
            return;
        }

        const file = files[0];

        if (!assignProfileDroppedFile(file)) {
            showProfileImageError('Use Browse photo to select this image.');
            return;
        }

        validateProfileImage(profileImageInput.files?.[0] || null);
    });

    /* VERIFICATION DOCUMENT UPLOADS */
    const allowedUploadExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    const allowedUploadMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

    function fileExtension(fileName) {
        const parts = String(fileName || '').toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function uploadElements(inputId) {
        const input = document.getElementById(inputId);
        const zone = document.querySelector('[data-upload-zone="' + inputId + '"]');

        if (!input || !zone) return null;

        return {
            input,
            zone,
            title: zone.querySelector('[data-upload-title]'),
            file: zone.querySelector('[data-upload-file]'),
            error: zone.querySelector('[data-upload-error]'),
        };
    }

    function resetUploadField(inputId, clearInput = true) {
        const parts = uploadElements(inputId);
        if (!parts) return;

        if (clearInput) parts.input.value = '';

        parts.input.setCustomValidity('');
        parts.zone.classList.remove('is-dragging', 'has-file', 'has-error');
        parts.title.textContent = 'Drag file here';
        parts.file.textContent = '';
        parts.file.hidden = true;
        parts.error.textContent = '';
        parts.error.hidden = true;
    }

    function rejectUpload(parts, message) {
        parts.input.value = '';
        parts.input.setCustomValidity(message);
        parts.zone.classList.remove('is-dragging', 'has-file');
        parts.zone.classList.add('has-error');
        parts.title.textContent = 'File not accepted';
        parts.file.textContent = '';
        parts.file.hidden = true;
        parts.error.textContent = message;
        parts.error.hidden = false;
    }

    function acceptUpload(parts, file) {
        parts.input.setCustomValidity('');
        parts.zone.classList.remove('is-dragging', 'has-error');
        parts.zone.classList.add('has-file');
        parts.title.textContent = 'File ready';
        parts.file.textContent = file.name;
        parts.file.hidden = false;
        parts.error.textContent = '';
        parts.error.hidden = true;
    }

    function validateUploadFile(parts, file) {
        if (!file) {
            resetUploadField(parts.input.id, false);
            return false;
        }

        const extension = fileExtension(file.name);
        const validExtension = allowedUploadExtensions.includes(extension);
        const validMime = allowedUploadMimeTypes.includes(String(file.type || '').toLowerCase());

        if (!validExtension || !validMime) {
            rejectUpload(
                parts,
                'Only JPG, JPEG, PNG, or PDF files are allowed.'
            );
            return false;
        }

        acceptUpload(parts, file);
        return true;
    }

    function assignDroppedFile(input, file) {
        try {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            return true;
        } catch (error) {
            console.warn('SARI upload: browser prevented assigning the dropped file.', error);
            return false;
        }
    }

    function setupUploadField(inputId) {
        const parts = uploadElements(inputId);
        if (!parts) return;

        parts.zone.addEventListener('click', function () {
            if (!parts.input.disabled) parts.input.click();
        });

        parts.zone.addEventListener('keydown', function (event) {
            if (parts.input.disabled) return;

            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                parts.input.click();
            }
        });

        parts.input.addEventListener('change', function () {
            validateUploadFile(parts, parts.input.files?.[0] || null);
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            parts.zone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (!parts.input.disabled) {
                    parts.zone.classList.add('is-dragging');
                }
            });
        });

        ['dragleave', 'dragend'].forEach(function (eventName) {
            parts.zone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                parts.zone.classList.remove('is-dragging');
            });
        });

        parts.zone.addEventListener('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            parts.zone.classList.remove('is-dragging');

            if (parts.input.disabled) return;

            const droppedFiles = Array.from(event.dataTransfer?.files || []);

            if (droppedFiles.length !== 1) {
                rejectUpload(parts, 'Please upload one file only.');
                return;
            }

            const file = droppedFiles[0];

            if (!validateUploadFile(parts, file)) return;

            if (!assignDroppedFile(parts.input, file)) {
                rejectUpload(parts, 'Use Browse to select this file in your browser.');
                return;
            }

            validateUploadFile(parts, parts.input.files?.[0] || null);
        });
    }

    setupUploadField('idDocumentInput');
    setupUploadField('businessPermitInput');

    /* ADDRESS */
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

    function locationCode(item) {
        return String(item.code ?? item.psgc_code ?? item.psgcCode ?? item.id ?? '');
    }

    function locationName(item) {
        return String(item.name ?? item.area_name ?? item.areaName ?? item.label ?? '');
    }

    function setAddressStatus(type, text) {
        statusText.textContent = text;

        statusDot.classList.remove(
            'bg-[#d29b2d]',
            'bg-[#56816a]',
            'bg-[#b25a5a]'
        );

        statusDot.classList.add(
            type === 'ready'
                ? 'bg-[#56816a]'
                : type === 'error'
                    ? 'bg-[#b25a5a]'
                    : 'bg-[#d29b2d]'
        );
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Address request failed: ' + response.status);
        }

        return response.json();
    }

    function renderResults(container, items, searchValue, onSelect) {
        container.innerHTML = '';

        const query = searchValue.trim().toLocaleLowerCase();

        const matches = items
            .filter(item => locationName(item).toLocaleLowerCase().includes(query))
            .slice(0, 30);

        if (!matches.length) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-3 text-[8px] text-[#91887c]';
            empty.textContent = 'No matching location found.';
            container.appendChild(empty);
            container.hidden = false;
            return;
        }

        matches.forEach(function (item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className =
                'sari-location-option block w-full rounded-lg px-3 py-2.5 text-left text-[9px] text-[#403930]';
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
            if (!input.disabled) {
                renderResults(container, getItems(), input.value, onSelect);
            }
        });

        input.addEventListener('input', function () {
            if (!input.disabled) {
                renderResults(container, getItems(), input.value, onSelect);
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !container.hidden) {
                const first = container.querySelector('button');

                if (first) {
                    event.preventDefault();
                    first.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                }
            }
        });

        input.addEventListener('blur', function () {
            window.setTimeout(() => container.hidden = true, 120);
        });
    }

    async function loadProvinces() {
        setAddressStatus('loading', 'Loading Philippine address directory...');

        try {
            provinces = normalizeList(
                await fetchJson(@json(route('address.provinces')))
            );

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
            const url =
                @json(url('/address/provinces'))
                + '/'
                + encodeURIComponent(selectedProvinceCode)
                + '/municipalities';

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
            const url =
                @json(url('/address/municipalities'))
                + '/'
                + encodeURIComponent(selectedMunicipalityCode)
                + '/barangays';

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
        provinceSearch.setCustomValidity('');
        clearFieldError(provinceSearch);
        provinceResults.hidden = true;

        loadMunicipalities(locationCode(item));
    }

    function selectMunicipality(item) {
        municipalityCode.value = locationCode(item);
        municipalityName.value = locationName(item);
        municipalitySearch.value = locationName(item);
        municipalitySearch.setCustomValidity('');
        clearFieldError(municipalitySearch);
        municipalityResults.hidden = true;

        loadBarangays(locationCode(item));
    }

    function selectBarangay(item) {
        barangayCode.value = locationCode(item);
        barangayName.value = locationName(item);
        barangaySearch.value = locationName(item);
        barangaySearch.setCustomValidity('');
        clearFieldError(barangaySearch);
        barangayResults.hidden = true;
    }

    bindAutocomplete(
        provinceSearch,
        provinceResults,
        () => provinces,
        selectProvince
    );

    bindAutocomplete(
        municipalitySearch,
        municipalityResults,
        () => municipalities,
        selectMunicipality
    );

    bindAutocomplete(
        barangaySearch,
        barangayResults,
        () => barangays,
        selectBarangay
    );

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

        setAddressStatus(
            'error',
            'Online address directory unavailable — manual entry enabled.'
        );

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

    [manualProvince, manualMunicipality, manualBarangay].forEach(
        input => input.addEventListener('input', syncManualAddress)
    );

    form.addEventListener('invalid', function (event) {
        event.preventDefault();
    }, true);

    form.addEventListener('submit', function (event) {
        if (!otpVerified) {
            event.preventDefault();

            if (currentStepName === 'otp') {
                verifyOtpCode();
            } else if (currentStepName === 'credentials') {
                startOtpFlow();
            } else {
                showStep('credentials');
            }

            return;
        }

        syncPhone();
        calculateAge();
        syncAddressFieldValidity();

        if (manualMode) {
            syncManualAddress();
        }

        if (!form.checkValidity()) {
            event.preventDefault();

            const invalid = form.querySelector(':invalid');

            if (invalid) {
                const step = invalid.closest('[data-step]');

                if (step && step.dataset.step !== currentStepName) {
                    showStep(step.dataset.step);
                }

                window.setTimeout(function () {
                    if (invalid.matches('[data-upload-input]')) {
                        const parts = uploadElements(invalid.id);
                        if (parts) {
                            parts.zone.classList.add('has-error');
                            parts.title.textContent = invalid.files?.length ? 'File not accepted' : 'File required';
                            parts.error.textContent = invalid.files?.length
                                ? validationMessageFor(invalid)
                                : 'Please add the required file.';
                            parts.error.hidden = false;
                        }
                    } else if (invalid.matches('[data-profile-input]')) {
                        showProfileImageError(
                            invalid.files?.length
                                ? validationMessageFor(invalid)
                                : 'A Logistics / Sorting Center business image is required.'
                        );
                    } else {
                        showFieldError(invalid);
                    }

                    focusInvalidField(invalid);
                }, step && step.dataset.step !== currentStepName ? 260 : 30);
            }

            return;
        }
    });

    /* LIVE INLINE VALIDATION */
    Array.from(form.querySelectorAll('input, select, textarea'))
        .filter(field =>
            field.type !== 'hidden'
            && !field.matches('[data-upload-input]')
            && !field.matches('[data-profile-input]')
        )
        .forEach(function (field) {
            const refreshError = function () {
                if (field.checkValidity()) {
                    clearFieldError(field);
                } else if (field.dataset.sariTouched === 'true') {
                    showFieldError(field);
                }
            };

            field.addEventListener('blur', function () {
                field.dataset.sariTouched = 'true';
                refreshError();
            });

            field.addEventListener('input', refreshError);
            field.addEventListener('change', refreshError);
        });

    const serverErrors = @json($errors->messages());
    const serverFieldMap = {
        province_code: 'provinceSearch',
        province_name: 'provinceSearch',
        municipality_code: 'municipalitySearch',
        municipality_name: 'municipalitySearch',
        barangay_code: 'barangaySearch',
        barangay_name: 'barangaySearch',
        profile_image: 'profileImageInput',
        id_document: 'idDocumentInput',
        business_permit: 'businessPermitInput'
    };

    function findFieldByName(name) {
        const mappedId = serverFieldMap[name];
        if (mappedId) return document.getElementById(mappedId);

        return Array.from(form.elements).find(function (field) {
            return field.name === name;
        }) || null;
    }

    let firstServerErrorField = null;

    Object.entries(serverErrors || {}).forEach(function ([name, messages]) {
        const field = findFieldByName(name);
        const message = Array.isArray(messages) ? messages[0] : String(messages || 'Please check this field.');

        if (!field) return;
        if (!firstServerErrorField) firstServerErrorField = field;

        if (field.matches('[data-upload-input]')) {
            const parts = uploadElements(field.id);
            if (parts) {
                parts.zone.classList.add('has-error');
                parts.title.textContent = 'Please check this file';
                parts.error.textContent = message;
                parts.error.hidden = false;
            }
        } else if (field.matches('[data-profile-input]')) {
            showProfileImageError(message);
        } else {
            field.dataset.sariTouched = 'true';
            showFieldError(field, message);
        }
    });

    if (firstServerErrorField) {
        const errorStep = firstServerErrorField.closest('[data-step]');
        if (errorStep && errorStep.dataset.step !== currentStepName) {
            showStep(errorStep.dataset.step);
        }
    }



    /* ============================================================
       STRICT INPUT GUARDS — UI SAFETY + CLEAR FEEDBACK
       ============================================================ */
    function sanitizeLocationTyping(value) {
        return String(value || '')
            .replace(/[^\p{L}\s.'()\-]/gu, '')
            .replace(/\s{2,}/g, ' ');
    }

    function bindLocationTextGuard(input) {
        if (!input) return;

        input.addEventListener('beforeinput', function (event) {
            if (!event.data || event.inputType.startsWith('delete')) return;
            if (/^[\p{L}\s.'()\-]+$/u.test(event.data)) return;
            event.preventDefault();
        });

        input.addEventListener('input', function () {
            const cleaned = sanitizeLocationTyping(input.value);
            if (cleaned !== input.value) input.value = cleaned;
        });
    }

    [provinceSearch, municipalitySearch, barangaySearch].forEach(bindLocationTextGuard);
    [manualProvince, manualMunicipality, manualBarangay].forEach(bindLocationTextGuard);

    contactNumber.addEventListener('beforeinput', function (event) {
        if (!event.data || event.inputType.startsWith('delete')) return;
        if (/^\d+$/.test(event.data)) return;
        event.preventDefault();
    });

    contactNumber.addEventListener('paste', function () {
        window.setTimeout(syncPhone, 0);
    });

    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('passwordConfirmation');

    function syncPasswordConfirmation() {
        if (!passwordConfirmation) return;

        const hasConfirmation = passwordConfirmation.value.length > 0;
        const matches = password?.value === passwordConfirmation.value;

        passwordConfirmation.setCustomValidity(
            hasConfirmation && !matches ? 'Passwords do not match.' : ''
        );

        if (passwordConfirmation.dataset.sariTouched === 'true') {
            passwordConfirmation.checkValidity()
                ? clearFieldError(passwordConfirmation)
                : showFieldError(passwordConfirmation);
        }
    }

    password?.addEventListener('input', syncPasswordConfirmation);
    passwordConfirmation?.addEventListener('input', syncPasswordConfirmation);
    passwordConfirmation?.addEventListener('blur', function () {
        passwordConfirmation.dataset.sariTouched = 'true';
        syncPasswordConfirmation();
    });



    /* ============================================================
       EMAIL OTP — REAL BACKEND VERIFICATION THROUGH register.submit
       ============================================================ */
    const otpLaunchButton = document.getElementById('otpLaunchButton');
    const otpLaunchButtonLabel = document.getElementById('otpLaunchButtonLabel');
    const otpLaunchError = document.getElementById('otpLaunchError');
    const otpVerifyButton = document.getElementById('otpVerifyButton');
    const otpVerifyButtonLabel = document.getElementById('otpVerifyButtonLabel');
    const otpResendButton = document.getElementById('otpResendButton');
    const otpResendLabel = document.getElementById('otpResendLabel');
    const otpDestination = document.getElementById('otpDestination');
    const otpCodeGroup = document.getElementById('otpCodeGroup');
    const otpError = document.getElementById('otpError');
    const otpDigits = Array.from(document.querySelectorAll('[data-otp-digit]'));
    const otpEntryPanel = document.getElementById('otpEntryPanel');
    const otpVerifyingPanel = document.getElementById('otpVerifyingPanel');
    const otpSuccessPanel = document.getElementById('otpSuccessPanel');
    const otpSuccessAnimation = document.getElementById('otpSuccessAnimation');
    const otpSuccessGlow = document.getElementById('otpSuccessGlow');
    const otpSuccessParticles = document.getElementById('otpSuccessParticles');
    const otpSuccessCheck = document.getElementById('otpSuccessCheck');
    const finalRegistrationSubmit = document.getElementById('finalRegistrationSubmit');
    const emailField = form.querySelector('input[name="email"]');
    const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

    let otpVerified = @json(
        (bool) (
            session('registration_email_otp.verified_at')
            && session('registration_email_otp.email') === strtolower((string) old('email'))
        )
    );
    let otpResendTimer = null;
    let otpRequestBusy = false;

    function maskEmailForUi(email) {
        const value = String(email || '').trim();
        const parts = value.split('@');

        if (parts.length !== 2) return value;

        const local = parts[0];
        const domain = parts[1];
        const visible = local.slice(0, Math.min(2, local.length));
        const hiddenCount = Math.max(3, local.length - visible.length);

        return visible + '•'.repeat(hiddenCount) + '@' + domain;
    }

    function otpCodeValue() {
        return otpDigits.map(input => input.value).join('');
    }

    function resetOtpInputs() {
        otpDigits.forEach(input => {
            input.value = '';
        });

        otpCodeGroup?.classList.remove('has-error');

        if (otpError) {
            otpError.textContent = '';
        }
    }

    function showOtpError(message) {
        if (otpEntryPanel) otpEntryPanel.hidden = false;
        if (otpVerifyingPanel) otpVerifyingPanel.hidden = true;
        if (otpSuccessPanel) otpSuccessPanel.hidden = true;

        otpCodeGroup?.classList.add('has-error');

        if (otpError) {
            otpError.textContent = message || 'Please check the verification code.';
        }

        const firstEmpty = otpDigits.find(input => !input.value) || otpDigits[0];
        firstEmpty?.focus();
    }

    function clearOtpError() {
        otpCodeGroup?.classList.remove('has-error');

        if (otpError) {
            otpError.textContent = '';
        }
    }

    function setOtpProgress(state) {
        const sent = document.querySelector('[data-otp-progress="sent"]');
        const verifying = document.querySelector('[data-otp-progress="verifying"]');
        const verified = document.querySelector('[data-otp-progress="verified"]');
        const sentLine = document.querySelector('[data-otp-line="sent"]');
        const verifiedLine = document.querySelector('[data-otp-line="verified"]');

        [sent, verifying, verified].forEach(node => {
            node?.classList.remove('is-current', 'is-complete');
        });

        sentLine?.classList.remove('is-complete');
        verifiedLine?.classList.remove('is-complete');

        sent?.classList.add('is-complete');

        if (state === 'sent') {
            verifying?.classList.remove('is-current');
            return;
        }

        sentLine?.classList.add('is-complete');

        if (state === 'verifying') {
            verifying?.classList.add('is-current');
            return;
        }

        verifying?.classList.add('is-complete');
        verifiedLine?.classList.add('is-complete');
        verified?.classList.add('is-complete');

        const verifyingDot = verifying?.querySelector('.sari-otp-status-dot');
        const verifiedDot = verified?.querySelector('.sari-otp-status-dot');

        if (verifyingDot) verifyingDot.textContent = '✓';
        if (verifiedDot) verifiedDot.textContent = '✓';
    }

    function setButtonBusy(button, label, busy, busyText, idleText) {
        if (!button || !label) return;

        button.disabled = busy;
        button.classList.toggle('sari-button-busy', busy);
        label.textContent = busy ? busyText : idleText;

        const icon = button.querySelector('.sari-button-icon');
        if (icon) icon.hidden = busy;
    }

    function startOtpResendCountdown(seconds) {
        window.clearInterval(otpResendTimer);

        let remaining = Math.max(0, Number(seconds) || 0);

        function render() {
            if (!otpResendButton || !otpResendLabel) return;

            if (remaining > 0) {
                otpResendButton.disabled = true;
                otpResendLabel.textContent = 'Resend in ' + remaining + 's';
                remaining -= 1;
                return;
            }

            otpResendButton.disabled = false;
            otpResendLabel.textContent = 'Resend code';
            window.clearInterval(otpResendTimer);
        }

        render();
        otpResendTimer = window.setInterval(render, 1000);
    }

    function ajaxErrorMessage(data, fallback) {
        if (data?.errors?.otp?.[0]) return data.errors.otp[0];
        if (data?.errors?.email?.[0]) return data.errors.email[0];
        if (data?.message) return data.message;
        return fallback;
    }

    async function postRegistrationOtp(action, payload = {}) {
        const body = new FormData();
        body.append('_token', csrfToken);
        body.append('_registration_action', action);

        Object.entries(payload).forEach(([key, value]) => {
            body.append(key, value);
        });

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body,
        });

        let data = {};

        try {
            data = await response.json();
        } catch (error) {
            data = {};
        }

        if (!response.ok) {
            const requestError = new Error(
                ajaxErrorMessage(data, 'Unable to complete email verification right now.')
            );
            requestError.status = response.status;
            requestError.data = data;
            throw requestError;
        }

        return data;
    }

    async function startOtpFlow() {
        if (otpRequestBusy) return;

        if (otpVerified) {
            form.requestSubmit(finalRegistrationSubmit);
            return;
        }

        if (currentStepName !== 'credentials') {
            showStep('credentials');
            return;
        }

        if (!validateCurrentStep()) return;

        const email = String(emailField?.value || '').trim();

        if (!email) {
            showFieldError(emailField, 'Email Address is required.');
            focusInvalidField(emailField);
            return;
        }

        otpRequestBusy = true;
        otpLaunchError.hidden = true;
        otpLaunchError.textContent = '';

        setButtonBusy(
            otpLaunchButton,
            otpLaunchButtonLabel,
            true,
            'Sending code...',
            'Continue to Email Verification'
        );

        try {
            const data = await postRegistrationOtp('otp_send', { email });

            otpDestination.textContent = data.masked_email || maskEmailForUi(email);
            resetOtpInputs();
            setOtpProgress('sent');
            showStep('otp');
            startOtpResendCountdown(data.retry_after || 45);

            window.setTimeout(() => otpDigits[0]?.focus(), 240);
        } catch (error) {
            if (error.status === 429 && error.data?.retry_after) {
                otpDestination.textContent = maskEmailForUi(email);
                resetOtpInputs();
                setOtpProgress('sent');
                showStep('otp');
                startOtpResendCountdown(error.data.retry_after);
                window.setTimeout(() => otpDigits[0]?.focus(), 240);
            } else {
                otpLaunchError.textContent = error.message;
                otpLaunchError.hidden = false;

                if (error.data?.errors?.email?.[0] && emailField) {
                    emailField.dataset.sariTouched = 'true';
                    showFieldError(emailField, error.data.errors.email[0]);
                    focusInvalidField(emailField);
                }
            }
        } finally {
            otpRequestBusy = false;
            setButtonBusy(
                otpLaunchButton,
                otpLaunchButtonLabel,
                false,
                'Sending code...',
                'Continue to Email Verification'
            );
        }
    }

    function playOtpSuccessAnimation() {
        if (!otpSuccessPanel || !otpSuccessAnimation) {
            return Promise.resolve();
        }

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

        const heading = otpSuccessPanel.querySelector('h2');
        const copyText = otpSuccessPanel.querySelector('.sari-otp-copy');

        [
            otpSuccessAnimation,
            otpSuccessGlow,
            otpSuccessCheck,
            heading,
            copyText
        ].forEach(function (element) {
            element?.getAnimations().forEach(function (animation) {
                animation.cancel();
            });
        });

        if (otpSuccessParticles) {
            otpSuccessParticles.innerHTML = '';
        }

        if (reducedMotion) {
            if (otpSuccessCheck) {
                otpSuccessCheck.style.strokeDashoffset = '0';
            }

            if (heading) {
                heading.style.opacity = '1';
                heading.style.transform = 'none';
            }

            if (copyText) {
                copyText.style.opacity = '1';
                copyText.style.transform = 'none';
            }

            return new Promise(function (resolve) {
                window.setTimeout(resolve, 650);
            });
        }

        otpSuccessAnimation.animate(
            [
                { opacity: 0, transform: 'scale(.55) rotate(-7deg)' },
                { opacity: 1, transform: 'scale(1.08) rotate(2deg)', offset: .58 },
                { opacity: 1, transform: 'scale(1) rotate(0deg)' }
            ],
            {
                duration: 620,
                easing: 'cubic-bezier(.18,.89,.32,1.28)',
                fill: 'both'
            }
        );

        if (otpSuccessGlow) {
            otpSuccessGlow.animate(
                [
                    { opacity: 0, transform: 'scale(.62)' },
                    { opacity: .85, transform: 'scale(1.18)', offset: .45 },
                    { opacity: 0, transform: 'scale(1.48)' }
                ],
                {
                    duration: 860,
                    easing: 'cubic-bezier(.22,1,.36,1)',
                    fill: 'both'
                }
            );
        }

        if (otpSuccessCheck) {
            otpSuccessCheck.style.strokeDashoffset = '30';

            otpSuccessCheck.animate(
                [
                    { strokeDashoffset: 30 },
                    { strokeDashoffset: 0 }
                ],
                {
                    duration: 430,
                    delay: 310,
                    easing: 'cubic-bezier(.65,0,.35,1)',
                    fill: 'forwards'
                }
            );
        }

        const colors = [
            '#d48f08',
            '#efb93f',
            '#f6d987',
            '#5d4a28'
        ];

        if (otpSuccessParticles) {
            const particleCount = 14;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('span');
                particle.className = 'sari-otp-success-particle';

                const angle = (Math.PI * 2 * i / particleCount)
                    + ((Math.random() - .5) * .22);

                const distance = 45 + Math.random() * 20;
                const x = Math.cos(angle) * distance;
                const y = Math.sin(angle) * distance;
                const size = 3 + Math.random() * 3;

                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.background = colors[i % colors.length];

                otpSuccessParticles.appendChild(particle);

                const particleAnimation = particle.animate(
                    [
                        {
                            opacity: 0,
                            transform:
                                'translate(-50%, -50%) translate(0, 0) scale(.3)'
                        },
                        {
                            opacity: 1,
                            transform:
                                'translate(-50%, -50%) translate('
                                + (x * .38) + 'px, '
                                + (y * .38) + 'px) scale(1)',
                            offset: .32
                        },
                        {
                            opacity: 0,
                            transform:
                                'translate(-50%, -50%) translate('
                                + x + 'px, '
                                + y + 'px) scale(.25)'
                        }
                    ],
                    {
                        duration: 760 + Math.random() * 180,
                        delay: 330 + Math.random() * 90,
                        easing: 'cubic-bezier(.17,.67,.37,1)',
                        fill: 'forwards'
                    }
                );

                particleAnimation.addEventListener(
                    'finish',
                    function () {
                        particle.remove();
                    },
                    { once: true }
                );
            }
        }

        heading?.animate(
            [
                { opacity: 0, transform: 'translateY(9px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ],
            {
                duration: 420,
                delay: 690,
                easing: 'cubic-bezier(.22,1,.36,1)',
                fill: 'forwards'
            }
        );

        copyText?.animate(
            [
                { opacity: 0, transform: 'translateY(7px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ],
            {
                duration: 420,
                delay: 800,
                easing: 'cubic-bezier(.22,1,.36,1)',
                fill: 'forwards'
            }
        );

        return new Promise(function (resolve) {
            // Keep the completed success state visible long enough to be noticed.
            window.setTimeout(resolve, 1850);
        });
    }

    async function verifyOtpCode() {
        if (otpRequestBusy || otpVerified) return;

        const code = otpCodeValue();

        if (!/^\d{6}$/.test(code)) {
            showOtpError('Enter the complete 6-digit verification code.');
            return;
        }

        otpRequestBusy = true;
        clearOtpError();
        setOtpProgress('verifying');

        if (otpEntryPanel) otpEntryPanel.hidden = true;
        if (otpVerifyingPanel) otpVerifyingPanel.hidden = false;
        if (otpSuccessPanel) otpSuccessPanel.hidden = true;

        setButtonBusy(
            otpVerifyButton,
            otpVerifyButtonLabel,
            true,
            'Verifying...',
            'Verify Code'
        );

        try {
            await postRegistrationOtp('otp_verify', {
                email: String(emailField?.value || '').trim(),
                otp: code,
            });

            otpVerified = true;
            setOtpProgress('verified');

            if (otpEntryPanel) otpEntryPanel.hidden = true;
            if (otpVerifyingPanel) otpVerifyingPanel.hidden = true;
            if (otpSuccessPanel) otpSuccessPanel.hidden = false;

            // Let the browser paint the success panel first, then run the
            // crisp JS check animation before the final form submission.
            await new Promise(function (resolve) {
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(resolve);
                });
            });

            try {
                await playOtpSuccessAnimation();
            } catch (animationError) {
                // Decorative animation must never block registration.
                await new Promise(function (resolve) {
                    window.setTimeout(resolve, 650);
                });
            }

            form.requestSubmit(finalRegistrationSubmit);
        } catch (error) {
            setOtpProgress('sent');
            showOtpError(error.message);
        } finally {
            otpRequestBusy = false;
            setButtonBusy(
                otpVerifyButton,
                otpVerifyButtonLabel,
                false,
                'Verifying...',
                'Verify Code'
            );
        }
    }

    async function resendOtpCode() {
        if (otpRequestBusy || otpResendButton?.disabled) return;

        const email = String(emailField?.value || '').trim();

        otpRequestBusy = true;
        otpResendButton.disabled = true;
        otpResendLabel.textContent = 'Sending...';
        clearOtpError();

        try {
            const data = await postRegistrationOtp('otp_send', { email });

            otpDestination.textContent = data.masked_email || maskEmailForUi(email);
            resetOtpInputs();
            setOtpProgress('sent');
            startOtpResendCountdown(data.retry_after || 45);
            otpDigits[0]?.focus();
        } catch (error) {
            if (error.status === 429 && error.data?.retry_after) {
                startOtpResendCountdown(error.data.retry_after);
            } else {
                otpResendButton.disabled = false;
                otpResendLabel.textContent = 'Resend code';
                showOtpError(error.message);
            }
        } finally {
            otpRequestBusy = false;
        }
    }

    otpLaunchButton?.addEventListener('click', startOtpFlow);
    otpVerifyButton?.addEventListener('click', verifyOtpCode);
    otpResendButton?.addEventListener('click', resendOtpCode);

    otpDigits.forEach(function (input, index) {
        input.addEventListener('beforeinput', function (event) {
            if (!event.data || event.inputType.startsWith('delete')) return;
            if (/^\d+$/.test(event.data)) return;
            event.preventDefault();
        });

        input.addEventListener('input', function () {
            input.value = input.value.replace(/\D/g, '').slice(-1);
            clearOtpError();

            if (input.value && index < otpDigits.length - 1) {
                otpDigits[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Backspace' && !input.value && index > 0) {
                otpDigits[index - 1].focus();
                return;
            }

            if (event.key === 'ArrowLeft' && index > 0) {
                event.preventDefault();
                otpDigits[index - 1].focus();
            }

            if (event.key === 'ArrowRight' && index < otpDigits.length - 1) {
                event.preventDefault();
                otpDigits[index + 1].focus();
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                verifyOtpCode();
            }
        });

        input.addEventListener('paste', function (event) {
            const pasted = String(event.clipboardData?.getData('text') || '')
                .replace(/\D/g, '')
                .slice(0, 6);

            if (!pasted) return;

            event.preventDefault();

            otpDigits.forEach((digit, digitIndex) => {
                digit.value = pasted[digitIndex] || '';
            });

            clearOtpError();

            const nextEmpty = otpDigits.find(digit => !digit.value);
            (nextEmpty || otpDigits[otpDigits.length - 1])?.focus();
        });
    });

    emailField?.addEventListener('input', function () {
        otpVerified = false;
    });

    syncRoleRequirements();
    syncRoleCards();
    calculateAge();
    syncPhone();
    updateProgress();
});
</script>

@endsection