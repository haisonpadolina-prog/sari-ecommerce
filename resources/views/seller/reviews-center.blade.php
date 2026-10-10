@extends('layouts.seller')



@section('title', 'Reviews & Ratings — SARI Seller')

@section('page-title', 'Reviews & Ratings')



@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/reviews-center.css') }}?v=20261007-1">



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
