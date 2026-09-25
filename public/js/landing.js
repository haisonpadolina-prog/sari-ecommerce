document.addEventListener('DOMContentLoaded', function () {
    const root = document.documentElement;
    const navbar = document.getElementById('sariNavbar');
    const menuButton = document.getElementById('sariMobileMenuButton');
    const mobileMenu = document.getElementById('sariMobileMenu');

    const searchButton = document.getElementById('sariSearchButton');
    const searchOverlay = document.getElementById('sariSearchOverlay');
    const searchClose = document.getElementById('sariSearchClose');
    const searchInput = document.getElementById('sariSearchInput');
    const searchForm = document.getElementById('sariSearchForm');

    const themeToggle = document.getElementById('sariThemeToggle');
    const mobileThemeToggle = document.getElementById('sariMobileThemeToggle');
    const themeIcon = document.getElementById('sariThemeIcon');
    const mobileThemeIcon = document.getElementById('sariMobileThemeIcon');
    const mobileThemeText = document.getElementById('sariMobileThemeText');

    function updateNavbar() {
        if (!navbar) return;
        navbar.classList.toggle('scrolled', window.scrollY > 35);
    }

    updateNavbar();
    window.addEventListener('scroll', updateNavbar, { passive: true });

    function closeMobileMenu() {
        if (!menuButton || !mobileMenu) return;

        menuButton.classList.remove('active');
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Open navigation menu');

        mobileMenu.classList.remove('open');
        mobileMenu.setAttribute('aria-hidden', 'true');

        document.body.style.overflow = '';
    }

    function openMobileMenu() {
        if (!menuButton || !mobileMenu) return;

        menuButton.classList.add('active');
        menuButton.setAttribute('aria-expanded', 'true');
        menuButton.setAttribute('aria-label', 'Close navigation menu');

        mobileMenu.classList.add('open');
        mobileMenu.setAttribute('aria-hidden', 'false');

        document.body.style.overflow = 'hidden';
    }

    function toggleMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.contains('open')
            ? closeMobileMenu()
            : openMobileMenu();
    }

    if (menuButton) {
        menuButton.addEventListener('click', toggleMobileMenu);
    }

    if (mobileMenu) {
        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) closeMobileMenu();
    });

    document.querySelectorAll('.sari-page a[href*="#"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            const rawHref = link.getAttribute('href');
            if (!rawHref || rawHref === '#') return;

            const hashIndex = rawHref.indexOf('#');
            if (hashIndex < 0) return;

            const hash = rawHref.slice(hashIndex);
            if (!hash || hash.length <= 1) return;

            const target = document.querySelector(hash);
            if (!target) return;

            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            const linkUrl = new URL(link.href, window.location.href);
            const linkPath = linkUrl.pathname.replace(/\/+$/, '') || '/';

            if (currentPath !== linkPath) return;

            event.preventDefault();

            const navbarHeight = navbar ? navbar.offsetHeight : 0;
            const top = target.getBoundingClientRect().top + window.scrollY - navbarHeight - 8;

            window.scrollTo({
                top: Math.max(top, 0),
                behavior: 'smooth'
            });

            closeMobileMenu();
        });
    });

    function updateThemeUI() {
        const isLight = root.classList.contains('light-mode');

        if (themeIcon) themeIcon.textContent = isLight ? '☀' : '☾';
        if (mobileThemeIcon) mobileThemeIcon.textContent = isLight ? '☀' : '☾';
        if (mobileThemeText) mobileThemeText.textContent = isLight ? 'Dark Mode' : 'Light Mode';

        const label = isLight ? 'Switch to dark mode' : 'Switch to light mode';
        if (themeToggle) themeToggle.setAttribute('aria-label', label);
        if (mobileThemeToggle) mobileThemeToggle.setAttribute('aria-label', label);
    }

    function setTheme(theme) {
        root.classList.toggle('light-mode', theme === 'light');
        localStorage.setItem('sari-theme', theme);
        updateThemeUI();
    }

    const savedTheme = localStorage.getItem('sari-theme');
    setTheme(savedTheme === 'light' ? 'light' : 'dark');

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            setTheme(root.classList.contains('light-mode') ? 'dark' : 'light');
        });
    }

    if (mobileThemeToggle) {
        mobileThemeToggle.addEventListener('click', function () {
            setTheme(root.classList.contains('light-mode') ? 'dark' : 'light');
        });
    }

    function openSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.add('open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        window.setTimeout(function () {
            if (searchInput) searchInput.focus();
        }, 120);
    }

    function closeSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.remove('open');
        searchOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (searchButton) searchButton.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    if (searchOverlay) {
        searchOverlay.addEventListener('click', function (event) {
            if (event.target === searchOverlay) closeSearch();
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const query = searchInput ? searchInput.value.trim() : '';
            if (!query) {
                if (searchInput) searchInput.focus();
                return;
            }

            const discover = document.getElementById('discover');
            if (discover) {
                const navbarHeight = navbar ? navbar.offsetHeight : 0;
                const top = discover.getBoundingClientRect().top + window.scrollY - navbarHeight - 8;
                window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' });
            }
            closeSearch();
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMobileMenu();
            closeSearch();
        }
    });

    const revealElements = document.querySelectorAll('.sari-reveal');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries, currentObserver) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    currentObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(function (element) {
            observer.observe(element);
        });
    } else {
        revealElements.forEach(function (element) {
            element.classList.add('is-visible');
        });
    }

    const year = document.getElementById('sariYear');
    if (year) year.textContent = new Date().getFullYear();
});
