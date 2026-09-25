@extends('layouts.seller')

@section('title', 'Archived Products — SARI Seller')
@section('page-title', 'Archived Products')

@section('content')
@php
    $totalHistoryCount = $products->count();
    $archivedCount = $products->where('archive_reason', '!=', 'deleted')->count();
    $deletedCount = $products->where('archive_reason', 'deleted')->count();
    $approvedHistoryCount = $products->where('moderation_status', 'approved')->count();
@endphp

<style>
    .archive-shell {
        --archive-gold: #D89B10;
        --archive-gold-dark: #B67A08;
        --archive-gold-soft: #FFF7E6;
        --archive-border: #E7E9EE;
        --archive-border-soft: #EEF1F4;
        --archive-text: #202124;
        --archive-muted: #8A919B;
        --archive-page: #F4F5F7;
        --archive-green: #4F7D63;
        --archive-green-soft: #EDF7F1;
        --archive-blue: #5A7B98;
        --archive-blue-soft: #EEF5FB;
        --archive-red: #B95D5D;
        --archive-red-soft: #FFF0F0;
        width: 100%;
        max-width: 1700px;
        margin: 0 auto;
        padding-bottom: 20px;
        color: var(--archive-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    .archive-shell * { box-sizing: border-box; }

    .archive-header { margin-bottom: 16px; }

    .archive-eyebrow {
        margin: 0;
        color: #A86F0B;
        font-size: 9.5px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: .17em;
        text-transform: uppercase;
    }

    .archive-title {
        margin: 7px 0 0;
        color: #1D1915;
        font-size: clamp(28px, 2.1vw, 35px);
        font-weight: 650;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .archive-title span:last-child { color: var(--archive-gold); }

    .archive-subtitle {
        max-width: 760px;
        margin: 10px 0 0;
        color: #7D746B;
        font-size: 10.5px;
        line-height: 1.6;
    }

    .archive-filter-panel {
        display: grid;
        grid-template-columns: minmax(320px, 1fr) 170px auto;
        gap: 9px;
        margin-top: 16px;
        padding: 10px;
        border: 1px solid var(--archive-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(15, 23, 42, .03);
    }

    .archive-search-field,
    .archive-history-dropdown { position: relative; }

    .archive-search-field > svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 15px;
        height: 15px;
        transform: translateY(-50%);
        color: #8B95A1;
        pointer-events: none;
    }

    #archiveProductSearch,
    #archiveClearFilters {
        height: 40px;
        border-radius: 10px;
        font-family: inherit;
        font-size: 9px;
    }

    #archiveProductSearch {
        width: 100%;
        border: 1px solid #DDE3EA;
        background: #fff;
        color: #354052;
        outline: none;
        padding: 0 12px 0 38px;
    }

    #archiveProductSearch:focus {
        border-color: rgba(216, 155, 16, .65);
        box-shadow: 0 0 0 3px rgba(216, 155, 16, .08);
    }

    .archive-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        overflow: hidden !important;
        clip: rect(0 0 0 0) !important;
        clip-path: inset(50%) !important;
        white-space: nowrap !important;
        border: 0 !important;
        padding: 0 !important;
        margin: -1px !important;
    }

    .archive-history-button {
        display: flex;
        width: 100%;
        height: 40px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #DDE3EA;
        border-radius: 10px;
        background: #fff;
        padding: 0 11px;
        color: #354052;
        font-family: inherit;
        font-size: 9px;
        font-weight: 650;
        cursor: pointer;
        outline: none;
        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;
    }

    .archive-history-button:hover {
        background: #FAFBFC;
        border-color: #D3D9E1;
    }

    .archive-history-button[aria-expanded="true"] {
        border-color: rgba(216, 155, 16, .55);
        box-shadow: 0 0 0 3px rgba(216, 155, 16, .08);
    }

    .archive-history-button-left {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }

    .archive-history-button-left > svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
        color: #9A7A3A;
    }

    .archive-history-chevron {
        width: 13px;
        height: 13px;
        flex: 0 0 auto;
        color: #8B95A1;
        transition: transform .16s ease;
    }

    .archive-history-button[aria-expanded="true"] .archive-history-chevron {
        transform: rotate(180deg);
    }

    .archive-history-menu {
        position: absolute;
        top: calc(100% + 7px);
        right: 0;
        z-index: 90;
        width: min(310px, 92vw);
        overflow: hidden;
        border: 1px solid #E2E7ED;
        border-radius: 14px;
        background: #fff;
        padding: 6px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, .13);
    }

    .archive-history-menu[hidden] {
        display: none !important;
    }

    .archive-history-option {
        display: flex;
        width: 100%;
        min-height: 50px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        padding: 9px 10px;
        text-align: left;
        cursor: pointer;
        transition: background-color .13s ease;
    }

    .archive-history-option:hover {
        background: #F8FAFC;
    }

    .archive-history-option.is-selected {
        background: #FFF8E9;
    }

    .archive-history-option span {
        display: block;
        min-width: 0;
    }

    .archive-history-option strong {
        display: block;
        color: #293240;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.3;
    }

    .archive-history-option small {
        display: block;
        margin-top: 3px;
        color: #8A919B;
        font-size: 7.5px;
        line-height: 1.4;
    }

    .archive-history-check {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
        opacity: 0;
        color: #C08312;
    }

    .archive-history-option.is-selected .archive-history-check {
        opacity: 1;
    }

    #archiveClearFilters {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        padding: 0 14px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    #archiveClearFilters {
        border: 1px solid #E0E5EB;
        background: #fff;
        color: #697586;
    }

    #archiveClearFilters:hover { background: #F8FAFC; }

    .archive-card-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .archive-product-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #E4E8ED;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .archive-product-card:hover {
        border-color: #D7DDE5;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        transform: translateY(-1px);
    }

    .archive-card-media {
        position: relative;
        margin: 10px 10px 0;
        height: 166px;
        overflow: hidden;
        border-radius: 12px;
        background: #F5F7F9;
    }

    .archive-card-media img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .archive-image-placeholder {
        display: grid;
        width: 100%;
        height: 100%;
        place-items: center;
        color: #B5BDC7;
    }

    .archive-image-placeholder svg { width: 34px; height: 34px; }

    .archive-card-badge-row {
        position: absolute;
        top: 9px;
        left: 9px;
        right: 9px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 7px;
        pointer-events: none;
    }

    .archive-badge {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 7.5px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(15, 23, 42, .05);
        backdrop-filter: blur(8px);
    }

    .archive-badge.pending { background: #EEF5FB; color: #557A9A; }
    .archive-badge.approved { background: #EDF7F1; color: #4F7D63; }
    .archive-badge.flagged,
    .archive-badge.rejected { background: #FFF0F0; color: #B95D5D; }
    .archive-badge.default { background: rgba(255,255,255,.94); color: #687386; }

    .archive-type-badge {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        border-radius: 999px;
        padding: 0 9px;
        font-size: 7px;
        font-weight: 700;
        background: rgba(32, 33, 36, .88);
        color: #fff;
    }

    .archive-type-badge.deleted { background: rgba(173, 74, 74, .92); }

    .archive-card-body { padding: 11px 13px 12px; }

    .archive-card-mainline {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .archive-card-name {
        min-width: 0;
        overflow: hidden;
        margin: 0;
        color: #242424;
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .archive-card-price {
        flex: 0 0 auto;
        color: #B97805;
        font-size: 11.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .archive-card-meta {
        overflow: hidden;
        margin-top: 4px;
        color: #8A8178;
        font-size: 7.8px;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .archive-card-divider {
        height: 1px;
        margin: 10px 0 9px;
        background: #E8E3DD;
    }

    .archive-card-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
    }

    .archive-card-stock {
        color: #403A34;
        font-size: 8.5px;
        font-weight: 700;
    }

    .archive-card-date {
        margin-top: 2px;
        color: #9A9188;
        font-size: 7px;
    }

    .archive-card-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .archive-icon-button {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 0;
        border-radius: 9px;
        background: transparent;
        cursor: pointer;
        transition: background-color .14s ease, color .14s ease, transform .14s ease;
    }

    .archive-icon-button svg { width: 15px; height: 15px; }

    .archive-icon-button.restore { color: #B77808; }
    .archive-icon-button.restore:hover { background: #FFF7E6; color: #986305; transform: translateY(-1px); }

    .archive-icon-button.delete {
        color: #FF2D2D;
    }

    .archive-icon-button.delete svg {
        stroke: #FF2D2D !important;
        stroke-width: 2.05 !important;
    }

    .archive-icon-button.delete:hover {
        background: #FFF0F0;
        color: #E60000;
        transform: translateY(-1px);
    }

    .archive-icon-button.delete:hover svg {
        stroke: #E60000 !important;
    }

    .archive-icon-button:disabled {
        cursor: not-allowed;
        opacity: .42;
        transform: none;
    }

    .archive-empty-state {
        grid-column: 1 / -1;
        display: grid;
        min-height: 170px;
        place-items: center;
        border: 1px solid var(--archive-border);
        border-radius: 16px;
        background: #fff;
        padding: 26px 18px;
        text-align: center;
    }

    .archive-empty-icon {
        display: grid;
        width: 44px;
        height: 44px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #E4E8ED;
        border-radius: 12px;
        background: #F8FAFC;
        color: #A7B0BA;
    }

    .archive-empty-icon svg { width: 19px; height: 19px; }

    .archive-empty-title {
        margin-top: 10px;
        color: #202124;
        font-size: 11px;
        font-weight: 700;
    }

    .archive-empty-copy {
        max-width: 460px;
        margin: 5px auto 0;
        color: #8A919B;
        font-size: 8px;
        line-height: 1.55;
    }

    .archive-result-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        margin: 9px 2px 0;
        color: #8A919B;
        font-size: 8px;
        font-weight: 500;
    }

    /* Permanent delete modal */
    .archive-delete-modal {
        position: fixed;
        inset: 0;
        z-index: 240;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 24, 39, .34);
        backdrop-filter: blur(5px);
    }

    .archive-delete-modal.is-open { display: flex; }

    .archive-delete-dialog {
        width: min(100%, 440px);
        border: 1px solid #E7E9EE;
        border-radius: 22px;
        background: #fff;
        padding: 22px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
    }

    .archive-delete-warning-icon {
        display: inline-flex;
        width: auto;
        height: auto;
        align-items: center;
        justify-content: flex-start;
        border: 0;
        border-radius: 0;
        background: transparent;
        color: #E5484D;
    }

    .archive-delete-warning-icon svg {
        width: 27px;
        height: 27px;
        stroke: #E5484D !important;
        stroke-width: 2.15 !important;
    }

    .archive-delete-kicker {
        margin-top: 15px;
        color: #D24A4F;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .archive-delete-title {
        margin-top: 5px;
        color: #202124;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.025em;
    }

    .archive-delete-copy {
        margin-top: 9px;
        color: #737D89;
        font-size: 10px;
        line-height: 1.7;
    }

    .archive-delete-product {
        margin-top: 12px;
        border: 1px solid #ECEFF3;
        border-radius: 12px;
        background: #F8FAFC;
        padding: 10px 12px;
        color: #394250;
        font-size: 10px;
        font-weight: 650;
    }

    .archive-delete-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 20px;
    }

    .archive-delete-actions button {
        height: 40px;
        border-radius: 11px;
        padding: 0 15px;
        font-family: inherit;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
    }

    #archiveDeleteCancel {
        border: 1px solid #DFE4EA;
        background: #fff;
        color: #677384;
    }

    #archiveDeleteConfirm {
        border: 1px solid #E5484D;
        background: #E5484D;
        color: #fff;
        box-shadow: 0 8px 18px rgba(229, 72, 77, .18);
    }

    #archiveDeleteConfirm:hover {
        border-color: #D93F44;
        background: #D93F44;
        box-shadow: 0 9px 20px rgba(217, 63, 68, .22);
    }
    #archiveDeleteConfirm:disabled { opacity: .55; cursor: not-allowed; }

    @media (max-width: 1380px) {
        .archive-card-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 1050px) {
        .archive-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 900px) {
        .archive-filter-panel { grid-template-columns: 1fr 150px auto; }
    }

    @media (max-width: 680px) {
        .archive-title { font-size: clamp(26px, 7.4vw, 32px); }
        .archive-filter-panel { grid-template-columns: 1fr; }
        .archive-card-grid { grid-template-columns: 1fr; }
        .archive-card-media { height: 190px; }
        .archive-delete-actions { flex-direction: column-reverse; }
        .archive-delete-actions button { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .archive-product-card,
        .archive-icon-button { transition: none !important; }
    }
</style>

<div class="archive-shell">
    @if (session('success'))
        <div class="mb-4 rounded-[14px] border border-[#d6e6dc] bg-[#f7fbf8] px-4 py-3 text-[9px] font-medium text-[#4f7d63]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#ecd5d5] bg-[#fff8f8] px-4 py-3 text-[9px] font-medium text-[#9f5f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="archive-header">
        <p class="archive-eyebrow">Archived Products</p>
        <h1 class="archive-title"><span>Archived</span><span>Products</span></h1>
        <p class="archive-subtitle">Search, review, restore, or permanently remove inactive product records from your catalog.</p>
    </header>
    <section class="archive-filter-panel" aria-label="Archive filters">
        <label class="archive-search-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
            <input id="archiveProductSearch" type="search" placeholder="Search archived products..." autocomplete="off">
        </label>

        <div class="archive-history-dropdown" data-history-dropdown>
            <select id="archiveProductFilter" class="archive-native-select" aria-hidden="true" tabindex="-1">
                <option value="">All History</option>
                <option value="archived">Archived</option>
                <option value="deleted">Deleted</option>
            </select>

            <button
                id="archiveHistoryButton"
                type="button"
                class="archive-history-button"
                aria-haspopup="listbox"
                aria-expanded="false"
            >
                <span class="archive-history-button-left">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M6 7v12h12V7"></path>
                        <path d="M9 11h6"></path>
                    </svg>
                    <span id="archiveHistoryLabel">All History</span>
                </span>
                <svg class="archive-history-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m7 10 5 5 5-5"></path>
                </svg>
            </button>

            <div id="archiveHistoryMenu" class="archive-history-menu" role="listbox" hidden>
                <button type="button" class="archive-history-option is-selected" data-history-value="">
                    <span>
                        <strong>All History</strong>
                        <small>Show every inactive product</small>
                    </span>
                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 12 4 4 8-8"></path>
                    </svg>
                </button>

                <button type="button" class="archive-history-option" data-history-value="archived">
                    <span>
                        <strong>Archived</strong>
                        <small>Products available to restore</small>
                    </span>
                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 12 4 4 8-8"></path>
                    </svg>
                </button>

                <button type="button" class="archive-history-option" data-history-value="deleted">
                    <span>
                        <strong>Deleted</strong>
                        <small>Recoverable deleted records</small>
                    </span>
                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 12 4 4 8-8"></path>
                    </svg>
                </button>
            </div>
        </div>
        <button id="archiveClearFilters" type="button">Clear</button>
    </section>

    <div class="archive-result-row">
        <span id="archiveResultCount">{{ $products->count() }} archived product{{ $products->count() === 1 ? '' : 's' }}</span>
    </div>

    <section>
        <div id="archiveProductGrid" class="archive-card-grid">
            @forelse ($products as $product)
                @php
                    $archiveType = $product->archive_reason === 'deleted' ? 'deleted' : 'archived';
                    $moderationState = in_array($product->moderation_status, ['pending', 'approved', 'flagged', 'rejected'], true)
                        ? $product->moderation_status
                        : 'default';
                    $moderationLabel = match ($product->moderation_status) {
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'flagged' => 'Flagged',
                        'rejected' => 'Rejected',
                        default => ucfirst((string) ($product->moderation_status ?: 'Inactive')),
                    };
                @endphp

                <article
                    class="archive-product-card"
                    data-archive-product
                    data-archive-type="{{ $archiveType }}"
                    data-moderation-status="{{ strtolower((string) $product->moderation_status) }}"
                    data-archive-search="{{ strtolower(trim(($product->name ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->sku ?? '') . ' ' . ($product->brand ?? ''))) }}"
                >
                    <div class="archive-card-media">
                        @if ($product->image_path)
                            <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        @else
                            <div class="archive-image-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m6.5 16 3.5-3.5 2.5 2.5 2-2 3 3"></path></svg>
                            </div>
                        @endif

                        <div class="archive-card-badge-row">
                            <span class="archive-badge {{ $moderationState }}">{{ $moderationLabel }}</span>
                            <span class="archive-type-badge {{ $archiveType === 'deleted' ? 'deleted' : '' }}">{{ ucfirst($archiveType) }}</span>
                        </div>
                    </div>

                    <div class="archive-card-body">
                        <div class="archive-card-mainline">
                            <h2 class="archive-card-name">{{ $product->name }}</h2>
                            <span class="archive-card-price">₱{{ number_format((float) $product->price, 2) }}</span>
                        </div>

                        <p class="archive-card-meta">
                            {{ $product->category ?: 'Uncategorized' }}
                            @if($product->brand)
                                · {{ $product->brand }}
                            @endif
                        </p>

                        <div class="archive-card-divider"></div>

                        <div class="archive-card-footer">
                            <div>
                                <div class="archive-card-stock">{{ number_format((int) ($product->stock ?? 0)) }} in stock</div>
                                <div class="archive-card-date">
                                    {{ $product->archived_at?->diffForHumans() ?: 'Archived recently' }}
                                    @if($product->sku)
                                        · {{ $product->sku }}
                                    @endif
                                </div>
                            </div>

                            <div class="archive-card-actions">
                                <form
                                    method="POST"
                                    action="{{ route('seller.products.restore', $product) }}"
                                    class="archive-restore-form"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="archive-icon-button restore"
                                        title="Restore product"
                                        aria-label="Restore product"
                                        @if ($seller->isSuspended()) disabled @endif
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 3v6h6"></path></svg>
                                    </button>
                                </form>

                                <button
                                    type="button"
                                    class="archive-icon-button delete"
                                    title="Delete permanently"
                                    aria-label="Delete permanently"
                                    data-archive-delete-open
                                    data-product-name="{{ $product->name }}"
                                    data-delete-url="{{ route('seller.products.delete', $product) }}"
                                    @if ($seller->isSuspended()) disabled @endif
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="archive-empty-state" data-archive-base-empty>
                    <div>
                        <span class="archive-empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path></svg>
                        </span>
                        <div class="archive-empty-title">No archived products</div>
                        <div class="archive-empty-copy">Products you archive or remove from your active catalog will appear here.</div>
                    </div>
                </div>
            @endforelse
        </div>


    </section>

    <div id="archiveDeleteModal" class="archive-delete-modal" aria-hidden="true">
        <div class="archive-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="archiveDeleteTitle">
            <span class="archive-delete-warning-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path></svg>
            </span>
            <div class="archive-delete-kicker">Permanent action</div>
            <h2 id="archiveDeleteTitle" class="archive-delete-title">Are you sure you want to delete this?</h2>
            <p class="archive-delete-copy">This permanently removes the product catalog record. This action cannot be undone. Products linked to protected transaction history may be blocked by the system.</p>
            <div id="archiveDeleteProductName" class="archive-delete-product">Product</div>

            <form id="archivePermanentDeleteForm" method="POST" action="">
                @csrf
                <input type="hidden" name="permanent" value="1">
                <div class="archive-delete-actions">
                    <button id="archiveDeleteCancel" type="button">Cancel</button>
                    <button id="archiveDeleteConfirm" type="submit">Delete permanently</button>
                </div>
            </form>
        </div>
    </div>

    <div id="archiveToastContainer" class="pointer-events-none fixed bottom-5 right-5 z-[260] flex w-[min(92vw,360px)] flex-col gap-3"></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const initArchivePage = () => {
        const searchInput = document.getElementById('archiveProductSearch');
        const typeFilter = document.getElementById('archiveProductFilter');
        const historyButton = document.getElementById('archiveHistoryButton');
        const historyLabel = document.getElementById('archiveHistoryLabel');
        const historyMenu = document.getElementById('archiveHistoryMenu');
        const historyOptions = Array.from(document.querySelectorAll('[data-history-value]'));
        const clearButton = document.getElementById('archiveClearFilters');
        const resultCount = document.getElementById('archiveResultCount');
        const grid = document.getElementById('archiveProductGrid');
        const toastContainer = document.getElementById('archiveToastContainer');

        const deleteModal = document.getElementById('archiveDeleteModal');
        const deleteForm = document.getElementById('archivePermanentDeleteForm');
        const deleteCancel = document.getElementById('archiveDeleteCancel');
        const deleteConfirm = document.getElementById('archiveDeleteConfirm');
        const deleteProductName = document.getElementById('archiveDeleteProductName');

        const totalCountNode = document.querySelector('[data-archive-total-count]');
        const archivedCountNode = document.querySelector('[data-archive-archived-count]');
        const deletedCountNode = document.querySelector('[data-archive-deleted-count]');
        const approvedCountNode = document.querySelector('[data-archive-approved-count]');

        const cards = Array.from(document.querySelectorAll('[data-archive-product]'));
        let pendingDeleteCard = null;

        function numberFrom(node) {
            return Number(String(node?.textContent || '0').replace(/,/g, '')) || 0;
        }

        function setNumber(node, value) {
            if (!node) return;
            node.textContent = Math.max(0, Number(value || 0)).toLocaleString('en-PH');
        }

        function decrementSummary(card) {
            if (!card) return;
            setNumber(totalCountNode, numberFrom(totalCountNode) - 1);

            if ((card.dataset.archiveType || '') === 'deleted') {
                setNumber(deletedCountNode, numberFrom(deletedCountNode) - 1);
            } else {
                setNumber(archivedCountNode, numberFrom(archivedCountNode) - 1);
            }

            if ((card.dataset.moderationStatus || '') === 'approved') {
                setNumber(approvedCountNode, numberFrom(approvedCountNode) - 1);
            }
        }

        function showToast(type, message) {
            if (!toastContainer) return;
            const error = type === 'error';
            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto translate-y-2 opacity-0 rounded-[14px] border px-4 py-3 shadow-[0_14px_30px_rgba(15,23,42,.10)] transition duration-300';
            toast.style.borderColor = error ? '#ECD5D5' : '#D8E6DD';
            toast.style.backgroundColor = error ? '#FFF9F9' : '#F8FBF9';
            toast.innerHTML = `
                <p class="text-[10px] font-bold" style="color:${error ? '#9F5F5F' : '#507560'}">${error ? 'Action required' : 'Success'}</p>
                <p class="mt-1 text-[9px] leading-5" style="color:${error ? '#866767' : '#5D7566'}">${String(message || '')}</p>
            `;
            toastContainer.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 260);
            }, 2800);
        }

        function visibleCards() {
            return cards.filter(card => !card.classList.contains('hidden'));
        }

        function updateResultCount() {
            if (!resultCount) return;
            const visible = visibleCards().length;

            if (cards.length === 0) {
                resultCount.textContent = '0 archived products';
                return;
            }

            if (visible === cards.length) {
                resultCount.textContent = `${cards.length} archived product${cards.length === 1 ? '' : 's'}`;
                return;
            }

            resultCount.textContent = `${visible} product${visible === 1 ? '' : 's'} found`;
        }

        function filterArchive() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const type = (typeFilter?.value || '').trim().toLowerCase();
            let visible = 0;

            cards.forEach(card => {
                const searchable = (card.dataset.archiveSearch || '').toLowerCase();
                const archiveType = (card.dataset.archiveType || '').toLowerCase();
                const matches = (!query || searchable.includes(query)) && (!type || archiveType === type);
                card.classList.toggle('hidden', !matches);
                if (matches) visible += 1;
            });

            if (grid) {
                grid.classList.remove('hidden');
            }

            updateResultCount();
        }

        function historyLabelFor(value) {
            if (value === 'archived') return 'Archived';
            if (value === 'deleted') return 'Deleted';
            return 'All History';
        }

        function syncHistoryDropdown(value = '') {
            if (typeFilter) typeFilter.value = value;
            if (historyLabel) historyLabel.textContent = historyLabelFor(value);

            historyOptions.forEach(option => {
                option.classList.toggle('is-selected', (option.dataset.historyValue || '') === value);
            });
        }

        function closeHistoryMenu() {
            if (!historyMenu || !historyButton) return;
            historyMenu.hidden = true;
            historyButton.setAttribute('aria-expanded', 'false');
        }

        function toggleHistoryMenu() {
            if (!historyMenu || !historyButton) return;
            const opening = historyMenu.hidden;
            historyMenu.hidden = !opening;
            historyButton.setAttribute('aria-expanded', opening ? 'true' : 'false');
        }

        function clearFilters() {
            if (searchInput) searchInput.value = '';
            syncHistoryDropdown('');
            filterArchive();
            searchInput?.focus();
        }

        function renderBaseEmptyIfNeeded() {
            if (!grid || cards.length !== 0) return;
            grid.innerHTML = `
                <div class="archive-empty-state" data-archive-base-empty>
                    <div>
                        <span class="archive-empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path></svg>
                        </span>
                        <div class="archive-empty-title">No archived products</div>
                        <div class="archive-empty-copy">Products you archive or remove from your active catalog will appear here.</div>
                    </div>
                </div>
            `;
        }

        function removeCard(card) {
            if (!card) return;
            decrementSummary(card);
            card.style.transition = 'opacity .20s ease, transform .20s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-4px) scale(.99)';

            setTimeout(() => {
                card.remove();
                const index = cards.indexOf(card);
                if (index >= 0) cards.splice(index, 1);
                renderBaseEmptyIfNeeded();
                filterArchive();
            }, 210);
        }

        async function handleRestoreSubmit(event) {
            event.preventDefault();
            const form = event.currentTarget;
            const button = form.querySelector('button[type="submit"]');
            const card = form.closest('[data-archive-product]');
            if (!form || !button || !card || button.disabled) return;

            const original = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg viewBox="0 0 24 24" class="animate-spin" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 1 1-2.64-6.36"></path></svg>';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload?.message || 'Unable to restore product right now.');

                removeCard(card);
                showToast('success', payload?.message || 'Product restored successfully.');
            } catch (error) {
                button.disabled = false;
                button.innerHTML = original;
                showToast('error', error?.message || 'Unable to restore product.');
            }
        }

        function openDeleteModal(button) {
            if (!deleteModal || !deleteForm || button.disabled) return;
            pendingDeleteCard = button.closest('[data-archive-product]');
            deleteForm.action = button.dataset.deleteUrl || '';
            if (deleteProductName) deleteProductName.textContent = button.dataset.productName || 'Product';
            deleteModal.classList.add('is-open');
            deleteModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            deleteCancel?.focus();
        }

        function closeDeleteModal() {
            deleteModal?.classList.remove('is-open');
            deleteModal?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            pendingDeleteCard = null;
            if (deleteForm) deleteForm.action = '';
        }

        async function handlePermanentDelete(event) {
            event.preventDefault();
            if (!deleteForm || !deleteConfirm || !pendingDeleteCard || !deleteForm.action) return;

            const card = pendingDeleteCard;
            const original = deleteConfirm.textContent;
            deleteConfirm.disabled = true;
            deleteConfirm.textContent = 'Deleting...';

            try {
                const response = await fetch(deleteForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(deleteForm),
                    credentials: 'same-origin',
                });

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const firstError = payload?.errors ? Object.values(payload.errors)?.[0]?.[0] : null;
                    throw new Error(firstError || payload?.message || 'Unable to permanently delete this product.');
                }

                closeDeleteModal();
                removeCard(card);
                showToast('success', payload?.message || 'Product permanently deleted.');
            } catch (error) {
                showToast('error', error?.message || 'Unable to permanently delete product.');
            } finally {
                deleteConfirm.disabled = false;
                deleteConfirm.textContent = original;
            }
        }

        searchInput?.addEventListener('input', filterArchive);

        historyButton?.addEventListener('click', event => {
            event.stopPropagation();
            toggleHistoryMenu();
        });

        historyOptions.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.historyValue || '';
                syncHistoryDropdown(value);
                closeHistoryMenu();
                filterArchive();
            });
        });

        clearButton?.addEventListener('click', clearFilters);

        document.addEventListener('click', event => {
            if (!event.target.closest('[data-history-dropdown]')) {
                closeHistoryMenu();
            }
        });

        document.querySelectorAll('.archive-restore-form').forEach(form => {
            form.addEventListener('submit', handleRestoreSubmit);
        });

        document.querySelectorAll('[data-archive-delete-open]').forEach(button => {
            button.addEventListener('click', () => openDeleteModal(button));
        });

        deleteCancel?.addEventListener('click', closeDeleteModal);
        deleteForm?.addEventListener('submit', handlePermanentDelete);

        deleteModal?.addEventListener('click', event => {
            if (event.target === deleteModal) closeDeleteModal();
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                if (deleteModal?.classList.contains('is-open')) closeDeleteModal();
                closeHistoryMenu();
            }
        });

        syncHistoryDropdown(typeFilter?.value || '');
        filterArchive();
    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(initArchivePage);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initArchivePage, { once: true });
    } else {
        initArchivePage();
    }
})();
</script>
@endpush
