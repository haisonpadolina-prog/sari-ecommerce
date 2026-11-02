/** SARI Buyer centralized action toasts — custom alert v7.
 * Normal notifications use the user's selected MP3, unchanged.
 * Invalid inputs and failed actions retain the distinct Buyer error WAV.
 * This file is served directly from public/ (no Vite build required).
 */
(function () {
    'use strict';
    if (window.__SARI_BUYER_TOAST_ACTION_BRIDGE_V2__) return;
    window.__SARI_BUYER_TOAST_ACTION_BRIDGE_V2__ = true;

    const presets = {
        success: { duration: 4000, svg: '<path d="m5 12 4.5 4.5L19 7"/>' },
        error: { duration: 6000, svg: '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/>' },
        info: { duration: 4600, svg: '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>' },
    };
    const closeSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
    // The buyer layout passes Laravel's asset base, so installations in a
    // subdirectory or on an asset domain still load the correct sound files.
    const soundsBase = String(document.currentScript?.dataset.soundsBase || '/sounds/buyer').replace(/\/+$/, '');
    const soundUrls = {
        // One consistent selected notification sound for positive/informational events.
        success: `${soundsBase}/notification-alert.mp3`,
        error: `${soundsBase}/notification-error.wav`,
        info: `${soundsBase}/notification-alert.mp3`,
    };
    const soundBuffers = new Map();
    const fallbackPlayers = new Map();
    const AUDIO_STORAGE_KEY = 'sari_buyer_toast_sound_enabled';
    const last = { key: '', time: 0, type: '', text: '' };
    let soundEnabled = true;
    let soundVolume = 0.45;
    let lastSoundTime = 0;
    let lastSoundCategory = '';
    let context = null;
    let preloading = false;

    try { soundEnabled = window.localStorage.getItem(AUDIO_STORAGE_KEY) !== 'false'; }
    catch (_) { /* Private-browsing storage can be unavailable. */ }

    function setSoundEnabled(enabled) {
        soundEnabled = Boolean(enabled);
        try { window.localStorage.setItem(AUDIO_STORAGE_KEY, String(soundEnabled)); } catch (_) {}
        return soundEnabled;
    }
    function setSoundVolume(value) {
        const n = Number(value);
        if (Number.isFinite(n)) soundVolume = Math.max(0, Math.min(1, n));
        return soundVolume;
    }

    // Preload the selected MP3 and the distinct error WAV as soon as the page loads.
    // This removes the first-toast request/decode bottleneck in normal conditions.
    function warmAudio() {
        if (preloading) return;
        preloading = true;
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (typeof AudioContextClass === 'function') {
            try { context = new AudioContextClass({ latencyHint: 'interactive' }); }
            catch (_) { context = null; }
        }
        if (typeof window.Audio === 'function') {
            for (const [type, url] of Object.entries(soundUrls)) {
                const player = new window.Audio(url);
                player.preload = 'auto';
                try { player.load(); } catch (_) {}
                fallbackPlayers.set(type, player);
            }
        }
        if (context) {
            for (const [type, url] of Object.entries(soundUrls)) {
                fetch(url, { cache: 'force-cache' })
                    .then((res) => { if (!res.ok) throw new Error('Audio preload failed'); return res.arrayBuffer(); })
                    .then((data) => context.decodeAudioData(data))
                    .then((buffer) => soundBuffers.set(type, buffer))
                    .catch(() => { /* Toast continues silently if a sound cannot load. */ });
            }
        }
    }

    // Browsers permit sound only after user activation. Unlock the context on the
    // first click/tap/key press instead of waiting until the notification is shown.
    function unlockAudio() {
        if (context?.state === 'suspended') {
            try { context.resume().catch(() => {}); } catch (_) {}
        }
    }
    document.addEventListener('pointerdown', unlockAudio, { capture: true, passive: true });
    document.addEventListener('keydown', unlockAudio, { capture: true });
    document.addEventListener('click', unlockAudio, { capture: true });
    document.addEventListener('touchstart', unlockAudio, { capture: true, passive: true });

    // Start an already-decoded SARI sound in the *same call* as the toast.
    // No Promise/microtask or AudioContext.resume() is in the hot path.
    function startBufferedSound(type) {
        const buffer = soundBuffers.get(type);
        if (!buffer || context?.state !== 'running') return false;
        try {
            const source = context.createBufferSource();
            const gain = context.createGain();
            gain.gain.value = soundVolume;
            source.buffer = buffer;
            source.connect(gain);
            gain.connect(context.destination);
            source.start(0);
            return true;
        } catch (_) { return false; }
    }

    // If an action arrives before a WAV is ready, use an immediate local chime
    // rather than queue the sound and play it after the popup has appeared.
    function startInstantChime(type) {
        if (context?.state !== 'running') return false;
        try {
            const at = context.currentTime;
            const pitches = type === 'error' ? [440, 350] :
                type === 'info' ? [520, 660] : [660, 880];
            pitches.forEach((frequency, index) => {
                const start = at + index * 0.095;
                const oscillator = context.createOscillator();
                const gain = context.createGain();
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(frequency, start);
                gain.gain.setValueAtTime(0.001, start);
                gain.gain.exponentialRampToValueAtTime(Math.max(0.001, 0.11 * soundVolume), start + 0.012);
                gain.gain.exponentialRampToValueAtTime(0.001, start + 0.15);
                oscillator.connect(gain);
                gain.connect(context.destination);
                oscillator.start(start);
                oscillator.stop(start + 0.16);
            });
            return true;
        } catch (_) { return false; }
    }

    function playSound(tone, force = false) {
        if (!soundEnabled || soundVolume <= 0) return false;
        if (!force && document.visibilityState === 'hidden') return false;
        const now = Date.now();
        const type = Object.prototype.hasOwnProperty.call(soundUrls, tone) ? tone : 'info';
        // The chosen MP3 is 2.4 seconds. Suppress repeated normal chimes,
        // but never let a previous success suppress a new warning sound.
        const category = type === 'error' ? 'error' : 'notification';
        if (!force && category === lastSoundCategory && now - lastSoundTime < 1600) return false;

        // Usually this is the path after a buyer has clicked/tapped once.
        if (startBufferedSound(type)) {
            lastSoundTime = now;
            lastSoundCategory = category;
            return true;
        }

        const player = fallbackPlayers.get(type);
        if (player && (player.readyState >= 2 || context?.state !== 'running')) {
            try {
                player.pause();
                player.currentTime = 0;
                player.volume = soundVolume;
                // The HTMLAudio play() call is initiated *now*, not after resume().
                const promise = player.play();
                if (promise?.then) promise.then(() => {
                    // Never let a slow download start an unrelated late sound.
                    if (Date.now() - now > 200) player.pause();
                }).catch(() => {
                    // If a gesture has just unlocked Web Audio, use it immediately.
                    if (Date.now() - now < 200 && !startBufferedSound(type)) startInstantChime(type);
                });
                lastSoundTime = now;
                lastSoundCategory = category;
                return true;
            } catch (_) { /* Try instant Web Audio below. */ }
        }

        // Audio not decoded yet: play a short synthesized fallback immediately.
        if (startInstantChime(type)) {
            lastSoundTime = now;
            lastSoundCategory = category;
            return true;
        }
        // No playback is guaranteed before browser user activation. A click/
        // pointerdown handler already resumes the context for later actions.
        return false;
    }

    function stack() {
        let node = document.getElementById('sariBuyerToastStack');
        if (!node) {
            node = document.createElement('div');
            node.id = 'sariBuyerToastStack';
            node.setAttribute('aria-label', 'Action notifications');
            document.body.appendChild(node);
        }
        return node;
    }

    function show(message, tone = 'success', options = {}) {
        if (!document.body) return null;
        const text = String(message ?? '').trim();
        if (!text) return null;
        const type = Object.prototype.hasOwnProperty.call(presets, tone) ? tone : 'info';
        const key = type + ':' + text;
        const now = Date.now();
        const repeatedCartAdd = type === 'success' && last.type === 'success'
            && /added.*cart/i.test(text) && /added.*cart/i.test(last.text);
        if ((last.key === key && now - last.time < 850) || (repeatedCartAdd && now - last.time < 1000)) return null;
        last.key = key;
        last.type = type;
        last.text = text;
        last.time = now;
        const limit = Number(options.duration);
        const duration = Number.isFinite(limit) && limit >= 1500 && limit <= 20000 ? limit : presets[type].duration;

        const toast = document.createElement('div');
        toast.className = 'sari-modern-toast';
        toast.dataset.tone = type;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        toast.setAttribute('aria-atomic', 'true');
        toast.title = text;

        const icon = document.createElement('span');
        icon.className = 'sari-modern-toast-icon';
        icon.setAttribute('aria-hidden', 'true');
        // SVG is a fixed template, never user-provided markup.
        icon.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${presets[type].svg}</svg>`;

        const copy = document.createElement('div');
        copy.className = 'sari-modern-toast-copy';
        const detail = document.createElement('p');
        detail.className = 'sari-modern-toast-message';
        detail.textContent = text;
        copy.appendChild(detail);

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'sari-modern-toast-close';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.innerHTML = closeSvg;
        toast.append(icon, copy, close);

        // Show the popup immediately. Sound playback is nonblocking and follows.
        const host = stack();
        host.appendChild(toast);
        while (host.children.length > 3) host.firstElementChild?.remove();
        if (options.sound !== false) playSound(type);

        let remaining = duration;
        let timeoutId = null;
        let started = 0;
        let dismissed = false;
        const reduced = !!window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            window.clearTimeout(timeoutId);
            toast.classList.add('is-leaving');
            if (reduced) toast.remove();
            else window.setTimeout(() => toast.remove(), 180);
        }
        function resume() {
            if (dismissed || timeoutId) return;
            started = Date.now();
            timeoutId = window.setTimeout(dismiss, remaining);
        }
        function pause() {
            if (!timeoutId || dismissed) return;
            window.clearTimeout(timeoutId);
            timeoutId = null;
            remaining = Math.max(0, remaining - (Date.now() - started));
        }
        close.addEventListener('click', dismiss);
        toast.addEventListener('mouseenter', pause);
        toast.addEventListener('mouseleave', () => { if (!toast.matches(':focus-within')) resume(); });
        toast.addEventListener('focusin', pause);
        toast.addEventListener('focusout', () => {
            window.setTimeout(() => { if (!toast.matches(':hover, :focus-within')) resume(); }, 0);
        });
        resume();
        return { element: toast, dismiss };
    }

    window.SariBuyerToast = {
        show,
        success(message, options = {}) { return show(message, 'success', options); },
        error(message, options = {}) { return show(message, 'error', options); },
        info(message, options = {}) { return show(message, 'info', options); },
        setSoundEnabled,
        setSoundVolume,
        isSoundEnabled() { return soundEnabled; },
        previewSound(tone = 'success') { return playSound(tone, true); },
    };

    // Native invalid forms do not trigger submit or a Laravel error response.
    // Only surface the FIRST invalid field after a deliberate submit action.
    // This preserves the browser's validation bubble, blocks no form submissions,
    // and avoids noise from programmatic checkValidity() or typing.
    let intendedForm = null;
    let intendedAt = 0;
    const formErrorTimes = new WeakMap();

    function rememberSubmitIntent(element) {
        const form = element?.form || element?.closest?.('form');
        if (!form || form.noValidate) return;
        intendedForm = form;
        intendedAt = Date.now();
    }
    document.addEventListener('pointerdown', (event) => {
        const button = event.target?.closest?.('button, input[type="submit"], input[type="image"]');
        if (!button || button.matches?.('[disabled]')) return;
        if (button.matches('button:not([type]), button[type="submit"], input[type="submit"], input[type="image"]') && !button.formNoValidate) {
            rememberSubmitIntent(button);
        }
    }, { capture: true, passive: true });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' || event.isComposing || event.defaultPrevented) return;
        const element = event.target;
        if (!element || !element.closest?.('form')) return;
        // Enter in a textarea is a newline, not a form submission.
        if (element.matches?.('textarea, [contenteditable="true"]')) return;
        rememberSubmitIntent(element);
    }, { capture: true });

    function fieldLabel(field) {
        const raw = field?.labels?.[0]?.textContent || field?.getAttribute?.('aria-label')
            || field?.getAttribute?.('placeholder') || field?.name || 'this field';
        return String(raw).replace(/\s+/g, ' ').replace(/[*:]+$/g, '').trim().slice(0, 65) || 'this field';
    }
    document.addEventListener('invalid', (event) => {
        const field = event.target;
        if (!field || !field.form || !field.validity) return;
        if (field.form !== intendedForm || Date.now() - intendedAt > 1800) return;
        if (!field.closest?.('#buyerMainContent')) return;
        const form = field.form;
        const now = Date.now();
        if (now - (formErrorTimes.get(form) || 0) < 1600) return;
        formErrorTimes.set(form, now);
        // Only one toast and warning sound, even if several fields are invalid.
        intendedForm = null;
        const label = fieldLabel(field);
        const v = field.validity;
        const message = v.valueMissing ? `Please fill in ${label}.`
            : v.typeMismatch && field.type === 'email' ? `Enter a valid email address in ${label}.`
            : v.typeMismatch ? `Enter a valid value for ${label}.`
            : v.tooShort ? `${label} is too short.`
            : v.tooLong ? `${label} is too long.`
            : v.patternMismatch ? `Please check the format of ${label}.`
            : v.rangeUnderflow || v.rangeOverflow || v.stepMismatch || v.badInput
                ? `Please check the value entered for ${label}.`
            : `Please correct ${label} before continuing.`;
        show(message, 'error');
    }, true);

    function consumeFlash() {
        const node = document.getElementById('sariBuyerToastFlash');
        if (!node || node.dataset.toastConsumed === '1') return;
        node.dataset.toastConsumed = '1';
        const success = (node.dataset.success || '').trim();
        const error = (node.dataset.error || '').trim();
        const cancel = (node.dataset.cancelSuccess || '').trim();
        const info = (node.dataset.info || '').trim();
        const status = (node.dataset.status || '').trim();
        if (success) show(success, 'success');
        if (cancel && cancel !== success) show(cancel, 'success');
        if (error) show(error, 'error');
        if (info) show(info, 'info');
        if (status && ![success, cancel, error, info].includes(status)) show(status, 'success');
    }

    const handledEvents = new WeakSet();
    function onToast(event) {
        if (handledEvents.has(event)) return;
        handledEvents.add(event);
        const detail = event.detail || {};
        if (detail.message) show(detail.message, detail.type || detail.tone || 'info', detail);
    }
    document.addEventListener('sari:toast', onToast);
    window.addEventListener('sari:toast', onToast);
    // Buyer-wide notification counter is updated by resources/js/buyer.js.
    // Announce only an INCREASE after the initial count is known; never
    // replay old unread notifications on page load or read/decrement events.
    document.addEventListener('sari:buyer-notification-count-changed', (event) => {
        const previous = Number(event.detail?.previous);
        const current = Number(event.detail?.count);
        if (!Number.isFinite(previous) || !Number.isFinite(current) || current <= previous) return;
        const newlyUnread = Math.max(1, Math.floor(current - previous));
        show(newlyUnread === 1 ? 'You have a new notification.'
            : `You have ${newlyUnread} new notifications.`, 'info');
    });
    document.addEventListener('livewire:navigated', consumeFlash);
    // Pre-create the players before a server-side flash toast is rendered.
    warmAudio();
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', consumeFlash, { once: true });
    else consumeFlash();


    // Bridge existing Buyer AJAX actions into the same toast component.
    // Calls are observed *after* Laravel replies; requests and responses are not changed.
    // Only JSON responses to same-origin Buyer mutation endpoints are considered.
    // Background GETs, cart counters and periodic polling will never create toasts.
    function buyerMutation(urlValue, method) {
        if (!['POST', 'PUT', 'PATCH', 'DELETE'].includes(String(method || '').toUpperCase())) return false;
        try {
            const url = new URL(String(urlValue || ''), window.location.href);
            return url.origin === window.location.origin && /^\/buyer(?:\/|$)/.test(url.pathname);
        } catch (_) { return false; }
    }

    function feedbackFromData(data, ok) {
        if (!data || typeof data !== 'object' || Array.isArray(data)) return;
        const firstError = data.errors && typeof data.errors === 'object'
            ? Object.values(data.errors).flat().find(item => typeof item === 'string' && item.trim())
            : null;
        const message = String(
            (!ok && (firstError || data.message)) ||
            (ok && (data.message || data.success_message)) || ''
        ).trim();
        if (!message || message.length > 300) return;
        const accepted = ok && data.ok !== false && data.success !== false;
        show(message, accepted ? 'success' : 'error');
    }

    if (typeof window.fetch === 'function' && !window.__SARI_BUYER_TOAST_FETCH_BRIDGE__) {
        window.__SARI_BUYER_TOAST_FETCH_BRIDGE__ = true;
        const originalFetch = window.fetch;
        window.fetch = function (input, init) {
            const url = typeof input === 'string' || input instanceof URL
                ? input : input?.url;
            const method = init?.method || (typeof input === 'object' ? input?.method : '') || 'GET';
            const shouldObserve = buyerMutation(url, method);
            const responsePromise = originalFetch.apply(this, arguments);
            if (shouldObserve) {
                responsePromise.then((response) => {
                    if (!response || !response.headers?.get('content-type')?.toLowerCase().includes('json')) return;
                    response.clone().json()
                        .then((data) => feedbackFromData(data, response.ok))
                        .catch(() => {});
                }).catch(() => {});
            }
            return responsePromise;
        };
    }

    // Some Laravel pages use Axios/XHR instead of fetch. Read the response,
    // but never alter the request, response, or any existing event handlers.
    if (typeof window.XMLHttpRequest === 'function' && !window.__SARI_BUYER_TOAST_XHR_BRIDGE__) {
        window.__SARI_BUYER_TOAST_XHR_BRIDGE__ = true;
        const proto = window.XMLHttpRequest.prototype;
        const oldOpen = proto.open;
        const oldSend = proto.send;
        const requestInfo = new WeakMap();
        proto.open = function (method, url) {
            requestInfo.set(this, { method, url });
            return oldOpen.apply(this, arguments);
        };
        proto.send = function () {
            const info = requestInfo.get(this);
            if (info && buyerMutation(info.url, info.method)) {
                this.addEventListener('loadend', () => {
                    try {
                        const contentType = this.getResponseHeader('content-type') || '';
                        if (!contentType.toLowerCase().includes('json')) return;
                        const data = this.responseType === 'json'
                            ? this.response
                            : JSON.parse(this.responseText);
                        feedbackFromData(data, this.status >= 200 && this.status < 300);
                    } catch (_) { /* Feedback must not interfere with app actions. */ }
                }, { once: true });
            }
            return oldSend.apply(this, arguments);
        };
    }

    // Flash node changes during Livewire navigation and after ordinary redirects.
    // Observe inserted flash nodes, not every click: only real server messages show.
    if (document.body && typeof window.MutationObserver === 'function') {
        const flashObserver = new MutationObserver(() => consumeFlash());
        const content = document.getElementById('buyerMainContent') || document.body;
        flashObserver.observe(content, { childList: true, subtree: true });
    }
    window.addEventListener('pageshow', consumeFlash);
})();
