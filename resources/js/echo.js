import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const configuredHost = String(import.meta.env.VITE_REVERB_HOST || '').trim();
const configuredScheme = String(import.meta.env.VITE_REVERB_SCHEME || '').trim().toLowerCase();
const configuredPort = Number(import.meta.env.VITE_REVERB_PORT || 0);

const pageIsSecure = window.location.protocol === 'https:';
const configuredHostIsLocal = ['localhost', '127.0.0.1', '0.0.0.0', '::1'].includes(
    configuredHost.toLowerCase()
);

// A common deployment bug is building production assets while VITE_REVERB_HOST
// still points at localhost. On a secure remote page that would make the browser
// connect to the visitor's own machine (or get blocked as mixed content).
// Prefer the current public hostname in that case; a reverse proxy can forward
// the websocket to the Reverb server.
const usePageHostFallback = pageIsSecure && configuredHostIsLocal;
const wsHost = usePageHostFallback
    ? window.location.hostname
    : (configuredHost || window.location.hostname);
const forceTLS = pageIsSecure || configuredScheme === 'https';
const wsPort = usePageHostFallback
    ? Number(window.location.port || 80)
    : (configuredPort || 80);
const wssPort = usePageHostFallback
    ? Number(window.location.port || 443)
    : (configuredPort || 443);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost,
    wsPort,
    wssPort,
    forceTLS,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

// Keep messaging pages informed about the actual Reverb socket state.
if (!window.__SARI_REVERB_CONNECTION_STATE_BOUND__) {
    const connection = window.Echo?.connector?.pusher?.connection;

    const announce = (state) => {
        window.dispatchEvent(new CustomEvent('sari:realtime-state', {
            detail: { state },
        }));
    };

    if (connection?.bind) {
        connection.bind('connected', () => announce('live'));
        connection.bind('connecting', () => announce('connecting'));
        connection.bind('disconnected', () => announce('fallback'));
        connection.bind('unavailable', () => announce('fallback'));
        connection.bind('failed', () => announce('fallback'));
        connection.bind('error', () => announce('fallback'));
    }

    window.__SARI_REVERB_CONNECTION_STATE_BOUND__ = true;
}

