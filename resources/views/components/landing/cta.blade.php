<style>
    /* ============================================================
       SARI EDITORIAL CTA
       Fully isolated component
       ============================================================ */

    .sari-editorial-hero {
        position: relative;
        width: 100%;
        min-height: 720px;

        display: flex;
        align-items: center;

        overflow: hidden;
        isolation: isolate;

        background:
            url("{{ asset('images/sari-hero-bg.png') }}")
            center center / cover no-repeat;

        font-family: 'Poppins', sans-serif;
    }


    /* Soft overlay */

    .sari-editorial-hero::before {
        content: "";
        position: absolute;
        inset: 0;

        background:
            linear-gradient(
                90deg,
                rgba(250, 247, 240, 0.97) 0%,
                rgba(250, 247, 240, 0.92) 32%,
                rgba(250, 247, 240, 0.42) 63%,
                rgba(250, 247, 240, 0.05) 100%
            );

        z-index: -1;
    }


    /* ============================================================
       CONTAINER
       ============================================================ */

    .sari-editorial-hero__container {
        position: relative;

        width: min(1400px, 100%);

        margin: 0 auto;

        padding: 80px 7%;
    }


    .sari-editorial-hero__content {
        width: min(720px, 100%);
    }


    /* ============================================================
       LOGO
       ============================================================ */

    .sari-editorial-hero__logo {
        display: block;

        width: 190px;
        height: auto;

        margin-bottom: 12px;

        object-fit: contain;

        /*
         * Your current logo is white.
         * brightness(0) converts it to a clean black logo
         * so it becomes visible on the light background.
         */
        filter: brightness(0);
    }


    /* Gold tagline */

    .sari-editorial-hero__tagline {
        margin-bottom: 48px;

        color: #c98a08;

        font-size: 11px;
        font-weight: 700;

        letter-spacing: 0.30em;

        text-transform: uppercase;
    }


    /* ============================================================
       HEADLINE
       ============================================================ */

    .sari-editorial-hero__title {
        margin: 0;

        color: #111111;

        font-size: clamp(
            58px,
            7vw,
            108px
        );

        font-weight: 700;

        line-height: 0.91;

        letter-spacing: -0.065em;
    }


    .sari-editorial-hero__title-accent {
        color: #c98a08;
    }


    /* ============================================================
       DESCRIPTION
       ============================================================ */

    .sari-editorial-hero__description {
        width: min(620px, 100%);

        margin: 32px 0 0;

        color: #514d47;

        font-size: 17px;
        font-weight: 400;

        line-height: 1.7;
    }


    /* ============================================================
       SINGLE CTA BUTTON
       ============================================================ */

    .sari-editorial-hero__actions {
        margin-top: 38px;
    }


    .sari-hero-enter-btn {
        min-width: 205px;
        height: 58px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 16px;

        padding: 0 32px;

        border: 1px solid #c98a08;
        border-radius: 10px;

        background: #c98a08;

        color: #ffffff !important;

        font-size: 14px;
        font-weight: 400;

        letter-spacing: 0.08em;

        text-decoration: none;
        text-transform: uppercase;

        transition:
            transform .25s ease,
            background .25s ease,
            border-color .25s ease,
            box-shadow .25s ease;
    }


    .sari-hero-enter-btn:hover {
        background: #b77b05;
        border-color: #b77b05;

        color: #ffffff !important;

        transform: translateY(-2px);

        box-shadow:
            0 12px 30px rgba(201, 138, 8, 0.20);
    }


    .sari-hero-enter-arrow {
        font-size: 20px;
        font-weight: 300;

        color: #ffffff !important;

        transition:
            transform .25s ease;
    }


    .sari-hero-enter-btn:hover
    .sari-hero-enter-arrow {
        transform: translateX(5px);
    }


    /* ============================================================
       SMALL LABEL
       ============================================================ */

    .sari-editorial-hero__bottom-label {
        position: absolute;

        left: 7%;
        bottom: 28px;

        display: flex;
        align-items: center;

        gap: 12px;

        color: #8b8479;

        font-size: 9px;
        font-weight: 600;

        letter-spacing: .18em;

        text-transform: uppercase;
    }


    .sari-editorial-hero__bottom-label::before {
        content: "";

        width: 32px;
        height: 1px;

        background: #c98a08;
    }


    /* ============================================================
       DARK MODE
       ============================================================ */

    html.dark .sari-editorial-hero::before,
    body.dark .sari-editorial-hero::before,
    html.dark-mode .sari-editorial-hero::before,
    body.dark-mode .sari-editorial-hero::before,
    html[data-theme="dark"] .sari-editorial-hero::before {

        background:
            linear-gradient(
                90deg,
                rgba(18, 16, 13, .97) 0%,
                rgba(18, 16, 13, .91) 35%,
                rgba(18, 16, 13, .40) 68%,
                rgba(18, 16, 13, .10) 100%
            );
    }


    html.dark .sari-editorial-hero__title,
    body.dark .sari-editorial-hero__title,
    html.dark-mode .sari-editorial-hero__title,
    body.dark-mode .sari-editorial-hero__title,
    html[data-theme="dark"] .sari-editorial-hero__title {

        color: #f7f3eb;
    }


    html.dark .sari-editorial-hero__description,
    body.dark .sari-editorial-hero__description,
    html.dark-mode .sari-editorial-hero__description,
    body.dark-mode .sari-editorial-hero__description,
    html[data-theme="dark"] .sari-editorial-hero__description {

        color: #c5beb1;
    }


    /* ============================================================
       RESPONSIVE
       ============================================================ */

    @media (max-width: 900px) {

        .sari-editorial-hero {
            min-height: 680px;

            background-position: 65% center;
        }


        .sari-editorial-hero::before {

            background:
                linear-gradient(
                    90deg,
                    rgba(250, 247, 240, .97) 0%,
                    rgba(250, 247, 240, .88) 58%,
                    rgba(250, 247, 240, .35) 100%
                );
        }


        .sari-editorial-hero__container {
            padding: 70px 28px;
        }


        .sari-editorial-hero__logo {
            width: 155px;
        }


        .sari-editorial-hero__title {
            font-size: clamp(
                54px,
                12vw,
                80px
            );
        }
    }


    @media (max-width: 600px) {

        .sari-editorial-hero {
            min-height: 700px;

            align-items: flex-start;

            background-position: 67% center;
        }


        .sari-editorial-hero::before {

            background:
                linear-gradient(
                    180deg,
                    rgba(250, 247, 240, .97) 0%,
                    rgba(250, 247, 240, .92) 52%,
                    rgba(250, 247, 240, .48) 100%
                );
        }


        .sari-editorial-hero__container {
            padding: 65px 22px;
        }


        .sari-editorial-hero__logo {
            width: 135px;
        }


        .sari-editorial-hero__tagline {
            margin-bottom: 34px;

            font-size: 9px;

            letter-spacing: .24em;
        }


        .sari-editorial-hero__title {
            font-size: clamp(
                48px,
                14vw,
                68px
            );

            line-height: .94;
        }


        .sari-editorial-hero__description {
            margin-top: 24px;

            font-size: 14px;

            line-height: 1.7;
        }


        .sari-editorial-hero__actions {
            margin-top: 30px;
        }


        .sari-hero-enter-btn {
            width: 100%;
            max-width: 230px;
        }


        .sari-editorial-hero__bottom-label {
            display: none;
        }
    }


    @media (prefers-reduced-motion: reduce) {

        .sari-hero-enter-btn,
        .sari-hero-enter-arrow {
            transition: none;
        }
    }
</style>


<section
    class="sari-editorial-hero"
    id="register"
    aria-labelledby="sari-editorial-hero-title"
>

    <div class="sari-editorial-hero__container">

        <div class="sari-editorial-hero__content">

            <!-- SARI LOGO -->
            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="sari-editorial-hero__logo"
            >


            <!-- TAGLINE -->
            <div class="sari-editorial-hero__tagline">
                Elevated Everyday
            </div>


            <!-- HEADLINE -->
            <h1
                class="sari-editorial-hero__title"
                id="sari-editorial-hero-title"
            >
                Elevate the way
                <br>
                you <span class="sari-editorial-hero__title-accent">shop.</span>
            </h1>


            <!-- DESCRIPTION -->
            <p class="sari-editorial-hero__description">
                Step into a marketplace designed around discovery,
                connection, and everyday convenience.
            </p>


            <!-- SINGLE BUTTON -->
            <div class="sari-editorial-hero__actions">

                <a
                    href="{{ route('login') }}"
                    class="sari-hero-enter-btn"
                >
                    <span>Enter SARI</span>

                    <span
                        class="sari-hero-enter-arrow"
                        aria-hidden="true"
                    >
                        →
                    </span>
                </a>

            </div>

        </div>

    </div>


    <div class="sari-editorial-hero__bottom-label">
        Elevated Everyday
    </div>

</section>

<style>
/* =========================================================
   SARI EDITORIAL HERO — CINEMATIC MOTION ENHANCEMENT
   Smooth, premium, restrained.
   ========================================================= */

/* Background settles in gently */
.sari-editorial-hero {
    background-size: 103% auto;
    transition:
        background-size 1.4s cubic-bezier(.22,1,.36,1),
        background-position 1.2s cubic-bezier(.22,1,.36,1);
}

.sari-editorial-hero.sari-editorial-motion-ready {
    background-size: 100% auto;
}

/* Overlay transition for theme changes */
.sari-editorial-hero::before {
    transition: background .5s ease;
}

/* Content reveal */
.sari-editorial-hero.sari-editorial-motion-ready .sari-editorial-reveal {
    opacity: 0;
    transform: translate3d(0, 20px, 0);
    transition:
        opacity .8s cubic-bezier(.22,1,.36,1),
        transform .9s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}

.sari-editorial-hero.sari-editorial-motion-ready .sari-editorial-reveal.is-visible {
    opacity: 1;
    transform: translate3d(0, 0, 0);
}

.sari-editorial-delay-1 { transition-delay: .05s !important; }
.sari-editorial-delay-2 { transition-delay: .14s !important; }
.sari-editorial-delay-3 { transition-delay: .24s !important; }
.sari-editorial-delay-4 { transition-delay: .34s !important; }
.sari-editorial-delay-5 { transition-delay: .46s !important; }

/* Logo polish */
.sari-editorial-hero__logo {
    transition:
        transform .6s cubic-bezier(.22,1,.36,1),
        filter .4s ease;
}

.sari-editorial-hero__logo:hover {
    transform: translateY(-2px) scale(1.01);
}

/* Accent word gets tiny editorial response */
.sari-editorial-hero__title-accent {
    display: inline-block;
    transition:
        transform .5s cubic-bezier(.22,1,.36,1),
        text-shadow .4s ease;
}

.sari-editorial-hero__title:hover .sari-editorial-hero__title-accent {
    transform: translateY(-2px);
    text-shadow: 0 8px 22px rgba(201,138,8,.10);
}

/* Tagline gold line effect */
.sari-editorial-hero__tagline {
    position: relative;
    width: fit-content;
}

.sari-editorial-hero__tagline::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -9px;
    width: 28px;
    height: 1px;
    background: #c98a08;
    transform-origin: left center;
    transition: width .45s cubic-bezier(.22,1,.36,1);
}

.sari-editorial-hero__tagline:hover::after {
    width: 46px;
}

/* =========================================================
   CTA — PREMIUM BUTTON
   ========================================================= */

.sari-hero-enter-btn {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    font-weight: 600;

    transition:
        transform .35s cubic-bezier(.22,1,.36,1),
        background-color .35s ease,
        border-color .35s ease,
        box-shadow .4s ease;
}

.sari-hero-enter-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background: linear-gradient(
        110deg,
        rgba(255,255,255,.05),
        rgba(255,255,255,.16) 48%,
        rgba(255,255,255,.04)
    );
    opacity: 0;
    transition: opacity .35s ease;
}

.sari-hero-enter-btn::after {
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
    transition: left .85s cubic-bezier(.22,1,.36,1);
    pointer-events: none;
}

.sari-hero-enter-btn > span {
    position: relative;
    z-index: 2;
}

.sari-hero-enter-btn:hover {
    transform: translateY(-3px);
    box-shadow:
        0 16px 34px rgba(201,138,8,.24);
}

.sari-hero-enter-btn:hover::before {
    opacity: 1;
}

.sari-hero-enter-btn:hover::after {
    left: 135%;
}

.sari-hero-enter-arrow {
    transition: transform .35s cubic-bezier(.22,1,.36,1);
}

.sari-hero-enter-btn:hover .sari-hero-enter-arrow {
    transform: translateX(5px);
}

.sari-hero-enter-btn:active {
    transform: translateY(-1px);
}

/* Bottom label: quiet motion */
.sari-editorial-hero__bottom-label {
    opacity: .85;
    transition:
        opacity .35s ease,
        transform .35s ease;
}

.sari-editorial-hero__bottom-label:hover {
    opacity: 1;
    transform: translateX(3px);
}

.sari-editorial-hero__bottom-label::before {
    transform-origin: left center;
    animation: sariEditorialLine 4.5s ease-in-out infinite;
}

@keyframes sariEditorialLine {
    0%, 100% {
        transform: scaleX(.72);
        opacity: .55;
    }
    50% {
        transform: scaleX(1);
        opacity: 1;
    }
}

/* Desktop hover: almost imperceptible cinematic zoom */
@media (hover: hover) and (pointer: fine) {
    .sari-editorial-hero:hover {
        background-size: 101% auto;
    }
}

@media (max-width: 900px) {
    .sari-editorial-hero,
    .sari-editorial-hero.sari-editorial-motion-ready {
        background-size: cover;
    }

    .sari-editorial-hero.sari-editorial-motion-ready .sari-editorial-reveal {
        transform: translate3d(0, 15px, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-editorial-hero,
    .sari-editorial-hero.sari-editorial-motion-ready .sari-editorial-reveal,
    .sari-editorial-hero__logo,
    .sari-editorial-hero__title-accent,
    .sari-editorial-hero__tagline::after,
    .sari-hero-enter-btn,
    .sari-hero-enter-btn::before,
    .sari-hero-enter-btn::after,
    .sari-hero-enter-arrow,
    .sari-editorial-hero__bottom-label,
    .sari-editorial-hero__bottom-label::before {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
        transition: none !important;
    }
}
</style>

<script>
(function () {
    const hero = document.querySelector('.sari-editorial-hero');
    if (!hero) return;

    const logo = hero.querySelector('.sari-editorial-hero__logo');
    const tagline = hero.querySelector('.sari-editorial-hero__tagline');
    const title = hero.querySelector('.sari-editorial-hero__title');
    const description = hero.querySelector('.sari-editorial-hero__description');
    const actions = hero.querySelector('.sari-editorial-hero__actions');
    const bottomLabel = hero.querySelector('.sari-editorial-hero__bottom-label');

    [
        [logo, 'sari-editorial-delay-1'],
        [tagline, 'sari-editorial-delay-2'],
        [title, 'sari-editorial-delay-3'],
        [description, 'sari-editorial-delay-4'],
        [actions, 'sari-editorial-delay-5'],
        [bottomLabel, 'sari-editorial-delay-5']
    ].forEach(([el, delay]) => {
        if (!el) return;
        el.classList.add('sari-editorial-reveal', delay);
    });

    hero.classList.add('sari-editorial-motion-ready');

    requestAnimationFrame(() => {
        hero.querySelectorAll('.sari-editorial-reveal').forEach(el => {
            el.classList.add('is-visible');
        });
    });
})();
</script>