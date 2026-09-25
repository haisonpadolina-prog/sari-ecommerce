<style>
/* ==========================================================
   SARI CATEGORIES — LANDING
   Desktop keeps the existing premium image-card treatment.
   Mobile is intentionally compact: exactly 2 cards per row.
   ========================================================== */
section.sari-categories {
    position: relative;
    display: block;
    width: 100%;
    overflow: visible !important;
    padding: 120px 0 !important;
    color: #17140e !important;
    background: #ffffff !important;
}

section.sari-categories::before,
section.sari-categories::after {
    display: none !important;
    content: none !important;
}

.sari-categories .sari-container-wide {
    position: relative;
    z-index: 2;
}

.sari-categories .sari-eyebrow {
    color: #c99128 !important;
}

.sari-categories .sari-section-title {
    color: #17140e !important;
}

.sari-categories .sari-section-title em {
    color: #c99128 !important;
    font-style: normal;
}

.sari-categories .sari-section-description {
    color: #756d60 !important;
}

.sari-categories-meta {
    color: #9a8d78 !important;
}

.sari-category-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 24px;
    margin-top: 60px;
}

.sari-category-card {
    position: relative;
    display: block;
    height: 390px;
    overflow: hidden;
    border: 1px solid #eee3d2 !important;
    border-radius: 30px;
    background: #ffffff !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, .06);
    text-decoration: none;
    transition: transform .4s ease, box-shadow .4s ease, border-color .4s ease;
}

.sari-category-image {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .6s ease;
}

.sari-category-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, .75), rgba(0, 0, 0, .05));
}

.sari-category-top {
    position: absolute;
    top: 22px;
    left: 22px;
    right: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sari-category-index {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .15em;
}

.sari-category-arrow {
    display: grid;
    width: 40px;
    height: 40px;
    place-items: center;
    border-radius: 50%;
    color: #fff;
    background: rgba(255, 255, 255, .18);
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
    transition: transform .3s ease, background-color .3s ease;
}

.sari-category-content {
    position: absolute;
    left: 25px;
    right: 25px;
    bottom: 25px;
}

.sari-category-name {
    display: block;
    color: #fff;
    font-size: 25px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -.03em;
}

.sari-category-subtitle {
    display: block;
    margin-top: 6px;
    color: rgba(255, 255, 255, .8);
    font-size: 14px;
}

.sari-categories-footer {
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 55px;
    padding-top: 25px;
    border-top: 1px solid #eee3d2;
}

.sari-categories-footer span {
    color: #62594c;
}

.sari-categories-footer i {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c99128;
}

.sari-categories .sari-reveal {
    opacity: 0;
    transform: translateY(35px);
    transition: opacity .8s ease, transform .8s ease;
}

.sari-categories .sari-reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (hover: hover) and (pointer: fine) {
    .sari-category-card:hover {
        transform: translateY(-8px);
        border-color: #dec89c !important;
        box-shadow: 0 26px 60px rgba(0, 0, 0, .12);
    }

    .sari-category-card:hover .sari-category-image {
        transform: scale(1.06);
    }

    .sari-category-card:hover .sari-category-arrow {
        background: #c99128;
        transform: rotate(45deg);
    }
}

@media (max-width: 1100px) {
    .sari-category-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* FINAL MOBILE GRID: exactly 2 compact cards per row. */
@media (max-width: 768px) {
    section.sari-categories {
        padding: 72px 0 !important;
    }

    section.sari-categories .sari-container-wide {
        width: calc(100% - 28px) !important;
        margin-inline: auto !important;
    }

    .sari-categories-header {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }

    .sari-categories .sari-eyebrow {
        font-size: 9px !important;
        letter-spacing: .14em !important;
    }

    .sari-categories .sari-section-title {
        margin-top: 10px !important;
        font-size: clamp(34px, 9.8vw, 44px) !important;
        line-height: 1.02 !important;
        letter-spacing: -.045em !important;
    }

    .sari-categories-meta {
        display: block;
        font-size: 8px !important;
        letter-spacing: .1em !important;
        line-height: 1.4 !important;
    }

    .sari-categories .sari-section-description {
        margin-top: 10px !important;
        font-size: 12px !important;
        line-height: 1.6 !important;
    }

    section.sari-categories .sari-container-wide .sari-category-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        grid-auto-flow: row !important;
        gap: 10px !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 28px 0 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    section.sari-categories .sari-container-wide .sari-category-grid > .sari-category-card {
        display: block !important;
        grid-column: auto !important;
        grid-row: auto !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        height: 190px !important;
        min-height: 190px !important;
        max-height: 190px !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 16px !important;
        box-sizing: border-box !important;
    }

    .sari-category-top {
        top: 10px !important;
        left: 10px !important;
        right: 10px !important;
    }

    .sari-category-index {
        font-size: 8px !important;
        letter-spacing: .12em !important;
    }

    .sari-category-arrow {
        width: 28px !important;
        height: 28px !important;
        font-size: 11px !important;
    }

    .sari-category-content {
        left: 11px !important;
        right: 11px !important;
        bottom: 12px !important;
    }

    .sari-category-name {
        font-size: 15px !important;
        line-height: 1.08 !important;
        overflow-wrap: anywhere;
    }

    .sari-category-subtitle {
        margin-top: 3px !important;
        font-size: 8.5px !important;
        line-height: 1.3 !important;
    }

    .sari-categories-footer {
        gap: 10px;
        margin-top: 32px;
        padding-top: 17px;
        font-size: 8.5px;
    }
}

@media (max-width: 390px) {
    section.sari-categories .sari-container-wide {
        width: calc(100% - 22px) !important;
    }

    section.sari-categories .sari-container-wide .sari-category-grid {
        gap: 8px !important;
    }

    section.sari-categories .sari-container-wide .sari-category-grid > .sari-category-card {
        height: 174px !important;
        min-height: 174px !important;
        max-height: 174px !important;
        border-radius: 14px !important;
    }

    .sari-category-name {
        font-size: 14px !important;
    }

    .sari-category-subtitle {
        font-size: 8px !important;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-categories .sari-reveal,
    .sari-category-card,
    .sari-category-image,
    .sari-category-arrow {
        animation: none !important;
        transition: none !important;
    }
}
</style>

<section class="sari-section sari-light-section sari-categories" id="categories">
    <div class="sari-container-wide">
        <div class="sari-categories-header sari-reveal">
            <div>
                <span class="sari-eyebrow">Discover</span>
                <h2 class="sari-section-title">
                    Something for<br>
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

        @php
            $categories = [
                ['01', 'Fashion', 'Everyday style', 'cat-fashion.jpg', 'fashion'],
                ['02', 'Electronics', 'Smart essentials', 'cat-electronics.jpg', 'electronics'],
                ['03', 'Home & Living', 'Make it yours', 'cat-home.jpg', 'home-living'],
                ['04', 'Beauty', 'Care & confidence', 'cat-beauty.jpg', 'beauty'],
                ['05', 'Accessories', 'The finishing touch', 'cat-accessories.jpg', 'accessories'],
                ['06', 'Food & Essentials', 'Everyday needs', 'cat-food.jpg', 'food-essentials'],
                ['07', 'Sports', 'Move your way', 'cat-sports.jpg', 'sports'],
                ['08', 'Lifestyle', 'Live it your way', 'cat-lifestyle.jpg', 'lifestyle'],
            ];
        @endphp

        <div class="sari-category-grid">
            @foreach($categories as $index => $category)
                <a
                    href="{{ route('marketplace.category', ['category' => $category[4]]) }}"
                    class="sari-category-card sari-reveal"
                    aria-label="Browse {{ $category[1] }} products"
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
            <span class="sari-categories-footer-arrow">Choose a category <b>↗</b></span>
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
        revealItems.forEach(function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    const observer = new IntersectionObserver(function (entries, currentObserver) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            currentObserver.unobserve(entry.target);
        });
    }, {
        threshold: 0.13,
        rootMargin: '0px 0px -7% 0px'
    });

    revealItems.forEach(function (el) {
        observer.observe(el);
    });
})();
</script>
