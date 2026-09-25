@extends('layouts.seller')

@section('title', 'Add Product — SARI')
@section('page-title', 'Add Product')

@push('styles')
{{-- Add Product design merged inline to avoid a separate public CSS request. --}}
<style id="sariSellerAddProductInlineStyles">
    .seller-create-page {
        --sari-gold: #d59617;
        --sari-gold-strong: #b97805;
        --sari-gold-soft: #fff8e8;
        --sari-gold-soft-2: #fffaf2;
        --sari-ink: #111827;
        --sari-muted: #6b7280;
        --sari-line: #e5e7eb;
        --sari-line-strong: #d7dde5;
        --sari-soft: #f8fafc;
        --sari-soft-2: #f3f6fa;
        --sari-blue-soft: #eff6ff;
        --sari-blue: #2563eb;
        --sari-green-soft: #ecfdf3;
        --sari-green: #15803d;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        color: var(--sari-ink);
    }

    .seller-create-topbar {
        position: relative;
        overflow: visible;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 72px;
        margin: 0 0 16px;
        padding: 2px 2px 16px;
        border: 0;
        border-bottom: 1px solid #e7ebf0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        backdrop-filter: none;
    }

    .seller-create-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 0;
    }

    .seller-create-heading-icon {
        display: grid;
        width: 50px;
        height: 50px;
        flex: 0 0 50px;
        place-items: center;
        border: 1px solid #efd7a3;
        border-radius: 16px;
        background: #fff9eb;
        color: #cf8906;
        box-shadow: 0 5px 14px rgba(183, 121, 5, .06);
    }

    .seller-create-heading-icon svg {
        width: 21px;
        height: 21px;
        stroke-width: 1.9;
    }

    .seller-create-heading-copy {
        min-width: 0;
    }

    .seller-create-heading-eyebrow {
        color: #b87605;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .16em;
        line-height: 1;
        text-transform: uppercase;
    }

    .seller-create-heading-title {
        margin-top: 6px;
        color: #111827;
        font-size: clamp(34px, 2.8vw, 44px);
        font-weight: 750;
        letter-spacing: -.045em;
        line-height: .98;
        white-space: nowrap;
    }

    .seller-create-heading-title span {
        color: #d18b08;
    }

    .seller-create-heading-back {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        gap: 5px;
        margin-top: 7px;
        color: #7c8592;
        font-size: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: color .12s ease;
    }

    .seller-create-heading-back:hover {
        color: #a56b05;
    }

    .seller-create-heading-back svg {
        width: 12px;
        height: 12px;
    }

    .seller-create-grid {
        display: grid;
        grid-template-columns: 190px minmax(0,1fr) minmax(230px,270px);
        gap: 16px;
        align-items: start;
        min-width: 0;
    }

    .seller-create-side,
    .seller-create-preview {
        position: sticky;
        top: 154px;
        min-width: 0;
        width: 100%;
    }

    .seller-create-preview {
        align-self: start;
        z-index: 1;
    }

    .seller-create-side-card,
    .seller-create-preview-card,
    .seller-create-section {
        border: 1px solid var(--sari-line);
        background: #fff;
    }

    .seller-create-side-card,
    .seller-create-preview-card,
    .seller-create-section {
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15,23,42,.05);
    }

    .seller-create-side-card {
        padding: 12px;
    }

    .seller-create-side-title {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px 12px;
        border-bottom: 1px solid #eef2f6;
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
    }

    .seller-create-nav {
        display: grid;
        gap: 6px;
        margin-top: 10px;
    }

    .seller-create-nav button {
        width: 100%;
        border: 1px solid transparent;
        border-radius: 12px;
        background: transparent;
        padding: 11px 12px;
        color: #667180;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }

    .seller-create-nav button:hover,
    .seller-create-nav button.is-active {
        background: var(--sari-gold-soft-2);
        border-color: #f4dfad;
        color: #9b6505;
    }

    .seller-create-workspace {
        display: grid;
        gap: 16px;
        min-width: 0;
    }

    .seller-create-section {
        scroll-margin-top: 160px;
        min-width: 0;
        overflow: hidden;
        padding: 20px;
    }

    .seller-create-section-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
    }

    .seller-create-section-head > .seller-create-section-copy:first-child {
        padding-left: 0;
    }

    .seller-create-section-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border: 1px solid #efd39a;
        border-radius: 12px;
        background: #fff8e8;
        color: #c88605;
    }

    .seller-create-section-copy {
        min-width: 0;
        flex: 1;
    }

    .seller-create-section-copy h2 {
        font-size: 16px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #111827;
    }

    .seller-create-section-copy p {
        margin-top: 4px;
        color: var(--sari-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .seller-create-rule {
        flex: 1;
        height: 1px;
        margin-top: 20px;
        background: #e8edf3;
    }

    .seller-create-label {
        display: block;
        margin-bottom: 8px;
        color: #344054;
        font-size: 12px;
        font-weight: 600;
    }

    .seller-create-input,
    .seller-create-select,
    .seller-create-textarea {
        width: 100%;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        background: #fff;
        color: #1f2937;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .seller-create-input,
    .seller-create-select {
        height: 44px;
        padding: 0 14px;
        font-size: 12px;
    }

    .seller-create-textarea {
        min-height: 146px;
        resize: vertical;
        padding: 13px 14px;
        font-size: 12px;
        line-height: 1.7;
    }

    .seller-create-input:focus,
    .seller-create-select:focus,
    .seller-create-textarea:focus,
    .seller-variant-table input[type="text"]:focus,
    .seller-variant-table input[type="number"]:focus {
        border-color: #d49a18;
        box-shadow: 0 0 0 4px rgba(212,154,24,.10);
        background: #fff;
    }

    .seller-create-money-field {
        display: flex;
        width: 100%;
        height: 44px;
        overflow: hidden;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        background: #ffffff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .seller-create-money-field:focus-within {
        border-color: #d59617;
        box-shadow: 0 0 0 4px rgba(213,150,23,.10);
    }

    .seller-create-money-prefix {
        display: grid;
        width: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-right: 1px solid #e6eaf0;
        background: #ffffff;
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
    }

    .seller-create-money-input {
        min-width: 0;
        flex: 1;
        border: 0;
        background: transparent;
        padding: 0 13px;
        color: #1f2937;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 12px;
        font-weight: 500;
        outline: none;
    }

    .seller-create-money-input::placeholder {
        color: #9aa3af;
        opacity: 1;
    }


    .seller-category-picker {
        position: relative;
    }

    .seller-category-trigger {
        display: flex;
        width: 100%;
        height: 44px;
        align-items: center;
        gap: 10px;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        background: #ffffff;
        padding: 0 12px;
        color: #1f2937;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 11px;
        font-weight: 500;
        text-align: left;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .seller-category-trigger:hover {
        border-color: #cfd6df;
        background: #fcfcfd;
    }

    .seller-category-trigger[aria-expanded="true"] {
        border-color: #d59617;
        box-shadow: 0 0 0 4px rgba(213,150,23,.09);
    }

    .seller-category-trigger-icon {
        display: inline-flex;
        width: 19px;
        height: 19px;
        flex: 0 0 19px;
        align-items: center;
        justify-content: center;
        color: #c98505;
    }

    .seller-category-trigger-icon svg {
        width: 18px;
        height: 18px;
        stroke-width: 1.9;
    }

    .seller-category-trigger-text {
        min-width: 0;
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-category-trigger-chevron {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        color: #98a2b3;
        transition: transform .15s ease;
    }

    .seller-category-trigger[aria-expanded="true"] .seller-category-trigger-chevron {
        transform: rotate(180deg);
    }

    .seller-category-menu {
        position: relative;
        z-index: 20;
        width: 100%;
        max-height: 248px;
        margin-top: 7px;
        overflow-y: auto;
        border: 1px solid #e0e5eb;
        border-radius: 14px;
        background: #ffffff;
        padding: 6px;
        box-shadow: 0 12px 28px rgba(15,23,42,.08);
        animation: sellerCategoryMenuIn 120ms ease both;
        scrollbar-width: thin;
        scrollbar-color: #cfd5dd transparent;
    }

    @keyframes sellerCategoryMenuIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .seller-category-option {
        display: flex;
        width: 100%;
        min-height: 38px;
        align-items: center;
        gap: 9px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        padding: 8px 10px;
        color: #475467;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 10px;
        font-weight: 500;
        text-align: left;
        transition: background .12s ease, color .12s ease;
    }

    .seller-category-option:hover,
    .seller-category-option.is-selected {
        background: #fff8e8;
        color: #9a6708;
    }

    .seller-category-option svg {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
        color: #c98505;
    }

    .seller-create-field-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 14px;
    }

    .seller-create-field-grid--3 { grid-template-columns: repeat(3, minmax(0,1fr)); }
    .seller-create-field-grid--4 { grid-template-columns: repeat(4, minmax(0,1fr)); }
    .seller-create-span-2 { grid-column: span 2; }

    .seller-create-help {
        margin-top: 6px;
        color: #8b96a5;
        font-size: 10.5px;
        line-height: 1.55;
    }

    .seller-create-upload {
        display: grid;
        min-height: 162px;
        cursor: pointer;
        place-items: center;
        overflow: hidden;
        border: 1px dashed #cfd7e2;
        border-radius: 14px;
        background: #fafbfc;
        text-align: center;
        transition: border-color .15s ease, background .15s ease, transform .15s ease;
    }

    .seller-create-upload:hover {
        border-color: #d49a18;
        background: #fffdf8;
        transform: translateY(-1px);
    }

    .seller-create-upload img {
        width: 100%;
        height: 100%;
        min-height: 162px;
        object-fit: cover;
    }

    .seller-create-gallery {
        display: grid;
        grid-template-columns: repeat(6, minmax(0,1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .seller-create-gallery-thumb {
        aspect-ratio: 1;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .seller-create-gallery-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .seller-create-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 14px;
        padding: 14px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .seller-create-setting-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 12px;
        margin-top: 14px;
    }


    .seller-create-plain-icon {
        display: inline-flex;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        align-items: center;
        justify-content: center;
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .seller-create-plain-icon svg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }

    .seller-create-plain-icon--shipping {
        color: #16a34a;
    }

    .seller-create-plain-icon--cod {
        color: #2563eb;
    }

    .seller-create-plain-icon--flash {
        color: #ef4444;
    }

    .seller-create-label-with-icon {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        color: #344054;
        font-size: 12px;
        font-weight: 600;
    }

    .seller-create-setting-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        min-height: 96px;
        padding: 15px 16px;
        border: 1px solid #e2e7ed;
        border-radius: 16px;
        background: #ffffff;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .seller-create-setting-card:hover {
        border-color: #e2bd67;
        background: #fffdf8;
        box-shadow: 0 8px 22px rgba(15,23,42,.035);
    }

    .seller-create-setting-copy {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 10px;
    }

    .seller-create-setting-copy strong {
        display: block;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 700;
        color: #111827;
    }

    .seller-create-setting-copy span {
        display: block;
        margin-top: 4px;
        max-width: 260px;
        font-size: 10.5px;
        line-height: 1.6;
        color: #667085;
    }

    .seller-create-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        flex: 0 0 50px;
        border-radius: 999px;
        background: #d7dce2;
        box-shadow: inset 0 1px 2px rgba(15,23,42,.08);
        transition: background .18s ease;
    }

    .seller-create-toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 2px 8px rgba(15,23,42,.18);
        transition: transform .18s ease;
    }

    .peer:checked + .seller-create-toggle {
        background: #d59617;
    }

    .peer:checked + .seller-create-toggle::after {
        transform: translateX(22px);
    }

    .seller-variant-table-wrap {
        width: 100%;
        max-width: 100%;
        margin-top: 12px;
        overflow: hidden;
        border: 1px solid #e2e7ed;
        border-radius: 14px;
        background: #ffffff;
    }

    .seller-variant-table {
        width: 100%;
        min-width: 0;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .seller-variant-table th {
        padding: 9px 7px;
        background: #f8fafc;
        color: #667085;
        font-size: 8.5px;
        font-weight: 700;
        line-height: 1.2;
        text-align: left;
        white-space: nowrap;
    }

    .seller-variant-table td {
        min-width: 0;
        padding: 9px 7px;
        border-top: 1px solid #edf1f5;
        vertical-align: middle;
    }

    .seller-variant-table th:nth-child(1),
    .seller-variant-table td:nth-child(1) { width: 54px; }

    .seller-variant-table th:nth-child(2),
    .seller-variant-table td:nth-child(2) { width: 18%; }

    .seller-variant-table th:nth-child(3),
    .seller-variant-table td:nth-child(3) { width: 18%; }

    .seller-variant-table th:nth-child(4),
    .seller-variant-table td:nth-child(4) { width: 16%; }

    .seller-variant-table th:nth-child(5),
    .seller-variant-table td:nth-child(5) { width: 15%; }

    .seller-variant-table th:nth-child(6),
    .seller-variant-table td:nth-child(6) { width: 13%; }

    .seller-variant-table th:nth-child(7),
    .seller-variant-table td:nth-child(7) { width: 42px; }

    .seller-variant-table input[type="text"],
    .seller-variant-table input[type="number"] {
        width: 100%;
        min-width: 0;
        height: 36px;
        border: 1px solid #d8dee7;
        border-radius: 9px;
        background: #fff;
        padding: 0 8px;
        color: #344054;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 9px;
        outline: none;
    }

    .seller-variant-image-label {
        display: grid;
        width: 38px;
        height: 38px;
        cursor: pointer;
        place-items: center;
        overflow: hidden;
        border: 1px dashed #cbd2da;
        border-radius: 11px;
        background: #fbfcfd;
        color: #7b8490;
    }

    .seller-variant-image-label img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .seller-create-button {
        display: inline-flex;
        height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        background: #fff;
        padding: 0 14px;
        color: #374151;
        font-size: 11px;
        font-weight: 700;
        transition: background .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
    }

    .seller-create-button:hover {
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .seller-create-button--primary {
        border-color: #d59617;
        background: #d59617;
        color: #fff;
        box-shadow: 0 8px 18px rgba(213,150,23,.16);
    }

    .seller-create-button--primary:hover {
        border-color: #c9890f;
        background: #c9890f;
    }

    .seller-create-button--soft {
        border-color: #efd9a7;
        background: #fffaf0;
        color: #9b6505;
    }

    .seller-create-preview-card {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
    }

    .seller-preview-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 16px;
        border-bottom: 1px solid #eef2f7;
    }

    .seller-preview-head h3 {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    .seller-preview-body {
        min-width: 0;
        padding: 13px;
    }

    .seller-preview-image {
        display: grid;
        aspect-ratio: 16 / 11;
        max-height: 190px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        background: #f8fafc;
        color: #9ca3af;
    }

    .seller-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .seller-preview-title {
        margin-top: 12px;
        overflow: hidden;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
        color: #111827;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .seller-preview-price {
        margin-top: 8px;
        color: #b7791f;
        font-size: 20px;
        font-weight: 800;
        white-space: nowrap;
    }

    .seller-preview-old-price {
        margin-left: 8px;
        color: #9ca3af;
        font-size: 11px;
        text-decoration: line-through;
    }

    .seller-preview-badges {
        display: flex;
        min-width: 0;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
    }

    .seller-preview-badge {
        display: inline-flex;
        min-height: 23px;
        max-width: 100%;
        align-items: center;
        border-radius: 999px;
        padding: 0 8px;
        font-size: 8px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .seller-preview-badge--green { background: var(--sari-green-soft); color: var(--sari-green); }
    .seller-preview-badge--gold { background: #fff6dc; color: #a76d06; }
    .seller-preview-badge--blue { background: var(--sari-blue-soft); color: #2f6faa; }

    .seller-preview-note {
        margin-top: 14px;
        border-top: 1px solid #edf0f3;
        padding-top: 12px;
        color: #8b96a5;
        font-size: 10px;
        line-height: 1.6;
    }

    .seller-create-mobile-preview {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 14px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .seller-create-mobile-preview-copy {
        min-width: 0;
    }

    .seller-create-mobile-preview-label {
        display: block;
        color: #9b6505;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .seller-create-mobile-preview-copy strong {
        display: block;
        margin-top: 3px;
        overflow: hidden;
        color: #111827;
        font-size: 11px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-create-mobile-preview-copy > span:last-child {
        display: block;
        margin-top: 2px;
        color: #6b7280;
        font-size: 9px;
    }

    .seller-create-mobile-preview-badges {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 5px;
    }

    .seller-promo-preview {
        margin-top: 14px;
        border: 1px solid #e4e8ed;
        border-radius: 16px;
        background: #ffffff;
        padding: 14px;
    }

    .seller-promo-preview-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eef2f7;
    }

    .seller-promo-preview-head strong {
        font-size: 13px;
        color: #111827;
    }

    .seller-promo-preview-head span {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        padding: 0 10px;
        border-radius: 999px;
        background: #fff7e6;
        color: #9a6708;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-promo-preview-price {
        margin-top: 12px;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .seller-promo-preview-price strong {
        font-size: 22px;
        line-height: 1;
        color: #111827;
    }

    .seller-promo-preview-price span {
        color: #9ca3af;
        font-size: 11px;
        text-decoration: line-through;
    }

    .seller-promo-preview-note {
        margin-top: 8px;
        font-size: 10.5px;
        line-height: 1.6;
        color: #6b7280;
    }

    .seller-spec-row {
        display: grid;
        grid-template-columns: 1fr 1.4fr 100px 42px;
        gap: 10px;
        margin-top: 10px;
    }

    .seller-inline-alert {
        margin-bottom: 14px;
        border: 1px solid #fecaca;
        border-radius: 14px;
        background: #fff7f7;
        padding: 12px 14px;
        color: #a33f3f;
        font-size: 11px;
        line-height: 1.65;
    }

    @media (max-width: 1500px) {
        .seller-create-grid {
            grid-template-columns: 175px minmax(0,1fr);
        }

        .seller-create-preview {
            display: none;
        }

        .seller-create-mobile-preview {
            display: flex;
        }
    }

    @media (max-width: 900px) {
        .seller-create-grid { grid-template-columns: 1fr; }
        .seller-create-side { display: none; }
    }


    @media (max-width: 760px) {
        .seller-variant-table,
        .seller-variant-table tbody,
        .seller-variant-table tr,
        .seller-variant-table td {
            display: block;
            width: 100%;
        }

        .seller-variant-table thead {
            display: none;
        }

        .seller-variant-table tr {
            padding: 10px;
            border-top: 1px solid #edf1f5;
        }

        .seller-variant-table tr:first-child {
            border-top: 0;
        }

        .seller-variant-table td {
            width: 100% !important;
            padding: 5px 0;
            border: 0;
        }

        .seller-variant-table td:first-child,
        .seller-variant-table td:last-child {
            display: inline-flex;
            width: auto !important;
            vertical-align: middle;
        }
    }

    @media (max-width: 760px) {
        .seller-create-topbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .seller-create-topbar > .flex.items-center.gap-2 {
            width: 100%;
        }

        .seller-create-topbar > .flex.items-center.gap-2 > * {
            flex: 1 1 0;
        }

        .seller-create-heading-title {
            font-size: clamp(31px, 9vw, 38px);
        }

        .seller-create-heading-icon {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
            border-radius: 14px;
        }
    }

    @media (max-width: 640px) {
        .seller-create-field-grid,
        .seller-create-field-grid--3,
        .seller-create-field-grid--4,
        .seller-create-setting-grid { grid-template-columns: 1fr; }
        .seller-create-span-2 { grid-column: auto; }
        .seller-create-gallery { grid-template-columns: repeat(3, minmax(0,1fr)); }
        .seller-spec-row { grid-template-columns: 1fr; }
        .seller-create-topbar { align-items: flex-start; }
        .seller-create-toggle-row,
        .seller-create-setting-card,
        .seller-promo-preview-head { align-items: flex-start; }
    }

    /* ============================================================
       SAVE DRAFT + MULTI IMAGE GALLERY
       ============================================================ */
    .seller-draft-status {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        gap: 6px;
        border: 1px solid #e3e7ec;
        border-radius: 999px;
        background: #ffffff;
        padding: 0 9px;
        color: #667085;
        font-size: 8px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }

    .seller-draft-status.is-success {
        border-color: #cfe7d7;
        background: #f5fbf7;
        color: #3f7f58;
    }

    .seller-draft-status.is-error {
        border-color: #f0d0d0;
        background: #fff8f8;
        color: #b54747;
    }

    .seller-draft-status.is-loading {
        border-color: #ead8ae;
        background: #fffaf0;
        color: #95630b;
    }

    .seller-gallery-count {
        display: inline-flex;
        min-height: 21px;
        align-items: center;
        border: 1px solid #e3e7ec;
        border-radius: 999px;
        background: #f8fafc;
        padding: 0 7px;
        color: #667085;
        font-size: 7.5px;
        font-weight: 700;
    }

    .seller-create-gallery-thumb {
        position: relative;
    }

    .seller-create-gallery-thumb::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.35);
    }

    .seller-gallery-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 2;
        display: grid;
        width: 24px;
        height: 24px;
        place-items: center;
        border: 1px solid rgba(255,255,255,.8);
        border-radius: 999px;
        background: rgba(17,24,39,.74);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(15,23,42,.12);
        cursor: pointer;
        transition: background-color .12s ease, transform .12s ease;
    }

    .seller-gallery-remove:hover {
        background: #b84444;
        transform: scale(1.04);
    }

    .seller-gallery-remove svg {
        width: 12px;
        height: 12px;
    }

    .seller-gallery-saved-badge {
        position: absolute;
        left: 5px;
        bottom: 5px;
        z-index: 2;
        display: inline-flex;
        min-height: 20px;
        align-items: center;
        border: 1px solid rgba(255,255,255,.75);
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        padding: 0 6px;
        color: #667085;
        font-size: 6.5px;
        font-weight: 700;
        line-height: 1;
    }

    #saveProductDraft:disabled {
        cursor: wait;
        opacity: .72;
        transform: none;
    }


    .seller-draft-manager {
        position: relative;
    }

    .seller-draft-count {
        display: inline-grid;
        min-width: 20px;
        height: 20px;
        place-items: center;
        border-radius: 999px;
        background: #f3f4f6;
        padding: 0 6px;
        color: #667085;
        font-size: 7.5px;
        font-weight: 800;
    }

    .seller-draft-chevron {
        width: 13px;
        height: 13px;
        color: #98a2b3;
        transition: transform .12s ease;
    }

    .seller-draft-manager.is-open .seller-draft-chevron {
        transform: rotate(180deg);
    }

    .seller-draft-menu {
        position: absolute;
        top: calc(100% + 7px);
        right: 0;
        z-index: 260;
        width: min(360px, 88vw);
        overflow: hidden;
        border: 1px solid #e2e7ed;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 18px 44px rgba(15,23,42,.13);
        animation: sellerDraftMenuIn 120ms ease both;
    }

    @keyframes sellerDraftMenuIn {
        from { opacity: 0; transform: translateY(-5px) scale(.992); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .seller-draft-menu-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 14px;
        border-bottom: 1px solid #edf1f5;
    }

    .seller-draft-menu-head strong {
        display: block;
        color: #111827;
        font-size: 11px;
        font-weight: 700;
    }

    .seller-draft-menu-head span {
        display: block;
        margin-top: 3px;
        color: #8a94a3;
        font-size: 7.5px;
        line-height: 1.45;
    }

    .seller-draft-new-button {
        display: inline-flex;
        height: 31px;
        flex: 0 0 auto;
        align-items: center;
        gap: 5px;
        border: 1px solid #e5c477;
        border-radius: 9px;
        background: #fffaf0;
        padding: 0 9px;
        color: #9a6708;
        font-size: 8px;
        font-weight: 700;
    }

    .seller-draft-new-button svg {
        width: 12px;
        height: 12px;
    }

    .seller-draft-list {
        max-height: 360px;
        overflow-y: auto;
        padding: 6px;
    }

    .seller-draft-empty {
        padding: 24px 14px;
        color: #98a2b3;
        font-size: 8.5px;
        text-align: center;
    }

    .seller-draft-item {
        display: grid;
        grid-template-columns: minmax(0,1fr) auto;
        gap: 8px;
        align-items: center;
        border: 1px solid transparent;
        border-radius: 11px;
        padding: 8px;
        transition: border-color .1s ease, background-color .1s ease;
    }

    .seller-draft-item:hover,
    .seller-draft-item.is-current {
        border-color: #eed8a7;
        background: #fffaf1;
    }

    .seller-draft-open {
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr);
        gap: 10px;
        min-width: 0;
        align-items: center;
        border: 0;
        background: transparent;
        padding: 0;
        text-align: left;
        cursor: pointer;
    }

    .seller-draft-thumb {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e4e8ed;
        border-radius: 10px;
        background: #f8fafc;
        color: #a1aab6;
    }

    .seller-draft-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .seller-draft-thumb svg {
        width: 18px;
        height: 18px;
    }

    .seller-draft-open-copy {
        min-width: 0;
    }

    .seller-draft-open strong {
        display: block;
        overflow: hidden;
        color: #344054;
        font-size: 9px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-draft-open span {
        display: block;
        margin-top: 3px;
        overflow: hidden;
        color: #98a2b3;
        font-size: 7px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-draft-delete {
        display: grid;
        width: 29px;
        height: 29px;
        place-items: center;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
        color: #98a2b3;
        cursor: pointer;
        transition: border-color .1s ease, color .1s ease, background-color .1s ease;
    }

    .seller-draft-delete:hover {
        border-color: #efc9c9;
        background: #fff8f8;
        color: #c34b4b;
    }

    .seller-draft-delete svg {
        width: 13px;
        height: 13px;
    }



    @keyframes sariDraftGoldSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes sariDraftGoldSpinReverse {
        to { transform: rotate(-360deg); }
    }


    /* Draft save feedback — compact, modern, success-only Lottie */
    .seller-draft-feedback {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(17,24,39,.14);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        pointer-events: none;
    }

    .seller-draft-feedback.is-visible { display: flex; }

    .seller-draft-feedback-card {
        display: flex;
        width: min(310px, calc(100vw - 32px));
        min-height: 108px;
        align-items: center;
        gap: 14px;
        border: 1px solid #e8e2d9;
        border-radius: 16px;
        background: #fff;
        padding: 16px 18px;
        box-shadow: 0 18px 44px rgba(17,24,39,.13);
    }

    .seller-draft-feedback-loader {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 12px;
        background: #fff9ed;
    }

    .seller-draft-feedback-spinner {
        width: 21px;
        height: 21px;
        border: 2px solid #ead9b3;
        border-top-color: #d59617;
        border-radius: 999px;
        animation: sariDraftGoldSpin .72s linear infinite;
    }

    .seller-draft-feedback-lottie {
        display: none;
        width: 74px;
        height: 74px;
        flex: 0 0 74px;
        border: 0;
        margin: -7px -6px -7px -8px;
        padding: 0;
        background: transparent;
        pointer-events: none;
    }

    .seller-draft-feedback-copy {
        min-width: 0;
        flex: 1;
    }

    .seller-draft-feedback-copy strong {
        display: block;
        color: #1f2937;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -.015em;
    }

    .seller-draft-feedback-copy span {
        display: block;
        margin-top: 4px;
        color: #7a8492;
        font-size: 8.5px;
        font-weight: 500;
        line-height: 1.5;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-card {
        border-color: #d7eadf;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-copy strong {
        color: #197548;
    }

    .seller-draft-feedback.is-error .seller-draft-feedback-card {
        border-color: #efd3d3;
    }

    .seller-draft-feedback.is-error .seller-draft-feedback-copy strong {
        color: #b54747;
    }


    /* ============================================================
       DRAFT SAVE FEEDBACK — MINIMAL / NO CARD CONTAINER
       Animation + text only.
       ============================================================ */
    .seller-draft-feedback {
        background: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        padding: 20px !important;
    }

    .seller-draft-feedback-card {
        display: flex !important;
        width: auto !important;
        min-width: 0 !important;
        max-width: min(360px, calc(100vw - 32px)) !important;
        min-height: 0 !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .seller-draft-feedback-loader {
        display: grid;
        width: 34px !important;
        height: 34px !important;
        flex: 0 0 34px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-draft-feedback-spinner {
        width: 24px !important;
        height: 24px !important;
        border-width: 2px !important;
        border-color: rgba(213, 150, 23, .22) !important;
        border-top-color: #d59617 !important;
    }

    .seller-draft-feedback-lottie {
        width: 112px !important;
        height: 112px !important;
        flex: 0 0 112px !important;
        margin: 0 !important;
    }

    .seller-draft-feedback-copy {
        width: min(320px, calc(100vw - 44px)) !important;
        text-align: center !important;
    }

    .seller-draft-feedback-copy strong {
        color: #26221d !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
    }

    .seller-draft-feedback-copy span {
        margin-top: 3px !important;
        color: #7d756c !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-card,
    .seller-draft-feedback.is-error .seller-draft-feedback-card {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-copy strong {
        color: #197548 !important;
    }

    .seller-draft-feedback.is-error .seller-draft-feedback-copy strong {
        color: #b54747 !important;
    }

    @media (max-width: 480px) {
        .seller-draft-feedback {
            padding: 16px !important;
        }

        .seller-draft-feedback-lottie {
            width: 100px !important;
            height: 100px !important;
            flex-basis: 100px !important;
        }
    }


    /* ============================================================
       DRAFT SAVE FEEDBACK — CLEAR / TRANSPARENT / HIGH VISIBILITY
       ============================================================ */
    .seller-draft-feedback {
        background: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    .seller-draft-feedback-card {
        width: auto !important;
        min-width: 0 !important;
        max-width: min(390px, calc(100vw - 32px)) !important;
        min-height: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .seller-draft-feedback-loader {
        width: 40px !important;
        height: 40px !important;
        flex-basis: 40px !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .seller-draft-feedback-spinner {
        width: 28px !important;
        height: 28px !important;
        border-width: 2.5px !important;
        border-color: rgba(213,150,23,.24) !important;
        border-top-color: #d59617 !important;
        filter: drop-shadow(0 2px 5px rgba(213,150,23,.18));
    }

    .seller-draft-feedback-lottie {
        width: 160px !important;
        height: 160px !important;
        flex: 0 0 160px !important;
        margin: -8px 0 -7px !important;
        border: 0 !important;
        background: transparent !important;
        opacity: 1 !important;
        filter: drop-shadow(0 8px 18px rgba(17,24,39,.10));
    }

    .seller-draft-feedback-copy {
        width: min(340px, calc(100vw - 40px)) !important;
        text-align: center !important;
    }

    .seller-draft-feedback-copy strong {
        color: #201c18 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.25 !important;
        text-shadow:
            0 1px 0 rgba(255,255,255,.95),
            0 2px 8px rgba(255,255,255,.9) !important;
    }

    .seller-draft-feedback-copy span {
        margin-top: 4px !important;
        color: #61594f !important;
        font-size: 9px !important;
        line-height: 1.45 !important;
        text-shadow:
            0 1px 0 rgba(255,255,255,.95),
            0 2px 8px rgba(255,255,255,.9) !important;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-copy strong {
        color: #0f7a46 !important;
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-copy span {
        color: #446153 !important;
    }

    @media (max-width: 480px) {
        .seller-draft-feedback-lottie {
            width: 138px !important;
            height: 138px !important;
            flex-basis: 138px !important;
        }

        .seller-draft-feedback-copy strong {
            font-size: 13px !important;
        }
    }



/* ==========================================================================
   ADD PRODUCT — FORMAL HEADER + PERFORMANCE PASS
   ========================================================================== */

.seller-create-heading-icon {
    display: none !important;
}

.seller-create-heading {
    gap: 0 !important;
}

.seller-create-heading-title {
    font-size: clamp(34px, 2.8vw, 44px) !important;
    line-height: .98 !important;
    font-weight: 750 !important;
    letter-spacing: -.045em !important;
}

.seller-create-heading-title span {
    color: #C9890B !important;
}

/* Avoid spending paint/layout work on long sections far below the viewport. */
.seller-create-section {
    content-visibility: auto;
    contain-intrinsic-size: auto 560px;
}

/* Faster-feeling control feedback. */
.seller-create-button,
.seller-create-input,
.seller-create-select,
.seller-create-textarea,
.seller-category-trigger,
.seller-create-nav button {
    transition-duration: 120ms !important;
}

.seller-create-gallery-thumb,
.seller-preview-image,
.seller-variant-image-label {
    contain: paint;
}

@media (max-width: 760px) {
    .seller-create-heading-title {
        font-size: clamp(31px, 9vw, 38px) !important;
    }
}

@media (prefers-reduced-motion: reduce) {
    .seller-create-page *,
    .seller-create-page *::before,
    .seller-create-page *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>

<style>
    .seller-create-page { background: transparent !important; }
    .seller-create-heading-icon { display: none !important; }

    .seller-create-heading-title {
        font-size: clamp(30px, 2.35vw, 38px) !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
        font-weight: 650 !important;
    }

    @media (max-width: 760px) {
        .seller-create-heading-title {
            font-size: clamp(28px, 8vw, 34px) !important;
            font-weight: 650 !important;
        }
    }
</style>

<style id="sariDraftLottieSmoothFinal">

    .seller-draft-feedback-fallback {
        display: none;
        width: 104px;
        height: 104px;
        place-items: center;
        opacity: 0;
        transform: translateY(4px) scale(.94);
        will-change: opacity, transform;
        transition:
            opacity 160ms ease-out,
            transform 220ms cubic-bezier(.2,.8,.2,1);
    }

    .seller-draft-feedback-fallback.is-playing {
        display: grid;
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .seller-draft-feedback-fallback svg {
        width: 92px;
        height: 92px;
        overflow: visible;
        filter: drop-shadow(0 8px 18px rgba(15,118,72,.08));
    }

    .seller-draft-fallback-ring {
        stroke: #D8EFE1;
        stroke-width: 4;
        stroke-linecap: round;
        stroke-dasharray: 170;
        stroke-dashoffset: 170;
        transform-origin: 36px 36px;
    }

    .seller-draft-fallback-check {
        stroke: #1E9A5D;
        stroke-width: 5;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 42;
        stroke-dashoffset: 42;
    }

    .seller-draft-feedback-fallback.is-playing .seller-draft-fallback-ring {
        animation: sariDraftFallbackRing 420ms cubic-bezier(.2,.8,.2,1) forwards;
    }

    .seller-draft-feedback-fallback.is-playing .seller-draft-fallback-check {
        animation: sariDraftFallbackCheck 360ms 240ms cubic-bezier(.2,.8,.2,1) forwards;
    }

    @keyframes sariDraftFallbackRing {
        to {
            stroke-dashoffset: 0;
            transform: rotate(360deg);
        }
    }

    @keyframes sariDraftFallbackCheck {
        to { stroke-dashoffset: 0; }
    }


    /* Main Add Product section headers are intentionally icon-free. */
    .seller-create-section-head {
        align-items: center !important;
    }

    .seller-create-section-copy h2 {
        margin: 0 !important;
    }

    /* Smooth Lottie reveal so the success animation is actually visible. */
    .seller-draft-feedback-lottie {
        display: none;
        opacity: 0 !important;
        transform: translateY(4px) scale(.94);
        will-change: opacity, transform;
        transition:
            opacity 180ms ease-out,
            transform 220ms cubic-bezier(.2,.8,.2,1) !important;
    }

    .seller-draft-feedback-lottie.is-playing {
        opacity: 1 !important;
        transform: translateY(0) scale(1);
    }

    .seller-draft-feedback.is-success .seller-draft-feedback-spinner {
        animation-duration: .62s !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-draft-feedback-lottie,
        .seller-draft-feedback-fallback {
            transition: none !important;
            transform: none !important;
        }

        .seller-draft-feedback-fallback.is-playing .seller-draft-fallback-ring,
        .seller-draft-feedback-fallback.is-playing .seller-draft-fallback-check {
            animation-duration: .01ms !important;
            animation-delay: 0ms !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $categories = [
        'Electronics',
        'Fashion & Apparel',
        'Beauty & Personal Care',
        'Home & Living',
        'Food & Beverages',
        'Sports & Outdoors',
        'Books & Stationery',
        'Automotive',
        'Baby & Kids',
        'Pet Supplies',
        'Health & Wellness',
        'Toys & Collectibles',
        'Appliances',
        'Others',
    ];
@endphp



<div class="seller-create-page mx-auto w-full max-w-[1440px]">
    <div class="seller-create-topbar">
        <div class="seller-create-heading">
            <div class="seller-create-heading-copy">
                <p class="seller-create-heading-eyebrow">Seller Catalog</p>
                <h1 class="seller-create-heading-title">Add <span>Product</span></h1>
                <a href="{{ route('seller.products.index') }}" class="seller-create-heading-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                    Back to product catalog
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <div id="productDraftManager" class="seller-draft-manager">
                <button id="productDraftMenuButton" type="button" class="seller-create-button" aria-haspopup="menu" aria-expanded="false">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M5 5h14v14H5z"></path>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h5"></path>
                    </svg>
                    Drafts
                    <span id="productDraftCount" class="seller-draft-count">0</span>
                    <svg viewBox="0 0 24 24" class="seller-draft-chevron" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </button>

                <div id="productDraftMenu" class="seller-draft-menu hidden" role="menu">
                    <div class="seller-draft-menu-head">
                        <div>
                            <strong>Saved drafts</strong>
                            <span>Open any saved product and continue editing.</span>
                        </div>
                        <button id="newBlankProduct" type="button" class="seller-draft-new-button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                            New
                        </button>
                    </div>

                    <div id="productDraftList" class="seller-draft-list">
                        <div class="seller-draft-empty">No saved drafts yet.</div>
                    </div>
                </div>
            </div>

            <button id="saveProductDraft" type="button" class="seller-create-button">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="M5 4h12l2 2v14H5z"></path>
                    <path d="M8 4v6h8V4"></path>
                    <path d="M8 16h8"></path>
                </svg>
                <span data-draft-button-label>Save as draft</span>
            </button>

            <button id="submitProductReview" type="submit" form="sellerCreateProductForm" class="seller-create-button seller-create-button--primary">
                Submit for review
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m5 12 14-7-5 14-2-5-7-2Z"></path></svg>
            </button>
        </div>
    </div>

    <div
        id="draftSaveFeedback"
        role="status"
        aria-live="polite"
        aria-atomic="true"
        class="seller-draft-feedback"
        aria-hidden="true"
    >
        <div class="seller-draft-feedback-card">
            <div id="draftGoldLoader" class="seller-draft-feedback-loader" aria-hidden="true">
                <span class="seller-draft-feedback-spinner"></span>
            </div>

            <iframe
                id="draftSaveLottie"
                src="about:blank"
                title="Draft saved animation"
                loading="eager"
                scrolling="no"
                allow="autoplay"
                tabindex="-1"
                class="seller-draft-feedback-lottie"
                aria-hidden="true"
            ></iframe>

            <div id="draftSaveFallback" class="seller-draft-feedback-fallback" aria-hidden="true">
                <svg viewBox="0 0 72 72" fill="none" aria-hidden="true">
                    <circle class="seller-draft-fallback-ring" cx="36" cy="36" r="27"></circle>
                    <path class="seller-draft-fallback-check" d="M23 36.5 32 45l18-19"></path>
                </svg>
            </div>

            <div class="seller-draft-feedback-copy">
                <strong id="draftSaveFeedbackTitle">Saving draft</strong>
                <span id="draftSaveFeedbackText">Please wait while your changes are saved.</span>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="seller-inline-alert">
            <strong class="block font-bold">Please check the product information.</strong>
            <ul class="mt-1 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="seller-create-grid">
        <aside class="seller-create-side">
            <div class="seller-create-side-card">
                <div class="seller-create-side-title">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#c88912]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12h3l2-3 3 7 2-4h6"></path></svg>
                    Suggestions
                </div>
                <p class="px-2 pt-2 text-[8px] leading-4 text-[#818995]">Complete each section to improve listing quality.</p>
                <nav class="seller-create-nav" aria-label="Add product sections">
                    <button type="button" data-create-nav="basic">Basic Information</button>
                    <button type="button" data-create-nav="details">Product Details</button>
                    <button type="button" data-create-nav="sales">Sales Information</button>
                    <button type="button" data-create-nav="shipping">Shipping</button>
                </nav>
            </div>
        </aside>

        <form id="sellerCreateProductForm" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="seller-create-workspace">
            @csrf
            <input id="sellerCreateDraftId" type="hidden" name="draft_id" value="{{ old('draft_id') }}">
            <input id="sellerCreateHasVariants" type="hidden" name="has_variants" value="{{ old('has_variants', 0) }}">

            <section id="create-basic" data-create-section="basic" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Basic information</h2>
                        <p>Core information buyers use to identify and discover your product.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid">
                    <div class="seller-create-span-2">
                        <label class="seller-create-label">Product name <span class="text-[#dc4c4c]">*</span></label>
                        <input id="createProductName" name="name" value="{{ old('name') }}" required maxlength="150" class="seller-create-input" placeholder="e.g. Nordic Solid Wood Dining Chair">
                    </div>
                    <div>
                        <label class="seller-create-label">Category <span class="text-[#dc4c4c]">*</span></label>

                        <div id="createCategoryPicker" class="seller-category-picker">
                            <select id="createProductCategory" name="category" required class="sr-only" tabindex="-1" aria-hidden="true">
                                <option value="">Choose product category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>

                            <button
                                id="createCategoryTrigger"
                                type="button"
                                class="seller-category-trigger"
                                aria-haspopup="listbox"
                                aria-expanded="false"
                                aria-controls="createCategoryMenu"
                            >
                                <span class="seller-category-trigger-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20 13 11 22l-8-8 9-9h7v7Z"></path>
                                        <circle cx="16" cy="9" r="1.4"></circle>
                                    </svg>
                                </span>
                                <span id="createCategoryTriggerText" class="seller-category-trigger-text">Choose product category</span>
                                <svg class="seller-category-trigger-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
                            </button>

                            <div id="createCategoryMenu" class="seller-category-menu hidden" role="listbox">
                                @foreach ($categories as $category)
                                    <button
                                        type="button"
                                        class="seller-category-option"
                                        data-category-option="{{ $category }}"
                                        role="option"
                                        aria-selected="{{ old('category') === $category ? 'true' : 'false' }}"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M20 13 11 22l-8-8 9-9h7v7Z"></path>
                                            <circle cx="16" cy="9" r="1.2"></circle>
                                        </svg>
                                        <span>{{ $category }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="seller-create-label">Brand</label>
                        <input id="createProductBrand" name="brand" value="{{ old('brand') }}" maxlength="120" class="seller-create-input" placeholder="Brand name or No Brand">
                    </div>
                    <div id="createCustomCategoryWrap" class="seller-create-span-2 hidden">
                        <label class="seller-create-label">Custom category</label>
                        <input id="createCustomCategory" name="custom_category" value="{{ old('custom_category') }}" maxlength="100" class="seller-create-input" placeholder="Enter your product category">
                    </div>
                </div>

                <details class="mt-4 rounded-[11px] border border-[#e4e8ed] bg-[#fafbfc]">
                    <summary class="cursor-pointer list-none px-3 py-3 text-[9px] font-semibold text-[#49515b]">More listing information <span class="ml-1 font-normal text-[#8a929c]">SKU, voucher and condition</span></summary>
                    <div class="seller-create-field-grid border-t border-[#e8ebef] p-3">
                        <div>
                            <label class="seller-create-label">Seller SKU</label>
                            <input name="sku" value="{{ old('sku') }}" maxlength="100" class="seller-create-input" placeholder="Optional">
                        </div>
                        <div>
                            <label class="seller-create-label">Voucher code</label>
                            <input name="voucher_code" value="{{ old('voucher_code') }}" maxlength="80" class="seller-create-input" placeholder="Optional">
                        </div>
                        <div>
                            <label class="seller-create-label">Condition</label>
                            <select name="condition" class="seller-create-select">
                                <option value="">Not specified</option>
                                <option value="new" @selected(old('condition') === 'new')>New</option>
                                <option value="like_new" @selected(old('condition') === 'like_new')>Like New</option>
                                <option value="used" @selected(old('condition') === 'used')>Used</option>
                            </select>
                        </div>
                    </div>
                </details>
            </section>

            <section id="create-details" data-create-section="details" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Product details</h2>
                        <p>Add clear photos and an accurate description of the product.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid md:grid-cols-[190px_minmax(0,1fr)]">
                    <div>
                        <label class="seller-create-label">Cover image</label>
                        <label class="seller-create-upload">
                            <input id="createCoverImage" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <span id="createCoverPlaceholder">
                                <span class="mx-auto grid h-11 w-11 place-items-center rounded-[11px] border border-[#dce1e7] bg-white text-[#65707c]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path></svg>
                                </span>
                                <strong class="mt-2 block text-[9px] text-[#39414a]">Upload cover image</strong>
                                <span class="mt-1 block text-[7px] text-[#8a929c]">JPG, PNG or WEBP · up to 4 MB</span>
                            </span>
                            <img id="createCoverPreview" class="hidden" alt="Cover preview" decoding="async">
                        </label>
                    </div>
                    <div>
                        <label class="seller-create-label">Product description</label>
                        <textarea id="createProductDescription" name="description" maxlength="5000" class="seller-create-textarea" placeholder="Describe the product clearly: features, materials, measurements, condition, package inclusions, warranty, or other important information...">{{ old('description') }}</textarea>
                        <div class="mt-1 text-right text-[7px] text-[#949ca6]"><span id="createDescriptionCount">0</span>/5000</div>
                    </div>
                </div>

                <div class="mt-4 border-t border-[#edf0f3] pt-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-[10px] font-semibold text-[#333840]">Product gallery</p>
                                <span id="createGalleryCount" class="seller-gallery-count">0 / 12 images</span>
                            </div>
                            <p class="mt-1 text-[8px] text-[#818995]">Add up to 12 extra images. You can choose images multiple times and they will stay selected.</p>
                        </div>
                        <label class="seller-create-button seller-create-button--soft cursor-pointer">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
                            Add images
                            <input id="createGalleryImages" name="gallery_images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
                        </label>
                    </div>
                    <div id="createGalleryPreview" class="seller-create-gallery"></div>
                    <p id="createGalleryEmpty" class="mt-2 rounded-[10px] border border-dashed border-[#d9dee5] bg-[#fafbfc] px-3 py-3 text-center text-[8px] text-[#8b939d]">No gallery images selected.</p>
                </div>
            </section>

            <section id="create-sales" data-create-section="sales" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Sales information</h2>
                        <p>Set price, stock, variants and buyer-facing promotions.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div id="createSimpleInventory" class="seller-create-field-grid">
                    <div>
                        <label class="seller-create-label">Price <span class="text-[#dc4c4c]">*</span></label>
                        <div class="seller-create-money-field">
                            <span class="seller-create-money-prefix" aria-hidden="true">₱</span>
                            <input
                                id="createProductPrice"
                                name="price"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('price') }}"
                                required
                                class="seller-create-money-input"
                                placeholder="0.00"
                                inputmode="decimal"
                            >
                        </div>
                    </div>
                    <div>
                        <label class="seller-create-label">Stock <span class="text-[#dc4c4c]">*</span></label>
                        <input id="createProductStock" name="stock" type="number" min="0" value="{{ old('stock') }}" required class="seller-create-input" placeholder="0">
                    </div>
                </div>

                <div class="seller-create-toggle-row">
                    <div class="min-w-0">
                        <strong class="block text-[10px] font-semibold text-[#303640]">Has different options?</strong>
                        <span class="mt-1 block text-[8px] leading-4 text-[#838b95]">Use variants for sizes, colors, designs or other options. Each row can have its own image, SKU, price and stock.</span>
                    </div>
                    <label class="shrink-0 cursor-pointer">
                        <input id="createVariantsToggle" type="checkbox" class="peer sr-only" @checked(old('has_variants'))>
                        <span class="seller-create-toggle"></span>
                    </label>
                </div>

                <div id="createVariantsArea" class="hidden">
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="seller-create-plain-icon text-[#c98505]" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 7h7v7H4z"></path>
                                    <path d="M13 10h7v7h-7z"></path>
                                </svg>
                            </span>
                            <p class="text-[10px] font-semibold text-[#344054]">Variants <span id="createVariantCount" class="ml-1 font-normal text-[#8a94a3]">0 variants</span></p>
                        </div>
                        <button id="addVariantRow" type="button" class="seller-create-button seller-create-button--soft">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                            Add variant
                        </button>
                    </div>
                    <div id="createVariantTableWrap" class="seller-variant-table-wrap hidden">
                        <table class="seller-variant-table">
                            <thead>
                                <tr><th>Image</th><th>Size / Variant</th><th>Color / Design</th><th>SKU</th><th>Price</th><th>Stock</th><th></th></tr>
                            </thead>
                            <tbody id="createVariantRows"></tbody>
                        </table>
                    </div>
                </div>


                <div class="seller-create-mobile-preview">
                    <div class="seller-create-mobile-preview-copy">
                        <span class="seller-create-mobile-preview-label">Buyer preview</span>
                        <strong id="mobilePreviewTitle">Product title</strong>
                        <span id="mobilePreviewMeta">Category · ₱0.00</span>
                    </div>
                    <div class="seller-create-mobile-preview-badges">
                        <span id="mobilePreviewDiscount" class="seller-preview-badge seller-preview-badge--gold hidden">0% OFF</span>
                        <span id="mobilePreviewShipping" class="seller-preview-badge seller-preview-badge--green hidden">Free Shipping</span>
                        <span id="mobilePreviewFlash" class="seller-preview-badge seller-preview-badge--blue hidden">Flash Sale</span>
                        <span id="mobilePreviewCOD" class="seller-preview-badge seller-preview-badge--blue hidden">COD</span>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#edf0f3] pt-4">
                    <div class="seller-create-field-grid">
                        <div>
                            <label class="seller-create-label">Discount %</label>
                            <input id="createDiscount" name="discount" type="number" min="0" max="100" step="0.01" value="{{ old('discount', 0) }}" class="seller-create-input" placeholder="0">
                            <p class="seller-create-help">Discount is applied automatically to the buyer price.</p>
                        </div>
                        <div>
                            <label class="seller-create-label-with-icon">
                                <span class="seller-create-plain-icon seller-create-plain-icon--flash" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M13 2 5 14h6l-1 8 8-12h-6l1-8Z"></path>
                                    </svg>
                                </span>
                                <span>Flash Sale end date & time</span>
                            </label>
                            <input id="createFlashSale" name="flash_sale_ends_at" type="datetime-local" value="{{ old('flash_sale_ends_at') }}" class="seller-create-input">
                            <p class="seller-create-help">Countdown begins only after administrator approval.</p>
                        </div>
                    </div>

                    <div class="seller-create-setting-grid">
                        <label class="seller-create-setting-card cursor-pointer">
                            <span class="seller-create-setting-copy">
                                <span class="seller-create-plain-icon seller-create-plain-icon--shipping" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M3 7h11v9H3z"></path>
                                        <path d="M14 10h4l3 3v3h-7z"></path>
                                        <circle cx="7" cy="18" r="1.5"></circle>
                                        <circle cx="18" cy="18" r="1.5"></circle>
                                    </svg>
                                </span>
                                <span>
                                    <strong>Free Shipping</strong>
                                    <span>Show the Free Shipping badge and waive the standard delivery fee when eligible.</span>
                                </span>
                            </span>
                            <span class="shrink-0">
                                <input id="createFreeShipping" name="free_shipping" type="checkbox" value="1" class="peer sr-only" @checked(old('free_shipping'))>
                                <span class="seller-create-toggle"></span>
                            </span>
                        </label>

                        <label class="seller-create-setting-card cursor-pointer">
                            <span class="seller-create-setting-copy">
                                <span class="seller-create-plain-icon seller-create-plain-icon--cod" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3.5" y="6" width="17" height="12" rx="2.5"></rect>
                                        <path d="M8 12h8"></path>
                                        <path d="M9 9.5v5"></path>
                                    </svg>
                                </span>
                                <span>
                                    <strong>Cash on Delivery</strong>
                                    <span>Let buyers pay when the order is delivered, if COD is available in checkout.</span>
                                </span>
                            </span>
                            <span class="shrink-0">
                                <input id="createCOD" name="cash_on_delivery" type="checkbox" value="1" class="peer sr-only" @checked(old('cash_on_delivery'))>
                                <span class="seller-create-toggle"></span>
                            </span>
                        </label>
                    </div>

                    <div class="seller-promo-preview">
                        <div class="seller-promo-preview-head">
                            <strong>Live sale preview</strong>
                            <span id="promoPreviewState">No discount</span>
                        </div>
                        <div class="seller-promo-preview-price">
                            <strong id="promoPreviewPrice">₱0.00</strong>
                            <span id="promoPreviewOldPrice" class="hidden">₱0.00</span>
                        </div>
                        <p id="promoPreviewNote" class="seller-promo-preview-note">Variant prices will be shown at their original values.</p>
                    </div>
                </div>
            </section>

            <section id="create-shipping" data-create-section="shipping" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Shipping</h2>
                        <p>Provide packaged weight, dimensions and preparation details used for fulfillment.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid seller-create-field-grid--4">
                    <div>
                        <label class="seller-create-label">Package weight</label>
                        <input name="package_weight" type="number" min="0" step="0.001" value="{{ old('package_weight') }}" class="seller-create-input" placeholder="kg">
                    </div>
                    <div>
                        <label class="seller-create-label">Preparation time</label>
                        <input name="preparation_days" type="number" min="0" max="365" value="{{ old('preparation_days') }}" class="seller-create-input" placeholder="days">
                    </div>
                    <div>
                        <label class="seller-create-label">Low stock alert</label>
                        <input name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', 5) }}" class="seller-create-input" placeholder="5">
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-[9px] font-semibold text-[#3e4650]">Package dimensions <span class="font-normal text-[#8a929c]">(cm)</span></p>
                    <div class="seller-create-field-grid seller-create-field-grid--3 mt-2">
                        <div><label class="seller-create-label">Length</label><input name="package_length" type="number" min="0" step="0.01" value="{{ old('package_length') }}" class="seller-create-input" placeholder="0"></div>
                        <div><label class="seller-create-label">Width</label><input name="package_width" type="number" min="0" step="0.01" value="{{ old('package_width') }}" class="seller-create-input" placeholder="0"></div>
                        <div><label class="seller-create-label">Height</label><input name="package_height" type="number" min="0" step="0.01" value="{{ old('package_height') }}" class="seller-create-input" placeholder="0"></div>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#edf0f3] pt-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#333840]">Product specifications</p>
                            <p class="mt-1 text-[8px] text-[#818995]">Add material, model, warranty, capacity or other searchable details.</p>
                        </div>
                        <button id="addSpecificationRow" type="button" class="seller-create-button">+ Add specification</button>
                    </div>
                    <div id="createSpecificationRows"></div>
                    <p id="createSpecificationsEmpty" class="mt-3 rounded-[10px] border border-dashed border-[#d9dee5] bg-[#fafbfc] px-3 py-3 text-center text-[8px] text-[#8b939d]">No specifications added yet.</p>
                </div>
            </section>
        </form>

        <aside class="seller-create-preview">
            <div class="seller-create-preview-card">
                <div class="seller-preview-head">
                    <h3>Preview</h3>
                    <span class="text-[7px] font-semibold text-[#9aa1a9]">BUYER VIEW</span>
                </div>
                <div class="seller-preview-body">
                    <div id="previewImage" class="seller-preview-image">
                        <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path></svg>
                    </div>
                    <p id="previewTitle" class="seller-preview-title">Product title</p>
                    <div class="mt-1 text-[8px] text-[#8c949e]"><span id="previewCategory">Category</span></div>
                    <div>
                        <span id="previewPrice" class="seller-preview-price">₱0.00</span>
                        <span id="previewOldPrice" class="seller-preview-old-price hidden">₱0.00</span>
                    </div>
                    <div class="seller-preview-badges">
                        <span id="previewDiscountBadge" class="seller-preview-badge seller-preview-badge--gold hidden">0% OFF</span>
                        <span id="previewShippingBadge" class="seller-preview-badge seller-preview-badge--green hidden">Free Shipping</span>
                        <span id="previewFlashBadge" class="seller-preview-badge seller-preview-badge--blue hidden">Flash Sale</span>
                        <span id="previewCODBadge" class="seller-preview-badge seller-preview-badge--blue hidden">COD</span>
                    </div>
                    <p class="seller-preview-note">For reference only. The final buyer view may vary based on category, promotions and checkout eligibility.</p>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const bootSellerCreateProductPage = function () {
    const form = document.getElementById('sellerCreateProductForm');
    if (!form || form.dataset.bound === '1') return;
    form.dataset.bound = '1';

    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const category = document.getElementById('createProductCategory');
    const categoryPicker = document.getElementById('createCategoryPicker');
    const categoryTrigger = document.getElementById('createCategoryTrigger');
    const categoryTriggerText = document.getElementById('createCategoryTriggerText');
    const categoryMenu = document.getElementById('createCategoryMenu');
    const customCategoryWrap = document.getElementById('createCustomCategoryWrap');
    const customCategory = document.getElementById('createCustomCategory');
    const nameInput = document.getElementById('createProductName');
    const description = document.getElementById('createProductDescription');
    const descriptionCount = document.getElementById('createDescriptionCount');
    const coverInput = document.getElementById('createCoverImage');
    const coverPreview = document.getElementById('createCoverPreview');
    const coverPlaceholder = document.getElementById('createCoverPlaceholder');
    const galleryInput = document.getElementById('createGalleryImages');
    const galleryPreview = document.getElementById('createGalleryPreview');
    const galleryEmpty = document.getElementById('createGalleryEmpty');
    const galleryCount = document.getElementById('createGalleryCount');
    const priceInput = document.getElementById('createProductPrice');
    const stockInput = document.getElementById('createProductStock');
    const discountInput = document.getElementById('createDiscount');
    const flashInput = document.getElementById('createFlashSale');
    const freeShipping = document.getElementById('createFreeShipping');
    const codToggle = document.getElementById('createCOD');
    const variantsToggle = document.getElementById('createVariantsToggle');
    const hasVariants = document.getElementById('sellerCreateHasVariants');
    const variantsArea = document.getElementById('createVariantsArea');
    const variantRows = document.getElementById('createVariantRows');
    const variantWrap = document.getElementById('createVariantTableWrap');
    const variantCount = document.getElementById('createVariantCount');
    const addVariantButton = document.getElementById('addVariantRow');
    const specRows = document.getElementById('createSpecificationRows');
    const specsEmpty = document.getElementById('createSpecificationsEmpty');
    const saveDraft = document.getElementById('saveProductDraft');
    const draftButtonLabel = saveDraft?.querySelector('[data-draft-button-label]');
    const draftFeedback = document.getElementById('draftSaveFeedback');
    const draftFeedbackTitle = document.getElementById('draftSaveFeedbackTitle');
    const draftFeedbackText = document.getElementById('draftSaveFeedbackText');
    const draftGoldLoader = document.getElementById('draftGoldLoader');
    const draftSaveLottie = document.getElementById('draftSaveLottie');
    const draftSaveFallback = document.getElementById('draftSaveFallback');
    const draftId = document.getElementById('sellerCreateDraftId');
    const draftManager = document.getElementById('productDraftManager');
    const draftMenuButton = document.getElementById('productDraftMenuButton');
    const draftMenu = document.getElementById('productDraftMenu');
    const draftCount = document.getElementById('productDraftCount');
    const draftList = document.getElementById('productDraftList');
    const newBlankProduct = document.getElementById('newBlankProduct');

    const previewTitle = document.getElementById('previewTitle');
    const previewCategory = document.getElementById('previewCategory');
    const previewPrice = document.getElementById('previewPrice');
    const previewOldPrice = document.getElementById('previewOldPrice');
    const previewImage = document.getElementById('previewImage');
    const previewDiscountBadge = document.getElementById('previewDiscountBadge');
    const previewShippingBadge = document.getElementById('previewShippingBadge');
    const previewFlashBadge = document.getElementById('previewFlashBadge');
    const previewCODBadge = document.getElementById('previewCODBadge');
    const promoPreviewState = document.getElementById('promoPreviewState');
    const promoPreviewPrice = document.getElementById('promoPreviewPrice');
    const promoPreviewOldPrice = document.getElementById('promoPreviewOldPrice');
    const promoPreviewNote = document.getElementById('promoPreviewNote');
    const mobilePreviewTitle = document.getElementById('mobilePreviewTitle');
    const mobilePreviewMeta = document.getElementById('mobilePreviewMeta');
    const mobilePreviewDiscount = document.getElementById('mobilePreviewDiscount');
    const mobilePreviewShipping = document.getElementById('mobilePreviewShipping');
    const mobilePreviewFlash = document.getElementById('mobilePreviewFlash');
    const mobilePreviewCOD = document.getElementById('mobilePreviewCOD');

    let variantIndex = 0;
    let specIndex = 0;
    let previewFrame = 0;

    const queuePreviewUpdate = () => {
        if (previewFrame) return;

        previewFrame = window.requestAnimationFrame(() => {
            previewFrame = 0;
            updatePreview();
        });
    };

    const GALLERY_LIMIT = 12;
    let pendingGalleryFiles = [];
    let savedDraftGallery = [];
    let galleryObjectUrls = [];

    const money = (value) => '₱' + Math.max(0, Number(value || 0)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function syncCategoryPicker() {
        const selected = category?.value || '';
        if (categoryTriggerText) {
            categoryTriggerText.textContent = selected || 'Choose product category';
        }

        categoryMenu?.querySelectorAll('[data-category-option]').forEach(option => {
            const active = option.dataset.categoryOption === selected;
            option.classList.toggle('is-selected', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function closeCategoryMenu() {
        categoryMenu?.classList.add('hidden');
        categoryTrigger?.setAttribute('aria-expanded', 'false');
    }

    function toggleCategoryMenu() {
        if (!categoryMenu || !categoryTrigger) return;
        const opening = categoryMenu.classList.contains('hidden');
        categoryMenu.classList.toggle('hidden', !opening);
        categoryTrigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
    }

    function updateCustomCategory() {
        const show = category?.value === 'Others';
        customCategoryWrap?.classList.toggle('hidden', !show);
        if (customCategory) customCategory.required = show;
        updatePreview();
    }

    function updateDescriptionCount() {
        if (descriptionCount) descriptionCount.textContent = String(description?.value?.length || 0);
    }

    function updatePreview() {
        const title = (nameInput?.value || '').trim() || 'Product title';
        const categoryText = category?.value === 'Others'
            ? ((customCategory?.value || '').trim() || 'Other category')
            : (category?.value || 'Category');
        const discount = Math.min(100, Math.max(0, Number(discountInput?.value || 0)));
        const hasFlash = Boolean(flashInput?.value);
        const hasFreeShipping = Boolean(freeShipping?.checked);
        const hasCOD = Boolean(codToggle?.checked);
        const variantMode = Boolean(variantsToggle?.checked);

        let base = Number(priceInput?.value || 0);
        if (variantMode) {
            const variantPrices = Array.from(variantRows?.querySelectorAll('input[data-variant-price]') || [])
                .map(input => Number(input.value))
                .filter(value => Number.isFinite(value) && value >= 0);
            if (variantPrices.length) base = Math.min(...variantPrices);
        }

        const finalPrice = discount > 0 ? base * (1 - discount / 100) : base;

        previewTitle.textContent = title;
        previewCategory.textContent = categoryText;
        previewPrice.textContent = money(finalPrice);

        if (discount > 0 && base > 0) {
            previewOldPrice.textContent = money(base);
            previewOldPrice.classList.remove('hidden');
            previewDiscountBadge.textContent = `${discount}% OFF`;
            previewDiscountBadge.classList.remove('hidden');
            if (promoPreviewState) promoPreviewState.textContent = `${discount}% OFF`;
            if (promoPreviewOldPrice) {
                promoPreviewOldPrice.textContent = money(base);
                promoPreviewOldPrice.classList.remove('hidden');
            }
            if (promoPreviewPrice) promoPreviewPrice.textContent = money(finalPrice);
            if (promoPreviewNote) {
                promoPreviewNote.textContent = variantMode
                    ? 'This discount will be applied to the buyer-facing variant prices.'
                    : 'The buyer-facing price is discounted automatically from the original price.';
            }
        } else {
            previewOldPrice.classList.add('hidden');
            previewDiscountBadge.classList.add('hidden');
            if (promoPreviewState) promoPreviewState.textContent = 'No discount';
            if (promoPreviewOldPrice) promoPreviewOldPrice.classList.add('hidden');
            if (promoPreviewPrice) promoPreviewPrice.textContent = money(base);
            if (promoPreviewNote) {
                promoPreviewNote.textContent = variantMode
                    ? 'Variant prices will be shown at their original values.'
                    : 'No sale discount is currently applied to this product.';
            }
        }

        previewShippingBadge.classList.toggle('hidden', !hasFreeShipping);
        previewFlashBadge.classList.toggle('hidden', !hasFlash);
        if (previewCODBadge) previewCODBadge.classList.toggle('hidden', !hasCOD);

        if (mobilePreviewTitle) mobilePreviewTitle.textContent = title;
        if (mobilePreviewMeta) mobilePreviewMeta.textContent = `${categoryText} · ${money(finalPrice)}`;
        if (mobilePreviewDiscount) {
            mobilePreviewDiscount.textContent = `${discount}% OFF`;
            mobilePreviewDiscount.classList.toggle('hidden', !(discount > 0 && base > 0));
        }
        mobilePreviewShipping?.classList.toggle('hidden', !hasFreeShipping);
        mobilePreviewFlash?.classList.toggle('hidden', !hasFlash);
        mobilePreviewCOD?.classList.toggle('hidden', !hasCOD);
    }

    function setCoverPreview(file) {
        if (!file || !previewImage) return;
        const url = URL.createObjectURL(file);
        coverPreview.src = url;
        coverPreview.classList.remove('hidden');
        coverPlaceholder.classList.add('hidden');
        previewImage.innerHTML = `<img src="${url}" alt="Product preview">`;
    }

    function clearGalleryObjectUrls() {
        galleryObjectUrls.forEach(url => URL.revokeObjectURL(url));
        galleryObjectUrls = [];
    }

    function galleryFileKey(file) {
        return [file?.name || '', file?.size || 0, file?.lastModified || 0].join('::');
    }

    function syncGalleryInputFiles() {
        if (!galleryInput || typeof DataTransfer === 'undefined') return;

        const transfer = new DataTransfer();
        pendingGalleryFiles.forEach(file => transfer.items.add(file));
        galleryInput.files = transfer.files;
    }

    const DRAFT_LOTTIE_URL = 'https://lottie.host/embed/909177ca-eabe-470c-8426-ef5b814b2f8c/r2Xp54jI2x.lottie';
    let draftFeedbackTimer = null;
    let draftLottiePreloaded = false;

    function clearDraftFeedbackTimers() {
        window.clearTimeout(draftFeedbackTimer);
        draftFeedbackTimer = null;
    }

    function resetDraftLottie() {
        if (draftSaveLottie) {
            draftSaveLottie.classList.remove('is-playing');
            draftSaveLottie.style.display = 'none';
            draftSaveLottie.setAttribute('aria-hidden', 'true');
        }

        if (draftSaveFallback) {
            draftSaveFallback.classList.remove('is-playing');
            draftSaveFallback.style.display = 'none';
            draftSaveFallback.setAttribute('aria-hidden', 'true');
        }
    }

    function playDraftFallback() {
        if (!draftSaveFallback) return;

        draftSaveFallback.classList.remove('is-playing');
        draftSaveFallback.style.display = 'grid';
        draftSaveFallback.setAttribute('aria-hidden', 'false');

        void draftSaveFallback.offsetWidth;

        window.requestAnimationFrame(() => {
            draftSaveFallback.classList.add('is-playing');
        });
    }

    function hideDraftFallback() {
        if (!draftSaveFallback) return;
        draftSaveFallback.classList.remove('is-playing');
        draftSaveFallback.style.display = 'none';
        draftSaveFallback.setAttribute('aria-hidden', 'true');
    }

    function preloadDraftLottie() {
        if (!draftSaveLottie || draftLottiePreloaded) return;

        draftLottiePreloaded = true;
        draftSaveLottie.classList.remove('is-playing');
        draftSaveLottie.style.display = 'block';
        draftSaveLottie.style.position = 'fixed';
        draftSaveLottie.style.left = '-9999px';
        draftSaveLottie.style.top = '-9999px';
        draftSaveLottie.style.opacity = '0';
        draftSaveLottie.style.pointerEvents = 'none';
        draftSaveLottie.setAttribute('aria-hidden', 'true');

        draftSaveLottie.src = DRAFT_LOTTIE_URL;
    }

    function playDraftLottie(onReady = null) {
        if (!draftSaveLottie) {
            if (typeof onReady === 'function') onReady(false);
            return;
        }

        let settled = false;
        let fallbackTimer = null;

        const finish = (loaded) => {
            if (settled) return;
            settled = true;

            if (fallbackTimer) {
                window.clearTimeout(fallbackTimer);
                fallbackTimer = null;
            }

            draftSaveLottie.removeEventListener('load', handleLoad);

            if (typeof onReady === 'function') {
                onReady(Boolean(loaded));
            }
        };

        const revealLottie = () => {
            draftSaveLottie.style.position = 'static';
            draftSaveLottie.style.left = '';
            draftSaveLottie.style.top = '';
            draftSaveLottie.style.pointerEvents = 'none';
            draftSaveLottie.style.display = 'block';
            draftSaveLottie.style.opacity = '';
            draftSaveLottie.setAttribute('aria-hidden', 'false');

            window.requestAnimationFrame(() => {
                draftSaveLottie.classList.add('is-playing');
            });
        };

        const handleLoad = () => {
            revealLottie();
            finish(true);
        };

        draftSaveLottie.classList.remove('is-playing');
        draftSaveLottie.addEventListener('load', handleLoad, { once: true });

        // Restart the already-warmed Lottie document.
        draftSaveLottie.src = 'about:blank';

        window.requestAnimationFrame(() => {
            draftSaveLottie.src = DRAFT_LOTTIE_URL;
        });

        // If the remote host is blocked/slow, the inline fallback stays visible.
        fallbackTimer = window.setTimeout(() => {
            finish(false);
        }, 900);
    }

    function hideDraftFeedback() {
        clearDraftFeedbackTimers();
        if (!draftFeedback) return;

        draftFeedback.classList.remove('is-visible', 'is-success', 'is-error');
        draftFeedback.setAttribute('aria-hidden', 'true');

        if (draftGoldLoader) draftGoldLoader.style.display = 'grid';
        resetDraftLottie();
    }

    function showDraftSaving(title = 'Saving draft', message = 'Please wait while your changes are saved.') {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();
        resetDraftLottie();

        draftFeedback.classList.remove('is-success', 'is-error');
        draftFeedback.classList.add('is-visible');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) draftGoldLoader.style.display = 'grid';

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;
    }

    function showDraftSaved(title = 'Draft saved', message = 'Your product draft has been saved.', autoHideMs = 2000) {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();

        draftFeedback.classList.remove('is-error');
        draftFeedback.classList.add('is-visible', 'is-success');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) {
            draftGoldLoader.style.display = 'none';
        }

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;

        // Always show a smooth success animation immediately.
        playDraftFallback();

        // Swap to the real Lottie only when it genuinely finishes loading.
        playDraftLottie((loaded) => {
            if (loaded) {
                hideDraftFallback();
            }
        });

        // Success feedback stays on screen for 2 seconds.
        if (autoHideMs > 0) {
            draftFeedbackTimer = window.setTimeout(hideDraftFeedback, autoHideMs);
        }
    }

    function showDraftError(title = 'Draft not saved', message = 'Please try again.', autoHideMs = 1600) {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();
        resetDraftLottie();

        draftFeedback.classList.remove('is-success');
        draftFeedback.classList.add('is-visible', 'is-error');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) draftGoldLoader.style.display = 'none';

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;

        if (autoHideMs > 0) {
            draftFeedbackTimer = window.setTimeout(hideDraftFeedback, autoHideMs);
        }
    }

    function showDraftFeedback(state = 'saving', title = '', message = '', autoHideMs = 0) {
        if (state === 'saved') {
            showDraftSaved(
                title || 'Draft saved',
                message || 'Your product draft has been saved.',
                autoHideMs || 2000
            );
            return;
        }

        if (state === 'error') {
            showDraftError(
                title || 'Draft not saved',
                message || 'Please try again.',
                autoHideMs || 1600
            );
            return;
        }

        showDraftSaving(
            title || 'Saving draft',
            message || 'Please wait while your changes are saved.'
        );
    }

    function setDraftStatus(message = '', state = '') {
        if (!message) {
            hideDraftFeedback();
            return;
        }

        if (state === 'loading') {
            showDraftSaving('Saving draft', message);
            return;
        }

        if (state === 'success') {
            showDraftSaved('Draft saved', message, 2000);
            return;
        }

        if (state === 'error') {
            showDraftError('Draft not saved', message, 1600);
        }
    }

    function showReviewSubmitSuccess() {
        return new Promise(resolve => {
            if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) {
                resolve();
                return;
            }

            clearDraftFeedbackTimers();

            draftFeedback.classList.remove('is-error');
            draftFeedback.classList.add('is-visible', 'is-success');
            draftFeedback.setAttribute('aria-hidden', 'false');

            if (draftGoldLoader) {
                draftGoldLoader.style.display = 'none';
            }

            draftFeedbackTitle.textContent = 'Submitted for review';
            draftFeedbackText.textContent = 'Your product was received and is ready for SARI screening.';

            playDraftLottie();

            draftFeedbackTimer = window.setTimeout(() => {
                hideDraftFeedback();
                resolve();
            }, 1400);
        });
    }

    window.__SARI_PRODUCT_REVIEW_FEEDBACK__ = {
        start() {
            showDraftSaving(
                'Submitting for review',
                'Please wait while your product is securely submitted.'
            );
        },

        success() {
            return showReviewSubmitSuccess();
        },

        hide() {
            hideDraftFeedback();
        }
    };


    function renderGallery() {
        if (!galleryPreview || !galleryEmpty) return;

        clearGalleryObjectUrls();
        galleryPreview.innerHTML = '';

        const total = savedDraftGallery.length + pendingGalleryFiles.length;
        galleryEmpty.classList.toggle('hidden', total > 0);

        if (galleryCount) {
            galleryCount.textContent = `${total} / ${GALLERY_LIMIT} images`;
        }

        savedDraftGallery.forEach((item, index) => {
            if (!item?.url) return;

            const node = document.createElement('div');
            node.className = 'seller-create-gallery-thumb';
            node.innerHTML = `
                <img src="${item.url}" alt="Saved gallery image ${index + 1}">
                <span class="seller-gallery-saved-badge">Saved draft</span>
            `;
            galleryPreview.appendChild(node);
        });

        pendingGalleryFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            galleryObjectUrls.push(url);

            const node = document.createElement('div');
            node.className = 'seller-create-gallery-thumb';
            node.innerHTML = `
                <img src="${url}" alt="Selected gallery image ${index + 1}">
                <button type="button" class="seller-gallery-remove" data-remove-pending-gallery="${index}" aria-label="Remove selected image">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M6 6l12 12"></path>
                        <path d="M18 6 6 18"></path>
                    </svg>
                </button>
            `;
            galleryPreview.appendChild(node);
        });
    }

    function addGalleryFiles(files) {
        const incoming = Array.from(files || []).filter(file => file instanceof File);
        if (!incoming.length) return;

        const existingKeys = new Set(pendingGalleryFiles.map(galleryFileKey));
        const availableSlots = Math.max(0, GALLERY_LIMIT - savedDraftGallery.length - pendingGalleryFiles.length);
        let added = 0;

        for (const file of incoming) {
            if (added >= availableSlots) break;

            const key = galleryFileKey(file);
            if (existingKeys.has(key)) continue;

            pendingGalleryFiles.push(file);
            existingKeys.add(key);
            added++;
        }

        syncGalleryInputFiles();
        renderGallery();

        if (incoming.length > added) {
            setDraftStatus(`Gallery limit is ${GALLERY_LIMIT} images.`, 'error');
            window.setTimeout(() => {
                if (draftStatus?.textContent?.includes('Gallery limit')) setDraftStatus('');
            }, 2600);
        }
    }

    galleryPreview?.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-pending-gallery]');
        if (!button) return;

        const index = Number(button.dataset.removePendingGallery);
        if (!Number.isInteger(index) || index < 0 || index >= pendingGalleryFiles.length) return;

        pendingGalleryFiles.splice(index, 1);
        syncGalleryInputFiles();
        renderGallery();
    });

    async function savedGalleryAsFiles() {
        const files = [];

        for (let index = 0; index < savedDraftGallery.length; index++) {
            const item = savedDraftGallery[index];
            if (!item?.url) continue;

            const response = await fetch(item.url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error('Unable to preserve one of the saved draft images.');
            }

            const blob = await response.blob();
            const extension = blob.type === 'image/png'
                ? 'png'
                : (blob.type === 'image/webp' ? 'webp' : 'jpg');

            files.push(new File(
                [blob],
                `saved-gallery-${index + 1}.${extension}`,
                { type: blob.type || 'image/jpeg' }
            ));
        }

        return files;
    }

    function syncVariantMode() {
        const active = Boolean(variantsToggle?.checked);
        hasVariants.value = active ? '1' : '0';
        variantsArea.classList.toggle('hidden', !active);
        priceInput.disabled = active;
        stockInput.disabled = active;
        priceInput.required = !active;
        stockInput.required = !active;
        if (active && !variantRows.children.length) addVariant();
        updatePreview();
    }

    function syncVariantOptions(row) {
        const size = row.querySelector('[data-variant-size]')?.value?.trim() || '';
        const color = row.querySelector('[data-variant-color]')?.value?.trim() || '';
        const hidden = row.querySelector('[data-variant-options]');
        const options = {};
        if (size) options.Size = size;
        if (color) options.Color = color;
        if (hidden) hidden.value = JSON.stringify(options);
    }

    function updateVariantCount() {
        const count = variantRows?.children.length || 0;
        variantCount.textContent = `${count} ${count === 1 ? 'variant' : 'variants'}`;
        variantWrap.classList.toggle('hidden', count === 0);
        updatePreview();
    }

    function addVariant(data = {}) {
        const index = variantIndex++;
        const row = document.createElement('tr');
        row.dataset.variantRow = String(index);
        row.innerHTML = `
            <td>
                <label class="seller-variant-image-label">
                    <input name="variants[${index}][image]" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" data-variant-image>
                    <span data-variant-image-preview>
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path></svg>
                    </span>
                </label>
            </td>
            <td><input data-variant-size type="text" value="${data.size || ''}" placeholder="Small (S)"></td>
            <td>
                <input data-variant-color type="text" value="${data.color || ''}" placeholder="Blue / Black">
                <input data-variant-options name="variants[${index}][options]" type="hidden" value="">
            </td>
            <td><input name="variants[${index}][sku]" type="text" value="${data.sku || ''}" placeholder="SKU"></td>
            <td><input data-variant-price name="variants[${index}][price]" type="number" min="0" step="0.01" value="${data.price ?? ''}" placeholder="0.00" required></td>
            <td><input name="variants[${index}][stock]" type="number" min="0" value="${data.stock ?? ''}" placeholder="0" required></td>
            <td><button type="button" data-remove-variant class="grid h-8 w-8 place-items-center rounded-[8px] border border-[#f0d0d0] bg-white text-[#d64b4b] hover:bg-[#fff5f5]" aria-label="Remove variant"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14"></path><path d="M9 7V5h6v2"></path><path d="M8 7l1 12h6l1-12"></path></svg></button></td>
        `;
        variantRows.appendChild(row);
        syncVariantOptions(row);

        row.querySelectorAll('[data-variant-size],[data-variant-color]').forEach(input => input.addEventListener('input', () => syncVariantOptions(row)));
        row.querySelector('[data-variant-price]')?.addEventListener('input', queuePreviewUpdate);
        row.querySelector('[data-remove-variant]')?.addEventListener('click', () => { row.remove(); updateVariantCount(); });
        row.querySelector('[data-variant-image]')?.addEventListener('change', event => {
            const file = event.target.files?.[0];
            if (!file) return;
            const target = row.querySelector('[data-variant-image-preview]');
            target.innerHTML = `<img src="${URL.createObjectURL(file)}" alt="Variant image">`;
        });
        updateVariantCount();
    }

    function addSpecification(data = {}) {
        const index = specIndex++;
        const row = document.createElement('div');
        row.className = 'seller-spec-row';
        row.innerHTML = `
            <input name="specifications[${index}][name]" value="${data.name || ''}" class="seller-create-input" placeholder="Attribute e.g. Material">
            <input name="specifications[${index}][value]" value="${data.value || ''}" class="seller-create-input" placeholder="Value e.g. Solid Wood">
            <input name="specifications[${index}][unit]" value="${data.unit || ''}" class="seller-create-input" placeholder="Unit">
            <button type="button" data-remove-spec class="grid h-[42px] w-9 place-items-center rounded-[9px] border border-[#e1e5ea] bg-white text-[#7d858f]" aria-label="Remove specification">×</button>
        `;
        specRows.appendChild(row);
        specsEmpty.classList.add('hidden');
        row.querySelector('[data-remove-spec]')?.addEventListener('click', () => {
            row.remove();
            specsEmpty.classList.toggle('hidden', specRows.children.length > 0);
        });
    }

    function draftBoolean(value) {
        return value === true
            || value === 1
            || value === '1'
            || value === 'true'
            || value === 'on';
    }

    function parseVariantOptions(value) {
        if (value && typeof value === 'object' && !Array.isArray(value)) return value;

        try {
            const parsed = JSON.parse(String(value || '{}'));
            return parsed && typeof parsed === 'object' ? parsed : {};
        } catch (_) {
            return {};
        }
    }

    function assignDraftField(name, value) {
        const field = form?.elements?.namedItem(name);
        if (!field || value === undefined || value === null) return;

        if (field instanceof RadioNodeList) return;

        if (field.type === 'checkbox') {
            field.checked = draftBoolean(value);
            return;
        }

        if (field.type !== 'file') {
            field.value = value;
        }
    }

    function restoreDraftPayload(draft) {
        if (!draft?.payload || !form) return false;

        const payload = draft.payload || {};

        if (draftId) draftId.value = draft.id || '';

        [
            'name',
            'brand',
            'sku',
            'voucher_code',
            'condition',
            'price',
            'stock',
            'discount',
            'flash_sale_ends_at',
            'package_weight',
            'package_length',
            'package_width',
            'package_height',
            'preparation_days',
            'low_stock_threshold',
            'description',
            'custom_category',
        ].forEach(name => assignDraftField(name, payload[name] ?? ''));

        if (category) {
            category.value = payload.category || '';
        }

        if (freeShipping) {
            freeShipping.checked = draftBoolean(payload.free_shipping);
        }

        if (codToggle) {
            codToggle.checked = draftBoolean(payload.cash_on_delivery);
        }

        const variantMode = draftBoolean(payload.has_variants);
        if (variantsToggle) variantsToggle.checked = variantMode;
        if (hasVariants) hasVariants.value = variantMode ? '1' : '0';

        if (variantRows) {
            variantRows.innerHTML = '';
            variantIndex = 0;
        }

        const savedVariants = Array.isArray(payload.variants) ? payload.variants : [];
        if (variantMode) {
            savedVariants.forEach(variant => {
                const options = parseVariantOptions(variant?.options);
                addVariant({
                    size: options.Size || options.size || '',
                    color: options.Color || options.color || '',
                    sku: variant?.sku || '',
                    price: variant?.price ?? '',
                    stock: variant?.stock ?? '',
                });
            });
        }

        if (specRows) {
            specRows.innerHTML = '';
            specIndex = 0;
        }

        const savedSpecs = Array.isArray(payload.specifications) ? payload.specifications : [];
        savedSpecs.forEach(spec => {
            addSpecification({
                name: spec?.name || '',
                value: spec?.value || '',
                unit: spec?.unit || '',
            });
        });

        if (specsEmpty) {
            specsEmpty.classList.toggle('hidden', savedSpecs.length > 0);
        }

        if (draft.cover_image_url && coverPreview && coverPlaceholder) {
            coverPreview.src = draft.cover_image_url;
            coverPreview.classList.remove('hidden');
            coverPlaceholder.classList.add('hidden');

            if (previewImage) {
                previewImage.innerHTML = `<img src="${draft.cover_image_url}" alt="Product preview">`;
            }
        }

        savedDraftGallery = Array.isArray(draft.gallery_images)
            ? draft.gallery_images.slice(0, GALLERY_LIMIT)
            : [];

        pendingGalleryFiles = [];
        if (galleryInput) galleryInput.value = '';

        syncCategoryPicker();
        updateCustomCategory();
        updateDescriptionCount();
        syncVariantMode();
        updateVariantCount();
        renderGallery();
        updatePreview();

        // Restoring an existing draft is intentionally silent so the header
        // stays clean. The active draft remains visible in the Drafts menu.
        hideDraftFeedback();

        return true;
    }

    function escapeDraftHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function currentDraftIdFromUrl() {
        const params = new URLSearchParams(window.location.search);

        // Explicit New Draft mode must never reuse an old hidden draft id.
        if (params.get('new') === '1') {
            return 0;
        }

        const value = Number(params.get('draft') || draftId?.value || 0);
        return Number.isInteger(value) && value > 0 ? value : 0;
    }

    function isNewDraftMode() {
        return new URLSearchParams(window.location.search).get('new') === '1';
    }

    function setDraftUrl(id = 0) {
        const url = new URL(window.location.href);

        url.searchParams.delete('new');
        url.searchParams.delete('fresh');

        if (id > 0) {
            url.searchParams.set('draft', String(id));
        } else {
            url.searchParams.delete('draft');
        }

        window.history.replaceState({}, '', url);
    }

    function closeDraftMenu() {
        draftManager?.classList.remove('is-open');
        draftMenu?.classList.add('hidden');
        draftMenuButton?.setAttribute('aria-expanded', 'false');
    }

    function toggleDraftMenu() {
        if (!draftMenu || !draftManager) return;
        const opening = draftMenu.classList.contains('hidden');
        draftMenu.classList.toggle('hidden', !opening);
        draftManager.classList.toggle('is-open', opening);
        draftMenuButton?.setAttribute('aria-expanded', opening ? 'true' : 'false');
    }

    function renderDraftList(drafts = []) {
        const list = Array.isArray(drafts) ? drafts : [];
        const activeId = currentDraftIdFromUrl();

        if (draftCount) draftCount.textContent = String(list.length);
        if (!draftList) return;

        if (!list.length) {
            draftList.innerHTML = '<div class="seller-draft-empty">No saved drafts yet.</div>';
            return;
        }

        draftList.innerHTML = list.map(draft => {
            const savedAt = draft?.saved_at ? new Date(draft.saved_at) : null;
            const time = savedAt && !Number.isNaN(savedAt.getTime())
                ? savedAt.toLocaleString([], { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' })
                : 'Saved draft';

            const mediaText = Number(draft?.gallery_count || 0) > 0
                ? ` · ${Number(draft.gallery_count)} gallery image${Number(draft.gallery_count) === 1 ? '' : 's'}`
                : '';

            const thumbnail = draft?.cover_image_url
                ? `<img src="${escapeDraftHtml(draft.cover_image_url)}" alt="">`
                : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2"></rect><path d="m7 16 3.5-4 2.5 3 2-2 2 3"></path><circle cx="15.5" cy="9.5" r="1.2"></circle></svg>`;

            return `
                <div class="seller-draft-item ${Number(draft?.id) === activeId ? 'is-current' : ''}" data-draft-row="${Number(draft?.id || 0)}">
                    <button type="button" class="seller-draft-open" data-open-draft="${Number(draft?.id || 0)}">
                        <span class="seller-draft-thumb">${thumbnail}</span>
                        <span class="seller-draft-open-copy">
                            <strong>${escapeDraftHtml(draft?.name || 'Untitled product')}</strong>
                            <span>${escapeDraftHtml(draft?.category || 'No category')} · ${escapeDraftHtml(time)}${mediaText}</span>
                        </span>
                    </button>

                    <button type="button" class="seller-draft-delete" data-delete-draft="${Number(draft?.id || 0)}" aria-label="Delete draft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 7h16"></path>
                            <path d="M9 7V5h6v2"></path>
                            <path d="M7 7l1 12h8l1-12"></path>
                            <path d="M10 11v5"></path>
                            <path d="M14 11v5"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    async function deleteServerDraft(id) {
        if (!id) return;

        try {
            const body = new FormData();
            body.append('draft_id', String(id));

            const response = await fetch(@json(route('seller.products.draft.delete')), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body,
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'Unable to delete draft.');
            }

            renderDraftList(data.drafts || []);

            if (currentDraftIdFromUrl() === Number(id)) {
                window.location.href = @json(route('seller.products.create'));
                return;
            }

            hideDraftFeedback();
        } catch (error) {
            setDraftStatus(error?.message || 'Unable to delete draft.', 'error');
        }
    }

    async function fetchDraftState(draftIdToOpen = 0) {
        const url = new URL(@json(route('seller.products.draft')), window.location.origin);

        if (Number(draftIdToOpen) > 0) {
            url.searchParams.set('draft_id', String(Number(draftIdToOpen)));
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data?.ok) {
            throw new Error(data?.message || 'Unable to load saved drafts.');
        }

        renderDraftList(data?.drafts || []);
        return data;
    }

    async function refreshDraftList() {
        try {
            return await fetchDraftState(0);
        } catch (error) {
            console.debug('Draft list refresh failed.', error);
            return null;
        }
    }

    async function openDraftById(id) {
        const draftNumber = Number(id || 0);
        if (!Number.isInteger(draftNumber) || draftNumber <= 0) return;

        closeDraftMenu();
        showDraftSaving('Loading draft', 'Restoring your saved product details.');

        try {
            const data = await fetchDraftState(draftNumber);

            if (!data?.draft) {
                throw new Error('That draft is no longer available.');
            }

            setDraftUrl(draftNumber);
            restoreDraftPayload(data.draft);
            renderDraftList(data.drafts || []);

            hideDraftFeedback();

            window.scrollTo({
                top: Math.max(0, document.querySelector('.seller-create-page')?.offsetTop || 0),
                behavior: 'smooth',
            });
        } catch (error) {
            showDraftFeedback(
                'error',
                'Draft could not open',
                error?.message || 'Please try again.',
                1500
            );
        }
    }

    async function loadServerDraft() {
        try {
            if (isNewDraftMode()) {
                if (draftId) draftId.value = '';
                await fetchDraftState(0);
                return;
            }

            const requestedDraftId = currentDraftIdFromUrl();
            const data = await fetchDraftState(requestedDraftId);

            if (requestedDraftId > 0 && data?.draft) {
                restoreDraftPayload(data.draft);
            }
        } catch (error) {
            console.debug('Saved product drafts could not be loaded.', error);
        }
    }

    async function saveDraftNow() {
        if (!form || !saveDraft) return;

        saveDraft.disabled = true;
        showDraftSaving('Saving draft', 'Please wait while your changes are saved.');

        try {
            const formData = new FormData(form);

            // New Draft mode is explicit. Never allow a stale hidden draft_id
            // to overwrite the previously saved product draft.
            if (isNewDraftMode()) {
                formData.delete('draft_id');
                formData.append('force_new_draft', '1');
            }

            // The file input only contains newly selected local files. When a
            // previous server draft already has gallery media, re-attach those
            // files too so the server can safely replace its gallery set
            // without losing previously saved images.
            formData.delete('gallery_images[]');

            const savedFiles = await savedGalleryAsFiles();
            const completeGallery = [...savedFiles, ...pendingGalleryFiles].slice(0, GALLERY_LIMIT);

            completeGallery.forEach(file => {
                formData.append('gallery_images[]', file, file.name);
            });

            const response = await fetch(@json(route('seller.products.draft.save')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.ok) {
                const firstError = Object.values(data?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || data.message || 'Unable to save draft.');
            }

            if (data.draft?.id && draftId) {
                draftId.value = data.draft.id;
                setDraftUrl(Number(data.draft.id));
            }

            if (Array.isArray(data.drafts)) {
                renderDraftList(data.drafts);
            } else {
                await refreshDraftList();
            }

            savedDraftGallery = Array.isArray(data.draft?.gallery_images)
                ? data.draft.gallery_images.slice(0, GALLERY_LIMIT)
                : savedDraftGallery;

            pendingGalleryFiles = [];
            if (galleryInput) galleryInput.value = '';
            renderGallery();

            showDraftSaved(
                'Draft saved',
                'Your product draft has been saved.',
                2000
            );
        } catch (error) {
            showDraftError(
                'Draft not saved',
                error?.message || 'Please try again.',
                1600
            );
        } finally {
            saveDraft.disabled = false;
        }
    }

    draftMenuButton?.addEventListener('click', async event => {
        event.stopPropagation();

        const opening = draftMenu?.classList.contains('hidden');
        toggleDraftMenu();

        if (opening) {
            // Drafts were already loaded on page boot. Refresh silently in background.
            window.setTimeout(() => refreshDraftList(), 0);
        }
    });

    draftMenu?.addEventListener('click', event => {
        event.stopPropagation();

        const openButton = event.target.closest('[data-open-draft]');
        if (openButton) {
            const id = Number(openButton.dataset.openDraft || 0);
            if (id > 0) {
                openDraftById(id);
            }
            return;
        }

        const deleteButton = event.target.closest('[data-delete-draft]');
        if (deleteButton) {
            const id = Number(deleteButton.dataset.deleteDraft || 0);
            if (id > 0 && window.confirm('Delete this saved draft?')) {
                deleteServerDraft(id);
            }
        }
    });

    newBlankProduct?.addEventListener('click', () => {
        closeDraftMenu();

        if (draftId) {
            draftId.value = '';
        }

        const url = new URL(@json(route('seller.products.create')), window.location.origin);
        url.searchParams.set('new', '1');
        url.searchParams.set('fresh', String(Date.now()));

        // Full navigation is intentional here: it guarantees a completely
        // blank form and prevents an old draft_id from being reused.
        window.location.assign(url.toString());
    });

    document.addEventListener('click', event => {
        if (draftManager && !draftManager.contains(event.target)) {
            closeDraftMenu();
        }
    });

    categoryTrigger?.addEventListener('click', event => {
        event.stopPropagation();
        toggleCategoryMenu();
    });

    categoryMenu?.querySelectorAll('[data-category-option]').forEach(option => {
        option.addEventListener('click', () => {
            if (!category) return;
            category.value = option.dataset.categoryOption || '';
            category.dispatchEvent(new Event('change', { bubbles: true }));
            syncCategoryPicker();
            closeCategoryMenu();
        });
    });

    document.addEventListener('click', event => {
        if (categoryPicker && !categoryPicker.contains(event.target)) {
            closeCategoryMenu();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeCategoryMenu();
    });

        document.querySelectorAll('[data-create-nav]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(`create-${button.dataset.createNav}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            document.querySelectorAll('[data-create-nav]').forEach(button => button.classList.toggle('is-active', button.dataset.createNav === entry.target.dataset.createSection));
        });
    }, { rootMargin: '-25% 0px -60% 0px', threshold: 0.01 });
    document.querySelectorAll('[data-create-section]').forEach(section => observer.observe(section));

    category?.addEventListener('change', updateCustomCategory);
    customCategory?.addEventListener('input', queuePreviewUpdate);
    nameInput?.addEventListener('input', queuePreviewUpdate);
    priceInput?.addEventListener('input', queuePreviewUpdate);
    discountInput?.addEventListener('input', queuePreviewUpdate);
    flashInput?.addEventListener('change', queuePreviewUpdate);
    freeShipping?.addEventListener('change', queuePreviewUpdate);
    codToggle?.addEventListener('change', queuePreviewUpdate);
    description?.addEventListener('input', updateDescriptionCount);
    coverInput?.addEventListener('change', event => setCoverPreview(event.target.files?.[0]));
    galleryInput?.addEventListener('change', event => {
        addGalleryFiles(event.target.files);
    });
    variantsToggle?.addEventListener('change', syncVariantMode);
    addVariantButton?.addEventListener('click', () => addVariant());
    document.getElementById('addSpecificationRow')?.addEventListener('click', () => addSpecification());
    saveDraft?.addEventListener('click', saveDraftNow);

    form.addEventListener('submit', event => {
        if (variantsToggle?.checked) {
            const rows = Array.from(variantRows.querySelectorAll('tr'));
            if (!rows.length) {
                event.preventDefault();
                alert('Add at least one product variant.');
                return;
            }
            const invalidOption = rows.find(row => {
                syncVariantOptions(row);
                const size = row.querySelector('[data-variant-size]')?.value?.trim();
                const color = row.querySelector('[data-variant-color]')?.value?.trim();
                return !size && !color;
            });
            if (invalidOption) {
                event.preventDefault();
                alert('Every variant needs a size/variant or color/design value.');
                invalidOption.querySelector('[data-variant-size]')?.focus();
            }
        }
    });

    syncCategoryPicker();
    updateCustomCategory();
    updateDescriptionCount();
    syncVariantMode();
    renderGallery();
    updatePreview();

    hideDraftFeedback();
    preloadDraftLottie();

    const shouldRestoreServerDraft = @json(!$errors->any());
    if (shouldRestoreServerDraft) {
        // Load draft state immediately on Add Product boot.
        loadServerDraft();
    }
    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(bootSellerCreateProductPage);
    } else {
        window.requestAnimationFrame(() => window.requestAnimationFrame(bootSellerCreateProductPage));
    }
})();
</script>
@endpush
