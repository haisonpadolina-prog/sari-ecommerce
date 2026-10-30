(() => {
    'use strict';

    const body = document.body;
    const sidebar = document.getElementById('courierSidebar');
    const main = document.getElementById('courierMain');
    const overlay = document.getElementById('courierSidebarOverlay');
    const mobileMenuButton = document.getElementById('courierMenuButton');
    const mobileCloseButton = document.getElementById('courierSidebarClose');
    const desktopToggle = document.getElementById('courierSidebarToggle');
    const desktop = window.matchMedia('(min-width: 1024px)');
    let mobileOpen = false;
    let collapsed = false;

    try {
        collapsed = localStorage.getItem('sariCourierSidebarCollapsed') === '1';
    } catch (_) {
        // Navigation also works when browser storage is unavailable.
    }

    const syncDesktop = () => {
        body.classList.toggle('courier-sidebar-collapsed', desktop.matches && collapsed);
        desktopToggle?.setAttribute('aria-expanded', String(!collapsed));
        desktopToggle?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
    };

    const closeMobileSidebar = (restoreFocus = true) => {
        const wasOpen = mobileOpen;
        mobileOpen = false;
        body.classList.remove('courier-menu-open');
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        mobileMenuButton?.setAttribute('aria-expanded', 'false');
        sidebar?.removeAttribute('role');
        sidebar?.removeAttribute('aria-modal');
        if (main) {
            main.inert = false;
        }
        if (sidebar) {
            sidebar.inert = !desktop.matches;
        }
        if (wasOpen && restoreFocus) {
            (desktop.matches ? desktopToggle : mobileMenuButton)?.focus();
        }
    };

    const openMobileSidebar = () => {
        if (desktop.matches || !sidebar) {
            return;
        }
        mobileOpen = true;
        sidebar.inert = false;
        sidebar.classList.remove('-translate-x-full');
        sidebar.setAttribute('role', 'dialog');
        sidebar.setAttribute('aria-modal', 'true');
        overlay?.classList.remove('hidden');
        body.classList.add('courier-menu-open');
        mobileMenuButton?.setAttribute('aria-expanded', 'true');
        mobileCloseButton?.focus();
        if (main) {
            main.inert = true;
        }
    };

    mobileMenuButton?.addEventListener('click', openMobileSidebar);
    mobileCloseButton?.addEventListener('click', () => closeMobileSidebar());
    overlay?.addEventListener('click', () => closeMobileSidebar());
    desktopToggle?.addEventListener('click', () => {
        collapsed = !collapsed;
        syncDesktop();
        try {
            localStorage.setItem('sariCourierSidebarCollapsed', collapsed ? '1' : '0');
        } catch (_) {}
    });

    document.addEventListener('keydown', (event) => {
        if (!mobileOpen) {
            return;
        }
        if (event.key === 'Escape') {
            event.preventDefault();
            closeMobileSidebar();
        }
        if (event.key === 'Tab') {
            const focusable = [...sidebar.querySelectorAll('a[href], button:not(:disabled), [tabindex="0"]')]
                .filter(element => element.getClientRects().length > 0);
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last?.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first?.focus();
            }
        }
    });

    desktop.addEventListener('change', () => {
        const focusedSidebar = sidebar?.contains(document.activeElement);
        const focusedMenuButton = document.activeElement === mobileMenuButton;
        closeMobileSidebar(false);
        syncDesktop();
        if (focusedSidebar && !desktop.matches) {
            mobileMenuButton?.focus();
        } else if (focusedMenuButton || document.activeElement === mobileCloseButton) {
            desktopToggle?.focus();
        }
    });

    sidebar?.querySelectorAll('a[data-courier-sidebar-item]').forEach(link => {
        link.setAttribute('aria-label', link.title || link.textContent.trim());
        if (link.classList.contains('bg-[#d9930a]')) {
            link.setAttribute('aria-current', 'page');
        }
    });

    syncDesktop();
    closeMobileSidebar(false);

    window.SariCourierToast = (title, message) => {
        const toast = document.getElementById('courierToast');
        const bell = document.getElementById('notificationBell');
        if (!toast) {
            return;
        }
        const titleElement = document.getElementById('courierToastTitle');
        const messageElement = document.getElementById('courierToastMessage');
        if (titleElement) {
            titleElement.textContent = title;
        }
        if (messageElement) {
            messageElement.textContent = message;
        }
        toast.classList.remove('hidden');
        bell?.classList.remove('bell-ring');
        if (bell) {
            void bell.offsetWidth;
        }
        bell?.classList.add('bell-ring');
        clearTimeout(window.__sariCourierToastTimer);
        window.__sariCourierToastTimer = setTimeout(() => toast.classList.add('hidden'), 3500);
    };
})();
