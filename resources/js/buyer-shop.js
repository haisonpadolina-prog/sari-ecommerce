document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('shopProductSearch');
    const grid = document.getElementById('shopProductGrid');
    const empty = document.getElementById('shopProductsEmpty');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('[data-product-card]'));
    const apply = () => {
        const q = String(search?.value || '').trim().toLowerCase();
        let visible = 0;
        cards.forEach((card) => {
            const haystack = `${card.dataset.productName || ''} ${card.dataset.productCategory || ''}`.toLowerCase();
            const show = !q || haystack.includes(q);
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        empty?.classList.toggle('hidden', visible > 0);
    };
    search?.addEventListener('input', apply);
    apply();
});
