<section class="sari-hero sari-section" id="home">

    <div class="sari-hero-content">

        {{-- BRAND --}}
        <div class="sari-hero-brand sari-hero-reveal sari-hero-delay-1">
            <div class="sari-hero-brand-mark">
                <span class="sari-hero-brand-line"></span>

                <img
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    class="sari-hero-logo"
                >

                <span class="sari-hero-brand-line"></span>
            </div>

            <p class="sari-hero-tagline">Elevated Everyday</p>
        </div>

        {{-- HERO COPY --}}
        <div class="sari-hero-copy">
            <p class="sari-hero-eyebrow sari-hero-reveal sari-hero-delay-2">
                Your everyday marketplace, made better.
            </p>

            <h1 class="sari-hero-title sari-hero-reveal sari-hero-delay-3">
                Discover more.<br>
                Shop with <span>ease.</span>
            </h1>

            <p class="sari-hero-description sari-hero-reveal sari-hero-delay-4">
                Find products from trusted sellers, enjoy a simpler shopping experience,
                and stay connected from checkout to delivery — all in one marketplace.
            </p>
        </div>

        {{-- HERO ACTIONS --}}
        <div class="sari-hero-actions sari-hero-reveal sari-hero-delay-5">
            <a href="#discover" class="sari-button sari-button-primary">
                <span>Start Exploring</span>

                <svg viewBox="0 0 24 24" class="sari-button-icon" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="M5 12h14"></path>
                    <path d="m14 7 5 5-5 5"></path>
                </svg>
            </a>

            <a href="#how-it-works" class="sari-button sari-button-secondary">
                <span>How SARI Works</span>

                <svg viewBox="0 0 24 24" class="sari-button-icon" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="m10 9 5 3-5 3V9Z"></path>
                </svg>
            </a>
        </div>

    </div>

    {{-- SCROLL INDICATOR --}}
    <a
        href="#discover"
        class="sari-scroll-indicator sari-hero-reveal sari-hero-delay-6"
        aria-label="Scroll to explore SARI"
    >
        <span class="sari-scroll-line"></span>
        <span class="sari-scroll-label">Scroll to explore</span>
        <span class="sari-scroll-line"></span>
    </a>

</section>

<style>
.sari-hero {
    position: relative;
    min-height: 100vh;
    min-height: 100svh;
    min-height: 100dvh;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 88px 20px 58px;
}

.sari-hero-content {
    position: relative;
    z-index: 2;
    width: min(100%, 940px);
    margin-inline: auto;
    text-align: center;
}

.sari-hero-brand {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.sari-hero-brand-mark {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
}

.sari-hero-logo {
    display: block;
    width: clamp(300px, 24vw, 420px);
    height: auto;
    object-fit: contain;
    transition: transform .45s cubic-bezier(.22,1,.36,1), opacity .3s ease;
}

.sari-hero-brand:hover .sari-hero-logo {
    transform: translateY(-2px);
}

.sari-hero-brand-line {
    width: clamp(28px, 3vw, 42px);
    height: 1px;
    background: rgba(255,255,255,.22);
}

.sari-hero-tagline {
    margin: 8px 0 0;
    color: rgba(255,255,255,.68);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .22em;
    text-transform: uppercase;
}

.sari-hero-copy {
    margin-top: 16px;
}

.sari-hero-eyebrow {
    margin: 0 0 14px;
    color: #a8731f;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
}

.sari-hero-title {
    margin: 0;
    color: #ffffff;
    font-size: clamp(48px, 6vw, 78px);
    font-weight: 700;
    line-height: 1.03;
    letter-spacing: -.055em;
}

.sari-hero-title span {
    position: relative;
    display: inline-block;
    color: #c88712;
}

.sari-hero-description {
    max-width: 640px;
    margin: 18px auto 0;
    color: rgba(255,255,255,.78);
    font-size: clamp(13.5px, 1.12vw, 16px);
    font-weight: 400;
    line-height: 1.8;
}

.sari-hero-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 28px;
}

.sari-hero .sari-button {
    position: relative;
    display: inline-flex;
    min-width: 168px;
    min-height: 47px;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 0 19px;
    border-radius: 7px;
    border: 1px solid transparent;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: -.01em;
    text-decoration: none;
    transition: transform .28s cubic-bezier(.22,1,.36,1), background-color .25s ease, border-color .25s ease, color .25s ease, box-shadow .28s ease;
}

.sari-button-icon {
    width: 15px;
    height: 15px;
    flex: 0 0 auto;
    transition: transform .28s cubic-bezier(.22,1,.36,1);
}

.sari-hero .sari-button-primary {
    color: #fff;
    border-color: #d48f08;
    background: #d48f08;
    box-shadow: 0 10px 24px rgba(212, 143, 8, .18);
}

.sari-hero .sari-button-primary:hover {
    transform: translateY(-2px);
    border-color: #bd7d05;
    background: #bd7d05;
    box-shadow: 0 14px 30px rgba(189, 125, 5, .22);
}

.sari-hero .sari-button-primary:hover .sari-button-icon {
    transform: translateX(3px);
}

.sari-hero .sari-button-secondary {
    color: #f4f1eb;
    border-color: rgba(255,255,255,.20);
    background: rgba(255,255,255,.10);
    box-shadow: 0 5px 14px rgba(0,0,0,.08);
    backdrop-filter: blur(8px);
}

.sari-hero .sari-button-secondary:hover {
    transform: translateY(-2px);
    color: #ffffff;
    border-color: rgba(255,255,255,.30);
    background: rgba(255,255,255,.16);
    box-shadow: 0 10px 24px rgba(0,0,0,.12);
}

.sari-hero .sari-button-secondary:hover .sari-button-icon {
    transform: scale(1.06);
}

.sari-hero .sari-button:active {
    transform: translateY(0);
}

.sari-scroll-indicator {
    position: absolute;
    z-index: 3;
    left: 50%;
    bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 13px;
    width: max-content;
    max-width: calc(100% - 32px);
    color: rgba(255,255,255,.62);
    text-decoration: none;
    transform: translateX(-50%);
    transition: color .25s ease, opacity .3s ease;
}

.sari-scroll-label {
    display: block;
    white-space: nowrap;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .16em;
    line-height: 1;
    text-align: center;
    text-transform: uppercase;
}

.sari-scroll-line {
    position: relative;
    display: block;
    width: 38px;
    height: 1px;
    overflow: hidden;
    background: rgba(255,255,255,.22);
}

.sari-scroll-line::after {
    content: "";
    position: absolute;
    inset: 0;
    background: #c58a1c;
    transform: translateX(-105%);
    animation: sariScrollLine 2.8s ease-in-out infinite;
}

.sari-scroll-indicator:hover {
    color: #a8731f;
}

@keyframes sariScrollLine {
    0%, 25% {
        transform: translateX(-105%);
        opacity: 0;
    }

    45% {
        opacity: .8;
    }

    75%, 100% {
        transform: translateX(105%);
        opacity: 0;
    }
}

.sari-hero.sari-hero-motion-ready .sari-hero-reveal {
    opacity: 0;
    transition: opacity .72s cubic-bezier(.22,1,.36,1), transform .82s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}

.sari-hero.sari-hero-motion-ready .sari-hero-reveal:not(.sari-scroll-indicator) {
    transform: translate3d(0, 15px, 0);
}

.sari-hero.sari-hero-motion-ready .sari-scroll-indicator {
    transform: translateX(-50%) translateY(10px);
}

.sari-hero.sari-hero-motion-ready .sari-hero-reveal.is-visible:not(.sari-scroll-indicator) {
    opacity: 1;
    transform: translate3d(0, 0, 0);
}

.sari-hero.sari-hero-motion-ready .sari-scroll-indicator.is-visible {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.sari-hero-delay-1 { transition-delay: .03s !important; }
.sari-hero-delay-2 { transition-delay: .10s !important; }
.sari-hero-delay-3 { transition-delay: .17s !important; }
.sari-hero-delay-4 { transition-delay: .25s !important; }
.sari-hero-delay-5 { transition-delay: .33s !important; }
.sari-hero-delay-6 { transition-delay: .48s !important; }

@media (max-width: 900px) {
    .sari-hero-logo {
        width: min(320px, 68vw);
    }

    .sari-hero {
        min-height: 100vh;
        min-height: 100svh;
        min-height: 100dvh;
        padding: 84px 20px 54px;
    }

    .sari-hero-copy {
        margin-top: 22px;
    }

    .sari-hero-title {
        font-size: clamp(40px, 8.2vw, 56px);
        line-height: 1.01;
    }

    .sari-hero-description {
        max-width: 540px;
        margin-top: 16px;
        font-size: 13px;
        line-height: 1.75;
    }

    .sari-hero-actions {
        margin-top: 22px;
    }

    .sari-scroll-indicator {
        bottom: 18px;
    }
}

@media (max-width: 600px) {
    .sari-hero-logo {
        width: min(260px, 72vw);
    }

    .sari-hero {
        padding-inline: 16px;
    }

    .sari-hero-brand-mark {
        gap: 12px;
    }

    .sari-hero-brand-line {
        width: 22px;
    }

    .sari-hero-eyebrow {
        font-size: 8.5px;
        letter-spacing: .08em;
    }

    .sari-hero-title {
        font-size: clamp(36px, 10.6vw, 48px);
    }

    .sari-hero-description {
        max-width: 390px;
    }

    .sari-hero-actions {
        width: min(100%, 350px);
        margin-inline: auto;
    }

    .sari-hero .sari-button {
        width: 100%;
        min-height: 46px;
    }

    .sari-scroll-line {
        width: 24px;
    }

    .sari-scroll-label {
        font-size: 7px;
        letter-spacing: .13em;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-hero.sari-hero-motion-ready .sari-hero-reveal,
    .sari-hero-logo,
    .sari-hero .sari-button,
    .sari-button-icon,
    .sari-scroll-line::after {
        opacity: 1 !important;
        animation: none !important;
        transition: none !important;
    }

    .sari-hero.sari-hero-motion-ready .sari-hero-reveal:not(.sari-scroll-indicator) {
        transform: none !important;
    }

    .sari-hero.sari-hero-motion-ready .sari-scroll-indicator,
    .sari-hero.sari-hero-motion-ready .sari-scroll-indicator.is-visible {
        transform: translateX(-50%) !important;
    }
}


/* =========================================================
   FINAL HERO LOGO SCALE OVERRIDE
   Higher specificity so landing-compact.css cannot shrink it.
   ========================================================= */
.sari-hero .sari-hero-brand .sari-hero-logo {
    width: clamp(250px, 22vw, 340px) !important;
    max-width: min(72vw, 340px) !important;
    height: auto !important;
}

.sari-hero .sari-hero-brand-mark {
    gap: 18px !important;
}

.sari-hero .sari-hero-brand-line {
    width: clamp(28px, 3vw, 42px) !important;
}

.sari-hero .sari-hero-tagline {
    margin-top: 8px !important;
}

/* Keep the bigger logo from pushing the headline too far down. */
.sari-hero .sari-hero-copy {
    margin-top: 14px !important;
}

@media (max-width: 900px) {
    .sari-hero .sari-hero-brand .sari-hero-logo {
        width: min(280px, 64vw) !important;
        max-width: 64vw !important;
    }
}

@media (max-width: 600px) {
    .sari-hero .sari-hero-brand .sari-hero-logo {
        width: min(230px, 68vw) !important;
        max-width: 68vw !important;
    }

    .sari-hero .sari-hero-brand-line {
        width: 20px !important;
    }
}



/* =========================================================
   HERO COPY + BUTTON SCALE — FINAL OVERRIDE
   Bigger hero message, smaller button labels.
   High specificity so landing-compact.css cannot override it.
   ========================================================= */

.sari-page .sari-hero .sari-hero-eyebrow {
    font-size: 12px !important;
    line-height: 1.45 !important;
    letter-spacing: .085em !important;
    margin-bottom: 12px !important;
}

.sari-page .sari-hero .sari-hero-title {
    font-size: clamp(44px, 5vw, 68px) !important;
    line-height: 1.02 !important;
    letter-spacing: -.052em !important;
}

.sari-page .sari-hero .sari-hero-description {
    max-width: 640px !important;
    margin-top: 16px !important;
    font-size: clamp(14px, 1.05vw, 15.5px) !important;
    line-height: 1.72 !important;
}

.sari-page .sari-hero .sari-button {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
}

.sari-page .sari-hero .sari-button-icon {
    width: 14px !important;
    height: 14px !important;
}

@media (max-width: 900px) {
    .sari-page .sari-hero .sari-hero-eyebrow {
        font-size: 10.5px !important;
    }

    .sari-page .sari-hero .sari-hero-title {
        font-size: clamp(40px, 7.2vw, 56px) !important;
    }

    .sari-page .sari-hero .sari-hero-description {
        max-width: 560px !important;
        font-size: 14px !important;
    }

    .sari-page .sari-hero .sari-button {
        font-size: 10px !important;
    }
}

@media (max-width: 600px) {
    .sari-page .sari-hero .sari-hero-eyebrow {
        font-size: 9.5px !important;
    }

    .sari-page .sari-hero .sari-hero-title {
        font-size: clamp(38px, 10.8vw, 50px) !important;
    }

    .sari-page .sari-hero .sari-hero-description {
        max-width: 410px !important;
        font-size: 13px !important;
        line-height: 1.68 !important;
    }

    .sari-page .sari-hero .sari-button {
        font-size: 9.5px !important;
    }
}

</style>

<script>
(function () {
    const hero = document.querySelector('.sari-hero');

    if (!hero) {
        return;
    }

    hero.classList.add('sari-hero-motion-ready');

    const items = hero.querySelectorAll('.sari-hero-reveal');

    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            items.forEach(function (item) {
                item.classList.add('is-visible');
            });
        });
    });
})();
</script>