/** SARI Buyer centralized action toasts.
 * Deployed as public/js/buyer/sari-toast.js (without a Vite build dependency).
 * Preloads original WAV sounds and plays at toast time without waiting for resume/decode.
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
        success: `${soundsBase}/notification-success.wav`,
        error: `${soundsBase}/notification-error.wav`,
        info: `${soundsBase}/notification-info.wav`,
    };
    const soundBuffers = new Map();
    const fallbackPlayers = new Map();
    const AUDIO_STORAGE_KEY = 'sari_buyer_toast_sound_enabled';
    const last = { key: '', time: 0, type: '', text: '' };
    let soundEnabled = true;
    let soundVolume = 0.45;
    let lastSoundTime = 0;
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

    // Fetch and decode all three small WAVs as soon as the app JavaScript loads.
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

    // Start an already-decoded SARI WAV in the *same call* as the toast.
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
            const pitches = type === 'error' ? [400, 300] :
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
        if (!force && now - lastSoundTime < 250) return false;
        const type = Object.prototype.hasOwnProperty.call(soundUrls, tone) ? tone : 'info';

        // Usually this is the path after a buyer has clicked/tapped once.
        if (startBufferedSound(type)) {
            lastSoundTime = now;
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
                return true;
            } catch (_) { /* Try instant Web Audio below. */ }
        }

        // WAV not decoded yet: play a short chime now rather than sound late.
        if (startInstantChime(type)) {
            lastSoundTime = now;
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
