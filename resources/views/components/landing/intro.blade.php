@once
    @push('styles')
        {{-- Flaticon UIcons used by the SARI Idea section --}}
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/4.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/4.0.0/uicons-regular-straight/css/uicons-regular-straight.css">
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/4.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/4.0.0/uicons-solid-straight/css/uicons-solid-straight.css">
    @endpush
@endonce

<style>
/* ==========================================================
   SARI IDEA SECTION
   ORIGINAL DESIGN PRESERVED
   Enhanced icons + DOUBLE rolling image rail only
   ========================================================== */

.sari-idea-section {
    position: relative;
    width: 100%;
    background: #faf8f3;
    color: #25221e;
    padding: 120px 0 0;
    overflow: hidden;
    font-family: 'Poppins', sans-serif;
}

.sari-idea-container {
    width: min(1200px, calc(100% - 48px));
    margin: auto;
}

/* ==========================================================
   HEADER — ORIGINAL LOOK
   ========================================================== */

.sari-idea-header {
    text-align: center;
}

.sari-idea-eyebrow {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    color: #c99128;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.sari-idea-line {
    width: 48px;
    height: 1px;
    background: rgba(201,145,40,.35);
}

.sari-idea-title {
    margin-top: 30px;
    font-size: clamp(38px,5vw,64px);
    line-height: 1.05;
    letter-spacing: -.05em;
    font-weight: 600;
    color: #211e19;
}

.sari-idea-title em {
    color: #c99128;
    font-style: normal;
}

.sari-idea-description {
    max-width: 650px;
    margin: 28px auto 0;
    color: #70695f;
    font-size: 16px;
    line-height: 1.8;
}

/* ==========================================================
   NETWORK — ORIGINAL LAYOUT PRESERVED
   ========================================================== */

.sari-idea-network {
    margin-top: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
}

.sari-idea-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 90px;
}

/* ==========================================================
   ICON ENHANCEMENT
   ========================================================== */

.sari-idea-icon {
    position: relative;
    isolation: isolate;
    width: 62px;
    height: 62px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg, #ffffff 0%, #fdfbf7 100%);
    border: 1px solid rgba(45,39,31,.12);
    color: #38332d;
    box-shadow: 0 8px 22px rgba(48,39,27,.055), inset 0 1px 0 rgba(255,255,255,.92);
    transition: transform .38s cubic-bezier(.22,1,.36,1), color .3s ease, border-color .3s ease, box-shadow .38s ease, background .3s ease;
}

.sari-idea-icon::before {
    content: "";
    position: absolute;
    inset: 5px;
    z-index: -1;
    border-radius: 50%;
    border: 1px solid rgba(201,145,40,.10);
    background: radial-gradient(circle at 35% 30%, rgba(201,145,40,.06), rgba(255,255,255,0) 62%);
    transition: border-color .3s ease, background .3s ease, transform .4s cubic-bezier(.22,1,.36,1);
}

.sari-idea-icon::after {
    content: "";
    position: absolute;
    top: 8px;
    right: 8px;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #c99128;
    box-shadow: 0 0 0 3px rgba(201,145,40,.08);
    opacity: .72;
    transition: transform .35s cubic-bezier(.22,1,.36,1), opacity .3s ease;
}

.sari-idea-icon svg {
    position: relative;
    z-index: 2;
    width: 25px;
    height: 25px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.65;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: transform .38s cubic-bezier(.22,1,.36,1);
}

.sari-idea-node:hover .sari-idea-icon {
    transform: translateY(-4px);
    color: #b77d18;
    border-color: rgba(201,145,40,.38);
    background: linear-gradient(145deg, #ffffff 0%, #fffaf0 100%);
    box-shadow: 0 15px 34px rgba(53,42,25,.10), 0 0 0 5px rgba(201,145,40,.045), inset 0 1px 0 rgba(255,255,255,.95);
}

.sari-idea-node:hover .sari-idea-icon::before {
    border-color: rgba(201,145,40,.22);
    transform: scale(.96);
}

.sari-idea-node:hover .sari-idea-icon::after {
    opacity: 1;
    transform: scale(1.25);
}

.sari-idea-node:hover .sari-idea-icon svg {
    transform: scale(1.055);
}

.sari-idea-label {
    margin-top: 14px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #756e64;
    transition: color .3s ease, transform .35s cubic-bezier(.22,1,.36,1);
}

.sari-idea-node:hover .sari-idea-label {
    color: #5f564b;
    transform: translateY(1px);
}

/* ==========================================================
   CONNECTOR
   ========================================================== */

.sari-idea-connector {
    position: relative;
    width: 120px;
    height: 1px;
    margin: 0 20px;
    background: linear-gradient(90deg, rgba(80,70,60,.08), rgba(80,70,60,.22) 50%, rgba(80,70,60,.08));
    overflow: hidden;
}

.sari-idea-connector::after {
    content: "";
    position: absolute;
    top: 0;
    left: -30%;
    width: 28%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(201,145,40,.75), transparent);
    animation: sariIdeaConnectorGlow 4.6s ease-in-out infinite;
}

@keyframes sariIdeaConnectorGlow {
    0%, 15% { left: -30%; opacity: 0; }
    25% { opacity: .75; }
    70% { opacity: .65; }
    85%, 100% { left: 105%; opacity: 0; }
}

/* ==========================================================
   CENTER LOGO
   ========================================================== */

.sari-idea-center {
    position: relative;
}

.sari-idea-logo {
    position: relative;
    isolation: isolate;
    width: 94px;
    height: 94px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 35% 28%, #39352d 0%, #292824 58%, #24231f 100%);
    border: 1px solid rgba(201,145,40,.42);
    box-shadow: 0 22px 52px rgba(0,0,0,.13), 0 0 0 7px rgba(201,145,40,.045);
    transition: transform .45s cubic-bezier(.22,1,.36,1), box-shadow .45s ease, border-color .45s ease;
}

.sari-idea-logo::before {
    content: "";
    position: absolute;
    inset: -15px;
    z-index: -1;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,145,40,.16) 0%, rgba(201,145,40,.055) 43%, transparent 72%);
    opacity: .85;
}

.sari-idea-logo::after {
    content: "";
    position: absolute;
    inset: 7px;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 50%;
    pointer-events: none;
}

.sari-idea-logo img {
    position: relative;
    z-index: 2;
    width: 49px;
    height: auto;
    display: block;
}

.sari-idea-center:hover .sari-idea-logo {
    transform: translateY(-5px) scale(1.035);
    border-color: rgba(201,145,40,.62);
    box-shadow: 0 26px 64px rgba(49,38,20,.17), 0 0 0 9px rgba(201,145,40,.06);
}

/* ==========================================================
   ORIGINAL SCROLL REVEAL
   ========================================================== */

.sari-idea-header,
.sari-idea-eyebrow,
.sari-idea-title,
.sari-idea-description,
.sari-idea-node,
.sari-idea-connector {
    opacity: 1;
    transform: none;
}

.sari-idea-section.sari-motion-ready .sari-idea-eyebrow,
.sari-idea-section.sari-motion-ready .sari-idea-title,
.sari-idea-section.sari-motion-ready .sari-idea-description {
    opacity: 0;
    transform: translate3d(0,22px,0);
    transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .85s cubic-bezier(.22,1,.36,1);
}

.sari-idea-section.sari-motion-ready .sari-idea-title {
    transform: translate3d(0,28px,0) scale(.985);
}

.sari-idea-section.sari-motion-ready .sari-idea-description {
    transform: translate3d(0,18px,0);
}

.sari-idea-section.sari-motion-ready .sari-idea-line {
    transform: scaleX(0);
    opacity: .25;
    transition: transform .8s cubic-bezier(.22,1,.36,1), opacity .6s ease;
}

.sari-idea-section.sari-motion-ready .sari-idea-line:first-child {
    transform-origin: right center;
}

.sari-idea-section.sari-motion-ready .sari-idea-line:last-child {
    transform-origin: left center;
}

.sari-idea-section.sari-motion-ready .sari-idea-node {
    opacity: 0;
    transform: translate3d(0,24px,0) scale(.94);
    transition: opacity .65s cubic-bezier(.22,1,.36,1), transform .8s cubic-bezier(.22,1,.36,1);
    transition-delay: var(--sari-delay,0ms);
}

.sari-idea-section.sari-motion-ready .sari-idea-connector {
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left center;
    transition: transform .75s cubic-bezier(.22,1,.36,1), opacity .45s ease;
    transition-delay: var(--sari-delay,0ms);
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-eyebrow,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-title,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-description,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-node {
    opacity: 1;
    transform: none;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-eyebrow { transition-delay: 80ms; }
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-title { transition-delay: 150ms; }
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-description { transition-delay: 230ms; }
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-line { transform: scaleX(1); opacity: 1; transition-delay: 120ms; }
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-connector { opacity: 1; transform: scaleX(1); }

/* ==========================================================
   RESPONSIVE
   ========================================================== */

@media (max-width:900px) {
    .sari-idea-network {
        flex-wrap: wrap;
        gap: 35px;
    }

    .sari-idea-connector {
        display: none;
    }
}

@media (max-width:600px) {
    .sari-idea-section {
        padding-top: 80px;
    }

    .sari-idea-container {
        width: calc(100% - 32px);
    }

    .sari-idea-title {
        font-size: 38px;
    }

    .sari-idea-network {
        display: grid;
        grid-template-columns: repeat(2,1fr);
    }

    .sari-idea-center {
        grid-column: 1/-1;
        order: -1;
    }

    .sari-idea-icon {
        width: 56px;
        height: 56px;
    }

    .sari-idea-logo {
        width: 84px;
        height: 84px;
    }
}

/* accessibility */
@media (prefers-reduced-motion: reduce) {
    .sari-idea-section.sari-motion-ready .sari-idea-eyebrow,
    .sari-idea-section.sari-motion-ready .sari-idea-title,
    .sari-idea-section.sari-motion-ready .sari-idea-description,
    .sari-idea-section.sari-motion-ready .sari-idea-node,
    .sari-idea-section.sari-motion-ready .sari-idea-connector,
    .sari-idea-line,
    .sari-idea-icon,
    .sari-idea-logo {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        animation: none !important;
    }

    .sari-idea-connector::after,
    .sari-idea-roll__track {
        animation: none !important;
    }

    .sari-idea-roll__viewport {
        overflow-x: auto;
    }
}

/* ==========================================================
   ICON REFINEMENT — CLEANER / LESS DECORATIVE
   ========================================================== */

.sari-idea-icon {
    position: relative !important;
    isolation: isolate !important;

    width: 60px !important;
    height: 60px !important;

    border-radius: 50% !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #ffffff !important;

    border: 1px solid rgba(35,31,26,.14) !important;

    color: #312d28 !important;

    box-shadow:
        0 8px 22px rgba(42,34,24,.055),
        inset 0 0 0 5px rgba(201,145,40,.035) !important;

    transition:
        transform .38s cubic-bezier(.22,1,.36,1),
        color .3s ease,
        border-color .3s ease,
        box-shadow .38s ease !important;
}

/* remove decorative dot and extra ring */
.sari-idea-icon::before,
.sari-idea-icon::after {
    display: none !important;
}

.sari-idea-icon svg {
    position: relative !important;
    z-index: 2 !important;

    width: 25px !important;
    height: 25px !important;

    fill: none !important;
    stroke: currentColor !important;
    stroke-width: 1.55 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;

    transition:
        transform .38s cubic-bezier(.22,1,.36,1) !important;
}

.sari-idea-node:hover .sari-idea-icon {
    transform: translateY(-4px) !important;

    color: #b77d18 !important;

    border-color: rgba(201,145,40,.34) !important;

    box-shadow:
        0 14px 30px rgba(48,39,27,.09),
        inset 0 0 0 5px rgba(201,145,40,.055) !important;
}

.sari-idea-node:hover .sari-idea-icon svg {
    transform: scale(1.06) !important;
}

@media (max-width:600px) {
    .sari-idea-icon {
        width: 54px !important;
        height: 54px !important;
    }

    .sari-idea-icon svg {
        width: 22px !important;
        height: 22px !important;
    }
}

</style>

<section class="sari-idea-section">

    <div class="sari-idea-container">

        <div class="sari-idea-header sari-idea-animate">
            <div class="sari-idea-eyebrow">
                <span class="sari-idea-line"></span>
                <span>The SARI Idea</span>
                <span class="sari-idea-line"></span>
            </div>

            <h2 class="sari-idea-title">
                Everyday shopping,
                <br>
                thoughtfully <em>elevated</em>.
            </h2>

            <p class="sari-idea-description">
                SARI connects products, sellers, customers,
                and delivery partners into one seamless marketplace.
                Built for convenience, discovery, and trust.
            </p>
        </div>

        <div class="sari-idea-network sari-idea-animate">

            <div class="sari-idea-node">
                <span class="sari-idea-icon" aria-hidden="true">
                    <i class="fi fi-rs-box-open"></i>
                </span>
                <span class="sari-idea-label">Products</span>
            </div>

            <span class="sari-idea-connector"></span>

            <div class="sari-idea-node">
                <span class="sari-idea-icon" aria-hidden="true">
                    <i class="fi fi-rr-seller"></i>
                </span>
                <span class="sari-idea-label">Sellers</span>
            </div>

            <span class="sari-idea-connector"></span>

            <div class="sari-idea-node sari-idea-center">
                <span class="sari-idea-logo">
                    <img src="{{ asset('images/sari-logo.png') }}" alt="SARI">
                </span>
            </div>

            <span class="sari-idea-connector"></span>

            <div class="sari-idea-node">
                <span class="sari-idea-icon" aria-hidden="true">
                    <i class="fi fi-rs-shipping-fast"></i>
                </span>
                <span class="sari-idea-label">Delivery</span>
            </div>

            <span class="sari-idea-connector"></span>

            <div class="sari-idea-node">
                <span class="sari-idea-icon" aria-hidden="true">
                    <i class="fi fi-rs-users-alt"></i>
                </span>
                <span class="sari-idea-label">People</span>
            </div>

        </div>
    </div>

    @php
        $sariIdeaRollingImages = [
            ['src' => 'images/cat-fashion.jpg',     'alt' => 'Fashion'],
            ['src' => 'images/cat-electronics.jpg', 'alt' => 'Electronics'],
            ['src' => 'images/cat-home.jpg',        'alt' => 'Home and Living'],
            ['src' => 'images/cat-beauty.jpg',      'alt' => 'Beauty'],
            ['src' => 'images/cat-accessories.jpg', 'alt' => 'Accessories'],
            ['src' => 'images/cat-food.jpg',        'alt' => 'Food and Essentials'],
            ['src' => 'images/cat-sports.jpg',      'alt' => 'Sports'],
            ['src' => 'images/cat-lifestyle.jpg',   'alt' => 'Lifestyle'],
        ];
    @endphp

    <div class="sari-idea-roll">
        <div class="sari-idea-roll__viewport">
            <div class="sari-idea-roll__track">

                <div class="sari-idea-roll__group">
                    @foreach ($sariIdeaRollingImages as $image)
                        <div class="sari-idea-roll__item">
                            <img
                                src="{{ asset($image['src']) }}"
                                alt="{{ $image['alt'] }}"
                                loading="lazy"
                            >

                            <div class="sari-idea-roll__label">
                                <span class="sari-idea-roll__icon" aria-hidden="true">
                                    @switch($image['alt'])

                                        @case('Fashion')
                                            <i class="fi fi-sr-vest-patches"></i>
                                            @break

                                        @case('Electronics')
                                            <i class="fi fi-ss-console-controller"></i>
                                            @break

                                        @case('Home and Living')
                                            <i class="fi fi-ss-house-hands"></i>
                                            @break

                                        @case('Beauty')
                                            <i class="fi fi-sr-blush"></i>
                                            @break

                                        @case('Accessories')
                                            <i class="fi fi-sr-diamond"></i>
                                            @break

                                        @case('Food and Essentials')
                                            <i class="fi fi-ss-grocery-basket"></i>
                                            @break

                                        @case('Sports')
                                            <i class="fi fi-ss-gym"></i>
                                            @break

                                        @case('Lifestyle')
                                            <i class="fi fi-sr-couch"></i>
                                            @break

                                        @default
                                            <i class="fi fi-sr-grid"></i>

                                    @endswitch
                                </span>

                                <strong>{{ $image['alt'] }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="sari-idea-roll__group" aria-hidden="true">
                    @foreach ($sariIdeaRollingImages as $image)
                        <div class="sari-idea-roll__item">
                            <img
                                src="{{ asset($image['src']) }}"
                                alt=""
                                loading="lazy"
                            >

                            <div class="sari-idea-roll__label">
                                <span class="sari-idea-roll__icon" aria-hidden="true">
                                    @switch($image['alt'])

                                        @case('Fashion')
                                            <i class="fi fi-sr-vest-patches"></i>
                                            @break

                                        @case('Electronics')
                                            <i class="fi fi-ss-console-controller"></i>
                                            @break

                                        @case('Home and Living')
                                            <i class="fi fi-ss-house-hands"></i>
                                            @break

                                        @case('Beauty')
                                            <i class="fi fi-sr-blush"></i>
                                            @break

                                        @case('Accessories')
                                            <i class="fi fi-sr-diamond"></i>
                                            @break

                                        @case('Food and Essentials')
                                            <i class="fi fi-ss-grocery-basket"></i>
                                            @break

                                        @case('Sports')
                                            <i class="fi fi-ss-gym"></i>
                                            @break

                                        @case('Lifestyle')
                                            <i class="fi fi-sr-couch"></i>
                                            @break

                                        @default
                                            <i class="fi fi-sr-grid"></i>

                                    @endswitch
                                </span>

                                <strong>{{ $image['alt'] }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</section>


<style>
/* ==========================================================
   SINGLE ROLLING TICKET — EDGE TO EDGE
   - one row only
   - no side fades
   - no vertical padding
   - no gaps
   - black frame
   - white base
   ========================================================== */

.sari-idea-roll {
    position: relative;
    width: 100%;

    margin: 70px 0 0 !important;
    padding: 0 !important;

    background: #ffffff !important;

    border-top: 1px solid #111111;
    border-bottom: 1px solid #111111;

    overflow: hidden;
}

/* remove left/right fade masks completely */
.sari-idea-roll::before,
.sari-idea-roll::after {
    display: none !important;
    content: none !important;
}

.sari-idea-roll__viewport {
    width: 100%;
    overflow: hidden;
    background: #ffffff;
}

.sari-idea-roll__track {
    display: flex;

    width: max-content;

    gap: 0 !important;

    animation: sariIdeaSingleTicket 34s linear infinite;

    will-change: transform;
}

.sari-idea-roll__group {
    display: flex;

    flex: 0 0 auto;

    gap: 0 !important;
}

.sari-idea-roll__item {
    position: relative;

    width: 176px;
    height: 108px;

    flex: 0 0 auto;

    overflow: hidden;

    margin: 0 !important;
    padding: 0 !important;

    border-top: 0;
    border-bottom: 0;
    border-left: 1px solid #111111;
    border-right: 0;

    border-radius: 0 !important;

    background: #ffffff;
}

/* final tile keeps the right frame */
.sari-idea-roll__group .sari-idea-roll__item:last-child {
    border-right: 1px solid #111111;
}

.sari-idea-roll__item img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    margin: 0;
    padding: 0;

    transform: scale(1.001);

    transition:
        transform .5s cubic-bezier(.22,1,.36,1);
}





@keyframes sariIdeaSingleTicket {
    from {
        transform: translate3d(0,0,0);
    }

    to {
        transform: translate3d(-50%,0,0);
    }
}

@media (max-width:600px) {
    .sari-idea-roll {
        margin-top: 48px !important;
    }

    .sari-idea-roll__item {
        width: 124px;
        height: 78px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-idea-roll__track {
        animation: none !important;
    }

    .sari-idea-roll__viewport {
        overflow-x: auto;
    }
}
</style>



<style>
/* ==========================================================
   SARI IDEA → ABOUT SARI
   FINAL NO-GAP FIX

   Root cause:
   public/css/landing-compact.css gives .sari-idea-section
   padding-bottom via the shared section rhythm.

   This component-level rule loads later and overrides ONLY
   the bottom spacing of the SARI Idea section.
   ========================================================== */

html body .sari-idea-section {
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}

html body .sari-idea-section .sari-idea-roll {
    margin-bottom: 0 !important;
}

/* About starts immediately after the ticket strip. */
html body .sari-idea-section + .sari-about-v2 {
    margin-top: 0 !important;
}

/* Prevent a tiny visual seam from default line/layout behavior. */
html body .sari-idea-section .sari-idea-roll__viewport,
html body .sari-idea-section .sari-idea-roll__track,
html body .sari-idea-section .sari-idea-roll__group,
html body .sari-idea-section .sari-idea-roll__item {
    margin-bottom: 0 !important;
}
</style>







<style>
/* ==========================================================
   SARI ROLLING TICKET — SEPARATED WHITE CARDS V4

   Inspired by the reference:
   - every item has its own white card
   - very small spacing between cards
   - warm neutral rail background
   - no black frame
   - no colored border
   - centered unique icon + Poppins label
   - hover = image only
   - animation never pauses
   ========================================================== */

.sari-idea-roll {
    background: #f5f3ee !important;
    padding: 8px 0 !important;
}

/* Continuous motion at all times. */
.sari-idea-roll .sari-idea-roll__track,
.sari-idea-roll:hover .sari-idea-roll__track,
.sari-idea-roll__viewport:hover .sari-idea-roll__track {
    animation-name: sariIdeaSingleTicket !important;
    animation-duration: 34s !important;
    animation-timing-function: linear !important;
    animation-delay: 0s !important;
    animation-iteration-count: infinite !important;
    animation-direction: normal !important;
    animation-play-state: running !important;
}

/* Small, consistent gap like the reference. */
.sari-idea-roll__track,
.sari-idea-roll__group {
    gap: 8px !important;
}

.sari-idea-roll__item {
    position: relative !important;

    width: 188px !important;
    height: 118px !important;

    flex: 0 0 auto !important;

    margin: 0 !important;
    padding: 0 !important;

    overflow: hidden !important;

    background: #ffffff !important;

    border: 0 !important;
    outline: 0 !important;

    /* Reference is almost square; keep only a tiny softness. */
    border-radius: 2px !important;

    box-shadow: 0 1px 0 rgba(31, 25, 19, .025) !important;

    font-family: 'Poppins', sans-serif !important;
}

/* Remove any legacy frame pseudo-elements. */
.sari-idea-roll__item::before,
.sari-idea-roll__item::after {
    content: none !important;
    display: none !important;
}

/* Image layer underneath. */
.sari-idea-roll__item img {
    position: absolute !important;
    inset: 0 !important;

    width: 100% !important;
    height: 100% !important;

    display: block !important;

    object-fit: cover !important;

    opacity: 0 !important;
    transform: scale(1.015) !important;

    border: 0 !important;

    transition:
        opacity .22s ease,
        transform .34s cubic-bezier(.22,1,.36,1) !important;

    pointer-events: none !important;
}

/* Default face: icon + category text. */
.sari-idea-roll__label {
    position: absolute !important;
    inset: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    gap: 11px !important;

    padding: 16px !important;

    background: #ffffff !important;

    color: #29241f !important;

    border: 0 !important;

    opacity: 1 !important;
    visibility: visible !important;

    font-family: 'Poppins', sans-serif !important;

    transition:
        opacity .18s ease,
        visibility .18s ease !important;

    pointer-events: none !important;
}

.sari-idea-roll__icon {
    width: 24px !important;
    height: 24px !important;

    flex: 0 0 24px !important;

    display: grid !important;
    place-items: center !important;

    color: #4a4139 !important;

    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
}

.sari-idea-roll__icon svg {
    width: 21px !important;
    height: 21px !important;

    fill: none !important;
    stroke: currentColor !important;
    stroke-width: 1.55 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
}

.sari-idea-roll__label strong {
    max-width: 124px !important;

    color: #342d27 !important;

    font-family: 'Poppins', sans-serif !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;
    letter-spacing: -.01em !important;

    text-align: left !important;
}

/* Desktop hover: image only. */
.sari-idea-roll__item:hover img {
    opacity: 1 !important;
    transform: scale(1) !important;
}

.sari-idea-roll__item:hover .sari-idea-roll__label {
    opacity: 0 !important;
    visibility: hidden !important;
}

/* Mobile/touch: direct image state. */
@media (hover: none), (pointer: coarse), (max-width: 600px) {
    .sari-idea-roll {
        padding: 6px 0 !important;
    }

    .sari-idea-roll__track,
    .sari-idea-roll__group {
        gap: 6px !important;
    }

    .sari-idea-roll__item {
        width: 132px !important;
        height: 82px !important;

        border-radius: 2px !important;
    }

    .sari-idea-roll__item img {
        opacity: 1 !important;
        transform: none !important;
    }

    .sari-idea-roll__label {
        display: none !important;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-idea-roll__track {
        animation: none !important;
    }
}
</style>



<style>
/* ==========================================================
   ROLLING RAIL — REMOVE TOP/BOTTOM LINES
   ========================================================== */

.sari-idea-roll {
    border-top: 0 !important;
    border-bottom: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
}
</style>



<style>
/* ==========================================================
   FLATICON UICONS — SARI IDEA INTEGRATION
   ========================================================== */

/* Main ecosystem Flaticon icons */
.sari-idea-icon > i.fi {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: auto !important;
    height: auto !important;

    margin: 0 !important;
    padding: 0 !important;

    color: currentColor !important;

    font-size: 25px !important;
    line-height: 1 !important;

    vertical-align: middle !important;

    transition:
        transform .38s cubic-bezier(.22,1,.36,1) !important;
}

.sari-idea-icon > i.fi::before {
    display: block !important;
    line-height: 1 !important;
    margin: 0 !important;
}

/* Keep the same subtle hover behavior as the previous SVG icons */
.sari-idea-node:hover .sari-idea-icon > i.fi {
    transform: scale(1.06) !important;
}

/* Rolling-card Flaticon icons */
.sari-idea-roll__icon > i.fi {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 24px !important;
    height: 24px !important;

    margin: 0 !important;
    padding: 0 !important;

    color: #4a4139 !important;

    font-size: 21px !important;
    line-height: 1 !important;

    vertical-align: middle !important;
}

.sari-idea-roll__icon > i.fi::before {
    display: block !important;
    line-height: 1 !important;
    margin: 0 !important;
}

/* Existing SVG fallback for Electronics + Food stays visually aligned */
.sari-idea-roll__icon > svg {
    width: 21px !important;
    height: 21px !important;

    fill: none !important;
    stroke: #4a4139 !important;
    stroke-width: 1.55 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
}

@media (max-width: 600px) {
    .sari-idea-icon > i.fi {
        font-size: 22px !important;
    }
}
</style>



<style>
/* ==========================================================
   SARI IDEA — CLEAN FLAT NETWORK FINAL
   ========================================================== */

/* ----------------------------------------------------------
   MAIN NETWORK ICONS
   - no circles
   - black only
   - flat / minimal
   ---------------------------------------------------------- */

.sari-idea-icon {
    width: auto !important;
    height: auto !important;

    min-width: 40px !important;
    min-height: 40px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    color: #111111 !important;

    transition:
        transform .25s ease,
        opacity .25s ease !important;
}

/* kill every decorative circle/ring/dot */
.sari-idea-icon::before,
.sari-idea-icon::after {
    content: none !important;
    display: none !important;
}

/* Flaticon main network icons */
.sari-idea-icon > i.fi {
    width: auto !important;
    height: auto !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 !important;

    color: #111111 !important;

    font-size: 24px !important;
    line-height: 1 !important;

    transform: none !important;

    transition:
        transform .25s ease,
        opacity .25s ease !important;
}

.sari-idea-icon > i.fi::before {
    display: block !important;
    margin: 0 !important;
    line-height: 1 !important;
}

/* subtle professional hover only */
.sari-idea-node:hover .sari-idea-icon {
    transform: translateY(-2px) !important;

    color: #111111 !important;

    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

.sari-idea-node:hover .sari-idea-icon > i.fi {
    transform: scale(1.04) !important;
}

/* ----------------------------------------------------------
   LABELS
   ---------------------------------------------------------- */

.sari-idea-label {
    margin-top: 16px !important;

    color: #222222 !important;

    font-family: 'Poppins', sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    letter-spacing: .18em !important;
    text-transform: uppercase !important;

    transition:
        color .2s ease,
        transform .2s ease !important;
}

.sari-idea-node:hover .sari-idea-label {
    color: #111111 !important;
    transform: none !important;
}

/* ----------------------------------------------------------
   CONNECTOR LINES
   - flat solid neutral line
   - remove gradient + moving gold highlight
   ---------------------------------------------------------- */

.sari-idea-connector {
    width: 120px !important;
    height: 1px !important;

    margin: 0 20px !important;

    background: #dedbd5 !important;

    overflow: visible !important;
}

.sari-idea-connector::after {
    content: none !important;
    display: none !important;
}

/* ----------------------------------------------------------
   CENTER SARI HUB
   - flat black circle
   - no gradient / halo / glow
   ---------------------------------------------------------- */

.sari-idea-logo {
    width: 104px !important;
    height: 104px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #171717 !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    transform: none !important;

    transition:
        transform .28s ease !important;
}

/* remove decorative halo/ring */
.sari-idea-logo::before,
.sari-idea-logo::after {
    content: none !important;
    display: none !important;
}

.sari-idea-logo img {
    width: 52px !important;
    height: auto !important;

    display: block !important;
}

.sari-idea-center:hover .sari-idea-logo {
    transform: scale(1.025) !important;

    border: 0 !important;
    box-shadow: none !important;
}

/* ----------------------------------------------------------
   ROLLING CARD ICONS
   ---------------------------------------------------------- */

.sari-idea-roll__icon > i.fi {
    width: 24px !important;
    height: 24px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 !important;

    color: #111111 !important;

    font-size: 21px !important;
    line-height: 1 !important;
}

.sari-idea-roll__icon > i.fi::before {
    display: block !important;
    margin: 0 !important;
    line-height: 1 !important;
}

/* Existing SVG fallback (Food & Essentials) also black only */
.sari-idea-roll__icon > svg {
    stroke: #111111 !important;
}

/* ----------------------------------------------------------
   MOBILE
   ---------------------------------------------------------- */

@media (max-width: 600px) {
    .sari-idea-icon {
        min-width: 34px !important;
        min-height: 34px !important;
    }

    .sari-idea-icon > i.fi {
        font-size: 27px !important;
    }

    .sari-idea-logo {
        width: 88px !important;
        height: 88px !important;
    }

    .sari-idea-logo img {
        width: 45px !important;
    }

    .sari-idea-label {
        margin-top: 12px !important;
        font-size: 10px !important;
        letter-spacing: .15em !important;
    }
}
</style>




<style>
/* ==========================================================
   ROLLING TICKET — WHITE ONLY + ACCURATE POINTER HOVER
   ========================================================== */

/* Remove the gray strip completely. */
.sari-idea-roll,
.sari-idea-roll__viewport,
.sari-idea-roll__track,
.sari-idea-roll__group {
    background: transparent !important;
}

.sari-idea-roll {
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
}

/* Keep only the individual white cards visible. */
.sari-idea-roll__item {
    background: #ffffff !important;
    border: 0 !important;
    outline: 0 !important;
    box-shadow: 0 0 0 1px rgba(25, 22, 18, .045) !important;
}

/*
 * Disable CSS hover as the source of truth.
 * Because the marquee itself is moving, :hover can lag behind a
 * stationary mouse pointer in some browsers.
 */
@media (hover: hover) and (pointer: fine) {
    .sari-idea-roll__item:hover img {
        opacity: 0 !important;
        transform: scale(1.015) !important;
    }

    .sari-idea-roll__item:hover .sari-idea-roll__label {
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Only the JS-detected card directly under the pointer animates. */
    .sari-idea-roll__item.is-pointer-active img {
        opacity: 1 !important;
        transform: scale(1) !important;
    }

    .sari-idea-roll__item.is-pointer-active .sari-idea-roll__label {
        opacity: 0 !important;
        visibility: hidden !important;
    }
}
</style>


<script>
/* ==========================================================
   ACCURATE MOVING-MARQUEE POINTER DETECTION
   The active card is recalculated every animation frame while
   the mouse is inside the rolling viewport.
   ========================================================== */
(function () {
    const viewport = document.querySelector('.sari-idea-roll__viewport');

    if (!viewport || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        return;
    }

    let pointerX = 0;
    let pointerY = 0;
    let pointerInside = false;
    let activeItem = null;
    let frameId = null;

    function clearActiveItem() {
        if (activeItem) {
            activeItem.classList.remove('is-pointer-active');
            activeItem = null;
        }
    }

    function updateActiveItem() {
        if (!pointerInside) {
            frameId = null;
            clearActiveItem();
            return;
        }

        const hit = document.elementFromPoint(pointerX, pointerY);
        const nextItem = hit ? hit.closest('.sari-idea-roll__item') : null;

        if (nextItem && viewport.contains(nextItem)) {
            if (nextItem !== activeItem) {
                clearActiveItem();
                activeItem = nextItem;
                activeItem.classList.add('is-pointer-active');
            }
        } else {
            clearActiveItem();
        }

        frameId = requestAnimationFrame(updateActiveItem);
    }

    viewport.addEventListener('pointerenter', function (event) {
        pointerInside = true;
        pointerX = event.clientX;
        pointerY = event.clientY;

        if (frameId === null) {
            frameId = requestAnimationFrame(updateActiveItem);
        }
    });

    viewport.addEventListener('pointermove', function (event) {
        pointerX = event.clientX;
        pointerY = event.clientY;
    }, { passive: true });

    viewport.addEventListener('pointerleave', function () {
        pointerInside = false;
        clearActiveItem();
    });

    window.addEventListener('blur', function () {
        pointerInside = false;
        clearActiveItem();
    });
})();
</script>



<style>
/* ==========================================================
   ROLLING TICKET — MATCH SECTION BACKGROUND FINAL

   Goal:
   - no separate gray strip behind the marquee
   - rail inherits the exact SARI Idea section background
   - only the individual cards stay white
   - preserve spacing + accurate hover behavior
   ========================================================== */

/* Parent section remains the single source of background color. */
.sari-idea-section {
    background: #faf8f3 !important;
}

/*
 * Every marquee wrapper is transparent, so the section background
 * shows through naturally. This removes the gray band completely.
 */
.sari-idea-roll,
.sari-idea-roll__viewport,
.sari-idea-roll__track,
.sari-idea-roll__group {
    background: transparent !important;
}

/* Remove any rail/frame styling that can create a separate strip. */
.sari-idea-roll,
.sari-idea-roll__viewport {
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
}

/* No side fades / overlay layers. */
.sari-idea-roll::before,
.sari-idea-roll::after,
.sari-idea-roll__viewport::before,
.sari-idea-roll__viewport::after {
    content: none !important;
    display: none !important;
}

/*
 * Keep only a little breathing room around the white cards.
 * Because the wrapper is transparent, this padding shows the same
 * cream background as the SARI Idea section.
 */
.sari-idea-roll {
    padding: 10px 0 !important;
}

/* Clean spacing between individual white containers. */
.sari-idea-roll__track,
.sari-idea-roll__group {
    gap: 12px !important;
}

/* White cards are the only visible containers. */
.sari-idea-roll__item {
    background: #ffffff !important;

    border: 0 !important;
    outline: 0 !important;

    border-radius: 2px !important;

    box-shadow:
        0 0 0 1px rgba(24, 20, 15, .035),
        0 1px 2px rgba(24, 20, 15, .02) !important;
}

/* Default card face stays pure white. */
.sari-idea-roll__label {
    background: #ffffff !important;
    border: 0 !important;
}

/* Keep legacy frame pseudo-elements disabled. */
.sari-idea-roll__item::before,
.sari-idea-roll__item::after {
    content: none !important;
    display: none !important;
}

/* Mobile keeps the same background relationship and tighter spacing. */
@media (hover: none), (pointer: coarse), (max-width: 600px) {
    .sari-idea-roll {
        padding: 8px 0 !important;
    }

    .sari-idea-roll__track,
    .sari-idea-roll__group {
        gap: 8px !important;
    }
}
</style>


<script>
(function () {
    const section = document.querySelector('.sari-idea-section');

    if (!section) {
        return;
    }

    section.classList.add('sari-motion-ready');

    const networkItems = section.querySelectorAll(
        '.sari-idea-network > .sari-idea-node, .sari-idea-network > .sari-idea-connector'
    );

    networkItems.forEach(function (item, index) {
        item.style.setProperty('--sari-delay', (320 + (index * 85)) + 'ms');
    });

    if (!('IntersectionObserver' in window)) {
        section.classList.add('is-visible');
        return;
    }

    const observer = new IntersectionObserver(
        function (entries, currentObserver) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                section.classList.add('is-visible');
                currentObserver.unobserve(section);
            });
        },
        {
            threshold: 0.22,
            rootMargin: '0px 0px -8% 0px'
        }
    );

    observer.observe(section);
})();
</script>


<style>
/* ==========================================================
   ROLLING TICKET — SPACED CARD LAYOUT
   Adds visible gaps between cards like the reference.
   ========================================================== */

.sari-idea-roll {
    background: #faf8f3 !important;
    padding: 12px 0 !important;
}

.sari-idea-roll__viewport {
    background: transparent !important;
}

.sari-idea-roll__track,
.sari-idea-roll__group {
    gap: 12px !important;
}

.sari-idea-roll__item {
    width: 190px !important;
    height: 118px !important;
    border: 0 !important;
    border-radius: 2px !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 1px rgba(22, 18, 13, 0.04), 0 1px 2px rgba(22, 18, 13, 0.02) !important;
}

.sari-idea-roll__label {
    padding: 18px !important;
    justify-content: flex-start !important;
}

.sari-idea-roll__label strong {
    max-width: 130px !important;
}

@media (hover: none), (pointer: coarse), (max-width: 600px) {
    .sari-idea-roll {
        padding: 8px 0 !important;
    }

    .sari-idea-roll__track,
    .sari-idea-roll__group {
        gap: 8px !important;
    }

    .sari-idea-roll__item {
        width: 136px !important;
        height: 84px !important;
        box-shadow: 0 0 0 1px rgba(22, 18, 13, 0.04) !important;
    }
}
</style>


<style>
/* ==========================================================
   FINAL ROLLING RAIL — EXACT BG + MOBILE TAP
   ========================================================== */

/* Exact same background as the parent SARI Idea section. */
html body .sari-idea-section,
html body .sari-idea-section .sari-idea-roll,
html body .sari-idea-section .sari-idea-roll__viewport,
html body .sari-idea-section .sari-idea-roll__track,
html body .sari-idea-section .sari-idea-roll__group {
    background-color: #faf8f3 !important;
    background-image: none !important;
}

/* Rail itself has no visible container styling. */
html body .sari-idea-section .sari-idea-roll,
html body .sari-idea-section .sari-idea-roll__viewport {
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
}

/* Only the individual cards are white. */
html body .sari-idea-section .sari-idea-roll__item,
html body .sari-idea-section .sari-idea-roll__label {
    background: #ffffff !important;
}

/* Keep clean spacing between cards. */
html body .sari-idea-section .sari-idea-roll__track,
html body .sari-idea-section .sari-idea-roll__group {
    gap: 12px !important;
}

/* ==========================================================
   MOBILE / TOUCH
   Default = icon + text.
   Tap      = image.
   ========================================================== */
@media (hover: none), (pointer: coarse), (max-width: 600px) {

    /* Neutralize the old mobile image-only rule. */
    html body .sari-idea-section .sari-idea-roll__item img,
    html body .sari-idea-section .sari-idea-roll__item:hover img {
        opacity: 0 !important;
        transform: scale(1.015) !important;
    }

    html body .sari-idea-section .sari-idea-roll__label,
    html body .sari-idea-section .sari-idea-roll__item:hover .sari-idea-roll__label {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Only a tapped card reveals its image. */
    html body .sari-idea-section .sari-idea-roll__item.is-tapped img {
        opacity: 1 !important;
        transform: scale(1) !important;
    }

    html body .sari-idea-section .sari-idea-roll__item.is-tapped .sari-idea-roll__label {
        display: flex !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    html body .sari-idea-section .sari-idea-roll__track,
    html body .sari-idea-section .sari-idea-roll__group {
        gap: 8px !important;
    }
}
</style>



<script>
/* ==========================================================
   MOBILE TAP-TO-IMAGE
   - tap exact card = show image
   - tap same card again = restore text/icon
   - tap another card = move active state
   - tap outside = restore all cards
   ========================================================== */
(function () {
    const viewport = document.querySelector('.sari-idea-roll__viewport');

    if (!viewport) {
        return;
    }

    const touchQuery = window.matchMedia('(hover: none), (pointer: coarse), (max-width: 600px)');

    function isTouchLayout() {
        return touchQuery.matches;
    }

    function clearTapped(exceptItem) {
        viewport.querySelectorAll('.sari-idea-roll__item.is-tapped').forEach(function (item) {
            if (item !== exceptItem) {
                item.classList.remove('is-tapped');
            }
        });
    }

    viewport.addEventListener('click', function (event) {
        if (!isTouchLayout()) {
            return;
        }

        const item = event.target.closest('.sari-idea-roll__item');

        if (!item || !viewport.contains(item)) {
            return;
        }

        const wasTapped = item.classList.contains('is-tapped');

        clearTapped();

        if (wasTapped) {
            item.classList.remove('is-tapped');
        } else {
            item.classList.add('is-tapped');
        }
    });

    document.addEventListener('click', function (event) {
        if (!isTouchLayout()) {
            return;
        }

        if (!viewport.contains(event.target)) {
            clearTapped();
        }
    });

    touchQuery.addEventListener?.('change', function () {
        if (!isTouchLayout()) {
            clearTapped();
        }
    });
})();
</script>

