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

    $categoryCollection = collect($categories ?? []);

    $featuredCollection = collect($featuredProducts ?? []);



    $bestSellerProducts = $featuredCollection

        ->sortByDesc(fn ($product) => (int) ($product['sold'] ?? 0))

        ->take(4)

        ->values();

@endphp



@push('styles')

    @foreach ($categoryCollection->take(10) as $category)

        @if (!empty($category['image']))

            <link rel="preload" as="image" href="{{ asset($category['image']) }}">

        @endif

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
