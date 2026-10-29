(function () {
    if (window.__SARI_BUYER_MESSAGES_BADGE__) return;
    window.__SARI_BUYER_MESSAGES_BADGE__ = true;
    const endpoint = '/buyer/messages/unread';
    function display(count) {
        const node = document.getElementById('buyerSidebarMessageCount');
        if (!node) return;
        const total = Math.max(0, Number(count) || 0);
        node.textContent = total > 99 ? '99+' : String(total);
        node.classList.toggle('hidden', total === 0);
        node.classList.toggle('grid', total > 0);
        node.setAttribute('aria-label', total + ' unread seller messages');
    }
    async function update() {
        if (document.hidden) return;
        try {
            const res = await fetch(endpoint, {credentials:'same-origin', cache:'no-store', headers:{Accept:'application/json'}});
            if (res.ok) display((await res.json()).unread_total);
        } catch (_) { /* Badge is progressive enhancement. */ }
    }
    document.addEventListener('sari:buyer-chat-unread', event => display(event.detail.total));
    document.addEventListener('sari:refresh-chat-unread', update);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) update(); });
    document.addEventListener('livewire:navigated', update);
    document.addEventListener('DOMContentLoaded', update);
    if (document.readyState !== 'loading') update();
    setInterval(update, 15000);
})();
