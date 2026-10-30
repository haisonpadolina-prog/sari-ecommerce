function initBuyerCheckout() {
    const form = document.getElementById('buyerCheckoutForm');
    if (!form || form.dataset.sariCheckoutBound === '1') return;
    form.dataset.sariCheckoutBound = '1';
    const button = document.getElementById('buyerPlaceOrderButton');
    form?.addEventListener('submit', () => {
        if (button) {
            button.disabled = true;
            button.textContent = 'Creating order...';
        }
    });
 }

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuyerCheckout, { once: true });
} else {
    initBuyerCheckout();
}
document.addEventListener('livewire:navigated', initBuyerCheckout);
