/** SARI Rider notification feedback: inspired by Buyer compact toasts.
 * User-selected MP3 for incoming notifications/assignments; original success/error chimes.
 * Server-confirmed actions only. Browser audio needs a user gesture.
 */
(() => {
    'use strict';
    if (window.__SARI_COURIER_SOUND_ENGINE_V5__) return;
    window.__SARI_COURIER_SOUND_ENGINE_V5__ = true;

    const script = document.currentScript;
    const soundsBase = String(script?.dataset.soundsBase || '/sounds/courier').replace(/\/+$/, '');
    const owner = String(script?.dataset.owner || 'rider');
    const storePrefix = 'sari_rider_notify_v5_' + owner + '_';
    const types = ['success', 'info', 'alert', 'error'];
    // Keep the exact uploaded chime for incoming messages and urgent new assignments.
    // Success and error remain distinct for clear action feedback.
    const soundFiles = {
        success: 'notification-success.wav',
        info: 'notification-info.mp3',
        alert: 'notification-alert.mp3',
        error: 'notification-error.wav',
    };
    const icons = {
        success: '<path d="m5 12 4.5 4.5L19 7"/>',
        info: '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
        alert: '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        error: '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/>',
    };
    const timings = { success: 4200, info: 5000, alert: 7000, error: 6500 };
    const readLocal = (key, fallback) => { try { return localStorage.getItem(storePrefix + key) ?? fallback; } catch (_) { return fallback; } };
    const writeLocal = (key, value) => { try { localStorage.setItem(storePrefix + key, String(value)); } catch (_) {} };
    let enabled = readLocal('sound', 'true') !== 'false';
    let volume = Number(readLocal('volume', '0.78'));
    if (!Number.isFinite(volume)) volume = 0.78;
    volume = Math.min(1, Math.max(0, volume));
    let context = null;
    let lastSound = 0;
    let lastToast = { key: '', at: 0 };
    const buffers = new Map();
    const players = new Map();

    function warmAudio() {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (typeof AudioContextClass === 'function') {
            try { context = new AudioContextClass({ latencyHint: 'interactive' }); } catch (_) {}
        }
        for (const type of types) {
            const url = `${soundsBase}/${soundFiles[type]}`;
            if (typeof window.Audio === 'function') {
                try {
                    const player = new Audio(url);
                    player.preload = 'auto';
                    player.load();
                    players.set(type, player);
                } catch (_) {}
            }
            if (context) {
                fetch(url, { cache: 'force-cache' })
                    .then(res => { if (!res.ok) throw Error('sound unavailable'); return res.arrayBuffer(); })
                    .then(data => context.decodeAudioData(data))
                    .then(buffer => buffers.set(type, buffer))
                    .catch(() => {});
            }
        }
    }
    function unlock() {
        if (context?.state === 'suspended') {
            try { context.resume().catch(() => {}); } catch (_) {}
        }
    }
    document.addEventListener('pointerdown', unlock, { capture: true, passive: true });
    document.addEventListener('keydown', unlock, { capture: true });
    document.addEventListener('touchstart', unlock, { capture: true, passive: true });

    function synthetic(type) {
        if (context?.state !== 'running') return false;
        try {
            const now = context.currentTime;
            const pitches = type === 'error' ? [490, 370] : type === 'alert' ? [660, 990, 1175] : type === 'success' ? [587, 740, 988] : [660, 880];
            pitches.forEach((pitch, i) => {
                const start = now + i * 0.115;
                const osc = context.createOscillator();
                const gain = context.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(pitch, start);
                gain.gain.setValueAtTime(0.001, start);
                gain.gain.exponentialRampToValueAtTime(Math.max(.001, volume * .24), start + .018);
                gain.gain.exponentialRampToValueAtTime(.001, start + .26);
                osc.connect(gain).connect(context.destination);
                osc.start(start); osc.stop(start + .27);
            });
            return true;
        } catch (_) { return false; }
    }
    function play(type = 'info', force = false) {
        if (!enabled || volume <= 0 || (!force && document.hidden)) return false;
        const now = Date.now();
        if (!force && now - lastSound < 900) return false;
        const tone = types.includes(type) ? type : 'info';
        if (context?.state === 'running' && buffers.has(tone)) {
            try {
                const source = context.createBufferSource();
                const gain = context.createGain();
                gain.gain.value = volume;
                source.buffer = buffers.get(tone);
                source.connect(gain).connect(context.destination);
                source.start();
                lastSound = now;
                return true;
            } catch (_) {}
        }
        const player = players.get(tone);
        if (player) {
            try {
                player.pause(); player.currentTime = 0; player.volume = volume;
                const promise = player.play();
                if (promise?.then) promise.catch(() => { if (Date.now() - now < 200) synthetic(tone); });
                lastSound = now;
                return true;
            } catch (_) {}
        }
        if (synthetic(tone)) { lastSound = now; return true; }
        return false;
    }
    function toggleSound(value) {
        enabled = !!value;
        writeLocal('sound', enabled);
        document.querySelectorAll('[data-courier-sound-toggle]').forEach(button => {
            button.textContent = enabled ? 'Sound on' : 'Sound off';
            button.setAttribute('aria-pressed', String(enabled));
            button.title = enabled ? 'Mute Rider notifications' : 'Enable Rider notification sounds';
        });
        return enabled;
    }
    function setVolume(value) {
        const n = Number(value);
        if (Number.isFinite(n)) { volume = Math.min(1, Math.max(0, n)); writeLocal('volume', volume); }
        document.querySelectorAll('[data-courier-sound-volume]').forEach(input => { input.value = String(Math.round(volume * 100)); });
        return volume;
    }

    function toastStack() {
        let node = document.getElementById('sariCourierToastStack');
        if (!node) {
            node = document.createElement('div');
            node.id = 'sariCourierToastStack';
            node.setAttribute('aria-label', 'Rider activity notifications');
            document.body.append(node);
        }
        return node;
    }
    function show(title, message, tone = 'info', opts = {}) {
        if (!document.body) return null;
        const type = types.includes(tone) ? tone : 'info';
        const heading = String(title || 'Rider update').trim().slice(0, 130);
        const text = String(message || '').trim().slice(0, 400);
        const key = type + ':' + heading + ':' + text;
        const now = Date.now();
        if (key === lastToast.key && now - lastToast.at < 1400) return null;
        lastToast = { key, at: now };
        const el = document.createElement('div');
        el.className = 'sari-courier-toast'; el.dataset.tone = type;
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        const icon = document.createElement('span');
        icon.className = 'sari-courier-toast-icon'; icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${icons[type]}</svg>`;
        const copy = document.createElement('div'); copy.className = 'sari-courier-toast-copy';
        const h = document.createElement('strong'); h.className = 'sari-courier-toast-title'; h.textContent = heading;
        const p = document.createElement('p'); p.className = 'sari-courier-toast-message'; p.textContent = text;
        copy.append(h, p);
        const close = document.createElement('button'); close.type = 'button';
        close.className = 'sari-courier-toast-close'; close.setAttribute('aria-label', 'Dismiss notification');
        close.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>';
        el.append(icon, copy, close);
        const host = toastStack(); host.append(el);
        while (host.children.length > 3) host.firstElementChild?.remove();
        if (opts.sound !== false) play(type);
        const time = Math.max(1800, Math.min(18000, Number(opts.duration) || timings[type]));
        let remaining = time, started = 0, timer = null, dismissed = false;
        const reduce = !!window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
        const dismiss = () => {
            if (dismissed) return;
            dismissed = true; clearTimeout(timer); el.classList.add('is-leaving');
            if (reduce) el.remove(); else setTimeout(() => el.remove(), 180);
        };
        const resume = () => { if (timer || dismissed) return; started = Date.now(); timer = setTimeout(dismiss, remaining); };
        const pause = () => { if (!timer) return; clearTimeout(timer); timer = null; remaining = Math.max(0, remaining - (Date.now() - started)); };
        close.addEventListener('click', dismiss);
        el.addEventListener('mouseenter', pause);
        el.addEventListener('mouseleave', () => { if (!el.matches(':focus-within')) resume(); });
        el.addEventListener('focusin', pause);
        el.addEventListener('focusout', () => setTimeout(() => { if (!el.matches(':hover,:focus-within')) resume(); }, 0));
        resume(); return { element:el, dismiss };
    }
    let activeModal = null;
    function closeModal() {
        if (!activeModal) return;
        const { node, focus } = activeModal;
        node.hidden = true; document.body.style.overflow = activeModal.overflow;
        activeModal = null;
        focus?.focus?.();
    }
    function assignmentModal(item) {
        if (!document.body || activeModal || document.hidden) return false;
        const modal = document.getElementById('sariCourierNoticeModal');
        if (!modal) return false;
        const title = modal.querySelector('[data-scn-title]');
        const text = modal.querySelector('[data-scn-message]');
        const link = modal.querySelector('[data-scn-link]');
        if (title) title.textContent = String(item.title || 'New assignment');
        if (text) text.textContent = String(item.message || 'You have a new assigned delivery.');
        let url = null;
        try { const parsed = new URL(String(item.url || ''), location.origin); if (parsed.origin === location.origin && /^https?:$/.test(parsed.protocol)) url = parsed.href; } catch (_) {}
        if (link) { link.href = url || '#'; link.hidden = !url; link.dataset.notificationId = String(item.id || ''); }
        activeModal = { node:modal, focus:document.activeElement, overflow:document.body.style.overflow };
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        modal.querySelector('[data-scn-dismiss]')?.focus();
        return true;
    }
    document.addEventListener('click', event => {
        if (event.target.closest('[data-scn-dismiss]') || event.target.matches('[data-scn-backdrop]')) closeModal();
        if (event.target.closest('[data-courier-sound-toggle]')) toggleSound(!enabled);
        if (event.target.closest('[data-courier-sound-preview]')) {
            if (!enabled) toggleSound(true);
            play('alert', true);
        }
    });
    document.addEventListener('input', event => {
        const input = event.target.closest('[data-courier-sound-volume]');
        if (input) setVolume(Number(input.value) / 100);
    });
    document.addEventListener('keydown', event => {
        if (!activeModal) return;
        if (event.key === 'Escape') { event.preventDefault(); closeModal(); }
        if (event.key === 'Tab') {
            const controls = [...activeModal.node.querySelectorAll('button:not(:disabled),a[href]:not([hidden])')];
            if (!controls.length) return;
            const i = controls.indexOf(document.activeElement);
            if (event.shiftKey && i <= 0) { event.preventDefault(); controls.at(-1).focus(); }
            else if (!event.shiftKey && i === controls.length - 1) { event.preventDefault(); controls[0].focus(); }
        }
    });

    window.SariCourierNotify = {
        show,
        success: (msg,opts={}) => show('Success',msg,'success',opts),
        error: (msg,opts={}) => show('Action unsuccessful',msg,'error',opts),
        info: (msg,opts={}) => show('Rider update',msg,'info',opts),
        incoming: (item) => {
            const title = String(item?.title || 'New Rider notification');
            const message = String(item?.message || '');
            const urgent = /^(New pickup assignment|New last-mile batch assigned)$/i.test(title);
            const shown = show(urgent ? 'New assignment' : title, message, urgent ? 'alert' : 'info', { duration: urgent ? 8000 : 6000 });
            if (shown && urgent) assignmentModal(item);
            return shown;
        },
        setSoundEnabled: toggleSound, isSoundEnabled: () => enabled,
        setVolume, getVolume: () => volume,
        testSound: () => { if (!enabled) toggleSound(true); return play('alert',true); },
    };
    window.SariCourierToast = (title,message) => show(title, message, 'success');

    function consumeFlash() {
        const el = document.getElementById('sariCourierFlashNotifications');
        if (!el || el.dataset.consumed === 'true') return;
        el.dataset.consumed = 'true';
        const success = el.dataset.success?.trim();
        const error = el.dataset.error?.trim();
        const status = el.dataset.status?.trim();
        if (success) show('Action completed', success, 'success');
        if (error) show('Action unsuccessful', error, 'error');
        if (status && status !== success && status !== error) show('Rider update', status, 'info');
    }
    function actionUrl(url,method) {
        if (!['POST','PUT','PATCH','DELETE'].includes(String(method || '').toUpperCase())) return false;
        try {
            const parsed = new URL(String(url), location.href);
            if (parsed.origin !== location.origin) return false;
            // Ignore notification mark-as-read and logout: not Rider delivery events.
            return /^\/courier(?:\/|$)/.test(parsed.pathname)
                && !/^\/courier\/(?:notifications\/|logout(?:\/|$))/.test(parsed.pathname);
        } catch (_) { return false; }
    }
    function responseToast(body, ok) {
        if (!body || typeof body !== 'object' || Array.isArray(body)) return;
        const error = body.errors && typeof body.errors === 'object' ? Object.values(body.errors).flat().find(x => typeof x === 'string') : '';
        const message = String(ok ? (body.message || body.success_message || body.payload || '') : (error || body.message || body.payload || '')).trim();
        if (!message || message.length > 350) return;
        const accepted = ok && body.ok !== false && body.success !== false;
        show(accepted ? 'Action completed' : 'Action unsuccessful', message, accepted ? 'success' : 'error');
    }
    // Observe successful/failed *responses*, never preemptively celebrate form submits or change server logic.
    if (typeof window.fetch === 'function' && !window.__SARI_COURIER_FETCH_TOAST_V5__) {
        window.__SARI_COURIER_FETCH_TOAST_V5__ = true;
        const nativeFetch = window.fetch;
        window.fetch = function(input, init) {
            const url = typeof input === 'string' || input instanceof URL ? input : input?.url;
            const method = init?.method || (typeof input === 'object' ? input?.method : '') || 'GET';
            const shouldObserve = actionUrl(url, method);
            const task = nativeFetch.apply(this, arguments);
            if (shouldObserve) task.then(res => {
                if (res?.headers?.get('content-type')?.toLowerCase().includes('json'))
                    res.clone().json().then(data => responseToast(data,res.ok)).catch(() => {});
            }).catch(() => {});
            return task;
        };
    }
    if (typeof window.XMLHttpRequest === 'function' && !window.__SARI_COURIER_XHR_TOAST_V5__) {
        window.__SARI_COURIER_XHR_TOAST_V5__ = true;
        const proto = window.XMLHttpRequest.prototype, originalOpen = proto.open, originalSend = proto.send, info = new WeakMap();
        proto.open = function(method,url) { info.set(this,{method,url});return originalOpen.apply(this,arguments); };
        proto.send = function() {
            const item = info.get(this);
            if (item && actionUrl(item.url,item.method)) this.addEventListener('loadend',() => {
                try {
                    if (!(this.getResponseHeader('content-type') || '').toLowerCase().includes('json')) return;
                    const data = this.responseType === 'json' ? this.response : JSON.parse(this.responseText);
                    responseToast(data,this.status >= 200 && this.status < 300);
                } catch (_) {}
            },{once:true});
            return originalSend.apply(this,arguments);
        };
    }
    warmAudio();
    toggleSound(enabled); setVolume(volume);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', consumeFlash, {once:true});
    else consumeFlash();
    document.addEventListener('livewire:navigated', consumeFlash);
    window.addEventListener('pageshow', consumeFlash);
})();
