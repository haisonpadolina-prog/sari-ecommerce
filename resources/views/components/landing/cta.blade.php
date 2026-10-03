{{-- resources/views/components/cta.blade.php --}}
<section
    class="sari-cta-v2"
    id="register"
    aria-labelledby="sari-cta-v2-title"
>
    <div class="sari-cta-v2__overlay"></div>

    <div class="sari-cta-v2__container">
        <div class="sari-cta-v2__content">

            <div class="sari-cta-v2__brand sari-cta-v2__reveal">
                <img
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    class="sari-cta-v2__logo"
                >

                <span class="sari-cta-v2__eyebrow">
                    Curated · Local · Everyday
                </span>
            </div>

            <h1
                class="sari-cta-v2__title sari-cta-v2__reveal"
                id="sari-cta-v2-title"
            >
                Elevate the way
                <br>
                you <span>shop.</span>
            </h1>

            <p class="sari-cta-v2__description sari-cta-v2__reveal">
                Discover a more connected marketplace built for everyday
                shopping — from trusted sellers to convenient delivery.
            </p>

            <div class="sari-cta-v2__actions sari-cta-v2__reveal">
                <a href="{{ route('login') }}" class="sari-cta-v2__primary">
                    Enter SARI
                    <span aria-hidden="true">→</span>
                </a>

                <a href="#categories" class="sari-cta-v2__secondary">
                    Explore categories
                    <span aria-hidden="true">↗</span>
                </a>
            </div>

            <div class="sari-cta-v2__trust sari-cta-v2__reveal">
                <span><i></i>Verified Sellers</span>
                <span><i></i>Secure Shopping</span>
                <span><i></i>Connected Delivery</span>
            </div>
        </div>
    </div>

    <div class="sari-cta-v2__floating sari-cta-v2__reveal" aria-hidden="true">
        <div class="sari-cta-v2__floating-icon">
            <svg viewBox="0 0 24 24">
                <path d="M4 7h16l-1 13H5L4 7Z"/>
                <path d="M8 9V6a4 4 0 0 1 8 0v3"/>
            </svg>
        </div>

        <div>
            <small>MARKETPLACE</small>
            <strong>Curated for everyday life.</strong>
        </div>
    </div>
</section>

<style>
.sari-cta-v2,
.sari-cta-v2 *,
.sari-cta-v2 *::before,
.sari-cta-v2 *::after {
    box-sizing: border-box;
}

.sari-cta-v2 {
    --cta-gold: #c98a08;
    --cta-gold-dark: #b77b05;
    --cta-ink: #111111;
    --cta-muted: #625d55;
    --cta-soft: #91897d;

    position: relative;
    width: 100%;
    min-height: 760px;
    display: flex;
    align-items: center;
    overflow: hidden;
    isolation: isolate;
    color: var(--cta-ink);
    font-family: 'Poppins', sans-serif;

    background-image: url("{{ asset('images/sari-hero-bg.png') }}");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 56% center;
}

.sari-cta-v2__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    background: linear-gradient(
        90deg,
        rgba(255,253,249,.995) 0%,
        rgba(255,253,249,.985) 28%,
        rgba(255,253,249,.90) 43%,
        rgba(255,253,249,.50) 59%,
        rgba(255,253,249,.10) 77%,
        rgba(255,253,249,0) 100%
    );
}

.sari-cta-v2__container {
    position: relative;
    z-index: 3;
    width: min(1440px, 100%);
    margin: 0 auto;
    padding: 86px 7%;
}

.sari-cta-v2__content {
    width: min(650px, 52vw);
}

.sari-cta-v2__brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 40px;
}

.sari-cta-v2__logo {
    display: block;
    width: 182px;
    height: auto;
    object-fit: contain;
    filter: brightness(0);
}

.sari-cta-v2__eyebrow {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding-left: 39px;
    color: var(--cta-gold);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .27em;
    text-transform: uppercase;
}

.sari-cta-v2__eyebrow::before {
    content: "";
    position: absolute;
    left: 0;
    width: 27px;
    height: 1px;
    background: currentColor;
}

.sari-cta-v2__title {
    max-width: 650px;
    margin: 0;
    color: var(--cta-ink);
    font-size: clamp(62px, 5.15vw, 90px);
    font-weight: 700;
    line-height: .97;
    letter-spacing: -.062em;
}

.sari-cta-v2__title span {
    color: var(--cta-gold);
}

.sari-cta-v2__description {
    width: min(570px, 100%);
    margin: 28px 0 0;
    color: var(--cta-muted);
    font-size: 16px;
    line-height: 1.75;
}

.sari-cta-v2__actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 25px;
    margin-top: 34px;
}

.sari-cta-v2__primary {
    min-width: 190px;
    height: 54px;
    padding: 0 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    border: 1px solid var(--cta-gold);
    border-radius: 8px;
    background: var(--cta-gold);
    color: #fff !important;
    text-decoration: none;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .09em;
    transition: transform .3s ease, background .3s ease, box-shadow .3s ease;
}

.sari-cta-v2__primary:hover {
    transform: translateY(-3px);
    background: var(--cta-gold-dark);
    box-shadow: 0 14px 34px rgba(201,138,8,.20);
}

.sari-cta-v2__primary span {
    font-size: 18px;
    font-weight: 300;
    transition: transform .3s ease;
}

.sari-cta-v2__primary:hover span {
    transform: translateX(4px);
}

.sari-cta-v2__secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--cta-ink);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: color .25s ease, transform .25s ease;
}

.sari-cta-v2__secondary span {
    color: var(--cta-gold);
}

.sari-cta-v2__secondary:hover {
    color: var(--cta-gold);
    transform: translateX(2px);
}

.sari-cta-v2__trust {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 13px 20px;
    margin-top: 35px;
}

.sari-cta-v2__trust span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--cta-soft);
    font-size: 10.5px;
    font-weight: 500;
}

.sari-cta-v2__trust i {
    display: block;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--cta-gold);
}

.sari-cta-v2__floating {
    position: absolute;
    right: 7%;
    bottom: 62px;
    z-index: 4;

    width: 248px;
    min-height: 76px;
    padding: 16px 17px;

    display: flex;
    align-items: center;
    gap: 12px;

    border: 1px solid rgba(255,255,255,.64);
    border-radius: 15px;
    background: rgba(255,255,255,.80);
    box-shadow: 0 18px 45px rgba(66,47,18,.11);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);

    animation: sariCtaFloat 7s ease-in-out infinite;
}

.sari-cta-v2__floating-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: rgba(201,138,8,.11);
    color: var(--cta-gold);
}

.sari-cta-v2__floating-icon svg {
    width: 20px;
    height: 20px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-cta-v2__floating small,
.sari-cta-v2__floating strong {
    display: block;
}

.sari-cta-v2__floating small {
    margin-bottom: 3px;
    color: var(--cta-soft);
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .15em;
}

.sari-cta-v2__floating strong {
    color: var(--cta-ink);
    font-size: 12px;
    line-height: 1.4;
}

@keyframes sariCtaFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-7px); }
}

/* reveal */
.sari-cta-v2__reveal {
    opacity: 0;
    transform: translateY(18px);
    transition:
        opacity .75s cubic-bezier(.22,1,.36,1),
        transform .85s cubic-bezier(.22,1,.36,1);
}

.sari-cta-v2__reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}


@media (max-width: 900px) {
    .sari-cta-v2 {
        min-height: 720px;
        background-position: 68% center;
    }

    .sari-cta-v2__overlay {
        background: linear-gradient(
            90deg,
            rgba(255,253,249,.99) 0%,
            rgba(255,253,249,.95) 56%,
            rgba(255,253,249,.55) 80%,
            rgba(255,253,249,.12) 100%
        );
    }

    .sari-cta-v2__container {
        padding: 72px 28px;
    }

    .sari-cta-v2__content {
        width: min(610px, 72vw);
    }

    .sari-cta-v2__floating {
        display: none;
    }
}

@media (max-width: 620px) {
    .sari-cta-v2 {
        min-height: 760px;
        align-items: flex-start;
        background-position: 72% center;
    }

    .sari-cta-v2__overlay {
        background: linear-gradient(
            180deg,
            rgba(255,253,249,.995) 0%,
            rgba(255,253,249,.965) 50%,
            rgba(255,253,249,.75) 73%,
            rgba(255,253,249,.32) 100%
        );
    }

    .sari-cta-v2__container {
        padding: 60px 20px 88px;
    }

    .sari-cta-v2__content {
        width: 100%;
    }

    .sari-cta-v2__brand {
        margin-bottom: 33px;
    }

    .sari-cta-v2__logo {
        width: 145px;
    }

    .sari-cta-v2__title {
        font-size: clamp(49px, 14vw, 64px);
        line-height: .98;
    }

    .sari-cta-v2__description {
        margin-top: 23px;
        font-size: 14px;
    }

    .sari-cta-v2__actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        margin-top: 28px;
    }

    .sari-cta-v2__trust {
        margin-top: 29px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-cta-v2__reveal,
    .sari-cta-v2__floating,
    .sari-cta-v2__primary,
    .sari-cta-v2__primary span,
    .sari-cta-v2__secondary {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        animation: none !important;
    }
}
</style>


<style>
/* =========================================================
   SARI CTA — HYBRID MOBILE FINAL
   Desktop/web design stays untouched.
   ========================================================= */

@media (max-width: 767px) {
    .sari-cta-v2 {
        min-height: 520px !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        background-size: cover !important;
        background-position: 64% center !important;
        overflow: hidden !important;
    }

    .sari-cta-v2__overlay {
        background: linear-gradient(
            90deg,
            rgba(255,253,249,.995) 0%,
            rgba(255,253,249,.98) 34%,
            rgba(255,253,249,.89) 49%,
            rgba(255,253,249,.58) 64%,
            rgba(255,253,249,.18) 81%,
            rgba(255,253,249,0) 100%
        ) !important;
    }

    .sari-cta-v2__container {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 48px 18px 58px !important;
    }

    .sari-cta-v2__content {
        width: 61% !important;
        max-width: 330px !important;
    }

    .sari-cta-v2__brand {
        gap: 8px !important;
        margin-bottom: 23px !important;
    }

    .sari-cta-v2__logo {
        width: 112px !important;
    }

    .sari-cta-v2__eyebrow {
        padding-left: 26px !important;
        font-size: 6.8px !important;
        letter-spacing: .22em !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__eyebrow::before {
        width: 18px !important;
    }

    .sari-cta-v2__title {
        max-width: 330px !important;
        font-size: clamp(34px, 9.5vw, 44px) !important;
        line-height: .97 !important;
        letter-spacing: -.055em !important;
    }

    .sari-cta-v2__description {
        width: 100% !important;
        max-width: 290px !important;
        margin-top: 18px !important;
        font-size: 11.5px !important;
        line-height: 1.58 !important;
    }

    .sari-cta-v2__actions {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 11px 14px !important;
        margin-top: 23px !important;
    }

    .sari-cta-v2__primary {
        min-width: 132px !important;
        height: 42px !important;
        padding: 0 16px !important;
        gap: 9px !important;
        border-radius: 6px !important;
        font-size: 8.4px !important;
        letter-spacing: .08em !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__primary span {
        font-size: 13px !important;
    }

    .sari-cta-v2__secondary {
        gap: 5px !important;
        font-size: 8.6px !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__trust {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 7px 12px !important;
        margin-top: 22px !important;
        padding-right: 6px !important;
    }

    .sari-cta-v2__trust span {
        gap: 5px !important;
        font-size: 7.1px !important;
        line-height: 1.3 !important;
    }

    .sari-cta-v2__trust i {
        width: 3.5px !important;
        height: 3.5px !important;
    }

    .sari-cta-v2__floating {
        display: flex !important;
        right: 12px !important;
        bottom: 14px !important;
        width: 156px !important;
        min-height: 54px !important;
        padding: 10px 11px !important;
        gap: 8px !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,.84) !important;
        box-shadow: 0 12px 30px rgba(66,47,18,.10) !important;
        backdrop-filter: blur(9px) !important;
        -webkit-backdrop-filter: blur(9px) !important;
        animation: none !important;
    }

    .sari-cta-v2__floating-icon {
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
    }

    .sari-cta-v2__floating-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-cta-v2__floating small {
        margin-bottom: 2px !important;
        font-size: 5.8px !important;
        letter-spacing: .12em !important;
    }

    .sari-cta-v2__floating strong {
        font-size: 8px !important;
        line-height: 1.3 !important;
    }

    .sari-cta-v2__primary:hover,
    .sari-cta-v2__secondary:hover,
    .sari-cta-v2__primary:hover span {
        transform: none !important;
    }
}

@media (max-width: 430px) {
    .sari-cta-v2 {
        min-height: 500px !important;
        background-position: 66% center !important;
    }

    .sari-cta-v2__overlay {
        background: linear-gradient(
            90deg,
            rgba(255,253,249,.997) 0%,
            rgba(255,253,249,.985) 38%,
            rgba(255,253,249,.90) 54%,
            rgba(255,253,249,.56) 70%,
            rgba(255,253,249,.12) 100%
        ) !important;
    }

    .sari-cta-v2__container {
        padding: 42px 14px 58px !important;
    }

    .sari-cta-v2__content {
        width: 64% !important;
        max-width: 255px !important;
    }

    .sari-cta-v2__brand {
        margin-bottom: 20px !important;
    }

    .sari-cta-v2__logo {
        width: 98px !important;
    }

    .sari-cta-v2__eyebrow {
        padding-left: 22px !important;
        font-size: 6px !important;
    }

    .sari-cta-v2__eyebrow::before {
        width: 15px !important;
    }

    .sari-cta-v2__title {
        max-width: 255px !important;
        font-size: clamp(32px, 10.5vw, 39px) !important;
    }

    .sari-cta-v2__description {
        max-width: 235px !important;
        margin-top: 16px !important;
        font-size: 10px !important;
        line-height: 1.55 !important;
    }

    .sari-cta-v2__actions {
        gap: 10px 12px !important;
        margin-top: 20px !important;
    }

    .sari-cta-v2__primary {
        min-width: 118px !important;
        height: 39px !important;
        padding-inline: 14px !important;
        font-size: 7.5px !important;
    }

    .sari-cta-v2__secondary {
        font-size: 7.7px !important;
    }

    .sari-cta-v2__trust {
        gap: 6px 10px !important;
        margin-top: 19px !important;
    }

    .sari-cta-v2__trust span {
        font-size: 6.3px !important;
    }

    .sari-cta-v2__floating {
        right: 8px !important;
        bottom: 10px !important;
        width: 138px !important;
        min-height: 48px !important;
        padding: 8px 9px !important;
    }

    .sari-cta-v2__floating-icon {
        width: 27px !important;
        height: 27px !important;
    }

    .sari-cta-v2__floating strong {
        font-size: 7.2px !important;
    }
}

@media (max-width: 360px) {
    .sari-cta-v2 {
        background-position: 67% center !important;
    }

    .sari-cta-v2__content {
        width: 67% !important;
        max-width: 225px !important;
    }

    .sari-cta-v2__title {
        font-size: 30px !important;
    }

    .sari-cta-v2__description {
        max-width: 210px !important;
        font-size: 9.3px !important;
    }

    .sari-cta-v2__actions {
        gap: 8px !important;
    }

    .sari-cta-v2__primary {
        min-width: 110px !important;
        height: 37px !important;
        font-size: 7px !important;
    }

    .sari-cta-v2__secondary {
        font-size: 7px !important;
    }

    .sari-cta-v2__trust span {
        font-size: 5.8px !important;
    }

    .sari-cta-v2__floating {
        width: 126px !important;
    }
}
</style>



<style>
/* =========================================================
   SARI CTA — MOBILE BACKGROUND SEAM FIX
   Keeps desktop untouched.
   Fixes the visible horizontal "split" on mobile.
   ========================================================= */

@media (max-width: 767px) {
    .sari-cta-v2 {
        background-color: #fffdf9 !important;

        /*
         * Keep the full image height intact instead of allowing
         * cover-cropping to create an awkward mobile composition.
         */
        background-size: auto 100% !important;
        background-position: right center !important;
        background-repeat: no-repeat !important;
    }

    /*
     * Two-layer veil:
     * 1) desktop-like left-to-right readability fade
     * 2) subtle top/bottom feather that visually removes any
     *    hard horizontal transition in the source image/crop
     */
    .sari-cta-v2__overlay {
        background:
            linear-gradient(
                180deg,
                rgba(255,253,249,.18) 0%,
                rgba(255,253,249,.05) 23%,
                rgba(255,253,249,0) 45%,
                rgba(255,253,249,0) 78%,
                rgba(255,253,249,.08) 100%
            ),
            linear-gradient(
                90deg,
                rgba(255,253,249,.998) 0%,
                rgba(255,253,249,.985) 34%,
                rgba(255,253,249,.91) 49%,
                rgba(255,253,249,.62) 64%,
                rgba(255,253,249,.24) 80%,
                rgba(255,253,249,.04) 100%
            ) !important;
    }
}

@media (max-width: 430px) {
    .sari-cta-v2 {
        background-size: auto 100% !important;
        background-position: right center !important;
    }

    .sari-cta-v2__overlay {
        background:
            linear-gradient(
                180deg,
                rgba(255,253,249,.16) 0%,
                rgba(255,253,249,.04) 25%,
                rgba(255,253,249,0) 47%,
                rgba(255,253,249,0) 80%,
                rgba(255,253,249,.08) 100%
            ),
            linear-gradient(
                90deg,
                rgba(255,253,249,.998) 0%,
                rgba(255,253,249,.99) 39%,
                rgba(255,253,249,.92) 54%,
                rgba(255,253,249,.64) 70%,
                rgba(255,253,249,.18) 100%
            ) !important;
    }
}
</style>



<style>
/* =========================================================
   SARI CTA — MOBILE RIGHT-PANEL FIX V2
   Desktop/web untouched.

   Mobile:
   - removes full-section background crop
   - renders the product scene as a full-height right panel
   - eliminates the horizontal seam
   - removes excessive top blank space
   ========================================================= */

@media (max-width: 767px) {

    .sari-cta-v2 {
        position: relative !important;

        min-height: 520px !important;
        height: auto !important;

        display: flex !important;
        align-items: flex-start !important;

        /*
         * Mobile no longer uses the full wide hero as the section background.
         * Left side becomes a clean cream canvas.
         */
        background-image: none !important;
        background-color: #fffdf9 !important;

        overflow: hidden !important;
    }

    /*
     * Dedicated right-side image layer.
     * Because we show only the right part of the source image,
     * the shopping bag / vase scene stays visible from TOP to BOTTOM.
     */
    .sari-cta-v2::before {
        content: "" !important;

        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;

        width: 54% !important;

        z-index: 0 !important;
        pointer-events: none !important;

        background-image: url("{{ asset('images/sari-hero-bg.png') }}") !important;
        background-repeat: no-repeat !important;
        background-size: auto 100% !important;
        background-position: right center !important;
    }

    /*
     * Blend the right image panel into the cream content area.
     */
    .sari-cta-v2__overlay {
        z-index: 1 !important;

        background:
            linear-gradient(
                90deg,
                #fffdf9 0%,
                #fffdf9 41%,
                rgba(255,253,249,.96) 48%,
                rgba(255,253,249,.78) 57%,
                rgba(255,253,249,.38) 69%,
                rgba(255,253,249,.08) 84%,
                rgba(255,253,249,0) 100%
            ) !important;
    }

    /*
     * Start content near the top instead of vertically centering it.
     * This removes the large empty mobile gap seen in the screenshot.
     */
    .sari-cta-v2__container {
        position: relative !important;
        z-index: 3 !important;

        width: 100% !important;
        max-width: none !important;

        margin: 0 !important;
        padding: 42px 18px 68px !important;
    }

    .sari-cta-v2__content {
        width: 62% !important;
        max-width: 330px !important;
    }

    .sari-cta-v2__brand {
        margin-bottom: 21px !important;
    }

    .sari-cta-v2__logo {
        width: 108px !important;
    }

    .sari-cta-v2__eyebrow {
        padding-left: 25px !important;
        font-size: 6.5px !important;
        letter-spacing: .21em !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__eyebrow::before {
        width: 17px !important;
    }

    .sari-cta-v2__title {
        max-width: 320px !important;

        font-size: clamp(34px, 9.4vw, 43px) !important;
        line-height: .97 !important;
        letter-spacing: -.055em !important;
    }

    .sari-cta-v2__description {
        width: 100% !important;
        max-width: 285px !important;

        margin-top: 17px !important;

        font-size: 11px !important;
        line-height: 1.56 !important;
    }

    .sari-cta-v2__actions {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        flex-wrap: wrap !important;

        gap: 11px 13px !important;
        margin-top: 22px !important;
    }

    .sari-cta-v2__primary {
        min-width: 128px !important;
        height: 41px !important;

        padding: 0 15px !important;

        border-radius: 6px !important;

        font-size: 8.2px !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__secondary {
        font-size: 8.4px !important;
        white-space: nowrap !important;
    }

    .sari-cta-v2__trust {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;

        gap: 7px 11px !important;

        margin-top: 21px !important;
    }

    .sari-cta-v2__trust span {
        gap: 5px !important;
        font-size: 6.9px !important;
    }

    .sari-cta-v2__trust i {
        width: 3.5px !important;
        height: 3.5px !important;
    }

    /*
     * Keep the floating marketplace card over the image panel.
     */
    .sari-cta-v2__floating {
        display: flex !important;

        right: 10px !important;
        bottom: 12px !important;

        width: 150px !important;
        min-height: 52px !important;

        padding: 9px 10px !important;
        gap: 8px !important;

        border-radius: 10px !important;

        z-index: 4 !important;

        background: rgba(255,255,255,.86) !important;
        box-shadow: 0 12px 30px rgba(66,47,18,.10) !important;

        backdrop-filter: blur(9px) !important;
        -webkit-backdrop-filter: blur(9px) !important;

        animation: none !important;
    }

    .sari-cta-v2__floating-icon {
        width: 29px !important;
        height: 29px !important;

        border-radius: 7px !important;
    }

    .sari-cta-v2__floating-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .sari-cta-v2__floating small {
        margin-bottom: 2px !important;
        font-size: 5.5px !important;
        letter-spacing: .12em !important;
    }

    .sari-cta-v2__floating strong {
        font-size: 7.7px !important;
        line-height: 1.28 !important;
    }

    /* No sticky touch hover movement. */
    .sari-cta-v2__primary:hover,
    .sari-cta-v2__secondary:hover,
    .sari-cta-v2__primary:hover span {
        transform: none !important;
    }
}

/* Standard narrow phones */
@media (max-width: 430px) {

    .sari-cta-v2 {
        min-height: 500px !important;
    }

    .sari-cta-v2::before {
        width: 55% !important;
        background-size: auto 100% !important;
        background-position: right center !important;
    }

    .sari-cta-v2__overlay {
        background:
            linear-gradient(
                90deg,
                #fffdf9 0%,
                #fffdf9 43%,
                rgba(255,253,249,.97) 50%,
                rgba(255,253,249,.80) 59%,
                rgba(255,253,249,.40) 71%,
                rgba(255,253,249,.08) 88%,
                rgba(255,253,249,0) 100%
            ) !important;
    }

    .sari-cta-v2__container {
        padding: 38px 14px 64px !important;
    }

    .sari-cta-v2__content {
        width: 64% !important;
        max-width: 255px !important;
    }

    .sari-cta-v2__logo {
        width: 98px !important;
    }

    .sari-cta-v2__title {
        font-size: clamp(32px, 10.4vw, 39px) !important;
    }

    .sari-cta-v2__description {
        max-width: 235px !important;
        font-size: 10px !important;
    }

    .sari-cta-v2__primary {
        min-width: 118px !important;
        height: 39px !important;
        font-size: 7.5px !important;
    }

    .sari-cta-v2__secondary {
        font-size: 7.6px !important;
    }

    .sari-cta-v2__trust span {
        font-size: 6.2px !important;
    }

    .sari-cta-v2__floating {
        right: 8px !important;
        bottom: 9px !important;

        width: 136px !important;
        min-height: 47px !important;
    }

    .sari-cta-v2__floating strong {
        font-size: 7px !important;
    }
}

/* Very narrow phones */
@media (max-width: 360px) {

    .sari-cta-v2::before {
        width: 56% !important;
    }

    .sari-cta-v2__content {
        width: 67% !important;
        max-width: 225px !important;
    }

    .sari-cta-v2__title {
        font-size: 30px !important;
    }

    .sari-cta-v2__description {
        max-width: 205px !important;
        font-size: 9.2px !important;
    }

    .sari-cta-v2__primary {
        min-width: 108px !important;
        height: 37px !important;
        font-size: 7px !important;
    }

    .sari-cta-v2__secondary {
        font-size: 7px !important;
    }

    .sari-cta-v2__floating {
        width: 124px !important;
    }
}
</style>


<script>
(function () {
    const section = document.querySelector('.sari-cta-v2');
    if (!section) return;

    const items = section.querySelectorAll('.sari-cta-v2__reveal');

    requestAnimationFrame(function () {
        items.forEach(function (item, index) {
            setTimeout(function () {
                item.classList.add('is-visible');
            }, index * 90);
        });
    });
})();
</script>
