// The Vite module persists during Livewire navigation; polling must not.
let orderPollTimer = null;
let orderPollController = null;
let orderPollGeneration = 0;

function stopBuyerOrderPolling() {
    orderPollGeneration++;
    if (orderPollTimer !== null) window.clearInterval(orderPollTimer);
    orderPollTimer = null;
    orderPollController?.abort();
    orderPollController = null;
}

function initBuyerOrderPolling() {
    stopBuyerOrderPolling();
    if (!document.querySelector('.sari-orders-page, .sari-track-page')) return;

    const generation = orderPollGeneration;
    const url = '/buyer/orders-live-state';
    let revision = null;
    let inFlight = false;
    async function sync() {
        if (document.hidden || inFlight || generation !== orderPollGeneration) return;
        inFlight = true;
        const controller = new AbortController();
        orderPollController = controller;
        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
            });
            if (!response.ok || generation !== orderPollGeneration) return;
            const data = await response.json();
            if (generation !== orderPollGeneration) return;
            if (revision && data.revision && String(revision) !== String(data.revision)) {
                // Preserve the SPA navigation shell and browser asset cache.
                // Still reload the page data when the order revision changes.
                if (typeof window.Livewire?.navigate === 'function') {
                    window.Livewire.navigate(window.location.href);
                } else {
                    window.location.reload();
                }
                return;
            }
            revision = data.revision || revision;
        } catch (_) {
            // Network failures are retried on the next normal polling interval.
        } finally {
            if (orderPollController === controller) orderPollController = null;
            inFlight = false;
        }
    }
    sync();
    orderPollTimer = window.setInterval(sync, 3500);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuyerOrderPolling, { once: true });
} else {
    initBuyerOrderPolling();
}
// Stop polling as soon as a navigation begins, rather than competing with its HTML request.
document.addEventListener('livewire:navigate', stopBuyerOrderPolling);
document.addEventListener('livewire:navigating', stopBuyerOrderPolling);
document.addEventListener('livewire:navigated', initBuyerOrderPolling);
