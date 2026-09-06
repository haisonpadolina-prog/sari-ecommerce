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

/* dark mode */
html.dark .sari-cta-v2,
body.dark .sari-cta-v2,
html.dark-mode .sari-cta-v2,
body.dark-mode .sari-cta-v2,
html[data-theme="dark"] .sari-cta-v2 {
    --cta-ink: #f7f3eb;
    --cta-muted: #c8c1b5;
    --cta-soft: #aaa295;
}

html.dark .sari-cta-v2__overlay,
body.dark .sari-cta-v2__overlay,
html.dark-mode .sari-cta-v2__overlay,
body.dark-mode .sari-cta-v2__overlay,
html[data-theme="dark"] .sari-cta-v2__overlay {
    background: linear-gradient(
        90deg,
        rgba(16,14,11,.985) 0%,
        rgba(16,14,11,.95) 31%,
        rgba(16,14,11,.83) 47%,
        rgba(16,14,11,.40) 67%,
        rgba(16,14,11,.08) 100%
    );
}

html.dark .sari-cta-v2__logo,
body.dark .sari-cta-v2__logo,
html.dark-mode .sari-cta-v2__logo,
body.dark-mode .sari-cta-v2__logo,
html[data-theme="dark"] .sari-cta-v2__logo {
    filter: brightness(0) invert(1);
}

html.dark .sari-cta-v2__floating,
body.dark .sari-cta-v2__floating,
html.dark-mode .sari-cta-v2__floating,
body.dark-mode .sari-cta-v2__floating,
html[data-theme="dark"] .sari-cta-v2__floating {
    background: rgba(28,25,20,.80);
    border-color: rgba(255,255,255,.10);
    box-shadow: 0 18px 45px rgba(0,0,0,.24);
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
