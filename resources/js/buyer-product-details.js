document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('buyerProductCartForm');
    const variant = document.getElementById('buyerVariantSelect');
    const qty = document.getElementById('buyerProductQty');
    const price = document.getElementById('buyerProductPrice');
    const stock = document.getElementById('buyerVariantStock');
    const button = document.getElementById('buyerProductAddButton');

    const syncVariant = () => {
        const option = variant?.selectedOptions?.[0];
        if (!option || !option.value) return;
        const optionPrice = Number(option.dataset.price || 0);
        const optionStock = Number(option.dataset.stock || 0);
        if (price && optionPrice > 0) price.textContent = `₱${optionPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        if (stock) stock.textContent = `${optionStock} available for this variation`;
        if (qty) qty.max = String(Math.max(1, optionStock));
        if (button) button.disabled = optionStock <= 0;
    };
    variant?.addEventListener('change', syncVariant);

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const productId = Number(form.querySelector('[name="product_id"]')?.value || 0);
        const variantId = variant?.value ? Number(variant.value) : null;
        const quantity = Number(qty?.value || 1);

        try {
            if (button) { button.disabled = true; button.textContent = 'Adding...'; }
            await window.SariBuyerCart.add(productId, quantity, variantId);
            window.location.href = document.body.dataset.buyerCartUrl || '/buyer/cart';
        } catch (error) {
            alert(error.message || 'Could not add this item to your cart.');
            if (button) { button.disabled = false; button.textContent = 'Add to Cart'; }
        }
    });
});
