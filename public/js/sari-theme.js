(function () {
    'use strict';

    if (window.SariTheme) {
        window.SariTheme.restore();
        return;
    }

    const storageKey = 'sari:dashboard-theme';
    const root = document.documentElement;
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    let preference = null;

    function readPreference() {
        try {
            const saved = window.localStorage.getItem(storageKey);
            return saved === 'dark' || saved === 'light' ? saved : null;
        } catch (_) {
            return preference;
        }
    }

    function syncControls() {
        const isDark = root.dataset.sariTheme === 'dark';
        document.querySelectorAll('[data-sari-theme-toggle]').forEach(function (button) {
            button.setAttribute('aria-pressed', String(isDark));
            button.setAttribute('aria-label', 'Dark mode');
            button.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
        });
    }

    function applyTheme(theme) {
        const previous = root.dataset.sariTheme;
        root.dataset.sariTheme = theme;
        syncControls();
        if (previous !== theme) {
            document.dispatchEvent(new CustomEvent('sari:theme-changed', {
                detail: { theme: theme }
            }));
        }
    }

    function restoreTheme() {
        preference = readPreference();
        applyTheme(preference || (systemTheme.matches ? 'dark' : 'light'));
    }

    function setTheme(theme) {
        if (theme !== 'dark' && theme !== 'light') {
            return;
        }
        preference = theme;
        try {
            window.localStorage.setItem(storageKey, theme);
        } catch (_) {
            // Keep the switch usable when browser storage is unavailable.
        }
        applyTheme(theme);
    }

    window.SariTheme = Object.freeze({
        restore: restoreTheme,
        set: setTheme,
        get: function () { return root.dataset.sariTheme; }
    });

    restoreTheme();

    // Delegation survives Livewire navigation and persisted role headers.
    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) {
            return;
        }
        const button = event.target.closest('[data-sari-theme-toggle]');
        if (!button || button.disabled) {
            return;
        }
        setTheme(root.dataset.sariTheme === 'dark' ? 'light' : 'dark');
    });

    document.addEventListener('DOMContentLoaded', syncControls);
    document.addEventListener('livewire:navigating', restoreTheme);
    document.addEventListener('livewire:navigated', restoreTheme);
    window.addEventListener('pageshow', restoreTheme);
    window.addEventListener('storage', function (event) {
        if (event.key === storageKey || event.key === null) {
            restoreTheme();
        }
    });
    function syncSystemTheme() {
        if (!preference) {
            applyTheme(systemTheme.matches ? 'dark' : 'light');
        }
    }
    if (systemTheme.addEventListener) {
        systemTheme.addEventListener('change', syncSystemTheme);
    } else {
        systemTheme.addListener(syncSystemTheme);
    }
})();
