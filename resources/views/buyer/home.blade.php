@extends('layouts.buyer')

@section('title', 'SARI — Shop Everything You Need')
@section('page-title', 'Home')

@push('styles')
    <link
        rel="preload"
        as="image"
        href="{{ asset('images/sari-hero-bg.webp') }}"
        type="image/webp"
        fetchpriority="high"
    >
    <link
        rel="preload"
        as="image"
        href="{{ asset('images/reward-voucher.png') }}"
        type="image/png"
        fetchpriority="high"
    >
@endpush

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | HOME PAGE DATA
    |--------------------------------------------------------------------------
    | Existing controller data is preserved.
    */
    $categoryCollection = collect($categories ?? []);
    $featuredCollection = collect($featuredProducts ?? []);

    $bestSellerProducts = $featuredCollection
        ->sortByDesc(fn ($product) => (int) ($product['sold'] ?? 0))
        ->take(4)
        ->values();

    $featuredDeals = [
        [
            'title' => 'Watch Essentials',
            'offer' => 'Up to 50% OFF',
            'description' => 'Timeless picks, better prices.',
            'image' => 'images/weekend-watch.png',
            'type' => 'flash',
        ],
        [
            'title' => 'Beauty Must-Haves',
            'offer' => 'Up to 40% OFF',
            'description' => 'Everyday beauty essentials.',
            'image' => 'images/beauty-bestsellers.png',
            'type' => 'beauty',
        ],
        [
            'title' => 'Home Upgrades',
            'offer' => 'Up to 35% OFF',
            'description' => 'Simple upgrades for your space.',
            'image' => 'images/home-essentials.png',
            'type' => 'home',
        ],
    ];

    $recommendedCategories = $categoryCollection
        ->pluck('name')
        ->filter()
        ->take(4)
        ->values();
@endphp

@push('styles')
    @foreach ($categoryCollection->take(10) as $category)
        @if (!empty($category['image']))
            <link rel="preload" as="image" href="{{ asset($category['image']) }}">
        @endif
    @endforeach

    @foreach ($featuredDeals as $deal)
        <link rel="preload" as="image" href="{{ asset($deal['image']) }}">
    @endforeach
@endpush

<style>
    /*
    |--------------------------------------------------------------------------
    | PREMIUM BUYER HOME
    |--------------------------------------------------------------------------
    */
    .sari-home {
        --sari-gold: #cc8a0b;
        --sari-gold-dark: #a96d07;
        --sari-cream: #fff8eb;
        --sari-soft: #faf8f4;
        --sari-border: #e9e3db;
        --sari-text: #24201c;
        --sari-muted: #7f776e;
        --sari-green: #4f8065;

        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        background: #fbfaf8;
        color: var(--sari-text);
    }

    .sari-home * {
        box-sizing: border-box;
    }

    .sari-home-shell {
        width: 100%;
        max-width: 1680px;
        margin: 0 auto;
    }

    .sari-section-title {
        letter-spacing: -.035em;
        line-height: 1.1;
    }

    .sari-section-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #9a6815;
        font-size: 8px;
        font-weight: 700;
        transition: color .16s ease;
    }

    .sari-section-link:hover {
        color: #76500d;
    }

    .sari-card {
        border: 1px solid var(--sari-border);
        background: #fff;
        box-shadow: 0 9px 24px rgba(37, 29, 21, .038);
    }

    /*
    |--------------------------------------------------------------------------
    | SIDEBAR ACTIVE STATE
    |--------------------------------------------------------------------------
    */
    #buyerSidebarNav [data-buyer-sidebar-item].sari-route-current {
        background: #d9930a !important;
        color: #fff !important;
        box-shadow: 0 10px 25px rgba(217,147,10,.18) !important;
        font-weight: 600 !important;
    }

    #buyerSidebarNav [data-buyer-sidebar-item].sari-route-not-current {
        background: transparent !important;
        color: #514b42 !important;
        box-shadow: none !important;
        font-weight: 500 !important;
    }

    #buyerSidebarNav [data-buyer-sidebar-item].sari-route-not-current:hover {
        background: #f9f1e3 !important;
        color: #a96e05 !important;
    }

    /*
    |--------------------------------------------------------------------------
    | HERO
    |--------------------------------------------------------------------------
    */
    .sari-home-hero {
        position: relative;
        min-height: 390px;
        overflow: hidden;
        border: 1px solid #eadfce;
        border-radius: 20px;
        background: #fffaf1;
        box-shadow: 0 13px 32px rgba(46, 35, 22, .055);
        isolation: isolate;
    }

    .sari-home-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        background:
            linear-gradient(
                90deg,
                rgba(255,252,247,1) 0%,
                rgba(255,250,241,.98) 39%,
                rgba(255,249,239,.86) 51%,
                rgba(255,249,239,.15) 72%,
                rgba(255,249,239,0) 100%
            );
    }

    .sari-hero-image {
        position: absolute;
        inset: 0 0 0 43%;
        z-index: -2;
        overflow: hidden;
    }

    .sari-hero-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        filter: saturate(.88) contrast(.97) brightness(1.02);
    }

    .sari-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 27px;
        border: 1px solid #e4c98f;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        padding: 0 11px;
        color: #996514;
        font-size: 7px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-hero-title {
        max-width: 720px;
        margin-top: 16px;
        font-size: clamp(34px, 4vw, 58px);
        font-weight: 700;
        line-height: 1.03;
        letter-spacing: -.055em;
        color: #171411;
    }

    .sari-hero-title span {
        display: block;
        color: #b8790f;
    }

    .sari-hero-copy {
        max-width: 560px;
        margin-top: 14px;
        color: #6f675f;
        font-size: 10px;
        line-height: 1.75;
    }

    .sari-primary-btn,
    .sari-secondary-btn {
        display: inline-flex;
        height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 9px;
        padding: 0 20px;
        font-size: 8.5px;
        font-weight: 700;
        transition: background-color .16s ease, border-color .16s ease, transform .16s ease;
    }

    .sari-primary-btn {
        background: var(--sari-gold);
        color: #fff;
        box-shadow: 0 8px 20px rgba(204,138,11,.16);
    }

    .sari-primary-btn:hover {
        background: var(--sari-gold-dark);
        transform: translateY(-1px);
    }

    .sari-secondary-btn {
        border: 1px solid #ddd3c6;
        background: #fff;
        color: #514940;
    }

    .sari-secondary-btn:hover {
        border-color: #cfb47c;
        background: #fffaf1;
        color: #8f6010;
    }

    .sari-hero-trust {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 18px;
    }

    .sari-hero-trust-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6f675f;
        font-size: 7.3px;
        font-weight: 600;
    }

    .sari-hero-trust-dot {
        display: grid;
        width: 18px;
        height: 18px;
        place-items: center;
        border-radius: 999px;
        background: #c98a17;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | BENEFITS
    |--------------------------------------------------------------------------
    */
    .sari-benefit-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        overflow: hidden;
        border: 1px solid #e9e3db;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(42, 33, 24, .035);
    }

    .sari-benefit-item {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
        padding: 14px 17px;
    }

    .sari-benefit-item + .sari-benefit-item {
        border-left: 1px solid #f0ebe5;
    }

    .sari-benefit-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #fff4dd;
        color: #b97810;
    }

    .sari-benefit-item h3 {
        font-size: 8.8px;
        font-weight: 700;
        color: #302a24;
    }

    .sari-benefit-item p {
        margin-top: 2px;
        font-size: 6.8px;
        color: #8f867c;
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */
    .sari-category-rail {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 2px 2px 7px;
        scroll-behavior: smooth;
        scrollbar-width: none;
    }

    .sari-category-rail::-webkit-scrollbar {
        display: none;
    }

    .sari-category-card {
        width: 132px;
        flex: 0 0 132px;
        overflow: hidden;
        border: 1px solid #e8e1d8;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 7px 18px rgba(43,34,24,.03);
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .sari-category-card:hover {
        border-color: #dbc394;
        box-shadow: 0 10px 24px rgba(43,34,24,.05);
        transform: translateY(-1px);
    }

    .sari-category-image {
        height: 78px;
        overflow: hidden;
        background: #f6f2eb;
    }

    .sari-category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .2s ease;
    }

    .sari-category-card:hover img {
        transform: scale(1.025);
    }

    .sari-category-name {
        display: block;
        min-height: 37px;
        padding: 10px 8px;
        color: #3a332c;
        font-size: 7.3px;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | FEATURED DEALS
    |--------------------------------------------------------------------------
    */
    .sari-deal-card {
        position: relative;
        min-height: 150px;
        overflow: hidden;
        border: 1px solid #eadfce;
        border-radius: 14px;
        background: #fff7e9;
        isolation: isolate;
        box-shadow: 0 8px 22px rgba(44,35,25,.035);
    }

    .sari-deal-media {
        position: absolute;
        inset: 0 0 0 46%;
        z-index: -2;
    }

    .sari-deal-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-deal-overlay {
        position: absolute;
        inset: 0;
        z-index: -1;
        background:
            linear-gradient(
                90deg,
                #fff8eb 0%,
                #fff8eb 41%,
                rgba(255,248,235,.95) 48%,
                rgba(255,248,235,.64) 65%,
                rgba(255,248,235,0) 88%
            );
    }

    .sari-deal-title {
        color: #2a241f;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .sari-deal-offer {
        margin-top: 5px;
        color: #ba7b0e;
        font-size: 10px;
        font-weight: 700;
    }

    .sari-deal-description {
        margin-top: 5px;
        color: #7c7369;
        font-size: 6.8px;
        line-height: 1.5;
    }

    .sari-deal-shop {
        display: inline-flex;
        height: 31px;
        align-items: center;
        gap: 7px;
        margin-top: 12px;
        border-radius: 7px;
        background: #c98a17;
        padding: 0 11px;
        color: #fff;
        font-size: 7px;
        font-weight: 700;
    }

    /*
    |--------------------------------------------------------------------------
    | RECOMMENDED PRODUCTS
    |--------------------------------------------------------------------------
    */
    .sari-filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
    }

    .sari-filter-chip {
        display: inline-flex;
        min-height: 29px;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 0 10px;
        color: #746b61;
        font-size: 6.8px;
        font-weight: 600;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }

    .sari-filter-chip:hover {
        border-color: #e0cfad;
        background: #fffaf1;
        color: #8f6111;
    }

    .sari-filter-chip.is-active {
        background: #cb8b16;
        color: #fff;
    }

    .sari-product-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) repeat(3, minmax(0, 1fr));
        grid-auto-flow: row dense;
        align-items: stretch;
        gap: 14px;
    }

    .sari-product-grid > .sari-featured-product-card {
        grid-row: span 2;
    }

    /*
    |--------------------------------------------------------------------------
    | REWARDS
    |--------------------------------------------------------------------------
    */
    .sari-rewards-banner {
        position: relative;
        min-height: 118px;
        overflow: hidden;
        border: 1px solid #3a3732;
        border-radius: 14px;
        background:
            radial-gradient(circle at 12% 100%, rgba(202,139,24,.18), transparent 24%),
            #272522;
        box-shadow: 0 12px 28px rgba(20,18,15,.10);
    }

    .sari-rewards-image-wrap {
        position: absolute;
        left: 18px;
        bottom: -18px;
        width: 205px;
        height: 142px;
        pointer-events: none;
    }

    .sari-rewards-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: bottom;
        filter: drop-shadow(0 8px 14px rgba(0,0,0,.20));
    }

    .sari-rewards-content {
        display: flex;
        min-height: 118px;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 18px 24px 18px 245px;
    }

    .sari-rewards-title {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.03em;
    }

    .sari-rewards-copy {
        margin-top: 4px;
        color: #c8c0b7;
        font-size: 7.5px;
        line-height: 1.5;
    }

    .sari-rewards-button {
        display: inline-flex;
        height: 37px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 8px;
        background: #d39211;
        padding: 0 17px;
        color: #fff;
        font-size: 7.5px;
        font-weight: 700;
        transition: background-color .16s ease;
    }

    .sari-rewards-button:hover {
        background: #b8780c;
    }

    /*
    |--------------------------------------------------------------------------
    | TRUST
    |--------------------------------------------------------------------------
    */
    .sari-trust-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .sari-trust-card {
        display: flex;
        align-items: center;
        gap: 11px;
        border: 1px solid #e9e3db;
        border-radius: 12px;
        background: #fff;
        padding: 13px;
        box-shadow: 0 7px 18px rgba(43,34,24,.025);
    }

    .sari-trust-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #fff4de;
        color: #b97810;
    }

    .sari-trust-card h3 {
        font-size: 8px;
        font-weight: 700;
        color: #39322c;
    }

    .sari-trust-card p {
        margin-top: 2px;
        color: #938b82;
        font-size: 6.4px;
        line-height: 1.4;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */
    @media (max-width: 1199px) {
        .sari-home-hero {
            min-height: 360px;
        }

        .sari-hero-image {
            inset-left: 46%;
        }

        .sari-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sari-product-grid > .sari-featured-product-card {
            grid-row: auto;
        }
    }

    @media (max-width: 1023px) {
        .sari-hero-image {
            inset: 0;
        }

        .sari-home-hero::before {
            background:
                linear-gradient(
                    90deg,
                    rgba(255,252,247,.98) 0%,
                    rgba(255,250,241,.97) 48%,
                    rgba(255,249,239,.78) 72%,
                    rgba(255,249,239,.55) 100%
                );
        }

        .sari-benefit-strip {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }

        .sari-benefit-item:nth-child(3) {
            border-left: 0;
            border-top: 1px solid #f0ebe5;
        }

        .sari-benefit-item:nth-child(4) {
            border-top: 1px solid #f0ebe5;
        }

        .sari-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sari-trust-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .sari-home-hero {
            min-height: 410px;
        }

        .sari-hero-content {
            padding: 30px 20px !important;
        }

        .sari-hero-title {
            font-size: 38px;
        }

        .sari-rewards-image-wrap {
            position: relative;
            left: auto;
            bottom: auto;
            width: 100%;
            height: 120px;
        }

        .sari-rewards-content {
            min-height: auto;
            flex-direction: column;
            align-items: stretch;
            padding: 0 18px 18px;
            text-align: center;
        }

        .sari-rewards-button {
            width: 100%;
        }
    }

    @media (max-width: 639px) {
        .sari-benefit-strip,
        .sari-trust-grid {
            grid-template-columns: 1fr;
        }

        .sari-benefit-item + .sari-benefit-item {
            border-left: 0;
            border-top: 1px solid #f0ebe5;
        }

        .sari-product-grid {
            grid-template-columns: 1fr;
        }

        .sari-category-card {
            width: 118px;
            flex-basis: 118px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-category-card,
        .sari-category-card img,
        .sari-primary-btn,
        .sari-secondary-btn {
            transition: none !important;
            transform: none !important;
        }
    }
</style>

@include('components.buyer.header')

<div class="sari-home">
    <main class="sari-home-shell px-4 pb-8 pt-4 sm:px-6 sm:pt-5 lg:px-8 xl:px-10">

        {{-- ============================================================
            HERO
        ============================================================ --}}
        <section class="sari-home-hero">
            <div class="sari-hero-image">
                <img
                    src="{{ asset('images/sari-hero-bg.webp') }}"
                    alt="SARI Marketplace"
                    width="1536"
                    height="1024"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </div>

            <div class="sari-hero-content flex min-h-[390px] items-center px-7 py-8 sm:px-10 lg:px-12 xl:px-14">
                <div class="w-full max-w-[720px]">
                    <span class="sari-hero-badge">
                        Elevated Everyday
                    </span>

                    <h1 class="sari-hero-title">
                        Variety You Need,
                        <span>Convenience You Deserve.</span>
                    </h1>

                    <p class="sari-hero-copy">
                        Discover brand new finds, everyday essentials, and everything in between —
                        all in one trusted marketplace.
                    </p>

                    <div class="mt-5 flex flex-col gap-2.5 sm:flex-row">
                        <a href="{{ route('buyer.products') }}" class="sari-primary-btn">
                            Shop Now
                            <span>→</span>
                        </a>

                        <a href="#categories" class="sari-secondary-btn">
                            Explore Categories
                        </a>
                    </div>

                    <div class="sari-hero-trust">
                        @foreach (['Trusted Sellers', 'Secure Payments', 'Easy Returns'] as $label)
                            <span class="sari-hero-trust-item">
                                <span class="sari-hero-trust-dot">✓</span>
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================
            BENEFITS
        ============================================================ --}}
        <section class="mt-3">
            <div class="sari-benefit-strip">
                <a href="{{ route('buyer.products') }}" class="sari-benefit-item">
                    <span class="sari-benefit-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M3 7h11v9H3z"></path>
                            <path d="M14 10h4l3 3v3h-7z"></path>
                            <circle cx="7" cy="18" r="1.5"></circle>
                            <circle cx="18" cy="18" r="1.5"></circle>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <h3>Free Shipping</h3>
                        <p>On eligible orders</p>
                    </span>
                </a>

                <a href="{{ route('buyer.rewards') }}" class="sari-benefit-item">
                    <span class="sari-benefit-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 9h16v11H4z"></path>
                            <path d="M12 9v11"></path>
                            <path d="M4 13h16"></path>
                            <path d="M8 9c-2.5 0-3.5-1.5-3.5-3S6 3.5 7.5 4c1.7.6 3.2 3 4.5 5"></path>
                            <path d="M16 9c2.5 0 3.5-1.5 3.5-3S18 3.5 16.5 4c-1.7.6-3.2 3-4.5 5"></path>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <h3>Earn Rewards</h3>
                        <p>Shop and earn points</p>
                    </span>
                </a>

                <div class="sari-benefit-item">
                    <span class="sari-benefit-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M12 3 5 6v5c0 4.5 2.8 8.1 7 10 4.2-1.9 7-5.5 7-10V6l-7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <h3>Secure Payments</h3>
                        <p>Safe and protected</p>
                    </span>
                </div>

                <div class="sari-benefit-item">
                    <span class="sari-benefit-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 10h16v10H4z"></path>
                            <path d="M3 10 5 4h14l2 6"></path>
                            <path d="M8 14h3v6H8z"></path>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <h3>Verified Sellers</h3>
                        <p>Quality and trusted</p>
                    </span>
                </div>
            </div>
        </section>

        {{-- ============================================================
            SHOP BY CATEGORY
        ============================================================ --}}
        <section id="categories" class="mt-5">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="sari-section-title text-[18px] font-bold text-[#201b17] sm:text-[20px]">Shop by Category</h2>
                </div>

                <a href="{{ route('buyer.products') }}" class="sari-section-link">
                    View all categories
                    <span>→</span>
                </a>
            </div>

            <div id="buyerCategoryRail" class="sari-category-rail mt-3">
                @forelse ($categoryCollection as $category)
                    <a
                        href="{{ route('buyer.products', ['category' => $category['name']]) }}"
                        class="sari-category-card"
                    >
                        <div class="sari-category-image">
                            @if (!empty($category['image']))
                                <img
                                    src="{{ asset($category['image']) }}"
                                    alt="{{ $category['name'] }}"
                                    width="160"
                                    height="100"
                                    loading="eager"
                                    decoding="async"
                                >
                            @endif
                        </div>
                        <span class="sari-category-name">{{ $category['name'] }}</span>
                    </a>
                @empty
                    <div class="sari-card rounded-[12px] px-4 py-5 text-[8px] text-[#8f867c]">
                        Categories will appear here.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ============================================================
            FEATURED DEALS
        ============================================================ --}}
        <section class="mt-5">
            <div class="flex items-end justify-between gap-4">
                <h2 class="sari-section-title text-[18px] font-bold text-[#201b17] sm:text-[20px]">Featured Deals</h2>

                <a href="{{ route('buyer.products', ['discounted' => 1]) }}" class="sari-section-link">
                    View all deals
                    <span>→</span>
                </a>
            </div>

            <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-3">
                @foreach ($featuredDeals as $deal)
                    <a
                        href="{{ route('buyer.products', ['discounted' => 1]) }}"
                        class="sari-deal-card p-4"
                    >
                        <div class="sari-deal-media">
                            <img
                                src="{{ asset($deal['image']) }}"
                                alt="{{ $deal['title'] }}"
                                loading="eager"
                                decoding="async"
                            >
                        </div>
                        <div class="sari-deal-overlay"></div>

                        <div class="relative z-10 max-w-[55%]">
                            <p class="sari-deal-title">{{ $deal['title'] }}</p>
                            <p class="sari-deal-offer">{{ $deal['offer'] }}</p>
                            <p class="sari-deal-description">{{ $deal['description'] }}</p>
                            <span class="sari-deal-shop">Shop Now →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ============================================================
            RECOMMENDED PRODUCTS
        ============================================================ --}}
        <section class="mt-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[7px] font-bold uppercase tracking-[.13em] text-[#aa7415]">Curated marketplace</p>
                    <h2 class="sari-section-title mt-1 text-[19px] font-bold text-[#201b17] sm:text-[22px]">Recommended for You</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="sari-filter-row">
                        <a href="{{ route('buyer.products') }}" class="sari-filter-chip is-active">All</a>

                        @foreach ($recommendedCategories as $categoryName)
                            <a
                                href="{{ route('buyer.products', ['category' => $categoryName]) }}"
                                class="sari-filter-chip"
                            >
                                {{ $categoryName }}
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ route('buyer.products') }}" class="sari-section-link ml-1">
                        View all
                        <span>→</span>
                    </a>
                </div>
            </div>

            <div class="sari-product-grid mt-3">
                @forelse ($featuredCollection as $product)
                    <x-buyer.product-card
                        :product="$product"
                        :priority="true"
                        :featured="$loop->first"
                        :show-promotions="true"
                        :show-stock="true"
                        :show-quick-add="true"
                    />
                @empty
                    <div class="sari-card col-span-full rounded-[14px] px-6 py-12 text-center">
                        <p class="text-[9px] font-semibold text-[#5f574f]">No products available yet.</p>
                        <p class="mt-1 text-[7.5px] text-[#938a80]">Approved seller products will appear here.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ============================================================
            REWARDS & VOUCHERS
        ============================================================ --}}
        <section class="sari-rewards-banner mt-5">
            <div class="sari-rewards-image-wrap">
                <img
                    src="{{ asset('images/reward-voucher.png') }}"
                    alt="SARI Rewards and Vouchers"
                    width="600"
                    height="400"
                    loading="eager"
                    decoding="async"
                >
            </div>

            <div class="sari-rewards-content">
                <div>
                    <h2 class="sari-rewards-title">Rewards & Vouchers</h2>
                    <p class="sari-rewards-copy">Shop more. Earn more. Enjoy exclusive deals and vouchers.</p>
                </div>

                <a href="{{ route('buyer.rewards') }}" class="sari-rewards-button">
                    Learn More
                    <span>→</span>
                </a>
            </div>
        </section>

        {{-- ============================================================
            TRUST & BUYER PROTECTION
        ============================================================ --}}
        <section class="mt-5">
            <h2 class="sari-section-title text-[17px] font-bold text-[#201b17] sm:text-[19px]">Trust & Buyer Protection</h2>

            <div class="sari-trust-grid mt-3">
                <article class="sari-trust-card">
                    <span class="sari-trust-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M12 3 5 6v5c0 4.5 2.8 8.1 7 10 4.2-1.9 7-5.5 7-10V6l-7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </span>
                    <div>
                        <h3>Quality Products</h3>
                        <p>Shop with confidence from approved listings.</p>
                    </div>
                </article>

                <article class="sari-trust-card">
                    <span class="sari-trust-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>
                    </span>
                    <div>
                        <h3>Secure Payments</h3>
                        <p>Your checkout data stays protected.</p>
                    </div>
                </article>

                <article class="sari-trust-card">
                    <span class="sari-trust-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 8 12 4l8 4-8 4-8-4Z"></path>
                            <path d="M5 11v6l7 3 7-3v-6"></path>
                            <path d="m9 15 2 2 4-4"></path>
                        </svg>
                    </span>
                    <div>
                        <h3>Easy Returns</h3>
                        <p>Hassle-free support for eligible purchases.</p>
                    </div>
                </article>

                <article class="sari-trust-card">
                    <span class="sari-trust-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 13v-2a8 8 0 0 1 16 0v2"></path>
                            <path d="M4 13h3v6H5a1 1 0 0 1-1-1v-5Z"></path>
                            <path d="M20 13h-3v6h2a1 1 0 0 0 1-1v-5Z"></path>
                        </svg>
                    </span>
                    <div>
                        <h3>24/7 Support</h3>
                        <p>Help is available whenever you need it.</p>
                    </div>
                </article>
            </div>
        </section>

        <div class="h-3"></div>
    </main>
</div>

@endsection

@push('scripts')
<script>
(function () {
    function normalizePath(value) {
        try {
            let path = new URL(value, window.location.origin).pathname;
            path = path.replace(/\/+$/, '');
            return path || '/';
        } catch (error) {
            return '/';
        }
    }

    function syncBuyerSidebarActiveState() {
        const nav = document.getElementById('buyerSidebarNav');

        if (!nav) return;

        const currentPath = normalizePath(window.location.href);
        const items = Array.from(nav.querySelectorAll('a[data-buyer-sidebar-item]'));
        const homePath = normalizePath(@json(route('buyer.home')));
        const productsPath = normalizePath(@json(route('buyer.products')));

        const homeAliases = new Set([
            homePath,
            '/buyer/home',
            '/buyer/dashboard'
        ]);

        let matchedItem = null;

        items.forEach(function (item) {
            item.classList.remove('sari-route-current');
            item.classList.add('sari-route-not-current');

            const itemPath = normalizePath(item.href);

            if (itemPath === currentPath) {
                matchedItem = item;
            }
        });

        if (!matchedItem && currentPath.startsWith(productsPath + '/')) {
            matchedItem = items.find(function (item) {
                return normalizePath(item.href) === productsPath;
            }) || null;
        }

        if (!matchedItem && homeAliases.has(currentPath)) {
            matchedItem = items.find(function (item) {
                const itemPath = normalizePath(item.href);
                return itemPath === homePath || itemPath === '/buyer/home';
            }) || null;
        }

        if (matchedItem) {
            matchedItem.classList.remove('sari-route-not-current');
            matchedItem.classList.add('sari-route-current');
        }
    }

    function initSariBuyerHome() {
        const rail = document.getElementById('buyerCategoryRail');

        if (rail && rail.dataset.sariBound !== '1') {
            rail.dataset.sariBound = '1';

            rail.addEventListener('wheel', function (event) {
                if (rail.scrollWidth <= rail.clientWidth) return;
                if (Math.abs(event.deltaY) <= Math.abs(event.deltaX)) return;

                event.preventDefault();

                rail.scrollBy({
                    left: event.deltaY,
                    behavior: 'auto'
                });
            }, { passive: false });
        }

        syncBuyerSidebarActiveState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSariBuyerHome, { once: true });
    } else {
        initSariBuyerHome();
    }

    document.addEventListener('livewire:navigated', function () {
        initSariBuyerHome();
        syncBuyerSidebarActiveState();
    });

    window.addEventListener('popstate', syncBuyerSidebarActiveState);
})();
</script>
@endpush
