@extends('layouts.buyer')

@section('title', ($shop['store_name'] ?? 'Shop') . ' — SARI')
@section('page-title', 'Seller Shop')

@section('content')
@include('components.buyer.header')

<style>
    .sari-shop-page {
        --shop-gold: #c88912;
        --shop-gold-dark: #9d680d;
        --shop-ink: #111827;
        --shop-text: #344054;
        --shop-muted: #7a8594;
        --shop-line: #e3e8ed;
        --shop-soft: #f7f8fa;
        --shop-bg: #f5f7f9;
        background: var(--shop-bg);
        color: var(--shop-text);
    }

    .sari-shop-shell {
        width: 100%;
        max-width: 1500px;
        margin-inline: auto;
        padding: 22px 18px 40px;
    }

    .sari-shop-card {
        overflow: hidden;
        border: 1px solid var(--shop-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .035);
    }

    .sari-shop-banner {
        height: 170px;
        overflow: hidden;
        background: #f1f3f5;
        border-bottom: 1px solid var(--shop-line);
    }

    .sari-shop-banner img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .sari-shop-profile {
        padding: 22px 24px 24px;
    }

    .sari-shop-profile-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .sari-shop-identity {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 15px;
    }

    .sari-shop-logo {
        width: 68px;
        height: 68px;
        flex: 0 0 68px;
        border: 1px solid #dfe5ea;
        border-radius: 15px;
        background: #fff;
        object-fit: cover;
    }

    .sari-shop-eyebrow {
        margin: 0;
        color: var(--shop-gold-dark);
        font-size: 9px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sari-shop-name {
        margin: 5px 0 0;
        color: var(--shop-ink);
        font-size: clamp(24px, 2.2vw, 31px);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -.035em;
    }

    .sari-shop-meta {
        margin: 7px 0 0;
        color: var(--shop-muted);
        font-size: 10px;
        line-height: 1.5;
    }

    .sari-shop-message {
        display: inline-flex;
        height: 42px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--shop-gold);
        border-radius: 10px;
        background: var(--shop-gold);
        padding-inline: 18px;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        text-decoration: none;
        transition: background-color .16s ease, border-color .16s ease, transform .16s ease;
    }

    .sari-shop-message:hover,
    .sari-shop-message:focus-visible {
        border-color: var(--shop-gold-dark);
        background: var(--shop-gold-dark);
        outline: none;
        transform: translateY(-1px);
    }

    .sari-shop-section-line {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--shop-line);
    }

    .sari-shop-info-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: start;
    }

    .sari-shop-section-label {
        margin: 0;
        color: #697586;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .sari-shop-description {
        max-width: 900px;
        margin: 8px 0 0;
        color: #667085;
        font-size: 10px;
        line-height: 1.8;
        white-space: pre-line;
    }

    .sari-shop-contact {
        min-width: 250px;
        margin: 0;
        overflow: hidden;
        border: 1px solid var(--shop-line);
        border-radius: 12px;
        background: #fff;
    }

    .sari-shop-contact-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 11px 13px;
    }

    .sari-shop-contact-row + .sari-shop-contact-row {
        border-top: 1px solid #edf0f3;
    }

    .sari-shop-contact dt {
        color: #8a94a1;
        font-size: 8px;
        font-weight: 600;
    }

    .sari-shop-contact dd {
        margin: 0;
        color: #344054;
        font-size: 8px;
        font-weight: 800;
        text-align: right;
        word-break: break-word;
    }

    .sari-shop-vouchers {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .sari-shop-voucher {
        min-width: 160px;
        border: 1px solid #e5d5b3;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        text-align: left;
        transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
    }

    .sari-shop-voucher:hover,
    .sari-shop-voucher:focus-visible {
        border-color: #d4b46c;
        background: #fffdf8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .06);
    }

    .sari-shop-voucher-code {
        display: block;
        color: var(--shop-gold-dark);
        font-size: 9px;
        font-weight: 800;
    }

    .sari-shop-voucher-meta {
        display: block;
        margin-top: 3px;
        color: #7e8793;
        font-size: 8px;
        line-height: 1.45;
    }

    .sari-shop-products-card {
        margin-top: 16px;
        padding: 20px 22px 24px;
        border: 1px solid var(--shop-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .03);
    }

    .sari-shop-products-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .sari-shop-products-title {
        margin: 0;
        color: var(--shop-ink);
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .sari-shop-products-subtitle {
        margin: 4px 0 0;
        color: var(--shop-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .sari-shop-search {
        width: 290px;
        height: 42px;
        border: 1px solid #dce2e7;
        border-radius: 10px;
        background: #fafbfc;
        padding: 0 14px;
        color: #344054;
        font-size: 9px;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .sari-shop-search::placeholder {
        color: #98a2b3;
    }

    .sari-shop-search:focus {
        border-color: #c9a253;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .07);
    }

    .sari-shop-product-grid {
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(265px, 325px));
        justify-content:start;
        align-items:stretch;
        gap:16px;
        margin-top:18px;
    }

    /* Keep each reusable product card compact like the reference.
       This only constrains the shop layout; the product-card component logic is untouched. */
    .sari-shop-product-grid > * {
        width:100%;
        max-width:325px;
        min-width:0;
    }

    .sari-shop-empty {
        margin-top: 18px;
        border: 1px dashed #d9e0e6;
        border-radius: 14px;
        background: #fafbfc;
        padding: 38px 20px;
        color: #7c8794;
        font-size: 10px;
        text-align: center;
    }

    @media (max-width: 1024px) {
        .sari-shop-profile-top,
        .sari-shop-products-head {
            align-items: flex-start;
        }

        .sari-shop-info-grid {
            grid-template-columns: 1fr;
        }

        .sari-shop-contact {
            width: 100%;
            min-width: 0;
        }

        .sari-shop-product-grid {
            grid-template-columns:repeat(2, minmax(0, 1fr));
            justify-content:stretch;
        }

        .sari-shop-product-grid > * {
            max-width:none;
        }
    }

    @media (max-width: 640px) {
        .sari-shop-shell {
            padding: 14px 12px 28px;
        }

        .sari-shop-card,
        .sari-shop-products-card {
            border-radius: 15px;
        }

        .sari-shop-banner {
            height: 132px;
        }

        .sari-shop-profile {
            padding: 17px;
        }

        .sari-shop-profile-top {
            flex-direction: column;
        }

        .sari-shop-logo {
            width: 58px;
            height: 58px;
            flex-basis: 58px;
            border-radius: 13px;
        }

        .sari-shop-message,
        .sari-shop-search {
            width: 100%;
        }

        .sari-shop-info-grid {
            gap: 16px;
        }

        .sari-shop-products-card {
            padding: 17px;
        }

        .sari-shop-products-head {
            flex-direction: column;
        }

        .sari-shop-product-grid {
            grid-template-columns:1fr;
        }

        .sari-shop-product-grid > * {
            max-width:none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-shop-message,
        .sari-shop-voucher,
        .sari-shop-search {
            transition: none;
        }
    }


    /* Compact product-card presentation on wide screens */
    @media (min-width:1025px) {
        .sari-shop-products-card {
            padding:20px 22px 26px;
        }

        .sari-shop-product-grid {
            column-gap:16px;
            row-gap:18px;
        }
    }


    /* ==============================================================
       SARI BUYER SELLER SHOP — FLOATING WORKSPACE + READABLE TYPE
       Major surfaces float; nested content stays light and flat.
       Scoped entirely inside the buyer's shop content area.
       Existing product grid, Blade data, actions and JS unchanged.
       ============================================================== */
    .sari-shop-page {
        --shop-bg: #f4f5f7;
        --shop-line: #e5e7eb;
        --shop-text: #3d4856;
        --shop-muted: #75808d;
        background: var(--shop-bg);
        color: var(--shop-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    .sari-shop-page .sari-shop-shell {
        max-width: 1480px;
        box-sizing: border-box;
        padding: 30px 32px 50px;
    }

    /* Keep only the two top-level sections elevated. */
    .sari-shop-page .sari-shop-card,
    .sari-shop-page .sari-shop-products-card {
        border: 1px solid #e5e7eb;
        border-radius: 19px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(15,23,42,.045),
            0 10px 24px rgba(15,23,42,.075),
            0 24px 54px rgba(15,23,42,.095),
            inset 0 1px 0 rgba(255,255,255,.92);
        transform: none;
    }

    .sari-shop-page .sari-shop-card {
        overflow: hidden; /* Keeps the full-width seller banner clipped to the panel. */
    }

    .sari-shop-page .sari-shop-profile {
        padding: 26px 30px 28px;
    }

    .sari-shop-page .sari-shop-profile-top {
        gap: 22px;
    }

    .sari-shop-page .sari-shop-identity {
        gap: 17px;
    }

    .sari-shop-page .sari-shop-logo {
        width: 74px;
        height: 74px;
        flex-basis: 74px;
        border-color: #e3e7eb;
    }

    .sari-shop-page .sari-shop-eyebrow {
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4;
        letter-spacing: .095em;
    }

    .sari-shop-page .sari-shop-name {
        margin-top: 5px;
        font-size: clamp(28px, 2.35vw, 36px);
        font-weight: 700;
        line-height: 1.17;
        letter-spacing: -.027em;
        overflow-wrap: anywhere;
    }

    .sari-shop-page .sari-shop-meta {
        margin-top: 9px;
        color: #707b88;
        font-size: 13px;
        font-weight: 450;
        line-height: 1.6;
    }

    .sari-shop-page .sari-shop-message {
        height: 46px;
        padding-inline: 20px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 650;
        line-height: 1;
        transform: none;
    }

    .sari-shop-page .sari-shop-message:hover,
    .sari-shop-page .sari-shop-message:focus-visible {
        transform: none;
    }

    /* Profile details remain a single panel; inner groups are dividers. */
    .sari-shop-page .sari-shop-section-line {
        margin-top: 24px;
        padding-top: 22px;
        border-top-color: #eceff2;
    }

    .sari-shop-page .sari-shop-info-grid {
        gap: 26px;
    }

    .sari-shop-page .sari-shop-section-label {
        color: #586475;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        letter-spacing: .065em;
    }

    .sari-shop-page .sari-shop-description {
        margin-top: 10px;
        max-width: 920px;
        color: #667383;
        font-size: 13.5px;
        font-weight: 400;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .sari-shop-page .sari-shop-contact {
        min-width: 270px;
        border-color: #e6e9ed;
        border-radius: 11px;
        background: #fbfcfd;
        box-shadow: none;
    }

    .sari-shop-page .sari-shop-contact-row {
        gap: 14px;
        padding: 13px 15px;
    }

    .sari-shop-page .sari-shop-contact dt {
        color: #7b8794;
        font-size: 11.5px;
        font-weight: 500;
        line-height: 1.4;
    }

    .sari-shop-page .sari-shop-contact dd {
        color: #354151;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
    }

    /* Voucher options are subtle outlined controls, not nested floating cards. */
    .sari-shop-page .sari-shop-vouchers {
        gap: 10px;
        margin-top: 12px;
    }

    .sari-shop-page .sari-shop-voucher {
        min-width: 190px;
        border-color: #e6ded0;
        border-radius: 10px;
        padding: 12px 14px;
        box-shadow: none;
    }

    .sari-shop-page .sari-shop-voucher-code {
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.4;
    }

    .sari-shop-page .sari-shop-voucher-meta {
        margin-top: 5px;
        color: #77828e;
        font-size: 11.5px;
        font-weight: 450;
        line-height: 1.55;
    }

    .sari-shop-page #shopVoucherFeedback {
        font-size: 12px !important;
        font-weight: 550 !important;
        line-height: 1.45;
    }

    .sari-shop-page .sari-shop-products-card {
        margin-top: 24px;
        padding: 25px 28px 30px;
        overflow: visible;
    }

    .sari-shop-page .sari-shop-products-head {
        gap: 18px;
    }

    .sari-shop-page .sari-shop-products-title {
        color: #26313f;
        font-size: 23px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -.025em;
    }

    .sari-shop-page .sari-shop-products-subtitle {
        margin-top: 5px;
        color: #798491;
        font-size: 12px;
        font-weight: 450;
        line-height: 1.55;
    }

    .sari-shop-page .sari-shop-search {
        width: 318px;
        height: 46px;
        border-color: #dfe4e9;
        border-radius: 10px;
        background: #fff;
        padding-inline: 15px;
        color: #344054;
        font-size: 13px;
        font-weight: 450;
    }

    .sari-shop-page .sari-shop-search::placeholder {
        color: #8b96a4;
        opacity: 1;
    }

    .sari-shop-page .sari-shop-product-grid {
        gap: 17px;
        margin-top: 21px;
    }

    /* Shared product component stays unchanged. Only this shop's
       card typography and mild inner elevation are refined. */
    .sari-shop-page .sari-shop-product-grid > .sari-product-card,
    .sari-shop-page .sari-shop-product-grid > .sari-product-card:hover,
    .sari-shop-page .sari-shop-product-grid > .sari-product-card:focus-within {
        border-color: #e4e8ec !important;
        box-shadow:
            0 1px 3px rgba(15,23,42,.035),
            0 7px 18px rgba(15,23,42,.06),
            inset 0 1px 0 rgba(255,255,255,.92) !important;
        transform: none !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-title {
        font-size: 15px !important;
        font-weight: 650 !important;
        line-height: 1.45 !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-meta {
        font-size: 11.5px !important;
        line-height: 1.5 !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-price {
        font-size: 17px !important;
        font-weight: 750 !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-old-price,
    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-secondary {
        font-size: 10.5px !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-stock {
        font-size: 11.5px !important;
        font-weight: 650 !important;
    }

    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-buy,
    .sari-shop-page .sari-shop-product-grid .sari-product-card .sari-buyer-card-view-only {
        font-size: 11px !important;
        font-weight: 650 !important;
    }

    .sari-shop-page .sari-shop-empty {
        border-color: #e3e7eb;
        background: #fafbfc;
        color: #6d7886;
        font-size: 13px;
        font-weight: 450;
        line-height: 1.6;
    }

    @media (max-width: 1024px) {
        .sari-shop-page .sari-shop-shell {
            padding: 24px 24px 42px;
        }

        .sari-shop-page .sari-shop-contact {
            min-width: 0;
        }
    }

    @media (max-width: 640px) {
        .sari-shop-page .sari-shop-shell {
            padding: 16px 14px 34px;
        }

        .sari-shop-page .sari-shop-card,
        .sari-shop-page .sari-shop-products-card {
            border-radius: 16px;
            box-shadow:
                0 1px 3px rgba(15,23,42,.035),
                0 7px 18px rgba(15,23,42,.06),
                0 15px 32px rgba(15,23,42,.065),
                inset 0 1px 0 rgba(255,255,255,.90);
        }

        .sari-shop-page .sari-shop-profile {
            padding: 19px 18px 22px;
        }

        .sari-shop-page .sari-shop-logo {
            width: 60px;
            height: 60px;
            flex-basis: 60px;
        }

        .sari-shop-page .sari-shop-name {
            font-size: clamp(25px, 6.5vw, 30px);
        }

        .sari-shop-page .sari-shop-meta {
            font-size: 12px;
        }

        .sari-shop-page .sari-shop-section-line {
            margin-top: 20px;
            padding-top: 20px;
        }

        .sari-shop-page .sari-shop-description {
            font-size: 12.5px;
        }

        .sari-shop-page .sari-shop-message,
        .sari-shop-page .sari-shop-search {
            width: 100%;
        }

        .sari-shop-page .sari-shop-products-card {
            margin-top: 18px;
            padding: 20px 17px 24px;
        }

        .sari-shop-page .sari-shop-products-title {
            font-size: 21px;
        }

        .sari-shop-page .sari-shop-product-grid {
            margin-top: 17px;
            gap: 14px;
        }

        .sari-shop-page .sari-shop-voucher {
            min-width: 0;
            width: 100%;
        }
    }

</style>

<div class="sari-shop-page">
    <div class="sari-shop-shell">
        <section class="sari-shop-card">
            @if (!empty($shop['banner']))
                <div class="sari-shop-banner">
                    <img
                        src="{{ $shop['banner'] }}"
                        alt="{{ $shop['store_name'] }} store banner"
                        decoding="async"
                    >
                </div>
            @endif

            <div class="sari-shop-profile">
                <div class="sari-shop-profile-top">
                    <div class="sari-shop-identity">
                        <img
                            src="{{ $shop['logo'] }}"
                            alt="{{ $shop['store_name'] }} logo"
                            class="sari-shop-logo"
                        >

                        <div class="min-w-0">
                            <p class="sari-shop-eyebrow">Verified SARI Seller</p>
                            <h1 class="sari-shop-name">{{ $shop['store_name'] }}</h1>
                            <p class="sari-shop-meta">
                                {{ $shop['products_count'] }} active products
                                <span aria-hidden="true">•</span>
                                ★ {{ number_format((float) $shop['rating'], 1) }}
                                from {{ $shop['rating_count'] }} reviews
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('buyer.messages', ['seller' => $shop['seller_account_id']]) }}"
                        class="sari-shop-message"
                    >
                        Message Seller
                    </a>
                </div>

                @if (!empty($shop['description']) || !empty($shop['public_phone']) || !empty($shop['public_email']))
                    <div class="sari-shop-section-line">
                        <div class="sari-shop-info-grid">
                            <div>
                                <p class="sari-shop-section-label">About this shop</p>
                                <p class="sari-shop-description">{{ $shop['description'] ?: 'This seller has not added a public store description yet.' }}</p>
                            </div>

                            @if (!empty($shop['public_phone']) || !empty($shop['public_email']))
                                <dl class="sari-shop-contact">
                                    @if (!empty($shop['public_phone']))
                                        <div class="sari-shop-contact-row">
                                            <dt>Store phone</dt>
                                            <dd>{{ $shop['public_phone'] }}</dd>
                                        </div>
                                    @endif

                                    @if (!empty($shop['public_email']))
                                        <div class="sari-shop-contact-row">
                                            <dt>Public email</dt>
                                            <dd>{{ $shop['public_email'] }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            @endif
                        </div>
                    </div>
                @endif

                @if (!empty($shop['vouchers']))
                    <div class="sari-shop-section-line">
                        <p class="sari-shop-section-label">Available seller vouchers</p>

                        <div class="sari-shop-vouchers">
                            @foreach ($shop['vouchers'] as $voucher)
                                <button
                                    type="button"
                                    data-copy-voucher="{{ $voucher['code'] }}"
                                    class="sari-shop-voucher"
                                    title="Copy voucher code"
                                >
                                    <span class="sari-shop-voucher-code">
                                        {{ $voucher['code'] }} · {{ $voucher['value'] }}
                                    </span>

                                    <span class="sari-shop-voucher-meta">
                                        {{ $voucher['name'] }}
                                        @if ((float) ($voucher['minimum'] ?? 0) > 0)
                                            · Min. ₱{{ number_format((float) $voucher['minimum'], 2) }}
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <p
                            id="shopVoucherFeedback"
                            class="mt-2 hidden text-[8px] font-semibold text-[#4f7d63]"
                            aria-live="polite"
                        ></p>
                    </div>
                @endif
            </div>
        </section>

        <section class="sari-shop-products-card">
            <div class="sari-shop-products-head">
                <div>
                    <h2 class="sari-shop-products-title">Products</h2>
                    <p class="sari-shop-products-subtitle">
                        Only approved and active listings are visible here.
                    </p>
                </div>

                <input
                    id="shopProductSearch"
                    type="search"
                    placeholder="Search this shop..."
                    class="sari-shop-search"
                >
            </div>

            <div id="shopProductGrid" class="sari-shop-product-grid">
                @foreach ($products as $product)
                    <x-buyer.product-card
                        :product="$product"
                        :interactive="true"
                        :show-promotions="true"
                        :show-stock="true"
                        :show-quick-add="true"
                    />
                @endforeach
            </div>

            <div id="shopProductsEmpty" class="sari-shop-empty hidden">
                No products match your search.
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-shop.js')
@endpush
