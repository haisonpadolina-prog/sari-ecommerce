@extends('layouts.app')

@section('title', $category['name'] . ' — SARI Marketplace')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | SARI rolling ticket image set
    |--------------------------------------------------------------------------
    | These 16 files are intentionally renamed so they DO NOT overwrite the
    | existing cat-* / p-* images already in public/images.
    |
    | Rolling rail = product/category images only.
    | No promo banners, story images, vouchers, or unrelated artwork.
    */
    $railImages = collect([
        asset('images/sari-ticket-01-product-drone.jpg'),
        asset('images/sari-ticket-02-product-earbuds.jpg'),
        asset('images/sari-ticket-03-product-keyboard.jpg'),
        asset('images/sari-ticket-04-product-laptop.jpg'),
        asset('images/sari-ticket-05-product-monitor.jpg'),
        asset('images/sari-ticket-06-product-phone.jpg'),
        asset('images/sari-ticket-07-product-speaker.jpg'),
        asset('images/sari-ticket-08-product-camera.jpg'),
        asset('images/sari-ticket-09-category-accessories.jpg'),
        asset('images/sari-ticket-10-category-beauty.jpg'),
        asset('images/sari-ticket-11-category-electronics.jpg'),
        asset('images/sari-ticket-12-category-fashion.jpg'),
        asset('images/sari-ticket-13-category-food.jpg'),
        asset('images/sari-ticket-14-category-home.jpg'),
        asset('images/sari-ticket-15-category-lifestyle.jpg'),
        asset('images/sari-ticket-16-category-sports.jpg'),
    ])->values();

    // Different but deterministic order for row 2.
    $railImagesReverse = $railImages->reverse()->values();

    // Current category hero remains category-specific.
    $heroImage = asset($category['image']);
@endphp

<div class="sari-market-page">
    @include('components.landing.navbar')
    @include('components.landing.mobile-menu')

    <main class="sari-market-main">
        {{-- =========================================================
             MOVING IMAGE RAIL
             Images only, mixed marketplace categories, 2 rows.
             ========================================================= --}}
        <section
            class="sari-market-rail sari-market-reveal"
            aria-label="SARI marketplace highlights"
        >
            <div class="sari-market-rail__viewport">
                <div class="sari-market-rail__track">
                    @for ($copyIndex = 0; $copyIndex < 2; $copyIndex++)
                        <div class="sari-market-rail__group">
                            @foreach ($railImages as $image)
                                <div class="sari-market-rail__image sari-market-rail__image--large">
                                    <img src="{{ $image }}" alt="" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @endfor
                </div>
            </div>

            <div class="sari-market-rail__viewport">
                <div class="sari-market-rail__track sari-market-rail__track--reverse">
                    @for ($copyIndex = 0; $copyIndex < 2; $copyIndex++)
                        <div class="sari-market-rail__group">
                            @foreach ($railImagesReverse as $image)
                                <div class="sari-market-rail__image sari-market-rail__image--small">
                                    <img src="{{ $image }}" alt="" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        {{-- =========================================================
             CATEGORY HERO
             ========================================================= --}}
        <section class="sari-market-hero">
            <div class="sari-market-shell">
                <div class="sari-market-hero__grid">
                    <div class="sari-market-hero__copy sari-market-reveal">
                        <div class="sari-market-micro-label" aria-label="Category context">
                            <i aria-hidden="true"></i>
                            <span>SARI Marketplace</span>
                            <b>/</b>
                            <span>{{ strtoupper($category['name']) }}</span>
                            <b>/</b>
                            <span>Curated</span>
                        </div>

                        <h1>{{ $category['name'] }}</h1>

                        <p class="sari-market-hero__lead">
                            {{ $category['subtitle'] }} for a better everyday life.
                            Discover approved products from trusted SARI sellers.
                        </p>

                        <dl class="sari-market-stats">
                            <div>
                                <span class="sari-market-stat-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M4.5 6.5h15v11h-15z"></path>
                                        <path d="M8 10h8M8 14h5"></path>
                                    </svg>
                                </span>

                                <dd>
                                    <strong>{{ number_format($products->total()) }}</strong>
                                    <span>products in this category</span>
                                </dd>
                            </div>

                            <div>
                                <span class="sari-market-stat-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3.5 19 6v5.2c0 4.2-2.7 7.5-7 9.3-4.3-1.8-7-5.1-7-9.3V6l7-2.5Z"></path>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </span>

                                <dd>
                                    <strong>Approved</strong>
                                    <span>seller listings only</span>
                                </dd>
                            </div>

                            <div>
                                <span class="sari-market-stat-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle cx="8" cy="9" r="2.5"></circle>
                                        <circle cx="16" cy="9" r="2.5"></circle>
                                        <path d="M3.5 18c.7-3 2.2-4.5 4.5-4.5S11.8 15 12.5 18"></path>
                                        <path d="M11.5 18c.7-3 2.2-4.5 4.5-4.5s3.8 1.5 4.5 4.5"></path>
                                    </svg>
                                </span>

                                <dd>
                                    <strong>Connected</strong>
                                    <span>buyer experience</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>
        </section>

        {{-- =========================================================
             CATEGORY NAVIGATION
             ========================================================= --}}
        <nav class="sari-market-category-nav" aria-label="Product categories">
            <div class="sari-market-shell">
                <div class="sari-market-category-scroll">
                    @foreach ($categories as $item)
                        <a
                            href="{{ route('marketplace.category', ['category' => $item['slug']]) }}"
                            class="sari-market-category-link {{ $item['slug'] === $category['slug'] ? 'is-active' : '' }}"
                            @if ($item['slug'] === $category['slug']) aria-current="page" @endif
                        >
                            <img
                                src="{{ asset($item['image']) }}"
                                alt=""
                                loading="lazy"
                            >

                            <span>
                                <strong>{{ $item['name'] }}</strong>
                                <small>{{ $item['subtitle'] }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        {{-- =========================================================
             SEARCH / SORT / PRODUCT RESULTS
             ========================================================= --}}
        <section class="sari-market-products">
            <div class="sari-market-shell">
                <form
                    method="GET"
                    action="{{ route('marketplace.category', ['category' => $category['slug']]) }}"
                    class="sari-market-toolbar sari-market-reveal"
                >
                    <label class="sari-market-search">
                        <span class="sr-only">Search products</span>

                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="m16 16 4.5 4.5"></path>
                        </svg>

                        <input
                            type="search"
                            name="q"
                            value="{{ $searchQuery }}"
                            placeholder="Search in {{ $category['name'] }}"
                        >
                    </label>

                    @php
                        $sortLabels = [
                            'featured' => 'Featured',
                            'newest' => 'Newest',
                            'price_low' => 'Price: Low to High',
                            'price_high' => 'Price: High to Low',
                        ];

                        $activeSortLabel = $sortLabels[$sort] ?? $sortLabels['featured'];
                    @endphp

                    <div class="sari-market-sort" data-sari-sort>
                        <input
                            type="hidden"
                            name="sort"
                            value="{{ $sort }}"
                            data-sari-sort-input
                        >

                        <button
                            type="button"
                            class="sari-market-sort__button"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            data-sari-sort-toggle
                        >
                            <span class="sari-market-sort__button-copy">
                                <small>Sort by</small>
                                <strong data-sari-sort-label>{{ $activeSortLabel }}</strong>
                            </span>

                            <span class="sari-market-sort__chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
                            </span>
                        </button>

                        <div
                            class="sari-market-sort__menu"
                            role="listbox"
                            aria-label="Sort products"
                            data-sari-sort-menu
                        >
                            @foreach ($sortLabels as $sortValue => $sortLabel)
                                <button
                                    type="button"
                                    role="option"
                                    class="sari-market-sort__option {{ $sort === $sortValue ? 'is-active' : '' }}"
                                    aria-selected="{{ $sort === $sortValue ? 'true' : 'false' }}"
                                    data-sari-sort-option
                                    data-sort-value="{{ $sortValue }}"
                                    data-sort-label="{{ $sortLabel }}"
                                >
                                    <span>{{ $sortLabel }}</span>

                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if ($searchQuery !== '')
                        <a
                            class="sari-market-clear"
                            href="{{ route('marketplace.category', [
                                'category' => $category['slug'],
                                'sort' => $sort,
                            ]) }}"
                        >
                            Clear
                        </a>
                    @endif
                </form>

                <div class="sari-market-results-head sari-market-reveal">
                    <div>
                        <span>Curated Results</span>
                        <h2>
                            {{ number_format($products->total()) }}
                            product{{ $products->total() === 1 ? '' : 's' }} found
                        </h2>
                    </div>

                    <p>Approved {{ strtolower($category['name']) }} listings</p>
                </div>

                @if ($products->count())
                    <div class="sari-market-product-grid">
                        @foreach ($products as $index => $product)
                            @php
                                $productId = (int) ($product['id'] ?? 0);
                                $rawImage = (string) ($product['image'] ?? '');

                                $productImage = str_starts_with($rawImage, 'buyer/products/')
                                    ? route('marketplace.product.image', ['product' => $productId])
                                    : (
                                        str_starts_with($rawImage, 'images/')
                                            ? asset($rawImage)
                                            : $heroImage
                                    );

                                $buyerBuyNowUrl = route(
                                    'buyer.product.details',
                                    ['product' => $productId],
                                    false
                                ) . '?buy_now=1';

                                $price = (float) ($product['price'] ?? 0);
                                $oldPrice = isset($product['old_price']) && $product['old_price'] !== null
                                    ? (float) $product['old_price']
                                    : null;

                                $stock = (int) ($product['stock'] ?? 0);
                                $rating = (float) ($product['rating'] ?? 0);
                                $ratingCount = (int) ($product['rating_count'] ?? 0);
                            @endphp

                            <article
                                class="sari-market-product-card sari-market-product-reveal"
                                style="--sari-card-delay: {{ min($index, 8) * 45 }}ms;"
                            >
                                <div class="sari-market-product-card__media">
                                    <img
                                        src="{{ $productImage }}"
                                        alt="{{ $product['name'] }}"
                                        loading="lazy"
                                    >

                                    @if (!empty($product['free_shipping']) || ($product['discount_percent'] ?? 0) > 0)
                                        <div class="sari-market-product-card__badges">
                                            @if (($product['discount_percent'] ?? 0) > 0)
                                                <span class="is-discount">
                                                    -{{ rtrim(rtrim(number_format((float) $product['discount_percent'], 1), '0'), '.') }}%
                                                </span>
                                            @endif

                                            @if (!empty($product['free_shipping']))
                                                <span>Free Shipping</span>
                                            @endif
                                        </div>
                                    @endif

                                    <a
                                        href="{{ route('buyer.product.details', ['product' => $productId]) }}"
                                        class="sari-market-product-card__view"
                                        aria-label="View {{ $product['name'] }}"
                                    >
                                        <span>View</span>
                                        <b aria-hidden="true">↗</b>
                                    </a>
                                </div>

                                <div class="sari-market-product-card__body">
                                    <p class="sari-market-product-card__seller">
                                        {{ $product['store_name'] ?? 'SARI Seller' }}
                                    </p>

                                    <h3>{{ $product['name'] }}</h3>

                                    <div class="sari-market-product-card__price">
                                        <strong>₱{{ number_format($price, 2) }}</strong>

                                        @if ($oldPrice !== null && $oldPrice > $price)
                                            <span>₱{{ number_format($oldPrice, 2) }}</span>
                                        @endif
                                    </div>

                                    <div class="sari-market-product-card__meta-line">
                                        <div class="sari-market-product-card__rating">
                                            <span class="star" aria-hidden="true">★</span>

                                            @if ($ratingCount > 0)
                                                <strong>{{ number_format($rating, 1) }}</strong>
                                                <span>({{ number_format($ratingCount) }})</span>
                                            @else
                                                <span>New listing</span>
                                            @endif
                                        </div>

                                        <span class="sari-market-product-card__stock {{ $stock > 0 ? 'is-in' : 'is-out' }}">
                                            {{ $stock > 0 ? 'In stock' : 'Out of stock' }}
                                        </span>
                                    </div>

                                    @if ($stock > 0)
                                        @if ($isBuyer)
                                            <a
                                                href="{{ $buyerBuyNowUrl }}"
                                                class="sari-market-buy"
                                            >
                                                Buy Now
                                            </a>
                                        @else
                                            <button
                                                type="button"
                                                class="sari-market-buy"
                                                data-market-auth-trigger
                                                data-redirect-to="{{ $buyerBuyNowUrl }}"
                                            >
                                                Buy Now
                                            </button>
                                        @endif
                                    @else
                                        <button
                                            type="button"
                                            class="sari-market-buy is-disabled"
                                            disabled
                                        >
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($products->hasPages())
                        <nav class="sari-market-pagination" aria-label="Product pages">
                            @if ($products->onFirstPage())
                                <span class="is-disabled">← Previous</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}">← Previous</a>
                            @endif

                            <span class="sari-market-page-number">
                                Page {{ $products->currentPage() }}
                                of {{ $products->lastPage() }}
                            </span>

                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}">Next →</a>
                            @else
                                <span class="is-disabled">Next →</span>
                            @endif
                        </nav>
                    @endif
                @else
                    <div class="sari-market-empty sari-market-reveal">
                        <h2>No products match your search.</h2>

                        <p>
                            @if ($searchQuery !== '')
                                Try a different keyword or clear the current search.
                            @else
                                Approved products from SARI sellers will appear here.
                            @endif
                        </p>

                        <a href="{{ route('marketplace.category', ['category' => $category['slug']]) }}">
                            Reset results
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('components.landing.footer')
    @include('components.marketplace.login-modal')
</div>
@endsection

@push('styles')
<style>
/* ==========================================================
   SARI MARKETPLACE CATEGORY
   Laravel implementation based on the supplied reference UI.
   ========================================================== */

.sari-market-page,
.sari-market-page * {
    box-sizing: border-box;
}

.sari-market-page {
    --sari-market-bg: #fbfaf7;
    --sari-market-card: #ffffff;
    --sari-market-ink: #26211c;
    --sari-market-muted: #777067;
    --sari-market-line: #e9e2d9;
    --sari-market-soft: #f4f0e9;
    --sari-market-gold: #c58a26;
    --sari-market-gold-soft: #f5ead6;
    --sari-market-success: #558064;
    --sari-market-ease: cubic-bezier(.22, 1, .36, 1);

    min-height: 100vh;
    color: var(--sari-market-ink);
    background: var(--sari-market-bg);
    font-family: 'Poppins', sans-serif;
}

.sari-market-page .sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* The landing navbar normally sits on a dark hero.
   Category pages use a light surface, so force readable contrast. */
/* ==========================================================
   CATEGORY PAGE NAVBAR
   Clean, modern, readable, no underline.
   ========================================================== */

.sari-market-page .sari-navbar {
    background: rgba(255, 255, 255, .97) !important;
    border-bottom: 1px solid rgba(38, 33, 28, .08) !important;
    box-shadow: 0 5px 20px rgba(38, 33, 28, .035) !important;
    -webkit-backdrop-filter: blur(16px);
    backdrop-filter: blur(16px);
}

.sari-market-page .sari-navbar-inner {
    width: min(calc(100% - 40px), 1280px) !important;
    min-height: 68px !important;
    margin-inline: auto !important;
    display: flex !important;
    align-items: center !important;
    gap: 20px !important;
}

.sari-market-page .sari-logo-link {
    flex: 0 0 auto !important;
    text-decoration: none !important;
}

.sari-market-page .sari-navbar-logo {
    display: block !important;
    width: auto !important;
    max-width: 88px !important;
    height: 27px !important;
    object-fit: contain !important;
}

.sari-market-page .sari-navbar-nav {
    display: flex !important;
    flex: 1 1 auto !important;
    align-items: center !important;
    justify-content: center !important;
    gap: clamp(18px, 2.2vw, 34px) !important;
    min-width: 0 !important;
}

.sari-market-page .sari-navbar .sari-nav-link,
.sari-market-page .sari-navbar .sari-login,
.sari-market-page .sari-navbar .sari-register {
    position: relative !important;
    color: #2a2520 !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    letter-spacing: .01em !important;
    line-height: 1 !important;
    text-decoration: none !important;
    text-decoration-line: none !important;
    white-space: nowrap !important;
}

.sari-market-page .sari-navbar .sari-nav-link::before,
.sari-market-page .sari-navbar .sari-nav-link::after,
.sari-market-page .sari-navbar .sari-login::before,
.sari-market-page .sari-navbar .sari-login::after,
.sari-market-page .sari-navbar .sari-register::before,
.sari-market-page .sari-navbar .sari-register::after {
    display: none !important;
    content: none !important;
}

.sari-market-page .sari-navbar a {
    text-decoration: none !important;
}

.sari-market-page .sari-navbar .sari-nav-link:hover {
    color: #a66e13 !important;
}

.sari-market-page .sari-navbar-actions {
    display: flex !important;
    flex: 0 0 auto !important;
    align-items: center !important;
    gap: 10px !important;
}

.sari-market-page .sari-search-button,
.sari-market-page .sari-theme-toggle {
    display: grid !important;
    width: 36px !important;
    height: 36px !important;
    place-items: center !important;
    border: 1px solid #ece6df !important;
    border-radius: 9px !important;
    color: #2a2520 !important;
    background: #ffffff !important;
    box-shadow: none !important;
}

.sari-market-page .sari-login {
    display: inline-flex !important;
    min-height: 38px !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1px solid transparent !important;
    border-radius: 10px !important;
    padding: 0 10px !important;
    color: #4f4740 !important;
    background: transparent !important;
    text-decoration: none !important;
    transition:
        color .2s ease,
        background-color .2s ease !important;
}

.sari-market-page .sari-login:hover {
    color: #25211d !important;
    background: #f7f3ed !important;
}

.sari-market-page .sari-register {
    display: inline-flex !important;
    min-height: 38px !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1px solid #d9c9ae !important;
    border-radius: 10px !important;
    padding: 0 16px !important;
    color: #2d2823 !important;
    background: #fffdf9 !important;
    box-shadow: 0 1px 0 rgba(38, 33, 28, .02) !important;
    text-decoration: none !important;
    transition:
        border-color .2s ease,
        background-color .2s ease,
        color .2s ease,
        transform .2s cubic-bezier(.22,1,.36,1) !important;
}

.sari-market-page .sari-register:hover {
    border-color: #c58a26 !important;
    color: #9a6511 !important;
    background: #fbf4e8 !important;
    transform: translateY(-1px);
}

.sari-market-page .sari-mobile-menu-button {
    color: #2a2520 !important;
}

.sari-market-main {
    padding-top: 76px;
}

.sari-market-shell {
    width: min(calc(100% - 40px), 1280px);
    margin-inline: auto;
}


@media (min-width: 901px) {
    .sari-market-page .sari-navbar-nav,
    .sari-market-page .sari-navbar-actions {
        display: flex !important;
    }

    .sari-market-page .sari-mobile-menu-button {
        display: none !important;
    }
}

@media (max-width: 900px) {
    .sari-market-page .sari-navbar-inner {
        width: calc(100% - 24px) !important;
        min-height: 64px !important;
        gap: 12px !important;
    }

    .sari-market-page .sari-navbar-logo {
        max-width: 82px !important;
        height: 24px !important;
    }

    .sari-market-page .sari-navbar-nav,
    .sari-market-page .sari-navbar-actions {
        display: none !important;
    }

    .sari-market-page .sari-mobile-menu-button {
        display: inline-flex !important;
        margin-left: auto !important;
    }
}

/* ==========================================================
   MOVING IMAGE RAIL
   ========================================================== */

.sari-market-rail {
    position: relative;
    overflow: hidden;
    padding: 8px 0;
    border-bottom: 1px solid rgba(38, 33, 28, .06);
    background: #f3f0ea;
}

.sari-market-rail::before,
.sari-market-rail::after {
    content: "";
    position: absolute;
    z-index: 4;
    top: 0;
    bottom: 0;
    width: 34px;
    pointer-events: none;
}

.sari-market-rail::before {
    left: 0;
    background: linear-gradient(90deg, #f3f0ea 12%, rgba(243, 240, 234, 0));
}

.sari-market-rail::after {
    right: 0;
    background: linear-gradient(270deg, #f3f0ea 12%, rgba(243, 240, 234, 0));
}

.sari-market-rail__viewport {
    width: 100%;
    overflow: hidden;
}

.sari-market-rail__viewport + .sari-market-rail__viewport {
    margin-top: 0;
}

.sari-market-rail__track {
    display: flex;
    width: max-content;
    will-change: transform;
    animation: sariMarketRailLeft 34s linear infinite;
}

.sari-market-rail__track--reverse {
    animation-name: sariMarketRailRight;
    animation-duration: 38s;
}

.sari-market-rail:hover .sari-market-rail__track {
    animation-play-state: paused;
}

.sari-market-rail__group {
    display: flex;
    flex: 0 0 auto;
    gap: 0;
    padding-right: 0;
}

.sari-market-rail__image {
    position: relative;
    flex: 0 0 auto;
    overflow: hidden;
    border: 0;
    border-radius: 0;
    background: #111111;
}

.sari-market-rail__image::after {
    content: "";
    position: absolute;
    z-index: 2;
    inset: 0;
    pointer-events: none;
    box-shadow:
        inset 0 0 0 2px rgba(17, 17, 17, .92);
    transition:
        box-shadow .24s ease;
}

.sari-market-rail__image--large {
    width: 112px;
    height: 76px;
}

.sari-market-rail__image--small {
    width: 96px;
    height: 66px;
}

.sari-market-rail__image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition:
        transform .45s var(--sari-market-ease),
        filter .35s ease;
}

@keyframes sariMarketRailLeft {
    from { transform: translate3d(0, 0, 0); }
    to { transform: translate3d(-50%, 0, 0); }
}

@keyframes sariMarketRailRight {
    from { transform: translate3d(-50%, 0, 0); }
    to { transform: translate3d(0, 0, 0); }
}

/* ==========================================================
   HERO
   ========================================================== */

.sari-market-hero {
    background: var(--sari-market-bg);
}

.sari-market-hero__grid {
    display: block;
    max-width: 920px;
    margin-inline: auto;
    padding: 34px 0 30px;
    text-align: center;
}

.sari-market-hero__copy {
    position: relative;
    min-width: 0;
    max-width: 820px;
    margin-inline: auto;
}

.sari-market-micro-label {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 7px;
    color: #8a8178;
    font-size: 8px;
    font-weight: 600;
    letter-spacing: .13em;
    line-height: 1.4;
    text-transform: uppercase;
}

.sari-market-micro-label i {
    width: 24px;
    height: 2px;
    flex: 0 0 24px;
    background: var(--sari-market-gold);
}

.sari-market-micro-label b {
    color: #c3bbb2;
    font-weight: 500;
}

.sari-market-micro-label span:first-of-type {
    color: var(--sari-market-gold);
    font-weight: 700;
}

.sari-market-hero h1 {
    margin: 10px 0 0;
    color: var(--sari-market-ink);
    font-family: 'Poppins', sans-serif;
    font-size: clamp(44px, 4.5vw, 64px);
    font-weight: 700;
    line-height: .99;
    letter-spacing: -.055em;
    text-wrap: balance;
}

.sari-market-hero__lead {
    max-width: 680px;
    margin: 12px auto 0;
    color: var(--sari-market-muted);
    font-size: 10px;
    font-weight: 400;
    line-height: 1.62;
    text-wrap: pretty;
}

.sari-market-stats {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0;
    width: fit-content;
    max-width: 100%;
    margin: 18px auto 0;
    padding: 14px 0 0;
    border-top: 1px solid var(--sari-market-line);
}

.sari-market-stats > div {
    position: relative;
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 0 16px;
}

.sari-market-stats > div:first-child {
    padding-left: 0;
}

.sari-market-stats > div + div::before {
    content: "";
    position: absolute;
    top: 4px;
    bottom: 4px;
    left: 0;
    width: 1px;
    background: var(--sari-market-line);
}

.sari-market-stat-icon {
    display: grid;
    width: 34px;
    height: 34px;
    place-items: center;
    color: var(--sari-market-gold);
}

.sari-market-stat-icon svg {
    width: 17px;
    height: 17px;
    stroke: currentColor;
    stroke-width: 1.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-market-stats dd {
    margin: 0;
}

.sari-market-stats strong,
.sari-market-stats span {
    display: block;
}

.sari-market-stats strong {
    color: #2f2924;
    font-size: 11px;
    font-weight: 700;
}

.sari-market-stats span {
    margin-top: 3px;
    color: #827a72;
    font-size: 8px;
    line-height: 1.35;
}


/* ==========================================================
   CATEGORY NAV
   ========================================================== */

.sari-market-category-nav {
    position: relative;
    z-index: 2;
    padding: 12px 0;
    border-top: 1px solid #ece6df;
    border-bottom: 1px solid #ece6df;
    background: #fffefa;
}

.sari-market-category-nav .sari-market-shell {
    width: min(calc(100% - 32px), 1120px);
}

.sari-market-category-scroll {
    display: flex;
    justify-content: center;
    gap: 8px;
    overflow-x: auto;
    padding: 0;
    scrollbar-width: none;
}

.sari-market-category-scroll::-webkit-scrollbar {
    display: none;
}

.sari-market-category-link {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 9px;
    min-width: 0;
    padding: 8px 11px;
    border: 1px solid #ece5dd;
    border-radius: 10px;
    color: #4c453e;
    background: #ffffff;
    text-decoration: none;
    box-shadow: 0 1px 0 rgba(38, 33, 28, .02);
    transition:
        border-color .22s ease,
        color .22s ease,
        background-color .22s ease,
        box-shadow .22s ease,
        transform .22s var(--sari-market-ease);
}

.sari-market-category-link img {
    display: block;
    width: 28px;
    height: 28px;
    border: 1px solid rgba(38, 33, 28, .08);
    border-radius: 6px;
    object-fit: cover;
}

.sari-market-category-link strong,
.sari-market-category-link small {
    display: block;
    white-space: nowrap;
}

.sari-market-category-link strong {
    color: inherit;
    font-size: 12px;
    line-height: 1.15;
}

.sari-market-category-link small {
    margin-top: 3px;
    color: #918980;
    font-size: 7.2px;
}

.sari-market-category-link.is-active {
    color: #ffffff;
    border-color: #1f1b18;
    background: #211d19;
    box-shadow: 0 6px 16px rgba(33, 29, 25, .12);
}

.sari-market-category-link.is-active strong {
    color: #ffffff;
}

.sari-market-category-link.is-active small {
    color: rgba(255, 255, 255, .62);
}

.sari-market-category-link.is-active img {
    border-color: rgba(255, 255, 255, .16);
}

/* ==========================================================
   PRODUCTS / TOOLBAR
   ========================================================== */

.sari-market-products {
    padding: 18px 0 62px;
}

.sari-market-products > .sari-market-shell {
    width: min(calc(100% - 40px), 1180px);
}

.sari-market-toolbar {
    position: relative;
    z-index: 20;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px;
    align-items: stretch;
    gap: 10px;
    margin: 0 auto;
    padding: 8px;
    border: 1px solid #e8e1d8;
    border-radius: 10px;
    background: #ffffff;
    box-shadow:
        0 10px 30px rgba(44, 37, 31, .045),
        inset 0 1px 0 rgba(255, 255, 255, .8);
}


.sari-market-search {
    position: relative;
    flex: 1 1 auto;
}

.sari-market-search svg {
    position: absolute;
    top: 50%;
    left: 16px;
    width: 18px;
    height: 18px;
    color: #918980;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    transform: translateY(-50%);
}

.sari-market-search input,
.sari-market-sort select {
    width: 100%;
    height: 46px;
    border: 1px solid #eee8e1;
    border-radius: 9px;
    outline: 0;
    color: var(--sari-market-ink);
    background: #fcfbf9;
    font: inherit;
    font-size: 10.5px;
    transition:
        border-color .2s ease,
        box-shadow .2s ease;
}

.sari-market-search input {
    padding: 0 16px 0 48px;
}

.sari-market-search input::placeholder {
    color: #9a9289;
}

.sari-market-search input:focus,
.sari-market-sort select:focus {
    border-color: rgba(197, 138, 38, .58);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(197, 138, 38, .075);
}



.sari-market-sort {
    position: relative;
    min-width: 0;
}

.sari-market-sort__button {
    display: flex;
    width: 100%;
    height: 46px;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border: 1px solid #ded6cc;
    border-radius: 9px;
    padding: 0 11px 0 14px;
    color: #2d2823;
    background: #fbfaf8;
    font: inherit;
    text-align: left;
    cursor: pointer;
    transition:
        border-color .2s ease,
        background-color .2s ease,
        box-shadow .2s ease;
}

.sari-market-sort__button:hover {
    border-color: #cfc4b8;
    background: #ffffff;
}

.sari-market-sort.is-open .sari-market-sort__button {
    border-color: rgba(197, 138, 38, .68);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(197, 138, 38, .08);
}

.sari-market-sort__button-copy {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 3px;
}

.sari-market-sort__button-copy small {
    color: #9b9289;
    font-size: 6px;
    font-weight: 700;
    letter-spacing: .14em;
    line-height: 1;
    text-transform: uppercase;
}

.sari-market-sort__button-copy strong {
    overflow: hidden;
    color: #302a25;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.15;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sari-market-sort__chevron {
    display: grid;
    width: 27px;
    height: 27px;
    flex: 0 0 27px;
    place-items: center;
    border: 1px solid #e7e0d8;
    border-radius: 8px;
    color: #716960;
    background: #ffffff;
    transition:
        transform .2s ease,
        border-color .2s ease,
        color .2s ease;
}

.sari-market-sort__chevron svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-market-sort.is-open .sari-market-sort__chevron {
    color: #b87811;
    border-color: rgba(197, 138, 38, .38);
    transform: rotate(180deg);
}

.sari-market-sort__menu {
    position: absolute;
    z-index: 80;
    top: calc(100% + 7px);
    right: 0;
    left: 0;
    display: grid;
    gap: 3px;
    padding: 6px;
    border: 1px solid #e2dbd2;
    border-radius: 12px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-5px) scale(.985);
    transform-origin: top right;
    background: #ffffff;
    box-shadow:
        0 18px 42px rgba(44, 37, 31, .14),
        0 4px 12px rgba(44, 37, 31, .06);
    pointer-events: none;
    transition:
        opacity .18s ease,
        visibility .18s ease,
        transform .2s cubic-bezier(.22, 1, .36, 1);
}

.sari-market-sort.is-open .sari-market-sort__menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

.sari-market-sort__option {
    display: flex;
    width: 100%;
    min-height: 38px;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border: 0;
    border-radius: 8px;
    padding: 0 10px;
    color: #504840;
    background: transparent;
    font: inherit;
    font-size: 9px;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    transition:
        color .18s ease,
        background-color .18s ease;
}

.sari-market-sort__option:hover,
.sari-market-sort__option:focus-visible {
    color: #241f1b;
    background: #f6f2ec;
    outline: 0;
}

.sari-market-sort__option.is-active {
    color: #a66e13;
    background: #f7eddc;
    font-weight: 650;
}

.sari-market-sort__option svg {
    width: 14px;
    height: 14px;
    flex: 0 0 14px;
    opacity: 0;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-market-sort__option.is-active svg {
    opacity: 1;
}

.sari-market-clear {
    display: inline-flex;
    height: 42px;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd6cd;
    border-radius: 9px;
    padding: 0 14px;
    color: #675f57;
    background: #fff;
    font-size: 10px;
    font-weight: 600;
    text-decoration: none;
}

.sari-market-results-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    margin-top: 22px;
}

.sari-market-results-head > div > span {
    color: var(--sari-market-gold);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
}

.sari-market-results-head h2 {
    margin: 4px 0 0;
    color: var(--sari-market-ink);
    font-family: 'Poppins', sans-serif;
    font-size: clamp(20px, 2vw, 27px);
    font-weight: 700;
    letter-spacing: -.04em;
}

.sari-market-results-head > p {
    margin: 0;
    color: #918980;
    font-size: 9px;
}

.sari-market-product-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-top: 14px;
}

.sari-market-product-card {
    display: flex;
    min-width: 0;
    overflow: hidden;
    flex-direction: column;
    border: 1px solid rgba(38, 33, 28, .09);
    border-radius: 10px;
    background: var(--sari-market-card);
    box-shadow: 0 1px 0 rgba(38, 33, 28, .02);
    transition:
        transform .3s var(--sari-market-ease),
        border-color .25s ease,
        box-shadow .3s ease;
}

.sari-market-product-card__media {
    position: relative;
    overflow: hidden;
    background: #eeebe5;
}

.sari-market-product-card__media > img {
    display: block;
    width: 100%;
    aspect-ratio: 1 / .78;
    object-fit: cover;
    transition: transform .5s var(--sari-market-ease);
}

.sari-market-product-card__badges {
    position: absolute;
    z-index: 2;
    top: 9px;
    left: 9px;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.sari-market-product-card__badges span {
    display: inline-flex;
    min-height: 19px;
    align-items: center;
    border-radius: 5px;
    padding: 0 6px;
    color: #fff;
    background: var(--sari-market-gold);
    font-size: 5.8px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.sari-market-product-card__badges span.is-discount {
    background: #27231f;
}

.sari-market-product-card__view {
    position: absolute;
    z-index: 2;
    top: 9px;
    right: 9px;
    display: inline-flex;
    min-height: 25px;
    align-items: center;
    gap: 5px;
    border: 1px solid rgba(255, 255, 255, .52);
    border-radius: 7px;
    padding: 0 7px;
    color: #29241f;
    background: rgba(255, 255, 255, .91);
    -webkit-backdrop-filter: blur(7px);
    backdrop-filter: blur(7px);
    font-size: 7px;
    font-weight: 650;
    text-decoration: none;
}

.sari-market-product-card__view b {
    color: var(--sari-market-gold);
    font-size: 9px;
}

.sari-market-product-card__body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    padding: 9px 10px 10px;
}

.sari-market-product-card__seller {
    overflow: hidden;
    margin: 0;
    color: #90877f;
    font-size: 6.3px;
    font-weight: 600;
    letter-spacing: .06em;
    text-overflow: ellipsis;
    text-transform: uppercase;
    white-space: nowrap;
}

.sari-market-product-card h3 {
    display: -webkit-box;
    min-height: 29px;
    margin: 3px 0 0;
    overflow: hidden;
    color: #302a25;
    font-size: 10.4px;
    font-weight: 600;
    line-height: 1.38;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.sari-market-product-card__price {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 5px;
    margin-top: 5px;
}

.sari-market-product-card__price strong {
    color: #27221e;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: -.025em;
}

.sari-market-product-card__price span {
    color: #a59d95;
    font-size: 6.8px;
    text-decoration: line-through;
}

.sari-market-product-card__meta-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #f0ebe5;
}

.sari-market-product-card__rating {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 3px;
    color: #8c847c;
    font-size: 6.7px;
}

.sari-market-product-card__rating .star {
    color: var(--sari-market-gold);
    font-size: 9px;
}

.sari-market-product-card__rating strong {
    color: #4a433c;
}

.sari-market-product-card__stock {
    flex: 0 0 auto;
    margin: 0;
    font-size: 6.3px;
    font-weight: 600;
}

.sari-market-product-card__stock.is-in {
    color: var(--sari-market-success);
}

.sari-market-product-card__stock.is-out {
    color: #a06565;
}

.sari-market-buy {
    display: inline-flex;
    width: 100%;
    min-height: 31px;
    align-items: center;
    justify-content: center;
    margin-top: 7px;
    border: 1px solid #2c2722;
    border-radius: 7px;
    color: #fff;
    background: #2c2722;
    font: inherit;
    font-size: 7.6px;
    font-weight: 650;
    letter-spacing: .01em;
    text-decoration: none;
    cursor: pointer;
    transition:
        transform .2s var(--sari-market-ease),
        border-color .2s ease,
        background-color .2s ease;
}

.sari-market-buy.is-disabled {
    border-color: #ded8d1;
    color: #9b938b;
    background: #f1eeea;
    cursor: not-allowed;
}

/* ==========================================================
   PAGINATION / EMPTY
   ========================================================== */

.sari-market-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 34px;
}

.sari-market-pagination a,
.sari-market-pagination > span:not(.sari-market-page-number) {
    display: inline-flex;
    min-height: 36px;
    align-items: center;
    border: 1px solid #ddd6cd;
    border-radius: 8px;
    padding: 0 11px;
    color: #5f5750;
    background: #fff;
    font-size: 8px;
    font-weight: 600;
    text-decoration: none;
}

.sari-market-pagination .is-disabled {
    opacity: .42;
}

.sari-market-page-number {
    color: #928a82;
    font-size: 8px;
}

.sari-market-empty {
    margin-top: 24px;
    border: 1px dashed #d9d2c9;
    border-radius: 12px;
    padding: 54px 22px;
    background: #fff;
    text-align: center;
}

.sari-market-empty h2 {
    margin: 0;
    color: #302a25;
    font-family: 'Poppins', sans-serif;
    font-size: 23px;
    font-weight: 700;
}

.sari-market-empty p {
    margin: 8px 0 0;
    color: #837b73;
    font-size: 10px;
}

.sari-market-empty a {
    display: inline-flex;
    margin-top: 15px;
    color: #a67018;
    font-size: 9px;
    font-weight: 700;
    text-decoration: none;
}

/* ==========================================================
   MOTION
   ========================================================== */

.sari-market-page.sari-market-motion-ready .sari-market-reveal,
.sari-market-page.sari-market-motion-ready .sari-market-product-reveal {
    opacity: 0;
    transform: translateY(14px);
    transition:
        opacity .62s var(--sari-market-ease),
        transform .7s var(--sari-market-ease);
}

.sari-market-page.sari-market-motion-ready .sari-market-reveal.is-visible,
.sari-market-page.sari-market-motion-ready .sari-market-product-reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.sari-market-page.sari-market-motion-ready .sari-market-product-reveal {
    transition-delay: var(--sari-card-delay, 0ms);
}

.sari-market-delay-1 {
    transition-delay: 100ms !important;
}

/* ==========================================================
   DESKTOP INTERACTIONS
   ========================================================== */

@media (hover: hover) and (pointer: fine) {
    .sari-market-rail__image:hover img {
        transform: scale(1.025);
        filter: saturate(.96) contrast(1.02);
    }

    .sari-market-rail__image:hover::after {
        box-shadow:
            inset 0 0 0 3px rgba(8, 8, 8, 1);
    }

    .sari-market-category-link:hover:not(.is-active) {
        transform: translateY(-1px);
        border-color: #d9d0c7;
        background: #fffdf9;
        box-shadow: 0 6px 16px rgba(42, 35, 29, .045);
    }

    .sari-market-product-card:hover {
        transform: translateY(-4px);
        border-color: rgba(197, 138, 38, .28);
        box-shadow: 0 14px 28px rgba(45, 37, 29, .055);
    }

    .sari-market-product-card:hover .sari-market-product-card__media > img {
        transform: scale(1.04);
    }

    .sari-market-buy:not(.is-disabled):hover {
        transform: translateY(-1px);
        border-color: var(--sari-market-gold);
        background: var(--sari-market-gold);
    }
}

/* ==========================================================
   TABLET
   ========================================================== */

@media (max-width: 980px) {
    .sari-market-hero__grid {
        grid-template-columns: minmax(0, 1fr) minmax(300px, .92fr);
        gap: 28px;
    }

    .sari-market-product-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* ==========================================================
   MOBILE
   ========================================================== */

@media (max-width: 720px) {
    .sari-market-main {
        padding-top: 68px;
    }

    .sari-market-shell {
        width: calc(100% - 24px);
    }

    .sari-market-rail {
        padding: 6px 0;
    }

    .sari-market-rail::before,
    .sari-market-rail::after {
        width: 18px;
    }

    .sari-market-rail__viewport + .sari-market-rail__viewport {
        margin-top: 0;
    }

    .sari-market-rail__group {
        gap: 0;
        padding-right: 0;
    }

    .sari-market-rail__image {
        border: 0;
        border-radius: 0;
        background: #111111;
    }

    .sari-market-rail__image::after {
        box-shadow:
            inset 0 0 0 1.5px rgba(17, 17, 17, .95);
    }

    .sari-market-rail__image--large {
        width: 78px;
        height: 54px;
    }

    .sari-market-rail__image--small {
        width: 70px;
        height: 48px;
    }

    .sari-market-rail__track {
        animation-duration: 24s;
    }

    .sari-market-rail__track--reverse {
        animation-duration: 28s;
    }

    .sari-market-hero__grid {
        display: block;
        padding: 22px 0 22px;
        text-align: left;
    }

    .sari-market-micro-label {
        justify-content: flex-start;
    }

    .sari-market-hero__copy {
        max-width: none;
        margin-inline: 0;
    }

    .sari-market-hero__lead {
        margin-inline: 0;
    }

    .sari-market-stats {
        justify-content: flex-start;
        width: 100%;
        margin-inline: 0;
    }

    .sari-market-hero h1 {
        font-size: clamp(34px, 10vw, 44px);
        font-weight: 700;
        letter-spacing: -.052em;
    }

    .sari-market-micro-label {
        gap: 5px;
        font-size: 6.7px;
        letter-spacing: .10em;
    }

    .sari-market-micro-label i {
        width: 18px;
        flex-basis: 18px;
    }

    .sari-market-hero__lead {
        margin-top: 9px;
        font-size: 8px;
        line-height: 1.5;
    }

    .sari-market-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0;
        margin-top: 14px;
        padding-top: 12px;
    }

    .sari-market-stats > div {
        display: block;
        padding: 0 8px;
    }

    .sari-market-stats > div:first-child {
        padding-left: 0;
    }

    .sari-market-stats > div + div::before {
        top: 2px;
        bottom: 2px;
    }

    .sari-market-stat-icon {
        width: 20px;
        height: 20px;
    }

    .sari-market-stats dd {
        margin-top: 5px;
    }

    .sari-market-stats strong {
        font-size: 8px;
    }

    .sari-market-stats span {
        font-size: 6.2px;
        line-height: 1.35;
    }


    .sari-market-category-nav {
        padding: 8px 0;
    }

    .sari-market-category-nav .sari-market-shell {
        width: 100%;
    }

    .sari-market-category-scroll {
        justify-content: flex-start;
        margin-inline: 0;
        padding-inline: 12px;
    }

    .sari-market-category-link {
        padding: 6px 8px;
    }

    .sari-market-category-link img {
        width: 30px;
        height: 30px;
    }

    .sari-market-category-link strong {
        font-size: 9.5px;
    }

    .sari-market-category-link small {
        display: none;
    }

    .sari-market-category-link.is-active {
        color: #ffffff;
        border-color: #1f1b18;
        background: #211d19;
        box-shadow: 0 4px 12px rgba(33, 29, 25, .10);
    }

    .sari-market-category-link.is-active strong {
        color: #ffffff;
    }

    .sari-market-category-link.is-active small {
        color: rgba(255, 255, 255, .62);
    }


    .sari-market-products {
        padding: 18px 0 46px;
    }

    .sari-market-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 116px;
        gap: 6px;
        padding: 6px;
        border-radius: 11px;
    }

    .sari-market-search {
        grid-column: auto;
    }

    .sari-market-sort__caption {
        top: 5px;
        left: 10px;
        font-size: 5.4px;
    }

    .sari-market-search input,
    .sari-market-sort select {
        height: 40px;
        font-size: 9px;
    }


    .sari-market-clear {
        grid-column: 1 / -1;
        width: fit-content;
        height: 30px;
        padding-inline: 9px;
    }

    .sari-market-sort__button {
        height: 40px;
        padding: 0 7px 0 10px;
        border-radius: 9px;
    }

    .sari-market-sort__button-copy {
        gap: 2px;
    }

    .sari-market-sort__button-copy small {
        font-size: 5px;
    }

    .sari-market-sort__button-copy strong {
        font-size: 7.5px;
    }

    .sari-market-sort__chevron {
        width: 23px;
        height: 23px;
        flex-basis: 23px;
        border-radius: 7px;
    }

    .sari-market-sort__chevron svg {
        width: 12px;
        height: 12px;
    }

    .sari-market-sort__menu {
        left: auto;
        width: min(220px, calc(100vw - 24px));
    }

    .sari-market-sort__option {
        min-height: 38px;
        font-size: 8px;
    }

    .sari-market-results-head {
        margin-top: 18px;
    }

    .sari-market-results-head h2 {
        font-size: 18px;
    }

    .sari-market-results-head > p {
        display: none;
    }

    /* EXACTLY 2 products per row on mobile. */
    .sari-market-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
        margin-top: 10px;
    }

    .sari-market-product-card {
        border-radius: 8px;
    }

    .sari-market-product-card__body {
        padding: 7px;
    }

    .sari-market-product-card__seller {
        font-size: 5.4px;
    }

    .sari-market-product-card h3 {
        min-height: 24px;
        margin-top: 2px;
        font-size: 8.8px;
        line-height: 1.32;
    }

    .sari-market-product-card__price {
        gap: 4px;
        margin-top: 4px;
    }

    .sari-market-product-card__price strong {
        font-size: 10.5px;
    }

    .sari-market-product-card__price span {
        font-size: 6px;
    }

    .sari-market-product-card__rating {
        margin-top: 0;
        font-size: 5.7px;
    }

    .sari-market-product-card__rating .star {
        font-size: 10px;
    }

    .sari-market-product-card__stock {
        margin-top: 0;
        font-size: 5.7px;
    }

    .sari-market-buy {
        min-height: 29px;
        margin-top: 6px;
        font-size: 6.8px;
    }



    .sari-market-product-card__meta-line {
        gap: 4px;
        margin-top: 4px;
        padding-top: 4px;
    }

    .sari-market-product-card__view {
        top: 6px;
        right: 6px;
        min-height: 22px;
        padding: 0 6px;
        font-size: 0;
    }

    .sari-market-product-card__view b {
        font-size: 8px;
    }

    .sari-market-product-card__badges {
        top: 6px;
        left: 6px;
        gap: 4px;
    }

    .sari-market-product-card__badges span {
        min-height: 17px;
        padding-inline: 5px;
        font-size: 4.8px;
    }

    .sari-market-product-card__view {
        top: 6px;
        right: 6px;
        width: auto;
        height: 22px;
    }

    .sari-market-pagination {
        gap: 6px;
        margin-top: 25px;
    }

    .sari-market-pagination a,
    .sari-market-pagination > span:not(.sari-market-page-number) {
        min-height: 33px;
        padding-inline: 8px;
        font-size: 7px;
    }

    .sari-market-page-number {
        font-size: 6.8px;
    }
}

@media (max-width: 360px) {
    .sari-market-shell {
        width: calc(100% - 16px);
    }

    .sari-market-hero h1 {
        font-size: 32px;
    }

    .sari-market-product-card__media > img {
        aspect-ratio: 1 / .74;
    }

    .sari-market-product-grid {
        gap: 7px;
    }

    .sari-market-product-card__body {
        padding: 8px;
    }

    .sari-market-product-card h3 {
        font-size: 9px;
    }
}


/* ==========================================================
   ACCESSIBLE FOCUS
   ========================================================== */

.sari-market-category-link:focus-visible,
.sari-market-search input:focus-visible,
.sari-market-sort__button:focus-visible,
.sari-market-sort__option:focus-visible,
.sari-market-sort select:focus-visible,
.sari-market-clear:focus-visible,
.sari-market-product-card__view:focus-visible,
.sari-market-buy:focus-visible,
.sari-market-pagination a:focus-visible {
    outline: 2px solid var(--sari-market-gold);
    outline-offset: 3px;
}

/* ==========================================================
   REDUCED MOTION
   ========================================================== */

@media (prefers-reduced-motion: reduce) {
    .sari-market-rail__track {
        animation: none !important;
        transform: none !important;
    }

    .sari-market-page.sari-market-motion-ready .sari-market-reveal,
    .sari-market-page.sari-market-motion-ready .sari-market-product-reveal,
    .sari-market-rail__image img,
    .sari-market-product-card,
    .sari-market-product-card__media > img,
    .sari-market-buy,
    .sari-market-category-link {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const page = document.querySelector('.sari-market-page');

    if (!page) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    page.classList.add('sari-market-motion-ready');

    const items = page.querySelectorAll(
        '.sari-market-reveal, .sari-market-product-reveal'
    );

    if (!('IntersectionObserver' in window)) {
        items.forEach(function (item) {
            item.classList.add('is-visible');
        });
        return;
    }

    const observer = new IntersectionObserver(function (entries, currentObserver) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            currentObserver.unobserve(entry.target);
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -5% 0px'
    });

    items.forEach(function (item) {
        observer.observe(item);
    });
})();

(function () {
    const sort = document.querySelector('[data-sari-sort]');

    if (!sort) {
        return;
    }

    const toggle = sort.querySelector('[data-sari-sort-toggle]');
    const menu = sort.querySelector('[data-sari-sort-menu]');
    const input = sort.querySelector('[data-sari-sort-input]');
    const label = sort.querySelector('[data-sari-sort-label]');
    const options = Array.from(sort.querySelectorAll('[data-sari-sort-option]'));
    const form = sort.closest('form');

    function closeSort() {
        sort.classList.remove('is-open');
        toggle?.setAttribute('aria-expanded', 'false');
    }

    function openSort() {
        sort.classList.add('is-open');
        toggle?.setAttribute('aria-expanded', 'true');
    }

    toggle?.addEventListener('click', function () {
        if (sort.classList.contains('is-open')) {
            closeSort();
        } else {
            openSort();
        }
    });

    options.forEach(function (option) {
        option.addEventListener('click', function () {
            const value = option.dataset.sortValue || 'featured';
            const optionLabel = option.dataset.sortLabel || 'Featured';

            input.value = value;
            label.textContent = optionLabel;

            options.forEach(function (item) {
                const active = item === option;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            closeSort();
            form?.submit();
        });
    });

    document.addEventListener('click', function (event) {
        if (!sort.contains(event.target)) {
            closeSort();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && sort.classList.contains('is-open')) {
            closeSort();
            toggle?.focus();
        }
    });
})();

</script>
@endpush
