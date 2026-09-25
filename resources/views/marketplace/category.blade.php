@extends('layouts.app')

@section('title', $category['name'] . ' — SARI Marketplace')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Existing SARI public imagery
    |--------------------------------------------------------------------------
    | Uses only assets already available in public/images.
    | Rolling rows are image-only by design.
    */
    $categoryVisuals = [
        'fashion' => [
            'images/cat-fashion.jpg',
            'images/hero.jpg',
            'images/cat-accessories.jpg',
            'images/sari-story.png',
            'images/cat-lifestyle.jpg',
            'images/12-12-mega-sale-banner.png',
        ],
        'electronics' => [
            'images/cat-electronics.jpg',
            'images/weekend-watch.png',
            'images/sari-story.png',
            'images/12-12-mega-sale-banner.png',
            'images/hero.jpg',
            'images/cat-accessories.jpg',
        ],
        'home-living' => [
            'images/cat-home.jpg',
            'images/home-essentials.png',
            'images/sari-hero-bg.webp',
            'images/cat-lifestyle.jpg',
            'images/sari-story.png',
            'images/hero.jpg',
        ],
        'beauty' => [
            'images/cat-beauty.jpg',
            'images/beauty-bestsellers.png',
            'images/reward-voucher.png',
            'images/sari-story.png',
            'images/cat-lifestyle.jpg',
            'images/hero.jpg',
        ],
        'accessories' => [
            'images/cat-accessories.jpg',
            'images/weekend-watch.png',
            'images/cat-fashion.jpg',
            'images/hero.jpg',
            'images/sari-story.png',
            'images/cat-lifestyle.jpg',
        ],
        'food-essentials' => [
            'images/cat-food.jpg',
            'images/12-12-mega-sale-banner.png',
            'images/reward-voucher.png',
            'images/cat-lifestyle.jpg',
            'images/sari-story.png',
            'images/home-essentials.png',
        ],
        'sports' => [
            'images/cat-sports.jpg',
            'images/cat-lifestyle.jpg',
            'images/12-12-mega-sale-banner.png',
            'images/sari-story.png',
            'images/hero.jpg',
            'images/cat-accessories.jpg',
        ],
        'lifestyle' => [
            'images/cat-lifestyle.jpg',
            'images/home-essentials.png',
            'images/cat-accessories.jpg',
            'images/sari-story.png',
            'images/hero.jpg',
            'images/cat-fashion.jpg',
        ],
    ];

    $visualPaths = collect(
        $categoryVisuals[$category['slug']]
        ?? [
            $category['image'],
            'images/sari-story.png',
            'images/hero.jpg',
            'images/sari-hero-bg.webp',
            'images/cat-lifestyle.jpg',
            'images/home-essentials.png',
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Add live category product cover images
    |--------------------------------------------------------------------------
    | Public assets come first. Live covers are appended when available.
    */
    $rollingImages = $visualPaths
        ->map(fn ($path) => asset($path))
        ->values();

    foreach ($products->getCollection() as $rollingProduct) {
        $rollingProductId = (int) ($rollingProduct['id'] ?? 0);
        $rollingRawImage = (string) ($rollingProduct['image'] ?? '');

        if ($rollingRawImage === '') {
            continue;
        }

        if (str_starts_with($rollingRawImage, 'buyer/products/')) {
            $rollingImages->push(
                route('marketplace.product.image', ['product' => $rollingProductId])
            );
        } elseif (str_starts_with($rollingRawImage, 'images/')) {
            $rollingImages->push(asset($rollingRawImage));
        }
    }

    $rollingImages = $rollingImages->filter()->unique()->values();

    while ($rollingImages->count() < 8) {
        $rollingImages = $rollingImages->concat($rollingImages)->values();
    }

    $rollingImages = $rollingImages->take(8)->values();
    $rollingImagesReverse = $rollingImages->reverse()->values();

    $heroImage = $visualPaths->first() ?? $category['image'];
@endphp

<div class="sari-category-page">
    @include('components.landing.navbar')
    @include('components.landing.mobile-menu')

    <main class="sari-category-main">
        <div class="sari-category-shell">

            <nav class="sari-category-breadcrumb sari-category-reveal" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>›</span>
                <a href="{{ url('/#categories') }}">Categories</a>
                <span>›</span>
                <strong>{{ $category['name'] }}</strong>
            </nav>

            {{-- =========================================================
                 TOP MOVING IMAGE RAIL
                 Image-only rectangular cards, 2 rows.
                 ========================================================= --}}
            <section
                class="sari-category-rolling sari-category-reveal"
                aria-label="{{ $category['name'] }} category highlights"
            >
                <div class="sari-category-rolling__header">
                    <span>{{ strtoupper($category['name']) }} · SARI EDIT</span>
                    <small>Curated marketplace imagery</small>
                </div>

                <div class="sari-category-rolling__viewport">
                    <div class="sari-category-rolling__track sari-category-rolling__track--left">
                        @for ($copy = 0; $copy < 2; $copy++)
                            <div class="sari-category-rolling__group">
                                @foreach ($rollingImages as $image)
                                    <div class="sari-category-rolling__image">
                                        <img src="{{ $image }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="sari-category-rolling__viewport">
                    <div class="sari-category-rolling__track sari-category-rolling__track--right">
                        @for ($copy = 0; $copy < 2; $copy++)
                            <div class="sari-category-rolling__group">
                                @foreach ($rollingImagesReverse as $image)
                                    <div class="sari-category-rolling__image sari-category-rolling__image--small">
                                        <img src="{{ $image }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            </section>

            {{-- =========================================================
                 SIMPLE CATEGORY HERO
                 ========================================================= --}}
            <section class="sari-category-hero sari-category-reveal sari-category-delay-1">
                <div class="sari-category-hero__copy">
                    <span class="sari-category-eyebrow">SARI Marketplace</span>

                    <h1>{{ $category['name'] }}</h1>

                    <p>
                        {{ $category['subtitle'] }} for a better everyday life.
                        Browse approved products from active SARI sellers in one
                        clean, connected marketplace.
                    </p>

                    <div class="sari-category-hero__facts">
                        <div>
                            <span class="sari-category-fact-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M5 6h14v12H5z"></path>
                                    <path d="M8 10h8M8 14h5"></path>
                                </svg>
                            </span>

                            <p>
                                <strong>{{ number_format($products->total()) }}</strong>
                                <small>Products available</small>
                            </p>
                        </div>

                        <div>
                            <span class="sari-category-fact-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            </span>

                            <p>
                                <strong>Approved</strong>
                                <small>Listings only</small>
                            </p>
                        </div>

                        <div>
                            <span class="sari-category-fact-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M3 7h11v10H3z"></path>
                                    <path d="M14 10h4l3 3v4h-7z"></path>
                                    <circle cx="7" cy="18" r="1.6"></circle>
                                    <circle cx="18" cy="18" r="1.6"></circle>
                                </svg>
                            </span>

                            <p>
                                <strong>Connected</strong>
                                <small>Buyer experience</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="sari-category-hero__media">
                    <img src="{{ asset($heroImage) }}" alt="{{ $category['name'] }} category">
                    <span>{{ strtoupper($category['name']) }}</span>
                </div>
            </section>

            {{-- CATEGORY SWITCHER --}}
            <nav class="sari-category-switcher sari-category-reveal sari-category-delay-1" aria-label="Marketplace categories">
                @foreach ($categories as $item)
                    @php
                        $switcherImage = collect($categoryVisuals[$item['slug']] ?? [])
                            ->first() ?? $item['image'];
                    @endphp

                    <a
                        href="{{ route('marketplace.category', ['category' => $item['slug']]) }}"
                        class="{{ $item['slug'] === $category['slug'] ? 'is-active' : '' }}"
                    >
                        <span class="sari-category-switcher__thumb">
                            <img src="{{ asset($switcherImage) }}" alt="">
                        </span>

                        <span>
                            <strong>{{ $item['name'] }}</strong>
                            <small>
                                {{ $item['slug'] === $category['slug']
                                    ? number_format($products->total()) . ' products'
                                    : $item['subtitle'] }}
                            </small>
                        </span>
                    </a>
                @endforeach
            </nav>

            {{-- SEARCH + SORT --}}
            <form
                method="GET"
                action="{{ route('marketplace.category', ['category' => $category['slug']]) }}"
                class="sari-category-toolbar sari-category-reveal sari-category-delay-2"
            >
                <label class="sari-category-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <circle cx="11" cy="11" r="6.5"></circle>
                        <path d="M16 16 21 21"></path>
                    </svg>

                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Search {{ strtolower($category['name']) }} products..."
                        aria-label="Search category products"
                    >
                </label>

                <label class="sari-category-sort">
                    <span>Sort by</span>

                    <select name="sort" onchange="this.form.submit()">
                        <option value="featured" @selected($sort === 'featured')>Featured</option>
                        <option value="newest" @selected($sort === 'newest')>Newest</option>
                        <option value="price_low" @selected($sort === 'price_low')>Price: Low to High</option>
                        <option value="price_high" @selected($sort === 'price_high')>Price: High to Low</option>
                    </select>
                </label>

                <button type="submit" class="sari-category-search-btn">
                    Search <span>→</span>
                </button>

                @if ($searchQuery !== '')
                    <a
                        href="{{ route('marketplace.category', ['category' => $category['slug'], 'sort' => $sort]) }}"
                        class="sari-category-clear"
                    >
                        Clear
                    </a>
                @endif
            </form>

            <div class="sari-category-results-head sari-category-reveal">
                <div>
                    <span>CURATED RESULTS</span>
                    <h2>{{ number_format($products->total()) }} products found</h2>
                </div>

                <p>Approved {{ strtolower($category['name']) }} listings</p>
            </div>

            {{-- PRODUCT GRID --}}
            @if ($products->count())
                <section class="sari-category-product-grid">
                    @foreach ($products as $index => $product)
                        @php
                            $productId = (int) ($product['id'] ?? 0);
                            $rawImage = (string) ($product['image'] ?? '');
                            $hasProductImage = str_starts_with($rawImage, 'buyer/products/');
                            $isPublicAssetImage = str_starts_with($rawImage, 'images/');

                            $productImage = $hasProductImage
                                ? route('marketplace.product.image', ['product' => $productId])
                                : asset($isPublicAssetImage ? $rawImage : $heroImage);

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
                            class="sari-product-card sari-product-reveal"
                            style="--delay: {{ min($index, 9) * 45 }}ms;"
                        >
                            <div class="sari-product-card__media">
                                <img
                                    src="{{ $productImage }}"
                                    alt="{{ $product['name'] }}"
                                    loading="lazy"
                                >

                                <div class="sari-product-card__badges">
                                    @if (($product['discount_percent'] ?? 0) > 0)
                                        <span class="is-sale">
                                            -{{ rtrim(rtrim(number_format((float) $product['discount_percent'], 1), '0'), '.') }}%
                                        </span>
                                    @endif

                                    @if (!empty($product['free_shipping']))
                                        <span>Free shipping</span>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('buyer.product.details', ['product' => $productId]) }}"
                                    class="sari-product-card__open"
                                    aria-label="View {{ $product['name'] }}"
                                >
                                    ↗
                                </a>
                            </div>

                            <div class="sari-product-card__body">
                                <p class="sari-product-card__seller">
                                    {{ $product['store_name'] ?? 'SARI Seller' }}
                                </p>

                                <h3>{{ $product['name'] }}</h3>

                                <div class="sari-product-card__price">
                                    <strong>₱{{ number_format($price, 2) }}</strong>

                                    @if ($oldPrice !== null && $oldPrice > $price)
                                        <span>₱{{ number_format($oldPrice, 2) }}</span>
                                    @endif
                                </div>

                                <div class="sari-product-card__meta">
                                    <span>
                                        @if ($ratingCount > 0)
                                            <b>★ {{ number_format($rating, 1) }}</b>
                                            · {{ number_format($ratingCount) }}
                                        @else
                                            New listing
                                        @endif
                                    </span>

                                    <span class="{{ $stock > 0 ? 'is-stock' : 'is-out' }}">
                                        {{ $stock > 0 ? number_format($stock) . ' left' : 'Out of stock' }}
                                    </span>
                                </div>

                                @if ($stock > 0)
                                    @if ($isBuyer)
                                        <a href="{{ $buyerBuyNowUrl }}" class="sari-product-card__buy">
                                            Buy Now <span>→</span>
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            class="sari-product-card__buy"
                                            data-market-auth-trigger
                                            data-redirect-to="{{ $buyerBuyNowUrl }}"
                                        >
                                            Buy Now <span>→</span>
                                        </button>
                                    @endif
                                @else
                                    <button
                                        type="button"
                                        class="sari-product-card__buy is-disabled"
                                        disabled
                                    >
                                        Out of Stock
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </section>

                @if ($products->hasPages())
                    <nav class="sari-category-pagination" aria-label="Product pages">
                        @if ($products->onFirstPage())
                            <span class="is-disabled">← Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}">← Previous</a>
                        @endif

                        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}">Next →</a>
                        @else
                            <span class="is-disabled">Next →</span>
                        @endif
                    </nav>
                @endif
            @else
                <section class="sari-category-empty">
                    <h2>No products found.</h2>

                    <p>
                        @if ($searchQuery !== '')
                            Try another search term or clear your current search.
                        @else
                            Approved products will appear here when sellers publish them.
                        @endif
                    </p>

                    <a href="{{ route('marketplace.category', ['category' => $category['slug']]) }}">
                        Reset results
                    </a>
                </section>
            @endif
        </div>
    </main>

    @include('components.landing.footer')
    @include('components.marketplace.login-modal')
</div>
@endsection

@push('styles')
<style>
/* ==========================================================
   SARI CATEGORY PAGE — CLEAN TOP ROLLING VERSION
   ========================================================== */

.sari-category-page,
.sari-category-page * {
    box-sizing: border-box;
}

.sari-category-page {
    --cat-ink:#191613;
    --cat-muted:#756e67;
    --cat-line:#e8e1d8;
    --cat-soft:#f7f4ef;
    --cat-gold:#c88a19;
    --cat-gold-dark:#a96f0f;
    --cat-ease:cubic-bezier(.22,1,.36,1);

    min-height:100vh;
    color:var(--cat-ink);
    background:#fff;
    font-family:'Poppins',sans-serif;
}

/* Light page navbar */
.sari-category-page .sari-navbar {
    background:rgba(255,255,255,.96) !important;
    border-bottom-color:rgba(30,26,22,.08) !important;
    box-shadow:0 8px 28px rgba(30,26,22,.04) !important;
    -webkit-backdrop-filter:blur(18px);
    backdrop-filter:blur(18px);
}

.sari-category-page .sari-navbar .sari-nav-link,
.sari-category-page .sari-navbar .sari-login {
    color:#28231f !important;
}

.sari-category-main {
    padding:110px 0 92px;
}

.sari-category-shell {
    width:min(calc(100% - 40px),1460px);
    margin-inline:auto;
}

.sari-category-breadcrumb {
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:9px;
    color:#958c83;
    font-size:9px;
}

.sari-category-breadcrumb a {
    color:inherit;
    text-decoration:none;
}

.sari-category-breadcrumb strong {
    color:#4b443d;
}

/* ==========================================================
   TOP ROLLING IMAGES
   ========================================================== */

.sari-category-rolling {
    margin-top:18px;
    overflow:hidden;
    border:1px solid var(--cat-line);
    border-radius:18px;
    padding:12px 0;
    background:#faf8f4;
}

.sari-category-rolling__header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:0 14px 10px;
}

.sari-category-rolling__header span {
    color:#9f6810;
    font-size:7.5px;
    font-weight:800;
    letter-spacing:.16em;
}

.sari-category-rolling__header small {
    color:#948b82;
    font-size:7.5px;
}

.sari-category-rolling__viewport {
    overflow:hidden;
}

.sari-category-rolling__viewport + .sari-category-rolling__viewport {
    margin-top:9px;
}

.sari-category-rolling__track {
    display:flex;
    width:max-content;
    will-change:transform;
}

.sari-category-rolling__track--left {
    animation:sariRollLeft 28s linear infinite;
}

.sari-category-rolling__track--right {
    animation:sariRollRight 32s linear infinite;
}

.sari-category-rolling:hover .sari-category-rolling__track {
    animation-play-state:paused;
}

.sari-category-rolling__group {
    display:flex;
    flex:0 0 auto;
    gap:9px;
    padding-right:9px;
}

.sari-category-rolling__image {
    flex:0 0 210px;
    width:210px;
    height:118px;
    overflow:hidden;
    border:1px solid #ded6cc;
    border-radius:13px;
    background:#ebe6df;
}

.sari-category-rolling__image--small {
    flex-basis:174px;
    width:174px;
    height:88px;
}

.sari-category-rolling__image img {
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform .55s var(--cat-ease);
}

@keyframes sariRollLeft {
    from { transform:translate3d(0,0,0); }
    to { transform:translate3d(-50%,0,0); }
}

@keyframes sariRollRight {
    from { transform:translate3d(-50%,0,0); }
    to { transform:translate3d(0,0,0); }
}

/* ==========================================================
   CATEGORY HERO
   ========================================================== */

.sari-category-hero {
    display:grid;
    grid-template-columns:minmax(380px,.78fr) minmax(0,1.22fr);
    min-height:410px;
    margin-top:18px;
    overflow:hidden;
    border:1px solid var(--cat-line);
    border-radius:22px;
    background:#f8f5f0;
}

.sari-category-hero__copy {
    display:flex;
    min-width:0;
    flex-direction:column;
    justify-content:center;
    padding:clamp(38px,4.5vw,68px);
}

.sari-category-eyebrow {
    color:#9f6810;
    font-size:9px;
    font-weight:800;
    letter-spacing:.18em;
    text-transform:uppercase;
}

.sari-category-hero h1 {
    margin:11px 0 0;
    color:#171411;
    font-size:clamp(58px,5.8vw,84px);
    font-weight:700;
    line-height:.94;
    letter-spacing:-.064em;
}

.sari-category-hero__copy > p {
    max-width:520px;
    margin:18px 0 0;
    color:#625b54;
    font-size:14px;
    line-height:1.72;
}

.sari-category-hero__facts {
    display:flex;
    flex-wrap:wrap;
    gap:18px 30px;
    margin-top:30px;
}

.sari-category-hero__facts > div {
    display:grid;
    grid-template-columns:38px minmax(0,1fr);
    gap:10px;
    align-items:center;
}

.sari-category-fact-icon {
    display:grid;
    width:38px;
    height:38px;
    place-items:center;
    color:#b37a16;
}

.sari-category-fact-icon svg {
    width:23px;
    height:23px;
    stroke:currentColor;
    stroke-width:1.5;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.sari-category-hero__facts p {
    margin:0;
}

.sari-category-hero__facts strong,
.sari-category-hero__facts small {
    display:block;
}

.sari-category-hero__facts strong {
    color:#302a24;
    font-size:11px;
}

.sari-category-hero__facts small {
    margin-top:2px;
    color:#8c847c;
    font-size:7.8px;
}

.sari-category-hero__media {
    position:relative;
    min-width:0;
    overflow:hidden;
    background:#e7e1d9;
}

.sari-category-hero__media img {
    display:block;
    width:100%;
    height:100%;
    min-height:410px;
    object-fit:cover;
    transition:transform .75s var(--cat-ease);
}

.sari-category-hero__media > span {
    position:absolute;
    right:18px;
    bottom:16px;
    border:1px solid rgba(255,255,255,.52);
    border-radius:999px;
    padding:7px 11px;
    color:#fff;
    background:rgba(24,21,18,.48);
    -webkit-backdrop-filter:blur(8px);
    backdrop-filter:blur(8px);
    font-size:7px;
    font-weight:800;
    letter-spacing:.14em;
}

/* ==========================================================
   CATEGORY SWITCHER
   ========================================================== */

.sari-category-switcher {
    display:grid;
    grid-template-columns:repeat(8,minmax(0,1fr));
    margin-top:18px;
    overflow:hidden;
    border:1px solid var(--cat-line);
    border-radius:15px;
    background:#fff;
}

.sari-category-switcher a {
    display:grid;
    grid-template-columns:38px minmax(0,1fr);
    gap:9px;
    align-items:center;
    min-width:0;
    padding:10px;
    color:#423b34;
    border-right:1px solid #eee8e1;
    text-decoration:none;
    transition:background-color .2s ease,color .2s ease;
}

.sari-category-switcher a:last-child {
    border-right:0;
}

.sari-category-switcher a.is-active {
    color:#fff;
    background:#1e1b18;
}

.sari-category-switcher__thumb {
    display:block;
    width:38px;
    height:38px;
    overflow:hidden;
    border-radius:9px;
    background:#eee8df;
}

.sari-category-switcher__thumb img {
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
}

.sari-category-switcher strong,
.sari-category-switcher small {
    display:block;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.sari-category-switcher strong {
    font-size:8px;
}

.sari-category-switcher small {
    margin-top:2px;
    color:#968d84;
    font-size:6px;
}

.sari-category-switcher a.is-active small {
    color:rgba(255,255,255,.58);
}

/* ==========================================================
   TOOLBAR
   ========================================================== */

.sari-category-toolbar {
    display:grid;
    grid-template-columns:minmax(0,1fr) 210px auto auto;
    gap:9px;
    margin-top:14px;
    padding:10px;
    border:1px solid var(--cat-line);
    border-radius:14px;
    background:#fff;
}

.sari-category-search {
    position:relative;
}

.sari-category-search svg {
    position:absolute;
    top:50%;
    left:14px;
    width:16px;
    height:16px;
    color:#8e867e;
    transform:translateY(-50%);
}

.sari-category-search input,
.sari-category-sort select {
    width:100%;
    height:44px;
    border:1px solid #ddd5cc;
    border-radius:9px;
    outline:0;
    color:#332d27;
    background:#fff;
    font:inherit;
    font-size:10px;
}

.sari-category-search input {
    padding:0 12px 0 40px;
}

.sari-category-sort {
    display:grid;
    grid-template-columns:auto 1fr;
    gap:7px;
    align-items:center;
}

.sari-category-sort span {
    color:#81786f;
    font-size:7.5px;
}

.sari-category-sort select {
    padding-inline:9px;
}

.sari-category-search-btn,
.sari-category-clear {
    display:inline-flex;
    height:44px;
    align-items:center;
    justify-content:center;
    gap:7px;
    border-radius:9px;
    padding:0 15px;
    font:inherit;
    font-size:8.5px;
    font-weight:750;
    text-decoration:none;
}

.sari-category-search-btn {
    border:1px solid #1d1a17;
    color:#fff;
    background:#1d1a17;
    cursor:pointer;
}

.sari-category-clear {
    border:1px solid #ded6cc;
    color:#645b53;
    background:#fff;
}

/* ==========================================================
   RESULTS
   ========================================================== */

.sari-category-results-head {
    display:flex;
    align-items:end;
    justify-content:space-between;
    gap:20px;
    margin-top:30px;
    padding-bottom:12px;
}

.sari-category-results-head > div > span {
    color:#a76f12;
    font-size:7px;
    font-weight:800;
    letter-spacing:.15em;
}

.sari-category-results-head h2 {
    margin:4px 0 0;
    color:#26211c;
    font-size:22px;
    letter-spacing:-.035em;
}

.sari-category-results-head > p {
    margin:0;
    color:#928a82;
    font-size:7.5px;
}

.sari-category-product-grid {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:15px;
    border-top:1px solid #eee8e1;
    padding-top:16px;
}

.sari-product-card {
    min-width:0;
    overflow:hidden;
    border:1px solid #ece5dd;
    border-radius:14px;
    background:#fff;
    transition:
        transform .28s var(--cat-ease),
        box-shadow .28s ease,
        border-color .24s ease;
}

.sari-product-card__media {
    position:relative;
    aspect-ratio:1/1.03;
    overflow:hidden;
    background:#f1ede7;
}

.sari-product-card__media > img {
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform .5s var(--cat-ease);
}

.sari-product-card__badges {
    position:absolute;
    z-index:2;
    top:8px;
    left:8px;
    display:flex;
    flex-wrap:wrap;
    gap:5px;
}

.sari-product-card__badges span {
    display:inline-flex;
    min-height:21px;
    align-items:center;
    border-radius:999px;
    padding:0 7px;
    color:#fff;
    background:rgba(27,24,20,.66);
    -webkit-backdrop-filter:blur(7px);
    backdrop-filter:blur(7px);
    font-size:5.8px;
    font-weight:700;
}

.sari-product-card__badges .is-sale {
    background:#c98a18;
}

.sari-product-card__open {
    position:absolute;
    z-index:2;
    top:8px;
    right:8px;
    display:grid;
    width:29px;
    height:29px;
    place-items:center;
    border-radius:999px;
    color:#28231e;
    background:rgba(255,255,255,.88);
    font-size:9px;
    text-decoration:none;
}

.sari-product-card__body {
    padding:12px;
}

.sari-product-card__seller {
    overflow:hidden;
    margin:0;
    color:#928a81;
    font-size:6.8px;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.sari-product-card__body h3 {
    display:-webkit-box;
    min-height:31px;
    margin:4px 0 0;
    overflow:hidden;
    color:#28231e;
    font-size:10.5px;
    line-height:1.42;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:2;
}

.sari-product-card__price {
    display:flex;
    flex-wrap:wrap;
    align-items:baseline;
    gap:6px;
    margin-top:8px;
}

.sari-product-card__price strong {
    color:#1f1b17;
    font-size:14px;
}

.sari-product-card__price span {
    color:#aaa29a;
    font-size:6.8px;
    text-decoration:line-through;
}

.sari-product-card__meta {
    display:flex;
    justify-content:space-between;
    gap:7px;
    margin-top:8px;
    padding-top:8px;
    border-top:1px solid #f0ebe5;
    color:#8d857d;
    font-size:6.3px;
}

.sari-product-card__meta b {
    color:#c78918;
}

.sari-product-card__meta .is-stock {
    color:#568066;
}

.sari-product-card__meta .is-out {
    color:#a45a5a;
}

.sari-product-card__buy {
    display:flex;
    width:100%;
    min-height:36px;
    align-items:center;
    justify-content:center;
    gap:7px;
    margin-top:9px;
    border:1px solid #1e1b18;
    border-radius:8px;
    color:#fff;
    background:#1e1b18;
    font:inherit;
    font-size:7.8px;
    font-weight:750;
    text-decoration:none;
    cursor:pointer;
}

.sari-product-card__buy.is-disabled {
    border-color:#ded7ce;
    color:#9a928a;
    background:#f4f1ed;
    cursor:not-allowed;
}

/* Pagination / empty */
.sari-category-pagination {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-top:28px;
}

.sari-category-pagination a,
.sari-category-pagination span {
    font-size:7.5px;
}

.sari-category-pagination a,
.sari-category-pagination .is-disabled {
    display:inline-flex;
    min-height:34px;
    align-items:center;
    border:1px solid #e1d9d0;
    border-radius:8px;
    padding:0 10px;
    color:#5f574f;
    background:#fff;
    text-decoration:none;
}

.sari-category-pagination .is-disabled {
    opacity:.45;
}

.sari-category-empty {
    border:1px dashed #ded6cd;
    border-radius:15px;
    padding:52px 22px;
    text-align:center;
}

.sari-category-empty h2 {
    margin:0;
    font-size:21px;
}

.sari-category-empty p {
    margin:7px 0 0;
    color:#817971;
    font-size:9px;
}

.sari-category-empty a {
    display:inline-flex;
    margin-top:16px;
    color:#aa7112;
    font-size:8px;
    font-weight:700;
    text-decoration:none;
}

/* Reveal */
.sari-category-page.is-motion-ready .sari-category-reveal,
.sari-category-page.is-motion-ready .sari-product-reveal {
    opacity:0;
    transform:translateY(14px);
    transition:
        opacity .64s var(--cat-ease),
        transform .72s var(--cat-ease);
}

.sari-category-page.is-motion-ready .sari-category-reveal.is-visible,
.sari-category-page.is-motion-ready .sari-product-reveal.is-visible {
    opacity:1;
    transform:translateY(0);
}

.sari-category-page.is-motion-ready .sari-product-reveal {
    transition-delay:var(--delay,0ms);
}

.sari-category-delay-1 {
    transition-delay:90ms !important;
}

.sari-category-delay-2 {
    transition-delay:150ms !important;
}

/* Hover */
@media (hover:hover) and (pointer:fine) {
    .sari-category-rolling__image:hover img {
        transform:scale(1.04);
    }

    .sari-category-hero__media:hover img {
        transform:scale(1.025);
    }

    .sari-category-switcher a:hover:not(.is-active) {
        background:#f8f5f0;
    }

    .sari-product-card:hover {
        transform:translateY(-4px);
        border-color:#d9cbb9;
        box-shadow:0 16px 32px rgba(38,31,23,.07);
    }

    .sari-product-card:hover .sari-product-card__media > img {
        transform:scale(1.04);
    }

    .sari-product-card__buy:not(.is-disabled):hover {
        border-color:#c88a19;
        background:#c88a19;
    }
}

/* Tablet */
@media (max-width:1050px) {
    .sari-category-hero {
        grid-template-columns:minmax(320px,.88fr) minmax(0,1.12fr);
    }

    .sari-category-switcher {
        grid-template-columns:repeat(4,minmax(0,1fr));
    }

    .sari-category-switcher a:nth-child(4) {
        border-right:0;
    }

    .sari-category-switcher a:nth-child(-n+4) {
        border-bottom:1px solid #eee8e1;
    }

    .sari-category-product-grid {
        grid-template-columns:repeat(3,minmax(0,1fr));
    }
}

/* Mobile */
@media (max-width:720px) {
    .sari-category-main {
        padding:88px 0 62px;
    }

    .sari-category-shell {
        width:calc(100% - 20px);
    }

    .sari-category-rolling {
        margin-top:14px;
        border-radius:14px;
        padding-block:10px;
    }

    .sari-category-rolling__header {
        padding:0 10px 8px;
    }

    .sari-category-rolling__header small {
        display:none;
    }

    .sari-category-rolling__image {
        flex-basis:138px;
        width:138px;
        height:82px;
        border-radius:10px;
    }

    .sari-category-rolling__image--small {
        flex-basis:116px;
        width:116px;
        height:64px;
    }

    .sari-category-rolling__track--left {
        animation-duration:21s;
    }

    .sari-category-rolling__track--right {
        animation-duration:24s;
    }

    .sari-category-hero {
        grid-template-columns:1fr;
        min-height:0;
        margin-top:14px;
        border-radius:18px;
    }

    .sari-category-hero__copy {
        padding:25px 17px 21px;
    }

    .sari-category-hero h1 {
        font-size:clamp(42px,12vw,55px);
    }

    .sari-category-hero__copy > p {
        margin-top:13px;
        font-size:10.8px;
        line-height:1.6;
    }

    .sari-category-hero__facts {
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:8px;
        margin-top:20px;
    }

    .sari-category-hero__facts > div {
        display:block;
    }

    .sari-category-fact-icon {
        width:28px;
        height:28px;
    }

    .sari-category-fact-icon svg {
        width:18px;
        height:18px;
    }

    .sari-category-hero__facts p {
        margin-top:4px;
    }

    .sari-category-hero__facts strong {
        font-size:8px;
    }

    .sari-category-hero__facts small {
        font-size:5.9px;
        line-height:1.35;
    }

    .sari-category-hero__media img {
        min-height:250px;
    }

    .sari-category-switcher {
        display:flex;
        overflow-x:auto;
        border-radius:13px;
        scrollbar-width:none;
    }

    .sari-category-switcher::-webkit-scrollbar {
        display:none;
    }

    .sari-category-switcher a,
    .sari-category-switcher a:nth-child(n) {
        flex:0 0 132px;
        border-right:1px solid #eee8e1;
        border-bottom:0;
    }

    .sari-category-toolbar {
        grid-template-columns:minmax(0,1fr) 112px;
        gap:7px;
        padding:8px;
    }

    .sari-category-search {
        grid-column:1/-1;
    }

    .sari-category-sort {
        display:block;
    }

    .sari-category-sort span {
        display:none;
    }

    .sari-category-search input,
    .sari-category-sort select,
    .sari-category-search-btn {
        height:39px;
    }

    .sari-category-clear {
        grid-column:1/-1;
        width:fit-content;
        height:32px;
    }

    .sari-category-results-head {
        margin-top:23px;
    }

    .sari-category-results-head h2 {
        font-size:18px;
    }

    .sari-category-product-grid {
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:8px;
        padding-top:11px;
    }

    .sari-product-card {
        border-radius:12px;
    }

    .sari-product-card__body {
        padding:9px;
    }

    .sari-product-card__body h3 {
        font-size:9.5px;
    }

    .sari-product-card__price strong {
        font-size:12px;
    }

    .sari-product-card__meta {
        display:grid;
        gap:3px;
        font-size:5.7px;
    }

    .sari-product-card__buy {
        min-height:34px;
        font-size:7px;
    }
}

@media (max-width:370px) {
    .sari-category-shell {
        width:calc(100% - 16px);
    }

    .sari-category-product-grid {
        gap:6px;
    }

    .sari-product-card__body {
        padding:8px;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion:reduce) {
    .sari-category-rolling__track {
        animation:none !important;
        transform:none !important;
    }

    .sari-category-page.is-motion-ready .sari-category-reveal,
    .sari-category-page.is-motion-ready .sari-product-reveal,
    .sari-category-rolling__image img,
    .sari-category-hero__media img,
    .sari-product-card,
    .sari-product-card__media > img {
        opacity:1 !important;
        transform:none !important;
        transition:none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const page = document.querySelector('.sari-category-page');

    if (!page) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    page.classList.add('is-motion-ready');

    const items = page.querySelectorAll(
        '.sari-category-reveal, .sari-product-reveal'
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
        threshold:.10,
        rootMargin:'0px 0px -5% 0px'
    });

    items.forEach(function (item) {
        observer.observe(item);
    });
})();
</script>
@endpush
