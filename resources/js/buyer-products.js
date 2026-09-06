document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('buyerProductGrid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('[data-product-card]'));
    const search = document.getElementById('buyerProductSearch');
    const sort = document.getElementById('buyerSort');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const inStock = document.getElementById('inStockFilter');
    const empty = document.getElementById('buyerProductEmpty');
    const resultIds = ['topResultCount', 'toolbarResultCount', 'buyerResultCount'];
    let category = new URLSearchParams(location.search).get('category') || '';

    function activeCategoryFromChecks() {
        const checked = document.querySelector('[data-category-check]:checked');
        if (checked) return checked.dataset.categoryCheck || '';
        return category;
    }

    function apply() {
        const q = String(search?.value || new URLSearchParams(location.search).get('search') || '').trim().toLowerCase();
        const cat = activeCategoryFromChecks().toLowerCase();
        const min = Number(minPrice?.value || 0);
        const max = Number(maxPrice?.value || Number.MAX_SAFE_INTEGER);
        let visible = cards.filter((card) => {
            const name = String(card.dataset.productName || '').toLowerCase();
            const c = String(card.dataset.productCategory || '').toLowerCase();
            const price = Number(card.dataset.productPrice || 0);
            const stock = Number(card.dataset.productStock || 0);
            const matches = (!q || name.includes(q) || c.includes(q)) && (!cat || c === cat) && price >= min && price <= max && (!inStock?.checked || stock > 0);
            card.classList.toggle('hidden', !matches);
            return matches;
        });

        const mode = sort?.value || '';
        visible.sort((a, b) => {
            if (mode === 'price-low') return Number(a.dataset.productPrice) - Number(b.dataset.productPrice);
            if (mode === 'price-high') return Number(b.dataset.productPrice) - Number(a.dataset.productPrice);
            if (mode === 'rating') return Number(b.dataset.productRating) - Number(a.dataset.productRating);
            if (mode === 'sold') return Number(b.dataset.productSold) - Number(a.dataset.productSold);
            return 0;
        }).forEach((card) => grid.appendChild(card));

        resultIds.forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.textContent = String(visible.length);
        });
        empty?.classList.toggle('hidden', visible.length > 0);
    }

    if (search && !search.value) search.value = new URLSearchParams(location.search).get('search') || '';
    search?.addEventListener('input', apply);
    sort?.addEventListener('change', apply);
    minPrice?.addEventListener('input', apply);
    maxPrice?.addEventListener('input', apply);
    inStock?.addEventListener('change', apply);
    document.querySelectorAll('[data-category-check]').forEach((checkbox) => checkbox.addEventListener('change', () => {
        document.querySelectorAll('[data-category-check]').forEach((other) => { if (other !== checkbox) other.checked = false; });
        category = checkbox.checked ? checkbox.dataset.categoryCheck || '' : '';
        apply();
    }));
    document.querySelectorAll('[data-filter-category]').forEach((button) => button.addEventListener('click', () => {
        category = button.dataset.filterCategory || '';
        document.querySelectorAll('[data-category-check]').forEach((box) => { box.checked = (box.dataset.categoryCheck || '') === category; });
        apply();
    }));
    document.getElementById('applyPriceFilter')?.addEventListener('click', apply);
    document.getElementById('clearProductFilters')?.addEventListener('click', () => {
        category = '';
        if (search) search.value = '';
        if (minPrice) minPrice.value = '';
        if (maxPrice) maxPrice.value = '';
        if (inStock) inStock.checked = false;
        document.querySelectorAll('[data-category-check]').forEach((box) => { box.checked = false; });
        apply();
    });
    apply();
});
