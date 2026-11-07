@extends('layouts.seller')



@section('title', 'Reviews & Ratings — SARI Seller')

@section('page-title', 'Reviews & Ratings')



@push('styles')
<style id="sariSellerEmbeddedCss-reviews-center" data-sari-inline-css="reviews-center">
@verbatim
/* SARI Seller Reviews & Ratings — clean design system, scoped to the page. */
/* Exact Buyer Cart multi-layer shadow, consistent with Seller Account / Compliance. */
.seller-reviews-page {
    --reviews-main-shadow:
    0 2px 5px rgba(15, 23, 42, .045),
    0 10px 24px rgba(15, 23, 42, .075),
    0 24px 54px rgba(15, 23, 42, .095),
    inset 0 1px 0 rgba(255, 255, 255, .92);
}
.seller-reviews-page{--reviews-gold:#C88912;--reviews-gold-hover:#B47B0E;--reviews-ink:#2F3742;--reviews-text:#475569;--reviews-muted:#8390A0;--reviews-line:#E5E7EB;--reviews-soft:#F8FAFC;width:100%;max-width:1520px;margin-inline:auto;padding-bottom:32px;color:var(--reviews-ink);font-family:'Poppins',ui-sans-serif,system-ui,sans-serif}
.seller-reviews-page *, .seller-reviews-page *::before,.seller-reviews-page *::after{box-sizing:border-box}
.seller-reviews-page :is(button,input,select,textarea,a){font-family:inherit}
.seller-reviews-page [hidden]{display:none!important}
.seller-reviews-header{min-height:0;margin:0;padding:2px 2px 17px;border:0;border-bottom:1px solid #E6EAF0;border-radius:0;background:transparent;box-shadow:none}
.seller-reviews-eyebrow{margin:0;color:#A87316;font-size:10.5px;line-height:1.4;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
.seller-reviews-title{margin:5px 0 0;color:var(--reviews-ink);font-size:clamp(27px,2.2vw,31px);font-weight:760;line-height:1.2;letter-spacing:-.04em}
.seller-reviews-title span:last-child{margin-left:.17em;color:var(--reviews-gold)}
.seller-reviews-subtitle{max-width:790px;margin:8px 0 0;color:#768397;font-size:11.5px;line-height:1.6}
.seller-reviews-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:22px}
.seller-reviews-stat{min-width:0;min-height:110px;border:1px solid var(--reviews-line);border-radius:20px;background:#fff;padding:17px 18px;box-shadow:var(--reviews-main-shadow)}
.seller-reviews-stat-inner{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
.seller-reviews-stat-inner .min-w-0{min-width:0}
.seller-reviews-stat-label{color:#5A6879;font-size:13px;font-weight:650;line-height:1.4}
.seller-reviews-stat-value{margin-top:8px;color:var(--reviews-ink);font-size:28px;font-weight:750;line-height:1.1;letter-spacing:-.04em;font-variant-numeric:tabular-nums}
.seller-reviews-stat-note{margin-top:8px;color:#8390A0;font-size:11.5px;line-height:1.45}
.seller-reviews-stat-icon{display:inline-flex;width:27px;height:27px;flex:0 0 27px;align-items:center;justify-content:center;border:0;border-radius:0;background:transparent;box-shadow:none;color:#98A2B3}
.seller-reviews-stat-icon svg{width:27px;height:27px;stroke-width:1.65}
.seller-reviews-stat-icon--average{color:#C88912}.seller-reviews-stat-icon--total{color:#94A3B8}.seller-reviews-stat-icon--five{color:#0E9F78}.seller-reviews-stat-icon--low{color:#E34C3B}
/* Unboxed search toolbar, same 48px controls as Pickup Requests. */
.seller-reviews-filter-form{display:grid;grid-template-columns:minmax(0,1fr) 190px 168px auto auto;gap:12px;align-items:center;margin-top:24px}
.seller-reviews-search-wrap{position:relative;min-width:0}
.seller-reviews-search-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);width:19px;height:19px;stroke:#94A3B8;pointer-events:none}
.seller-reviews-search,.seller-reviews-native,.seller-reviews-dropdown-trigger,.seller-reviews-filter-button,.seller-reviews-reset{display:inline-flex;width:100%;height:48px;min-height:48px;align-items:center;border:1px solid #DCE3EA;border-radius:14px;background:#fff;color:#465262;font-size:11.5px;font-weight:600;line-height:1.4}
.seller-reviews-search{display:block;padding:0 16px 0 45px;outline:0;min-width:0}
.seller-reviews-search::placeholder{color:#95A1B0;font-weight:500}
.seller-reviews-search:focus-visible,.seller-reviews-native:focus-visible,.seller-reviews-dropdown-trigger:focus-visible,.seller-reviews-dropdown-trigger[aria-expanded="true"]{border-color:#C88912;outline:0;box-shadow:0 0 0 3px rgba(200,137,18,.09)}
.seller-reviews-dropdown{position:relative;min-width:0}
.seller-reviews-native{padding:0 13px;appearance:auto}
.seller-reviews-dropdown-trigger{justify-content:space-between;gap:12px;padding:0 15px;text-align:left;cursor:pointer}
.seller-reviews-dropdown-trigger svg{width:16px;height:16px;flex:0 0 16px;stroke:#64748B}
.seller-reviews-dropdown[data-enhanced="true"] .seller-reviews-native{position:absolute!important;width:1px!important;height:1px!important;overflow:hidden!important;clip-path:inset(50%)!important;opacity:0!important;pointer-events:none!important}
.seller-reviews-dropdown-trigger[hidden]{display:none!important}
.seller-reviews-dropdown-menu{position:absolute;top:calc(100% + 8px);left:0;right:0;min-width:180px;max-height:265px;overflow-y:auto;z-index:60;padding:7px;border:1px solid #E2E8F0;border-radius:14px;background:white;box-shadow:0 16px 32px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.05)}
.seller-reviews-dropdown-option{display:flex;width:100%;min-height:38px;align-items:center;border:0;border-radius:10px;background:transparent;padding:8px 11px;color:#475569;font-size:11.5px;font-weight:600;text-align:left;cursor:pointer}
.seller-reviews-dropdown-option:hover,.seller-reviews-dropdown-option:focus-visible{background:#F8FAFC;color:#A56D0A;outline:0}
.seller-reviews-dropdown-option[aria-selected="true"]{background:#F8FAFC;color:#2F3742;font-weight:750}
.seller-reviews-filter-button{width:auto;min-width:128px;justify-content:center;gap:8px;border-color:var(--reviews-gold);background:var(--reviews-gold);padding:0 17px;color:#fff;font-size:12px;font-weight:750;cursor:pointer}
.seller-reviews-filter-button svg{width:15px;height:15px;stroke:#fff}
.seller-reviews-filter-button:hover,.seller-reviews-filter-button:focus-visible{border-color:var(--reviews-gold-hover);background:var(--reviews-gold-hover);outline:0}
.seller-reviews-reset{width:auto;min-width:96px;justify-content:center;padding:0 20px;text-decoration:none;white-space:nowrap}
.seller-reviews-reset:hover,.seller-reviews-reset:focus-visible{background:#F8FAFC;color:#A56D0A}
/* Main feed */
.seller-reviews-feed{margin-top:24px;overflow:hidden;border:1px solid var(--reviews-line);border-radius:20px;background:#fff;box-shadow:var(--reviews-main-shadow)}
.seller-reviews-feed-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:24px 26px;border-bottom:1px solid #E9EDF2;background:white}
.seller-reviews-feed-title-group{display:flex;align-items:flex-start;gap:12px}
.seller-reviews-feed-icon{display:flex;width:27px;height:27px;align-items:center;justify-content:center;color:var(--reviews-gold);background:transparent;border:0;flex:0 0 27px}
.seller-reviews-feed-icon svg{width:24px;height:24px}
.seller-reviews-feed-title{margin:0;font-size:18px;font-weight:770;line-height:1.4;color:var(--reviews-ink);letter-spacing:-.025em}
.seller-reviews-feed-subtitle{margin:4px 0 0;font-size:11.5px;line-height:1.5;color:#8592A3}
.seller-reviews-results{color:#8592A3;font-size:11px;font-weight:600;white-space:nowrap}
.seller-review-row{border-bottom:1px solid #EBEEF2;background:#fff;transition:background-color .14s ease}
.seller-review-row:last-child{border-bottom:0}
.seller-review-row:hover{background:#FBFCFD}
.seller-review-main{padding:20px 26px 18px}
.seller-review-top{display:flex;justify-content:space-between;align-items:flex-start;gap:20px}
.seller-review-product{min-width:0}
.seller-review-product-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.seller-review-product-name{margin:0;color:#27323F;font-size:13px;font-weight:750;line-height:1.45;overflow-wrap:anywhere}
.seller-review-reply-state{display:inline-flex;min-height:25px;align-items:center;border-radius:999px;border:1px solid;padding:3px 10px;font-size:10.5px;line-height:1.2;font-weight:700;white-space:nowrap}
.seller-review-reply-state--replied{border-color:#BFE7D0;background:#EFFCF4;color:#167D4B}
.seller-review-reply-state--pending{border-color:#E7D6B9;background:#F8FAFC;color:#A56D0A}
.seller-review-stars-row{display:flex;align-items:center;flex-wrap:wrap;gap:9px;margin-top:10px}
.seller-review-stars{display:inline-flex;align-items:center;gap:2px;color:#C88912}
.seller-review-stars svg{width:17px;height:17px}
.seller-review-stars svg[fill="none"]{color:#CBD5E1}
.seller-review-rating-value{font-size:11px;font-weight:650;color:#69778B}
.seller-review-meta{display:flex;align-items:center;flex-wrap:wrap;gap:5px 10px;margin-top:10px;font-size:11px;line-height:1.5;color:#8491A1}
.seller-review-meta strong{color:#536173;font-weight:700}
.seller-review-meta-separator{color:#CBD5E1}
.seller-review-date{color:#8491A1;font-size:11px;font-weight:550;white-space:nowrap}
.seller-review-comment{margin:15px 0 0;max-width:100%;font-size:12px;line-height:1.8;color:#465262;white-space:pre-line;overflow-wrap:anywhere}
.seller-review-photos{display:grid;grid-template-columns:repeat(5,minmax(0,76px));gap:9px;margin-top:15px}
.seller-review-photo{display:block;aspect-ratio:1;overflow:hidden;border:1px solid #DFE5EB;border-radius:11px;background:#F8FAFC}
.seller-review-photo img{width:100%;height:100%;object-fit:cover}
.seller-review-photo:focus-visible{outline:3px solid rgba(200,137,18,.25);outline-offset:3px}
.seller-review-reply-area{padding:16px 26px 18px;border-top:1px solid #EFF2F5;background:#FAFBFC}
.seller-review-reply-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:10px}
.seller-review-reply-label{font-size:11.5px;font-weight:750;color:#344054}
.seller-review-reply-help{font-size:10.5px;color:#8A96A6;line-height:1.5}
.seller-review-reply-form{display:grid;grid-template-columns:minmax(0,1fr) 145px;gap:12px;align-items:stretch}
.seller-review-reply-input{display:block;width:100%;min-height:66px;max-height:170px;resize:vertical;padding:12px 14px;border:1px solid #DCE3EA;border-radius:12px;background:#fff;color:#344054;font-size:11.5px;line-height:1.6;outline:0}
.seller-review-reply-input::placeholder{color:#A0AAB9}
.seller-review-reply-input:focus-visible{border-color:#C88912;box-shadow:0 0 0 3px rgba(200,137,18,.09)}
.seller-review-reply-button{display:inline-flex;align-items:center;justify-content:center;align-self:end;gap:8px;min-height:48px;border:1px solid #C88912;border-radius:12px;background:#C88912;padding:0 15px;color:white;font-size:11.5px;font-weight:750;cursor:pointer}
.seller-review-reply-button svg{width:15px;height:15px}
.seller-review-reply-button:hover,.seller-review-reply-button:focus-visible{background:#B47B0E;border-color:#B47B0E;outline:0}
/* Empty state + Laravel pagination */
.seller-reviews-empty{display:grid;min-height:260px;place-items:center;padding:35px 22px;text-align:center}
.seller-reviews-empty-icon{display:grid;width:72px;height:72px;place-items:center;margin-inline:auto;color:#94A3B8;border-radius:20px;background:#F8FAFC}
.seller-reviews-empty-icon svg{width:37px;height:37px}
.seller-reviews-empty-title{margin-top:16px;color:#344054;font-size:17px;font-weight:750}
.seller-reviews-empty-copy{margin:7px auto 0;max-width:410px;color:#8794A4;font-size:11.5px;line-height:1.65}
.seller-reviews-pagination{padding:15px 24px;border-top:1px solid #E9EDF2;background:white}
.seller-reviews-pagination nav{font-family:inherit;font-size:11px}
.seller-reviews-pagination nav a,.seller-reviews-pagination nav span{border-color:#E2E8F0!important}
.seller-reviews-pagination nav [aria-current="page"] span{background:#C88912!important;color:#fff!important;border-color:#C88912!important}
.seller-reviews-page :is(button,input,textarea,a):focus-visible{outline-offset:2px}
.seller-reviews-alert{display:flex;gap:12px;margin-bottom:14px;padding:12px 16px;border:1px solid;border-radius:13px;font-size:11.5px;line-height:1.6}
.seller-reviews-alert--success{border-color:#BFE7D0;background:#F0FCF5;color:#166534}
.seller-reviews-alert--error{border-color:#FBC5C5;background:#FEF3F3;color:#B91C1C}
.seller-reviews-alert-icon{display:grid;width:25px;height:25px;place-items:center;flex:0 0 25px}
.seller-reviews-alert-icon svg{width:19px;height:19px}
@media(max-width:1080px){.seller-reviews-filter-form{grid-template-columns:minmax(0,1fr) minmax(155px,190px) minmax(150px,175px)}.seller-reviews-filter-button,.seller-reviews-reset{width:100%}.seller-reviews-filter-button{grid-column:2}.seller-reviews-reset{grid-column:3}}
@media(max-width:900px){.seller-reviews-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.seller-reviews-filter-form{grid-template-columns:repeat(2,minmax(0,1fr))}.seller-reviews-search-wrap{grid-column:1/-1}.seller-reviews-filter-button{grid-column:auto}}
@media(max-width:640px){.seller-reviews-title{font-size:25px}.seller-reviews-eyebrow{font-size:9.5px}.seller-reviews-summary{gap:10px;margin-top:18px}.seller-reviews-stat{min-height:105px;padding:14px}.seller-reviews-stat-label{font-size:11.5px}.seller-reviews-stat-value{font-size:23px}.seller-reviews-stat-note{font-size:10px}.seller-reviews-stat-icon,.seller-reviews-stat-icon svg{width:23px;height:23px;flex-basis:23px}.seller-reviews-filter-form{gap:9px;margin-top:18px}.seller-reviews-search-wrap{grid-column:1/-1}.seller-reviews-search,.seller-reviews-native,.seller-reviews-dropdown-trigger,.seller-reviews-filter-button,.seller-reviews-reset{height:46px;min-height:46px;font-size:11px}.seller-reviews-feed{margin-top:18px}.seller-reviews-feed-head{padding:18px 16px;align-items:flex-start;flex-direction:column}.seller-reviews-feed-title{font-size:17px}.seller-review-main{padding:17px 16px}.seller-review-top{flex-direction:column;gap:7px}.seller-review-product-name{font-size:12.5px}.seller-review-date{font-size:10.5px}.seller-review-reply-area{padding:14px 16px}.seller-review-reply-head{flex-direction:column;align-items:flex-start;gap:3px}.seller-review-reply-form{grid-template-columns:1fr}.seller-review-reply-button{min-height:44px;width:100%}.seller-review-photos{grid-template-columns:repeat(auto-fill,minmax(55px,1fr))}.seller-reviews-pagination{padding:14px 12px}}
/* Buyer Cart mobile depth and shared Seller page rounded-panel geometry. */
@media (max-width: 767px) {
    .seller-reviews-page {
        --reviews-main-shadow:
            0 1px 3px rgba(15, 23, 42, .035),
            0 6px 16px rgba(15, 23, 42, .055),
            0 13px 28px rgba(15, 23, 42, .065),
            inset 0 1px 0 rgba(255, 255, 255, .90);
    }
    .seller-reviews-page .seller-reviews-stat,
    .seller-reviews-page .seller-reviews-feed {
        border-radius: 16px;
    }
}
@media print {
    .seller-reviews-page .seller-reviews-stat,
    .seller-reviews-page .seller-reviews-feed {
        box-shadow: none;
        break-inside: avoid;
    }
}

@media(prefers-reduced-motion:reduce){.seller-reviews-page *{transition-duration:.01ms!important;scroll-behavior:auto!important}}

@endverbatim
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



    <form method="GET" action="{{ route('seller.reviews-center.index') }}" class="seller-reviews-filter-form" aria-label="Filter buyer reviews">
        <label class="seller-reviews-search-wrap">
            <span class="sr-only">Search reviews</span>
            <svg class="seller-reviews-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input class="seller-reviews-search" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search product, order, buyer, or comment..." autocomplete="off">
        </label>
        <div class="seller-reviews-dropdown" data-sari-review-dropdown>
            <select class="seller-reviews-native" name="rating" aria-label="Filter by star rating" data-sari-review-native>
                <option value="all" @selected(($ratingFilter ?? 'all') === 'all')>All Ratings</option>
                @for($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" @selected((string)($ratingFilter ?? 'all') === (string)$r)>{{ $r }} Star{{ $r === 1 ? '' : 's' }}</option>
                @endfor
            </select>
            <button type="button" class="seller-reviews-dropdown-trigger" data-sari-review-trigger aria-haspopup="listbox" aria-expanded="false" hidden>
                <span data-sari-review-label>All Ratings</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
            </button>
            <div class="seller-reviews-dropdown-menu" role="listbox" aria-label="Star rating" data-sari-review-menu hidden></div>
        </div>
        <div class="seller-reviews-dropdown" data-sari-review-dropdown>
            <select class="seller-reviews-native" name="reply" aria-label="Filter by reply status" data-sari-review-native>
                <option value="all" @selected(($replyFilter ?? 'all') === 'all')>All Replies</option>
                <option value="pending" @selected(($replyFilter ?? 'all') === 'pending')>Needs Reply</option>
                <option value="replied" @selected(($replyFilter ?? 'all') === 'replied')>Replied</option>
            </select>
            <button type="button" class="seller-reviews-dropdown-trigger" data-sari-review-trigger aria-haspopup="listbox" aria-expanded="false" hidden>
                <span data-sari-review-label>All Replies</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
            </button>
            <div class="seller-reviews-dropdown-menu" role="listbox" aria-label="Reply status" data-sari-review-menu hidden></div>
        </div>
        <button type="submit" class="seller-reviews-filter-button"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M7 12h10M10 17h4"/></svg>Apply Filter</button>
        <a class="seller-reviews-reset" href="{{ route('seller.reviews-center.index') }}">Reset</a>
    </form>

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
            <span class="seller-reviews-results">{{ number_format($reviews->total()) }} {{ $reviews->total() === 1 ? 'review' : 'reviews' }}</span>

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

                        @if(!empty($review->image_paths))
                            <div class="seller-review-photos" aria-label="Buyer review photos">
                                @foreach(array_slice((array) $review->image_paths, 0, 5) as $reviewImagePath)
                                    <a
                                        href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($reviewImagePath) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="seller-review-photo"
                                    >
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($reviewImagePath) }}"
                                            alt="Buyer review photo"
                                            loading="lazy"
                                            
                                        >
                                    </a>
                                @endforeach
                            </div>
                        @endif

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



                        <div class="seller-reviews-empty-title">{{ ($search ?? '') !== '' || ($ratingFilter ?? 'all') !== 'all' || ($replyFilter ?? 'all') !== 'all' ? 'No matching reviews' : 'No Buyer reviews yet' }}</div>

                        <div class="seller-reviews-empty-copy">

                            {{ ($search ?? '') !== '' || ($ratingFilter ?? 'all') !== 'all' || ($replyFilter ?? 'all') !== 'all' ? 'Try a different search or reset the filters.' : 'Verified product feedback will appear here after Buyers submit reviews.' }}

                        </div>

                    </div>

                </div>

            @endforelse

        </div>



        @if($reviews->total() > 0)
            <div class="seller-reviews-pagination">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                    <span class="seller-reviews-results">Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews</span>
                    @if($reviews->hasPages())
                        {{ $reviews->links() }}
                    @endif
                </div>
            </div>
        @endif


    </section>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('.seller-reviews-page');
    if (!root) return;
    // Self-contained enhancement: native selects remain functional without JS.
    const menus = [...root.querySelectorAll('[data-sari-review-dropdown]')];
    if (root.dataset.sariReviewsFiltersReady === '1') return;
    root.dataset.sariReviewsFiltersReady = '1';
    const closeAll = (except = null) => menus.forEach(el => {
        if (el === except) return;
        const menu = el.querySelector('[data-sari-review-menu]');
        const trigger = el.querySelector('[data-sari-review-trigger]');
        if (menu) menu.hidden = true;
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
    menus.forEach(el => {
        const select = el.querySelector('[data-sari-review-native]');
        const trigger = el.querySelector('[data-sari-review-trigger]');
        const menu = el.querySelector('[data-sari-review-menu]');
        const label = el.querySelector('[data-sari-review-label]');
        if (!select || !trigger || !menu || !label) return;
        const options = [...select.options];
        const sync = () => {
            label.textContent = select.selectedOptions[0]?.textContent?.trim() || '';
            menu.querySelectorAll('[data-review-value]').forEach(button => {
                button.setAttribute('aria-selected', String(button.dataset.reviewValue === select.value));
            });
        };
        options.forEach(option => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'seller-reviews-dropdown-option';
            btn.setAttribute('role', 'option');
            btn.dataset.reviewValue = option.value;
            btn.textContent = option.textContent.trim();
            btn.addEventListener('click', () => {
                select.value = option.value;
                sync();
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            });
            menu.appendChild(btn);
        });
        trigger.hidden = false;
        el.dataset.enhanced = 'true';
        sync();
        trigger.addEventListener('click', () => {
            const open = menu.hidden;
            closeAll(el);
            menu.hidden = !open;
            trigger.setAttribute('aria-expanded', String(open));
        });
        trigger.addEventListener('keydown', event => {
            if (!['ArrowDown','ArrowUp'].includes(event.key)) return;
            event.preventDefault();
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            const items = [...menu.querySelectorAll('button')];
            const current = Math.max(0,items.findIndex(x => x.dataset.reviewValue === select.value));
            items[event.key === 'ArrowDown' ? Math.min(items.length - 1,current + 1) : Math.max(0,current - 1)]?.focus();
        });
        menu.addEventListener('keydown', event => {
            const items = [...menu.querySelectorAll('button')];
            const index = items.indexOf(document.activeElement);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                items[(index + (event.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length]?.focus();
            }
        });
    });
    document.addEventListener('pointerdown', event => {
        if (!menus.some(el => el.contains(event.target))) closeAll();
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        const opened = menus.find(el => !el.querySelector('[data-sari-review-menu]')?.hidden);
        if (opened) { closeAll(); opened.querySelector('[data-sari-review-trigger]')?.focus(); }
    });
})();
</script>
@endpush

