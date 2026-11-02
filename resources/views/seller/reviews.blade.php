@extends('layouts.seller')

@section('title', 'Product Reviews — SARI Seller')
@section('page-title', 'Product Reviews')

@push('styles')
<style id="sariSellerReviewsCleanUI">
/* SARI Seller Product Reviews — standalone, scoped styling; no new Blade dependencies. */
.sari-reviews-page {
    --rv-gold: #C88912;
    --rv-gold-hover: #B47B0E;
    --rv-ink: #2F3742;
    --rv-text: #475569;
    --rv-muted: #8491A1;
    --rv-border: #E5E7EB;
    --rv-soft: #F8FAFC;
    width: 100%;
    max-width: 1520px;
    margin-inline: auto;
    padding-bottom: 32px;
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    color: var(--rv-ink);
}
.sari-reviews-page *, .sari-reviews-page *::before, .sari-reviews-page *::after { box-sizing: border-box; }
.sari-reviews-page :is(button, input, a) { font-family: inherit; }
.sari-reviews-page [hidden] { display: none !important; }
.sari-reviews-page button { -webkit-tap-highlight-color: transparent; }

/* Title + cart-style navigation */
.sari-reviews-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    padding: 2px 2px 17px;
    border-bottom: 1px solid #E6EAF0;
}
.sari-reviews-eyebrow {
    margin: 0;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .12em;
    line-height: 1.4;
    text-transform: uppercase;
    color: #A87316;
}
.sari-reviews-title {
    margin: 6px 0 0;
    font-size: clamp(27px, 2.2vw, 31px);
    font-weight: 760;
    line-height: 1.18;
    letter-spacing: -.04em;
    color: var(--rv-ink);
}
.sari-reviews-title span { color: var(--rv-gold); }
.sari-reviews-subtitle {
    margin: 8px 0 0;
    font-size: 11.5px;
    line-height: 1.6;
    color: #768397;
}
.sari-reviews-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
    margin-top: 4px;
    color: #59636E;
    font-size: 11.5px;
    font-weight: 750;
    line-height: 1.35;
    text-decoration: none;
    transition: color .14s ease;
}
.sari-reviews-back > span { font-size: 18px; font-weight: 800; line-height: .8; transform: translateY(-1px); }
.sari-reviews-back:hover, .sari-reviews-back:focus-visible { color: var(--rv-gold); }
.sari-reviews-back:focus-visible { outline: 2px solid var(--rv-gold); outline-offset: 5px; border-radius: 3px; }

/* Three metrics, same dimensions as Pickup Requests */
.sari-reviews-metrics {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-top: 22px;
}
.sari-reviews-metric {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    min-width: 0;
    min-height: 110px;
    padding: 17px 18px;
    border: 1px solid var(--rv-border);
    border-radius: 18px;
    background: #FFF;
    box-shadow: 0 2px 6px rgba(15,23,42,.035), 0 10px 24px rgba(15,23,42,.07);
}
.sari-reviews-metric-copy { min-width: 0; }
.sari-reviews-metric-label { margin: 0; color: #5A6879; font-size: 13px; font-weight: 650; line-height: 1.35; }
.sari-reviews-metric-value {
    margin: 8px 0 0;
    font-size: clamp(25px, 2.1vw, 30px);
    line-height: 1.08;
    font-weight: 750;
    letter-spacing: -.04em;
    color: #273442;
    font-variant-numeric: tabular-nums;
}
.sari-reviews-metric-value small { font-size: 13px; font-weight: 600; color: #8793A3; letter-spacing: 0; }
.sari-reviews-metric-note { margin: 7px 0 0; font-size: 10.5px; line-height: 1.4; color: #8793A3; }
.sari-reviews-metric-icon { width: 27px; height: 27px; flex: 0 0 27px; fill: none; stroke: currentColor; color: #94A3B8; }
.sari-reviews-metric:nth-child(1) .sari-reviews-metric-icon { color: var(--rv-gold); }
.sari-reviews-metric:nth-child(3) .sari-reviews-metric-icon { color: #16966B; }

/* Search + custom rating menu (no check icons or cream selected styles). */
.sari-reviews-toolbar {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(180px,210px) 140px 92px;
    align-items: center;
    gap: 12px;
    margin-top: 22px;
    position: relative;
    z-index: 2;
}
.sari-reviews-search { position: relative; display: block; min-width: 0; }
.sari-reviews-search svg {
    position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
    width: 21px; height: 21px; fill: none; stroke: #94A3B8; pointer-events: none;
}
.sari-reviews-search input,
.sari-reviews-rating-trigger,
.sari-reviews-apply,
.sari-reviews-reset {
    width: 100%; height: 48px; min-height: 48px;
    border-radius: 14px; font-size: 11.5px; font-weight: 600;
}
.sari-reviews-search input {
    padding: 0 16px 0 46px; border: 1px solid #DCE2E8;
    background: #FFF; color: var(--rv-ink); outline: 0;
}
.sari-reviews-search input::placeholder { color: #94A3B8; opacity: 1; }
.sari-reviews-search input:focus-visible,
.sari-reviews-rating-trigger:focus-visible,
.sari-reviews-rating-trigger[aria-expanded="true"] {
    border-color: var(--rv-gold); outline: 0; box-shadow: 0 0 0 3px rgba(200,137,18,.1);
}
.sari-reviews-rating { position: relative; min-width: 0; }
.sari-reviews-rating-trigger {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 0 16px; border: 1px solid #DCE2E8; background: white;
    color: #3F4B59; cursor: pointer; text-align: left;
}
.sari-reviews-rating-trigger:hover { background: var(--rv-soft); }
.sari-reviews-rating-trigger svg { width: 17px; height: 17px; flex: 0 0 17px; fill: none; stroke: #64748B; }
.sari-reviews-rating-menu {
    position: absolute; z-index: 30; top: calc(100% + 8px); left: 0; right: 0;
    min-width: 192px; padding: 8px; border: 1px solid #E2E8F0;
    border-radius: 14px; background: #FFF;
    box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05);
}
.sari-reviews-rating-option {
    display: block; width: 100%; min-height: 38px; padding: 8px 12px;
    border: 0; border-radius: 10px; background: transparent;
    text-align: left; color: #475569; font-size: 11.5px;
    font-weight: 600; cursor: pointer;
}
.sari-reviews-rating-option:hover, .sari-reviews-rating-option:focus-visible { background: var(--rv-soft); color: #A56D0A; outline: 0; }
.sari-reviews-rating-option.is-selected { background: var(--rv-soft); color: var(--rv-ink); font-weight: 750; }
.sari-reviews-apply, .sari-reviews-reset {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    text-decoration: none; cursor: pointer;
}
.sari-reviews-apply { border: 1px solid var(--rv-gold); background: var(--rv-gold); color: white; font-weight: 750; }
.sari-reviews-apply:hover, .sari-reviews-apply:focus-visible { background: var(--rv-gold-hover); border-color: var(--rv-gold-hover); }
.sari-reviews-apply svg { width: 15px; height: 15px; fill: none; stroke: currentColor; }
.sari-reviews-reset { border: 1px solid #DCE2E8; background: white; color: #526071; }
.sari-reviews-reset:hover, .sari-reviews-reset:focus-visible { background: var(--rv-soft); }
.sari-reviews-apply:focus-visible, .sari-reviews-reset:focus-visible { outline: 3px solid rgba(200,137,18,.2); outline-offset: 2px; }

/* Main reviews container + two-column cards */
.sari-reviews-panel {
    margin-top: 22px; overflow: hidden;
    border: 1px solid var(--rv-border); border-radius: 18px;
    background: #FFF; box-shadow: 0 2px 6px rgba(15,23,42,.035), 0 12px 28px rgba(15,23,42,.085);
}
.sari-reviews-panel-head {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    gap: 12px; min-height: 92px; padding: 20px 24px;
    border-bottom: 1px solid #ECEFF2;
}
.sari-reviews-panel-title { margin: 0; color: #263442; font-size: 18px; font-weight: 780; line-height: 1.3; letter-spacing: -.025em; }
.sari-reviews-panel-subtitle { margin: 4px 0 0; color: #8491A1; font-size: 11px; line-height: 1.45; }
.sari-reviews-visible-label { color: #8491A1; font-size: 11px; font-weight: 650; }
.sari-reviews-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 14px; padding: 20px 24px; }
.sari-review-card {
    min-width: 0; padding: 19px 20px;
    border: 1px solid #E7EBF0; border-radius: 14px;
    background: #FFF;
}
.sari-review-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
.sari-review-product { margin: 0; color: #263442; font-size: 13px; font-weight: 750; line-height: 1.5; overflow-wrap: anywhere; }
.sari-review-order { margin: 4px 0 0; color: #8894A4; font-size: 11px; line-height: 1.45; overflow-wrap: anywhere; }
.sari-review-rating { flex: 0 0 auto; display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.sari-review-stars { font-size: 17px; line-height: 1.25; white-space: nowrap; letter-spacing: .025em; }
.sari-review-stars .filled { color: #C88912; }
.sari-review-stars .empty { color: #D7DFE8; }
.sari-review-rating-label { font-size: 10px; font-weight: 650; color: #748295; white-space: nowrap; }
.sari-review-comment {
    margin: 18px 0; color: #475569; font-size: 12px;
    line-height: 1.75; overflow-wrap: anywhere; min-height: 40px;
}
.sari-review-comment.is-placeholder { color: #8B97A5; font-style: italic; }
.sari-review-footer {
    display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
    gap: 6px 12px; padding-top: 13px; border-top: 1px solid #EDF0F4;
    color: #8491A1; font-size: 10.5px; line-height: 1.5;
}
.sari-review-buyer { color: #5F6D7C; font-weight: 600; overflow-wrap: anywhere; }
.sari-review-footer time { font-variant-numeric: tabular-nums; }
.sari-reviews-empty { display: grid; place-items: center; min-height: 225px; padding: 36px 20px; text-align: center; }
.sari-reviews-empty svg { width: 54px; height: 54px; margin-inline: auto; fill: none; stroke: #A0ACB9; }
.sari-reviews-empty h3 { margin: 12px 0 0; color: #344054; font-size: 16px; font-weight: 750; }
.sari-reviews-empty p { margin: 7px auto 0; max-width: 360px; color: #8B97A5; font-size: 11.5px; line-height: 1.65; }

/* Pagination belongs inside its parent container */
.sari-reviews-pagination {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    gap: 12px; padding: 16px 24px; border-top: 1px solid #ECEFF2;
}
.sari-reviews-page-count { color: #8390A1; font-size: 11px; font-weight: 600; font-variant-numeric: tabular-nums; }
.sari-reviews-pages { display: flex; align-items: center; gap: 5px; }
.sari-reviews-page-button {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 8px;
    border: 1px solid #E2E8F0; border-radius: 9px;
    background: #FFF; color: #657387; font-size: 11px; font-weight: 700; cursor: pointer;
}
.sari-reviews-page-button:hover:not(:disabled):not(.is-current) { background: var(--rv-soft); }
.sari-reviews-page-button.is-current { border-color: var(--rv-gold); background: var(--rv-gold); color: white; }
.sari-reviews-page-button:disabled { opacity: .4; cursor: default; }
.sari-reviews-page-button:focus-visible { outline: 2px solid var(--rv-gold); outline-offset: 2px; }
.sari-reviews-page-button svg { width: 15px; height: 15px; fill: none; stroke: currentColor; }

@media(max-width: 1024px) {
    .sari-reviews-toolbar { grid-template-columns: minmax(0,1fr) minmax(160px,210px) 130px 85px; gap: 9px; }
}
@media(max-width: 760px) {
    .sari-reviews-grid { grid-template-columns: 1fr; padding: 16px; }
    .sari-reviews-toolbar { grid-template-columns: minmax(0,1fr) minmax(0,1fr); }
    .sari-reviews-search, .sari-reviews-rating { grid-column: 1 / -1; }
    .sari-reviews-panel-head { min-height: 80px; padding: 18px 16px; }
    .sari-reviews-pagination { padding: 15px 16px; }
}
@media(max-width: 640px) {
    .sari-reviews-header { flex-wrap: wrap; gap: 10px; }
    .sari-reviews-title { font-size: 25px; }
    .sari-reviews-eyebrow { font-size: 9.5px; }
    .sari-reviews-back { font-size: 10.5px; margin-top: 0; }
    .sari-reviews-back > span { font-size: 16px; }
    .sari-reviews-metrics { grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; margin-top: 16px; }
    .sari-reviews-metric { padding: 15px; min-height: 110px; }
    .sari-reviews-metric:last-child { grid-column: 1 / -1; }
    .sari-reviews-metric-label { font-size: 11.5px; }
    .sari-reviews-metric-value { font-size: 25px; }
    .sari-reviews-metric-note { font-size: 10px; }
    .sari-reviews-toolbar { margin-top: 16px; }
    .sari-reviews-panel { margin-top: 16px; }
    .sari-review-card { padding: 16px; }
    .sari-review-stars { font-size: 15px; }
    .sari-reviews-pagination { justify-content: center; }
    .sari-reviews-page-count { width: 100%; text-align: center; }
}
@media (prefers-reduced-motion: reduce) {
    .sari-reviews-page *, .sari-reviews-page *::before, .sari-reviews-page *::after { transition-duration: .01ms !important; }
}
</style>
@endpush

@section('content')
@php
    $reviewTotal = $reviews->count();
    $fiveStarTotal = $reviews->where('rating', 5)->count();
@endphp

<main class="sari-reviews-page" id="sariSellerReviews">
    <header class="sari-reviews-header">
        <div>
            <p class="sari-reviews-eyebrow">Delivered-order feedback</p>
            <h1 class="sari-reviews-title">Buyer Product <span>Reviews</span></h1>
            <p class="sari-reviews-subtitle">Feedback from buyers whose orders have been delivered.</p>
        </div>
        <a href="{{ route('seller.orders') }}" class="sari-reviews-back">
            Back to Orders <span aria-hidden="true">&gt;</span>
        </a>
    </header>

    <section class="sari-reviews-metrics" aria-label="Review summary">
        <article class="sari-reviews-metric">
            <div class="sari-reviews-metric-copy">
                <p class="sari-reviews-metric-label">Average Rating</p>
                <p class="sari-reviews-metric-value">{{ number_format($average, 2) }} <small>/ 5</small></p>
                <p class="sari-reviews-metric-note">Across received reviews</p>
            </div>
            <svg class="sari-reviews-metric-icon" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><path d="m12 3 2.9 5.9 6.5.95-4.7 4.6 1.1 6.5-5.8-3.05-5.8 3.05 1.1-6.5-4.7-4.6 6.5-.95L12 3Z"/></svg>
        </article>
        <article class="sari-reviews-metric">
            <div class="sari-reviews-metric-copy">
                <p class="sari-reviews-metric-label">Total Reviews</p>
                <p class="sari-reviews-metric-value">{{ number_format($reviewTotal) }}</p>
                <p class="sari-reviews-metric-note">Delivered-order feedback</p>
            </div>
            <svg class="sari-reviews-metric-icon" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H5l-2 2v-9.5a7.5 7.5 0 0 1 7.5-7.5h2A7.5 7.5 0 0 1 20 11.5Z"/><path d="M8 10h8M8 14h5"/></svg>
        </article>
        <article class="sari-reviews-metric">
            <div class="sari-reviews-metric-copy">
                <p class="sari-reviews-metric-label">5-Star Reviews</p>
                <p class="sari-reviews-metric-value">{{ number_format($fiveStarTotal) }}</p>
                <p class="sari-reviews-metric-note">Highest-rated feedback</p>
            </div>
            <svg class="sari-reviews-metric-icon" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><path d="M12 3 15 9l6.5 1-4.7 4.7L18 21l-6-3.15L6 21l1.2-6.3L2.5 10 9 9l3-6Z"/><path d="m9 13 2 2 4-4"/></svg>
        </article>
    </section>

    @if ($reviewTotal > 0)
        <form class="sari-reviews-toolbar" data-reviews-filters role="search" novalidate>
            <label class="sari-reviews-search">
                <span class="sr-only">Search product reviews</span>
                <svg viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input type="search" placeholder="Search product, order, buyer, or comment..." aria-label="Search product reviews" data-reviews-search autocomplete="off">
            </label>
            <div class="sari-reviews-rating" data-reviews-rating>
                <button class="sari-reviews-rating-trigger" type="button" data-reviews-rating-trigger aria-haspopup="listbox" aria-expanded="false" aria-controls="sariReviewsRatingMenu">
                    <span data-reviews-rating-label>All Ratings</span>
                    <svg viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                </button>
                <div class="sari-reviews-rating-menu" id="sariReviewsRatingMenu" data-reviews-rating-menu role="listbox" aria-label="Filter reviews by rating" hidden>
                    @foreach ([['all', 'All Ratings'], ['5','5 Stars'], ['4','4 Stars'], ['3','3 Stars'], ['2','2 Stars'], ['1','1 Star']] as [$ratingValue, $ratingLabel])
                        <button type="button" class="sari-reviews-rating-option {{ $ratingValue === 'all' ? 'is-selected' : '' }}" role="option" aria-selected="{{ $ratingValue === 'all' ? 'true' : 'false' }}" data-reviews-rating-option="{{ $ratingValue }}">{{ $ratingLabel }}</button>
                    @endforeach
                </div>
            </div>
            <button class="sari-reviews-apply" type="submit"><svg viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M7 12h10M10 17h4"/></svg>Apply Filter</button>
            <button class="sari-reviews-reset" type="button" data-reviews-reset>Reset</button>
        </form>
    @endif

    <section class="sari-reviews-panel" aria-label="Buyer product review records">
        <div class="sari-reviews-panel-head">
            <div>
                <h2 class="sari-reviews-panel-title">Product Reviews</h2>
                <p class="sari-reviews-panel-subtitle">Browse feedback submitted for delivered orders.</p>
            </div>
            @if ($reviewTotal > 0)
                <span class="sari-reviews-visible-label" data-reviews-head-count aria-live="polite">Showing {{ min(10, $reviewTotal) }} of {{ $reviewTotal }}</span>
            @endif
        </div>

        @if ($reviewTotal > 0)
            <div class="sari-reviews-grid" data-reviews-grid>
                @foreach ($reviews as $review)
                    @php $rating = max(0, min(5, (int) $review->rating)); @endphp
                    <article class="sari-review-card" data-review-card data-review-rating="{{ $rating }}">
                        <div class="sari-review-top">
                            <div>
                                <h3 class="sari-review-product">{{ $review->product?->name ?: 'Product' }}</h3>
                                <p class="sari-review-order">Order {{ $review->order?->order_number ?: 'unavailable' }}</p>
                            </div>
                            <div class="sari-review-rating" aria-label="{{ $rating }} out of 5 stars">
                                <div class="sari-review-stars" aria-hidden="true"><span class="filled">{{ str_repeat('★', $rating) }}</span><span class="empty">{{ str_repeat('☆', 5 - $rating) }}</span></div>
                                <span class="sari-review-rating-label">{{ $rating }} / 5 rating</span>
                            </div>
                        </div>
                        <p class="sari-review-comment{{ $review->comment ? '' : ' is-placeholder' }}">{{ $review->comment ?: 'Buyer submitted a rating without a written comment.' }}</p>
                        <div class="sari-review-footer">
                            <span class="sari-review-buyer">{{ $review->buyer ? trim($review->buyer->first_name . ' ' . $review->buyer->last_name) : ($review->socialBuyer?->name ?: 'SARI Buyer') }}</span>
                            <time datetime="{{ $review->created_at?->toIso8601String() }}">{{ $review->created_at?->format('M d, Y · h:i A') ?: 'Date unavailable' }}</time>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="sari-reviews-empty" data-reviews-no-match hidden>
                <div>
                    <svg viewBox="0 0 24 24" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4M8 11h6"/></svg>
                    <h3>No matching reviews</h3>
                    <p>Try another keyword or choose a different star rating.</p>
                </div>
            </div>
            <footer class="sari-reviews-pagination" data-reviews-pagination aria-label="Review pagination">
                <span class="sari-reviews-page-count" data-reviews-page-count aria-live="polite">Showing 1–{{ min(10, $reviewTotal) }} of {{ $reviewTotal }} reviews</span>
                <nav class="sari-reviews-pages" data-reviews-pages aria-label="Review pages"></nav>
            </footer>
        @else
            <div class="sari-reviews-empty">
                <div>
                    <svg viewBox="0 0 24 24" stroke-width="1.6" aria-hidden="true"><path d="M7 5h10a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3Z"/><path d="m12 8 1.1 2.2 2.5.4-1.8 1.8.4 2.5-2.2-1.2-2.2 1.2.4-2.5-1.8-1.8 2.5-.4L12 8Z"/></svg>
                    <h3>No product reviews yet</h3>
                    <p>Reviews will appear here after buyers submit feedback for their delivered orders.</p>
                </div>
            </div>
        @endif
    </section>
</main>
@endsection

@push('scripts')
<script>
(() => {
    window.__SARI_SELLER_REVIEWS_CLEAN_LIFECYCLE__?.abort();
    const lifecycle = new AbortController();
    window.__SARI_SELLER_REVIEWS_CLEAN_LIFECYCLE__ = lifecycle;
    const { signal } = lifecycle;

    function initReviews() {
        const root = document.getElementById('sariSellerReviews');
        if (!root || root.dataset.reviewsReady === '1') return;
        root.dataset.reviewsReady = '1';

        const cards = Array.from(root.querySelectorAll('[data-review-card]'));
        if (!cards.length) return;
        const filterForm = root.querySelector('[data-reviews-filters]');
        const searchInput = root.querySelector('[data-reviews-search]');
        const ratingRoot = root.querySelector('[data-reviews-rating]');
        const ratingTrigger = root.querySelector('[data-reviews-rating-trigger]');
        const ratingMenu = root.querySelector('[data-reviews-rating-menu]');
        const ratingLabel = root.querySelector('[data-reviews-rating-label]');
        const ratingOptions = Array.from(root.querySelectorAll('[data-reviews-rating-option]'));
        const reset = root.querySelector('[data-reviews-reset]');
        const grid = root.querySelector('[data-reviews-grid]');
        const noMatch = root.querySelector('[data-reviews-no-match]');
        const footer = root.querySelector('[data-reviews-pagination]');
        const pageCount = root.querySelector('[data-reviews-page-count]');
        const headCount = root.querySelector('[data-reviews-head-count]');
        const pageContainer = root.querySelector('[data-reviews-pages]');
        const pageSize = 10;
        let pendingRating = 'all';
        let appliedRating = 'all';
        let appliedQuery = '';
        let currentPage = 1;

        function closeMenu() {
            ratingMenu.hidden = true;
            ratingTrigger.setAttribute('aria-expanded', 'false');
        }
        function openMenu() {
            ratingMenu.hidden = false;
            ratingTrigger.setAttribute('aria-expanded', 'true');
        }
        function chooseRating(value) {
            pendingRating = value;
            ratingOptions.forEach(option => {
                const selected = option.dataset.reviewsRatingOption === value;
                option.classList.toggle('is-selected', selected);
                option.setAttribute('aria-selected', String(selected));
                if (selected) ratingLabel.textContent = option.textContent.trim();
            });
        }
        ratingTrigger?.addEventListener('click', () => ratingMenu.hidden ? openMenu() : closeMenu(), { signal });
        ratingOptions.forEach((option, index) => {
            option.addEventListener('click', () => {
                chooseRating(option.dataset.reviewsRatingOption);
                closeMenu();
                ratingTrigger.focus();
            }, { signal });
            option.addEventListener('keydown', event => {
                const direction = event.key === 'ArrowDown' ? 1 : event.key === 'ArrowUp' ? -1 : 0;
                if (!direction) return;
                event.preventDefault();
                ratingOptions[(index + direction + ratingOptions.length) % ratingOptions.length].focus();
            }, { signal });
        });
        document.addEventListener('pointerdown', event => {
            if (!ratingRoot.contains(event.target)) closeMenu();
        }, { signal });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !ratingMenu.hidden) {
                closeMenu(); ratingTrigger.focus();
            }
        }, { signal });

        function makePageButton(label, target, disabled, current, iconDirection) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'sari-reviews-page-button' + (current ? ' is-current' : '');
            button.disabled = disabled;
            if (current) button.setAttribute('aria-current', 'page');
            button.setAttribute('aria-label', iconDirection === 'left' ? 'Previous page' : iconDirection === 'right' ? 'Next page' : 'Page ' + label);
            if (iconDirection) {
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('stroke-width', '1.9');
                svg.setAttribute('aria-hidden', 'true');
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', iconDirection === 'left' ? 'm14 18-6-6 6-6' : 'm10 18 6-6-6-6');
                svg.appendChild(path);
                button.appendChild(svg);
            } else {
                button.textContent = label;
            }
            button.addEventListener('click', () => { currentPage = target; render(); }, { signal });
            return button;
        }

        function render() {
            const filtered = cards.filter(card => {
                const matchesRating = appliedRating === 'all' || card.dataset.reviewRating === appliedRating;
                const matchesQuery = card.textContent.toLocaleLowerCase().includes(appliedQuery);
                return matchesRating && matchesQuery;
            });
            const maxPage = Math.max(1, Math.ceil(filtered.length / pageSize));
            currentPage = Math.min(currentPage, maxPage);
            const start = (currentPage - 1) * pageSize;
            const visible = new Set(filtered.slice(start, start + pageSize));
            cards.forEach(card => { card.hidden = !visible.has(card); });
            grid.hidden = filtered.length === 0;
            noMatch.hidden = filtered.length !== 0;
            footer.hidden = filtered.length === 0;
            const from = filtered.length ? start + 1 : 0;
            const to = Math.min(start + pageSize, filtered.length);
            pageCount.textContent = `Showing ${from}–${to} of ${filtered.length} reviews`;
            headCount.textContent = `Showing ${filtered.length ? to - start : 0} of ${filtered.length}`;

            pageContainer.replaceChildren();
            if (filtered.length > 0) {
                pageContainer.appendChild(makePageButton('Previous', Math.max(1,currentPage - 1), currentPage === 1, false, 'left'));
                const first = Math.max(1, Math.min(currentPage - 2, maxPage - 4));
                const last = Math.min(maxPage, first + 4);
                for (let p = first; p <= last; p++) pageContainer.appendChild(makePageButton(String(p), p, false, p === currentPage));
                pageContainer.appendChild(makePageButton('Next', Math.min(maxPage,currentPage + 1), currentPage === maxPage, false, 'right'));
            }
        }

        filterForm?.addEventListener('submit', event => {
            event.preventDefault();
            appliedQuery = (searchInput.value || '').trim().toLocaleLowerCase();
            appliedRating = pendingRating;
            currentPage = 1;
            closeMenu();
            render();
        }, { signal });
        reset?.addEventListener('click', () => {
            searchInput.value = '';
            appliedQuery = '';
            appliedRating = 'all';
            chooseRating('all');
            closeMenu();
            currentPage = 1;
            render();
        }, { signal });
        render();
    }

    if (typeof window.__SARI_SELLER_AFTER_PAINT__ === 'function') {
        window.__SARI_SELLER_AFTER_PAINT__(initReviews);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReviews, { once: true, signal });
    } else {
        initReviews();
    }
    const cleanup = () => {
        lifecycle.abort();
        if (window.__SARI_SELLER_REVIEWS_CLEAN_LIFECYCLE__ === lifecycle) window.__SARI_SELLER_REVIEWS_CLEAN_LIFECYCLE__ = null;
    };
    document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
    window.addEventListener('beforeunload', cleanup, { once: true, signal });
})();
</script>
@endpush
