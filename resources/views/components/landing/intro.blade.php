<style>

/* ==========================================================
   SARI IDEA SECTION
   NEW UNIQUE COMPONENT
   ========================================================== */


.sari-idea-section {

    position: relative;

    width:100%;

    background:#faf8f3;

    color:#25221e;

    padding:120px 0;

}



.sari-idea-container {

    width:min(1200px, calc(100% - 48px));

    margin:auto;

}



/* ==========================================================
   HEADER
   ========================================================== */


.sari-idea-header {

    text-align:center;

}



.sari-idea-eyebrow {

    display:flex;

    align-items:center;

    justify-content:center;

    gap:18px;

    color:#c99128;

    font-size:11px;

    font-weight:700;

    letter-spacing:.18em;

    text-transform:uppercase;

}



.sari-idea-line {

    width:48px;

    height:1px;

    background:rgba(201,145,40,.35);

}



.sari-idea-title {

    margin-top:30px;

    font-size:clamp(38px,5vw,64px);

    line-height:1.05;

    letter-spacing:-.05em;

    font-weight:600;

    color:#211e19;

}



.sari-idea-title em {

    color:#c99128;

    font-style:normal;

}



.sari-idea-description {

    max-width:650px;

    margin:28px auto 0;

    color:#70695f;

    font-size:16px;

    line-height:1.8;

}



/* ==========================================================
   NETWORK
   ========================================================== */


.sari-idea-network {

    margin-top:75px;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:0;

}



.sari-idea-node {

    display:flex;

    flex-direction:column;

    align-items:center;

    justify-content:center;

    min-width:90px;

    transition:.3s ease;

}



.sari-idea-node:hover {

    transform:translateY(-5px);

}



.sari-idea-icon {

    width:58px;

    height:58px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#ffffff;

    border:1px solid rgba(40,35,30,.12);

    color:#403a32;

}



.sari-idea-icon svg {

    width:26px;

    height:26px;

}



.sari-idea-label {

    margin-top:14px;

    font-size:11px;

    font-weight:600;

    letter-spacing:.12em;

    text-transform:uppercase;

    color:#756e64;

}



/* ==========================================================
   CONNECTOR
   ========================================================== */


.sari-idea-connector {

    width:120px;

    height:1px;

    background:rgba(80,70,60,.18);

    margin:0 20px;

}



/* ==========================================================
   CENTER LOGO
   ========================================================== */


.sari-idea-center {

    position:relative;

}



.sari-idea-logo {

    width:90px;

    height:90px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#292824;

    border:1px solid rgba(201,145,40,.35);

    box-shadow:

    0 20px 50px rgba(0,0,0,.12);

}



.sari-idea-logo img {

    width:48px;

    height:auto;

    display:block;

}



/* ==========================================================
   MODERN SCROLL ANIMATION
   Triggers only when this section enters the viewport
   ========================================================== */

/* No-JS fallback: everything remains visible by default */
.sari-idea-header,
.sari-idea-eyebrow,
.sari-idea-title,
.sari-idea-description,
.sari-idea-node,
.sari-idea-connector {
    opacity: 1;
    transform: none;
}

/* Motion is enabled only after JS adds .sari-motion-ready */
.sari-idea-section.sari-motion-ready .sari-idea-eyebrow,
.sari-idea-section.sari-motion-ready .sari-idea-title,
.sari-idea-section.sari-motion-ready .sari-idea-description {
    opacity: 0;
    transform: translate3d(0, 22px, 0);
    transition:
        opacity .7s cubic-bezier(.22,1,.36,1),
        transform .85s cubic-bezier(.22,1,.36,1);
}

.sari-idea-section.sari-motion-ready .sari-idea-title {
    transform: translate3d(0, 28px, 0) scale(.985);
}

.sari-idea-section.sari-motion-ready .sari-idea-description {
    transform: translate3d(0, 18px, 0);
}

.sari-idea-section.sari-motion-ready .sari-idea-line {
    transform: scaleX(0);
    opacity: .25;
    transition:
        transform .8s cubic-bezier(.22,1,.36,1),
        opacity .6s ease;
}

.sari-idea-section.sari-motion-ready .sari-idea-line:first-child {
    transform-origin: right center;
}

.sari-idea-section.sari-motion-ready .sari-idea-line:last-child {
    transform-origin: left center;
}

/* Network nodes */
.sari-idea-section.sari-motion-ready .sari-idea-node {
    opacity: 0;
    transform: translate3d(0, 24px, 0) scale(.94);
    transition:
        opacity .65s cubic-bezier(.22,1,.36,1),
        transform .8s cubic-bezier(.22,1,.36,1);
    transition-delay: var(--sari-delay, 0ms);
}

/* Connector draw animation */
.sari-idea-section.sari-motion-ready .sari-idea-connector {
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left center;
    transition:
        transform .75s cubic-bezier(.22,1,.36,1),
        opacity .45s ease;
    transition-delay: var(--sari-delay, 0ms);
}

/* Visible state */
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-eyebrow,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-title,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-description,
.sari-idea-section.sari-motion-ready.is-visible .sari-idea-node {
    opacity: 1;
    transform: none;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-eyebrow {
    transition-delay: 80ms;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-title {
    transition-delay: 150ms;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-description {
    transition-delay: 230ms;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-line {
    transform: scaleX(1);
    opacity: 1;
    transition-delay: 120ms;
}

.sari-idea-section.sari-motion-ready.is-visible .sari-idea-connector {
    opacity: 1;
    transform: scaleX(1);
}

/* Center SARI logo — subtle premium halo after reveal */
.sari-idea-logo {
    position: relative;
    isolation: isolate;
    transition:
        transform .45s cubic-bezier(.22,1,.36,1),
        box-shadow .45s ease,
        border-color .45s ease;
}

.sari-idea-logo::before {
    content: "";
    position: absolute;
    inset: -12px;
    z-index: -1;
    border-radius: 50%;
    background: radial-gradient(
        circle,
        rgba(201,145,40,.16) 0%,
        rgba(201,145,40,.07) 42%,
        transparent 72%
    );
    opacity: 0;
    transform: scale(.78);
    transition:
        opacity .7s ease,
        transform .9s cubic-bezier(.22,1,.36,1);
}

.sari-idea-section.is-visible .sari-idea-logo::before {
    opacity: 1;
    transform: scale(1);
}

.sari-idea-center:hover .sari-idea-logo {
    transform: translateY(-5px) scale(1.035);
    border-color: rgba(201,145,40,.55);
    box-shadow: 0 24px 60px rgba(49,38,20,.16);
}

/* Icon micro-interactions */
.sari-idea-icon {
    transition:
        transform .35s cubic-bezier(.22,1,.36,1),
        border-color .3s ease,
        color .3s ease,
        box-shadow .35s ease,
        background-color .3s ease;
}

.sari-idea-node:hover .sari-idea-icon {
    transform: translateY(-3px);
    color: #b77d18;
    border-color: rgba(201,145,40,.36);
    box-shadow: 0 12px 28px rgba(52,43,31,.08);
}

/* Disable the old per-node hover translation so the icon is the focus */
.sari-idea-node:hover {
    transform: none;
}

/* Respect accessibility preference */
@media (prefers-reduced-motion: reduce) {
    .sari-idea-section.sari-motion-ready .sari-idea-eyebrow,
    .sari-idea-section.sari-motion-ready .sari-idea-title,
    .sari-idea-section.sari-motion-ready .sari-idea-description,
    .sari-idea-section.sari-motion-ready .sari-idea-node,
    .sari-idea-section.sari-motion-ready .sari-idea-connector,
    .sari-idea-line,
    .sari-idea-icon,
    .sari-idea-logo,
    .sari-idea-logo::before {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        animation: none !important;
    }
}



/* ==========================================================
   RESPONSIVE
   ========================================================== */


@media(max-width:900px){


    .sari-idea-network {

        flex-wrap:wrap;

        gap:35px;

    }



    .sari-idea-connector {

        display:none;

    }


}



@media(max-width:600px){


    .sari-idea-section {

        padding:80px 0;

    }



    .sari-idea-container {

        width:calc(100% - 32px);

    }



    .sari-idea-title {

        font-size:38px;

    }



    .sari-idea-network {

        display:grid;

        grid-template-columns:repeat(2,1fr);

    }



    .sari-idea-center {

        grid-column:1/-1;

        order:-1;

    }


}


</style>

{{-- ==========================================================
     SARI IDEA SECTION
     ========================================================== --}}


<section class="sari-idea-section">


    <div class="sari-idea-container">


        {{-- HEADER --}}

        <div class="sari-idea-header sari-idea-animate">


            <div class="sari-idea-eyebrow">

                <span class="sari-idea-line"></span>

                <span>
                    The SARI Idea
                </span>

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





        {{-- NETWORK SYSTEM --}}


        <div class="sari-idea-network sari-idea-animate">





            {{-- PRODUCTS --}}


            <div class="sari-idea-node">


                <span class="sari-idea-icon">


                    <svg viewBox="0 0 24 24" fill="none">


                        <path
                            d="M4 8L12 4L20 8L12 12L4 8Z"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <path
                            d="M4 8V16L12 20L20 16V8"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <path
                            d="M12 12V20"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                    </svg>


                </span>



                <span class="sari-idea-label">
                    Products
                </span>


            </div>





            <span class="sari-idea-connector"></span>





            {{-- SELLERS --}}


            <div class="sari-idea-node">


                <span class="sari-idea-icon">


                    <svg viewBox="0 0 24 24" fill="none">


                        <circle
                            cx="9"
                            cy="8"
                            r="3"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <path
                            d="M4 19C4 15.5 6.2 13.5 9 13.5C11.8 13.5 14 15.5 14 19"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                    </svg>


                </span>


                <span class="sari-idea-label">
                    Sellers
                </span>


            </div>






            <span class="sari-idea-connector"></span>






            {{-- SARI LOGO --}}


            <div class="sari-idea-node sari-idea-center">


                <span class="sari-idea-logo">


                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                    >


                </span>


            </div>






            <span class="sari-idea-connector"></span>







            {{-- DELIVERY --}}


            <div class="sari-idea-node">


                <span class="sari-idea-icon">


                    <svg viewBox="0 0 24 24" fill="none">


                        <path
                            d="M3 7H14V15H3V7Z"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <path
                            d="M14 10H17L20 13V15H14V10Z"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <circle
                            cx="7"
                            cy="17"
                            r="1.5"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <circle
                            cx="17"
                            cy="17"
                            r="1.5"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                    </svg>


                </span>



                <span class="sari-idea-label">
                    Delivery
                </span>


            </div>






            <span class="sari-idea-connector"></span>






            {{-- PEOPLE --}}


            <div class="sari-idea-node">


                <span class="sari-idea-icon">


                    <svg viewBox="0 0 24 24" fill="none">


                        <circle
                            cx="8"
                            cy="8"
                            r="3"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <circle
                            cx="16"
                            cy="9"
                            r="2.5"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                        <path
                            d="M3 19C3 15.5 5.2 13 8 13C11 13 13 15.5 13 19"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />


                    </svg>


                </span>


                <span class="sari-idea-label">
                    People
                </span>


            </div>



        </div>



    </div>


</section>

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
        item.style.setProperty(
            '--sari-delay',
            (320 + (index * 85)) + 'ms'
        );
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
