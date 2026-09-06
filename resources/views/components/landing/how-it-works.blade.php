<style>
/* ==========================================================
   SARI HOW IT WORKS
   PREMIUM WHITE COMMERCE DESIGN
========================================================== */

.sari-dark-section.sari-works {
    background:#ffffff !important;
    color:#17140e;
    position:relative;
    overflow:hidden;
    padding:120px 0;
}


/* soft background decoration */

.sari-works::before {

    content:"";

    position:absolute;

    width:600px;
    height:600px;

    top:-250px;
    left:-200px;

    background:
    radial-gradient(
        circle,
        rgba(201,145,40,.08),
        transparent 70%
    );

    pointer-events:none;

}


.sari-works::after {

    content:"";

    position:absolute;

    width:500px;
    height:500px;

    bottom:-250px;
    right:-150px;

    background:
    radial-gradient(
        circle,
        rgba(201,145,40,.06),
        transparent 70%
    );

}


/* Heading */


.sari-works-title {

    color:#15130f !important;

    font-weight:700;

    letter-spacing:-0.06em;

}


.sari-works-title em {

    color:#c99128 !important;

    font-style:normal;

}


.sari-eyebrow,
.sari-works-kicker {

    color:#c99128 !important;

    font-weight:700;

}


.sari-works-intro {

    color:#746b5d !important;

    line-height:1.8;

}


/* CARD AREA */


.sari-work-card {

    background:#ffffff !important;

    border:1px solid #eadfcd !important;

    border-radius:32px !important;

    padding:26px;

    box-shadow:

    0 20px 60px rgba(20,15,5,.06);

    transition:.35s ease;

}


.sari-work-card:hover {

    transform:translateY(-12px);

    box-shadow:

    0 30px 80px rgba(20,15,5,.12);

    border-color:#d9b66a !important;

}



/* NUMBER */


.sari-work-number {

    color:#c99128 !important;

    font-weight:700;

}


.sari-work-label {

    color:#8b806f !important;

}



/* SCENE */


.sari-commerce-scene {

    background:#faf8f3 !important;

    border-radius:26px;

    border:1px solid #eee3d3;

    min-height:230px;

    display:flex;

    align-items:center;

    justify-content:center;

}



/* inner windows */


.sari-shopping-window,
.sari-dashboard-window,
.sari-delivery-window {


    background:white !important;

    border:1px solid #eee4d6 !important;

    border-radius:18px;

    box-shadow:

    0 15px 40px rgba(0,0,0,.08);

}



/* browser header */


.sari-window-top {

    background:#faf7f1 !important;

    border-bottom:1px solid #eee3d3;

}


.sari-window-top b {

    color:#4d463b !important;

}



/* Text */


.sari-work-copy h3 {

    color:#17140e !important;

}


.sari-work-copy h3 span {

    color:#c99128 !important;

}


.sari-work-copy p {

    color:#756b5c !important;

    line-height:1.8;

}



/* Badge */


.sari-scene-badge {

    background:white !important;

    color:#51483c !important;

    border:1px solid #e8dcc7;

    box-shadow:

    0 10px 25px rgba(0,0,0,.08);

}


.sari-scene-badge i {

    background:#c99128 !important;

}



/* SELLER STATS */


.sari-stats-row div {

    background:#faf8f3 !important;

    border:1px solid #eee2d0;

}


.sari-stats-row strong {

    color:#17140e !important;

}



/* DELIVERY */


.sari-route-step strong {

    color:#17140e !important;

}


.sari-route-step small {

    color:#827869 !important;

}


.sari-route-step i {

    background:white;

    border:2px solid #c99128;

    color:#c99128;

}


.sari-route-step.active i {

    background:#c99128;

    color:white;

}


.sari-route-line.active {

    background:#c99128 !important;

}



/* Bottom journey */


.sari-works-bottom {

    border-top:1px solid #eee3d3;

}


.sari-connected-status {

    color:#51483c;

}


.sari-connected-status i {

    background:#c99128;

}


.sari-journey span {

    color:#51483c;

}


.sari-journey b {

    color:#c99128;

}



/* Animation */


.sari-reveal {

    opacity:0;

    transform:translateY(40px);

    transition:

    opacity .8s ease,

    transform .8s ease;

}


.sari-reveal.is-visible {

    opacity:1;

    transform:translateY(0);

}


.sari-delay-1 {

    transition-delay:.15s;

}


.sari-delay-2 {

    transition-delay:.3s;

}
    
</style>


<!-- =========================================================
     SARI HOW IT WORKS — V5 NAVY + GOLD EDITORIAL COMMERCE
     Smooth / formal motion enabled
     ========================================================= -->
<section class="sari-section sari-dark-section sari-works" id="how-it-works">
    <div class="sari-container">

        <div class="sari-works-heading sari-reveal">
            <div>
                <span class="sari-eyebrow">How SARI Works</span>

                <h2 class="sari-works-title">
                    One marketplace.
                    <br>
                    Three <em>connected</em>
                    <br>
                    commerce journeys.
                </h2>
            </div>

            <div class="sari-works-intro-wrap">
                <span class="sari-works-kicker">BUILT FOR EVERY STEP</span>
                <p class="sari-works-intro">
                    SARI brings shoppers, sellers, and delivery partners
                    into one coordinated marketplace — from the first
                    product discovery to the final doorstep.
                </p>
            </div>
        </div>

        <div class="sari-works-flow">

            <article class="sari-work-card sari-work-buy sari-reveal">
                <div class="sari-work-top">
                    <span class="sari-work-number">01</span>
                    <span class="sari-work-label">BUY</span>
                </div>

                <div class="sari-commerce-scene">
                    <div class="sari-scene-glow"></div>

                    <div class="sari-shopping-window">
                        <div class="sari-window-top">
                            <span></span><span></span><span></span>
                            <b>SHOP</b>
                        </div>

                        <div class="sari-product-line">
                            <div class="sari-product-image">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M7 8h10l1 11H6L7 8Z"/>
                                    <path d="M9 8a3 3 0 0 1 6 0"/>
                                </svg>
                            </div>

                            <div class="sari-product-info">
                                <strong>Featured product</strong>
                                <small>Trusted seller</small>
                                <b>₱1,299</b>
                            </div>

                            <div class="sari-cart-button">+</div>
                        </div>

                        <div class="sari-checkout-row">
                            <span>Cart · 3 items</span>
                            <b>Checkout →</b>
                        </div>
                    </div>

                    <span class="sari-scene-badge sari-badge-cart">
                        <i></i> Cart ready
                    </span>
                </div>

                <div class="sari-work-copy">
                    <h3>Discover.<br><span>Shop.</span></h3>

                    <p>
                        Find products from trusted sellers, add them
                        to your cart, and checkout through one seamless
                        shopping experience.
                    </p>

                    <span class="sari-work-link">
                        Explore products <b>↗</b>
                    </span>
                </div>
            </article>

            <article class="sari-work-card sari-work-sell sari-reveal sari-delay-1">
                <div class="sari-work-top">
                    <span class="sari-work-number">02</span>
                    <span class="sari-work-label">SELL</span>
                </div>

                <div class="sari-commerce-scene">
                    <div class="sari-scene-glow"></div>

                    <div class="sari-dashboard-window">
                        <div class="sari-window-top">
                            <span></span><span></span><span></span>
                            <b>SELLER</b>
                        </div>

                        <div class="sari-dashboard-title">
                            <strong>Store overview</strong>
                            <small>Today</small>
                        </div>

                        <div class="sari-stats-row">
                            <div><small>ORDERS</small><strong>24</strong></div>
                            <div><small>SALES</small><strong>+24%</strong></div>
                            <div><small>PRODUCTS</small><strong>128</strong></div>
                        </div>

                        <div class="sari-sales-line">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                            <i></i>
                        </div>
                    </div>

                    <span class="sari-scene-badge sari-badge-sales">
                        <i></i> Growing
                    </span>
                </div>

                <div class="sari-work-copy">
                    <h3>Manage.<br><span>Grow.</span></h3>

                    <p>
                        Build your storefront, manage products and orders,
                        track sales, and grow your business from one
                        organized seller experience.
                    </p>

                    <span class="sari-work-link">
                        Start selling <b>↗</b>
                    </span>
                </div>
            </article>

            <article class="sari-work-card sari-work-deliver sari-reveal sari-delay-2">
                <div class="sari-work-top">
                    <span class="sari-work-number">03</span>
                    <span class="sari-work-label">DELIVER</span>
                </div>

                <div class="sari-commerce-scene">
                    <div class="sari-scene-glow"></div>

                    <div class="sari-delivery-window">
                        <div class="sari-window-top">
                            <span></span><span></span><span></span>
                            <b>DELIVERY</b>
                        </div>

                        <div class="sari-route">
                            <div class="sari-route-step active">
                                <i>✓</i>
                                <div>
                                    <strong>Picked up</strong>
                                    <small>Order collected</small>
                                </div>
                            </div>

                            <div class="sari-route-line active"></div>

                            <div class="sari-route-step active">
                                <i>•</i>
                                <div>
                                    <strong>On the way</strong>
                                    <small>Courier is moving</small>
                                </div>
                            </div>

                            <div class="sari-route-line"></div>

                            <div class="sari-route-step">
                                <i>3</i>
                                <div>
                                    <strong>Delivered</strong>
                                    <small>Final destination</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="sari-scene-badge sari-badge-delivery">
                        <i></i> Tracking
                    </span>
                </div>

                <div class="sari-work-copy">
                    <h3>Pick up.<br><span>Complete.</span></h3>

                    <p>
                        Connect orders with fulfillment partners for
                        pickup, tracking, coordinated delivery, and
                        successful completion.
                    </p>

                    <span class="sari-work-link">
                        Track delivery <b>↗</b>
                    </span>
                </div>
            </article>

        </div>

        <div class="sari-works-bottom">
            <span class="sari-connected-status">
                <i></i>
                One connected marketplace
            </span>

            <div class="sari-journey">
                <span>Discover</span><b>→</b>
                <span>Buy</span><b>→</b>
                <span>Sell</span><b>→</b>
                <span>Deliver</span>
            </div>
        </div>

    </div>
</section>

<script>
(function () {
    const section = document.querySelector('.sari-works');
    if (!section) return;

    section.classList.add('sari-works-motion-ready');

    const revealItems = section.querySelectorAll('.sari-reveal');
    const bottom = section.querySelector('.sari-works-bottom');

    if (!('IntersectionObserver' in window)) {
        revealItems.forEach(el => el.classList.add('is-visible'));
        if (bottom) bottom.classList.add('is-visible');
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
    if (bottom) observer.observe(bottom);
})();
</script>