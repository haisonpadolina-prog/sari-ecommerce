(function () {
    'use strict';

    function parsePageData() {
        const node = document.getElementById('buyerProductsPageData');
        if (!node) return { products: [], routes: {}, searchQuery: '' };

        try {
            return JSON.parse(node.textContent || '{}') || { products: [], routes: {}, searchQuery: '' };
        } catch (_) {
            return { products: [], routes: {}, searchQuery: '' };
        }
    }

    function normalize(value) {
        return String(value ?? '')
            .toLowerCase()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function money(value) {
        const amount = Number(value || 0);
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(Number.isFinite(amount) ? amount : 0);
    }

    function initBuyerProducts() {
        const grid = document.getElementById('buyerProductGrid');
        if (!grid || grid.dataset.productsFiltersBound === '1') return;

        grid.dataset.productsFiltersBound = '1';

        const pageData = parsePageData();
        const pageProducts = Array.isArray(pageData.products) ? pageData.products : [];
        const productById = new Map(
            pageProducts.map((product) => [String(product?.id ?? ''), product])
        );

        const cards = Array.from(grid.querySelectorAll('[data-product-card]'));
        const search = document.getElementById('buyerProductSearch');
        const clearSearch = document.getElementById('buyerLiveSearchClear');
        const livePanel = document.getElementById('buyerLiveSearchPanel');
        const liveList = document.getElementById('buyerLiveSearchList');
        const liveCount = document.getElementById('buyerLiveSearchCount');
        const liveViewAll = document.getElementById('buyerLiveSearchViewAll');
        const liveViewAllText = document.getElementById('buyerLiveSearchViewAllText');
        const liveWrap = document.getElementById('buyerLiveSearchWrap');

        const sort = document.getElementById('buyerSort');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const inStock = document.getElementById('inStockFilter');
        const empty = document.getElementById('buyerProductEmpty');
        const resultIds = ['topResultCount', 'toolbarResultCount', 'buyerResultCount'];
        const resultLabel = document.getElementById('toolbarResultLabel');
        const clearAll = document.getElementById('clearAllFilters');
        const clearProductFilters = document.getElementById('clearProductFilters');

        let category = new URLSearchParams(window.location.search).get('category') || '';

        const searchableTextByCard = new Map();

        cards.forEach((card) => {
            const product = productById.get(String(card.dataset.productId || '')) || {};
            const variations = Array.isArray(product?.variations) ? product.variations.join(' ') : '';
            const sizes = Array.isArray(product?.sizes) ? product.sizes.join(' ') : '';

            const searchable = [
                card.dataset.productName,
                card.dataset.productCategory,
                card.dataset.productVariations,
                product?.name,
                product?.category,
                product?.brand,
                product?.sku,
                product?.shop_name,
                product?.store_name,
                product?.description,
                variations,
                sizes,
            ].filter(Boolean).join(' ');

            searchableTextByCard.set(card, normalize(searchable));
        });

        function activeCategory() {
            return normalize(category);
        }

        function queryValue() {
            return normalize(search?.value || '');
        }

        function cardMatchesSearch(card, query) {
            if (!query) return true;
            const haystack = searchableTextByCard.get(card) || '';
            const tokens = query.split(' ').filter(Boolean);
            return tokens.every((token) => haystack.includes(token));
        }

        function applyFilters() {
            const query = queryValue();
            const cat = activeCategory();
            const min = Number(minPrice?.value || 0);
            const maxInput = String(maxPrice?.value || '').trim();
            const max = maxInput === '' ? Number.POSITIVE_INFINITY : Number(maxInput);

            let visible = cards.filter((card) => {
                const cardCategory = normalize(card.dataset.productCategory || '');
                const price = Number(card.dataset.productPrice || 0);
                const stock = Number(card.dataset.productStock || 0);

                const matches =
                    cardMatchesSearch(card, query) &&
                    (!cat || cardCategory === cat) &&
                    price >= (Number.isFinite(min) ? min : 0) &&
                    price <= (Number.isFinite(max) ? max : Number.POSITIVE_INFINITY) &&
                    (!inStock?.checked || stock > 0);

                card.classList.toggle('hidden', !matches);
                return matches;
            });

            const mode = String(sort?.value || 'featured');
            visible = visible.sort((a, b) => {
                if (mode === 'price-low') {
                    return Number(a.dataset.productPrice || 0) - Number(b.dataset.productPrice || 0);
                }
                if (mode === 'price-high') {
                    return Number(b.dataset.productPrice || 0) - Number(a.dataset.productPrice || 0);
                }
                if (mode === 'rating') {
                    return Number(b.dataset.productRating || 0) - Number(a.dataset.productRating || 0);
                }
                if (mode === 'sold') {
                    return Number(b.dataset.productSold || 0) - Number(a.dataset.productSold || 0);
                }
                if (mode === 'latest') {
                    return Number(b.dataset.productId || 0) - Number(a.dataset.productId || 0);
                }
                return Number(a.dataset.originalIndex || 0) - Number(b.dataset.originalIndex || 0);
            });

            visible.forEach((card) => grid.appendChild(card));

            resultIds.forEach((id) => {
                const node = document.getElementById(id);
                if (node) node.textContent = String(visible.length);
            });

            if (resultLabel) {
                resultLabel.innerHTML = `&nbsp;${visible.length === 1 ? 'product' : 'products'}`;
            }

            empty?.classList.toggle('hidden', visible.length > 0);
            clearSearch?.classList.toggle('is-visible', Boolean(search?.value?.trim()));

            return visible;
        }

        cards.forEach((card, index) => {
            card.dataset.originalIndex = String(index);
        });

        function hideLivePanel() {
            livePanel?.classList.add('hidden');
            search?.setAttribute('aria-expanded', 'false');
        }

        function showLivePanel() {
            if (!livePanel) return;
            livePanel.classList.remove('hidden');
            search?.setAttribute('aria-expanded', 'true');
        }

        function renderSuggestions() {
            if (!search || !livePanel || !liveList) return;

            const query = queryValue();
            if (!query) {
                hideLivePanel();
                liveList.replaceChildren();
                if (liveCount) liveCount.textContent = '0 matches';
                return;
            }

            const matches = cards.filter((card) => cardMatchesSearch(card, query));
            const suggestions = matches.slice(0, 6);

            liveList.replaceChildren();

            suggestions.forEach((card) => {
                const product = productById.get(String(card.dataset.productId || '')) || {};
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'sari-live-result';
                row.setAttribute('role', 'option');

                const imageShell = document.createElement('span');
                imageShell.className = 'sari-live-result-image';

                const sourceImage = card.querySelector('img');
                if (sourceImage?.src) {
                    const image = document.createElement('img');
                    image.src = sourceImage.currentSrc || sourceImage.src;
                    image.alt = '';
                    image.loading = 'lazy';
                    imageShell.appendChild(image);
                } else {
                    imageShell.textContent = 'S';
                }

                const copy = document.createElement('span');
                copy.className = 'sari-live-result-copy';

                const name = document.createElement('strong');
                name.className = 'sari-live-result-name';
                name.textContent = String(product?.name || card.dataset.productName || 'Product');

                const meta = document.createElement('span');
                meta.className = 'sari-live-result-meta';
                const categoryText = String(product?.category || card.dataset.productCategory || '');
                const brandText = String(product?.brand || '').trim();
                const sellerText = String(product?.shop_name || product?.store_name || '').trim();
                meta.textContent = [categoryText, brandText, sellerText].filter(Boolean).join(' • ');

                copy.append(name, meta);

                const price = document.createElement('span');
                price.className = 'sari-live-result-price';
                price.textContent = money(card.dataset.productPrice || product?.price || 0);

                row.append(imageShell, copy, price);
                row.addEventListener('click', () => {
                    const url = String(card.dataset.productUrl || '').trim();
                    if (url) {
                        window.location.href = url;
                        return;
                    }

                    search.value = String(product?.name || card.dataset.productName || '').trim();
                    applyFilters();
                    hideLivePanel();
                    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                liveList.appendChild(row);
            });

            if (!suggestions.length) {
                const emptyState = document.createElement('div');
                emptyState.className = 'sari-live-search-empty';

                const title = document.createElement('strong');
                title.textContent = 'No matching products';

                const copy = document.createElement('p');
                copy.textContent = 'Try a product name, category, brand, seller, SKU, or variant.';

                emptyState.append(title, copy);
                liveList.appendChild(emptyState);
            }

            if (liveCount) {
                liveCount.textContent = `${matches.length} ${matches.length === 1 ? 'match' : 'matches'}`;
            }

            if (liveViewAllText) {
                liveViewAllText.textContent = matches.length
                    ? `View ${matches.length} matching ${matches.length === 1 ? 'product' : 'products'}`
                    : 'No matching products';
            }

            showLivePanel();
        }

        if (search && !search.value) {
            search.value = String(pageData.searchQuery || new URLSearchParams(window.location.search).get('search') || '');
        }

        search?.addEventListener('input', () => {
            applyFilters();
            renderSuggestions();
        });

        search?.addEventListener('focus', () => {
            if (search.value.trim()) renderSuggestions();
        });

        search?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                hideLivePanel();
                search.blur();
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                applyFilters();
                hideLivePanel();
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        clearSearch?.addEventListener('click', () => {
            if (!search) return;
            search.value = '';
            applyFilters();
            hideLivePanel();
            search.focus();
        });

        liveViewAll?.addEventListener('click', () => {
            applyFilters();
            hideLivePanel();
            grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        sort?.addEventListener('change', applyFilters);
        minPrice?.addEventListener('input', applyFilters);
        maxPrice?.addEventListener('input', applyFilters);
        inStock?.addEventListener('change', applyFilters);

        document.querySelectorAll('[data-filter-category]').forEach((button) => {
            button.addEventListener('click', () => {
                category = String(button.dataset.filterCategory || '');
                applyFilters();
            });
        });

        clearAll?.addEventListener('click', () => {
            category = '';
            if (search) search.value = '';
            applyFilters();
            hideLivePanel();
        });

        clearProductFilters?.addEventListener('click', () => {
            category = '';
            if (search) search.value = '';
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            if (inStock) inStock.checked = false;
            if (sort) sort.value = 'featured';
            applyFilters();
            hideLivePanel();
        });

        document.addEventListener('click', (event) => {
            if (!liveWrap?.contains(event.target)) hideLivePanel();
        });

        applyFilters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBuyerProducts, { once: true });
    } else {
        initBuyerProducts();
    }

    document.addEventListener('livewire:navigated', initBuyerProducts);
})();
