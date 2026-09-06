<style>
   /* ==========================================================
   SARI CATEGORIES SECTION - FORCE WHITE PREMIUM VERSION
   ========================================================== */


/* Main Section */

section.sari-categories {

    background:#ffffff !important;

    color:#17140e !important;

    position:relative;

    overflow:visible !important;

    padding:120px 0 !important;

    display:block;

    width:100%;

}
.sari-categories-footer {
    position:relative;
    z-index:5;
}
.sari-category-card {
    height:390px;
    overflow:hidden;
}


/* Remove old backgrounds */

section.sari-categories::before,
section.sari-categories::after {

    display:none !important;

    content:none !important;

}



/* Container */

.sari-categories .sari-container-wide {

    position:relative;

    z-index:2;

}



/* Header */


.sari-categories .sari-eyebrow {

    color:#c99128 !important;

}



.sari-categories .sari-section-title {

    color:#17140e !important;

}



.sari-categories .sari-section-title em {

    color:#c99128 !important;

    font-style:normal;

}



.sari-categories .sari-section-description {

    color:#756d60 !important;

}



.sari-categories-meta {

    color:#9a8d78 !important;

}



/* CATEGORY GRID */


.sari-category-grid {

    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:24px;

    margin-top:60px;

}



/* CATEGORY CARD */


.sari-category-card {


    position:relative;


    height:390px;


    overflow:hidden;


    border-radius:30px;


    background:#ffffff !important;


    border:1px solid #eee3d2 !important;


    box-shadow:

    0 15px 40px rgba(0,0,0,.06);


    transition:.4s ease;


}



.sari-category-card:hover {


    transform:translateY(-10px);


    box-shadow:

    0 30px 70px rgba(0,0,0,.14);


}



/* IMAGE */


.sari-category-image {


    width:100%;

    height:100%;


    object-fit:cover;


    transition:.6s ease;


}



.sari-category-card:hover 
.sari-category-image {


    transform:scale(1.08);


}



/* Overlay */


.sari-category-overlay {


    position:absolute;


    inset:0;


    background:

    linear-gradient(

        to top,

        rgba(0,0,0,.75),

        rgba(0,0,0,.05)

    );


}



/* Number + Arrow */


.sari-category-top {


    position:absolute;


    top:22px;


    left:22px;


    right:22px;


    display:flex;


    justify-content:space-between;


}



.sari-category-index {


    color:white;


    font-size:12px;


    font-weight:700;


    letter-spacing:.15em;


}



.sari-category-arrow {


    width:40px;

    height:40px;


    display:grid;


    place-items:center;


    border-radius:50%;


    background:

    rgba(255,255,255,.18);


    color:white;


    backdrop-filter:blur(10px);


    transition:.3s ease;


}



.sari-category-card:hover 
.sari-category-arrow {


    background:#c99128;


    transform:rotate(45deg);


}



/* Text */


.sari-category-content {


    position:absolute;


    left:25px;


    bottom:25px;


}



.sari-category-name {


    display:block;


    color:white;


    font-size:25px;


    font-weight:700;


    letter-spacing:-.03em;


}



.sari-category-subtitle {


    display:block;


    margin-top:6px;


    color:rgba(255,255,255,.8);


    font-size:14px;


}



/* Footer */


.sari-categories-footer {


    margin-top:55px;


    padding-top:25px;


    border-top:1px solid #eee3d2;


}



.sari-categories-footer span {


    color:#62594c;


}



.sari-categories-footer i {


    width:8px;

    height:8px;


    display:inline-block;


    border-radius:50%;


    background:#c99128;


}



/* Animation */

.sari-categories .sari-reveal {


    opacity:0;


    transform:translateY(35px);


    transition:.8s ease;


}



.sari-categories .sari-reveal.is-visible {


    opacity:1;


    transform:translateY(0);


}



/* Responsive */


@media(max-width:1100px){


    .sari-category-grid {


        grid-template-columns:repeat(2,1fr);


    }


}



@media(max-width:650px){


    .sari-category-grid {


        grid-template-columns:1fr;


    }


}
</style>
{{-- resources/views/components/categories.blade.php --}}
<section class="sari-section sari-light-section sari-categories" id="categories">
    <div class="sari-container-wide">

        <div class="sari-categories-header sari-reveal">
            <div>
                <span class="sari-eyebrow">Discover</span>

                <h2 class="sari-section-title">
                    Something for
                    <br>
                    every <em>everyday.</em>
                </h2>
            </div>

            <div class="sari-categories-intro">
                <span class="sari-categories-meta">08 CATEGORIES · CURATED FOR EVERYDAY LIFE</span>
                <p class="sari-section-description">
                    Explore a curated marketplace across the categories
                    that make everyday life more convenient and complete.
                </p>
            </div>
        </div>

        <div class="sari-category-grid">

            @php
                $categories = [
                    ['01', 'Fashion', 'Everyday style', 'cat-fashion.jpg', 'category-fashion'],
                    ['02', 'Electronics', 'Smart essentials', 'cat-electronics.jpg', 'category-electronics'],
                    ['03', 'Home & Living', 'Make it yours', 'cat-home.jpg', 'category-home'],
                    ['04', 'Beauty', 'Care & confidence', 'cat-beauty.jpg', 'category-beauty'],
                    ['05', 'Accessories', 'The finishing touch', 'cat-accessories.jpg', 'category-accessories'],
                    ['06', 'Food & Essentials', 'Everyday needs', 'cat-food.jpg', 'category-food'],
                    ['07', 'Sports', 'Move your way', 'cat-sports.jpg', 'category-sports'],
                    ['08', 'Lifestyle', 'Live it your way', 'cat-lifestyle.jpg', 'category-lifestyle'],
                ];
            @endphp

            @foreach($categories as $index => $category)
                <a
                    href="#{{ $category[4] }}"
                    class="sari-category-card sari-reveal"
                    style="--card-delay: {{ ($index % 4) * 70 }}ms;"
                    aria-label="Explore {{ $category[1] }}"
                >
                    <img
                        src="{{ asset('images/' . $category[3]) }}"
                        alt="{{ $category[1] }} category"
                        class="sari-category-image"
                        loading="lazy"
                    >

                    <div class="sari-category-overlay"></div>

                    <div class="sari-category-top">
                        <span class="sari-category-index">{{ $category[0] }}</span>
                        <span class="sari-category-arrow" aria-hidden="true">↗</span>
                    </div>

                    <div class="sari-category-content">
                        <span class="sari-category-name">{{ $category[1] }}</span>
                        <span class="sari-category-subtitle">{{ $category[2] }}</span>
                    </div>
                </a>
            @endforeach

        </div>

        <div class="sari-categories-footer sari-reveal">
            <span><i></i> One marketplace, many possibilities.</span>
            <span class="sari-categories-footer-arrow">Explore categories <b>↓</b></span>
        </div>

    </div>
</section>

<script>
(function () {
    const section = document.querySelector('.sari-categories');
    if (!section) return;

    section.classList.add('sari-categories-motion-ready');

    const revealItems = section.querySelectorAll('.sari-reveal');

    if (!('IntersectionObserver' in window)) {
        revealItems.forEach(el => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
        });
    }, {
        threshold: 0.13,
        rootMargin: '0px 0px -7% 0px'
    });

    revealItems.forEach(el => observer.observe(el));
})();
</script>