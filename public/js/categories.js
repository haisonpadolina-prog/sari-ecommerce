/* SARI Categories — isolated reveal / interaction script */
(function () {
    'use strict';

    function initSariCategories() {
        const section = document.querySelector('.sari-categories-v2');
        if (!section) return;

        const items = section.querySelectorAll('.sari-reveal');
        if (!items.length) return;

        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            items.forEach((item) => item.classList.add('is-visible'));
            return;
        }

        if (!('IntersectionObserver' in window)) {
            items.forEach((item) => item.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -35px 0px'
        });

        items.forEach((item) => observer.observe(item));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSariCategories);
    } else {
        initSariCategories();
    }
})();
