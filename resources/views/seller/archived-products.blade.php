@extends('layouts.seller')

@section('title', 'Archived Products — SARI Seller')

@section('page-title', 'Archived Products')

@push('styles')
<style id="sariSellerEmbeddedCss-archived-products" data-sari-inline-css="archived-products">
@verbatim
/* SARI Seller — Archived Products / Buyer-card-inspired clean UI */
.archive-shell {
  --archive-gold: #c88912;
  --archive-gold-dark: #9b6508;
  --archive-ink: #17202d;
  --archive-muted: #788495;
  --archive-line: #e3e8ef;
  --archive-focus: rgba(200,137,18,.14);
  width: 100%; max-width: 1520px; margin: 0 auto; padding: 15px 10px 28px;
  font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
  color: var(--archive-ink);
}
.archive-shell, .archive-shell * { box-sizing: border-box; }
.archive-header { display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-start; gap: 0; width: 100%; margin: 0 0 20px; padding: 0 0 14px; border-bottom: 1px solid #e9edf1; text-align: left; }
.archive-eyebrow { margin: 0 0 7px; font-size: 10.5px; line-height: 1.4; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #a87316; }
.archive-title { align-self: flex-start; margin: 0; font-size: clamp(27px, 2.3vw, 31px); line-height: 1.12; letter-spacing: -.04em; font-weight: 760; color: #2e343b; text-align: left; }
.archive-title span + span { margin-left: .21em; color: var(--archive-gold); }
.archive-alert { font-size: 12px; line-height: 1.6; }
.archive-subtitle { margin: 8px 0 0; font-size: 13px; color: var(--archive-muted); }

/* Controls are a horizontal row, not another nested floating card. */
.archive-filter-panel { display: grid; grid-template-columns: minmax(0, 1fr) minmax(185px, 210px) auto; align-items: center; gap: 12px; margin: 0; padding: 0; border: 0; background: transparent; box-shadow: none; }
.archive-search-field, .archive-history-dropdown { position: relative; min-width: 0; }
.archive-search-field { display: block; }
.archive-search-field > svg { position: absolute; top: 50%; left: 15px; width: 21px; height: 21px; color: #8c97a6; transform: translateY(-50%); pointer-events: none; }
#archiveProductSearch, .archive-history-button, #archiveClearFilters { height: 48px; font: 600 11.5px/1.3 Poppins, ui-sans-serif, system-ui, sans-serif; border-radius: 14px; outline: none; }
#archiveProductSearch { display: block; width: 100%; padding: 0 16px 0 46px; color: #3f4b59; border: 1px solid #dce2e8; background: #fff; box-shadow: 0 3px 10px rgba(15,23,42,.035); }
#archiveProductSearch::placeholder { color: #98a3b1; }
#archiveProductSearch:focus { border-color: var(--archive-gold); box-shadow: 0 0 0 3px var(--archive-focus); }
.archive-native-select { position: absolute!important; width: 1px!important; height: 1px!important; overflow: hidden!important; clip: rect(0,0,0,0)!important; clip-path: inset(50%)!important; white-space: nowrap!important; border: 0!important; padding: 0!important; margin: -1px!important; }
.archive-history-button { display: flex; align-items: center; justify-content: space-between; gap: 10px; width: 100%; padding: 0 15px; cursor: pointer; border: 1px solid #dce2e8; background: #fff; color: #3f4b59; box-shadow: 0 3px 10px rgba(15,23,42,.035); font-weight: 600; text-align: left; }
.archive-history-button:hover { border-color: #cbd4df; }
.archive-history-button[aria-expanded="true"], .archive-history-button:focus-visible { border-color: var(--archive-gold); box-shadow: 0 0 0 3px var(--archive-focus); }
.archive-history-button-left { display: inline-flex; gap: 0; align-items: center; min-width: 0; }
.archive-history-button-left > svg { display: none; } /* All Status-style text-only filter trigger */
.archive-history-chevron { width: 16px; height: 16px; color: #8793a5; transition: transform .15s ease; }
.archive-history-button[aria-expanded="true"] .archive-history-chevron { transform: rotate(180deg); }
.archive-history-menu { position: absolute; top: calc(100% + 7px); right: 0; z-index: 100; width: max(100%, 270px); max-width: calc(100vw - 32px); overflow: hidden; padding: 8px; border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; box-shadow: 0 14px 30px rgba(15,23,42,.12); }
.archive-history-menu[hidden] { display: none!important; }
.archive-history-option { display: grid; grid-template-columns: 20px minmax(0, 1fr) 16px; align-items: center; gap: 10px; min-height: 52px; width: 100%; text-align: left; padding: 9px 10px; border: 0; border-radius: 9px; background: transparent; cursor: pointer; }
.archive-history-option:hover, .archive-history-option:focus-visible { background: #f5f7fa; outline: none; }
.archive-history-option.is-selected { background: #f2f4f7; }
.archive-history-option > span { display: block; min-width: 0; }
.archive-history-item-icon { display: block; width: 19px; height: 19px; color: #8490a0; stroke: currentColor; flex: none; }
.archive-history-option.is-selected .archive-history-item-icon { color: #637083; }
.archive-history-option strong { display: block; font-size: 11.5px; font-weight: 650; color: #334155; }
.archive-history-option small { display: block; margin-top: 2px; font-size: 10px; line-height: 1.4; color: #8792a1; }
.archive-history-check { width: 16px; height: 16px; flex: none; color: #64748b; opacity: 0; }
.archive-history-option.is-selected .archive-history-check { opacity: 1; }
#archiveClearFilters { display: inline-flex; justify-content: center; align-items: center; min-width: 86px; padding: 0 18px; border: 1px solid #dce2e8; background: #fff; font-size: 12px; font-weight: 700; color: #677588; cursor: pointer; box-shadow: 0 3px 10px rgba(15,23,42,.035); }
#archiveClearFilters:hover, #archiveClearFilters:focus-visible { border-color: #cbd5e1; background: #f5f7fa; color: #445164; outline: none; }
.archive-result-row { display: flex; justify-content: flex-end; min-height: 24px; align-items: center; margin: 10px 1px 14px; font-size: 12px; font-weight: 550; color: #7a8798; }

/* Buyer-style product card proportions, with seller-appropriate archived status. */
.archive-card-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; align-items: stretch; }
.archive-product-card { display: flex; flex-direction: column; min-width: 0; min-height: 380px; overflow: hidden; border: 1px solid #e3e8ee; border-radius: 22px; background: #fff; box-shadow: 0 12px 30px rgba(18,29,45,.065); transition: border-color .14s ease, box-shadow .14s ease; }
.archive-product-card[hidden] { display: none!important; }
.archive-product-card:hover { border-color: #d4dce7; box-shadow: 0 16px 34px rgba(18,29,45,.085); }
.archive-card-media { position: relative; flex: 0 0 auto; aspect-ratio: 1.42; isolation: isolate; overflow: hidden; margin: 12px 12px 0; border-radius: 14px; background: #f2f4f7; }
.archive-card-media > .archive-card-image-bg { position: absolute; inset: -12px; z-index: 0; width: calc(100% + 24px); height: calc(100% + 24px); display: block; object-fit: cover; filter: blur(16px) brightness(.93); opacity: .42; }
.archive-card-media > .archive-card-image-main { position: relative; z-index: 1; width: 100%; height: 100%; display: block; object-fit: contain; object-position: center; }
.archive-image-placeholder { display: grid; width: 100%; height: 100%; place-items: center; color: #aeb8c6; background: #f3f5f8; }
.archive-image-placeholder svg { width: 40px; height: 40px; }
.archive-card-badge-row { position: absolute; z-index: 3; top: 12px; left: 12px; right: 12px; display: flex; align-items: center; flex-wrap: wrap; justify-content: flex-start; gap: 7px; pointer-events: none; }
.archive-badge, .archive-type-badge { display: inline-flex; align-items: center; min-height: 29px; max-width: 100%; padding: 0 12px; border-radius: 999px; font-size: 10px; line-height: 1.1; font-weight: 750; white-space: nowrap; box-shadow: 0 3px 9px rgba(15,23,42,.09); backdrop-filter: blur(5px); }
.archive-badge.pending { background: #e8f1fb; color: #38638c; }
.archive-badge.approved { background: #d7f4df; color: #08754b; }
.archive-badge.flagged, .archive-badge.rejected { background: #ffeded; color: #a83943; }
.archive-badge.default { background: rgba(255,255,255,.93); color: #596579; }
.archive-type-badge { background: rgba(31,33,39,.91); color: #fff; }
.archive-type-badge.deleted { background: rgba(157,54,63,.94); }
.archive-card-body { display: flex; flex: 1 1 auto; flex-direction: column; min-width: 0; padding: 16px 17px 16px; }
.archive-card-mainline { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; min-width: 0; }
.archive-card-name { display: -webkit-box; min-width: 0; min-height: 43px; overflow: hidden; margin: 0; color: #1a202b; font-size: 15px; font-weight: 750; line-height: 1.45; letter-spacing: -.02em; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow-wrap: anywhere; }
.archive-card-price { flex: 0 0 auto; max-width: 49%; padding-top: 1px; color: #c18408; font-size: 15px; line-height: 1.5; font-weight: 800; letter-spacing: -.02em; overflow-wrap: anywhere; text-align: right; }
.archive-card-meta { display: -webkit-box; min-width: 0; min-height: 21px; overflow: hidden; margin: 4px 0 0; color: #8995a6; font-size: 11.5px; line-height: 1.5; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow-wrap: anywhere; }
.archive-card-divider { height: 1px; margin: 15px 0 12px; background: #e8edf2; }
.archive-card-footer { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; margin-top: auto; min-width: 0; }
.archive-card-footer > div:first-child { min-width: 0; }
.archive-card-stock { font-size: 12px; line-height: 1.5; color: #293648; font-weight: 750; }
.archive-card-date { margin-top: 3px; overflow: hidden; color: #8895a5; font-size: 10px; line-height: 1.6; overflow-wrap: anywhere; }
.archive-card-actions { display: flex; flex: 0 0 auto; align-items: center; gap: 7px; }
.archive-card-actions form { margin: 0; }
.archive-icon-button { display: grid; place-items: center; width: 35px; height: 35px; padding: 0; border: 1px solid #e0e6ed; border-radius: 10px; background: #fff; color: #b67a06; cursor: pointer; box-shadow: 0 3px 9px rgba(15,23,42,.04); transition: background .12s ease, border-color .12s ease; }
.archive-icon-button svg { width: 17px; height: 17px; }
.archive-icon-button.restore:hover, .archive-icon-button.restore:focus-visible { border-color: #d6b675; background: #fff9eb; color: #9f6704; outline: none; }
.archive-icon-button.delete { color: #ce454d; }
.archive-icon-button.delete:hover, .archive-icon-button.delete:focus-visible { border-color: #efc5c8; background: #fff3f3; color: #bd2636; outline: none; }
.archive-icon-button:disabled { opacity: .45; cursor: not-allowed; }
.archive-icon-button:disabled:hover { background: #fff; border-color: #e0e6ed; }

/* Search empty-state and no-archive state use the same spacing. */
.archive-empty-state { grid-column: 1 / -1; display: grid; min-height: 245px; width: 100%; place-items: center; padding: 44px 18px; border: 0; border-radius: 0; background: transparent; box-shadow: none; text-align: center; }
.archive-empty-state > div { max-width: 560px; margin-inline: auto; }
.archive-filter-empty[hidden] { display: none!important; }
.archive-empty-icon { display: grid; width: 68px; height: 68px; margin: auto; place-items: center; border: 0; border-radius: 0; background: transparent; box-shadow: none; color: #96a1b0; }
.archive-empty-icon svg { width: 42px; height: 42px; }
.archive-empty-title { margin-top: 14px; font-size: clamp(20px, 1.8vw, 23px); line-height: 1.3; font-weight: 750; color: #273142; }
.archive-empty-copy { max-width: 500px; margin: 8px auto 0; font-size: 13.5px; line-height: 1.7; color: #778597; }

/* Permanent deletion remains visually distinct from ordinary actions. */
.archive-delete-modal { position: fixed; inset: 0; z-index: 240; display: none; align-items: center; justify-content: center; padding: 18px; background: rgba(17,24,39,.46); backdrop-filter: blur(5px); }
.archive-delete-modal.is-open { display: flex; }
.archive-delete-dialog { width: min(100%, 470px); border: 1px solid #e5e9ef; border-radius: 22px; background: #fff; padding: 28px; box-shadow: 0 26px 80px rgba(15,23,42,.20); }
.archive-delete-warning-icon { display: inline-flex; align-items: center; color: #d5434e; }
.archive-delete-warning-icon svg { width: 29px; height: 29px; stroke: currentColor; }
.archive-delete-kicker { margin-top: 12px; color: #bd434d; font-size: 11px; font-weight: 750; text-transform: uppercase; letter-spacing: .08em; }
.archive-delete-title { margin-top: 5px; color: #232c3a; font-size: 21px; line-height: 1.3; font-weight: 750; }
.archive-delete-copy { margin-top: 9px; font-size: 13px; line-height: 1.7; color: #697687; }
.archive-delete-product { margin-top: 14px; border: 1px solid #e6ebf1; border-radius: 11px; background: #f7f9fb; padding: 12px; color: #374355; font-size: 12px; font-weight: 650; overflow-wrap: anywhere; }
.archive-delete-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }
.archive-delete-actions button { min-height: 43px; padding: 0 17px; border-radius: 11px; font: 650 12px Poppins,ui-sans-serif,system-ui,sans-serif; cursor: pointer; }
#archiveDeleteCancel { border: 1px solid #dce2ea; color: #576679; background: #fff; }
#archiveDeleteCancel:hover { background: #f5f7fa; }
#archiveDeleteConfirm { border: 1px solid #d6424b; color: #fff; background: #d6424b; }
#archiveDeleteConfirm:hover { background: #bf303b; }
#archiveDeleteConfirm:disabled { opacity: .55; cursor: not-allowed; }

@media (max-width: 1420px) { .archive-card-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } }
@media (max-width: 1020px) { .archive-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 900px) {
  .archive-filter-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .archive-search-field, .archive-history-dropdown { grid-column: 1 / -1; }
  #archiveClearFilters { grid-column: 2; justify-self: end; min-width: 120px; }
}
@media (max-width: 760px) {
  .archive-shell { padding: 10px 1px 28px; }
  .archive-header { align-items: flex-start; flex-direction: column; }
  .archive-title { font-size: 25px; }
  .archive-filter-panel { grid-template-columns: minmax(0, 1fr); gap: 10px; }
  .archive-search-field, .archive-history-dropdown { grid-column: auto; }
  #archiveClearFilters { grid-column: auto; justify-self: stretch; min-height: 46px; height: 46px; }
  .archive-card-grid { gap: 13px; }
  .archive-card-body { padding: 13px; }
  .archive-card-name { font-size: 14px; }
  .archive-card-price { font-size: 14px; }
  .archive-card-media { margin: 9px 9px 0; }
}
@media (max-width: 560px) {
  .archive-card-grid { grid-template-columns: minmax(0,1fr); }
  .archive-filter-panel { grid-template-columns: 1fr; }
  #archiveClearFilters { grid-column: auto; }
  .archive-card-media { aspect-ratio: 1.53; }
  .archive-product-card { min-height: 340px; }
  .archive-delete-dialog { padding: 22px; }
  .archive-delete-actions { flex-direction: column-reverse; }
  .archive-delete-actions button { width: 100%; }
}
@media (prefers-reduced-motion: reduce) {
  .archive-product-card, .archive-icon-button, .archive-history-chevron { transition: none; }
}

@endverbatim
</style>
@endpush

@section('content')

@php

    $totalHistoryCount = $products->count();

    $archivedCount = $products->where('archive_reason', '!=', 'deleted')->count();

    $deletedCount = $products->where('archive_reason', 'deleted')->count();

    $approvedHistoryCount = $products->where('moderation_status', 'approved')->count();

@endphp



<div class="archive-shell">

    @if (session('success'))

        <div class="archive-alert archive-alert--success mb-4 rounded-[14px] border border-[#d6e6dc] bg-[#f7fbf8] px-4 py-3 text-[9px] font-medium text-[#4f7d63]">

            {{ session('success') }}

        </div>

    @endif

    @if ($errors->any())

        <div class="archive-alert archive-alert--error mb-4 rounded-[14px] border border-[#ecd5d5] bg-[#fff8f8] px-4 py-3 text-[9px] font-medium text-[#9f5f5f]">

            {{ $errors->first() }}

        </div>

    @endif

    <header class="archive-header">

        <p class="archive-eyebrow">Seller Catalog</p>

        <h1 class="archive-title"><span>Archived</span><span>Products</span></h1>

        

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

                        <svg class="archive-history-item-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"></rect><path d="M4 9h16M9 13h6"></path></svg>

                    <span>

                        <strong>All History</strong>

                        <small>Show every inactive product</small>

                    </span>

                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <path d="m6 12 4 4 8-8"></path>

                    </svg>

                </button>

                <button type="button" class="archive-history-option" data-history-value="archived">

                        <svg class="archive-history-item-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v4H4zM6 9v11h12V9M10 13h4"></path></svg>

                    <span>

                        <strong>Archived</strong>

                        <small>Products available to restore</small>

                    </span>

                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <path d="m6 12 4 4 8-8"></path>

                    </svg>

                </button>

                <button type="button" class="archive-history-option" data-history-value="deleted">

                        <svg class="archive-history-item-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"></path></svg>

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

        <button id="archiveClearFilters" type="button">Reset</button>

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

                            <img class="archive-card-image-bg" src="{{ route('seller.products.image', $product) }}" alt="" aria-hidden="true" loading="lazy" decoding="async">
                            <img class="archive-card-image-main" src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">

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
        const shell = document.querySelector('.archive-shell');
        if (!shell || shell.dataset.archiveInitialized === '1') return;
        shell.dataset.archiveInitialized = '1';

        window.__SARI_SELLER_ARCHIVE_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ = lifecycle;

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
        const deleteDialog = deleteModal?.querySelector('.archive-delete-dialog');
        const deleteForm = document.getElementById('archivePermanentDeleteForm');
        const deleteCancel = document.getElementById('archiveDeleteCancel');
        const deleteConfirm = document.getElementById('archiveDeleteConfirm');
        const deleteProductName = document.getElementById('archiveDeleteProductName');

        const totalCountNode = document.querySelector('[data-archive-total-count]');
        const archivedCountNode = document.querySelector('[data-archive-archived-count]');
        const deletedCountNode = document.querySelector('[data-archive-deleted-count]');
        const approvedCountNode = document.querySelector('[data-archive-approved-count]');

        let cards = Array.from(document.querySelectorAll('[data-archive-product]'));
        let pendingDeleteCard = null;
        let deleteTrigger = null;

        /* --------------------------------------------------------------
           Feedback
           -------------------------------------------------------------- */
        const parseResponse = async (response) => {
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                return response.json().catch(() => ({}));
            }

            return {};
        };

        const showToast = (type, message) => {
            if (!toastContainer) return;

            const isError = type === 'error';
            const toast = document.createElement('div');

            toast.className =
                'pointer-events-auto translate-y-2 opacity-0 rounded-[14px] border px-4 py-3 shadow-[0_14px_30px_rgba(15,23,42,.10)] transition duration-300';

            toast.style.borderColor = isError ? '#ECD5D5' : '#D8E6DD';
            toast.style.backgroundColor = isError ? '#FFF9F9' : '#F8FBF9';

            const title = document.createElement('p');
            title.className = 'text-[10px] font-bold';
            title.style.color = isError ? '#9F5F5F' : '#507560';
            title.textContent = isError ? 'Action required' : 'Success';

            const copy = document.createElement('p');
            copy.className = 'mt-1 text-[9px] leading-5';
            copy.style.color = isError ? '#866767' : '#5D7566';
            copy.textContent = String(message || '');

            toast.append(title, copy);
            toastContainer.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            window.setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                window.setTimeout(() => toast.remove(), 260);
            }, 3000);
        };

        /* --------------------------------------------------------------
           Search + History dropdown
           -------------------------------------------------------------- */
        const getVisibleCards = () => cards.filter((card) => !card.hidden);

        let filteredEmpty = document.querySelector('[data-archive-filter-empty]');

        if (!filteredEmpty && grid) {
            filteredEmpty = document.createElement('div');
            filteredEmpty.className = 'archive-empty-state archive-filter-empty';
            filteredEmpty.dataset.archiveFilterEmpty = '';
            filteredEmpty.hidden = true;
            filteredEmpty.innerHTML = `
                <div>
                    <span class="archive-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6"></circle>
                            <path d="m16 16 4 4"></path>
                        </svg>
                    </span>
                    <div class="archive-empty-title">No matching products</div>
                    <div class="archive-empty-copy">Try another search term or change the History filter.</div>
                </div>
            `;
            grid.appendChild(filteredEmpty);
        }

        const updateResultCount = () => {
            if (!resultCount) return;

            const visible = getVisibleCards().length;

            if (cards.length === 0) {
                resultCount.textContent = '0 archived products';
                return;
            }

            if (visible === cards.length) {
                resultCount.textContent =
                    `${cards.length} archived product${cards.length === 1 ? '' : 's'}`;
                return;
            }

            resultCount.textContent =
                `${visible} product${visible === 1 ? '' : 's'} found`;
        };

        const filterArchive = () => {
            const query = String(searchInput?.value || '').trim().toLowerCase();
            const type = String(typeFilter?.value || '').trim().toLowerCase();

            let visible = 0;

            cards.forEach((card) => {
                const searchable = String(card.dataset.archiveSearch || '').toLowerCase();
                const archiveType = String(card.dataset.archiveType || '').toLowerCase();

                const matchesSearch = !query || searchable.includes(query);
                const matchesType = !type || archiveType === type;
                const matches = matchesSearch && matchesType;

                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (filteredEmpty) {
                filteredEmpty.hidden = !(cards.length > 0 && visible === 0);
            }

            updateResultCount();
        };

        const historyLabelFor = (value) => {
            if (value === 'archived') return 'Archived';
            if (value === 'deleted') return 'Deleted';
            return 'All History';
        };

        const syncHistoryDropdown = (value = '') => {
            const normalized = ['archived', 'deleted'].includes(value) ? value : '';

            if (typeFilter) typeFilter.value = normalized;
            if (historyLabel) historyLabel.textContent = historyLabelFor(normalized);

            historyOptions.forEach((option) => {
                const selected = (option.dataset.historyValue || '') === normalized;
                option.classList.toggle('is-selected', selected);
                option.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
        };

        const closeHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;
            historyMenu.hidden = true;
            historyButton.setAttribute('aria-expanded', 'false');
        };

        const openHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;
            historyMenu.hidden = false;
            historyButton.setAttribute('aria-expanded', 'true');

            const selected =
                historyOptions.find((option) => option.classList.contains('is-selected')) ||
                historyOptions[0];

            selected?.focus();
        };

        const toggleHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;

            if (historyMenu.hidden) {
                openHistoryMenu();
            } else {
                closeHistoryMenu();
            }
        };

        const clearFilters = () => {
            if (searchInput) searchInput.value = '';
            syncHistoryDropdown('');
            closeHistoryMenu();
            filterArchive();
            searchInput?.focus();
        };

        searchInput?.addEventListener('input', filterArchive);

        historyButton?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleHistoryMenu();
        });

        historyOptions.forEach((option, index) => {
            option.setAttribute('role', 'option');

            option.addEventListener('click', () => {
                syncHistoryDropdown(option.dataset.historyValue || '');
                closeHistoryMenu();
                filterArchive();
                historyButton?.focus();
            });

            option.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();

                    const direction = event.key === 'ArrowDown' ? 1 : -1;
                    const nextIndex =
                        (index + direction + historyOptions.length) % historyOptions.length;

                    historyOptions[nextIndex]?.focus();
                    return;
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeHistoryMenu();
                    historyButton?.focus();
                }
            });
        });

        clearButton?.addEventListener('click', clearFilters);

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[data-history-dropdown]')) {
                closeHistoryMenu();
            }
        }, { signal });

        /* --------------------------------------------------------------
           Card removal after successful restore / permanent delete
           -------------------------------------------------------------- */
        const numberFrom = (node) => {
            return Number(String(node?.textContent || '0').replace(/,/g, '')) || 0;
        };

        const setNumber = (node, value) => {
            if (!node) return;
            node.textContent = Math.max(0, Number(value || 0)).toLocaleString('en-PH');
        };

        const decrementSummary = (card) => {
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
        };

        const renderBaseEmptyIfNeeded = () => {
            if (!grid || cards.length !== 0) return;

            filteredEmpty?.remove();
            filteredEmpty = null;

            if (grid.querySelector('[data-archive-base-empty]')) return;

            const empty = document.createElement('div');
            empty.className = 'archive-empty-state';
            empty.dataset.archiveBaseEmpty = '';
            empty.innerHTML = `
                <div>
                    <span class="archive-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M6 7v12h12V7"></path>
                            <path d="M9 11h6"></path>
                        </svg>
                    </span>
                    <div class="archive-empty-title">No archived products</div>
                    <div class="archive-empty-copy">
                        Products you archive or remove from your active catalog will appear here.
                    </div>
                </div>
            `;

            grid.appendChild(empty);
        };

        const removeCard = (card) => {
            if (!card) return;

            decrementSummary(card);

            card.style.transition = 'opacity .18s ease';
            card.style.opacity = '0';

            window.setTimeout(() => {
                card.remove();
                cards = cards.filter((item) => item !== card);
                renderBaseEmptyIfNeeded();
                filterArchive();
            }, 190);
        };

        /* --------------------------------------------------------------
           Restore
           -------------------------------------------------------------- */
        const handleRestoreSubmit = async (event) => {
            event.preventDefault();

            const form = event.currentTarget;
            const button = form.querySelector('button[type="submit"]');
            const card = form.closest('[data-archive-product]');

            if (!form || !button || !card || button.disabled) return;

            const originalHTML = button.innerHTML;

            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            button.innerHTML = `
                <svg viewBox="0 0 24 24" class="animate-spin" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 12a9 9 0 1 1-2.64-6.36"></path>
                </svg>
            `;

            try {
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                const payload = await parseResponse(response);

                if (!response.ok) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors)?.[0]?.[0]
                        : null;

                    throw new Error(
                        firstError ||
                        payload?.message ||
                        'Unable to restore this product right now.'
                    );
                }

                removeCard(card);
                showToast('success', payload?.message || 'Product restored successfully.');
            } catch (error) {
                button.disabled = false;
                button.removeAttribute('aria-busy');
                button.innerHTML = originalHTML;

                showToast(
                    'error',
                    error?.message || 'Unable to restore this product.'
                );
            }
        };

        document.querySelectorAll('.archive-restore-form').forEach((form) => {
            form.addEventListener('submit', handleRestoreSubmit);
        });

        /* --------------------------------------------------------------
           Permanent delete confirmation modal
           -------------------------------------------------------------- */
        const openDeleteModal = (button) => {
            if (
                !deleteModal ||
                !deleteForm ||
                !deleteConfirm ||
                !button ||
                button.disabled
            ) {
                return;
            }

            pendingDeleteCard = button.closest('[data-archive-product]');
            deleteTrigger = button;

            const deleteUrl = button.dataset.deleteUrl || '';
            const productName = button.dataset.productName || 'Product';

            if (!pendingDeleteCard || !deleteUrl) {
                showToast('error', 'The delete action is not available for this product.');
                return;
            }

            deleteForm.action = deleteUrl;

            if (deleteProductName) {
                deleteProductName.textContent = productName;
            }

            deleteModal.classList.add('is-open');
            deleteModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');

            window.requestAnimationFrame(() => {
                deleteCancel?.focus();
            });
        };

const closeDeleteModal = ({ restoreFocus = true } = {}) => {
            deleteModal?.classList.remove('is-open');
            deleteModal?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');

            if (deleteForm) deleteForm.action = '';

            pendingDeleteCard = null;

            if (restoreFocus) {
                deleteTrigger?.focus();
            }

            deleteTrigger = null;
        };

        const handlePermanentDelete = async (event) => {
            event.preventDefault();

            if (
                !deleteForm ||
                !deleteConfirm ||
                !pendingDeleteCard ||
                !deleteForm.action
            ) {
                return;
            }

            const card = pendingDeleteCard;
            const originalText = deleteConfirm.textContent;

            deleteConfirm.disabled = true;
            deleteConfirm.setAttribute('aria-busy', 'true');
            deleteConfirm.textContent = 'Deleting...';

            try {
                const response = await fetch(deleteForm.action, {
                    method: deleteForm.method || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(deleteForm),
                    credentials: 'same-origin',
                });

                const payload = await parseResponse(response);

                if (!response.ok) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors)?.[0]?.[0]
                        : null;

                    throw new Error(
                        firstError ||
                        payload?.message ||
                        'Unable to permanently delete this product.'
                    );
                }

                closeDeleteModal({ restoreFocus: false });
                removeCard(card);

                showToast(
                    'success',
                    payload?.message || 'Product permanently deleted.'
                );
            } catch (error) {
                showToast(
                    'error',
                    error?.message || 'Unable to permanently delete this product.'
                );
            } finally {
                deleteConfirm.disabled = false;
                deleteConfirm.removeAttribute('aria-busy');
                deleteConfirm.textContent = originalText;
            }
        };

        document.querySelectorAll('[data-archive-delete-open]').forEach((button) => {
            button.addEventListener('click', () => openDeleteModal(button));
        });

        deleteCancel?.addEventListener('click', () => closeDeleteModal());

        deleteForm?.addEventListener('submit', handlePermanentDelete);

        deleteModal?.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        deleteDialog?.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;

            if (deleteModal?.classList.contains('is-open')) {
                closeDeleteModal();
                return;
            }

            closeHistoryMenu();
        }, { signal });

        const cleanup = () => {
            shell.dataset.archiveInitialized = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ = null;
            }
        };

        document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
        window.addEventListener('beforeunload', cleanup, { once: true, signal });

        /* --------------------------------------------------------------
           Initial state
           -------------------------------------------------------------- */
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
