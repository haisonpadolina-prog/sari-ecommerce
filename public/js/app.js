   import './bootstrap';
   document.addEventListener('DOMContentLoaded', function () {

        /* =====================================================
           ELEMENT REFERENCES
        ====================================================== */

        const root = document.documentElement;

        const navbar =
            document.getElementById('sariNavbar');

        const mobileMenuButton =
            document.getElementById('sariMobileMenuButton');

        const mobileMenu =
            document.getElementById('sariMobileMenu');

        const themeToggle =
            document.getElementById('sariThemeToggle');

        const mobileThemeToggle =
            document.getElementById('sariMobileThemeToggle');

        const themeIcon =
            document.getElementById('sariThemeIcon');

        const mobileThemeIcon =
            document.getElementById('sariMobileThemeIcon');

        const mobileThemeText =
            document.getElementById('sariMobileThemeText');

        const searchButton =
            document.getElementById('sariSearchButton');

        const searchOverlay =
            document.getElementById('sariSearchOverlay');

        const searchClose =
            document.getElementById('sariSearchClose');

        const searchInput =
            document.getElementById('sariSearchInput');

        const searchForm =
            document.getElementById('sariSearchForm');


        /* =====================================================
           THEME
        ====================================================== */

        function updateThemeUI() {

            const isLight =
                root.classList.contains('light-mode');


            if (themeIcon) {
                themeIcon.textContent =
                    isLight ? '☀' : '☾';
            }


            if (mobileThemeIcon) {
                mobileThemeIcon.textContent =
                    isLight ? '☀' : '☾';
            }


            if (mobileThemeText) {
                mobileThemeText.textContent =
                    isLight
                        ? 'Dark Mode'
                        : 'Light Mode';
            }


            if (themeToggle) {

                const label =
                    isLight
                        ? 'Switch to dark mode'
                        : 'Switch to light mode';

                themeToggle.setAttribute(
                    'aria-label',
                    label
                );

                themeToggle.setAttribute(
                    'title',
                    label
                );
            }


            if (mobileThemeToggle) {

                mobileThemeToggle.setAttribute(
                    'aria-label',
                    isLight
                        ? 'Switch to dark mode'
                        : 'Switch to light mode'
                );
            }

        }


        function setTheme(theme) {

            if (theme === 'light') {

                root.classList.add('light-mode');

                localStorage.setItem(
                    'sari-theme',
                    'light'
                );

            } else {

                root.classList.remove('light-mode');

                localStorage.setItem(
                    'sari-theme',
                    'dark'
                );

            }

            updateThemeUI();
        }


        const savedTheme =
            localStorage.getItem('sari-theme');


        if (savedTheme === 'light') {
            setTheme('light');
        } else {
            setTheme('dark');
        }


        if (themeToggle) {

            themeToggle.addEventListener(
                'click',
                function () {

                    const isLight =
                        root.classList.contains(
                            'light-mode'
                        );

                    setTheme(
                        isLight
                            ? 'dark'
                            : 'light'
                    );

                }
            );

        }


        if (mobileThemeToggle) {

            mobileThemeToggle.addEventListener(
                'click',
                function () {

                    const isLight =
                        root.classList.contains(
                            'light-mode'
                        );

                    setTheme(
                        isLight
                            ? 'dark'
                            : 'light'
                    );

                }
            );

        }


        /* =====================================================
           NAVBAR SCROLL
        ====================================================== */

        function updateNavbar() {

            if (!navbar) {
                return;
            }

            if (window.scrollY > 35) {

                navbar.classList.add('scrolled');

            } else {

                navbar.classList.remove('scrolled');

            }

        }


        updateNavbar();


        window.addEventListener(
            'scroll',
            updateNavbar,
            {
                passive: true
            }
        );


        /* =====================================================
           MOBILE MENU
        ====================================================== */

        function closeMobileMenu() {

            if (!mobileMenuButton || !mobileMenu) {
                return;
            }

            mobileMenuButton.classList.remove('active');

            mobileMenu.classList.remove('open');

            mobileMenuButton.setAttribute(
                'aria-expanded',
                'false'
            );

            mobileMenuButton.setAttribute(
                'aria-label',
                'Open navigation menu'
            );

            document.body.style.overflow = '';

        }


        function toggleMobileMenu() {

            if (!mobileMenuButton || !mobileMenu) {
                return;
            }

            const isOpen =
                mobileMenu.classList.contains('open');


            if (isOpen) {

                closeMobileMenu();

            } else {

                mobileMenuButton.classList.add('active');

                mobileMenu.classList.add('open');

                mobileMenuButton.setAttribute(
                    'aria-expanded',
                    'true'
                );

                mobileMenuButton.setAttribute(
                    'aria-label',
                    'Close navigation menu'
                );

            }

        }


        if (mobileMenuButton) {

            mobileMenuButton.addEventListener(
                'click',
                toggleMobileMenu
            );

        }


        if (mobileMenu) {

            const mobileLinks =
                mobileMenu.querySelectorAll('a');

            mobileLinks.forEach(function (link) {

                link.addEventListener(
                    'click',
                    closeMobileMenu
                );

            });

        }


        window.addEventListener(
            'resize',
            function () {

                if (window.innerWidth > 768) {
                    closeMobileMenu();
                }

            }
        );


        /* =====================================================
           SMOOTH SCROLL
        ====================================================== */

        const internalLinks =
            document.querySelectorAll(
                '.sari-page a[href^="#"]'
            );


        internalLinks.forEach(function (link) {

            link.addEventListener(
                'click',
                function (event) {

                    const href =
                        link.getAttribute('href');


                    if (
                        !href ||
                        href === '#' ||
                        href.length <= 1
                    ) {
                        return;
                    }


                    const target =
                        document.querySelector(href);


                    if (!target) {
                        return;
                    }


                    event.preventDefault();


                    const navbarHeight =
                        navbar
                            ? navbar.offsetHeight
                            : 0;


                    const targetPosition =
                        target.getBoundingClientRect().top +
                        window.scrollY -
                        navbarHeight;


                    window.scrollTo({
                        top: Math.max(
                            targetPosition,
                            0
                        ),
                        behavior: 'smooth'
                    });


                    closeMobileMenu();

                }
            );

        });


        /* =====================================================
           SEARCH
        ====================================================== */

        function openSearch() {

            if (!searchOverlay) {
                return;
            }

            searchOverlay.classList.add('open');

            searchOverlay.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.style.overflow = 'hidden';


            window.setTimeout(
                function () {

                    if (searchInput) {
                        searchInput.focus();
                    }

                },
                150
            );

        }


        function closeSearch() {

            if (!searchOverlay) {
                return;
            }

            searchOverlay.classList.remove('open');

            searchOverlay.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow = '';

        }


        if (searchButton) {

            searchButton.addEventListener(
                'click',
                openSearch
            );

        }


        if (searchClose) {

            searchClose.addEventListener(
                'click',
                closeSearch
            );

        }


        if (searchOverlay) {

            searchOverlay.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target ===
                        searchOverlay
                    ) {
                        closeSearch();
                    }

                }
            );

        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {

                    closeSearch();

                    closeMobileMenu();

                }

            }
        );


        if (searchForm) {

            searchForm.addEventListener(
                'submit',
                function (event) {

                    event.preventDefault();

                    const query =
                        searchInput
                            ? searchInput.value.trim()
                            : '';


                    if (!query) {

                        if (searchInput) {
                            searchInput.focus();
                        }

                        return;
                    }



                    const encodedQuery =
                        encodeURIComponent(query);

                    window.location.href =
                        '#discover?q=' +
                        encodedQuery;

                    closeSearch();

                }
            );

        }


        /* =====================================================
           SCROLL REVEAL
        ====================================================== */

        const revealElements =
            document.querySelectorAll(
                '.sari-reveal'
            );


        if (
            'IntersectionObserver' in window
        ) {

            const revealObserver =
                new IntersectionObserver(
                    function (entries, observer) {

                        entries.forEach(
                            function (entry) {

                                if (
                                    entry.isIntersecting
                                ) {

                                    entry.target.classList.add(
                                        'is-visible'
                                    );

                                    observer.unobserve(
                                        entry.target
                                    );

                                }

                            }
                        );

                    },
                    {
                        threshold: 0.12,
                        rootMargin: '0px 0px -40px 0px'
                    }
                );


            revealElements.forEach(
                function (element) {

                    revealObserver.observe(
                        element
                    );

                }
            );

        } else {

            revealElements.forEach(
                function (element) {

                    element.classList.add(
                        'is-visible'
                    );

                }
            );

        }


        /* =====================================================
           INITIAL HERO REVEAL
        ====================================================== */

        window.setTimeout(
            function () {

                const heroElements =
                    document.querySelectorAll(
                        '.sari-hero .sari-reveal'
                    );

                heroElements.forEach(
                    function (element) {

                        element.classList.add(
                            'is-visible'
                        );

                    }
                );

            },
            120
        );

    });

    (function () {
        var year = document.getElementById('sariYear');
        if (year) year.textContent = new Date().getFullYear();
    })();
