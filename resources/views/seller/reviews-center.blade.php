@extends('layouts.seller')

@section('title', 'Reviews & Ratings — SARI Seller')
@section('page-title', 'Reviews & Ratings')

@push('styles')
<style id="sariSellerReviewsCenterProfessionalStyles">
    .seller-reviews-page {
        --reviews-gold: #d59617;
        --reviews-gold-hover: #c9890f;
        --reviews-gold-soft: #fffaf0;
        --reviews-ink: #111827;
        --reviews-text: #344054;
        --reviews-muted: #7c8592;
        --reviews-soft-muted: #98a2b3;
        --reviews-line: #e5e7eb;

        width: 100%;
        max-width: 1440px;
        margin-inline: auto;
        padding-bottom: 24px;
        color: var(--reviews-ink);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-reviews-page *,
    .seller-reviews-page *::before,
    .seller-reviews-page *::after {
        box-sizing: border-box;
    }

    .seller-reviews-page button,
    .seller-reviews-page input,
    .seller-reviews-page textarea,
    .seller-reviews-page a {
        font-family: inherit;
    }

    /* ------------------------------------------------------------
       Feedback
       ------------------------------------------------------------ */
    .seller-reviews-alert {
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

    .seller-reviews-alert--success {
        border-color: #d5e7dc;
        background: #f5faf7;
        color: #4f7d63;
    }

    .seller-reviews-alert--error {
        border-color: #efdada;
        background: #fff7f7;
        color: #a65353;
    }

    .seller-reviews-alert-icon {
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

    .seller-reviews-alert-icon svg {
        width: 13px;
        height: 13px;
    }

    /* ------------------------------------------------------------
       Header
       ------------------------------------------------------------ */
    .seller-reviews-header {
        min-height: 66px;
        padding: 2px 2px 13px;
        border-bottom: 1px solid #e7ebf0;
    }

    .seller-reviews-eyebrow {
        margin: 0;
        color: #b87605;
        font-size: 8px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .seller-reviews-title {
        margin: 5px 0 0;
        color: var(--reviews-ink);
        font-size: clamp(29px, 2.2vw, 36px);
        font-weight: 650;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .seller-reviews-title span:last-child {
        margin-left: .18em;
        color: #C9890B;
    }

    .seller-reviews-subtitle {
        max-width: 760px;
        margin: 7px 0 0;
        color: var(--reviews-muted);
        font-size: 10px;
        line-height: 1.5;
    }

    /* ------------------------------------------------------------
       Summary cards
       ------------------------------------------------------------ */
    .seller-reviews-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 9px;
        margin-top: 12px;
    }

    .seller-reviews-stat {
        min-width: 0;
        min-height: 78px;
        border: 1px solid var(--reviews-line);
        border-radius: 13px;
        background: #fff;
        padding: 10px 12px;
        box-shadow: 0 5px 16px rgba(15, 23, 42, .028);
    }

    .seller-reviews-stat-inner {
        display: flex;
        height: 100%;
        align-items: flex-start;
        justify-content: space-between;
        gap: 9px;
    }

    .seller-reviews-stat-label {
        color: #667085;
        font-size: 9.5px;
        font-weight: 600;
        line-height: 1.3;
    }

    .seller-reviews-stat-value {
        margin-top: 3px;
        color: var(--reviews-ink);
        font-size: 21px;
        font-weight: 720;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .seller-reviews-stat-note {
        margin-top: 5px;
        color: var(--reviews-soft-muted);
        font-size: 8.3px;
        line-height: 1.35;
    }

    .seller-reviews-stat-icon {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        place-items: center;
        border: 1px solid rgba(17, 24, 39, .05);
        border-radius: 9px;
    }

    .seller-reviews-stat-icon svg {
        width: 14px;
        height: 14px;
    }

    .seller-reviews-stat-icon--average {
        background: #fff8e9;
        color: #b97805;
    }

    .seller-reviews-stat-icon--total {
        background: #f3f7fb;
        color: #52789b;
    }

    .seller-reviews-stat-icon--five {
        background: #f1f8f4;
        color: #4f7d63;
    }

    .seller-reviews-stat-icon--low {
        background: #fff6f2;
        color: #b66745;
    }

    /* ------------------------------------------------------------
       Review feed panel
       ------------------------------------------------------------ */
    .seller-reviews-feed {
        margin-top: 10px;
        overflow: hidden;
        border: 1px solid var(--reviews-line);
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);
    }

    .seller-reviews-feed-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #edf0f3;
        padding: 11px 13px;
        background: #fff;
    }

    .seller-reviews-feed-title-group {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }

    .seller-reviews-feed-icon {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border: 1px solid #efe3c7;
        border-radius: 8px;
        background: var(--reviews-gold-soft);
        color: #b97805;
    }

    .seller-reviews-feed-icon svg {
        width: 13px;
        height: 13px;
    }

    .seller-reviews-feed-title {
        margin: 0;
        color: #1f2937;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.3;
    }

    .seller-reviews-feed-subtitle {
        margin: 2px 0 0;
        color: var(--reviews-soft-muted);
        font-size: 8px;
        line-height: 1.35;
    }

    .seller-reviews-count {
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
       Review row
       ------------------------------------------------------------ */
    .seller-review-row {
        border-bottom: 1px solid #edf0f3;
        background: #fff;
        transition: background-color .14s ease;
    }

    .seller-review-row:last-child {
        border-bottom: 0;
    }

    .seller-review-row:hover {
        background: #fffdf9;
    }

    .seller-review-main {
        padding: 12px 13px 10px;
    }

    .seller-review-top {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
    }

    .seller-review-product {
        min-width: 0;
    }

    .seller-review-product-row {
        display: flex;
        min-width: 0;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
    }

    .seller-review-product-name {
        overflow: hidden;
        margin: 0;
        color: #1f2937;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-review-reply-state {
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

    .seller-review-reply-state::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }

    .seller-review-reply-state--replied {
        border-color: #cfe4d7;
        background: #f1f8f4;
        color: #4f7d63;
    }

    .seller-review-reply-state--pending {
        border-color: #eadfc9;
        background: #fff9ef;
        color: #a8731f;
    }

    .seller-review-stars-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        margin-top: 5px;
    }

    .seller-review-stars {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        color: var(--reviews-gold);
    }

    .seller-review-stars svg {
        width: 13px;
        height: 13px;
    }

    .seller-review-rating-value {
        color: #667085;
        font-size: 8.5px;
        font-weight: 650;
    }

    .seller-review-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px 7px;
        margin-top: 5px;
        color: var(--reviews-soft-muted);
        font-size: 8px;
        line-height: 1.4;
    }

    .seller-review-meta strong {
        color: #667085;
        font-weight: 600;
    }

    .seller-review-meta-separator {
        color: #d4d8de;
    }

    .seller-review-date {
        color: var(--reviews-soft-muted);
        font-size: 8px;
        font-weight: 500;
        white-space: nowrap;
    }

    .seller-review-comment {
        margin: 9px 0 0;
        max-width: 980px;
        color: #475467;
        font-size: 9.2px;
        line-height: 1.55;
    }

    /* ------------------------------------------------------------
       Seller reply composer
       ------------------------------------------------------------ */
    .seller-review-reply-area {
        border-top: 1px solid #edf0f3;
        background: #fcfcfd;
        padding: 9px 13px 10px;
    }

    .seller-review-reply-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
    }

    .seller-review-reply-label {
        color: #475467;
        font-size: 8.6px;
        font-weight: 700;
    }

    .seller-review-reply-help {
        color: var(--reviews-soft-muted);
        font-size: 7.7px;
        line-height: 1.35;
    }

    .seller-review-reply-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 7px;
        align-items: stretch;
    }

    .seller-review-reply-input {
        width: 100%;
        min-height: 48px;
        max-height: 112px;
        resize: vertical;
        border: 1px solid #d8dee6;
        border-radius: 9px;
        background: #fff;
        padding: 8px 10px;
        color: #344054;
        font-size: 9.3px;
        line-height: 1.45;
        outline: none;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .015);
        transition: border-color .14s ease, box-shadow .14s ease;
    }

    .seller-review-reply-input::placeholder {
        color: #98a2b3;
    }

    .seller-review-reply-input:focus {
        border-color: var(--reviews-gold);
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08);
    }

    .seller-review-reply-button {
        display: inline-flex;
        min-width: 106px;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid var(--reviews-gold);
        border-radius: 9px;
        background: var(--reviews-gold);
        padding: 0 12px;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 6px 14px rgba(213, 150, 23, .13);
        transition: border-color .14s ease, background-color .14s ease;
    }

    .seller-review-reply-button:hover,
    .seller-review-reply-button:focus-visible {
        outline: none;
        border-color: var(--reviews-gold-hover);
        background: var(--reviews-gold-hover);
    }

    .seller-review-reply-button svg {
        width: 13px;
        height: 13px;
    }

    /* ------------------------------------------------------------
       Empty + pagination
       ------------------------------------------------------------ */
    .seller-reviews-empty {
        display: grid;
        min-height: 170px;
        place-items: center;
        padding: 28px 18px;
        text-align: center;
    }

    .seller-reviews-empty-icon {
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

    .seller-reviews-empty-icon svg {
        width: 17px;
        height: 17px;
    }

    .seller-reviews-empty-title {
        margin-top: 9px;
        color: #344054;
        font-size: 10.5px;
        font-weight: 650;
    }

    .seller-reviews-empty-copy {
        margin-top: 4px;
        color: var(--reviews-soft-muted);
        font-size: 8.8px;
        line-height: 1.5;
    }

    .seller-reviews-pagination {
        border-top: 1px solid #edf0f3;
        padding: 10px 13px;
        background: #fff;
    }

    /* ------------------------------------------------------------
       Responsive
       ------------------------------------------------------------ */
    @media (max-width: 900px) {
        .seller-reviews-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .seller-reviews-title {
            font-size: clamp(28px, 8vw, 34px);
        }

        .seller-review-top {
            grid-template-columns: 1fr;
            gap: 7px;
        }

        .seller-review-date {
            white-space: normal;
        }

        .seller-review-reply-form {
            grid-template-columns: 1fr;
        }

        .seller-review-reply-button {
            min-height: 40px;
        }
    }

    @media (max-width: 520px) {
        .seller-reviews-summary {
            grid-template-columns: 1fr;
        }

        .seller-reviews-stat {
            min-height: 74px;
        }

        .seller-review-main,
        .seller-review-reply-area {
            padding-left: 10px;
            padding-right: 10px;
        }

        .seller-review-reply-head {
            align-items: flex-start;
            flex-direction: column;
            gap: 2px;
        }

        .seller-review-reply-input {
            min-height: 54px;
            font-size: 9.8px;
        }

        .seller-review-reply-button {
            height: 42px;
            min-height: 42px;
            font-size: 9.8px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-reviews-page * {
            transition-duration: .01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $summaryCards = [
        [
            'label' => 'Average Rating',
            'value' => number_format((float) $stats['average'], 2),
            'note' => 'Across verified reviews',
            'icon' => 'average',
        ],
        [
            'label' => 'Total Reviews',
            'value' => (int) $stats['count'],
            'note' => 'Buyer feedback received',
            'icon' => 'total',
        ],
        [
            'label' => '5-Star Reviews',
            'value' => (int) $stats['five'],
            'note' => 'Highest-rating feedback',
            'icon' => 'five',
        ],
        [
            'label' => '1–2 Star Reviews',
            'value' => (int) $stats['low'],
            'note' => 'Feedback needing attention',
            'icon' => 'low',
        ],
    ];
@endphp

<div class="seller-reviews-page">
    @if(session('success'))
        <div class="seller-reviews-alert seller-reviews-alert--success">
            <span class="seller-reviews-alert-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="seller-reviews-alert seller-reviews-alert--error">
            <span class="seller-reviews-alert-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M12 3 3 20h18L12 3Z"></path>
                    <path d="M12 9v5"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </span>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <header class="seller-reviews-header">
        <p class="seller-reviews-eyebrow">Business Reputation</p>
        <h1 class="seller-reviews-title">
            <span>Reviews</span><span>& Ratings</span>
        </h1>
        <p class="seller-reviews-subtitle">
            Monitor verified product feedback and publish one Seller response per review.
        </p>
    </header>

    <section class="seller-reviews-summary" aria-label="Review statistics">
        @foreach($summaryCards as $card)
            <article class="seller-reviews-stat">
                <div class="seller-reviews-stat-inner">
                    <div class="min-w-0">
                        <div class="seller-reviews-stat-label">{{ $card['label'] }}</div>
                        <div class="seller-reviews-stat-value">{{ $card['value'] }}</div>
                        <div class="seller-reviews-stat-note">{{ $card['note'] }}</div>
                    </div>

                    <span class="seller-reviews-stat-icon seller-reviews-stat-icon--{{ $card['icon'] }}" aria-hidden="true">
                        @if($card['icon'] === 'average')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m12 3 2.7 5.4 6 .9-4.35 4.2 1.05 5.9L12 16.6 6.6 19.4l1.05-5.9L3.3 9.3l6-.9L12 3Z"></path>
                            </svg>
                        @elseif($card['icon'] === 'total')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 5h14v11H9l-4 3v-14Z"></path>
                                <path d="M8 9h8"></path>
                                <path d="M8 12h5"></path>
                            </svg>
                        @elseif($card['icon'] === 'five')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3 3 20h18L12 3Z"></path>
                                <path d="M12 9v5"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        @endif
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="seller-reviews-feed">
        <div class="seller-reviews-feed-head">
            <div class="seller-reviews-feed-title-group">
                <span class="seller-reviews-feed-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 5h14v11H9l-4 3v-14Z"></path>
                        <path d="m9 10 1.7 1.7L15 8"></path>
                    </svg>
                </span>

                <div>
                    <h2 class="seller-reviews-feed-title">Buyer Review Feed</h2>
                    <p class="seller-reviews-feed-subtitle">Verified product ratings and Seller responses.</p>
                </div>
            </div>

            <span class="seller-reviews-count">
                {{ number_format($reviews->total()) }} reviews
            </span>
        </div>

        <div>
            @forelse($reviews as $review)
                @php
                    $buyerName = $review->buyer
                        ? trim(($review->buyer->first_name ?? '') . ' ' . ($review->buyer->last_name ?? ''))
                        : ($review->socialBuyer?->name ?: 'SARI Buyer');

                    $rating = max(0, min(5, (int) $review->rating));
                @endphp

                <article class="seller-review-row">
                    <div class="seller-review-main">
                        <div class="seller-review-top">
                            <div class="seller-review-product">
                                <div class="seller-review-product-row">
                                    <p class="seller-review-product-name">
                                        {{ $review->product?->name ?: 'Product' }}
                                    </p>

                                    <span class="seller-review-reply-state {{ $review->sellerReply ? 'seller-review-reply-state--replied' : 'seller-review-reply-state--pending' }}">
                                        {{ $review->sellerReply ? 'Replied' : 'Needs Reply' }}
                                    </span>
                                </div>

                                <div class="seller-review-stars-row">
                                    <span
                                        class="seller-review-stars"
                                        aria-label="{{ $rating }} out of 5 stars"
                                    >
                                        @for($star = 1; $star <= 5; $star++)
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="{{ $star <= $rating ? 'currentColor' : 'none' }}"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                aria-hidden="true"
                                            >
                                                <path d="m12 3 2.7 5.4 6 .9-4.35 4.2 1.05 5.9L12 16.6 6.6 19.4l1.05-5.9L3.3 9.3l6-.9L12 3Z"></path>
                                            </svg>
                                        @endfor
                                    </span>

                                    <span class="seller-review-rating-value">{{ $rating }} / 5</span>
                                </div>

                                <div class="seller-review-meta">
                                    <span>Buyer <strong>{{ $buyerName !== '' ? $buyerName : 'SARI Buyer' }}</strong></span>

                                    @if($review->order?->order_number)
                                        <span class="seller-review-meta-separator">•</span>
                                        <span>Order <strong>{{ $review->order->order_number }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <time class="seller-review-date" datetime="{{ $review->created_at?->toAtomString() }}">
                                {{ $review->created_at?->format('M d, Y') }}
                            </time>
                        </div>

                        <p class="seller-review-comment">
                            {{ $review->comment ?: 'Buyer left a rating without a written comment.' }}
                        </p>
                    </div>

                    <div class="seller-review-reply-area">
                        <div class="seller-review-reply-head">
                            <span class="seller-review-reply-label">Seller Response</span>
                            <span class="seller-review-reply-help">Visible as your official reply to this review.</span>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('seller.reviews-center.reply', $review) }}"
                            class="seller-review-reply-form"
                        >
                            @csrf

                            <textarea
                                name="reply"
                                required
                                maxlength="1500"
                                rows="2"
                                placeholder="Write a professional Seller response..."
                                class="seller-review-reply-input"
                                aria-label="Seller response for {{ $review->product?->name ?: 'product review' }}"
                            >{{ $review->sellerReply?->reply }}</textarea>

                            <button type="submit" class="seller-review-reply-button">
                                @if($review->sellerReply)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path d="M4 20h4l10-10-4-4L4 16v4Z"></path>
                                        <path d="m12 8 4 4"></path>
                                    </svg>
                                    Update Reply
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path d="M5 5h14v11H9l-4 3v-14Z"></path>
                                        <path d="M9 10h6"></path>
                                    </svg>
                                    Reply
                                @endif
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="seller-reviews-empty">
                    <div>
                        <span class="seller-reviews-empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 5h14v11H9l-4 3v-14Z"></path>
                                <path d="M9 10h6"></path>
                                <path d="M9 13h4"></path>
                            </svg>
                        </span>

                        <div class="seller-reviews-empty-title">No Buyer reviews yet</div>
                        <div class="seller-reviews-empty-copy">
                            Verified product feedback will appear here after Buyers submit reviews.
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
            <div class="seller-reviews-pagination">
                {{ $reviews->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
