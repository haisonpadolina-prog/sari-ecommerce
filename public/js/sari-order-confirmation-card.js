(function () {
    if (window.SariOrderCard) return;
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);

    function render(order, isBuyer, confirmBase) {
        if (!order) return '';
        const items = Array.isArray(order.items) ? order.items : [];
        const confirmed = Boolean(order.confirmed);
        const terminal = ['cancelled', 'delivered'].includes(order.status);
        const productLines = items.slice(0, 3).map(item =>
            `<div class="sari-order-product">${item.image_url ? `<img src="${escapeHtml(item.image_url)}" alt="" loading="lazy">` : ''}<span>${Number(item.qty) || 1} × ${escapeHtml(item.name || 'Product')}</span></div>`
        ).join('');
        const more = items.length > 3 ? `<small>+${items.length - 3} more products</small>` : '';
        const action = confirmed
            ? '<div class="sari-order-confirmed">✓ Delivery details confirmed</div>'
            : (isBuyer && !terminal && confirmBase
                ? `<form method="POST" action="${escapeHtml(confirmBase.replace('__ORDER__', encodeURIComponent(order.id)))}" data-confirm-address-form>
                       <button class="sari-order-confirm-button" type="submit">Confirm delivery details</button>
                   </form><p class="sari-order-hint">Wrong details? Message the seller below instead of confirming.</p>`
                : `<p class="sari-order-hint">${terminal ? 'Confirmation is no longer available.' : 'Waiting for buyer confirmation.'}</p>`);
        const total = Number(order.total);
        return `<div class="sari-order-card" data-order-card="${Number(order.id)}">
            <div class="sari-order-card-head">
                <span class="sari-order-icon" aria-hidden="true">▣</span>
                <div class="sari-order-headline"><strong>Order confirmation</strong><small>${escapeHtml(order.number)}</small></div>
                <span class="sari-order-status ${confirmed ? 'is-confirmed' : ''}">${confirmed ? 'Confirmed' : 'Needs confirmation'}</span>
            </div>
            <div class="sari-order-products"><span class="sari-order-products-label">Your items</span>
                ${productLines}${more}<strong class="sari-order-total">₱${Number.isFinite(total) ? total.toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2}) : '0.00'} total</strong>
            </div>
            <dl class="sari-order-details"><div><dt>Phone number</dt><dd>${escapeHtml(order.phone || 'Not provided')}</dd></div>
            <div><dt>Delivery address</dt><dd>${escapeHtml(order.address)}</dd></div></dl>
            ${action}
        </div>`;
    }
    window.SariOrderCard = { render, escapeHtml };
})();
