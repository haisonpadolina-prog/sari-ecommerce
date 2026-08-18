<style>
/* ============================================================
   SARI TRUST — FULLY ISOLATED
   No shared .sari-* utility classes.
   ============================================================ */

.sari-trust-final {
    --tf-bg: #fbf8f2;
    --tf-card: #ffffff;
    --tf-ink: #111111;
    --tf-muted: #57534d;
    --tf-gold: #c98a08;
    --tf-border: #e6ded1;
    --tf-soft: #f3eee5;

    width: 100%;
    position: relative;
    padding: 125px 24px 110px;
    background: var(--tf-bg);
    color: var(--tf-ink);
    font-family: 'Poppins', sans-serif;
    overflow: hidden;
    isolation: isolate;
}

.sari-trust-final *,
.sari-trust-final *::before,
.sari-trust-final *::after {
    box-sizing: border-box;
}

.sari-trust-final__wrap {
    width: min(1320px, 100%);
    margin: 0 auto;
    position: relative;
}

.sari-trust-final__header {
    width: min(900px, 100%);
    margin: 0 auto 70px;
    text-align: center;
}

.sari-trust-final__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 26px;
    color: var(--tf-gold);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .30em;
    text-transform: uppercase;
}

.sari-trust-final__eyebrow::before,
.sari-trust-final__eyebrow::after {
    content: "";
    width: 34px;
    height: 1px;
    background: currentColor;
    opacity: .8;
}

.sari-trust-final__headline {
    margin: 0;
    color: var(--tf-ink);
    font-size: clamp(48px, 7vw, 82px);
    font-weight: 700;
    line-height: .96;
    letter-spacing: -.065em;
}

.sari-trust-final__headline-accent {
    color: var(--tf-gold);
}

.sari-trust-final__intro {
    width: min(760px, 100%);
    margin: 30px auto 0;
    color: var(--tf-muted);
    font-size: 16px;
    line-height: 1.75;
}

/* subtle decorative rings */
.sari-trust-final__rings {
    position: absolute;
    top: -60px;
    right: -115px;
    width: 260px;
    height: 260px;
    pointer-events: none;
    opacity: .7;
}

.sari-trust-final__rings::before,
.sari-trust-final__rings::after {
    content: "";
    position: absolute;
    border: 1px solid rgba(201, 138, 8, .16);
    border-radius: 50%;
}

.sari-trust-final__rings::before {
    inset: 0;
}

.sari-trust-final__rings::after {
    inset: 28px;
}

/* cards */
.sari-trust-final__cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    border: 1px solid var(--tf-border);
    border-radius: 16px;
    background: var(--tf-card);
    overflow: hidden;
    box-shadow: 0 25px 70px rgba(40, 30, 15, .055);
}

.sari-trust-final__card {
    position: relative;
    min-height: 475px;
    padding: 38px 34px 36px;
    border-right: 1px solid var(--tf-border);
    background: var(--tf-card);
    transition: transform .35s ease, background .35s ease, box-shadow .35s ease;
}

.sari-trust-final__card:last-child {
    border-right: 0;
}

.sari-trust-final__card:hover {
    z-index: 2;
    transform: translateY(-5px);
    background: #fffdf9;
    box-shadow: 0 20px 50px rgba(40, 30, 15, .10);
}

.sari-trust-final__badge {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 72px;
    border: 1px solid var(--tf-border);
    border-radius: 50%;
    background: var(--tf-soft);
    color: var(--tf-gold);
    font-size: 12px;
    font-weight: 700;
}

.sari-trust-final__card:nth-child(2) .sari-trust-final__badge,
.sari-trust-final__card:nth-child(4) .sari-trust-final__badge {
    background: var(--tf-gold);
    border-color: var(--tf-gold);
    color: #fff;
}

.sari-trust-final__card-title {
    max-width: 270px;
    margin: 0;
    color: var(--tf-ink);
    font-size: 25px;
    font-weight: 700;
    line-height: 1.12;
    letter-spacing: -.035em;
}

.sari-trust-final__rule {
    width: 52px;
    height: 2px;
    margin: 22px 0 26px;
    background: var(--tf-gold);
}

.sari-trust-final__card-text {
    max-width: 275px;
    margin: 0;
    color: var(--tf-muted);
    font-size: 15px;
    line-height: 1.75;
}

.sari-trust-final__icon {
    position: absolute;
    left: 34px;
    bottom: 34px;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--tf-soft);
    color: var(--tf-gold);
}

.sari-trust-final__icon svg {
    width: 31px;
    height: 31px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* dark mode */
html.dark .sari-trust-final,
body.dark .sari-trust-final,
html.dark-mode .sari-trust-final,
body.dark-mode .sari-trust-final,
html[data-theme="dark"] .sari-trust-final {
    --tf-bg: #15130e;
    --tf-card: #201d16;
    --tf-ink: #f5f1e8;
    --tf-muted: #c2bbad;
    --tf-gold: #d8a857;
    --tf-border: #3a3429;
    --tf-soft: #2a261e;
}

html.dark .sari-trust-final__card:hover,
body.dark .sari-trust-final__card:hover,
html.dark-mode .sari-trust-final__card:hover,
body.dark-mode .sari-trust-final__card:hover,
html[data-theme="dark"] .sari-trust-final__card:hover {
    background: #252119;
    box-shadow: 0 20px 50px rgba(0,0,0,.25);
}

/* responsive */
@media (max-width: 1050px) {
    .sari-trust-final__cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .sari-trust-final__card:nth-child(2) {
        border-right: 0;
    }

    .sari-trust-final__card:nth-child(-n+2) {
        border-bottom: 1px solid var(--tf-border);
    }
}

@media (max-width: 620px) {
    .sari-trust-final {
        padding: 90px 16px 80px;
    }

    .sari-trust-final__header {
        margin-bottom: 48px;
    }

    .sari-trust-final__eyebrow {
        font-size: 10px;
        letter-spacing: .23em;
        gap: 10px;
    }

    .sari-trust-final__eyebrow::before,
    .sari-trust-final__eyebrow::after {
        width: 22px;
    }

    .sari-trust-final__headline {
        font-size: clamp(43px, 13vw, 62px);
    }

    .sari-trust-final__intro {
        margin-top: 22px;
        font-size: 14px;
    }

    .sari-trust-final__cards {
        grid-template-columns: 1fr;
        border-radius: 12px;
    }

    .sari-trust-final__card,
    .sari-trust-final__card:nth-child(2) {
        min-height: 360px;
        padding: 30px 26px;
        border-right: 0;
        border-bottom: 1px solid var(--tf-border);
    }

    .sari-trust-final__card:last-child {
        border-bottom: 0;
    }

    .sari-trust-final__badge {
        margin-bottom: 55px;
    }

    .sari-trust-final__card-title {
        font-size: 23px;
    }

    .sari-trust-final__card-text {
        font-size: 14px;
    }

    .sari-trust-final__icon {
        left: 26px;
        bottom: 26px;
    }

    .sari-trust-final__rings {
        right: -150px;
        top: -80px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-trust-final__card {
        transition: none;
    }
}
</style>

<section class="sari-trust-final" id="sari-trust-final-section" aria-labelledby="sari-trust-final-headline">
    <div class="sari-trust-final__wrap">

        <div class="sari-trust-final__rings" aria-hidden="true"></div>

        <header class="sari-trust-final__header">
            <div class="sari-trust-final__eyebrow">Trust</div>

            <h2 class="sari-trust-final__headline" id="sari-trust-final-headline">
                Designed around
                <br>
                <span class="sari-trust-final__headline-accent">confidence.</span>
            </h2>

            <p class="sari-trust-final__intro">
                Every part of SARI is designed to make marketplace interactions
                clearer, more connected, and more trustworthy.
            </p>
        </header>

        <div class="sari-trust-final__cards">

            <article class="sari-trust-final__card">
                <div class="sari-trust-final__badge">01</div>

                <h3 class="sari-trust-final__card-title">
                    Verified Users
                </h3>

                <div class="sari-trust-final__rule"></div>

                <p class="sari-trust-final__card-text">
                    Build confidence through a marketplace designed around
                    responsible participation.
                </p>

                <div class="sari-trust-final__icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32">
                        <path d="M16 3l10 4v7c0 7-4.2 12-10 15-5.8-3-10-8-10-15V7l10-4z"/>
                        <path d="M11 16l3.2 3.2L21.5 12"/>
                    </svg>
                </div>
            </article>

            <article class="sari-trust-final__card">
                <div class="sari-trust-final__badge">02</div>

                <h3 class="sari-trust-final__card-title">
                    Order Transparency
                </h3>

                <div class="sari-trust-final__rule"></div>

                <p class="sari-trust-final__card-text">
                    Keep important order information visible throughout the
                    shopping journey.
                </p>

                <div class="sari-trust-final__icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32">
                        <path d="M8 4h13l4 4v20H8z"/>
                        <path d="M21 4v5h4"/>
                        <path d="M12 13h9M12 17h9M12 21h5"/>
                        <circle cx="22" cy="22" r="4"/>
                        <path d="M25 25l3 3"/>
                    </svg>
                </div>
            </article>

            <article class="sari-trust-final__card">
                <div class="sari-trust-final__badge">03</div>

                <h3 class="sari-trust-final__card-title">
                    Connected Delivery
                </h3>

                <div class="sari-trust-final__rule"></div>

                <p class="sari-trust-final__card-text">
                    Connect customers, sellers, and couriers around a
                    coordinated delivery process.
                </p>

                <div class="sari-trust-final__icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32">
                        <path d="M3 9h17v14H3z"/>
                        <path d="M20 14h5l4 4v5h-9z"/>
                        <circle cx="9" cy="25" r="3"/>
                        <circle cx="25" cy="25" r="3"/>
                        <path d="M12 25h10M20 18h5"/>
                    </svg>
                </div>
            </article>

            <article class="sari-trust-final__card">
                <div class="sari-trust-final__badge">04</div>

                <h3 class="sari-trust-final__card-title">
                    Responsible Marketplace
                </h3>

                <div class="sari-trust-final__rule"></div>

                <p class="sari-trust-final__card-text">
                    Create a digital marketplace experience built around
                    clarity, responsibility, and trust.
                </p>

                <div class="sari-trust-final__icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32">
                        <path d="M16 27S5 20.5 5 12.5C5 8.5 8 6 11.5 6c2.2 0 3.8 1.1 4.5 2.7C16.7 7.1 18.3 6 20.5 6 24 6 27 8.5 27 12.5 27 20.5 16 27 16 27z"/>
                        <path d="M10 15c2 0 3.2 1 4.3 2.2L17 14.5c1.1-1.1 2.4-1.1 3.4-.2l1.5 1.4"/>
                    </svg>
                </div>
            </article>

        </div>
    </div>
</section>


<style>
/* =========================================================
   SARI TRUST — PREMIUM MOTION ENHANCEMENT
   Smooth, restrained, editorial.
   ========================================================= */

.sari-trust-final.sari-trust-motion-ready .sari-trust-reveal {
    opacity: 0;
    transform: translate3d(0, 18px, 0);
    transition:
        opacity .75s cubic-bezier(.22,1,.36,1),
        transform .85s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}

.sari-trust-final.sari-trust-motion-ready .sari-trust-reveal.is-visible {
    opacity: 1;
    transform: translate3d(0,0,0);
}

.sari-trust-delay-1 { transition-delay: .08s !important; }
.sari-trust-delay-2 { transition-delay: .16s !important; }
.sari-trust-delay-3 { transition-delay: .24s !important; }
.sari-trust-delay-4 { transition-delay: .32s !important; }

/* Decorative rings: slow breathing, no spinning */
.sari-trust-final__rings {
    animation: sariTrustRings 7s ease-in-out infinite;
}

@keyframes sariTrustRings {
    0%, 100% {
        transform: scale(1);
        opacity: .55;
    }
    50% {
        transform: scale(1.035);
        opacity: .78;
    }
}

/* Cards feel less flat */
.sari-trust-final__card {
    transition:
        transform .45s cubic-bezier(.22,1,.36,1),
        background-color .35s ease,
        border-color .35s ease,
        box-shadow .45s ease;
}

.sari-trust-final__card:hover {
    transform: translateY(-6px);
    box-shadow:
        0 26px 60px rgba(40,30,15,.10);
}

/* Gold rule expands on hover */
.sari-trust-final__rule {
    transform-origin: left center;
    transition:
        width .45s cubic-bezier(.22,1,.36,1),
        opacity .35s ease;
}

.sari-trust-final__card:hover .sari-trust-final__rule {
    width: 72px;
}

/* Badge has tiny premium lift */
.sari-trust-final__badge {
    transition:
        transform .4s cubic-bezier(.22,1,.36,1),
        box-shadow .4s ease,
        background-color .35s ease,
        border-color .35s ease,
        color .35s ease;
}

.sari-trust-final__card:hover .sari-trust-final__badge {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(201,138,8,.10);
}

/* Icon follows slightly after badge */
.sari-trust-final__icon {
    transition:
        transform .45s cubic-bezier(.22,1,.36,1),
        box-shadow .4s ease,
        background-color .35s ease,
        color .35s ease;
}

.sari-trust-final__card:hover .sari-trust-final__icon {
    transform: translateY(-3px) scale(1.025);
    box-shadow: 0 12px 26px rgba(201,138,8,.08);
}

/* SVG stroke draw feel on reveal */
.sari-trust-final__icon svg path,
.sari-trust-final__icon svg circle {
    transition:
        stroke .35s ease,
        opacity .35s ease;
}

/* Headline accent receives a gentle settle */
.sari-trust-final__headline-accent {
    display: inline-block;
    transition: transform .5s cubic-bezier(.22,1,.36,1);
}

.sari-trust-final__header:hover .sari-trust-final__headline-accent {
    transform: translateY(-2px);
}

/* Slight text polish */
.sari-trust-final__card-title,
.sari-trust-final__card-text {
    transition:
        transform .4s cubic-bezier(.22,1,.36,1),
        color .35s ease;
}

.sari-trust-final__card:hover .sari-trust-final__card-title {
    transform: translateY(-1px);
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .sari-trust-final.sari-trust-motion-ready .sari-trust-reveal,
    .sari-trust-final__rings,
    .sari-trust-final__card,
    .sari-trust-final__rule,
    .sari-trust-final__badge,
    .sari-trust-final__icon,
    .sari-trust-final__headline-accent,
    .sari-trust-final__card-title,
    .sari-trust-final__card-text {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        animation: none !important;
    }
}
</style>

<script>
(function () {
    const section = document.querySelector('.sari-trust-final');
    if (!section) return;

    section.classList.add('sari-trust-motion-ready');

    const header = section.querySelector('.sari-trust-final__header');
    if (header) header.classList.add('sari-trust-reveal');

    section.querySelectorAll('.sari-trust-final__card').forEach((card, index) => {
        card.classList.add('sari-trust-reveal', 'sari-trust-delay-' + (index + 1));
    });

    const items = section.querySelectorAll('.sari-trust-reveal');

    if (!('IntersectionObserver' in window)) {
        items.forEach(el => el.classList.add('is-visible'));
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

    items.forEach(el => observer.observe(el));
})();
</script>