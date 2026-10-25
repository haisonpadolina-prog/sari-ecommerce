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

@endpush



@section('content')



@php

    /*

    |--------------------------------------------------------------------------

    | HOME PAGE DATA

    |--------------------------------------------------------------------------

    | Existing controller data is preserved.

    */

    $categoryCollection = collect([
        ['name' => 'Fashion', 'slug' => 'fashion', 'image' => 'images/cat-fashion.jpg'],
        ['name' => 'Electronics', 'slug' => 'electronics', 'image' => 'images/cat-electronics.jpg'],
        ['name' => 'Home & Living', 'slug' => 'home-living', 'image' => 'images/cat-home.jpg'],
        ['name' => 'Beauty', 'slug' => 'beauty', 'image' => 'images/cat-beauty.jpg'],
        ['name' => 'Accessories', 'slug' => 'accessories', 'image' => 'images/cat-accessories.jpg'],
        ['name' => 'Food & Essentials', 'slug' => 'food-essentials', 'image' => 'images/cat-food.jpg'],
        ['name' => 'Sports', 'slug' => 'sports', 'image' => 'images/cat-sports.jpg'],
        ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'image' => 'images/cat-lifestyle.jpg'],
    ]);

    $featuredCollection = collect($featuredProducts ?? []);



    $bestSellerProducts = $featuredCollection

        ->sortByDesc(fn ($product) => (int) ($product['sold'] ?? 0))

        ->take(4)

        ->values();

@endphp






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



    /* SARI_COMPACT_HOME_RECOMMENDATIONS_APPLIED_V1 */

    .sari-home-shell {

        width: 100%;

        max-width: 1500px;

        margin: 0 auto;

    }



    .sari-section-title {

        letter-spacing: -.035em;

        line-height: 1.05;

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

        min-height: 300px;

        overflow: hidden;

        border: 1px solid #eadfce;

        border-radius: 16px;

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





    .sari-hero-content {

        min-height: 300px !important;

        padding: 24px 32px !important;

    }



    .sari-hero-badge {

        display: inline-flex;

        align-items: center;

        gap: 7px;

        min-height: 24px;

        border: 1px solid #e4c98f;

        border-radius: 999px;

        background: rgba(255,255,255,.92);

        padding: 0 10px;

        color: #996514;

        font-size: 6.5px;

        font-weight: 700;

        letter-spacing: .08em;

        text-transform: uppercase;

    }



    .sari-hero-title {

        max-width: 580px;

        margin-top: 12px;

        font-size: clamp(30px, 3.2vw, 46px);

        font-weight: 700;

        line-height: 1.05;

        letter-spacing: -.055em;

        color: #171411;

    }



    .sari-hero-title span {

        display: block;

        color: #b8790f;

    }



    .sari-hero-copy {

        max-width: 470px;

        margin-top: 10px;

        color: #6f675f;

        font-size: 9px;

        line-height: 1.6;

    }



    .sari-primary-btn,

    .sari-secondary-btn {

        display: inline-flex;

        height: 38px;

        align-items: center;

        justify-content: center;

        gap: 8px;

        border-radius: 9px;

        padding: 0 16px;

        font-size: 8px;

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

        gap: 12px;

        margin-top: 14px;

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

    | BENEFITS — FLOATING BUYER VALUE STRIP

    |--------------------------------------------------------------------------

    | Clean cool-white treatment inspired by the Seller dashboard cards.

    | SARI gold is used only as an accent; no cream panel backgrounds.

    */

    .sari-benefits-section {

        position: relative;

        z-index: 2;

        margin-top: 12px;

    }



    .sari-benefit-strip {

        display: grid;

        grid-template-columns: repeat(4, minmax(0, 1fr));

        overflow: hidden;

        border: 1px solid #e2e8f0;

        border-radius: 14px;

        background: #ffffff;

        box-shadow:

            0 18px 46px rgba(15, 23, 42, .075),

            0 3px 10px rgba(15, 23, 42, .035);

        isolation: isolate;

    }



    .sari-benefit-item {

        display: flex;

        min-width: 0;

        min-height: 66px;

        align-items: center;

        gap: 10px;

        padding: 11px 16px;

        background: #ffffff;

    }



    a.sari-benefit-item {

        transition:

            background-color .16s ease,

            color .16s ease;

    }



    a.sari-benefit-item:hover,

    a.sari-benefit-item:focus-visible {

        background: #f8fafc;

    }



    a.sari-benefit-item:focus-visible {

        position: relative;

        z-index: 1;

        outline: 2px solid rgba(201, 137, 11, .28);

        outline-offset: -2px;

    }



    .sari-benefit-item + .sari-benefit-item {

        border-left: 1px solid #e5e7eb;

    }



    .sari-benefit-icon {

        display: grid;

        width: 36px;

        height: 36px;

        flex: 0 0 auto;

        place-items: center;

        border: 1px solid #e8edf3;

        border-radius: 10px;

        background: #f4f6f8;

        color: #b77905;

    }



    .sari-benefit-item h3 {

        color: #111827;

        font-size: 8.5px;

        font-weight: 700;

        letter-spacing: -.01em;

    }



    .sari-benefit-item p {

        margin-top: 3px;

        color: #7b8493;

        font-size: 6.5px;

        line-height: 1.45;

    }



    /*

    |--------------------------------------------------------------------------

    | PROMOTION SHOWCASE

    |--------------------------------------------------------------------------

    | Marketplace-style promotional rail using existing public assets only.

    */

    /* Compact marketplace promo rail: controlled height, no image-driven grid stretching. */

    .sari-promo-showcase {

         display: grid;
    grid-template-columns: minmax(0, 2.1fr) minmax(220px, .9fr);

    height: clamp(340px, 28vw, 380px);

    gap: 10px;
    margin-top: 14px;

    }



    .sari-promo-carousel,

    .sari-promo-side-card {

        position: relative;

        overflow: hidden;

        border: 1px solid #e2e8f0;

        background: #ffffff;

        box-shadow:

            0 18px 42px rgba(15, 23, 42, .07),

            0 2px 8px rgba(15, 23, 42, .03);

    }



    .sari-promo-carousel {

        min-width: 0;

        min-height: 0;

        height: 100%;

        border-radius: 14px;

    }



    .sari-promo-slide {

        position: absolute;

        inset: 0;

        display: block;

        opacity: 0;

        pointer-events: none;

        transition: opacity .42s ease;

    }



    .sari-promo-slide.is-active {

        z-index: 1;

        opacity: 1;

        pointer-events: auto;

    }



    .sari-promo-slide img,

    .sari-promo-side-card img {

        display: block;

        width: 100%;

        height: 100%;

        object-fit: cover;

    }



    .sari-promo-slide[data-promo-style="soft"] img {

        object-position: center;

    }



    .sari-promo-slide-copy {

        position: absolute;

        top: 50%;

        left: 5.5%;

        z-index: 2;

        display: flex;

        width: min(43%, 320px);

        flex-direction: column;

        align-items: flex-start;

        transform: translateY(-50%);

    }



    .sari-promo-slide-copy--light {

        color: #ffffff;

        text-shadow: 0 2px 16px rgba(0,0,0,.28);

    }



    .sari-promo-slide-copy--dark {

        color: #111827;

    }



    .sari-promo-kicker {

        display: inline-flex;

        min-height: 21px;

        align-items: center;

        border: 1px solid rgba(201,137,11,.32);

        border-radius: 999px;

        background: rgba(255,255,255,.90);

        padding: 0 9px;

        color: #a96d07;

        font-size: 6.2px;

        font-weight: 800;

        letter-spacing: .08em;

        text-transform: uppercase;

        backdrop-filter: blur(8px);

    }



    .sari-promo-heading {

        display: block;

        max-width: 300px;

        margin-top: 7px;

        font-size: clamp(18px, 1.65vw, 24px);

        font-weight: 800;

        line-height: 1.08;

        letter-spacing: -.03em;

    }



    .sari-promo-copy {

        display: block;

        max-width: 275px;

        margin-top: 5px;

        font-size: 7px;

        line-height: 1.45;

        opacity: .82;

    }



    .sari-promo-cta {

        display: inline-flex;

        min-height: 27px;

        align-items: center;

        gap: 6px;

        margin-top: 8px;

        border-radius: 7px;

        background: #d9930a;

        padding: 0 10px;

        color: #ffffff;

        font-size: 6.5px;

        font-weight: 800;

        box-shadow: 0 7px 15px rgba(217,147,10,.18);

    }



    .sari-promo-carousel-controls {

        position: absolute;

        inset: 0;

        z-index: 4;

        pointer-events: none;

    }



    .sari-promo-arrow {

        position: absolute;

        top: 50%;

        display: grid;

        width: 28px;

        height: 28px;

        place-items: center;

        transform: translateY(-50%);

        border: 1px solid rgba(226,232,240,.95);

        border-radius: 999px;

        background: rgba(255,255,255,.94);

        color: #334155;

        box-shadow: 0 7px 20px rgba(15,23,42,.11);

        pointer-events: auto;

        transition: background-color .16s ease, color .16s ease, transform .16s ease;

    }



    .sari-promo-arrow:hover {

        background: #ffffff;

        color: #b77905;

        transform: translateY(-50%) scale(1.04);

    }



    .sari-promo-arrow--prev {

        left: 7px;

    }



    .sari-promo-arrow--next {

        right: 7px;

    }



    .sari-promo-dots {

        position: absolute;

        bottom: 8px;

        left: 50%;

        z-index: 5;

        display: flex;

        gap: 6px;

        transform: translateX(-50%);

    }



    .sari-promo-dot {

        width: 6px;

        height: 6px;

        border: 0;

        border-radius: 999px;

        background: rgba(255,255,255,.72);

        box-shadow: 0 1px 5px rgba(15,23,42,.15);

        transition: width .2s ease, background-color .2s ease;

    }



    .sari-promo-dot.is-active {

        width: 18px;

        background: #d9930a;

    }



    .sari-promo-side-stack {

        display: grid;

        min-width: 0;

        min-height: 0;

        grid-template-rows: repeat(2, minmax(0, 1fr));

        gap: 8px;

    }



    .sari-promo-side-card {

        min-width: 0;

        min-height: 0;

        height: 100%;

        border-radius: 13px;

    }



    .sari-promo-side-card::after {

        content: "";

        position: absolute;

        inset: 0;

        background: linear-gradient(90deg, rgba(15,23,42,.38) 0%, rgba(15,23,42,.10) 52%, transparent 82%);

        pointer-events: none;

    }



    .sari-promo-side-card--flash::after {

        background: linear-gradient(90deg, rgba(5,8,16,.66) 0%, rgba(5,8,16,.18) 70%, transparent 100%);

    }



    .sari-promo-side-copy {

        position: absolute;

        top: 50%;

        left: 12px;

        z-index: 2;

        display: flex;

        max-width: 56%;

        flex-direction: column;

        transform: translateY(-50%);

        color: #ffffff;

    }



    .sari-promo-side-copy strong {

        display: block;

        font-size: 8.6px;

        font-weight: 800;

        line-height: 1.15;

        letter-spacing: -.02em;

    }



    .sari-promo-side-copy span {

        display: block;

        margin-top: 3px;

        font-size: 5.8px;

        line-height: 1.35;

        opacity: .86;

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

        width: 118px;

        flex: 0 0 118px;

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

        height: 68px;

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

        min-height: 34px;

        padding: 8px 7px;

        color: #3a332c;

        font-size: 7px;

        font-weight: 700;

        line-height: 1.3;

        text-align: center;

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

        grid-template-columns: repeat(4, minmax(0, 1fr));

        grid-auto-flow: row dense;

        align-items: stretch;

        gap: 14px;

    }



    .sari-product-grid > .sari-featured-product-card {

        grid-row: auto;

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

            min-height: 300px;

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



        .sari-promo-showcase {

            height: auto;

            grid-template-columns: 1fr;

        }



        .sari-promo-carousel {

            height: 228px;

            min-height: 228px;

        }



        .sari-promo-side-stack {

            grid-template-columns: repeat(2, minmax(0, 1fr));

            grid-template-rows: none;

        }



        .sari-promo-side-card {

            height: 104px;

            min-height: 104px;

        }



        .sari-benefit-item:nth-child(3) {

            border-left: 0;

            border-top: 1px solid #e5e7eb;

        }



        .sari-benefit-item:nth-child(4) {

            border-top: 1px solid #e5e7eb;

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

            min-height: 330px;

        }



        .sari-hero-content {

            min-height: 330px !important;

            padding: 24px 20px !important;

        }



        .sari-hero-title {

            font-size: 32px;

        }



        .sari-promo-carousel {

            height: 202px;

            min-height: 202px;

        }



        .sari-promo-slide-copy {

            left: 20px;

            width: 62%;

        }



        .sari-promo-heading {

            max-width: 235px;

            font-size: 18px;

        }



        .sari-promo-copy {

            display: none;

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



        .sari-promo-carousel {

            height: 184px;

            min-height: 184px;

            border-radius: 13px;

        }



        .sari-promo-side-stack {

            grid-template-columns: repeat(2, minmax(0, 1fr));

            grid-template-rows: none;

        }



        .sari-promo-side-card {

            height: 88px;

            min-height: 88px;

        }



        .sari-promo-side-copy {

            left: 10px;

            max-width: 68%;

        }



        .sari-promo-side-copy strong {

            font-size: 7.8px;

        }



        .sari-promo-side-copy span {

            display: none;

        }



        .sari-promo-slide-copy {

            left: 17px;

            width: 70%;

        }



        .sari-promo-heading {

            max-width: 210px;

            font-size: 16px;

        }



        .sari-promo-arrow {

            width: 25px;

            height: 25px;

        }



        .sari-promo-arrow--prev {

            left: 8px;

        }



        .sari-promo-arrow--next {

            right: 8px;

        }



        .sari-benefit-item + .sari-benefit-item {

            border-left: 0;

            border-top: 1px solid #e5e7eb;

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



    /* ============================================================
       HOME HERO — LIGHT WHITE GRADIENT RESTORE
       Benefits below stay clean white. Existing links/data/JS unchanged.
    ============================================================ */
    .sari-home-hero {
        border-color: #eadfce !important;
        background: #fffaf1 !important;
        box-shadow: 0 13px 32px rgba(46, 35, 22, .055) !important;
    }

    .sari-home-hero::before {
        inset: 0 !important;
        width: auto !important;
        background: linear-gradient(
            90deg,
            rgba(255,252,247,1) 0%,
            rgba(255,250,241,.98) 39%,
            rgba(255,249,239,.86) 51%,
            rgba(255,249,239,.15) 72%,
            rgba(255,249,239,0) 100%
        ) !important;
    }

    .sari-hero-image img {
        filter: saturate(.88) contrast(.97) brightness(1.02) !important;
    }

    .sari-hero-badge {
        border-color: #e4c98f !important;
        background: rgba(255,255,255,.92) !important;
        color: #996514 !important;
        backdrop-filter: blur(8px);
    }

    .sari-hero-title {
        color: #171411 !important;
        text-shadow: none !important;
    }

    .sari-hero-title span {
        color: #b8790f !important;
    }

    .sari-hero-copy {
        color: #6f675f !important;
    }

    .sari-primary-btn {
        border: 1px solid var(--sari-gold) !important;
        background: var(--sari-gold) !important;
        color: #fff !important;
        box-shadow: 0 8px 20px rgba(204,138,11,.16) !important;
    }

    .sari-primary-btn:hover,
    .sari-primary-btn:focus-visible {
        border-color: var(--sari-gold-dark) !important;
        background: var(--sari-gold-dark) !important;
        color: #fff !important;
        outline: none !important;
    }

    .sari-secondary-btn {
        border-color: #ddd3c6 !important;
        background: #fff !important;
        color: #514940 !important;
        box-shadow: none !important;
    }

    .sari-secondary-btn:hover,
    .sari-secondary-btn:focus-visible {
        border-color: #cfb47c !important;
        background: #fffaf1 !important;
        color: #8f6010 !important;
        outline: none !important;
    }

    .sari-hero-trust {
        gap: 12px !important;
        margin-top: 14px !important;
    }

    .sari-hero-trust-item {
        color: #6f675f !important;
        font-size: 7.3px !important;
        font-weight: 600 !important;
    }

    .sari-hero-trust-dot {
        width: 18px !important;
        height: 18px !important;
        border: 0 !important;
        background: #c98a17 !important;
        color: #fff !important;
        font-size: 9px !important;
    }

    /* Keep the benefits section clean white and readable. */
    .sari-benefits-section {
        margin-top: 14px !important;
    }

    .sari-benefit-strip {
        border-color: #e2e8f0 !important;
        background: #ffffff !important;
        box-shadow:
            0 18px 46px rgba(15, 23, 42, .075),
            0 3px 10px rgba(15, 23, 42, .035) !important;
    }

    .sari-benefit-item {
        min-height: 84px !important;
        gap: 14px !important;
        padding: 15px 18px !important;
        background: #ffffff !important;
    }

    a.sari-benefit-item:hover,
    a.sari-benefit-item:focus-visible {
        background: #f8fafc !important;
    }

    .sari-benefit-item + .sari-benefit-item {
        border-left-color: #e5e7eb !important;
    }

    .sari-benefit-icon {
        width: 44px !important;
        height: 44px !important;
        flex-basis: 44px !important;
        border-color: #e8edf3 !important;
        border-radius: 12px !important;
        background: #f4f6f8 !important;
        color: #b77905 !important;
    }

    .sari-benefit-icon svg {
        width: 21px !important;
        height: 21px !important;
        stroke-width: 1.8 !important;
    }

    .sari-benefit-item h3 {
        color: #111827 !important;
        font-size: 13px !important;
        font-weight: 750 !important;
        line-height: 1.3 !important;
        letter-spacing: -.015em !important;
    }

    .sari-benefit-item p {
        margin-top: 4px !important;
        color: #7b8493 !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        line-height: 1.45 !important;
    }

    @media (max-width: 1023px) {
        .sari-home-hero::before {
            inset: 0 !important;
            width: auto !important;
            background: linear-gradient(
                90deg,
                rgba(255,252,247,.98) 0%,
                rgba(255,250,241,.97) 48%,
                rgba(255,249,239,.78) 72%,
                rgba(255,249,239,.55) 100%
            ) !important;
        }

        .sari-benefit-item:nth-child(3),
        .sari-benefit-item:nth-child(4) {
            border-top-color: #e5e7eb !important;
        }
    }

    @media (max-width: 639px) {
        .sari-home-hero::before {
            inset: 0 !important;
            width: auto !important;
            background: linear-gradient(
                90deg,
                rgba(255,252,247,.98) 0%,
                rgba(255,250,241,.96) 58%,
                rgba(255,249,239,.80) 100%
            ) !important;
        }

        .sari-hero-copy {
            font-size: 10px !important;
        }

        .sari-hero-trust-item {
            font-size: 8.2px !important;
        }

        .sari-benefit-item {
            min-height: 78px !important;
            padding: 14px 16px !important;
        }

        .sari-benefit-item + .sari-benefit-item {
            border-left: 0 !important;
        }
    }


    /* ============================================================
       FINAL HERO — WARM STONE / TAUPE PREMIUM MOCKUP
       Visual-only override. Existing routes, data and JS stay intact.
    ============================================================ */
    .sari-home-hero {
        min-height: 350px !important;
        border: 1px solid #ded6ca !important;
        border-radius: 18px !important;
        background: #ece7df !important;
        box-shadow:
            0 22px 54px rgba(43, 38, 31, .10),
            0 4px 14px rgba(43, 38, 31, .045) !important;
    }

    .sari-home-hero::before {
        inset: 0 !important;
        width: auto !important;
        z-index: -1 !important;
        background:
            linear-gradient(
                90deg,
                rgba(236, 231, 223, .99) 0%,
                rgba(239, 234, 226, .985) 31%,
                rgba(241, 236, 228, .93) 45%,
                rgba(242, 237, 230, .68) 57%,
                rgba(242, 237, 230, .22) 70%,
                rgba(242, 237, 230, 0) 83%
            ) !important;
    }

    .sari-hero-image {
        inset: 0 0 0 48% !important;
    }

    .sari-hero-image::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            linear-gradient(90deg, rgba(236,231,223,.18) 0%, rgba(236,231,223,0) 28%),
            linear-gradient(180deg, rgba(255,255,255,.04), rgba(25,20,14,.035));
    }

    .sari-hero-image img {
        filter: saturate(.90) contrast(.99) brightness(1.045) !important;
        object-position: center 53% !important;
        transform: scale(1.012);
    }

    .sari-hero-content {
        min-height: 350px !important;
        padding: 34px 40px !important;
    }

    .sari-hero-content > div {
        max-width: 760px !important;
    }

    .sari-hero-badge {
        min-height: 29px !important;
        border: 1px solid rgba(190, 127, 15, .44) !important;
        border-radius: 999px !important;
        background: rgba(249, 247, 243, .76) !important;
        padding: 0 13px !important;
        color: #9b650f !important;
        font-size: 7.2px !important;
        font-weight: 800 !important;
        letter-spacing: .12em !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.64);
        backdrop-filter: blur(10px);
    }

    .sari-hero-title {
        max-width: 760px !important;
        margin-top: 18px !important;
        color: #17191d !important;
        font-size: clamp(40px, 4vw, 62px) !important;
        font-weight: 800 !important;
        line-height: .99 !important;
        letter-spacing: -.058em !important;
        text-shadow: none !important;
    }

    .sari-hero-title span {
        display: block !important;
        margin-top: 4px;
        color: #c48610 !important;
    }

    .sari-hero-copy {
        max-width: 560px !important;
        margin-top: 18px !important;
        color: #5f625f !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        line-height: 1.7 !important;
    }

    .sari-hero-content .mt-5 {
        margin-top: 22px !important;
        gap: 12px !important;
    }

    .sari-primary-btn,
    .sari-secondary-btn {
        min-width: 142px;
        height: 46px !important;
        border-radius: 11px !important;
        padding: 0 20px !important;
        font-size: 9.5px !important;
        font-weight: 800 !important;
        letter-spacing: -.01em;
    }

    .sari-primary-btn {
        border: 1px solid #c88912 !important;
        background: linear-gradient(180deg, #d59618 0%, #c88912 100%) !important;
        color: #fff !important;
        box-shadow:
            0 10px 22px rgba(200,137,18,.20),
            inset 0 1px 0 rgba(255,255,255,.14) !important;
    }

    .sari-primary-btn:hover,
    .sari-primary-btn:focus-visible {
        border-color: #ad760f !important;
        background: linear-gradient(180deg, #c88912 0%, #ad760f 100%) !important;
        color: #fff !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    .sari-secondary-btn {
        border: 1px solid rgba(181, 126, 28, .46) !important;
        background: rgba(255,255,255,.56) !important;
        color: #282b31 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.70) !important;
        backdrop-filter: blur(9px);
    }

    .sari-secondary-btn:hover,
    .sari-secondary-btn:focus-visible {
        border-color: #c88912 !important;
        background: rgba(255,255,255,.82) !important;
        color: #8f5f0e !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    .sari-hero-trust {
        gap: 20px !important;
        margin-top: 23px !important;
    }

    .sari-hero-trust-item {
        gap: 8px !important;
        color: #3f4348 !important;
        font-size: 8.5px !important;
        font-weight: 700 !important;
    }

    .sari-hero-trust-dot {
        width: 23px !important;
        height: 23px !important;
        border: 1px solid rgba(173,118,15,.20) !important;
        background: linear-gradient(180deg, #d99a22 0%, #c88912 100%) !important;
        color: #fff !important;
        font-size: 11px !important;
        box-shadow: 0 5px 12px rgba(200,137,18,.16);
    }

    /* Benefits stay clean white, as requested. */
    .sari-benefits-section {
        margin-top: 16px !important;
    }

    .sari-benefit-strip {
        border-color: #e1e6eb !important;
        background: #fff !important;
        box-shadow:
            0 18px 46px rgba(15, 23, 42, .075),
            0 3px 10px rgba(15, 23, 42, .035) !important;
    }

    .sari-benefit-item {
        background: #fff !important;
    }

    @media (max-width: 1199px) {
        .sari-hero-title {
            font-size: clamp(36px, 4vw, 52px) !important;
        }

        .sari-hero-image {
            left: 50% !important;
            right: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
        }
    }

    @media (max-width: 1023px) {
        .sari-home-hero {
            min-height: 340px !important;
        }

        .sari-hero-image {
            inset: 0 !important;
        }

        .sari-home-hero::before {
            background:
                linear-gradient(
                    90deg,
                    rgba(236,231,223,.99) 0%,
                    rgba(239,234,226,.98) 44%,
                    rgba(241,236,228,.82) 66%,
                    rgba(241,236,228,.50) 100%
                ) !important;
        }

        .sari-hero-content {
            min-height: 340px !important;
            padding: 30px 32px !important;
        }

        .sari-hero-title {
            max-width: 650px !important;
            font-size: clamp(34px, 5.3vw, 48px) !important;
        }
    }

    @media (max-width: 767px) {
        .sari-home-hero {
            min-height: 380px !important;
            border-radius: 16px !important;
        }

        .sari-home-hero::before {
            background:
                linear-gradient(
                    90deg,
                    rgba(236,231,223,.985) 0%,
                    rgba(239,234,226,.97) 62%,
                    rgba(241,236,228,.78) 100%
                ) !important;
        }

        .sari-hero-content {
            min-height: 380px !important;
            padding: 28px 22px !important;
        }

        .sari-hero-title {
            font-size: clamp(32px, 9vw, 42px) !important;
            line-height: 1.01 !important;
        }

        .sari-hero-copy {
            max-width: 520px !important;
            font-size: 10.5px !important;
        }

        .sari-hero-trust {
            gap: 12px 16px !important;
        }
    }

    @media (max-width: 639px) {
        .sari-home-hero {
            min-height: 430px !important;
        }

        .sari-hero-content {
            min-height: 430px !important;
        }

        .sari-primary-btn,
        .sari-secondary-btn {
            width: 100%;
        }

        .sari-hero-trust-item {
            font-size: 8.2px !important;
        }
    }


    /* ============================================================
       FINAL HERO CLEANUP — SOLID BUTTONS + SIMPLIFIED CONTENT
       Hero only. Other Home sections remain unchanged.
    ============================================================ */
    .sari-hero-content .mt-5 {
        margin-top: 24px !important;
    }

    .sari-primary-btn {
        border: 1px solid #c88912 !important;
        background: #c88912 !important;
        background-image: none !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(200,137,18,.16) !important;
    }

    .sari-primary-btn:hover,
    .sari-primary-btn:focus-visible {
        border-color: #ad760f !important;
        background: #ad760f !important;
        background-image: none !important;
        color: #fff !important;
        outline: none !important;
    }

    .sari-secondary-btn {
        border: 1px solid #cfc6ba !important;
        background: #fff !important;
        background-image: none !important;
        color: #282b31 !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .sari-secondary-btn:hover,
    .sari-secondary-btn:focus-visible {
        border-color: #c88912 !important;
        background: #fff !important;
        background-image: none !important;
        color: #8f5f0e !important;
        outline: none !important;
    }

    .sari-hero-trust {
        margin-top: 20px !important;
    }



    /* ============================================================
       FINAL HERO TYPOGRAPHY + CTA POLISH
       Hero-only refinement. No functional changes.
    ============================================================ */
    .sari-hero-content {
        min-height: 350px !important;
        padding: 38px 40px !important;
    }

    .sari-hero-content > div {
        max-width: 760px !important;
    }

    .sari-hero-badge {
        min-height: 30px !important;
        border: 1px solid rgba(200, 137, 18, .42) !important;
        border-radius: 999px !important;
        background: rgba(255, 255, 255, .72) !important;
        padding: 0 13px !important;
        color: #9d6810 !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        letter-spacing: .14em !important;
        box-shadow: 0 3px 10px rgba(91, 67, 31, .04) !important;
        backdrop-filter: blur(8px) !important;
    }

    .sari-hero-title {
        max-width: 760px !important;
        margin-top: 18px !important;
        color: #17191d !important;
        font-size: clamp(42px, 4.35vw, 66px) !important;
        font-weight: 780 !important;
        line-height: .99 !important;
        letter-spacing: -.058em !important;
        text-wrap: balance;
    }

    .sari-hero-title span {
        display: block !important;
        margin-top: 6px !important;
        color: #c2810e !important;
        font-weight: 740 !important;
        letter-spacing: -.052em !important;
    }

    .sari-hero-content .mt-5 {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-top: 30px !important;
    }

    .sari-primary-btn,
    .sari-secondary-btn {
        min-width: 148px !important;
        height: 46px !important;
        border-radius: 12px !important;
        padding: 0 20px !important;
        font-size: 10.5px !important;
        font-weight: 760 !important;
        letter-spacing: -.01em !important;
        transition:
            background-color .16s ease,
            border-color .16s ease,
            color .16s ease,
            box-shadow .16s ease,
            transform .16s ease !important;
    }

    .sari-primary-btn {
        border: 1px solid #c88912 !important;
        background: #c88912 !important;
        background-image: none !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(200, 137, 18, .16) !important;
    }

    .sari-primary-btn:hover,
    .sari-primary-btn:focus-visible {
        border-color: #ad760f !important;
        background: #ad760f !important;
        background-image: none !important;
        color: #fff !important;
        box-shadow: 0 10px 22px rgba(173, 118, 15, .18) !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    .sari-primary-btn > span {
        font-size: 14px !important;
        line-height: 1 !important;
        transition: transform .16s ease !important;
    }

    .sari-primary-btn:hover > span,
    .sari-primary-btn:focus-visible > span {
        transform: translateX(2px) !important;
    }

    .sari-secondary-btn {
        border: 1px solid #d4c8b9 !important;
        background: rgba(255, 255, 255, .82) !important;
        background-image: none !important;
        color: #2b2d31 !important;
        box-shadow: 0 4px 12px rgba(72, 57, 39, .035) !important;
        backdrop-filter: blur(6px) !important;
    }

    .sari-secondary-btn:hover,
    .sari-secondary-btn:focus-visible {
        border-color: #c88912 !important;
        background: #fff !important;
        background-image: none !important;
        color: #9a6610 !important;
        box-shadow: 0 7px 16px rgba(80, 60, 32, .055) !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    @media (max-width: 1199px) {
        .sari-hero-title {
            font-size: clamp(38px, 4.5vw, 56px) !important;
        }
    }

    @media (max-width: 767px) {
        .sari-hero-content {
            min-height: 370px !important;
            padding: 30px 24px !important;
        }

        .sari-hero-title {
            margin-top: 15px !important;
            font-size: clamp(34px, 9vw, 44px) !important;
            line-height: 1.01 !important;
        }

        .sari-hero-content .mt-5 {
            margin-top: 24px !important;
        }
    }

    @media (max-width: 639px) {
        .sari-primary-btn,
        .sari-secondary-btn {
            width: 100% !important;
            min-width: 0 !important;
        }
    }

    /* ============================================================
       HERO BRAND — LOGO REPLACES EYEBROW TEXT ONLY
    ============================================================ */
    .sari-hero-brand-logo {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        width: fit-content;
        max-width: 100%;
        margin: 0;
    }

    .sari-hero-brand-logo img {
        display: block;
        width: auto;
        height: 42px;
        max-width: min(190px, 48vw);
        object-fit: contain;
        object-position: left center;
    }

    .sari-hero-brand-logo + .sari-hero-title {
        margin-top: 18px !important;
    }

    @media (max-width: 767px) {
        .sari-hero-brand-logo img {
            height: 36px;
            max-width: min(165px, 52vw);
        }
    }


    /* ============================================================
       FINAL HERO — TWO-LOGO LOCKUP + COMPACT PROFESSIONAL CTA
       Fixes wide SARI wordmark sizing. Solid buttons only.
       Hero-only visual changes; routes/data/JS remain untouched.
    ============================================================ */
    .sari-home-hero {
        min-height: 310px !important;
    }

    .sari-hero-content {
        min-height: 310px !important;
        padding: 28px 36px !important;
    }

    .sari-hero-content > div {
        max-width: 720px !important;
    }

    .sari-hero-brand-lockup {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        max-width: 100%;
        gap: 10px;
        margin: 0;
        padding-bottom: 10px;
        position: relative;
    }

    .sari-hero-brand-lockup::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 2px;
        border-radius: 999px;
        background: #c88912;
    }

    .sari-hero-brand-main,
    .sari-hero-brand-mark {
        display: block;
        object-fit: contain;
        object-position: left center;
    }

    .sari-hero-brand-main {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        max-width: none;
    }

    .sari-hero-brand-divider {
        width: 1px;
        height: 26px;
        flex: 0 0 1px;
        background: rgba(54, 47, 38, .20);
    }

    /*
       sari-logo.png is a wide 3:1 wordmark (2172x724).
       Size it by WIDTH, not height + a tiny max-width, otherwise
       the max-width keeps shrinking its visible height.
    */
    .sari-hero-brand-mark {
        width: 132px;
        height: auto;
        max-width: none;
        flex: 0 0 132px;
        filter: brightness(0) saturate(100%);
    }

    .sari-hero-brand-lockup + .sari-hero-title {
        max-width: 680px !important;
        margin-top: 14px !important;
        color: #17191d !important;
        font-size: clamp(38px, 3.8vw, 56px) !important;
        font-weight: 760 !important;
        line-height: 1 !important;
        letter-spacing: -.052em !important;
        text-wrap: balance;
    }

    .sari-hero-title span {
        display: block !important;
        margin-top: 5px !important;
        color: #bf7e0e !important;
        font-weight: 700 !important;
        letter-spacing: -.046em !important;
    }

    .sari-hero-content .mt-5 {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        margin-top: 22px !important;
    }

    .sari-primary-btn,
    .sari-secondary-btn {
        min-width: 154px !important;
        height: 44px !important;
        border-radius: 9px !important;
        padding: 0 20px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        letter-spacing: 0 !important;
        line-height: 1 !important;
        background-image: none !important;
        transition:
            background-color .16s ease,
            border-color .16s ease,
            color .16s ease,
            box-shadow .16s ease,
            transform .16s ease !important;
    }

    .sari-primary-btn {
        border: 1px solid #c88912 !important;
        background: #c88912 !important;
        color: #fff !important;
        box-shadow: 0 6px 14px rgba(200, 137, 18, .14) !important;
    }

    .sari-primary-btn:hover,
    .sari-primary-btn:focus-visible {
        border-color: #ad760f !important;
        background: #ad760f !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(173, 118, 15, .16) !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    .sari-primary-btn > span {
        font-size: 15px !important;
        font-weight: 400 !important;
        line-height: 1 !important;
        transition: transform .16s ease !important;
    }

    .sari-primary-btn:hover > span,
    .sari-primary-btn:focus-visible > span {
        transform: translateX(2px) !important;
    }

    .sari-secondary-btn {
        border: 1px solid #c9c1b7 !important;
        background: #fff !important;
        color: #292b2f !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .sari-secondary-btn:hover,
    .sari-secondary-btn:focus-visible {
        border-color: #c88912 !important;
        background: #fff !important;
        color: #98640f !important;
        box-shadow: 0 5px 12px rgba(52, 44, 34, .05) !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    @media (max-width: 1199px) {
        .sari-home-hero,
        .sari-hero-content {
            min-height: 305px !important;
        }

        .sari-hero-brand-lockup + .sari-hero-title {
            font-size: clamp(36px, 4.2vw, 50px) !important;
        }
    }

    @media (max-width: 767px) {
        .sari-home-hero,
        .sari-hero-content {
            min-height: 340px !important;
        }

        .sari-hero-content {
            padding: 24px 22px !important;
        }

        .sari-hero-brand-lockup {
            gap: 8px;
            padding-bottom: 9px;
        }

        .sari-hero-brand-main {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .sari-hero-brand-divider {
            height: 22px;
        }

        .sari-hero-brand-mark {
            width: 112px;
            height: auto;
            max-width: none;
            flex-basis: 112px;
            filter: brightness(0) saturate(100%);
        }

        .sari-hero-brand-lockup + .sari-hero-title {
            margin-top: 14px !important;
            font-size: clamp(32px, 8.5vw, 42px) !important;
            line-height: 1.01 !important;
        }

        .sari-hero-content .mt-5 {
            margin-top: 20px !important;
        }

        .sari-primary-btn,
        .sari-secondary-btn {
            font-size: 11.5px !important;
            font-weight: 600 !important;
        }
    }

    @media (max-width: 639px) {
        .sari-primary-btn,
        .sari-secondary-btn {
            width: 100% !important;
            min-width: 0 !important;
        }
    }



    /* ============================================================
       FINAL HERO HEADLINE SPACING FIX
       Looser title rhythm only; no layout/function changes.
    ============================================================ */
    .sari-hero-brand-lockup + .sari-hero-title {
        line-height: 1.06 !important;
        letter-spacing: -.045em !important;
    }

    .sari-hero-title span {
        margin-top: 12px !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    @media (max-width: 767px) {
        .sari-hero-brand-lockup + .sari-hero-title {
            line-height: 1.08 !important;
        }

        .sari-hero-title span {
            margin-top: 9px !important;
            line-height: 1.1 !important;
        }
    }

    /* ============================================================
       FINAL HERO DESCRIPTION + BRAND LINE REMOVAL
       Hero copy only. Existing routes/functions remain unchanged.
    ============================================================ */
    .sari-hero-brand-lockup {
        padding-bottom: 0 !important;
    }

    .sari-hero-brand-lockup::after {
        content: none !important;
        display: none !important;
    }

    .sari-hero-description {
        max-width: 525px;
        margin: 17px 0 0;
        color: #17191d;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        font-size: 12px;
        font-weight: 300;
        line-height: 1.7;
        letter-spacing: .005em;
        text-shadow: none;
    }

    .sari-hero-description + .mt-5 {
        margin-top: 19px !important;
    }

    @media (max-width: 767px) {
        .sari-hero-description {
            max-width: 450px;
            margin-top: 14px;
            font-size: 11px;
            line-height: 1.65;
        }

        .sari-hero-description + .mt-5 {
            margin-top: 17px !important;
        }
    }



    /* ============================================================
       PROMOTION SHOWCASE — PROFESSIONAL TYPOGRAPHY + CLEAN LAYOUT
       Visual-only. Carousel links, images, data attributes and JS stay intact.
    ============================================================ */
    .sari-promo-showcase {
        grid-template-columns: minmax(0, 2.15fr) minmax(250px, .85fr) !important;
        height: clamp(350px, 29vw, 390px) !important;
        gap: 12px !important;
        margin-top: 16px !important;
    }

    .sari-promo-carousel,
    .sari-promo-side-card {
        border-color: #dde2e7 !important;
        background: #fff !important;
        box-shadow:
            0 18px 42px rgba(15, 23, 42, .075),
            0 3px 10px rgba(15, 23, 42, .035) !important;
    }

    .sari-promo-carousel {
        border-radius: 16px !important;
    }

    .sari-promo-side-card {
        border-radius: 15px !important;
    }

    .sari-promo-slide-copy {
        left: 34px !important;
        width: min(48%, 430px) !important;
        max-width: 430px !important;
        align-items: flex-start !important;
    }

    .sari-promo-slide-copy--dark {
        padding: 18px 20px !important;
        border: 1px solid rgba(226, 232, 240, .95) !important;
        border-radius: 14px !important;
        background: rgba(255, 255, 255, .94) !important;
        color: #171a1f !important;
        box-shadow: 0 14px 32px rgba(15, 23, 42, .10) !important;
        text-shadow: none !important;
    }

    .sari-promo-slide-copy--light {
        width: min(46%, 410px) !important;
        color: #fff !important;
        text-shadow: 0 2px 16px rgba(0, 0, 0, .32) !important;
    }

    .sari-promo-kicker {
        min-height: 27px !important;
        padding: 0 11px !important;
        border-radius: 999px !important;
        background: #fff !important;
        color: #a96d07 !important;
        font-size: 8.5px !important;
        font-weight: 700 !important;
        letter-spacing: .09em !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .sari-promo-heading {
        max-width: 390px !important;
        margin-top: 11px !important;
        font-size: clamp(25px, 2.15vw, 31px) !important;
        font-weight: 700 !important;
        line-height: 1.12 !important;
        letter-spacing: -.035em !important;
    }

    .sari-promo-copy {
        max-width: 360px !important;
        margin-top: 8px !important;
        font-size: 11px !important;
        font-weight: 400 !important;
        line-height: 1.6 !important;
        opacity: .82 !important;
    }

    .sari-promo-cta {
        min-height: 38px !important;
        margin-top: 13px !important;
        border-radius: 9px !important;
        background: #c88912 !important;
        padding: 0 15px !important;
        color: #fff !important;
        font-size: 10px !important;
        font-weight: 650 !important;
        letter-spacing: -.01em !important;
        box-shadow: 0 7px 16px rgba(200, 137, 18, .15) !important;
    }

    .sari-promo-slide:hover .sari-promo-cta,
    .sari-promo-slide:focus-visible .sari-promo-cta {
        background: #ad760f !important;
    }

    .sari-promo-side-stack {
        gap: 10px !important;
    }

    .sari-promo-side-card::after,
    .sari-promo-side-card--flash::after {
        background: linear-gradient(
            0deg,
            rgba(10, 14, 20, .76) 0%,
            rgba(10, 14, 20, .48) 34%,
            rgba(10, 14, 20, .12) 66%,
            rgba(10, 14, 20, 0) 100%
        ) !important;
    }

    .sari-promo-side-card img {
        transition: transform .28s ease !important;
    }

    .sari-promo-side-card:hover img,
    .sari-promo-side-card:focus-visible img {
        transform: scale(1.025) !important;
    }

    .sari-promo-side-copy {
        top: auto !important;
        bottom: 18px !important;
        left: 18px !important;
        max-width: calc(100% - 36px) !important;
        transform: none !important;
        color: #fff !important;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .28) !important;
    }

    .sari-promo-side-copy strong {
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: -.025em !important;
    }

    .sari-promo-side-copy span {
        max-width: 230px !important;
        margin-top: 5px !important;
        font-size: 9.5px !important;
        font-weight: 400 !important;
        line-height: 1.45 !important;
        opacity: .88 !important;
    }

    .sari-promo-arrow {
        width: 34px !important;
        height: 34px !important;
        border-color: #e2e8f0 !important;
        background: #fff !important;
        color: #334155 !important;
        box-shadow: 0 7px 18px rgba(15, 23, 42, .10) !important;
    }

    .sari-promo-dot {
        width: 7px !important;
        height: 7px !important;
    }

    .sari-promo-dot.is-active {
        width: 22px !important;
        background: #c88912 !important;
    }

    @media (max-width: 1023px) {
        .sari-promo-showcase {
            height: auto !important;
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .sari-promo-carousel {
            height: 270px !important;
            min-height: 270px !important;
        }

        .sari-promo-side-stack {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            grid-template-rows: none !important;
        }

        .sari-promo-side-card {
            height: 132px !important;
            min-height: 132px !important;
        }

        .sari-promo-slide-copy {
            left: 26px !important;
            width: min(56%, 390px) !important;
        }

        .sari-promo-heading {
            font-size: clamp(22px, 3.4vw, 28px) !important;
        }

        .sari-promo-copy {
            font-size: 10.5px !important;
        }

        .sari-promo-side-copy {
            bottom: 14px !important;
            left: 15px !important;
            max-width: calc(100% - 30px) !important;
        }

        .sari-promo-side-copy strong {
            font-size: 13.5px !important;
        }

        .sari-promo-side-copy span {
            font-size: 8.8px !important;
        }
    }

    @media (max-width: 767px) {
        .sari-promo-carousel {
            height: 238px !important;
            min-height: 238px !important;
        }

        .sari-promo-slide-copy {
            left: 18px !important;
            width: min(72%, 350px) !important;
        }

        .sari-promo-slide-copy--dark {
            padding: 14px 15px !important;
            border-radius: 12px !important;
        }

        .sari-promo-kicker {
            min-height: 24px !important;
            font-size: 7.5px !important;
        }

        .sari-promo-heading {
            margin-top: 8px !important;
            font-size: clamp(19px, 5vw, 23px) !important;
        }

        .sari-promo-copy {
            display: block !important;
            margin-top: 6px !important;
            font-size: 9.3px !important;
            line-height: 1.45 !important;
        }

        .sari-promo-cta {
            min-height: 34px !important;
            margin-top: 9px !important;
            padding: 0 12px !important;
            font-size: 9px !important;
        }

        .sari-promo-side-card {
            height: 112px !important;
            min-height: 112px !important;
        }

        .sari-promo-side-copy strong {
            font-size: 12px !important;
        }

        .sari-promo-side-copy span {
            display: block !important;
            font-size: 8px !important;
        }
    }

    @media (max-width: 639px) {
        .sari-promo-carousel {
            height: 220px !important;
            min-height: 220px !important;
        }

        .sari-promo-slide-copy {
            left: 14px !important;
            width: min(78%, 320px) !important;
        }

        .sari-promo-heading {
            font-size: 18px !important;
        }

        .sari-promo-copy {
            font-size: 8.8px !important;
        }

        .sari-promo-side-stack {
            grid-template-columns: 1fr !important;
        }

        .sari-promo-side-card {
            height: 132px !important;
            min-height: 132px !important;
        }

        .sari-promo-side-copy strong {
            font-size: 13px !important;
        }

        .sari-promo-side-copy span {
            font-size: 8.5px !important;
        }
    }


    /* ============================================================
       PROMO CLEANUP — NO TEXT CONTAINERS + ARROW CLEARANCE
       Visual-only. Keeps carousel logic, links and images unchanged.
    ============================================================ */
    .sari-promo-slide-copy {
        top: 42% !important;
        left: 46px !important;
        width: min(48%, 430px) !important;
        max-width: 430px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        transform: translateY(-50%) !important;
    }

    .sari-promo-slide-copy--dark,
    .sari-promo-slide-copy--light {
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .sari-promo-slide-copy--dark {
        color: #171a1f !important;
        text-shadow: none !important;
    }

    .sari-promo-slide-copy--light {
        color: #ffffff !important;
        text-shadow: 0 2px 16px rgba(0, 0, 0, .30) !important;
    }

    .sari-promo-kicker {
        min-height: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        color: #b77905 !important;
        font-size: 8.5px !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
    }

    .sari-promo-slide-copy--light .sari-promo-kicker {
        color: #ffffff !important;
    }

    .sari-promo-heading {
        margin-top: 9px !important;
    }

    .sari-promo-side-copy {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    @media (max-width: 1023px) {
        .sari-promo-slide-copy {
            top: 41% !important;
            left: 44px !important;
            width: min(58%, 390px) !important;
        }
    }

    @media (max-width: 767px) {
        .sari-promo-slide-copy {
            top: 40% !important;
            left: 42px !important;
            width: min(72%, 350px) !important;
        }

        .sari-promo-kicker {
            min-height: 0 !important;
            padding: 0 !important;
            font-size: 7.5px !important;
        }
    }

    @media (max-width: 639px) {
        .sari-promo-slide-copy {
            top: 39% !important;
            left: 40px !important;
            width: calc(100% - 62px) !important;
            max-width: 300px !important;
        }

        .sari-promo-heading {
            max-width: 250px !important;
        }
    }


    /* ============================================================
       SHOP BY CATEGORY — CIRCULAR IMAGE + TEXT BELOW
       Visual-only refinement. Existing category links/data unchanged.
    ============================================================ */
    #categories .sari-section-title {
        font-size: 24px !important;
        font-weight: 700 !important;
        letter-spacing: -.03em !important;
        color: #1f2329 !important;
    }

    .sari-category-rail {
        gap: 18px !important;
        padding: 6px 3px 12px !important;
    }

    .sari-category-card {
        width: 128px !important;
        flex: 0 0 128px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 2px 4px 6px !important;
        text-align: center !important;
        transition: transform .18s ease !important;
    }

    .sari-category-card:hover {
        border-color: transparent !important;
        box-shadow: none !important;
        transform: translateY(-2px) !important;
    }

    .sari-category-image {
        width: 104px !important;
        height: 104px !important;
        margin: 0 auto !important;
        overflow: hidden !important;
        border: 1px solid #e1e5ea !important;
        border-radius: 999px !important;
        background: #f5f6f8 !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .07) !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease !important;
    }

    .sari-category-image img {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: inherit !important;
        transition: transform .22s ease !important;
    }

    .sari-category-card:hover .sari-category-image {
        border-color: rgba(200, 137, 18, .55) !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .10) !important;
    }

    .sari-category-card:hover .sari-category-image img {
        transform: scale(1.045) !important;
    }

    .sari-category-name {
        display: block !important;
        min-height: 0 !important;
        margin-top: 10px !important;
        padding: 0 2px !important;
        color: #252a31 !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        line-height: 1.35 !important;
        letter-spacing: -.01em !important;
        text-align: center !important;
    }

    .sari-category-card:hover .sari-category-name {
        color: #a96f0e !important;
    }

    @media (max-width: 767px) {
        #categories .sari-section-title {
            font-size: 21px !important;
        }

        .sari-category-rail {
            gap: 14px !important;
        }

        .sari-category-card {
            width: 112px !important;
            flex-basis: 112px !important;
        }

        .sari-category-image {
            width: 92px !important;
            height: 92px !important;
        }

        .sari-category-name {
            margin-top: 9px !important;
            font-size: 10.5px !important;
        }
    }



    /* ============================================================
       SHOP BY CATEGORY — ICON-BASED PROFESSIONAL RAIL
       Inline SVG icons replace category images. Links/data unchanged.
    ============================================================ */
    #categories .sari-section-title {
        color: #1f2329 !important;
        font-size: 25px !important;
        font-weight: 700 !important;
        letter-spacing: -.035em !important;
    }

    .sari-category-rail {
        gap: 18px !important;
        padding: 6px 3px 12px !important;
        scroll-padding-inline: 4px;
    }

    .sari-category-card {
        display: flex !important;
        width: 146px !important;
        flex: 0 0 146px !important;
        flex-direction: column !important;
        align-items: center !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 3px 4px 7px !important;
        text-align: center !important;
        transition: transform .18s ease !important;
    }

    .sari-category-card:hover,
    .sari-category-card:focus-visible {
        border-color: transparent !important;
        box-shadow: none !important;
        transform: translateY(-2px) !important;
        outline: none !important;
    }

    .sari-category-icon {
        display: grid;
        width: 110px;
        height: 110px;
        flex: 0 0 110px;
        place-items: center;
        border: 1px solid #e1e5ea;
        border-radius: 999px;
        background: #f7f8fa;
        color: #323840;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .055);
        transition:
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    .sari-category-icon svg {
        display: block;
        width: 43px;
        height: 43px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.65;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sari-category-card:hover .sari-category-icon,
    .sari-category-card:focus-visible .sari-category-icon {
        border-color: rgba(200, 137, 18, .58);
        background: #fff;
        color: #b8780d;
        box-shadow: 0 11px 26px rgba(15, 23, 42, .085);
        transform: translateY(-1px);
    }

    .sari-category-name {
        display: block !important;
        width: 100% !important;
        max-width: 142px !important;
        min-height: 0 !important;
        margin: 12px auto 0 !important;
        padding: 0 !important;
        color: #20252c !important;
        font-size: 14.5px !important;
        font-weight: 600 !important;
        line-height: 1.32 !important;
        letter-spacing: -.014em !important;
        text-align: center !important;
        text-wrap: balance;
        transition: color .18s ease !important;
    }

    .sari-category-card:hover .sari-category-name,
    .sari-category-card:focus-visible .sari-category-name {
        color: #9f690e !important;
    }

    @media (max-width: 767px) {
        #categories .sari-section-title {
            font-size: 22px !important;
        }

        .sari-category-rail {
            gap: 14px !important;
        }

        .sari-category-card {
            width: 126px !important;
            flex-basis: 126px !important;
        }

        .sari-category-icon {
            width: 94px;
            height: 94px;
            flex-basis: 94px;
        }

        .sari-category-icon svg {
            width: 37px;
            height: 37px;
        }

        .sari-category-name {
            max-width: 122px !important;
            margin-top: 10px !important;
            font-size: 13px !important;
            line-height: 1.3 !important;
        }
    }


    /* ============================================================
       OFFICIAL HOME CATEGORIES — EXACT 8 MARKETPLACE GROUPS
       Icons only; always visible even when a category has zero products.
    ============================================================ */
    #categories .sari-category-rail {
        gap: 18px !important;
        padding-top: 8px !important;
        padding-bottom: 12px !important;
    }

    #categories .sari-category-card {
        width: 132px !important;
        flex: 0 0 132px !important;
    }

    #categories .sari-category-icon {
        width: 92px !important;
        height: 92px !important;
        flex-basis: 92px !important;
    }

    #categories .sari-category-icon svg {
        width: 37px !important;
        height: 37px !important;
    }

    #categories .sari-category-name {
        max-width: 148px !important;
        margin-top: 12px !important;
        font-size: 14.5px !important;
        font-weight: 600 !important;
        line-height: 1.32 !important;
    }

    @media (max-width: 767px) {
        #categories .sari-category-rail {
            gap: 13px !important;
        }

        #categories .sari-category-card {
            width: 116px !important;
            flex-basis: 116px !important;
        }

        #categories .sari-category-icon {
            width: 82px !important;
            height: 82px !important;
            flex-basis: 82px !important;
        }

        #categories .sari-category-icon svg {
            width: 32px !important;
            height: 32px !important;
        }

        #categories .sari-category-name {
            max-width: 124px !important;
            font-size: 13px !important;
        }
    }

    /* ============================================================
       HOME CATEGORIES — LANDING PAGE IMAGERY
       Uses the same eight category assets as the public landing page.
       Keeps the compact circular Home layout and existing links.
    ============================================================ */
    #categories .sari-category-photo {
        display: block;
        width: 84px;
        height: 84px;
        flex: 0 0 84px;
        overflow: hidden;
        border: 1px solid #e1e5ea;
        border-radius: 999px;
        background: #f5f6f8;
        box-shadow: 0 7px 18px rgba(15, 23, 42, .065);
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    #categories .sari-category-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: inherit;
        transition: transform .22s ease, filter .22s ease;
    }

    #categories .sari-category-card:hover .sari-category-photo,
    #categories .sari-category-card:focus-visible .sari-category-photo {
        border-color: rgba(200, 137, 18, .58);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .10);
        transform: translateY(-1px);
    }

    #categories .sari-category-card:hover .sari-category-photo img,
    #categories .sari-category-card:focus-visible .sari-category-photo img {
        transform: scale(1.045);
        filter: saturate(1.03) contrast(1.01);
    }

    @media (max-width: 767px) {
        #categories .sari-category-photo {
            width: 74px;
            height: 74px;
            flex-basis: 74px;
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



            <div class="sari-hero-content flex min-h-[300px] items-center px-6 py-6 sm:px-8 lg:px-10 xl:px-12">

                <div class="w-full max-w-[720px]">

                    <div class="sari-hero-brand-lockup" aria-label="SARI brand">
                        <img
                            class="sari-hero-brand-main"
                            src="{{ asset('images/sari-main-logo.png') }}"
                            alt="SARI"
                            width="180"
                            height="56"
                            loading="eager"
                            decoding="async"
                        >
                        <span class="sari-hero-brand-divider" aria-hidden="true"></span>
                        <img
                            class="sari-hero-brand-mark"
                            src="{{ asset('images/sari-logo.png') }}"
                            alt=""
                            width="56"
                            height="56"
                            loading="eager"
                            decoding="async"
                        >
                    </div>



                    <h1 class="sari-hero-title">

                        Variety You Need,

                        <span>Convenience You Deserve.</span>

                    </h1>

                    <p class="sari-hero-description">
                        A trusted marketplace for curated finds, everyday essentials, and effortless shopping.
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
</div>

            </div>

        </section>



        {{-- ============================================================

            BENEFITS

        ============================================================ --}}

        <section class="sari-benefits-section">

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

            PROMOTIONS — AUTO-SWIPING MARKETPLACE BANNERS

            Existing public assets only; no backend dependency.

        ============================================================ --}}

        <section

            id="buyerPromoShowcase"

            class="sari-promo-showcase"

            aria-label="Featured promotions"

            data-promo-carousel

        >

            <div class="sari-promo-carousel">

                <a

                    href="{{ route('buyer.products') }}"

                    class="sari-promo-slide is-active"

                    data-promo-slide

                    data-promo-style="soft"

                    aria-label="Shop the SARI featured collection"

                >

                    <img

                        src="{{ asset('images/hero.jpg') }}"

                        alt="SARI featured collection"

                        width="1600"

                        height="900"

                        loading="eager"

                        decoding="async"

                    >

                    <span class="sari-promo-slide-copy sari-promo-slide-copy--light">

                        <span class="sari-promo-kicker">Featured collection</span>

                        <strong class="sari-promo-heading">Modern picks for everyday living.</strong>

                        <span class="sari-promo-copy">Curated essentials from trusted SARI sellers.</span>

                        <span class="sari-promo-cta">Shop collection <span aria-hidden="true">→</span></span>

                    </span>

                </a>



                <a

                    href="{{ route('buyer.products') }}"

                    class="sari-promo-slide"

                    data-promo-slide

                    data-promo-style="soft"

                    aria-label="Shop beauty essentials"

                >

                    <img

                        src="{{ asset('images/beauty-bestsellers.png') }}"

                        alt="Beauty bestsellers"

                        width="1884"

                        height="835"

                        loading="lazy"

                        decoding="async"

                    >

                    <span class="sari-promo-slide-copy sari-promo-slide-copy--dark">

                        <span class="sari-promo-kicker">Beauty edit</span>

                        <strong class="sari-promo-heading">Everyday beauty, elevated.</strong>

                        <span class="sari-promo-copy">Polished essentials from trusted SARI sellers.</span>

                        <span class="sari-promo-cta">Shop beauty <span aria-hidden="true">→</span></span>

                    </span>

                </a>



                <a

                    href="{{ route('buyer.products') }}"

                    class="sari-promo-slide"

                    data-promo-slide

                    data-promo-style="soft"

                    aria-label="Shop home essentials"

                >

                    <img

                        src="{{ asset('images/home-essentials.png') }}"

                        alt="Home essentials"

                        width="1774"

                        height="887"

                        loading="lazy"

                        decoding="async"

                    >

                    <span class="sari-promo-slide-copy sari-promo-slide-copy--dark">

                        <span class="sari-promo-kicker">Home refresh</span>

                        <strong class="sari-promo-heading">Comfort for every corner.</strong>

                        <span class="sari-promo-copy">Simple finds for a calmer everyday space.</span>

                        <span class="sari-promo-cta">Explore home <span aria-hidden="true">→</span></span>

                    </span>

                </a>



                <a

                    href="{{ route('buyer.products') }}"

                    class="sari-promo-slide"

                    data-promo-slide

                    data-promo-style="soft"

                    aria-label="Shop watch essentials"

                >

                    <img

                        src="{{ asset('images/weekend-watch.png') }}"

                        alt="SARI watch essentials"

                        width="1254"

                        height="1254"

                        loading="lazy"

                        decoding="async"

                    >

                    <span class="sari-promo-slide-copy sari-promo-slide-copy--dark">

                        <span class="sari-promo-kicker">Style spotlight</span>

                        <strong class="sari-promo-heading">Timeless details, modern style.</strong>

                        <span class="sari-promo-copy">Refined accessories from trusted SARI sellers.</span>

                        <span class="sari-promo-cta">Discover more <span aria-hidden="true">→</span></span>

                    </span>

                </a>



                <div class="sari-promo-carousel-controls" aria-hidden="false">

                    <button

                        type="button"

                        class="sari-promo-arrow sari-promo-arrow--prev"

                        data-promo-prev

                        aria-label="Previous promotion"

                    >

                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">

                            <path d="m15 18-6-6 6-6"></path>

                        </svg>

                    </button>

                    <button

                        type="button"

                        class="sari-promo-arrow sari-promo-arrow--next"

                        data-promo-next

                        aria-label="Next promotion"

                    >

                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">

                            <path d="m9 18 6-6-6-6"></path>

                        </svg>

                    </button>

                </div>



                <div class="sari-promo-dots" role="tablist" aria-label="Promotion slides">

                    @for ($promoIndex = 0; $promoIndex < 4; $promoIndex++)

                        <button

                            type="button"

                            class="sari-promo-dot {{ $promoIndex === 0 ? 'is-active' : '' }}"

                            data-promo-dot="{{ $promoIndex }}"

                            aria-label="Show promotion {{ $promoIndex + 1 }}"

                            aria-selected="{{ $promoIndex === 0 ? 'true' : 'false' }}"

                        ></button>

                    @endfor

                </div>

            </div>



            <div class="sari-promo-side-stack">

                <a

                    href="{{ route('buyer.products') }}"

                    class="sari-promo-side-card"

                    aria-label="Shop SARI beauty bestsellers"

                >

                    <img

                        src="{{ asset('images/cat-beauty.jpg') }}"

                        alt="SARI beauty bestsellers"

                        width="800"

                        height="1067"

                        loading="lazy"

                        decoding="async"

                    >

                    <span class="sari-promo-side-copy">

                        <strong>Beauty bestsellers</strong>

                        <span>Everyday favorites from trusted sellers.</span>

                    </span>

                </a>



                <a

                    href="{{ route('buyer.products', ['discounted' => 1]) }}"

                    class="sari-promo-side-card sari-promo-side-card--flash"

                    aria-label="Shop flash sale electronics"

                >

                    <img

                        src="{{ asset('images/sari-ticket-08-product-camera.jpg') }}"

                        alt="SARI flash sale electronics"

                        width="832"

                        height="832"

                        loading="lazy"

                        decoding="async"

                    >

                    <span class="sari-promo-side-copy">

                        <strong>Flash sale</strong>

                        <span>Limited-time tech and lifestyle deals.</span>

                    </span>

                </a>

            </div>

        </section>



        {{-- ============================================================

            SHOP BY CATEGORY

        ============================================================ --}}

        <section id="categories" class="mt-5">

            <div>

                <h2 class="sari-section-title text-[18px] font-bold text-[#201b17] sm:text-[20px]">Shop by Category</h2>

            </div>



            <div id="buyerCategoryRail" class="sari-category-rail mt-3">

                @forelse ($categoryCollection as $category)

                    <a

                        href="{{ route('marketplace.category', ['category' => $category['slug']]) }}"

                        class="sari-category-card"

                        aria-label="Browse {{ $category['name'] }}"

                    >

                        <span class="sari-category-photo">
                            <img
                                src="{{ asset($category['image']) }}"
                                alt="{{ $category['name'] }} category"
                                width="184"
                                height="184"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>

                        <span class="sari-category-name">{{ $category['name'] }}</span>

                    </a>

                @empty

                    <div class="sari-card rounded-[12px] px-4 py-5 text-[11px] text-[#8f867c]">

                        Categories will appear here.

                    </div>

                @endforelse

            </div>

        </section>



        {{-- ============================================================

            RECOMMENDED PRODUCTS

        ============================================================ --}}

        <section class="mt-5">

            <div>

                <p class="text-[7px] font-bold uppercase tracking-[.13em] text-[#aa7415]">Curated marketplace</p>

                <h2 class="sari-section-title mt-1 text-[19px] font-bold text-[#201b17] sm:text-[22px]">Recommended for You</h2>

            </div>



            <div class="sari-product-grid mt-3">

                @forelse ($featuredCollection as $product)

                    <x-buyer.product-card

                        :product="$product"

                        :priority="true"

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



        const promo = document.querySelector('[data-promo-carousel]');



        if (window.__sariBuyerPromoTimer) {

            window.clearInterval(window.__sariBuyerPromoTimer);

            window.__sariBuyerPromoTimer = null;

        }



        if (promo && promo.dataset.sariPromoBound !== '1') {

            promo.dataset.sariPromoBound = '1';



            const slides = Array.from(promo.querySelectorAll('[data-promo-slide]'));

            const dots = Array.from(promo.querySelectorAll('[data-promo-dot]'));

            const prev = promo.querySelector('[data-promo-prev]');

            const next = promo.querySelector('[data-promo-next]');

            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            let currentIndex = 0;



            const showPromo = (index) => {

                if (!slides.length) return;



                currentIndex = (index + slides.length) % slides.length;



                slides.forEach((slide, slideIndex) => {

                    const active = slideIndex === currentIndex;

                    slide.classList.toggle('is-active', active);

                    slide.setAttribute('aria-hidden', active ? 'false' : 'true');

                    slide.setAttribute('tabindex', active ? '0' : '-1');

                });



                dots.forEach((dot, dotIndex) => {

                    const active = dotIndex === currentIndex;

                    dot.classList.toggle('is-active', active);

                    dot.setAttribute('aria-selected', active ? 'true' : 'false');

                });

            };



            const stopAutoPromo = () => {

                if (!window.__sariBuyerPromoTimer) return;

                window.clearInterval(window.__sariBuyerPromoTimer);

                window.__sariBuyerPromoTimer = null;

            };



            const startAutoPromo = () => {

                stopAutoPromo();



                if (prefersReducedMotion || slides.length < 2) return;



                window.__sariBuyerPromoTimer = window.setInterval(() => {

                    showPromo(currentIndex + 1);

                }, 5200);

            };



            prev?.addEventListener('click', () => {

                showPromo(currentIndex - 1);

                startAutoPromo();

            });



            next?.addEventListener('click', () => {

                showPromo(currentIndex + 1);

                startAutoPromo();

            });



            dots.forEach((dot, index) => {

                dot.addEventListener('click', () => {

                    showPromo(index);

                    startAutoPromo();

                });

            });



            promo.addEventListener('mouseenter', stopAutoPromo);

            promo.addEventListener('mouseleave', startAutoPromo);

            promo.addEventListener('focusin', stopAutoPromo);

            promo.addEventListener('focusout', (event) => {

                if (!promo.contains(event.relatedTarget)) startAutoPromo();

            });



            showPromo(0);

            startAutoPromo();

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
