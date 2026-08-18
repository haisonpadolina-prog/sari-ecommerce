<style>
/* ============================================================
   SARI INTRO SECTION
   Light / Dark Mode + Responsive
   Fully scoped to .sari-intro
   ============================================================ */

.sari-intro {
    position: relative;
    overflow: hidden;

    background: #faf8f3;
    color: #25221e;

    transition:
        background-color .35s ease,
        color .35s ease;
}


/* ============================================================
   EYEBROW
   ============================================================ */

.sari-intro .sari-intro-eyebrow-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
}

.sari-intro .sari-eyebrow {
    color: #9b742f;
    transition: color .35s ease;
}

.sari-intro .sari-intro-rule {
    display: block;

    width: 48px;
    height: 1px;

    background: rgba(155, 116, 47, .35);

    transition: background-color .35s ease;
}


/* ============================================================
   TITLE
   ============================================================ */

.sari-intro .sari-intro-title {
    color: #25221e;
    transition: color .35s ease;
}

.sari-intro .sari-intro-title em {
    color: #b18438;
    transition: color .35s ease;
}


/* ============================================================
   DESCRIPTION
   ============================================================ */

.sari-intro .sari-intro-copy {
    color: #68625a;
    transition: color .35s ease;
}


/* ============================================================
   CONNECTION SYSTEM
   ============================================================ */

.sari-intro .sari-connect {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 100%;

    margin-top: 64px;

    gap: 0;
}


/* ============================================================
   CONNECTION NODE
   ============================================================ */

.sari-intro .sari-connect-node {
    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    flex: 0 0 auto;

    color: #4e4942;

    transition:
        color .35s ease,
        transform .3s ease;
}

.sari-intro .sari-connect-node:hover {
    transform: translateY(-3px);
}


/* ============================================================
   ICON CIRCLE
   ============================================================ */

.sari-intro .sari-connect-icon {
    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid rgba(70, 65, 58, .14);
    border-radius: 50%;

    background: rgba(255, 255, 255, .55);

    color: #4e4942;

    transition:
        background-color .35s ease,
        border-color .35s ease,
        color .35s ease,
        box-shadow .35s ease,
        transform .3s ease;
}


/* SVG ICON */

.sari-intro .sari-connect-icon svg {
    width: 24px;
    height: 24px;

    display: block;

    color: inherit;
    stroke: currentColor;
}


/* ============================================================
   LABEL
   ============================================================ */

.sari-intro .sari-connect-label {
    margin-top: 12px;

    color: #69635b;

    font-size: 11px;
    font-weight: 500;

    letter-spacing: .08em;
    text-transform: uppercase;

    white-space: nowrap;

    transition: color .35s ease;
}


/* ============================================================
   CONNECTION LINES
   ============================================================ */

.sari-intro .sari-connect-line {
    flex: 1 1 100px;

    max-width: 130px;
    min-width: 35px;

    height: 1px;

    margin: 0 18px;

    background: rgba(70, 65, 58, .16);

    transition: background-color .35s ease;
}


/* ============================================================
   CENTER SARI LOGO
   White logo + dark gray background
   ============================================================ */

.sari-intro .sari-connect-node-center {
    position: relative;
}

.sari-intro .sari-connect-mark {
    width: 72px;
    height: 72px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid rgba(155, 116, 47, .30);
    border-radius: 50%;

    /*
     * Dark gray background in LIGHT MODE
     */
    background: #292824;

    box-shadow:
        0 8px 28px rgba(45, 40, 32, .10);

    transition:
        background-color .35s ease,
        border-color .35s ease,
        box-shadow .35s ease;
}


/*
 * IMPORTANT:
 * The original SARI logo remains WHITE.
 * No brightness() or invert() filter.
 */

.sari-intro .sari-connect-mark img {
    width: 42px;
    height: auto;

    display: block;

    object-fit: contain;

    filter: none !important;
}


/* ============================================================
   DARK MODE
   ============================================================ */

html.dark .sari-intro,
body.dark .sari-intro,
html.dark-mode .sari-intro,
body.dark-mode .sari-intro,
html[data-theme="dark"] .sari-intro {

    background: #151310;
    color: #f4f0e8;
}


/* ============================================================
   DARK MODE — EYEBROW
   ============================================================ */

html.dark .sari-intro .sari-eyebrow,
body.dark .sari-intro .sari-eyebrow,
html.dark-mode .sari-intro .sari-eyebrow,
body.dark-mode .sari-intro .sari-eyebrow,
html[data-theme="dark"] .sari-intro .sari-eyebrow {

    color: #d2a65c;
}


/* ============================================================
   DARK MODE — RULES
   ============================================================ */

html.dark .sari-intro .sari-intro-rule,
body.dark .sari-intro .sari-intro-rule,
html.dark-mode .sari-intro .sari-intro-rule,
body.dark-mode .sari-intro .sari-intro-rule,
html[data-theme="dark"] .sari-intro .sari-intro-rule {

    background: rgba(210, 166, 92, .30);
}


/* ============================================================
   DARK MODE — TITLE
   ============================================================ */

html.dark .sari-intro .sari-intro-title,
body.dark .sari-intro .sari-intro-title,
html.dark-mode .sari-intro .sari-intro-title,
body.dark-mode .sari-intro .sari-intro-title,
html[data-theme="dark"] .sari-intro .sari-intro-title {

    color: #f4f0e8;
}


/* ============================================================
   DARK MODE — TITLE ACCENT
   ============================================================ */

html.dark .sari-intro .sari-intro-title em,
body.dark .sari-intro .sari-intro-title em,
html.dark-mode .sari-intro .sari-intro-title em,
body.dark-mode .sari-intro .sari-intro-title em,
html[data-theme="dark"] .sari-intro .sari-intro-title em {

    color: #d2a65c;
}


/* ============================================================
   DARK MODE — DESCRIPTION
   ============================================================ */

html.dark .sari-intro .sari-intro-copy,
body.dark .sari-intro .sari-intro-copy,
html.dark-mode .sari-intro .sari-intro-copy,
body.dark-mode .sari-intro .sari-intro-copy,
html[data-theme="dark"] .sari-intro .sari-intro-copy {

    color: #bdb6aa;
}


/* ============================================================
   DARK MODE — ICON NODES
   ============================================================ */

html.dark .sari-intro .sari-connect-node,
body.dark .sari-intro .sari-connect-node,
html.dark-mode .sari-intro .sari-connect-node,
body.dark-mode .sari-intro .sari-connect-node,
html[data-theme="dark"] .sari-intro .sari-connect-node {

    color: #ddd6ca;
}


/* ============================================================
   DARK MODE — ICON CIRCLES
   ============================================================ */

html.dark .sari-intro .sari-connect-icon,
body.dark .sari-intro .sari-connect-icon,
html.dark-mode .sari-intro .sari-connect-icon,
body.dark-mode .sari-intro .sari-connect-icon,
html[data-theme="dark"] .sari-intro .sari-connect-icon {

    border-color: rgba(230, 224, 214, .18);

    background: rgba(255, 255, 255, .045);

    color: #ddd6ca;

    box-shadow:
        0 6px 20px rgba(0, 0, 0, .15);
}


/* ============================================================
   DARK MODE — LABELS
   ============================================================ */

html.dark .sari-intro .sari-connect-label,
body.dark .sari-intro .sari-connect-label,
html.dark-mode .sari-intro .sari-connect-label,
body.dark-mode .sari-intro .sari-connect-label,
html[data-theme="dark"] .sari-intro .sari-connect-label {

    color: #aaa297;
}


/* ============================================================
   DARK MODE — CONNECTION LINES
   ============================================================ */

html.dark .sari-intro .sari-connect-line,
body.dark .sari-intro .sari-connect-line,
html.dark-mode .sari-intro .sari-connect-line,
body.dark-mode .sari-intro .sari-connect-line,
html[data-theme="dark"] .sari-intro .sari-connect-line {

    background: rgba(230, 224, 214, .16);
}


/* ============================================================
   DARK MODE — SARI CENTER LOGO
   ============================================================ */

html.dark .sari-intro .sari-connect-mark,
body.dark .sari-intro .sari-connect-mark,
html.dark-mode .sari-intro .sari-connect-mark,
body.dark-mode .sari-intro .sari-connect-mark,
html[data-theme="dark"] .sari-intro .sari-connect-mark {

    /*
     * Slightly darker background for dark mode
     */
    background: #24221f;

    border-color: rgba(210, 166, 92, .35);

    box-shadow:
        0 10px 30px rgba(0, 0, 0, .25);
}


/*
 * NO FILTER HERE.
 *
 * The original SARI logo stays WHITE.
 */

html.dark .sari-intro .sari-connect-mark img,
body.dark .sari-intro .sari-connect-mark img,
html.dark-mode .sari-intro .sari-connect-mark img,
body.dark-mode .sari-intro .sari-connect-mark img,
html[data-theme="dark"] .sari-intro .sari-connect-mark img {

    filter: none !important;
}


/* ============================================================
   TABLET
   ============================================================ */

@media (max-width: 900px) {

    .sari-intro .sari-connect {
        margin-top: 50px;
    }

    .sari-intro .sari-connect-icon {
        width: 46px;
        height: 46px;
    }

    .sari-intro .sari-connect-icon svg {
        width: 21px;
        height: 21px;
    }

    .sari-intro .sari-connect-mark {
        width: 64px;
        height: 64px;
    }

    .sari-intro .sari-connect-mark img {
        width: 37px;
    }

    .sari-intro .sari-connect-line {
        margin: 0 10px;
    }
}


/* ============================================================
   MOBILE
   ============================================================ */

@media (max-width: 600px) {

    .sari-intro .sari-intro-eyebrow-row {
        gap: 10px;
    }

    .sari-intro .sari-intro-rule {
        width: 28px;
    }


    /*
     * Change connection system to grid.
     * Prevents icons from becoming cramped.
     */

    .sari-intro .sari-connect {

        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 28px 14px;

        margin-top: 42px;
    }


    /* Hide desktop lines */

    .sari-intro .sari-connect-line {
        display: none;
    }


    /* Nodes */

    .sari-intro .sari-connect-node {

        min-width: 0;
        width: 100%;
    }


    /* SARI logo goes to top center */

    .sari-intro .sari-connect-node-center {

        grid-column: 1 / -1;

        grid-row: 1;

        order: -1;
    }


    /* Icons */

    .sari-intro .sari-connect-icon {

        width: 48px;
        height: 48px;
    }

    .sari-intro .sari-connect-icon svg {

        width: 22px;
        height: 22px;
    }


    /* Labels */

    .sari-intro .sari-connect-label {

        margin-top: 9px;

        font-size: 10px;

        letter-spacing: .07em;
    }


    /* Center SARI logo */

    .sari-intro .sari-connect-mark {

        width: 68px;
        height: 68px;
    }

    .sari-intro .sari-connect-mark img {

        width: 40px;
    }
}


/* ============================================================
   SMALL MOBILE
   ============================================================ */

@media (max-width: 380px) {

    .sari-intro .sari-connect {

        gap: 24px 8px;
    }

    .sari-intro .sari-connect-icon {

        width: 44px;
        height: 44px;
    }

    .sari-intro .sari-connect-icon svg {

        width: 20px;
        height: 20px;
    }

    .sari-intro .sari-connect-label {

        font-size: 9px;
    }
}


/* ============================================================
   REDUCED MOTION
   ============================================================ */

@media (prefers-reduced-motion: reduce) {

    .sari-intro,
    .sari-intro *,
    .sari-intro *::before,
    .sari-intro *::after {

        transition: none !important;
    }
}
</style>


<!-- ============================================================
     SARI INTRO SECTION
     ============================================================ -->

<section
    class="sari-section sari-light-section sari-intro"
    id="discover"
>

    <div class="sari-container">

        <div class="sari-intro-inner sari-reveal">


            <!-- EYEBROW -->

            <div class="sari-intro-eyebrow-row">

                <span
                    class="sari-intro-rule"
                    aria-hidden="true"
                ></span>

                <span class="sari-eyebrow">
                    The SARI Idea
                </span>

                <span
                    class="sari-intro-rule"
                    aria-hidden="true"
                ></span>

            </div>


            <!-- TITLE -->

            <h2 class="sari-intro-title">

                Everyday shopping,

                <br>

                thoughtfully <em>elevated</em>.

            </h2>


            <!-- DESCRIPTION -->

            <p class="sari-intro-copy">

                SARI brings products, people, sellers, and
                delivery partners into one connected marketplace.
                Designed around simplicity, discovery, and trust,
                every interaction is made to feel effortless.

            </p>


            <!-- CONNECTION SYSTEM -->

            <div class="sari-connect">


                <!-- PRODUCTS -->

                <div class="sari-connect-node">

                    <span class="sari-connect-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >

                            <path
                                d="M4 8L12 4L20 8L12 12L4 8Z"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M4 8V16L12 20L20 16V8"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M12 12V20"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                        </svg>

                    </span>

                    <span class="sari-connect-label">
                        Products
                    </span>

                </div>


                <!-- LINE -->

                <span
                    class="sari-connect-line"
                    aria-hidden="true"
                ></span>


                <!-- SELLERS -->

                <div class="sari-connect-node">

                    <span class="sari-connect-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >

                            <circle
                                cx="9"
                                cy="8"
                                r="3.2"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                            <path
                                d="M4 19C4 15.6 6.2 13.5 9 13.5C11.8 13.5 14 15.6 14 19"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linecap="round"
                            />

                            <path
                                d="M15.5 9.5C16.6 9.2 17.4 8.2 17.4 7C17.4 5.6 16.3 4.5 14.9 4.5"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linecap="round"
                            />

                            <path
                                d="M16 14C18.3 14.3 20 16 20 19"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linecap="round"
                            />

                        </svg>

                    </span>

                    <span class="sari-connect-label">
                        Sellers
                    </span>

                </div>


                <!-- LINE -->

                <span
                    class="sari-connect-line"
                    aria-hidden="true"
                ></span>


                <!-- SARI CENTER LOGO -->

                <div class="sari-connect-node sari-connect-node-center">

                    <span class="sari-connect-mark">

                        <img
                            src="{{ asset('images/sari-logo.png') }}"
                            alt="SARI"
                        >

                    </span>

                </div>


                <!-- LINE -->

                <span
                    class="sari-connect-line"
                    aria-hidden="true"
                ></span>


                <!-- DELIVERY -->

                <div class="sari-connect-node">

                    <span class="sari-connect-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >

                            <path
                                d="M3 7H14V15H3V7Z"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M14 10H17.5L20 13V15H14V10Z"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linejoin="round"
                            />

                            <circle
                                cx="7"
                                cy="17.5"
                                r="1.6"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                            <circle
                                cx="17"
                                cy="17.5"
                                r="1.6"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                        </svg>

                    </span>

                    <span class="sari-connect-label">
                        Delivery
                    </span>

                </div>


                <!-- LINE -->

                <span
                    class="sari-connect-line"
                    aria-hidden="true"
                ></span>


                <!-- PEOPLE -->

                <div class="sari-connect-node">

                    <span class="sari-connect-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >

                            <circle
                                cx="8"
                                cy="8"
                                r="3.2"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                            <circle
                                cx="16.5"
                                cy="9"
                                r="2.6"
                                stroke="currentColor"
                                stroke-width="1.4"
                            />

                            <path
                                d="M2.8 19C2.8 15.4 5 13 8 13C11 13 13.2 15.4 13.2 19"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linecap="round"
                            />

                            <path
                                d="M14.5 13.3C17.6 13.6 19.6 15.8 19.6 19"
                                stroke="currentColor"
                                stroke-width="1.4"
                                stroke-linecap="round"
                            />

                        </svg>

                    </span>

                    <span class="sari-connect-label">
                        People
                    </span>

                </div>


            </div>

        </div>

    </div>

</section>