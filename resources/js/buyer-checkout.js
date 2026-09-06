document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('buyerCheckoutForm');
    const button = document.getElementById('buyerPlaceOrderButton');
    form?.addEventListener('submit', () => {
        if (button) {
            button.disabled = true;
            button.textContent = 'Creating order...';
        }
    });
});
