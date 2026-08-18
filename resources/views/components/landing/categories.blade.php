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