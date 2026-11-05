/* Authentication feedback uses the Buyer compact notification UI.
   Its server-provided notice is authoritative; the timestamp-only fallback
   never authenticates anyone or allows access to protected pages. */
(function () {
    'use strict';
    const attemptKey = 'sari:login:attempt';
    const maxAttemptAgeMs = 3 * 60 * 1000;
    const successIconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>';

    function freshLoginAttempt() {
        try {
            const raw = sessionStorage.getItem(attemptKey);
            if (!raw) return false;
            const attempt = JSON.parse(raw);
            const elapsed = Date.now() - Number(attempt.at);
            if (elapsed < 0 || elapsed > maxAttemptAgeMs) {
                sessionStorage.removeItem(attemptKey);
                return false;
            }
            return true;
        } catch (_) { return false; }
    }

    function forgetLoginAttempt() {
        try { sessionStorage.removeItem(attemptKey); } catch (_) {}
    }

    function initializeLoginFeedback() {
        const root = document.querySelector('[data-sari-login-feedback]');
        if (!root || root.dataset.initialized === '1') return;

        const hasServerNotice = root.dataset.hasServerNotice === '1';
        const authenticated = root.dataset.authenticatedDestination === '1';
        const fromLogin = freshLoginAttempt();
        if (!hasServerNotice && !(authenticated && fromLogin)) return;

        const toast = root.querySelector('.sari-login-feedback__toast');
        if (!toast) return;

        if (!hasServerNotice) {
            // The final authenticated destination was rendered by Laravel.
            // This is only a one-time visual fallback after a redirect.
            root.dataset.noticeType = 'success';
            toast.dataset.tone = 'success';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            const icon = toast.querySelector('[data-sari-login-icon]');
            const title = toast.querySelector('[data-sari-login-title]');
            const message = toast.querySelector('[data-sari-login-message]');
            if (icon) icon.innerHTML = successIconSvg; // Trusted fixed SVG template only.
            if (title) title.textContent = 'Login successful';
            if (message) message.textContent = 'Welcome back to SARI!';
        }

        forgetLoginAttempt();
        root.dataset.initialized = '1';
        root.hidden = false;
        const success = root.dataset.noticeType === 'success';
        const registrationRequired = root.dataset.registrationRequired === '1';
        const soundUrl = root.dataset.sariSoundUrl;

        let dismissed = false;
        let timeoutId = null;
        let remaining = registrationRequired ? 0 : (success ? 4000 : 6000);
        let startedAt = 0;
        const reduceMotion = Boolean(window.matchMedia?.('(prefers-reduced-motion: reduce)').matches);

        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            if (timeoutId !== null) window.clearTimeout(timeoutId);
            timeoutId = null;
            if (reduceMotion) {
                root.hidden = true;
            } else {
                toast.classList.add('is-leaving');
                window.setTimeout(function () { root.hidden = true; }, 180);
            }
            document.removeEventListener('keydown', onKeydown);
        }
        function resumeTimer() {
            if (registrationRequired || dismissed || timeoutId !== null) return;
            startedAt = Date.now();
            timeoutId = window.setTimeout(dismiss, remaining);
        }
        function pauseTimer() {
            if (dismissed || timeoutId === null) return;
            window.clearTimeout(timeoutId);
            timeoutId = null;
            remaining = Math.max(0, remaining - (Date.now() - startedAt));
        }
        function onKeydown(event) {
            if (event.key === 'Escape') dismiss();
        }
        toast.querySelectorAll('[data-sari-modal-dismiss]').forEach(function (button) {
            button.addEventListener('click', dismiss);
        });
        toast.addEventListener('mouseenter', pauseTimer);
        toast.addEventListener('mouseleave', function () {
            if (!toast.matches(':focus-within')) resumeTimer();
        });
        toast.addEventListener('focusin', pauseTimer);
        toast.addEventListener('focusout', function () {
            window.setTimeout(function () {
                if (!toast.matches(':hover, :focus-within')) resumeTimer();
            }, 0);
        });
        document.addEventListener('keydown', onKeydown);
        resumeTimer();

        // Auto-play is best effort: OAuth navigation may cause browser autoplay
        // restrictions. There is intentionally no replay-sound button.
        if (soundUrl && typeof window.Audio === 'function') {
            try {
                const sound = new window.Audio(soundUrl);
                sound.volume = 0.55;
                const playing = sound.play();
                if (playing && typeof playing.catch === 'function') playing.catch(function () {});
            } catch (_) {}
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeLoginFeedback, { once: true });
    } else {
        initializeLoginFeedback();
    }
    document.addEventListener('livewire:navigated', initializeLoginFeedback);
})();
