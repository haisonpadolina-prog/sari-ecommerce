<section 
    class="sari-hero sari-section" 
    id="home" 
> 

    <div class="sari-hero-content"> 

        {{-- SARI BRAND --}} 
        <div class="sari-hero-brand sari-hero-reveal sari-hero-delay-1"> 

            <div class="sari-hero-brand-mark" aria-hidden="true"> 

                <span class="sari-hero-brand-line"></span> 

                <img 
                    src="{{ asset('images/sari-logo.png') }}" 
                    alt="SARI" 
                    class="sari-hero-logo" 
                > 

                <span class="sari-hero-brand-line"></span> 

            </div> 

            <div class="sari-hero-tagline"> 
                Elevated Everyday 
            </div> 

        </div> 


        {{-- HERO TITLE --}} 
        <h1 class="sari-hero-title sari-hero-reveal sari-hero-delay-2"> 
            Redefining Shopping.<br> 
            Connecting <span>Everything.</span> 
        </h1> 


        {{-- HERO DESCRIPTION --}} 
        <p class="sari-hero-description sari-hero-reveal sari-hero-delay-3"> 
            A modern marketplace designed to bring 
            discovery, shopping, and delivery together 
            in one seamless experience. 
        </p> 


        {{-- HERO ACTIONS --}} 
        <div class="sari-hero-actions sari-hero-reveal sari-hero-delay-4"> 

            <a 
                href="#discover" 
                class="sari-button sari-button-primary" 
            > 
                <span class="sari-button-label">Explore SARI</span>
                <span class="sari-button-arrow">↗</span> 
            </a> 

            <a 
                href="#how-it-works" 
                class="sari-button sari-button-outline" 
            > 
                <span class="sari-button-label">How It Works</span>
                <span class="sari-button-arrow">↗</span> 
            </a> 

        </div> 

    </div> 


    {{-- SCROLL INDICATOR --}} 
    <a 
        href="#discover" 
        class="sari-scroll-indicator sari-hero-reveal sari-hero-delay-5" 
        aria-label="Scroll to discover" 
    > 

        <span class="sari-scroll-line"></span> 

        <span> 
            Scroll to Discover 
        </span> 

        <span class="sari-scroll-line"></span> 

    </a> 

</section>

<style>
/* =========================================================
   SARI HERO — PREMIUM MOTION + BUTTON ENHANCEMENT
   Quiet, formal, smooth.
   ========================================================= */

.sari-hero {
    position: relative;
}

/* Entrance */
.sari-hero.sari-hero-motion-ready .sari-hero-reveal {
    opacity: 0;
    transform: translate3d(0, 18px, 0);
    transition:
        opacity .8s cubic-bezier(.22,1,.36,1),
        transform .9s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}

.sari-hero.sari-hero-motion-ready .sari-hero-reveal.is-visible {
    opacity: 1;
    transform: translate3d(0,0,0);
}

.sari-hero-delay-1 { transition-delay: .05s !important; }
.sari-hero-delay-2 { transition-delay: .14s !important; }
.sari-hero-delay-3 { transition-delay: .23s !important; }
.sari-hero-delay-4 { transition-delay: .32s !important; }
.sari-hero-delay-5 { transition-delay: .48s !important; }

/* Logo settle */
.sari-hero-logo {
    transition:
        transform .7s cubic-bezier(.22,1,.36,1),
        filter .45s ease;
}

.sari-hero-brand:hover .sari-hero-logo {
    transform: translateY(-2px) scale(1.015);
}

/* Brand lines subtly extend */
.sari-hero-brand-line {
    transform-origin: center;
    transition:
        transform .6s cubic-bezier(.22,1,.36,1),
        opacity .4s ease;
}

.sari-hero-brand:hover .sari-hero-brand-line {
    transform: scaleX(1.08);
}

/* =========================================================
   BUTTONS
   ========================================================= */

.sari-hero .sari-button {
    position: relative;
    isolation: isolate;
    overflow: hidden;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 14px;

    min-height: 52px;
    padding: 0 22px;

    border-radius: 8px;

    font-weight: 700;
    letter-spacing: .01em;

    transform: translateZ(0);

    transition:
        transform .35s cubic-bezier(.22,1,.36,1),
        box-shadow .35s ease,
        background-color .35s ease,
        border-color .35s ease,
        color .35s ease;
}

/* Primary */
.sari-hero .sari-button-primary {
    box-shadow:
        0 12px 28px rgba(184,134,59,.18);
}

.sari-hero .sari-button-primary::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    opacity: 0;
    background:
        linear-gradient(
            110deg,
            rgba(255,255,255,.06),
            rgba(255,255,255,.18) 48%,
            rgba(255,255,255,.04)
        );
    transition: opacity .35s ease;
}

.sari-hero .sari-button-primary::after {
    content: "";
    position: absolute;
    top: -45%;
    left: -80%;
    width: 46%;
    height: 190%;
    z-index: 0;
    transform: skewX(-18deg);
    background: linear-gradient(
        100deg,
        transparent,
        rgba(255,255,255,.20),
        transparent
    );
    transition: left .8s cubic-bezier(.22,1,.36,1);
    pointer-events: none;
}

.sari-hero .sari-button-primary:hover {
    transform: translateY(-3px);
    box-shadow:
        0 17px 34px rgba(184,134,59,.24);
}

.sari-hero .sari-button-primary:hover::before {
    opacity: 1;
}

.sari-hero .sari-button-primary:hover::after {
    left: 135%;
}

/* Outline */
.sari-hero .sari-button-outline {
    background: transparent;
}

.sari-hero .sari-button-outline::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    transform: scaleX(0);
    transform-origin: left center;
    background: rgba(184,134,59,.08);
    transition: transform .45s cubic-bezier(.22,1,.36,1);
}

.sari-hero .sari-button-outline:hover {
    transform: translateY(-3px);
    border-color: rgba(184,134,59,.72);
    box-shadow:
        0 12px 26px rgba(22,20,15,.08);
}

.sari-hero .sari-button-outline:hover::before {
    transform: scaleX(1);
}

/* Labels stay crisp over effects */
.sari-button-label,
.sari-button-arrow {
    position: relative;
    z-index: 2;
}

/* Arrow */
.sari-hero .sari-button-arrow {
    display: inline-block;
    font-size: 17px;
    line-height: 1;

    transition:
        transform .35s cubic-bezier(.22,1,.36,1);
}

.sari-hero .sari-button:hover .sari-button-arrow {
    transform: translate(3px, -3px);
}

.sari-hero .sari-button:active {
    transform: translateY(-1px);
}

/* =========================================================
   SCROLL INDICATOR
   ========================================================= */

.sari-scroll-indicator {
    transition:
        opacity .35s ease,
        transform .35s ease;
}

.sari-scroll-indicator:hover {
    transform: translateY(2px);
}

.sari-scroll-line {
    position: relative;
    overflow: hidden;
}

.sari-scroll-line::after {
    content: "";
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: currentColor;
    opacity: .65;
    animation: sariScrollLine 3.4s ease-in-out infinite;
}

@keyframes sariScrollLine {
    0%, 20% {
        transform: translateX(-100%);
        opacity: 0;
    }
    45% {
        opacity: .7;
    }
    75%, 100% {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Optional tiny motion on highlighted word */
.sari-hero-title span {
    display: inline-block;
    transition: transform .5s cubic-bezier(.22,1,.36,1);
}

.sari-hero-title:hover span {
    transform: translateY(-2px);
}

/* Mobile */
@media (max-width: 620px) {
    .sari-hero .sari-button {
        min-height: 50px;
        padding: 0 18px;
        gap: 11px;
    }

    .sari-hero.sari-hero-motion-ready .sari-hero-reveal {
        transform: translate3d(0, 14px, 0);
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .sari-hero.sari-hero-motion-ready .sari-hero-reveal,
    .sari-hero-logo,
    .sari-hero-brand-line,
    .sari-hero .sari-button,
    .sari-hero .sari-button::before,
    .sari-hero .sari-button::after,
    .sari-hero .sari-button-arrow,
    .sari-scroll-indicator,
    .sari-scroll-line::after,
    .sari-hero-title span {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
        transition: none !important;
    }
}
</style>

<script>
(function () {
    const hero = document.querySelector('.sari-hero');
    if (!hero) return;

    hero.classList.add('sari-hero-motion-ready');

    const items = hero.querySelectorAll('.sari-hero-reveal');

    requestAnimationFrame(() => {
        items.forEach(item => item.classList.add('is-visible'));
    });
})();
</script>