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
   ANIMATION
   ========================================================== */


.sari-idea-animate {

    opacity:0;

    transform:translateY(30px);

    animation:sariIdeaShow .8s ease forwards;

}



.sari-idea-header {

    animation-delay:.1s;

}



.sari-idea-network {

    animation-delay:.25s;

}



@keyframes sariIdeaShow {


    from {

        opacity:0;

        transform:translateY(30px);

    }


    to {

        opacity:1;

        transform:none;

    }


}




/* ==========================================================
   DARK MODE READY
   ========================================================== */


html.dark .sari-idea-section,
body.dark .sari-idea-section {

    background:#151310;

    color:#f5efe5;

}



html.dark .sari-idea-title,
body.dark .sari-idea-title {

    color:#f5efe5;

}



html.dark .sari-idea-description,
body.dark .sari-idea-description {

    color:#bdb5a9;

}



html.dark .sari-idea-icon,
body.dark .sari-idea-icon {

    background:rgba(255,255,255,.05);

    border-color:rgba(255,255,255,.15);

    color:#ddd;

}



html.dark .sari-idea-label,
body.dark .sari-idea-label {

    color:#aaa;

}



html.dark .sari-idea-connector,
body.dark .sari-idea-connector {

    background:rgba(255,255,255,.18);

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