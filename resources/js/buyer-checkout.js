// AJAX checkout avoids fetching/rendering My Orders before acknowledging success.
// The Laravel controller remains the authority for inventory, discounts and orders.
function initBuyerCheckout() {
    const form = document.getElementById('buyerCheckoutForm');
    if (!form || form.dataset.sariCheckoutBound === '1') return;
    form.dataset.sariCheckoutBound = '1';

    const button = document.getElementById('buyerPlaceOrderButton');
    const modal = document.getElementById('sariCheckoutSuccessModal');
    const modalMessage = document.getElementById('sariCheckoutSuccessText');
    const ordersButton = document.getElementById('sariCheckoutViewOrders');
    const originalButtonText = button?.textContent || 'Place Order';
    let inFlight = false;
    let navigationStarted = false;
    let navigateTimer = null;

    function setBusy(busy) {
        if (!button) return;
        button.disabled = busy;
        button.setAttribute('aria-busy', String(busy));
        button.textContent = busy ? 'Placing your order…' : originalButtonText;
    }

    function showIssue(message, mayHaveCommitted = false) {
        let alert = form.querySelector('[data-sari-checkout-error]');
        if (!alert) {
            alert = document.createElement('p');
            alert.dataset.sariCheckoutError = '1';
            alert.setAttribute('role', 'alert');
            alert.style.cssText = 'grid-column:1/-1;color:#a43e3e;font-size:12px;line-height:1.55;margin:8px 0 0;';
            button?.parentElement?.insertAdjacentElement('afterend', alert);
        }
        alert.textContent = message;
        if (mayHaveCommitted) {
            const link = document.createElement('a');
            link.href = form.dataset.ordersUrl || '/buyer/orders';
            link.textContent = ' Check My Orders';
            link.style.cssText = 'color:#8b620c;font-weight:700;text-decoration:underline;';
            alert.append(link);
        }
        alert.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function navigateToOrders(url) {
        if (navigationStarted) return;
        navigationStarted = true;
        window.clearTimeout(navigateTimer);
        // Normal navigation is deliberate here: it guarantees My Orders loads
        // from the server and does not re-submit the purchase.
        window.location.assign(url);
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (inFlight || navigationStarted) return;
        inFlight = true;
        setBusy(true);
        form.querySelector('[data-sari-checkout-error]')?.remove();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const isJson = (response.headers.get('content-type') || '').includes('application/json');
            const data = isJson ? await response.json() : null;

            if (!response.ok || data?.ok !== true) {
                const firstError = data?.errors && typeof data.errors === 'object'
                    ? Object.values(data.errors).flat().find(Boolean)
                    : null;
                const knownValidation = [401, 419, 422].includes(response.status);
                if (knownValidation) {
                    inFlight = false;
                    setBusy(false);
                    showIssue(firstError || data?.message || 'Please check your details and try again.');
                } else {
                    showIssue('We could not confirm the order result. Please check My Orders before trying again.', true);
                    if (button) button.textContent = 'Check My Orders';
                }
                return;
            }

            const ordersUrl = data.redirect_url;
            if (typeof ordersUrl !== 'string' || !ordersUrl) {
                showIssue('Your order was saved, but the destination is unavailable. Open My Orders to continue.', true);
                return;
            }
            const parsedUrl = new URL(ordersUrl, window.location.href);
            if (parsedUrl.origin !== window.location.origin || parsedUrl.pathname !== new URL(form.dataset.ordersUrl, window.location.href).pathname) {
                showIssue('Your order was saved. Please open My Orders from the navigation menu.', true);
                return;
            }

            if (modal) {
                // Move to body so fixed positioning is never clipped by any
                // sticky parent, sidebar transition, or Livewire content shell.
                document.body.appendChild(modal);
                if (modalMessage) {
                    const count = Number(data.order_count || 1);
                    modalMessage.textContent = count > 1
                        ? `${count} seller orders were created successfully. Opening My Orders…`
                        : 'Your order is confirmed. Opening My Orders…';
                }
                modal.hidden = false;
                ordersButton?.focus({ preventScroll: true });
                window.SariBuyerToast?.previewSound?.('success');
                ordersButton?.addEventListener('click', () => navigateToOrders(parsedUrl.href), { once: true });
                // Visible confirmation before navigation, without a slow extra
                // server round trip or dependence on Orders page rendering.
                navigateTimer = window.setTimeout(() => navigateToOrders(parsedUrl.href), 1750);
            } else {
                navigateToOrders(parsedUrl.href);
            }
        } catch (error) {
            // Network failures can occur AFTER the database committed.
            // Keep the button locked to prevent accidental duplicate purchases.
            showIssue('Connection lost while checking your order. Check My Orders before retrying.', true);
            if (button) button.textContent = 'Check My Orders';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuyerCheckout, { once: true });
} else {
    initBuyerCheckout();
}
document.addEventListener('livewire:navigated', initBuyerCheckout);
