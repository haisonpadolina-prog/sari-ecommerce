{{-- SARI Platform UI — fully namespaced to avoid app.css conflicts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; }
    body { margin: 0; }


    .sari-platform-ui {
        --sari-platform-section-bg: #FFFFFF;
        --sari-platform-bg: #FBF8F2;
        --sari-platform-surface: #FFFFFF;
        --sari-platform-sidebar: #F5F1E8;
        --sari-platform-border: #EBE3D2;
        --sari-platform-ink: #16140F;
        --sari-platform-muted: #9A9488;
        --sari-platform-muted-2: #4A453D;
        --sari-platform-gold: #B8863B;
        --sari-platform-skeleton: #E2DAC3;
        --sari-platform-skeleton-soft: #EEE8D9;
        --sari-platform-skeleton-mid: #E7E0CC;
        --sari-platform-phone-body: #16140F;
        --sari-platform-phone-screen: #FFFFFF;
        --sari-platform-shadow-soft: rgba(22, 20, 15, 0.18);
        --sari-platform-shadow-strong: rgba(22, 20, 15, 0.38);

        background: var(--sari-platform-section-bg);
        padding: 130px 0;
        font-family: 'Poppins', sans-serif;
        /* this section must never clip its children — the phone
           intentionally overflows the bottom of the browser card. */
        overflow: visible;
        transition: background 0.3s ease;
    }

    /* ---------- header ---------- */

    .sari-platform-ui-header {
        max-width: 620px;
        margin: 0 auto 72px;
        text-align: center;
    }

    .sari-platform-ui .sari-platform-eyebrow {
        display: block;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--sari-platform-gold);
        margin-bottom: 18px;
    }

    .sari-platform-ui .sari-platform-section-title {
        font-weight: 700;
        font-size: clamp(30px, 4vw, 44px);
        line-height: 1.2;
        letter-spacing: -0.01em;
        color: var(--sari-platform-ink);
        margin: 0 0 18px;
    }

    .sari-platform-ui .sari-platform-section-description {
        font-weight: 400;
        font-size: 15.5px;
        line-height: 1.75;
        color: var(--sari-platform-muted-2);
        max-width: 460px;
        margin: 0 auto;
    }

    /* ---------- stage ---------- */

    .sari-platform-ui-stage {
        position: relative;
        max-width: 960px;
        margin: 0 auto;
        /* room for the phone to hang below, and the cart card to hang
           above, the browser card — without either getting clipped by
           the next section or a parent overflow:hidden */
        padding-top: 40px;
        padding-bottom: 90px;
        overflow: visible;
    }

    /* ---------- browser frame ---------- */

    .sari-platform-browser {
        background: var(--sari-platform-surface);
        border: 1px solid var(--sari-platform-border);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 30px 60px -25px var(--sari-platform-shadow-soft);
        position: relative;
        z-index: 1;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .sari-platform-browser-top {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid var(--sari-platform-border);
    }

    .sari-platform-browser-dots {
        display: flex;
        gap: 6px;
    }

    .sari-platform-browser-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--sari-platform-skeleton);
    }

    .sari-platform-browser-content {
        display: grid;
        grid-template-columns: 190px 1fr;
        min-height: 380px;
    }

    /* sidebar */

    .sari-platform-mock-sidebar {
        background: var(--sari-platform-sidebar);
        border-right: 1px solid var(--sari-platform-border);
        padding: 24px 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .sari-platform-mock-brand {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: var(--sari-platform-gold);
        margin-bottom: 10px;
    }

    .sari-platform-mock-nav-line {
        height: 8px;
        width: 78%;
        border-radius: 4px;
        background: var(--sari-platform-skeleton-mid);
    }

    .sari-platform-mock-nav-line.active {
        width: 92%;
        background: var(--sari-platform-gold);
    }

    /* main */

    .sari-platform-mock-main {
        padding: 28px 30px;
    }

    .sari-platform-mock-heading {
        height: 14px;
        width: 42%;
        border-radius: 4px;
        background: var(--sari-platform-skeleton);
        margin-bottom: 10px;
    }

    .sari-platform-mock-subheading {
        height: 9px;
        width: 62%;
        border-radius: 4px;
        background: var(--sari-platform-skeleton-soft);
        margin-bottom: 30px;
    }

    .sari-platform-mock-products {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .sari-platform-mock-product {
        background: var(--sari-platform-bg);
        border: 1px solid var(--sari-platform-border);
        border-radius: 12px;
        padding: 14px;
    }

    .sari-platform-mock-product-image {
        height: 72px;
        border-radius: 8px;
        background: var(--sari-platform-skeleton-mid);
        margin-bottom: 14px;
    }

    .sari-platform-mock-product-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sari-platform-mock-product-line {
        height: 8px;
        width: 85%;
        border-radius: 4px;
        background: var(--sari-platform-skeleton);
    }

    .sari-platform-mock-product-line.small {
        width: 50%;
        background: var(--sari-platform-skeleton-soft);
    }

    /* ---------- shared gentle float, used by both floating cards ---------- */

    @keyframes sari-platform-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-platform-phone,
        .sari-platform-cart-card {
            animation: none !important;
        }
    }


    /* ---------- floating cart card (top-left, balances the phone) ---------- */

    .sari-platform-cart-card {
        position: absolute;
        top: -30px;
        left: 40px;
        width: 196px;
        background: var(--sari-platform-surface);
        border: 1px solid var(--sari-platform-border);
        border-radius: 16px;
        padding: 16px;
        z-index: 4;
        box-shadow:
            0 24px 46px -18px var(--sari-platform-shadow-strong),
            0 4px 10px -4px var(--sari-platform-shadow-soft);
        display: flex;
        flex-direction: column;
        gap: 12px;
        animation: sari-platform-float 6s ease-in-out infinite;
        animation-delay: -3s;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .sari-platform-cart-card-head {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .sari-platform-cart-card-icon {
        position: relative;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: var(--sari-platform-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sari-platform-cart-card-icon svg {
        width: 14px;
        height: 14px;
    }

    .sari-platform-cart-card-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 15px;
        height: 15px;
        padding: 0 3px;
        border-radius: 8px;
        background: var(--sari-platform-ink);
        color: var(--sari-platform-surface);
        font-size: 8.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--sari-platform-surface);
    }

    .sari-platform-cart-card-title {
        font-size: 11px;
        font-weight: 600;
        color: var(--sari-platform-ink);
    }

    .sari-platform-cart-card-sub {
        font-size: 9px;
        color: var(--sari-platform-muted);
    }

    .sari-platform-cart-item {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .sari-platform-cart-item-thumb {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: var(--sari-platform-skeleton-mid);
        flex-shrink: 0;
    }

    .sari-platform-cart-item-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }

    .sari-platform-cart-item-name {
        font-size: 9.5px;
        font-weight: 500;
        color: var(--sari-platform-ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sari-platform-cart-item-price {
        font-size: 9px;
        color: var(--sari-platform-muted);
    }

    .sari-platform-cart-card-divider {
        height: 1px;
        background: var(--sari-platform-border);
        margin: 0;
    }

    .sari-platform-cart-card-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sari-platform-cart-card-total-label {
        font-size: 9.5px;
        color: var(--sari-platform-muted);
    }

    .sari-platform-cart-card-total-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--sari-platform-ink);
    }

    .sari-platform-cart-card-cta {
        width: 100%;
        padding: 8px 0;
        border-radius: 9px;
        background: var(--sari-platform-gold);
        color: var(--sari-platform-cta-text, #FFFFFF);
        font-family: 'Poppins', sans-serif;
        font-size: 10px;
        font-weight: 600;
        text-align: center;
        border: none;
        letter-spacing: 0.01em;
    }

    /* ---------- floating phone ---------- */

    .sari-platform-phone {
        position: absolute;
        right: 32px;
        bottom: -46px;
        width: 172px;
        background: var(--sari-platform-phone-body);
        border-radius: 32px;
        padding: 8px;
        z-index: 5;
        box-shadow:
            0 24px 46px -18px var(--sari-platform-shadow-strong),
            0 4px 10px -4px var(--sari-platform-shadow-soft);
        transition: background 0.3s ease;
        animation: sari-platform-float 6s ease-in-out infinite;
    }

    /* dynamic-island style notch, sits INSIDE the screen like a real phone */

    .sari-platform-phone-notch {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 52px;
        height: 18px;
        border-radius: 10px;
        background: var(--sari-platform-phone-body);
        z-index: 3;
    }

    .sari-platform-phone-screen {
        position: relative;
        overflow: hidden;
        background: var(--sari-platform-phone-screen);
        border-radius: 25px;
        padding: 14px 12px 16px;
        height: 280px;
        transition: background 0.3s ease;
    }

    /* real status bar: time + signal/wifi/battery glyphs instead of dots */

    .sari-platform-phone-statusbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 0 2px;
    }

    .sari-platform-phone-time {
        font-size: 11px;
        font-weight: 600;
        color: var(--sari-platform-ink);
        letter-spacing: 0.02em;
    }

    .sari-platform-phone-icons {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .sari-platform-phone-signal {
        display: flex;
        align-items: flex-end;
        gap: 1.5px;
        height: 8px;
    }

    .sari-platform-phone-signal span {
        width: 2.5px;
        background: var(--sari-platform-ink);
        border-radius: 1px;
    }

    .sari-platform-phone-signal span:nth-child(1) { height: 35%; }
    .sari-platform-phone-signal span:nth-child(2) { height: 60%; }
    .sari-platform-phone-signal span:nth-child(3) { height: 80%; }
    .sari-platform-phone-signal span:nth-child(4) { height: 100%; }

    .sari-platform-phone-battery {
        width: 18px;
        height: 9px;
        border: 1px solid var(--sari-platform-ink);
        border-radius: 2.5px;
        padding: 1px;
        position: relative;
    }

    .sari-platform-phone-battery::after {
        content: '';
        position: absolute;
        right: -3px;
        top: 2.5px;
        width: 1.5px;
        height: 3px;
        background: var(--sari-platform-ink);
        border-radius: 0 1px 1px 0;
    }

    .sari-platform-phone-battery-fill {
        background: var(--sari-platform-ink);
        height: 100%;
        width: 80%;
        border-radius: 1px;
    }

    .sari-platform-phone-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sari-platform-phone-line {
        height: 8px;
        width: 70%;
        border-radius: 4px;
        background: var(--sari-platform-skeleton-mid);
    }

    .sari-platform-phone-line.short {
        width: 45%;
        height: 6px;
        background: var(--sari-platform-skeleton-soft);
        margin-bottom: 6px;
    }

    .sari-platform-phone-row {
        height: 30px;
        border-radius: 8px;
        background: var(--sari-platform-bg);
        border: 1px solid var(--sari-platform-border);
    }

    /* home indicator, sits over the bottom of the screen like real iOS */

    .sari-platform-phone-home-indicator {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 46px;
        height: 4px;
        border-radius: 2px;
        background: var(--sari-platform-ink);
        opacity: 0.25;
    }

    /* notification — slides in from the top of the phone screen, settles
       with a soft spring, holds, then eases back out. loops.
       NOTE: solid opaque background, no backdrop-filter — that blur was
       softening the icon + text on every frame of the animation. */

    .sari-platform-phone-notif {
        position: absolute;
        top: 0;
        left: 8px;
        right: 8px;
        display: flex;
        align-items: flex-start;
        gap: 9px;
        background: var(--sari-platform-surface);
        border: 1px solid var(--sari-platform-border);
        border-radius: 14px;
        padding: 10px 11px;
        box-shadow: 0 10px 24px -10px var(--sari-platform-shadow-strong);
        transform: translateY(-90px);
        opacity: 0;
        animation: sari-platform-phone-notif-slide 5s cubic-bezier(0.34, 1.4, 0.4, 1) infinite;
        /* keeps text crisp at every animation frame */
        backface-visibility: hidden;
        will-change: transform, opacity;
    }

    .sari-platform-phone-notif-icon {
        width: 22px;
        height: 22px;
        border-radius: 7px;
        background: var(--sari-platform-gold);
        flex-shrink: 0;
        margin-top: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sari-platform-phone-notif-icon svg {
        width: 12px;
        height: 12px;
    }

    .sari-platform-phone-notif-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .sari-platform-phone-notif-title {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--sari-platform-ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sari-platform-phone-notif-sub {
        font-size: 9.5px;
        font-weight: 400;
        color: var(--sari-platform-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @keyframes sari-platform-phone-notif-slide {
        0% {
            transform: translateY(-90px);
            opacity: 0;
        }
        14% {
            transform: translateY(14px);
            opacity: 1;
        }
        20% {
            transform: translateY(10px);
            opacity: 1;
        }
        80% {
            transform: translateY(10px);
            opacity: 1;
        }
        94% {
            transform: translateY(-90px);
            opacity: 0;
        }
        100% {
            transform: translateY(-90px);
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-platform-phone-notif {
            animation: none;
            transform: translateY(10px);
            opacity: 1;
        }
    }


    /* ---- responsive ---- */
    @media (max-width: 820px) {
        .sari-platform-browser-content {
            grid-template-columns: 1fr;
        }

        .sari-platform-mock-sidebar {
            flex-direction: row;
            align-items: center;
            border-right: none;
            border-bottom: 1px solid var(--sari-platform-border);
            padding: 16px 20px;
        }

        .sari-platform-mock-brand {
            margin-bottom: 0;
        }

        .sari-platform-mock-nav-line {
            display: none;
        }

        .sari-platform-mock-products {
            grid-template-columns: 1fr;
        }

        .sari-platform-ui-stage {
            padding-top: 0;
            padding-bottom: 0;
        }

        .sari-platform-phone {
            position: static;
            margin: 28px auto 0;
        }

        .sari-platform-cart-card {
            position: static;
            margin: 0 auto 20px;
            animation: none;
        }
    }

    @media (max-width: 480px) {
        .sari-platform-ui {
            padding: 90px 0;
        }

        .sari-platform-mock-main {
            padding: 22px 18px;
        }
    }
</style>
<section class="sari-platform-section sari-platform-light-section sari-platform-ui" id="sari-platform-ui-section">

    <div class="sari-platform-container">

        <div class="sari-platform-ui-header sari-platform-reveal">
            <span class="sari-platform-eyebrow">Platform Experience</span>
            <h2 class="sari-platform-section-title">Everything connected<br>in one place.</h2>
            <p class="sari-platform-section-description">
                SARI brings marketplace discovery, product
                management, orders, and delivery into one
                organized digital experience.
            </p>
        </div>

        <div class="sari-platform-ui-stage sari-platform-reveal" aria-label="SARI marketplace interface preview">

            <!-- FLOATING CART CARD -->
            <div class="sari-platform-cart-card" aria-hidden="true">

                <div class="sari-platform-cart-card-head">
                    <div class="sari-platform-cart-card-icon">
                        <span class="sari-platform-cart-card-badge">2</span>
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 4H5L6.5 14.5C6.6 15.3 7.3 16 8.1 16H17.5C18.3 16 19 15.4 19.1 14.6L20.5 7H6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="9" cy="20" r="1.4" fill="white"/>
                            <circle cx="17" cy="20" r="1.4" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <div class="sari-platform-cart-card-title">Your cart</div>
                        <div class="sari-platform-cart-card-sub">2 items</div>
                    </div>
                </div>

                <div class="sari-platform-cart-item">
                    <div class="sari-platform-cart-item-thumb"></div>
                    <div class="sari-platform-cart-item-info">
                        <span class="sari-platform-cart-item-name">Braided rattan bag</span>
                        <span class="sari-platform-cart-item-price">&#8369;1,240</span>
                    </div>
                </div>

                <div class="sari-platform-cart-item">
                    <div class="sari-platform-cart-item-thumb"></div>
                    <div class="sari-platform-cart-item-info">
                        <span class="sari-platform-cart-item-name">Ceramic mug set</span>
                        <span class="sari-platform-cart-item-price">&#8369;890</span>
                    </div>
                </div>

                <hr class="sari-platform-cart-card-divider">

                <div class="sari-platform-cart-card-total">
                    <span class="sari-platform-cart-card-total-label">Total</span>
                    <span class="sari-platform-cart-card-total-value">&#8369;2,130</span>
                </div>

                <button class="sari-platform-cart-card-cta" type="button">Checkout &#8594;</button>

            </div>

            <div class="sari-platform-browser">
                <div class="sari-platform-browser-top">
                    <div class="sari-platform-browser-dots">
                        <span class="sari-platform-browser-dot"></span>
                        <span class="sari-platform-browser-dot"></span>
                        <span class="sari-platform-browser-dot"></span>
                    </div>
                </div>

                <div class="sari-platform-browser-content">
                    <aside class="sari-platform-mock-sidebar">
                        <div class="sari-platform-mock-brand"></div>
                        <div class="sari-platform-mock-nav-line active"></div>
                        <div class="sari-platform-mock-nav-line"></div>
                        <div class="sari-platform-mock-nav-line"></div>
                        <div class="sari-platform-mock-nav-line"></div>
                        <div class="sari-platform-mock-nav-line"></div>
                    </aside>

                    <div class="sari-platform-mock-main">
                        <div class="sari-platform-mock-heading"></div>
                        <div class="sari-platform-mock-subheading"></div>

                        <div class="sari-platform-mock-products">
                            <div class="sari-platform-mock-product">
                                <div class="sari-platform-mock-product-image"></div>
                                <div class="sari-platform-mock-product-info">
                                    <div class="sari-platform-mock-product-line"></div>
                                    <div class="sari-platform-mock-product-line small"></div>
                                </div>
                            </div>
                            <div class="sari-platform-mock-product">
                                <div class="sari-platform-mock-product-image"></div>
                                <div class="sari-platform-mock-product-info">
                                    <div class="sari-platform-mock-product-line"></div>
                                    <div class="sari-platform-mock-product-line small"></div>
                                </div>
                            </div>
                            <div class="sari-platform-mock-product">
                                <div class="sari-platform-mock-product-image"></div>
                                <div class="sari-platform-mock-product-info">
                                    <div class="sari-platform-mock-product-line"></div>
                                    <div class="sari-platform-mock-product-line small"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FLOATING PHONE WITH ANIMATED NOTIFICATION -->
            <div class="sari-platform-phone" aria-hidden="true">

                <div class="sari-platform-phone-notch"></div>

                <div class="sari-platform-phone-screen">

                    <div class="sari-platform-phone-statusbar">
                        <span class="sari-platform-phone-time">9:41</span>
                        <div class="sari-platform-phone-icons">
                            <div class="sari-platform-phone-signal">
                                <span></span><span></span><span></span><span></span>
                            </div>
                            <div class="sari-platform-phone-battery">
                                <div class="sari-platform-phone-battery-fill"></div>
                            </div>
                        </div>
                    </div>

                    <div class="sari-platform-phone-content">
                        <div class="sari-platform-phone-line"></div>
                        <div class="sari-platform-phone-line short"></div>
                        <div class="sari-platform-phone-row"></div>
                        <div class="sari-platform-phone-row"></div>
                        <div class="sari-platform-phone-row"></div>
                    </div>

                    <div class="sari-platform-phone-notif">
                        <div class="sari-platform-phone-notif-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7L12 13L20 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="sari-platform-phone-notif-text">
                            <span class="sari-platform-phone-notif-title">New order received</span>
                            <span class="sari-platform-phone-notif-sub">Wireless headphones &middot; &#8369;2,590</span>
                        </div>
                    </div>

                    <div class="sari-platform-phone-home-indicator"></div>

                </div>
            </div>

        </div>

    </div>

</section>




<style>
/* =========================================================
   SARI PLATFORM — REALISTIC MOTION ENHANCEMENT
   Quiet, premium, product-like micro interactions.
   ========================================================= */

/* Scroll reveal */
.sari-platform-ui.sari-platform-motion-ready .sari-platform-reveal {
    opacity: 0;
    transform: translate3d(0, 18px, 0);
    transition:
        opacity .75s cubic-bezier(.22,1,.36,1),
        transform .85s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}

.sari-platform-ui.sari-platform-motion-ready .sari-platform-reveal.is-visible {
    opacity: 1;
    transform: translate3d(0,0,0);
}

/* Stage settles like a real product showcase */
.sari-platform-browser {
    transform: perspective(1200px) rotateX(0deg) translateY(0);
    transform-origin: center bottom;
    transition:
        transform .7s cubic-bezier(.22,1,.36,1),
        box-shadow .55s ease,
        background-color .35s ease,
        border-color .35s ease;
}

.sari-platform-ui-stage:hover .sari-platform-browser {
    transform: perspective(1200px) rotateX(.7deg) translateY(-2px);
    box-shadow: 0 34px 72px -28px var(--sari-platform-shadow-soft);
}

/* Very subtle skeleton shimmer */
.sari-platform-mock-heading,
.sari-platform-mock-subheading,
.sari-platform-mock-nav-line,
.sari-platform-mock-product-image,
.sari-platform-mock-product-line,
.sari-platform-cart-item-thumb,
.sari-platform-phone-line,
.sari-platform-phone-row {
    position: relative;
    overflow: hidden;
}

.sari-platform-mock-heading::after,
.sari-platform-mock-subheading::after,
.sari-platform-mock-nav-line::after,
.sari-platform-mock-product-image::after,
.sari-platform-mock-product-line::after,
.sari-platform-cart-item-thumb::after,
.sari-platform-phone-line::after,
.sari-platform-phone-row::after {
    content: "";
    position: absolute;
    inset: 0;
    transform: translateX(-120%);
    background: linear-gradient(
        100deg,
        transparent 20%,
        rgba(255,255,255,.18) 48%,
        transparent 76%
    );
    animation: sari-platform-shimmer 5.8s ease-in-out infinite;
}

@keyframes sari-platform-shimmer {
    0%, 58% { transform: translateX(-120%); }
    78%, 100% { transform: translateX(120%); }
}

/* Product cards respond like UI tiles */
.sari-platform-mock-product {
    transition:
        transform .42s cubic-bezier(.22,1,.36,1),
        border-color .35s ease,
        box-shadow .42s ease,
        background-color .35s ease;
}

.sari-platform-mock-product:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -18px var(--sari-platform-shadow-soft);
}

/* Cart card: tiny rotation + depth instead of exaggerated float */
.sari-platform-cart-card {
    transform-origin: center center;
    animation: sari-platform-cart-float 7s ease-in-out infinite;
    transition:
        transform .45s cubic-bezier(.22,1,.36,1),
        box-shadow .45s ease,
        background-color .35s ease,
        border-color .35s ease;
}

@keyframes sari-platform-cart-float {
    0%, 100% { transform: translateY(0) rotate(-.25deg); }
    50% { transform: translateY(-6px) rotate(.2deg); }
}

.sari-platform-cart-card:hover {
    transform: translateY(-5px) rotate(0deg);
    box-shadow:
        0 28px 54px -18px var(--sari-platform-shadow-strong),
        0 5px 12px -4px var(--sari-platform-shadow-soft);
}

/* Cart CTA feels clickable */
.sari-platform-cart-card-cta {
    cursor: pointer;
    transition:
        transform .3s cubic-bezier(.22,1,.36,1),
        box-shadow .3s ease,
        filter .3s ease;
}

.sari-platform-cart-card-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(184,134,59,.18);
    filter: brightness(1.03);
}

.sari-platform-cart-card-cta:active {
    transform: translateY(0);
}

/* Cart badge gives one soft attention pulse */
.sari-platform-cart-card-badge {
    animation: sari-platform-badge-pulse 4.8s ease-in-out infinite;
}

@keyframes sari-platform-badge-pulse {
    0%, 70%, 100% { transform: scale(1); }
    78% { transform: scale(1.08); }
    86% { transform: scale(1); }
}

/* Phone floats independently and slightly slower */
.sari-platform-phone {
    animation: sari-platform-phone-float 7.6s ease-in-out infinite;
    transform-origin: 50% 65%;
    transition:
        transform .55s cubic-bezier(.22,1,.36,1),
        background-color .35s ease,
        box-shadow .45s ease;
}

@keyframes sari-platform-phone-float {
    0%, 100% { transform: translateY(0) rotate(.25deg); }
    50% { transform: translateY(-7px) rotate(-.2deg); }
}

.sari-platform-phone:hover {
    transform: translateY(-5px) rotate(0deg);
}

/* Notification: slower, more realistic enter/hold/exit */
.sari-platform-phone-notif {
    animation: sari-platform-phone-notif-real 6.8s cubic-bezier(.22,1,.36,1) infinite;
}

@keyframes sari-platform-phone-notif-real {
    0%, 8% {
        transform: translateY(-90px) scale(.98);
        opacity: 0;
    }
    16% {
        transform: translateY(11px) scale(1);
        opacity: 1;
    }
    21% {
        transform: translateY(9px) scale(1);
        opacity: 1;
    }
    78% {
        transform: translateY(9px) scale(1);
        opacity: 1;
    }
    88%, 100% {
        transform: translateY(-90px) scale(.985);
        opacity: 0;
    }
}

/* Notification icon gets a tiny confirmation pulse */
.sari-platform-phone-notif-icon {
    animation: sari-platform-notif-icon 6.8s ease-in-out infinite;
}

@keyframes sari-platform-notif-icon {
    0%, 15%, 100% { transform: scale(1); }
    19% { transform: scale(1.06); }
    24% { transform: scale(1); }
}

/* Active nav gets a faint breathing highlight */
.sari-platform-mock-nav-line.active {
    animation: sari-platform-nav-active 4.5s ease-in-out infinite;
}

@keyframes sari-platform-nav-active {
    0%, 100% { filter: brightness(1); }
    50% { filter: brightness(1.06); }
}

/* Keep interactions tasteful on mobile */
@media (max-width: 820px) {
    .sari-platform-ui-stage:hover .sari-platform-browser,
    .sari-platform-cart-card:hover,
    .sari-platform-phone:hover,
    .sari-platform-mock-product:hover {
        transform: none;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .sari-platform-ui.sari-platform-motion-ready .sari-platform-reveal,
    .sari-platform-browser,
    .sari-platform-cart-card,
    .sari-platform-phone,
    .sari-platform-phone-notif,
    .sari-platform-phone-notif-icon,
    .sari-platform-cart-card-badge,
    .sari-platform-mock-nav-line.active,
    .sari-platform-mock-product,
    .sari-platform-mock-heading::after,
    .sari-platform-mock-subheading::after,
    .sari-platform-mock-nav-line::after,
    .sari-platform-mock-product-image::after,
    .sari-platform-mock-product-line::after,
    .sari-platform-cart-item-thumb::after,
    .sari-platform-phone-line::after,
    .sari-platform-phone-row::after {
        animation: none !important;
        transition: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
}
</style>

<script>
(function () {
    const section = document.querySelector('.sari-platform-ui');
    if (!section) return;

    section.classList.add('sari-platform-motion-ready');

    const revealItems = section.querySelectorAll('.sari-platform-reveal');

    if (!('IntersectionObserver' in window)) {
        revealItems.forEach(el => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -7% 0px'
    });

    revealItems.forEach(el => observer.observe(el));
})();
</script>

</body>
</html>