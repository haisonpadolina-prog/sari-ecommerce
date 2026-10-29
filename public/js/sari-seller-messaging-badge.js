(function () {
    if (window.__SARI_SELLER_MESSAGES_BADGE__) return;
    window.__SARI_SELLER_MESSAGES_BADGE__ = true;
    function updateBadge(count) {
        const node = document.querySelector('#sellerSidebarMessageBadge');
        if (!node) return;
        const total = Math.max(0, Number(count) || 0);
        node.textContent = total > 99 ? '99+' : String(total);
        node.dataset.hasUnread = total ? 'true' : 'false';
        node.classList.toggle('hidden', total === 0);
        node.classList.toggle('grid', total > 0);
        node.setAttribute('aria-label', total + ' unread buyer messages');
    }
    async function refresh() {
        if (document.hidden) return;
        try {
            const response = await fetch('/seller/buyer-messages/unread', {credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}});
            if (response.ok) updateBadge((await response.json()).unread_total);
        } catch (_) { /* Safe refresh fallback. */ }
    }
    document.addEventListener('DOMContentLoaded', refresh);
    document.addEventListener('livewire:navigated', refresh);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
    if (document.readyState !== 'loading') refresh();
    const startSubscription = () => {
        const sidebar = document.getElementById('sellerSidebarMessageBadge');
        const sellerId = document.documentElement.dataset.sariSellerId || sidebar?.dataset.sellerId;
        if (window.Echo && sellerId && !window.__SARI_SELLER_BADGE_CHANNEL__) {
            try {
                const channel = `sari.buyer-seller.seller.${sellerId}`;
                window.__SARI_SELLER_BADGE_CHANNEL__ = window.Echo.private(channel)
                    .listen('.buyer-seller.message', refresh);
            } catch (_) { /* Polling handles unavailable socket. */ }
        }
    };
    document.addEventListener('DOMContentLoaded', startSubscription);
    if (document.readyState !== 'loading') startSubscription();
    setInterval(refresh, 12000);
})();
